// --- GLOBAL STATE AND CONFIGURATION ---
let simulationInterval;
let map = null;
let markers = [];
let viewMode = "avg"; // 'avg' or 'total'
let selectedNodeId = "all";

// Store fetched devices globally
let devicesData = [];
let NODES = [];         // nodes WITH GPS location (for map)
let ALL_DEVICES = [];   // ALL devices regardless of location (for table)

// Map toggle functionality
let isMapFullscreen = false;

function setupMapToggle() {
  const mapContainer = document.getElementById("map-container");
  const mapToggle = document.getElementById("map-toggle");
  const maximizeIcon = document.getElementById("maximize-icon");
  const minimizeIcon = document.getElementById("minimize-icon");
  const toggleText = document.getElementById("map-toggle-text");

  if (!mapToggle) return;

  mapToggle.addEventListener("click", function () {
    isMapFullscreen = !isMapFullscreen;

    if (isMapFullscreen) {
      mapContainer.classList.add("fullscreen");
      maximizeIcon.classList.add("hidden");
      minimizeIcon.classList.remove("hidden");
      toggleText.textContent = "Minimize";
    } else {
      mapContainer.classList.remove("fullscreen");
      maximizeIcon.classList.remove("hidden");
      minimizeIcon.classList.add("hidden");
      toggleText.textContent = "Maximize";
    }

    // Trigger map resize after transition
    setTimeout(() => {
      if (map) map.invalidateSize();
    }, 300);
  });

  // Add escape key to exit fullscreen
  document.addEventListener("keydown", function (e) {
    if (e.key === "Escape" && isMapFullscreen) {
      mapToggle.click();
    }
  });
}

// Generate random hex color
function generateRandomColor() {
  const letters = "0123456789ABCDEF";
  let color = "#";
  for (let i = 0; i < 6; i++) {
    color += letters[Math.floor(Math.random() * 16)];
  }
  return color;
}

// Node data storage
let nodesData = {};

// Simulation constants
const CARBON_REDUCTION_FACTOR = 0.585;

// DOM Element Mapping
const elements = {
  coordinatesText: document.getElementById("coordinates-text"),
  authStatus: document.getElementById("auth-status"),
  lastUpdated: document.getElementById("last-updated-time"),
  nodeSelector: document.getElementById("node-selector"),
  nodesTableBody: document.getElementById("nodes-table-body"),
  totalNodes: document.getElementById("total-nodes"),
  activeNodesCount: document.getElementById("active-nodes-count"),
};

// --- FETCH DATA FROM SERVER ---
async function fetchDevicesFromServer() {
  try {
    // FIX: Use "jwt" key — matches what auth.js / login stores
    const authToken = localStorage.getItem("jwt");
    if (!authToken) {
      console.error("No authentication token available (key: jwt)");
      throw new Error("Please login first");
    }

    console.log("Fetching devices from server...");

    const response = await fetch("/proxy2.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
        Authorization: `Bearer ${authToken}`,
      },
      body: new URLSearchParams({
        action: "get_devices_with_data",
      }),
    });

    if (!response.ok) {
      throw new Error(`Failed to fetch devices: ${response.status}`);
    }

    const result = await response.json();

    if (result.success && result.data) {
      console.log(`Fetched ${result.data.length} devices from server`);
      devicesData = result.data;

      // FIX: Update summary stats directly from server response
      if (result.summary) {
        updateSummaryFromServer(result.summary);
      }

      return result.data;
    } else {
      throw new Error(result.message || "Failed to fetch devices from server");
    }
  } catch (error) {
    console.error("Error fetching devices:", error);
    throw error;
  }
}

// FIX: Update header summary stats from server summary object
function updateSummaryFromServer(summary) {
  // Active nodes count in header status bar
  if (elements.activeNodesCount) {
    elements.activeNodesCount.textContent = summary.active_devices ?? 0;
  }

  // Total nodes badge on table
  if (elements.totalNodes) {
    elements.totalNodes.textContent = summary.total_devices ?? 0;
  }

  // Total CO₂ Reduction (large card)
  const totalCarbonEl = document.querySelector("#total-carbon-card .text-5xl");
  if (totalCarbonEl && summary.total_carbon_reduction != null) {
    totalCarbonEl.innerHTML = `${parseFloat(summary.total_carbon_reduction).toFixed(3)}<span class="text-2xl text-[#FDA300] ml-2">kg</span>`;
  }

  // CO₂ Reduction (smaller card) — lifetime / cumulative energy-based
  const carbonValEl = document.getElementById("carbon-reduction-value");
  if (carbonValEl && summary.total_energy != null) {
    const lifetimeCarbon = (parseFloat(summary.total_energy) * CARBON_REDUCTION_FACTOR).toFixed(2);
    carbonValEl.innerHTML = `${lifetimeCarbon}<span class="text-lg text-[#FDA300] ml-1">kg</span>`;
  }
}

