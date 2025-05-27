<!-- eslint-disable @typescript-eslint/no-unused-expressions -->
<!-- eslint-disable vue/html-self-closing -->
<!-- eslint-disable vue/first-attribute-linebreak -->
<template>
    <div v-if="cargandoNaves" class="flex justify-center items-center mt-20">
        <i class="fas fa-spinner fa-spin text-yellow-400 text-4xl"></i>
    </div>

  <div v-if="!autenticando">
        <!-- Buscador -->
    <div class="flex justify-center mt-6">
      <!-- Envolvemos el input en un div con posición relativa -->
      <!-- Esto nos permite posicionar elementos hijos (como el icono) de forma absoluta dentro -->
      <div class="relative w-full max-w-md">
        <!-- Input de búsqueda -->
        <input
          v-model="busqueda" type="text" placeholder="Buscar nave/piloto..."
          class="w-full pl-10 p-2 border border-yellow-500 bg-gray-800 text-white rounded-lg 
                focus:outline-none focus:ring 
                focus:drop-shadow-[0_0_8px_#38bdf8]" >

        <!-- Icono de lupa -->
        <!-- 🔸 Se posiciona absolutamente dentro del input -->
        <!-- 🔸 left-3 lo sitúa 12px desde la izquierda -->
        <!-- 🔸 top-1/2 + transform -translate-y-1/2 lo centran verticalmente -->
        <!-- 🔸 text-yellow-400 para que el color combine con los bordes -->
        <div class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 103 10.5a7.5 7.5 0 0013.15 6.15z" />
          </svg>
        </div>
      </div>
    </div>

    <!--  ---------------  PAGINACIÓN  ---------------- -->
    <div v-if="totalPaginas > 1" class="flex justify-center items-center gap-4 mt-5 custom-starwars">
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
            : 'text-white'
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

      <div v-if="paginatedStarships.length"      class="grid grid-cols-1 sm:grid-cols-2 gap-x-16 gap-y-14 p-6 mx-auto max-w-screen-2xl mt-8">
      <div
        v-for="starship in paginatedStarships" :key="starship.id"
        class="flex flex-col md:flex-row bg-gray-900 border border-yellow-700 rounded-xl shadow-md hover:bg-gray-800 transition duration-300 overflow-hidden w-full min-h-[300px]"
        style="height: 420px;"
        >
        <div class="w-full md:w-[26rem] h-60 md:h-auto overflow-hidden">
          <img
            :src="starship.image2_url" :alt="starship.name"
            class="object-cover w-full h-full transition-transform duration-300 hover:scale-120" >
        </div>

        <div class="flex flex-col justify-between p-4 text-gray-300 flex-1">
          <h5 class="mb-2 text-2xl font-bold tracking-wide text-yellow-300 text-center font-serif">
            {{ starship.name }}
          </h5>
          <p class="mt-8 text-center md:text-left  font-serif">
            <strong class="text-blue-400">Modelo:</strong> {{ starship.model }}
          </p>
          <p class=" text-center md:text-left mt-4 font-serif">
            <strong class="text-blue-400">Pilotos:</strong> {{ starship.pilots.length }}
            <button
                aria-label="Añadir piloto"
                class="px-3 py-2 ml-20  text-white bg-green-700 rounded-lg 
                      hover:scale-125 hover:bg-green-600 focus:ring-4 focus:outline-none focus:ring-green-500 transition"
                @click="fetchAvailablePilots(starship.id)">
                <i class="fa-solid fa-user-plus"></i>
              </button>
          </p>


          <div class="mt-4 flex flex-col items-center gap-4 w-full">
            <!-- Botón para mostrar formulario -->
              <!-- Desplegable -->
            <div v-if="showPilotForm === starship.id" class="space-y-2 w-full">
              <select v-model="selectedPilotId" class="w-full p-2 rounded bg-gray-800 text-white border border-gray-600">
                <option disabled value="">Selecciona un piloto</option>
                <option v-for="pilot in availablePilots" :key="pilot.id" :value="pilot.id">
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
                  @click="addPilotToStarship(starship.id)">
                  <i class="fa-solid fa-check"></i>
                </button>
                <button 
                  aria-label="Cancelar selección de piloto"
                  class="py-2 px-4 bg-red-600 text-white rounded-xl hover:bg-red-500 transition" 
                  @click="cancelar">
                  <i class="fa-solid fa-xmark"></i>
                </button>
              </div>
            </div>
          </div>

          
          <div class="mt-auto flex justify-center mb-6">
            <button
              class="px-4 py-2 text-sm font-medium text-white bg-yellow-600 rounded-lg 
                  focus:ring-4 focus:outline-none focus:ring-yellow-500 transition
                  hover:drop-shadow-[0_0_5px_#38bdf8] "
              @click="openModal(starship)">
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
        <div v-if="showModal" 
        class="fixed inset-0 bg-opacity-50 flex backdrop-blur-sm justify-center items-center z-50 "
        @click.self="closeModal">
        <!-- Modal con tamaño fijo, sin scroll -->
        <!-- <div class="bg-white rounded-lg p-6 w-full max-w-3xl shadow-xl"> -->
          <div
            class="relative rounded-4xl p-6 w-full max-w-3xl shadow-xl bg-cover bg-center text-white max-h-[90vh]"
            :class="{
              'overflow-y-auto': scrollModalActivo || scrollPorPantallaPequena,
            }"
            :style="`background-image: url('images/hangar1.jpg'); background-blend-mode: overlay; background-color: rgba(0,0,0,0.6);`"
          >
          <!-- Cabecera -->
          <h3 class="text-2xl font-bold text-center mb-4 bg-yellow-500 py-2 rounded-xl">
            {{ modalStarship.name }}
          </h3>

          <!-- Imagen más pequeña -->
          <div class="flex justify-center mb-4">
            <img
              :src="modalStarship.image2_url"
              alt="Imagen de {{ modalStarship.name }}"
              class="h-48 sm:h-60 object-contain rounded-xl shadow-md"
            />
          </div>

          <!-- Detalles -->
          <p class="text-sm sm:text-base"><strong class="text-yellow-400">Modelo:</strong> {{ modalStarship.model }}</p>
          <p class="text-sm sm:text-base"><strong class="text-yellow-400">Fabricante:</strong> {{ modalStarship.manufacturer }}</p>
          <p class="text-sm sm:text-base">
            <strong class="text-yellow-400">Coste:</strong> {{ modalStarship.cost_in_credits || "Desconocido" }}
          </p>

          <!-- Pilotos -->
          <div class="mt-8">
            <h4 class="text-xl font-semibold mb-2 bg-yellow-500 py-1 px-2 rounded-xl">
              Pilotos:
            </h4>

            <!-- Si no hay pilotos -->
            <div v-if="modalStarship.pilots.length === 0" class="text-yellow-500 font-semibold text-center py-4">

              <p>¡Sin pilotos!</p> 
              <p>Añade tus pilotos favoritos y que la fuerza os acompañe</p>
            </div>

            <!-- Si hay solo 1 piloto -->
            <div
              v-else-if="modalStarship.pilots.length === 1"
              class="max-h-64 overflow-y-auto pr-2 flex justify-center"
            >
              <div
                v-for="pilot in modalStarship.pilots"
                :key="pilot.id"
                class="flex items-center justify-between gap-2 rounded-lg px-4 py-2 w-[15rem]"
              >
                <div class="flex items-center gap-2">
                  <img
                    :src="pilot.image_url"
                    alt="Imagen del piloto"
                    class="w-12 h-12 rounded-full border border-yellow-500 object-cover"
                  />
                  <span class="text-md text-white">{{ pilot.name }}</span>
                </div>
                <button
                  aria-label="Borrar piloto"
                  class="text-white hover:scale-120 hover:bg-red-600 p-3 rounded-lg transition"
                  @click="deletePilot(modalStarship.id, pilot.id)"
                >
                  <i class="fas fa-trash" />
                </button>
              </div>
            </div>

            <!-- Si hay más de 1 piloto -->
            <div
              v-else
              class="max-h-64 overflow-y-auto w-2xl ml-8 pr-2 grid grid-cols-2 gap-x-27 gap-1"
            >
              <div
                v-for="pilot in modalStarship.pilots"
                :key="pilot.id"
                class="flex items-center justify-between gap-2 rounded-lg px-4 py-2"
              >
                <div class="flex items-center gap-2">
                  <img
                    :src="pilot.image_url"
                    alt="Imagen del piloto"
                    class="w-12 h-12 rounded-full border border-yellow-500 object-cover"
                  />
                  <span class="text-md text-white">{{ pilot.name }}</span>
                </div>
                <button
                  aria-label="Borrar piloto"
                  class="text-white hover:scale-120 hover:bg-red-600 p-3 rounded-lg transition"
                  @click="deletePilot(modalStarship.id, pilot.id)"
                >
                  <i class="fas fa-trash" />
                </button>
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
      </div>
      </transition>


    <!--  ---------------  PAGINACIÓN  ---------------- -->
    <div v-if="totalPaginas > 1" class="flex justify-center items-center gap-4 mt-5 custom-starwars">
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
            : 'text-white'
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

<script setup>
import { swalDark } from '#imports';
import Swal from 'sweetalert2'
import { GLTFLoader } from 'three/examples/jsm/loaders/GLTFLoader'
const config = useRuntimeConfig();

// Estado
const busqueda = ref("");
const starships = ref({ data: [] }); // Asegura estructura desde el inicio
const pagina = ref(1);
const navesPorPagina = 4;
const autenticando = ref(true)


// Variable para mensajico de error
const errorSeleccion = ref("");


// Modal
const showModal = ref(false);
const modalStarship = ref({});

// Formulario pilotos
const availablePilots = ref([]);
const selectedPilotId = ref(null);
const showPilotForm = ref(null);

const scrollModalActivo = computed(() => {
  const cantidad = modalStarship.value?.pilots?.length || 0;
  const anchoPantalla = window.innerWidth;

  return (anchoPantalla < 768 && cantidad > 2) || (anchoPantalla >= 768 && cantidad > 4);
});

onMounted(() => {
  window.addEventListener('resize', () => {
    // eslint-disable-next-line @typescript-eslint/no-unused-expressions
    scrollModalActivo.value; // fuerza reevaluación
    // eslint-disable-next-line @typescript-eslint/no-unused-expressions
    scrollPorPantallaPequena.value; // fuerza reevaluación 

  });
});


// Cargar datos iniciales
const loadStarships = async () => {
  cargandoNaves.value = true
  try {
    const token = localStorage.getItem("token")
    const res = await $fetch(`${config.public.API_URL}/api/starships`, {
      headers: { Authorization: `Bearer ${token}` },
    })
    starships.value = res?.data ? res : { data: [] }
  } catch (e) {
    console.error("Error cargando naves", e)
  } finally {
    cargandoNaves.value = false
  }
}



// llamar a nave que se modifica
const actualizarNave = async (id) => {
  const token = localStorage.getItem("token");

  try {
    const res = await $fetch(`${config.public.API_URL}/api/starships/${id}`, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    });

    const index = starships.value.data.findIndex(s => s.id === id);
    if (index !== -1) {
      starships.value.data[index] = res;
    }
  } catch (e) {
    console.error("Error actualizando nave individual", e);
  }
};


