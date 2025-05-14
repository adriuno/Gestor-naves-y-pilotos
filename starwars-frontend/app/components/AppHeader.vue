<!-- componente ...  Navbar -->
<template>
  <header class="custom-starwars bg-black/80 shadow-md h-20 px-6 flex items-center justify-between relative">
    
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
      <span class="text-yellow-400 text-2xl mr-2">APP</span>
    </div>

    <div>
      <UButton
            class="absolute py-2 sm:py-3 sm:px-3 sm:text-2xl top-4 sm:top-5 right-2 sm:right-11 bg-red-600 rounded-3xl
              hover:bg-red-700 hover:scale-115 transition-transform z-10
              hover:drop-shadow-[0_0_8px_#38bdf8] z-10 transition duration-200"
        
      @click="cerrarSesion">
        <i class="fa-solid fa-power-off "/>      
      </UButton>
    </div>

  </header>
</template>

<script setup>

  const cerrarSesion = async () => {

    const token = localStorage.getItem('token')

    try {
    await $fetch('http://localhost:8000/api/logout', {
      method: 'POST',
      headers: {
        Authorization: `Bearer ${token}`,
      },
    })

    // Limpias el token
    localStorage.removeItem('token')

    // Rediriges
    navigateTo('/login')
  } catch (error) {
    console.error('Error al cerrar sesión:', error)
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