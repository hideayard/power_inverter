// --- GLOBAL STATE AND CONFIGURATION ---
let simulationInterval;
let map = null;
let markers = {};
let viewMode = "avg"; // 'avg' or 'total'
let selectedNodeId = "all";
let serverUpdateInterval = null;

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
  selectedNode: document.getElementById("selected-node"),
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

// Start periodic server updates (every 10 seconds for faster updates)
function startServerUpdates() {
  if (serverUpdateInterval) {
    clearInterval(serverUpdateInterval);
  }

  // Update from server every 10 seconds
  serverUpdateInterval = setInterval(async () => {
    try {
      await updateDataFromServer();
    } catch (error) {
      console.error("Server update failed:", error);
    }
  }, 10000); // 10 seconds
}

// Update data from server
async function updateDataFromServer() {
  try {
    const devices = await fetchDevicesFromServer();

    if (devices.length === 0) {
      // No data from server, show empty state
      updateEmptyState();
      return;
    }

    // Convert to NODES format
    const newNODES = convertDevicesToNodes(devices);

    if (newNODES.length === 0) {
      updateEmptyState();
      return;
    }

    // Update NODES array
    NODES = newNODES;

    // Update node data
    NODES.forEach((node) => {
      const device = node.raw_device;
      const hasTelemetry = device.telemetry !== null;

      // Extract values from device telemetry
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

      // Calculate battery percentage from metrics or DC voltage
      let batterySoC = 0;
      if (device.metrics?.battery_percentage > 0) {
        batterySoC = device.metrics.battery_percentage / 100;
      } else if (device.telemetry?.dc_voltage) {
        const dcVoltage = parseFloat(device.telemetry.dc_voltage);
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

    // Update all UI components
    await updateAllUI();
  } catch (error) {
    console.error("Failed to update data from server:", error);
    updateEmptyState();
  }
}

// Convert server device data to NODES format
function convertDevicesToNodes(devices) {
  return devices.map((device, index) => {
    // Extract location from device
    let latitude = 1.5551 + (Math.random() * 0.05 - 0.025); // Random near Taman U
    let longitude = 103.7158 + (Math.random() * 0.05 - 0.025);

    if (
      device.location &&
      device.location.latitude &&
      device.location.longitude
    ) {
      latitude = parseFloat(device.location.latitude);
      longitude = parseFloat(device.location.longitude);
    }

    // Determine status from device data
    let status = "inactive";
    if (device.status === "active" || device.telemetry) {
      status = "active";
    } else if (device.status === "warning") {
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
      id: `node-${index + 1}`,
      server_id: device.device_id,
      name: deviceName,
      location: locationText,
      latitude: latitude,
      longitude: longitude,
      status: status,
      color: color,
      raw_device: device, // Store original device data
    };
  });
}

// Initialize data from server devices
async function initializeDataFromServer() {
  try {
    // Fetch devices from server
    const devices = await fetchDevicesFromServer();

    if (devices.length === 0) {
      updateEmptyState();
      return;
    }

    // Convert to NODES format
    NODES = convertDevicesToNodes(devices);

    console.log(`Initialized ${NODES.length} nodes from server`);

    // Initialize node data
    NODES.forEach((node) => {
      const device = node.raw_device;
      const hasTelemetry = device.telemetry !== null;

      // Extract values from device telemetry
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

      // Calculate battery percentage from metrics or DC voltage
      let batterySoC = 0;
      if (device.metrics?.battery_percentage > 0) {
        batterySoC = device.metrics.battery_percentage / 100;
      } else if (device.telemetry?.dc_voltage) {
        const dcVoltage = parseFloat(device.telemetry.dc_voltage);
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
    updateEmptyState();
  }
}

// Update empty state when no data
function updateEmptyState() {
  NODES = [];
  nodesData = {};
  markers = {};

  // Update UI to show no data
  updateAllUI();

  // Show empty state message
  if (elements.authStatus) {
    elements.authStatus.innerHTML =
      '<i class="fas fa-exclamation-triangle mr-2"></i>No devices found';
  }
}

// Update all UI components
async function updateAllUI() {
  // Update table first (fastest)
  updateNodesTable();
  updateAggregateMetrics();
  updateNodeSelector();

  // Update map markers (optimized)
  // await updateMapMarkersOptimized();
  updateMapMarkersOptimized();
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
  // Set default view to Taman U, Johor Bahru
  map = L.map("map").setView([1.5551, 103.7158], 12);

  L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
    attribution: "© OpenStreetMap contributors",
    maxZoom: 19,
  }).addTo(map);

  // Add markers after NODES are populated
  updateMapMarkersOptimized();
}

// Optimized map marker updates
async function updateMapMarkersOptimized() {
  if (!map) return;

  if (NODES.length === 0) {
    // Clear all markers
    Object.values(markers).forEach((marker) => {
      if (map.hasLayer(marker)) {
        map.removeLayer(marker);
      }
    });
    markers = {};

    // Reset to default view
    map.setView([1.5551, 103.7158], 12);

    if (elements.coordinatesText) {
      elements.coordinatesText.textContent = "No devices configured";
    }
    return;
  }

  // Track which nodes still exist
  const existingNodeIds = new Set(NODES.map((node) => node.id));

  // Remove markers for nodes that no longer exist
  Object.keys(markers).forEach((nodeId) => {
    if (!existingNodeIds.has(nodeId)) {
      if (map.hasLayer(markers[nodeId])) {
        map.removeLayer(markers[nodeId]);
      }
      delete markers[nodeId];
    }
  });

  // Add or update markers for current nodes
  NODES.forEach((node) => {
    const nodeData = nodesData[node.id];

    if (markers[node.id]) {
      // Update existing marker
      const marker = markers[node.id];

      // Update position if changed
      const currentLatLng = marker.getLatLng();
      const newLatLng = [node.latitude, node.longitude];

      if (
        currentLatLng.lat !== node.latitude ||
        currentLatLng.lng !== node.longitude
      ) {
        marker.setLatLng(newLatLng);
      }

      // Update popup content
      marker.setPopupContent(createNodePopup(node));

      // Update marker appearance based on status
      updateMarkerAppearance(marker, node);
    } else {
      // Create new marker
      const icon = L.divIcon({
        className: "node-marker",
        html: createMarkerHTML(node),
        iconSize: [24, 24],
        iconAnchor: [12, 12],
      });

      const marker = L.marker([node.latitude, node.longitude], {
        icon,
        title: node.name,
      })
        .addTo(map)
        .bindPopup(createNodePopup(node));

      marker.nodeId = node.id;
      marker.on("click", function () {
        selectNode(node.id);
      });

      markers[node.id] = marker;
    }

    if (marker._lastStatus !== node.status) {
      updateMarkerAppearance(marker, node);
      marker._lastStatus = node.status;
    }
  });

  // Fit bounds to show all markers if we have markers
  const markerArray = Object.values(markers);
  // if (markerArray.length > 0) {
  //   const group = new L.featureGroup(markerArray);
  //   map.fitBounds(group.getBounds().pad(0.1));
  // }

  if (!mapBoundsInitialized && markerArray.length > 0) {
    const group = new L.featureGroup(markerArray);
    map.fitBounds(group.getBounds().pad(0.1));
    mapBoundsInitialized = true;
  }

  // Force map redraw
  // map.invalidateSize();

  return Promise.resolve();
}

// Create marker HTML
function createMarkerHTML(node) {
  const statusColor =
    node.status === "active"
      ? "#10B981"
      : node.status === "warning"
        ? "#F59E0B"
        : "#EF4444";

  return `
    <div style="
      background-color: ${node.color}; 
      width: 24px; 
      height: 24px; 
      border-radius: 50%; 
      border: 3px solid ${statusColor}; 
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.5); 
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: bold;
      color: white;
      font-size: 10px;
      transition: all 0.3s ease;
    ">
      ${node.name.charAt(0)}
    </div>
  `;
}

// Update marker appearance
function updateMarkerAppearance(marker, node) {
  const statusColor =
    node.status === "active"
      ? "#10B981"
      : node.status === "warning"
        ? "#F59E0B"
        : "#EF4444";

  const iconDiv = marker.getElement();
  if (iconDiv) {
    const innerDiv = iconDiv.querySelector("div");
    if (innerDiv) {
      innerDiv.style.borderColor = statusColor;
    }
  }
}

// --- NODE MANAGEMENT FUNCTIONS ---
function initializeNodes() {
  // Update UI
  updateNodeSelector();
  updateNodesTable();
  updateAggregateMetrics();
}

// --- UI UPDATE FUNCTIONS ---
function updateNodeSelector() {
  const nodeSelector = elements.nodeSelector;
  if (!nodeSelector) return;

  nodeSelector.innerHTML = "";

  if (NODES.length === 0) {
    const noDataButton = document.createElement("button");
    noDataButton.className =
      "px-4 py-2 rounded-lg font-medium bg-gray-700 text-gray-400";
    noDataButton.textContent = "No Devices Found";
    noDataButton.disabled = true;
    nodeSelector.appendChild(noDataButton);
  } else {
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

  // Update dropdown
  const selectElement = elements.selectedNode;
  if (selectElement) {
    selectElement.innerHTML = "";

    if (NODES.length === 0) {
      const option = document.createElement("option");
      option.value = "";
      option.textContent = "No Devices Available";
      option.disabled = true;
      selectElement.appendChild(option);
    } else {
      const allOption = document.createElement("option");
      allOption.value = "all";
      allOption.textContent = "All Nodes (Aggregate)";
      selectElement.appendChild(allOption);

      NODES.forEach((node) => {
        const option = document.createElement("option");
        option.value = node.id;
        option.textContent = node.name;
        selectElement.appendChild(option);
      });
      selectElement.value = selectedNodeId;
    }
  }
}

function updateNodesTable() {
  const tbody = elements.nodesTableBody;
  if (!tbody) return;

  tbody.innerHTML = "";

  if (NODES.length === 0) {
    const row = document.createElement("tr");
    row.className = "no-data-row";
    row.innerHTML = `
      <td colspan="6" class="px-4 py-8 text-center text-gray-400">
        <i class="fas fa-database mr-2"></i>No device data available
      </td>
    `;
    tbody.appendChild(row);
  } else {
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
              nodeData.power,
            ).toFixed(1)}</td>
            <td class="px-4 py-3">${nodeData.voltage.toFixed(1)}</td>
            <td class="px-4 py-3">${nodeData.current.toFixed(2)}</td>
        `;

      // Add click event with proper node selection
      row.addEventListener("click", (e) => {
        selectNode(node.id);
      });

      tbody.appendChild(row);
    });
  }

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
    (sum, id) => sum + nodesData[id].energy,
    0,
  );
  const totalCarbon = nodeIds.reduce(
    (sum, id) => sum + parseFloat(nodesData[id].carbonReduction),
    0,
  );
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
          <div class="font-bold text-indigo-500">${device.telemetry.frequency || "50.0"} <span class="text-xs">Hz</span></div>
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

  // Zoom to show all markers or highlight selected
  if (nodeId === "all") {
    const markerArray = Object.values(markers);
    if (markerArray.length > 0 && map) {
      const group = new L.featureGroup(markerArray);
      map.fitBounds(group.getBounds().pad(0.1));
    }
  } else {
    // Find and highlight the selected marker
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

  // Update dropdown
  if (elements.selectedNode) {
    elements.selectedNode.value = nodeId;
  }
}

function highlightSelectedMarker(nodeId) {
  if (!map) return;

  // Reset all markers to normal
  Object.values(markers).forEach((marker) => {
    const iconDiv = marker.getElement();
    if (iconDiv) {
      iconDiv.style.transform = "scale(1)";
      iconDiv.style.zIndex = "1";
    }
  });

  // Find and highlight the selected marker
  const selectedMarker = markers[nodeId];
  if (selectedMarker) {
    // Zoom to the selected marker
    map.setView(selectedMarker.getLatLng(), 15);

    // Highlight the marker
    const iconDiv = selectedMarker.getElement();
    if (iconDiv) {
      iconDiv.style.transform = "scale(1.5)";
      iconDiv.style.zIndex = "1000";
      iconDiv.style.transition = "all 0.3s ease";
      iconDiv.style.filter = "drop-shadow(0 0 10px rgba(251, 191, 36, 0.8))";
    }

    // Open popup
    selectedMarker.openPopup();

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
            filter: drop-shadow(0 0 0 rgba(251, 191, 36, 0.7));
        }
        70% {
            filter: drop-shadow(0 0 10px rgba(251, 191, 36, 0.4));
        }
        100% {
            filter: drop-shadow(0 0 0 rgba(251, 191, 36, 0));
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

    // Initialize map
    initMap();

    // Initialize map toggle
    setupMapToggle();

    // Initialize data from server
    await initializeDataFromServer();

    // Initialize nodes
    initializeNodes();

    // Set initial display
    if (NODES.length > 0) {
      selectNode("all");
    }

    // Start periodic server updates
    startServerUpdates();

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

    if (elements.selectedNode) {
      elements.selectedNode.onchange = function () {
        selectNode(this.value);
      };
    }

    if (elements.authStatus) {
      elements.authStatus.textContent =
        NODES.length > 0 ? "Authenticated" : "No devices found";
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
  if (serverUpdateInterval) {
    clearInterval(serverUpdateInterval);
  }

  if (elements.authStatus) {
    elements.authStatus.textContent = "Refreshing data...";
  }

  await updateDataFromServer();

  // Restart server updates
  startServerUpdates();

  if (elements.authStatus) {
    elements.authStatus.textContent =
      NODES.length > 0 ? "Data refreshed" : "No devices found";
    setTimeout(() => {
      elements.authStatus.textContent =
        NODES.length > 0 ? "Authenticated" : "No devices found";
    }, 2000);
  }
};

// --- START APPLICATION ---
document.addEventListener("DOMContentLoaded", initApplication);
