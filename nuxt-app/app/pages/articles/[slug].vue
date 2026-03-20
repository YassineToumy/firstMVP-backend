<template>
  <div class="min-h-screen bg-gray-50 pt-[84px]">

    <!-- ── Loading ── -->
    <div v-if="loading" class="max-w-[900px] mx-auto px-6 py-16 space-y-4">
      <div class="h-8 bg-gray-200 rounded animate-pulse w-2/3" />
      <div class="h-4 bg-gray-200 rounded animate-pulse w-1/3" />
      <div class="h-[400px] bg-gray-200 rounded-2xl animate-pulse mt-8" />
      <div class="space-y-3 mt-8">
        <div v-for="n in 6" :key="n" class="h-4 bg-gray-200 rounded animate-pulse" :style="`width:${70 + Math.random()*30}%`" />
      </div>
    </div>

    <template v-else-if="article">
      <!-- ── Back bar ── -->
      <div class="bg-white border-b border-gray-200 py-4 px-8">
        <div class="max-w-[900px] mx-auto">
          <NuxtLink
            to="/articles"
            class="inline-flex items-center gap-2 text-[#00878E] font-semibold hover:gap-3 transition-all duration-300 text-sm"
          >
            <Icon name="lucide:arrow-left" class="w-4 h-4" />
            Retour aux articles
          </NuxtLink>
        </div>
      </div>

      <!-- ── Hero image ── -->
      <div v-if="article.image_url" class="relative h-[400px] w-full overflow-hidden">
        <img
          :src="article.image_url"
          :alt="article.alt_image || article.title"
          class="w-full h-full object-cover"
        />
        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent" />

        <!-- Share button -->
        <div class="absolute top-6 right-6">
          <button
            @click="shareOpen = true"
            class="px-4 py-2 bg-white/90 backdrop-blur-sm rounded-full flex items-center gap-2 hover:bg-white transition-colors duration-300 shadow-md"
          >
            <Icon name="lucide:share-2" class="w-4 h-4 text-gray-700" />
            <span class="text-sm text-gray-700 font-medium">Partager</span>
          </button>
        </div>
      </div>

      <!-- ── Article body ── -->
      <div class="py-12 px-8">
        <div class="max-w-[900px] mx-auto">

          <!-- Meta + title -->
          <div class="mb-8">
            <div class="flex flex-wrap items-center gap-3 mb-5">
              <span v-if="article.country" class="text-xs font-semibold uppercase tracking-wide text-[#00878E] bg-[#00878E]/10 px-3 py-1 rounded-full">
                {{ article.country }}
              </span>
              <span v-if="article.read_time" class="text-xs text-gray-400 flex items-center gap-1">
                <Icon name="lucide:clock" class="w-3.5 h-3.5" /> {{ article.read_time }} min de lecture
              </span>
              <span v-if="article.published_at" class="text-xs text-gray-400 flex items-center gap-1">
                <Icon name="lucide:calendar" class="w-3.5 h-3.5" /> {{ formatDate(article.published_at) }}
              </span>
            </div>
            <h1 class="font-bold text-3xl sm:text-4xl md:text-5xl text-[#313131] leading-tight mb-5">
              {{ article.title }}
            </h1>
            <p v-if="article.excerpt" class="text-lg text-gray-500 leading-relaxed">
              {{ article.excerpt }}
            </p>
          </div>

          <!-- Content card -->
          <div class="bg-white rounded-2xl p-8 md:p-12 shadow-md mb-12">
            <div class="prose prose-lg max-w-none" v-html="article.article_contents" />
          </div>

          <!-- CTA -->
          <div class="text-center mb-16">
            <NuxtLink
              to="/listings"
              class="inline-block px-8 py-4 rounded-full bg-[#00878E] font-semibold text-base text-white hover:bg-[#006b70] transition-all duration-300 shadow-md hover:shadow-lg"
            >
              Découvrir nos annonces
            </NuxtLink>
          </div>

          <!-- Similar articles -->
          <div v-if="similar.length">
            <h3 class="font-bold text-2xl text-[#313131] mb-6">
              Ces articles peuvent vous intéresser
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              <NuxtLink
                v-for="a in similar"
                :key="a.id"
                :to="`/articles/${a.slug}`"
                class="group bg-white rounded-2xl overflow-hidden shadow-md hover:shadow-xl transition-all duration-300 block"
              >
                <div class="relative h-48 overflow-hidden bg-gray-100">
                  <img
                    v-if="a.image_url"
                    :src="a.image_url"
                    :alt="a.alt_image || a.title"
                    class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                  />
                  <div v-else class="w-full h-full flex items-center justify-center">
                    <Icon name="lucide:newspaper" class="w-8 h-8 text-gray-200" />
                  </div>
                </div>
                <div class="p-5">
                  <div class="flex items-center gap-2 mb-2">
                    <span v-if="a.country" class="text-[11px] font-semibold uppercase tracking-wide text-[#00878E] bg-[#00878E]/10 px-2 py-0.5 rounded-full">{{ a.country }}</span>
                    <span v-if="a.read_time" class="text-[11px] text-gray-400">{{ a.read_time }} min</span>
                  </div>
                  <h4 class="font-semibold text-lg text-[#313131] mb-2 line-clamp-2 group-hover:text-[#00878E] transition-colors">
                    {{ a.title }}
                  </h4>
                  <p v-if="a.excerpt" class="text-sm text-gray-600 mb-3 line-clamp-2">{{ a.excerpt }}</p>
                  <div class="flex items-center gap-2 text-[#00878E] font-semibold text-sm group-hover:gap-3 transition-all duration-300">
                    Lire l'article <Icon name="lucide:arrow-right" class="w-4 h-4" />
                  </div>
                </div>
              </NuxtLink>
            </div>
          </div>

        </div>
      </div>
    </template>

    <!-- ── Not found ── -->
    <div v-else class="max-w-[800px] mx-auto px-6 py-24 text-center text-gray-400">
      <Icon name="lucide:file-x" class="w-12 h-12 mx-auto mb-4 opacity-30" />
      <p class="font-medium">Article introuvable.</p>
      <NuxtLink to="/articles" class="mt-4 inline-block text-sm text-[#00878E] hover:underline">
        Retour aux articles
      </NuxtLink>
    </div>

    <!-- ── Share modal ── -->
    <Teleport to="body">
      <div
        v-if="shareOpen"
        class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 px-4"
        @mousedown.self="shareOpen = false"
      >
        <div class="bg-white rounded-2xl p-6 max-w-md w-full shadow-2xl">
          <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-lg text-[#313131]">Partager cet article</h3>
            <button @click="shareOpen = false" class="text-gray-400 hover:text-gray-600 transition-colors">
              <Icon name="lucide:x" class="w-5 h-5" />
            </button>
          </div>
          <p class="text-sm text-gray-600 mb-4">Copiez le lien ci-dessous pour partager cet article</p>
          <div class="flex items-center gap-2 bg-gray-50 rounded-lg p-3 mb-4">
            <input
              type="text"
              readonly
              :value="pageUrl"
              class="flex-1 bg-transparent text-sm text-gray-700 outline-none"
            />
          </div>
          <button
            @click="copyLink"
            class="w-full flex items-center justify-center gap-2 px-6 py-3 rounded-full font-semibold text-white transition-all duration-300"
            :class="copied ? 'bg-green-500' : 'bg-[#00878E] hover:bg-[#006b70]'"
          >
            <Icon :name="copied ? 'lucide:check' : 'lucide:copy'" class="w-5 h-5" />
            {{ copied ? 'Lien copié !' : 'Copier le lien' }}
          </button>
        </div>
      </div>
    </Teleport>

  </div>
</template>

<script setup lang="ts">
const route = useRoute()
const { apiFetch } = useApi()

const article = ref<any>(null)
const similar = ref<any[]>([])
const loading = ref(true)
const shareOpen = ref(false)
const copied = ref(false)

const pageUrl = computed(() => {
  if (import.meta.client) return window.location.href
  return ''
})

onMounted(async () => {
  try {
    const res = await apiFetch<{ data: any }>(`/articles/${route.params.slug}`)
    article.value = res.data

    // Load similar articles (same country if possible, else recent)
    try {
      const params = new URLSearchParams({ per_page: '4' })
      if (article.value?.country) params.set('country', article.value.country)
      const simRes = await apiFetch<any>(`/articles?${params}`)
      similar.value = (simRes.data || [])
        .filter((a: any) => a.slug !== route.params.slug)
        .slice(0, 3)
    } catch {}
  } catch {
    article.value = null
  } finally {
    loading.value = false
  }
})

function formatDate(d: string) {
  return new Date(d).toLocaleDateString('fr-FR', { day: '2-digit', month: 'long', year: 'numeric' })
}

async function copyLink() {
  try {
    await navigator.clipboard.writeText(pageUrl.value)
    copied.value = true
    setTimeout(() => {
      copied.value = false
      shareOpen.value = false
    }, 1500)
  } catch {}
}

useHead(computed(() => ({
  title: article.value?.meta_title || article.value?.title || 'Article — GlobalRent',
  meta: article.value?.meta_description
    ? [{ name: 'description', content: article.value.meta_description }]
    : [],
})))
</script>

<style scoped>
.prose :deep(h2) { font-size: 1.5rem; font-weight: 700; margin: 2rem 0 1rem; color: #313131; }
.prose :deep(h3) { font-size: 1.25rem; font-weight: 600; margin: 1.5rem 0 0.75rem; color: #313131; }
.prose :deep(p) { margin: 1rem 0; line-height: 1.8; color: #4b5563; }
.prose :deep(ul) { list-style: disc; padding-left: 1.5rem; margin: 1rem 0; }
.prose :deep(ol) { list-style: decimal; padding-left: 1.5rem; margin: 1rem 0; }
.prose :deep(li) { margin: 0.4rem 0; color: #4b5563; }
.prose :deep(a) { color: #00878E; text-decoration: underline; }
.prose :deep(strong) { font-weight: 600; color: #313131; }
.prose :deep(img) { border-radius: 0.75rem; margin: 1.5rem 0; max-width: 100%; }
.prose :deep(blockquote) { border-left: 4px solid #00878E; padding-left: 1rem; color: #6b7280; font-style: italic; margin: 1.5rem 0; }
</style>
