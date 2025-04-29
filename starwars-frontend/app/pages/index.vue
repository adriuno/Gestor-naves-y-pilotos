<template>
  <div class="relative w-full min-h-screen md:h-screen overflow-y-auto bg-black">
    <!-- Vídeo que siempre se ve -->
    <video
      ref="videoRef"
      src="/sounds/introSW.mp4"
      autoplay
      muted
      playsinline
      class="w-full h-full object-cover"
      @canplay="initVideo"
      @ended="goToLogin"
    />

    <!-- Botón para saltar -->
    <button
      class="absolute top-6 right-6 bg-yellow-600 text-black font-semibold px-3 py-2 rounded-2xl text-sm hover:bg-yellow-500"
      @click="goToLogin"
    >
      Saltar intro  
    </button>


    <!-- Control de volumen fijo abajo a la derecha -->
    <!-- Se puede usar FIXED o STICKY para fijar la barra o algún elmnto.. si hace falta cuando haya scroll  -->
    <div
      class="fixed bottom-4 right-4 bg-black bg-opacity-60 px-4 py-2 rounded-lg text-white flex items-center space-x-2 z-50"
    >
    <label for="volume">{{ volumenIcono }}</label>
    <input
        id="volume"
        v-model.number="volume"
        type="range"
        min="0"
        max="1"
        step="0.01"
        
        class="w-32 accent-yellow-500"
        @input="activateSound"
      >
    </div>
    <div>
      <button
        class="fixed bottom-4 ml-4 hover:bg-yellow-800 rounded-2xl text-black px-2 py-1"
        :class="reproduccion ? 'bg-gray-600' : 'bg-yellow-600'"
        @click="reproducir">▶️/⏸️</button>
    </div>
  </div>
</template>



<script setup>

const router = useRouter()

const videoRef = ref(null)
const reproduccion = ref(true)
const volume = ref(0) // volumen inicial a mitad

const initVideo = () => {
  if (videoRef.value) {
    videoRef.value.volume = volume.value
  }
}

const reproducir = () => {
  if (videoRef.value.paused) {
  videoRef.value.play()
  reproduccion.value = true
} else {
  videoRef.value.pause()
  reproduccion.value = false
}
// y si quisieramos con ternario: 
// videoRef.value.paused ? videoRef.value.play() : videoRef.value.pause()
}

// esta funcion hace que al interactuar el usuario, se quite el MUTED y se ajuste el volumen del video!!!!!
const activateSound = () => {
  if (videoRef.value) {
    videoRef.value.muted = false // activa sonido al mover el slider
    videoRef.value.volume = volume.value
  }
}

// Función para irse a login
const goToLogin = () => {
  router.push('/login')
}

// Si queremos 3 opciones por ejemplo:  OJO daba error xk computed() esperaba un return.. al final
const volumenIcono = computed(() => {
  if (volume.value === 0) return '🔇'
  if (volume.value > 0.5) return '🔊'
  if (volume.value > 0) return '🔉'
  return '🔊'
})


/* Para que se muestre un iconito u otro:  CON UN TERNARIO SE HACE FÁCIL PAR 2 opciones
const volumeEmoji = computed(() => {
  return volume.value > 0 ? '🔊' : '🔇'  aunque ojo... se pueden anidar ternarios??
})
  */


</script>
