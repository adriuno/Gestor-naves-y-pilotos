<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-900">
    <div class="w-full max-w-md bg-gray-800 p-8 rounded-2xl shadow-lg">
      <h2 class="text-3xl text-center text-yellow-500 mb-8 custom-starwars">Registro</h2>

      <UForm :state="form" class="space-y-6" @submit="handleSubmit">
        <UFormField name="username">
          <label class="text-white">Nombre de usuario</label>
          <UInput
            v-model="form.username"
            placeholder="example123.."
            icon="i-lucide-user"
            class="w-full"
            :ui="{ base: 'bg-gray-900 text-yellow-500' }"
          />
        </UFormField>

        <UFormField name="email">
          <label class="text-white">Email</label>
          <UInput
            v-model="form.email"
            placeholder="mail@example.com"
            icon="i-lucide-mail"
            class="w-full"
            :ui="{ base: 'bg-gray-900 text-yellow-500' }"
          />
        </UFormField>

        <UFormField name="password">
          <label class="text-white">Contraseña</label>
          <UInput
            v-model="form.password"
            :type="show ? 'text' : 'password'"
            placeholder="contraseña..."
            icon="i-lucide-lock"
            class="w-full"
            :ui="{ base: 'bg-gray-800 text-yellow-500', trailing: 'pe-1' }"
            :aria-invalid="score < 4"
            aria-describedby="password-strength"
          >
            <template #trailing>
              <UButton
                variant="link"
                size="sm"
                :icon="show ? 'i-lucide-eye-off' : 'i-lucide-eye'"
                :aria-label="show ? 'Ocultar' : 'Mostrar'"
                :aria-pressed="show"
                @click="show = !show"
              />
            </template>
          </UInput>
        </UFormField>

        <UProgress
          :color="color"
          :indicator="text"
          :model-value="score"
          :max="4"
          size="md"
        />
        <p id="password-strength" class="text-xs text-white mt-2">
          {{ text }} Debe contener min.6 caracteres, 1 nº, 1 mayus, 1 minus.
        </p>
        <p v-if="showErrors && passwordErrors.length" class="text-sm text-red-400 mt-2">
          {{ passwordErrors.join(', ') }}
        </p>

        <UButton type="submit" color="primary" class="text-black font-bold" block>
          Crear cuenta
        </UButton>

        <div class="text-center text-white mt-8">        
          <p class="custom-starwars mb-5">¿ya tienes cuenta?</p>
          <nuxt-link to="login" class="btn rounded-4xl bg-blue-500 p-3">Iniciar sesión</nuxt-link>
        </div> 
      </UForm>
    </div>
  </div>
</template>

<script setup>
const config = useRuntimeConfig()

const form = ref({
  username: '',
  email: '',
  password: '',
})

const show = ref(false)
const showErrors = ref(false)

const checkStrength = (str) => [
  { regex: /.{6,}/, text: 'Al menos 6 caracteres', met: /.{6,}/.test(str) },
  { regex: /\d/, text: 'Al menos 1 número', met: /\d/.test(str) },
  { regex: /[a-z]/, text: 'Al menos 1 minúscula', met: /[a-z]/.test(str) },
  { regex: /[A-Z]/, text: 'Al menos 1 mayúscula', met: /[A-Z]/.test(str) },
]

const passwordErrors = computed(() =>
  checkStrength(form.value.password).filter(req => !req.met).map(req => req.text)
)

const strength = computed(() => checkStrength(form.value.password))
const score = computed(() => strength.value.filter(req => req.met).length)

const color = computed(() => {
  const safeIndex = Math.min(Math.max(score.value, 0), 4)
  return ['neutral', 'error', 'warning', 'warning', 'success'][safeIndex]
})
const text = computed(() =>
  ['', 'Contraseña débil', 'Contraseña débil. ', 'Contraseña media. ', 'Contraseña fuerte. '][score.value]
)

const handleSubmit = async () => {
  showErrors.value = false

  const unmet = checkStrength(form.value.password).filter(req => !req.met)
  if (unmet.length > 0) {
    showErrors.value = true
    return
  }

  try {
    await $fetch(`${config.public.API_URL}/api/register`, {
      method: 'POST',
      body: {
        username: form.value.username,
        email: form.value.email,
        password: form.value.password,
      },
    })

    alert('Usuario registrado correctamente')
    navigateTo('/login')
  } catch (err) {
  console.error('Error al registrar usuario:', err);
  alert(err?.data?.message || 'Error al registrar usuario');
  }
}
</script>

<style>
.custom-starwars {
  font-family: 'Starjedi', sans-serif;
}
</style>
