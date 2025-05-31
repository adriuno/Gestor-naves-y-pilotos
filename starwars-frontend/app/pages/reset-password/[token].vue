<!-- eslint-disable vue/html-self-closing -->
<template>
  <div
    class="min-h-screen flex items-center justify-center bg-black text-white px-4"
    role="main"
    aria-label="Formulario para cambiar la contraseña"
  >
    <div
      class="bg-gray-800 p-8 rounded-xl shadow-xl max-w-md w-full"
      role="form"
    >
      <!-- Título -->
      <h1
        class="text-2xl font-bold mb-6 text-yellow-400 text-center"
        tabindex="0"
      >
        Nueva contraseña
      </h1>

      <!-- Campo de email oculto -->
      <input
        v-model="email"
        type="hidden"
        name="email"
      />

      <!-- Nueva contraseña -->
      <div class="relative mb-4">
        <label for="password" class="sr-only">Nueva contraseña</label>
        <input
          id="password"
          v-model="password"
          :type="showPassword ? 'text' : 'password'"
          autocomplete="new-password"
          aria-label="Escribe tu nueva contraseña"
          class="w-full p-2 pr-10 rounded bg-gray-700 text-white"
          placeholder="Nueva contraseña"
          required
          minlength="8"
        />
        <span class="absolute right-2 top-1.5">
          <UButton
            variant="link"
            size="sm"
            class="text-yellow-400"
            :icon="showPassword ? 'i-lucide-eye-off' : 'i-lucide-eye'"
            :aria-label="showPassword ? 'Ocultar contraseña' : 'Mostrar contraseña'"
            @click="showPassword = !showPassword"
          />
        </span>
      </div>

      <!-- Confirmar contraseña -->
      <div class="relative mb-4">
        <label for="password_confirmation" class="sr-only">Confirmar contraseña</label>
        <input
          id="password_confirmation"
          v-model="password_confirmation"
          :type="showConfirm ? 'text' : 'password'"
          autocomplete="new-password"
          aria-label="Repite tu nueva contraseña"
          class="w-full p-2 pr-10 rounded bg-gray-700 text-white"
          placeholder="Repetir contraseña"
          required
          minlength="8"
        />
        <span class="absolute right-2 top-1.5">
          <UButton
            variant="link"
            size="sm"
            class="text-yellow-400"
            :aria-label="showConfirm ? 'Ocultar confirmación' : 'Mostrar confirmación'"
            :icon="showConfirm ? 'i-lucide-eye-off' : 'i-lucide-eye'"
            @click="showConfirm = !showConfirm"
          />
        </span>
      </div>

      <!-- Botón -->
      <button
        class="bg-yellow-500 text-xl hover:bg-yellow-600 text-black font-semibold px-4 py-2 rounded-4xl w-full"
        aria-label="Confirmar cambio de contraseña"
        @click="handleSubmit"
      >
        Cambiar contraseña
      </button>

      <!-- Mensajes -->
      <p
        v-if="message"
        class="text-green-400 mt-4 text-sm text-center"
        aria-live="polite"
      >
        {{ message }}
      </p>
      <p
        v-if="error"
        class="text-red-400 mt-4 text-sm text-center"
        aria-live="assertive"
      >
        {{ error }}
      </p>
    </div>
  </div>
</template>

<script setup>
import Swal from "sweetalert2";
const route = useRoute();

const token = route.params.token;
// Captura automáticamente el email desde la URL
const email = ref(route.query.email || "");

const password = ref("");
const password_confirmation = ref("");
const showPassword = ref(false);
const showConfirm = ref(false);
const message = ref("");
const error = ref("");

const handleSubmit = async () => {
  message.value = "";
  error.value = "";

  if (password.value !== password_confirmation.value) {
    error.value = "Las contraseñas no coinciden.";
    return;
  }

  try {
    await $fetch("http://localhost:8000/api/reset-password", {
      method: "POST",
      body: {
        token,
        email: email.value,
        password: password.value,
        password_confirmation: password_confirmation.value,
      },
    });

    await Swal.fire({
      icon: "success",
      title: "¡Contraseña actualizada!",
      text: "Ahora puedes iniciar sesión con tu nueva contraseña.",
      timer: 2500,
      showConfirmButton: false,
    });

    navigateTo("/login");
  } catch (err) {
    error.value = err?.data?.message || "Error al cambiar la contraseña";
    Swal.fire({
      icon: "error",
      title: "Oops...",
      text: error.value,
    });
  }
};
</script>
