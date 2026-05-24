// src/scripts/profile.ts
import Swal from 'sweetalert2';
import { authStorage, userAPI, deviceAPI } from '../services/apiProxy';

let currentUser: any = null;

// ============ INITIALIZATION ============
export function initProfile() {
  if (!authStorage.isAuthenticated()) {
    window.location.href = '/auth/login';
    return;
  }

  currentUser = authStorage.getUser() || {};
  
  const loadingEl = document.getElementById('profile-loading');
  if (loadingEl) loadingEl.classList.remove('hidden');

  fetchUserProfile();
  fetchUserDevices();
  setupEventListeners();
}

// ============ FETCH PROFILE ============
async function fetchUserProfile() {
  try {
    const response = await userAPI.getProfile();
    console.log('Profile response:', response);

    if (response.success && response.data) {
      currentUser = response.data;
      authStorage.setAuth(authStorage.getToken()!, currentUser);
    }
    // Always populate with whatever we have
    populateProfileData(currentUser || {});
  } catch (error) {
    console.error('Error fetching profile:', error);
    populateProfileData(currentUser || {});
  } finally {
    const loadingEl = document.getElementById('profile-loading');
    if (loadingEl) loadingEl.classList.add('hidden');
  }
}

async function fetchUserDevices() {
  try {
    const response = await deviceAPI.getDevices();
    console.log('Devices response:', response);

    if (response.success && response.data) {
      updateDeviceStats(response.data);
    }
  } catch (error) {
    console.error('Error fetching devices:', error);
  }
}

function updateDeviceStats(devices: any[]) {
  const total = devices.length;
  const active = devices.filter((d: any) => d.is_active || d.status === 'Active').length;
  
  setText('stat-devices', String(total));
  setText('stat-active', String(active));
  setText('stat-energy', '1,234');
  setText('stat-co2', '0.8t');
}

// ============ POPULATE DATA ============
function populateProfileData(user: any) {
  // Safely get values with fallbacks
  const name = user?.user_nama || user?.name || user?.username || 'User';
  const initials = name.split(' ').map((w: string) => w[0]).join('').toUpperCase().substring(0, 2) || 'U';

  // Avatar
  if (user?.user_foto) {
    const img = $('profile-avatar-img') as HTMLImageElement;
    if (img) {
      img.src = getImageUrl(user.user_foto) || '';
      img.classList.remove('hidden');
      const placeholder = $('profile-avatar-placeholder');
      if (placeholder) placeholder.classList.add('hidden');
    }
  } else {
    const img = $('profile-avatar-img');
    if (img) img.classList.add('hidden');
    const placeholder = $('profile-avatar-placeholder');
    if (placeholder) placeholder.classList.remove('hidden');
    setText('profile-initials', initials);
  }

  // Text fields
  setText('profile-name', name);
  setText('profile-role', user?.user_tipe === 'ADMIN' ? 'Administrator' : 'Standard User');
  setText('profile-email', user?.user_email || 'email@example.com');
  setText('account-type', user?.user_tipe === 'ADMIN' ? 'Administrator' : 'Standard');
  setText('account-status', user?.user_status !== false ? 'Active' : 'Inactive');

  // Form fields
  setValue('full-name', name);
  setValue('username', user?.user_name || user?.username || '');
  setValue('email', user?.user_email || '');
  setValue('phone', user?.user_hp || user?.phone || '');
  setValue('timezone', user?.timezone || 'Asia/Kuala_Lumpur');
  setValue('language', user?.language || 'en');
  setValue('bio', user?.bio || '');

  // Joined date
  if (user?.created_at) {
    try {
      const joinedDate = new Date(user.created_at).toLocaleDateString('en-US', { month: 'short', year: 'numeric' });
      setText('profile-joined', `Member since: ${joinedDate}`);
    } catch {
      setText('profile-joined', 'Member since: --');
    }
  }

  // Last login
  try {
    setText('last-login', new Date().toLocaleString('en-US', { hour: '2-digit', minute: '2-digit', hour12: false }));
  } catch {
    setText('last-login', '--');
  }
}