// Default location: Iskandar Puteri, Johor (project area)
const DEFAULT_LAT = 1.4236;
const DEFAULT_LNG = 103.6358;

// Convert server device data to node format
// Every device gets a map marker — real GPS if available, otherwise a
// slightly-offset default pin at Iskandar Puteri so the map is never empty.
function convertDevicesToNodes(devices) {
  return devices.map((device, index) => {
      const color = generateRandomColor();
      const deviceName =
        device.device_alias ||
        device.device_name ||
        device.telemetry?.device_name_from_telemetry ||
        `Device ${index + 1}`;

      // Determine status
      let status = "active";
      if (device.status === "warning" || device.metrics?.status === "warning") {
        status = "warning";
      } else if (device.status === "offline" || !device.telemetry) {
        status = "inactive";
      } else if (device.metrics?.battery_percentage < 30) {
        status = "warning";
      }

      // Try real GPS: device.location first, then telemetry fields
      let latitude = null;
      let longitude = null;
      let hasGPS = false;

      if (device.location && device.location.latitude && device.location.longitude) {
        latitude = parseFloat(device.location.latitude);
        longitude = parseFloat(device.location.longitude);
        hasGPS = true;
      } else if (device.telemetry?.latitude && device.telemetry?.longitude) {
        latitude = parseFloat(device.telemetry.latitude);
        longitude = parseFloat(device.telemetry.longitude);
        hasGPS = true;
      }

      // Fallback: place at default Johor location with a small unique offset
      // so multiple devices don't overlap perfectly on the map
      if (!hasGPS) {
        const spread = 0.003; // ~300m spread
        latitude  = DEFAULT_LAT + (index - devices.length / 2) * spread;
        longitude = DEFAULT_LNG + (index % 3 - 1) * spread;
      }

      const locationText = hasGPS
        ? `${latitude.toFixed(5)}, ${longitude.toFixed(5)}`
        : device.device_description || "Iskandar Puteri, Johor (approx.)";

      return {
        id: `node-${device.id || index + 1}`,
        server_id: device.device_id,
        name: deviceName,
        description: device.device_description || "",
        location: locationText,
        latitude,
        longitude,
        hasGPS,           // true = real GPS lock, false = default pin
        status,
        color,
        raw_device: device,
      };
    });
}

// Build nodesData map (keyed by node.id) for ALL devices
function buildNodesData(nodes) {
  nodes.forEach((node) => {
    const device = node.raw_device;
    const hasTelemetry = device.telemetry !== null && device.telemetry !== undefined;

    const voltage   = hasTelemetry ? parseFloat(device.telemetry.ac_voltage  || "0") : 0;
    const current   = hasTelemetry ? parseFloat(device.telemetry.ac_current  || "0") : 0;
    const pf        = hasTelemetry ? parseFloat(device.telemetry.power_factor || "0") : 0;
    const power     = hasTelemetry ? parseFloat(device.telemetry.ac_power    || "0") : 0;
    const energy    = hasTelemetry ? parseFloat(device.telemetry.energy      || "0") : 0;
    const frequency = hasTelemetry ? parseFloat(device.telemetry.frequency   || "0") : 0;

    // Battery SoC
    let batterySoC = 0;
    if (device.metrics?.battery_percentage > 0) {
      batterySoC = device.metrics.battery_percentage / 100;
    } else if (device.telemetry?.dc_voltage) {
      const dcV = parseFloat(device.telemetry.dc_voltage);
      batterySoC = Math.max(0, Math.min(1, (dcV - 12) / 1.8));
    }

    nodesData[node.id] = {
      ...node,
      voltage,
      current,
      power: power > 0 ? power : parseFloat((voltage * current * pf).toFixed(1)),
      energy,
      frequency,
      pf,
      batterySoC,
      batteryTime: calculateBatteryTime(batterySoC),
      carbonReduction: parseFloat((energy * CARBON_REDUCTION_FACTOR).toFixed(2)),
      lastUpdated: new Date(device.telemetry?.data_timestamp || Date.now()).getTime(),
      raw_telemetry: device.telemetry,
      raw_metrics: device.metrics,
    };
  });
}

