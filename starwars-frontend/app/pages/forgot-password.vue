<!-- eslint-disable vue/html-self-closing -->
<template>
  <div class="min-h-screen flex items-center justify-center bg-black text-white px-4">
    <div class="bg-gray-800 p-8 rounded-xl shadow-xl max-w-md w-full">
      <h1 class="text-xl text-center mb-6 text-yellow-400 custom-starwars">recuperar clave</h1>

      <!-- FORMULARIO con validación de HTML5.. gracias al required -->
      <form @submit.prevent="handleSubmit">
        <input
          v-model="email"
          type="email"
          required
          class="w-full p-2 rounded bg-gray-700 text-yellow-500 mb-6"
          placeholder="Introduce tu email"
        />

        <button
          type="submit"
          class="bg-yellow-500 hover:bg-yellow-600 text-black font-semibold py-2 rounded-4xl w-full text-lg"
        >
          Enviar enlace
        </button>
      </form>

      <div class="text-center text-white mt-6">
        <nuxt-link
          to="login"
          class="block w-full btn rounded-4xl bg-gray-500 p-2 hover:bg-gray-600 text-lg"
        >
          Volver
        </nuxt-link>
      </div>
      <!-- <p v-if="message" class="text-green-400 mt-4">{{ message }}</p> -->
    </div>
  </div>
</template>


<script setup>
import Swal from 'sweetalert2'

const email = ref('')
const message = ref('')
const error = ref('')

const handleSubmit = async () => {
  error.value = ''
  message.value = ''

  try {
    Swal.fire({
      icon: 'info',
      title: 'Enviando...',
      text: 'Procesando solicitud...',
      showConfirmButton: false,
      allowOutsideClick: false,
      didOpen: () => {
        Swal.showLoading()
      }
    })

    await $fetch('http://localhost:8000/api/forgot-password', {
      method: 'POST',
      body: { email: email.value },
    })

    Swal.close()

    await Swal.fire({
      icon: 'success',
      title: '¡Correo enviado!',
      text: 'Revisa tu bandeja de entrada para el enlace de recuperación.',
      confirmButtonText: 'Aceptar',
    })

    message.value = 'Correo enviado correctamente.'

  } catch (err) {
    Swal.close()

    const msg = err?.data?.error || 'Error al enviar el correo.'

    await Swal.fire({
      icon: 'error',
      title: 'Error',
      text: msg,
      confirmButtonText: 'Aceptar'
    })

    error.value = msg
  }
}

</script>


<style>
.custom-starwars {
  font-family: 'Starjedi', sans-serif;
}
</style>