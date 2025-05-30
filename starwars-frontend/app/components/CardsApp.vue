<!-- eslint-disable @typescript-eslint/no-unused-expressions -->
<!-- eslint-disable vue/html-self-closing -->
<!-- eslint-disable vue/first-attribute-linebreak -->
<template>
  <div v-if="cargandoNaves" class="flex justify-center items-center mt-20">
    <i class="fas fa-spinner fa-spin text-yellow-400 text-4xl"></i>
  </div>

  <div v-if="!autenticando">
    <!-- Buscador con autocompletado -->
    <div class="flex justify-center mt-6">
      <!-- Input de búsqueda -->
      <div class="relative w-full max-w-md">
        <input
          v-model="busqueda"
          type="text"
          placeholder="Buscar nave/piloto..."
          class="w-full pl-10 p-2 border border-yellow-500 bg-gray-800 text-white rounded-lg focus:outline-none focus:ring focus:drop-shadow-[0_0_8px_#38bdf8]"
          aria-label="Buscar nave o piloto"
          @focus="mostrarSugerencias = true"
          @blur="ocultarSugerenciasConRetardo"
          @input="actualizarSugerencias"
        />

        <!-- Icono de lupa -->
        <div
          class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"
          aria-hidden="true"
        >
          <i class="fas fa-search" />
        </div>

        <!-- Sugerencias -->
        <ul
          v-if="mostrarSugerencias && sugerencias.length"
          class="absolute z-20 mt-1 w-full bg-gray-900 border rounded-lg shadow-lg max-h-60 overflow-y-auto"
        >
          <li
            v-for="(item, i) in sugerencias"
            :key="i"
            class="px-4 py-2 hover:bg-yellow-400/80 hover:text-black font-semibold cursor-pointer transition text-left"
            @mousedown.prevent="seleccionarSugerencia(item)"
          >
            {{ item }}
          </li>
        </ul>
      </div>
    </div>

    <!--  ---------------  PAGINACIÓN  ---------------- -->
    <div
      v-if="totalPaginas > 1"
      class="flex justify-center items-center gap-4 mt-5 custom-starwars"
    >
      <!-- Botón Primera Página -->
      <button
        :disabled="pagina === 1"
        class="px-2 py-1 text-yellow-400 text-5xl disabled:opacity-50 hover:bg-yellow-400 hover:text-black rounded-4xl"
        @click="pagina = 1"
      >
        «
      </button>

      <!-- Botón Anterior -->
      <button
        :disabled="pagina === 1"
        class="px-4 py-2 bg-yellow-400 text-black rounded-3xl disabled:opacity-50 active:bg-yellow-600"
        @click="pagina--"
      >
        Anterior
      </button>

      <!-- Botones de páginas -->
      <button
        v-for="paginaActual in paginasVisibles"
        :key="paginaActual"
        :class="[
          'px-3 py-1 rounded',
          pagina === paginaActual
            ? 'bg-yellow-400 text-black rounded-full drop-shadow-[0_0_8px_#38bdf8]'
            : 'text-white',
        ]"
        @click="pagina = paginaActual"
      >
        {{ paginaActual }}
      </button>

      <!-- Botón Siguiente -->
      <button
        :disabled="pagina === totalPaginas"
        class="px-4 py-2 bg-yellow-400 text-black rounded-3xl disabled:opacity-50 active:bg-yellow-600"
        @click="pagina++"
      >
        Siguiente
      </button>

      <!-- Botón Última Página -->
      <button
        :disabled="pagina === totalPaginas"
        class="px-2 py-1 text-yellow-400 text-5xl disabled:opacity-50 hover:bg-yellow-400 hover:text-black rounded-4xl"
        @click="pagina = totalPaginas"
      >
        »
      </button>
    </div>

    <!-- cards -->
    <div class="min-h-[300px] mt-8">
      <div
        v-if="paginatedStarships.length"
        class="grid grid-cols-1 sm:grid-cols-2 gap-x-16 gap-y-14 p-6 mx-auto max-w-screen-2xl mt-8"
      >
        <div
          v-for="starship in paginatedStarships"
          :key="starship.id"
          class="flex flex-col md:flex-row bg-gray-900 border border-yellow-700 rounded-xl shadow-md hover:bg-gray-800 transition duration-300 overflow-hidden w-full min-h-[300px]"
          style="height: 420px"
        >
          <div class="w-full md:w-[26rem] h-60 md:h-auto overflow-hidden">
            <img
              :src="starship.image2_url"
              :alt="starship.name"
              class="object-cover w-full h-full transition-transform duration-300 hover:scale-120"
            />
          </div>

          <div class="flex flex-col justify-between p-4 text-gray-300 flex-1">
            <h5
              class="mb-2 text-2xl font-bold tracking-wide text-yellow-300 text-center font-serif"
            >
              {{ starship.name }}
            </h5>
            <p class="mt-8 text-center md:text-left font-serif">
              <strong class="text-blue-400">Modelo:</strong>
              {{ starship.model }}
            </p>
            <p class="text-center md:text-left mt-4 font-serif">
              <strong class="text-blue-400">Pilotos:</strong>
              {{ starship.pilots.length }}
              <button
                aria-label="Añadir piloto"
                class="px-3 py-2 ml-20 text-white bg-green-700 rounded-lg hover:scale-125 hover:bg-green-600 focus:ring-4 focus:outline-none focus:ring-green-500 transition"
                @click="fetchAvailablePilots(starship.id)"
              >
                <i class="fa-solid fa-user-plus"></i>
              </button>
            </p>

            <div class="mt-4 flex flex-col items-center gap-4 w-full">
              <!-- Botón para mostrar formulario -->
              <!-- Desplegable -->
              <div
                v-if="showPilotForm === starship.id"
                class="space-y-2 w-full"
              >
                <select
                  v-model="selectedPilotId"
                  class="w-full p-2 rounded-xl bg-gray-800 text-white border border-gray-600"
                >
                  <option disabled value="">Selecciona un piloto</option>
                  <option
                    v-for="pilot in availablePilots"
                    :key="pilot.id"
                    :value="pilot.id"
                  >
                    {{ pilot.name }}
                  </option>
                </select>
                <!-- Mensaje de error -->
                <p v-if="errorSeleccion" class="text-red-400 text-sm mt-1">
                  {{ errorSeleccion }}
                </p>

                <div class="flex gap-2 justify-center">
                  <!-- si le ponemos  :disabled="!selectedPilotId"  en el <button .. > NO dejaría seleccionar este botón si no se elige una opción!!-->
                  <button
                    aria-label="Confirmar selección de piloto"
                    class="py-2 px-4 bg-green-700 text-white rounded-xl hover:bg-green-500 transition"
                    @click="addPilotToStarship(starship.id)"
                  >
                    <i class="fa-solid fa-check"></i>
                  </button>
                  <button
                    aria-label="Cancelar selección de piloto"
                    class="py-2 px-4 bg-red-600 text-white rounded-xl hover:bg-red-500 transition"
                    @click="cancelar"
                  >
                    <i class="fa-solid fa-xmark"></i>
                  </button>
                </div>
              </div>
            </div>

            <div class="mt-auto flex justify-center mb-6">
              <button
                class="px-4 py-2 text-sm font-medium text-white bg-yellow-600 rounded-lg transition hover:drop-shadow-[0_0_5px_#38bdf8]"
                @click="openModal(starship)"
              >
                Detalles
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- else buscador -->
      <div v-else class="text-center text-yellow-400 text-lg mt-12">
        En esta galaxia no se ha encontrado tu nave/piloto indicado!!!
      </div>
    </div>

    <!--  --------------------- MODAL ------------------- -->

    <transition name="modal-fade">
      <div
        v-if="showModal"
        class="fixed inset-0 bg-opacity-50 flex backdrop-blur-sm justify-center items-center z-50"
        role="dialog"
        aria-modal="true"
        aria-labelledby="titulo-modal"
        aria-describedby="contenido-modal"
        tabindex="-1"
        @click.self="closeModal"
      >
        <!-- Modal con tamaño fijo, sin scroll -->
        <!-- <div class="bg-white rounded-lg p-6 w-full max-w-3xl shadow-xl"> -->
        <div
          ref="modalRef"
        tabindex="-1"
          class="relative rounded-4xl p-6 w-full max-w-3xl shadow-xl bg-cover bg-center text-white max-h-[90vh]"
          :class="{
            'overflow-y-auto': scrollModalActivo || scrollPorPantallaPequena,
          }"
          :style="`background-image: url('images/hangar1.jpg'); background-blend-mode: overlay; background-color: rgba(0,0,0,0.6);`"
        >
          <!-- Cabecera -->
          <h3
            id="titulo-modal"
            tabindex="0"
            class="text-2xl font-bold text-center text-black mb-4 bg-yellow-500 py-1 rounded-xl"
          >
            {{ modalStarship.name }}
          </h3>

          <!-- Imagen más pequeña -->
          <div class="flex justify-center mb-4">
            <img
              :src="modalStarship.image2_url"
              :alt="`Imagen de la nave ${modalStarship.name}`"
              class="h-48 sm:h-60 object-contain rounded-xl shadow-md"
            />
          </div>

          <!-- Detalles alineados a la izquierda y centrados en la modal -->
          <div class="mx-auto text-left w-fit space-y-2" role="group">
            <p class="text-sm sm:text-base">
              <strong
                class="bg-yellow-500 text-black px-3 py-1 rounded-full shadow font-semibold"
              >
                Modelo:
              </strong>
              <span tabindex="0" aria-label="Modelo de la nave">{{
                modalStarship.model
              }}</span>
            </p>
            <p class="text-sm sm:text-base">
              <strong
                class="bg-yellow-500 text-black px-3 py-1 rounded-full shadow font-semibold"
              >
                Fabricante:
              </strong>
              <span tabindex="0" aria-label="Fabricante de la nave">{{
                modalStarship.manufacturer
              }}</span>
            </p>
            <p class="text-sm sm:text-base">
              <strong
                class="bg-yellow-500 text-black px-3 py-1 rounded-full shadow font-semibold"
              >
                Coste:
              </strong>
              <span tabindex="0" aria-label="Coste en créditos galácticos">
                {{ modalStarship.cost_in_credits || "Desconocido" }} uds.
              </span>
            </p>
          </div>

          <!-- Pilotos -->
          <div
            class="mt-8"
            role="region"
            aria-labelledby="heading-pilotos"
            tabindex="0"
          >
            <!-- Título con ID para aria-labelledby -->
            <h4
              id="heading-pilotos"
              class="text-xl font-bold mb-2 bg-yellow-500 text-black py-1 px-2 rounded-xl"
            >
              Pilotos:
            </h4>

            <!-- Si no hay pilotos -->
            <div
              v-if="modalStarship.pilots.length === 0"
              class="text-yellow-500 text-center py-4"
              role="alert"
              aria-live="polite"
              tabindex="0"
            >
              <p>¡Sin pilotos!</p>
              <p>Añade tus pilotos favoritos y que la fuerza os acompañe</p>
            </div>

            <!-- Si hay solo 1 piloto -->
            <div
              v-else-if="modalStarship.pilots.length === 1"
              class="max-h-64 overflow-y-auto pr-2 flex justify-center"
              role="list"
              aria-label="Piloto asignado a esta nave"
            >
              <div
                v-for="pilot in modalStarship.pilots"
                :key="pilot.id"
                role="listitem"
                class="flex items-center justify-between gap-2 rounded-lg px-4 py-2 w-[15rem]"
                tabindex="0"
              >
                <div class="flex items-center gap-2">
                  <img
                    :src="pilot.image_url"
                    class="w-12 h-12 rounded-full border border-yellow-500 object-cover"
                  />
                  <span class="text-md text-white">{{ pilot.name }}</span>
                </div>
                <button
                  :aria-label="`Eliminar al piloto ${pilot.name}`"
                  class="text-white hover:scale-120 hover:bg-red-600 p-3 rounded-lg transition"
                  @click="deletePilot(modalStarship.id, pilot.id)"
                >
                  <i class="fas fa-trash" aria-hidden="true" />
                </button>
              </div>
            </div>

            <!-- Si hay varios pilotos -->
            <div
              v-else
              class="max-h-64 overflow-y-auto w-2xl ml-8 pr-2 grid grid-cols-2 gap-x-27 gap-1"
              role="list"
              aria-label="Lista de pilotos asignados a esta nave"
            >
              <div
                v-for="pilot in modalStarship.pilots"
                :key="pilot.id"
                role="listitem"
                class="flex items-center justify-between gap-2 hover:bg-gray-700/20 hover:rounded-4xl rounded-lg px-4 py-2"
                tabindex="0"
              >
                <div class="flex items-center gap-2">
                  <img
                    :src="pilot.image_url"
                    class="w-12 h-12 rounded-full border border-yellow-500 object-cover"
                  />
                  <span class="text-md text-white">{{ pilot.name }}</span>
                </div>
                <button
                  :aria-label="`Botón para eliminar al piloto ${pilot.name}`"
                  class="text-white hover:scale-120 hover:bg-red-600 p-3 rounded-lg transition"
                  @click="deletePilot(modalStarship.id, pilot.id)"
                >
                  <i class="fas fa-trash" aria-hidden="true" />
                </button>
              </div>
            </div>

            <!-- Botón cerrar -->
            <div class="mt-2">
              <button
                class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700"
                aria-label="Cerrar ventana modal"
                @click="closeModal"
              >
                Cerrar
              </button>
            </div>
          </div>
        </div>
      </div>
    </transition>

    <!--  ---------------  PAGINACIÓN  ---------------- -->
    <div
      v-if="totalPaginas > 1"
      class="flex justify-center items-center gap-4 mt-5 custom-starwars"
    >
      <!-- Botón Primera Página -->
      <button
        :disabled="pagina === 1"
        class="px-2 py-1 text-yellow-400 text-5xl disabled:opacity-50 hover:bg-yellow-400 hover:text-black rounded-4xl"
        @click="pagina = 1"
      >
        «
      </button>

      <!-- Botón Anterior -->
      <button
        :disabled="pagina === 1"
        class="px-4 py-2 bg-yellow-400 text-black rounded-3xl disabled:opacity-50 active:bg-yellow-600"
        @click="pagina--"
      >
        Anterior
      </button>

      <!-- Botones de páginas -->
      <button
        v-for="paginaActual in paginasVisibles"
        :key="paginaActual"
        :class="[
          'px-3 py-1 rounded',
          pagina === paginaActual
            ? 'bg-yellow-400 text-black rounded-full drop-shadow-[0_0_8px_#38bdf8]'
            : 'text-white',
        ]"
        @click="pagina = paginaActual"
      >
        {{ paginaActual }}
      </button>

      <!-- Botón Siguiente -->
      <button
        :disabled="pagina === totalPaginas"
        class="px-4 py-2 bg-yellow-400 text-black rounded-3xl disabled:opacity-50 active:bg-yellow-600"
        @click="pagina++"
      >
        Siguiente
      </button>

      <!-- Botón Última Página -->
      <button
        :disabled="pagina === totalPaginas"
        class="px-2 py-1 text-yellow-400 text-5xl disabled:opacity-50 hover:bg-yellow-400 hover:text-black rounded-4xl"
        @click="pagina = totalPaginas"
      >
        »
      </button>
    </div>
  </div>