// Initialize data from server devices
async function initializeDataFromServer() {
  try {
    const devices = await fetchDevicesFromServer();

    // Build ALL_DEVICES — every device gets coordinates (real GPS or default pin)
    ALL_DEVICES = convertDevicesToNodes(devices);

    // NODES = ALL_DEVICES: every device gets a map marker
    NODES = ALL_DEVICES;

    // Build nodesData for all devices
    buildNodesData(ALL_DEVICES);

    const gpsCount = ALL_DEVICES.filter(n => n.hasGPS).length;
    console.log(`Initialized: ${ALL_DEVICES.length} total devices, ${gpsCount} with real GPS, ${ALL_DEVICES.length - gpsCount} using default pin`);
  } catch (error) {
    console.error("Failed to initialize data from server:", error);
    throw error;
  }
}

// Calculate battery time from SoC
function calculateBatteryTime(batterySoC) {
  if (batterySoC <= 0) return "00:00";
  const remainingHours = batterySoC * 8;
  const hours = Math.floor(remainingHours);
  const minutes = Math.floor((remainingHours % 1) * 60);
  return `${String(hours).padStart(2, "0")}:${String(minutes).padStart(2, "0")}`;
}

// --- INITIALIZE MAP ---
function initMap() {
  // Default view: Iskandar Puteri, Johor — where devices are deployed
  map = L.map("map").setView([DEFAULT_LAT, DEFAULT_LNG], 13);

  L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
    attribution: "© OpenStreetMap contributors",
    maxZoom: 18,
    minZoom: 3,
  }).addTo(map);

  setTimeout(() => {
    if (map) map.invalidateSize();
  }, 300);

  updateMapMarkers();
}

// --- MAP FUNCTIONS ---
function updateMapMarkers() {
  markers.forEach((marker) => {
    if (map && map.hasLayer(marker)) map.removeLayer(marker);
  });
  markers = [];

  if (!map || NODES.length === 0) return;

  NODES.forEach((node) => {
    if (isNaN(node.latitude) || isNaN(node.longitude)) {
      console.warn(`Invalid coordinates for node ${node.id}:`, node.latitude, node.longitude);
      return;
    }

    const statusRingColor =
      node.status === "active"  ? "#10B981" :
      node.status === "warning" ? "#F59E0B" : "#EF4444";

    const statusPulse = node.status === "active"
      ? `<span class="marker-pulse" style="background:${statusRingColor};"></span>` : "";

    const gpsIcon = node.hasGPS
      ? `<span style="font-size:8px;line-height:1;">📡</span>`
      : `<span style="font-size:8px;line-height:1;opacity:0.7;">〜</span>`;

    // Teardrop pin: colored body + white inner circle with device initial + status ring
    const pinHtml = `
      <div class="marker-pin-wrap" style="position:relative;width:40px;height:48px;cursor:pointer;">
        ${statusPulse}
        <!-- teardrop body -->
        <div style="
          position:absolute;top:0;left:0;
          width:40px;height:40px;
          background: linear-gradient(135deg, ${node.color}ee, ${node.color}99);
          border-radius: 50% 50% 50% 0;
          transform: rotate(-45deg);
          box-shadow: 0 4px 15px ${node.color}88, 0 2px 6px rgba(0,0,0,0.35);
          border: 2.5px solid rgba(255,255,255,0.9);
        "></div>
        <!-- inner white circle -->
        <div style="
          position:absolute;top:6px;left:6px;
          width:28px;height:28px;
          background:rgba(255,255,255,0.92);
          border-radius:50%;
          display:flex;align-items:center;justify-content:center;
          flex-direction:column;gap:0;
        ">
          <span style="font-weight:800;font-size:11px;color:${node.color};line-height:1.1;">${node.name.charAt(0).toUpperCase()}</span>
          ${gpsIcon}
        </div>
        <!-- status ring dot -->
        <div style="
          position:absolute;top:0;left:26px;
          width:12px;height:12px;
          background:${statusRingColor};
          border-radius:50%;
          border:2px solid white;
          box-shadow:0 1px 4px rgba(0,0,0,0.3);
        "></div>
        <!-- pin tail shadow -->
        <div style="
          position:absolute;bottom:0;left:50%;
          transform:translateX(-50%);
          width:6px;height:6px;
          background:rgba(0,0,0,0.18);
          border-radius:50%;
          filter:blur(2px);
        "></div>
      </div>`;

    const icon = L.divIcon({
      className: "",
      html: pinHtml,
      iconSize: [40, 48],
      iconAnchor: [20, 46],
      popupAnchor: [0, -46],
    });

    const marker = L.marker([node.latitude, node.longitude], { icon, draggable: false, autoPan: false })
      .addTo(map)
      .bindPopup(createNodePopup(node), {
        maxWidth: 300,
        minWidth: 250,
        closeButton: true,
        autoClose: false,
        closeOnClick: false,
        className: "custom-popup",
      });

    marker.nodeId = node.id;

    marker.on("click", function (e) {
      e.originalEvent.preventDefault();
      e.originalEvent.stopPropagation();
      markers.forEach((m) => { if (m !== marker && m.isPopupOpen()) m.closePopup(); });
      selectNode(node.id);
    });

    markers.push(marker);
  });

  if (markers.length > 0) {
    const group = new L.featureGroup(markers);
    map.fitBounds(group.getBounds().pad(0.1));
  }
}

