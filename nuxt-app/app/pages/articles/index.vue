<template>
  <div class="min-h-screen bg-gray-50 pt-[84px]">
    <!-- Hero -->
    <div class="bg-white border-b border-gray-200 py-12 px-8">
      <div class="max-w-[1440px] mx-auto">
        <h1 class="font-bold text-4xl text-[#313131] mb-2">Articles</h1>
        <p class="text-gray-500">Découvrez nos derniers articles et actualités immobilières</p>
      </div>
    </div>

    <div class="max-w-[1440px] mx-auto px-8 py-12">
      <!-- Filters -->
      <div class="flex items-center gap-3 mb-8 flex-wrap">
        <button
          v-for="c in countries"
          :key="c.code"
          @click="selectedCountry = selectedCountry === c.code ? '' : c.code"
          class="flex items-center gap-2 px-4 py-2 rounded-full border text-sm font-medium transition-all duration-200"
          :class="selectedCountry === c.code
            ? 'border-[#00878E] bg-[#00878E] text-white'
            : 'border-gray-300 bg-white text-gray-600 hover:border-[#00878E]'"
        >
          <img :src="`https://flagcdn.com/20x15/${c.code.toLowerCase()}.png`" :alt="c.name" class="w-4 h-auto rounded-sm" />
          {{ c.name }}
        </button>
      </div>

      <!-- Skeleton -->
      <div v-if="loading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <div v-for="n in 6" :key="n" class="bg-white rounded-2xl overflow-hidden shadow-sm">
          <div class="h-52 bg-gray-100 animate-pulse" />
          <div class="p-5 space-y-3">
            <div class="h-4 bg-gray-100 rounded animate-pulse w-1/3" />
            <div class="h-5 bg-gray-100 rounded animate-pulse w-3/4" />
            <div class="h-4 bg-gray-100 rounded animate-pulse w-full" />
          </div>
        </div>
      </div>

      <!-- Grid -->
      <div v-else-if="articles.length" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <NuxtLink
          v-for="article in articles"
          :key="article.id"
          :to="`/articles/${article.slug}`"
          class="group bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-lg border border-gray-100 hover:border-[#00878E]/30 transition-all duration-300 block"
        >
          <div class="h-52 bg-gray-100 overflow-hidden">
            <img
              v-if="article.image_url"
              :src="article.image_url"
              :alt="article.alt_image || article.title"
              class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
            />
            <div v-else class="w-full h-full flex items-center justify-center">
              <Icon name="lucide:newspaper" class="w-10 h-10 text-gray-200" />
            </div>
          </div>
          <div class="p-5">
            <div class="flex items-center gap-2 mb-2">
              <span v-if="article.country" class="text-[11px] font-semibold uppercase tracking-wide text-[#00878E] bg-[#00878E]/10 px-2.5 py-0.5 rounded-full">{{ article.country }}</span>
              <span v-if="article.read_time" class="text-[11px] text-gray-400 flex items-center gap-1">
                <Icon name="lucide:clock" class="w-3 h-3" /> {{ article.read_time }} min
              </span>
            </div>
            <h3 class="font-semibold text-[#313131] mb-2 line-clamp-2 text-[17px] group-hover:text-[#00878E] transition-colors">{{ article.title }}</h3>
            <p v-if="article.excerpt" class="text-sm text-gray-500 line-clamp-2 mb-4">{{ article.excerpt }}</p>
            <div class="flex items-center gap-2 text-[#00878E] font-semibold text-sm group-hover:gap-3 transition-all duration-300">
              Lire l'article <Icon name="lucide:arrow-right" class="w-4 h-4" />
            </div>
          </div>
        </NuxtLink>
      </div>

      <!-- Empty -->
      <div v-else class="text-center py-24">
        <div class="w-16 h-16 mx-auto rounded-2xl bg-gray-100 flex items-center justify-center mb-4">
          <Icon name="lucide:newspaper" class="w-8 h-8 text-gray-300" />
        </div>
        <p class="text-gray-500 font-medium">Aucun article disponible</p>
        <p class="text-sm text-gray-400 mt-1">Revenez bientôt pour découvrir nos articles</p>
      </div>

      <!-- Pagination -->
      <div v-if="meta && meta.last_page > 1" class="flex items-center justify-center gap-2 mt-10">
        <button
          v-for="p in meta.last_page"
          :key="p"
          @click="load(p)"
          class="w-9 h-9 rounded-lg text-sm font-medium transition-colors"
          :class="p === meta.current_page ? 'bg-[#00878E] text-white' : 'bg-white text-gray-600 hover:bg-gray-50 border border-gray-200'"
        >{{ p }}</button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
const { apiFetch } = useApi()

const articles = ref<any[]>([])
const meta = ref<any>(null)
const loading = ref(true)
const selectedCountry = ref('')

const countries = [
  { code: 'FR', name: 'France' },
  { code: 'TN', name: 'Tunisie' },
  { code: 'EG', name: 'Égypte' },
  { code: 'CA', name: 'Canada' },
]

async function load(page = 1) {
  loading.value = true
  try {
    const params = new URLSearchParams({ page: String(page), per_page: '12' })
    if (selectedCountry.value) params.set('country', selectedCountry.value)
    const res = await apiFetch<any>(`/articles?${params}`)
    articles.value = res.data
    meta.value = res.meta
  } finally {
    loading.value = false
  }
}

watch(selectedCountry, () => load(1))
onMounted(() => load())

useHead({ title: 'Articles — GlobalRent' })
</script>