onMounted(async () => {
  const token = localStorage.getItem("token");
  if (!token) {
    return navigateTo('/login?unauthorized=true');
  }

  try {
    await loadStarships();
  } catch (err) {
    if (err?.response?.status === 401) {
      console.warn('[TOKEN inválido] Redirigiendo al login...');
      localStorage.removeItem("token");
      return navigateTo('/login?unauthorized=true');
    } else {
      console.error("Error inesperado al cargar starships:", err);
    }
  } finally {
    autenticando.value = false; // solo se muestra contenido si no hay redirección
  }
});



// Búsqueda + filtrado
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

// Paginación VISIBLE
const paginasVisibles = computed(() => {
  const paginas = [];
  if (pagina.value > 1) paginas.push(pagina.value - 1);
  paginas.push(pagina.value);
  if (pagina.value < totalPaginas.value) paginas.push(pagina.value + 1);
  return paginas;
});

// Paginación de resultados filtrados
const paginatedStarships = computed(() => {
  const lista = filteredStarships.value ?? [];
  const start = (pagina.value - 1) * navesPorPagina;
  return lista.slice(start, start + navesPorPagina);
});

const totalPaginas = computed(() => {
  return Math.ceil(filteredStarships.value.length / navesPorPagina);
});

// Resetear a primera página si cambia la búsqueda
watch(busqueda, () => {
  pagina.value = 1;
});

