<template>
  <!-- Solo se muestra el contenido si ya se ha autenticado -->
  <div
    v-if="!autenticando"
    role="main"
    class="relative min-h-screen bg-black bg-cover bg-center bg-no-repeat text-white px-6 py-4 flex flex-col justify-between"
    style="background-image: url('/images/sw3.jpg')"
  >
    <!-- Botón de logout, arriba a la derecha -->
    <UButton
      aria-label="Cerrar sesión"
      title="Cerrar sesión"
      class="absolute py-2 sm:py-3 sm:px-3 sm:text-2xl top-4 sm:top-5 right-2 sm:right-15 bg-red-600 rounded-3xl hover:bg-red-700 hover:scale-115 transition-transform z-10 hover:drop-shadow-[0_0_8px_#38bdf8] transition duration-200"
      @click="cerrarSesion"
    >
      <!-- Icono de apagado -->
      <i class="fa-solid fa-power-off" />
    </UButton>

    <!-- Título principal de la pantalla -->
    <div class="flex justify-center">
      <h1
        tabindex="0"
        aria-label="En esta pantalla puedes elegir entre acceder a la sección de naves o a la de pilotos"
        class="inline-block text-3xl sm:text-4xl text-black bg-yellow-400 mt-9 sm:mt-4 custom-starwars rounded-4xl sm:px-5 py-4 text-center"
      >
        selecciona una opcion
      </h1>
    </div>

    <!-- Sección de selección: Naves y Pilotos -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-center">
      <!-- Opción: Ver Naves -->
      <div class="flex flex-col items-center">
        <!-- Canvas donde se muestra el modelo 3D de la nave -->
        <canvas ref="naveCanvas" class="h-32 w-full" />
        <!-- Botón para ir a la página de naves -->
        <button
          class="inline-block text-2xl text-black bg-yellow-400 mt-1 mb-8 custom-starwars rounded-4xl px-6 py-3 hover:scale-155 transition-transform hover:text-yellow-500 hover:bg-violet-600 transition-colors duration-300"
          aria-label="Ir a la sección de naves"
          tabindex="0"
          @click="goTo('starships')"
        >
          Ver Naves
        </button>
      </div>

      <!-- Opción: Ver Pilotos -->
      <div class="flex flex-col items-center">
        <!-- Canvas donde se muestra el modelo 3D del piloto -->
        <canvas ref="pilotoCanvas" class="h-32 w-full" />
        <!-- Botón para ir a la página de pilotos -->
        <button
          class="inline-block text-2xl text-black bg-yellow-400 mt-2 mb-9 custom-starwars rounded-4xl px-6 py-3 hover:scale-155 transition-transform hover:text-yellow-500 hover:bg-violet-600 transition-colors duration-300"
          aria-label="Ir a la sección de pilotos"
          tabindex="0"
          @click="goTo('pilots')"
        >
          Ver Pilotos
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
// SweetAlert2 para confirmar cierre de sesión
import Swal from "sweetalert2";

// Librería Three.js para mostrar modelos 3D
import * as THREE from "three";
// Cargador de modelos GLTF
import { GLTFLoader } from "three/examples/jsm/loaders/GLTFLoader";

// Utilidades de Vue
import { onMounted, nextTick, watch, ref } from "vue";
// Router de Vue para redirecciones
import { useRouter } from "vue-router";

useHead({
  title: "Gestor | Opciones",
  htmlAttrs: {
    lang: "es",
  },
  link: [
    {
      rel: "icon",
      type: "image/png",
      href: "/images/logo/sw4.webp", // Asegúrate que esta ruta sea correcta y el archivo exista
    }
  ]
});


// Instancia del router
const router = useRouter();

// Refs a los canvas para los modelos
const naveCanvas = ref();
const pilotoCanvas = ref();

// Caché de modelos 3D para no recargarlos cada vez
const modelCache = {};

// Redirección según la opción seleccionada
const goTo = (section) => {
  router.push(`/${section}`);
};

