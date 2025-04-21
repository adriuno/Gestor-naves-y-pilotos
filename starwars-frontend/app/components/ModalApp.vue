<!-- components/BaseModal.vue -->
<template>
    <div>
      <UButton label="Abrir Modal" color="warning" @click="isOpen = true"  />
  
      <UModal v-model="isOpen" title="Modal estilo Bootstrap">
        <p class="text-[--ui-color]">Este es un modal creado con Nuxt UI, igual de funcional y más personalizable.</p>
      </UModal>
    </div>
  </template>
  
  <script setup>
  const isOpen = ref(false)
  </script>
  