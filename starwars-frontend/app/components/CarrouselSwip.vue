<!-- eslint-disable vue/html-self-closing -->
<template>
  <main :aria-hidden="modalPiloto ? 'true' : 'false'">
    <!-- Buscador -->
    <div class="flex justify-center mt-3">
      <div class="relative w-full max-w-md">
        <input
          v-model="busqueda"
          type="text"
          placeholder="Buscar piloto..."
          class="w-full pl-10 p-2 border border-yellow-500 bg-gray-800 text-white rounded-lg focus:outline-none focus:ring focus:drop-shadow-[0_0_8px_#38bdf8]"
          aria-label="Buscar piloto"
        />
        <div
          class="absolute left-3 top-1/2 transform -translate-y-1/2 text-yellow-400"
          aria-hidden="true"
        >
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
        role="group"
        aria-label="Carrusel de pilotos"
        effect="coverflow"
        grab-cursor
        centered-slides
        slides-per-view="auto"
        :space-between="20"
        :breakpoints="{
          0: { spaceBetween: 30 }, // más espacio en pantallas pequeñas
          640: { spaceBetween: 40 },
          1024: { spaceBetween: 50 },
        }"
        :coverflow-effect="{
          rotate: 0,
          stretch: 0,
          depth: 180,
          modifier: 1,
          slideShadows: false,
        }"
        navigation
        mousewheel
        class="pilot-carousel"
      >
        <SwiperSlide
          v-for="pilot in pilots"
          :key="pilot.id"
          class="pilot-slide"
          tabindex="0"
          role="button"
          :aria-label="`Ver detalles de ${pilot.name}`"
          @click="abrirModal(pilot)"
          @keydown.enter.stop.prevent="abrirModal(pilot)"
          @keydown.space.stop.prevent="abrirModal(pilot)"
        >
          <div class="relative pilot-card cursor-pointer group">
            <img
              :src="pilot.image_url"
              :alt="`Retrato de ${pilot.name}`"
              class="w-full object-cover rounded-xl"
            />
            <div
              class="absolute bottom-0 w-full bg-black bg-opacity-60 text-yellow-300 text-center py-2 text-lg rounded-b-xl group-hover:bg-opacity-90"
            >
              {{ pilot.name }}
            </div>
          </div>
        </SwiperSlide>
      </Swiper>
    </div>

    <p
      v-if="!cargandoPilotos && pilots.length === 0"
      class="text-center text-yellow-400 mt-10"
    >
      No se encontraron pilotos con ese nombre.
    </p>

    <!--  ------------------------------ MODAL ------------------------------
                              con más datos del piloto -->

    <transition name="modal-fade">
      <div
        v-if="modalPiloto"
        class="fixed inset-0 bg-opacity-70 backdrop-blur-sm z-30 flex justify-center sm:items-center items-start overflow-y-auto py-6 px-2"
        :aria-labelledby="'modal-titulo'"
        :aria-describedby="'modal-contenido'"
        role="dialog"
        aria-modal="true"
        @click.self="cerrarModal"
      >
        <div
          class="relative rounded-xl text-white w-full max-w-4xl shadow-xl overflow-hidden"
        >
          <!-- Capa oscura encima del fondo -->
          <div
            class="absolute inset-0 z-0"
            style="
              background-image: url('/images/fondoPilotos3.jpg');
              background-size: cover;
              background-position: center;
              filter: brightness(0.7);
            "
          ></div>

          <!-- Contenido principal -->
          <div
            id="modal-contenido"
            ref="modalContainer"
            class="relative z-10 p-4 sm:p-6 flex flex-col md:flex-row gap-4 sm:gap-6"
            tabindex="-1"
          >
            <!-- COLUMNA DE LA IZQUIERDA -->
            <!-- Imagen -->
            <div class="flex flex-col items-center ml-4">
              <img
                :src="modalPiloto.image_url"
                :alt="`Retrato de ${modalPiloto.name}`"
                class="w-22 h-22 sm:w-27 sm:h-27 object-cover rounded-full border-4 border-yellow-500"
              />
              <h2
                id="modal-titulo"
                class="text-sm sm:text-lg text-yellow-400 font-bold mb-2"
              >
                {{ modalPiloto.name }}
              </h2>
              <p class="text-xs">
                <strong
                  class="bg-yellow-500 text-black px-3 py-1 rounded-full shadow text-sm font-semibold"
                  >Género:</strong
                >
                {{ genderTraduction(modalPiloto.gender) }}
              </p>

              <p class="mt-25 mb-4">
                <strong
                  class="bg-yellow-500 text-black px-3 py-1 rounded-full shadow text-lg font-semibold"
                >
                  Piloto en naves:
                </strong>
              </p>
              <div class="flex flex-col items-center gap-2 mt-2 mb-6">
                <template v-if="modalPiloto.starships?.length">
                  <span
                    v-for="(ship, index) in uniqueStarships"
                    :key="index"
                    tabindex="0"
                    class="bg-gradient-to-br from-gray-400 via-gray-600 mb-1 to-gray-800 text-yellow-300 px-3 py-1 rounded-full shadow-inner shadow-yellow-600/20 border border-gray-400 text-xs font-semibold"
                  >
                    {{ ship.name }}
                  </span>
                </template>
                <p v-else class="italic text-gray-600 mb-6">
                  No asignado a ninguna nave
                </p>
              </div>
            </div>

            <!-- LÍNEA VERTICAL -->
            <div
              class="hidden md:block w-1 rounded bg-gray-400 h-auto ml-5 self-stretch"
            ></div>

            <!-- COLUMNA DE LA DERECHA -->
            <!-- Info del piloto -->
            <div class="flex-1 flex flex-col justify-between min-h-[400px]">
              <div class="space-y-2 text-xs mt-2 text-gray-300">
                <h3>
                  <strong
                    class="bg-yellow-500 text-black px-3 py-1 rounded-full shadow text-lg font-semibold"
                    >Películas:</strong
                  >
                </h3>
                <div
                  :class="[
                    'grid mt-6 gap-6',
                    modalPiloto.films?.length === 1
                      ? 'grid-cols-1 justify-items-center'
                      : modalPiloto.films?.length === 2
                      ? 'grid-cols-2 justify-items-center'
                      : 'grid-cols-2 sm:grid-cols-3',
                  ]"
                >
                  <template v-if="modalPiloto.films?.length">
                    <div
                      v-for="film in modalPiloto.films"
                      :key="film"
                      class="flex justify-center"
                    >
                      <!--   Usaremos :title en vez de title="" porque se le pasa variable yy
                          ambas sirven para sacarle una especie de tooltip    -->
                      <img
                        :src="filmMap[film]"
                        :title="filmTitles[film]"
                        :alt="`Imagen de cartel de ${filmTitles[film]}`"
                        tabindex="0"
                        class="w-24 sm:w-28 h-auto rounded-lg shadow-md hover:scale-105 transition-transform duration-300"
                      />
                    </div>
                  </template>
                  <p v-else class="italic text-gray-400 mb-6" tabindex="0">
                    No aparece en películas
                  </p>
                </div>
              </div>

              <!-- Botón cerrar -->
              <div class="text-center mt-4">
                <button
                  class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition hover:drop-shadow-[0_0_15px_#38bdf8]"
                  aria-label="Cerrar modal"
                  @click="cerrarModal"
                >
                  Cerrar
                </button>
              </div>
            </div>

            <!-- fin columna der -->
          </div>
        </div>
      </div>
    </transition>
  </main>

  <!--    Usa alt="..." si es un texto fijo.
          y   :alt="..." si el texto viene con una variable. -->
</template>

<script setup>
import { ref, computed, onMounted, watch } from "vue";
import { Swiper, SwiperSlide } from "swiper/vue";
import { Navigation, Mousewheel, EffectCoverflow } from "swiper/modules";
import "swiper/css";
import "swiper/css/navigation";
import "swiper/css/effect-coverflow";

const config = useRuntimeConfig();

const pilots = ref([]);
const busqueda = ref("");
const modalPiloto = ref(null);
const cargandoPilotos = ref(false);

const filmMap = {
  "https://swapi.dev/api/films/1/": "/images/films/1.webp",
  "https://swapi.dev/api/films/2/": "/images/films/2.webp",
  "https://swapi.dev/api/films/3/": "/images/films/3.webp",
  "https://swapi.dev/api/films/4/": "/images/films/4.webp",
  "https://swapi.dev/api/films/5/": "/images/films/5.webp",
  "https://swapi.dev/api/films/6/": "/images/films/6.webp",
};

const filmTitles = {
  "https://swapi.dev/api/films/1/": "Una nueva esperanza",
  "https://swapi.dev/api/films/2/": "El imperio contraataca",
  "https://swapi.dev/api/films/3/": "El retorno del Jedi",
  "https://swapi.dev/api/films/4/": "La amenaza fantasma",
  "https://swapi.dev/api/films/5/": "El ataque de los clones",
  "https://swapi.dev/api/films/6/": "La venganza de los Sith",
};

useHead({
  htmlAttrs: {
    lang: "es",
  },
});

const fetchPilotsFromStarships = async () => {
  cargandoPilotos.value = true;
  const token = localStorage.getItem("token");
  if (!token) return navigateTo("/login?unauthorized=true");

  try {
    const res = await $fetch(`${config.public.API_URL}/api/starships`, {
      headers: { Authorization: `Bearer ${token}` },
    });

    const allStarships = res.data || [];
    const pilotMap = {};

    for (const ship of allStarships) {
      for (const pilot of ship.pilots || []) {
        const pilotId = Number(pilot.id);
        if (!pilotMap[pilotId]) {
          pilotMap[pilotId] = {
            ...pilot,
            starships: [{ name: ship.name }],
          };
        } else {
          if (!pilotMap[pilotId].starships.some((s) => s.name === ship.name)) {
            pilotMap[pilotId].starships.push({ name: ship.name });
          }
        }
      }
    }

    let filtered = Object.values(pilotMap);
    const term = busqueda.value.trim().toLowerCase();
    if (term) {
      filtered = filtered.filter((p) => p.name.toLowerCase().includes(term));
    }

    filtered.sort((a, b) => a.name.localeCompare(b.name));
    pilots.value = filtered;
  } catch (err) {
    if (err?.response?.status === 401) {
      localStorage.removeItem("token");
      navigateTo("/login?unauthorized=true");
    } else {
      console.error("Error al cargar pilotos desde starships:", err);
    }
  } finally {
    cargandoPilotos.value = false;
  }
};

onMounted(fetchPilotsFromStarships);

watch(busqueda, () => {
  fetchPilotsFromStarships();
});

// Esta función impide que el foco salga del modal cuando se usa Tab o Shift+Tab
const trapFocus = (e) => {
  // Solo actuamos si se está presionando la tecla Tab
  if (e.key !== "Tab") return;

  // Obtenemos el contenedor del modal
  const modal = document.getElementById("modal-contenido");
  if (!modal) return;

  // Seleccionamos todos los elementos focusables dentro del modal
  const focusables = modal.querySelectorAll(
    'a[href], button, textarea, input, select, [tabindex]:not([tabindex="-1"])'
  );

  // Convertimos el NodeList a un array para poder trabajar cómodamente
  const focusablesArray = Array.from(focusables);

  // Obtenemos el primer y el último elemento con foco
  const first = focusablesArray[0];
  const last = focusablesArray[focusablesArray.length - 1];

  // Comprobamos si se está presionando Shift (para navegación inversa)
  const isShift = e.shiftKey;

  // Si estamos en el primer elemento y el usuario pulsa Shift+Tab,
  // prevenimos el comportamiento por defecto y llevamos el foco al último
  if (isShift && document.activeElement === first) {
    e.preventDefault();
    last.focus();
  }

  // Si estamos en el último y el usuario pulsa Tab,
  // prevenimos el comportamiento y llevamos el foco al primero
  else if (!isShift && document.activeElement === last) {
    e.preventDefault();
    first.focus();
  }

  // De esta forma el foco queda encerrado dentro del modal, sin poder salirse al tabular
};

// para impedir scroll al abrir una modal!!
watch(modalPiloto, (nuevoValor) => {
  if (nuevoValor) {
    nextTick(() => {
      const modal = document.getElementById("modal-contenido");
      if (modal) modal.focus(); // enfoca el modal
    });

    document.body.classList.add("overflow-hidden");
    document.addEventListener("keydown", trapFocus);
  } else {
    document.body.classList.remove("overflow-hidden");
    document.removeEventListener("keydown", trapFocus);
  }
});

const abrirModal = (pilot) => {
  if (typeof pilot.films === "string") {
    try {
      pilot.films = JSON.parse(pilot.films);
    } catch {
      pilot.films = [];
    }
  }
  modalPiloto.value = pilot;
};

const cerrarModal = () => {
  modalPiloto.value = null;
};

const uniqueStarships = computed(() => {
  const seen = new Set();
  return (
    modalPiloto.value?.starships?.filter((ship) => {
      if (seen.has(ship.name)) return false;
      seen.add(ship.name);
      return true;
    }) || []
  );
});

// traduccion de géneros de pilotos
const genderTraduction = (gender) => {
  switch (gender?.toLowerCase()) {
    case "male":
      return "Masculino";
    case "female":
      return "Femenino";
    default:
      return "Desconocido";
  }
};

// para cerrar modal desde teclado:
onMounted(() => {
  window.addEventListener("keydown", handleEscape);
});

onUnmounted(() => {
  window.removeEventListener("keydown", handleEscape);
});

const handleEscape = (e) => {
  if (e.key === "Escape" && modalPiloto.value) cerrarModal();
};
</script>

<style>
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

@media (max-width: 640px) {
  .pilot-card {
    width: 200px;
    height: 250px;
  }
}

@media (max-width: 768px) and (orientation: landscape) {
  .pilot-card {
    width: 150px;
    height: 200px;
  }
}

.pilot-slide:focus {
  outline: 2px solid #facc15;
  outline-offset: 4px;
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

.swiper-button-next,
.swiper-button-prev {
  color: #facc15;
  font-weight: 900;
}
</style>
