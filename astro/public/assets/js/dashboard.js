// --- GLOBAL STATE AND CONFIGURATION ---
let simulationInterval;
let map = null;
let markers = [];
let powerChart;
let viewMode = "avg"; // 'avg' or 'total'
let selectedNodeId = "all";

// Map toggle functionality
let isMapFullscreen = false;

function setupMapToggle(devices) {
  const mapContainer = document.getElementById("map-container");
  const mapToggle = document.getElementById("map-toggle");
  const maximizeIcon = document.getElementById("maximize-icon");
  const minimizeIcon = document.getElementById("minimize-icon");
  const toggleText = document.getElementById("map-toggle-text");

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
// Sample Nodes Data
const NODES = [
  {
    id: "node-1",
    name: "Main Inverter Station",
    location: "Johor Bahru",
    latitude: 1.5501,
    longitude: 103.78,
    status: "active",
    color: "#fbbf24",
  },
  {
    id: "node-2",
    name: "Residential Unit A",
    location: "Skudai",
    latitude: 1.53,
    longitude: 103.67,
    status: "active",
    color: "#10b981",
  },
  {
    id: "node-3",
    name: "Commercial Building B",
    location: "Senai",
    latitude: 1.6,
    longitude: 103.65,
    status: "active",
    color: "#8b5cf6",
  },
  {
    id: "node-4",
    name: "Industrial Zone C",
    location: "Pasir Gudang",
    latitude: 1.47,
    longitude: 103.9,
    status: "warning",
    color: "#f97316",
  },
  {
    id: "node-5",
    name: "Backup Station D",
    location: "Kulai",
    latitude: 1.67,
    longitude: 103.6,
    status: "active",
    color: "#06b6d4",
  },
];

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

// --- INITIALIZE MAP ---
function initMap() {
  map = L.map("map").setView([1.5501, 103.78], 11);

  L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
    attribution: "© OpenStreetMap contributors",
    maxZoom: 19,
  }).addTo(map);

  // Add markers for each node
  NODES.forEach((node) => {
    const icon = L.divIcon({
      className: "node-marker",
      html: `<div style="background-color: ${node.color}; width: 20px; height: 20px; border-radius: 50%; border: 3px solid white; box-shadow: 0 0 10px rgba(251, 191, 36, 0.8); cursor: pointer;"></div>`,
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
}

// --- NODE MANAGEMENT FUNCTIONS ---
function initializeNodes() {
  // Initialize data for each inverter
  NODES.forEach((node) => {
    nodesData[node.id] = {
      ...node,
      voltage: 230.0 + Math.random() * 10 - 5,
      current: 1.5 + Math.random() * 3,
      power: 0, // Default to 0
      energy: 1250.55 + Math.random() * 1000,
      frequency: 50.0 + Math.random() * 0.1 - 0.05,
      pf: 0.95 + Math.random() * 0.1,
      batterySoC: 0.7 + Math.random() * 0.3,
      batteryTime: "04:32",
      carbonReduction: 0, // Default to 0
      lastUpdated: Date.now(),
    };

    // Calculate initial power
    const nodeData = nodesData[node.id];
    nodeData.power = parseFloat(
      (nodeData.voltage * nodeData.current * nodeData.pf).toFixed(1)
    );
    nodeData.carbonReduction = parseFloat(
      (nodeData.energy * CARBON_REDUCTION_FACTOR).toFixed(2)
    );
  });

  // Initialize historical data
  NODES.forEach((node) => {
    historicalData[node.id] = generateHistoricalDataForNode(node.id);
  });

  // Update UI
  updateNodeSelector();
  updateNodesTable();
  updateAggregateMetrics();
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

    // Generate realistic fluctuations based on node data
    const voltage = node.voltage + (Math.random() * 0.8 - 0.4);
    const current = node.current + (Math.random() * 0.3 - 0.15);
    const pf = Math.min(
      1,
      Math.max(0.85, node.pf + (Math.random() * 0.06 - 0.03))
    );
    const power = voltage * current * pf;

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

function updateNodeData() {
  // addMarkerAnimations();
  const intervalSeconds = 2;
  const intervalHours = intervalSeconds / 3600;

  // Update each node's data
  // Add markers for each node (inverter)
  NODES.forEach((node) => {
    const icon = L.divIcon({
      className: "inverter-marker",
      html: `
            <div style="position: relative;">
                <div style="
                    width: 24px;
                    height: 24px;
                    background: linear-gradient(135deg, ${node.color} 0%, ${
        node.color
      }80 100%);
                    border-radius: 4px;
                    transform: rotate(45deg);
                    border: 2px solid white;
                    box-shadow: 0 0 15px ${node.color}80;
                    position: relative;
                ">
                    <div style="
                        position: absolute;
                        top: 50%;
                        left: 50%;
                        transform: translate(-50%, -50%) rotate(-45deg);
                        font-weight: bold;
                        color: white;
                        font-size: 10px;
                    ">
                        ${node.name.charAt(0)}
                    </div>
                </div>
                <div style="
                    position: absolute;
                    top: -5px;
                    right: -5px;
                    width: 10px;
                    height: 10px;
                    border-radius: 50%;
                    background-color: ${
                      node.status === "active"
                        ? "#10B981"
                        : node.status === "warning"
                        ? "#F59E0B"
                        : "#EF4444"
                    };
                    border: 1px solid white;
                    box-shadow: 0 0 5px rgba(0,0,0,0.3);
                "></div>
            </div>
        `,
      iconSize: [24, 24],
      iconAnchor: [12, 12],
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

  // Update UI
  updateNodesTable();
  updateAggregateMetrics();

  // UPDATE MAP MARKERS - ADD THIS LINE
  updateMapMarkers();

  if (selectedNodeId === "all") {
    updateChartForAggregate();
  } else {
    updateChartData(historicalData[selectedNodeId]);
  }

  updateCurrentDisplay();
}

function addMarkerAnimations() {
  markers.forEach((marker) => {
    // Add pulsing effect for active markers
    const iconDiv = marker.getElement();
    if (iconDiv) {
      iconDiv.style.transition = "all 0.5s ease";
      iconDiv.addEventListener("mouseenter", () => {
        iconDiv.style.transform = "scale(1.2)";
      });
      iconDiv.addEventListener("mouseleave", () => {
        iconDiv.style.transform = "scale(1)";
      });
    }
  });
}

// --- UI UPDATE FUNCTIONS ---
function updateNodeSelector() {
  const nodeSelector = elements.nodeSelector;
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

function updateNodesTable() {
  const tbody = elements.nodesTableBody;
  tbody.innerHTML = "";

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
              nodeData.power
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

  elements.totalNodes.textContent = NODES.length;
  elements.activeNodesCount.textContent = NODES.filter(
    (n) => n.status === "active"
  ).length;
}

function updateAggregateMetrics() {
  // Calculate aggregate values
  const nodeIds = Object.keys(nodesData);
  const totalEnergy = nodeIds.reduce(
    (sum, id) => sum + nodesData[id].energy,
    0
  );
  const totalCarbon = nodeIds.reduce(
    (sum, id) => sum + parseFloat(nodesData[id].carbonReduction),
    0
  );

  // Update business metrics
  elements.totalSavings.textContent = `RM ${(totalEnergy * 1.6425).toFixed(2)}`;
  elements.totalSaved.textContent = `RM ${(totalEnergy * 0.0045).toFixed(
    2
  )} saved per cycle`;
}

function updateCurrentDisplay() {
  if (selectedNodeId === "all") {
    const nodeIds = Object.keys(nodesData);
    const activeNodes = nodeIds.filter((id) => nodesData[id].batterySoC > 0);

    if (viewMode === "avg") {
      // Calculate averages for all metrics
      const avgVoltage =
        nodeIds.reduce((sum, id) => sum + nodesData[id].voltage, 0) /
        nodeIds.length;
      const avgCurrent =
        nodeIds.reduce((sum, id) => sum + nodesData[id].current, 0) /
        nodeIds.length;
      const avgPower =
        nodeIds.reduce((sum, id) => sum + parseFloat(nodesData[id].power), 0) /
        nodeIds.length;
      const avgPF =
        nodeIds.reduce((sum, id) => sum + nodesData[id].pf, 0) / nodeIds.length;
      const totalEnergy = nodeIds.reduce(
        (sum, id) => sum + nodesData[id].energy,
        0
      );
      const avgFrequency =
        nodeIds.reduce((sum, id) => sum + nodesData[id].frequency, 0) /
        nodeIds.length;
      const totalCarbon = nodeIds.reduce(
        (sum, id) => sum + parseFloat(nodesData[id].carbonReduction),
        0
      );

      // Calculate AVERAGE battery SoC
      let avgBatterySoC = 0;
      let avgBatteryTime = "00:00";

      if (activeNodes.length > 0) {
        avgBatterySoC =
          activeNodes.reduce((sum, id) => sum + nodesData[id].batterySoC, 0) /
          activeNodes.length;

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
        avgBatteryTime = `${String(hours).padStart(2, "0")}:${String(
          minutes
        ).padStart(2, "0")}`;
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

      elements.chartTitle.textContent =
        "All Inverters Average Parameter Trend Analysis";
    } else {
      // TOTAL mode
      const totalCurrent = nodeIds.reduce(
        (sum, id) => sum + nodesData[id].current,
        0
      );
      const totalPower = nodeIds.reduce(
        (sum, id) => sum + parseFloat(nodesData[id].power),
        0
      );
      const totalEnergy = nodeIds.reduce(
        (sum, id) => sum + nodesData[id].energy,
        0
      );
      const totalCarbon = nodeIds.reduce(
        (sum, id) => sum + parseFloat(nodesData[id].carbonReduction),
        0
      );

      // Calculate AVERAGES for voltage and frequency (not sums)
      const avgVoltage =
        nodeIds.reduce((sum, id) => sum + nodesData[id].voltage, 0) /
        nodeIds.length;
      const avgFrequency =
        nodeIds.reduce((sum, id) => sum + nodesData[id].frequency, 0) /
        nodeIds.length;
      const avgPF =
        nodeIds.reduce((sum, id) => sum + nodesData[id].pf, 0) / nodeIds.length;

      // Calculate AVERAGE battery capacity
      let avgBatterySoC = 0;
      let totalBatteryTime = "00:00";

      if (activeNodes.length > 0) {
        avgBatterySoC =
          activeNodes.reduce((sum, id) => sum + nodesData[id].batterySoC, 0) /
          activeNodes.length;

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
          totalBatteryTime = `${String(hours).padStart(2, "0")}:${String(
            minutes
          ).padStart(2, "0")}`;
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
      elements.batteryTime.textContent = totalBatteryTime; // Show MINIMUM time among all inverters
      elements.batteryBar.style.width = `${(avgBatterySoC * 100).toFixed(0)}%`;
      elements.carbonReduction.textContent = totalCarbon.toFixed(2);

      // Update battery bar color
      updateBatteryBarColor(avgBatterySoC);

      elements.chartTitle.textContent =
        "All Inverters Total Parameter Trend Analysis";
    }

    elements.coordinatesText.textContent = "Multiple inverters selected";
  } else {
    // Show selected inverter values
    const nodeData = nodesData[selectedNodeId];

    elements.voltage.textContent = nodeData.voltage.toFixed(2);
    elements.current.textContent = nodeData.current.toFixed(3);
    elements.power.textContent = parseFloat(nodeData.power).toFixed(1);
    elements.energy.textContent = nodeData.energy.toFixed(2);
    elements.frequency.textContent = nodeData.frequency.toFixed(2);
    elements.pf.textContent = nodeData.pf.toFixed(3);
    elements.batteryTime.textContent = nodeData.batteryTime;
    elements.batterySoC.textContent = `${(nodeData.batterySoC * 100).toFixed(
      0
    )}%`;
    elements.batteryBar.style.width = `${(nodeData.batterySoC * 100).toFixed(
      0
    )}%`;
    elements.carbonReduction.textContent = nodeData.carbonReduction;

    // Update battery bar color
    updateBatteryBarColor(nodeData.batterySoC);

    const node = NODES.find((n) => n.id === selectedNodeId);
    if (node) {
      elements.coordinatesText.textContent = `Inverter: ${
        node.name
      } - ${node.latitude.toFixed(4)} N, ${node.longitude.toFixed(4)} E`;
    }

    elements.chartTitle.textContent = `${nodeData.name} - Inverter Parameter Trend Analysis`;
  }

  // Update timestamp
  elements.lastUpdated.textContent = new Date().toLocaleTimeString("en-US", {
    hour: "2-digit",
    minute: "2-digit",
    second: "2-digit",
    hour12: false,
  });
}

// Add this helper function for battery color
function updateBatteryBarColor(batterySoC) {
  const batteryBar = elements.batteryBar;
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
  const timeLabels = historicalData[nodeIds[0]].map((d) => d.timeString);

  // Calculate aggregate data for each time point
  const aggregateData = timeLabels.map((_, index) => {
    if (viewMode === "avg") {
      // Calculate average for each metric
      const voltages = nodeIds.map((id) => historicalData[id][index].voltage);
      const currents = nodeIds.map((id) => historicalData[id][index].current);
      const powers = nodeIds.map((id) => historicalData[id][index].power);
      const pfs = nodeIds.map((id) => historicalData[id][index].pf);

      return {
        voltage: voltages.reduce((a, b) => a + b, 0) / voltages.length,
        current: currents.reduce((a, b) => a + b, 0) / currents.length,
        power: powers.reduce((a, b) => a + b, 0) / powers.length,
        pf: pfs.reduce((a, b) => a + b, 0) / pfs.length,
      };
    } else {
      // Calculate totals for power and current, but keep voltage as average
      const currents = nodeIds.map((id) => historicalData[id][index].current);
      const powers = nodeIds.map((id) => historicalData[id][index].power);
      const voltages = nodeIds.map((id) => historicalData[id][index].voltage);

      return {
        voltage: voltages.reduce((a, b) => a + b, 0) / voltages.length, // AVERAGE voltage
        current: currents.reduce((a, b) => a + b, 0), // TOTAL current
        power: powers.reduce((a, b) => a + b, 0), // TOTAL power
        pf: 0, // PF doesn't sum meaningfully in total mode
      };
    }
  });

  updateChartData(aggregateData, timeLabels);
}

// --- CHART FUNCTIONS ---
function getSelectedMetrics() {
  const checkboxes = elements.paramSelector.querySelectorAll(
    'input[type="checkbox"]'
  );
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
  const ctx = document.getElementById("powerChart").getContext("2d");

  powerChart = new Chart(ctx, {
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
    if (markers.length > 0) {
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
  elements.selectedNode.value = nodeId;
}

function highlightSelectedMarker(nodeId) {
  // Reset all markers to normal
  markers.forEach((marker) => {
    const iconDiv = marker.getElement();
    if (iconDiv) {
      iconDiv.style.transform = "scale(1)";
      iconDiv.style.boxShadow = "0 0 15px rgba(0,0,0,0.3)";
      iconDiv.style.zIndex = "1";
    }
  });

  // Find and highlight the selected marker
  const selectedMarker = markers.find((m) => m.nodeId === nodeId);
  if (selectedMarker) {
    // Zoom to the selected marker
    map.setView(selectedMarker.getLatLng(), 15);

    // Highlight the marker
    const iconDiv = selectedMarker.getElement();
    if (iconDiv) {
      iconDiv.style.transform = "scale(1.3)";
      iconDiv.style.boxShadow = "0 0 25px rgba(251, 191, 36, 0.8)";
      iconDiv.style.zIndex = "1000";
      iconDiv.style.transition = "all 0.3s ease";
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

function createNodePopup(node) {
  const nodeData = nodesData[node.id];
  const statusColor =
    node.status === "active"
      ? "#10B981"
      : node.status === "warning"
      ? "#F59E0B"
      : "#EF4444";

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
                    <span class="text-gray-600">ID:</span>
                    <span class="font-medium">${node.id}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Status:</span>
                    <span class="font-medium" style="color: ${statusColor}">${node.status.toUpperCase()}</span>
                </div>
                <hr class="my-1 border-gray-200">
                <div class="grid grid-cols-2 gap-x-4 gap-y-2">
                    <div>
                        <div class="text-xs text-gray-500">Voltage</div>
                        <div class="font-bold text-blue-500">${
                          nodeData?.voltage?.toFixed(1) || "0.0"
                        } <span class="text-xs">V</span></div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500">Current</div>
                        <div class="font-bold text-red-500">${
                          nodeData?.current?.toFixed(2) || "0.00"
                        } <span class="text-xs">A</span></div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500">Power</div>
                        <div class="font-bold text-green-500">${
                          nodeData?.power || "0"
                        } <span class="text-xs">W</span></div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500">Frequency</div>
                        <div class="font-bold text-purple-500">${
                          nodeData?.frequency?.toFixed(2) || "0.00"
                        } <span class="text-xs">Hz</span></div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500">Power Factor</div>
                        <div class="font-bold text-indigo-500">${
                          nodeData?.pf?.toFixed(3) || "0.000"
                        }</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500">Battery</div>
                        <div class="font-bold text-orange-500">${
                          nodeData?.batterySoC
                            ? (nodeData.batterySoC * 100).toFixed(0)
                            : "0"
                        }%</div>
                    </div>
                </div>
                <div class="pt-2 text-xs text-gray-500 text-center">
                    Updated: ${
                      nodeData?.lastUpdated
                        ? new Date(nodeData.lastUpdated).toLocaleTimeString()
                        : "Just now"
                    }
                </div>
            </div>
            
            <!--button onclick="selectNode('${node.id}')" 
                    class="mt-3 w-full bg-blue-600 hover:bg-blue-700 text-white py-2 px-3 rounded-lg text-sm font-medium transition-colors">
                View Inverter Details
            </!button-->
        </div>
    `;
}

function updateMapMarkers() {
  markers.forEach((marker) => {
    // Get the node ID from the marker's property
    const nodeId = marker.nodeId;

    // Find the corresponding node in NODES array
    const node = NODES.find((n) => n.id === nodeId);
    if (!node) return;

    const nodeData = nodesData[nodeId];

    // Update marker popup content if it's open
    if (marker.isPopupOpen && marker.isPopupOpen()) {
      const popupContent = createNodePopup(node);
      marker.setPopupContent(popupContent);
    }

    // Update marker color based on status and conditions
    if (nodeData) {
      let markerColor = node.color;

      // Change color based on conditions
      if (nodeData.power > 800) {
        markerColor = "#ef4444"; // Red for high power
      } else if (nodeData.batterySoC < 0.3) {
        markerColor = "#f59e0b"; // Orange for low battery
      } else if (node.status === "warning") {
        markerColor = "#f59e0b"; // Yellow for warning status
      } else if (node.status !== "active") {
        markerColor = "#ef4444"; // Red for inactive
      }

      // Only update if color changed
      const currentIcon = marker.options.icon;
      const currentColor = node.color; // This might need adjustment

      // Update marker icon dynamically if needed
      const icon = L.divIcon({
        className: "inverter-marker",
        html: `
                    <div style="position: relative;">
                        <div style="
                            width: 24px;
                            height: 24px;
                            background: linear-gradient(135deg, ${markerColor} 0%, ${markerColor}80 100%);
                            border-radius: 4px;
                            transform: rotate(45deg);
                            border: 2px solid white;
                            box-shadow: 0 0 15px ${markerColor}80;
                            position: relative;
                        ">
                            <div style="
                                position: absolute;
                                top: 50%;
                                left: 50%;
                                transform: translate(-50%, -50%) rotate(-45deg);
                                font-weight: bold;
                                color: white;
                                font-size: 10px;
                            ">
                                ${node.name.charAt(0)}
                            </div>
                        </div>
                        <div style="
                            position: absolute;
                            top: -5px;
                            right: -5px;
                            width: 10px;
                            height: 10px;
                            border-radius: 50%;
                            background-color: ${
                              node.status === "active"
                                ? "#10B981"
                                : node.status === "warning"
                                ? "#F59E0B"
                                : "#EF4444"
                            };
                            border: 1px solid white;
                            box-shadow: 0 0 5px rgba(0,0,0,0.3);
                        "></div>
                    </div>
                `,
        iconSize: [24, 24],
        iconAnchor: [12, 12],
      });

      marker.setIcon(icon);
    }
  });
}

const PROXY2_ENDPOINT = "/proxy2.php"; // Your proxy file

// Store token globally after login
let authToken = null;

function setAuthToken(token) {
  authToken = token;
  localStorage.setItem("authToken", token);
  console.log("Auth token set");
}

// --- INITIALIZATION ---
function initApplication() {
  loadAndDisplayDevices();
  const storedToken = localStorage.getItem("authToken");
  let devices;
  if (storedToken) {
    setAuthToken(storedToken);
    // devices = getDevices(storedToken);
    // devices = getDevicesWithTelemetry(storedToken);
    // Initialize map
    // initMap();

    // // Initialize map toggle
    // setupMapToggle(devices);

    // // Initialize nodes
    // initializeNodes();

    // // Initialize chart
    // initChart();

    // // Set initial display
    // selectNode("all");

    // Start simulation
    simulationInterval = setInterval(updateNodeData, 2000);
  } else {
    // Prompt for login if no token
    console.log("No auth token found. Please login first.");
    return;
  }

  // Set up event listeners
  document.getElementById("toggle-avg").onclick = function () {
    viewMode = "avg";
    this.classList.add("bg-blue-600", "text-white");
    this.classList.remove("text-gray-300");
    document
      .getElementById("toggle-total")
      .classList.remove("bg-blue-600", "text-white");
    document.getElementById("toggle-total").classList.add("text-gray-300");
    updateCurrentDisplay();
    if (selectedNodeId === "all") {
      updateChartForAggregate();
    }
  };

  document.getElementById("toggle-total").onclick = function () {
    viewMode = "total";
    this.classList.add("bg-blue-600", "text-white");
    this.classList.remove("text-gray-300");
    document
      .getElementById("toggle-avg")
      .classList.remove("bg-blue-600", "text-white");
    document.getElementById("toggle-avg").classList.add("text-gray-300");
    updateCurrentDisplay();
    if (selectedNodeId === "all") {
      updateChartForAggregate();
    }
  };

  elements.selectedNode.onchange = function () {
    selectNode(this.value);
  };

  console.log("Multi-node dashboard initialized");
}

// Get devices with Bearer token
async function getDevices(authToken) {
  try {
    console.log("Fetching devices via proxy...");

    const response = await fetch(PROXY2_ENDPOINT, {
      method: "POST",
      headers: {
        Authorization: `Bearer ${authToken}`,
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: new URLSearchParams({
        action: "get_devices",
      }),
    });

    console.log("Devices response status:", response.status);

    if (!response.ok) {
      throw new Error(`Failed to fetch devices: ${response.status}`);
    }

    const result = await response.json();

    if (result.success) {
      console.log("Devices fetched:", result.data);
      return result.data;
    } else {
      throw new Error(result.message || "Failed to get devices");
    }
  } catch (error) {
    console.error("Error fetching devices:", error);
    throw error;
  }
}

async function getDevicesWithTelemetry(authToken) {
  try {
    const response = await fetch(PROXY2_ENDPOINT, {
      method: "POST",
      headers: {
        Authorization: `Bearer ${authToken}`,
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: new URLSearchParams({
        action: "get_devices_with_data",
      }),
    });

    if (!response.ok) {
      throw new Error(`Failed to fetch devices: ${response.status}`);
    }

    const result = await response.json();

    if (result.success) {
      console.log(`Got ${result.data.length} devices with telemetry`);
      console.log("Summary:", result.summary);
      return {
        devices: result.data,
        summary: result.summary,
      };
    } else {
      throw new Error(result.message || "Failed to get devices");
    }
  } catch (error) {
    console.error("Error fetching devices with telemetry:", error);
    throw error;
  }
}

// Function to display device cards
function displayDeviceCards(devices) {
  const container = document.getElementById("devices-container");
  if (!container) return;

  container.innerHTML = "";

  devices.forEach((device) => {
    const card = createDeviceCard(device);
    container.appendChild(card);
  });
}

// Create device card HTML
function createDeviceCard(device) {
  const card = document.createElement("div");
  card.className = "device-card";
  card.id = `device-${device.device_id}`;

  const hasTelemetry = device.telemetry !== null;
  const hasLocation = device.has_location;

  card.innerHTML = `
        <div class="device-header">
            <div class="device-status ${device.status}">
                ${device.status_text}
            </div>
            <div class="device-last-updated">
                ${device.last_updated_ago}
            </div>
        </div>
        
        <div class="device-info">
            <h3>${
              device.device_name || device.device_alias || device.device_id
            }</h3>
            <p class="device-id">ID: ${device.device_id}</p>
            ${
              device.device_description
                ? `<p class="device-description">${device.device_description}</p>`
                : ""
            }
        </div>
        
        ${
          hasTelemetry
            ? `
        <div class="device-telemetry">
            <div class="telemetry-grid">
                <div class="telemetry-item">
                    <span class="label">AC Power</span>
                    <span class="value">${
                      device.metrics.calculated_power
                    } W</span>
                </div>
                <div class="telemetry-item">
                    <span class="label">Energy</span>
                    <span class="value">${
                      device.telemetry.energy || 0
                    } kWh</span>
                </div>
                <div class="telemetry-item">
                    <span class="label">Voltage</span>
                    <span class="value">${
                      device.telemetry.ac_voltage || 0
                    } V</span>
                </div>
                <div class="telemetry-item">
                    <span class="label">Current</span>
                    <span class="value">${
                      device.telemetry.ac_current || 0
                    } A</span>
                </div>
                <div class="telemetry-item">
                    <span class="label">Power Factor</span>
                    <span class="value">${
                      device.telemetry.power_factor || 0
                    }</span>
                </div>
                <div class="telemetry-item">
                    <span class="label">Frequency</span>
                    <span class="value">${
                      device.telemetry.frequency || 0
                    } Hz</span>
                </div>
                ${
                  device.telemetry.dc_voltage
                    ? `
                <div class="telemetry-item">
                    <span class="label">DC Voltage</span>
                    <span class="value">${device.telemetry.dc_voltage} V</span>
                </div>
                <div class="telemetry-item">
                    <span class="label">DC Current</span>
                    <span class="value">${
                      device.telemetry.dc_current || 0
                    } A</span>
                </div>
                `
                    : ""
                }
            </div>
            
            <div class="device-metrics">
                <div class="metric">
                    <span class="metric-label">Carbon Reduction</span>
                    <span class="metric-value">${
                      device.metrics.carbon_reduction
                    } kg</span>
                </div>
                <div class="metric">
                    <span class="metric-label">Uptime</span>
                    <span class="metric-value">${device.metrics.uptime}%</span>
                </div>
                ${
                  device.metrics.battery_percentage > 0
                    ? `
                <div class="metric">
                    <span class="metric-label">Battery</span>
                    <span class="metric-value">${device.metrics.battery_percentage}%</span>
                </div>
                `
                    : ""
                }
                <div class="metric">
                    <span class="metric-label">Efficiency</span>
                    <span class="metric-value">${
                      device.metrics.efficiency
                    }%</span>
                </div>
            </div>
            
            ${
              device.metrics.low_voltage
                ? `
            <div class="device-alert warning">
                ⚠️ Low Voltage Warning
            </div>
            `
                : ""
            }
            
            ${
              device.metrics.relay_on
                ? `
            <div class="device-alert info">
                🔌 Relay is ON
            </div>
            `
                : ""
            }
        </div>
        `
            : `
        <div class="no-telemetry">
            <p>No telemetry data available</p>
        </div>
        `
        }
        
        ${
          hasLocation
            ? `
        <div class="device-location">
            <p><strong>Location:</strong> ${device.location.formatted}</p>
            <a href="${device.location.google_maps_link}" target="_blank" class="map-link">
                📍 View on Map
            </a>
        </div>
        `
            : ""
        }
        
        <div class="device-actions">
            <button onclick="viewDeviceDetails('${
              device.device_id
            }')" class="btn btn-primary">
                View Details
            </button>
            <button onclick="viewDeviceHistory('${
              device.device_id
            }')" class="btn btn-secondary">
                History
            </button>
        </div>
    `;

  return card;
}

// Function to update summary dashboard
function updateSummaryDashboard(summary) {
  const summaryEl = document.getElementById("dashboard-summary");
  if (!summaryEl) return;

  summaryEl.innerHTML = `
        <div class="summary-card">
            <div class="summary-value">${summary.total_devices}</div>
            <div class="summary-label">Total Devices</div>
        </div>
        <div class="summary-card">
            <div class="summary-value">${summary.active_devices}</div>
            <div class="summary-label">Active</div>
        </div>
        <div class="summary-card">
            <div class="summary-value">${summary.warning_devices}</div>
            <div class="summary-label">Warning</div>
        </div>
        <div class="summary-card">
            <div class="summary-value">${summary.offline_devices}</div>
            <div class="summary-label">Offline</div>
        </div>
        <div class="summary-card">
            <div class="summary-value">${summary.with_telemetry}</div>
            <div class="summary-label">With Data</div>
        </div>
        <div class="summary-card">
            <div class="summary-value">${summary.with_location}</div>
            <div class="summary-label">With Location</div>
        </div>
        <div class="summary-card">
            <div class="summary-value">${summary.total_power.toFixed(0)}</div>
            <div class="summary-label">Total Power (W)</div>
        </div>
        <div class="summary-card">
            <div class="summary-value">${summary.total_energy.toFixed(2)}</div>
            <div class="summary-label">Total Energy (kWh)</div>
        </div>
        <div class="summary-card">
            <div class="summary-value">${summary.total_carbon_reduction.toFixed(
              1
            )}</div>
            <div class="summary-label">CO₂ Reduced (kg)</div>
        </div>
    `;
}

// Main function to load and display data
async function loadAndDisplayDevices() {
  try {
    const storedToken = localStorage.getItem("authToken");

    const { devices, summary } = await getDevicesWithTelemetry(storedToken);

    // Display devices
    displayDeviceCards(devices);

    // Update summary
    updateSummaryDashboard(summary);

    // Update map with device locations
    updateDeviceMap(devices.filter((d) => d.has_location));

    // Update charts with device data
    updatePowerChart(devices);
    updateEnergyChart(devices);

    return devices;
  } catch (error) {
    console.error("Failed to load devices:", error);
    showError("Failed to load device data");
    return [];
  }
}

async function fetchDevicesFromServer() {
  try {
    if (!authToken) {
      console.error("No authentication token available");
      return null;
    }

    console.log("Fetching devices from server...");

    const formData = new FormData();
    formData.append("action", "get-devices"); // Added action parameter

    const response = await fetch(PROXY2_ENDPOINT, {
      method: "POST",
      body: formData,
      credentials: "same-origin",
    });

    console.log("Devices response status:", response.status);

    if (!response.ok) {
      const errorText = await response.text();
      console.error("Failed to fetch devices:", errorText);

      try {
        const errorData = JSON.parse(errorText);
        throw new Error(
          errorData.message || `Failed to fetch devices: ${response.status}`
        );
      } catch {
        throw new Error(
          `Failed to fetch devices: ${response.status} - Server error`
        );
      }
    }

    const result = await response.json();

    if (result.success && result.data) {
      console.log(
        "Devices fetched successfully:",
        result.data.length,
        "devices found"
      );
      return result.data;
    } else {
      throw new Error(result.message || "No devices data received");
    }
  } catch (error) {
    console.error("Error fetching devices:", error);
    throw error;
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

// --- START APPLICATION ---
document.addEventListener("DOMContentLoaded", initApplication);
