// --- GLOBAL STATE AND CONFIGURATION ---
let simulationInterval;
let map = null;
let markers = [];
let viewMode = "avg"; // 'avg' or 'total'
let selectedNodeId = "all";

// Store fetched devices globally
let devicesData = [];
let NODES = [];

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
    const authToken = localStorage.getItem("authToken");
    if (!authToken) {
      console.error("No authentication token available");
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
      return result.data;
    } else {
      throw new Error(result.message || "Failed to fetch devices from server");
    }
  } catch (error) {
    console.error("Error fetching devices:", error);
    throw error;
  }
}

// Convert server device data to NODES format
function convertDevicesToNodes(devices) {
  return devices
    .map((device, index) => {
      // Extract location from device
      let latitude = null;
      let longitude = null;

      if (
        device.location &&
        device.location.latitude &&
        device.location.longitude
      ) {
        latitude = parseFloat(device.location.latitude);
        longitude = parseFloat(device.location.longitude);
      } else {
        // If no location, don't create a node for map
        return null;
      }

      // Determine status from device data
      let status = "active";
      if (device.status === "warning" || device.metrics?.status === "warning") {
        status = "warning";
      } else if (device.status === "offline" || !device.telemetry) {
        status = "inactive";
      } else if (device.metrics?.battery_percentage < 30) {
        status = "warning";
      }

      // Use random color
      const color = generateRandomColor();

      // Get device name
      const deviceName =
        device.device_alias ||
        device.device_name ||
        device.telemetry?.device_name_from_telemetry ||
        `Device ${index + 1}`;

      // Get location text
      const locationText =
        device.location?.formatted || device.location?.latitude
          ? `${device.location.latitude}, ${device.location.longitude}`
          : "Unknown Location";

      return {
        id: `node-${device.device_id || index + 1}`,
        server_id: device.device_id,
        name: deviceName,
        location: locationText,
        latitude: latitude,
        longitude: longitude,
        status: status,
        color: color,
        raw_device: device, // Store original device data
      };
    })
    .filter((node) => node !== null); // Filter out nodes without location
}

