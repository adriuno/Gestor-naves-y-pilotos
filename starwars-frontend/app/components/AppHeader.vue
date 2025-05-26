<!-- eslint-disable vue/html-self-closing -->
<!-- componente ...  Navbar -->
<template>
  <header class="custom-starwars bg-black/80 shadow-md h-20 z-50 px-6 flex items-center justify-between relative">
    
    <!-- Volver a la izquierda -->
    <div>
    <nuxt-link
      to="/options"
      class="text-yellow-400 transform hover:scale-115 transition duration-200 flex flex-col sm:flex-row items-center ml-3 text-center">
      <i class="fa-solid fa-arrow-left text-3xl sm:mr-2 mb-1 sm:mb-0" />
      <span class="text-sm sm:text-base">Volver</span>
    </nuxt-link>


    </div>

    <!-- Bloque central: STAR / WARS bien centrado -->
    <div class="absolute left-1/2 -translate-x-1/2 flex items-center space-x-4 ml-8">
      <div class="text-yellow-400 text-3xl leading-none text-center">
        <nuxt-link to="/options" class="flex flex-col">
          <p>&nbsp;&nbsp;&nbsp;STAR<sup>&reg;</sup></p>
          <p>WARS</p>
        </nuxt-link>
      </div>
    <span class="text-yellow-400 text-xs sm:text-xl md:text-2xl mr-2">APP</span>
    </div>

  <!-- Contenedor de nombre y logout -->
  <div class="absolute top-4 right-2 sm:top-5 sm:right-11 z-10 flex flex-col item-center sm:flex-row items-end sm:items-center gap-1 sm:gap-4">

    <!-- Nombre de usuario -->
    <div
      v-if="userName"
      class="text-white text-xs sm:text-sm bg-gray-800 bg-opacity-80 px-3 py-1 rounded-full border border-yellow-500 flex items-center"
      style="font-size: 14px"
      >
      <i class="fa-solid fa-user text-yellow-400 mr-2"></i>
      {{ userName }}
    </div>

    <!-- Botón logout -->
    <UButton
      class="py-1 sm:py-3 sm:px-3 sm:text-2xl bg-red-600 rounded-3xl
            hover:bg-red-700 hover:scale-115 transition-transform
            hover:drop-shadow-[0_0_8px_#38bdf8] duration-200"
      @click="cerrarSesion"
    >
      <i class="fa-solid fa-power-off" />
    </UButton>
  </div>


  </header>
</template>

<script setup>

import Swal from 'sweetalert2'

const userName = ref('')
try {
  const user = JSON.parse(localStorage.getItem('user'))
  userName.value = user?.username || ''
} catch (e) {
  console.warn('Error al leer usuario:', e)
}


const cerrarSesion = async () => {
  const confirm = await Swal.fire({
    title: '¿Cerrar sesión?',
    text: 'Vas a salir de la aplicación',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Sí, cerrar',
    cancelButtonText: 'Cancelar',
    confirmButtonColor: '#d33',
    cancelButtonColor: '#3085d6',
  })

  if (!confirm.isConfirmed) return

  const token = localStorage.getItem('token')

  try {
    await $fetch('http://localhost:8000/api/logout', {
      method: 'POST',
      headers: {
        Authorization: `Bearer ${token}`,
      },
    })

    localStorage.removeItem('token')

    // Redirigir directamente sin mostrar mensaje
    navigateTo('/login')

  } catch (error) {
    console.error('Error al cerrar sesión:', error)
    Swal.fire({
      icon: 'error',
      title: 'Error',
      text: 'No se pudo cerrar la sesión. Inténtalo de nuevo.',
    })
  }
}



</script>



<style>
.custom-starwars {
  font-family: 'Starjedi', sans-serif;
}

header {
  position: sticky;
  top: 0;
  z-index: 1;
}
</style>