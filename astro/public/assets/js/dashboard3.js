// --- GLOBAL STATE AND CONFIGURATION ---
let simulationInterval;
let map = null;
let markers = [];
let viewMode = "avg"; // 'avg' or 'total'
let selectedNodeId = "all";

// Store fetched devices globally
let devicesData = [];
let NODES = []; // nodes WITH GPS location (for map)
let ALL_DEVICES = []; // ALL devices regardless of location (for table)

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
    elements.activeNodesCount.textContent =
      (summary.total_devices ?? 0) + " / " + (summary.total_devices ?? 0);
  }

  // Total nodes badge on table
  if (elements.totalNodes) {
    elements.totalNodes.textContent = (summary.total_devices ?? 0) + " Active";
  }

  // Total CO₂ Reduction (large card)
  const totalCarbonEl = document.querySelector("#total-carbon-card .text-5xl");
  if (totalCarbonEl && summary.total_carbon_reduction != null) {
    totalCarbonEl.innerHTML = `${parseFloat(summary.total_carbon_reduction).toFixed(3)}<span class="text-2xl text-[#FDA300] ml-2">kg</span>`;
  }

  // CO₂ Reduction (smaller card) — lifetime / cumulative energy-based
  const carbonValEl = document.getElementById("carbon-reduction-value");
  if (carbonValEl && summary.total_energy != null) {
    const lifetimeCarbon = (
      parseFloat(summary.total_energy) * CARBON_REDUCTION_FACTOR
    ).toFixed(2);
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

    if (
      device.location &&
      device.location.latitude &&
      device.location.longitude
    ) {
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
      latitude = DEFAULT_LAT + (index - devices.length / 2) * spread;
      longitude = DEFAULT_LNG + ((index % 3) - 1) * spread;
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
      hasGPS, // true = real GPS lock, false = default pin
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
    const hasTelemetry =
      device.telemetry !== null && device.telemetry !== undefined;

    const voltage = hasTelemetry
      ? parseFloat(device.telemetry.ac_voltage || "0")
      : 0;
    const current = hasTelemetry
      ? parseFloat(device.telemetry.ac_current || "0")
      : 0;
    const pf = hasTelemetry
      ? parseFloat(device.telemetry.power_factor || "0")
      : 0;
    const power = hasTelemetry
      ? parseFloat(device.telemetry.ac_power || "0")
      : 0;
    const energy = hasTelemetry
      ? parseFloat(device.telemetry.energy || "0")
      : 0;
    const frequency = hasTelemetry
      ? parseFloat(device.telemetry.frequency || "0")
      : 0;

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
      power:
        power > 0 ? power : parseFloat((voltage * current * pf).toFixed(1)),
      energy,
      frequency,
      pf,
      batterySoC,
      batteryTime: calculateBatteryTime(batterySoC),
      carbonReduction: parseFloat(
        (energy * CARBON_REDUCTION_FACTOR).toFixed(2),
      ),
      lastUpdated: new Date(
        device.telemetry?.data_timestamp || Date.now(),
      ).getTime(),
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

    const gpsCount = ALL_DEVICES.filter((n) => n.hasGPS).length;
    console.log(
      `Initialized: ${ALL_DEVICES.length} total devices, ${gpsCount} with real GPS, ${ALL_DEVICES.length - gpsCount} using default pin`,
    );
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
      console.warn(
        `Invalid coordinates for node ${node.id}:`,
        node.latitude,
        node.longitude,
      );
      return;
    }

    const borderStyle = node.hasGPS
      ? "border: 3px solid white;"
      : "border: 3px dashed white; opacity: 0.85;";

    const icon = L.divIcon({
      className: "node-marker",
      html: `<div style="background-color: ${node.color}; width: 24px; height: 24px; border-radius: 50%; ${borderStyle} box-shadow: 0 0 10px rgba(0,0,0,0.5); cursor: pointer; position: relative;"></div>`,
      iconSize: [24, 24],
      iconAnchor: [12, 12],
      popupAnchor: [0, -12],
    });

    const marker = L.marker([node.latitude, node.longitude], {
      icon,
      draggable: false,
      autoPan: false,
    })
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
      markers.forEach((m) => {
        if (m !== marker && m.isPopupOpen()) m.closePopup();
      });
      selectNode(node.id);
    });

    markers.push(marker);
  });

  if (markers.length > 0) {
    const group = new L.featureGroup(markers);
    map.fitBounds(group.getBounds().pad(0.1));
  }
}

