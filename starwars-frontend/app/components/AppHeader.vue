<!-- componente ...  Navbar -->
<template>
  <header class="custom-starwars bg-black/80 shadow-md relative flex justify-center items-center h-20">
    
    <!-- STAR / WARS perfectamente centrado -->
    <div class="absolute left-1/2 -translate-x-1/2 flex flex-col leading-none text-yellow-400 text-3xl text-center">
      <p>&nbsp;&nbsp;&nbsp;STAR<sup> &reg;</sup></p>
      <p>WARS</p>
    </div>

    <!-- APP a la derecha, con margen -->
    <p class="text-yellow-400 text-2xl ml-64">APP</p>

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