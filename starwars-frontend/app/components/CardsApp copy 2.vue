<template>
  <!--    COPIA   buena  -->
  <div class="grid grid-cols-1 sm:grid-cols-2 gap-10 p-6 mx-auto max-w-7xl mt-12">
    <a
      v-for="starship in starships.data"
      :key="starship.id"
      href="#"
      class="flex flex-col items-center bg-gray-900 border border-yellow-700 rounded-2xl shadow-md hover:bg-gray-800 transition duration-300 md:flex-row md:max-w-3xl overflow-hidden w-full"
    >
      <img
        :src="starship.image2_url"
        :alt="starship.name"
        class="object-cover w-full h-64 md:h-60 md:w-72 md:rounded-none md:rounded-s-lg"
      >

      <div class="flex flex-col justify-between p-6 text-gray-300 w-full">
        <h5 class="mb-3 text-3xl font-bold tracking-wide text-yellow-300 text-center md:text-left">
          {{ starship.name }}
        </h5>

        <p class="mb-2 text-lg text-center md:text-left">
          <span class="font-semibold text-blue-400">Modelo:</span> {{ starship.model }}
        </p>

        <p class="mb-4 text-lg text-center md:text-left">
          <span class="font-semibold text-blue-400">Pilotos:</span> {{ starship.pilots.length }}
        </p>

        <button
          class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-blue-700 rounded-lg hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-500 w-full transition"
        >
          Ver detalles
        </button>
      </div>
    </a>
  </div>
</template>

<script setup>
const config = useRuntimeConfig()

const { data: starships } = await useFetch(`${config.public.API_URL}/starships`)
</script>