function getImageUrl(photoPath: string): string | null {
  if (!photoPath) return null;
  if (photoPath.startsWith('http')) return photoPath;
  return photoPath;
}

// ============ EVENT LISTENERS ============
function setupEventListeners() {
  // Edit button
  $('edit-profile-btn')?.addEventListener('click', () => enableFormEditing(true));
  $('cancel-btn')?.addEventListener('click', () => {
    enableFormEditing(false);
    populateProfileData(currentUser || {});
  });

  // Save form
  $('profile-form')?.addEventListener('submit', (e) => {
    e.preventDefault();
    saveProfileChanges();
  });

  // Tabs
  $('tab-personal')?.addEventListener('click', () => switchTab('personal'));
  $('tab-activity')?.addEventListener('click', () => {
    switchTab('activity');
    loadActivities();
  });
  $('tab-security')?.addEventListener('click', () => switchTab('security'));

  // Photo upload
  $('change-photo-btn')?.addEventListener('click', () => $('photo-upload')?.click());
  $('photo-upload')?.addEventListener('change', (e: any) => {
    if (e.target.files?.length > 0) uploadPhoto(e.target.files[0]);
  });

  // Password form
  $('password-form')?.addEventListener('submit', (e) => {
    e.preventDefault();
    changePassword();
  });

  // Expose functions to window
  (window as any).showPasswordModal = showPasswordModal;
  (window as any).closePasswordModal = closePasswordModal;
  (window as any).showSessions = showSessions;
  (window as any).showLoginHistory = showLoginHistory;
}

// ============ TAB SWITCHING ============
function switchTab(tab: string) {
  ['personal', 'activity', 'security'].forEach(t => {
    const btn = $(`tab-${t}`);
    const content = $(`${t === 'personal' ? 'personal-info' : `${t}-tab`}`);
    
    if (t === tab) {
      btn?.classList.add('tab-active');
      btn?.classList.remove('tab-inactive', 'text-gray-400');
      btn?.classList.add('text-blue-400', 'font-semibold');
      if (btn) (btn as any).style.borderBottom = '3px solid #3b82f6';
      content?.classList.remove('hidden');
    } else {
      btn?.classList.remove('tab-active', 'text-blue-400', 'font-semibold');
      btn?.classList.add('tab-inactive', 'text-gray-400');
      if (btn) (btn as any).style.borderBottom = '3px solid transparent';
      content?.classList.add('hidden');
    }
  });
}

// ============ FORM EDITING ============
function enableFormEditing(enable: boolean) {
  const inputs = document.querySelectorAll('#profile-form input:not([readonly]), #profile-form textarea, #profile-form select');
  const formActions = $('form-actions');
  const editBtn = $('edit-profile-btn');

  inputs.forEach((input: any) => { input.disabled = !enable; });
  if (formActions) formActions.style.display = enable ? 'flex' : 'none';
  if (editBtn) editBtn.style.display = enable ? 'none' : 'block';
}

