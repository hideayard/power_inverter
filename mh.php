<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forex Market Hours Dashboard with Map-Only Overlay</title>
    
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
            background: linear-gradient(135deg, #0a1929 0%, #0f2a3e 50%, #143653 100%);
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
            background: rgba(10, 25, 41, 0.7);
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
            height: 700px;
            display: flex;
            flex-direction: column;
            position: relative;
        }
        
        .markets-container {
            background: rgba(16, 30, 46, 0.8);
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            overflow-y: auto;
            max-height: 700px;
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
        
        /* Map wrapper with relative positioning for overlay */
        .map-wrapper {
            flex: 1;
            position: relative;
            border-radius: 10px;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        #market-map {
            width: 100%;
            height: 100%;
            position: relative;
            z-index: 100;
        }
        
        /* Overlay container - positioned absolutely inside map-wrapper */
        .map-overlay-container {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 200;
            border-radius: 10px;
            overflow: hidden;
        }
        
        /* Night overlay - darkens the night side */
        .night-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 200;
            pointer-events: none;
        }
        
        /* Terminator line */
        .terminator-line {
            position: absolute;
            top: 0;
            width: 6px;
            height: 100%;
            background: linear-gradient(to bottom, 
                rgba(255, 215, 0, 0) 0%,
                rgba(255, 215, 0, 0.8) 50%,
                rgba(255, 215, 0, 0) 100%);
            box-shadow: 0 0 20px rgba(255, 215, 0, 0.6);
            z-index: 201;
            pointer-events: none;
        }
        
        /* Day/Night overlay indicator */
        .day-night-indicator {
            position: absolute;
            top: 20px;
            right: 20px;
            background: rgba(0, 0, 0, 0.7);
            padding: 10px 15px;
            border-radius: 10px;
            z-index: 300;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.9rem;
            pointer-events: none;
        }
        
        .sun-icon {
            color: #FFD700;
            animation: sunGlow 3s infinite alternate;
        }
        
        .moon-icon {
            color: #E6E6FA;
            animation: moonGlow 4s infinite alternate;
        }
        
        @keyframes sunGlow {
            0% { opacity: 0.8; }
            100% { opacity: 1; text-shadow: 0 0 10px #FFD700; }
        }
        
        @keyframes moonGlow {
            0% { opacity: 0.7; }
            100% { opacity: 1; text-shadow: 0 0 8px #E6E6FA; }
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
            gap: 10px;
        }
        
        .flag-icon {
            width: 24px;
            height: 18px;
            border-radius: 3px;
            object-fit: cover;
            box-shadow: 0 2px 4px rgba(0,0,0,0.3);
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
            position: relative;
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
            display: flex;
            align-items: center;
            gap: 10px;
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
            flex-wrap: wrap;
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
        
        .day-night-info {
            background: rgba(10, 25, 41, 0.8);
            border-radius: 10px;
            padding: 15px;
            margin-top: 20px;
            border-left: 4px solid #FFD700;
        }
        
        .day-night-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
            color: #FFD700;
        }
        
        .time-of-day-bar {
            height: 20px;
            background: linear-gradient(90deg, 
                #143653 0%,     /* Midnight */
                #1a4d7a 12.5%,  /* Early morning */
                #2c7da0 25%,    /* Morning */
                #61a5c2 37.5%,  /* Late morning */
                #a9d6e5 50%,    /* Noon */
                #61a5c2 62.5%,  /* Afternoon */
                #2c7da0 75%,    /* Evening */
                #1a4d7a 87.5%,  /* Night */
                #143653 100%    /* Midnight */
            );
            border-radius: 10px;
            margin: 10px 0;
            position: relative;
            overflow: hidden;
        }
        
        .time-indicator {
            position: absolute;
            top: 0;
            width: 4px;
            height: 100%;
            background: white;
            box-shadow: 0 0 10px white;
            z-index: 10;
        }
        
        .time-labels {
            display: flex;
            justify-content: space-between;
            font-size: 0.8rem;
            opacity: 0.8;
            margin-top: 5px;
        }
        
        .globe-icon {
            font-size: 1.5rem;
            margin-right: 5px;
        }
        
        @media (max-width: 768px) {
            .dashboard {
                gap: 15px;
            }
            
            .markets-container, .map-container {
                padding: 15px;
                height: auto;
            }
            
            .map-container {
                height: 500px;
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
            
            .controls {
                flex-direction: column;
            }
        }
        
        .leaflet-popup-content {
            color: #333;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .leaflet-popup-content h3 {
            color: #2c5364;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .market-popup {
            min-width: 250px;
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
        
        .popup-flag {
            width: 32px;
            height: 24px;
            border-radius: 4px;
            object-fit: cover;
        }
        
        /* Custom marker styling with flags */
        .custom-market-marker {
            background: white;
            border-radius: 50%;
            width: 44px !important;
            height: 44px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            border: 3px solid white;
            box-shadow: 0 0 10px rgba(0,0,0,0.5);
            transition: all 0.3s ease;
        }
        
        .market-marker-inner {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }
        
        .market-marker-flag {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .market-marker-status {
            position: absolute;
            bottom: -3px;
            right: -3px;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            border: 2px solid white;
        }
        
        /* Time zone info */
        .timezone-info {
            margin-top: 15px;
            padding: 10px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 8px;
            font-size: 0.9rem;
        }
        
        .client-time-highlight {
            color: #FFD700;
            font-weight: bold;
        }
        
        /* Sun/Moon position indicators */
        .celestial-indicator {
            position: absolute;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            z-index: 202;
            pointer-events: none;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            opacity: 0.8;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1><i class="fas fa-globe-americas"></i> Forex Market Hours Dashboard</h1>
            <p class="subtitle">Interactive world map with day/night visualization confined to map area. Map is fully interactive.</p>
            <div class="current-time-display">
                <div><i class="far fa-clock"></i> Your Local Time: <span id="client-time">Loading...</span></div>
                <div><i class="fas fa-globe"></i> UTC Time: <span id="utc-time">Loading...</span></div>
            </div>
            <div class="timezone-info">
                <i class="fas fa-info-circle"></i> Day/Night overlay is <span class="client-time-highlight">confined to the map area only</span>. 
                The dark overlay shows nighttime areas based on your local time.
            </div>
        </header>
        
        <div class="dashboard">
            <div class="map-container">
                <h2 class="section-title"><i class="fas fa-map-marked-alt"></i> Global Market Map</h2>
                <div class="controls">
                    <button id="zoom-world" class="control-btn"><i class="fas fa-globe"></i> View All Markets</button>
                    <button id="show-open" class="control-btn"><i class="fas fa-eye"></i> Show Open Markets</button>
                    <button id="toggle-visualization" class="control-btn"><i class="fas fa-adjust"></i> Toggle Day/Night</button>
                </div>
                
                <!-- Map wrapper with proper positioning -->
                <div class="map-wrapper">
                    <!-- The actual Leaflet map -->
                    <div id="market-map"></div>
                    
                    <!-- Overlay container - ONLY covers the map -->
                    <div class="map-overlay-container" id="map-overlay-container">
                        <!-- Night overlay will be generated here -->
                    </div>
                    
                    <!-- Day/Night indicator - positioned over map -->
                    <div class="day-night-indicator">
                        <i class="fas fa-sun sun-icon"></i>
                        <span id="day-night-text">Calculating...</span>
                        <i class="fas fa-moon moon-icon"></i>
                    </div>
                </div>
                
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
                    <h3 id="detail-market-name">
                        <img id="detail-flag" class="flag-icon" src="" alt="Flag">
                        <span>Market Name</span>
                    </h3>
                    <div id="detail-market-status" class="popup-status">Status</div>
                    <div class="market-info-grid">
                        <div class="info-item">
                            <div class="info-label">Local Time</div>
                            <div class="info-value" id="detail-local-time">--:--</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Day/Night</div>
                            <div class="info-value" id="detail-daynight">--</div>
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
                            <div class="info-label">Currency</div>
                            <div class="info-value" id="detail-currency">---</div>
                        </div>
                    </div>
                </div>
                
                <div class="day-night-info">
                    <div class="day-night-header">
                        <i class="fas fa-globe globe-icon"></i>
                        <h3>Day/Night Cycle (Your Time)</h3>
                    </div>
                    <p style="margin-bottom: 10px; font-size: 0.95rem;">
                        <i class="fas fa-user-circle client-time-highlight"></i> Based on <strong>your local time</strong>: 
                        <span id="client-local-time-display">--:--</span>
                    </p>
                    <div class="time-of-day-bar" id="time-of-day-bar">
                        <div class="time-indicator" id="time-indicator"></div>
                    </div>
                    <div class="time-labels">
                        <span>00:00</span>
                        <span>06:00</span>
                        <span>12:00</span>
                        <span>18:00</span>
                        <span>24:00</span>
                    </div>
                    <p style="margin-top: 10px; font-size: 0.9rem; opacity: 0.9;">
                        <i class="fas fa-sun sun-icon"></i> The yellow line on the map shows the day/night terminator. 
                        <i class="fas fa-moon moon-icon"></i> Dark overlay shows nighttime areas based on <span class="client-time-highlight">your local time</span>.
                    </p>
                </div>
            </div>
        </div>
        
        <div class="footer">
            <p>Forex Market Hours Dashboard | Day/Night overlay confined to map area | Map is fully interactive</p>
            <p>Visualization based on your local time | Overlay doesn't interfere with map controls</p>
        </div>
    </div>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
    <script>
        // Market data with coordinates and flag information
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
                countryCode: 'au',
                currency: 'AUD',
                flagEmoji: '🇦🇺'
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
                countryCode: 'jp',
                currency: 'JPY',
                flagEmoji: '🇯🇵'
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
                countryCode: 'hk',
                currency: 'HKD',
                flagEmoji: '🇭🇰'
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
                countryCode: 'cn',
                currency: 'CNY',
                flagEmoji: '🇨🇳'
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
                countryCode: 'in',
                currency: 'INR',
                flagEmoji: '🇮🇳'
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
                countryCode: 'de',
                currency: 'EUR',
                flagEmoji: '🇩🇪'
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
                countryCode: 'ch',
                currency: 'CHF',
                flagEmoji: '🇨🇭'
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
                countryCode: 'gb',
                currency: 'GBP',
                flagEmoji: '🇬🇧'
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
                countryCode: 'eu',
                currency: 'EUR',
                flagEmoji: '🇪🇺'
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
                countryCode: 'us',
                currency: 'USD',
                flagEmoji: '🇺🇸'
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
                countryCode: 'ca',
                currency: 'CAD',
                flagEmoji: '🇨🇦'
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
                countryCode: 'sg',
                currency: 'SGD',
                flagEmoji: '🇸🇬'
            }
        ];

        // Global variables
        let map;
        let markers = {};
        let selectedMarketId = null;
        let visualizationEnabled = true;
        let clientTimezoneOffset = new Date().getTimezoneOffset() / -60; // Hours offset from UTC
        let terminatorLine = null;
        let nightOverlay = null;
        let sunElement = null;
        let moonElement = null;

        // Initialize the dashboard
        document.addEventListener('DOMContentLoaded', function() {
            initMap();
            updateTimeDisplays();
            initOverlayElements();
            updateDayNightVisualization();
            renderMarketsTable();
            updateMarketStatus();
            
            // Set up event listeners
            document.getElementById('zoom-world').addEventListener('click', zoomToWorldView);
            document.getElementById('show-open').addEventListener('click', highlightOpenMarkets);
            document.getElementById('toggle-visualization').addEventListener('click', toggleVisualization);
            
            // Update every minute
            setInterval(function() {
                updateTimeDisplays();
                updateMarketStatus();
                updateDayNightVisualization();
            }, 60000);
            
            // Update market table every 10 seconds for countdown
            setInterval(updateMarketTable, 10000);
            
            // Update visualization more frequently for smooth movement
            setInterval(updateDayNightVisualization, 30000);
        });

        // Initialize Leaflet map with zoom limits
        function initMap() {
            // Create map centered on world view with zoom limits
            map = L.map('market-map', {
                center: [20, 0],
                zoom: 2,
                minZoom: 1,  // Prevent zooming out too far
                maxZoom: 8,  // Prevent zooming in too close
                maxBounds: [[-85, -180], [85, 180]], // Limit panning
                maxBoundsViscosity: 1.0 // How much the map resists going beyond bounds
            });
            
            // Add tile layer
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                maxZoom: 8,
            }).addTo(map);
            
            // Add event listener for zoom end to enforce limits
            map.on('zoomend', function() {
                const currentZoom = map.getZoom();
                if (currentZoom < 1) map.setZoom(1);
                if (currentZoom > 8) map.setZoom(8);
            });
            
            // Add market markers
            markets.forEach(market => {
                addMarketMarker(market);
            });
        }

        // Initialize overlay elements
        function initOverlayElements() {
            const overlayContainer = document.getElementById('map-overlay-container');
            
            // Create night overlay
            nightOverlay = document.createElement('div');
            nightOverlay.className = 'night-overlay';
            nightOverlay.id = 'night-overlay';
            overlayContainer.appendChild(nightOverlay);
            
            // Create terminator line
            terminatorLine = document.createElement('div');
            terminatorLine.className = 'terminator-line';
            terminatorLine.id = 'terminator-line';
            overlayContainer.appendChild(terminatorLine);
            
            // Create sun element
            sunElement = document.createElement('div');
            sunElement.className = 'celestial-indicator';
            sunElement.innerHTML = '☀️';
            sunElement.id = 'sun-indicator';
            overlayContainer.appendChild(sunElement);
            
            // Create moon element
            moonElement = document.createElement('div');
            moonElement.className = 'celestial-indicator';
            moonElement.innerHTML = '🌙';
            moonElement.id = 'moon-indicator';
            overlayContainer.appendChild(moonElement);
        }

        // Add a marker for a market with country flag
        function addMarketMarker(market) {
            const status = getMarketStatus(market);
            const icon = createMarketIcon(market, status.class);
            
            // Create marker
            const marker = L.marker(market.coordinates, { 
                icon: icon,
                title: `${market.name} (${market.country})`
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

        // Create custom icon for market markers with flags
        function createMarketIcon(market, statusClass) {
            const statusColor = getStatusColor(statusClass);
            const flagUrl = `https://flagcdn.com/w40/${market.countryCode}.png`;
            
            const iconHtml = `
                <div class="custom-market-marker">
                    <div class="market-marker-inner">
                        <img src="${flagUrl}" alt="${market.country} flag" class="market-marker-flag" 
                             onerror="this.src='https://flagcdn.com/w40/un.png'">
                        <div class="market-marker-status" style="background-color: ${statusColor};"></div>
                    </div>
                </div>
            `;
            
            return L.divIcon({
                html: iconHtml,
                className: '',
                iconSize: [44, 44],
                iconAnchor: [22, 22]
            });
        }

        // Get status color
        function getStatusColor(statusClass) {
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
            
            // Determine if market is in day or night based on local time
            const localHour = new Date().getUTCHours() + market.localOffset;
            const adjustedHour = (localHour + 24) % 24; // Handle negative values
            const isDayAtMarket = adjustedHour >= 6 && adjustedHour < 18;
            const dayNightText = isDayAtMarket ? 'Day' : 'Night';
            const dayNightIcon = isDayAtMarket ? '☀️' : '🌙';
            
            const flagUrl = `https://flagcdn.com/w80/${market.countryCode}.png`;
            
            return `
                <div class="market-popup">
                    <h3>
                        <img src="${flagUrl}" alt="${market.country} flag" class="popup-flag"
                             onerror="this.src='https://flagcdn.com/w80/un.png'">
                        ${market.name}
                    </h3>
                    <div class="popup-status popup-${statusClass}">${statusText.toUpperCase()}</div>
                    <p><strong>Country:</strong> ${market.country} ${market.flagEmoji}</p>
                    <p><strong>Local Time:</strong> ${localTime} ${dayNightIcon} (${dayNightText})</p>
                    <p><strong>Status:</strong> ${status.text}</p>
                    <p><strong>Time Left:</strong> ${status.timeLeft}</p>
                    <p><strong>Currency:</strong> ${market.currency}</p>
                    <p><strong>Next Event:</strong> ${market.nextEvent}</p>
                </div>
            `;
        }

        // Update marker icon based on current status
        function updateMarkerIcon(marketId) {
            const market = markets.find(m => m.id === marketId);
            if (!market || !markers[marketId]) return;
            
            const status = getMarketStatus(market);
            const icon = createMarketIcon(market, status.class);
            markers[marketId].setIcon(icon);
            
            // Update popup content
            const popupContent = createPopupContent(market, status);
            markers[marketId].setPopupContent(popupContent);
        }

        // Toggle day/night visualization
        function toggleVisualization() {
            visualizationEnabled = !visualizationEnabled;
            const overlayContainer = document.getElementById('map-overlay-container');
            const dayNightIndicator = document.querySelector('.day-night-indicator');
            
            if (visualizationEnabled) {
                overlayContainer.style.display = 'block';
                dayNightIndicator.style.display = 'flex';
                document.getElementById('toggle-visualization').innerHTML = 
                    '<i class="fas fa-eye-slash"></i> Hide Day/Night';
            } else {
                overlayContainer.style.display = 'none';
                dayNightIndicator.style.display = 'none';
                document.getElementById('toggle-visualization').innerHTML = 
                    '<i class="fas fa-eye"></i> Show Day/Night';
            }
        }

        // Update time displays
        function updateTimeDisplays() {
            const now = new Date();
            
            // Client local time
            const clientTime = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
            document.getElementById('client-time').textContent = clientTime;
            
            // UTC time
            const utcTime = now.toUTCString().split(' ')[4];
            document.getElementById('utc-time').textContent = utcTime;
            
            // Update client local time in day/night info
            document.getElementById('client-local-time-display').textContent = clientTime;
        }

        // Update day/night visualization based on CLIENT LOCAL TIME
        function updateDayNightVisualization() {
            if (!visualizationEnabled) return;
            
            const now = new Date();
            const clientHour = now.getHours();
            const clientMinute = now.getMinutes();
            const clientTime = clientHour + clientMinute / 60;
            
            // Calculate terminator position based on CLIENT LOCAL TIME
            // When it's noon for the client (12:00), terminator is 180° away
            // Terminator longitude = (clientHour - 12) * 15 + clientTimezoneOffset * 15
            const terminatorLongitude = ((clientHour - 12) * 15) + (clientTimezoneOffset * 15);
            
            // Normalize to -180 to 180 range
            let normalizedLongitude = terminatorLongitude % 360;
            if (normalizedLongitude > 180) normalizedLongitude -= 360;
            if (normalizedLongitude < -180) normalizedLongitude += 360;
            
            // Update time indicator position
            const indicator = document.getElementById('time-indicator');
            const percentage = (clientTime / 24) * 100;
            indicator.style.left = `calc(${percentage}% - 2px)`;
            
            // Update night overlay based on client time
            updateNightOverlay(normalizedLongitude, clientHour);
            
            // Update terminator line position on map
            updateTerminatorPosition(normalizedLongitude);
            
            // Update celestial bodies
            updateCelestialBodies(normalizedLongitude, clientHour);
            
            // Update day/night indicator text
            updateDayNightIndicator(clientHour);
            
            // Update time displays
            updateTimeDisplays();
        }

        // Update night overlay - darkens the night side
        function updateNightOverlay(terminatorLongitude, clientHour) {
            if (!nightOverlay) return;
            
            // Calculate gradient based on terminator position
            // Night is from terminator to terminator+180° (wrapping around)
            let nightStart = terminatorLongitude;
            let nightEnd = terminatorLongitude + 180;
            
            // Normalize to 0-360 range for CSS gradient
            if (nightStart < 0) nightStart += 360;
            if (nightEnd > 360) nightEnd -= 360;
            
            // Convert to percentages for CSS gradient
            const startPercent = (nightStart / 360) * 100;
            const endPercent = (nightEnd / 360) * 100;
            
            // Create gradient for night overlay
            // Dark overlay covers the night side, transparent on day side
            let gradient;
            
            if (startPercent < endPercent) {
                // Simple case: night doesn't wrap around
                gradient = `linear-gradient(
                    90deg,
                    rgba(10, 25, 41, 0) 0%,
                    rgba(10, 25, 41, 0) ${startPercent}%,
                    rgba(10, 25, 41, 0.7) ${Math.max(startPercent + 1, 0)}%,
                    rgba(10, 25, 41, 0.7) ${endPercent}%,
                    rgba(10, 25, 41, 0) ${Math.min(endPercent + 1, 100)}%,
                    rgba(10, 25, 41, 0) 100%
                )`;
            } else {
                // Night wraps around (e.g., start at 300%, end at 120%)
                gradient = `linear-gradient(
                    90deg,
                    rgba(10, 25, 41, 0.7) 0%,
                    rgba(10, 25, 41, 0.7) ${endPercent}%,
                    rgba(10, 25, 41, 0) ${Math.min(endPercent + 1, 100)}%,
                    rgba(10, 25, 41, 0) ${startPercent}%,
                    rgba(10, 25, 41, 0.7) ${Math.max(startPercent + 1, 0)}%,
                    rgba(10, 25, 41, 0.7) 100%
                )`;
            }
            
            nightOverlay.style.background = gradient;
            
            // Adjust opacity based on time of day (darker at midnight, lighter at dawn/dusk)
            const opacity = clientHour >= 18 || clientHour < 6 ? 0.7 : 0.4;
            nightOverlay.style.opacity = opacity;
        }

        // Update terminator line position on map
        function updateTerminatorPosition(terminatorLongitude) {
            if (!terminatorLine) return;
            
            // Convert longitude to percentage of map width
            const mapWidth = document.querySelector('.map-wrapper').offsetWidth;
            const longitudeToX = (lon) => {
                // Convert longitude (-180 to 180) to pixel position
                return ((lon + 180) / 360) * 100;
            };
            
            const xPosition = longitudeToX(terminatorLongitude);
            terminatorLine.style.left = `${xPosition}%`;
            
            // Add glow effect based on client time
            const clientHour = new Date().getHours();
            if (clientHour >= 6 && clientHour < 18) {
                // Daytime - brighter terminator
                terminatorLine.style.background = 'linear-gradient(to bottom, rgba(255, 215, 0, 0) 0%, rgba(255, 215, 0, 0.8) 50%, rgba(255, 215, 0, 0) 100%)';
                terminatorLine.style.boxShadow = '0 0 25px rgba(255, 215, 0, 0.7)';
            } else {
                // Nighttime - softer terminator
                terminatorLine.style.background = 'linear-gradient(to bottom, rgba(173, 216, 230, 0) 0%, rgba(173, 216, 230, 0.6) 50%, rgba(173, 216, 230, 0) 100%)';
                terminatorLine.style.boxShadow = '0 0 20px rgba(173, 216, 230, 0.5)';
            }
        }

        // Update celestial bodies positions
        function updateCelestialBodies(terminatorLongitude, clientHour) {
            if (!sunElement || !moonElement) return;
            
            // Sun position (opposite side of terminator)
            const sunLongitude = (terminatorLongitude + 180) % 360;
            if (sunLongitude > 180) sunLongitude -= 360;
            
            // Moon position (90 degrees from sun)
            const moonLongitude = (sunLongitude + 90) % 360;
            if (moonLongitude > 180) moonLongitude -= 360;
            
            // Random latitudes for visual interest
            const sunLat = 20 + Math.sin(clientHour * Math.PI / 12) * 30;
            const moonLat = -20 + Math.cos(clientHour * Math.PI / 12) * 30;
            
            // Convert to pixel positions
            const longitudeToX = (lon) => {
                return ((lon + 180) / 360) * 100;
            };
            
            const latitudeToY = (lat) => {
                return ((90 - lat) / 180) * 100;
            };
            
            // Update positions
            sunElement.style.left = `${longitudeToX(sunLongitude)}%`;
            sunElement.style.top = `${latitudeToY(sunLat)}%`;
            
            moonElement.style.left = `${longitudeToX(moonLongitude)}%`;
            moonElement.style.top = `${latitudeToY(moonLat)}%`;
            
            // Show/hide based on day/night
            if (clientHour >= 6 && clientHour < 18) {
                sunElement.style.opacity = '0.8';
                moonElement.style.opacity = '0.3';
            } else {
                sunElement.style.opacity = '0.3';
                moonElement.style.opacity = '0.8';
            }
        }

        // Update day/night indicator text
        function updateDayNightIndicator(clientHour) {
            const dayNightText = document.getElementById('day-night-text');
            const isDaytime = clientHour >= 6 && clientHour < 18;
            
            if (isDaytime) {
                dayNightText.textContent = 'Daytime (Your Local)';
                dayNightText.style.color = '#FFD700';
            } else {
                dayNightText.textContent = 'Nighttime (Your Local)';
                dayNightText.style.color = '#E6E6FA';
            }
        }

        // Select a market (when clicked on map or table)
        function selectMarket(marketId) {
            selectedMarketId = marketId;
            const market = markets.find(m => m.id === marketId);
            
            if (!market) return;
            
            // Center map on selected market (with zoom limit)
            map.setView(market.coordinates, Math.min(map.getZoom(), 5));
            
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
            
            // Determine if market is in day or night
            const localHour = new Date().getUTCHours() + market.localOffset;
            const adjustedHour = (localHour + 24) % 24;
            const isDayAtMarket = adjustedHour >= 6 && adjustedHour < 18;
            const dayNightText = isDayAtMarket ? 'Daytime ☀️' : 'Nighttime 🌙';
            
            const flagUrl = `https://flagcdn.com/w40/${market.countryCode}.png`;
            
            document.getElementById('market-details').style.display = 'block';
            document.getElementById('detail-market-name').innerHTML = 
                `<img src="${flagUrl}" class="flag-icon" alt="${market.country} flag" 
                      onerror="this.src='https://flagcdn.com/w40/un.png'">
                 <span>${market.name} (${market.country})</span>`;
            
            document.getElementById('detail-local-time').textContent = localTime;
            document.getElementById('detail-daynight').textContent = dayNightText;
            document.getElementById('detail-opens-at').textContent = formatTime(market.openTime) + ' UTC';
            document.getElementById('detail-closes-at').textContent = formatTime(market.closeTime) + ' UTC';
            document.getElementById('detail-next-event').textContent = market.nextEvent;
            document.getElementById('detail-currency').textContent = market.currency;
            
            // Set status with appropriate class
            const statusElement = document.getElementById('detail-market-status');
            statusElement.textContent = status.class.replace('-', ' ').toUpperCase();
            statusElement.className = `popup-status popup-${status.class}`;
        }

        // Zoom to show all markets (within zoom limits)
        function zoomToWorldView() {
            map.setView([20, 0], 2);
        }

        // Highlight open markets
        function highlightOpenMarkets() {
            // Find bounds of open markets
            const openMarkets = markets.filter(market => {
                const status = getMarketStatus(market);
                return status.isOpen;
            });
            
            if (openMarkets.length > 0) {
                const bounds = L.latLngBounds(openMarkets.map(m => m.coordinates));
                // Fit bounds with padding and ensure we don't exceed max zoom
                map.fitBounds(bounds, { 
                    padding: [50, 50],
                    maxZoom: 6 // Limit zoom when showing open markets
                });
            }
        }

        // Render markets table with flags
        function renderMarketsTable() {
            const tableBody = document.getElementById('markets-table-body');
            tableBody.innerHTML = '';
            
            markets.forEach(market => {
                const localTime = getLocalTime(market.localOffset);
                const status = getMarketStatus(market);
                const flagUrl = `https://flagcdn.com/w20/${market.countryCode}.png`;
                
                const row = document.createElement('tr');
                row.setAttribute('data-market', market.id);
                row.innerHTML = `
                    <td>
                        <div class="market-name">
                            <img src="${flagUrl}" class="flag-icon" alt="${market.country} flag" 
                                 onerror="this.src='https://flagcdn.com/w20/un.png'">
                            ${market.name}
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