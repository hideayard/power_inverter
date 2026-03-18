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

// Store summary data from server
let serverSummary = {
  total_devices: 0,
  total_carbon_reduction: 0,
  total_energy: 0
};

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

// DOM Element Mapping - FIX: Move inside function to ensure elements exist
function getElements() {
  return {
    coordinatesText: document.getElementById("coordinates-text"),
    authStatus: document.getElementById("auth-status"),
    lastUpdated: document.getElementById("last-updated-time"),
    nodeSelector: document.getElementById("node-selector"),
    nodesTableBody: document.getElementById("nodes-table-body"),
    totalNodes: document.getElementById("total-nodes"),
    activeNodesCount: document.getElementById("active-nodes-count"),
  };
}

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

      // FIX: Store summary data globally
      if (result.summary) {
        serverSummary = result.summary;
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
  const elements = getElements();
  
  // Active nodes count in header status bar
  if (elements.activeNodesCount) {
    const activeCount = ALL_DEVICES.filter(d => d.status === 'active').length;
    elements.activeNodesCount.textContent = activeCount + " / " + (summary.total_devices ?? 0);
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
    
    return ALL_DEVICES;
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

// ============================================
// MARKER CREATION AND STYLING SECTION
// ============================================

/**
 * Updates all markers on the map based on current NODES data
 * This function clears existing markers and creates new ones
 * Each marker represents a device/inverter on the map
 */
function updateMapMarkers() {
  // Clear existing markers from the map
  if (markers.length > 0) {
    markers.forEach((marker) => {
      if (map && map.hasLayer(marker)) map.removeLayer(marker);
    });
    markers = [];
  }

  if (!map || NODES.length === 0) return;

  // Create new markers for each node/device
  NODES.forEach((node) => {
    if (isNaN(node.latitude) || isNaN(node.longitude)) {
      console.warn(
        `Invalid coordinates for node ${node.id}:`,
        node.latitude,
        node.longitude,
      );
      return;
    }

    // ============================================
    // MARKER VISUAL STYLING - YOU CAN MODIFY THIS SECTION
    // ============================================
    
    // Get status color based on device status
    const statusColor = getStatusColor(node.status);
    
    // Border style - solid for GPS locked, dashed for approximate location
    const borderStyle = node.hasGPS
      ? "border: 3px solid white;"  // Real GPS location
      : "border: 3px dashed white; opacity: 0.9;"; // Approximate location
    
    // Create custom marker with enhanced visibility
    // You can modify these styles to change marker appearance:
    // - width/height: Size of the marker
    // - background-color: Main color of the marker
    // - box-shadow: Glow effect around marker
    // - border: Border styling
    const icon = L.divIcon({
      className: "node-marker",
      html: `
        <div style="
          background-color: ${statusColor}; 
          width: 32px; 
          height: 32px; 
          border-radius: 50%; 
          ${borderStyle} 
          box-shadow: 0 0 15px rgba(0,0,0,0.5), 0 0 10px ${statusColor};
          cursor: pointer; 
          position: relative;
          transition: all 0.3s ease;
          display: flex;
          align-items: center;
          justify-content: center;
        ">
          <!-- Optional inner dot for better visibility -->
          <div style="
            width: 10px; 
            height: 10px; 
            border-radius: 50%; 
            background-color: white;
            opacity: 0.8;
          "></div>
          
          <!-- Power indicator (shows if device is generating power) -->
          ${node.raw_device?.telemetry?.ac_power > 0 ? 
            `<div style="
              position: absolute;
              top: -5px;
              right: -5px;
              width: 12px;
              height: 12px;
              border-radius: 50%;
              background-color: #10B981;
              border: 2px solid white;
              box-shadow: 0 0 10px #10B981;
            "></div>` : ''
          }
        </div>
      `,
      iconSize: [32, 32],  // Size of the marker icon
      iconAnchor: [16, 16], // Point of the icon which will correspond to marker's location
      popupAnchor: [0, -16], // Point from which the popup should open relative to iconAnchor
    });

    // Create the marker with custom icon
    const marker = L.marker([node.latitude, node.longitude], {
      icon,
      draggable: false,
      autoPan: false,
      riseOnHover: true, // Bring marker to front on hover
      zIndexOffset: node.hasGPS ? 1000 : 500, // Priority for GPS-locked devices
    })
      .addTo(map)
      .bindPopup(createNodePopup(node), {
        maxWidth: 350,
        minWidth: 280,
        maxHeight: 400, // Limit popup height
        className: "custom-popup",
        keepInView: true,
        autoPan: true,
        autoPanPadding: [50, 50], // Padding when auto-panning
      });

    // Store node ID in marker for reference
    marker.nodeId = node.id;
    
    // Store device data in marker for popup
    marker.deviceData = node;

    // Add click event to marker
    marker.on("click", function (e) {
      e.originalEvent.preventDefault();
      e.originalEvent.stopPropagation();
      
      // Close other popups
      markers.forEach((m) => {
        if (m !== marker && m.isPopupOpen()) m.closePopup();
      });
      
      // Select this node
      selectNode(node.id);
    });

    // Add hover effects
    marker.on("mouseover", function() {
      const iconDiv = this.getElement();
      if (iconDiv) {
        iconDiv.style.transform = "scale(1.2)";
        iconDiv.style.transition = "transform 0.2s ease";
        iconDiv.style.zIndex = "2000";
      }
    });

    marker.on("mouseout", function() {
      const iconDiv = this.getElement();
      if (iconDiv && selectedNodeId !== this.nodeId) {
        iconDiv.style.transform = "scale(1)";
        iconDiv.style.zIndex = node.hasGPS ? "1000" : "500";
      }
    });

    markers.push(marker);
  });

  // Fit map bounds to show all markers
  if (markers.length > 0) {
    const group = new L.featureGroup(markers);
    map.fitBounds(group.getBounds().pad(0.1));
  }
}

/**
 * Gets color based on device status
 * @param {string} status - Device status (active, warning, inactive)
 * @returns {string} Color code for the status
 */
function getStatusColor(status) {
  switch(status) {
    case "active":
      return "#10B981"; // Green
    case "warning":
      return "#F59E0B"; // Orange/Yellow
    case "inactive":
      return "#EF4444"; // Red
    default:
      return "#6B7280"; // Gray
  }
}

/**
 * Creates HTML content for marker popup with proper light/dark mode styling
 * @param {object} node - Node/device data
 * @returns {string} HTML string for popup content
 */
function createNodePopup(node) {
  const nodeData = nodesData[node.id];
  const device = node.raw_device;
  const hasTelemetry = device && device.telemetry;
  const hasMetrics = device && device.metrics;

  const statusColor = getStatusColor(node.status);
  
  // Get current theme
  const isDarkMode = document.documentElement.classList.contains('dark-mode');

  // ============================================
  // POPUP CONTENT - YOU CAN MODIFY THIS SECTION
  // ============================================
  
  // Telemetry data display - using theme-appropriate classes
  let telemetryInfo = "";
  if (hasTelemetry) {
    telemetryInfo = `
      <div class="grid grid-cols-2 gap-2 mt-2">
        <div class="bg-gray-100 dark:bg-gray-800 p-2 rounded">
          <div class="text-xs text-gray-600 dark:text-gray-400">AC Voltage</div>
          <div class="font-bold text-blue-600 dark:text-blue-400">${device.telemetry.ac_voltage || "0.0"} <span class="text-xs">V</span></div>
        </div>
        <div class="bg-gray-100 dark:bg-gray-800 p-2 rounded">
          <div class="text-xs text-gray-600 dark:text-gray-400">AC Current</div>
          <div class="font-bold text-red-600 dark:text-red-400">${device.telemetry.ac_current || "0.000"} <span class="text-xs">A</span></div>
        </div>
        <div class="bg-gray-100 dark:bg-gray-800 p-2 rounded">
          <div class="text-xs text-gray-600 dark:text-gray-400">AC Power</div>
          <div class="font-bold text-green-600 dark:text-green-400">${device.telemetry.ac_power || "0.0"} <span class="text-xs">W</span></div>
        </div>
        <div class="bg-gray-100 dark:bg-gray-800 p-2 rounded">
          <div class="text-xs text-gray-600 dark:text-gray-400">Energy</div>
          <div class="font-bold text-purple-600 dark:text-purple-400">${device.telemetry.energy || "0.000"} <span class="text-xs">kWh</span></div>
        </div>
        <div class="bg-gray-100 dark:bg-gray-800 p-2 rounded">
          <div class="text-xs text-gray-600 dark:text-gray-400">Frequency</div>
          <div class="font-bold text-indigo-600 dark:text-indigo-400">${device.telemetry.frequency || "0.0"} <span class="text-xs">Hz</span></div>
        </div>
        <div class="bg-gray-100 dark:bg-gray-800 p-2 rounded">
          <div class="text-xs text-gray-600 dark:text-gray-400">Power Factor</div>
          <div class="font-bold text-pink-600 dark:text-pink-400">${device.telemetry.power_factor || "0.000"}</div>
        </div>
        ${device.telemetry.dc_voltage ? `
        <div class="bg-gray-100 dark:bg-gray-800 p-2 rounded">
          <div class="text-xs text-gray-600 dark:text-gray-400">DC Voltage</div>
          <div class="font-bold text-orange-600 dark:text-orange-400">${device.telemetry.dc_voltage} <span class="text-xs">V</span></div>
        </div>` : ''}
        ${device.telemetry.dc_current ? `
        <div class="bg-gray-100 dark:bg-gray-800 p-2 rounded">
          <div class="text-xs text-gray-600 dark:text-gray-400">DC Current</div>
          <div class="font-bold text-amber-600 dark:text-amber-400">${device.telemetry.dc_current} <span class="text-xs">A</span></div>
        </div>` : ''}
      </div>
    `;
  }

  // Metrics display - using theme-appropriate classes
  let metricsInfo = "";
  if (hasMetrics) {
    metricsInfo = `
      <div class="mt-3">
        <div class="text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Device Metrics:</div>
        <div class="grid grid-cols-2 gap-2 text-xs">
          ${device.metrics.carbon_reduction ? `
          <div class="flex justify-between bg-green-100 dark:bg-green-900/30 p-1 rounded">
            <span class="text-gray-700 dark:text-gray-400">CO₂:</span>
            <span class="font-medium text-green-700 dark:text-green-400">${device.metrics.carbon_reduction} kg</span>
          </div>` : ''}
          ${device.metrics.efficiency ? `
          <div class="flex justify-between bg-blue-100 dark:bg-blue-900/30 p-1 rounded">
            <span class="text-gray-700 dark:text-gray-400">Efficiency:</span>
            <span class="font-medium text-blue-700 dark:text-blue-400">${device.metrics.efficiency}%</span>
          </div>` : ''}
          ${device.metrics.battery_percentage != null ? `
          <div class="flex justify-between bg-yellow-100 dark:bg-yellow-900/30 p-1 rounded">
            <span class="text-gray-700 dark:text-gray-400">Battery:</span>
            <span class="font-medium text-yellow-700 dark:text-yellow-400">${device.metrics.battery_percentage}%</span>
          </div>` : ''}
          ${device.metrics.signal_quality ? `
          <div class="flex justify-between bg-purple-100 dark:bg-purple-900/30 p-1 rounded">
            <span class="text-gray-700 dark:text-gray-400">Signal:</span>
            <span class="font-medium text-purple-700 dark:text-purple-400">${device.metrics.signal_quality}</span>
          </div>` : ''}
        </div>
      </div>
    `;
  }

  return `
    <div class="popup-content">
      <!-- Header with device name and status -->
      <div class="flex items-center gap-3 mb-3 pb-2 border-b border-gray-300 dark:border-gray-700">
        <div style="
          width: 40px; 
          height: 40px; 
          background: ${node.color};
          border-radius: 10px;
          transform: rotate(45deg);
          display: flex; 
          align-items: center; 
          justify-content: center;
          box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        ">
          <span style="transform: rotate(-45deg); font-weight: bold; color: white; font-size: 16px;">
            ${node.name.charAt(0)}
          </span>
        </div>
        <div class="flex-1">
          <h3 class="font-bold text-lg text-gray-900 dark:text-white">${node.name}</h3>
          <p class="text-xs text-gray-600 dark:text-gray-400">${node.description || node.location}</p>
        </div>
        <div style="
          width: 12px; 
          height: 12px; 
          border-radius: 50%; 
          background: ${statusColor};
          box-shadow: 0 0 10px ${statusColor};
        "></div>
      </div>

      <!-- Device details -->
      <div class="space-y-2 text-sm">
        <div class="flex justify-between items-center bg-gray-100 dark:bg-gray-800 p-2 rounded">
          <span class="text-gray-700 dark:text-gray-400">Device ID:</span>
          <span class="font-mono font-medium text-gray-900 dark:text-white">${device?.device_id || node.id}</span>
        </div>
        <div class="flex justify-between items-center bg-gray-100 dark:bg-gray-800 p-2 rounded">
          <span class="text-gray-700 dark:text-gray-400">Alias:</span>
          <span class="font-medium text-gray-900 dark:text-white">${device?.device_alias || "N/A"}</span>
        </div>
        <div class="flex justify-between items-center bg-gray-100 dark:bg-gray-800 p-2 rounded">
          <span class="text-gray-700 dark:text-gray-400">Status:</span>
          <span class="font-medium" style="color: ${statusColor}">${device?.status_text || node.status.toUpperCase()}</span>
        </div>
        <div class="flex justify-between items-center bg-gray-100 dark:bg-gray-800 p-2 rounded">
          <span class="text-gray-700 dark:text-gray-400">Location Type:</span>
          ${node.hasGPS
            ? `<span class="font-medium text-green-700 dark:text-green-400">📍 GPS Locked</span>`
            : `<span class="font-medium text-orange-700 dark:text-orange-400">📍 Approximate</span>`
          }
        </div>
      </div>

      <!-- Telemetry data -->
      ${telemetryInfo || `<div class="text-center text-gray-600 dark:text-gray-400 text-sm py-3 bg-gray-100 dark:bg-gray-800 rounded mt-2">No telemetry data available</div>`}

      <!-- Metrics data -->
      ${metricsInfo}

      <!-- Timestamp -->
      <div class="mt-3 pt-2 text-xs text-gray-600 dark:text-gray-500 border-t border-gray-300 dark:border-gray-700 text-center">
        Last Data: ${device?.telemetry?.data_timestamp ? new Date(device.telemetry.data_timestamp).toLocaleString() : "N/A"}
        <br>
        <span class="text-[#FDA300]">⏱️</span> Updated: ${nodeData?.lastUpdated ? new Date(nodeData.lastUpdated).toLocaleTimeString() : "Just now"}
      </div>
    </div>
  `;
}

// ============================================
// END OF MARKER SECTION
// ============================================

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
    updateMapMarkers(); // This will refresh all markers with new data
    // FIX: Update last-updated timestamp
    const elements = getElements();
    if (elements.lastUpdated) {
      elements.lastUpdated.textContent = new Date().toLocaleTimeString();
    }
  } catch (error) {
    console.error("Error updating node data:", error);
  }
}

// --- UI UPDATE FUNCTIONS ---
function updateNodeSelector() {
  const elements = getElements();
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
  const elements = getElements();
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
    const activeCount = ALL_DEVICES.filter((n) => n.status === "active").length;
    elements.activeNodesCount.textContent = activeCount + " / " + (serverSummary.total_devices ?? 0);
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
  updateNodesTable(); // FIX: Update table to show selected row

  if (nodeId === "all") {
    if (markers.length > 0 && map) {
      const group = new L.featureGroup(markers);
      map.fitBounds(group.getBounds().pad(0.1));
    }
  } else {
    highlightSelectedMarker(nodeId);
  }
}

/**
 * Highlights the selected marker on the map
 * @param {string} nodeId - ID of the node to highlight
 */
function highlightSelectedMarker(nodeId) {
  if (!map) return;

  // Reset all markers to normal state
  markers.forEach((marker) => {
    const iconDiv = marker.getElement();
    if (iconDiv) {
      iconDiv.style.transform = "scale(1)";
      iconDiv.style.boxShadow = marker.deviceData?.hasGPS 
        ? "0 0 15px rgba(0,0,0,0.5), 0 0 10px " + getStatusColor(marker.deviceData?.status)
        : "0 0 10px rgba(0,0,0,0.3)";
      iconDiv.style.zIndex = marker.deviceData?.hasGPS ? "1000" : "500";
      iconDiv.style.transition = "all 0.3s ease";
      iconDiv.classList.remove("pulse-animation");
    }
  });

  // Highlight selected marker
  const selectedMarker = markers.find((m) => m.nodeId === nodeId);
  if (selectedMarker) {
    // Fly to selected marker
    setTimeout(() => {
      if (map) {
        map.flyTo(selectedMarker.getLatLng(), 15, {
          duration: 1,
          easeLinearity: 0.25,
        });
        setTimeout(() => {
          selectedMarker.openPopup();
        }, 1000);
      }
    }, 100);

    // Enhance marker appearance
    const iconDiv = selectedMarker.getElement();
    if (iconDiv) {
      iconDiv.style.transform = "scale(1.5)";
      iconDiv.style.boxShadow = "0 0 25px rgba(253, 163, 0, 0.8), 0 0 15px " + getStatusColor(selectedMarker.deviceData?.status);
      iconDiv.style.zIndex = "2000";
      iconDiv.style.transition = "all 0.3s ease";
      iconDiv.classList.add("pulse-animation");
      
      // Remove pulse after animation
      setTimeout(() => {
        iconDiv.classList.remove("pulse-animation");
      }, 2000);
    }
  }
}

// Add CSS for pulse animation and enhanced styles with proper light/dark mode support
const style = document.createElement("style");
style.textContent = `
  .pulse-animation { 
    animation: pulse 1.5s ease-in-out infinite; 
  }
  
  @keyframes pulse {
    0% { 
      box-shadow: 0 0 0 0 rgba(253, 163, 0, 0.7), 0 0 0 0 rgba(253, 163, 0, 0.4);
    }
    70% { 
      box-shadow: 0 0 0 15px rgba(253, 163, 0, 0), 0 0 0 30px rgba(253, 163, 0, 0);
    }
    100% { 
      box-shadow: 0 0 0 0 rgba(253, 163, 0, 0), 0 0 0 0 rgba(253, 163, 0, 0);
    }
  }
  
  .node-row.active {
    background-color: rgba(253, 163, 0, 0.1) !important;
    border-left: 3px solid #FDA300;
  }
  
  .node-row {
    cursor: pointer;
    transition: all 0.2s ease;
  }
  
  .node-row:hover {
    background-color: rgba(253, 163, 0, 0.05);
  }

  /* Popup styling - Light mode default */
  .custom-popup .leaflet-popup-content-wrapper {
    border-radius: 12px;
    padding: 0;
    overflow: hidden;
    box-shadow: 0 5px 20px rgba(0,0,0,0.2);
    background: white;
    color: #111827;
    max-width: 350px !important;
    max-height: 400px !important;
    overflow-y: auto;
  }

  .custom-popup .leaflet-popup-content {
    margin: 0;
    min-width: 280px;
    max-width: 350px;
    width: auto !important;
  }

  .custom-popup .leaflet-popup-tip {
    background: white;
    box-shadow: 0 5px 20px rgba(0,0,0,0.2);
  }

  /* Dark mode popup styling */
  .dark-mode .custom-popup .leaflet-popup-content-wrapper {
    background: #1F2937;
    color: #F3F4F6;
  }

  .dark-mode .custom-popup .leaflet-popup-tip {
    background: #1F2937;
  }

  .popup-content {
    padding: 16px;
    max-height: 400px;
    overflow-y: auto;
  }

  /* Scrollbar styling for popup */
  .popup-content::-webkit-scrollbar {
    width: 6px;
  }

  .popup-content::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
  }

  .popup-content::-webkit-scrollbar-thumb {
    background: #FDA300;
    border-radius: 10px;
  }

  .dark-mode .popup-content::-webkit-scrollbar-track {
    background: #374151;
  }

  /* Marker hover effect */
  .node-marker:hover {
    filter: brightness(1.2);
    transition: filter 0.2s ease;
  }

  /* Ensure popup doesn't cover more than 70% of map */
  .leaflet-popup {
    max-width: 90vw !important;
  }

  @media (min-width: 768px) {
    .leaflet-popup {
      max-width: 350px !important;
    }
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
      const elements = getElements();
      if (elements.authStatus) {
        elements.authStatus.textContent = "Please login first";
      }
      return;
    }

    const elements = getElements();
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
    if (simulationInterval) clearInterval(simulationInterval);
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
      const user = JSON.parse(localStorage.getItem("user") || "{}");
      elements.authStatus.innerHTML = `<i class="fas fa-shield-alt text-[#FDA300]"></i>Connected as: <span id="current-role" class="text-[#FDA300]">${user.user_tipe || "ADMIN"}</span>`;
    }

    console.log("Inverter dashboard initialized with server data");
  } catch (error) {
    console.error("Failed to initialize application:", error);
    const elements = getElements();
    if (elements.authStatus) {
      elements.authStatus.textContent = "Failed to load data";
    }
  }
}

// --- GLOBAL FUNCTIONS ---

// Refresh data from server (callable from HTML button)
window.refreshData = async function () {
  if (simulationInterval) clearInterval(simulationInterval);

  const elements = getElements();
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
      const user = JSON.parse(localStorage.getItem("user") || "{}");
      elements.authStatus.innerHTML = `<i class="fas fa-shield-alt text-[#FDA300]"></i>Connected as: <span id="current-role" class="text-[#FDA300]">${user.user_tipe || "ADMIN"}</span>`;
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