function createNodePopup(node) {
  const nodeData = nodesData[node.id];
  const device = node.raw_device;
  const hasTelemetry = device && device.telemetry;
  const hasMetrics = device && device.metrics;

  const statusColor =
    node.status === "active"
      ? "#10B981"
      : node.status === "warning"
        ? "#F59E0B"
        : "#EF4444";

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
        ${
          device.telemetry.dc_voltage
            ? `
        <div>
          <div class="text-xs text-gray-500">DC Voltage</div>
          <div class="font-bold text-orange-500">${device.telemetry.dc_voltage} <span class="text-xs">V</span></div>
        </div>`
            : ""
        }
        ${
          device.telemetry.dc_current
            ? `
        <div>
          <div class="text-xs text-gray-500">DC Current</div>
          <div class="font-bold text-amber-500">${device.telemetry.dc_current} <span class="text-xs">A</span></div>
        </div>`
            : ""
        }
      </div>
    `;
  }

  let metricsInfo = "";
  if (hasMetrics) {
    metricsInfo = `
      <div class="mt-2">
        <div class="text-xs font-medium text-gray-500 mb-1">Device Metrics:</div>
        <div class="grid grid-cols-2 gap-x-2 gap-y-1 text-xs">
          ${
            device.metrics.carbon_reduction
              ? `
          <div class="flex justify-between">
            <span class="text-gray-600">CO₂ Reduced:</span>
            <span class="font-medium">${device.metrics.carbon_reduction} kg</span>
          </div>`
              : ""
          }
          ${
            device.metrics.efficiency
              ? `
          <div class="flex justify-between">
            <span class="text-gray-600">Efficiency:</span>
            <span class="font-medium">${device.metrics.efficiency}%</span>
          </div>`
              : ""
          }
          ${
            device.metrics.battery_percentage != null
              ? `
          <div class="flex justify-between">
            <span class="text-gray-600">Battery:</span>
            <span class="font-medium">${device.metrics.battery_percentage}%</span>
          </div>`
              : ""
          }
          ${
            device.metrics.signal_quality
              ? `
          <div class="flex justify-between">
            <span class="text-gray-600">Signal:</span>
            <span class="font-medium">${device.metrics.signal_quality}</span>
          </div>`
              : ""
          }
        </div>
      </div>
    `;
  }

  return `
    <div class="popup-content">
      <div class="flex items-center gap-2 mb-2">
        <div style="
          width: 20px; height: 20px;
          background: ${node.color};
          border-radius: 3px;
          transform: rotate(45deg);
          display: flex; align-items: center; justify-content: center;
        ">
          <span style="transform: rotate(-45deg); font-weight: bold; color: white; font-size: 10px;">
            ${node.name.charAt(0)}
          </span>
        </div>
        <div>
          <h3 class="font-bold text-lg">${node.name}</h3>
          <p class="text-sm text-gray-600">${node.description || node.location}</p>
        </div>
        <div style="width: 8px; height: 8px; border-radius: 50%; background: ${statusColor};"></div>
      </div>

      <div class="mt-2 space-y-1 text-sm">
        <div class="flex justify-between items-center">
          <span class="text-gray-600">Device ID:</span>
          <span class="font-medium">${device?.device_id || node.id}</span>
        </div>
        <div class="flex justify-between items-center">
          <span class="text-gray-600">Alias:</span>
          <span class="font-medium">${device?.device_alias || "N/A"}</span>
        </div>
        <div class="flex justify-between items-center">
          <span class="text-gray-600">Status:</span>
          <span class="font-medium" style="color: ${statusColor}">${device?.status_text || node.status.toUpperCase()}</span>
        </div>
        <div class="flex justify-between items-center">
          <span class="text-gray-600">Location:</span>
          ${
            node.hasGPS
              ? `<span class="font-medium text-green-600">📍 GPS Locked</span>`
              : `<span class="font-medium text-orange-500">📍 Approx. (no GPS fix)</span>`
          }
        </div>
        <hr class="my-1 border-gray-200">

        ${telemetryInfo || `<div class="text-center text-gray-500 text-sm py-2">No telemetry data available</div>`}

        ${metricsInfo}

        <div class="pt-2 text-xs text-gray-500 text-center">
          Last Data: ${device?.telemetry?.data_timestamp ? new Date(device.telemetry.data_timestamp).toLocaleString() : "N/A"}
          <br>
          Updated: ${nodeData?.lastUpdated ? new Date(nodeData.lastUpdated).toLocaleTimeString() : "Just now"}
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
    selectedNodeId === "all"
      ? "bg-blue-600 text-white"
      : "bg-gray-700 text-gray-300 hover:bg-gray-600"
  }`;
  allButton.textContent = "All Nodes";
  allButton.onclick = () => selectNode("all");
  nodeSelector.appendChild(allButton);

  // FIX: Use ALL_DEVICES for node selector (not just GPS nodes)
  ALL_DEVICES.forEach((node) => {
    const button = document.createElement("button");
    button.className = `px-4 py-2 rounded-lg font-medium ${
      selectedNodeId === node.id
        ? "bg-blue-600 text-white"
        : "bg-gray-700 text-gray-300 hover:bg-gray-600"
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
      node.status === "active"
        ? "bg-green-500"
        : node.status === "warning"
          ? "bg-yellow-500"
          : "bg-red-500";

    // FIX: Show device description + alias as name/subtitle
    const displayName = node.name;
    const displaySub = node.description || node.location;

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
    elements.totalNodes.textContent = ALL_DEVICES.length + " Active";
  }
  if (elements.activeNodesCount) {
    elements.activeNodesCount.textContent =
      ALL_DEVICES.filter((n) => n.status === "active").length +
      " / " +
      (summary.total_devices ?? 0);
  }
}

