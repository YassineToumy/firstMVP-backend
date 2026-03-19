<template>
  <div>
    <!-- Header -->
    <div class="flex items-center gap-3 mb-8">
      <NuxtLink to="/admin/articles" class="w-9 h-9 flex items-center justify-center rounded-xl border border-gray-200 bg-white hover:border-[#00878E] text-gray-400 hover:text-[#00878E] transition-all">
        <Icon name="lucide:arrow-left" class="w-4 h-4" />
      </NuxtLink>
      <div>
        <h1 class="font-bold text-2xl text-[#0f172a]">Modifier l'article</h1>
        <p class="text-gray-400 text-sm mt-0.5">Mettre à jour le contenu et les paramètres</p>
      </div>
    </div>

    <div v-if="loadingArticle" class="flex items-center justify-center py-24 text-gray-400">
      <Icon name="lucide:loader-circle" class="w-8 h-8 animate-spin text-[#00878E]" />
    </div>

    <form v-else @submit.prevent="submit" class="grid grid-cols-3 gap-6">
      <!-- Left: content -->
      <div class="col-span-2 space-y-5">
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 space-y-4">
          <div>
            <label class="field-label">Titre *</label>
            <input v-model="form.title" required type="text" class="field-input" />
          </div>
          <div>
            <label class="field-label">Slug</label>
            <input v-model="form.slug" type="text" class="field-input" />
          </div>
          <div>
            <label class="field-label">Extrait</label>
            <textarea v-model="form.excerpt" rows="3" class="field-input resize-none" />
          </div>
          <div>
            <label class="field-label">Contenu *</label>
            <textarea v-model="form.article_contents" rows="16" required class="field-input resize-y font-mono text-sm" />
          </div>
        </div>

        <!-- SEO -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 space-y-4">
          <h2 class="font-semibold text-[15px] text-[#0f172a] flex items-center gap-2">
            <Icon name="lucide:search" class="w-4 h-4 text-gray-400" /> SEO
          </h2>
          <div>
            <label class="field-label">Meta title</label>
            <input v-model="form.meta_title" type="text" class="field-input" />
          </div>
          <div>
            <label class="field-label">Meta description</label>
            <textarea v-model="form.meta_description" rows="2" class="field-input resize-none" />
          </div>
        </div>
      </div>

      <!-- Right: settings -->
      <div class="space-y-5">
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 space-y-4">
          <h2 class="font-semibold text-[15px] text-[#0f172a] flex items-center gap-2">
            <Icon name="lucide:send" class="w-4 h-4 text-gray-400" /> Publication
          </h2>
          <div>
            <label class="field-label">Statut</label>
            <select v-model="form.status" class="field-input">
              <option value="draft">Brouillon</option>
              <option value="published">Publié</option>
              <option value="archived">Archivé</option>
            </select>
          </div>
          <div>
            <label class="field-label">Pays cible</label>
            <select v-model="form.country" class="field-input">
              <option value="">Tous les pays</option>
              <option value="FR">France</option>
              <option value="TN">Tunisie</option>
              <option value="EG">Égypte</option>
              <option value="CA">Canada</option>
            </select>
          </div>
          <div>
            <label class="field-label">Temps de lecture (min)</label>
            <input v-model.number="form.read_time" type="number" min="1" max="999" class="field-input" />
          </div>
          <p v-if="error" class="text-sm text-red-500 bg-red-50 px-3 py-2 rounded-xl">{{ error }}</p>
          <button type="submit" :disabled="saving"
            class="w-full py-3 rounded-full bg-[#00878E] hover:bg-[#006b70] text-white font-semibold text-sm transition-colors disabled:opacity-60 flex items-center justify-center gap-2 shadow-sm">
            <Icon v-if="saving" name="lucide:loader-circle" class="w-4 h-4 animate-spin" />
            {{ saving ? 'Enregistrement...' : 'Enregistrer les modifications' }}
          </button>
        </div>

        <!-- Image -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 space-y-4">
          <h2 class="font-semibold text-[15px] text-[#0f172a] flex items-center gap-2">
            <Icon name="lucide:image" class="w-4 h-4 text-gray-400" /> Image
          </h2>
          <div>
            <label class="field-label">URL de l'image</label>
            <input v-model="form.image_url" type="url" class="field-input" />
          </div>
          <div v-if="form.image_url" class="rounded-xl overflow-hidden border border-gray-100">
            <img :src="form.image_url" class="w-full h-36 object-cover" />
          </div>
          <div>
            <label class="field-label">Texte alternatif</label>
            <input v-model="form.alt_image" type="text" class="field-input" />
          </div>
        </div>
      </div>
    </form>
  </div>
</template>

<script setup lang="ts">
import { useAdminAuth } from '~/composables/useAdminAuth'

definePageMeta({ layout: 'admin' as any })

const { adminFetch } = useAdminAuth()
const route = useRoute()
const router = useRouter()
const id = route.params.id as string

const loadingArticle = ref(true)
const saving = ref(false)
const error = ref('')

const form = reactive({
  title: '', slug: '', excerpt: '', article_contents: '',
  meta_title: '', meta_description: '',
  image_url: '', alt_image: '', country: '', read_time: undefined as number | undefined,
  status: 'draft',
})

onMounted(async () => {
  try {
    const res = await adminFetch<{ data: any }>(`/admin/articles/${id}`)
    Object.assign(form, res.data)
  } finally {
    loadingArticle.value = false
  }
})

async function submit() {
  error.value = ''
  saving.value = true
  try {
    await adminFetch(`/admin/articles/${id}`, { method: 'PUT', body: { ...form } })
    router.push('/admin/articles')
  } catch (e: any) {
    error.value = e?.data?.message || 'Erreur lors de la mise à jour.'
  } finally {
    saving.value = false
  }
}
</script>

<style scoped>
@reference "tailwindcss";
.field-label { @apply block text-sm font-medium text-gray-600 mb-1.5; }
.field-input { @apply w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-[#00878E] transition-colors bg-white; }
</style>
