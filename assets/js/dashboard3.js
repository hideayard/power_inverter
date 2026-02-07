// --- GLOBAL STATE AND CONFIGURATION ---
let simulationInterval;
let map = null;
let markers = [];
let powerChart;
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
let historicalData = {};
const MAX_HISTORY_POINTS = 30;

// Configuration for the chart metrics
const METRIC_CONFIG = {
  voltage: {
    label: "Voltage",
    unit: "Volts (V)",
    color: "#06b6d4",
    checked: true,
  },
  current: {
    label: "Current",
    unit: "Amperes (A)",
    color: "#f87171",
    checked: true,
  },
  power: {
    label: "Active Power",
    unit: "Watts (W)",
    color: "#10b981",
    checked: true,
  },
  pf: {
    label: "Power Factor",
    unit: "Ratio",
    color: "#818cf8",
    checked: false,
  },
};

// Simulation constants
const CARBON_REDUCTION_FACTOR = 0.585;

// DOM Element Mapping
const elements = {
  voltage: document.getElementById("voltage-value"),
  current: document.getElementById("current-value"),
  power: document.getElementById("power-value"),
  energy: document.getElementById("energy-value"),
  frequency: document.getElementById("frequency-value"),
  pf: document.getElementById("pf-value"),
  batteryTime: document.getElementById("battery-time-value"),
  batterySoC: document.getElementById("battery-soc-percent"),
  batteryBar: document.getElementById("battery-bar"),
  carbonReduction: document.getElementById("carbon-reduction-value"),
  coordinatesText: document.getElementById("coordinates-text"),
  authStatus: document.getElementById("auth-status"),
  lastUpdated: document.getElementById("last-updated-time"),
  paramSelector: document.getElementById("param-selector"),
  nodeSelector: document.getElementById("node-selector"),
  selectedNode: document.getElementById("selected-node"),
  nodesTableBody: document.getElementById("nodes-table-body"),
  totalNodes: document.getElementById("total-nodes"),
  activeNodesCount: document.getElementById("active-nodes-count"),
  totalSavings: document.getElementById("total-savings"),
  avgReduction: document.getElementById("avg-reduction"),
  totalSaved: document.getElementById("total-saved"),
  chartTitle: document.getElementById("chart-title"),
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
    // Show loading state
    showLoadingState();

    // Fetch devices from server
    const devices = await fetchDevicesFromServer();

    // Convert to NODES format
    NODES = convertDevicesToNodes(devices);

    if (NODES.length === 0) {
      console.warn("No devices with location data found from server");
      showNoDataState();
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

    // Initialize historical data
    NODES.forEach((node) => {
      historicalData[node.id] = generateHistoricalDataForNode(node.id);
    });

    // Hide loading state
    hideLoadingState();
  } catch (error) {
    console.error("Failed to initialize data from server:", error);
    showNoDataState();
  }
}

// Show loading state
function showLoadingState() {
  // Update metrics cards
  if (elements.voltage) elements.voltage.textContent = "Loading...";
  if (elements.current) elements.current.textContent = "Loading...";
  if (elements.power) elements.power.textContent = "Loading...";
  if (elements.energy) elements.energy.textContent = "Loading...";
  if (elements.frequency) elements.frequency.textContent = "Loading...";
  if (elements.pf) elements.pf.textContent = "Loading...";
  if (elements.batteryTime) elements.batteryTime.textContent = "00:00";
  if (elements.batterySoC) elements.batterySoC.textContent = "0%";
  if (elements.batteryBar) elements.batteryBar.style.width = "0%";
  if (elements.carbonReduction) elements.carbonReduction.textContent = "0.00";

  // Update business metrics
  if (elements.totalSavings) elements.totalSavings.textContent = "RM 0.00";
  if (elements.totalSaved) elements.totalSaved.textContent = "Loading data...";

  // Update table
  if (elements.nodesTableBody) {
    elements.nodesTableBody.innerHTML = `
      <tr>
        <td colspan="6" class="px-4 py-8 text-center text-gray-400">
          <i class="fas fa-spinner fa-spin mr-2"></i>Loading device data...
        </td>
      </tr>
    `;
  }

  // Update coordinates
  if (elements.coordinatesText) {
    elements.coordinatesText.textContent = "Loading device locations...";
  }
}

