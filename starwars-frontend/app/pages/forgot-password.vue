<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-900 text-white px-4">
    <div class="bg-gray-800 p-8 rounded-xl shadow-xl max-w-md w-full">
      <h1 class="text-xl text-center mb-6 text-yellow-400 custom-starwars">recuperar contraseña</h1>

      <input
        v-model="email"
        type="email"
        class="w-full p-2 rounded bg-gray-700 text-white mb-6"
        placeholder="Introduce tu email"
      />

      <button
        @click="handleSubmit"
        class="bg-yellow-500 hover:bg-yellow-600 text-black font-semibold py-2 rounded-4xl w-full text-lg"
      >
        Enviar enlace
      </button>
        <div class="text-center text-white mt-6">        
          <nuxt-link to="login" class="block w-full btn rounded-4xl bg-gray-500 p-2 hover:bg-gray-600 text-lg">Volver</nuxt-link>
        </div> 

      <p v-if="message" class="text-green-400 mt-4">{{ message }}</p>
      <p v-if="error" class="text-red-400 mt-4">{{ error }}</p>
    </div>
  </div>
</template>



<script setup>
const email = ref('')
const message = ref('')
const error = ref('')

const handleSubmit = async () => {
  message.value = ''
  error.value = ''

  try {
    const res = await $fetch('http://localhost:8000/api/forgot-password', {
      method: 'POST',
      body: { email: email.value },
    })
    message.value = res.message
  } catch (err) {
    error.value = err?.data?.message || 'Error al enviar el correo'
  }
}
</script>


<style>
.custom-starwars {
  font-family: 'Starjedi', sans-serif;
}
</style>