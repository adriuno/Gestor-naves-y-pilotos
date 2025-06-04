<template>
  <!-- Contenido principal -->
  <main
    id="registro-main"
    role="main"
    class="min-h-screen flex items-center justify-center bg-black px-8"
  >
    <div
      class="w-full max-w-md bg-gray-800 p-8 rounded-2xl shadow-lg"
      aria-label="Formulario de registro"
    >
      <h1
        class="text-3xl text-center text-yellow-500 mb-8 custom-starwars"
        tabindex="0"
      >
        Registro
      </h1>

      <UForm :state="form" class="space-y-6" role="form" @submit="handleSubmit">
        <!-- Campo usuario -->
        <UFormField name="username">
          <template #label>
            <span class="text-gray-300 font-semibold">Nombre de usuario</span>
          </template>
          <UInput
            v-model="form.username"
            placeholder="Usuario3"
            icon="i-lucide-user"
            class="w-full"
            :ui="{ base: 'bg-gray-800 text-yellow-500 opacity-90' }"
            aria-label="Campo para el nombre de usuario"
          />
          <p
            v-if="errors.username"
            class="text-red-500 italic text-sm mt-1"
            role="alert"
          >
            {{ errors.username[0] }}
          </p>
        </UFormField>

        <!-- Campo email -->
        <UFormField name="email">
          <template #label>
            <span class="text-gray-300 font-semibold">Email</span>
          </template>
          <UInput
            v-model="form.email"
            placeholder="usuario@ejemplo.com"
            icon="i-lucide-mail"
            class="w-full"
            :ui="{ base: 'bg-gray-800 text-yellow-500 opacity-90' }"
            aria-label="Campo para el correo electrónico"
          />
          <p
            v-if="errors.email"
            class="text-red-500 italic text-sm mt-1"
            role="alert"
          >
            {{ errors.email[0] }}
          </p>
        </UFormField>

        <!-- Campo contraseña -->
        <UFormField name="password">
          <template #label>
            <span class="text-gray-300 font-semibold">Contraseña</span>
          </template>
          <UInput
            v-model="form.password"
            :type="show ? 'text' : 'password'"
            placeholder="Secreto123"
            icon="i-lucide-lock"
            class="w-full"
            :ui="{
              base: 'bg-gray-800 text-yellow-500 opacity-90',
              trailing: 'pe-1',
            }"
            :aria-invalid="score < 4"
            aria-describedby="password-strength"
            aria-label="Campo para la contraseña"
          >
            <template #trailing>
              <UButton
                variant="link"
                size="sm"
                :icon="show ? 'i-lucide-eye-off' : 'i-lucide-eye'"
                :aria-label="show ? 'Ocultar contraseña' : 'Mostrar contraseña'"
                :title="show ? 'Ocultar contraseña' : 'Mostrar contraseña'"
                @click="show = !show"
              />
            </template>
          </UInput>

          <p
            v-if="errors.password"
            class="text-red-500 italic text-sm mt-1"
            role="alert"
          >
            {{ errors.password[0] }}
          </p>
        </UFormField>

        <!-- Indicador de seguridad de la contraseña -->
        <UProgress
          :color="color"
          :indicator="text"
          :model-value="score"
          :max="4"
          size="md"
          aria-hidden="true"
        />
        <p id="password-strength" class="text-xs text-white">
          {{ text }} Debe contener mínimo 6 caracteres, 1 nº, 1 mayúscula y 1
          minúscula.
        </p>

        <!-- Botón de enviar formulario -->
        <UButton
          type="submit"
          color="primary"
          size="xl"
          class="text-black font-bold rounded-4xl"
          block
          aria-label="Botón para crear cuenta con los datos introducidos"
        >
          Crear cuenta
        </UButton>

        <!-- Enlace a login -->
        <div class="text-center text-white mt-8" role="contentinfo">
          <p class="custom-starwars mb-5" tabindex="0">¿ya tienes cuenta?</p>
          <UButton
            class="block w-full btn text-md rounded-4xl bg-blue-600 font-bold p-2 hover:bg-blue-700"
            aria-label="Botón para volver a la pantalla de inicio de sesión"
            @click="irAlLogin"
          >
            Iniciar sesión
          </UButton>
        </div>
      </UForm>
    </div>
  </main>
</template>

<script setup>
import { ref, computed } from "vue";
import swalDark from "@/utils/swalDark";

useHead({
  title: "Gestor | Registro",
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


const config = useRuntimeConfig();

const form = ref({
  username: "",
  email: "",
  password: "",
});

const show = ref(false);

const checkStrength = (str) => [
  { regex: /.{6,}/, text: "Al menos 6 caracteres", met: /.{6,}/.test(str) },
  { regex: /\d/, text: "Al menos 1 número", met: /\d/.test(str) },
  { regex: /[a-z]/, text: "Al menos 1 minúscula", met: /[a-z]/.test(str) },
  { regex: /[A-Z]/, text: "Al menos 1 mayúscula", met: /[A-Z]/.test(str) },
];

const strength = computed(() => checkStrength(form.value.password));
const score = computed(() => strength.value.filter((req) => req.met).length);

const color = computed(() => {
  const safeIndex = Math.min(Math.max(score.value, 0), 4);
  return ["neutral", "error", "warning", "warning", "success"][safeIndex];
});
const text = computed(
  () =>
    [
      "",
      "Contraseña débil. ",
      "Contraseña débil. ",
      "Contraseña media. ",
      "Contraseña fuerte. ",
    ][score.value]
);

const errors = ref({});

const handleSubmit = async () => {
  errors.value = {}; // limpiar errores anteriores

  try {
    // eslint-disable-next-line @typescript-eslint/no-unused-vars
    const res = await $fetch(`${config.public.API_URL}/api/register`, {
      method: "POST",
      body: {
        username: form.value.username,
        email: form.value.email,
        password: form.value.password,
      },
      throw: true,
    });

    // Alerta sweetalert
    await swalDark.fire({
      icon: "success",
      title: "¡Registro con exito!",
      text: "Redirigiendo...",
      timer: 3000,
      showConfirmButton: false,
    });

    irAlLogin();
  } catch (err) {
    if (err?.data?.errors) {
      errors.value = err.data.errors;
    } else {
      error.value = err?.data?.message || "Error al registrar usuario";
      await swalDark.fire({
        icon: "error",
        title: "Error",
        text: error.value,
        confirmButtonText: "Cerrar",
      });
    }
  }
};

const irAlLogin = () => {
  sessionStorage.setItem("fromRegister", "true");
  navigateTo("/login");
};

// función para llevar foco a primer campo
onMounted(() => {
  const usernameInput = document.querySelector(
    'input[aria-label="Campo para el nombre de usuario"]'
  );
  usernameInput?.focus();
});
</script>

<style>
.custom-starwars {
  font-family: "Starjedi", sans-serif;
}
</style>
