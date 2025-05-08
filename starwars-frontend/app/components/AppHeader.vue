<!-- componente ...  Navbar -->
<template>
  <header class="custom-starwars bg-black/80 shadow-md h-20 px-6 flex items-center justify-between relative">
    
    <!-- Volver a la izquierda -->
    <div>
      <nuxt-link
        to="/options"
        class="text-yellow-400 transform hover:scale-115 transition duration-200 inline-flex items-center ml-3 ">
        <i class="fa-solid fa-arrow-left text-3xl mr-2"/>
        Volver
      </nuxt-link>

    </div>

    <!-- Bloque central: STAR / WARS perfectamente centrado -->
    <div class="absolute left-1/2 -translate-x-1/2 flex items-center space-x-4 ml-8">
      <div class="text-yellow-400 text-3xl leading-none text-center">
        <nuxt-link to="/options" class="flex flex-col">
          <p>&nbsp;&nbsp;&nbsp;STAR<sup>&reg;</sup></p>
          <p>WARS</p>
        </nuxt-link>
      </div>
      <span class="text-yellow-400 text-2xl">APP</span>
    </div>


  </header>
</template>





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