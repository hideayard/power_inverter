<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>TradingView Market Dashboard</title>
    <!-- Load TradingView script FIRST -->
    <script type="text/javascript" src="https://s3.tradingview.com/tv.js"></script>
    <link rel="stylesheet" href="../assets/css/ma.css">
    <style>
        /* Add loading styles here in head */
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
        
        /* Error state */
        .chart-error {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            color: var(--accent-red);
            text-align: center;
            padding: 20px;
        }
        
        .chart-error button {
            margin-top: 15px;
            padding: 8px 16px;
            background: var(--accent-blue);
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
    </style>
</head>

<body>
    <!-- Your HTML structure remains the same -->
    <div class="container">
        <!-- ... rest of your HTML ... -->
        
        <!-- Chart Section -->
        <section class="chart-section">
            <div class="chart-header">
                <!-- ... header content ... -->
            </div>
            <div class="chart-container">
                <div id="chart">
                    <!-- Initial loading state -->
                    <div class="chart-loading">
                        <div class="loading-spinner"></div>
                        <p>Loading TradingView...</p>
                    </div>
                </div>
            </div>
        </section>
        
        <!-- ... rest of your HTML ... -->
    </div>

    <script>
        // Global variables
        let currentChart = null;
        let isChartLoading = false;
        let tradingViewReady = false;

        // Wait for TradingView script to load
        function waitForTradingView() {
            return new Promise((resolve, reject) => {
                // Check if TradingView is already loaded
                if (window.TradingView) {
                    tradingViewReady = true;
                    resolve();
                    return;
                }
                
                // Set up a check interval
                let checkCount = 0;
                const maxChecks = 50; // 10 seconds max
                const checkInterval = setInterval(() => {
                    checkCount++;
                    if (window.TradingView) {
                        clearInterval(checkInterval);
                        tradingViewReady = true;
                        resolve();
                    } else if (checkCount >= maxChecks) {
                        clearInterval(checkInterval);
                        reject(new Error('TradingView failed to load'));
                    }
                }, 200);
            });
        }

        // Modified loadChart function with better error handling
        function loadChart() {
            if (isChartLoading) return;
            
            const chartContainer = document.getElementById("chart");
            if (!chartContainer) {
                console.error('Chart container not found');
                return;
            }
            
            const symbol = document.getElementById("pair").value;
            const tf = document.getElementById("tf").value;
            const timeframeLabel = getTimeframeLabel(tf);

            // Update display
            document.getElementById("current-pair").textContent = `${symbol} - ${timeframeLabel} Chart`;
            if (document.getElementById("current-tf")) {
                document.getElementById("current-tf").textContent = timeframeLabel;
            }

            // Show loading state
            chartContainer.innerHTML = `
                <div class="chart-loading">
                    <div class="loading-spinner"></div>
                    <p>Loading ${symbol} chart...</p>
                </div>
            `;

            isChartLoading = true;

            // Wait for TradingView to be ready
            waitForTradingView()
                .then(() => {
                    // Clear previous chart
                    if (currentChart && typeof currentChart.remove === 'function') {
                        try {
                            currentChart.remove();
                        } catch (e) {
                            console.warn('Error removing previous chart:', e);
                        }
                    }

                    // Clear container
                    chartContainer.innerHTML = '';

                    // Create new chart
                    try {
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
                        console.log('Chart loaded successfully');
                        
                        // Update market status
                        updateMarketStatus(symbol);
                        
                    } catch (error) {
                        console.error('Error creating TradingView widget:', error);
                        showChartError(chartContainer, symbol);
                        isChartLoading = false;
                    }
                })
                .catch((error) => {
                    console.error('TradingView not loaded:', error);
                    showChartError(chartContainer, symbol);
                    isChartLoading = false;
                });
        }

        function showChartError(container, symbol) {
            container.innerHTML = `
                <div class="chart-error">
                    <h3>⚠️ Chart Failed to Load</h3>
                    <p>Unable to load ${symbol} chart. This could be due to:</p>
                    <ul style="text-align: left; margin: 10px 0;">
                        <li>Network connectivity issues</li>
                        <li>Ad blocker blocking TradingView</li>
                        <li>TradingView service temporarily unavailable</li>
                    </ul>
                    <button onclick="retryLoadChart()">Retry</button>
                </div>
            `;
        }

        function retryLoadChart() {
            const container = document.getElementById("chart");
            container.innerHTML = `
                <div class="chart-loading">
                    <div class="loading-spinner"></div>
                    <p>Retrying...</p>
                </div>
            `;
            setTimeout(loadChart, 1000);
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
            // Your existing updateMarketStatus function
            const now = new Date();
            const hour = now.getUTCHours();
            const isOpen = (hour >= 0 && hour < 21);
            
            const statusIndicators = document.querySelectorAll('.status-indicator');
            statusIndicators.forEach(indicator => {
                const dot = indicator.querySelector('.live-dot');
                const text = indicator.querySelector('span:last-child') || indicator;
                
                if (dot) {
                    dot.style.backgroundColor = isOpen ? 'var(--accent-green)' : 'var(--accent-red)';
                }
                
                if (text && text.textContent) {
                    if (text.textContent.includes('Live Market Data') || text.textContent.includes('Market')) {
                        text.textContent = isOpen ? 'Live Market Data' : 'Markets Closed';
                    }
                }
            });
        }

        function toggleFullscreen() {
            const chartContainer = document.querySelector('.chart-container');
            if (!document.fullscreenElement) {
                if (chartContainer.requestFullscreen) {
                    chartContainer.requestFullscreen();
                } else if (chartContainer.webkitRequestFullscreen) {
                    chartContainer.webkitRequestFullscreen();
                } else if (chartContainer.msRequestFullscreen) {
                    chartContainer.msRequestFullscreen();
                }
            } else {
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                } else if (document.webkitExitFullscreen) {
                    document.webkitExitFullscreen();
                } else if (document.msExitFullscreen) {
                    document.msExitFullscreen();
                }
            }
        }

        // Initialize on DOM ready
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM loaded, initializing dashboard...');
            
            // Set up event listeners
            document.getElementById('pair').addEventListener('change', loadChart);
            document.getElementById('tf').addEventListener('change', loadChart);
            
            const refreshBtn = document.querySelector('.refresh-btn');
            if (refreshBtn) {
                refreshBtn.addEventListener('click', loadChart);
            }
            
            const fullscreenBtn = document.querySelector('.fullscreen-btn');
            if (fullscreenBtn) {
                fullscreenBtn.addEventListener('click', toggleFullscreen);
            }
            
            // Start loading chart after a short delay to ensure DOM is ready
            setTimeout(() => {
                loadChart();
                
                // Try to load analysis data if needed
                const symbol = document.getElementById("pair").value;
                const tf = document.getElementById("tf").value;
                // updateAnalysis(symbol, tf); // Uncomment if you want to load analysis
            }, 500);
        });

        // Handle window resize
        window.addEventListener('resize', function() {
            if (currentChart && typeof currentChart.onResize === 'function') {
                setTimeout(() => {
                    try {
                        currentChart.onResize();
                    } catch (e) {
                        console.warn('Error resizing chart:', e);
                    }
                }, 100);
            }
        });

        // Handle fullscreen change
        document.addEventListener('fullscreenchange', function() {
            if (currentChart && typeof currentChart.onResize === 'function') {
                setTimeout(() => currentChart.onResize(), 300);
            }
        });

        // Gauge needle function (your existing)
        function setNeedleAngle(angle) {
            const gauge = document.getElementById('gauge');
            if (gauge) {
                gauge.style.setProperty('--angle', angle + 'deg');
            }
        }

        // Set initial gauge angle
        document.addEventListener('DOMContentLoaded', function() {
            setNeedleAngle(170); // Strong Buy
        });
    </script>
</body>

</html>