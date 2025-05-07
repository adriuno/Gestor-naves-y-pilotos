<template>
  <!-- Contenedor principal a pantalla completa con fondo oscuro -->
  <div class="min-h-screen flex items-center justify-center bg-gray-900">
    <!-- Tarjeta del formulario con fondo más claro y sombra -->
    <div class="w-full max-w-md bg-gray-800 p-8 rounded-2xl shadow-lg">
      <!-- Título del formulario con fuente personalizada -->
      <h2 class="text-3xl text-center text-yellow-500 mb-8 custom-starwars">Login</h2>

      <!-- Formulario manejado por Nuxt UI con @submit="handleSubmit" -->
      <UForm :state="form" class="space-y-6" @submit="handleSubmit">
        
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
              :ui="{ base: 'bg-gray-800 text-yellow-500', trailing: 'pe-1' }"
              :aria-invalid="score < 4"
              aria-describedby="password-comparativa"
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
          </UFormField>

          <!-- Texto con resumen de la seguridad de la contraseña -->
          <p id="password-comparativa" class="text-xs font-small text-white mt-2">
            {{ text }}
          </p>


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

// Función que evalúa los requisitos de seguridad de la contraseña
function checkStrength(str) {
  const requirements = [
    { regex: /.{8,}/, text: 'Al menos 8 caracteres' },
    { regex: /\d/, text: 'Al menos 1 número' },
    { regex: /[a-z]/, text: 'Al menos 1 letra minúscula' },
    { regex: /[A-Z]/, text: 'Al menos 1 letra mayúscula' },
  ]

  return requirements.map(req => ({
    met: req.regex.test(str),
    text: req.text,
  }))
}

// Resultado de la validación (lista de requisitos cumplidos o no)
const comparativa = computed(() => checkStrength(form.value.password))

// Cantidad de requisitos cumplidos (0 a 4)
const score = computed(() => comparativa.value.filter(req => req.met).length)

// Texto resumen según la fuerza de la contraseña
const text = computed(() => {
  if (score.value === 0) return ''
  if (score.value <= 2) return 'Contraseña débil. '
  if (score.value === 3) return 'Contraseña media. '
  return 'Contraseña fuerte. '
})

// Router de Nuxt para redireccionar después del login
const router = useRouter()

// Función que se ejecuta al enviar el formulario
const handleSubmit = async () => {
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
    console.error('Error en login:', err?.data?.message || err.message)
  }
}

</script>



<style>
.custom-starwars {
  font-family: 'Starjedi', sans-serif;
}
</style>