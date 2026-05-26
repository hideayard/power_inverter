// src/pages/api/proxy.ts
export const prerender = false;

import type { APIRoute } from "astro";

const API_ENDPOINTS: Record<string, string> = {
  login: "https://itrust-tech.id/web/mobile/login",
  register: "https://itrust-tech.id/web/mobile/register",
  logout: "https://itrust-tech.id/web/mobile/logout",
  forgot_password: "https://itrust-tech.id/web/mobile/forgot-password",
  get_devices: "https://itrust-tech.id/web/mobile/get-devices",
  get_devices_with_data:
    "https://itrust-tech.id/web/mobile/get-devices-with-data",
  create_device: "https://itrust-tech.id/web/mobile/create-device",
  update_device: "https://itrust-tech.id/web/mobile/update-device",
  delete_device: "https://itrust-tech.id/web/mobile/delete-device",
  get_scrape_data: "https://itrust-tech.id/web/mobile/get-latest-scrape-data",
  get_scrape_data_v2:
    "https://itrust-tech.id/web/mobile/get-latest-scrape-data-v2",
  get_user_profile: "https://itrust-tech.id/web/mobile/get-user-profile",
  update_user: "https://itrust-tech.id/web/mobile/update-user",
  change_password: "https://itrust-tech.id/web/mobile/change-password",
  update_photo: "https://itrust-tech.id/web/mobile/update-photo",
  get_user_stats: "https://itrust-tech.id/web/mobile/get-user-stats",
  get_users: "https://itrust-tech.id/web/mobile/get-users",
};

export const POST: APIRoute = async ({ request }) => {
  // ============ CORS HEADERS ============
  const corsHeaders = {
    "Access-Control-Allow-Origin": request.headers.get("origin") || "*",
    "Access-Control-Allow-Credentials": "true",
    "Access-Control-Allow-Methods": "GET, POST, OPTIONS",
    "Access-Control-Allow-Headers": "Content-Type, Authorization",
  };

  try {
    // Parse request
    const contentType = request.headers.get("content-type") || "";
    let action: string;
    let bodyData: Record<string, any> = {};

    if (contentType.includes("application/json")) {
      const json = await request.json();
      action = json.action || "login";
      bodyData = { ...json };
      delete bodyData.action;
    } else if (
      contentType.includes("multipart/form-data") ||
      contentType.includes("application/x-www-form-urlencoded")
    ) {
      const formData = await request.formData();
      action = formData.get("action")?.toString() || "login";
      formData.forEach((value, key) => {
        if (key !== "action") bodyData[key] = value;
      });
    } else {
      const text = await request.text();
      try {
        const json = JSON.parse(text);
        action = json.action || "login";
        bodyData = { ...json };
        delete bodyData.action;
      } catch {
        action = "login";
      }
    }

    const apiUrl = API_ENDPOINTS[action] || API_ENDPOINTS["login"];
    const authHeader = request.headers.get("Authorization");

    console.log(
      `Proxy: ${action} -> ${apiUrl} | Auth: ${authHeader ? "Yes" : "No"}`,
    );

    // Build request to external API
    const fetchOptions: RequestInit = {
      method: "POST",
      headers: {
        Accept: "application/json",
        ...(authHeader ? { Authorization: authHeader } : {}),
      },
    };

    // Send as form data to external API (matches PHP version)
    const externalFormData = new FormData();
    Object.entries(bodyData).forEach(([key, value]) => {
      externalFormData.append(key, String(value));
    });
    fetchOptions.body = externalFormData;

    // Make request to external API
    const response = await fetch(apiUrl, fetchOptions);
    const responseText = await response.text();

    let responseData: any;
    try {
      responseData = JSON.parse(responseText);
    } catch {
      responseData = { raw: responseText };
    }

    if (typeof responseData === "object") {
      responseData.action = action;
    }

    console.log(
      `Proxy Response: ${response.status} | Success: ${responseData.success}`,
    );

    return new Response(JSON.stringify(responseData), {
      status: response.status,
      headers: {
        "Content-Type": "application/json",
        ...corsHeaders,
      },
    });
  } catch (error) {
    console.error("Proxy error:", error);
    return new Response(
      JSON.stringify({ success: false, message: "Internal server error" }),
      {
        status: 500,
        headers: { "Content-Type": "application/json", ...corsHeaders },
      },
    );
  }
};

// Handle OPTIONS preflight
export const OPTIONS: APIRoute = async ({ request }) => {
  return new Response(null, {
    status: 204,
    headers: {
      "Access-Control-Allow-Origin": request.headers.get("origin") || "*",
      "Access-Control-Allow-Credentials": "true",
      "Access-Control-Allow-Methods": "GET, POST, OPTIONS",
      "Access-Control-Allow-Headers": "Content-Type, Authorization",
    },
  });
};
