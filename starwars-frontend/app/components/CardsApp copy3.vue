<template>
  <div class="grid grid-cols-1 sm:grid-cols-2 gap-10 p-6 mx-auto max-w-7xl mt-8">


    <!--      Copia que FUNCIONA  bienn!!!      -->


    <!-- GRID DE LAS CARDS -->
    <a
      v-for="starship in paginatedStarships"
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

  <!-- Paginación -->
  <div class="flex justify-center gap-4 mt-6">
    <button
      :disabled="page === 1"
      @click="page--"
      class="px-4 py-2 bg-yellow-600 text-white rounded disabled:opacity-50"
    >
      Anterior
    </button>

    <button
      v-for="n in totalPages"
      :key="n"
      
      :class="[
        'px-3 py-1 rounded',
        page === n ? 'bg-yellow-700 text-white' : 'bg-gray-300 text-black'
      ]" @click="page = n"
    >
      {{ n }}
    </button>

    <button
      :disabled="page === totalPages"
      
      class="px-4 py-2 bg-yellow-600 text-white rounded disabled:opacity-50"
      @click="page++"
    >
      Siguiente
    </button>
  </div>

</template>

<script setup>
const config = useRuntimeConfig()

// Cargar datos de las naves
const { data: starships } = await useFetch(`${config.public.API_URL}/starships`)

const page = ref(1)
const perPage = 4 // Número de items por página

// Computed property para obtener las naves paginadas
const paginatedStarships = computed(() => {
  if (!starships.value || !Array.isArray(starships.value.data)) {
    return [] // Si no tenemos datos o si no es un array válido, devolvemos un array vacío.
  }
  const start = (page.value - 1) * perPage
  return starships.value.data.slice(start, start + perPage)
})

// Calcular el total de páginas
const totalPages = computed(() => {
  return Math.ceil(starships.value.data.length / perPage)
})
</script>