</template>

<!-- Tu archivo ya contiene buenos comentarios visuales, aquí se mantendrán y se ampliarán donde sea necesario -->
<!-- Se agregan explicaciones técnicas y de propósito a nivel de variables, funciones y estructuras importantes -->

<script setup>
// Importamos SweetAlert2 para los cuadros de diálogo personalizados y una versión custom (swalDark)
import { onMounted, ref, nextTick, watch } from 'vue'
import { swalDark } from "#imports";
import Swal from "sweetalert2";


const modalRef = ref(null)

// Accedemos a las variables públicas de entorno
const config = useRuntimeConfig();

// Variables reactivas de estado general de la vista
const busqueda = ref(""); // Contiene el texto de búsqueda del input
const starships = ref({ data: [] }); // Lista de naves recuperadas desde la API (estructura esperada: { data: [...] })
const pagina = ref(1); // Página actual
const navesPorPagina = 4; // Número de naves por página
const autenticando = ref(true); // Estado de autenticación para mostrar el contenido solo cuando esté listo
const cargandoNaves = ref(true); // Muestra spinner mientras se cargan las naves

// Variables relacionadas con la gestión del modal
const showModal = ref(false); // Controla si el modal está visible o no
const modalStarship = ref({}); // Objeto que contiene los datos de la nave seleccionada