// Modal
const openModal = (starship) => {
  modalStarship.value = starship;
  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
};

watch(showModal, (isOpen) => {
  document.body.classList.toggle("overflow-hidden", isOpen);
});

onUnmounted(() => {
  document.body.classList.remove("overflow-hidden");
});

// Añadir pilotos
const fetchAvailablePilots = async (starshipId) => {
  try {
    const res = await $fetch(`${config.public.API_URL}/starships/${starshipId}/available-pilots`);
    availablePilots.value = res.data;
    selectedPilotId.value = null;
    showPilotForm.value = starshipId;
  } catch (err) {
    console.error("Error al cargar pilotos disponibles", err);
    alert("Error al cargar pilotos. Ver consola.");
  }
};

const addPilotToStarship = async (starshipId) => {
  const pilotoNombre = availablePilots.value.find((p) => p.id === selectedPilotId.value)?.name || "este piloto";
  const token = localStorage.getItem("token");

  if (!selectedPilotId.value) {
    errorSeleccion.value = "No has seleccionado ningún piloto.";
    return;
  }

  errorSeleccion.value = "";

  const result = await swalDark.fire({
    title: `¿Añadir a ${pilotoNombre}?`,
    text: "¿Estás seguro de esta selección?",
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Sí, añadir',
    cancelButtonText: 'Cancelar',
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
  });

  if (result.isConfirmed) {
    try {
      await $fetch(`${config.public.API_URL}/api/starships/${starshipId}/pilots`, {
        method: "POST",
        body: { pilot_id: selectedPilotId.value },
        headers: {
          Authorization: `Bearer ${token}`,
        },
      });

      selectedPilotId.value = null;
      showPilotForm.value = null;
      await actualizarNave(starshipId);

      await Swal.fire({
        icon: 'success',
        title: 'Piloto añadido',
        text: 'El piloto ha sido asignado con éxito.',
        timer: 1800,
        showConfirmButton: false,
      });
    } catch (err) {
      Swal.fire({
        icon: 'error',
        title: 'Error al añadir piloto',
        text: err?.data?.reason || err.message,
      });
    }
  }
};



