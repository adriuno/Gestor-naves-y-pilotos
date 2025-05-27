<!-- eslint-disable vue/html-self-closing -->
<template>
  <div>
    <!-- Buscador -->
    <div class="flex justify-center mt-3">
      <div class="relative w-full max-w-md">
        <input
          v-model="busqueda"
          type="text"
          placeholder="Buscar piloto..."
          class="w-full pl-10 p-2 border border-yellow-500 bg-gray-800 text-white rounded-lg focus:outline-none focus:ring focus:drop-shadow-[0_0_8px_#38bdf8]"
        />
        <div class="absolute left-3 top-1/2 transform -translate-y-1/2 text-yellow-400">
          <i class="fas fa-search" />
        </div>
      </div>
    </div>

    <!-- Spinner de carga -->
    <div v-if="cargandoPilotos" class="flex justify-center items-center my-12">
      <i class="fas fa-spinner fa-spin text-yellow-400 text-4xl"></i>
    </div>

    <!-- Carrusel con pilotos -->
    <div v-if="pilots.length">
      <Swiper
        :modules="[Navigation, Mousewheel, EffectCoverflow]"
        effect="coverflow"
        grab-cursor
        centered-slides
        slides-per-view="auto"
        :coverflow-effect="{
          rotate: 0,
          stretch: 0,
          depth: 180,
          modifier: 1,
          slideShadows: false
        }"
        navigation
        mousewheel
        class="pilot-carousel"
      >
        <SwiperSlide
          v-for="pilot in pilots"
          :key="pilot.id"
          class="pilot-slide"
          @click="abrirModal(pilot)"
        >
          <div class="relative pilot-card cursor-pointer group">
            <img :src="pilot.image_url" :alt="pilot.name" class="w-full object-cover rounded-xl" />
            <div class="absolute bottom-0 w-full bg-black bg-opacity-60 text-yellow-300 text-center py-2 text-lg rounded-b-xl group-hover:bg-opacity-90">
              {{ pilot.name }}
            </div>
          </div>
        </SwiperSlide>
      </Swiper>
    </div>

    <p v-if="!cargandoPilotos && pilots.length === 0" class="text-center text-yellow-400 mt-10">
      No se encontraron pilotos con ese nombre.
    </p>

    <!-- Modal con más datos del piloto -->
    <transition name="modal-fade">
      <div
        v-if="modalPiloto"
        class="fixed inset-0 bg-opacity-70 backdrop-blur-sm flex justify-center items-center z-30"
        @click.self="cerrarModal"
      >
        <div
          class="rounded-xl p-6 text-white w-full max-w-4xl shadow-xl flex flex-col md:flex-row gap-6"
          :style="`background-image: url('images/fondoPilotos3.jpg'); background-repeat: no-repeat; background-size: cover; background-position: center;`"
        >
          <!-- Imagen -->
          <div class="flex justify-center md:justify-start">
            <img :src="modalPiloto.image_url" alt="imagen piloto" class="w-52 h-52 object-cover rounded-full border-4 border-yellow-500" />
          </div>

          <!-- Info del piloto -->
          <div class="flex-1">
            <h2 class="text-2xl text-yellow-400 font-bold mb-4">{{ modalPiloto.name }}</h2>

            <div class="space-y-2 text-sm text-gray-300">
              <p><strong class="text-yellow-400">Género:</strong> {{ genderTraduction(modalPiloto.gender) }}</p>

              <p><strong class="text-yellow-400">Películas:</strong></p>
              <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                <template v-if="modalPiloto.films?.length">
                  <div v-for="film in modalPiloto.films" :key="film" class="flex justify-center">
                    <img :src="filmMap[film]" alt="Cartel" class="w-24 h-auto rounded shadow-lg" />
                  </div>
                </template>
                <p v-else class="italic text-gray-400">No aparece en películas</p>
              </div>

              <p><strong class="text-yellow-400">Naves:</strong></p>
              <ul class="list-disc list-inside ml-2">
                <template v-if="modalPiloto.starships?.length">
                  <li v-for="(ship, index) in uniqueStarships" :key="index">
                    {{ ship.name }}
                  </li>
                </template>
                <li v-else class="italic text-gray-400">No asignado a ninguna nave</li>
              </ul>
            </div>

            <!-- Botón cerrar -->
            <div class="text-center mt-4">
              <button class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700" @click="cerrarModal">Cerrar</button>
            </div>
          </div>
        </div>
      </div>
    </transition>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { Swiper, SwiperSlide } from 'swiper/vue'