// Helper: small colored metric chip for popup
function metricChip(label, value, unit, color) {
  return `
    <div style="
      background:${color}11;
      border:1px solid ${color}33;
      border-radius:8px;
      padding:5px 6px;
      text-align:center;
    ">
      <div style="font-size:9px;color:#6B7280;font-weight:600;text-transform:uppercase;letter-spacing:.04em;margin-bottom:2px;">${label}</div>
      <div style="font-size:13px;font-weight:700;color:${color};line-height:1.1;">${value}<span style="font-size:9px;font-weight:500;margin-left:1px;">${unit}</span></div>
    </div>`;
}

function createNodePopup(node) {
  const nodeData = nodesData[node.id];
  const device = node.raw_device;
  const hasTelemetry = device && device.telemetry;
  const hasMetrics = device && device.metrics;

  const statusColor =
    node.status === "active" ? "#10B981" :
    node.status === "warning" ? "#F59E0B" : "#EF4444";

  let telemetryInfo = "";
  if (hasTelemetry) {
    telemetryInfo = `
      <div class="grid grid-cols-2 gap-x-4 gap-y-2">
        <div>
          <div class="text-xs text-gray-500">AC Voltage</div>
          <div class="font-bold text-blue-500">${device.telemetry.ac_voltage || "0.0"} <span class="text-xs">V</span></div>
        </div>
        <div>
          <div class="text-xs text-gray-500">AC Current</div>
          <div class="font-bold text-red-500">${device.telemetry.ac_current || "0.000"} <span class="text-xs">A</span></div>
        </div>
        <div>
          <div class="text-xs text-gray-500">AC Power</div>
          <div class="font-bold text-green-500">${device.telemetry.ac_power || "0.0"} <span class="text-xs">W</span></div>
        </div>
        <div>
          <div class="text-xs text-gray-500">Energy</div>
          <div class="font-bold text-purple-500">${device.telemetry.energy || "0.000"} <span class="text-xs">kWh</span></div>
        </div>
        <div>
          <div class="text-xs text-gray-500">Frequency</div>
          <div class="font-bold text-indigo-500">${device.telemetry.frequency || "0.0"} <span class="text-xs">Hz</span></div>
        </div>
        <div>
          <div class="text-xs text-gray-500">Power Factor</div>
          <div class="font-bold text-pink-500">${device.telemetry.power_factor || "0.000"}</div>
        </div>
        ${device.telemetry.dc_voltage ? `
        <div>
          <div class="text-xs text-gray-500">DC Voltage</div>
          <div class="font-bold text-orange-500">${device.telemetry.dc_voltage} <span class="text-xs">V</span></div>
        </div>` : ""}
        ${device.telemetry.dc_current ? `
        <div>
          <div class="text-xs text-gray-500">DC Current</div>
          <div class="font-bold text-amber-500">${device.telemetry.dc_current} <span class="text-xs">A</span></div>
        </div>` : ""}
      </div>
    `;
  }

  let metricsInfo = "";
  if (hasMetrics) {
    metricsInfo = `
      <div class="mt-2">
        <div class="text-xs font-medium text-gray-500 mb-1">Device Metrics:</div>
        <div class="grid grid-cols-2 gap-x-2 gap-y-1 text-xs">
          ${device.metrics.carbon_reduction ? `
          <div class="flex justify-between">
            <span class="text-gray-600">CO₂ Reduced:</span>
            <span class="font-medium">${device.metrics.carbon_reduction} kg</span>
          </div>` : ""}
          ${device.metrics.efficiency ? `
          <div class="flex justify-between">
            <span class="text-gray-600">Efficiency:</span>
            <span class="font-medium">${device.metrics.efficiency}%</span>
          </div>` : ""}
          ${device.metrics.battery_percentage != null ? `
          <div class="flex justify-between">
            <span class="text-gray-600">Battery:</span>
            <span class="font-medium">${device.metrics.battery_percentage}%</span>
          </div>` : ""}
          ${device.metrics.signal_quality ? `
          <div class="flex justify-between">
            <span class="text-gray-600">Signal:</span>
            <span class="font-medium">${device.metrics.signal_quality}</span>
          </div>` : ""}
        </div>
      </div>
    `;
  }

  const acPower    = device?.telemetry?.ac_power    ?? "—";
  const acVoltage  = device?.telemetry?.ac_voltage  ?? "—";
  const acCurrent  = device?.telemetry?.ac_current  ?? "—";
  const dcVoltage  = device?.telemetry?.dc_voltage  ?? "—";
  const frequency  = device?.telemetry?.frequency   ?? "—";
  const energy     = device?.telemetry?.energy      ?? "—";
  const pf         = device?.telemetry?.power_factor ?? "—";
  const carbonKg   = device?.metrics?.carbon_reduction ?? "—";
  const efficiency = device?.metrics?.efficiency    ?? "—";
  const signal     = device?.metrics?.signal_quality ?? "—";
  const lastTs     = device?.telemetry?.data_timestamp
    ? new Date(device.telemetry.data_timestamp).toLocaleString()
    : "N/A";

  const statusBg =
    node.status === "active"  ? "linear-gradient(135deg,#059669,#10B981)" :
    node.status === "warning" ? "linear-gradient(135deg,#D97706,#F59E0B)" :
                                "linear-gradient(135deg,#B91C1C,#EF4444)";

  const statusLabel =
    node.status === "active"  ? "🟢 Online" :
    node.status === "warning" ? "🟡 Warning" : "🔴 Offline";

  const gpsLabel = node.hasGPS
    ? `<span style="background:#D1FAE5;color:#065F46;padding:2px 7px;border-radius:999px;font-size:10px;font-weight:600;">📡 GPS Locked</span>`
    : `<span style="background:#FEF3C7;color:#92400E;padding:2px 7px;border-radius:999px;font-size:10px;font-weight:600;">📍 Approx. location</span>`;

  return `
    <div style="font-family:system-ui,sans-serif;width:280px;border-radius:14px;overflow:hidden;box-shadow:0 8px 30px rgba(0,0,0,0.18);">

      <!-- Header band -->
      <div style="background:${statusBg};padding:14px 16px 10px;">
        <div style="display:flex;align-items:center;gap:10px;">
          <div style="
            width:38px;height:38px;
            background:rgba(255,255,255,0.22);
            border-radius:50%;
            display:flex;align-items:center;justify-content:center;
            font-size:18px;font-weight:800;color:white;
            border:2px solid rgba(255,255,255,0.5);
            flex-shrink:0;
          ">${node.name.charAt(0).toUpperCase()}</div>
          <div style="flex:1;min-width:0;">
            <div style="color:white;font-weight:700;font-size:14px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${node.name}</div>
            <div style="color:rgba(255,255,255,0.8);font-size:11px;margin-top:1px;">${node.description || node.raw_device?.device_id || ""}</div>
          </div>
          <div style="text-align:right;flex-shrink:0;">
            <div style="color:white;font-size:11px;font-weight:600;">${statusLabel}</div>
            <div style="margin-top:4px;">${gpsLabel}</div>
          </div>
        </div>
      </div>

      <!-- Body -->
      <div style="background:#fff;padding:12px 14px 10px;">

        <!-- AC Metrics grid -->
        <div style="font-size:10px;font-weight:700;color:#6B7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:6px;">⚡ AC Output</div>
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:6px;margin-bottom:10px;">
          ${metricChip("Voltage", acVoltage, "V",  "#2563EB")}
          ${metricChip("Current", acCurrent, "A",  "#DC2626")}
          ${metricChip("Power",   acPower,   "W",  "#059669")}
          ${metricChip("Freq.",   frequency, "Hz", "#7C3AED")}
          ${metricChip("PF",      pf,        "",   "#DB2777")}
          ${metricChip("Energy",  energy,    "kWh","#D97706")}
        </div>

        <!-- DC row -->
        <div style="font-size:10px;font-weight:700;color:#6B7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:6px;">🔋 DC Input</div>
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:6px;margin-bottom:10px;">
          ${metricChip("DC Volt", dcVoltage, "V", "#EA580C")}
          ${metricChip("CO₂ ↓",  carbonKg,  "kg","#16A34A")}
          ${metricChip("Signal", signal,    "",  "#0891B2")}
        </div>

        <!-- Footer timestamp -->
        <div style="border-top:1px solid #F3F4F6;padding-top:8px;display:flex;justify-content:space-between;align-items:center;">
          <span style="font-size:10px;color:#9CA3AF;">Last data</span>
          <span style="font-size:10px;color:#374151;font-weight:600;">${lastTs}</span>
        </div>
      </div>
    </div>
  `;
}

