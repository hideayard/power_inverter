<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Economic Data Dashboard</title>
    <style>
        /* ===== Base Styles ===== */
        :root {
            --bg-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --bg-color: #ffffff;
            --text-color: #333333;
            --card-bg: #ffffff;
            --border-color: #e5e7eb;
            --header-bg: transparent;
            --header-text: #ffffff;
            --card-header-text: #4f46e5;
            --primary-btn: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --secondary-btn-bg: #e5e7eb;
            --secondary-btn-text: #374151;
            --value-color: #059669;
            --status-bar-bg: rgba(255, 255, 255, 0.9);
            --status-success-bg: rgba(220, 252, 231, 0.9);
            --status-error-bg: rgba(254, 226, 226, 0.9);
            --signal-bg: #f8fafc;
            --indicators-bg: #f8fafc;
            --event-bg: #f8fafc;
            --modal-bg: rgba(0, 0, 0, 0.5);
            --modal-content-bg: #ffffff;
            --indicator-bullish-bg: #dcfce7;
            --indicator-bullish-text: #166534;
            --indicator-bearish-bg: #fee2e2;
            --indicator-bearish-text: #991b1b;
            --indicator-neutral-bg: #fef3c7;
            --indicator-neutral-text: #92400e;
            --scrollbar-track: #f1f1f1;
            --scrollbar-thumb: #c1c1c1;
            --table-header-bg: #f3f4f6;
        }

        [data-theme="dark"] {
            --bg-gradient: linear-gradient(135deg, #2d1b69 0%, #4a1e4e 100%);
            --bg-color: #0f172a;
            --text-color: #e5e7eb;
            --card-bg: #020617;
            --border-color: #1e293b;
            --header-bg: transparent;
            --header-text: #ffffff;
            --card-header-text: #38bdf8;
            --primary-btn: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            --secondary-btn-bg: #334155;
            --secondary-btn-text: #e2e8f0;
            --value-color: #10b981;
            --status-bar-bg: rgba(30, 41, 59, 0.9);
            --status-success-bg: rgba(6, 95, 70, 0.9);
            --status-error-bg: rgba(153, 27, 27, 0.9);
            --signal-bg: #1e293b;
            --indicators-bg: #1e293b;
            --event-bg: #1e293b;
            --modal-bg: rgba(0, 0, 0, 0.7);
            --modal-content-bg: #020617;
            --indicator-bullish-bg: #064e3b;
            --indicator-bullish-text: #6ee7b7;
            --indicator-bearish-bg: #7f1d1d;
            --indicator-bearish-text: #fca5a5;
            --indicator-neutral-bg: #78350f;
            --indicator-neutral-text: #fcd34d;
            --scrollbar-track: #1e293b;
            --scrollbar-thumb: #475569;
            --table-header-bg: #1e293b;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: var(--bg-gradient);
            color: var(--text-color);
            min-height: 100vh;
            padding: 20px;
            transition: background 0.3s ease, color 0.3s ease;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        /* ===== Header ===== */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid rgba(255, 255, 255, 0.2);
        }

        .header h1 {
            font-size: 24px;
            color: var(--header-text);
            margin: 0;
        }

        .theme-toggle {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            border-radius: 20px;
            padding: 8px 16px;
            color: var(--header-text);
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.2s;
        }

        .theme-toggle:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-1px);
        }

        /* ===== Data Sections ===== */
        .data-section {
            margin-bottom: 30px;
        }

        .section-title {
            color: var(--card-header-text);
            font-size: 20px;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--border-color);
        }

        /* ===== Cards Grid ===== */
        .cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .card {
            background: var(--card-bg);
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s, background 0.3s ease;
        }

        .card:hover {
            transform: translateY(-2px);
        }

        .card h3 {
            color: var(--card-header-text);
            margin-bottom: 15px;
            font-size: 18px;
            display: flex;
            align-items: center;
        }

        .card h3::before {
            content: "•";
            margin-right: 8px;
            color: var(--card-header-text);
            opacity: 0.8;
        }

        /* ===== Status Cards ===== */
        .status-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }

        .status-card {
            background: var(--card-bg);
            border-radius: 8px;
            padding: 15px;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .status-icon {
            font-size: 24px;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .status-success .status-icon {
            background: var(--indicator-bullish-bg);
            color: var(--indicator-bullish-text);
        }

        .status-error .status-icon {
            background: var(--indicator-bearish-bg);
            color: var(--indicator-bearish-text);
        }

        .status-info .status-icon {
            background: var(--indicator-neutral-bg);
            color: var(--indicator-neutral-text);
        }

        .status-content h4 {
            font-size: 16px;
            margin-bottom: 5px;
        }

        .status-content p {
            font-size: 14px;
            opacity: 0.8;
        }

        /* ===== Data Tables ===== */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        .data-table th {
            background: var(--table-header-bg);
            color: var(--text-color);
            font-weight: 600;
            text-align: left;
            padding: 12px 15px;
            border-bottom: 2px solid var(--border-color);
        }

        .data-table td {
            padding: 12px 15px;
            border-bottom: 1px solid var(--border-color);
        }

        .data-table tr:hover {
            background: var(--event-bg);
        }

        /* ===== Economic Events ===== */
        .events-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 15px;
        }

        .event-card {
            background: var(--event-bg);
            border-radius: 8px;
            padding: 15px;
            border-left: 4px solid #3b82f6;
            transition: all 0.2s ease;
        }

        .event-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .event-card.high-impact {
            border-left-color: #ef4444;
            background: linear-gradient(to right, rgba(239, 68, 68, 0.05) 0%, var(--event-bg) 100%);
        }

        .event-card.medium-impact {
            border-left-color: #f59e0b;
        }

        .event-card.low-impact {
            border-left-color: #10b981;
        }

        .event-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .event-currency {
            background: rgba(0, 0, 0, 0.05);
            padding: 4px 8px;
            border-radius: 4px;
            font-family: monospace;
            font-weight: bold;
            font-size: 14px;
        }

        .event-impact {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .event-impact.high {
            background: #fee2e2;
            color: #991b1b;
        }

        .event-impact.medium {
            background: #fef3c7;
            color: #92400e;
        }

        .event-impact.low {
            background: #dcfce7;
            color: #166534;
        }

        .event-name {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 10px;
            line-height: 1.4;
        }

        .event-details {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin-bottom: 10px;
            font-size: 14px;
        }

        .detail-item {
            display: flex;
            flex-direction: column;
        }

        .detail-label {
            font-size: 12px;
            opacity: 0.7;
            margin-bottom: 2px;
        }

        .detail-value {
            font-weight: 600;
            color: var(--value-color);
        }

        .event-time {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 14px;
            opacity: 0.8;
        }

        /* ===== Interest Rates ===== */
        .rates-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .rate-card {
            background: var(--card-bg);
            border-radius: 10px;
            padding: 20px;
            text-align: center;
        }

        .central-bank {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 10px;
            color: var(--card-header-text);
        }

        .rate-value {
            font-size: 32px;
            font-weight: 700;
            color: var(--value-color);
            margin-bottom: 10px;
        }

        .rate-change {
            font-size: 14px;
            padding: 4px 8px;
            border-radius: 4px;
            display: inline-block;
        }

        .rate-change.positive {
            background: var(--indicator-bullish-bg);
            color: var(--indicator-bullish-text);
        }

        .rate-change.negative {
            background: var(--indicator-bearish-bg);
            color: var(--indicator-bearish-text);
        }

        .next-meeting {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid var(--border-color);
            font-size: 14px;
        }

        /* ===== Loading States ===== */
        .loading {
            text-align: center;
            padding: 40px;
            color: var(--text-color);
            opacity: 0.7;
        }

        .spinner {
            border: 3px solid var(--border-color);
            border-top: 3px solid var(--card-header-text);
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* ===== Controls ===== */
        .controls {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.2s;
            font-size: 14px;
        }

        .btn-primary {
            background: var(--primary-btn);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }

        .btn-secondary {
            background: var(--secondary-btn-bg);
            color: var(--secondary-btn-text);
        }

        .btn-secondary:hover {
            background: var(--secondary-btn-text);
            color: var(--secondary-btn-bg);
        }

        /* ===== Scrollbar ===== */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: var(--scrollbar-track);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb {
            background: var(--scrollbar-thumb);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            opacity: 0.8;
        }

        /* ===== Responsive ===== */
        @media (max-width: 768px) {
            .cards-grid {
                grid-template-columns: 1fr;
            }
            
            .events-grid {
                grid-template-columns: 1fr;
            }
            
            .event-details {
                grid-template-columns: 1fr;
            }
            
            .header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
        }
    </style>
</head>
<body data-theme="light">
    <div class="container">
        <!-- Header -->
        <header class="header">
            <h1>📊 Economic Data Dashboard</h1>
            <button id="themeToggle" class="theme-toggle">
                <span class="theme-icon">☀️</span>
                <span class="theme-text">Light Mode</span>
            </button>
        </header>

        <!-- Status Overview -->
        <div class="data-section">
            <h2 class="section-title">System Status</h2>
            <div class="status-grid" id="statusGrid">
                <!-- Status cards will be populated by JavaScript -->
                <div class="loading">
                    <div class="spinner"></div>
                    <p>Loading status data...</p>
                </div>
            </div>
        </div>

        <!-- Interest Rates -->
        <div class="data-section">
            <h2 class="section-title">Central Bank Rates</h2>
            <div class="rates-grid" id="ratesGrid">
                <div class="loading">
                    <div class="spinner"></div>
                    <p>Loading interest rates...</p>
                </div>
            </div>
        </div>

        <!-- Economic Events -->
        <div class="data-section">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2 class="section-title">Upcoming Economic Events</h2>
                <div class="controls">
                    <button id="refreshBtn" class="btn btn-primary">🔄 Refresh Data</button>
                    <button id="exportBtn" class="btn btn-secondary">📥 Export JSON</button>
                </div>
            </div>
            <div class="events-grid" id="eventsGrid">
                <div class="loading">
                    <div class="spinner"></div>
                    <p>Loading economic events...</p>
                </div>
            </div>
        </div>

        <!-- Market Data Tables -->
        <div class="data-section">
            <h2 class="section-title">Market Indicators</h2>
            <div class="cards-grid">
                <!-- Technical Indicators -->
                <div class="card">
                    <h3>Technical Analysis</h3>
                    <table class="data-table" id="technicalTable">
                        <thead>
                            <tr>
                                <th>Indicator</th>
                                <th>Value</th>
                                <th>Signal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="3" style="text-align: center;">Loading...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Market Sentiment -->
                <div class="card">
                    <h3>Market Sentiment</h3>
                    <table class="data-table" id="sentimentTable">
                        <thead>
                            <tr>
                                <th>Metric</th>
                                <th>Value</th>
                                <th>Trend</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="3" style="text-align: center;">Loading...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Debug Info -->
        <div class="card">
            <h3>System Information</h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                <div>
                    <p><strong>Last Updated:</strong> <span id="lastUpdated">-</span></p>
                    <p><strong>Data Source:</strong> <span id="dataSource">Yii2 API</span></p>
                </div>
                <div>
                    <p><strong>Total Events:</strong> <span id="totalEvents">0</span></p>
                    <p><strong>API Status:</strong> <span id="apiStatus">Checking...</span></p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Theme toggle
        const themeToggle = document.getElementById('themeToggle');
        const themeIcon = themeToggle.querySelector('.theme-icon');
        const themeText = themeToggle.querySelector('.theme-text');
        
        themeToggle.addEventListener('click', () => {
            const currentTheme = document.body.getAttribute('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            
            document.body.setAttribute('data-theme', newTheme);
            
            // Update button text and icon
            if (newTheme === 'dark') {
                themeIcon.textContent = '🌙';
                themeText.textContent = 'Dark Mode';
            } else {
                themeIcon.textContent = '☀️';
                themeText.textContent = 'Light Mode';
            }
            
            // Save preference
            localStorage.setItem('theme', newTheme);
        });

        // Load saved theme
        const savedTheme = localStorage.getItem('theme') || 'light';
        document.body.setAttribute('data-theme', savedTheme);
        if (savedTheme === 'dark') {
            themeIcon.textContent = '🌙';
            themeText.textContent = 'Dark Mode';
        }

        // API Endpoint (replace with your actual Yii2 endpoint)
        const API_ENDPOINT = 'https://itrust-tech.id/web/mobile/get-scrape-data'; // Replace with your actual endpoint

        // Sample data structure (this will be replaced with actual API call)
        const sampleData = {
            status: {
                last_updated: '2024-01-15 14:30:00',
                data_source: 'Forex Factory + Investing.com',
                total_events: 12,
                api_status: 'online'
            },
            interest_rates: [
                {
                    central_bank: 'ECB',
                    rate: '4.50%',
                    change: '+0.25%',
                    change_type: 'positive',
                    next_meeting: '2024-01-25',
                    meeting_importance: 'high'
                },
                {
                    central_bank: 'BOJ',
                    rate: '-0.10%',
                    change: '0.00%',
                    change_type: 'neutral',
                    next_meeting: '2024-01-23',
                    meeting_importance: 'medium'
                },
                {
                    central_bank: 'FED',
                    rate: '5.50%',
                    change: '0.00%',
                    change_type: 'neutral',
                    next_meeting: '2024-01-31',
                    meeting_importance: 'high'
                },
                {
                    central_bank: 'BOE',
                    rate: '5.25%',
                    change: '0.00%',
                    change_type: 'neutral',
                    next_meeting: '2024-02-01',
                    meeting_importance: 'medium'
                }
            ],
            economic_events: [
                {
                    currency: 'USD',
                    country: 'United States',
                    event_name: 'CPI (Consumer Price Index)',
                    impact: 'high',
                    actual: '3.4%',
                    previous: '3.1%',
                    consensus: '3.2%',
                    time: '2024-01-15 13:30 GMT',
                    date: 'Today'
                },
                {
                    currency: 'EUR',
                    country: 'Germany',
                    event_name: 'ZEW Economic Sentiment',
                    impact: 'medium',
                    actual: '12.8',
                    previous: '10.0',
                    consensus: '11.5',
                    time: '2024-01-15 10:00 GMT',
                    date: 'Today'
                },
                {
                    currency: 'GBP',
                    country: 'United Kingdom',
                    event_name: 'Average Earnings Index',
                    impact: 'medium',
                    actual: '7.3%',
                    previous: '7.2%',
                    consensus: '7.4%',
                    time: '2024-01-16 07:00 GMT',
                    date: 'Tomorrow'
                },
                {
                    currency: 'JPY',
                    country: 'Japan',
                    event_name: 'Core Machinery Orders',
                    impact: 'low',
                    actual: '0.7%',
                    previous: '-2.2%',
                    consensus: '0.5%',
                    time: '2024-01-16 23:50 GMT',
                    date: 'Tomorrow'
                }
            ],
            technical_indicators: [
                { name: 'RSI (14)', value: '58.4', signal: 'bullish' },
                { name: 'MACD', value: '12.5', signal: 'bullish' },
                { name: 'Moving Avg (50)', value: '145.2', signal: 'neutral' },
                { name: 'Moving Avg (200)', value: '142.8', signal: 'bullish' },
                { name: 'Stochastic', value: '72.3', signal: 'overbought' },
                { name: 'Bollinger Bands', value: '±2.5%', signal: 'neutral' }
            ],
            market_sentiment: [
                { metric: 'Bullish Ratio', value: '62%', trend: 'up' },
                { metric: 'Volatility Index', value: '15.2', trend: 'down' },
                { metric: 'Put/Call Ratio', value: '0.85', trend: 'neutral' },
                { metric: 'Advance/Decline', value: '1.42', trend: 'up' }
            ]
        };

        // Function to fetch data from API
        async function fetchData() {
            try {
                // Show loading states
                document.getElementById('apiStatus').textContent = 'Fetching...';
                document.getElementById('apiStatus').style.color = 'orange';
                
                // In a real implementation, you would use:
                const response = await fetch(API_ENDPOINT);
                const data = await response.json();
                
                // For demo purposes, we'll use sample data with a delay
                // await new Promise(resolve => setTimeout(resolve, 1000));
                // const data = sampleData;
                
                // Update UI with data
                updateStatus(data);
                updateInterestRates(data.interest_rates);
                updateEconomicEvents(data.economic_events);
                updateTechnicalIndicators(data.technical_indicators);
                updateMarketSentiment(data.market_sentiment);
                updateSystemInfo(data.status);
                
                // Update API status
                document.getElementById('apiStatus').textContent = 'Online';
                document.getElementById('apiStatus').style.color = 'var(--value-color)';
                
            } catch (error) {
                console.error('Error fetching data:', error);
                document.getElementById('apiStatus').textContent = 'Offline';
                document.getElementById('apiStatus').style.color = '#ef4444';
                
                // Show error in status grid
                document.getElementById('statusGrid').innerHTML = `
                    <div class="status-card status-error">
                        <div class="status-icon">⚠️</div>
                        <div class="status-content">
                            <h4>API Error</h4>
                            <p>Failed to fetch data from server</p>
                        </div>
                    </div>
                `;
            }
        }

        // Update status cards
        function updateStatus(data) {
            const statusGrid = document.getElementById('statusGrid');
            statusGrid.innerHTML = `
                <div class="status-card status-success">
                    <div class="status-icon">✅</div>
                    <div class="status-content">
                        <h4>Data Collection</h4>
                        <p>${data.status.total_events} events processed</p>
                    </div>
                </div>
                <div class="status-card status-info">
                    <div class="status-icon">🔄</div>
                    <div class="status-content">
                        <h4>Last Updated</h4>
                        <p>${formatDate(data.status.last_updated)}</p>
                    </div>
                </div>
                <div class="status-card status-success">
                    <div class="status-icon">📊</div>
                    <div class="status-content">
                        <h4>Data Sources</h4>
                        <p>${data.status.data_source}</p>
                    </div>
                </div>
                <div class="status-card status-info">
                    <div class="status-icon">⚡</div>
                    <div class="status-content">
                        <h4>System Status</h4>
                        <p>All systems operational</p>
                    </div>
                </div>
            `;
        }

        // Update interest rates
        function updateInterestRates(rates) {
            const ratesGrid = document.getElementById('ratesGrid');
            ratesGrid.innerHTML = rates.map(rate => `
                <div class="rate-card">
                    <div class="central-bank">${rate.central_bank}</div>
                    <div class="rate-value">${rate.rate}</div>
                    <div class="rate-change ${rate.change_type}">
                        ${rate.change !== '0.00%' ? rate.change : 'Unchanged'}
                    </div>
                    <div class="next-meeting">
                        <strong>Next Meeting:</strong> ${formatDate(rate.next_meeting)}<br>
                        <small>Importance: <span class="event-impact ${rate.meeting_importance}">${rate.meeting_importance}</span></small>
                    </div>
                </div>
            `).join('');
        }

        // Update economic events
        function updateEconomicEvents(events) {
            const eventsGrid = document.getElementById('eventsGrid');
            document.getElementById('totalEvents').textContent = events.length;
            
            eventsGrid.innerHTML = events.map(event => `
                <div class="event-card ${event.impact}-impact">
                    <div class="event-header">
                        <span class="event-currency">${event.currency}</span>
                        <span class="event-impact ${event.impact}">${event.impact}</span>
                    </div>
                    <h4 class="event-name">${event.event_name}</h4>
                    <div class="event-details">
                        <div class="detail-item">
                            <span class="detail-label">Previous</span>
                            <span class="detail-value">${event.previous}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Consensus</span>
                            <span class="detail-value">${event.consensus}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Actual</span>
                            <span class="detail-value">${event.actual}</span>
                        </div>
                    </div>
                    <div class="event-time">
                        <span>🕒</span>
                        <span>${event.date} • ${event.time}</span>
                    </div>
                </div>
            `).join('');
        }

        // Update technical indicators
        function updateTechnicalIndicators(indicators) {
            const tableBody = document.getElementById('technicalTable').querySelector('tbody');
            tableBody.innerHTML = indicators.map(indicator => `
                <tr>
                    <td>${indicator.name}</td>
                    <td>${indicator.value}</td>
                    <td>
                        <span class="event-impact ${indicator.signal === 'bullish' ? 'low' : 
                            indicator.signal === 'bearish' ? 'high' : 'medium'}">
                            ${indicator.signal}
                        </span>
                    </td>
                </tr>
            `).join('');
        }

        // Update market sentiment
        function updateMarketSentiment(sentiment) {
            const tableBody = document.getElementById('sentimentTable').querySelector('tbody');
            tableBody.innerHTML = sentiment.map(item => `
                <tr>
                    <td>${item.metric}</td>
                    <td>${item.value}</td>
                    <td>
                        ${item.trend === 'up' ? '📈' : item.trend === 'down' ? '📉' : '➡️'}
                        ${item.trend}
                    </td>
                </tr>
            `).join('');
        }

        // Update system info
        function updateSystemInfo(status) {
            document.getElementById('lastUpdated').textContent = formatDate(status.last_updated);
            document.getElementById('dataSource').textContent = status.data_source;
            document.getElementById('totalEvents').textContent = status.total_events;
        }

        // Format date
        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }

        // Export data as JSON
        function exportData() {
            const data = {
                ...sampleData,
                exported_at: new Date().toISOString()
            };
            
            const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `economic-data-${new Date().toISOString().split('T')[0]}.json`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
        }

        // Event listeners
        document.getElementById('refreshBtn').addEventListener('click', fetchData);
        document.getElementById('exportBtn').addEventListener('click', exportData);

        // Initial data load
        document.addEventListener('DOMContentLoaded', fetchData);

        // Auto-refresh every 5 minutes
        setInterval(fetchData, 5 * 60 * 1000);
    </script>
</body>
</html>