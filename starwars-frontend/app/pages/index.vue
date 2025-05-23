<template>
  <div class="relative w-full min-h-screen bg-black overflow-hidden">
    <!-- Vídeo de fondo -->
    <video
      ref="videoRef"
      src="/sounds/introSW.mp4"
      autoplay
      muted
      playsinline
      class="w-full h-full object-cover absolute inset-0 z-0"
      @canplay="initVideo"
      @ended="lanzarTransicion"
    />

    <!-- Botón para saltar -->
    <button
      class="absolute top-6 right-6 z-10 bg-yellow-600 text-black font-semibold px-3 py-2 rounded-2xl text-sm hover:bg-yellow-500"
      @click="lanzarTransicion"
    >
      Saltar intro
    </button>

    <!-- Controles -->
    <div class="fixed bottom-4 right-4 z-10 bg-black bg-opacity-60 px-4 py-2 rounded-lg text-white flex items-center space-x-2">
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

    <!-- Botón play/pause -->
    <button
      class="fixed bottom-4 ml-4 z-10 hover:bg-yellow-800 rounded-2xl text-black px-2 py-1"
      :class="reproduccion ? 'bg-gray-600' : 'bg-yellow-600'"
      @click="reproducir"
    >
      ▶️/⏸️
    </button>

    <!-- Capa de transición negra que sube -->
    <transition name="cortina-slide" @after-enter="redirigirLogin">
      <div
        v-if="mostrandoTransicion"
        class="absolute inset-0 z-20 bg-black flex items-center justify-center"
      >
        <!-- Puedes poner un loader aquí si quieres -->
      </div>
    </transition>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const videoRef = ref(null)
const volume = ref(0)
const reproduccion = ref(true)
const mostrandoTransicion = ref(false)

const lanzarTransicion = () => {
  mostrandoTransicion.value = true
}

const redirigirLogin = () => {
  router.push('/login')
}

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
}

const activateSound = () => {
  if (videoRef.value) {
    videoRef.value.muted = false
    videoRef.value.volume = volume.value
  }
}

const volumenIcono = computed(() => {
  if (volume.value === 0) return '🔇'
  if (volume.value > 0.5) return '🔊'
  if (volume.value > 0) return '🔉'
  return '🔊'
})
</script>

<style scoped>
.cortina-slide-enter-active {
  transition: transform 1s ease-in-out, opacity 0.8s ease;
}
.cortina-slide-enter-from {
  transform: translateY(100%);
  opacity: 0;
}
.cortina-slide-enter-to {
  transform: translateY(0%);
  opacity: 1;
}
</style>