// --- NODE MANAGEMENT FUNCTIONS ---
function initializeNodes() {
  updateNodeSelector();
  updateNodesTable();
  updateAggregateMetrics();
}

async function updateNodeData() {
  try {
    await initializeDataFromServer();
    updateNodesTable();
    updateAggregateMetrics();
    updateMapMarkers();
    // FIX: Update last-updated timestamp
    if (elements.lastUpdated) {
      elements.lastUpdated.textContent = new Date().toLocaleTimeString();
    }
  } catch (error) {
    console.error("Error updating node data:", error);
  }
}

// --- UI UPDATE FUNCTIONS ---
function updateNodeSelector() {
  const nodeSelector = elements.nodeSelector;
  if (!nodeSelector) return;

  nodeSelector.innerHTML = "";

  const allButton = document.createElement("button");
  allButton.className = `px-4 py-2 rounded-lg font-medium ${
    selectedNodeId === "all" ? "bg-blue-600 text-white" : "bg-gray-700 text-gray-300 hover:bg-gray-600"
  }`;
  allButton.textContent = "All Nodes";
  allButton.onclick = () => selectNode("all");
  nodeSelector.appendChild(allButton);

  // FIX: Use ALL_DEVICES for node selector (not just GPS nodes)
  ALL_DEVICES.forEach((node) => {
    const button = document.createElement("button");
    button.className = `px-4 py-2 rounded-lg font-medium ${
      selectedNodeId === node.id ? "bg-blue-600 text-white" : "bg-gray-700 text-gray-300 hover:bg-gray-600"
    }`;
    button.textContent = node.name;
    button.onclick = () => selectNode(node.id);
    nodeSelector.appendChild(button);
  });
}