function updateAggregateMetrics() {
  if (ALL_DEVICES.length === 0) return;

  const nodeIds = Object.keys(nodesData);
  const totalEnergy = nodeIds.reduce(
    (sum, id) => sum + (nodesData[id].energy || 0),
    0,
  );
  const totalCarbon = nodeIds.reduce(
    (sum, id) => sum + parseFloat(nodesData[id].carbonReduction || 0),
    0,
  );

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
    if (
      row.cells[0].textContent.trim() ===
      String(ALL_DEVICES.find((n) => n.id === nodeId)?.raw_device?.id ?? "")
    ) {
      row.classList.add("active");
    }
  });
}

function highlightSelectedMarker(nodeId) {
  if (!map) return;

  markers.forEach((marker) => {
    const iconDiv = marker.getElement();
    if (iconDiv) {
      iconDiv.style.transform = "scale(1)";
      iconDiv.style.boxShadow = "0 0 10px rgba(0,0,0,0.5)";
      iconDiv.style.zIndex = "1";
    }
  });

  const selectedMarker = markers.find((m) => m.nodeId === nodeId);
  if (selectedMarker) {
    setTimeout(() => {
      if (map) {
        map.flyTo(selectedMarker.getLatLng(), 13, {
          duration: 1,
          easeLinearity: 0.25,
        });
        setTimeout(() => {
          selectedMarker.openPopup();
        }, 1000);
      }
    }, 100);

    const iconDiv = selectedMarker.getElement();
    if (iconDiv) {
      iconDiv.style.transform = "scale(1.3)";
      iconDiv.style.boxShadow = "0 0 25px rgba(251,191,36,0.8)";
      iconDiv.style.zIndex = "1000";
      iconDiv.style.transition = "all 0.3s ease";
      iconDiv.classList.add("pulse-animation");
      setTimeout(() => {
        iconDiv.classList.remove("pulse-animation");
      }, 1500);
    }
  }
}

// Add CSS for pulse animation
const style = document.createElement("style");
style.textContent = `
  .pulse-animation { animation: pulse 1.5s ease-in-out infinite; }
  @keyframes pulse {
    0%   { box-shadow: 0 0 0 0   rgba(251,191,36,0.7); }
    70%  { box-shadow: 0 0 0 10px rgba(251,191,36,0);   }
    100% { box-shadow: 0 0 0 0   rgba(251,191,36,0);   }
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
    const toggleAvg = document.getElementById("toggle-avg");
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
      elements.authStatus.innerHTML = `<i class="fas fa-shield-alt text-[#FDA300]"></i>Connected as: <span id="current-role" class="text-[#FDA300]">${JSON.parse(localStorage.getItem("user") || "{}").user_tipe || "ADMIN"}</span>`;
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
      elements.authStatus.innerHTML = `<i class="fas fa-shield-alt text-[#FDA300]"></i>Connected as: <span id="current-role" class="text-[#FDA300]">${JSON.parse(localStorage.getItem("user") || "{}").user_tipe || "ADMIN"}</span>`;
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