// Variables relacionadas con el formulario para añadir pilotos
const availablePilots = ref([]); // Pilotos disponibles para asignar a la nave
const selectedPilotId = ref(null); // ID del piloto seleccionado en el formulario
const showPilotForm = ref(null); // ID de la nave que tiene abierto el formulario
const errorSeleccion = ref(""); // Error si el usuario intenta confirmar sin seleccionar

// Scroll controlado en el modal cuando hay más de 2 pilotos
const scrollModalActivo = computed(() => {
  const cantidad = modalStarship.value?.pilots?.length || 0;
  return cantidad > 2;
});

// Detecta si estamos en una pantalla pequeña (movil)
const scrollPorPantallaPequena = computed(() => {
  return window.innerWidth < 768; // true si es móvil
});


const handleKeyDown = (event) => {
  if (event.key === 'Escape' && showModal.value) {
    closeModal()
  }
}

useHead({
  htmlAttrs: {
    lang: "es",
  },
});

watch(showModal, (isOpen) => {
  if (isOpen) {
    document.addEventListener('keydown', handleKeyDown)

    // Esperamos a que la DOM se actualice (modal renderizada)
    nextTick(() => {
      modalRef.value?.focus()
    })
  } else {
    document.removeEventListener('keydown', handleKeyDown)
  }
})


