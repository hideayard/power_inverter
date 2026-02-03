<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>TradingView Market Dashboard</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <script type="text/javascript" src="https://s3.tradingview.com/tv.js"></script>
    <link rel="stylesheet" href="../assets/css/combine.css">
    <!-- <link rel="stylesheet" href="../assets/css/latest_ma.css"> -->

    <!-- Load external JavaScript -->
    <script src="../assets/js/market.js" defer></script>

</head>

<body class="bg-dark text-light">
    <div class="container-fluid p-3 h-100">
        <div class="dashboard-header card bg-dark border-secondary mb-3">
            <div class="card-body py-3">
                <div class="row align-items-center">
                    <div class="col-lg-6 col-md-12 mb-3 mb-lg-0">
                        <h1 class="h4 mb-2">📈 Market Analysis Dashboard</h1>
                        <p class="text-muted mb-0 small">
                            Advanced real-time charts, market data, economic calendar, and news
                            in one unified view
                        </p>
                    </div>
                    <div class="col-lg-6 col-md-12">
                        <div class="row g-2">
                            <div class="col-md-4">
                                <label for="pair" class="form-label small text-muted mb-1">Currency Pair</label>
                                <select id="pair" class="form-select bg-dark text-light border-secondary form-select-sm">
                                    <option value="EURJPY" selected>EUR/JPY</option>
                                    <option value="USDJPY">USD/JPY</option>
                                    <option value="EURUSD">EUR/USD</option>
                                    <option value="GBPUSD">GBP/USD</option>
                                    <option value="XAUUSD">XAU/USD</option>
                                    <option value="XAGUSD">XAG/USD</option>
                                    <option value="BTCUSD">BTC/USD</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="tf" class="form-label small text-muted mb-1">Timeframe</label>
                                <select id="tf" class="form-select bg-dark text-light border-secondary form-select-sm">
                                    <option value="15">15min</option>
                                    <option value="30">30min</option>
                                    <option value="60">1H</option>
                                    <option value="240" selected>4H</option>
                                    <option value="D">Daily</option>
                                    <option value="W">Weekly</option>
                                </select>
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button class="btn btn-primary btn-sm w-100" onclick="loadChart()">
                                    <i class="fas fa-sync-alt me-1"></i>Load Chart
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="dashboard-layout row g-3 h-100">
            <!-- Left Sidebar - Financial News -->
            <!-- <aside class="sidebar sidebar-left col-lg-2 col-md-12 h-100">
                <section class="widget card bg-dark border-secondary h-100">
                    <div class="widget-header card-header border-secondary d-flex justify-content-between align-items-center">
                        <h2 class="h6 mb-0">📰 Financial News</h2>
                        <span class="widget-badge badge bg-success">Live</span>
                    </div>
                    <div class="widget-content card-body p-0">
                        <div class="tradingview-widget-container h-100">
                            <div class="tradingview-widget-container__widget h-100"></div>
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
                    </div>
                </section>
            </aside> -->

            <!-- Main Content Area - Scrollable -->
            <div class="main-content-scrollable col-lg-10 col-md-12 h-100">
                <div class="main-content-wrapper h-100 overflow-auto pe-2">
                    <div class="row p-2">
                        <!-- Chart Section -->
                        <div class="col-lg-8 col-md-12">
                            <section class="chart-section card bg-dark border-secondary mb-3 widget-height-equal">
                                <div class="chart-header card-header border-secondary d-flex justify-content-between align-items-center">
                                    <div class="chart-title">
                                        <h2 id="current-pair" class="h5 mb-1">EURJPY - 4H Chart</h2>
                                        <!-- <div class="chart-meta d-flex gap-2">
                                            <span class="badge bg-info">FXCM</span>
                                            <span class="badge bg-success">Spread: 0.8 pips</span>
                                        </div> -->
                                    </div>
                                    <div class="chart-controls d-flex align-items-center gap-2">
                                        <div class="status-indicator d-flex align-items-center">
                                            <span class="live-dot me-1"></span>
                                            <span class="d-none d-md-inline">Live Market Data</span>
                                            <span class="d-inline d-md-none">Live</span>
                                        </div>
                                        <button class="refresh-btn btn btn-outline-secondary btn-sm" onclick="loadChart()">
                                            <i class="fas fa-sync-alt"></i>
                                            <span class="d-none d-md-inline ms-1">Refresh</span>
                                        </button>
                                        <button class="fullscreen-btn btn btn-outline-secondary btn-sm" onclick="toggleFullscreen()">
                                            <i class="fas fa-expand"></i>
                                            <span class="d-none d-md-inline ms-1">Fullscreen</span>
                                        </button>
                                    </div>
                                </div>
                                <div class="chart-container card-body p-0" style="min-height: 400px;">
                                    <div id="chart" class="h-100 w-100"></div>
                                </div>
                            </section>
                        </div>

                        <!-- Lower Dashboard Grid -->
                        <div class="col-lg-4 col-md-12">
                            <div class="lower-dashboard row g-3">
                                <section class="widget card bg-dark border-secondary widget-height-equal">
                                    <div class="widget-header card-header border-secondary">
                                        <h2 class="h6 mb-0">📊 Market Data Watchlist</h2>
                                    </div>
                                    <div class="widget-content card-body p-0">
                                        <div class="watchlist-container" id="watchlist-all">
                                            <tv-market-summary
                                                symbol-sectors='[{"sectionName":"Currency","symbols":["OANDA:EURJPY","OANDA:EURUSD","OANDA:USDJPY","OANDA:GBPUSD","OANDA:GBPJPY"]},{"sectionName":"Crypto","symbols":["BINANCEUS:BTCUSDT","BINANCEUS:ETHUSDT","BINANCEUS:XRPUSDT","BINANCEUS:SOLUSDT","OKX:HYPEUSDT","BINANCE:BNBUSDT","CRYPTOCAP:TOTAL3","OKX:XAUTUSDT"]},{"sectionName":"Stocks","symbols":["SPREADEX:SPX","NASDAQ:TSLA","NASDAQ:NVDA","NASDAQ:GOOGL","FXOPEN:DXY","IDX:BBCA","IDX:COMPOSITE","IDX:ANTM","IDX:BBRI"]},{"sectionName":"Commodity","symbols":["CMCMARKETS:GOLD","CMCMARKETS:SILVER","TVC:USOIL"]}]'
                                                show-time-range layout-mode="grid" item-size="compact" mode="custom"></tv-market-summary>
                                        </div>
                                        <script type="module"
                                            src="https://widgets.tradingview-widget.com/w/en/tv-market-summary.js"></script>
                                    </div>
                                </section>

                            </div>
                        </div>
                    </div>

                    <div class="row p-2">
                        <div class="col-lg-12 col-md-12">

                            <section class="widget card bg-dark border-secondary mb-3">
                                <div class="widget-header card-header border-secondary d-flex justify-content-between align-items-center">
                                    <h2 class="h6 mb-0">📈 Technical Analysis and Pattern</h2>
                                    <div>
                                        <div class="timeframe-display badge bg-primary" id="current-tf">4H</div>
                                        <span class="timestamp badge bg-warning text-dark" id="fxbTimestamp">-</span>

                                    </div>
                                </div>
                                <div class="widget-content card-body">
                                    <!-- Summary with Gauge -->
                                    <div class="analysis-container row g-3 mb-3">
                                        <div class="col-lg-4 col-md-6">
                                            <div class="analysis-item p-3 bg-secondary rounded h-100">
                                                <h3 class="h6 mb-3">Technical Pattern</h3>
                                                <div class="wrapper">
                                                    <div id="ti-gauge" class="gauge mx-auto" style="--angle: 90deg;">
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
                                                    <div class="gauge-rating text-white text-center mt-3" id="ti-rating">Neutral</div>
                                                </div>
                                                <div class="summary-details mt-3">
                                                    <div class="indicator-row d-flex justify-content-between align-items-center p-2 bg-dark rounded">
                                                        <span>Signal Count:</span>
                                                        <span id="tiCounts" class="badge bg-secondary">Buy: 0 | Sell: 0</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-4 col-md-6">
                                            <div class="analysis-item p-3 bg-secondary rounded h-100">
                                                <h3 class="h6 mb-3">Summary</h3>
                                                <div class="wrapper">
                                                    <div id="summary-gauge" class="gauge mx-auto" style="--angle: 90deg;">
                                                        <div class="slice-colors">
                                                            <div class="st slice-item"></div>
                                                            <div class="st slice-item"></div>
                                                            <div class="st slice-item"></div>
                                                            <div class="st slice-item"></div>
                                                            <div class="st slice-item"></div>
                                                        </div>
                                                        <div analysis-item col-lg-6 col-md-6="summary-needle" class="needle"></div>
                                                        <div class="gauge-center"></div>
                                                    </div>
                                                    <div class="gauge-rating text-white text-center mt-3" id="summary-rating">Neutral</div>
                                                </div>
                                                <div class="summary-details mt-3">
                                                    <div class="indicator-row d-flex justify-content-between align-items-center p-2 bg-dark rounded">
                                                        <span>Overall Signal:</span>
                                                        <span id="overallCounts" class="badge bg-secondary">Buy: 0 | Sell: 0</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-4 col-md-6">
                                            <div class="analysis-item p-3 bg-secondary rounded h-100">
                                                <h3 class="h6 mb-3">Moving Averages</h3>
                                                <div class="wrapper">
                                                    <div id="ma-gauge" class="gauge mx-auto" style="--angle: 90deg;">
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
                                                    <div class="gauge-rating text-white text-center mt-3" id="ma-rating">Neutral</div>
                                                </div>
                                                <div class="summary-details mt-3">
                                                    <div class="indicator-row d-flex justify-content-between align-items-center p-2 bg-dark rounded">
                                                        <span>MA Signal Count:</span>
                                                        <span id="maCounts" class="badge bg-secondary">Buy: 0 | Sell: 0</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="analysis-container row g-3">
                                        <div class="col-lg-4 col-md-6 mb-3">
                                            <div class="analysis-item">
                                                <h3 class="h6 mb-3">Technical Analysis</h3>
                                                <div class="ta-container p-3 bg-secondary rounded h-100" id="taContainer">
                                                    <div class="signal d-flex justify-content-between mb-3">
                                                        <span class="label">Overall Signal:</span>
                                                        <span class="value fw-bold" id="overallSignal">-</span>
                                                    </div>
                                                    <div class="indicators">
                                                        <h3 class="h6 mb-2">Indicators (<span id="indicatorsCount">0</span>)</h3>
                                                        <div id="indicatorsList"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-4 col-md-6 mb-3">
                                            <div class="analysis-item">
                                                <h3 class="h6 mb-3">Moving Averages</h3>

                                                <div class="ta-container p-3 bg-secondary rounded h-100">
                                                    <!-- Moving Averages Table -->
                                                    <div class="table-header d-flex justify-content-between align-items-center mb-3 p-3 bg-secondary rounded">
                                                        <span>Summary: <strong class="rating-text" id="maSummary">Neutral</strong></span>
                                                        <span id="maSummaryCounts" class="badge bg-dark">Buy: 0 | Sell: 0</span>
                                                        <span class="timestamp badge bg-warning text-dark" id="maTimestamp">-</span>
                                                    </div>
                                                    <div class="table-container">
                                                        <table class="rates-table table table-dark table-hover" id="maTable">
                                                            <thead>
                                                                <tr>
                                                                    <th class="text-start">Name</th>
                                                                    <th class="text-center">Simple</th>
                                                                    <th class="text-center">Exponential</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="maTableBody">
                                                                <tr>
                                                                    <td colspan="3" class="loading-cell text-center py-4">Loading moving averages...</td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-4 col-md-6 mb-3">
                                            <!-- Technical Indicators Table -->
                                            <div class="analysis-item mb-3">
                                                <h3 class="h6 mb-3">Technical Indicators</h3>
                                                <div class="table-header d-flex justify-content-between align-items-center mb-3 p-3 bg-secondary rounded">
                                                    <span>Summary: <strong class="rating-text" id="tiSummary">Neutral</strong></span>
                                                    <span id="tiSummaryCounts" class="badge bg-dark">Buy: 0 | Sell: 0</span>
                                                    <span class="timestamp badge bg-warning text-dark" id="tiTimestamp">-</span>
                                                </div>
                                                <div class="table-container">
                                                    <table class="rates-table table table-dark table-hover" id="tiTable">
                                                        <thead>
                                                            <tr>
                                                                <th class="text-start">Name</th>
                                                                <th class="text-center">Value</th>
                                                                <th class="text-center">Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="tiTableBody">
                                                            <tr>
                                                                <td colspan="3" class="loading-cell text-center py-4">Loading technical indicators...</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="analysis-container row g-3">
                                        <div class="col-lg-12 col-md-12 mb-3">

                                            <!-- Pivot Points Table -->
                                            <div class="analysis-item">
                                                <h3 class="h6 mb-3">Pivot Points</h3>
                                                <div class="table-container">
                                                    <table class="rates-table table table-dark table-hover" id="pivotTable">
                                                        <thead>
                                                            <tr>
                                                                <th class="text-start">Name</th>
                                                                <th class="text-center">S3</th>
                                                                <th class="text-center">S2</th>
                                                                <th class="text-center">S1</th>
                                                                <th class="text-center">Pivot Points</th>
                                                                <th class="text-center">R1</th>
                                                                <th class="text-center">R2</th>
                                                                <th class="text-center">R3</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="pivotTableBody">
                                                            <tr>
                                                                <td colspan="8" class="loading-cell text-center py-4">Loading pivot points...</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </section>

                            <section class="widget card bg-dark border-secondary mb-3" id="interestRatesWidget">
                                <div class="widget-header card-header border-secondary">
                                    <div class="header-content">
                                        <h2 class="h6 mb-0"><i class="fas fa-percentage me-2"></i> EUR JPY Interest Rates Dashboard</h2>
                                    </div>
                                </div>

                                <div class="widget-content card-body" id="interestRateContainer">
                                    <!-- Summary Stats (will be populated dynamically) -->
                                    <div class="stats-grid row g-3 mb-3" id="summaryStats">
                                        <!-- Stats will be generated by JavaScript -->
                                    </div>

                                    <!-- Main Table -->
                                    <div class="table-container">
                                        <table class="rates-table table table-dark table-hover" id="ratesTable">
                                            <thead>
                                                <tr>
                                                    <th class="text-start">
                                                        <span>Country</span>
                                                        <i class="fas fa-sort ms-1"></i>
                                                    </th>
                                                    <th class="text-start">
                                                        <span>Central Bank</span>
                                                    </th>
                                                    <th class="text-end">
                                                        <span>Current Rate</span>
                                                        <i class="fas fa-sort ms-1"></i>
                                                    </th>
                                                    <th class="text-end">
                                                        <span>Previous</span>
                                                    </th>
                                                    <th class="text-center">
                                                        <span>Change</span>
                                                    </th>
                                                    <th class="text-center">
                                                        <span>Next Meeting</span>
                                                        <i class="fas fa-sort ms-1"></i>
                                                    </th>
                                                    <th class="text-center">
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
                                    <div class="widget-footer d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mt-3 pt-3 border-top">
                                        <div class="trend-legend d-flex flex-wrap gap-3">
                                            <div class="legend-item d-flex align-items-center gap-2">
                                                <div class="legend-dot hike"></div>
                                                <span>Rate Hike</span>
                                            </div>
                                            <div class="legend-item d-flex align-items-center gap-2">
                                                <div class="legend-dot cut"></div>
                                                <span>Rate Cut</span>
                                            </div>
                                            <div class="legend-item d-flex align-items-center gap-2">
                                                <div class="legend-dot hold"></div>
                                                <span>On Hold</span>
                                            </div>
                                        </div>
                                        <div class="footer-right d-flex align-items-center gap-3">
                                            <div class="data-info d-flex align-items-center gap-2">
                                                <i class="fas fa-database"></i>
                                                <span>Last scraped: <span id="scrapeTime">-</span></span>
                                            </div>
                                            <button class="export-btn btn btn-outline-primary btn-sm" onclick="exportData()">
                                                <i class="fas fa-download me-2"></i>
                                                Export Data
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </section>

                            <!-- Additional Analysis Section -->
                            <section class="widget card bg-dark border-secondary">
                                <div class="widget-header card-header border-secondary">
                                    <h2 class="h6 mb-0">🔍 Detailed Analysis</h2>
                                </div>
                                <div class="widget-content card-body">
                                    <div class="detailed-analysis row g-3" id="detailedAnalysis">
                                        <div class="analysis-card col-md-4">
                                            <h4 class="h6 mb-2">Price Action</h4>
                                            <p class="small mb-3">Currently trading above 50-day moving average. Bullish engulfing pattern detected on 4H timeframe.</p>
                                            <div class="analysis-tags d-flex flex-wrap gap-1">
                                                <span class="tag bullish badge bg-success">Bullish Pattern</span>
                                                <span class="tag neutral badge bg-secondary">Consolidation</span>
                                            </div>
                                        </div>
                                        <div class="analysis-card col-md-4">
                                            <h4 class="h6 mb-2">Volume Analysis</h4>
                                            <p class="small mb-3">Volume increasing on up moves, decreasing on down moves. Supports bullish bias.</p>
                                            <div class="analysis-tags d-flex flex-wrap gap-1">
                                                <span class="tag positive badge bg-success">Volume Confirmation</span>
                                            </div>
                                        </div>
                                        <div class="analysis-card col-md-4">
                                            <h4 class="h6 mb-2">Risk Levels</h4>
                                            <p class="small mb-3">Stop loss: 158.00, Take profit: 160.50. Risk/Reward ratio: 1:2.5</p>
                                            <div class="analysis-tags d-flex flex-wrap gap-1">
                                                <span class="tag good-rr badge bg-warning text-dark">Good R:R</span>
                                                <span class="tag medium-risk badge bg-info">Medium Risk</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </section>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Sidebar - Economic Calendar -->
            <aside class="sidebar sidebar-right col-lg-2 col-md-12 h-100">
                <section class="widget card bg-dark border-secondary h-100">
                    <div class="widget-header card-header border-secondary d-flex justify-content-between align-items-center">
                        <h2 class="h6 mb-0">📅 Economic Calendar</h2>
                        <span class="widget-badge today badge bg-primary">Today</span>
                    </div>
                    <div class="widget-content card-body p-0">
                        <iframe src="https://widget.myfxbook.com/widget/calendar.html?lang=en&impacts=0,1,2,3&symbols=AUD,CAD,CHF,CNY,EUR,GBP,IDR,JPY,NZD,USD" style="border: 0; width:100%; height:100%;"></iframe>
                        <div class="p-3">
                            <div class="text-center small text-muted">
                                <a href="https://www.myfxbook.com/forex-economic-calendar?utm_source=widget13&utm_medium=link&utm_campaign=copyright" title="Economic Calendar" class="myfxbookLink" target="_blank" rel="noopener"><b>Economic Calendar</b></a>
                                by Myfxbook.com
                            </div>
                        </div>
                    </div>
                </section>
            </aside>
        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>