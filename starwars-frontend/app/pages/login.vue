<!-- eslint-disable vue/html-self-closing -->
<!-- eslint-disable vue/no-multiple-template-root -->
<template>
  <!-- Fondo negro fijo detrás de la animación -->
  <div class="fixed inset-0 bg-black z-0" />

  <Transition :name="sinTransicion ? undefined : 'login-slide'" appear>
    <!-- Contenedor principal a pantalla completa con fondo oscuro -->
    <main
      id="login-main"
      aria-labelledby="tituloLogin"
      class="min-h-screen flex items-center justify-center bg-black px-8 z-1 relative"
    >
      <!-- Tarjeta del formulario con fondo más claro y sombra -->
      <div
        class="w-full max-w-md bg-gray-800 p-8 rounded-2xl shadow-lg"
        aria-label="Formulario de inicio de sesión"
      >
        <!-- Título del formulario con fuente personalizada -->
        <h1
          id="tituloLogin"
          class="text-3xl text-center text-yellow-500 mb-8 custom-starwars"
          tabindex="0"
        >
          iniciar sesion
        </h1>

        <!-- Formulario manejado por Nuxt UI con @submit="handleSubmit" -->
        <UForm
          :state="form"
          class="space-y-6"
          role="form"
          @submit="handleSubmit"
        >
          <!-- Campo de Email -->
          <UFormField name="email">
            <template #label>
              <span class="text-gray-300 font-semibold">Email</span>
            </template>

            <UInput
              id="email"
              v-model="form.email"
              class="w-full"
              icon="i-lucide-mail"
              type="email"
              placeholder="Email"
              :ui="{ base: 'bg-gray-800 text-yellow-500 opacity-90' }"
              aria-label="Campo para insertar el correo electrónico"
            />
            <p
              v-if="errors.email"
              class="text-red-500 text-sm mt-1 italic"
              role="alert"
            >
              {{ errors.email[0] }}
            </p>
          </UFormField>

          <!-- Campo de Contraseña con validación visual -->
          <div class="space-y-2">
            <UFormField name="password" class="text-yellow-500">
              <template #label>
                <span class="text-gray-300 font-semibold">Contraseña</span>
              </template>

              <!-- Input de contraseña con botón de mostrar/ocultar -->
              <UInput
                id="password"
                v-model="form.password"
                placeholder="Contraseña"
                icon="i-lucide-text"
                :type="show ? 'text' : 'password'"
                :ui="{
                  base: 'bg-gray-800 text-yellow-500 opacity-90',
                  trailing: 'pe-1',
                }"
                class="w-full"
                aria-describedby="passwordHelp"
                aria-label="Campo para introducir la contraseña"
              >
                <!-- Botón de mostrar/ocultar contraseña -->
                <template #trailing>
                  <UButton
                    variant="link"
                    size="sm"
                    :icon="show ? 'i-lucide-eye-off' : 'i-lucide-eye'"
                    :aria-label="
                      show ? 'Ocultar contraseña' : 'Mostrar contraseña'
                    "
                    :title="show ? 'Ocultar contraseña' : 'Mostrar contraseña'"
                    @click="show = !show"
                  />
                </template>
              </UInput>

              <p
                v-if="errors.password"
                class="text-red-500 text-sm mt-1 italic"
                role="alert"
              >
                {{ errors.password[0] }}
              </p>

              <!-- Mensaje de error (email o contraseña incorrecta, o demasiados intentos) -->
              <div
                v-if="errorGlobal"
                class="text-red-500 text-sm mb-2 mt-2 text-center italic"
                role="alert"
              >
                {{ errorGlobal }}
              </div>

              <!-- Enlace para recuperación -->
              <div class="text-center mt-2 mb-8">
                <nuxt-link
                  to="/forgot-password"
                  class="text-sm text-white text-center underline"
                  aria-label="Enlace para Recuperar contraseña"
                >
                  He olvidado mi contraseña
                </nuxt-link>
              </div>
            </UFormField>
          </div>

          <!-- Spinner accesible con Font Awesome -->
          <div
            v-if="isSubmitting"
            class="flex justify-center items-center mt-4 gap-2"
            role="status"
            aria-live="polite"
            aria-busy="true"
          >
            <i
              class="fas fa-spinner fa-spin text-yellow-400 text-4xl"
              aria-hidden="true"
            ></i>
          </div>

          <!-- Botón principal para enviar el formulario -->
          <UButton
            type="submit"
            color="primary"
            size="xl"
            class="text-black font-bold rounded-4xl"
            block
            aria-label="Botón para iniciar sesión con los datos introducidos"
            :disabled="isSubmitting"
          >
            Iniciar sesión
          </UButton>

          <!-- Login con Google -->
          <div class="text-center">
            <UButton
              color="white"
              class="block w-full text-white py-2.5 font-semibold bg-red-600 rounded-4xl hover:bg-red-700 transition"
              aria-label="Botón para iniciar sesión a través de una cuenta de Google"
              @click="loginWithGoogle"
            >
              <i class="fa-brands fa-google mr-3 text-xl" />
              Continuar con Google
            </UButton>
          </div>

          <!-- Enlace a registro para nuevos usuarios -->
          <div class="text-center text-white mt-14" role="contentinfo">
            <p class="custom-starwars mb-5" tabindex="0">
              ¿Aún no tienes cuenta?
            </p>
            <nuxt-link
              to="registro"
              class="block w-full text-center rounded-4xl font-semibold bg-blue-600 p-2 text-white hover:bg-blue-700 transition"
              aria-label="Botón para acceder al formulario de registro de una nueva cuenta"
            >
              Registro
            </nuxt-link>
          </div>
        </UForm>
      </div>
    </main>
  </Transition>