// Carga inicial de naves desde la API protegida con token
const loadStarships = async () => {
  cargandoNaves.value = true;
  try {
    const token = localStorage.getItem("token");
    const res = await $fetch(`${config.public.API_URL}/api/starships`, {
      headers: { Authorization: `Bearer ${token}` },
    });
    starships.value = res?.data ? res : { data: [] };
  } catch (e) {
    console.error("Error cargando naves", e);
  } finally {
    cargandoNaves.value = false;
  }
};

// Actualiza una nave individual tras modificarla (por ejemplo, tras añadir piloto)
const actualizarNave = async (id) => {
  const token = localStorage.getItem("token");
  try {
    const res = await $fetch(`${config.public.API_URL}/api/starships/${id}`, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    });
    const index = starships.value.data.findIndex((s) => s.id === id);
    if (index !== -1) {
      starships.value.data[index] = res;
    }
  } catch (e) {
    console.error("Error actualizando nave individual", e);
  }
};

// Validamos si hay token, si no, redirigimos al login. Si lo hay, cargamos naves.
onMounted(async () => {
  const token = localStorage.getItem("token");
  if (!token) {
    return navigateTo("/login?unauthorized=true");
  }
  try {
    await loadStarships();
  } catch (err) {
    if (err?.response?.status === 401) {
      console.warn("[TOKEN inválido] Redirigiendo al login...");
      localStorage.removeItem("token");
      return navigateTo("/login?unauthorized=true");
    } else {
      console.error("Error inesperado al cargar starships:", err);
    }
  } finally {
    autenticando.value = false;
  }
});

