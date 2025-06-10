<template>
  <div
    class="relative w-full min-h-screen bg-black overflow-hidden"
    role="main"
    aria-label="Video introductorio del gestor de naves y pilotos"
  >
    <!-- Vídeo de fondo -->
    <video
      ref="videoRef"
      src="/sounds/introSW.mp4"
      preload="auto"
      autoplay
      muted
      playsinline
      class="w-full h-full object-contain md:object-cover absolute inset-0 z-0"
      tabindex="0"
      @canplay="initVideo"
      @ended="lanzarTransicion"
    />

    <!-- Botón para saltar -->
    <button
      v-if="mostrarControles"
      class="absolute top-6 right-6 z-10 bg-yellow-600 text-black font-semibold px-3 py-2 rounded-2xl text-sm hover:bg-yellow-500"
      aria-label="Saltar vídeo de introducción y acceder al login"
      @click="lanzarTransicion"
    >
      Saltar intro
    </button>

    <!-- Controles de volumen -->
    <div
      v-if="mostrarControles"
      class="fixed bottom-4 right-4 z-10 bg-black bg-opacity-60 px-4 py-2 rounded-lg text-white flex items-center space-x-2"
      role="group"
      aria-label="Control del volumen del video"
    >
      <label for="volume" class="sr-only">Control de volumen</label>
      <span aria-hidden="true">{{ volumenIcono }}</span>
      <input
        id="volume"
        v-model.number="volume"
        type="range"
        min="0"
        max="1"
        step="0.01"
        class="w-32 accent-yellow-500"
        aria-valuemin="0"
        aria-valuemax="1"
        :aria-valuenow="volume"
        aria-label="Control de volumen"
        @input="activateSound"
      >
    </div>

    <!-- Botón play/pause -->
    <!-- CON lo del :aria-label..... conseguimos que el Lector diga una cosa u otra, en función del estado de la variable!!!!!!! -->
    <button
      v-if="mostrarControles"
      class="fixed bottom-4 ml-4 z-10 hover:bg-yellow-800 rounded-2xl text-black px-2 py-1"
      :aria-label="reproduccion ? 'Pausar vídeo' : 'Reproducir vídeo'"
      :aria-pressed="reproduccion"
      aria-controls="volume"
      :class="reproduccion ? 'bg-gray-600' : 'bg-yellow-600'"
      @click="reproducir"
    >
      ▶️/⏸️
    </button>

    <!-- Capa de transición negra que sube -->
    <transition name="cortina-slide">
      <div
        v-if="mostrandoTransicion"
        class="absolute inset-0 z-20 bg-black flex items-center justify-center"
        aria-hidden="true"
        tabindex="-1"
      />
    </transition>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, nextTick } from "vue";
import { useRouter } from "vue-router";

const router = useRouter();
const videoRef = ref(null);
const volume = ref(0);
const reproduccion = ref(true);
const mostrandoTransicion = ref(false);

useHead({
  title: "Gestor | Intro",
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


//   Si ya ha visto la intro antes, redirigir directamente
// para que los controles se muestren a los 4 segundicos:
const mostrarControles = ref(false);

onMounted(() => {
  const introVista = localStorage.getItem("introVista");
  if (introVista) {
    router.replace("/login");
  }

  // Mostrar botones de control tras 4 segundos
  setTimeout(() => {
    mostrarControles.value = true;
  }, 1900);
});

//  Marca como vista y lanza la animación de salida
const lanzarTransicion = async () => {
  localStorage.setItem("introVista", "true");
  mostrandoTransicion.value = true;
  await nextTick();
  setTimeout(() => {
    router.push("/login");
  }, 1000); // Coincide con duración CSS de transición
};

const initVideo = () => {
  if (videoRef.value) {
    videoRef.value.volume = volume.value;
  }
};

const reproducir = () => {
  if (videoRef.value.paused) {
    videoRef.value.play();
    reproduccion.value = true;
  } else {
    videoRef.value.pause();
    reproduccion.value = false;
  }
};

const activateSound = () => {
  if (videoRef.value) {
    videoRef.value.muted = false;
    videoRef.value.volume = volume.value;
  }
};

const volumenIcono = computed(() => {
  if (volume.value === 0) return "🔇";
  if (volume.value > 0.5) return "🔊";
  if (volume.value > 0) return "🔉";
  return "🔊";
});
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
