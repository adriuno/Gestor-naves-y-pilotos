<!-- eslint-disable vue/html-self-closing -->
<template>
  <main
    id="recovery-main"
    role="main"
    class="min-h-screen flex items-center justify-center bg-black text-white px-4"
  >
    <div
      class="bg-gray-800 p-8 rounded-xl shadow-xl max-w-md w-full"
      role="region"
      aria-label="Formulario de recuperación de contraseña"
    >
      <h1
        class="text-2xl text-center mb-10 text-yellow-400 custom-starwars"
        tabindex="0"
      >
        Recuperar clave
      </h1>

      <!-- FORMULARIO con validación de HTML5.. gracias al required -->
      <form
        role="form"
        aria-label="Formulario para recuperar contraseña"
        @submit.prevent="handleSubmit"
      >
        <label for="email" class="block text-md font-semibold text-white mb-2">
          Introduce tu email
        </label>
        <input
          id="email"
          ref="emailInput"
          v-model="email"
          type="email"
          required
          class="w-full p-1 rounded text-md bg-gray-700 text-yellow-500 mb-6 border border-white focus:border-yellow-500 focus:outline-none placeholder-yellow-500 placeholder-opacity-60"
          placeholder=" usuario@ejemplo.com"
          aria-label="Campo para poner el correo electrónico del cual se quiere recuperar contraseña"
        />

        <button
          type="submit"
          class="bg-yellow-500 hover:bg-yellow-600 text-black font-semibold py-2 rounded-4xl w-full text-lg"
          aria-label="Enviar enlace de recuperación"
        >
          Enviar enlace
        </button>
      </form>

      <div class="text-center text-white mt-6" role="contentinfo">
        <nuxt-link
          to="login"
          class="block w-full btn rounded-4xl bg-gray-500 p-2 hover:bg-gray-600 text-lg"
          aria-label="Volver a la página de inicio de sesión"
        >
          Volver
        </nuxt-link>
      </div>
    </div>
  </main>
</template>

<script setup>
import { onMounted, ref } from "vue";
import Swal from "sweetalert2";
import swalDark from "@/utils/swalDark";

const email = ref("");
const message = ref("");
const error = ref("");

useHead({
  title: "Recuperar contraseña",
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


const handleSubmit = async () => {
  error.value = "";
  message.value = "";

  try {
    swalDark.fire({
      icon: "info",
      title: "Enviando solicitud...",
      showConfirmButton: false,
      allowOutsideClick: false,
      didOpen: () => {
        Swal.showLoading();
      },
    });

    await $fetch("http://localhost:8000/api/forgot-password", {
      method: "POST",
      body: { email: email.value },
    });

    Swal.close();

    await swalDark.fire({
      icon: "success",
      title: "¡Correo enviado!",
      text: "Revisa tu bandeja de entrada.",
      confirmButtonText: "Aceptar",
    });

    message.value = "Correo enviado correctamente.";
  } catch (err) {
    Swal.close();

    const msg = err?.data?.error || "Prueba mas tarde.";

    await swalDark.fire({
      icon: "error",
      title: "Error",
      text: msg,
      confirmButtonText: "Aceptar",
    });

    error.value = msg;
  }
};

// Referencia al input y así para focalizar atencion en input de email directamente!!
const emailInput = ref(null);

onMounted(() => {
  emailInput.value?.focus();
});
</script>

<style>
.custom-starwars {
  font-family: "Starjedi", sans-serif;
}
</style>
