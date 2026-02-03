// TradingView Chart Configuration
let currentChart = null;
let isChartLoading = false;
let lastApiRequestTime = 0;
const API_REQUEST_COOLDOWN = 60000; // 1 minute cooldown between API requests
let rateLimitMessageShown = false;
let lastAnalysisUpdateTime = 0;
const MIN_ANALYSIS_INTERVAL = 60000; // 60 seconds minimum between analysis updates

/**
 * Convert timestamp to human-readable "2 words" format
 * @param {string|Date} timestamp - ISO string, Date object, or 'YYYY-MM-DD HH:MM:SS'
 * @returns {string} - Human readable time in "2 words" format
 */
function timeAgoTwoWords(timestamp) {
  // Parse the timestamp
  let date;
  if (timestamp instanceof Date) {
    date = timestamp;
  } else if (typeof timestamp === "string") {
    // Handle 'YYYY-MM-DD HH:MM:SS' format
    if (timestamp.includes(" ")) {
      // Replace space with 'T' for ISO format
      timestamp = timestamp.replace(" ", "T");
    }
    date = new Date(timestamp);
  } else {
    return "Invalid date";
  }

  // Get current time
  const now = new Date();
  const diffMs = now - date;

  // If future date, handle differently
  if (diffMs < 0) {
    return "In future";
  }

  // Convert to seconds
  const diffSec = Math.floor(diffMs / 1000);

  // Time units in seconds
  const minute = 60;
  const hour = minute * 60;
  const day = hour * 24;
  const week = day * 7;
  const month = day * 30; // Approximation
  const year = day * 365; // Approximation

  // Calculate time differences
  if (diffSec < minute) {
    return "Just now";
  } else if (diffSec < hour) {
    const minutes = Math.floor(diffSec / minute);
    return `${minutes} min${minutes > 1 ? "s" : ""} ago`;
  } else if (diffSec < day) {
    const hours = Math.floor(diffSec / hour);
    return `${hours} hr${hours > 1 ? "s" : ""} ago`;
  } else if (diffSec < week) {
    const days = Math.floor(diffSec / day);
    return `${days} day${days > 1 ? "s" : ""} ago`;
  } else if (diffSec < month) {
    const weeks = Math.floor(diffSec / week);
    if (weeks === 1) return "1 week ago";
    return `${weeks} wk${weeks > 1 ? "s" : ""} ago`;
  } else if (diffSec < year) {
    const months = Math.floor(diffSec / month);
    if (months === 1) return "1 month ago";
    return `${months} mo${months > 1 ? "s" : ""} ago`;
  } else {
    const years = Math.floor(diffSec / year);
    if (years === 1) return "1 year ago";
    return `${years} yr${years > 1 ? "s" : ""} ago`;
  }
}

/**
 * Alternative: Get "2 days 2 hours" format (without "ago")
 * @param {string|Date} timestamp - ISO string, Date object, or 'YYYY-MM-DD HH:MM:SS'
 * @returns {string} - Human readable time in "X units Y units" format
 */
function timeDiffTwoUnits(timestamp) {
  // Parse the timestamp
  let date;
  if (timestamp instanceof Date) {
    date = timestamp;
  } else if (typeof timestamp === "string") {
    if (timestamp.includes(" ")) {
      timestamp = timestamp.replace(" ", "T");
    }
    date = new Date(timestamp);
  } else {
    return "Invalid date";
  }

  const now = new Date();
  const diffMs = now - date;

  if (diffMs < 0) {
    return "0 mins";
  }

  const diffSec = Math.floor(diffMs / 1000);
  const minute = 60;
  const hour = minute * 60;
  const day = hour * 24;

  // Calculate all units
  const days = Math.floor(diffSec / day);
  const hours = Math.floor((diffSec % day) / hour);
  const minutes = Math.floor((diffSec % hour) / minute);

  // Return the two largest non-zero units
  if (days > 0) {
    if (hours > 0) {
      return `${days} day${days > 1 ? "s" : ""} ${hours} hr${hours > 1 ? "s" : ""}`;
    } else if (minutes > 0) {
      return `${days} day${days > 1 ? "s" : ""} ${minutes} min${minutes > 1 ? "s" : ""}`;
    } else {
      return `${days} day${days > 1 ? "s" : ""}`;
    }
  } else if (hours > 0) {
    if (minutes > 0) {
      return `${hours} hr${hours > 1 ? "s" : ""} ${minutes} min${minutes > 1 ? "s" : ""}`;
    } else {
      return `${hours} hr${hours > 1 ? "s" : ""}`;
    }
  } else {
    return `${minutes} min${minutes > 1 ? "s" : ""}`;
  }
}

/**
 * Compact version: Always returns exactly 2 words (number + unit)
 * @param {string|Date} timestamp - ISO string, Date object, or 'YYYY-MM-DD HH:MM:SS'
 * @returns {string} - Exactly 2 words format
 */
function timeAgoCompact(timestamp) {
  // Parse the timestamp
  let date;
  if (timestamp instanceof Date) {
    date = timestamp;
  } else if (typeof timestamp === "string") {
    if (timestamp.includes(" ")) {
      timestamp = timestamp.replace(" ", "T");
    }
    date = new Date(timestamp);
  } else {
    return "Invalid date";
  }

  const now = new Date();
  const diffMs = now - date;

  if (diffMs < 0) {
    return "Future";
  }

  const diffSec = Math.floor(diffMs / 1000);
  const units = [
    { unit: "year", seconds: 31536000, short: "yr" },
    { unit: "month", seconds: 2592000, short: "mo" },
    { unit: "week", seconds: 604800, short: "wk" },
    { unit: "day", seconds: 86400, short: "day" },
    { unit: "hour", seconds: 3600, short: "hr" },
    { unit: "minute", seconds: 60, short: "min" },
    { unit: "second", seconds: 1, short: "sec" },
  ];

  for (let i = 0; i < units.length; i++) {
    const { seconds, short } = units[i];
    const count = Math.floor(diffSec / seconds);

    if (count >= 1) {
      return `${count} ${short}`;
    }
  }

  return "0 sec";
}

/**
 * Update all timestamp elements on the page
 */
