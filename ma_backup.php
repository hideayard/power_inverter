<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>TradingView Market Dashboard</title>
    <script type="text/javascript" src="https://s3.tradingview.com/tv.js"></script>
    <link rel="stylesheet" href="../assets/css/ma.css">

</head>

<body>
    <div class="container">
        <header class="dashboard-header">
            <div class="header-left">
                <h1>📈 Market Analysis Dashboard</h1>
                <p>
                    Advanced real-time charts, market data, economic calendar, and news
                    in one unified view
                </p>
            </div>
            <div class="controls">
                <div class="control-group">
                    <label for="pair">Currency Pair</label>
                    <select id="pair">
                        <option value="EURJPY" selected>EUR/JPY</option>
                        <option value="USDJPY">USD/JPY</option>
                        <option value="EURUSD">EUR/USD</option>
                        <option value="GBPUSD">GBP/USD</option>
                        <option value="XAUUSD">XAU/USD</option>
                        <option value="XAGUSD">XAG/USD</option>
                        <option value="BTCUSD">BTC/USD</option>
                    </select>
                </div>
                <div class="control-group">
                    <label for="tf">Timeframe</label>
                    <select id="tf">
                        <option value="15">15min</option>
                        <option value="30">30min</option>
                        <option value="60">1H</option>
                        <option value="240" selected>4H</option>
                        <option value="D">Daily</option>
                        <option value="W">Weekly</option>
                    </select>
                </div>
                <div class="control-group">
                    <label>&nbsp;</label>
                    <button onclick="loadChart()">Load Chart</button>
                </div>
            </div>
        </header>

        <main class="dashboard-layout">
            <!-- Left Sidebar - Financial News -->
            <aside class="sidebar sidebar-left">
                <section class="widget">
                    <div class="widget-header">
                        <h2>📰 Financial News</h2>
                        <span class="widget-badge">Live</span>
                    </div>
                    <div class="widget-content">
                        <!-- TradingView Widget BEGIN -->
                        <div class="tradingview-widget-container">
                            <div class="tradingview-widget-container__widget"></div>
                            <div class="tradingview-widget-copyright">
                                <a href="https://www.tradingview.com/news/top-providers/tradingview/"
                                    rel="noopener nofollow"
                                    target="_blank"><span class="blue-text">Top stories</span></a>
                                <span class="trademark"> by TradingView</span>
                            </div>
                            <script type="text/javascript"
                                src="https://s3.tradingview.com/external-embedding/embed-widget-timeline.js"
                                async>
                                {
                                    "displayMode": "compact",
                                    "feedMode": "market",
                                    "colorTheme": "dark",
                                    "isTransparent": false,
                                    "locale": "en",
                                    "market": "forex",
                                    "width": "100%",
                                    "height": "100%"
                                }
                            </script>
                        </div>
                        <!-- TradingView Widget END -->
                    </div>
                </section>
            </aside>

            <!-- Main Content Area - Scrollable -->
            <div class="main-content-scrollable">
                <div class="main-content-wrapper">
                    <!-- Chart Section -->
                    <section class="chart-section">
                        <div class="chart-header">
                            <div class="chart-title">
                                <h2 id="current-pair">EURJPY - 4H Chart</h2>
                                <div class="chart-meta">
                                    <span class="exchange">FXCM</span>
                                    <span class="spread">Spread: 0.8 pips</span>
                                </div>
                            </div>
                            <div class="chart-controls">
                                <div class="status-indicator">
                                    <span class="live-dot"></span> Live Market Data
                                </div>
                                <button class="refresh-btn" onclick="loadChart()">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M23 4v6h-6M1 20v-6h6M3.51 9a9 9 0 0114.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0020.49 15" />
                                    </svg>
                                    Refresh
                                </button>
                                <button class="fullscreen-btn" onclick="toggleFullscreen()">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3" />
                                    </svg>
                                    Fullscreen
                                </button>
                            </div>
                        </div>
                        <div class="chart-container">
                            <div id="chart"></div>
                        </div>
                    </section>

                    <!-- Lower Dashboard Grid -->
                    <div class="lower-dashboard">
                        <section class="widget full-width">
                            <div class="widget-header">
                                <h2>📊 Market Data Watchlist</h2>
                            </div>
                            <div class="widget-content">
                                <div class="watchlist-container" id="watchlist-all">
                                    <tv-market-summary
                                        symbol-sectors='[{"sectionName":"Currency","symbols":["OANDA:EURJPY","OANDA:EURUSD","OANDA:USDJPY","OANDA:GBPUSD","OANDA:GBPJPY"]},{"sectionName":"Crypto","symbols":["BINANCEUS:BTCUSDT","BINANCEUS:ETHUSDT","BINANCEUS:XRPUSDT","BINANCEUS:SOLUSDT","OKX:HYPEUSDT","BINANCE:BNBUSDT","CRYPTOCAP:TOTAL3","OKX:XAUTUSDT"]},{"sectionName":"Stocks","symbols":["SPREADEX:SPX","NASDAQ:TSLA","NASDAQ:NVDA","NASDAQ:GOOGL","FXOPEN:DXY","IDX:BBCA","IDX:COMPOSITE","IDX:ANTM","IDX:BBRI"]},{"sectionName":"Commodity","symbols":["CMCMARKETS:GOLD","CMCMARKETS:SILVER","TVC:USOIL"]}]'
                                        show-time-range layout-mode="grid" item-size="compact" mode="custom"></tv-market-summary>
                                </div>
                                <script type="module"
                                    src="https://widgets.tradingview-widget.com/w/en/tv-market-summary.js"></script>
                            </div>
                        </section>

                        <section class="widget full-width">
                            <div class="widget-header">
                                <h2>📈 Market Analysis First Source</h2>
                                <div class="timeframe-display" id="current-tf">4H</div>
                            </div>
                            <div class="widget-content">
                                <div class="analysis-placeholder">
                                    <div class="analysis-item">
                                        <div class="ta-container" id="taContainer">
                                            <div class="signal" id="signal">
                                                <span class="label">Overall Signal:</span>
                                                <span class="value" id="overallSignal">-</span>
                                            </div>
                                            <div class="indicators">
                                                <h3>Indicators (<span id="indicatorsCount">0</span>)</h3>
                                                <div id="indicatorsList"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="analysis-item">
                                        <h3>Support & Resistance</h3>
                                        <div class="indicator-row">
                                            <span>Support:</span>
                                            <span class="support" id="supportLevel">158.50</span>
                                        </div>
                                        <div class="indicator-row">
                                            <span>Resistance:</span>
                                            <span class="resistance" id="resistanceLevel">160.20</span>
                                        </div>
                                        <div class="indicator-row">
                                            <span>Pivot:</span>
                                            <span class="pivot" id="pivotLevel">159.35</span>
                                        </div>
                                        <div class="indicator-row">
                                            <span>Range:</span>
                                            <span id="rangeValue">170 pips</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <section class="widget full-width">
                            <div class="widget-header">
                                <h2>📈 Market Analysis Second Source</h2>
                                <div class="timeframe-display" id="current-tf-second">4H</div>
                            </div>
                            <div class="widget-content">
                                <div class="analysis-second-placeholder">
                                    <!-- Summary with Gauge -->
                                    <div class="analysis-item">
                                        <h3>Summary</h3>

                                        <div class="wrapper">
                                            <div id="gauge" class="gauge" style="--angle: 30deg;">
                                                <div class="slice-colors">
                                                    <div class="st slice-item"></div>
                                                    <div class="st slice-item"></div>
                                                    <div class="st slice-item"></div>
                                                    <div class="st slice-item"></div>
                                                    <div class="st slice-item"></div>
                                                </div>

                                                <div id="arrow-speedometer" class="needle"></div>
                                                <div class="gauge-center"></div>
                                            </div>
                                            <div class="gauge-rating text-white" id="gaugeRating">Neutral</div>
                                        </div>

                                        <div class="summary-details">
                                            <div class="indicator-row">
                                                <span>Moving Averages:</span>
                                                <span id="maRating">Neutral</span>
                                                <span id="maCounts">Buy: 0 | Sell: 0</span>
                                            </div>
                                            <div class="indicator-row">
                                                <span>Technical Indicators:</span>
                                                <span id="tiRating">Neutral</span>
                                                <span id="tiCounts">Buy: 0 | Sell: 0</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Technical Indicators Table -->
                                    <div class="analysis-item">
                                        <h3>Technical Indicators</h3>
                                        <div class="table-header">
                                            <span>Summary: <strong class="rating-text" id="tiSummary">Neutral</strong></span>
                                            <span id="tiSummaryCounts">Buy: 0 | Sell: 0</span>
                                            <span class="timestamp" id="tiTimestamp">-</span>
                                        </div>
                                        <div class="table-container">
                                            <table class="rates-table" id="tiTable">
                                                <thead>
                                                    <tr>
                                                        <th class="name-col">
                                                            <span>Name</span>
                                                        </th>
                                                        <th class="value-col">
                                                            <span>Value</span>
                                                        </th>
                                                        <th class="action-col">
                                                            <span>Action</span>
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody id="tiTableBody">
                                                    <tr>
                                                        <td colspan="3" class="loading-cell">Loading technical indicators...</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <!-- Moving Averages Table -->
                                    <div class="analysis-item">
                                        <h3>Moving Averages</h3>
                                        <div class="table-header">
                                            <span>Summary: <strong class="rating-text" id="maSummary">Neutral</strong></span>
                                            <span id="maSummaryCounts">Buy: 0 | Sell: 0</span>
                                            <span class="timestamp" id="maTimestamp">-</span>
                                        </div>
                                        <div class="table-container">
                                            <table class="rates-table" id="maTable">
                                                <thead>
                                                    <tr>
                                                        <th class="name-col">
                                                            <span>Name</span>
                                                        </th>
                                                        <th class="simple-col">
                                                            <span>Simple</span>
                                                        </th>
                                                        <th class="exponential-col">
                                                            <span>Exponential</span>
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody id="maTableBody">
                                                    <tr>
                                                        <td colspan="3" class="loading-cell">Loading moving averages...</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <!-- Pivot Points Table -->
                                    <div class="analysis-item">
                                        <h3>Pivot Points</h3>
                                        <div class="table-container">
                                            <table class="rates-table" id="pivotTable">
                                                <thead>
                                                    <tr>
                                                        <th class="name-col">
                                                            <span>Name</span>
                                                        </th>
                                                        <th class="value-col">
                                                            <span>S3</span>
                                                        </th>
                                                        <th class="value-col">
                                                            <span>S2</span>
                                                        </th>
                                                        <th class="value-col">
                                                            <span>S1</span>
                                                        </th>
                                                        <th class="pivot-col">
                                                            <span>Pivot Points</span>
                                                        </th>
                                                        <th class="value-col">
                                                            <span>R1</span>
                                                        </th>
                                                        <th class="value-col">
                                                            <span>R2</span>
                                                        </th>
                                                        <th class="value-col">
                                                            <span>R3</span>
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody id="pivotTableBody">
                                                    <tr>
                                                        <td colspan="8" class="loading-cell">Loading pivot points...</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <section class="widget full-width dark-theme" id="interestRatesWidget">
                            <div class="widget-header">
                                <div class="header-content">
                                    <h2><i class="fas fa-percentage"></i> EUR JPY Interest Rates Dashboard</h2>
                                </div>
                            </div>

                            <div class="widget-content" id="interestRateContainer">
                                <!-- Summary Stats (will be populated dynamically) -->
                                <div class="stats-grid" id="summaryStats">
                                    <!-- Stats will be generated by JavaScript -->
                                </div>

                                <!-- Main Table -->
                                <div class="table-container">
                                    <table class="rates-table" id="ratesTable">
                                        <thead>
                                            <tr>
                                                <th class="country-col">
                                                    <span>Country</span>
                                                    <i class="fas fa-sort"></i>
                                                </th>
                                                <th class="bank-col">
                                                    <span>Central Bank</span>
                                                </th>
                                                <th class="rate-col">
                                                    <span>Current Rate</span>
                                                    <i class="fas fa-sort"></i>
                                                </th>
                                                <th class="previous-col">
                                                    <span>Previous</span>
                                                </th>
                                                <th class="change-col">
                                                    <span>Change</span>
                                                </th>
                                                <th class="meeting-col">
                                                    <span>Next Meeting</span>
                                                    <i class="fas fa-sort"></i>
                                                </th>
                                                <th class="outlook-col">
                                                    <span>Outlook</span>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody id="ratesTableBody">
                                            <!-- Data will be loaded here from JSON -->
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Footer -->
                                <div class="widget-footer">
                                    <div class="footer-left">
                                        <div class="trend-legend">
                                            <div class="legend-item">
                                                <div class="legend-dot hike"></div>
                                                <span>Rate Hike</span>
                                            </div>
                                            <div class="legend-item">
                                                <div class="legend-dot cut"></div>
                                                <span>Rate Cut</span>
                                            </div>
                                            <div class="legend-item">
                                                <div class="legend-dot hold"></div>
                                                <span>On Hold</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="footer-right">
                                        <div class="data-info">
                                            <i class="fas fa-database"></i>
                                            <span>Last scraped: <span id="scrapeTime">-</span></span>
                                        </div>
                                        <button class="export-btn" onclick="exportData()">
                                            <i class="fas fa-download"></i>
                                            Export Data
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- Additional Analysis Section -->
                        <section class="widget full-width">
                            <div class="widget-header">
                                <h2>🔍 Detailed Analysis</h2>
                            </div>
                            <div class="widget-content">
                                <div class="detailed-analysis" id="detailedAnalysis">
                                    <div class="analysis-card">
                                        <h4>Price Action</h4>
                                        <p>Currently trading above 50-day moving average. Bullish engulfing pattern detected on 4H timeframe.</p>
                                        <div class="analysis-tags">
                                            <span class="tag bullish">Bullish Pattern</span>
                                            <span class="tag neutral">Consolidation</span>
                                        </div>
                                    </div>
                                    <div class="analysis-card">
                                        <h4>Volume Analysis</h4>
                                        <p>Volume increasing on up moves, decreasing on down moves. Supports bullish bias.</p>
                                        <div class="analysis-tags">
                                            <span class="tag positive">Volume Confirmation</span>
                                        </div>
                                    </div>
                                    <div class="analysis-card">
                                        <h4>Risk Levels</h4>
                                        <p>Stop loss: 158.00, Take profit: 160.50. Risk/Reward ratio: 1:2.5</p>
                                        <div class="analysis-tags">
                                            <span class="tag good-rr">Good R:R</span>
                                            <span class="tag medium-risk">Medium Risk</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            </div>

            <!-- Right Sidebar - Economic Calendar -->
            <aside class="sidebar sidebar-right">
                <section class="widget">
                    <div class="widget-header">
                        <h2>📅 Economic Calendar</h2>
                        <span class="widget-badge today">Today</span>
                    </div>
                    <div class="widget-content">
                        <!-- TradingView Widget BEGIN -->
                        <div class="tradingview-widget-container">
                            <div class="tradingview-widget-container__widget"></div>
                            <div class="tradingview-widget-copyright">
                                <a href="https://www.tradingview.com/economic-calendar/"
                                    rel="noopener nofollow"
                                    target="_blank"><span class="blue-text">Economic Calendar</span></a>
                                <span class="trademark"> by TradingView</span>
                            </div>
                            <script type="text/javascript"
                                src="https://s3.tradingview.com/external-embedding/embed-widget-events.js"
                                async>
                                {
                                    "colorTheme": "dark",
                                    "isTransparent": false,
                                    "locale": "en",
                                    "countryFilter": "ar,au,br,ca,cn,fr,de,in,id,it,jp,kr,mx,ru,sa,za,tr,gb,us,eu",
                                    "importanceFilter": "-1,0,1",
                                    "width": "100%",
                                    "height": "100%"
                                }
                            </script>
                        </div>
                        <!-- TradingView Widget END -->
                    </div>
                </section>
            </aside>
        </main>
    </div>

    <script>
        // TradingView Chart Configuration
        let currentChart = null;
        let isChartLoading = false;
        let lastApiRequestTime = 0;
        const API_REQUEST_COOLDOWN = 60000; // 1 minute cooldown between API requests
        let rateLimitMessageShown = false;
        let lastAnalysisUpdateTime = 0;
        const MIN_ANALYSIS_INTERVAL = 60000; // 60 seconds minimum between analysis updates

        // Map rating to needle angle
        function getNeedleAngleForRating(rating) {
            const ratingAngles = {
                'strong sell': 15,
                'strong-sell': 15,
                'sell': 50,
                'neutral': 90,
                'buy': 130,
                'strong buy': 170,
                'strong-buy': 170
            };

            const normalizedRating = rating.toLowerCase().trim();
            return ratingAngles[normalizedRating] || ratingAngles[normalizedRating.replace(/\s+/g, '-')] || 90; // Default to neutral
        }

        // Map rating to CSS class
        function getClassForRating(rating) {
            const ratingClasses = {
                'strong sell': 'strong-sell',
                'strong-sell': 'strong-sell',
                'sell': 'sell',
                'neutral': 'neutral',
                'buy': 'buy',
                'strong buy': 'strong-buy',
                'strong-buy': 'strong-buy'
            };

            const normalizedRating = rating.toLowerCase().trim();
            return ratingClasses[normalizedRating] || ratingClasses[normalizedRating.replace(/\s+/g, '-')] || 'neutral'; // Default to neutral
        }

        function setNeedleAngle(angle) {
            const gauge = document.getElementById('gauge');
            if (gauge) {
                gauge.style.setProperty('--angle', angle + 'deg');
            }
        }

        function updateGaugeRating(rating) {
            const gaugeRating = document.getElementById('gaugeRating');
            if (!gaugeRating) return;

            // Get angle and class for the rating
            const needleAngle = getNeedleAngleForRating(rating);
            const ratingClass = getClassForRating(rating);

            // Update needle position
            setNeedleAngle(needleAngle);

            // Clear existing classes and update display
            gaugeRating.className = 'gauge-rating';

            if (ratingClass !== 'neutral' || rating.toLowerCase().includes('neutral')) {
                // Valid rating found
                gaugeRating.textContent = rating;
                gaugeRating.classList.add(ratingClass);
            } else {
                // Unknown rating, default to neutral
                console.warn(`Unknown rating: "${rating}". Defaulting to neutral.`);
                gaugeRating.textContent = 'Neutral';
                gaugeRating.classList.add('neutral');
            }
        }

        function updateGaugeRatingBackup(rating) {
            const gaugeRating = document.getElementById('gaugeRating');
            if (gaugeRating) {
                gaugeRating.textContent = rating;

                // Remove all existing signal classes
                gaugeRating.classList.remove('strong-sell', 'sell', 'neutral', 'buy', 'strong-buy');

                // Convert rating to lowercase and replace spaces with hyphens
                const ratingClass = rating.toLowerCase().replace(/\s+/g, '-');

                // Add the appropriate class based on the rating
                if (ratingClass === 'strong-sell' || ratingClass === 'sell' ||
                    ratingClass === 'neutral' || ratingClass === 'buy' ||
                    ratingClass === 'strong-buy') {
                    gaugeRating.classList.add(ratingClass);
                } else {
                    // Fallback to neutral if rating doesn't match expected values
                    console.warn(`Unknown rating: "${rating}". Defaulting to neutral.`);
                    gaugeRating.classList.add('neutral');
                    gaugeRating.textContent = 'Neutral';
                }

                // Ensure base class is always present
                gaugeRating.classList.add('gauge-rating');
            }
        }

        function getGaugeAngleFromRating(rating) {
            const ratingMap = {
                'Strong Sell': 30, // Far left
                'Sell': 60, // Left
                'Neutral': 90, // Middle
                'Buy': 130, // Right
                'Strong Buy': 170 // Far right
            };
            return ratingMap[rating] || 90; // Default to neutral
        }

        function updateMarketStatus(symbol) {
            const statusElement = document.querySelector('.status-indicator');
            if (statusElement) {
                statusElement.innerHTML = '<span class="live-dot"></span> Live ' + symbol + ' Data';
            }
        }

        function getTimeframeLabel(value) {
            const labels = {
                1: "1min",
                5: "5min",
                15: "15min",
                30: "30min",
                60: "1H",
                120: "2H",
                240: "4H",
                D: "Daily",
                W: "Weekly",
                M: "Monthly"
            };
            return labels[value] || value;
        }

        function loadChart() {
            if (isChartLoading) return;

            isChartLoading = true;
            const symbol = document.getElementById("pair").value;
            const tf = document.getElementById("tf").value;
            const timeframeLabel = getTimeframeLabel(tf);

            // Update current pair display
            document.getElementById("current-pair").textContent = `${symbol} - ${timeframeLabel} Chart`;
            document.getElementById("current-tf").textContent = timeframeLabel;
            document.getElementById("current-tf-second").textContent = timeframeLabel;

            // Show loading state
            const chartContainer = document.getElementById("chart");
            chartContainer.innerHTML = `
                <div class="chart-loading">
                    <div class="loading-spinner"></div>
                    <p>Loading ${symbol} chart...</p>
                </div>
            `;

            // Clear previous chart after a short delay
            setTimeout(() => {
                chartContainer.innerHTML = "";

                // Load new chart
                currentChart = new TradingView.widget({
                    container_id: "chart",
                    width: "100%",
                    height: "100%",
                    symbol: symbol,
                    interval: tf,
                    timezone: "Etc/UTC",
                    theme: "dark",
                    style: "1",
                    locale: "en",
                    enable_publishing: false,
                    withdateranges: true,
                    hide_side_toolbar: false,
                    allow_symbol_change: true,
                    save_image: true,
                    details: true,
                    hotlist: true,
                    calendar: false,
                    studies: [
                        "RSI@tv-basicstudies",
                        "MACD@tv-basicstudies",
                        "Volume@tv-basicstudies",
                        "MovingAverage@tv-basicstudies",
                        "BollingerBands@tv-basicstudies"
                    ],
                    show_popup_button: true,
                    popup_width: "1000",
                    popup_height: "650",
                    toolbar_bg: "#1a1f2e",
                    indicator_width: 1,
                    disabled_features: ["use_localstorage_for_settings"],
                    enabled_features: ["study_templates", "side_toolbar_in_fullscreen"],
                    overrides: {
                        "paneProperties.background": "#1a1f2e",
                        "paneProperties.vertGridProperties.color": "#2a3245",
                        "paneProperties.horzGridProperties.color": "#2a3245",
                        "volumePaneSize": "medium"
                    },
                    studies_overrides: {
                        "volume.volume.color.0": "#ef4444",
                        "volume.volume.color.1": "#10b981",
                        "volume.volume.transparency": 70
                    }
                });

                isChartLoading = false;

                // Update header with market status
                updateMarketStatus(symbol);

                // Update analysis data when chart loads
                setTimeout(() => {
                    updateAnalysis(symbol, tf);
                }, 1000);

            }, 100);
        }

        async function fetchTechnicalAnalysis(pair, timeframe) {
            try {
                // Check rate limiting BEFORE making the request
                const now = Date.now();
                if (now - lastApiRequestTime < API_REQUEST_COOLDOWN) {
                    const remainingTime = Math.ceil((API_REQUEST_COOLDOWN - (now - lastApiRequestTime)) / 1000);

                    if (!rateLimitMessageShown) {
                        showRateLimitMessage(remainingTime);
                        rateLimitMessageShown = true;

                        setTimeout(() => {
                            rateLimitMessageShown = false;
                        }, API_REQUEST_COOLDOWN);
                    }

                    console.log(`Rate limited: Please wait ${remainingTime} seconds before making another API request`);
                    throw new Error(`REQUEST TOO SOON: Please wait ${remainingTime} seconds`);
                }

                // Convert timeframe to match API format
                const tfMap = {
                    '15': 'm15',
                    '30': 'm30',
                    '60': 'H1',
                    '240': 'H4',
                    'D': 'D1',
                    'W': 'W1',
                    'M': 'MN'
                };

                const apiTimeframe = tfMap[timeframe] || timeframe;

                // Prepare request data
                const formData = new FormData();
                formData.append("action", "get_scrape_data_v3");
                formData.append("pair", pair);
                formData.append("timeframe", apiTimeframe);

                const PROXY_ENDPOINT = "/proxy.php";

                console.log("Fetching data for:", pair, apiTimeframe);

                // Update last API request time
                lastApiRequestTime = Date.now();

                const res = await fetch(PROXY_ENDPOINT, {
                    method: "POST",
                    body: formData,
                    credentials: "same-origin",
                });

                console.log("Proxy response status:", res.status);

                if (!res.ok) {
                    const errorText = await res.text();
                    console.error("Scrape Data failed via proxy:", errorText);

                    try {
                        const errorData = JSON.parse(errorText);
                        throw new Error(errorData.message || `Scrape Data failed: ${res.status}`);
                    } catch {
                        throw new Error(`Scrape Data failed: ${res.status} - Server error`);
                    }
                }

                const data = await res.json();
                console.log("Scrape Data V3 response:", data);

                if (data.action !== "get_scrape_data_v3") {
                    console.warn("Unexpected action in response:", data.action);
                }

                if (!data.success) {
                    throw new Error(data.message || "Failed to fetch technical analysis");
                }

                return data;

            } catch (error) {
                console.error('Error fetching technical analysis:', error);

                if (error.message.includes('REQUEST TOO SOON')) {
                    throw error;
                }

                return getFallbackData(pair, timeframe);
            }
        }

        function showRateLimitMessage(remainingSeconds) {
            removeRateLimitMessage();

            const message = document.createElement('div');
            message.className = 'rate-limit-message';
            message.innerHTML = `
            <div style="
                position: fixed;
                top: 20px;
                right: 20px;
                background: #ef4444;
                color: white;
                padding: 12px 16px;
                border-radius: 8px;
                z-index: 9999;
                animation: slideIn 0.3s ease-out;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                display: flex;
                align-items: center;
                gap: 8px;
                max-width: 300px;
            ">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <path d="M12 8v4M12 16h.01"></path>
                </svg>
                <div>
                    <div style="font-weight: 600;">Rate Limited</div>
                    <div style="font-size: 14px; opacity: 0.9;">Please wait ${remainingSeconds} seconds before refreshing</div>
                </div>
                <button onclick="this.parentElement.remove()" style="
                    background: none;
                    border: none;
                    color: white;
                    cursor: pointer;
                    padding: 4px;
                    margin-left: auto;
                ">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M18 6L6 18M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        `;

            document.body.appendChild(message);

            setTimeout(() => {
                removeRateLimitMessage();
            }, 5000);
        }

        function removeRateLimitMessage() {
            const existingMessage = document.querySelector('.rate-limit-message');
            if (existingMessage) {
                existingMessage.remove();
            }
        }

        function getFallbackData(pair, timeframe) {
            console.log('Using fallback data for', pair, timeframe);
            return {
                success: true,
                data: {
                    myfxbook_data: [{
                        technical_analysis: {
                            total_patterns: 5,
                            technical_summary: "Neutral",
                            counts: {
                                buy: 3,
                                sell: 2,
                                neutral: 0
                            },
                            patterns_sample: [{
                                    name: "Doji",
                                    signal: "neutral"
                                },
                                {
                                    name: "Engulfing Pattern",
                                    signal: "buy"
                                },
                                {
                                    name: "Hammer",
                                    signal: "buy"
                                }
                            ]
                        },
                        interest_rates: {
                            total_rates: 2,
                            rates_sample: [{
                                    country: "Euro Area",
                                    centralBank: "European Central Bank",
                                    currentRate: "2.15%",
                                    previousRate: "2.15%",
                                    nextMeeting: "8 days"
                                },
                                {
                                    country: "Japan",
                                    centralBank: "Bank of Japan",
                                    currentRate: "0.75%",
                                    previousRate: "0.75%",
                                    nextMeeting: "49 days"
                                }
                            ]
                        }
                    }],
                    investing_data: [{
                        overall_signal: "Neutral",
                        scrape_timestamp: new Date().toLocaleString(),
                        technical_indicators: {
                            sample: [{
                                    name: "RSI(14)",
                                    value: "50.0",
                                    action: "Neutral"
                                },
                                {
                                    name: "MACD(12,26)",
                                    value: "0.0",
                                    action: "Neutral"
                                }
                            ]
                        },
                        moving_averages: {
                            sample: [{
                                    name: "MA5",
                                    simple: "0.0 Neutral",
                                    exponential: "Neutral"
                                },
                                {
                                    name: "MA10",
                                    simple: "0.0 Neutral",
                                    exponential: "Neutral"
                                }
                            ]
                        },
                        pivot_points: {
                            sample: [{
                                name: "Classic",
                                s3: "0.0",
                                s2: "0.0",
                                s1: "0.0",
                                pivot_points: "0.0",
                                r1: "0.0",
                                r2: "0.0",
                                r3: "0.0"
                            }]
                        }
                    }]
                }
            };
        }

        async function updateAnalysis(symbol, timeframe) {
            try {
                // Update the last analysis update time
                lastAnalysisUpdateTime = Date.now();

                // Fetch real data from API
                const apiResponse = await fetchTechnicalAnalysis(symbol, timeframe);

                if (!apiResponse || !apiResponse.success) {
                    throw new Error('Invalid API response');
                }

                console.log("API Response received:", apiResponse);

                // Get the latest myfxbook data (first item in array)
                const myfxbookLatest = apiResponse.data?.myfxbook_data?.[0];
                const investingLatest = apiResponse.data?.investing_data?.[0];

                if (!myfxbookLatest && !investingLatest) {
                    throw new Error('No analysis data found');
                }

                // Update Market Analysis First Source (Myfxbook)
                if (myfxbookLatest) {
                    updateFirstSourceAnalysis(myfxbookLatest, symbol, timeframe);

                    // Update interest rates
                    if (myfxbookLatest.interest_rates) {
                        renderInterestRate(myfxbookLatest.interest_rates);
                    }
                }

                // Update Market Analysis Second Source (Investing.com)
                if (investingLatest) {
                    updateSecondSourceAnalysis(investingLatest, symbol, timeframe);
                }

                // Update detailed analysis section
                updateDetailedAnalysisSection(myfxbookLatest, investingLatest, symbol, timeframe);

                // Update the last updated timestamp
                const updateTime = document.getElementById('time-since-update');
                if (updateTime) {
                    updateTime.textContent = `Updated: ${new Date().toLocaleTimeString()}`;
                    updateTime.style.color = '#10b981';
                }

            } catch (error) {
                console.error('Error updating analysis:', error);

                if (error.message.includes('REQUEST TOO SOON')) {
                    // Show rate limit error
                    const analysisContainer = document.querySelector('.analysis-placeholder');
                    if (analysisContainer) {
                        analysisContainer.innerHTML = `
                            <div class="analysis-item">
                                <div class="ta-container" id="taContainer">
                                    <div class="signal" id="signal">
                                        <span class="label">Rate Limited:</span>
                                        <span class="value" style="color: #ef4444;">${error.message}</span>
                                    </div>
                                </div>
                            </div>
                        `;
                    }
                } else {
                    // Fallback to simulated data for other errors
                    updateAnalysisWithFallback(symbol, timeframe);
                }
            }
        }

        function updateFirstSourceAnalysis(data, symbol, timeframe) {
            if (!data.technical_analysis) return;

            const ta = data.technical_analysis;

            // Update overall signal
            const overallSignalElement = document.getElementById('overallSignal');
            if (overallSignalElement) {
                const signal = ta.technical_summary || 'Neutral';
                overallSignalElement.textContent = signal;
                overallSignalElement.className = 'value ' + signal.toLowerCase();
            }

            // Update indicators count
            const indicatorsCountElement = document.getElementById('indicatorsCount');
            if (indicatorsCountElement) {
                indicatorsCountElement.textContent = ta.total_patterns || 0;
            }

            // Update indicators list
            const indicatorsListElement = document.getElementById('indicatorsList');
            if (indicatorsListElement && ta.patterns_sample) {
                indicatorsListElement.innerHTML = '';

                ta.patterns_sample.forEach(pattern => {
                    const div = document.createElement('div');
                    div.className = 'indicator-item';

                    let timeframes = '';
                    if (pattern.buy && pattern.sell) {
                        timeframes = `Buy: ${pattern.buy} | Sell: ${pattern.sell}`;
                    } else if (pattern.buy) {
                        timeframes = `Buy: ${pattern.buy}`;
                    } else if (pattern.sell) {
                        timeframes = `Sell: ${pattern.sell}`;
                    }

                    div.innerHTML = `
                        <span class="indicator-name">${pattern.name}</span>
                        <span class="indicator-value">${pattern.signal}</span>
                        <span class="indicator-action ${pattern.signal.toLowerCase()}">${pattern.signal}</span>
                        ${timeframes ? `<br><small style="font-size: 0.8em; color: #a0aec0;">${timeframes}</small>` : ''}
                    `;
                    indicatorsListElement.appendChild(div);
                });
            }

            // Update counts display
            if (ta.counts) {
                console.log(`Myfxbook - Buy: ${ta.counts.buy} | Sell: ${ta.counts.sell} | Neutral: ${ta.counts.neutral || 0}`);
            }
        }

        function updateSecondSourceAnalysis(data, symbol, timeframe) {
            if (!data) return;

            // Update overall rating display
            const overallRating = data.overall_signal || 'Neutral';

            // Update gauge
            const gaugeAngle = getGaugeAngleFromRating(overallRating);
            setNeedleAngle(gaugeAngle);
            updateGaugeRating(overallRating);

            // Update summary details
            if (data.moving_averages && data.moving_averages.sample) {
                const maData = data.moving_averages.sample;
                const buyCount = maData.filter(ma =>
                    ma.simple.includes('Buy') || ma.exponential.includes('Buy')
                ).length;
                const sellCount = maData.filter(ma =>
                    ma.simple.includes('Sell') || ma.exponential.includes('Sell')
                ).length;

                const maSummary = buyCount > sellCount ? 'Buy' :
                    sellCount > buyCount ? 'Sell' : 'Neutral';

                const maCounts = `Buy: ${buyCount} | Sell: ${sellCount}`;

                document.getElementById('maRating').textContent = maSummary;
                document.getElementById('maRating').className = maSummary.toLowerCase();
                document.getElementById('maCounts').textContent = maCounts;

                document.getElementById('maSummary').textContent = maSummary;
                document.getElementById('maSummary').className = 'rating-text ' + maSummary.toLowerCase();
                document.getElementById('maSummaryCounts').textContent = maCounts;
                document.getElementById('maTimestamp').textContent = data.scrape_timestamp || '-';

                // Update moving averages table
                updateMovingAveragesTable(maData);
            }

            if (data.technical_indicators && data.technical_indicators.sample) {
                const tiData = data.technical_indicators.sample;
                const buyCount = tiData.filter(ti =>
                    ti.action === 'Buy' || ti.action === 'Strong Buy'
                ).length;
                const sellCount = tiData.filter(ti =>
                    ti.action === 'Sell' || ti.action === 'Strong Sell'
                ).length;

                const tiSummary = buyCount > sellCount ? 'Buy' :
                    sellCount > buyCount ? 'Sell' : 'Neutral';

                const tiCounts = `Buy: ${buyCount} | Sell: ${sellCount}`;

                document.getElementById('tiRating').textContent = tiSummary;
                document.getElementById('tiRating').className = tiSummary.toLowerCase();
                document.getElementById('tiCounts').textContent = tiCounts;

                document.getElementById('tiSummary').textContent = tiSummary;
                document.getElementById('tiSummary').className = 'rating-text ' + tiSummary.toLowerCase();
                document.getElementById('tiSummaryCounts').textContent = tiCounts;
                document.getElementById('tiTimestamp').textContent = data.scrape_timestamp || '-';

                // Update technical indicators table
                updateTechnicalIndicatorsTable(tiData);
            }

            if (data.pivot_points && data.pivot_points.sample) {
                updatePivotPointsTable(data.pivot_points.sample);
            }
        }

        function updateMovingAveragesTable(maData) {
            const tableBody = document.getElementById('maTableBody');
            if (!tableBody || !maData) return;

            tableBody.innerHTML = '';

            maData.forEach(item => {
                const row = document.createElement('tr');

                // Extract value and action from simple field
                const simpleMatch = item.simple?.match(/([\d.]+)\s*(.+)/);
                const simpleValue = simpleMatch ? simpleMatch[1] : '';
                const simpleAction = simpleMatch ? simpleMatch[2] : '';

                // Extract value and action from exponential field
                const expAction = item.exponential || '';

                row.innerHTML = `
                    <td class="name-col">${item.name}</td>
                    <td class="simple-col ${simpleAction.toLowerCase()}">
                        <div class="value-action">
                            <span>${simpleValue}</span>
                            <span class="action ${simpleAction.toLowerCase()}">${simpleAction}</span>
                        </div>
                    </td>
                    <td class="exponential-col ${expAction.toLowerCase()}">
                        <div class="value-action">
                            <span class="action ${expAction.toLowerCase()}">${expAction}</span>
                        </div>
                    </td>
                `;
                tableBody.appendChild(row);
            });
        }

        function updateTechnicalIndicatorsTable(tiData) {
            const tableBody = document.getElementById('tiTableBody');
            if (!tableBody || !tiData) return;

            tableBody.innerHTML = '';

            tiData.forEach(item => {
                const row = document.createElement('tr');
                const actionClass = item.action.toLowerCase().replace(' ', '-');
                row.innerHTML = `
                    <td class="name-col">${item.name}</td>
                    <td class="value-col">${item.value}</td>
                    <td class="action-col ${actionClass}">${item.action}</td>
                `;
                tableBody.appendChild(row);
            });
        }

        function updatePivotPointsTable(pivotData) {
            const tableBody = document.getElementById('pivotTableBody');
            if (!tableBody || !pivotData) return;

            tableBody.innerHTML = '';

            pivotData.forEach(item => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td class="name-col">${item.name}</td>
                    <td class="value-col">${item.s3 || ''}</td>
                    <td class="value-col">${item.s2 || ''}</td>
                    <td class="value-col">${item.s1 || ''}</td>
                    <td class="pivot-col">${item.pivot_points || ''}</td>
                    <td class="value-col">${item.r1 || ''}</td>
                    <td class="value-col">${item.r2 || ''}</td>
                    <td class="value-col">${item.r3 || ''}</td>
                `;
                tableBody.appendChild(row);
            });
        }

        function renderInterestRate(interestRateData) {
            if (!interestRateData || !interestRateData.rates_sample) return;

            const tableBody = document.getElementById('ratesTableBody');
            if (!tableBody) return;

            tableBody.innerHTML = '';

            interestRateData.rates_sample.forEach(rate => {
                const currentRate = parseFloat(rate.currentRate);
                const previousRate = parseFloat(rate.previousRate);
                const change = currentRate - previousRate;

                let changeClass = 'hold';
                let changeIcon = '';
                let changeText = 'Hold';

                if (change > 0) {
                    changeClass = 'hike';
                    changeIcon = '↑';
                    changeText = `+${change.toFixed(2)}%`;
                } else if (change < 0) {
                    changeClass = 'cut';
                    changeIcon = '↓';
                    changeText = `${change.toFixed(2)}%`;
                }

                const row = document.createElement('tr');
                row.innerHTML = `
                    <td class="country-col">
                        <div class="country-flag"></div>
                        <span>${rate.country}</span>
                    </td>
                    <td class="bank-col">${rate.centralBank}</td>
                    <td class="rate-col">${rate.currentRate}</td>
                    <td class="previous-col">${rate.previousRate}</td>
                    <td class="change-col ${changeClass}">
                        <span>${changeIcon} ${changeText}</span>
                    </td>
                    <td class="meeting-col">${rate.nextMeeting}</td>
                    <td class="outlook-col">
                        <span class="outlook ${changeClass}">${changeText}</span>
                    </td>
                `;
                tableBody.appendChild(row);
            });

            // Update scrape time
            const scrapeTimeElement = document.getElementById('scrapeTime');
            if (scrapeTimeElement) {
                scrapeTimeElement.textContent = new Date().toLocaleString();
            }
        }

        function updateDetailedAnalysisSection(myfxbookData, investingData, symbol, timeframe) {
            const detailedAnalysis = document.getElementById('detailedAnalysis');
            if (!detailedAnalysis) return;

            let analysisHTML = '';

            // Add myfxbook pattern analysis
            if (myfxbookData?.technical_analysis) {
                const ta = myfxbookData.technical_analysis;
                const buyCount = ta.counts?.buy || 0;
                const sellCount = ta.counts?.sell || 0;

                analysisHTML += `
                    <div class="analysis-card">
                        <h4>Pattern Analysis (Myfxbook)</h4>
                        <p>${getPatternAnalysis(ta)}</p>
                        <div class="analysis-tags">
                            <span class="tag ${ta.technical_summary.toLowerCase()}">
                                ${ta.technical_summary}
                            </span>
                            ${buyCount > sellCount ? '<span class="tag bullish">Bullish Bias</span>' : 
                              sellCount > buyCount ? '<span class="tag bearish">Bearish Bias</span>' : 
                              '<span class="tag neutral">Balanced</span>'}
                        </div>
                    </div>
                `;
            }

            // Add investing.com technical indicators summary
            if (investingData?.technical_indicators?.sample) {
                const ti = investingData.technical_indicators;
                const buyCount = ti.sample.filter(item =>
                    item.action === 'Buy' || item.action === 'Strong Buy'
                ).length || 0;
                const sellCount = ti.sample.filter(item =>
                    item.action === 'Sell' || item.action === 'Strong Sell'
                ).length || 0;

                analysisHTML += `
                    <div class="analysis-card">
                        <h4>Technical Indicators (Investing.com)</h4>
                        <p>${buyCount} bullish vs ${sellCount} bearish indicators detected</p>
                        <div class="analysis-tags">
                            ${buyCount > sellCount ? '<span class="tag bullish">Bullish Bias</span>' : 
                              sellCount > buyCount ? '<span class="tag bearish">Bearish Bias</span>' : 
                              '<span class="tag neutral">Balanced</span>'}
                        </div>
                    </div>
                `;
            }

            // Add interest rates if available
            if (myfxbookData?.interest_rates?.rates_sample) {
                const rates = myfxbookData.interest_rates.rates_sample;
                analysisHTML += `
                    <div class="analysis-card">
                        <h4>Interest Rates</h4>
                        <p>${rates.map(rate => 
                            `${rate.country}: ${rate.currentRate} (Next meeting: ${rate.nextMeeting})`
                        ).join('<br>')}</p>
                        <div class="analysis-tags">
                            ${rates.map(rate => 
                                `<span class="tag neutral">${rate.centralBank}</span>`
                            ).join('')}
                        </div>
                    </div>
                `;
            }

            detailedAnalysis.innerHTML = analysisHTML;
        }

        function getPatternAnalysis(taData) {
            if (!taData || !taData.patterns_sample) return 'No pattern data available';

            const patterns = taData.patterns_sample;
            const topPatterns = patterns.slice(0, 3);

            return `Detected ${taData.total_patterns} patterns including ${topPatterns.map(p => p.name).join(', ')}`;
        }

        function updateAnalysisWithFallback(symbol, timeframe) {
            const fallbackData = getFallbackData(symbol, timeframe);

            if (fallbackData.success && fallbackData.data) {
                const myfxbookLatest = fallbackData.data.myfxbook_data?.[0];
                const investingLatest = fallbackData.data.investing_data?.[0];

                if (myfxbookLatest) {
                    updateFirstSourceAnalysis(myfxbookLatest, symbol, timeframe);
                    if (myfxbookLatest.interest_rates) {
                        renderInterestRate(myfxbookLatest.interest_rates);
                    }
                }

                if (investingLatest) {
                    updateSecondSourceAnalysis(investingLatest, symbol, timeframe);
                }

                updateDetailedAnalysisSection(myfxbookLatest, investingLatest, symbol, timeframe);
            }
        }

        // Initialize chart on page load
        document.addEventListener('DOMContentLoaded', () => {
            console.log("DOMContentLoaded");

            // Initialize gauge as neutral

            updateGaugeRating('Neutral');

            // Add loading styles
            const style = document.createElement('style');
            style.textContent = `
                .chart-loading {
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    justify-content: center;
                    height: 100%;
                    color: var(--text-secondary);
                }
                
                .loading-spinner {
                    width: 40px;
                    height: 40px;
                    border: 3px solid var(--border-color);
                    border-top-color: var(--accent-blue);
                    border-radius: 50%;
                    animation: spin 1s linear infinite;
                    margin-bottom: 15px;
                }
                
                @keyframes spin {
                    to { transform: rotate(360deg); }
                }
                
                .analysis-updating {
                    animation: pulse 2s infinite;
                }
                
                @keyframes pulse {
                    0% { opacity: 0.6; }
                    50% { opacity: 1; }
                    100% { opacity: 0.6; }
                }
            `;
            document.head.appendChild(style);

            // Set initial timestamp to avoid immediate analysis update on page load
            lastAnalysisUpdateTime = Date.now() - MIN_ANALYSIS_INTERVAL;

            // Load initial chart with a delay for analysis
            setTimeout(() => {
                console.log("Initializing chart...");
                loadChart();
            }, 1000);

            // Add event listeners
            document.getElementById('pair').addEventListener('change', () => {
                const now = Date.now();
                if (now - lastAnalysisUpdateTime < MIN_ANALYSIS_INTERVAL) {
                    const remainingTime = Math.ceil((MIN_ANALYSIS_INTERVAL - (now - lastAnalysisUpdateTime)) / 1000);
                    showRateLimitMessage(remainingTime);
                    return;
                }
                loadChart();
            });

            document.getElementById('tf').addEventListener('change', () => {
                const now = Date.now();
                if (now - lastAnalysisUpdateTime < MIN_ANALYSIS_INTERVAL) {
                    const remainingTime = Math.ceil((MIN_ANALYSIS_INTERVAL - (now - lastAnalysisUpdateTime)) / 1000);
                    showRateLimitMessage(remainingTime);
                    return;
                }
                loadChart();
            });

            // Add click handler to refresh button
            document.querySelector('.refresh-btn').addEventListener('click', () => {
                const now = Date.now();
                if (now - lastAnalysisUpdateTime < MIN_ANALYSIS_INTERVAL) {
                    const remainingTime = Math.ceil((MIN_ANALYSIS_INTERVAL - (now - lastAnalysisUpdateTime)) / 1000);
                    showRateLimitMessage(remainingTime);
                    return;
                }
                loadChart();
            });

            // Handle window resize
            window.addEventListener('resize', () => {
                if (currentChart && typeof currentChart.onResize === 'function') {
                    setTimeout(() => currentChart.onResize(), 100);
                }
            });
        });
    </script>
</body>

</html>