// Initialize data from server devices
async function initializeDataFromServer() {
  try {

    // Fetch devices from server
    const devices = await fetchDevicesFromServer();

    // Convert to NODES format
    NODES = convertDevicesToNodes(devices);

    if (NODES.length === 0) {
      console.warn("No devices with location data found from server");
        return;
    }

    console.log(`Initialized ${NODES.length} nodes from server`);

    // Initialize node data
    NODES.forEach((node) => {
      const device = node.raw_device;
      const hasTelemetry = device.telemetry !== null;

      // Extract values from device telemetry
      const voltage = hasTelemetry
        ? parseFloat(device.telemetry.ac_voltage || "0.0")
        : 0.0;
      const current = hasTelemetry
        ? parseFloat(device.telemetry.ac_current || "0.0")
        : 0.0;
      const pf = hasTelemetry
        ? parseFloat(device.telemetry.power_factor || "0.0")
        : 0.0;
      const power = hasTelemetry
        ? parseFloat(device.telemetry.ac_power || "0")
        : 0;
      const energy = hasTelemetry
        ? parseFloat(device.telemetry.energy || "0")
        : 0;
      const frequency = hasTelemetry
        ? parseFloat(device.telemetry.frequency || "0.0")
        : 0.0;

      // Calculate battery percentage from metrics or DC voltage
      let batterySoC = 0.0; // Default
      if (device.metrics?.battery_percentage > 0) {
        batterySoC = device.metrics.battery_percentage / 100;
      } else if (device.telemetry?.dc_voltage) {
        const dcVoltage = parseFloat(device.telemetry.dc_voltage);
        // Simple estimation: 12V battery = 0%, 13.8V battery = 100%
        batterySoC = Math.max(0, Math.min(1, (dcVoltage - 12) / 1.8));
      }

      nodesData[node.id] = {
        ...node,
        voltage: voltage,
        current: current,
        power:
          power > 0 ? power : parseFloat((voltage * current * pf).toFixed(1)),
        energy: energy,
        frequency: frequency,
        pf: pf,
        batterySoC: batterySoC,
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


  } catch (error) {
    console.error("Failed to initialize data from server:", error);
  }
}




// Calculate battery time from SoC
function calculateBatteryTime(batterySoC) {
  if (batterySoC <= 0) return "00:00";

  const remainingHours = batterySoC * 8; // 8 hours at full capacity
  const hours = Math.floor(remainingHours);
  const minutes = Math.floor((remainingHours % 1) * 60);
  return `${String(hours).padStart(2, "0")}:${String(minutes).padStart(2, "0")}`;
}

// --- INITIALIZE MAP ---
function initMap() {
  // Set default view to Malaysia
  map = L.map("map").setView([4.2105, 101.9758], 6);

  L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
    attribution: "© OpenStreetMap contributors",
    maxZoom: 18, // Reduced from 19 to prevent excessive zoom
    minZoom: 3,
  }).addTo(map);

  // Force map resize after a short delay to ensure tiles load properly
  setTimeout(() => {
    if (map) {
      map.invalidateSize();
    }
  }, 300);

  // Add markers after NODES are populated
  updateMapMarkers();
}

// Update the map marker creation to ensure proper positioning
// --- MAP FUNCTIONS ---
function updateMapMarkers() {
  // Clear existing markers
  markers.forEach((marker) => {
    if (map && map.hasLayer(marker)) {
      map.removeLayer(marker);
    }
  });
  markers = [];

  if (!map || NODES.length === 0) return;

  // Add markers for each node
  NODES.forEach((node) => {
    // Ensure coordinates are valid numbers
    if (isNaN(node.latitude) || isNaN(node.longitude)) {
      console.warn(
        `Invalid coordinates for node ${node.id}:`,
        node.latitude,
        node.longitude,
      );
      return;
    }

    const icon = L.divIcon({
      className: "node-marker",
      html: `<div style="background-color: ${node.color}; width: 24px; height: 24px; border-radius: 50%; border: 3px solid white; box-shadow: 0 0 10px rgba(0, 0, 0, 0.5); cursor: pointer; position: relative;"></div>`,
      iconSize: [24, 24],
      iconAnchor: [12, 12], // Center the anchor point
      popupAnchor: [0, -12], // Position popup above marker
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

    // Fix marker click event
    marker.on("click", function (e) {
      e.originalEvent.preventDefault();
      e.originalEvent.stopPropagation();

      // Close all other popups
      markers.forEach((m) => {
        if (m !== marker && m.isPopupOpen()) {
          m.closePopup();
        }
      });

      selectNode(node.id);
    });

    markers.push(marker);
  });

  // Fit bounds to show all markers
  if (markers.length > 0) {
    const group = new L.featureGroup(markers);
    map.fitBounds(group.getBounds().pad(0.1));
  }
}

// --- NODE MANAGEMENT FUNCTIONS ---
function initializeNodes() {
  // Update UI
  updateNodeSelector();
  updateNodesTable();
  updateAggregateMetrics();
}

async function updateNodeData() {
  try {
    // Fetch fresh data from server
    await initializeDataFromServer();

    // Update UI
    updateNodesTable();
    updateAggregateMetrics();
    updateMapMarkers();

  } catch (error) {
    console.error("Error updating node data:", error);
  }
}

// --- UI UPDATE FUNCTIONS ---
function updateNodeSelector() {
  const nodeSelector = elements.nodeSelector;
  if (!nodeSelector) return;

  nodeSelector.innerHTML = "";

  // Add "All Nodes" button
  const allButton = document.createElement("button");
  allButton.className = `px-4 py-2 rounded-lg font-medium ${
    selectedNodeId === "all"
      ? "bg-blue-600 text-white"
      : "bg-gray-700 text-gray-300 hover:bg-gray-600"
  }`;
  allButton.textContent = "All Nodes";
  allButton.onclick = () => selectNode("all");
  nodeSelector.appendChild(allButton);

  // Add buttons for each node
  NODES.forEach((node) => {
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

  if (NODES.length === 0) {
    const row = document.createElement("tr");
    row.innerHTML = `
      <td colspan="6" class="px-4 py-8 text-center text-gray-400">
        <i class="fas fa-exclamation-circle mr-2"></i>No devices available
      </td>
    `;
    tbody.appendChild(row);
    return;
  }

  NODES.forEach((node) => {
    const nodeData = nodesData[node.id];
    const row = document.createElement("tr");
    row.className = `node-row ${selectedNodeId === node.id ? "active" : ""}`;

    const statusColor =
      node.status === "active"
        ? "bg-green-500"
        : node.status === "warning"
          ? "bg-yellow-500"
          : "bg-red-500";

    row.innerHTML = `
            <td class="px-4 py-3 font-medium">${node.id}</td>
            <td class="px-4 py-3">
                <div class="font-medium">${node.name}</div>
                <div class="text-xs text-gray-400">${node.location}</div>
            </td>
            <td class="px-4 py-3">
                <div class="flex items-center">
                    <div class="w-2 h-2 ${statusColor} rounded-full mr-2"></div>
                    <span class="capitalize">${node.status}</span>
                </div>
            </td>
            <td class="px-4 py-3 font-bold">${parseFloat(
              nodeData.power || 0,
            ).toFixed(1)}</td>
            <td class="px-4 py-3">${(nodeData.voltage || 0).toFixed(1)}</td>
            <td class="px-4 py-3">${(nodeData.current || 0).toFixed(2)}</td>
        `;

    // Add click event with proper node selection
    row.addEventListener("click", (e) => {
      selectNode(node.id);
    });

    tbody.appendChild(row);
  });

  if (elements.totalNodes) {
    elements.totalNodes.textContent = NODES.length;
  }

  if (elements.activeNodesCount) {
    elements.activeNodesCount.textContent = NODES.filter(
      (n) => n.status === "active",
    ).length;
  }
}

function updateAggregateMetrics() {
  if (NODES.length === 0) return;

  // Calculate aggregate values
  const nodeIds = Object.keys(nodesData);
  const totalEnergy = nodeIds.reduce(
    (sum, id) => sum + (nodesData[id].energy || 0),
    0,
  );
  const totalCarbon = nodeIds.reduce(
    (sum, id) => sum + parseFloat(nodesData[id].carbonReduction || 0),
    0,
  );
}



// --- MAP FUNCTIONS ---
function updateMapMarkers() {
  // Clear existing markers
  markers.forEach((marker) => {
    if (map && map.hasLayer(marker)) {
      map.removeLayer(marker);
    }
  });
  markers = [];

  if (!map || NODES.length === 0) return;

  // Add markers for each node
  NODES.forEach((node) => {
    const icon = L.divIcon({
      className: "node-marker",
      html: `<div style="background-color: ${node.color}; width: 20px; height: 20px; border-radius: 50%; border: 3px solid white; box-shadow: 0 0 10px rgba(0, 0, 0, 0.5); cursor: pointer;"></div>`,
      iconSize: [20, 20],
      iconAnchor: [10, 10],
    });

    const marker = L.marker([node.latitude, node.longitude], {
      icon,
    })
      .addTo(map)
      .bindPopup(createNodePopup(node));

    marker.nodeId = node.id;
    marker.on("click", function () {
      selectNode(node.id);
    });

    markers.push(marker);
  });

  // Fit bounds to show all markers
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
        </div>
        `
            : ""
        }
        ${
          device.telemetry.dc_current
            ? `
        <div>
          <div class="text-xs text-gray-500">DC Current</div>
          <div class="font-bold text-amber-500">${device.telemetry.dc_current} <span class="text-xs">A</span></div>
        </div>
        `
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
          </div>
          `
              : ""
          }
          ${
            device.metrics.efficiency
              ? `
          <div class="flex justify-between">
            <span class="text-gray-600">Efficiency:</span>
            <span class="font-medium">${device.metrics.efficiency}%</span>
          </div>
          `
              : ""
          }
          ${
            device.metrics.battery_percentage
              ? `
          <div class="flex justify-between">
            <span class="text-gray-600">Battery:</span>
            <span class="font-medium">${device.metrics.battery_percentage}%</span>
          </div>
          `
              : ""
          }
          ${
            device.metrics.signal_quality
              ? `
          <div class="flex justify-between">
            <span class="text-gray-600">Signal:</span>
            <span class="font-medium">${device.metrics.signal_quality}</span>
          </div>
          `
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
          width: 20px;
          height: 20px;
          background: ${node.color};
          border-radius: 3px;
          transform: rotate(45deg);
          display: flex;
          align-items: center;
          justify-content: center;
        ">
          <span style="transform: rotate(-45deg); font-weight: bold; color: white; font-size: 10px;">
            ${node.name.charAt(0)}
          </span>
        </div>
        <div>
          <h3 class="font-bold text-lg">${node.name}</h3>
          <p class="text-sm text-gray-600">${node.location}</p>
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
        <hr class="my-1 border-gray-200">
        
        ${
          telemetryInfo ||
          `
        <div class="text-center text-gray-500 text-sm py-2">
          No telemetry data available
        </div>
        `
        }
        
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

// --- EVENT HANDLERS ---
function selectNode(nodeId) {
  selectedNodeId = nodeId;

  // Update UI
  updateNodeSelector();

  // Update map
  if (nodeId === "all") {
    if (markers.length > 0 && map) {
      const group = new L.featureGroup(markers);
      map.fitBounds(group.getBounds().pad(0.1));
    }
  } else {
    highlightSelectedMarker(nodeId);
  }

  // Update table selection
  const rows = document.querySelectorAll(".node-row");
  rows.forEach((row) => {
    row.classList.remove("active");
    if (row.cells[0].textContent === nodeId) {
      row.classList.add("active");
    }
  });

}

function highlightSelectedMarker(nodeId) {
  if (!map) return;

  // Reset all markers to normal
  markers.forEach((marker) => {
    const iconDiv = marker.getElement();
    if (iconDiv) {
      iconDiv.style.transform = "scale(1)";
      iconDiv.style.boxShadow = "0 0 10px rgba(0,0,0,0.5)";
      iconDiv.style.zIndex = "1";
    }
  });

  // Find and highlight the selected marker
  const selectedMarker = markers.find((m) => m.nodeId === nodeId);
  if (selectedMarker) {
    // IMPORTANT FIX: Wait for map to be ready and invalidate size before zooming
    setTimeout(() => {
      if (map) {
        // Get marker position
        const markerLatLng = selectedMarker.getLatLng();

        // Set zoom level to 13 (moderate zoom, not too close)
        const targetZoom = 13;

        // Fly to position with smooth animation
        map.flyTo(markerLatLng, targetZoom, {
          duration: 1, // 1 second animation
          easeLinearity: 0.25,
        });

        // After animation, open popup
        setTimeout(() => {
          selectedMarker.openPopup();
        }, 1000); // Wait for animation to complete
      }
    }, 100);

    // Highlight the marker immediately
    const iconDiv = selectedMarker.getElement();
    if (iconDiv) {
      iconDiv.style.transform = "scale(1.3)";
      iconDiv.style.boxShadow = "0 0 25px rgba(251, 191, 36, 0.8)";
      iconDiv.style.zIndex = "1000";
      iconDiv.style.transition = "all 0.3s ease";
    }

    // Add pulsing animation
    if (iconDiv) {
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
    .pulse-animation {
        animation: pulse 1.5s ease-in-out infinite;
    }
    
    @keyframes pulse {
        0% {
            box-shadow: 0 0 0 0 rgba(251, 191, 36, 0.7);
        }
        70% {
            box-shadow: 0 0 0 10px rgba(251, 191, 36, 0);
        }
        100% {
            box-shadow: 0 0 0 0 rgba(251, 191, 36, 0);
        }
    }
`;
document.head.appendChild(style);

// --- INITIALIZATION ---
async function initApplication() {
  try {
    // Check authentication
    const authToken = localStorage.getItem("authToken");
    if (!authToken) {
      console.error("No authentication token found");
      if (elements.authStatus) {
        elements.authStatus.textContent = "Please login first";
      }
      return;
    }

    if (elements.authStatus) {
      elements.authStatus.textContent = "Loading data...";
    }

    // Initialize data from server
    await initializeDataFromServer();

    // Initialize map
    initMap();

    // Initialize map toggle
    setupMapToggle();

    // Initialize nodes
    initializeNodes();


    // Set initial display
    selectNode("all");

    // Start simulation with 30-second intervals
    simulationInterval = setInterval(updateNodeData, 30000);

    // Set up event listeners
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
      elements.authStatus.textContent = "Authenticated";
    }

    console.log("Multi-node dashboard initialized with server data");
  } catch (error) {
    console.error("Failed to initialize application:", error);
    if (elements.authStatus) {
      elements.authStatus.textContent = "Failed to load data";
    }
  }
}

// --- GLOBAL FUNCTIONS ---

// Refresh data from server
window.refreshData = async function () {
  if (simulationInterval) {
    clearInterval(simulationInterval);
  }

  if (elements.authStatus) {
    elements.authStatus.textContent = "Refreshing data...";
  }

  await initializeDataFromServer();
  updateNodeSelector();
  updateNodesTable();
  updateAggregateMetrics();
  updateMapMarkers();

  // Restart interval with 30 seconds
  simulationInterval = setInterval(updateNodeData, 30000);

  if (elements.authStatus) {
    elements.authStatus.textContent = "Data refreshed";
    setTimeout(() => {
      elements.authStatus.textContent = "Authenticated";
    }, 2000);
  }
};

// --- START APPLICATION ---
document.addEventListener("DOMContentLoaded", initApplication);