function updateAllTimestamps() {
  // Find all elements with data-timestamp attribute
  document.querySelectorAll("[data-timestamp]").forEach((element) => {
    const timestamp = element.getAttribute("data-timestamp");
    const formatted = timeAgoTwoWords(timestamp);
    element.textContent = formatted;

    // Update tooltip with full date
    if (element.hasAttribute("data-tooltip")) {
      const date = new Date(timestamp.replace(" ", "T"));
      const fullDate = date.toLocaleString("en-US", {
        weekday: "long",
        year: "numeric",
        month: "long",
        day: "numeric",
        hour: "2-digit",
        minute: "2-digit",
        second: "2-digit",
        timeZoneName: "short",
      });
      element.setAttribute("data-tooltip", `Updated: ${fullDate}`);
    }
  });

  // Update all elements with specific IDs
  const timestampIds = ["maTimestamp", "tiTimestamp", "fxbTimestamp"];
  timestampIds.forEach((id) => {
    const element = document.getElementById(id);
    if (element && element.dataset.originalTime) {
      const formatted = timeAgoTwoWords(element.dataset.originalTime);
      element.textContent = formatted;
    }
  });
}

/**
 * Initialize timestamps with auto-update
 */
function initTimestamps() {
  console.log("initTimestamps");

  // Initial update
  updateAllTimestamps();

  // Update every minute
  setInterval(updateAllTimestamps, 60000);

  // Mark elements that should auto-update
  document.querySelectorAll(".timestamp").forEach((element) => {
    if (!element.hasAttribute("data-original-time") && element.textContent) {
      // Try to parse current text as a time
      const text = element.textContent;
      if (text.includes("min") || text.includes("hr") || text.includes("day")) {
        // Set a fake timestamp 2 hours ago for demo
        const twoHoursAgo = new Date(Date.now() - 2 * 60 * 60 * 1000);
        element.dataset.originalTime = twoHoursAgo
          .toISOString()
          .replace("T", " ")
          .slice(0, 19);
      }
    }
  });
}

// Map rating to needle angle
function getNeedleAngleForRating(rating) {
  const ratingAngles = {
    "strong sell": 15,
    "strong-sell": 15,
    sell: 50,
    neutral: 90,
    buy: 130,
    "strong buy": 170,
    "strong-buy": 170,
  };

  const normalizedRating = rating.toLowerCase().trim();
  return (
    ratingAngles[normalizedRating] ||
    ratingAngles[normalizedRating.replace(/\s+/g, "-")] ||
    90
  );
}

// Map rating to CSS class
function getClassForRating(rating) {
  const ratingClasses = {
    "strong sell": "strong-sell",
    "strong-sell": "strong-sell",
    sell: "sell",
    neutral: "neutral",
    buy: "buy",
    "strong buy": "strong-buy",
    "strong-buy": "strong-buy",
  };

  const normalizedRating = rating.toLowerCase().trim();
  return (
    ratingClasses[normalizedRating] ||
    ratingClasses[normalizedRating.replace(/\s+/g, "-")] ||
    "neutral"
  );
}