// Filtrado en tiempo real de las naves en base al texto de búsqueda
const filteredStarships = computed(() => {
  const lista = starships.value?.data ?? [];
  const term = busqueda.value.toLowerCase().trim();
  if (!term) return lista;

  return lista.filter((s) => {
    const nameMatch = s.name?.toLowerCase().includes(term);
    const pilotMatch = s.pilots?.some((pilot) =>
      pilot.name?.toLowerCase().includes(term)
    );
    return nameMatch || pilotMatch;
  });
});

// Calcula qué botones de paginación mostrar alrededor de la página actual
const paginasVisibles = computed(() => {
  const paginas = [];
  if (pagina.value > 1) paginas.push(pagina.value - 1);
  paginas.push(pagina.value);
  if (pagina.value < totalPaginas.value) paginas.push(pagina.value + 1);
  return paginas;
});

// Corta el array filtrado en base a la página actual y cantidad por página
const paginatedStarships = computed(() => {
  const lista = filteredStarships.value ?? [];
  const start = (pagina.value - 1) * navesPorPagina;
  return lista.slice(start, start + navesPorPagina);
});

// Total de páginas según resultado filtrado
const totalPaginas = computed(() => {
  return Math.ceil(filteredStarships.value.length / navesPorPagina);
});

// Reinicia a página 1 cuando cambia la búsqueda
watch(busqueda, () => {
  pagina.value = 1;
});

