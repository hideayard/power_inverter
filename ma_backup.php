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
                                    <div class="analysis-container" style="display: flex; gap: 20px; justify-content: space-around; flex-wrap: wrap;">

                                        <!-- Technical Indicators Gauge -->
                                        <div class="analysis-item" style="flex: 1; min-width: 250px;">
                                            <h3>Technical Indicators</h3>
                                            <div class="wrapper">
                                                <div id="ti-gauge" class="gauge" style="--angle: 90deg;">
                                                    <div class="slice-colors">
                                                        <div class="st slice-item"></div>
                                                        <div class="st slice-item"></div>
                                                        <div class="st slice-item"></div>
                                                        <div class="st slice-item"></div>
                                                        <div class="st slice-item"></div>
                                                    </div>
                                                    <div id="ti-needle" class="needle"></div>
                                                    <div class="gauge-center"></div>
                                                </div>
                                                <div class="gauge-rating text-white" id="ti-rating">Neutral</div>
                                            </div>
                                            <div class="summary-details">
                                                <div class="indicator-row">
                                                    <span>Signal Count:</span>
                                                    <span id="tiCounts">Buy: 0 | Sell: 0</span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Summary Gauge -->
                                        <div class="analysis-item" style="flex: 1; min-width: 250px;">
                                            <h3>Summary</h3>
                                            <div class="wrapper">
                                                <div id="summary-gauge" class="gauge" style="--angle: 90deg;">
                                                    <div class="slice-colors">
                                                        <div class="st slice-item"></div>
                                                        <div class="st slice-item"></div>
                                                        <div class="st slice-item"></div>
                                                        <div class="st slice-item"></div>
                                                        <div class="st slice-item"></div>
                                                    </div>
                                                    <div id="summary-needle" class="needle"></div>
                                                    <div class="gauge-center"></div>
                                                </div>
                                                <div class="gauge-rating text-white" id="summary-rating">Neutral</div>
                                            </div>
                                            <div class="summary-details">
                                                <div class="indicator-row">
                                                    <span>Overall Signal:</span>
                                                    <span id="overallCounts">Buy: 0 | Sell: 0</span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Moving Averages Gauge -->
                                        <div class="analysis-item" style="flex: 1; min-width: 250px;">
                                            <h3>Moving Averages</h3>
                                            <div class="wrapper">
                                                <div id="ma-gauge" class="gauge" style="--angle: 90deg;">
                                                    <div class="slice-colors">
                                                        <div class="st slice-item"></div>
                                                        <div class="st slice-item"></div>
                                                        <div class="st slice-item"></div>
                                                        <div class="st slice-item"></div>
                                                        <div class="st slice-item"></div>
                                                    </div>
                                                    <div id="ma-needle" class="needle"></div>
                                                    <div class="gauge-center"></div>
                                                </div>
                                                <div class="gauge-rating text-white" id="ma-rating">Neutral</div>
                                            </div>
                                            <div class="summary-details">
                                                <div class="indicator-row">
                                                    <span>MA Signal Count:</span>
                                                    <span id="maCounts">Buy: 0 | Sell: 0</span>
                                                </div>
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
                        <!-- myfxbook.com Economic Calendar Widget - Start -->
                        <iframe src="https://widget.myfxbook.com/widget/calendar.html?lang=en&impacts=0,1,2,3&symbols=AUD,CAD,CHF,CNY,EUR,GBP,IDR,JPY,NZD,USD" style="border: 0; width:100%; height:100%;"></iframe>
                        <div style="margin-top: 10px;">
                            <div style="width: fit-content; margin: auto;font-family: roboto,sans-serif!important; font-size: 13px; color: #666666;">
                                <a href="https://www.myfxbook.com/forex-economic-calendar?utm_source=widget13&utm_medium=link&utm_campaign=copyright" title="Economic Calendar" class="myfxbookLink" target="_blank" rel="noopener"><b style="color:#666666">Economic Calendar</b></a>
                                by Myfxbook.com
                            </div>
                        </div>
                        <!-- myfxbook.com Economic Calendar Widget - End -->
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
            return ratingAngles[normalizedRating] || ratingAngles[normalizedRating.replace(/\s+/g, '-')] || 90;
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
            return ratingClasses[normalizedRating] || ratingClasses[normalizedRating.replace(/\s+/g, '-')] || 'neutral';
        }

        // Set needle angle with safety check
        function setNeedleAngle(needleId, angle) {
            const needle = document.getElementById(needleId);
            if (needle) {
                const gauge = needle.closest('.gauge');
                if (gauge) {
                    gauge.style.setProperty('--angle', angle + 'deg');
                }
            } else {
                console.warn(`Needle element with ID "${needleId}" not found.`);
            }
        }

        // Update gauge rating with safety checks
        function updateGaugeRating(needleId, ratingElementId, rating) {
            const gaugeRating = document.getElementById(ratingElementId);
            if (!gaugeRating) {
                console.error(`Rating element with ID "${ratingElementId}" not found.`);
                return false;
            }

            const needleAngle = getNeedleAngleForRating(rating);
            const ratingClass = getClassForRating(rating);

            if (needleId) {
                setNeedleAngle(needleId, needleAngle);
            }

            gaugeRating.className = 'gauge-rating text-white';

            if (ratingClass !== 'neutral' || rating.toLowerCase().includes('neutral')) {
                gaugeRating.textContent = rating;
                gaugeRating.classList.add(ratingClass);
            } else {
                console.warn(`Unknown rating: "${rating}". Defaulting to neutral.`);
                gaugeRating.textContent = 'Neutral';
                gaugeRating.classList.add('neutral');
            }

            return true;
        }

        // Update count display with safety check
        function updateCountDisplay(elementId, buyCount = 0, sellCount = 0) {
            const element = document.getElementById(elementId);
            if (element) {
                element.textContent = `Buy: ${buyCount} | Sell: ${sellCount}`;
            } else {
                console.warn(`Count display element with ID "${elementId}" not found.`);
            }
        }

        // Convenience functions with safety checks
        function updateTechnicalIndicators(rating, buyCount = 0, sellCount = 0) {
            const success = updateGaugeRating('ti-needle', 'ti-rating', rating);
            if (success) {
                updateCountDisplay('tiCounts', buyCount, sellCount);
            }
        }

        function updateSummary(rating, buyCount = 0, sellCount = 0) {
            const success = updateGaugeRating('summary-needle', 'summary-rating', rating);
            if (success) {
                updateCountDisplay('overallCounts', buyCount, sellCount);
            }
        }

        function updateMovingAverages(rating, buyCount = 0, sellCount = 0) {
            const success = updateGaugeRating('ma-needle', 'ma-rating', rating);
            if (success) {
                updateCountDisplay('maCounts', buyCount, sellCount);
            }
        }

        // Check if all required elements exist
        function validateGaugeElements() {
            const requiredElements = [
                'ti-needle', 'ti-rating', 'tiCounts',
                'summary-needle', 'summary-rating', 'overallCounts',
                'ma-needle', 'ma-rating', 'maCounts'
            ];

            const missingElements = [];

            requiredElements.forEach(id => {
                if (!document.getElementById(id)) {
                    missingElements.push(id);
                }
            });

            if (missingElements.length > 0) {
                console.error('Missing gauge elements:', missingElements);
                return false;
            }

            return true;
        }

        // Initialize all gauges safely
        function initializeGauges() {
            if (!validateGaugeElements()) {
                console.error('Cannot initialize gauges - missing HTML elements');
                return;
            }

            updateTechnicalIndicators('Neutral');
            updateSummary('Neutral');
            updateMovingAverages('Neutral');
        }

        // Safe way to update all gauges
        function updateAllGauges(data) {
            if (!data) {
                console.error('No data provided to update gauges');
                return;
            }

            // Update Technical Indicators if data exists
            if (data.technical) {
                updateTechnicalIndicators(
                    data.technical.rating || 'Neutral',
                    data.technical.buyCount || 0,
                    data.technical.sellCount || 0
                );
            }

            // Update Summary if data exists
            if (data.summary) {
                updateSummary(
                    data.summary.rating || 'Neutral',
                    data.summary.buyCount || 0,
                    data.summary.sellCount || 0
                );
            }

            // Update Moving Averages if data exists
            if (data.movingAverages) {
                updateMovingAverages(
                    data.movingAverages.rating || 'Neutral',
                    data.movingAverages.buyCount || 0,
                    data.movingAverages.sellCount || 0
                );
            }
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
            const container = document.getElementById('taContainer');

            if (!container) return;

            // Clear container and build the correct structure
            container.innerHTML = '';

            // Technical Summary Header
            const summaryHeader = document.createElement('div');
            summaryHeader.className = 'technical-summary-header';
            summaryHeader.style.cssText = 'display: flex; justify-content: center; align-items: center; width: 100%; font-size: 13px; margin-bottom: 15px; padding: 8px; background: var(--signal-bg); border-radius: 6px;';

            const summaryLabel = document.createElement('div');
            summaryLabel.textContent = 'Technical Summary:';
            summaryLabel.style.marginRight = '8px';

            const summaryValue = document.createElement('div');
            const signal = ta.technical_summary || 'Neutral';
            summaryValue.textContent = signal;
            summaryValue.style.cssText = 'font-weight: 600; color: var(--indicator-neutral-text);';

            // Set color based on signal
            if (signal.toLowerCase() === 'buy' || signal.toLowerCase() === 'strong buy') {
                summaryValue.style.color = 'var(--indicator-bullish-text)';
            } else if (signal.toLowerCase() === 'sell' || signal.toLowerCase() === 'strong sell') {
                summaryValue.style.color = 'var(--indicator-bearish-text)';
            }

            summaryHeader.appendChild(summaryLabel);
            summaryHeader.appendChild(summaryValue);
            container.appendChild(summaryHeader);

            // Create table container
            const tableContainer = document.createElement('div');
            tableContainer.style.cssText = 'overflow-x: auto; margin-top: 10px;';

            // Create table
            const table = document.createElement('table');
            table.className = 'ta-table';
            table.style.cssText = 'width: 100%; border-collapse: collapse;';

            // Create table header
            const thead = document.createElement('thead');
            thead.innerHTML = `
        <tr style="background: var(--table-header-bg, #f3f4f6);">
            <th style="text-align: left; padding: 10px; border-bottom: 2px solid var(--border-color); font-size: 13px; height: 38px;">Pattern</th>
            <th style="text-align: center; padding: 10px; border-bottom: 2px solid var(--border-color); font-size: 13px; height: 38px;">Buy (${ta.counts?.buy || 0})</th>
            <th style="text-align: center; padding: 10px; border-bottom: 2px solid var(--border-color); font-size: 13px; height: 38px;">Sell (${ta.counts?.sell || 0})</th>
        </tr>
    `;
            table.appendChild(thead);

            // Create table body
            const tbody = document.createElement('tbody');

            if (ta.patterns_sample && ta.patterns_sample.length > 0) {
                ta.patterns_sample.forEach(pattern => {
                    const row = document.createElement('tr');
                    row.style.height = '32px';

                    // Pattern name cell
                    const nameCell = document.createElement('td');
                    nameCell.style.cssText = 'padding: 0px 10px; min-width: 100px; height: 32px;';

                    const nameLink = document.createElement('a');
                    nameLink.href = '#';
                    nameLink.style.cssText = 'text-decoration: none; color: var(--text-color);';
                    nameLink.textContent = pattern.name;
                    nameCell.appendChild(nameLink);
                    row.appendChild(nameCell);

                    // Buy cell
                    const buyCell = document.createElement('td');
                    buyCell.style.cssText = 'padding: 0px; text-align: center; height: 32px;';

                    if (pattern.buy) {
                        const buyDiv = document.createElement('div');
                        buyDiv.className = 'bg-buy';
                        buyDiv.style.cssText = 'background: var(--indicator-bullish-bg); color: var(--indicator-bullish-text); height: 32px; display: flex; justify-content: center; align-items: center; text-transform: uppercase; font-weight: 600; font-size: 12px;';
                        buyDiv.textContent = pattern.buy;
                        buyCell.appendChild(buyDiv);
                    }
                    row.appendChild(buyCell);

                    // Sell cell
                    const sellCell = document.createElement('td');
                    sellCell.style.cssText = 'padding: 0px; text-align: center; height: 32px;';

                    if (pattern.sell) {
                        const sellDiv = document.createElement('div');
                        sellDiv.className = 'bg-sell';
                        sellDiv.style.cssText = 'background: var(--indicator-bearish-bg); color: var(--indicator-bearish-text); height: 32px; display: flex; justify-content: center; align-items: center; text-transform: uppercase; font-weight: 600; font-size: 12px;';
                        sellDiv.textContent = pattern.sell;
                        sellCell.appendChild(sellDiv);
                    }
                    row.appendChild(sellCell);

                    // If signal is "both", we need to handle buy and sell in separate cells
                    // If signal is neutral or other, we need to show it spanning both columns
                    if (pattern.signal && pattern.signal.toLowerCase() === 'both') {
                        // Already handled above with separate buy/sell cells
                    } else if (pattern.signal && pattern.signal.toLowerCase() === 'neutral') {
                        // Clear cells and create neutral cell spanning both columns
                        buyCell.innerHTML = '';
                        buyCell.style.textAlign = 'center';
                        buyCell.colSpan = 2;

                        const neutralDiv = document.createElement('div');
                        neutralDiv.className = 'bg-neutral';
                        neutralDiv.style.cssText = 'background: var(--indicator-neutral-bg); color: var(--indicator-neutral-text); height: 32px; display: flex; justify-content: center; align-items: center; text-transform: uppercase; font-weight: 600; font-size: 12px; margin: 0px auto; max-width: 43%;';
                        neutralDiv.textContent = pattern.buy || pattern.sell || 'N/A';
                        buyCell.appendChild(neutralDiv);

                        // Remove sell cell
                        row.removeChild(sellCell);
                    } else if (pattern.signal) {
                        // Handle other signals (buy, sell, strong buy, strong sell)
                        const signal = pattern.signal.toLowerCase();
                        if (signal === 'buy' || signal === 'strong buy') {
                            sellCell.innerHTML = '';
                            const buyDiv = document.createElement('div');
                            buyDiv.className = 'bg-buy';
                            buyDiv.style.cssText = 'background: var(--indicator-bullish-bg); color: var(--indicator-bullish-text); height: 32px; display: flex; justify-content: center; align-items: center; text-transform: uppercase; font-weight: 600; font-size: 12px;';
                            buyDiv.textContent = pattern.buy || pattern.timeframe || 'N/A';
                            buyCell.innerHTML = '';
                            buyCell.appendChild(buyDiv);
                        } else if (signal === 'sell' || signal === 'strong sell') {
                            buyCell.innerHTML = '';
                            const sellDiv = document.createElement('div');
                            sellDiv.className = 'bg-sell';
                            sellDiv.style.cssText = 'background: var(--indicator-bearish-bg); color: var(--indicator-bearish-text); height: 32px; display: flex; justify-content: center; align-items: center; text-transform: uppercase; font-weight: 600; font-size: 12px;';
                            sellDiv.textContent = pattern.sell || pattern.timeframe || 'N/A';
                            sellCell.innerHTML = '';
                            sellCell.appendChild(sellDiv);
                        }
                    }

                    tbody.appendChild(row);
                });
            }

            table.appendChild(tbody);
            tableContainer.appendChild(table);
            container.appendChild(tableContainer);

            // Create legend
            const legend = document.createElement('div');
            legend.id = 'technicalSummaryLegend';
            legend.style.cssText = 'display: flex; justify-content: space-between; align-items: center; margin-top: 15px; padding: 10px 0px; font-size: 12px;';

            const legendLabel = document.createElement('div');
            legendLabel.textContent = 'Legend:';
            legendLabel.style.cssText = 'padding-right: 10px; font-weight: 600;';

            const legendItems = document.createElement('div');
            legendItems.style.cssText = 'display: flex; justify-content: space-around; width: 100%;';

            // Buy legend item
            const buyLegend = document.createElement('div');
            buyLegend.style.cssText = 'display: flex; align-items: center;';
            buyLegend.innerHTML = `
        <div style="width: 12px; height: 12px; background: var(--indicator-bullish-bg); border-radius: 2px; margin-right: 6px;"></div>
        <div>Buy</div>
    `;

            // Sell legend item
            const sellLegend = document.createElement('div');
            sellLegend.style.cssText = 'display: flex; align-items: center;';
            sellLegend.innerHTML = `
        <div style="width: 12px; height: 12px; background: var(--indicator-bearish-bg); border-radius: 2px; margin-right: 6px;"></div>
        <div>Sell</div>
    `;

            // Neutral legend item
            const neutralLegend = document.createElement('div');
            neutralLegend.style.cssText = 'display: flex; align-items: center;';
            neutralLegend.innerHTML = `
        <div style="width: 12px; height: 12px; background: var(--indicator-neutral-bg); border-radius: 2px; margin-right: 6px;"></div>
        <div>Neutral</div>
    `;

            legendItems.appendChild(buyLegend);
            legendItems.appendChild(sellLegend);
            legendItems.appendChild(neutralLegend);

            legend.appendChild(legendLabel);
            legend.appendChild(legendItems);
            container.appendChild(legend);

            // Update counts display in console
            if (ta.counts) {
                console.log(`Myfxbook - Buy: ${ta.counts.buy} | Sell: ${ta.counts.sell} | Neutral: ${ta.counts.neutral || 0}`);
            }
        }

        function updateSecondSourceAnalysis(data, symbol, timeframe) {
            if (!data) return;

            // Update overall rating display
            const overallRating = data.overall_signal || 'Neutral';
            let tiSummary = 'Neutral'; // Define with default value
            let maSummary = 'Neutral'; // Define with default value

            // Update summary details
            if (data.moving_averages && data.moving_averages.sample) {
                const maData = data.moving_averages.sample;
                const buyCount = maData.filter(ma =>
                    ma.simple.includes('Buy') || ma.exponential.includes('Buy')
                ).length;
                const sellCount = maData.filter(ma =>
                    ma.simple.includes('Sell') || ma.exponential.includes('Sell')
                ).length;

                maSummary = buyCount > sellCount ? 'Buy' :
                    sellCount > buyCount ? 'Sell' : 'Neutral';

                const maCounts = `Buy: ${buyCount} | Sell: ${sellCount}`;

                // Safely update elements if they exist
                const maRatingEl = document.getElementById('maRating');
                if (maRatingEl) {
                    maRatingEl.textContent = maSummary;
                    maRatingEl.className = maSummary.toLowerCase();
                }

                const maCountsEl = document.getElementById('maCounts');
                if (maCountsEl) {
                    maCountsEl.textContent = maCounts;
                }

                const maSummaryEl = document.getElementById('maSummary');
                if (maSummaryEl) {
                    maSummaryEl.textContent = maSummary;
                    maSummaryEl.className = 'rating-text ' + maSummary.toLowerCase();
                }

                const maSummaryCountsEl = document.getElementById('maSummaryCounts');
                if (maSummaryCountsEl) {
                    maSummaryCountsEl.textContent = maCounts;
                }

                const maTimestampEl = document.getElementById('maTimestamp');
                if (maTimestampEl) {
                    maTimestampEl.textContent = data.scrape_timestamp || '-';
                }

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

                tiSummary = buyCount > sellCount ? 'Buy' :
                    sellCount > buyCount ? 'Sell' : 'Neutral';

                const tiCounts = `Buy: ${buyCount} | Sell: ${sellCount}`;

                // Safely update elements if they exist
                const tiRatingEl = document.getElementById('tiRating');
                if (tiRatingEl) {
                    tiRatingEl.textContent = tiSummary;
                    tiRatingEl.className = tiSummary.toLowerCase();
                }

                const tiCountsEl = document.getElementById('tiCounts');
                if (tiCountsEl) {
                    tiCountsEl.textContent = tiCounts;
                }

                const tiSummaryEl = document.getElementById('tiSummary');
                if (tiSummaryEl) {
                    tiSummaryEl.textContent = tiSummary;
                    tiSummaryEl.className = 'rating-text ' + tiSummary.toLowerCase();
                }

                const tiSummaryCountsEl = document.getElementById('tiSummaryCounts');
                if (tiSummaryCountsEl) {
                    tiSummaryCountsEl.textContent = tiCounts;
                }

                const tiTimestampEl = document.getElementById('tiTimestamp');
                if (tiTimestampEl) {
                    tiTimestampEl.textContent = data.scrape_timestamp || '-';
                }

                // Update technical indicators table
                updateTechnicalIndicatorsTable(tiData);
            }

            if (data.pivot_points && data.pivot_points.sample) {
                updatePivotPointsTable(data.pivot_points.sample);
            }

            // Update gauges - these should exist if you have the 3-gauge layout
            updateGaugeRating('summary-needle', 'summary-rating', overallRating);
            updateGaugeRating('ti-needle', 'ti-rating', tiSummary);
            updateGaugeRating('ma-needle', 'ma-rating', maSummary);

            // Also update count displays on the gauges if they exist
            if (data.technical_indicators && data.technical_indicators.sample) {
                const tiData = data.technical_indicators.sample;
                const tiBuyCount = tiData.filter(ti =>
                    ti.action === 'Buy' || ti.action === 'Strong Buy'
                ).length;
                const tiSellCount = tiData.filter(ti =>
                    ti.action === 'Sell' || ti.action === 'Strong Sell'
                ).length;

                // Update the gauge count display
                const tiCountsEl = document.getElementById('tiCounts');
                if (tiCountsEl) {
                    tiCountsEl.textContent = `Buy: ${tiBuyCount} | Sell: ${tiSellCount}`;
                }
            }

            if (data.moving_averages && data.moving_averages.sample) {
                const maData = data.moving_averages.sample;
                const maBuyCount = maData.filter(ma =>
                    ma.simple.includes('Buy') || ma.exponential.includes('Buy')
                ).length;
                const maSellCount = maData.filter(ma =>
                    ma.simple.includes('Sell') || ma.exponential.includes('Sell')
                ).length;

                // Update the gauge count display
                const maCountsEl = document.getElementById('maCounts');
                if (maCountsEl) {
                    maCountsEl.textContent = `Buy: ${maBuyCount} | Sell: ${maSellCount}`;
                }
            }

            // Update summary counts if the element exists
            const overallCountsEl = document.getElementById('overallCounts');
            if (overallCountsEl) {
                // You might want to calculate overall counts from both MA and TI
                const tiBuyCount = data.technical_indicators?.sample?.filter(ti =>
                    ti.action === 'Buy' || ti.action === 'Strong Buy'
                ).length || 0;
                const tiSellCount = data.technical_indicators?.sample?.filter(ti =>
                    ti.action === 'Sell' || ti.action === 'Strong Sell'
                ).length || 0;

                const maBuyCount = data.moving_averages?.sample?.filter(ma =>
                    ma.simple.includes('Buy') || ma.exponential.includes('Buy')
                ).length || 0;
                const maSellCount = data.moving_averages?.sample?.filter(ma =>
                    ma.simple.includes('Sell') || ma.exponential.includes('Sell')
                ).length || 0;

                const totalBuy = tiBuyCount + maBuyCount;
                const totalSell = tiSellCount + maSellCount;

                overallCountsEl.textContent = `Buy: ${totalBuy} | Sell: ${totalSell}`;
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

            const container = document.getElementById('interestRateContainer');
            if (!container) return;

            // Calculate stats for header
            const rates = interestRateData.rates_sample.map(r => parseFloat(r.currentRate));
            const highestRate = Math.max(...rates);
            const avgRate = rates.reduce((a, b) => a + b, 0) / rates.length;

            // Build the HTML structure matching the example
            let html = `
        <div class="interest-rate-header" style="display: flex; justify-content: space-between; align-items: center; padding: 15px 20px; background: var(--bg-secondary); border-bottom: 1px solid var(--border-color);">
            <div style="display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-percentage" style="color: var(--accent-blue); font-size: 18px;"></i>
                <h3 style="margin: 0px; font-size: 16px; font-weight: 600; color: var(--text-primary);">Central Bank Rates</h3>
            </div>
            <div style="display: flex; align-items: center; gap: 15px; font-size: 12px; color: var(--text-secondary);">
                <div><strong style="color: var(--accent-red);">${highestRate.toFixed(2)}%</strong> Highest</div>
                <div><strong style="color: var(--text-primary);">${avgRate.toFixed(2)}%</strong> Avg</div>
            </div>
        </div>
        <div style="overflow-x: auto;">
            <table class="interest-rate-table" style="width: 100%; border-collapse: collapse; background: var(--bg-primary);">
                <thead>
                    <tr style="background: var(--bg-secondary);">
                        <th style="
                            padding: 15px 20px;
                            text-align: left;
                            border-bottom: 1px solid var(--border-color);
                            font-size: 12px;
                            font-weight: 600;
                            color: var(--text-secondary);
                            text-transform: uppercase;
                            letter-spacing: 0.5px;
                            width: 180px;
                        ">Country</th>
                        <th style="
                            padding: 15px 20px;
                            text-align: left;
                            border-bottom: 1px solid var(--border-color);
                            font-size: 12px;
                            font-weight: 600;
                            color: var(--text-secondary);
                            text-transform: uppercase;
                            letter-spacing: 0.5px;
                            width: 180px;
                        ">Central Bank</th>
                        <th style="
                            padding: 15px 20px;
                            text-align: right;
                            border-bottom: 1px solid var(--border-color);
                            font-size: 12px;
                            font-weight: 600;
                            color: var(--text-secondary);
                            text-transform: uppercase;
                            letter-spacing: 0.5px;
                            width: 100px;
                        ">Current</th>
                        <th style="
                            padding: 15px 20px;
                            text-align: right;
                            border-bottom: 1px solid var(--border-color);
                            font-size: 12px;
                            font-weight: 600;
                            color: var(--text-secondary);
                            text-transform: uppercase;
                            letter-spacing: 0.5px;
                            width: 100px;
                        ">Previous</th>
                        <th style="
                            padding: 15px 20px;
                            text-align: center;
                            border-bottom: 1px solid var(--border-color);
                            font-size: 12px;
                            font-weight: 600;
                            color: var(--text-secondary);
                            text-transform: uppercase;
                            letter-spacing: 0.5px;
                            width: 120px;
                        ">Change</th>
                        <th style="
                            padding: 15px 20px;
                            text-align: center;
                            border-bottom: 1px solid var(--border-color);
                            font-size: 12px;
                            font-weight: 600;
                            color: var(--text-secondary);
                            text-transform: uppercase;
                            letter-spacing: 0.5px;
                            width: 140px;
                        ">Next Meeting</th>
                    </tr>
                </thead>
                <tbody>
    `;

            interestRateData.rates_sample.forEach(rate => {
                const currentRate = parseFloat(rate.currentRate);
                const previousRate = parseFloat(rate.previousRate);
                const change = currentRate - previousRate;

                // Determine change style
                let changeStyle = 'background: rgba(100, 116, 139, 0.1); color: var(--text-muted); border: 1px solid rgba(100, 116, 139, 0.2);';
                let changeText = '0.00%';

                if (change > 0) {
                    changeStyle = 'background: rgba(239, 68, 68, 0.1); color: var(--accent-red); border: 1px solid rgba(239, 68, 68, 0.2);';
                    changeText = `+${change.toFixed(2)}%`;
                } else if (change < 0) {
                    changeStyle = 'background: rgba(16, 185, 129, 0.1); color: var(--accent-green); border: 1px solid rgba(16, 185, 129, 0.2);';
                    changeText = `${change.toFixed(2)}%`;
                }

                // Determine meeting style based on days
                const meetingDays = parseInt(rate.nextMeeting) || 0;
                let meetingStyle = 'background: rgba(59, 130, 246, 0.1); color: var(--accent-blue); border: 1px solid rgba(59, 130, 246, 0.2);';

                if (meetingDays < 7) {
                    meetingStyle = 'background: rgba(239, 68, 68, 0.1); color: var(--accent-red); border: 1px solid rgba(239, 68, 68, 0.2);';
                } else if (meetingDays < 30) {
                    meetingStyle = 'background: rgba(245, 158, 11, 0.1); color: var(--accent-yellow); border: 1px solid rgba(245, 158, 11, 0.2);';
                }

                // Get currency symbol and flag style
                const currencyCode = rate.country === 'Euro Area' ? 'EUR' :
                    rate.country === 'Japan' ? 'JPY' :
                    rate.country === 'United States' ? 'USD' :
                    rate.country === 'United Kingdom' ? 'GBP' :
                    rate.country === 'Switzerland' ? 'CHF' : '';

                let flagStyle = 'background: linear-gradient(135deg, #003399 0%, #ffcc00 100%); color: white;';
                let currencyIcon = 'fas fa-euro-sign';

                if (currencyCode === 'JPY') {
                    flagStyle = 'background: linear-gradient(135deg, #bc002d 0%, #ffffff 100%); color: white;';
                    currencyIcon = 'fas fa-yen-sign';
                } else if (currencyCode === 'USD') {
                    flagStyle = 'background: linear-gradient(135deg, #3c3b6e 0%, #b22234 100%); color: white;';
                    currencyIcon = 'fas fa-dollar-sign';
                } else if (currencyCode === 'GBP') {
                    flagStyle = 'background: linear-gradient(135deg, #012169 0%, #c8102e 100%); color: white;';
                    currencyIcon = 'fas fa-pound-sign';
                } else if (currencyCode === 'CHF') {
                    flagStyle = 'background: linear-gradient(135deg, #ff0000 0%, #ffffff 100%); color: white;';
                    currencyIcon = 'fas fa-franc-sign';
                }

                html += `
            <tr style="border-bottom: 1px solid var(--border-color); transition: background-color 0.2s; background-color: transparent;">
                <td style="
                    padding: 15px 20px;
                    text-align: left;
                    font-weight: 500;
                    color: var(--text-primary);
                ">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <div class="country-flag" style="
                            width: 24px;
                            height: 24px;
                            border-radius: 50%;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            font-size: 12px;
                            font-weight: bold;
                            ${flagStyle}
                        ">
                            <i class="${currencyIcon}"></i>
                        </div>
                        <div>
                            <div style="font-size: 14px; font-weight: 500;">${rate.country}</div>
                            <div style="font-size: 11px; color: var(--text-secondary); margin-top: 2px;">${currencyCode}</div>
                        </div>
                    </div>
                </td>
                <td style="
                    padding: 15px 20px;
                    text-align: left;
                    color: var(--text-primary);
                    font-size: 14px;
                ">
                    <div>${rate.centralBank}</div>
                    <div style="
                        font-size: 11px;
                        color: var(--text-secondary);
                        margin-top: 2px;
                    ">
                        ${getBankAbbreviation(rate.centralBank)}
                    </div>
                </td>
                <td style="
                    padding: 15px 20px;
                    text-align: right;
                    font-size: 16px;
                    font-weight: 700;
                    color: var(--text-primary);
                ">
                    ${rate.currentRate}%
                </td>
                <td style="
                    padding: 15px 20px;
                    text-align: right;
                    font-size: 14px;
                    color: var(--text-secondary);
                ">
                    ${rate.previousRate}%
                </td>
                <td style="
                    padding: 15px 20px;
                    text-align: center;
                ">
                    <div style="
                        display: inline-block;
                        padding: 6px 12px;
                        border-radius: 20px;
                        font-size: 12px;
                        font-weight: 600;
                        ${changeStyle}
                    ">
                        ${changeText}
                    </div>
                </td>
                <td style="
                    padding: 15px 20px;
                    text-align: center;
                ">
                    <div style="
                        display: inline-block;
                        padding: 6px 12px;
                        border-radius: 20px;
                        font-size: 12px;
                        font-weight: 600;
                        ${meetingStyle}
                    ">
                        ${rate.nextMeeting}
                    </div>
                </td>
            </tr>
        `;
            });

            html += `
                </tbody>
            </table>
        </div>
        <div style="display: flex; justify-content: space-between; align-items: center; padding: 15px 20px; background: var(--bg-secondary); border-top: 1px solid var(--border-color); font-size: 11px; color: var(--text-secondary);">
            <div style="display: flex; align-items: center; gap: 15px;">
                <div style="display: flex; align-items: center; gap: 5px;">
                    <div style="width: 10px; height: 10px; border-radius: 50%; background: var(--accent-red);"></div>
                    <span>Rate Hike</span>
                </div>
                <div style="display: flex; align-items: center; gap: 5px;">
                    <div style="width: 10px; height: 10px; border-radius: 50%; background: var(--accent-green);"></div>
                    <span>Rate Cut</span>
                </div>
                <div style="display: flex; align-items: center; gap: 5px;">
                    <div style="width: 10px; height: 10px; border-radius: 50%; background: var(--text-muted);"></div>
                    <span>On Hold</span>
                </div>
            </div>
            <div style="display: flex; align-items: center; gap: 15px;">
                <div style="display: flex; align-items: center; gap: 5px;">
                    <div style="width: 10px; height: 10px; border-radius: 50%; background: var(--accent-red);"></div>
                    <span>&lt; 7 days</span>
                </div>
                <div style="display: flex; align-items: center; gap: 5px;">
                    <div style="width: 10px; height: 10px; border-radius: 50%; background: var(--accent-yellow);"></div>
                    <span>&lt; 30 days</span>
                </div>
                <div style="display: flex; align-items: center; gap: 5px;">
                    <div style="width: 10px; height: 10px; border-radius: 50%; background: var(--accent-blue);"></div>
                    <span>&gt; 30 days</span>
                </div>
            </div>
        </div>
    `;

            container.innerHTML = html;

            // Update scrape time if you still want to show it
            const scrapeTimeElement = document.getElementById('scrapeTime');
            if (scrapeTimeElement) {
                scrapeTimeElement.textContent = new Date().toLocaleString();
            }
        }

        // Helper function to get bank abbreviations
        function getBankAbbreviation(bankName) {
            const abbreviations = {
                'European Central Bank': 'ECB',
                'Bank of Japan': 'BoJ',
                'Federal Reserve': 'Fed',
                'Bank of England': 'BoE',
                'Swiss National Bank': 'SNB',
                'Reserve Bank of Australia': 'RBA',
                'Bank of Canada': 'BoC',
                'Reserve Bank of New Zealand': 'RBNZ'
            };

            return abbreviations[bankName] || bankName.substring(0, 3).toUpperCase();
        }

        // If you want to keep your original HTML structure but just fix the flags,
        // here's a simpler version that only updates the flags:
        function renderInterestRateSimple(interestRateData) {
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

                // Get currency icon based on country
                const country = rate.country.toLowerCase();
                let currencyIcon = 'fas fa-money-bill-wave';

                if (country.includes('euro') || country.includes('eur')) {
                    currencyIcon = 'fas fa-euro-sign';
                } else if (country.includes('japan') || country.includes('jpy')) {
                    currencyIcon = 'fas fa-yen-sign';
                } else if (country.includes('usa') || country.includes('usd') || country.includes('united states')) {
                    currencyIcon = 'fas fa-dollar-sign';
                } else if (country.includes('uk') || country.includes('gbp') || country.includes('united kingdom')) {
                    currencyIcon = 'fas fa-pound-sign';
                } else if (country.includes('swiss') || country.includes('chf')) {
                    currencyIcon = 'fas fa-franc-sign';
                }

                const row = document.createElement('tr');
                row.innerHTML = `
            <td class="country-col">
                <div class="country-flag">
                    <i class="${currencyIcon}"></i>
                </div>
                <span>${rate.country}</span>
            </td>
            <td class="bank-col">${rate.centralBank}</td>
            <td class="rate-col">${rate.currentRate}%</td>
            <td class="previous-col">${rate.previousRate}%</td>
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

            // Check if we're on a page with gauges
            if (document.querySelector('.analysis-container')) {
                initializeGauges();
            }
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