// Show no data state
function showNoDataState() {
  // Update metrics cards
  if (elements.voltage) elements.voltage.textContent = "0.00";
  if (elements.current) elements.current.textContent = "0.000";
  if (elements.power) elements.power.textContent = "0.0";
  if (elements.energy) elements.energy.textContent = "0.00";
  if (elements.frequency) elements.frequency.textContent = "0.00";
  if (elements.pf) elements.pf.textContent = "0.000";
  if (elements.batteryTime) elements.batteryTime.textContent = "00:00";
  if (elements.batterySoC) elements.batterySoC.textContent = "0%";
  if (elements.batteryBar) {
    elements.batteryBar.style.width = "0%";
    updateBatteryBarColor(0);
  }
  if (elements.carbonReduction) elements.carbonReduction.textContent = "0.00";

  // Update business metrics
  if (elements.totalSavings) elements.totalSavings.textContent = "RM 0.00";
  if (elements.totalSaved)
    elements.totalSaved.textContent = "No data available";

  // Update table
  if (elements.nodesTableBody) {
    elements.nodesTableBody.innerHTML = `
      <tr>
        <td colspan="6" class="px-4 py-8 text-center text-gray-400">
          <i class="fas fa-exclamation-circle mr-2"></i>No device data available
        </td>
      </tr>
    `;
  }

  // Update node count
  if (elements.totalNodes) elements.totalNodes.textContent = "0";
  if (elements.activeNodesCount) elements.activeNodesCount.textContent = "0";

  // Update coordinates
  if (elements.coordinatesText) {
    elements.coordinatesText.textContent = "No device locations available";
  }

  // Clear chart if exists
  if (powerChart) {
    powerChart.data.labels = [];
    powerChart.data.datasets = [];
    powerChart.update();
  }
}

// Hide loading state
function hideLoadingState() {
  // Nothing specific to hide, UI will be updated with real data
}

// Calculate battery time from SoC
function calculateBatteryTime(batterySoC) {
  if (batterySoC <= 0) return "00:00";

  const remainingHours = batterySoC * 8; // 8 hours at full capacity
  const hours = Math.floor(remainingHours);
  const minutes = Math.floor((remainingHours % 1) * 60);
  return `${String(hours).padStart(2, "0")}:${String(minutes).padStart(2, "0")}`;
}

function generateHistoricalDataForNode(nodeId) {
  const now = Date.now();
  const data = [];
  const node = nodesData[nodeId];

  for (let i = MAX_HISTORY_POINTS - 1; i >= 0; i--) {
    const timestamp = now - i * 2000;
    const timeString = new Date(timestamp).toLocaleTimeString("en-US", {
      hour: "2-digit",
      minute: "2-digit",
      second: "2-digit",
      hour12: false,
    });

    // Use actual node data
    const voltage = node.voltage || 0;
    const current = node.current || 0;
    const pf = node.pf || 0;
    const power = node.power || 0;

    data.push({
      timestamp,
      timeString,
      voltage: parseFloat(voltage.toFixed(2)),
      current: parseFloat(current.toFixed(3)),
      power: parseFloat(power.toFixed(1)),
      pf: parseFloat(pf.toFixed(3)),
    });
  }

  return data;
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
  updateCurrentDisplay();
}