// Carga un modelo 3D en un canvas usando Three.js
const loadModel = async (canvasRef, modelUrl, options = {}) => {
  const scene = new THREE.Scene();

  // Cámara con perspectiva
  const camera = new THREE.PerspectiveCamera(75, 1, 0.1, 1000);
  camera.position.z = options.cameraZ || 6;

  // Renderer de WebGL
  const renderer = new THREE.WebGLRenderer({
    canvas: canvasRef.value,
    alpha: true, // Fondo transparente
    antialias: false, // Mejor rendimiento
  });
  renderer.setSize(canvasRef.value.clientWidth, canvasRef.value.clientHeight);
  renderer.setPixelRatio(window.devicePixelRatio);

  // Iluminación ambiental
  const ambientLight = new THREE.AmbientLight(0xffffff, 1.5);
  scene.add(ambientLight);

  // Luz direccional como si fuera el sol
  const directionalLight = new THREE.DirectionalLight(0xffffff, 1.5);
  directionalLight.position.set(3, 5, 2);
  scene.add(directionalLight);

  // Cargar o usar modelo desde caché
  let model;
  if (modelCache[modelUrl]) {
    model = modelCache[modelUrl].clone();
  } else {
    const gltf = await new Promise((resolve, reject) => {
      new GLTFLoader().load(modelUrl, resolve, undefined, reject);
    });
    model = gltf.scene;
    modelCache[modelUrl] = model.clone();
  }

  // Escala y posición del modelo
  const scale = options.scale || 1.5;
  model.scale.set(scale, scale, scale);
  model.position.set(0, -0.5, 0);
  scene.add(model);

  // Variables para el movimiento con el ratón
  let mouseX = 0;
  let mouseY = 0;

  // Renderiza la escena con rotación del modelo
  const renderScene = () => {
    model.rotation.y = mouseX * 1.5;
    model.rotation.x = mouseY * 1.0;
    renderer.render(scene, camera);
  };

  renderScene(); // Render inicial

  // Detectar movimiento del ratón para girar el modelo
  canvasRef.value.addEventListener("mousemove", (event) => {
    const rect = canvasRef.value.getBoundingClientRect();
    const x = (event.clientX - rect.left) / rect.width;
    const y = (event.clientY - rect.top) / rect.height;
    mouseX = (x - 0.5) * 6;
    mouseY = (y - 0.5) * 2;
    renderScene();
  });
};

// Variable para saber si estamos esperando autenticación
const autenticando = ref(true);

// Al montar el componente: comprobamos si hay token
onMounted(() => {
  const token = localStorage.getItem("token");
  if (!token) {
    return navigateTo("/login?unauthorized=true");
  }

  autenticando.value = false; // Ahora sí mostramos la vista
});

// Cuando autenticando cambia, cargamos los modelos
watch(autenticando, (valor) => {
  if (!valor) {
    nextTick(() => {
      if (naveCanvas.value && pilotoCanvas.value) {
        loadModel(naveCanvas, "/models/awing.glb", {
          scale: 2,
          cameraZ: 30,
        });
        loadModel(pilotoCanvas, "/models/pilot1.glb", {
          scale: 2,
          cameraZ: 6,
        });
      } else {
        console.warn("Canvas no disponibles tras mostrar vista.");
      }
    });
  }
});

// Cierre de sesión con confirmación
const cerrarSesion = async () => {
  const confirm = await swalDark.fire({
    title: "¿Cerrar sesion?",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Salir",
    cancelButtonText: "Cancelar",
    confirmButtonColor: "#d33",
    cancelButtonColor: "#3085d6",
  });

  if (!confirm.isConfirmed) return;

  const token = localStorage.getItem("token");

  try {
    await $fetch("http://localhost:8000/api/logout", {
      method: "POST",
      headers: {
        Authorization: `Bearer ${token}`,
      },
    });

    localStorage.removeItem("token");
    router.push("/login");
  } catch (error) {
    console.error("Error al cerrar sesión:", error);
    Swal.fire({
      icon: "error",
      title: "Error",
      text: "No se pudo cerrar la sesión. Inténtalo de nuevo.",
    });
  }
};

// Middleware para proteger esta ruta (solo accesible si estás logueado)
definePageMeta({
  middleware: "auth",
});

// Precarga de vistas y datos para mejorar experiencia del usuario
onMounted(() => {
  const token = localStorage.getItem("token");
  if (!token) {
    return navigateTo("/login?unauthorized=true");
  }

  autenticando.value = false;

  // Precarga de componentes
  router.resolve("/pilots").matched.forEach((record) => {
    if (record.components && typeof record.components.default === "function") {
      record.components.default();
    }
  });

  router.resolve("/starships").matched.forEach((record) => {
    if (record.components && typeof record.components.default === "function") {
      record.components.default();
    }
  });

  // Precarga de datos desde API para hacer que se muestren algo más rapido!!
  fetch("http://localhost:8000/api/pilots")
    .then(() => console.log("[Precarga] Pilotos precargados"))
    .catch(() => console.warn("[Precarga] Error al precargar pilotos"));

  fetch("http://localhost:8000/api/starships")
    .then(() => console.log("[Precarga] Naves precargadas"))
    .catch(() => console.warn("[Precarga] Error al precargar naves"));
});

const userName = ref("");
try {
  const user = JSON.parse(localStorage.getItem("user"));
  userName.value = user?.username || "";
} catch (e) {
  console.warn("Error al leer usuario:", e);
}
</script>

<style scoped>
canvas {
  width: 100%;
  max-width: 640px;
  height: auto;
  aspect-ratio: 4 / 3;
  margin: 0 auto;
  display: block;
}

.custom-starwars {
  font-family: "Starjedi", sans-serif;
}
</style>
