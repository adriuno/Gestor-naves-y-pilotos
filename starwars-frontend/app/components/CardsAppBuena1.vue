<template>
<!-- Buscador -->
<div class="flex justify-center mt-6">
  <!-- Envolvemos el input en un div con posición relativa -->
  <!-- Esto nos permite posicionar elementos hijos (como el icono) de forma absoluta dentro -->
  <div class="relative w-full max-w-md">

    <!-- Input de búsqueda -->
    <input
      v-model="busqueda"
      type="text"
      placeholder="Buscar nave o piloto..."
      class="w-full pl-10 p-2 border border-yellow-500 bg-gray-800 text-white rounded-lg focus:outline-none focus:ring focus:border-yellow-400"
    >

    <!-- Icono de lupa -->
    <!-- 🔸 Se posiciona absolutamente dentro del input -->
    <!-- 🔸 left-3 lo sitúa 12px desde la izquierda -->
    <!-- 🔸 top-1/2 + transform -translate-y-1/2 lo centran verticalmente -->
    <!-- 🔸 text-yellow-400 para que el color combine con los bordes -->
    <div class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
      <svg
          xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
          viewBox="0 0 24 24" stroke="currentColor">
        <path
              stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 103 10.5a7.5 7.5 0 0013.15 6.15z" />
      </svg>
    </div>

  </div>
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
            class="px-4 py-2 text-sm font-medium text-white bg-yellow-600 rounded-lg hover:bg-yellow-500 focus:ring-4 focus:outline-none focus:ring-yellow-600 transition"
            @click="openModal(starship)"
          >
            Detalles
          </button>
        </div>

        <div class="mt-4 flex flex-col items-center gap-4 w-full">

          <!-- Botón para mostrar formulario -->
          <div v-if="showPilotForm !== starship.id" class="flex justify-center gap-4 w-full">
            <button
              class="px-2 py-2 text-sm font-medium text-white bg-blue-700 rounded-lg hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-500 transition"
              @click="fetchAvailablePilots(starship.id)"
            >
            <i class="fas fa-add"></i>  
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

            <div class="flex gap-2 justify-center">
              <button
                :disabled="!selectedPilotId"
                class="py-2 px-4 bg-green-700 text-white rounded-xl hover:bg-green-500 transition"
                @click="addPilotToStarship(starship.id)"
              >
                <i class="fa-solid fa-check"></i>
              </button>
              <button
                class=" py-2 px-4 bg-red-600 text-white rounded-xl hover:bg-red-500 transition"
                @click="cancelar"
              >
              <i class="fa-solid fa-xmark"></i>
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
  class="fixed inset-0 bg-opacity-50 flex backdrop-blur-sm justify-center items-center z-50"
  @click.self="closeModal"
>
  <!-- Modal con tamaño fijo, sin scroll -->
  <div class="bg-white rounded-lg p-6 w-full max-w-3xl shadow-xl">
    
    <!-- Cabecera -->
    <h3 class="text-2xl font-bold text-center mb-4 bg-yellow-500 py-2 rounded">
      {{ modalStarship.name }}
    </h3>

    <!-- Imagen más pequeña -->
    <div class="flex justify-center mb-4">
      <img
        :src="modalStarship.image2_url"
        alt="Image"
        class="h-48 object-contain rounded"
      >
    </div>

    <!-- Detalles -->
    <p><strong>Modelo:</strong> {{ modalStarship.model }}</p>
    <p><strong>Fabricante:</strong> {{ modalStarship.manufacturer }}</p>
    <p><strong>Coste:</strong> {{ modalStarship.cost_in_credits || 'Desconocido' }}</p>

    <!-- Pilotos -->
    <div class="mt-4">
      <h4 class="text-xl font-semibold mb-2 bg-yellow-500 py-1 px-2 rounded">Pilotos:</h4>
      <div class="grid grid-cols-2 gap-4">
        <div
          v-for="pilot in modalStarship.pilots"
          :key="pilot.id"
          class="flex items-center bg-gray-100 p-2 rounded"
        >
          <img
            :src="pilot.image_url"
            alt="Pilot Image"
            class="w-12 h-12 rounded-full object-cover mr-2"
          >
          <span class="text-sm text-gray-700">{{ pilot.name }}</span>
          <button
            class="ml-auto px-3 py-2 text-sm font-medium text-white bg-red-700 rounded-lg hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-500 transition"
            @click="deletePilot(modalStarship.id, pilot.id)"
          >
          <i class="fas fa-trash"/>
          </button>
        </div>
      </div>
    </div>

    <!-- Botón cerrar -->
    <div class="mt-6">
      <button class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700" @click="closeModal">
        Cerrar
      </button>
    </div>
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
