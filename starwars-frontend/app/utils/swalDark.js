import Swal from 'sweetalert2'

const swalDark = Swal.mixin({
  background: '#52357B', 

  color: '#facc15',
  iconColor: '#facc15',
  confirmButtonColor: '#facc15',
  cancelButtonColor: '#64748b',

  customClass: {
    popup: 'w-full rounded-2xl border-2 border-yellow-500 bg-gradient-to-b from-blue-900 via-blue-800 to-blue-600 p-6',
    title: 'text-xl text-yellow-300 custom-starwars tracking-widest',
    htmlContainer: 'text-white text-base',
    confirmButton: 'bg-yellow-400 text-black px-6 py-2 rounded-lg font-bold hover:scale-105 transition-transform',
    cancelButton: 'bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 transition-colors',
  },

  showClass: {
    popup: 'animate__animated animate__zoomIn',
  },
  hideClass: {
    popup: 'animate__animated animate__fadeOut',
  },
})

export default swalDark
