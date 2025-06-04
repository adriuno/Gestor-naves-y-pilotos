import Swal from 'sweetalert2'
import swalDark from './swalDark'

/**
 * Muestra un loader con estilo swalDark y spinner de carga.
 */
export const swalDarkLoading = (title = 'Enviando...', text = 'Procesando solicitud...') => {
  return swalDark.fire({
    icon: 'info',
    title,
    text,
    showConfirmButton: false,
    allowOutsideClick: false,
    didOpen: () => {
      Swal.showLoading(); // el método viene de Swal original
    },
  });
}

/**
 * Muestra una alerta de éxito.
 */
export const swalSuccess = (title = '¡Éxito!', text = 'Operación realizada correctamente.') => {
  return swalDark.fire({
    icon: 'success',
    title,
    text,
    confirmButtonText: 'Aceptar',
  });
}

/**
 * Muestra una alerta de error.
 */
export const swalError = (title = 'Error', text = 'Ha ocurrido un problema.') => {
  return swalDark.fire({
    icon: 'error',
    title,
    text,
    confirmButtonText: 'Aceptar',
  });
}
