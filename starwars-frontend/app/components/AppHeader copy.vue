<!-- components/AppNavbar.vue -->
<template>
  <header class="bg-black/80 shadow-md">
    <div class="custom-starwars max-w-7xl mx-auto px-4 py-4 flex justify-content-center">
      <div class="text-2xl font-bold text-yellow-400">
         StarWars App
      </div>
      <!-- <UNavigationMenu
        :items="items"
        variant="link"
        highlight
        class="flex gap-4" -->

      />
    </div>
  </header>
</template>

<script setup lang="ts">
// import type { NavigationMenuItem } from '@nuxt/ui'

// const items = ref<NavigationMenuItem[][]>([
//   [
//     { label: 'Inicio', to: '/' },
//     { label: 'Sobre', to: '/about' },
//     { label: 'Contacto', to: '/contact' }
//   ]
// ])
</script>


<style>
.custom-starwars {
  font-family: 'Starjedi', sans-serif;
}
</style>