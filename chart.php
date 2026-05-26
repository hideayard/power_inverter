<!DOCTYPE html>
<html>
<head>
    <title>Market Data Chart - {{ symbol }}</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-chart-financial@0.2.0/dist/chartjs-chart-financial.umd.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background: #1a1a2e;
            color: #eee;
        }
        .container {
            max-width: 1400px;
            margin: 0 auto;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .controls {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        select, button {
            padding: 8px 15px;
            background: #16213e;
            color: #eee;
            border: 1px solid #0f3460;
            border-radius: 5px;
            cursor: pointer;
        }
        button {
            background: #0f3460;
        }
        button:hover {
            background: #1a4080;
        }
        .chart-container {
            background: #16213e;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .volume-chart {
            height: 200px;
        }
        .main-chart {
            height: 600px;
        }
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        .stat-card {
            background: #16213e;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
        }
        .stat-value {
            font-size: 24px;
            font-weight: bold;
            color: #00d4aa;
        }
        .stat-label {
            font-size: 14px;
            color: #888;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ symbol }} Chart</h1>
            <div class="controls">
                <label for="timeframe">Timeframe:</label>
                <select id="timeframe" onchange="loadData()">
                    <option value="M1">M1</option>
                    <option value="M5">M5</option>
                    <option value="M15">M15</option>
                    <option value="M30">M30</option>
                    <option value="H1" selected>H1</option>
                    <option value="H4">H4</option>
                    <option value="D1">D1</option>
                    <option value="W1">W1</option>
                    <option value="MN1">MN1</option>
                </select>
                <label for="limit">Bars:</label>
                <select id="limit" onchange="loadData()">
                    <option value="100">100</option>
                    <option value="200">200</option>
                    <option value="500">500</option>
                    <option value="1000" selected>1000</option>
                    <option value="2000">2000</option>
                </select>
                <button onclick="loadData()">Refresh</button>
                <button onclick="toggleChartType()">Switch View</button>
            </div>
        </div>

        <div class="stats" id="stats">
            <div class="stat-card">
                <div class="stat-value" id="currentPrice">-</div>
                <div class="stat-label">Current Price</div>
            </div>
            <div class="stat-card">
                <div class="stat-value" id="dayHigh">-</div>
                <div class="stat-label">Period High</div>
            </div>
            <div class="stat-card">
                <div class="stat-value" id="dayLow">-</div>
                <div class="stat-label">Period Low</div>
            </div>
            <div class="stat-card">
                <div class="stat-value" id="avgVolume">-</div>
                <div class="stat-label">Avg Volume</div>
            </div>
        </div>

        <div class="chart-container">
            <canvas id="mainChart" class="main-chart"></canvas>
        </div>
        
        <div class="chart-container">
            <canvas id="volumeChart" class="volume-chart"></canvas>
        </div>
    </div>

    <script>
        let mainChart = null;
        let volumeChart = null;
        let currentType = 'candle';

        // Register financial chart type
        Chart.register(Chart.Financial);

        async function loadData() {
            const symbol = '{{ symbol }}';
            const timeframe = document.getElementById('timeframe').value;
            const limit = document.getElementById('limit').value;

            try {
                const response = await fetch(`/api/market-data/${symbol}?timeframe=${timeframe}&limit=${limit}`);
                const result = await response.json();
                
                if (result.data && result.data.length > 0) {
                    updateCharts(result.data, symbol, timeframe);
                    updateStats(result.data);
                } else {
                    alert('No data available');
                }
            } catch (error) {
                console.error('Error loading data:', error);
                alert('Error loading data');
            }
        }

        function updateCharts(data, symbol, timeframe) {
            // Prepare data for candlestick chart
            const candleData = data.map(d => ({
                x: new Date(d.time || d.x),
                o: d.open || d.o,
                h: d.high || d.h,
                l: d.low || d.l,
                c: d.close || d.c
            }));

            // Destroy existing charts
            if (mainChart) mainChart.destroy();
            if (volumeChart) volumeChart.destroy();

            const ctx = document.getElementById('mainChart').getContext('2d');
            
            if (currentType === 'candle') {
                mainChart = new Chart(ctx, {
                    type: 'candlestick',
                    data: {
                        datasets: [{
                            label: `${symbol} ${timeframe}`,
                            data: candleData,
                            color: {
                                up: '#00d4aa',
                                down: '#ff4757',
                                unchanged: '#999'
                            }
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: (ctx) => {
                                        const d = ctx.raw;
                                        return [
                                            `Open: ${d.o.toFixed(5)}`,
                                            `High: ${d.h.toFixed(5)}`,
                                            `Low: ${d.l.toFixed(5)}`,
                                            `Close: ${d.c.toFixed(5)}`
                                        ];
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                type: 'time',
                                time: { unit: 'hour' },
                                grid: { color: '#1a1a3e' }
                            },
                            y: {
                                grid: { color: '#1a1a3e' }
                            }
                        }
                    }
                });
            } else {
                // Line chart
                const lineData = data.map(d => d.close || d.c);
                const labels = data.map(d => new Date(d.time || d.x));
                
                mainChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: `${symbol} ${timeframe}`,
                            data: lineData,
                            borderColor: '#00d4aa',
                            backgroundColor: 'rgba(0, 212, 170, 0.1)',
                            fill: true,
                            tension: 0.1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            x: {
                                type: 'time',
                                time: { unit: 'hour' },
                                grid: { color: '#1a1a3e' }
                            },
                            y: {
                                grid: { color: '#1a1a3e' }
                            }
                        }
                    }
                });
            }

            // Volume chart
            const volumeData = data.map(d => ({
                x: new Date(d.time || d.x),
                v: d.volume || 0
            }));

            const volCtx = document.getElementById('volumeChart').getContext('2d');
            volumeChart = new Chart(volCtx, {
                type: 'bar',
                data: {
                    datasets: [{
                        label: 'Volume',
                        data: volumeData.map(d => d.v),
                        backgroundColor: volumeData.map((d, i) => {
                            if (i === 0) return '#666';
                            const currentClose = data[i].close || data[i].c;
                            const prevClose = data[i-1].close || data[i-1].c;
                            return currentClose >= prevClose ? '#00d4aa' : '#ff4757';
                        }),
                        borderColor: 'transparent',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: {
                            type: 'time',
                            time: { unit: 'hour' },
                            grid: { display: false },
                            labels: data.map(d => new Date(d.time || d.x))
                        },
                        y: {
                            grid: { color: '#1a1a3e' }
                        }
                    }
                }
            });
        }

        function updateStats(data) {
            const lastBar = data[data.length - 1];
            const currentPrice = lastBar.close || lastBar.c;
            
            const highs = data.map(d => d.high || d.h);
            const lows = data.map(d => d.low || d.l);
            const volumes = data.map(d => d.volume || 0);
            
            const periodHigh = Math.max(...highs);
            const periodLow = Math.min(...lows);
            const avgVolume = volumes.reduce((a, b) => a + b, 0) / volumes.length;

            document.getElementById('currentPrice').textContent = currentPrice.toFixed(5);
            document.getElementById('dayHigh').textContent = periodHigh.toFixed(5);
            document.getElementById('dayLow').textContent = periodLow.toFixed(5);
            document.getElementById('avgVolume').textContent = Math.round(avgVolume).toLocaleString();
        }

        function toggleChartType() {
            currentType = currentType === 'candle' ? 'line' : 'candle';
            loadData();
        }

        // Load data on page load
        window.onload = loadData;

        // Auto-refresh every 60 seconds
        setInterval(loadData, 60000);
    </script>
</body>
</html>