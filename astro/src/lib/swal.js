// Import SweetAlert2 directly from node_modules
import Swal from 'sweetalert2';

// Export Swal directly
export default Swal;

// Convenience helpers
export function showSwal(options) {
  return Swal.fire(options);
}

export const SwalHelper = {
  fire(options) {
    return Swal.fire(options);
  },
  
  success(title, text) {
    return Swal.fire({ 
      icon: 'success', 
      title, 
      text, 
      timer: 2000, 
      showConfirmButton: false 
    });
  },
  
  error(title, text) {
    return Swal.fire({ icon: 'error', title, text });
  },
  
  warning(title, text) {
    return Swal.fire({ icon: 'warning', title, text });
  },
  
  info(title, text) {
    return Swal.fire({ icon: 'info', title, text });
  },
  
  question(title, text, confirmText = 'Yes', cancelText = 'Cancel') {
    return Swal.fire({
      title,
      text,
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: confirmText,
      cancelButtonText: cancelText,
    });
  },
  
  loading(title, text = 'Please wait...') {
    return Swal.fire({
      title,
      text,
      allowOutsideClick: false,
      didOpen: () => Swal.showLoading(),
    });
  },
  
  close() {
    Swal.close();
  },
};