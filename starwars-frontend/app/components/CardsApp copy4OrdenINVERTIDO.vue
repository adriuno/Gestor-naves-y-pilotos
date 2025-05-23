<template>
  <div class="grid grid-cols-1 sm:grid-cols-2 gap-10 p-6 mx-auto max-w-7xl mt-8">

    <!-- GRID DE LAS CARDS -->
    <div
      v-for="starship in paginatedStarships"
      :key="starship.id"
      href="#"
      class="flex flex-col bg-gray-900 border border-yellow-700 rounded-xl shadow-md hover:bg-gray-800 transition duration-300 overflow-hidden w-full min-h-[400px]"
      >
      <!-- Imagen siempre en proporción -->
      <div class="w-full h-60 overflow-hidden">
        <img
          :src="starship.image2_url"
          :alt="starship.name"
          class="object-cover w-full h-full"
        >
      </div>

      <!-- Contenido -->
      <div class="flex flex-col justify-between p-4 text-gray-300 flex-1">
        <h5 class="mb-2 text-2xl font-bold tracking-wide text-yellow-300 text-center">
          {{ starship.name }}
        </h5>

        <p class="mb-1 text-center text-blue-400">
          Modelo: {{ starship.model }}
        </p>

        <p class="mb-4 text-center text-blue-400">
          Pilotos: {{ starship.pilots.length }}
        </p>

        <button
          class="mt-auto w-full px-4 py-2 text-sm font-medium text-white bg-blue-700 rounded-lg hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-500 transition"
          @click="openModal(starship)"
        >
          Ver detalles
        </button>
      </div>
    </div>



    </div>

    <!-- Modal -->
    <div
      v-if="showModal"
      class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50"
      @click.self="closeModal"
    >
      <div class="bg-white rounded-lg p-6 max-w-md w-full">
        <h3 class="text-2xl font-bold text-center mb-4">{{ modalStarship.name }}</h3>
        <img :src="modalStarship.image2_url" alt="Image" class="w-full mb-4" >
        <p><strong>Modelo:</strong> {{ modalStarship.model }}</p>
        <p><strong>Fabricante:</strong> {{ modalStarship.manufacturer }}</p>
        <p><strong>Coste:</strong> {{ modalStarship.cost_in_credits }}</p>
        <p><strong>Clase:</strong> {{ modalStarship.starship_class }}</p>

        <!-- Mostrar Pilotos -->
        <div class="mt-4">
          <h4 class="text-xl font-semibold mb-2">Pilotos:</h4>
          <div class="grid grid-cols-2 gap-4">
            <div v-for="pilot in modalStarship.pilots" :key="pilot.id" class="flex items-center">
              <img
                :src="pilot.image_url"
                alt="Pilot Image"
                class="w-12 h-12 rounded-full object-cover mr-2"
              >
              <span class="text-sm text-gray-700">{{ pilot.name }}</span>
            </div>
          </div>
    </div>

      <button
        class="mt-4 w-full bg-red-600 text-white py-2 rounded-lg hover:bg-red-700"
        @click="closeModal"
      >
        Cerrar
      </button>
    </div>
  </div>

  <!-- Paginación -->
  <div class="flex justify-center gap-4 mt-5 custom-starwars">
    <button
      :disabled="page === 1"
      class="px-4 py-2 bg-yellow-400 text-black rounded-3xl disabled:opacity-50"
      @click="page--"
    >
      Anterior
    </button>

    <button
      v-for="paginaActual in totalPages"
      :key="paginaActual"
      :class="[ 
        'px-3 py-1 rounded',
        page === paginaActual ? 'bg-yellow-400 text-black rounded-full' : ' text-white'
      ]"
      @click="page = paginaActual"
    >
      {{ paginaActual }}
    </button>

    <button
      :disabled="page === totalPages"
      class="px-4 py-2 bg-yellow-400 text-black rounded-3xl disabled:opacity-50"
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

// Estado para la modal
const showModal = ref(false)
const modalStarship = ref({})

// Función para abrir la modal y mostrar los detalles de la nave
const openModal = (starship) => {
  modalStarship.value = starship
  showModal.value = true
}

// Función para cerrar la modal
const closeModal = () => {
  showModal.value = false
}
</script>
