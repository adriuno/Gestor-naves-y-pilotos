<template>

  <div class="grid grid-cols-1 sm:grid-cols-2 gap-10 p-6 mx-auto max-w-7xl mt-8">

     <!-- GRID DE LAS CARDS -->
     <div
        v-for="starship in paginatedStarships"
      :key="starship.id"
      href="#"
      class="flex flex-col md:flex-row bg-gray-900 border border-yellow-700 rounded-xl shadow-md hover:bg-gray-800 transition duration-300 overflow-hidden w-full min-h-[300px]"
      >
      <!-- Imagen izquierda -->   
      <!--   y ojo, w-[22rem]  sería un término medio entre w-80 y w-96 ya que tailwind aquí permite adaptar valores  ....  -->
      <div class="w-full md:w-[22rem]  h-60 md:h-auto overflow-hidden">
        <img
          :src="starship.image2_url"
          :alt="starship.name"
          class="object-cover w-full h-full transition-transform duration-300 hover:scale-120 "
          >
      </div>

      <!-- Contenido derecha -->
      <div class="flex flex-col justify-between p-4 text-gray-300 flex-1">
        <h5 class="mb-2 text-2xl font-bold tracking-wide text-yellow-300 text-center md:text-left">
          {{ starship.name }}
        </h5>

        <p class="mb-1 text-center md:text-left text-blue-400">
          Modelo: {{ starship.model }}
        </p>

        <p class="mb-4 text-center md:text-left text-blue-400">
          Pilotos: {{ starship.pilots.length }}
        </p>

        <div class="mt-auto flex justify-center">
          <button
            class="px-4 py-2 text-sm font-medium text-white bg-blue-700 rounded-lg hover:bg-blue-76500 focus:ring-4 focus:outline-none focus:ring-blue-500 transition"
            @click="openModal(starship)"
          >
            + Detalles
          </button>
        </div>

        <div class="mt-4 flex flex-col items-center gap-4 w-full">
  <!-- Botones visibles solo si el formulario NO está abierto -->
  <div v-if="showPilotForm !== starship.id" class="flex justify-center gap-4 w-full">
    <button
      class="px-4 py-2 text-sm font-medium text-white bg-green-700 rounded-lg hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-500 transition w-full"
      @click="fetchAvailablePilots(starship.id)"
    >
      Añadir piloto
    </button>

    <button
      class="px-4 py-2 text-sm font-medium text-white bg-red-700 rounded-lg hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-500 transition w-full"
      @click="deletePilot(starshipId, pilotId)"
      >
      Eliminar piloto
    </button>
  </div>

  <!-- Formulario visible solo si está activo para esta nave -->
  <div v-if="showPilotForm === starship.id" class="space-y-2 w-full">
    <select
      v-model="selectedPilotId"
      class="w-full p-2 rounded bg-gray-800 text-white border border-gray-600"
    >
      <option disabled value="">Selecciona un piloto</option>
      <option v-for="pilot in availablePilots" :key="pilot.id" :value="pilot.id">
        {{ pilot.name }}
      </option>
    </select>

    <div class="flex gap-2">
      <!-- Confirmar -->
      <button
        :disabled="!selectedPilotId"
        class="flex-1 px-4 py-2 bg-yellow-600 text-white rounded hover:bg-yellow-500 transition"
        @click="addPilotToStarship(starship.id)"
      >
        Añadir
      </button>

      <!-- Cancelar -->
      <button
        class="flex-1 px-4 py-2 bg-red-600 text-white rounded hover:bg-red-500 transition"
        @click="cancelar"
      >
        Cancelar
      </button>
    </div>
  </div>
</div>

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


// ------------------ Añadir PILOTOS ------------------



const loadStarships = async () => {
  try {
    const res = await $fetch(`${config.public.API_URL}/starships`)
    starships.value = res
  } catch (error) {
    console.error('Error al cargar starships', error)
  }
}

// 🔁 Carga inicial
await loadStarships()


const availablePilots = ref([])

const selectedPilotId = ref(null)
const showPilotForm = ref(null)

const fetchAvailablePilots = async (starshipId) => {
  try {
    const res = await $fetch(`${config.public.API_URL}/starships/${starshipId}/available-pilots`)
    availablePilots.value = res.data
    selectedPilotId.value = null
    showPilotForm.value = starshipId
  } catch (err) {
    console.error("Error al cargar pilotos disponibles", err)
    alert("Error al cargar pilotos. Ver consola.")
  }
}

const addPilotToStarship = async (starshipId) => {

  const pilotoNombre = availablePilots.value.find(p => p.id === selectedPilotId.value)?.name || 'este piloto'
if (confirm(`¿Quieres AÑADIR al piloto ${pilotoNombre}?`)) {
  try {
    await $fetch(`${config.public.API_URL}/api/starships/${starshipId}/pilots`, {
      method: 'POST',
      body: {
        pilot_id: selectedPilotId.value
      }
    })


    selectedPilotId.value = null
    showPilotForm.value = null
    await loadStarships()
  } catch (err) {
    alert("Error al añadir piloto: " + (err?.data?.reason || err.message))
  }
}
}


const cancelar = () => {
  showPilotForm.value = null;
  selectedPilotId.value = null
}



// ------------------ Borrar PILOTOS ------------------


const deletePilot = async (starshipId, pilotId) => {
  // opción si quiero añadir el nombre en la confirm
  // const pilotoNombre = starships.value.find(p => p.id === pilotId) ?.name 

if (confirm(`¿Quieres BORRAR al piloto?`)) {
  try{
    await $fetch(`${config.public.API_URL}/api/starships/${starshipId}/pilots/${pilotId}`, {
      method: 'DELETE',
    }
    )
  }catch(error){
    alert('error al intentar borrar' + error)
  }
}
}



</script>