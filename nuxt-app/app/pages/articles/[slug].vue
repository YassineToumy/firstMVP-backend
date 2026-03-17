<template>
  <div class="min-h-screen bg-gray-50 pt-[84px]">
    <div v-if="loading" class="max-w-[800px] mx-auto px-6 py-16 space-y-4">
      <div class="h-8 bg-gray-200 rounded animate-pulse w-2/3" />
      <div class="h-4 bg-gray-200 rounded animate-pulse w-1/3" />
      <div class="h-64 bg-gray-200 rounded-2xl animate-pulse mt-8" />
    </div>

    <div v-else-if="article" class="max-w-[800px] mx-auto px-6 py-12">
      <!-- Back -->
      <NuxtLink to="/articles" class="inline-flex items-center gap-2 text-sm text-gray-400 hover:text-[#00878E] mb-8 transition-colors">
        <Icon name="lucide:arrow-left" class="w-4 h-4" /> Retour aux articles
      </NuxtLink>

      <!-- Meta -->
      <div class="flex items-center gap-3 mb-4">
        <span v-if="article.country" class="text-xs font-semibold uppercase tracking-wide text-[#00878E] bg-[#00878E]/10 px-3 py-1 rounded-full">
          {{ article.country }}
        </span>
        <span v-if="article.read_time" class="text-xs text-gray-400 flex items-center gap-1">
          <Icon name="lucide:clock" class="w-3 h-3" /> {{ article.read_time }} min de lecture
        </span>
        <span v-if="article.published_at" class="text-xs text-gray-400">
          {{ formatDate(article.published_at) }}
        </span>
      </div>

      <!-- Title -->
      <h1 class="font-bold text-3xl sm:text-4xl text-[#313131] leading-tight mb-4">{{ article.title }}</h1>

      <!-- Excerpt -->
      <p v-if="article.excerpt" class="text-lg text-gray-500 mb-8 leading-relaxed">{{ article.excerpt }}</p>

      <!-- Cover image -->
      <div v-if="article.image_url" class="rounded-2xl overflow-hidden mb-10 shadow-md">
        <img :src="article.image_url" :alt="article.alt_image || article.title" class="w-full h-[380px] object-cover" />
      </div>

      <!-- Content -->
      <div class="prose prose-lg max-w-none" v-html="article.article_contents" />
    </div>

    <div v-else class="max-w-[800px] mx-auto px-6 py-24 text-center text-gray-400">
      <Icon name="lucide:file-x" class="w-12 h-12 mx-auto mb-4 opacity-30" />
      <p>Article introuvable.</p>
      <NuxtLink to="/" class="mt-4 inline-block text-sm text-[#00878E] hover:underline">Retour à l'accueil</NuxtLink>
    </div>
  </div>
</template>

<script setup lang="ts">
const route = useRoute()
const { apiFetch } = useApi()

const article = ref<any>(null)
const loading = ref(true)

onMounted(async () => {
  try {
    const res = await apiFetch<{ data: any }>(`/articles/${route.params.slug}`)
    article.value = res.data
  } catch {
    article.value = null
  } finally {
    loading.value = false
  }
})

function formatDate(d: string) {
  return new Date(d).toLocaleDateString('fr-FR', { day: '2-digit', month: 'long', year: 'numeric' })
}

useHead(computed(() => ({
  title: article.value?.meta_title || article.value?.title || 'Article',
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
