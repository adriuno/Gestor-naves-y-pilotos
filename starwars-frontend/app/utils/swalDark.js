import Swal from 'sweetalert2'

const swalDark = Swal.mixin({
  background: '#1e293b', // Gris azulado (Tailwind: slate-800)
  color: '#facc15',       // Amarillo Jedi
  iconColor: '#facc15',   // Íconos dorados brillantes
  confirmButtonColor: '#facc15',
  cancelButtonColor: '#64748b', // Azul grisáceo

  customClass: {
    popup: 'rounded-2xl shadow-2xl border border-yellow-500',
    title: 'text-2xl text-yellow-300 font-starwars tracking-widest',
    htmlContainer: 'text-white text-base',
    confirmButton: 'bg-yellow-400 text-black px-6 py-2 rounded-lg font-bold shadow hover:scale-105 transition-transform',
    cancelButton: 'bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600',
  },

  showClass: {
    popup: 'animate__animated animate__zoomIn',
  },
  hideClass: {
    popup: 'animate__animated animate__fadeOut',
  },
})

export default swalDark