function updateNodesTable() {
  const tbody = elements.nodesTableBody;
  if (!tbody) return;

  tbody.innerHTML = "";

  // FIX: Use ALL_DEVICES so devices without GPS still appear in the table
  if (ALL_DEVICES.length === 0) {
    const row = document.createElement("tr");
    row.innerHTML = `
      <td colspan="6" class="px-4 py-8 text-center text-gray-400">
        <i class="fas fa-exclamation-circle mr-2"></i>No devices available
      </td>
    `;
    tbody.appendChild(row);
    return;
  }

  ALL_DEVICES.forEach((node) => {
    const nodeData = nodesData[node.id];
    const row = document.createElement("tr");
    row.className = `node-row ${selectedNodeId === node.id ? "active" : ""}`;

    const statusColor =
      node.status === "active" ? "bg-green-500" :
      node.status === "warning" ? "bg-yellow-500" : "bg-red-500";

    // FIX: Show device description + alias as name/subtitle
    const displayName = node.name;
    const displaySub  = node.description || node.location;

    row.innerHTML = `
      <td class="px-4 py-3 font-medium">${node.raw_device?.id ?? node.id}</td>
      <td class="px-4 py-3">
        <div class="font-medium">${displayName}</div>
        <div class="text-xs text-gray-400">${displaySub}</div>
      </td>
      <td class="px-4 py-3">
        <div class="flex items-center">
          <div class="w-2 h-2 ${statusColor} rounded-full mr-2"></div>
          <span class="capitalize">${node.status}</span>
        </div>
      </td>
      <td class="px-4 py-3 font-bold">${parseFloat(nodeData?.power || 0).toFixed(1)}</td>
      <td class="px-4 py-3">${(nodeData?.voltage || 0).toFixed(1)}</td>
      <td class="px-4 py-3">${(nodeData?.current || 0).toFixed(3)}</td>
    `;

    row.addEventListener("click", () => selectNode(node.id));
    tbody.appendChild(row);
  });

  // FIX: Count based on ALL_DEVICES
  if (elements.totalNodes) {
    elements.totalNodes.textContent = ALL_DEVICES.length;
  }
  if (elements.activeNodesCount) {
    elements.activeNodesCount.textContent = ALL_DEVICES.filter((n) => n.status === "active").length;
  }
}

