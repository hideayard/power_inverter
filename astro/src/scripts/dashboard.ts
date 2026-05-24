// src/scripts/dashboard.ts
import Swal from 'sweetalert2';
import { authStorage } from '../services/apiProxy';
import * as L from 'leaflet';
import 'leaflet/dist/leaflet.css';

let map: L.Map | null = null;
let markers: L.CircleMarker[] = [];
// ============ LAYOUT INITIALIZATION ============
export function initLayout() {
  initTheme();
  initUserDropdown();
  initAuthCheck();
}

// ============ DASHBOARD INITIALIZATION ============
export function initDashboard() {
  initMap();
  loadNodes();
  updateDateTime();
  
  // Refresh every 30 seconds
  setInterval(() => {
    loadNodes();
    updateDateTime();
  }, 30000);
}

// ============ THEME ============
function initTheme() {
  const savedTheme = localStorage.getItem('theme') || 'light';
  applyTheme(savedTheme);

  document.getElementById('theme-toggle')?.addEventListener('click', () => {
    const currentTheme = document.documentElement.classList.contains('dark-mode') ? 'dark' : 'light';
    const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
    localStorage.setItem('theme', newTheme);
    applyTheme(newTheme);
    updateMapTheme();
  });
}

function applyTheme(theme: string) {
  const html = document.documentElement;
  const themeText = document.getElementById('theme-text');
  const iconDark = document.getElementById('theme-icon-dark');
  const iconLight = document.getElementById('theme-icon-light');

  if (theme === 'dark') {
    html.classList.add('dark-mode');
    html.classList.remove('light-mode');
    if (themeText) themeText.textContent = 'Dark Mode';
    if (iconDark) iconDark.classList.remove('hidden');
    if (iconLight) iconLight.classList.add('hidden');
  } else {
    html.classList.add('light-mode');
    html.classList.remove('dark-mode');
    if (themeText) themeText.textContent = 'Light Mode';
    if (iconDark) iconDark.classList.add('hidden');
    if (iconLight) iconLight.classList.remove('hidden');
  }
}

// ============ USER DROPDOWN ============
function initUserDropdown() {
  const menuButton = document.getElementById('user-menu-button');
  const dropdown = document.getElementById('user-dropdown');

  if (menuButton && dropdown) {
    menuButton.addEventListener('click', (e) => {
      e.stopPropagation();
      dropdown.classList.toggle('hidden');
    });

    document.addEventListener('click', (e) => {
      if (!menuButton.contains(e.target as Node) && !dropdown.contains(e.target as Node)) {
        dropdown.classList.add('hidden');
      }
    });
  }

  // Logout
  document.getElementById('logout-button')?.addEventListener('click', () => {
    Swal.fire({
      title: 'Sign Out?',
      text: 'Are you sure you want to sign out?',
      icon: 'question',
      showCancelButton: true,
      confirmButtonColor: '#FDA300',
      cancelButtonColor: '#2C5282',
      confirmButtonText: 'Yes, sign out',
      cancelButtonText: 'Cancel',
    }).then((result) => {
      if (result.isConfirmed) {
        authStorage.clearAuth();
        window.location.href = '/auth/login';
      }
    });
  });

  // Load user data
  loadUserData();
  setInterval(loadUserData, 300000); // Refresh every 5 minutes
}

async function loadUserData() {
  const user = authStorage.getUser();
  
  if (user) {
    const userName = user.user_nama || user.name || user.username || 'User';
    const userEmail = user.user_email || user.email || 'user@example.com';
    const userRole = user.user_tipe || user.role || 'ADMIN';
    const initials = getInitials(userName);

    updateElement('user-name', userName);
    updateElement('dropdown-name', userName);
    updateElement('dropdown-email', userEmail);
    updateElement('dropdown-role', `Role: ${userRole}`);
    updateElement('current-role', userRole);
    updateElement('user-avatar-initials', initials);
    updateElement('dropdown-avatar-initials', initials);
  }
}

function getInitials(name: string): string {
  return name.split(' ').map(word => word[0]).join('').toUpperCase().substring(0, 2) || 'U';
}

function updateElement(id: string, text: string) {
  const el = document.getElementById(id);
  if (el) el.textContent = text;
}

// ============ AUTH CHECK ============
function initAuthCheck() {
  if (!authStorage.isAuthenticated()) {
    window.location.href = '/auth/login';
  }
}

