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
      <h1
        class="text-2xl font-bold mb-6 text-yellow-400 text-center"
        tabindex="0"
      >
        Nueva contraseña
      </h1>

      <form
        aria-describedby="formDescription"
        autocomplete="off"
        @submit.prevent="handleSubmit"
      >
        <fieldset class="border-0 p-0 m-0">
          <legend class="sr-only">Formulario para cambiar contraseña</legend>

          <p id="formDescription" class="sr-only">
            Introduce tu nueva contraseña y confírmala.
          </p>

          <!-- Email oculto -->
          <input type="hidden" :value="email" name="email" />

          <!-- Nueva contraseña -->
          <div class="relative mb-4">
            <label for="password" class="sr-only">Nueva contraseña</label>
            <input
              id="password"
              v-model="password"
              :type="showPassword ? 'text' : 'password'"
              autocomplete="new-password"
              aria-label="Escribe tu nueva contraseña"
              aria-describedby="passwordHelp"
              :class="[
                'w-full p-1 pr-10 rounded bg-gray-700 text-white border focus:border-yellow-500 focus:outline-none',
                passwordTooShort
                  ? 'border-red-500'
                  : password.length >= passwordMinLength
                  ? 'border-green-500'
                  : 'border-gray-600',
              ]"
              placeholder="Nueva contraseña"
              required
              minlength="8"
            />
            <!-- Botón ojo -->
            <button
              type="button"
              class="absolute top-1.5 right-2 text-yellow-400"
              :aria-label="
                showPassword ? 'Ocultar contraseña' : 'Mostrar contraseña'
              "
              @click="showPassword = !showPassword"
            >
              <UIcon
                :name="showPassword ? 'i-lucide-eye-off' : 'i-lucide-eye'"
              />
            </button>

            <p
              id="passwordHelp"
              class="text-xs mt-1 italic"
              :class="passwordTooShort ? 'text-red-400' : 'text-gray-400'"
            >
              {{
                passwordTooShort
                  ? "Debe tener al menos 8 caracteres"
                  : "Mínimo 8 caracteres."
              }}
            </p>
          </div>

          <!-- Confirmar contraseña -->
          <div class="relative mb-4">
            <label for="password_confirmation" class="sr-only"
              >Confirmar contraseña</label
            >
            <input
              id="password_confirmation"
              v-model="password_confirmation"
              :type="showConfirm ? 'text' : 'password'"
              autocomplete="new-password"
              aria-label="Repite tu nueva contraseña"
              :class="[
                'w-full p-1 pr-10 rounded bg-gray-700 text-white border focus:border-yellow-500 focus:outline-none',
                password_confirmation.length && !passwordsMatch
                  ? 'border-red-500'
                  : password_confirmation.length && passwordsMatch
                  ? 'border-green-500'
                  : 'border-gray-600',
              ]"
              placeholder="Repetir contraseña"
              required
              minlength="8"
            />

            <!-- Botón ojo -->
            <button
              type="button"
              class="absolute top-1.5 right-2 text-yellow-400"
              :aria-label="
                showConfirm ? 'Ocultar confirmación' : 'Mostrar confirmación'
              "
              @click="showConfirm = !showConfirm"
            >
              <UIcon
                :name="showConfirm ? 'i-lucide-eye-off' : 'i-lucide-eye'"
              />
            </button>

            <!-- Validación coincidencia -->
            <p
              v-if="password_confirmation.length > 0"
              class="text-xs mt-1 italic"
              :class="passwordsMatch ? 'text-green-400' : 'text-red-400'"
            >
              {{
                passwordsMatch
                  ? "Ambas coinciden"
                  : "Las contraseñas no coinciden"
              }}
            </p>
          </div>

          <!-- Botón -->
          <button
            type="submit"
            class="bg-yellow-500 text-xl hover:bg-yellow-600 text-black font-semibold px-4 py-2 rounded-4xl w-full"
            aria-label="Confirmar cambio de contraseña"
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
            ref="errorRef"
            tabindex="-1"
            class="text-red-400 mt-4 text-sm text-center"
            aria-live="assertive"
          >
            {{ error }}
          </p>
        </fieldset>
      </form>
    </div>
  </div>
</template>

<script setup>
import swalDark from "@/utils/swalDark";

useHead({
  title: "Nueva contraseñas",
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



const route = useRoute();

const token = route.params.token;
// Captura automáticamente el email desde la URL
const email = ref(route.query.email || "");
// captura el email tbn

const password = ref("");
const password_confirmation = ref("");
const showPassword = ref(false);
const showConfirm = ref(false);
const message = ref("");
const error = ref("");
const config = useRuntimeConfig();


const handleSubmit = async () => {
  message.value = "";
  error.value = "";

  if (password.value !== password_confirmation.value) {
    error.value = "Las contraseñas no coinciden.";
    return;
  }

  try {

    await $fetch(`${config.public.API_URL}/api/reset-password`, {
      method: "POST",
      body: {
        token,
        email: email.value,
        password: password.value,
        password_confirmation: password_confirmation.value,
      },
    });


    await swalDark.fire({
      icon: "success",
      title: "¡Clave actualizada!",
      timer: 2500,
      showConfirmButton: false,
    });

    navigateTo("/login");
  } catch (err) {
    error.value = err?.data?.message || "Error al cambiar la contraseña";
    swalDark.fire({
      icon: "error",
      title: "Oops...",
      text: error.value,
    });
  }
};

const passwordMinLength = 8;

const passwordTooShort = computed(
  () => password.value.length > 0 && password.value.length < passwordMinLength
);

const passwordsMatch = computed(
  () =>
    password.value &&
    password_confirmation.value &&
    password.value === password_confirmation.value
);
</script>