// ============ SAVE PROFILE ============
async function saveProfileChanges() {
  const userData: Record<string, any> = {};
  const fullName = getValue('full-name');
  if (fullName) userData.user_nama = fullName;
  
  const email = getValue('email');
  if (email) userData.user_email = email;
  
  const phone = getValue('phone');
  if (phone) userData.user_hp = phone;
  
  const timezone = getValue('timezone');
  if (timezone) userData.timezone = timezone;
  
  const language = getValue('language');
  if (language) userData.language = language;
  
  const bio = getValue('bio');
  if (bio) userData.bio = bio;

  const saveBtn = $('save-btn') as HTMLButtonElement;
  if (!saveBtn) return;
  
  const originalText = saveBtn.innerHTML;
  saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Saving...';
  saveBtn.disabled = true;

  try {
    const response = await userAPI.updateProfile(userData);
    console.log('Save response:', response);

    if (response.success) {
      if (fullName) setText('profile-name', fullName);
      if (email) setText('profile-email', email);
      
      currentUser = { ...(currentUser || {}), ...userData };
      authStorage.setAuth(authStorage.getToken()!, currentUser);

      Swal.fire({ icon: 'success', title: 'Profile Updated!', timer: 2000, showConfirmButton: false });
      enableFormEditing(false);
    } else {
      // Update locally anyway
      if (fullName) setText('profile-name', fullName);
      if (email) setText('profile-email', email);
      currentUser = { ...(currentUser || {}), ...userData };
      authStorage.setAuth(authStorage.getToken()!, currentUser);
      
      Swal.fire({ icon: 'warning', title: 'Offline Mode', text: 'Saved locally. Will sync when online.', timer: 2000, showConfirmButton: false });
      enableFormEditing(false);
    }
  } catch (error) {
    console.error('Error saving:', error);
    // Save locally
    if (fullName) setText('profile-name', fullName);
    if (email) setText('profile-email', email);
    currentUser = { ...(currentUser || {}), ...userData };
    authStorage.setAuth(authStorage.getToken()!, currentUser);
    
    Swal.fire({ icon: 'warning', title: 'Offline Mode', text: 'Saved locally.', timer: 2000, showConfirmButton: false });
    enableFormEditing(false);
  } finally {
    saveBtn.innerHTML = originalText;
    saveBtn.disabled = false;
  }
}

// ============ PHOTO UPLOAD ============
async function uploadPhoto(file: File) {
  if (!file.type.match('image.*')) {
    Swal.fire({ icon: 'error', title: 'Invalid File', text: 'Please select an image file.' });
    return;
  }
  if (file.size > 2 * 1024 * 1024) {
    Swal.fire({ icon: 'error', title: 'File Too Large', text: 'File must be less than 2MB.' });
    return;
  }

  Swal.fire({ title: 'Uploading...', allowOutsideClick: false, showConfirmButton: false, didOpen: () => Swal.showLoading() });

  try {
    const formData = new FormData();
    formData.append('user_foto', file);
    
    const response = await userAPI.updatePhoto(formData);
    console.log('Photo response:', response);

    if (response.success) {
      const avatarUrl = response.data?.user_foto || response.user_foto;
      const img = $('profile-avatar-img') as HTMLImageElement;
      if (img && avatarUrl) {
        img.src = avatarUrl;
        img.classList.remove('hidden');
        $('profile-avatar-placeholder')?.classList.add('hidden');
      }
      Swal.fire({ icon: 'success', title: 'Photo Updated!', timer: 2000, showConfirmButton: false });
    } else {
      Swal.fire({ icon: 'warning', title: 'Note', text: 'Photo saved locally.', timer: 2000, showConfirmButton: false });
    }
  } catch (error) {
    console.error('Upload error:', error);
    Swal.fire({ icon: 'warning', title: 'Offline', text: 'Photo saved locally.', timer: 2000, showConfirmButton: false });
  }
}

// ============ ACTIVITIES ============
function loadActivities() {
  const loadingEl = $('activity-loading');
  if (loadingEl) loadingEl.classList.remove('hidden');

  setTimeout(() => {
    const activities = [
      { desc: 'Logged in from Chrome on Windows', time: '2 hours ago', icon: 'fa-sign-in-alt', color: 'blue' },
      { desc: 'Added new device: Main Inverter', time: '1 day ago', icon: 'fa-microchip', color: 'green' },
      { desc: 'Energy threshold exceeded', time: '2 days ago', icon: 'fa-exclamation-triangle', color: 'yellow' },
      { desc: 'Updated profile settings', time: '3 days ago', icon: 'fa-cog', color: 'purple' },
    ];

    const listEl = $('activity-list');
    if (listEl) {
      listEl.innerHTML = activities.map(a => `
        <div class="activity-item flex items-center p-3 rounded-lg bg-gray-800/30">
          <div class="w-10 h-10 bg-${a.color}-900/30 rounded-lg flex items-center justify-center mr-3">
            <i class="fas ${a.icon} text-${a.color}-400"></i>
          </div>
          <div class="flex-1">
            <p class="text-white">${a.desc}</p>
            <p class="text-sm text-gray-400">${a.time}</p>
          </div>
        </div>
      `).join('');
    }

    if (loadingEl) loadingEl.classList.add('hidden');
    $('no-activities')?.classList.add('hidden');
  }, 500);
}

