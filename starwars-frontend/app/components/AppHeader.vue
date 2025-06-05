<!-- eslint-disable vue/html-self-closing -->
<!-- componente ...  Navbar -->
<template>
  <header
    role="banner"
    class="custom-starwars bg-black/80 shadow-md h-20 z-50 px-6 flex items-center justify-between relative"
    aria-label="Encabezado principal con navegación, nombre de usuario y botón de cierre de sesión"
  >
    <!-- Volver a la izquierda -->
    <div>
      <nuxt-link
        to="/options"
        class="text-yellow-400 transform hover:scale-115 transition duration-200 flex flex-col sm:flex-row items-center ml-3 text-center"
        aria-label="Volver al menú de opciones"
      >
        <i
          class="fa-solid fa-arrow-left text-3xl sm:mr-2 mb-1 sm:mb-0"
          aria-hidden="true"
        />
        <span class="text-sm sm:text-base">Volver</span>
      </nuxt-link>
    </div>

    <!-- Bloque central: STAR / WARS bien centrado -->
    <div
      class="absolute left-1/2 -translate-x-1/2 flex items-center space-x-4 ml-8"
      aria-label="Logo de la aplicación Star Wars App"
      title="Volver a opciones"
    >
      <div class="text-yellow-400 text-3xl leading-none text-center">
        <nuxt-link
          to="/options"
          class="flex flex-col"
          aria-label="Inicio de la aplicación"
        >
          <p aria-hidden="true">&nbsp;&nbsp;&nbsp;STAR<sup>&reg;</sup></p>
          <p aria-hidden="true">WARS</p>
        </nuxt-link>
      </div>
      <span
        class="text-yellow-400 text-xs sm:text-xl md:text-2xl mr-2"
        aria-hidden="true"
        >APP</span
      >
    </div>

    <!-- Contenedor de nombre y logout -->
    <div
      class="absolute top-4 right-2 sm:top-5 sm:right-11 z-10 flex flex-col item-center sm:flex-row items-end sm:items-center gap-1 sm:gap-4"
      role="group"
      aria-label="Usuario y botón de cierre de sesión"
    >
      <!-- Nombre de usuario, oculto en móviles -->
      <div
        v-if="userName"
        class="hidden md:flex text-white text-xs sm:text-sm bg-gray-800 bg-opacity-80 px-3 py-1 rounded-full border border-yellow-500 items-center"
        style="font-size: 14px"
        tabindex="0"
        :aria-label="`Nombre de usuario: ${userName}`"
        :title="`Usuario: ${userName}`"
      >
        <i class="fa-solid fa-user text-yellow-400 mr-2" aria-hidden="true" />
        {{ userName }}
      </div>


      <!-- Botón logout -->
      <UButton
        aria-label="Cerrar sesión"
        title="Cerrar sesión"
        class="py-1 sm:py-3 sm:px-3 sm:text-2xl bg-red-600 rounded-3xl hover:bg-red-700 hover:scale-115 transition-transform hover:drop-shadow-[0_0_8px_#38bdf8] duration-200"
        @click="cerrarSesion"
      >
        <i class="fa-solid fa-power-off" aria-hidden="true" />
      </UButton>
    </div>
  </header>
</template>

<script setup>
import swalDark from "@/utils/swalDark";
const config = useRuntimeConfig();


const userName = ref("");
try {
  const user = JSON.parse(localStorage.getItem("user"));
  userName.value = user?.username || "";
} catch (e) {
  console.warn("Error al leer usuario:", e);
}

const cerrarSesion = async () => {
  const confirm = await swalDark.fire({
    title: "¿Cerrar sesion?",
    text: "¡Vas a salir de la aplicación!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: " Salir ",
    cancelButtonText: "Cancelar",
    confirmButtonColor: "#d33",
    cancelButtonColor: "#3085d6",
  });

  if (!confirm.isConfirmed) return;

  const token = localStorage.getItem("token");

  try {

    await $fetch(`${config.public.API_URL}/api/logout`, {
      method: "POST",
      headers: {
        Authorization: `Bearer ${token}`,
      },
    });



    localStorage.removeItem("token");

    // Redirigir directamente sin mostrar mensaje
    navigateTo("/login");
  } catch (error) {
    console.error("Error al cerrar sesión:", error);
    swalDark.fire({
      icon: "error",
      title: "Error",
      text: "No se pudo cerrar la sesión. Inténtalo de nuevo.",
    });
  }
};
</script>

<style>
.custom-starwars {
  font-family: "Starjedi", sans-serif;
}

header {
  position: sticky;
  top: 0;
  z-index: 1;
}
</style>
