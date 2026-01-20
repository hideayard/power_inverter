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
                                            <span class="support">158.50</span>
                                        </div>
                                        <div class="indicator-row">
                                            <span>Resistance:</span>
                                            <span class="resistance">160.20</span>
                                        </div>
                                        <div class="indicator-row">
                                            <span>Pivot:</span>
                                            <span class="pivot">159.35</span>
                                        </div>
                                        <div class="indicator-row">
                                            <span>Range:</span>
                                            <span>170 pips</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <section class="widget full-width">
                            <div class="widget-header">
                                <h2>📈 Market Analysis second Source</h2>
                                <div class="timeframe-display" id="current-tf">4H</div>
                            </div>
                            <div class="widget-content">
                                <div class="analysis-second-placeholder">
                                    <!-- Summary with Gauge -->
                                    <div class="analysis-item">
                                        <h3>Summary</h3>
                                        <div class="gauge-container">
                                            <div class="analyst-price-target_gaugeContainer__F_79r" style="width: 300px; height: 135px;" data-c="4">
                                                <div class="analyst-price-target_gauge__mc_8B" style="width: 204px; height: 102px;">
                                                    <div class="analyst-price-target_bar__nhotN" data-i="0"></div>
                                                    <div class="analyst-price-target_bar__nhotN" data-i="1"></div>
                                                    <div class="analyst-price-target_bar__nhotN" data-i="2"></div>
                                                    <div class="analyst-price-target_bar__nhotN" data-i="3"></div>
                                                    <div class="analyst-price-target_bar__nhotN" data-i="4"></div>
                                                    <div class="analyst-price-target_separator__682r6" style="height: 106px; left: 100px; width: 1px;" data-i="0"></div>
                                                    <div class="analyst-price-target_separator__682r6" style="height: 106px; left: 100px; width: 1px;" data-i="1"></div>
                                                    <div class="analyst-price-target_separator__682r6" style="height: 106px; left: 100px; width: 1px;" data-i="2"></div>
                                                    <div class="analyst-price-target_separator__682r6" style="height: 106px; left: 100px; width: 1px;" data-i="3"></div>
                                                    <div class="analyst-price-target_indicator__dhPLO" style="width: 198px; height: 198px; top: 3px; left: 3px;">
                                                        <div class="analyst-price-target_arrow__ZRmAZ" style="bottom: 101px; left: 98px; height: 60px;"></div>
                                                    </div>
                                                </div>
                                                <div class="analyst-price-target_gauge__mc_8B" style="position: absolute; opacity: 0.2; width: 204px; height: 102px;">
                                                    <div class="analyst-price-target_bar__nhotN" data-i="0"></div>
                                                    <div class="analyst-price-target_bar__nhotN" data-i="1"></div>
                                                    <div class="analyst-price-target_bar__nhotN" data-i="2"></div>
                                                    <div class="analyst-price-target_bar__nhotN" data-i="3"></div>
                                                    <div class="analyst-price-target_bar__nhotN" data-i="4"></div>
                                                    <div class="analyst-price-target_separator__682r6" style="height: 106px; left: 100px; width: 1px;" data-i="0"></div>
                                                    <div class="analyst-price-target_separator__682r6" style="height: 106px; left: 100px; width: 1px;" data-i="1"></div>
                                                    <div class="analyst-price-target_separator__682r6" style="height: 106px; left: 100px; width: 1px;" data-i="2"></div>
                                                    <div class="analyst-price-target_separator__682r6" style="height: 106px; left: 100px; width: 1px;" data-i="3"></div>
                                                    <div class="analyst-price-target_indicator__dhPLO" style="width: 174px; height: 174px; top: 15px; left: 15px;"></div>
                                                </div>
                                                <div class="analyst-price-target_circle__ykYoV" style="top: 113px; bottom: 19px;"></div>
                                                <div class="analyst-price-target_strongSell__OtkYw" style="bottom: 19px; left: 14px;">Strong Sell</div>
                                                <div class="analyst-price-target_sell__umjJy" style="left: 37px; top: 15px;">Sell</div>
                                                <div class="analyst-price-target_neutral__L8xm4" style="top: -11px;">Neutral</div>
                                                <div class="analyst-price-target_buy__5XS2x" style="right: 38px; top: 15px;">Buy</div>
                                                <div class="analyst-price-target_strongBuy__QaJ8j" style="bottom: 19px; right: 14px;">Strong Buy</div>
                                            </div>
                                            <div class="gauge-rating strong-buy">Strong Buy</div>
                                        </div>
                                        <div class="summary-details">
                                            <div class="indicator-row">
                                                <span>Moving Averages:</span>
                                                <span class="strong-buy">Strong Buy</span>
                                                <span>Buy: 12 | Sell: 0</span>
                                            </div>
                                            <div class="indicator-row">
                                                <span>Technical Indicators:</span>
                                                <span class="strong-buy">Strong Buy</span>
                                                <span>Buy: 11 | Sell: 0</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Technical Indicators Table -->
                                    <div class="analysis-item">
                                        <h3>Technical Indicators</h3>
                                        <div class="table-header">
                                            <span>Summary: <strong class="strong-buy">Strong Buy</strong></span>
                                            <span>Buy: 11 | Neutral: 0 | Sell: 0</span>
                                            <span class="timestamp">Jan 20, 2026 09:07AM GMT</span>
                                        </div>
                                        <div class="table-container">
                                            <table class="rates-table">
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
                                                <tbody>
                                                    <tr>
                                                        <td class="name-col">RSI(14)</td>
                                                        <td class="value-col">69.506</td>
                                                        <td class="action-col buy">Buy</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="name-col">STOCH(9,6)</td>
                                                        <td class="value-col">67.824</td>
                                                        <td class="action-col buy">Buy</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="name-col">STOCHRSI(14)</td>
                                                        <td class="value-col">64.032</td>
                                                        <td class="action-col buy">Buy</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="name-col">MACD(12,26)</td>
                                                        <td class="value-col">0.300</td>
                                                        <td class="action-col buy">Buy</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="name-col">ADX(14)</td>
                                                        <td class="value-col">59.889</td>
                                                        <td class="action-col buy">Buy</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="name-col">Williams %R</td>
                                                        <td class="value-col">-24.306</td>
                                                        <td class="action-col buy">Buy</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="name-col">CCI(14)</td>
                                                        <td class="value-col">118.2944</td>
                                                        <td class="action-col buy">Buy</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="name-col">ATR(14)</td>
                                                        <td class="value-col">0.235</td>
                                                        <td class="action-col neutral">High Volatility</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="name-col">Highs/Lows(14)</td>
                                                        <td class="value-col">0.4171</td>
                                                        <td class="action-col buy">Buy</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="name-col">Ultimate Oscillator</td>
                                                        <td class="value-col">68.996</td>
                                                        <td class="action-col buy">Buy</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="name-col">ROC</td>
                                                        <td class="value-col">0.435</td>
                                                        <td class="action-col buy">Buy</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="name-col">Bull/Bear Power(13)</td>
                                                        <td class="value-col">0.680</td>
                                                        <td class="action-col buy">Buy</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <!-- Moving Averages Table -->
                                    <div class="analysis-item">
                                        <h3>Moving Averages</h3>
                                        <div class="table-header">
                                            <span>Summary: <strong class="strong-buy">Strong Buy</strong></span>
                                            <span>Buy: 12 | Sell: 0</span>
                                            <span class="timestamp">Jan 20, 2026 09:07AM GMT</span>
                                        </div>
                                        <div class="table-container">
                                            <table class="rates-table">
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
                                                <tbody>
                                                    <tr>
                                                        <td class="name-col">MA5</td>
                                                        <td class="simple-col">
                                                            <div class="value-action">
                                                                <span>184.83</span>
                                                                <span class="action buy">Buy</span>
                                                            </div>
                                                        </td>
                                                        <td class="exponential-col">
                                                            <div class="value-action">
                                                                <span>184.77</span>
                                                                <span class="action buy">Buy</span>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="name-col">MA10</td>
                                                        <td class="simple-col">
                                                            <div class="value-action">
                                                                <span>184.44</span>
                                                                <span class="action buy">Buy</span>
                                                            </div>
                                                        </td>
                                                        <td class="exponential-col">
                                                            <div class="value-action">
                                                                <span>184.58</span>
                                                                <span class="action buy">Buy</span>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="name-col">MA20</td>
                                                        <td class="simple-col">
                                                            <div class="value-action">
                                                                <span>184.25</span>
                                                                <span class="action buy">Buy</span>
                                                            </div>
                                                        </td>
                                                        <td class="exponential-col">
                                                            <div class="value-action">
                                                                <span>184.30</span>
                                                                <span class="action buy">Buy</span>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="name-col">MA50</td>
                                                        <td class="simple-col">
                                                            <div class="value-action">
                                                                <span>183.79</span>
                                                                <span class="action buy">Buy</span>
                                                            </div>
                                                        </td>
                                                        <td class="exponential-col">
                                                            <div class="value-action">
                                                                <span>184.07</span>
                                                                <span class="action buy">Buy</span>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="name-col">MA100</td>
                                                        <td class="simple-col">
                                                            <div class="value-action">
                                                                <span>184.07</span>
                                                                <span class="action buy">Buy</span>
                                                            </div>
                                                        </td>
                                                        <td class="exponential-col">
                                                            <div class="value-action">
                                                                <span>184.02</span>
                                                                <span class="action buy">Buy</span>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="name-col">MA200</td>
                                                        <td class="simple-col">
                                                            <div class="value-action">
                                                                <span>184.10</span>
                                                                <span class="action buy">Buy</span>
                                                            </div>
                                                        </td>
                                                        <td class="exponential-col">
                                                            <div class="value-action">
                                                                <span>184.06</span>
                                                                <span class="action buy">Buy</span>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <!-- Pivot Points Table -->
                                    <div class="analysis-item">
                                        <h3>Pivot Points</h3>
                                        <div class="table-container">
                                            <table class="rates-table">
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
                                                <tbody>
                                                    <tr>
                                                        <td class="name-col">Classic</td>
                                                        <td class="value-col">184.45</td>
                                                        <td class="value-col">184.63</td>
                                                        <td class="value-col">184.77</td>
                                                        <td class="pivot-col">184.95</td>
                                                        <td class="value-col">185.09</td>
                                                        <td class="value-col">185.27</td>
                                                        <td class="value-col">185.41</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="name-col">Fibonacci</td>
                                                        <td class="value-col">184.63</td>
                                                        <td class="value-col">184.75</td>
                                                        <td class="value-col">184.83</td>
                                                        <td class="pivot-col">184.95</td>
                                                        <td class="value-col">185.07</td>
                                                        <td class="value-col">185.15</td>
                                                        <td class="value-col">185.27</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="name-col">Camarilla</td>
                                                        <td class="value-col">184.81</td>
                                                        <td class="value-col">184.84</td>
                                                        <td class="value-col">184.87</td>
                                                        <td class="pivot-col">184.95</td>
                                                        <td class="value-col">184.93</td>
                                                        <td class="value-col">184.96</td>
                                                        <td class="value-col">184.99</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="name-col">Woodie's</td>
                                                        <td class="value-col">184.41</td>
                                                        <td class="value-col">184.61</td>
                                                        <td class="value-col">184.73</td>
                                                        <td class="pivot-col">184.93</td>
                                                        <td class="value-col">185.05</td>
                                                        <td class="value-col">185.25</td>
                                                        <td class="value-col">185.37</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="name-col">DeMark's</td>
                                                        <td class="value-col">-</td>
                                                        <td class="value-col">-</td>
                                                        <td class="value-col">184.69</td>
                                                        <td class="pivot-col">184.91</td>
                                                        <td class="value-col">185.02</td>
                                                        <td class="value-col">-</td>
                                                        <td class="value-col">-</td>
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

                // Update analysis with dynamic data
                updateAnalysis(symbol, timeframeLabel);

                // Update header with market status
                updateMarketStatus(symbol);
            }, 100);
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

        function updateMarketStatus(symbol) {
            const marketOpen = document.querySelector('.market-open');
            const marketStatus = document.querySelector('.market-status span:last-child');

            // Simulate market hours check
            const now = new Date();
            const hour = now.getUTCHours();
            const isOpen = (hour >= 0 && hour < 21); // Forex market hours

            if (marketOpen) {
                marketOpen.style.backgroundColor = isOpen ? 'var(--accent-green)' : 'var(--accent-red)';
            }

            if (marketStatus) {
                marketStatus.textContent = isOpen ? 'Markets Open' : 'Markets Closed';
            }
        }

        function renderTechnicalAnalysis(data) {
            const container = document.getElementById("taContainer");
            if (!container) return;

            container.innerHTML = "";

            // Overall signal from technical summary
            const signalElement = document.getElementById("overallSignal");
            if (signalElement && data.technicalSummary) {
                signalElement.textContent = data.technicalSummary;
                signalElement.className = "value";
                signalElement.classList.add(data.technicalSummary.toLowerCase());
            }

            // Update indicators count
            const indicatorsCount = document.getElementById("indicatorsCount");
            if (indicatorsCount) {
                indicatorsCount.textContent = data.totalPatterns || 0;
            }

            // Create Technical Summary header
            const summaryHeader = document.createElement("div");
            summaryHeader.className = "technical-summary-header";
            summaryHeader.style.cssText = `
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    width: 100%;
                    font-size: 13px;
                    margin-bottom: 15px;
                    padding: 8px;
                    background: var(--signal-bg);
                    border-radius: 6px;
                `;

            const summaryText = document.createElement("div");
            summaryText.textContent = "Technical Summary:";
            summaryText.style.marginRight = "8px";

            const summaryValue = document.createElement("div");
            if (data.technicalSummary) {
                summaryValue.textContent = data.technicalSummary;
                summaryValue.style.fontWeight = "600";

                // Color the summary based on value
                if (data.technicalSummary.toLowerCase() == "buy") {
                    summaryValue.style.color = "var(--indicator-bullish-text)";
                } else if (data.technicalSummary.toLowerCase() == "sell") {
                    summaryValue.style.color = "var(--indicator-bearish-text)";
                } else if (data.technicalSummary.toLowerCase() == "neutral") {
                    summaryValue.style.color = "var(--indicator-neutral-text)";
                }
            } else {
                summaryValue.textContent = "-";
                summaryValue.style.color = "var(--text-color)";
            }

            summaryHeader.appendChild(summaryText);
            summaryHeader.appendChild(summaryValue);

            // Create table
            const table = document.createElement("table");
            table.className = "ta-table";
            table.style.cssText = "width: 100%; border-collapse: collapse;";

            // Table header
            const displayBuyCount =
                data.headerCounts?.buy > 0 ?
                data.headerCounts.buy :
                data.counts?.buy || 0;
            const displaySellCount =
                data.headerCounts?.sell > 0 ?
                data.headerCounts.sell :
                data.counts?.sell || 0;

            const thead = document.createElement("thead");
            thead.innerHTML = `
                    <tr style="background: var(--table-header-bg, #f3f4f6);">
                    <th style="text-align: left; padding: 10px; border-bottom: 2px solid var(--border-color); font-size: 13px; height: 38px;">Pattern</th>
                    <th style="text-align: center; padding: 10px; border-bottom: 2px solid var(--border-color); font-size: 13px; height: 38px;">Buy (${displayBuyCount})</th>
                    <th style="text-align: center; padding: 10px; border-bottom: 2px solid var(--border-color); font-size: 13px; height: 38px;">Sell (${displaySellCount})</th>
                    </tr>
                `;
            table.appendChild(thead);

            // Table body
            const tbody = document.createElement("tbody");

            data.patterns.forEach((pattern) => {
                const row = document.createElement("tr");
                row.style.height = "32px";

                // Pattern name cell
                const patternCell = document.createElement("td");
                patternCell.style.cssText =
                    "padding: 0 10px; min-width: 100px; height: 32px;";

                const patternLink = document.createElement("a");
                patternLink.href = "#";
                patternLink.textContent = pattern.name;
                patternLink.style.cssText =
                    "text-decoration: none; color: var(--text-color);";
                patternLink.addEventListener("click", (e) => e.preventDefault());

                patternCell.appendChild(patternLink);

                // Check if it's a neutral pattern (has timeframes property)
                if (pattern.signal == "neutral" && pattern.timeframes) {
                    // Neutral pattern spans both columns
                    const neutralCell = document.createElement("td");
                    neutralCell.colSpan = 2;
                    neutralCell.style.cssText =
                        "padding: 0; text-align: center; height: 32px;";

                    const neutralDiv = document.createElement("div");
                    neutralDiv.className = "bg-neutral";
                    neutralDiv.textContent = pattern.timeframes.toUpperCase();
                    neutralDiv.style.cssText = `
                        background: var(--indicator-neutral-bg);
                        color: var(--indicator-neutral-text);
                        height: 32px;
                        display: flex;
                        justify-content: center;
                        align-items: center;
                        text-transform: uppercase;
                        font-weight: 600;
                        font-size: 12px;
                        margin: 0 auto;
                        max-width: 43%;
                    `;

                    neutralCell.appendChild(neutralDiv);

                    row.appendChild(patternCell);
                    row.appendChild(neutralCell);
                } else {
                    // Regular pattern with buy/sell columns
                    // Buy cell
                    const buyCell = document.createElement("td");
                    buyCell.style.cssText = "padding: 0; text-align: center; height: 32px;";

                    if (pattern.buy) {
                        const buyDiv = document.createElement("div");
                        buyDiv.className = "bg-buy";
                        buyDiv.textContent = pattern.buy.toUpperCase();
                        buyDiv.style.cssText = `
                            background: var(--indicator-bullish-bg);
                            color: var(--indicator-bullish-text);
                            height: 32px;
                            display: flex;
                            justify-content: center;
                            align-items: center;
                            text-transform: uppercase;
                            font-weight: 600;
                            font-size: 12px;
                            `;
                        buyCell.appendChild(buyDiv);
                    }

                    // Sell cell
                    const sellCell = document.createElement("td");
                    sellCell.style.cssText =
                        "padding: 0; text-align: center; height: 32px;";

                    if (pattern.sell) {
                        const sellDiv = document.createElement("div");
                        sellDiv.className = "bg-sell";
                        sellDiv.textContent = pattern.sell.toUpperCase();
                        sellDiv.style.cssText = `
                            background: var(--indicator-bearish-bg);
                            color: var(--indicator-bearish-text);
                            height: 32px;
                            display: flex;
                            justify-content: center;
                            align-items: center;
                            text-transform: uppercase;
                            font-weight: 600;
                            font-size: 12px;
                            `;
                        sellCell.appendChild(sellDiv);
                    }

                    row.appendChild(patternCell);
                    row.appendChild(buyCell);
                    row.appendChild(sellCell);
                }

                tbody.appendChild(row);
            });

            table.appendChild(tbody);

            // Add table to container
            const tableContainer = document.createElement("div");
            tableContainer.style.cssText = "overflow-x: auto; margin-top: 10px;";
            tableContainer.appendChild(table);

            // Add legend
            const legend = document.createElement("div");
            legend.id = "technicalSummaryLegend";
            legend.style.cssText = `
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-top: 15px;
                padding: 10px 0;
                font-size: 12px;
            `;

            legend.innerHTML = `
                    <div style="padding-right: 10px; font-weight: 600;">Legend:</div>
                    <div style="display: flex; justify-content: space-around; width: 100%;">
                    <div style="display: flex; align-items: center;">
                        <div style="width: 12px; height: 12px; background: var(--indicator-bullish-bg); border-radius: 2px; margin-right: 6px;"></div>
                        <div>Buy</div>
                    </div>
                    <div style="display: flex; align-items: center;">
                        <div style="width: 12px; height: 12px; background: var(--indicator-bearish-bg); border-radius: 2px; margin-right: 6px;"></div>
                        <div>Sell</div>
                    </div>
                    <div style="display: flex; align-items: center;">
                        <div style="width: 12px; height: 12px; background: var(--indicator-neutral-bg); border-radius: 2px; margin-right: 6px;"></div>
                        <div>Neutral</div>
                    </div>
                    </div>
                `;

            // Create indicators list container
            const indicatorsList = document.getElementById("indicatorsList");
            if (indicatorsList) {
                indicatorsList.innerHTML = "";
                indicatorsList.appendChild(summaryHeader);
                indicatorsList.appendChild(tableContainer);
                indicatorsList.appendChild(legend);
            } else {
                container.appendChild(summaryHeader);
                container.appendChild(tableContainer);
                container.appendChild(legend);
            }

            return data;
        }

        async function fetchTechnicalAnalysis(pair, timeframe) {
            try {
                // Convert timeframe to match API format (e.g., "4H" to "H4")
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


                const formData = new FormData();
                formData.append("action", "get_scrape_data"); // Added action parameter
                formData.append("pair", pair);
                formData.append("timeframe", timeframe);

                const PROXY_ENDPOINT = "/proxy.php"; // Your proxy file

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
                console.log("Scrape Data response:", data);

                // Check if action matches
                if (data.action !== "get_scrape_data") {
                    console.warn("Unexpected action in response:", data.action);
                }

                if (!data.success) {
                    throw new Error(data.message || "Invalid username or password");
                }

                return data;
            } catch (error) {
                console.error('Error fetching technical analysis:', error);
                return null;
            }
        }

        async function updateAnalysis(symbol, timeframe) {
            try {
                // Fetch real data from API
                const apiResponse = await fetchTechnicalAnalysis(symbol, timeframe);

                if (!apiResponse || !apiResponse.success || !apiResponse.data?.latest_data?.request_data) {
                    throw new Error('Invalid API response');
                }

                // Parse the JSON string in request_data
                const scrapedData = JSON.parse(apiResponse.data.latest_data.request_data);
                const analysisData = scrapedData.data?.technicalAnalysis;
                const interestRateData = scrapedData.data?.interestRates;

                if (!analysisData) {
                    throw new Error('No technical analysis data found');
                }

                // Calculate sentiment percentages
                const buyCount = analysisData.counts?.buy || 0;
                const sellCount = analysisData.counts?.sell || 0;
                const neutralCount = analysisData.counts?.neutral || 0;
                const totalPatterns = analysisData.totalPatterns || 1;

                const bullishPercent = Math.round((buyCount / totalPatterns) * 100);
                const bearishPercent = Math.round((sellCount / totalPatterns) * 100);

                // Get timeframe for display
                const timeframeLabel = getTimeframeLabel(timeframe);

                renderTechnicalAnalysis(analysisData);

                // Update detailed analysis with patterns
                const detailedAnalysis = document.querySelector('.detailed-analysis');
                if (detailedAnalysis && analysisData.patterns) {
                    detailedAnalysis.innerHTML = `
                <div class="analysis-card">
                    <h4>Pattern Analysis</h4>
                    <p>${getPatternAnalysis(analysisData)}</p>
                    <div class="analysis-tags">
                        <span class="tag ${analysisData.technicalSummary.toLowerCase()}">
                            ${analysisData.technicalSummary}
                        </span>
                        ${buyCount > sellCount ? '<span class="tag bullish">Bullish Bias</span>' : 
                          buyCount < sellCount ? '<span class="tag bearish">Bearish Bias</span>' : 
                          '<span class="tag neutral">Balanced</span>'}
                    </div>
                </div>
                <div class="analysis-card">
                    <h4>Top Patterns Detected</h4>
                    <p>${analysisData.patterns.slice(0, 3).map(p => 
                        `${p.name} (${p.signal})${p.timeframes ? ` on ${p.timeframes}` : ''}`
                    ).join(', ')}</p>
                    <div class="analysis-tags">
                        ${analysisData.patterns.slice(0, 3).map(p => 
                            `<span class="tag ${p.signal}">${p.name}</span>`
                        ).join('')}
                    </div>
                </div>
                <div class="analysis-card">
                    <h4>Interest Rates</h4>
                    ${scrapedData.data?.interestRates?.length > 0 ? `
                        <p>${scrapedData.data.interestRates.map(rate => 
                            `${rate.country}: ${rate.currentRate} (Next: ${rate.nextMeeting})`
                        ).join('<br>')}</p>
                        <div class="analysis-tags">
                            ${scrapedData.data.interestRates.map(rate => 
                                `<span class="tag neutral">${rate.centralBank}</span>`
                            ).join('')}
                        </div>
                    ` : '<p>No interest rate data available</p>'}
                </div>
            `;
                }

                if (!interestRateData) {
                    throw new Error('No Interest Rate data found');
                }

                renderInterestRate(interestRateData);

                // Update economic calendar data if available
                updateEconomicCalendar(scrapedData.data?.economicCalendar);

            } catch (error) {
                console.error('Error updating analysis:', error);
                // Fallback to simulated data
                updateAnalysisWithFallback(symbol, timeframe);
            }
        }

        function renderInterestRate(interestRateData) {
            const container = document.getElementById("interestRateContainer");
            if (!container) {
                console.error("Container #interestRateContainer not found");
                return;
            }

            container.innerHTML = "";

            // Check if we have data
            if (!interestRateData || !Array.isArray(interestRateData) || interestRateData.length === 0) {
                container.innerHTML = `
            <div class="no-data-message" style="
                padding: 20px;
                text-align: center;
                color: var(--text-color);
                font-size: 14px;
                opacity: 0.7;
            ">
                No interest rate data available
            </div>
        `;
                return;
            }

            // Calculate summary statistics
            const stats = calculateInterestRateStats(interestRateData);

            // Create widget header
            const widgetHeader = document.createElement("div");
            widgetHeader.className = "interest-rate-header";
            widgetHeader.style.cssText = `
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 20px;
        background: var(--bg-secondary);
        border-bottom: 1px solid var(--border-color);
    `;

            const headerLeft = document.createElement("div");
            headerLeft.style.cssText = `
        display: flex;
        align-items: center;
        gap: 10px;
    `;

            const titleIcon = document.createElement("i");
            titleIcon.className = "fas fa-percentage";
            titleIcon.style.cssText = `
        color: var(--accent-blue);
        font-size: 18px;
    `;

            const title = document.createElement("h3");
            title.textContent = "Central Bank Rates";
            title.style.cssText = `
        margin: 0;
        font-size: 16px;
        font-weight: 600;
        color: var(--text-primary);
    `;

            headerLeft.appendChild(titleIcon);
            headerLeft.appendChild(title);

            const headerRight = document.createElement("div");
            headerRight.style.cssText = `
        display: flex;
        align-items: center;
        gap: 15px;
        font-size: 12px;
        color: var(--text-secondary);
    `;

            const highestRate = document.createElement("div");
            highestRate.innerHTML = `<strong style="color: var(--accent-red);">${stats.highestRate}</strong> Highest`;

            const avgRate = document.createElement("div");
            avgRate.innerHTML = `<strong style="color: var(--text-primary);">${stats.avgRate}</strong> Avg`;

            headerRight.appendChild(highestRate);
            headerRight.appendChild(avgRate);

            widgetHeader.appendChild(headerLeft);
            widgetHeader.appendChild(headerRight);

            // Create table
            const table = document.createElement("table");
            table.className = "interest-rate-table";
            table.style.cssText = `
        width: 100%;
        border-collapse: collapse;
        background: var(--bg-primary);
    `;

            // Table header
            const thead = document.createElement("thead");
            thead.innerHTML = `
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
    `;
            table.appendChild(thead);

            // Table body
            const tbody = document.createElement("tbody");

            // Sort by current rate (highest first)
            const sortedData = [...interestRateData].sort((a, b) => {
                const rateA = parseFloat(a.currentRate.replace('%', ''));
                const rateB = parseFloat(b.currentRate.replace('%', ''));
                return rateB - rateA;
            });

            sortedData.forEach((rate, index) => {
                const row = document.createElement("tr");
                row.style.cssText = `
            border-bottom: 1px solid var(--border-color);
            transition: background-color 0.2s;
        `;

                row.addEventListener('mouseenter', () => {
                    row.style.backgroundColor = 'var(--bg-hover)';
                });

                row.addEventListener('mouseleave', () => {
                    row.style.backgroundColor = 'transparent';
                });

                // Calculate change and trend
                const current = parseFloat(rate.currentRate.replace('%', ''));
                const previous = parseFloat(rate.previousRate.replace('%', ''));
                const change = current - previous;
                const trend = change > 0 ? 'hike' : change < 0 ? 'cut' : 'hold';
                const changeText = change > 0 ? `+${change.toFixed(2)}%` : change < 0 ? `${change.toFixed(2)}%` : '0.00%';

                // Get country info
                const countryInfo = getCountryInfo(rate.country);
                const days = parseInt(rate.nextMeeting.replace(' days', ''));

                // Determine meeting urgency
                const meetingClass = days <= 7 ? 'urgent-meeting' : days <= 30 ? 'upcoming-meeting' : 'normal-meeting';

                row.innerHTML = `
            <td style="
                padding: 15px 20px;
                text-align: left;
                font-weight: 500;
                color: var(--text-primary);
            ">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <div class="country-flag ${countryInfo.flag}" style="
                        width: 24px;
                        height: 24px;
                        border-radius: 50%;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        font-size: 12px;
                        font-weight: bold;
                        background: ${countryInfo.flag === 'euro' ? 'linear-gradient(135deg, #003399 0%, #ffcc00 100%)' :
                                      countryInfo.flag === 'japan' ? 'linear-gradient(135deg, #bc002d 0%, #ffffff 100%)' :
                                      countryInfo.flag === 'us' ? 'linear-gradient(135deg, #3c3b6e 0%, #b22234 50%, #ffffff 50%)' :
                                      countryInfo.flag === 'uk' ? 'linear-gradient(135deg, #012169 0%, #c8102e 33%, #ffffff 33%)' :
                                      'linear-gradient(135deg, #ff0000 0%, #ffffff 50%)'};
                        color: white;
                    ">
                        <i class="${countryInfo.symbol}"></i>
                    </div>
                    <div>
                        <div style="font-size: 14px; font-weight: 500;">${rate.country}</div>
                        <div style="font-size: 11px; color: var(--text-secondary); margin-top: 2px;">${countryInfo.code}</div>
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
                    ${rate.centralBank.split(' ').map(word => word[0]).join('')}
                </div>
            </td>
            <td style="
                padding: 15px 20px;
                text-align: right;
                font-size: 16px;
                font-weight: 700;
                color: var(${trend === 'hike' ? '--accent-red' : trend === 'cut' ? '--accent-green' : '--text-primary'});
            ">
                ${rate.currentRate}
            </td>
            <td style="
                padding: 15px 20px;
                text-align: right;
                font-size: 14px;
                color: var(--text-secondary);
            ">
                ${rate.previousRate}
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
                    background: ${trend === 'hike' ? 'rgba(239, 68, 68, 0.1)' : 
                                 trend === 'cut' ? 'rgba(16, 185, 129, 0.1)' : 
                                 'rgba(100, 116, 139, 0.1)'};
                    color: ${trend === 'hike' ? 'var(--accent-red)' : 
                            trend === 'cut' ? 'var(--accent-green)' : 
                            'var(--text-muted)'};
                    border: 1px solid ${trend === 'hike' ? 'rgba(239, 68, 68, 0.2)' : 
                                      trend === 'cut' ? 'rgba(16, 185, 129, 0.2)' : 
                                      'rgba(100, 116, 139, 0.2)'};
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
                    background: ${meetingClass === 'urgent-meeting' ? 'rgba(239, 68, 68, 0.1)' : 
                                 meetingClass === 'upcoming-meeting' ? 'rgba(245, 158, 11, 0.1)' : 
                                 'rgba(59, 130, 246, 0.1)'};
                    color: ${meetingClass === 'urgent-meeting' ? 'var(--accent-red)' : 
                            meetingClass === 'upcoming-meeting' ? 'var(--accent-yellow)' : 
                            'var(--accent-blue)'};
                    border: 1px solid ${meetingClass === 'urgent-meeting' ? 'rgba(239, 68, 68, 0.2)' : 
                                      meetingClass === 'upcoming-meeting' ? 'rgba(245, 158, 11, 0.2)' : 
                                      'rgba(59, 130, 246, 0.2)'};
                ">
                    ${rate.nextMeeting}
                </div>
            </td>
        `;

                tbody.appendChild(row);
            });

            table.appendChild(tbody);

            // Create table container with responsive design
            const tableContainer = document.createElement("div");
            tableContainer.style.cssText = `
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    `;
            tableContainer.appendChild(table);

            // Add legend
            const legend = document.createElement("div");
            legend.style.cssText = `
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 20px;
        background: var(--bg-secondary);
        border-top: 1px solid var(--border-color);
        font-size: 11px;
        color: var(--text-secondary);
    `;

            legend.innerHTML = `
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
    `;

            // Assemble the widget
            container.appendChild(widgetHeader);
            container.appendChild(tableContainer);
            container.appendChild(legend);

            // Return data for chaining
            return interestRateData;
        }

        // Helper function to calculate interest rate statistics
        function calculateInterestRateStats(interestRateData) {
            if (!interestRateData || interestRateData.length === 0) {
                return {
                    highestRate: '0%',
                    lowestRate: '0%',
                    avgRate: '0%',
                    hikeCount: 0,
                    cutCount: 0,
                    holdCount: 0
                };
            }

            let highestRate = 0;
            let lowestRate = Infinity;
            let totalRate = 0;
            let hikeCount = 0;
            let cutCount = 0;
            let holdCount = 0;

            interestRateData.forEach(rate => {
                const current = parseFloat(rate.currentRate.replace('%', ''));
                const previous = parseFloat(rate.previousRate.replace('%', ''));

                if (current > highestRate) highestRate = current;
                if (current < lowestRate) lowestRate = current;
                totalRate += current;

                const change = current - previous;
                if (change > 0) hikeCount++;
                else if (change < 0) cutCount++;
                else holdCount++;
            });

            return {
                highestRate: `${highestRate.toFixed(2)}%`,
                lowestRate: `${lowestRate.toFixed(2)}%`,
                avgRate: `${(totalRate / interestRateData.length).toFixed(2)}%`,
                hikeCount,
                cutCount,
                holdCount
            };
        }

        // Helper function to get country info
        function getCountryInfo(country) {
            const countryMap = {
                'Euro Area': {
                    flag: 'euro',
                    code: 'EUR',
                    symbol: 'fas fa-euro-sign'
                },
                'Japan': {
                    flag: 'japan',
                    code: 'JPY',
                    symbol: 'fas fa-yen-sign'
                },
                'United States': {
                    flag: 'us',
                    code: 'USD',
                    symbol: 'fas fa-dollar-sign'
                },
                'USA': {
                    flag: 'us',
                    code: 'USD',
                    symbol: 'fas fa-dollar-sign'
                },
                'United Kingdom': {
                    flag: 'uk',
                    code: 'GBP',
                    symbol: 'fas fa-pound-sign'
                },
                'UK': {
                    flag: 'uk',
                    code: 'GBP',
                    symbol: 'fas fa-pound-sign'
                },
                'Switzerland': {
                    flag: 'swiss',
                    code: 'CHF',
                    symbol: 'fas fa-franc-sign'
                },
                'Canada': {
                    flag: 'canada',
                    code: 'CAD',
                    symbol: 'fas fa-dollar-sign'
                },
                'Australia': {
                    flag: 'australia',
                    code: 'AUD',
                    symbol: 'fas fa-dollar-sign'
                },
                'New Zealand': {
                    flag: 'nz',
                    code: 'NZD',
                    symbol: 'fas fa-dollar-sign'
                }
            };

            return countryMap[country] || {
                flag: 'default',
                code: country.substring(0, 3).toUpperCase(),
                symbol: 'fas fa-flag'
            };
        }

        // Helper function for pattern analysis
        function getPatternAnalysis(analysisData) {
            const buyCount = analysisData.counts?.buy || 0;
            const sellCount = analysisData.counts?.sell || 0;
            const totalPatterns = analysisData.totalPatterns || 1;

            if (buyCount > sellCount * 1.5) {
                return "Strong bullish signals with multiple buy patterns detected. Consider long positions with proper risk management.";
            } else if (sellCount > buyCount * 1.5) {
                return "Strong bearish signals with multiple sell patterns detected. Consider short positions or taking profits.";
            } else if (buyCount > sellCount) {
                return "Slight bullish bias with more buy signals than sell signals. Look for entry opportunities on pullbacks.";
            } else if (sellCount > buyCount) {
                return "Slight bearish bias with more sell signals than buy signals. Exercise caution on long positions.";
            } else {
                return "Neutral market conditions with balanced buy/sell signals. Wait for clearer directional signals.";
            }
        }

        // Function to update economic calendar
        function updateEconomicCalendar(calendarData) {
            if (!calendarData || !calendarData.events || calendarData.events.length === 0) {
                return;
            }

            // Update the economic calendar widget or add to analysis
            const calendarWidget = document.querySelector('.sidebar-right .widget-content');
            if (calendarWidget) {
                // You can update the existing TradingView widget or add custom display
                // For now, let's add a summary to the analysis
                console.log('Economic calendar data available:', calendarData.events.length, 'events');
            }
        }

        // Fallback function for simulated data
        function updateAnalysisWithFallback(symbol, timeframe) {
            const analysisData = {
                technicalSummary: "Neutral",
                counts: {
                    buy: Math.floor(Math.random() * 5) + 1,
                    sell: Math.floor(Math.random() * 5) + 1,
                    neutral: Math.floor(Math.random() * 3) + 1
                },
                totalPatterns: 10,
                patterns: [{
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
            };

            // Reuse the main update logic with fallback data
            const timeframeLabel = getTimeframeLabel(timeframe);
            const buyCount = analysisData.counts.buy;
            const sellCount = analysisData.counts.sell;
            const neutralCount = analysisData.counts.neutral;
            const totalPatterns = analysisData.totalPatterns;

            const bullishPercent = Math.round((buyCount / totalPatterns) * 100);
            const bearishPercent = Math.round((sellCount / totalPatterns) * 100);

            const analysisContainer = document.querySelector('.analysis-placeholder');
            if (analysisContainer) {
                analysisContainer.innerHTML = `
            <div class="analysis-item">
                <h3>Technical Patterns (${timeframeLabel}) - Simulated</h3>
                <div class="indicator-row">
                    <span>Summary:</span>
                    <span class="neutral">${analysisData.technicalSummary}</span>
                </div>
                <div class="indicator-row">
                    <span>Buy Signals:</span>
                    <span class="bullish">${buyCount} patterns</span>
                </div>
                <div class="indicator-row">
                    <span>Sell Signals:</span>
                    <span class="bearish">${sellCount} patterns</span>
                </div>
                <div class="indicator-row">
                    <span>Neutral:</span>
                    <span class="neutral">${neutralCount} patterns</span>
                </div>
            </div>

        `;
            }
        }

        // Initialize chart on page load
        document.addEventListener('DOMContentLoaded', () => {
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
    `;
            document.head.appendChild(style);

            // Load initial chart
            loadChart();

            // Update analysis with real data from API
            updateAnalysis(symbol, tf);

            // Update header with market status
            updateMarketStatus(symbol);

            // Add event listeners
            document.getElementById('pair').addEventListener('change', loadChart);
            document.getElementById('tf').addEventListener('change', loadChart);

            // Add click handler to refresh button
            document.querySelector('.refresh-btn').addEventListener('click', loadChart);

            // Auto-refresh every 2 minutes
            setInterval(() => {
                if (document.visibilityState === 'visible' && !isChartLoading) {
                    loadChart();
                }
            }, 120000);

            // Update market status every minute
            setInterval(() => {
                const symbol = document.getElementById("pair").value;
                updateMarketStatus(symbol);
            }, 60000);
        });

        // Handle window resize
        window.addEventListener('resize', () => {
            // TradingView charts handle their own resizing
            if (currentChart && typeof currentChart.onResize === 'function') {
                setTimeout(() => currentChart.onResize(), 100);
            }
        });
    </script>
</body>

</html>