import { Navigation, Mousewheel, EffectCoverflow } from 'swiper/modules'
import 'swiper/css'
import 'swiper/css/navigation'
import 'swiper/css/effect-coverflow'

const config = useRuntimeConfig()

const pilots = ref([])
const busqueda = ref('')
const modalPiloto = ref(null)
const cargandoPilotos = ref(false)

const filmMap = {
  "https://swapi.dev/api/films/1/": "/images/films/1.webp",
  "https://swapi.dev/api/films/2/": "/images/films/2.webp",
  "https://swapi.dev/api/films/3/": "/images/films/3.webp",
  "https://swapi.dev/api/films/4/": "/images/films/4.webp",
  "https://swapi.dev/api/films/5/": "/images/films/5.webp",
  "https://swapi.dev/api/films/6/": "/images/films/6.webp",
}

const fetchPilotsFromStarships = async () => {
  cargandoPilotos.value = true
  const token = localStorage.getItem('token')
  if (!token) return navigateTo('/login?unauthorized=true')

  try {
    const res = await $fetch(`${config.public.API_URL}/api/starships`, {
      headers: { Authorization: `Bearer ${token}` },
    })

    const allStarships = res.data || []
    const pilotMap = {}

    for (const ship of allStarships) {
      for (const pilot of ship.pilots || []) {
        const pilotId = Number(pilot.id)
        if (!pilotMap[pilotId]) {
          pilotMap[pilotId] = {
            ...pilot,
            starships: [{ name: ship.name }]
          }
        } else {
          if (!pilotMap[pilotId].starships.some(s => s.name === ship.name)) {
            pilotMap[pilotId].starships.push({ name: ship.name })
          }
        }
      }
    }

    let filtered = Object.values(pilotMap)
    const term = busqueda.value.trim().toLowerCase()
    if (term) {
      filtered = filtered.filter(p => p.name.toLowerCase().includes(term))
    }

    filtered.sort((a, b) => a.name.localeCompare(b.name))
    pilots.value = filtered

  } catch (err) {
    if (err?.response?.status === 401) {
      localStorage.removeItem('token')
      navigateTo('/login?unauthorized=true')
    } else {
      console.error('Error al cargar pilotos desde starships:', err)
    }
  } finally {
    cargandoPilotos.value = false
  }
}

onMounted(fetchPilotsFromStarships)

watch(busqueda, () => {
  fetchPilotsFromStarships()
})

const abrirModal = (pilot) => {
  if (typeof pilot.films === 'string') {
    try {
      pilot.films = JSON.parse(pilot.films)
    } catch {
      pilot.films = []
    }
  }
  modalPiloto.value = pilot
}

const cerrarModal = () => {
  modalPiloto.value = null
}

const uniqueStarships = computed(() => {
  const seen = new Set()
  return modalPiloto.value?.starships?.filter(ship => {
    if (seen.has(ship.name)) return false
    seen.add(ship.name)
    return true
  }) || []
})




const genderTraduction = (gender) => {
  switch (gender?.toLowerCase()) {
    case 'male':
      return 'Masculino'
    case 'female':
      return 'Femenino'
    default:
      return 'Desconocido'
  }
}

</script>

<style scoped>
.pilot-carousel {
  width: 75%;
  padding: 4rem;
}
.pilot-slide {
  display: flex;
  justify-content: center;
  align-items: center;
}
.pilot-card {
  width: 330px;
  height: 420px;
  overflow: hidden;
  border: 2px solid #facc15;
  border-radius: 1rem;
  box-shadow: 0 0 12px rgba(250, 204, 21, 0.5);
  transition: transform 0.3s ease;
}
.pilot-card:hover {
  transform: scale(1.25);
}
.modal-fade-enter-active,
.modal-fade-leave-active {
  transition: opacity 0.3s ease;
}
.modal-fade-enter-from,
.modal-fade-leave-to {
  opacity: 0;
}
</style>