// Abre el modal y guarda la nave seleccionada
const openModal = (starship) => {
  modalStarship.value = starship;
  showModal.value = true;
};

// Cierra el modal
const closeModal = () => {
  showModal.value = false;
};

// Lógica para mantener el scroll original tras cerrar el modal (evita que suba arriba en móvil)
let scrollY = 0;
watch(showModal, (isOpen) => {
  if (isOpen) {
    scrollY = window.scrollY;
    document.body.style.position = "fixed";
    document.body.style.top = `-${scrollY}px`;
    document.body.style.width = "100%";
    document.documentElement.classList.add("overflow-hidden");
  } else {
    document.body.style.position = "";
    document.body.style.top = "";
    document.body.style.width = "";
    document.documentElement.classList.remove("overflow-hidden");
    window.scrollTo(0, scrollY);
  }
});

// Limpieza del DOM al cerrar modal o cambiar de componente
onUnmounted(() => {
  document.body.style.position = "";
  document.body.style.top = "";
  document.body.style.width = "";
  document.documentElement.classList.remove("overflow-hidden");
});

// Obtiene los pilotos disponibles para una nave concreta (llamado al pulsar "Añadir piloto")
const fetchAvailablePilots = async (starshipId) => {
  try {
    const res = await $fetch(
      `${config.public.API_URL}/starships/${starshipId}/available-pilots`
    );
    availablePilots.value = res.data;
    selectedPilotId.value = null;
    showPilotForm.value = starshipId;
  } catch (err) {
    console.error("Error al cargar pilotos disponibles", err);
    alert("Error al cargar pilotos. Ver consola.");
  }
};

// Asocia un piloto seleccionado a la nave, previa confirmación con SweetAlert
const addPilotToStarship = async (starshipId) => {
  const pilotoNombre =
    availablePilots.value.find((p) => p.id === selectedPilotId.value)?.name ||
    "este piloto";
  const token = localStorage.getItem("token");

  if (!selectedPilotId.value) {
    errorSeleccion.value = "No has seleccionado ningún piloto.";
    return;
  }

  errorSeleccion.value = "";

  const result = await swalDark.fire({
    title: `¿Añadir a ${pilotoNombre}?`,
    text: "¿Estás seguro de esta selección?",
    icon: "question",
    showCancelButton: true,
    confirmButtonText: "Sí, añadir",
    cancelButtonText: "Cancelar",
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
  });

  if (result.isConfirmed) {
    try {
      await $fetch(
        `${config.public.API_URL}/api/starships/${starshipId}/pilots`,
        {
          method: "POST",
          body: { pilot_id: selectedPilotId.value },
          headers: {
            Authorization: `Bearer ${token}`,
          },
        }
      );

      selectedPilotId.value = null;
      showPilotForm.value = null;
      await actualizarNave(starshipId);

      await Swal.fire({
        icon: "success",
        title: "Piloto añadido",
        text: "El piloto ha sido asignado con éxito.",
        timer: 1800,
        showConfirmButton: false,
      });
    } catch (err) {
      Swal.fire({
        icon: "error",
        title: "Error al añadir piloto",
        text: err?.data?.reason || err.message,
      });
    }
  }
};

// Cancela el formulario de selección de piloto
const cancelar = () => {
  showPilotForm.value = null;
  selectedPilotId.value = null;
};

