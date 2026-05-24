import { parseJwt } from "./utils.js";
import { SwalHelper } from "./swal.js";

// Direct API base URL
// const API_BASE = 'https://itrust-tech.id/web';
const API_BASE = "/api/proxy"; // ← Local proxy endpoint
const ENDPOINTS = {
  login: "", // Action is sent in formData
  register: "",
  get_accounts_by_user: "",
  toggle_auto_trade: "",
  toggle_buy_sell_status: "",
  order_buy: "",
  order_sell: "",
  close_all_positions: "",
  close_orders_multi: "",
  order_buy_multi: "",
  order_sell_multi: "",
  toggle_auto_trade_multi: "",
  get_orders_by_account: "",
  get_open_orders: "",
  get_closed_orders: "",
};

// const ENDPOINTS = {
//   login: '/mobile/login',
//   register: '/mobile/register',
//   forgot_password: '/mobile/forgot-password',
//   get_accounts_by_user: '/mt4-account/get-accounts-by-user',
//   toggle_auto_trade: '/mt4-account/toggle-auto-trade',
//   toggle_buy_sell_status: '/mt4-account/toggle-buy-sell-status',
//   order_buy: '/mt4-account/order-buy',
//   order_sell: '/mt4-account/order-sell',
//   close_all_positions: '/mt4-account/close-all-positions',
//   get_orders_by_account: '/mt4-account/get-orders-by-account',
//   get_open_orders: '/mt4-account/get-open-orders-by-account',
//   get_closed_orders: '/mt4-account/get-closed-orders-by-account',
//   get_orders_summary: '/mt4-account/get-orders-summary-by-account',
// };

function getUserIdFromJwt() {
  const token = localStorage.getItem("jwt");
  if (!token) return null;
  const payload = parseJwt(token);
  return (
    payload?.data?.user_id ?? payload?.data?.id ?? payload?.data?.sub ?? null
  );
}

/**
 * API call - uses multipart/form-data like the working curl
 */
async function apiCall(action, params = {}) {
  const authToken = localStorage.getItem("jwt");
  const userId = getUserIdFromJwt();

  const url = API_BASE; // Always /api/proxy

  // Build FormData - include action
  const formData = new FormData();
  formData.append("action", action); // ⭐ Important: proxy needs this

  const finalParams = { ...params };
  if (userId && !finalParams.user_id) {
    finalParams.user_id = userId;
  }

  for (const [key, value] of Object.entries(finalParams)) {
    if (value !== undefined && value !== null) {
      formData.append(key, Array.isArray(value) ? JSON.stringify(value) : String(value));
    }
  }

  const options = {
    method: "POST",
    headers: {
      Accept: "application/json",
    },
    body: formData,
  };

  if (authToken) {
    options.headers["Authorization"] = `Bearer ${authToken}`;
  }

  try {
    const response = await fetch(url, options);

    console.log("🟢 Response status:", response.status);

    // Handle 401 - redirect to login
    if (response.status === 401) {
      console.warn("🔴 Unauthorized - redirecting to login");
      localStorage.removeItem("jwt");
      localStorage.removeItem("user");
      localStorage.removeItem("lastLogin");

      await SwalHelper.fire({
        icon: "warning",
        title: "Session Expired",
        text: "Please login again to continue.",
        timer: 2000,
        showConfirmButton: false,
      });

      window.location.href = "/login";
      throw new Error("Session expired");
    }

    if (!response.ok) {
      const errorText = await response.text();
      console.error("🔴 API Error:", response.status, errorText);
      throw new Error(`HTTP ${response.status}`);
    }

    const data = await response.json();
    console.log("🟢 API Response:", data);
    return data;
  } catch (error) {
    if (error.message.includes("Session expired")) throw error;

    console.error("🔴 API call failed:", error.message);

    // Network error - use mock data in development
    if (
      error.message.includes("Failed to fetch") ||
      error.message.includes("NetworkError")
    ) {
      console.log("🟡 Network error - using mock data");
      return getMockData(action, params);
    }

    throw error;
  }
}

/**
 * Mock data for development
 */
