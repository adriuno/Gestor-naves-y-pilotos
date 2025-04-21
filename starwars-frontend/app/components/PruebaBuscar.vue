<!-- <script setup>
const search = ref('')
const allStarships = ref([])
const filteredStarships = ref([])
const loading = ref(false)

const config = useRuntimeConfig()

const fetchStarships = async () => {
  loading.value = true

  const { data: data } = await useFetch(`${config.public.API_URL}/starships-all`)

  if (data.value) {
    allStarships.value = data.value
    filteredStarships.value = data.value
  }

  loading.value = false
}

onMounted(() => {
  fetchStarships()
})

watch(search, () => {
  if (search.value === '') {
    filteredStarships.value = allStarships.value
  } else {
    filteredStarships.value = allStarships.value.filter(s =>
      s.name.toLowerCase().includes(search.value.toLowerCase())
      || s.pilots.some(p => p.name.toLowerCase().includes(search.value.toLowerCase()))
    )
  }
})
</script>

<template>
  <div class="max-w-5xl mx-auto p-4 space-y-6">

    <h1 class="text-3xl font-bold text-center text-yellow-300">Starships</h1>

    <input
      v-model="search"
      type="text"
      placeholder="Buscar starship o piloto..."
      class="border border-gray-300 rounded-lg px-4 py-2 w-full focus:ring-2 focus:ring-yellow-400 transition"
    />

    <div v-if="loading" class="text-center py-10 text-gray-500 animate-pulse">
      Cargando starships...
    </div>

    <div v-else-if="filteredStarships?.length === 0" class="text-center py-10 text-gray-400">
      No se encontraron resultados.
    </div>

    <div v-else class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <div
        v-for="starship in filteredStarships"
        :key="starship.id"
        class="border border-gray-200 rounded-lg overflow-hidden shadow hover:shadow-md transition bg-white/5 backdrop-blur"
      >
        <img
          :src="starship.image2_url"
          :alt="starship.name"
          class="w-full h-48 object-cover"
        />

        <div class="p-4">
          <h2 class="font-semibold text-lg mb-2 text-yellow-300">{{ starship.name }}</h2>
          <p class="text-gray-400 text-sm">{{ starship.model }}</p>
        </div>
      </div>
    </div>

  </div>
</template> -->
