<template>
    <!-- Fondo a pantalla completa -->
    <div
      class="min-h-screen bg-cover bg-center text-white p-6 text-center"
      style="background-image: url('/images/sw3.jpg')"
    >
    <h1
        class="inline-block text-4xl text-black bg-yellow-400 mt-5 custom-starwars rounded-4xl px-5 py-4"
        >
        selecciona una opcion
    </h1>
  
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <!-- Nave -->
    <div>
    <canvas ref="naveCanvas" />
    <button
        class="inline-block text-2xl text-black bg-yellow-400 bg-gray-500 mt-1 mb-5 custom-starwars rounded-4xl px-6 py-3 
        hover:scale-155 transition-transform 
        hover:text-yellow-500 hover:bg-violet-600 transition-colors duration-300"
        @click="goTo('home')"
    >
        Ver Naves
    </button>
    </div>

    <!-- Piloto -->
    <div>
    <canvas ref="pilotoCanvas" />
    <button
        class="inline-block text-2xl text-black bg-yellow-400 bg-gray-500 mt-2 mb-4 custom-starwars rounded-4xl px-6 py-3 
            hover:scale-155 transition-transform hover:text-yellow-500 hover:bg-violet-600 transition-colors duration-300"
        @click="goTo('pilots')"
    >
        Ver Pilotos
    </button>
    </div>

      </div>
    </div>
  </template>
  
  <script setup>
import * as THREE from 'three'
import { GLTFLoader } from 'three/examples/jsm/loaders/GLTFLoader'
import { onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const naveCanvas = ref()
const pilotoCanvas = ref()

const goTo = (section) => {
  router.push(`/${section}`)
}

// Función para cargar un modelo 3D con tamaño fijo personalizado
const loadModel = (canvasRef, modelUrl, options = {}) => {
  const scene = new THREE.Scene()

  const camera = new THREE.PerspectiveCamera(75, 1, 0.1, 1000)
  camera.position.z = options.cameraZ || 6 // Distancia de cámara por defecto: 6

  const renderer = new THREE.WebGLRenderer({
    canvas: canvasRef.value,
    alpha: true,
    antialias: true,
  })
  renderer.setSize(canvasRef.value.clientWidth, canvasRef.value.clientHeight)
  renderer.setPixelRatio(window.devicePixelRatio)
  renderer.shadowMap.enabled = true

  // Luces de escena
  scene.add(new THREE.AmbientLight(0xffffff, 1.2))

  const directionalLight = new THREE.DirectionalLight(0xffffff, 2)
  directionalLight.position.set(5, 5, 5)
  directionalLight.castShadow = true
  scene.add(directionalLight)

  const fillLight = new THREE.DirectionalLight(0xffffff, 1)
  fillLight.position.set(-5, 2, 2)
  scene.add(fillLight)

  const backLight = new THREE.DirectionalLight(0xffffff, 0.6)
  backLight.position.set(0, 2, -5)
  scene.add(backLight)

  let model = null

  const loader = new GLTFLoader()
  loader.load(modelUrl, (gltf) => {
    model = gltf.scene

    // Escala personalizada
    const scale = options.scale || 1.5
    model.scale.set(scale, scale, scale)

    model.position.set(0, -0.5, 0)

    model.traverse((child) => {
      if (child.isMesh) {
        child.castShadow = true
        child.receiveShadow = true
      }
    })

    scene.add(model)
    animate()
  })

  // Movimiento del ratón → rotación del modelo
  let mouseX = 0
  let mouseY = 0
  canvasRef.value.addEventListener('mousemove', (event) => {
    const rect = canvasRef.value.getBoundingClientRect()
    const x = (event.clientX - rect.left) / rect.width
    const y = (event.clientY - rect.top) / rect.height
    mouseX = (x - 0.5) * 6
    mouseY = (y - 0.5) * 2
  })

  // Render loop
  function animate() {
    requestAnimationFrame(animate)
    if (model) {
      model.rotation.y = mouseX * 1.5
      model.rotation.x = mouseY * 1.0
    }
    renderer.render(scene, camera)
  }
}

// Llamadas con tamaño personalizado
onMounted(() => {
  // La nave: más grande, más alejada
  loadModel(naveCanvas, '/models/awing.glb', {
    scale: 2,
    cameraZ: 30,
  })

  // El piloto: un poco más pequeño, más cerca
  loadModel(pilotoCanvas, '/models/pilot1.glb', {
    scale: 2,
    cameraZ: 5,
  })
})
</script>

  
  <style scoped>
    canvas {
    width: 100%;
    max-width: 640px;  /* o 250px si quieres más pequeño */
    height: auto;
    aspect-ratio: 4 / 3; /* relación de aspecto cómoda */
    margin: 0 auto;
    display: block;
    }


    .custom-starwars {
        font-family: 'Starjedi', sans-serif;
    }

  </style>
  