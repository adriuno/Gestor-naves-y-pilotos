<!-- eslint-disable vue/no-multiple-template-root -->
<template>
    <!-- Fondo negro fijo detrás de la animación -->
  <div class="fixed inset-0 bg-black z-0"></div>

  <Transition name="login-slide" appear>
  <!-- Contenedor principal a pantalla completa con fondo oscuro -->
  <div class="min-h-screen flex items-center justify-center bg-black px-8 z-1 relative">
    <!-- Tarjeta del formulario con fondo más claro y sombra -->
    <div class="w-full max-w-md bg-gray-800 p-8 rounded-2xl shadow-lg">
      <!-- Título del formulario con fuente personalizada -->
      <h2 class="text-3xl text-center text-yellow-500 mb-8 custom-starwars">Login</h2>



      <!-- Formulario manejado por Nuxt UI con @submit="handleSubmit" -->
      <UForm :state="form" 	class="space-y-6" @submit="handleSubmit">
        
        <!-- Campo de Email -->
        <UFormField name="email">
          <template #label>
            <label class="text-white">Email</label>
          </template>

          <UInput
            v-model="form.email"
            class="w-full"
            icon="i-lucide-mail"
            placeholder="Email"
            :ui="{ base: 'bg-gray-800 text-yellow-500' }"
          />
          <p v-if="errors.email" class="text-red-500 text-xs mt-1 italic ">
            {{ errors.email[0] }}
          </p>

        </UFormField>   

        <!-- Campo de Contraseña con validación visual -->
        <div class="space-y-2">
          <UFormField name="password" class="text-white">
            <template #label>
              <label class="text-white">Contraseña</label>
            </template> 

            <!-- Input de contraseña con botón de mostrar/ocultar -->
            <UInput
              v-model="form.password"
              placeholder="Contraseña"
              icon="i-lucide-text"
              :type="show ? 'text' : 'password'"
              :ui="{ base: 'bg-gray-800 text-yellow-00', trailing: 'pe-1' }"
              class="w-full"
            >
              <!-- Botón de mostrar/ocultar contraseña -->
              <template #trailing>
                <UButton
                  variant="link"
                  size="sm"
                  :icon="show ? 'i-lucide-eye-off' : 'i-lucide-eye'"
                  :aria-label="show ? 'Ocultar password' : 'Mostrar password'"
                  :aria-pressed="show"
                  aria-controls="password"
                  @click="show = !show"
                />
              </template>
            </UInput>
            <p v-if="errors.password" class="text-red-500 text-sm mt-1 italic">
              {{ errors.password[0] }}
            </p>
            <div v-if="errorGlobal" class="text-red-500 text-xs mb-4 mt-2 text-center italic">
              {{ errorGlobal }}
            </div>


            <div class="text-center mt-2 mb-8">
              <nuxt-link
                to="/forgot-password"
                class="text-sm text-white text-center underline"
              >
                He olvidado mi contraseña
              </nuxt-link>
            </div>
          </UFormField>



        </div>

        <!-- Botón principal para enviar el formulario -->
        <UButton
          type="submit"
          color="primary"
          size="xl"
          class="text-black font-bold rounded-4xl"
          block
        >
          Iniciar sesión
        </UButton>
        <!-- Login con Google -->
        <div class="text-center">
          <UButton
            color="white"
            class="block w-full text-white py-2.5 font-semibold bg-red-600 rounded-4xl hover:bg-red-700 transition"
            @click="loginWithGoogle"
          >
            <i class="fa-brands fa-google mr-3 text-xl"  />
            Continuar con Google
          </UButton>
        </div>

        <!-- Enlace a registro para nuevos usuarios -->
        <div class="text-center text-white mt-14">        
          <p class="custom-starwars mb-5">¿Aun no tienes cuenta?</p>
          <nuxt-link 
            to="registro"
            class="block w-full text-center rounded-4xl bg-blue-500 p-2 text-white hover:bg-blue-600 transition"
          >
            Registro
          </nuxt-link>
        </div>
      </UForm>
    </div>
  </div>
  </Transition>
</template>


<script setup>



// Estado del formulario
const form = ref({
  email: '',
  password: '',
})

// Estado para mostrar/ocultar la contraseña
const show = ref(false)


const errors = ref({})
const errorGlobal = ref('')
const isSubmitting = ref(false)


// Router de Nuxt para redireccionar después del login
const router = useRouter()

// Función que se ejecuta al enviar el formulario
const handleSubmit = async () => {
  errorGlobal.value = ''
  errors.value = {}
  isSubmitting.value = true

  try {
    const response = await $fetch('http://localhost:8000/api/login', {
      method: 'POST',
      body: form.value,
    })

    // Guarda el token en localStorage
    localStorage.setItem('token', response.access_token)
    // Y ahora guardamos el usuario y sus datos
    localStorage.setItem('user', JSON.stringify(response.user))

    // Redirige tras login
    router.push('/options')
  } catch (err) {
       
    //  Detecta el código de error
    const status = err?.response?.status || err?.status

    if (status === 422) {
      // Errores de validación de campos
      errors.value = err.data.errors
    } else if (status === 404) {
      // Email no existe
      errorGlobal.value = 'Email incorrecto.'
    } else if (status === 401) {
      // Contraseña incorrecta
      errorGlobal.value = 'Contraseña incorrecta.'
    } else {
      // Otro error inesperado
      errorGlobal.value = 'Error con el servidor. Intenta de nuevo más tarde.'
    }

  } finally {
    isSubmitting.value = false
  }
}



// Login con GOOGLE:

const loginWithGoogle = () => {
  window.location.href = 'http://localhost:8000/api/auth/google/redirect' 
}

// eslint-disable-next-line nuxt/prefer-import-meta
if (process.client && router.currentRoute.value.query.access_token !== undefined) {
  localStorage.setItem('token', router.currentRoute.value.query.access_token)
  router.push('/options')
}


onMounted(() => {
  const route = useRoute()
  if (route.query.unauthorized) {
    import('sweetalert2').then(Swal => {
      Swal.default.fire({
        icon: 'error',
        title: 'Acceso no autorizado',
        text: 'Por favor inicia sesión de nuevo.',
        confirmButtonColor: '#facc15',
      })
    })
  }
})


</script>



<style scoped>
.login-slide-enter-active {
  transition: transform 2s ease, opacity 2s ease;
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