function updateAggregateMetrics() {
  if (ALL_DEVICES.length === 0) return;

  const nodeIds = Object.keys(nodesData);
  const totalEnergy = nodeIds.reduce((sum, id) => sum + (nodesData[id].energy || 0), 0);
  const totalCarbon = nodeIds.reduce((sum, id) => sum + parseFloat(nodesData[id].carbonReduction || 0), 0);

  // Update CO₂ reduction card from local computation if server summary not available
  const carbonValEl = document.getElementById("carbon-reduction-value");
  if (carbonValEl) {
    carbonValEl.innerHTML = `${totalCarbon.toFixed(2)}<span class="text-lg text-[#FDA300] ml-1">kg</span>`;
  }
}

// --- EVENT HANDLERS ---
function selectNode(nodeId) {
  selectedNodeId = nodeId;

  updateNodeSelector();

  if (nodeId === "all") {
    if (markers.length > 0 && map) {
      const group = new L.featureGroup(markers);
      map.fitBounds(group.getBounds().pad(0.1));
    }
  } else {
    highlightSelectedMarker(nodeId);
  }

  const rows = document.querySelectorAll(".node-row");
  rows.forEach((row) => {
    row.classList.remove("active");
    if (row.cells[0].textContent.trim() === String(ALL_DEVICES.find(n => n.id === nodeId)?.raw_device?.id ?? "")) {
      row.classList.add("active");
    }
  });
}

function highlightSelectedMarker(nodeId) {
  if (!map) return;

  markers.forEach((marker) => {
    const iconDiv = marker.getElement();
    if (iconDiv) {
      iconDiv.style.transform = "";
      iconDiv.style.filter = "";
      iconDiv.style.zIndex = "";
    }
  });

  const selectedMarker = markers.find((m) => m.nodeId === nodeId);
  if (selectedMarker) {
    setTimeout(() => {
      if (map) {
        map.flyTo(selectedMarker.getLatLng(), 13, { duration: 1, easeLinearity: 0.25 });
        setTimeout(() => { selectedMarker.openPopup(); }, 1000);
      }
    }, 100);

    const iconDiv = selectedMarker.getElement();
    if (iconDiv) {
      iconDiv.style.transform = "scale(1.2) translateY(-3px)";
      iconDiv.style.filter = "drop-shadow(0 8px 16px rgba(251,191,36,0.7))";
      iconDiv.style.zIndex = "1000";
      iconDiv.style.transition = "all 0.3s ease";
      iconDiv.classList.add("marker-selected");
      setTimeout(() => { iconDiv.classList.remove("marker-selected"); }, 400);
    }
  }
}

// Add CSS for marker animations and popup styling
const style = document.createElement("style");
style.textContent = `
  /* Teardrop pin hover lift */
  .marker-pin-wrap { transition: transform 0.15s ease, filter 0.15s ease; }
  .marker-pin-wrap:hover { transform: translateY(-4px) scale(1.08); filter: drop-shadow(0 6px 10px rgba(0,0,0,0.25)); }

  /* Active-status pulse ring */
  .marker-pulse {
    position: absolute;
    top: -4px; left: -4px;
    width: 48px; height: 48px;
    border-radius: 50% 50% 50% 0;
    transform: rotate(-45deg);
    animation: markerPulse 2s ease-out infinite;
    pointer-events: none;
  }
  @keyframes markerPulse {
    0%   { box-shadow: 0 0 0 0   currentColor; opacity: 0.7; }
    70%  { box-shadow: 0 0 0 12px transparent; opacity: 0;   }
    100% { box-shadow: 0 0 0 0   transparent; opacity: 0;   }
  }

  /* Leaflet popup: remove default white border/shadow, let our card do it */
  .leaflet-popup-content-wrapper {
    padding: 0 !important;
    border-radius: 14px !important;
    overflow: hidden !important;
    box-shadow: 0 8px 30px rgba(0,0,0,0.18) !important;
    border: none !important;
  }
  .leaflet-popup-content {
    margin: 0 !important;
    width: auto !important;
  }
  .leaflet-popup-tip-container { margin-top: -1px; }
  .leaflet-popup-tip { box-shadow: none; }
  .leaflet-popup-close-button {
    color: white !important;
    font-size: 18px !important;
    top: 6px !important;
    right: 10px !important;
    font-weight: 700 !important;
  }

  /* Selected marker scale animation */
  .marker-selected { animation: markerSelected 0.3s ease forwards; }
  @keyframes markerSelected {
    0%   { transform: scale(1); }
    50%  { transform: scale(1.25) translateY(-4px); }
    100% { transform: scale(1.15) translateY(-2px); }
  }
`;
document.head.appendChild(style);