// Elimina un piloto de una nave tras confirmación
const deletePilot = async (starshipId, pilotId) => {
  const token = localStorage.getItem("token");

  const result = await Swal.fire({
    title: "¿Borrar piloto?",
    text: "Esta acción no se puede deshacer",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Sí, borrar",
    cancelButtonText: "Cancelar",
    confirmButtonColor: "#d33",
    cancelButtonColor: "#aaa",
  });

  if (result.isConfirmed) {
    try {
      await $fetch(
        `${config.public.API_URL}/api/starships/${starshipId}/pilots/${pilotId}`,
        {
          method: "DELETE",
          headers: {
            Authorization: `Bearer ${token}`,
          },
        }
      );

      // Refrescamos la nave completa tras borrar
      const res = await $fetch(
        `${config.public.API_URL}/api/starships/${starshipId}`,
        {
          headers: { Authorization: `Bearer ${token}` },
        }
      );

      const index = starships.value.data.findIndex((s) => s.id === starshipId);
      if (index !== -1) starships.value.data[index] = res;
      modalStarship.value = res;

      await Swal.fire({
        icon: "success",
        title: "Piloto borrado",
        text: "Se ha eliminado correctamente.",
        timer: 1800,
        showConfirmButton: false,
      });
    } catch (error) {
      Swal.fire({
        icon: "error",
        title: "Error al borrar piloto",
        text: error?.data?.reason || error.message,
      });
    }
  }
};

// Pre-carga imágenes de la siguiente página para mejorar experiencia visual
watch(pagina, () => {
  const start = pagina.value * navesPorPagina;
  const end = start + navesPorPagina;
  const siguientes = filteredStarships.value.slice(start, end);
  siguientes.forEach((nave) => {
    const preload = new Image();
    preload.src = nave.image2_url;
  });
});

const sugerencias = ref([]); // Lista de sugerencias a mostrar
const mostrarSugerencias = ref(false); // Controla si se ve la lista desplegable

const actualizarSugerencias = () => {
  // Obtenemos el texto introducido por el usuario en minúsculas y sin espacios extras
  const term = busqueda.value.toLowerCase().trim();

  // Si no hay texto, limpiamos las sugerencias y ocultamos la lista
  if (!term) {
    sugerencias.value = [];
    mostrarSugerencias.value = false;
    return;
  }

  //  Buscamos nombres de naves que incluyan el texto introducido
  const nombresNaves = starships.value.data
    .filter((s) => s.name?.toLowerCase().includes(term)) // Coincidencia parcial
    .map((s) => s.name); // Extraemos solo el nombre

  //  Buscamos nombres de pilotos que incluyan el texto introducido
  const nombresPilotos = starships.value.data
    .flatMap((s) => s.pilots || []) // Aplanamos la lista de pilotos de todas las naves
    .filter((p) => p.name?.toLowerCase().includes(term)) // Coincidencia parcial
    .map((p) => p.name); // Extraemos solo el nombre

  //  Unimos ambas listas y eliminamos duplicados
  const conjunto = [...new Set([...nombresNaves, ...nombresPilotos])];

  //  Mostramos solo un máximo de 6 sugerencias
  sugerencias.value = conjunto.slice(0, 6);

  //  Si lo que ha escrito el usuario coincide exactamente con alguna sugerencia...
  if (conjunto.some((nombre) => nombre.toLowerCase() === term)) {
    // ...ocultamos la lista de sugerencias (no hace falta mostrarla)
    mostrarSugerencias.value = false;
  } else {
    //  Si no coincide exactamente, seguimos mostrando sugerencias
    mostrarSugerencias.value = true;
  }
};

const seleccionarSugerencia = (item) => {
  busqueda.value = item;
  mostrarSugerencias.value = false;
};

const ocultarSugerenciasConRetardo = () => {
  setTimeout(() => {
    mostrarSugerencias.value = false;
  }, 200); // Espera para permitir click
};
</script>

<style>
.modal-fade-enter-active {
  transition: opacity 0.5s ease;
}

.modal-fade-leave-active {
  transition: opacity 0.5s ease;
}

.modal-fade-enter-from {
  opacity: 0;
}

.modal-fade-leave-to {
  opacity: 0;
}

body.overflow-hidden,
html.overflow-hidden {
  overflow: hidden !important;
  height: 100% !important;
}
</style>
