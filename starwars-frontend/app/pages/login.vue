<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-900">
    <div class="w-full max-w-md bg-gray-800 p-8 rounded-2xl shadow-lg">
      <h2 class="text-3xl font-bold text-center text-white mb-8">Login</h2>

      <UForm :state="form" @submit="handleSubmit" class="space-y-6">
        
        <UFormField label="Email" name="email" class="text-white">
          <UInput v-model="form.email" class=" w-full" icon="i-lucide-mail" placeholder="Introduce tu email" />
        </UFormField>

        <!-- Password field con fuerza de contraseña -->
        <div class="space-y-2">
          <UFormField label="Contraseña" name="password"  class="text-white">
            <UInput
                  v-model="form.password"
                  placeholder="Introduce tu contraseña"
                  icon="i-lucide-text"
                  :type="show ? 'text' : 'password'"
                  :ui="{ trailing: 'pe-1' }"
                  :aria-invalid="score < 4"
                  aria-describedby="password-strength"
                  class="w-full text-white focus:ring-2 focus:ring-yellow-500"
                >
              <template #trailing>
                <UButton
                  variant="link"
                  size="sm"
                  :icon="show ? 'i-lucide-eye-off' : 'i-lucide-eye'"
                  :aria-label="show ? 'Ocultar contraseña' : 'Mostrar contraseña'"
                  :aria-pressed="show"
                  aria-controls="password"
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
            size="sm"
          />

          <p id="password-strength" class="text-sm font-medium text-white">
            {{ text }}. Debe contener:
          </p>

          <ul class="space-y-1" aria-label="Password requirements">
            <li
              v-for="(req, index) in strength"
              :key="index"
              class="flex items-center gap-0.5"
              :class="req.met ? 'text-success' : 'text-muted'"
            >
              <UIcon :name="req.met ? 'i-lucide-circle-check' : 'i-lucide-circle-x'" class="size-4 shrink-0" />
              <span class="text-xs font-light">
                {{ req.text }}
                <span class="sr-only">
                  {{ req.met ? ' - Requisito cumplido' : ' - Requisito no cumplido' }}
                </span>
              </span>
            </li>
          </ul>
        </div>

        <!-- Botón de enviar -->
        <UButton type="submit" color="primary" block>Entrar</UButton>

      </UForm>
    </div>
  </div>
</template>

<script setup lang="ts">

const emit = defineEmits(['login']);

const form = ref({
  email: '',
  password: '',
});

// Password strength meter
const show = ref(false);

function checkStrength(str: string) {
  const requirements = [
    { regex: /.{8,}/, text: 'Al menos 8 caracteres' },
    { regex: /\d/, text: 'Al menos 1 número' },
    { regex: /[a-z]/, text: 'Al menos 1 letra minúscula' },
    { regex: /[A-Z]/, text: 'Al menos 1 letra mayúscula' },
  ];

  return requirements.map(req => ({
    met: req.regex.test(str),
    text: req.text,
  }));
}

const strength = computed(() => checkStrength(form.value.password));
const score = computed(() => strength.value.filter(req => req.met).length);

const color = computed(() => {
  if (score.value === 0) return 'neutral';
  if (score.value <= 1) return 'error';
  if (score.value <= 2) return 'warning';
  if (score.value === 3) return 'warning';
  return 'success';
});

const text = computed(() => {
  if (score.value === 0) return 'Introduce una contraseña';
  if (score.value <= 2) return 'Contraseña débil';
  if (score.value === 3) return 'Contraseña media';
  return 'Contraseña fuerte';
});

const handleSubmit = async () => {
  emit('login', form.value);
};
</script>
