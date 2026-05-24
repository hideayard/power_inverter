import Swal from 'sweetalert2'

export const SwalHelper = {
  success(title: string, text: string) {
    return Swal.fire({
      icon: 'success',
      title,
      text
    })
  },

  error(title: string, text: string) {
    return Swal.fire({
      icon: 'error',
      title,
      text
    })
  }
}