async function updateNodeData() {
  try {
    // Fetch fresh data from server
    await initializeDataFromServer();

    // Update UI
    updateNodesTable();
    updateAggregateMetrics();
    updateMapMarkers();

    if (selectedNodeId === "all") {
      updateChartForAggregate();
    } else {
      updateChartData(historicalData[selectedNodeId]);
    }

    updateCurrentDisplay();
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

  // Update dropdown
  const selectElement = elements.selectedNode;
  if (selectElement) {
    selectElement.innerHTML =
      '<option value="all">All Nodes (Aggregate)</option>';
    NODES.forEach((node) => {
      const option = document.createElement("option");
      option.value = node.id;
      option.textContent = node.name;
      selectElement.appendChild(option);
    });
    selectElement.value = selectedNodeId;
  }
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
  if (NODES.length === 0) {
    if (elements.totalSavings) {
      elements.totalSavings.textContent = "RM 0.00";
    }

    if (elements.totalSaved) {
      elements.totalSaved.textContent = "No data available";
    }
    return;
  }

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

  // Update business metrics
  if (elements.totalSavings) {
    elements.totalSavings.textContent = `RM ${(totalEnergy * 1.6425).toFixed(2)}`;
  }

  if (elements.totalSaved) {
    elements.totalSaved.textContent = `RM ${(totalEnergy * 0.0045).toFixed(2)} saved per cycle`;
  }
}

function updateCurrentDisplay() {
  if (!elements.voltage) return;

  if (selectedNodeId === "all") {
    const nodeIds = Object.keys(nodesData);

    if (nodeIds.length === 0) {
      // No data available
      elements.voltage.textContent = "0.00";
      elements.current.textContent = "0.000";
      elements.power.textContent = "0.0";
      elements.energy.textContent = "0.00";
      elements.frequency.textContent = "0.00";
      elements.pf.textContent = "0.000";
      elements.batterySoC.textContent = "0%";
      elements.batteryTime.textContent = "00:00";
      elements.batteryBar.style.width = "0%";
      elements.carbonReduction.textContent = "0.00";

      updateBatteryBarColor(0);

      if (elements.chartTitle) {
        elements.chartTitle.textContent =
          "All Inverters Average Parameter Trend Analysis";
      }

      if (elements.coordinatesText) {
        elements.coordinatesText.textContent = "No inverters selected";
      }
    } else {
      const activeNodes = nodeIds.filter((id) => nodesData[id].batterySoC > 0);

      if (viewMode === "avg") {
        // Calculate averages for all metrics
        const avgVoltage =
          nodeIds.reduce((sum, id) => sum + (nodesData[id].voltage || 0), 0) /
          nodeIds.length;
        const avgCurrent =
          nodeIds.reduce((sum, id) => sum + (nodesData[id].current || 0), 0) /
          nodeIds.length;
        const avgPower =
          nodeIds.reduce(
            (sum, id) => sum + parseFloat(nodesData[id].power || 0),
            0,
          ) / nodeIds.length;
        const avgPF =
          nodeIds.reduce((sum, id) => sum + (nodesData[id].pf || 0), 0) /
          nodeIds.length;
        const totalEnergy = nodeIds.reduce(
          (sum, id) => sum + (nodesData[id].energy || 0),
          0,
        );
        const avgFrequency =
          nodeIds.reduce((sum, id) => sum + (nodesData[id].frequency || 0), 0) /
          nodeIds.length;
        const totalCarbon = nodeIds.reduce(
          (sum, id) => sum + parseFloat(nodesData[id].carbonReduction || 0),
          0,
        );

        // Calculate AVERAGE battery SoC
        let avgBatterySoC = 0;
        let avgBatteryTime = "00:00";

        if (activeNodes.length > 0) {
          avgBatterySoC =
            activeNodes.reduce(
              (sum, id) => sum + (nodesData[id].batterySoC || 0),
              0,
            ) / activeNodes.length;

          // Calculate average remaining time
          const totalHours = activeNodes.reduce((sum, id) => {
            const timeStr = nodesData[id].batteryTime;
            if (timeStr.includes(":")) {
              const [hours, minutes] = timeStr.split(":").map(Number);
              return sum + hours + minutes / 60;
            }
            return sum;
          }, 0);

          const avgHours = totalHours / activeNodes.length;
          const hours = Math.floor(avgHours);
          const minutes = Math.floor((avgHours % 1) * 60);
          avgBatteryTime = `${String(hours).padStart(2, "0")}:${String(minutes).padStart(2, "0")}`;
        }

        // Update display
        elements.voltage.textContent = avgVoltage.toFixed(2);
        elements.current.textContent = avgCurrent.toFixed(3);
        elements.power.textContent = avgPower.toFixed(1);
        elements.energy.textContent = totalEnergy.toFixed(2);
        elements.frequency.textContent = avgFrequency.toFixed(2);
        elements.pf.textContent = avgPF.toFixed(3);
        elements.batterySoC.textContent = `${(avgBatterySoC * 100).toFixed(0)}%`;
        elements.batteryTime.textContent = avgBatteryTime;
        elements.batteryBar.style.width = `${(avgBatterySoC * 100).toFixed(0)}%`;
        elements.carbonReduction.textContent = totalCarbon.toFixed(2);

        // Update battery bar color
        updateBatteryBarColor(avgBatterySoC);

        if (elements.chartTitle) {
          elements.chartTitle.textContent =
            "All Inverters Average Parameter Trend Analysis";
        }
      } else {
        // TOTAL mode
        const totalCurrent = nodeIds.reduce(
          (sum, id) => sum + (nodesData[id].current || 0),
          0,
        );
        const totalPower = nodeIds.reduce(
          (sum, id) => sum + parseFloat(nodesData[id].power || 0),
          0,
        );
        const totalEnergy = nodeIds.reduce(
          (sum, id) => sum + (nodesData[id].energy || 0),
          0,
        );
        const totalCarbon = nodeIds.reduce(
          (sum, id) => sum + parseFloat(nodesData[id].carbonReduction || 0),
          0,
        );

        // Calculate AVERAGES for voltage and frequency (not sums)
        const avgVoltage =
          nodeIds.reduce((sum, id) => sum + (nodesData[id].voltage || 0), 0) /
          nodeIds.length;
        const avgFrequency =
          nodeIds.reduce((sum, id) => sum + (nodesData[id].frequency || 0), 0) /
          nodeIds.length;
        const avgPF =
          nodeIds.reduce((sum, id) => sum + (nodesData[id].pf || 0), 0) /
          nodeIds.length;

        // Calculate AVERAGE battery capacity
        let avgBatterySoC = 0;
        let totalBatteryTime = "00:00";

        if (activeNodes.length > 0) {
          avgBatterySoC =
            activeNodes.reduce(
              (sum, id) => sum + (nodesData[id].batterySoC || 0),
              0,
            ) / activeNodes.length;

          // Calculate MINIMUM remaining time among all inverters (safest estimate)
          let minRemainingMinutes = Infinity;

          activeNodes.forEach((id) => {
            const nodeData = nodesData[id];
            const timeStr = nodeData.batteryTime;
            if (timeStr.includes(":")) {
              const [hours, minutes] = timeStr.split(":").map(Number);
              const totalMinutes = hours * 60 + minutes;
              if (totalMinutes < minRemainingMinutes) {
                minRemainingMinutes = totalMinutes;
              }
            }
          });

          if (minRemainingMinutes !== Infinity && minRemainingMinutes > 0) {
            const hours = Math.floor(minRemainingMinutes / 60);
            const minutes = minRemainingMinutes % 60;
            totalBatteryTime = `${String(hours).padStart(2, "0")}:${String(minutes).padStart(2, "0")}`;
          } else {
            totalBatteryTime = "00:00";
          }
        }

        // Update display
        elements.voltage.textContent = avgVoltage.toFixed(2);
        elements.current.textContent = totalCurrent.toFixed(3);
        elements.power.textContent = totalPower.toFixed(1);
        elements.energy.textContent = totalEnergy.toFixed(2);
        elements.frequency.textContent = avgFrequency.toFixed(2);
        elements.pf.textContent = avgPF.toFixed(3);
        elements.batterySoC.textContent = `${(avgBatterySoC * 100).toFixed(0)}%`;
        elements.batteryTime.textContent = totalBatteryTime;
        elements.batteryBar.style.width = `${(avgBatterySoC * 100).toFixed(0)}%`;
        elements.carbonReduction.textContent = totalCarbon.toFixed(2);

        // Update battery bar color
        updateBatteryBarColor(avgBatterySoC);

        if (elements.chartTitle) {
          elements.chartTitle.textContent =
            "All Inverters Total Parameter Trend Analysis";
        }
      }

      if (elements.coordinatesText) {
        elements.coordinatesText.textContent = "Multiple inverters selected";
      }
    }
  } else {
    // Show selected inverter values
    const nodeData = nodesData[selectedNodeId];

    if (!nodeData) {
      elements.voltage.textContent = "0.00";
      elements.current.textContent = "0.000";
      elements.power.textContent = "0.0";
      elements.energy.textContent = "0.00";
      elements.frequency.textContent = "0.00";
      elements.pf.textContent = "0.000";
      elements.batteryTime.textContent = "00:00";
      elements.batterySoC.textContent = "0%";
      elements.batteryBar.style.width = "0%";
      elements.carbonReduction.textContent = "0.00";

      updateBatteryBarColor(0);

      if (elements.coordinatesText) {
        elements.coordinatesText.textContent = "Inverter not found";
      }
    } else {
      elements.voltage.textContent = (nodeData.voltage || 0).toFixed(2);
      elements.current.textContent = (nodeData.current || 0).toFixed(3);
      elements.power.textContent = parseFloat(nodeData.power || 0).toFixed(1);
      elements.energy.textContent = (nodeData.energy || 0).toFixed(2);
      elements.frequency.textContent = (nodeData.frequency || 0).toFixed(2);
      elements.pf.textContent = (nodeData.pf || 0).toFixed(3);
      elements.batteryTime.textContent = nodeData.batteryTime || "00:00";
      elements.batterySoC.textContent = `${((nodeData.batterySoC || 0) * 100).toFixed(0)}%`;
      elements.batteryBar.style.width = `${((nodeData.batterySoC || 0) * 100).toFixed(0)}%`;
      elements.carbonReduction.textContent = nodeData.carbonReduction || "0.00";

      // Update battery bar color
      updateBatteryBarColor(nodeData.batterySoC || 0);

      const node = NODES.find((n) => n.id === selectedNodeId);
      if (node && elements.coordinatesText) {
        elements.coordinatesText.textContent = `Inverter: ${
          node.name
        } - ${node.latitude.toFixed(4)} N, ${node.longitude.toFixed(4)} E`;
      }
    }

    if (elements.chartTitle) {
      elements.chartTitle.textContent = `Inverter Parameter Trend Analysis`;
    }
  }

  // Update timestamp
  if (elements.lastUpdated) {
    elements.lastUpdated.textContent = new Date().toLocaleTimeString("en-US", {
      hour: "2-digit",
      minute: "2-digit",
      second: "2-digit",
      hour12: false,
    });
  }
}

// Add this helper function for battery color
function updateBatteryBarColor(batterySoC) {
  const batteryBar = elements.batteryBar;
  if (!batteryBar) return;

  if (batterySoC < 0.2) {
    batteryBar.className = "h-full bg-red-600 rounded-full battery-bar";
  } else if (batterySoC < 0.5) {
    batteryBar.className = "h-full bg-yellow-500 rounded-full battery-bar";
  } else {
    batteryBar.className = "h-full bg-green-500 rounded-full battery-bar";
  }
}

function updateChartForAggregate() {
  if (selectedNodeId !== "all") return;

  const nodeIds = Object.keys(nodesData);
  if (nodeIds.length === 0) {
    // Clear chart if no data
    if (powerChart) {
      powerChart.data.labels = [];
      powerChart.data.datasets = [];
      powerChart.update();
    }
    return;
  }

  const timeLabels = historicalData[nodeIds[0]]?.map((d) => d.timeString) || [];

  // Calculate aggregate data for each time point
  const aggregateData = timeLabels.map((_, index) => {
    if (viewMode === "avg") {
      // Calculate average for each metric
      const voltages = nodeIds.map(
        (id) => historicalData[id]?.[index]?.voltage || 0,
      );
      const currents = nodeIds.map(
        (id) => historicalData[id]?.[index]?.current || 0,
      );
      const powers = nodeIds.map(
        (id) => historicalData[id]?.[index]?.power || 0,
      );
      const pfs = nodeIds.map((id) => historicalData[id]?.[index]?.pf || 0);

      return {
        voltage:
          voltages.reduce((a, b) => a + b, 0) / Math.max(voltages.length, 1),
        current:
          currents.reduce((a, b) => a + b, 0) / Math.max(currents.length, 1),
        power: powers.reduce((a, b) => a + b, 0) / Math.max(powers.length, 1),
        pf: pfs.reduce((a, b) => a + b, 0) / Math.max(pfs.length, 1),
      };
    } else {
      // Calculate totals for power and current, but keep voltage as average
      const currents = nodeIds.map(
        (id) => historicalData[id]?.[index]?.current || 0,
      );
      const powers = nodeIds.map(
        (id) => historicalData[id]?.[index]?.power || 0,
      );
      const voltages = nodeIds.map(
        (id) => historicalData[id]?.[index]?.voltage || 0,
      );

      return {
        voltage:
          voltages.reduce((a, b) => a + b, 0) / Math.max(voltages.length, 1),
        current: currents.reduce((a, b) => a + b, 0),
        power: powers.reduce((a, b) => a + b, 0),
        pf: 0,
      };
    }
  });

  updateChartData(aggregateData, timeLabels);
}

// --- CHART FUNCTIONS ---
function getSelectedMetrics() {
  const paramSelector = elements.paramSelector;
  if (!paramSelector) return ["power"];

  const checkboxes = paramSelector.querySelectorAll('input[type="checkbox"]');
  const selected = [];
  checkboxes.forEach((cb) => {
    if (cb.checked) {
      selected.push(cb.value);
    }
  });
  return selected.length > 0 ? selected : ["power"];
}

function updateChartData(data, customLabels = null) {
  const timeLabels = customLabels || data.map((d) => d.timeString);
  const selectedMetrics = getSelectedMetrics();
  const newDatasets = [];

  selectedMetrics.forEach((metricKey) => {
    const config = METRIC_CONFIG[metricKey];
    const metricData = data.map((d) => d[metricKey]);

    const ctx = document.getElementById("powerChart").getContext("2d");
    const gradient = ctx.createLinearGradient(0, 0, 0, 250);
    gradient.addColorStop(0, `${config.color}44`);
    gradient.addColorStop(1, `${config.color}00`);

    const isElectricMetric = metricKey !== "pf";

    newDatasets.push({
      label: `${config.label}`,
      data: metricData,
      borderColor: config.color,
      backgroundColor: isElectricMetric ? gradient : "transparent",
      borderWidth: 3,
      pointRadius: 3,
      pointHoverRadius: 6,
      tension: 0.4,
      fill: isElectricMetric ? "start" : false,
      yAxisID: isElectricMetric ? "electric-y-axis" : "ratio-y-axis",
    });
  });

  if (powerChart) {
    powerChart.data.labels = timeLabels;
    powerChart.data.datasets = newDatasets;

    const scales = {
      x: {
        display: true,
        title: {
          display: true,
          text: "Time",
          color: "#9ca3af",
          font: {
            size: 14,
            weight: "600",
          },
        },
        grid: {
          color: "rgba(55, 65, 81, 0.5)",
          drawBorder: true,
        },
        ticks: {
          color: "#d1d5db",
          maxRotation: 45,
          minRotation: 45,
        },
      },
    };

    const hasElectricMetric = selectedMetrics.some((m) => m !== "pf");
    if (hasElectricMetric) {
      scales["electric-y-axis"] = {
        display: true,
        position: "left",
        title: {
          display: true,
          text: "V / A / W",
          color: "#06b6d4",
          font: {
            size: 14,
            weight: "600",
          },
        },
        grid: {
          color: "rgba(55, 65, 81, 0.5)",
        },
        ticks: {
          color: "#d1d5db",
        },
      };
    }

    const hasPfMetric = selectedMetrics.includes("pf");
    if (hasPfMetric) {
      scales["ratio-y-axis"] = {
        display: true,
        position: hasElectricMetric ? "right" : "left",
        title: {
          display: true,
          text: "Power Factor (Ratio)",
          color: "#818cf8",
          font: {
            size: 14,
            weight: "600",
          },
        },
        min: 0.9,
        max: 1.05,
        grid: {
          color: hasElectricMetric
            ? "rgba(55, 65, 81, 0.1)"
            : "rgba(55, 65, 81, 0.5)",
          drawOnChartArea: hasElectricMetric ? false : true,
        },
        ticks: {
          color: "#d1d5db",
        },
      };
    }

    powerChart.options.scales = scales;
    powerChart.options.plugins.legend.display = selectedMetrics.length > 1;

    powerChart.update("none");
  }
}

function initChart() {
  const ctx = document.getElementById("powerChart");
  if (!ctx) return;

  powerChart = new Chart(ctx.getContext("2d"), {
    type: "line",
    data: {
      labels: [],
      datasets: [],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      animation: {
        duration: 0,
      },
      interaction: {
        mode: "index",
        intersect: false,
      },
      plugins: {
        legend: {
          display: true,
          labels: {
            color: "#d1d5db",
            font: {
              size: 14,
            },
          },
        },
        tooltip: {
          mode: "index",
          intersect: false,
          backgroundColor: "rgba(31, 41, 55, 0.9)",
          titleColor: "#ffffff",
          bodyColor: "#e5e7eb",
          borderColor: "#4b5563",
          borderWidth: 1,
          cornerRadius: 8,
          padding: 12,
        },
      },
      scales: {},
    },
  });
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
  updateCurrentDisplay();

  // Update chart
  if (nodeId === "all") {
    updateChartForAggregate();
    // Zoom to show all markers
    if (markers.length > 0 && map) {
      const group = new L.featureGroup(markers);
      map.fitBounds(group.getBounds().pad(0.1));
    }
  } else {
    updateChartData(historicalData[nodeId]);
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

    // Initialize chart
    initChart();

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
        updateCurrentDisplay();
        if (selectedNodeId === "all") {
          updateChartForAggregate();
        }
      };

      toggleTotal.onclick = function () {
        viewMode = "total";
        this.classList.add("bg-blue-600", "text-white");
        this.classList.remove("text-gray-300");
        toggleAvg.classList.remove("bg-blue-600", "text-white");
        toggleAvg.classList.add("text-gray-300");
        updateCurrentDisplay();
        if (selectedNodeId === "all") {
          updateChartForAggregate();
        }
      };
    }

    if (elements.selectedNode) {
      elements.selectedNode.onchange = function () {
        selectNode(this.value);
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
    showNoDataState();
  }
}

// --- GLOBAL FUNCTIONS ---
window.handleParamChange = function () {
  if (selectedNodeId === "all") {
    updateChartForAggregate();
  } else {
    updateChartData(historicalData[selectedNodeId]);
  }
};

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
  updateCurrentDisplay();

  if (selectedNodeId === "all") {
    updateChartForAggregate();
  } else {
    updateChartData(historicalData[selectedNodeId]);
  }

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
