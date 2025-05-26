<!-- eslint-disable vue/html-self-closing -->
<template>
  <div class="min-h-screen flex items-center justify-center bg-black text-white px-4">
    <div class="bg-gray-800 p-8 rounded-xl shadow-xl max-w-md w-full">
      <h1 class="text-2xl font-bold mb-6 text-yellow-400 text-center">Nueva contraseña</h1>

      <input
        v-model="email"
        type="email"
        class="w-full p-2 rounded bg-gray-700 text-white mb-4"
        placeholder="Tu email"
      />

    <!-- Contraseña -->
    <div class="relative mb-4">
    <input
        v-model="password"
        :type="showPassword ? 'text' : 'password'"
        class="w-full p-2 pr-10 rounded bg-gray-700 text-white"
        placeholder="Nueva contraseña"
    />
    <span class="absolute right-2 top-1.5">
        <UButton
        variant="link"
        size="sm"
        class="text-yellow-400"
        :icon="showPassword ? 'i-lucide-eye-off' : 'i-lucide-eye'"
        @click="showPassword = !showPassword"
        />
    </span>
    </div>

    <!-- Confirmación -->
    <div class="relative mb-4">
    <input
        v-model="password_confirmation"
        :type="showConfirm ? 'text' : 'password'"
        class="w-full p-2 pr-10 rounded bg-gray-700 text-white"
        placeholder="Confirmar contraseña"
    />
    <span class="absolute right-2 top-1.5">
        <UButton
        variant="link"
        size="sm"
        class="text-yellow-400"
        :icon="showConfirm ? 'i-lucide-eye-off' : 'i-lucide-eye'"
        @click="showConfirm = !showConfirm"
        />
    </span>
    </div>



      <button
        class="bg-yellow-500 text-xl hover:bg-yellow-600 text-black font-semibold px-4 py-2 rounded-4xl w-full"
        @click="handleSubmit"
      >
        Cambiar contraseña
      </button>

      <p v-if="message" class="text-green-400 mt-4 text-sm text-center">{{ message }}</p>
      <p v-if="error" class="text-red-400 mt-4 text-sm text-center">{{ error }}</p>
    </div>
  </div>
</template>

<script setup>
import Swal from 'sweetalert2'
const route = useRoute()

const token = route.params.token
const email = ref('')
const password = ref('')
const password_confirmation = ref('')
const showPassword = ref(false)
const showConfirm = ref(false)
const message = ref('')
const error = ref('')

const handleSubmit = async () => {
  message.value = ''
  error.value = ''

  // Validación antes de enviar
  if (password.value !== password_confirmation.value) {
    error.value = 'Las contraseñas no coinciden.'
    return
  }

  try {

  await $fetch('http://localhost:8000/api/reset-password', {
    method: 'POST',
    body: {
      token,
      email: email.value,
      password: password.value,
      password_confirmation: password_confirmation.value
    },
  })

  await Swal.fire({
    icon: 'success',
    title: '¡Contraseña actualizada!',
    text: 'Ahora puedes iniciar sesión con tu nueva contraseña.',
    timer: 2500,
    showConfirmButton: false,
  })

  navigateTo('/login')

} catch (err) {
  error.value = err?.data?.message || 'Error al cambiar la contraseña'
  Swal.fire({
    icon: 'error',
    title: 'Oops...',
    text: error.value,
  })
}
}


</script>
