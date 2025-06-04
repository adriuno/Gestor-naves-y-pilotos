<template>
  <div class="relative min-h-screen">
    <!-- Fondo -->
    <div class="absolute inset-0">
      <img
        src="/images/sw3.jpg"
        alt="galaxy"
        class="w-full h-full object-cover brightness-75"
      >
    </div>

    <div class="relative z-10">
      <AppHeader />
      <main class="p-8 mx-auto bg-black/70 rounded-xl text-center">
        <h1
          id="introTitle"
          class="mt-4 text-5xl text-yellow-300 custom-starwars"
        >
          En una galaxia muy, muy lejana...
        </h1>
        <br ><br >
        <h2 class="text-3xl text-yellow-300 custom-starwars">
          ¡Bienvenido al gestor de
        </h2>
        <h2 class="text-3xl text-yellow-300 custom-starwars">
          naves y pilotos!
        </h2>
        <br >

        <!-- <CardsApp  />  -->
        <CardsApp />
        <AppFooter />
      </main>
    </div>
  </div>
</template>

<script setup>
definePageMeta({
  middleware: "auth",
});

useHead({
  title: "Gestor | Naves",
  htmlAttrs: {
    lang: "es",
  },
  link: [
    {
      rel: "icon",
      type: "image/png",
      href: "/images/logo/sw4.webp", // Asegúrate que esta ruta sea correcta y el archivo exista
    }
  ]
});


onMounted(() => {
  const title = document.getElementById("introTitle");
  title?.focus();
});
</script>

<style>
.custom-starwars {
  font-family: "Starjedi", sans-serif;
}
</style>
