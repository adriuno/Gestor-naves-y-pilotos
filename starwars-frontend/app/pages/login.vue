<template>
  <!-- Contenedor principal a pantalla completa con fondo oscuro -->
  <div class="min-h-screen flex items-center justify-center bg-gray-900">
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
            placeholder="correo@example.com"
            :ui="{ base: 'bg-gray-900 text-yellow-500' }"
          />
          <p v-if="errors.email" class="text-red-500 text-sm mt-1 italic">
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
              placeholder="contraseña..."
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
            <div v-if="errorGlobal" class="text-red-500 text-sm mb-4 text-center italic">
              {{ errorGlobal }}
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

        <!-- Enlace a registro para nuevos usuarios -->
        <div class="text-center text-white mt-8">        
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

</script>



<style>
.custom-starwars {
  font-family: 'Starjedi', sans-serif;
}
</style>