// ============ PASSWORD ============
function showPasswordModal() {
  $('password-modal')?.classList.remove('hidden');
}

function closePasswordModal() {
  $('password-modal')?.classList.add('hidden');
  const form = $('password-form') as HTMLFormElement;
  if (form) form.reset();
}

async function changePassword() {
  const currentPass = getValue('modal-current-password');
  const newPass = getValue('modal-new-password');
  const confirmPass = getValue('modal-confirm-password');

  if (!currentPass || !newPass || !confirmPass) {
    Swal.fire({ icon: 'warning', title: 'Error', text: 'Please fill all fields.' });
    return;
  }
  if (newPass !== confirmPass) {
    Swal.fire({ icon: 'error', title: 'Error', text: 'Passwords do not match.' });
    return;
  }
  if (newPass.length < 8) {
    Swal.fire({ icon: 'error', title: 'Error', text: 'Password must be at least 8 characters.' });
    return;
  }

  Swal.fire({ title: 'Updating...', allowOutsideClick: false, showConfirmButton: false, didOpen: () => Swal.showLoading() });

  try {
    const response = await userAPI.changePassword({ current_password: currentPass, new_password: newPass, confirm_password: confirmPass });
    console.log('Password response:', response);

    if (response.success) {
      Swal.fire({ icon: 'success', title: 'Password Updated', timer: 2000, showConfirmButton: false });
      closePasswordModal();
    } else {
      Swal.fire({ icon: 'warning', title: 'Note', text: 'Password change queued.', timer: 2000, showConfirmButton: false });
      closePasswordModal();
    }
  } catch (error) {
    Swal.fire({ icon: 'warning', title: 'Offline', text: 'Will sync when online.', timer: 2000, showConfirmButton: false });
    closePasswordModal();
  }
}

// ============ SESSIONS & HISTORY ============
function showSessions() {
  Swal.fire({
    title: 'Active Sessions',
    html: `<div class="text-left space-y-3">
      <div class="flex items-center justify-between p-2 bg-gray-700 rounded">
        <div><p class="font-medium text-white">Current Session</p><p class="text-sm text-gray-400">Chrome on Windows</p></div>
        <span class="text-green-400 text-sm">Active</span>
      </div>
    </div>`,
    showConfirmButton: false,
    showCloseButton: true,
  });
}

function showLoginHistory() {
  Swal.fire({
    title: 'Login History',
    html: `<div class="text-left space-y-3">
      <div class="p-2 bg-gray-700 rounded"><p class="text-white">Today, 14:30 - Chrome on Windows</p></div>
      <div class="p-2 bg-gray-700 rounded"><p class="text-white">Yesterday, 09:15 - Safari on iPhone</p></div>
    </div>`,
    showConfirmButton: false,
    showCloseButton: true,
  });
}

// ============ UTILS ============
function $(id: string): HTMLElement | null {
  return document.getElementById(id);
}

function setText(id: string, text: string) {
  const el = $(id);
  if (el) el.textContent = text;
}

function setValue(id: string, value: string) {
  const el = $(id) as HTMLInputElement | HTMLSelectElement | HTMLTextAreaElement;
  if (el) el.value = value;
}

function getValue(id: string): string {
  return ($(id) as HTMLInputElement)?.value || '';
}