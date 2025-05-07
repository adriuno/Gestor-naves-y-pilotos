<template>
  <!-- Buscador -->
  <div class="flex justify-center mt-6">
    <input
      v-model="busqueda"
      type="text"
      placeholder="Buscar nave o piloto..."
      class="w-full max-w-md p-2 border border-yellow-500 bg-gray-800 text-white rounded-lg focus:outline-none focus:ring focus:border-yellow-400"
    >
  </div>

  <!-- cards -->
  <div
    v-if="paginatedStarships.length"
    class="grid grid-cols-1 sm:grid-cols-2 gap-10 p-6 mx-auto max-w-7xl mt-8"
  >
    <div
      v-for="starship in paginatedStarships"
      :key="starship.id"
      class="flex flex-col md:flex-row bg-gray-900 border border-yellow-700 rounded-xl shadow-md hover:bg-gray-800 transition duration-300 overflow-hidden w-full min-h-[300px]"
    >
      
      <div class="w-full md:w-[22rem] h-60 md:h-auto overflow-hidden">
        <img
          :src="starship.image2_url"
          :alt="starship.name"
          class="object-cover w-full h-full transition-transform duration-300 hover:scale-120"
        >
      </div>

      
      <div class="flex flex-col justify-between p-4 text-gray-300 flex-1">
        <h5 class="mb-2 text-2xl font-bold tracking-wide text-yellow-300 text-center md:text-left">
          {{ starship.name }}
        </h5>
        <p class="mb-1 text-center md:text-left text-blue-400">Modelo: {{ starship.model }}</p>
        <p class="mb-4 text-center md:text-left text-blue-400">Pilotos: {{ starship.pilots.length }}</p>

        <div class="mt-auto flex justify-center">
          <button
            class="px-4 py-2 text-sm font-medium text-white bg-blue-700 rounded-lg hover:bg-blue-76500 focus:ring-4 focus:outline-none focus:ring-blue-500 transition"
            @click="openModal(starship)"
          >
            + Detalles
          </button>
        </div>

        <div class="mt-4 flex flex-col items-center gap-4 w-full">

          <!-- Botón para mostrar formulario -->
          <div v-if="showPilotForm !== starship.id" class="flex justify-center gap-4 w-full">
            <button
              class="px-4 py-2 text-sm font-medium text-white bg-green-700 rounded-lg hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-500 transition"
              @click="fetchAvailablePilots(starship.id)"
            >
              Añadir piloto
            </button>
          </div>


          <!-- Desplegable -->
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
              <button
                :disabled="!selectedPilotId"
                class="flex-1 px-4 py-2 bg-yellow-600 text-white rounded hover:bg-yellow-500 transition"
                @click="addPilotToStarship(starship.id)"
              >
                Añadir
              </button>
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

  <!-- else buscador -->
  <div v-else class="text-center text-yellow-400 text-xl mt-12">
    No se ha encontrado la nave/piloto indicado!
  </div>






  <!--  --------------------- MODAL ------------------- -->
  <div
    v-if="showModal"
    class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50"
    @click.self="closeModal"
  >
    <div class="bg-white rounded-lg p-6 max-w-md w-full">
      <h3 class="text-2xl font-bold text-center mb-4 bg-yellow-500">{{ modalStarship.name }}</h3>
      <img :src="modalStarship.image2_url" alt="Image" class="w-full mb-4" >
      <p><strong>Modelo:</strong> {{ modalStarship.model }}</p>
      <p><strong>Fabricante:</strong> {{ modalStarship.manufacturer }}</p>
      <p><strong>Coste:</strong> {{ modalStarship.cost_in_credits }}</p>
      <p><strong>Clase:</strong> {{ modalStarship.starship_class }}</p>

      <div class="mt-4">
        <h4 class="text-xl font-semibold mb-2 bg-yellow-500">Pilotos:</h4>
        <div class="grid grid-cols-2 gap-4">
          <div v-for="pilot in modalStarship.pilots" :key="pilot.id" class="flex items-center">
            <img
              :src="pilot.image_url"
              alt="Pilot Image"
              class="w-12 h-12 rounded-full object-cover mr-2"
            >
            <span class="text-sm text-gray-700">{{ pilot.name }}</span>
            <button
              class="ml-4 px-3 py-2 text-sm font-medium text-white bg-red-700 rounded-lg hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-500 transition"
              @click="deletePilot(modalStarship.id, pilot.id)"
            >
              Eliminar
            </button>
          </div>
        </div>
      </div>

      <button class="mt-4 p-3 bg-red-600 text-white py-2 rounded-lg hover:bg-red-700" @click="closeModal">
        Cerrar
      </button>
    </div>
  </div>


  <!--  ---------------  PAGINACIÓN  ---------------- -->
  <div v-if="totalPaginas > 1" class="flex justify-center gap-4 mt-5 custom-starwars">
    <button
      :disabled="pagina === 1"
      class="px-4 py-2 bg-yellow-400 text-black rounded-3xl disabled:opacity-50"
      @click="pagina--"
    >
      Anterior
    </button>

    <button
      v-for="paginaActual in totalPaginas"
      :key="paginaActual"
      :class="[
        'px-3 py-1 rounded',
        pagina === paginaActual ? 'bg-yellow-400 text-black rounded-full' : ' text-white',
      ]"
      @click="pagina = paginaActual"
    >
      {{ paginaActual }}
    </button>

    <button
      :disabled="pagina === totalPaginas"
      class="px-4 py-2 bg-yellow-400 text-black rounded-3xl disabled:opacity-50"
      @click="pagina++"
    >
      Siguiente
    </button>
  </div>