// ============ MAP ============
function initMap() {
  // Johor, Malaysia coordinates
  const johorLat = 1.4927;
  const johorLng = 103.7414;

  const const_map = L.map('map');
  map = L.map('map').setView([johorLat, johorLng], 13);

  const isDark = document.documentElement.classList.contains('dark-mode');
  
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap contributors',
    className: isDark ? 'dark-tiles' : '',
  }).addTo(map);

  // Map toggle
  document.getElementById('map-toggle')?.addEventListener('click', () => {
    const mapContainer = document.getElementById('map-container');
    const maximizeIcon = document.getElementById('maximize-icon');
    const minimizeIcon = document.getElementById('minimize-icon');
    const toggleText = document.getElementById('map-toggle-text');

    if (mapContainer) {
      mapContainer.classList.toggle('lg:col-span-2');
      mapContainer.classList.toggle('lg:col-span-1');
    }

    if (maximizeIcon && minimizeIcon) {
      maximizeIcon.classList.toggle('hidden');
      minimizeIcon.classList.toggle('hidden');
    }

    if (toggleText) {
      toggleText.textContent = toggleText.textContent === 'Maximize' ? 'Minimize' : 'Maximize';
    }

    setTimeout(() => map?.invalidateSize(), 300);
  });

  // Add sample markers
  addMarker(johorLat, johorLng, 'Inverter 1', 'Active');
  addMarker(johorLat + 0.01, johorLng + 0.01, 'Inverter 2', 'Active');
  addMarker(johorLat - 0.01, johorLng - 0.005, 'Inverter 3', 'Warning');
}

function addMarker(lat: number, lng: number, name: string, status: string) {
  if (!map) return;

  const markerColor = status === 'Active' ? '#10B981' : status === 'Warning' ? '#F59E0B' : '#EF4444';
  
  const marker = L.circleMarker([lat, lng], {
    radius: 10,
    fillColor: markerColor,
    color: '#FFFFFF',
    weight: 2,
    opacity: 1,
    fillOpacity: 0.8,
  }).addTo(map);

  marker.bindPopup(`
    <div style="font-family: sans-serif;">
      <strong>${name}</strong><br/>
      Status: ${status}<br/>
      Lat: ${lat.toFixed(4)}, Lng: ${lng.toFixed(4)}
    </div>
  `);

  markers.push(marker);
}

function updateMapTheme() {
  // Map theme would update here if using custom tile providers
}

// ============ NODES TABLE ============
async function loadNodes() {
  try {
    // Example nodes data - replace with API call
    const nodes = [
      { id: 'INV-001', name: 'Inverter Utama', status: 'Active', power: 1200, voltage: 240, current: 5.0 },
      { id: 'INV-002', name: 'Inverter Kedua', status: 'Active', power: 980, voltage: 238, current: 4.1 },
      { id: 'INV-003', name: 'Inverter Ketiga', status: 'Warning', power: 750, voltage: 235, current: 3.2 },
      { id: 'INV-004', name: 'Inverter Keempat', status: 'Active', power: 1100, voltage: 242, current: 4.5 },
      { id: 'INV-005', name: 'Inverter Kelima', status: 'Active', power: 890, voltage: 237, current: 3.8 },
    ];

    renderNodesTable(nodes);
    updateTotalNodes(nodes);
    updateCarbonMetrics(nodes);
  } catch (error) {
    console.error('Error loading nodes:', error);
  }
}

function renderNodesTable(nodes: any[]) {
  const tbody = document.getElementById('nodes-table-body');
  if (!tbody) return;

  tbody.innerHTML = nodes.map(node => `
    <tr>
      <td class="px-4 py-3 font-mono text-xs">${node.id}</td>
      <td class="px-4 py-3 font-medium">${node.name}</td>
      <td class="px-4 py-3">
        <span class="px-2 py-1 rounded-full text-xs font-medium ${
          node.status === 'Active' ? 'bg-green-100 text-green-700' : 
          node.status === 'Warning' ? 'bg-yellow-100 text-yellow-700' : 
          'bg-red-100 text-red-700'
        }">
          ${node.status}
        </span>
      </td>
      <td class="px-4 py-3">${node.power.toLocaleString()}</td>
      <td class="px-4 py-3">${node.voltage.toFixed(1)}</td>
      <td class="px-4 py-3">${node.current.toFixed(2)}</td>
    </tr>
  `).join('');
}

function updateTotalNodes(nodes: any[]) {
  const el = document.getElementById('total-nodes');
  if (el) el.textContent = `${nodes.length} Active`;
  
  const countEl = document.getElementById('active-nodes-count');
  if (countEl) countEl.textContent = nodes.length.toString();
}

function updateCarbonMetrics(nodes: any[]) {
  const totalPower = nodes.reduce((sum, node) => sum + node.power, 0);
  const carbonReduction = (totalPower * 0.85 / 1000).toFixed(2); // kg CO2
  
  const carbonEl = document.getElementById('carbon-reduction-value');
  if (carbonEl) {
    carbonEl.innerHTML = `${parseFloat(carbonReduction).toLocaleString()}<span class="text-lg text-[#FDA300] ml-1">kg</span>`;
  }
}

// ============ UTILS ============
function updateDateTime() {
  const el = document.getElementById('last-updated-time');
  if (el) {
    el.textContent = new Date().toLocaleTimeString('en-MY', { 
      hour: '2-digit', 
      minute: '2-digit', 
      second: '2-digit' 
    });
  }
}