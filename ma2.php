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
                        <option value="15">M15</option>
                        <option value="30">M30</option>
                        <option value="60">H1</option>
                        <option value="240" selected>H4</option>
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
                                    <span class="last-update" id="time-since-update">Never updated</span>
                                </div>
                            </div>
                            <div class="chart-controls">
                                <div class="status-indicator">
                                    <span class="live-dot"></span> Live Market Data
                                </div>
                                <button class="refresh-btn" onclick="loadChart()" id="refresh-btn">
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

                        <!-- Market Analysis First Source (Myfxbook) -->
                        <section class="widget full-width">
                            <div class="widget-header">
                                <h2>📈 Market Analysis First Source</h2>
                                <div class="timeframe-display" id="current-tf">H4</div>
                            </div>
                            <div class="widget-content">
                                <div class="analysis-placeholder">
                                    <div class="analysis-item">
                                        <div class="ta-container" id="taContainer">
                                            <div class="signal" id="signal">
                                                <span class="label">Overall Signal:</span>
                                                <span class="value" id="overallSignal">Loading...</span>
                                            </div>
                                            <div class="indicators">
                                                <h3>Indicators (<span id="indicatorsCount">0</span>)</h3>
                                                <div id="indicatorsList">
                                                    <p class="loading-text">Loading indicators...</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="analysis-item">
                                        <h3>Support & Resistance</h3>
                                        <div class="indicator-row">
                                            <span>Support:</span>
                                            <span class="support" id="supportLevel">-</span>
                                        </div>
                                        <div class="indicator-row">
                                            <span>Resistance:</span>
                                            <span class="resistance" id="resistanceLevel">-</span>
                                        </div>
                                        <div class="indicator-row">
                                            <span>Pivot:</span>
                                            <span class="pivot" id="pivotLevel">-</span>
                                        </div>
                                        <div class="indicator-row">
                                            <span>Range:</span>
                                            <span id="rangeValue">-</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- Market Analysis Second Source (Investing.com) -->
                        <section class="widget full-width">
                            <div class="widget-header">
                                <h2>📈 Market Analysis Second Source</h2>
                                <div class="timeframe-display" id="current-tf-second">H4</div>
                            </div>
                            <div class="widget-content">
                                <div class="analysis-second-placeholder">
                                    <!-- Summary with Gauge -->
                                    <div class="analysis-item">
                                        <h3>Summary</h3>
                                        <!-- Neutral gauge by default -->
                                        <div class="gauge-container">
                                            <div class="analyst-price-target_gaugeContainer__F_79r" id="gaugeContainer" style="width: 300px; height: 135px;" data-c="2"> <!-- Changed to 2 for neutral -->
                                                <div class="analyst-price-target_gauge__mc_8B" style="width: 204px; height: 102px;">
                                                    <!-- Background bars for visualization -->
                                                    <div class="analyst-price-target_bar__nhotN" data-i="0" style="left: 0; width: 40px; background: linear-gradient(to right, #dc2626, #ef4444);"></div>
                                                    <div class="analyst-price-target_bar__nhotN" data-i="1" style="left: 40px; width: 40px; background: linear-gradient(to right, #ef4444, #f59e0b);"></div>
                                                    <div class="analyst-price-target_bar__nhotN" data-i="2" style="left: 80px; width: 40px; background: #f59e0b;"></div> <!-- Neutral position highlighted -->
                                                    <div class="analyst-price-target_bar__nhotN" data-i="3" style="left: 120px; width: 40px; background: linear-gradient(to right, #f59e0b, #22c55e);"></div>
                                                    <div class="analyst-price-target_bar__nhotN" data-i="4" style="left: 160px; width: 40px; background: linear-gradient(to right, #22c55e, #10b981);"></div>

                                                    <!-- Arrow indicator - position for neutral (90deg = straight up) -->
                                                    <div class="analyst-price-target_indicator__dhPLO" style="width: 198px; height: 198px; top: 3px; left: 3px;">
                                                        <div class="analyst-price-target_arrow__ZRmAZ" id="gaugeArrow" style="bottom: 101px; left: 98px; height: 60px; transform: rotate(90deg);"> <!-- 90deg = neutral -->
                                                            <svg width="20" height="60" viewBox="0 0 20 60">
                                                                <path d="M10 0 L20 60 L0 60 Z" fill="#ffffff" stroke="#1a1f2e" stroke-width="2" />
                                                            </svg>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Labels: Strong Sell (left) to Strong Buy (right) -->
                                                <div class="analyst-price-target_strongSell__OtkYw" style="bottom: 19px; left: 5px;">Strong Sell</div>
                                                <div class="analyst-price-target_sell__umjJy" style="left: 40px; top: 15px;">Sell</div>
                                                <div class="analyst-price-target_neutral__L8xm4" style="left: 88px; top: -11px;">Neutral</div>
                                                <div class="analyst-price-target_buy__5XS2x" style="right: 40px; top: 15px;">Buy</div>
                                                <div class="analyst-price-target_strongBuy__QaJ8j" style="bottom: 19px; right: 5px;">Strong Buy</div>
                                            </div>
                                            <div class="gauge-rating" id="gaugeRating">Neutral</div>
                                        </div>
                                        <div class="summary-details">
                                            <div class="indicator-row">
                                                <span>Moving Averages:</span>
                                                <span class="rating-text" id="maRating">Loading...</span>
                                                <span id="maCounts">-</span>
                                            </div>
                                            <div class="indicator-row">
                                                <span>Technical Indicators:</span>
                                                <span class="rating-text" id="tiRating">Loading...</span>
                                                <span id="tiCounts">-</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Technical Indicators Table -->
                                    <div class="analysis-item">
                                        <h3>Technical Indicators</h3>
                                        <div class="table-header">
                                            <span>Summary: <strong class="rating-text" id="tiSummary">Loading...</strong></span>
                                            <span id="tiSummaryCounts">-</span>
                                            <span class="timestamp" id="tiTimestamp">Loading...</span>
                                        </div>
                                        <div class="table-container">
                                            <table class="rates-table" id="tiTable">
                                                <thead>
                                                    <tr>
                                                        <th class="name-col">Name</th>
                                                        <th class="value-col">Value</th>
                                                        <th class="action-col">Action</th>
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
                                            <span>Summary: <strong class="rating-text" id="maSummary">Loading...</strong></span>
                                            <span id="maSummaryCounts">-</span>
                                            <span class="timestamp" id="maTimestamp">Loading...</span>
                                        </div>
                                        <div class="table-container">
                                            <table class="rates-table" id="maTable">
                                                <thead>
                                                    <tr>
                                                        <th class="name-col">Name</th>
                                                        <th class="simple-col">Simple</th>
                                                        <th class="exponential-col">Exponential</th>
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
                                                        <th class="name-col">Name</th>
                                                        <th class="value-col">S3</th>
                                                        <th class="value-col">S2</th>
                                                        <th class="value-col">S1</th>
                                                        <th class="pivot-col">Pivot Points</th>
                                                        <th class="value-col">R1</th>
                                                        <th class="value-col">R2</th>
                                                        <th class="value-col">R3</th>
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
                                    <!-- <div class="header-actions">
                                        <div class="last-updated">
                                            <i class="fas fa-sync-alt"></i>
                                            <span>Updated: <strong id="updateTimestamp">Today 14:30 GMT</strong></span>
                                        </div>
                                        <div class="rate-filter">
                                            <button class="filter-btn active">All</button>
                                            <button class="filter-btn">Hiking</button>
                                            <button class="filter-btn">Cutting</button>
                                        </div>
                                    </div> -->
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
                                            <span>Last scraped: <span id="scrapeTime">2026-01-08 13:30</span></span>
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
                                <div class="detailed-analysis">
                                    <div class="analysis-card">
                                        <h4>Price Action</h4>
                                        <p>Currently trading above 50-day moving average. Bullish engulfing pattern detected on H4 timeframe.</p>
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

        // Initialize the gauge as neutral on page load
        function initializeGaugeAsNeutral() {
            // Set gauge to neutral position (index 2)
            const gaugeContainer = document.getElementById('gaugeContainer');
            const gaugeArrow = document.getElementById('gaugeArrow');
            const gaugeRating = document.getElementById('gaugeRating');

            if (gaugeContainer) gaugeContainer.setAttribute('data-c', '2');
            if (gaugeArrow) gaugeArrow.style.transform = 'rotate(90deg)';
            if (gaugeRating) {
                gaugeRating.textContent = 'Neutral';
                gaugeRating.className = 'gauge-rating neutral';
            }

            // Update rating text colors
            const ratingElements = document.querySelectorAll('.rating-text');
            ratingElements.forEach(el => {
                el.textContent = 'Loading...';
                el.className = 'rating-text neutral';
            });
        }

        // Update gauge based on rating - FIXED NEEDLE POSITIONS
        function updateGauge(rating) {
            const ratingMap = {
                'Strong Sell': {
                    index: 0,
                    angle: 50,
                    className: 'strong-sell'
                },
                'Sell': {
                    index: 1,
                    angle: 70,
                    className: 'sell'
                },
                'Neutral': {
                    index: 2,
                    angle: 90,
                    className: 'neutral'
                },
                'Buy': {
                    index: 3,
                    angle: 110,
                    className: 'buy'
                },
                'Strong Buy': {
                    index: 4,
                    angle: 130,
                    className: 'strong-buy'
                }
            };

            const gaugeData = ratingMap[rating] || ratingMap['Neutral'];
            const gaugeContainer = document.getElementById('gaugeContainer');
            const gaugeArrow = document.getElementById('gaugeArrow');
            const gaugeRating = document.getElementById('gaugeRating');

            console.log('Updating gauge to:', rating, 'Angle:', gaugeData.angle);

            if (gaugeContainer) gaugeContainer.setAttribute('data-c', gaugeData.index);
            if (gaugeArrow) {
                gaugeArrow.style.transform = `rotate(${gaugeData.angle}deg)`;
                console.log('Arrow transform set to:', gaugeArrow.style.transform);
            }
            if (gaugeRating) {
                gaugeRating.textContent = rating;
                gaugeRating.className = `gauge-rating ${gaugeData.className}`;
            }
        }

        // Update market status display
        function updateMarketStatus(symbol) {
            const statusElement = document.querySelector('.status-indicator');
            if (statusElement) {
                statusElement.innerHTML = '<span class="live-dot"></span> Live ' + symbol + ' Data';
            }

            // Update timestamp
            const updateTime = document.getElementById('time-since-update');
            if (updateTime) {
                updateTime.textContent = 'Updated: Just now';
            }
        }

        // Get fallback data for when API fails
    function getFallbackData(pair, timeframe) {
        console.log('Using fallback data for', pair, timeframe);
        return {
            success: true,
            data: {
                myfxbook: {
                    overallSignal: 'Neutral',
                    indicators: [],
                    support: 158.50,
                    resistance: 160.20,
                    pivot: 159.35
                },
                investing: {
                    overallRating: 'Neutral',
                    movingAverages: {
                        summary: 'Neutral',
                        counts: 'Buy: 6 | Sell: 6'
                    },
                    technicalIndicators: {
                        summary: 'Neutral',
                        counts: 'Buy: 6 | Sell: 6'
                    },
                    timestamp: new Date().toLocaleString()
                },
                combined: {
                    interestRates: []
                }
            }
        };
    }

    // Update Market Analysis First Source
    function updateFirstSourceAnalysis(data, symbol, timeframe) {
        if (!data) return;
        
        document.getElementById('overallSignal').textContent = data.overallSignal || 'Neutral';
        document.getElementById('overallSignal').className = (data.overallSignal || 'neutral').toLowerCase().replace(' ', '-');
        
        if (data.indicators && data.indicators.length > 0) {
            document.getElementById('indicatorsCount').textContent = data.indicators.length;
            const indicatorsList = document.getElementById('indicatorsList');
            indicatorsList.innerHTML = '';
            data.indicators.forEach(indicator => {
                const div = document.createElement('div');
                div.className = 'indicator-item';
                div.innerHTML = `
                    <span class="indicator-name">${indicator.name}</span>
                    <span class="indicator-value">${indicator.value}</span>
                    <span class="indicator-action ${indicator.action.toLowerCase()}">${indicator.action}</span>
                `;
                indicatorsList.appendChild(div);
            });
        }
        
        // Update support & resistance
        if (data.support) document.getElementById('supportLevel').textContent = data.support;
        if (data.resistance) document.getElementById('resistanceLevel').textContent = data.resistance;
        if (data.pivot) document.getElementById('pivotLevel').textContent = data.pivot;
        
        // Calculate range
        if (data.support && data.resistance) {
            const range = Math.abs(data.resistance - data.support);
            document.getElementById('rangeValue').textContent = range.toFixed(2) + ' pips';
        }
    }

    // Update interest rates dashboard
    function updateInterestRatesDashboard(ratesData, symbol) {
        // Implementation depends on your API response structure
        console.log('Updating interest rates:', ratesData);
        // Add your interest rates update logic here
    }

    // Update support and resistance
    function updateSupportResistance(symbol, timeframe) {
        // This would typically come from your API
        // For now, using placeholder values
        const supportResistance = {
            'EURJPY': { support: 158.50, resistance: 160.20, pivot: 159.35 },
            'USDJPY': { support: 148.50, resistance: 150.20, pivot: 149.35 },
            'EURUSD': { support: 1.0850, resistance: 1.1020, pivot: 1.0935 }
        };
        
        const data = supportResistance[symbol] || supportResistance['EURJPY'];
        
        document.getElementById('supportLevel').textContent = data.support;
        document.getElementById('resistanceLevel').textContent = data.resistance;
        document.getElementById('pivotLevel').textContent = data.pivot;
        document.getElementById('rangeValue').textContent = '170 pips';
    }

    // Update detailed analysis
    function updateDetailedAnalysis(data, symbol, timeframe) {
        // Add detailed analysis update logic here
        console.log('Updating detailed analysis for', symbol, timeframe);
    }

    // Update analysis with fallback data
    function updateAnalysisWithFallback(symbol, timeframe) {
        const fallbackData = getFallbackData(symbol, timeframe);
        
        if (fallbackData.success && fallbackData.data) {
            updateFirstSourceAnalysis(fallbackData.data.myfxbook, symbol, timeframe);
            updateSecondSourceAnalysis(fallbackData.data.investing, symbol, timeframe);
            updateInterestRatesDashboard(fallbackData.data.combined?.interestRates || [], symbol);
            updateSupportResistance(symbol, timeframe);
            updateDetailedAnalysis(fallbackData.data, symbol, timeframe);
        }
    }

    // Show rate limit message
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

    // Remove rate limit message
    function removeRateLimitMessage() {
        const existingMessage = document.querySelector('.rate-limit-message');
        if (existingMessage) {
            existingMessage.remove();
        }
    }
    

        // Update Market Analysis Second Source
        function updateSecondSourceAnalysis(data, symbol, timeframe) {
            if (!data) return;

            // Update gauge based on overall rating
            const overallRating = data.overallRating || 'Neutral';
            updateGauge(overallRating);

            // Update summary details
            document.getElementById('maRating').textContent = data.movingAverages?.summary || 'Neutral';
            document.getElementById('maRating').className = `rating-text ${(data.movingAverages?.summary || 'neutral').toLowerCase().replace(' ', '-')}`;
            document.getElementById('maCounts').textContent = data.movingAverages?.counts || '-';

            document.getElementById('tiRating').textContent = data.technicalIndicators?.summary || 'Neutral';
            document.getElementById('tiRating').className = `rating-text ${(data.technicalIndicators?.summary || 'neutral').toLowerCase().replace(' ', '-')}`;
            document.getElementById('tiCounts').textContent = data.technicalIndicators?.counts || '-';

            // Update table headers
            document.getElementById('tiSummary').textContent = data.technicalIndicators?.summary || 'Neutral';
            document.getElementById('tiSummary').className = data.technicalIndicators?.summary?.toLowerCase().replace(' ', '-') || 'neutral';
            document.getElementById('tiSummaryCounts').textContent = data.technicalIndicators?.counts || '-';
            document.getElementById('tiTimestamp').textContent = data.timestamp || new Date().toLocaleString();

            document.getElementById('maSummary').textContent = data.movingAverages?.summary || 'Neutral';
            document.getElementById('maSummary').className = data.movingAverages?.summary?.toLowerCase().replace(' ', '-') || 'neutral';
            document.getElementById('maSummaryCounts').textContent = data.movingAverages?.counts || '-';
            document.getElementById('maTimestamp').textContent = data.timestamp || new Date().toLocaleString();

            // Update tables with data
            if (data.technicalIndicators?.table) {
                updateTableData('tiTableBody', data.technicalIndicators.table);
            }

            if (data.movingAverages?.table) {
                updateTableData('maTableBody', data.movingAverages.table);
            }

            if (data.pivotPoints?.table) {
                updateTableData('pivotTableBody', data.pivotPoints.table);
            }
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

                // Update header with market status (no API call needed)
                updateMarketStatus(symbol);

            }, 100);
        }

        async function fetchTechnicalAnalysis(pair, timeframe) {
            try {
                // Check rate limiting BEFORE making the request
                const now = Date.now();
                if (now - lastApiRequestTime < API_REQUEST_COOLDOWN) {
                    const remainingTime = Math.ceil((API_REQUEST_COOLDOWN - (now - lastApiRequestTime)) / 1000);

                    // Show rate limit message only once per rate limit period
                    if (!rateLimitMessageShown) {
                        showRateLimitMessage(remainingTime);
                        rateLimitMessageShown = true;

                        // Reset rate limit message flag after cooldown
                        setTimeout(() => {
                            rateLimitMessageShown = false;
                        }, API_REQUEST_COOLDOWN);
                    }

                    console.log(`Rate limited: Please wait ${remainingTime} seconds before making another API request`);
                    throw new Error(`REQUEST TOO SOON: Please wait ${remainingTime} seconds`);
                }

                // Convert timeframe to match API format
                const tfMap = {
                    '15': 'M15',
                    '30': 'M30',
                    '60': 'H1',
                    '240': 'H4',
                    'D': 'D1',
                    'W': 'W1'
                };

                const apiTimeframe = tfMap[timeframe] || timeframe;

                // Prepare request data
                const formData = new FormData();
                formData.append("action", "get_latest_scrape_data_v2");
                formData.append("pair", pair);
                formData.append("timeframe", apiTimeframe);

                const PROXY_ENDPOINT = "/proxy.php";

                console.log("Fetching V2 data for:", pair, apiTimeframe);

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
                console.log("Scrape Data V2 response:", data);

                // Check if action matches
                if (data.action !== "get_latest_scrape_data_v2") {
                    console.warn("Unexpected action in response:", data.action);
                }

                if (!data.success) {
                    throw new Error(data.message || "Failed to fetch technical analysis");
                }

                return data;

            } catch (error) {
                console.error('Error fetching technical analysis V2:', error);

                // Don't use fallback data if rate limited - just throw the error
                if (error.message.includes('REQUEST TOO SOON')) {
                    throw error;
                }

                return getFallbackData(pair, timeframe);
            }
        }

        /**
         * Show rate limit message to user
         */
        function showRateLimitMessage(remainingSeconds) {
            // Remove any existing rate limit message
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

            // Auto-remove after 5 seconds
            setTimeout(() => {
                removeRateLimitMessage();
            }, 5000);
        }

        /**
         * Remove rate limit message
         */
        function removeRateLimitMessage() {
            const existingMessage = document.querySelector('.rate-limit-message');
            if (existingMessage) {
                existingMessage.remove();
            }
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

                const scrapedData = apiResponse.data;

                // Update Market Analysis First Source (Myfxbook)
                updateFirstSourceAnalysis(scrapedData.myfxbook, symbol, timeframe);

                // Update Market Analysis Second Source (Investing.com)
                updateSecondSourceAnalysis(scrapedData.investing, symbol, timeframe);

                // Update Interest Rates Dashboard
                updateInterestRatesDashboard(scrapedData.combined?.interestRates || [], symbol);

                // Update Support & Resistance with data
                updateSupportResistance(symbol, timeframe);

                // Update detailed analysis
                updateDetailedAnalysis(scrapedData, symbol, timeframe);

            } catch (error) {
                console.error('Error updating analysis:', error);

                // Check if it's a rate limit error
                if (error.message.includes('REQUEST TOO SOON')) {
                    // Show error in the analysis section
                    const analysisContainer = document.querySelector('.analysis-placeholder');
                    if (analysisContainer) {
                        analysisContainer.innerHTML = `
                    <div class="analysis-item">
                        <div class="ta-container" id="taContainer">
                            <div class="signal" id="signal">
                                <span class="label">Rate Limited:</span>
                                <span class="value" style="color: #ef4444;">${error.message}</span>
                            </div>
                            <div class="indicators">
                                <h3>Indicators (<span id="indicatorsCount">0</span>)</h3>
                                <div id="indicatorsList">
                                    <p style="text-align: center; color: var(--text-secondary); padding: 20px;">
                                        Please wait before refreshing again
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                    }

                    // Also update second source
                    const secondSource = document.querySelector('.analysis-second-placeholder');
                    if (secondSource) {
                        secondSource.innerHTML = `
                    <div class="analysis-item">
                        <h3>Summary</h3>
                        <div class="gauge-container">
                            <p style="text-align: center; color: #ef4444; padding: 40px;">
                                ${error.message}
                            </p>
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

        // Rest of your functions remain the same...
        // [Keep all other functions from the previous code: getTimeframeLabel, updateMarketStatus, renderTechnicalAnalysis, etc.]
        // Helper function to convert timeframe code to label
        function getTimeframeLabel(tf) {
            const tfMap = {
                '15': 'M15',
                '30': 'M30',
                '60': 'H1',
                '240': 'H4',
                'D': 'Daily',
                'W': 'Weekly'
            };
            return tfMap[tf] || tf;
        }
        // Initialize chart on page load
        document.addEventListener('DOMContentLoaded', () => {

            // Initialize gauge as neutral
            initializeGaugeAsNeutral();

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
                
                /* Add a subtle pulse animation to indicate analysis is updating */
                @keyframes pulse {
                    0% { opacity: 0.6; }
                    50% { opacity: 1; }
                    100% { opacity: 0.6; }
                }
                
                .analysis-updating {
                    animation: pulse 2s infinite;
                }
            `;

            document.head.appendChild(style);

            // Set initial timestamp to avoid immediate analysis update on page load
            lastAnalysisUpdateTime = Date.now() - MIN_ANALYSIS_INTERVAL;

            // Load initial chart with a delay for analysis
            setTimeout(() => {
                console.log("loadChart setTimeout DOMContentLoaded");
                loadChart();

                // Request initial data
                updateAnalysis('EURJPY', 'H4');

                // Add a visual indicator that analysis will update
                const chartTitle = document.getElementById('current-pair');
                if (chartTitle) {
                    chartTitle.innerHTML = 'EURJPY - 4H Chart <span style="font-size: 12px; color: #a0aec0;">(analysis loading...)</span>';
                }
            }, 1000); // 1 second delay before initial load

            // Add event listeners
            document.getElementById('pair').addEventListener('change', () => {
                // Check if we're rate limited for analysis
                const now = Date.now();
                if (now - lastAnalysisUpdateTime < MIN_ANALYSIS_INTERVAL) {
                    const remainingTime = Math.ceil((MIN_ANALYSIS_INTERVAL - (now - lastAnalysisUpdateTime)) / 1000);
                    showRateLimitMessage(remainingTime, 'analysis');
                    return;
                }
                loadChart();
            });

            document.getElementById('tf').addEventListener('change', () => {
                // Check if we're rate limited for analysis
                const now = Date.now();
                if (now - lastAnalysisUpdateTime < MIN_ANALYSIS_INTERVAL) {
                    const remainingTime = Math.ceil((MIN_ANALYSIS_INTERVAL - (now - lastAnalysisUpdateTime)) / 1000);
                    showRateLimitMessage(remainingTime, 'analysis');
                    return;
                }
                loadChart();
            });

            // Add click handler to refresh button
            document.querySelector('.refresh-btn').addEventListener('click', () => {
                // Check if we're rate limited for analysis
                const now = Date.now();
                if (now - lastAnalysisUpdateTime < MIN_ANALYSIS_INTERVAL) {
                    const remainingTime = Math.ceil((MIN_ANALYSIS_INTERVAL - (now - lastAnalysisUpdateTime)) / 1000);
                    showRateLimitMessage(remainingTime, 'analysis');
                    return;
                }
                loadChart();
            });

            // Update the rate limit message function to support different types
            function showRateLimitMessage(remainingTime, type = 'api') {
                const messageType = type === 'analysis' ? 'Analysis Update' : 'API Request';
                // ... rest of the showRateLimitMessage function remains the same
                // just update the message text
            }

            // Show time since last analysis update
            setInterval(() => {
                const timeSinceLastUpdate = Date.now() - lastAnalysisUpdateTime;
                const minutes = Math.floor(timeSinceLastUpdate / 60000);
                const seconds = Math.floor((timeSinceLastUpdate % 60000) / 1000);

                const timeDisplay = document.getElementById('time-since-update');
                if (timeDisplay) {
                    if (lastAnalysisUpdateTime === 0) {
                        timeDisplay.textContent = 'Analysis: Never updated';
                    } else if (minutes > 0) {
                        timeDisplay.textContent = `Analysis: ${minutes}m ${seconds}s ago`;
                    } else {
                        timeDisplay.textContent = `Analysis: ${seconds}s ago`;
                    }

                    // Color code based on freshness
                    if (timeSinceLastUpdate < 60000) { // Less than 1 minute
                        timeDisplay.style.color = '#10b981';
                    } else if (timeSinceLastUpdate < 300000) { // Less than 5 minutes
                        timeDisplay.style.color = '#f59e0b';
                    } else { // More than 5 minutes
                        timeDisplay.style.color = '#ef4444';
                    }
                }

                // Update refresh button state
                const refreshBtn = document.querySelector('.refresh-btn');
                if (refreshBtn) {
                    const timeToNextAnalysis = MIN_ANALYSIS_INTERVAL - timeSinceLastUpdate;
                    const canRefresh = timeToNextAnalysis <= 0;
                    refreshBtn.disabled = !canRefresh;
                    refreshBtn.style.opacity = canRefresh ? '1' : '0.5';
                    refreshBtn.style.cursor = canRefresh ? 'pointer' : 'not-allowed';

                    if (canRefresh) {
                        refreshBtn.title = 'Refresh analysis data';
                    } else {
                        const waitSeconds = Math.ceil(timeToNextAnalysis / 1000);
                        refreshBtn.title = `Analysis refresh available in ${waitSeconds}s`;
                    }
                }
            }, 1000);
        });

        // You can also add this function to show remaining time in the UI
        function getRemainingCooldown() {
            const now = Date.now();
            const timeSinceLastRequest = now - lastApiRequestTime;

            if (timeSinceLastRequest >= API_REQUEST_COOLDOWN) {
                return 0;
            }

            return Math.ceil((API_REQUEST_COOLDOWN - timeSinceLastRequest) / 1000);
        }
    </script>
</body>

</html>