</template>

<script setup>
useHead({
  title: "Gestor | Iniciar sesión",
  htmlAttrs: {
    lang: "es",
  },
});

// Esto para quitar la transición DE registro A login!!
const sinTransicion = ref(false);

// eslint-disable-next-line nuxt/prefer-import-meta
if (process.client && sessionStorage.getItem("fromRegister") === "true") {
  sinTransicion.value = true;
  sessionStorage.removeItem("fromRegister");
}

// ESTADOS Y REFERENCIAS

// Objeto reactivo con los datos del formulario (email y contraseña)
const form = ref({ email: "", password: "" });

// Control para mostrar u ocultar el contenido del input de contraseña
const show = ref(false);

// Objeto para almacenar errores por campo (422 Unprocessable Entity)
const errors = ref({});

// Variable para mostrar errores generales (login fallido, demasiados intentos, etc.)
const errorGlobal = ref("");

// Variable booleana para desactivar el botón de enviar mientras se realiza la petición
const isSubmitting = ref(false);

// Obtenemos el router de Nuxt para hacer redirecciones
const router = useRouter();

// FUNCIÓN DE ENVÍO DEL FORMULARIO
// ==============================
const handleSubmit = async () => {
  // Limpiar errores anteriores antes de enviar la nueva petición
  errorGlobal.value = "";
  errors.value = {};
  isSubmitting.value = true; // desactiva el botón

  try {
    // Enviamos los datos del formulario al backend mediante fetch
    const response = await $fetch("http://localhost:8000/api/login", {
      method: "POST",
      body: form.value,
    });

    // Si el login es exitoso, guardamos el token y datos del usuario en localStorage
    localStorage.setItem("token", response.access_token);
    localStorage.setItem("user", JSON.stringify(response.user));

    // Redirigimos al usuario a la pantalla principal de la app
    router.push("/options");
  } catch (err) {
    // Captura del código de error HTTP (si lo hay)
    const status = err?.response?.status || err?.status;

    // Casos posibles según el tipo de error recibido desde Laravel:
    if (status === 422) {
      // Errores de validación por campo (por ejemplo: email vacío, mal formato, etc.)
      errors.value = err.data.errors;
    } else if ([401, 404].includes(status)) {
      // Login incorrecto → credenciales inválidas
      // Laravel devuelve el mensaje en el campo "message"
      errorGlobal.value =
        err.data?.message || "Email o contraseña incorrectos.";
    } else if (status === 429) {
      // Demasiados intentos → Laravel envía el mensaje con los segundos de espera
      errorGlobal.value =
        err.data?.message || "Demasiados intentos. Intenta más tarde.";
    } else {
      // Cualquier otro tipo de error inesperado (problema con el servidor, conexión, etc.)
      errorGlobal.value = "Error del servidor. Intenta más tarde.";
    }
  } finally {
    // Sea cual sea el resultado, indicamos que ha terminado el envío
    isSubmitting.value = false;
  }
};

// ==============================
// LOGIN CON GOOGLE

// Redirige al backend para iniciar el flujo de login con Google
const loginWithGoogle = () => {
  window.location.href = "http://localhost:8000/api/auth/google/redirect";
};

// ==============================
// DETECTAR REDIRECCIÓN DESDE GOOGLE

// Si venimos redirigidos desde Google con un token y datos del usuario, los guardamos

if (
  // eslint-disable-next-line nuxt/prefer-import-meta
  process.client &&
  router.currentRoute.value.query.access_token !== undefined
) {
  // Guardamos el token recibido tras login con Google
  localStorage.setItem("token", router.currentRoute.value.query.access_token);

  // También almacenamos el objeto del usuario si está presente en la URL
  const userString = router.currentRoute.value.query.user;
  if (userString) {
    const user = JSON.parse(decodeURIComponent(userString));
    localStorage.setItem("user", JSON.stringify(user));
  }

  // Redirigimos a la pantalla principal
  router.push("/options");
}

// ==============================
// AVISO SI EL USUARIO LLEGÓ DESDE UNA RUTA NO AUTORIZADA

// Si se detecta que el usuario fue redirigido con un flag "unauthorized", mostramos alerta
onMounted(() => {
  const route = useRoute();
  if (route.query.unauthorized) {
    // Cargamos SweetAlert2 de forma dinámica para mostrar la alerta
    import("sweetalert2").then((Swal) => {
      Swal.default.fire({
        icon: "error",
        title: "Acceso no autorizado",
        text: "Por favor inicia sesión de nuevo.",
        confirmButtonColor: "#facc15",
      });
    });
  }
});
</script>

<style scoped>
.login-slide-enter-active {
  transition: transform 1s ease, opacity 1s ease;
}
.login-slide-enter-from {
  transform: translateY(100%);
  opacity: 0;
}
.login-slide-enter-to {
  transform: translateY(0%);
  opacity: 1;
}
</style>
