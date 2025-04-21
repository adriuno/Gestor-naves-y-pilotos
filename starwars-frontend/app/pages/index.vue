<template>
  <div class="relative min-h-screen">
    <!-- Fondo -->
    <div class="absolute inset-0">
      <img src="/images/sw3.jpg" alt="galaxy" class="w-full h-full object-cover brightness-75">
    </div>
 
    <div class="relative z-10">
      <AppHeader />
      <main class="p-8  mx-auto  bg-black/70 rounded-xl text-center">
        <p class="mt-4 text-4xl text-yellow-300 custom-starwars">En una galaxia muy, muy lejana...</p>
        <br><br>
        <p class="text-3xl text-yellow-300 custom-starwars">¡Bienvenido al gestor de</p>
        <p class="text-3xl text-yellow-300 custom-starwars">naves y pilotos!</p> <br>

        <!-- <CardsApp  />  -->
         <CardsApp />
      </main>
      
    </div>
  </div>
</template>



<style>
.custom-starwars {
  font-family: 'Starjedi', sans-serif;
}
</style>