// Set needle angle with safety check
function setNeedleAngle(needleId, angle) {
  const needle = document.getElementById(needleId);
  if (needle) {
    const gauge = needle.closest(".gauge");
    if (gauge) {
      gauge.style.setProperty("--angle", angle + "deg");
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

  gaugeRating.className = "gauge-rating text-white text-center mt-3";

  if (ratingClass !== "neutral" || rating.toLowerCase().includes("neutral")) {
    gaugeRating.textContent = rating;
    gaugeRating.classList.add(ratingClass);
  } else {
    console.warn(`Unknown rating: "${rating}". Defaulting to neutral.`);
    gaugeRating.textContent = "Neutral";
    gaugeRating.classList.add("neutral");
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
  const success = updateGaugeRating("ti-needle", "ti-rating", rating);
  if (success) {
    updateCountDisplay("tiCounts", buyCount, sellCount);
  }
}

function updateSummary(rating, buyCount = 0, sellCount = 0) {
  const success = updateGaugeRating("summary-needle", "summary-rating", rating);
  if (success) {
    updateCountDisplay("overallCounts", buyCount, sellCount);
  }
}

function updateMovingAverages(rating, buyCount = 0, sellCount = 0) {
  const success = updateGaugeRating("ma-needle", "ma-rating", rating);
  if (success) {
    updateCountDisplay("maCounts", buyCount, sellCount);
  }
}

// Check if all required elements exist
function validateGaugeElements() {
  const requiredElements = [
    "ti-needle",
    "ti-rating",
    "tiCounts",
    "summary-needle",
    "summary-rating",
    "overallCounts",
    "ma-needle",
    "ma-rating",
    "maCounts",
  ];

  const missingElements = [];

  requiredElements.forEach((id) => {
    if (!document.getElementById(id)) {
      missingElements.push(id);
    }
  });

  if (missingElements.length > 0) {
    console.error("Missing gauge elements:", missingElements);
    return false;
  }

  return true;
}

// Initialize all gauges safely
function initializeGauges() {
  if (!validateGaugeElements()) {
    console.error("Cannot initialize gauges - missing HTML elements");
    return;
  }

  updateTechnicalIndicators("Neutral");
  updateSummary("Neutral");
  updateMovingAverages("Neutral");
}

// Safe way to update all gauges
function updateAllGauges(data) {
  if (!data) {
    console.error("No data provided to update gauges");
    return;
  }

  // Update Technical Indicators if data exists
  if (data.technical) {
    updateTechnicalIndicators(
      data.technical.rating || "Neutral",
      data.technical.buyCount || 0,
      data.technical.sellCount || 0,
    );
  }

  // Update Summary if data exists
  if (data.summary) {
    updateSummary(
      data.summary.rating || "Neutral",
      data.summary.buyCount || 0,
      data.summary.sellCount || 0,
    );
  }

  // Update Moving Averages if data exists
  if (data.movingAverages) {
    updateMovingAverages(
      data.movingAverages.rating || "Neutral",
      data.movingAverages.buyCount || 0,
      data.movingAverages.sellCount || 0,
    );
  }
}

function updateMarketStatus(symbol) {
  const statusElement = document.querySelector(".status-indicator");
  if (statusElement) {
    statusElement.innerHTML =
      '<span class="live-dot me-2"></span> Live ' + symbol;
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
    M: "Monthly",
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
  document.getElementById("current-pair").textContent =
    `${symbol} - ${timeframeLabel} Chart`;
  //   document.getElementById("current-tf").textContent = timeframeLabel;
  //   document.getElementById("current-tf-second").textContent = timeframeLabel;

  // Show loading state
  const chartContainer = document.getElementById("chart");
  chartContainer.innerHTML = `
        <div class="chart-loading d-flex flex-column align-items-center justify-content-center h-100">
            <div class="loading-spinner mb-3"></div>
            <p class="text-muted">Loading ${symbol} chart...</p>
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
        "BollingerBands@tv-basicstudies",
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
        volumePaneSize: "medium",
      },
      studies_overrides: {
        "volume.volume.color.0": "#ef4444",
        "volume.volume.color.1": "#10b981",
        "volume.volume.transparency": 70,
      },
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
      const remainingTime = Math.ceil(
        (API_REQUEST_COOLDOWN - (now - lastApiRequestTime)) / 1000,
      );

      if (!rateLimitMessageShown) {
        showRateLimitMessage(remainingTime);
        rateLimitMessageShown = true;

        setTimeout(() => {
          rateLimitMessageShown = false;
        }, API_REQUEST_COOLDOWN);
      }

      console.log(
        `Rate limited: Please wait ${remainingTime} seconds before making another API request`,
      );
      throw new Error(`REQUEST TOO SOON: Please wait ${remainingTime} seconds`);
    }

    // Convert timeframe to match API format
    const tfMap = {
      15: "m15",
      30: "m30",
      60: "H1",
      240: "H4",
      D: "D1",
      W: "W1",
      M: "MN",
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
        throw new Error(
          errorData.message || `Scrape Data failed: ${res.status}`,
        );
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
    console.error("Error fetching technical analysis:", error);

    if (error.message.includes("REQUEST TOO SOON")) {
      throw error;
    }

    return getFallbackData(pair, timeframe);
  }
}

function showRateLimitMessage(remainingSeconds) {
  removeRateLimitMessage();

  const message = document.createElement("div");
  message.className = "rate-limit-message";
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
        <i class="fas fa-exclamation-circle"></i>
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
            <i class="fas fa-times"></i>
        </button>
    </div>
`;

  document.body.appendChild(message);

  setTimeout(() => {
    removeRateLimitMessage();
  }, 5000);
}

function removeRateLimitMessage() {
  const existingMessage = document.querySelector(".rate-limit-message");
  if (existingMessage) {
    existingMessage.remove();
  }
}

function getFallbackData(pair, timeframe) {
  console.log("Using fallback data for", pair, timeframe);
  return {
    success: true,
    data: {
      myfxbook_data: [
        {
          technical_analysis: {
            total_patterns: 5,
            technical_summary: "Neutral",
            counts: {
              buy: 3,
              sell: 2,
              neutral: 0,
            },
            patterns_sample: [
              {
                name: "Doji",
                signal: "neutral",
              },
              {
                name: "Engulfing Pattern",
                signal: "buy",
              },
              {
                name: "Hammer",
                signal: "buy",
              },
            ],
          },
          interest_rates: {
            total_rates: 2,
            rates_sample: [
              {
                country: "Euro Area",
                centralBank: "European Central Bank",
                currentRate: "2.15%",
                previousRate: "2.15%",
                nextMeeting: "8 days",
              },
              {
                country: "Japan",
                centralBank: "Bank of Japan",
                currentRate: "0.75%",
                previousRate: "0.75%",
                nextMeeting: "49 days",
              },
            ],
          },
        },
      ],
      investing_data: [
        {
          overall_signal: "Neutral",
          scrape_timestamp: new Date().toLocaleString(),
          technical_indicators: {
            sample: [
              {
                name: "RSI(14)",
                value: "50.0",
                action: "Neutral",
              },
              {
                name: "MACD(12,26)",
                value: "0.0",
                action: "Neutral",
              },
            ],
          },
          moving_averages: {
            sample: [
              {
                name: "MA5",
                simple: "0.0 Neutral",
                exponential: "Neutral",
              },
              {
                name: "MA10",
                simple: "0.0 Neutral",
                exponential: "Neutral",
              },
            ],
          },
          pivot_points: {
            sample: [
              {
                name: "Classic",
                s3: "0.0",
                s2: "0.0",
                s1: "0.0",
                pivot_points: "0.0",
                r1: "0.0",
                r2: "0.0",
                r3: "0.0",
              },
            ],
          },
        },
      ],
    },
  };
}

async function updateAnalysis(symbol, timeframe) {
  try {
    // Update the last analysis update time
    lastAnalysisUpdateTime = Date.now();

    // Fetch real data from API
    const apiResponse = await fetchTechnicalAnalysis(symbol, timeframe);

    if (!apiResponse || !apiResponse.success) {
      throw new Error("Invalid API response");
    }

    console.log("API Response received:", apiResponse);

    // Get the latest myfxbook data (first item in array)
    const myfxbookLatest = apiResponse.data?.myfxbook_data?.[0];
    const investingLatest = apiResponse.data?.investing_data?.[0];

    if (!myfxbookLatest && !investingLatest) {
      throw new Error("No analysis data found");
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
    updateDetailedAnalysisSection(
      myfxbookLatest,
      investingLatest,
      symbol,
      timeframe,
    );

    // Update the last updated timestamp
    const updateTime = document.getElementById("time-since-update");
    if (updateTime) {
      updateTime.textContent = `Updated: ${new Date().toLocaleTimeString()}`;
      updateTime.style.color = "#10b981";
    }
  } catch (error) {
    console.error("Error updating analysis:", error);

    if (error.message.includes("REQUEST TOO SOON")) {
      // Show rate limit error
      const analysisContainer = document.querySelector(".analysis-placeholder");
      if (analysisContainer) {
        analysisContainer.innerHTML = `
                    <div class="analysis-item">
                        <div class="ta-container" id="taContainer">
                            <div class="signal" id="signal">
                                <span class="label">Rate Limited:</span>
                                <span class="value text-danger">${error.message}</span>
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
  const container = document.getElementById("taContainer");

  if (!container) return;

  // Clear container and build the correct structure
  container.innerHTML = "";

  // Technical Summary Header
  const summaryHeader = document.createElement("div");
  summaryHeader.className =
    "technical-summary-header p-3 bg-secondary rounded mb-3";
  summaryHeader.style.cssText =
    "display: flex; justify-content: center; align-items: center; width: 100%; font-size: 14px;";

  const summaryLabel = document.createElement("div");
  summaryLabel.textContent = "Technical Summary:";
  summaryLabel.className = "me-2";

  const summaryValue = document.createElement("div");
  const signal = ta.technical_summary || "Neutral";
  summaryValue.textContent = signal;
  summaryValue.className = "fw-bold";

  // Set color based on signal
  if (signal.toLowerCase() === "buy" || signal.toLowerCase() === "strong buy") {
    summaryValue.classList.add("text-success");
  } else if (
    signal.toLowerCase() === "sell" ||
    signal.toLowerCase() === "strong sell"
  ) {
    summaryValue.classList.add("text-danger");
  } else {
    summaryValue.classList.add("text-warning");
  }

  summaryHeader.appendChild(summaryLabel);
  summaryHeader.appendChild(summaryValue);
  container.appendChild(summaryHeader);

  // Create table container
  const tableContainer = document.createElement("div");
  tableContainer.className = "table-responsive";

  // Create table
  const table = document.createElement("table");
  table.className = "table table-dark table-hover ta-table";

  // Create table header
  const thead = document.createElement("thead");
  thead.innerHTML = `
    <tr>
        <th class="text-start">Pattern</th>
        <th class="text-center">Buy (${ta.counts?.buy || 0})</th>
        <th class="text-center">Sell (${ta.counts?.sell || 0})</th>
    </tr>
`;
  table.appendChild(thead);

  // Create table body
  const tbody = document.createElement("tbody");

  if (ta.patterns_sample && ta.patterns_sample.length > 0) {
    ta.patterns_sample.forEach((pattern) => {
      const row = document.createElement("tr");

      // Pattern name cell
      const nameCell = document.createElement("td");
      nameCell.className = "text-start";

      const nameLink = document.createElement("a");
      nameLink.href = "#";
      nameLink.className = "text-decoration-none text-light";
      nameLink.textContent = pattern.name;
      nameCell.appendChild(nameLink);
      row.appendChild(nameCell);

      // Buy cell
      const buyCell = document.createElement("td");
      buyCell.className = "text-center";

      if (pattern.buy) {
        const buyDiv = document.createElement("div");
        buyDiv.className =
          "bg-buy d-flex justify-content-center align-items-center text-uppercase fw-bold";
        buyDiv.textContent = pattern.buy;
        buyCell.appendChild(buyDiv);
      }
      row.appendChild(buyCell);

      // Sell cell
      const sellCell = document.createElement("td");
      sellCell.className = "text-center";

      if (pattern.sell) {
        const sellDiv = document.createElement("div");
        sellDiv.className =
          "bg-sell d-flex justify-content-center align-items-center text-uppercase fw-bold";
        sellDiv.textContent = pattern.sell;
        sellCell.appendChild(sellDiv);
      }
      row.appendChild(sellCell);

      // If signal is "both", we need to handle buy and sell in separate cells
      // If signal is neutral or other, we need to show it spanning both columns
      if (pattern.signal && pattern.signal.toLowerCase() === "both") {
        // Already handled above with separate buy/sell cells
      } else if (pattern.signal && pattern.signal.toLowerCase() === "neutral") {
        // Clear cells and create neutral cell spanning both columns
        buyCell.innerHTML = "";
        buyCell.colSpan = 2;

        const neutralDiv = document.createElement("div");
        neutralDiv.className =
          "bg-neutral d-flex justify-content-center align-items-center text-uppercase fw-bold mx-auto";
        neutralDiv.style.maxWidth = "43%";
        neutralDiv.textContent = pattern.buy || pattern.sell || "Neutral";
        buyCell.appendChild(neutralDiv);

        // Remove sell cell
        row.removeChild(sellCell);
      } else if (pattern.signal) {
        // Handle other signals (buy, sell, strong buy, strong sell)
        const signal = pattern.signal.toLowerCase();
        if (signal === "buy" || signal === "strong buy") {
          sellCell.innerHTML = "";
          const buyDiv = document.createElement("div");
          buyDiv.className =
            "bg-buy d-flex justify-content-center align-items-center text-uppercase fw-bold";
          buyDiv.textContent = pattern.buy || pattern.timeframe || "N/A";
          buyCell.innerHTML = "";
          buyCell.appendChild(buyDiv);
        } else if (signal === "sell" || signal === "strong sell") {
          buyCell.innerHTML = "";
          const sellDiv = document.createElement("div");
          sellDiv.className =
            "bg-sell d-flex justify-content-center align-items-center text-uppercase fw-bold";
          sellDiv.textContent = pattern.sell || pattern.timeframe || "N/A";
          sellCell.innerHTML = "";
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
  const legend = document.createElement("div");
  legend.id = "technicalSummaryLegend";
  legend.className =
    "d-flex justify-content-between align-items-center mt-3 pt-3 border-top";

  const legendLabel = document.createElement("div");
  legendLabel.textContent = "Legend:";
  legendLabel.className = "fw-bold me-3";

  const legendItems = document.createElement("div");
  legendItems.className = "d-flex justify-content-around w-100 flex-wrap gap-2";

  // Buy legend item
  const buyLegend = document.createElement("div");
  buyLegend.className = "d-flex align-items-center";
  buyLegend.innerHTML = `
    <div class="legend-color bg-success me-2" style="width: 12px; height: 12px; border-radius: 2px;"></div>
    <div>Buy</div>
`;

  // Sell legend item
  const sellLegend = document.createElement("div");
  sellLegend.className = "d-flex align-items-center";
  sellLegend.innerHTML = `
    <div class="legend-color bg-danger me-2" style="width: 12px; height: 12px; border-radius: 2px;"></div>
    <div>Sell</div>
`;

  // Neutral legend item
  const neutralLegend = document.createElement("div");
  neutralLegend.className = "d-flex align-items-center";
  neutralLegend.innerHTML = `
    <div class="legend-color bg-warning me-2" style="width: 12px; height: 12px; border-radius: 2px;"></div>
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
    console.log(
      `Myfxbook - Buy: ${ta.counts.buy} | Sell: ${ta.counts.sell} | Neutral: ${ta.counts.neutral || 0}`,
    );
  }
}

function updateSecondSourceAnalysis(data, symbol, timeframe) {
  if (!data) return;

  // Update overall rating display
  const overallRating = data.overall_signal || "Neutral";
  let tiSummary = "Neutral"; // Define with default value
  let maSummary = "Neutral"; // Define with default value

  // Update summary details
  if (data.moving_averages && data.moving_averages.sample) {
    const maData = data.moving_averages.sample;
    const buyCount = maData.filter(
      (ma) => ma.simple.includes("Buy") || ma.exponential.includes("Buy"),
    ).length;
    const sellCount = maData.filter(
      (ma) => ma.simple.includes("Sell") || ma.exponential.includes("Sell"),
    ).length;

    maSummary =
      buyCount > sellCount ? "Buy" : sellCount > buyCount ? "Sell" : "Neutral";

    const maCounts = `Buy: ${buyCount} | Sell: ${sellCount}`;

    // Safely update elements if they exist
    const maRatingEl = document.getElementById("maRating");
    if (maRatingEl) {
      maRatingEl.textContent = maSummary;
      maRatingEl.className = `gauge-rating text-white text-center mt-3 ${maSummary.toLowerCase()}`;
    }

    const maCountsEl = document.getElementById("maCounts");
    if (maCountsEl) {
      maCountsEl.textContent = maCounts;
    }

    const maSummaryEl = document.getElementById("maSummary");
    if (maSummaryEl) {
      maSummaryEl.textContent = maSummary;
      maSummaryEl.className = "rating-text fw-bold " + maSummary.toLowerCase();
    }

    const maSummaryCountsEl = document.getElementById("maSummaryCounts");
    if (maSummaryCountsEl) {
      maSummaryCountsEl.textContent = maCounts;
    }

    const maTimestampEl = document.getElementById("maTimestamp");
    if (maTimestampEl) {
      const timestamp = data.scrape_timestamp || "-";
      const timeAgoText = timeAgoTwoWords(timestamp);

      maTimestampEl.textContent = timeAgoText;

      // Set the full timestamp as the tooltip title
      if (timestamp !== "-") {
        maTimestampEl.setAttribute("data-bs-original-title", timestamp);
        maTimestampEl.setAttribute("title", timestamp);
      }

      // Initialize Bootstrap tooltip if it exists
      if (typeof bootstrap !== "undefined" && bootstrap.Tooltip) {
        new bootstrap.Tooltip(maTimestampEl, {
          placement: "top",
          trigger: "hover",
        });
      }
    }

    // Update moving averages table
    updateMovingAveragesTable(maData);
  }

  if (data.technical_indicators && data.technical_indicators.sample) {
    const tiData = data.technical_indicators.sample;
    const buyCount = tiData.filter(
      (ti) => ti.action === "Buy" || ti.action === "Strong Buy",
    ).length;
    const sellCount = tiData.filter(
      (ti) => ti.action === "Sell" || ti.action === "Strong Sell",
    ).length;

    tiSummary =
      buyCount > sellCount ? "Buy" : sellCount > buyCount ? "Sell" : "Neutral";

    const tiCounts = `Buy: ${buyCount} | Sell: ${sellCount}`;

    // Safely update elements if they exist
    const tiRatingEl = document.getElementById("tiRating");
    if (tiRatingEl) {
      tiRatingEl.textContent = tiSummary;
      tiRatingEl.className = `gauge-rating text-white text-center mt-3 ${tiSummary.toLowerCase()}`;
    }

    const tiCountsEl = document.getElementById("tiCounts");
    if (tiCountsEl) {
      tiCountsEl.textContent = tiCounts;
    }

    const tiSummaryEl = document.getElementById("tiSummary");
    if (tiSummaryEl) {
      tiSummaryEl.textContent = tiSummary;
      tiSummaryEl.className = "rating-text fw-bold " + tiSummary.toLowerCase();
    }

    const tiSummaryCountsEl = document.getElementById("tiSummaryCounts");
    if (tiSummaryCountsEl) {
      tiSummaryCountsEl.textContent = tiCounts;
    }

    const tiTimestampEl = document.getElementById("tiTimestamp");
    if (tiTimestampEl) {
      const timestamp = data.scrape_timestamp || "-";
      const timeAgoText = timeAgoTwoWords(timestamp);

      tiTimestampEl.textContent = timeAgoText;

      // Set the full timestamp as the tooltip title
      if (timestamp !== "-") {
        tiTimestampEl.setAttribute("data-bs-original-title", timestamp);
        tiTimestampEl.setAttribute("title", timestamp);
      }

      // Initialize Bootstrap tooltip if it exists
      if (typeof bootstrap !== "undefined" && bootstrap.Tooltip) {
        new bootstrap.Tooltip(tiTimestampEl, {
          placement: "top",
          trigger: "hover",
        });
      }
    }

    // Update technical indicators table
    updateTechnicalIndicatorsTable(tiData);
  }

  if (data.pivot_points && data.pivot_points.sample) {
    updatePivotPointsTable(data.pivot_points.sample);
  }

  // Update gauges - these should exist if you have the 3-gauge layout
  updateGaugeRating("summary-needle", "summary-rating", overallRating);
  updateGaugeRating("ti-needle", "ti-rating", tiSummary);
  updateGaugeRating("ma-needle", "ma-rating", maSummary);

  // Also update count displays on the gauges if they exist
  if (data.technical_indicators && data.technical_indicators.sample) {
    const tiData = data.technical_indicators.sample;
    const tiBuyCount = tiData.filter(
      (ti) => ti.action === "Buy" || ti.action === "Strong Buy",
    ).length;
    const tiSellCount = tiData.filter(
      (ti) => ti.action === "Sell" || ti.action === "Strong Sell",
    ).length;

    // Update the gauge count display
    const tiCountsEl = document.getElementById("tiCounts");
    if (tiCountsEl) {
      tiCountsEl.textContent = `Buy: ${tiBuyCount} | Sell: ${tiSellCount}`;
    }
  }

  if (data.moving_averages && data.moving_averages.sample) {
    const maData = data.moving_averages.sample;
    const maBuyCount = maData.filter(
      (ma) => ma.simple.includes("Buy") || ma.exponential.includes("Buy"),
    ).length;
    const maSellCount = maData.filter(
      (ma) => ma.simple.includes("Sell") || ma.exponential.includes("Sell"),
    ).length;

    // Update the gauge count display
    const maCountsEl = document.getElementById("maCounts");
    if (maCountsEl) {
      maCountsEl.textContent = `Buy: ${maBuyCount} | Sell: ${maSellCount}`;
    }
  }

  // Update summary counts if the element exists
  const overallCountsEl = document.getElementById("overallCounts");
  if (overallCountsEl) {
    // You might want to calculate overall counts from both MA and TI
    const tiBuyCount =
      data.technical_indicators?.sample?.filter(
        (ti) => ti.action === "Buy" || ti.action === "Strong Buy",
      ).length || 0;
    const tiSellCount =
      data.technical_indicators?.sample?.filter(
        (ti) => ti.action === "Sell" || ti.action === "Strong Sell",
      ).length || 0;

    const maBuyCount =
      data.moving_averages?.sample?.filter(
        (ma) => ma.simple.includes("Buy") || ma.exponential.includes("Buy"),
      ).length || 0;
    const maSellCount =
      data.moving_averages?.sample?.filter(
        (ma) => ma.simple.includes("Sell") || ma.exponential.includes("Sell"),
      ).length || 0;

    const totalBuy = tiBuyCount + maBuyCount;
    const totalSell = tiSellCount + maSellCount;

    overallCountsEl.textContent = `Buy: ${totalBuy} | Sell: ${totalSell}`;
  }
}

function updateMovingAveragesTable(maData) {
  const tableBody = document.getElementById("maTableBody");
  if (!tableBody || !maData) return;

  tableBody.innerHTML = "";

  maData.forEach((item) => {
    const row = document.createElement("tr");

    // Extract value and action from simple field
    const simpleMatch = item.simple?.match(/([\d.]+)\s*(.+)/);
    const simpleValue = simpleMatch ? simpleMatch[1] : "";
    const simpleAction = simpleMatch ? simpleMatch[2] : "";

    // Extract value and action from exponential field
    const expAction = item.exponential || "";

    row.innerHTML = `
            <td class="name-col">${item.name}</td>
            <td class="simple-col ${simpleAction.toLowerCase()}">
                <div class="value-action">
                    <span>${simpleValue}</span>
                    <span class="badge action ${simpleAction.toLowerCase()}">${simpleAction}</span>
                </div>
            </td>
            <td class="exponential-col ${expAction.toLowerCase()}">
                <div class="value-action">
                    <span class="badge action ${expAction.toLowerCase()}">${expAction}</span>
                </div>
            </td>
        `;
    tableBody.appendChild(row);
  });
}

function updateTechnicalIndicatorsTable(tiData) {
  const tableBody = document.getElementById("tiTableBody");
  if (!tableBody || !tiData) return;

  tableBody.innerHTML = "";

  tiData.forEach((item) => {
    const row = document.createElement("tr");
    const actionClass = item.action.toLowerCase().replace(" ", "-");
    row.innerHTML = `
            <td class="name-col">${item.name}</td>
            <td class="value-col">${item.value}</td>
            <td class="action-col"><span class="badge ${actionClass}">${item.action}</span></td>
        `;
    tableBody.appendChild(row);
  });
}

function updatePivotPointsTable(pivotData) {
  const tableBody = document.getElementById("pivotTableBody");
  if (!tableBody || !pivotData) return;

  tableBody.innerHTML = "";

  pivotData.forEach((item) => {
    const row = document.createElement("tr");
    row.innerHTML = `
            <td class="name-col">${item.name}</td>
            <td class="value-col">${item.s3 || ""}</td>
            <td class="value-col">${item.s2 || ""}</td>
            <td class="value-col">${item.s1 || ""}</td>
            <td class="pivot-col">${item.pivot_points || ""}</td>
            <td class="value-col">${item.r1 || ""}</td>
            <td class="value-col">${item.r2 || ""}</td>
            <td class="value-col">${item.r3 || ""}</td>
        `;
    tableBody.appendChild(row);
  });
}

function renderInterestRate(interestRateData) {
  if (!interestRateData || !interestRateData.rates_sample) return;

  const container = document.getElementById("interestRateContainer");
  if (!container) return;

  // Calculate stats for header
  const rates = interestRateData.rates_sample.map((r) =>
    parseFloat(r.currentRate),
  );
  const highestRate = Math.max(...rates);
  const avgRate = rates.reduce((a, b) => a + b, 0) / rates.length;

  // Build the HTML structure matching the example
  let html = `
    <div class="interest-rate-header d-flex justify-content-between align-items-center p-3 bg-secondary border-bottom">
        <div class="d-flex align-items-center gap-3">
            <i class="fas fa-percentage text-primary fs-5"></i>
            <h3 class="h5 mb-0">Central Bank Rates</h3>
        </div>
        <div class="d-flex align-items-center gap-3 small text-muted">
            <div><strong class="text-danger">${highestRate.toFixed(2)}%</strong> Highest</div>
            <div><strong>${avgRate.toFixed(2)}%</strong> Avg</div>
        </div>
    </div>
    <div class="table-responsive">
        <table class="interest-rate-table table table-dark">
            <thead>
                <tr>
                    <th class="text-start">Country</th>
                    <th class="text-start">Central Bank</th>
                    <th class="text-end">Current</th>
                    <th class="text-end">Previous</th>
                    <th class="text-center">Change</th>
                    <th class="text-center">Next Meeting</th>
                </tr>
            </thead>
            <tbody>
`;

  interestRateData.rates_sample.forEach((rate) => {
    const currentRate = parseFloat(rate.currentRate);
    const previousRate = parseFloat(rate.previousRate);
    const change = currentRate - previousRate;

    // Determine change style
    let changeClass = "hold";
    let changeText = "0.00%";

    if (change > 0) {
      changeClass = "hike";
      changeText = `+${change.toFixed(2)}%`;
    } else if (change < 0) {
      changeClass = "cut";
      changeText = `${change.toFixed(2)}%`;
    }

    // Determine meeting style based on days
    const meetingDays = parseInt(rate.nextMeeting) || 0;
    let meetingClass = "";

    if (meetingDays < 7) {
      meetingClass = "urgent";
    } else if (meetingDays < 30) {
      meetingClass = "upcoming";
    }

    // Get currency symbol and flag style
    const currencyCode =
      rate.country === "Euro Area"
        ? "EUR"
        : rate.country === "Japan"
          ? "JPY"
          : rate.country === "United States"
            ? "USD"
            : rate.country === "United Kingdom"
              ? "GBP"
              : rate.country === "Switzerland"
                ? "CHF"
                : "";

    let flagClass = "euro";
    let currencyIcon = "fas fa-euro-sign";

    if (currencyCode === "JPY") {
      flagClass = "japan";
      currencyIcon = "fas fa-yen-sign";
    } else if (currencyCode === "USD") {
      flagClass = "us";
      currencyIcon = "fas fa-dollar-sign";
    } else if (currencyCode === "GBP") {
      flagClass = "uk";
      currencyIcon = "fas fa-pound-sign";
    } else if (currencyCode === "CHF") {
      flagClass = "swiss";
      currencyIcon = "fas fa-franc-sign";
    }

    html += `
        <tr>
            <td>
                <div class="d-flex align-items-center gap-3">
                    <div class="country-flag ${flagClass} d-flex align-items-center justify-content-center rounded-circle" style="width: 32px; height: 32px;">
                        <i class="${currencyIcon}"></i>
                    </div>
                    <div>
                        <div class="fw-bold">${rate.country}</div>
                        <div class="small text-muted">${currencyCode}</div>
                    </div>
                </div>
            </td>
            <td>
                <div class="fw-bold">${rate.centralBank}</div>
                <div class="small text-muted">
                    ${getBankAbbreviation(rate.centralBank)}
                </div>
            </td>
            <td class="text-end fw-bold fs-5">
                ${rate.currentRate}%
            </td>
            <td class="text-end text-muted">
                ${rate.previousRate}%
            </td>
            <td class="text-center">
                <div class="badge change-indicator ${changeClass}">
                    ${changeText}
                </div>
            </td>
            <td class="text-center">
                <div class="badge meeting-countdown ${meetingClass}">
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
    <div class="d-flex justify-content-between align-items-center p-3 bg-secondary border-top small">
        <div class="d-flex gap-4">
            <div class="d-flex align-items-center gap-2">
                <div class="legend-dot hike"></div>
                <span>Rate Hike</span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <div class="legend-dot cut"></div>
                <span>Rate Cut</span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <div class="legend-dot hold"></div>
                <span>On Hold</span>
            </div>
        </div>
        <div class="d-flex gap-4">
            <div class="d-flex align-items-center gap-2">
                <div class="legend-dot urgent" style="background: var(--accent-red);"></div>
                <span>&lt; 7 days</span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <div class="legend-dot upcoming" style="background: var(--accent-yellow);"></div>
                <span>&lt; 30 days</span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <div class="legend-dot" style="background: var(--accent-blue);"></div>
                <span>&gt; 30 days</span>
            </div>
        </div>
    </div>
`;

  container.innerHTML = html;

  // Update scrape time if you still want to show it
  const scrapeTimeElement = document.getElementById("scrapeTime");
  if (scrapeTimeElement) {
    scrapeTimeElement.textContent = new Date().toLocaleString();
  }
}

// Helper function to get bank abbreviations
function getBankAbbreviation(bankName) {
  const abbreviations = {
    "European Central Bank": "ECB",
    "Bank of Japan": "BoJ",
    "Federal Reserve": "Fed",
    "Bank of England": "BoE",
    "Swiss National Bank": "SNB",
    "Reserve Bank of Australia": "RBA",
    "Bank of Canada": "BoC",
    "Reserve Bank of New Zealand": "RBNZ",
  };

  return abbreviations[bankName] || bankName.substring(0, 3).toUpperCase();
}

function renderInterestRateSimple(interestRateData) {
  if (!interestRateData || !interestRateData.rates_sample) return;

  const tableBody = document.getElementById("ratesTableBody");
  if (!tableBody) return;

  tableBody.innerHTML = "";

  interestRateData.rates_sample.forEach((rate) => {
    const currentRate = parseFloat(rate.currentRate);
    const previousRate = parseFloat(rate.previousRate);
    const change = currentRate - previousRate;

    let changeClass = "hold";
    let changeIcon = "";
    let changeText = "Hold";

    if (change > 0) {
      changeClass = "hike";
      changeIcon = "↑";
      changeText = `+${change.toFixed(2)}%`;
    } else if (change < 0) {
      changeClass = "cut";
      changeIcon = "↓";
      changeText = `${change.toFixed(2)}%`;
    }

    // Get currency icon based on country
    const country = rate.country.toLowerCase();
    let currencyIcon = "fas fa-money-bill-wave";

    if (country.includes("euro") || country.includes("eur")) {
      currencyIcon = "fas fa-euro-sign";
    } else if (country.includes("japan") || country.includes("jpy")) {
      currencyIcon = "fas fa-yen-sign";
    } else if (
      country.includes("usa") ||
      country.includes("usd") ||
      country.includes("united states")
    ) {
      currencyIcon = "fas fa-dollar-sign";
    } else if (
      country.includes("uk") ||
      country.includes("gbp") ||
      country.includes("united kingdom")
    ) {
      currencyIcon = "fas fa-pound-sign";
    } else if (country.includes("swiss") || country.includes("chf")) {
      currencyIcon = "fas fa-franc-sign";
    }

    const row = document.createElement("tr");
    row.innerHTML = `
        <td class="country-col">
            <div class="d-flex align-items-center gap-2">
                <div class="country-flag">
                    <i class="${currencyIcon}"></i>
                </div>
                <span>${rate.country}</span>
            </div>
        </td>
        <td class="bank-col">${rate.centralBank}</td>
        <td class="rate-col">${rate.currentRate}%</td>
        <td class="previous-col">${rate.previousRate}%</td>
        <td class="change-col">
            <span class="badge ${changeClass}">${changeIcon} ${changeText}</span>
        </td>
        <td class="meeting-col">${rate.nextMeeting}</td>
        <td class="outlook-col">
            <span class="badge outlook ${changeClass}">${changeText}</span>
        </td>
    `;
    tableBody.appendChild(row);
  });

  // Update scrape time
  const scrapeTimeElement = document.getElementById("scrapeTime");
  if (scrapeTimeElement) {
    scrapeTimeElement.textContent = new Date().toLocaleString();
  }
}

function updateDetailedAnalysisSection(
  myfxbookData,
  investingData,
  symbol,
  timeframe,
) {
  const detailedAnalysis = document.getElementById("detailedAnalysis");

  if (!detailedAnalysis) return;

  let analysisHTML = "";

  const fxbTimestampEl = document.getElementById("fxbTimestamp");
  if (fxbTimestampEl) {
    const timestamp = myfxbookData.created_at || "-";
    const timeAgoText = timeAgoTwoWords(timestamp);

    fxbTimestampEl.textContent = timeAgoText;

    // Set the full timestamp as the tooltip title
    if (timestamp !== "-") {
      fxbTimestampEl.setAttribute("data-bs-original-title", timestamp);
      fxbTimestampEl.setAttribute("title", timestamp);
    }

    // Initialize Bootstrap tooltip if it exists
    if (typeof bootstrap !== "undefined" && bootstrap.Tooltip) {
      new bootstrap.Tooltip(fxbTimestampEl, {
        placement: "top",
        trigger: "hover",
      });
    }
  }

  // Add myfxbook pattern analysis
  if (myfxbookData?.technical_analysis) {
    const ta = myfxbookData.technical_analysis;
    const buyCount = ta.counts?.buy || 0;
    const sellCount = ta.counts?.sell || 0;

    analysisHTML += `
            <div class="analysis-card col-md-4">
                <h4>Pattern Analysis (Myfxbook)</h4>
                <p>${getPatternAnalysis(ta)}</p>
                <div class="analysis-tags">
                    <span class="badge ${ta.technical_summary.toLowerCase()}">
                        ${ta.technical_summary}
                    </span>
                    ${
                      buyCount > sellCount
                        ? '<span class="badge bg-success ms-1">Bullish Bias</span>'
                        : sellCount > buyCount
                          ? '<span class="badge bg-danger ms-1">Bearish Bias</span>'
                          : '<span class="badge bg-warning ms-1">Balanced</span>'
                    }
                </div>
            </div>
        `;
  }

  // Add investing.com technical indicators summary
  if (investingData?.technical_indicators?.sample) {
    const ti = investingData.technical_indicators;
    const buyCount =
      ti.sample.filter(
        (item) => item.action === "Buy" || item.action === "Strong Buy",
      ).length || 0;
    const sellCount =
      ti.sample.filter(
        (item) => item.action === "Sell" || item.action === "Strong Sell",
      ).length || 0;

    analysisHTML += `
            <div class="analysis-card col-md-4">
                <h4>Technical Indicators (Investing.com)</h4>
                <p>${buyCount} bullish vs ${sellCount} bearish indicators detected</p>
                <div class="analysis-tags">
                    ${
                      buyCount > sellCount
                        ? '<span class="badge bg-success">Bullish Bias</span>'
                        : sellCount > buyCount
                          ? '<span class="badge bg-danger">Bearish Bias</span>'
                          : '<span class="badge bg-warning">Balanced</span>'
                    }
                </div>
            </div>
        `;
  }

  // Add interest rates if available
  if (myfxbookData?.interest_rates?.rates_sample) {
    const rates = myfxbookData.interest_rates.rates_sample;
    analysisHTML += `
            <div class="analysis-card col-md-4">
                <h4>Interest Rates</h4>
                <p>${rates
                  .map(
                    (rate) =>
                      `${rate.country}: ${rate.currentRate} (Next meeting: ${rate.nextMeeting})`,
                  )
                  .join("<br>")}</p>
                <div class="analysis-tags">
                    ${rates
                      .map(
                        (rate) =>
                          `<span class="badge bg-secondary me-1">${rate.centralBank}</span>`,
                      )
                      .join("")}
                </div>
            </div>
        `;
  }

  detailedAnalysis.innerHTML = analysisHTML;
}

function getPatternAnalysis(taData) {
  if (!taData || !taData.patterns_sample) return "No pattern data available";

  const patterns = taData.patterns_sample;
  const topPatterns = patterns.slice(0, 3);

  return `Detected ${taData.total_patterns} patterns including ${topPatterns.map((p) => p.name).join(", ")}`;
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

    updateDetailedAnalysisSection(
      myfxbookLatest,
      investingLatest,
      symbol,
      timeframe,
    );
  }
}

// Fullscreen toggle function
function toggleFullscreen() {
  const chartSection = document.querySelector(".chart-section");
  if (!document.fullscreenElement) {
    if (chartSection.requestFullscreen) {
      chartSection.requestFullscreen();
    } else if (chartSection.webkitRequestFullscreen) {
      chartSection.webkitRequestFullscreen();
    } else if (chartSection.msRequestFullscreen) {
      chartSection.msRequestFullscreen();
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

// Export data function
function exportData() {
  // Implement export functionality here
  alert("Export functionality would be implemented here");
}

// Initialize chart on page load
document.addEventListener("DOMContentLoaded", () => {
  console.log("DOMContentLoaded");

  initTimestamps();

  // Check if we're on a page with gauges
  if (document.querySelector(".analysis-container")) {
    initializeGauges();
  }

  // Add loading styles
  const style = document.createElement("style");
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
  document.getElementById("pair").addEventListener("change", () => {
    const now = Date.now();
    if (now - lastAnalysisUpdateTime < MIN_ANALYSIS_INTERVAL) {
      const remainingTime = Math.ceil(
        (MIN_ANALYSIS_INTERVAL - (now - lastAnalysisUpdateTime)) / 1000,
      );
      showRateLimitMessage(remainingTime);
      return;
    }
    loadChart();
  });

  document.getElementById("tf").addEventListener("change", () => {
    const now = Date.now();
    if (now - lastAnalysisUpdateTime < MIN_ANALYSIS_INTERVAL) {
      const remainingTime = Math.ceil(
        (MIN_ANALYSIS_INTERVAL - (now - lastAnalysisUpdateTime)) / 1000,
      );
      showRateLimitMessage(remainingTime);
      return;
    }
    loadChart();
  });

  // Add click handler to refresh button
  document.querySelector(".refresh-btn").addEventListener("click", () => {
    const now = Date.now();
    if (now - lastAnalysisUpdateTime < MIN_ANALYSIS_INTERVAL) {
      const remainingTime = Math.ceil(
        (MIN_ANALYSIS_INTERVAL - (now - lastAnalysisUpdateTime)) / 1000,
      );
      showRateLimitMessage(remainingTime);
      return;
    }
    loadChart();
  });

  // Handle window resize
  window.addEventListener("resize", () => {
    if (currentChart && typeof currentChart.onResize === "function") {
      setTimeout(() => currentChart.onResize(), 100);
    }
  });
});
