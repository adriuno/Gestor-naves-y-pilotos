<template>
  <div v-if="!autenticando"
    class="relative min-h-screen bg-black bg-cover bg-center bg-no-repeat text-white px-6 py-4 flex flex-col justify-between"
    style="background-image: url('/images/sw3.jpg')"
  >
    <!-- Botón logout -->
    <UButton
      class="absolute py-2 sm:py-3 sm:px-3 sm:text-2xl top-4 sm:top-5 right-2 sm:right-15 bg-red-600 rounded-3xl
              hover:bg-red-700 hover:scale-115 transition-transform z-10
              hover:drop-shadow-[0_0_8px_#38bdf8] transition duration-200"
      @click="cerrarSesion"
    >
      <i class="fa-solid fa-power-off" />
    </UButton>

    <!-- Título -->
    <div class="flex justify-center">
      <h1 class="inline-block text-3xl sm:text-4xl text-black bg-yellow-400 mt-9 sm:mt-4 custom-starwars rounded-4xl sm:px-5 py-4 text-center">
        selecciona una opcion
      </h1>
    </div>

    <!-- Naves y Pilotos -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-center">
      <!-- Nave -->
      <div class="flex flex-col items-center">
        <canvas ref="naveCanvas" class="h-32 w-full" />
        <button
          class="inline-block text-2xl text-black bg-yellow-400 bg-gray-500 mt-1 mb-8 custom-starwars rounded-4xl px-6 py-3 
                 hover:scale-155 transition-transform 
                 hover:text-yellow-500 hover:bg-violet-600 transition-colors duration-300"
          @click="goTo('home')"
        >
          Ver Naves
        </button>
      </div>

      <!-- Piloto -->
      <div class="flex flex-col items-center">
        <canvas ref="pilotoCanvas" class="h-32 w-full" />
        <button
          class="inline-block text-2xl text-black bg-yellow-400 bg-gray-500 mt-2 mb-9 custom-starwars rounded-4xl px-6 py-3 
                 hover:scale-155 transition-transform 
                 hover:text-yellow-500 hover:bg-violet-600 transition-colors duration-300"
          @click="goTo('pilots')"
        >
          Ver Pilotos
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import Swal from 'sweetalert2'
import * as THREE from 'three'
import { GLTFLoader } from 'three/examples/jsm/loaders/GLTFLoader'
import { onMounted, nextTick, watch, ref } from 'vue'
import { useRouter } from 'vue-router'


const router = useRouter()
const naveCanvas = ref()
const pilotoCanvas = ref()
const modelCache = {}

const goTo = (section) => {
  router.push(`/${section}`)
}

const loadModel = async (canvasRef, modelUrl, options = {}) => {
  const scene = new THREE.Scene()

  const camera = new THREE.PerspectiveCamera(75, 1, 0.1, 1000)
  camera.position.z = options.cameraZ || 6

  const renderer = new THREE.WebGLRenderer({
    canvas: canvasRef.value,
    alpha: true,
    antialias: false, // Desactiva si quieres más rendimiento
  })
  renderer.setSize(canvasRef.value.clientWidth, canvasRef.value.clientHeight)
  renderer.setPixelRatio(window.devicePixelRatio)

  // Luz ambiental más fuerte y cálida
  const ambientLight = new THREE.AmbientLight(0xffffff, 1.5)
  scene.add(ambientLight)

  // Luz direccional que simula el sol
  const directionalLight = new THREE.DirectionalLight(0xffffff, 1.5)
  directionalLight.position.set(3, 5, 2)
  scene.add(directionalLight)

  let model

  // Cacheo del modelo
  if (modelCache[modelUrl]) {
    model = modelCache[modelUrl].clone()
  } else {
    const gltf = await new Promise((resolve, reject) => {
      new GLTFLoader().load(modelUrl, resolve, undefined, reject)
    })
    model = gltf.scene
    modelCache[modelUrl] = model.clone()
  }

  const scale = options.scale || 1.5
  model.scale.set(scale, scale, scale)
  model.position.set(0, -0.5, 0)
  scene.add(model)

  let mouseX = 0
  let mouseY = 0

  const renderScene = () => {
    model.rotation.y = mouseX * 1.5
    model.rotation.x = mouseY * 1.0
    renderer.render(scene, camera)
  }

  renderScene() // Primer render

  // Solo renderizar cuando el ratón se mueva
  canvasRef.value.addEventListener('mousemove', (event) => {
    const rect = canvasRef.value.getBoundingClientRect()
    const x = (event.clientX - rect.left) / rect.width
    const y = (event.clientY - rect.top) / rect.height
    mouseX = (x - 0.5) * 6
    mouseY = (y - 0.5) * 2
    renderScene()
  })
}


const autenticando = ref(true)

onMounted(() => {
  const token = localStorage.getItem("token")
  if (!token) {
    return navigateTo("/login?unauthorized=true")
  }

  autenticando.value = false // 👉 Esto hace que se muestre el DOM con los canvas
})



watch(autenticando, (valor) => {
  if (!valor) {
    nextTick(() => {
      if (naveCanvas.value && pilotoCanvas.value) {
        loadModel(naveCanvas, '/models/awing.glb', {
          scale: 2,
          cameraZ: 30,
        })

        loadModel(pilotoCanvas, '/models/pilot1.glb', {
          scale: 2,
          cameraZ: 6,
        })
      } else {
        console.warn('Canvas no disponibles tras mostrar vista.')
      }
    })
  }
})


const cerrarSesion = async () => {
  const confirm = await Swal.fire({
    title: '¿Cerrar sesión?',
    text: 'Vas a salir de la aplicación',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Sí, cerrar',
    cancelButtonText: 'Cancelar',
    confirmButtonColor: '#d33',
    cancelButtonColor: '#3085d6',
  })

  if (!confirm.isConfirmed) return

  const token = localStorage.getItem('token')

  try {
    await $fetch('http://localhost:8000/api/logout', {
      method: 'POST',
      headers: {
        Authorization: `Bearer ${token}`,
      },
    })

    localStorage.removeItem('token')
    router.push('/login')
  } catch (error) {
    console.error('Error al cerrar sesión:', error)
    Swal.fire({
      icon: 'error',
      title: 'Error',
      text: 'No se pudo cerrar la sesión. Inténtalo de nuevo.',
    })
  }
}

// Y para ver si se stá o no autenticado......

definePageMeta({
  middleware: 'auth',
})

onMounted(() => {
  const token = localStorage.getItem("token")
  if (!token) {
    return navigateTo("/login?unauthorized=true")
  }

  autenticando.value = false // 👉 Muestra la vista

  // Precarga del componente /pilots
  router.resolve('/pilots').matched.forEach((record) => {
    if (record.components && typeof record.components.default === 'function') {
      record.components.default()
    }
  })

  // Precarga del componente /home (naves)
  router.resolve('/home').matched.forEach((record) => {
    if (record.components && typeof record.components.default === 'function') {
      record.components.default()
    }
  })

  // Precarga de datos de pilotos
  fetch('http://localhost:8000/api/pilots')
    .then(() => console.log('[Precarga] Pilotos precargados'))
    .catch(() => console.warn('[Precarga] Error al precargar pilotos'))

  // Precarga de datos de naves
  fetch('http://localhost:8000/api/starships')
    .then(() => console.log('[Precarga] Naves precargadas'))
    .catch(() => console.warn('[Precarga] Error al precargar naves'))
})


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
  font-family: 'Starjedi', sans-serif;
}
</style>