const cancelar = () => {
  showPilotForm.value = null;
  selectedPilotId.value = null;
};

// Borrar pilotos

const deletePilot = async (starshipId, pilotId) => {
  const token = localStorage.getItem("token");

  const result = await Swal.fire({
    title: '¿Borrar piloto?',
    text: "Esta acción no se puede deshacer",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Sí, borrar',
    cancelButtonText: 'Cancelar',
    confirmButtonColor: '#d33',
    cancelButtonColor: '#aaa',
  });

  if (result.isConfirmed) {
    try {
      await $fetch(`${config.public.API_URL}/api/starships/${starshipId}/pilots/${pilotId}`, {
        method: "DELETE",
        headers: {
          Authorization: `Bearer ${token}`,
        },
      });

      const res = await $fetch(`${config.public.API_URL}/api/starships/${starshipId}`, {
        headers: { Authorization: `Bearer ${token}` }
      });

      const index = starships.value.data.findIndex(s => s.id === starshipId);
      if (index !== -1) starships.value.data[index] = res;

      modalStarship.value = res;

      await Swal.fire({
        icon: 'success',
        title: 'Piloto borrado',
        text: 'Se ha eliminado correctamente.',
        timer: 1800,
        showConfirmButton: false,
      });
    } catch (error) {
      Swal.fire({
        icon: 'error',
        title: 'Error al borrar piloto',
        text: error?.data?.reason || error.message,
      });
    }
  }
};



/* antes tenía esto.. y lo he cambiado xk se edevuelve ya una variable cn la función de actualizarNave..
const deletePilot = async (starshipId, pilotId) => {
  const token = localStorage.getItem("token");

  if (confirm(`¿Quieres BORRAR al piloto?`)) {
    try {
      await $fetch(`${config.public.API_URL}/api/starships/${starshipId}/pilots/${pilotId}`, {
        method: "DELETE",
        headers: {
          Authorization: `Bearer ${token}`,
        },
      });

  await actualizarNave(starshipId);
  modalStarship.value = starships.value.data.find(s => s.id === starshipId);
    } catch (error) {
      alert("Error al intentar borrar: " + error);
    }
  }
}; 
*/

  watch(pagina, () => {
    const start = pagina.value * navesPorPagina
    const end = start + navesPorPagina
    const siguientes = filteredStarships.value.slice(start, end)

    siguientes.forEach(nave => {
      const preload = new Image()
      preload.src = nave.image2_url
    })
  })
  /*
  Esto último es para pre-cargar las imágenes así...
    cuando el usuario está en la página 1, se descargan imágenes de la página 2 en segundo plano.
    yyy cuando pase a la página 2, las imágenes ya estarán cargadas o parcialmente cacheadas. 
  */

  onMounted(() => {
    // Precarga modelos de options
    const loader = new GLTFLoader()
    loader.load('/models/awing.glb', () => {})
    loader.load('/models/pilot1.glb', () => {})
  })


  const scrollPorPantallaPequena = computed(() => {
    return window.innerWidth < 768; // true si es móvil
  });


  //spinner para naves

  const cargandoNaves = ref(true)



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

</style>