// --- INITIALIZATION ---
async function initApplication() {
  try {
    // FIX: Check "jwt" key (matches login/auth.js)
    const authToken = localStorage.getItem("jwt");
    if (!authToken) {
      console.error("No authentication token found (key: jwt)");
      if (elements.authStatus) {
        elements.authStatus.textContent = "Please login first";
      }
      return;
    }

    if (elements.authStatus) {
      elements.authStatus.innerHTML = `<i class="fas fa-spinner fa-spin text-[#FDA300] mr-1"></i> Loading data...`;
    }

    // Initialize data from server
    await initializeDataFromServer();

    // Initialize map
    initMap();

    // Initialize map toggle
    setupMapToggle();

    // Initialize nodes UI
    initializeNodes();

    // Set initial display
    selectNode("all");

    // FIX: Update last-updated timestamp on initial load
    if (elements.lastUpdated) {
      elements.lastUpdated.textContent = new Date().toLocaleTimeString();
    }

    // Refresh every 30 seconds
    simulationInterval = setInterval(updateNodeData, 30000);

    // View mode toggle buttons (avg / total)
    const toggleAvg   = document.getElementById("toggle-avg");
    const toggleTotal = document.getElementById("toggle-total");

    if (toggleAvg && toggleTotal) {
      toggleAvg.onclick = function () {
        viewMode = "avg";
        this.classList.add("bg-blue-600", "text-white");
        this.classList.remove("text-gray-300");
        toggleTotal.classList.remove("bg-blue-600", "text-white");
        toggleTotal.classList.add("text-gray-300");
      };
      toggleTotal.onclick = function () {
        viewMode = "total";
        this.classList.add("bg-blue-600", "text-white");
        this.classList.remove("text-gray-300");
        toggleAvg.classList.remove("bg-blue-600", "text-white");
        toggleAvg.classList.add("text-gray-300");
      };
    }

    if (elements.authStatus) {
      // Restore the original auth-status HTML structure
      elements.authStatus.innerHTML = `<i class="fas fa-shield-alt text-[#FDA300]"></i>Connected as: <span id="current-role" class="text-[#FDA300]">${(JSON.parse(localStorage.getItem("user") || "{}").user_tipe) || "ADMIN"}</span>`;
    }

    console.log("Inverter dashboard initialized with server data");
  } catch (error) {
    console.error("Failed to initialize application:", error);
    if (elements.authStatus) {
      elements.authStatus.textContent = "Failed to load data";
    }
  }
}

// --- GLOBAL FUNCTIONS ---

// Refresh data from server (callable from HTML button)
window.refreshData = async function () {
  if (simulationInterval) clearInterval(simulationInterval);

  if (elements.authStatus) {
    elements.authStatus.innerHTML = `<i class="fas fa-spinner fa-spin text-[#FDA300] mr-1"></i> Refreshing...`;
  }

  try {
    await initializeDataFromServer();
    updateNodeSelector();
    updateNodesTable();
    updateAggregateMetrics();
    updateMapMarkers();

    if (elements.lastUpdated) {
      elements.lastUpdated.textContent = new Date().toLocaleTimeString();
    }
  } catch (e) {
    console.error("Refresh error:", e);
  }

  simulationInterval = setInterval(updateNodeData, 30000);

  if (elements.authStatus) {
    elements.authStatus.innerHTML = `<i class="fas fa-check text-[#FDA300] mr-1"></i> Data refreshed`;
    setTimeout(() => {
      elements.authStatus.innerHTML = `<i class="fas fa-shield-alt text-[#FDA300]"></i>Connected as: <span id="current-role" class="text-[#FDA300]">${(JSON.parse(localStorage.getItem("user") || "{}").user_tipe) || "ADMIN"}</span>`;
    }, 2000);
  }
};

// Expose updateMapTheme for theme toggle (called from dashboard-inverter.php)
window.updateMapTheme = function () {
  if (!map) return;
  // Re-render tiles if needed; for now just invalidate size
  map.invalidateSize();
};

// --- START APPLICATION ---
document.addEventListener("DOMContentLoaded", initApplication);