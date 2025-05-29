<template>
  <footer class="w-full mt-10 text-sm text-gray-400 relative z-0">
    <div class="max-w-screen-xl mx-auto px-4 py-6 flex flex-col items-center text-center gap-2">
      <!-- GitHub -->
      <div class="flex items-center gap-2 text-yellow-400">
        <a
          href="https://github.com/adriuno"
          target="_blank"
          class="hover:underline hover:text-yellow-600 hover:italic text-base"
        >
          <i class="fa-brands fa-github text-3xl mr-1"></i>
          GitHub
        </a>
      </div>

      <!-- Enlace para ver intro -->
      <button
        class="text-yellow-400 hover:underline hover:text-yellow-600 hover:italic text-sm"
        @click="abrirIntro"
      >
        Ver INTRO otra vez
      </button>

      <!-- Modal de la intro -->
      <div v-if="mostrarIntro" class="fixed inset-0 bg-black bg-opacity-80 backdrop-blur-sm flex items-center justify-center z-50">
        <div class="relative w-full max-w-4xl aspect-video rounded-xl overflow-hidden shadow-2xl">
          <video
            ref="videoRef"
            src="/sounds/introSW.mp4"
            autoplay
            playsinline
            class="w-full h-full object-cover"
            @canplay="activarSonido"
          />

          <!-- Botón para cerrar -->
          <button
            class="absolute top-2 right-2 bg-red-600 text-white px-3 py-2 rounded-xl text-sm hover:bg-red-700 z-10"
            @click="cerrarIntro"
          >
            Cerrar
          </button>

          <!-- Control de volumen -->
          <div class="absolute bottom-4 right-4 bg-black bg-opacity-60 px-3 py-2 rounded-lg text-white flex items-center gap-2 z-10">
            <label for="volume" class="text-lg">{{ volumenIcono }}</label>
            <input
              id="volume"
              v-model.number="volume"
              type="range"
              min="0"
              max="1"
              step="0.01"
              class="w-32 accent-yellow-500"
              @input="cambiarVolumen"
            >
          </div>
        </div>
      </div>

      <!-- Créditos -->
      <p>© 2025 Gestor de naves y pilotos.</p>
      <p><i> Por Adrián Ginel Mañas. </i></p>
    </div>
  </footer>
</template>

<script setup>
import { ref, computed, nextTick } from 'vue'

const mostrarIntro = ref(false)
const videoRef = ref(null)
const volume = ref(0.5) // volumen por defecto

const abrirIntro = async () => {
  localStorage.removeItem('introVista')  //esto hace que se resetee y al entrar de nuevon en la web se pueda ver la intro como desde 0
  mostrarIntro.value = true
  await nextTick()
  activarSonido()
}

const cerrarIntro = () => {
  mostrarIntro.value = false
  if (videoRef.value) {
    videoRef.value.pause()
    videoRef.value.currentTime = 0
  }
}

const cambiarVolumen = () => {
  if (videoRef.value) {
    videoRef.value.muted = false
    videoRef.value.volume = volume.value
  }
}

const activarSonido = () => {
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
