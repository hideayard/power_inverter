<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forex Market Hours Dashboard with Leaflet Map</title>
    
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #0f2027 0%, #203a43 50%, #2c5364 100%);
            color: #f0f0f0;
            min-height: 100vh;
        }
        
        .container {
            max-width: 1600px;
            margin: 0 auto;
            padding: 20px;
        }
        
        header {
            text-align: center;
            margin-bottom: 25px;
            padding: 20px;
            background: rgba(0, 0, 0, 0.3);
            border-radius: 15px;
            border-bottom: 3px solid #00b4d8;
        }
        
        h1 {
            font-size: 2.8rem;
            margin-bottom: 10px;
            background: linear-gradient(to right, #00b4d8, #90e0ef);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }
        
        .subtitle {
            font-size: 1.1rem;
            opacity: 0.8;
            max-width: 900px;
            margin: 0 auto 15px;
            line-height: 1.6;
        }
        
        .dashboard {
            display: grid;
            grid-template-columns: 1.2fr 0.8fr;
            gap: 25px;
            margin-bottom: 30px;
        }
        
        @media (max-width: 1200px) {
            .dashboard {
                grid-template-columns: 1fr;
            }
        }
        
        .map-container {
            background: rgba(16, 30, 46, 0.8);
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            height: 650px;
            display: flex;
            flex-direction: column;
        }
        
        .markets-container {
            background: rgba(16, 30, 46, 0.8);
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            overflow-y: auto;
            max-height: 650px;
        }
        
        .section-title {
            font-size: 1.5rem;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #00b4d8;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .section-title i {
            color: #00b4d8;
        }
        
        #market-map {
            flex: 1;
            border-radius: 10px;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .markets-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .markets-table thead {
            background: rgba(0, 180, 216, 0.2);
        }
        
        .markets-table th {
            padding: 15px 12px;
            text-align: left;
            font-weight: 600;
            border-bottom: 2px solid #00b4d8;
        }
        
        .markets-table td {
            padding: 14px 12px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .markets-table tbody tr:hover {
            background: rgba(0, 180, 216, 0.1);
            cursor: pointer;
        }
        
        .market-name {
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .status-indicator {
            display: inline-block;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 8px;
        }
        
        .status-open {
            background-color: #2ecc71;
            box-shadow: 0 0 8px #2ecc71;
        }
        
        .status-closed {
            background-color: #e74c3c;
            box-shadow: 0 0 8px #e74c3c;
        }
        
        .status-closing-soon {
            background-color: #f39c12;
            box-shadow: 0 0 8px #f39c12;
            animation: pulse 2s infinite;
        }
        
        .status-opening-soon {
            background-color: #3498db;
            box-shadow: 0 0 8px #3498db;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.6; }
            100% { opacity: 1; }
        }
        
        .time-left {
            font-weight: bold;
            color: #90e0ef;
        }
        
        .current-time-display {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: rgba(0, 0, 0, 0.3);
            padding: 12px 20px;
            border-radius: 10px;
            margin-top: 15px;
            font-family: monospace;
            font-size: 1.1rem;
        }
        
        .legend {
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
            gap: 15px;
            margin-top: 15px;
            padding: 15px;
            background: rgba(0, 0, 0, 0.2);
            border-radius: 10px;
        }
        
        .legend-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .footer {
            text-align: center;
            padding: 20px;
            margin-top: 30px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            font-size: 0.9rem;
            opacity: 0.7;
        }
        
        .market-details {
            background: rgba(0, 0, 0, 0.7);
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
            border-left: 4px solid #00b4d8;
        }
        
        .market-details h3 {
            margin-bottom: 10px;
            color: #90e0ef;
        }
        
        .market-info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-top: 10px;
        }
        
        .info-item {
            padding: 8px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 5px;
        }
        
        .info-label {
            font-size: 0.9rem;
            opacity: 0.8;
        }
        
        .info-value {
            font-weight: bold;
            margin-top: 5px;
        }
        
        .highlighted {
            background: rgba(0, 180, 216, 0.2) !important;
        }
        
        .controls {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
        }
        
        .control-btn {
            padding: 8px 15px;
            background: rgba(0, 180, 216, 0.3);
            border: none;
            border-radius: 5px;
            color: white;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 5px;
            transition: background 0.3s;
        }
        
        .control-btn:hover {
            background: rgba(0, 180, 216, 0.5);
        }
        
        .active-session {
            background: rgba(46, 204, 113, 0.15);
            border-left: 4px solid #2ecc71;
        }
        
        @media (max-width: 768px) {
            .dashboard {
                gap: 15px;
            }
            
            .markets-container, .map-container {
                padding: 15px;
            }
            
            h1 {
                font-size: 2rem;
            }
            
            .markets-table th, .markets-table td {
                padding: 10px 8px;
                font-size: 0.9rem;
            }
            
            .market-info-grid {
                grid-template-columns: 1fr;
            }
        }
        
        .leaflet-popup-content {
            color: #333;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .leaflet-popup-content h3 {
            color: #2c5364;
            margin-bottom: 10px;
        }
        
        .market-popup {
            min-width: 200px;
        }
        
        .popup-status {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .popup-open {
            background-color: #d4edda;
            color: #155724;
        }
        
        .popup-closed {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .popup-closing-soon {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .popup-opening-soon {
            background-color: #d1ecf1;
            color: #0c5460;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1><i class="fas fa-globe-americas"></i> Forex Market Hours Dashboard</h1>
            <p class="subtitle">Interactive world map showing real-time status of global financial markets. Click on market markers or table rows for detailed information.</p>
            <div class="current-time-display">
                <div><i class="far fa-clock"></i> Current UTC Time: <span id="utc-time">Loading...</span></div>
                <div><i class="fas fa-sync-alt"></i> Auto-updating every minute</div>
            </div>
        </header>
        
        <div class="dashboard">
            <div class="map-container">
                <h2 class="section-title"><i class="fas fa-map-marked-alt"></i> Global Market Map</h2>
                <div class="controls">
                    <button id="zoom-world" class="control-btn"><i class="fas fa-globe"></i> View All Markets</button>
                    <button id="show-open" class="control-btn"><i class="fas fa-eye"></i> Show Open Markets</button>
                </div>
                <div id="market-map"></div>
                <div class="legend">
                    <div class="legend-item"><div class="status-indicator status-open"></div> Open</div>
                    <div class="legend-item"><div class="status-indicator status-closed"></div> Closed</div>
                    <div class="legend-item"><div class="status-indicator status-closing-soon"></div> Closing Soon</div>
                    <div class="legend-item"><div class="status-indicator status-opening-soon"></div> Opening Soon</div>
                </div>
            </div>
            
            <div class="markets-container">
                <h2 class="section-title"><i class="fas fa-exchange-alt"></i> Market Status</h2>
                <table class="markets-table">
                    <thead>
                        <tr>
                            <th>Market</th>
                            <th>Local Time</th>
                            <th>Status</th>
                            <th>Time Left</th>
                        </tr>
                    </thead>
                    <tbody id="markets-table-body">
                        <!-- Market data will be populated by JavaScript -->
                    </tbody>
                </table>
                
                <div id="market-details" class="market-details" style="display: none;">
                    <h3 id="detail-market-name">Market Name</h3>
                    <div id="detail-market-status" class="popup-status">Status</div>
                    <div class="market-info-grid">
                        <div class="info-item">
                            <div class="info-label">Local Time</div>
                            <div class="info-value" id="detail-local-time">--:--</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">UTC Time</div>
                            <div class="info-value" id="detail-utc-time">--:--</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Opens At</div>
                            <div class="info-value" id="detail-opens-at">--:-- UTC</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Closes At</div>
                            <div class="info-value" id="detail-closes-at">--:-- UTC</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Next Event</div>
                            <div class="info-value" id="detail-next-event">None</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Time Zone</div>
                            <div class="info-value" id="detail-timezone">UTC±0</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="footer">
            <p>Forex Market Hours Dashboard with Leaflet Map | Data updates in real-time</p>
            <p>Market hours are based on standard trading sessions and may vary on holidays</p>
        </div>
    </div>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
    <script>
        // Market data with coordinates for the map
        const markets = [
            {
                id: 'sydney',
                name: 'Sydney',
                openTime: 21, // UTC hour when market opens
                closeTime: 6, // UTC hour when market closes
                localOffset: 10, // UTC+10 for Sydney
                coordinates: [-33.8688, 151.2093], // Lat, Lng
                nextEvent: 'PPI QoQ',
                country: 'Australia',
                currency: 'AUD'
            },
            {
                id: 'tokyo',
                name: 'Tokyo',
                openTime: 0,
                closeTime: 9,
                localOffset: 9,
                coordinates: [35.6762, 139.6503],
                nextEvent: 'Unemployment Rate',
                country: 'Japan',
                currency: 'JPY'
            },
            {
                id: 'hongkong',
                name: 'Hong Kong',
                openTime: 1,
                closeTime: 9,
                localOffset: 8,
                coordinates: [22.3193, 114.1694],
                nextEvent: 'GDP Growth Rate YoY',
                country: 'Hong Kong',
                currency: 'HKD'
            },
            {
                id: 'shanghai',
                name: 'Shanghai',
                openTime: 1,
                closeTime: 9,
                localOffset: 8,
                coordinates: [31.2304, 121.4737],
                nextEvent: 'NBS Manufacturing PMI',
                country: 'China',
                currency: 'CNY'
            },
            {
                id: 'mumbai',
                name: 'Mumbai',
                openTime: 3.5, // 3:30 AM
                closeTime: 11.5, // 11:30 AM
                localOffset: 5.5,
                coordinates: [19.0760, 72.8777],
                nextEvent: 'Government Budget Value',
                country: 'India',
                currency: 'INR'
            },
            {
                id: 'frankfurt',
                name: 'Frankfurt',
                openTime: 8,
                closeTime: 16,
                localOffset: 1,
                coordinates: [50.1109, 8.6821],
                nextEvent: 'Import Prices YoY',
                country: 'Germany',
                currency: 'EUR'
            },
            {
                id: 'zurich',
                name: 'Zurich',
                openTime: 9,
                closeTime: 17,
                localOffset: 1,
                coordinates: [47.3769, 8.5417],
                nextEvent: 'KOF Leading Indicators',
                country: 'Switzerland',
                currency: 'CHF'
            },
            {
                id: 'london',
                name: 'London',
                openTime: 9,
                closeTime: 18,
                localOffset: 0,
                coordinates: [51.5074, -0.1278],
                nextEvent: 'Nationwide Housing Prices MoM',
                country: 'UK',
                currency: 'GBP'
            },
            {
                id: 'euronext',
                name: 'Euronext',
                openTime: 7,
                closeTime: 16.5, // 16:30
                localOffset: 1,
                coordinates: [48.8765, 2.3592], // Paris
                nextEvent: 'Myfxbook EURUSD Sentiment',
                country: 'EU',
                currency: 'EUR'
            },
            {
                id: 'newyork',
                name: 'New York',
                openTime: 14,
                closeTime: 23,
                localOffset: -4,
                coordinates: [40.7128, -74.0060],
                nextEvent: 'NY Fed Bill Purchases',
                country: 'USA',
                currency: 'USD'
            },
            {
                id: 'toronto',
                name: 'Toronto',
                openTime: 14,
                closeTime: 23,
                localOffset: -4,
                coordinates: [43.6532, -79.3832],
                nextEvent: '10-Year Bond Auction',
                country: 'Canada',
                currency: 'CAD'
            },
            {
                id: 'singapore',
                name: 'Singapore',
                openTime: 1,
                closeTime: 9,
                localOffset: 8,
                coordinates: [1.3521, 103.8198],
                nextEvent: 'Manufacturing PMI',
                country: 'Singapore',
                currency: 'SGD'
            }
        ];

        // Global variables
        let map;
        let markers = {};
        let selectedMarketId = null;

        // Initialize the dashboard
        document.addEventListener('DOMContentLoaded', function() {
            initMap();
            updateUTCTime();
            renderMarketsTable();
            updateMarketStatus();
            
            // Set up event listeners
            document.getElementById('zoom-world').addEventListener('click', zoomToWorldView);
            document.getElementById('show-open').addEventListener('click', highlightOpenMarkets);
            
            // Update every minute
            setInterval(function() {
                updateUTCTime();
                updateMarketStatus();
            }, 60000);
            
            // Update market table every 10 seconds for countdown
            setInterval(updateMarketTable, 10000);
        });

        // Initialize Leaflet map
        function initMap() {
            // Create map centered on world view
            map = L.map('market-map').setView([20, 0], 2);
            
            // Add tile layer (OpenStreetMap)
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                maxZoom: 18,
            }).addTo(map);
            
            // Create marker cluster group for better performance
            // Note: In a real implementation, you might want to use Leaflet.markercluster plugin
            // For simplicity, we'll add markers directly
            
            // Add market markers
            markets.forEach(market => {
                addMarketMarker(market);
            });
        }

        // Add a marker for a market
        function addMarketMarker(market) {
            const status = getMarketStatus(market);
            const icon = createMarketIcon(status.class);
            
            // Create marker
            const marker = L.marker(market.coordinates, { 
                icon: icon,
                title: market.name
            }).addTo(map);
            
            // Store reference
            markers[market.id] = marker;
            
            // Add popup
            const popupContent = createPopupContent(market, status);
            marker.bindPopup(popupContent);
            
            // Add click event
            marker.on('click', function() {
                selectMarket(market.id);
            });
            
            // Add mouseover event to highlight table row
            marker.on('mouseover', function() {
                highlightTableRow(market.id, true);
            });
            
            marker.on('mouseout', function() {
                highlightTableRow(market.id, false);
            });
        }

        // Create custom icon for market markers
        function createMarketIcon(statusClass) {
            const iconColor = getIconColor(statusClass);
            const iconHtml = `<div style="
                width: 24px; 
                height: 24px; 
                background: ${iconColor}; 
                border-radius: 50%; 
                border: 2px solid white;
                box-shadow: 0 0 10px rgba(0,0,0,0.5);
                display: flex;
                align-items: center;
                justify-content: center;
                font-weight: bold;
                color: white;
                font-size: 12px;
            "></div>`;
            
            return L.divIcon({
                html: iconHtml,
                className: 'market-marker',
                iconSize: [24, 24],
                iconAnchor: [12, 12]
            });
        }

        // Get icon color based on status
        function getIconColor(statusClass) {
            switch(statusClass) {
                case 'open': return '#2ecc71';
                case 'closed': return '#e74c3c';
                case 'closing-soon': return '#f39c12';
                case 'opening-soon': return '#3498db';
                default: return '#95a5a6';
            }
        }

        // Create popup content for market marker
        function createPopupContent(market, status) {
            const localTime = getLocalTime(market.localOffset);
            const statusClass = status.class;
            const statusText = status.class.replace('-', ' ');
            
            return `
                <div class="market-popup">
                    <h3>${market.name}</h3>
                    <div class="popup-status popup-${statusClass}">${statusText.toUpperCase()}</div>
                    <p><strong>Local Time:</strong> ${localTime}</p>
                    <p><strong>Status:</strong> ${status.text}</p>
                    <p><strong>Time Left:</strong> ${status.timeLeft}</p>
                    <p><strong>Next Event:</strong> ${market.nextEvent}</p>
                    <p><strong>Country:</strong> ${market.country}</p>
                    <p><strong>Currency:</strong> ${market.currency}</p>
                </div>
            `;
        }

        // Update marker icon based on current status
        function updateMarkerIcon(marketId) {
            const market = markets.find(m => m.id === marketId);
            if (!market || !markers[marketId]) return;
            
            const status = getMarketStatus(market);
            const icon = createMarketIcon(status.class);
            markers[marketId].setIcon(icon);
            
            // Update popup content
            const popupContent = createPopupContent(market, status);
            markers[marketId].setPopupContent(popupContent);
        }

        // Select a market (when clicked on map or table)
        function selectMarket(marketId) {
            selectedMarketId = marketId;
            const market = markets.find(m => m.id === marketId);
            
            if (!market) return;
            
            // Center map on selected market
            map.setView(market.coordinates, 5);
            
            // Open popup
            if (markers[marketId]) {
                markers[marketId].openPopup();
            }
            
            // Update details panel
            updateMarketDetails(market);
            
            // Highlight table row
            highlightTableRow(marketId, true, true);
        }

        // Update market details panel
        function updateMarketDetails(market) {
            const status = getMarketStatus(market);
            const localTime = getLocalTime(market.localOffset);
            const utcTime = new Date().toUTCString().split(' ')[4];
            
            document.getElementById('market-details').style.display = 'block';
            document.getElementById('detail-market-name').textContent = `${market.name} (${market.country})`;
            document.getElementById('detail-local-time').textContent = localTime;
            document.getElementById('detail-utc-time').textContent = utcTime;
            document.getElementById('detail-opens-at').textContent = formatTime(market.openTime) + ' UTC';
            document.getElementById('detail-closes-at').textContent = formatTime(market.closeTime) + ' UTC';
            document.getElementById('detail-next-event').textContent = market.nextEvent;
            
            // Set status with appropriate class
            const statusElement = document.getElementById('detail-market-status');
            statusElement.textContent = status.class.replace('-', ' ').toUpperCase();
            statusElement.className = `popup-status popup-${status.class}`;
            
            // Set timezone
            const offset = market.localOffset >= 0 ? `+${market.localOffset}` : market.localOffset;
            document.getElementById('detail-timezone').textContent = `UTC${offset}`;
        }

        // Zoom to show all markets
        function zoomToWorldView() {
            map.setView([20, 0], 2);
        }

        // Highlight open markets
        function highlightOpenMarkets() {
            // Reset all markers to normal size
            Object.values(markers).forEach(marker => {
                // In a real implementation, you might adjust icon size
                // For now, we'll just open popups for open markets
            });
            
            // Find bounds of open markets
            const openMarkets = markets.filter(market => {
                const status = getMarketStatus(market);
                return status.isOpen;
            });
            
            if (openMarkets.length > 0) {
                const bounds = L.latLngBounds(openMarkets.map(m => m.coordinates));
                map.fitBounds(bounds, { padding: [50, 50] });
            }
        }

        // Render markets table
        function renderMarketsTable() {
            const tableBody = document.getElementById('markets-table-body');
            tableBody.innerHTML = '';
            
            markets.forEach(market => {
                const localTime = getLocalTime(market.localOffset);
                const status = getMarketStatus(market);
                
                const row = document.createElement('tr');
                row.setAttribute('data-market', market.id);
                row.innerHTML = `
                    <td>
                        <div class="market-name">
                            <i class="fas fa-city"></i> ${market.name}
                        </div>
                    </td>
                    <td>${localTime}</td>
                    <td>
                        <span class="status-indicator status-${status.class}"></span>
                        ${status.text}
                    </td>
                    <td class="time-left">${status.timeLeft}</td>
                `;
                
                // Add click event to select market
                row.addEventListener('click', function() {
                    selectMarket(market.id);
                });
                
                // Add hover events
                row.addEventListener('mouseover', function() {
                    highlightTableRow(market.id, true);
                    if (markers[market.id]) {
                        markers[market.id].fire('mouseover');
                    }
                });
                
                row.addEventListener('mouseout', function() {
                    if (selectedMarketId !== market.id) {
                        highlightTableRow(market.id, false);
                    }
                    if (markers[market.id]) {
                        markers[market.id].fire('mouseout');
                    }
                });
                
                tableBody.appendChild(row);
            });
        }

        // Update market table with current data
        function updateMarketTable() {
            const rows = document.querySelectorAll('#markets-table-body tr');
            
            rows.forEach((row, index) => {
                const market = markets[index];
                const localTime = getLocalTime(market.localOffset);
                const status = getMarketStatus(market);
                
                // Update time and status cells
                row.cells[1].textContent = localTime;
                row.cells[2].innerHTML = `<span class="status-indicator status-${status.class}"></span> ${status.text}`;
                row.cells[3].textContent = status.timeLeft;
                
                // Update marker icon
                updateMarkerIcon(market.id);
            });
        }

        // Highlight table row
        function highlightTableRow(marketId, highlight, permanent = false) {
            const row = document.querySelector(`tr[data-market="${marketId}"]`);
            
            if (!row) return;
            
            if (highlight) {
                row.classList.add('highlighted');
            } else if (!permanent || selectedMarketId !== marketId) {
                row.classList.remove('highlighted');
            }
        }

        // Calculate market status
        function getMarketStatus(market) {
            const now = new Date();
            const currentHour = now.getUTCHours() + now.getUTCMinutes() / 60;
            
            // Handle markets that close after midnight
            let isOpen;
            if (market.closeTime > market.openTime) {
                isOpen = currentHour >= market.openTime && currentHour < market.closeTime;
            } else {
                isOpen = currentHour >= market.openTime || currentHour < market.closeTime;
            }
            
            // Calculate time until next change
            let nextChangeHour, timeLeft, statusClass, statusText;
            
            if (isOpen) {
                nextChangeHour = market.closeTime;
                if (nextChangeHour < currentHour) nextChangeHour += 24;
                
                const hoursUntilClose = nextChangeHour - currentHour;
                
                if (hoursUntilClose <= 1) {
                    statusClass = 'closing-soon';
                    statusText = `Closes at ${formatTime(market.closeTime)} UTC`;
                } else {
                    statusClass = 'open';
                    statusText = `Open until ${formatTime(market.closeTime)} UTC`;
                }
                
                timeLeft = formatTimeLeft(hoursUntilClose);
            } else {
                nextChangeHour = market.openTime;
                if (nextChangeHour <= currentHour) nextChangeHour += 24;
                
                const hoursUntilOpen = nextChangeHour - currentHour;
                
                if (hoursUntilOpen <= 1) {
                    statusClass = 'opening-soon';
                    statusText = `Opens at ${formatTime(market.openTime)} UTC`;
                } else {
                    statusClass = 'closed';
                    statusText = `Opens at ${formatTime(market.openTime)} UTC`;
                }
                
                timeLeft = formatTimeLeft(hoursUntilOpen);
            }
            
            return {
                class: statusClass,
                text: statusText,
                timeLeft: timeLeft,
                isOpen: isOpen
            };
        }

        // Update all market statuses
        function updateMarketStatus() {
            markets.forEach(market => {
                updateMarkerIcon(market.id);
            });
            
            // Update details if a market is selected
            if (selectedMarketId) {
                const market = markets.find(m => m.id === selectedMarketId);
                if (market) {
                    updateMarketDetails(market);
                }
            }
        }

        // Get local time for a market
        function getLocalTime(offset) {
            const now = new Date();
            const utc = now.getTime() + (now.getTimezoneOffset() * 60000);
            const localTime = new Date(utc + (3600000 * offset));
            
            return localTime.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        }

        // Update UTC time display
        function updateUTCTime() {
            const now = new Date();
            const utcTime = now.toUTCString().split(' ')[4];
            document.getElementById('utc-time').textContent = utcTime;
        }

        // Format time (decimal hours to HH:MM)
        function formatTime(decimalHours) {
            const hours = Math.floor(decimalHours);
            const minutes = Math.round((decimalHours - hours) * 60);
            return `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}`;
        }

        // Format time left
        function formatTimeLeft(hours) {
            const totalMinutes = Math.round(hours * 60);
            const hrs = Math.floor(totalMinutes / 60);
            const mins = totalMinutes % 60;
            
            if (hrs > 0) {
                return `${hrs}h ${mins}m`;
            } else if (mins > 0) {
                return `${mins}m`;
            } else {
                return 'Closing';
            }
        }
    </script>
</body>
</html>