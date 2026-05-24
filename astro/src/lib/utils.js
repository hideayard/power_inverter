import { SwalHelper } from "./swal.js";

const BASE_URL = 'https://itrust-tech.id/web';

// Card gradient palette
export const CARD_GRADIENTS = [
  "linear-gradient(135deg,#667eea 0%,#764ba2 100%)",
  "linear-gradient(135deg,#f59e0b 0%,#d97706 100%)",
  "linear-gradient(135deg,#ef4444 0%,#dc2626 100%)",
  "linear-gradient(135deg,#10b981 0%,#059669 100%)",
  "linear-gradient(135deg,#6b7280 0%,#4b5563 100%)",
  "linear-gradient(135deg,#8b5cf6 0%,#7c3aed 100%)",
  "linear-gradient(135deg,#3b82f6 0%,#1d4ed8 100%)",
  "linear-gradient(135deg,#ec4899 0%,#be185d 100%)",
];

// Format money values
export function formatMoney(val) {
  const num = parseFloat(val) || 0;
  const n = Math.abs(num);
  const sign = num < 0 ? "-" : "";
  if (n >= 1000000) return sign + "$" + (n / 1000000).toFixed(2) + "M";
  if (n >= 1000) return sign + "$" + (n / 1000).toFixed(1) + "k";
  return sign + "$" + n.toFixed(2);
}

// Format relative time
export function formatLastSync(lastSync) {
  if (!lastSync) return "–";
  const date = new Date(lastSync);
  const now = new Date();
  const diffMs = now - date;
  if (diffMs < 0)
    return date.toLocaleTimeString([], { hour: "2-digit", minute: "2-digit" });
  const diffSec = Math.floor(diffMs / 1000);
  const diffMin = Math.floor(diffSec / 60);
  const diffHr = Math.floor(diffMin / 60);
  const diffDay = Math.floor(diffHr / 24);
  if (diffSec < 60) return "just now";
  if (diffMin < 60) return diffMin + "m ago";
  if (diffHr < 24) return diffHr + "h ago";
  if (diffDay < 7) return diffDay + "d ago";
  if (diffDay < 30) return Math.floor(diffDay / 7) + "w ago";
  return Math.floor(diffDay / 30) + "mo ago";
}

// Parse JWT token
export function parseJwt(token) {
  try {
    const base64Url = token.split(".")[1];
    const base64 = base64Url.replace(/-/g, "+").replace(/_/g, "/");
    const jsonPayload = decodeURIComponent(
      atob(base64)
        .split("")
        .map((c) => "%" + ("00" + c.charCodeAt(0).toString(16)).slice(-2))
        .join(""),
    );
    return JSON.parse(jsonPayload);
  } catch (e) {
    return null;
  }
}

// Get user ID from JWT
export function getUserIdFromJwt() {
  const token = localStorage.getItem("jwt");
  if (!token) return null;
  const payload = parseJwt(token);
  return (
    payload?.data?.user_id ?? payload?.data?.id ?? payload?.data?.sub ?? null
  );
}

// ⭐ SINGLE logout function - only declared ONCE ⭐
export function logout() {
  SwalHelper.fire({
    title: "Sign Out?",
    text: "Are you sure you want to sign out?",
    icon: "question",
    showCancelButton: true,
    confirmButtonColor: "#667eea",
    cancelButtonColor: "#764ba2",
    confirmButtonText: "Yes, sign out",
  }).then((result) => {
    if (result.isConfirmed) {
      localStorage.clear();
      window.location.href = "/login";
    }
  });
}

// Navigate to page
export function navigateTo(page) {
  const pages = {
    dashboard: "/",
    accounts: "/accounts",
    trading: "/trading",
    history: "/history",
    analytics: "/analytics",
    alerts: "/alerts",
    profile: "/profile",
    settings: "/settings",
  };
  if (pages[page]) {
    window.location.href = pages[page];
  }
}

/**
 * Check if JWT token is expired
 * Returns true if expired, false if valid
 */
export function isTokenExpired(token) {
  if (!token) return true;

  try {
    const payload = parseJwt(token);
    if (!payload || !payload.exp) {
      // No expiration claim - check our stored expiry
      const storedExpiry = localStorage.getItem("jwt_expires");
      if (storedExpiry) {
        return Date.now() > parseInt(storedExpiry);
      }
      return false; // Can't determine, assume valid
    }

    // JWT exp is in seconds, Date.now() is in milliseconds
    const expiryTime = payload.exp * 1000;
    return Date.now() > expiryTime;
  } catch (e) {
    return true; // Error parsing = treat as expired
  }
}

/**
 * Check auth and redirect to login if invalid
 * Returns true if valid, redirects if not
 */
export function requireAuth() {
  const token = localStorage.getItem("jwt");
  const user = localStorage.getItem("user");

  if (!token || !user) {
    redirectToLogin();
    return false;
  }

  if (isTokenExpired(token)) {
    redirectToLogin("Session expired. Please login again.");
    return false;
  }

  return true;
}

function redirectToLogin(message) {
  localStorage.removeItem("jwt");
  localStorage.removeItem("user");
  localStorage.removeItem("lastLogin");
  localStorage.removeItem("jwt_expires");

  if (message && typeof Swal !== "undefined") {
    SwalHelper.fire({
      icon: "warning",
      title: "Session Expired",
      text: message,
      timer: 2000,
      showConfirmButton: false,
    }).then(() => {
      window.location.href = "/login";
    });
  } else {
    window.location.href = "/login";
  }
}


/**
 * Get full photo URL from relative path
 */
export function getPhotoUrl(photo) {
  if (!photo) return '';
  // If already a full URL, return as-is
  if (photo.startsWith('http://') || photo.startsWith('https://')) {
    return photo;
  }
  // Prepend base URL for relative paths like "uploads/faisal.png"
  return `${BASE_URL}/${photo}`;
}