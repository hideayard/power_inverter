// src/services/apiProxy.ts

const API_ENDPOINTS: Record<string, string> = {
  login: 'https://itrust-tech.id/web/mobile/login',
  register: 'https://itrust-tech.id/web/mobile/register',
  logout: 'https://itrust-tech.id/web/mobile/logout',
  forgot_password: 'https://itrust-tech.id/web/mobile/forgot-password',
  get_devices: 'https://itrust-tech.id/web/mobile/get-devices',
  get_devices_with_data: 'https://itrust-tech.id/web/mobile/get-devices-with-data',
  create_device: 'https://itrust-tech.id/web/mobile/create-device',
  update_device: 'https://itrust-tech.id/web/mobile/update-device',
  delete_device: 'https://itrust-tech.id/web/mobile/delete-device',
  get_scrape_data: 'https://itrust-tech.id/web/mobile/get-latest-scrape-data',
  get_scrape_data_v2: 'https://itrust-tech.id/web/mobile/get-latest-scrape-data-v2',
  get_user_profile: 'https://itrust-tech.id/web/mobile/get-user-profile',
  update_user: 'https://itrust-tech.id/web/mobile/update-user',
  change_password: 'https://itrust-tech.id/web/mobile/change-password',
  update_photo: 'https://itrust-tech.id/web/mobile/update-photo',
  get_user_stats: 'https://itrust-tech.id/web/mobile/get-user-stats',
  get_users: 'https://itrust-tech.id/web/mobile/get-users',
};

export interface ApiResponse {
  success: boolean;
  message?: string;
  action?: string;
  token?: string;
  user?: any;
  data?: any;
  [key: string]: any;
}

export interface ProxyOptions {
  action: string;
  data?: Record<string, any>;
  method?: 'GET' | 'POST';
}

// ============ AUTH STORAGE ============
export const authStorage = {
  setAuth(token: string, user: any, expiresIn?: number) {
    localStorage.setItem('auth_token', token);
    localStorage.setItem('auth_user', JSON.stringify(user));
    if (expiresIn) {
      localStorage.setItem('auth_token_expires', (Date.now() + expiresIn).toString());
    }
  },

  getToken(): string | null {
    return localStorage.getItem('auth_token');
  },

  getUser(): any {
    try {
      const userStr = localStorage.getItem('auth_user');
      return userStr ? JSON.parse(userStr) : null;
    } catch {
      return null;
    }
  },

  isAuthenticated(): boolean {
    const token = this.getToken();
    if (!token) return false;
    const expires = localStorage.getItem('auth_token_expires');
    if (expires && Date.now() > parseInt(expires)) {
      this.clearAuth();
      return false;
    }
    return true;
  },

  clearAuth() {
    localStorage.removeItem('auth_token');
    localStorage.removeItem('auth_user');
    localStorage.removeItem('auth_token_expires');
  },
};

// ============ PROXY REQUEST (goes through /api/proxy) ============
export async function proxyRequest(options: ProxyOptions): Promise<ApiResponse> {
  const { action, data = {}, method = 'POST' } = options;

  try {
    const token = authStorage.getToken();

    // IMPORTANT: Call YOUR Astro API endpoint, not the external API directly
    const response = await fetch('/api/proxy', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        // Pass auth token if available
        ...(token ? { 'Authorization': `Bearer ${token}` } : {}),
      },
      body: JSON.stringify({
        action,
        ...data,
      }),
    });

    if (!response.ok) {
      const errorData = await response.json().catch(() => ({}));
      return {
        success: false,
        message: errorData.message || `HTTP ${response.status}`,
        action,
      };
    }

    const responseData = await response.json();
    responseData.action = action;
    return responseData;
  } catch (error) {
    console.error('Proxy error:', error);
    return {
      success: false,
      message: error instanceof Error ? error.message : 'Network error',
      action,
    };
  }
}

// ============ AUTH API ============
export const authAPI = {
  login: async (username: string, password: string) => {
    const response = await proxyRequest({
      action: 'login',
      data: { username, password },
    });

    if (response.success && response.token) {
      authStorage.setAuth(response.token, response.user || { username }, 24 * 60 * 60 * 1000);
    }

    return response;
  },

  register: async (userData: Record<string, any>) => {
    const response = await proxyRequest({
      action: 'register',
      data: userData,
    });

    if (response.success && response.token) {
      authStorage.setAuth(response.token, response.user || userData, 24 * 60 * 60 * 1000);
    }

    return response;
  },

  logout: async () => {
    const response = await proxyRequest({ action: 'logout' });
    authStorage.clearAuth();
    return response;
  },

  forgotPassword: async (email: string) => {
    return proxyRequest({ action: 'forgot_password', data: { email } });
  },
};

// ============ DEVICE API ============
export const deviceAPI = {
  getDevices: async () => proxyRequest({ action: 'get_devices' }),
  getDevicesWithData: async () => proxyRequest({ action: 'get_devices_with_data' }),
  getScrapeData: async () => proxyRequest({ action: 'get_scrape_data' }),
};

// ============ USER API ============
export const userAPI = {
  getProfile: async () => proxyRequest({ action: 'get_user_profile' }),
  updateProfile: async (data: Record<string, any>) => proxyRequest({ action: 'update_user', data }),
  changePassword: async (data: Record<string, any>) => proxyRequest({ action: 'change_password', data }),
  updatePhoto: async (formData: FormData) => {
    const token = authStorage.getToken();
    formData.append('action', 'update_photo');

    const response = await fetch('/api/proxy', {
      method: 'POST',
      headers: token ? { 'Authorization': `Bearer ${token}` } : {},
      body: formData,
    });

    return response.json();
  },
};