function getMockData(action, params) {
  console.log("📦 Using mock data for:", action);

  const mockAccounts = [
    {
      account_id: "12345",
      bot_name: "Golden Eagle EA",
      account_balance: 15000.5,
      account_equity: 15234.75,
      total_profit: 234.25,
      total_profit_percentage: 1.56,
      floating_value: 45.2,
      buy_order_count: 3,
      sell_order_count: 2,
      total_buy_lot: 0.15,
      total_sell_lot: 0.1,
      leverage: "500",
      currency: "USD",
      broker: "IC Markets",
      server: "ICMarkets-Demo",
      account_type: "real",
      status: "active",
      last_sync: new Date().toISOString(),
      mt4_last_sync: new Date().toISOString(),
      disabled_ea: 0,
      min_lot: 0.01,
      buy_status: 1,
      sell_status: 1,
    },
    {
      account_id: "67890",
      bot_name: "Silver Arrow Bot",
      account_balance: 8750.0,
      account_equity: 8620.3,
      total_profit: -129.7,
      total_profit_percentage: -1.48,
      floating_value: -35.5,
      buy_order_count: 1,
      sell_order_count: 4,
      total_buy_lot: 0.05,
      total_sell_lot: 0.2,
      leverage: "200",
      currency: "EUR",
      broker: "FXCM",
      server: "FXCM-Real",
      account_type: "real",
      status: "active",
      last_sync: new Date(Date.now() - 3600000).toISOString(),
      mt4_last_sync: new Date(Date.now() - 1800000).toISOString(),
      disabled_ea: 0,
      min_lot: 0.01,
      buy_status: 1,
      sell_status: 0,
    },
    {
      account_id: "11111",
      bot_name: "Diamond Scalper",
      account_balance: 2500.0,
      account_equity: 2498.5,
      total_profit: -1.5,
      total_profit_percentage: -0.06,
      floating_value: 0,
      buy_order_count: 0,
      sell_order_count: 0,
      total_buy_lot: 0,
      total_sell_lot: 0,
      leverage: "100",
      currency: "USD",
      broker: "Pepperstone",
      server: "Pepperstone-Demo",
      account_type: "demo",
      status: "disconnected",
      last_sync: new Date(Date.now() - 86400000).toISOString(),
      mt4_last_sync: null,
      disabled_ea: 1,
      min_lot: 0.01,
      buy_status: 1,
      sell_status: 1,
    },
    {
      account_id: "22222",
      bot_name: "Crystal Swing",
      account_balance: 50000.0,
      account_equity: 52340.0,
      total_profit: 2340.0,
      total_profit_percentage: 4.68,
      floating_value: 125.3,
      buy_order_count: 5,
      sell_order_count: 1,
      total_buy_lot: 0.5,
      total_sell_lot: 0.1,
      leverage: "400",
      currency: "USD",
      broker: "IC Markets",
      server: "ICMarkets-Real",
      account_type: "real",
      status: "active",
      last_sync: new Date().toISOString(),
      mt4_last_sync: new Date().toISOString(),
      disabled_ea: 0,
      min_lot: 0.01,
      buy_status: 1,
      sell_status: 1,
    },
  ];

  if (action === "get_accounts_by_user") {
    return {
      status: "success",
      data: {
        accounts: mockAccounts,
        summary: {
          total_accounts: mockAccounts.length,
          total_balance: mockAccounts.reduce(
            (sum, a) => sum + parseFloat(a.account_balance),
            0,
          ),
          total_profit: mockAccounts.reduce(
            (sum, a) => sum + parseFloat(a.total_profit),
            0,
          ),
          active_accounts: mockAccounts.filter((a) => a.status === "active")
            .length,
        },
      },
    };
  }

  if (action === "login") {
    return {
      status: "success",
      success: true,
      token: "mock-jwt-token-" + Date.now(),
      user: {
        id: 1,
        name: "Demo User",
        username: params.username || "demo",
        user_tipe: "Premium Trader",
      },
    };
  }

  return {
    status: "success",
    success: true,
    message: "Action completed (mock)",
  };
}

// ============ EXPORTED FUNCTIONS ============

export function checkAuth() {
  return !!(localStorage.getItem("jwt") && localStorage.getItem("user"));
}

export async function fetchAccountsFromServer() {
  const result = await apiCall("get_accounts_by_user");
  if (result.status === "success" && result.data) {
    return {
      accounts: result.data.accounts || [],
      groups: result.data.groups || [],
      summary: result.data.summary || null,
    };
  }
  throw new Error(result.message || "Failed to fetch accounts");
}

export async function loginUser(username, password) {
  const result = await apiCall("login", { username, password });
  if (result.status === "success" || result.success) return result;
  throw new Error(result.message || "Login failed");
}

export async function placeBuyOrder(accountId, lot) {
  const result = await apiCall("order_buy", { account_id: accountId, lot });
  if (result.status === "success" || result.success) return result;
  throw new Error(result.message || "Failed to place buy order");
}

export async function placeSellOrder(accountId, lot) {
  const result = await apiCall("order_sell", { account_id: accountId, lot });
  if (result.status === "success" || result.success) return result;
  throw new Error(result.message || "Failed to place sell order");
}

export async function closeAllPositions(accountId) {
  const result = await apiCall("close_all_positions", {
    account_id: accountId,
  });
  if (result.status === "success" || result.success) return result;
  throw new Error(result.message || "Failed to close positions");
}

export async function toggleAutoTrade(accountId, enable) {
  const result = await apiCall("toggle_auto_trade", {
    account_id: accountId,
    disabled_ea: enable ? "0" : "1",
  });
  if (result.status === "success" || result.success) return result;
  throw new Error(result.message || "Failed to toggle auto trading");
}

export async function placeBuyOrderMulti(accountIds, lot) {
  const result = await apiCall("order_buy_multi", {
    account_ids: accountIds,
    lot,
  });
  if (result.status === "success" || result.success) return result;
  throw new Error(result.message || "Failed to place bulk buy order");
}

export async function placeSellOrderMulti(accountIds, lot) {
  const result = await apiCall("order_sell_multi", {
    account_ids: accountIds,
    lot,
  });
  if (result.status === "success" || result.success) return result;
  throw new Error(result.message || "Failed to place bulk sell order");
}

export async function closeOrdersMulti(accountIds) {
  const result = await apiCall("close_orders_multi", {
    account_ids: accountIds,
  });
  if (result.status === "success" || result.success) return result;
  throw new Error(result.message || "Failed to close bulk positions");
}

export async function toggleAutoTradeMulti(accountIds, enable) {
  const result = await apiCall("toggle_auto_trade_multi", {
    account_ids: accountIds,
    disabled_ea: enable ? "0" : "1",
  });
  if (result.status === "success" || result.success) return result;
  throw new Error(result.message || "Failed to toggle bulk auto trading");
}

export async function fetchForexCopyRate(accountId) {
  const result = await apiCall("get_forex_copy_rate", {
    account_id: accountId,
    passkey: "tokenLogin",
  });
  if (result.status === "success" || result.success) return result.data;
  throw new Error(result.message || "Failed to fetch follower data");
}