</template>

<script setup>
const config = useRuntimeConfig()

// Estado
const busqueda = ref("")
const starships = ref([])
const pagina = ref(1)
const navesPorPagina = 4

// Modal
const showModal = ref(false)
const modalStarship = ref({})

// Formulario pilotos
const availablePilots = ref([])
const selectedPilotId = ref(null)
const showPilotForm = ref(null)

// Cargar datos iniciales
const loadStarships = async () => {
  try {
    const res = await $fetch(`${config.public.API_URL}/starships`)
    starships.value = res
  } catch (error) {
    console.error('Error al cargar starships', error)
  }
}
await loadStarships()


// Búsqueda + filtrado
const filteredStarships = computed(() => {
  //if (!starships.value || !Array.isArray(starships.value.data)) return []
  const term = busqueda.value.toLowerCase().trim()
  if (!term) return starships.value.data

  return starships.value.data.filter((s) => {
    const nameMatch = s.name.toLowerCase().includes(term)
    const pilotMatch = s.pilots?.some(pilot =>
      pilot.name.toLowerCase().includes(term)
    )
    return nameMatch || pilotMatch
  })
})



// Paginación de resultados filtrados
const paginatedStarships = computed(() => {
  const start = (pagina.value - 1) * navesPorPagina
  return filteredStarships.value.slice(start, start + navesPorPagina)
})

const totalPaginas = computed(() => {
  return Math.ceil(filteredStarships.value.length / navesPorPagina)
})


// Resetear a primera página si cambia la búsqueda
watch(busqueda, () => {
  pagina.value = 1
})



// Modal
const openModal = (starship) => {
  modalStarship.value = starship
  showModal.value = true
}
const closeModal = () => {
  showModal.value = false
}



//  ----------  añadir pilotos -----------
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
  const token = localStorage.getItem('token')

  if (confirm(`¿Quieres AÑADIR al piloto ${pilotoNombre}?`)) {
    try {
      await $fetch(`${config.public.API_URL}/api/starships/${starshipId}/pilots`, {
        method: 'POST',
        body: {
          pilot_id: selectedPilotId.value
        },
        headers: {
          Authorization: `Bearer ${token}`
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
  showPilotForm.value = null
  selectedPilotId.value = null
}

// -------------  borrar pilotos  -------------

const deletePilot = async (starshipId, pilotId) => {
  const token = localStorage.getItem('token')

  if (confirm(`¿Quieres BORRAR al piloto?`)) {
    try {
      await $fetch(`${config.public.API_URL}/api/starships/${starshipId}/pilots/${pilotId}`, {
        method: 'DELETE',
        headers: {
          Authorization: `Bearer ${token}`
        }
      })

      closeModal()
      await loadStarships()
    } catch (error) {
      alert('error al intentar borrar: ' + error)
    }
  }
}

</script>
