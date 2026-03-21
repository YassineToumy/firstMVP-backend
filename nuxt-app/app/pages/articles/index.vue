<template>
  <div class="min-h-screen bg-gray-50 pt-[84px]">

    <!-- ── Hero with wallpaper + search ── -->
    <section class="relative w-full h-[340px] sm:h-[400px] flex items-center justify-center overflow-hidden">
      <!-- Background image -->
      <img
        src="/Image_wallpeper.png"
        alt=""
        class="absolute inset-0 w-full h-full object-cover"
      />
      <!-- Dark overlay -->
      <div class="absolute inset-0 bg-gradient-to-b from-black/55 via-black/40 to-black/65" />

      <!-- Content -->
      <div class="relative z-10 w-full max-w-[760px] mx-auto px-6 text-center">
        <span class="inline-block text-xs font-semibold uppercase tracking-widest text-white/80 bg-white/15 backdrop-blur-sm px-3 py-1 rounded-full mb-5">
          Blog &amp; Actualités
        </span>
        <h1 class="font-bold text-4xl sm:text-5xl text-white mb-4 leading-tight">
          {{ $t('articles.heroTitle') }}
        </h1>
        <p class="text-white/80 text-lg mb-8 leading-relaxed">
          {{ $t('articles.heroSubtitle') }}
        </p>

        <!-- Search bar -->
        <div class="relative bg-white rounded-[14px] shadow-[0_4px_24px_rgba(0,0,0,0.18)] pl-5 pr-3 py-3 flex items-center gap-3">
          <Icon name="lucide:search" class="w-5 h-5 text-gray-400 shrink-0" />
          <input
            v-model="search"
            type="text"
            :placeholder="$t('articles.searchPlaceholder')"
            class="flex-1 text-[16px] text-gray-900 placeholder:text-gray-400 outline-none bg-transparent"
          />
          <button
            v-if="search"
            @click="search = ''"
            class="w-8 h-8 rounded-full flex items-center justify-center text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-all shrink-0"
          >
            <Icon name="lucide:x" class="w-4 h-4" />
          </button>
        </div>
      </div>
    </section>

    <div class="max-w-[1440px] mx-auto px-8 py-12">

      <!-- ── Loading skeleton ── -->
      <template v-if="loading">
        <!-- Featured skeleton -->
        <div class="bg-white rounded-2xl overflow-hidden shadow-sm mb-10 h-[420px] animate-pulse" />
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          <div v-for="n in 6" :key="n" class="bg-white rounded-2xl overflow-hidden shadow-sm">
            <div class="h-52 bg-gray-100 animate-pulse" />
            <div class="p-5 space-y-3">
              <div class="h-4 bg-gray-100 rounded animate-pulse w-1/3" />
              <div class="h-5 bg-gray-100 rounded animate-pulse w-3/4" />
              <div class="h-4 bg-gray-100 rounded animate-pulse w-full" />
            </div>
          </div>
        </div>
      </template>

      <template v-else-if="filteredArticles.length">
        <!-- ── Featured article (first) ── -->
        <NuxtLink
          :to="`/articles/${filteredArticles[0].slug}`"
          class="group block bg-white rounded-2xl overflow-hidden shadow-md hover:shadow-xl transition-all duration-300 mb-10"
        >
          <div class="flex flex-col lg:flex-row">
            <!-- Image -->
            <div class="relative lg:w-[55%] h-[280px] lg:h-[380px] overflow-hidden bg-gray-100 flex-shrink-0">
              <img
                v-if="filteredArticles[0].image_url"
                :src="filteredArticles[0].image_url"
                :alt="filteredArticles[0].alt_image || filteredArticles[0].title"
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
              />
              <div v-else class="w-full h-full flex items-center justify-center">
                <Icon name="lucide:newspaper" class="w-12 h-12 text-gray-200" />
              </div>
              <!-- Featured badge -->
              <div class="absolute top-4 left-4">
                <span class="bg-[#00878E] text-white text-xs font-semibold px-3 py-1 rounded-full">
                  À la une
                </span>
              </div>
            </div>
            <!-- Content -->
            <div class="flex flex-col justify-center p-8 lg:p-12">
              <div class="flex items-center gap-2 mb-4">
                <span v-if="filteredArticles[0].country" class="text-[11px] font-semibold uppercase tracking-wide text-[#00878E] bg-[#00878E]/10 px-2.5 py-0.5 rounded-full">
                  {{ filteredArticles[0].country }}
                </span>
                <span v-if="filteredArticles[0].read_time" class="text-xs text-gray-400 flex items-center gap-1">
                  <Icon name="lucide:clock" class="w-3 h-3" /> {{ filteredArticles[0].read_time }} min
                </span>
              </div>
              <h2 class="font-bold text-2xl sm:text-3xl text-[#313131] mb-4 leading-snug group-hover:text-[#00878E] transition-colors">
                {{ filteredArticles[0].title }}
              </h2>
              <p v-if="filteredArticles[0].excerpt" class="text-gray-500 leading-relaxed mb-6 line-clamp-3">
                {{ filteredArticles[0].excerpt }}
              </p>
              <div class="flex items-center gap-2 text-[#00878E] font-semibold text-sm group-hover:gap-3 transition-all duration-300">
                Lire l'article <Icon name="lucide:arrow-right" class="w-4 h-4" />
              </div>
            </div>
          </div>
        </NuxtLink>

        <!-- ── Rest of articles grid ── -->
        <div v-if="filteredArticles.length > 1" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          <NuxtLink
            v-for="article in filteredArticles.slice(1)"
            :key="article.id"
            :to="`/articles/${article.slug}`"
            class="group bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl border border-gray-100 hover:border-[#00878E]/30 transition-all duration-300 block"
          >
            <div class="h-52 bg-gray-100 overflow-hidden">
              <img
                v-if="article.image_url"
                :src="article.image_url"
                :alt="article.alt_image || article.title"
                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
              />
              <div v-else class="w-full h-full flex items-center justify-center">
                <Icon name="lucide:newspaper" class="w-10 h-10 text-gray-200" />
              </div>
            </div>
            <div class="p-5">
              <div class="flex items-center gap-2 mb-2">
                <span v-if="article.country" class="text-[11px] font-semibold uppercase tracking-wide text-[#00878E] bg-[#00878E]/10 px-2.5 py-0.5 rounded-full">
                  {{ article.country }}
                </span>
                <span v-if="article.read_time" class="text-[11px] text-gray-400 flex items-center gap-1">
                  <Icon name="lucide:clock" class="w-3 h-3" /> {{ article.read_time }} min
                </span>
              </div>
              <h3 class="font-semibold text-[#313131] mb-2 line-clamp-2 text-[17px] group-hover:text-[#00878E] transition-colors">
                {{ article.title }}
              </h3>
              <p v-if="article.excerpt" class="text-sm text-gray-500 line-clamp-2 mb-4">{{ article.excerpt }}</p>
              <div class="flex items-center gap-2 text-[#00878E] font-semibold text-sm group-hover:gap-3 transition-all duration-300">
                Lire l'article <Icon name="lucide:arrow-right" class="w-4 h-4" />
              </div>
            </div>
          </NuxtLink>
        </div>

        <!-- ── Pagination ── -->
        <div v-if="meta && meta.last_page > 1" class="flex items-center justify-center gap-2 mt-12">
          <button
            :disabled="meta.current_page === 1"
            @click="load(meta.current_page - 1)"
            class="w-9 h-9 rounded-lg text-sm font-medium transition-colors flex items-center justify-center border border-gray-200 bg-white text-gray-600 hover:bg-gray-50 disabled:opacity-40 disabled:cursor-not-allowed"
          >
            <Icon name="lucide:chevron-left" class="w-4 h-4" />
          </button>
          <button
            v-for="p in meta.last_page"
            :key="p"
            @click="load(p)"
            class="w-9 h-9 rounded-lg text-sm font-medium transition-colors"
            :class="p === meta.current_page ? 'bg-[#00878E] text-white shadow-sm' : 'bg-white text-gray-600 hover:bg-gray-50 border border-gray-200'"
          >
            {{ p }}
          </button>
          <button
            :disabled="meta.current_page === meta.last_page"
            @click="load(meta.current_page + 1)"
            class="w-9 h-9 rounded-lg text-sm font-medium transition-colors flex items-center justify-center border border-gray-200 bg-white text-gray-600 hover:bg-gray-50 disabled:opacity-40 disabled:cursor-not-allowed"
          >
            <Icon name="lucide:chevron-right" class="w-4 h-4" />
          </button>
        </div>
      </template>

      <!-- ── Empty ── -->
      <div v-else class="text-center py-24">
        <div class="w-16 h-16 mx-auto rounded-2xl bg-gray-100 flex items-center justify-center mb-4">
          <Icon name="lucide:newspaper" class="w-8 h-8 text-gray-300" />
        </div>
        <p class="text-gray-500 font-medium">{{ search ? 'Aucun article trouvé' : 'Aucun article disponible' }}</p>
        <p class="text-sm text-gray-400 mt-1">{{ search ? 'Essayez un autre mot-clé' : 'Revenez bientôt pour découvrir nos articles' }}</p>
        <button v-if="search" @click="search = ''" class="mt-4 text-sm font-semibold text-[#00878E] hover:text-[#006b70] transition-colors">
          Effacer la recherche
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
const { apiFetch } = useApi()
const region = useRegionStore()

const articles = ref<any[]>([])
const meta = ref<any>(null)
const loading = ref(true)
const search = ref('')

const filteredArticles = computed(() => {
  if (!search.value.trim()) return articles.value
  const q = search.value.trim().toLowerCase()
  return articles.value.filter(a =>
    a.title?.toLowerCase().includes(q) ||
    a.excerpt?.toLowerCase().includes(q)
  )
})

async function load(page = 1) {
  loading.value = true
  try {
    const params = new URLSearchParams({ page: String(page), per_page: '10' })
    if (region.currentCode) params.set('country', region.currentCode)
    const res = await apiFetch<any>(`/articles?${params}`)
    articles.value = res.data
    meta.value = res.meta
  } finally {
    loading.value = false
  }
}

watch(() => region.currentCode, () => load(1))
onMounted(() => load())

useHead({ title: 'Articles — GlobalRent' })
</script>
