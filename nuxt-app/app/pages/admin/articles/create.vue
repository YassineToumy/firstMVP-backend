<template>
  <div class="min-h-screen bg-gray-100">
    <aside class="fixed top-0 left-0 h-full w-[240px] bg-white border-r border-gray-200 flex flex-col z-40">
      <div class="p-6 border-b border-gray-100">
        <div class="flex items-center gap-3">
          <div class="w-9 h-9 bg-[#00878E] rounded-xl flex items-center justify-center">
            <Icon name="lucide:shield" class="w-4 h-4 text-white" />
          </div>
          <div>
            <p class="font-bold text-[14px] text-[#313131]">Admin</p>
            <p class="text-[11px] text-gray-400">{{ admin?.name }}</p>
          </div>
        </div>
      </div>
      <nav class="flex-1 p-4 space-y-1">
        <NuxtLink to="/admin" class="sidebar-link" exact-active-class="sidebar-link-active"><Icon name="lucide:layout-dashboard" class="w-4 h-4" /> Dashboard</NuxtLink>
        <NuxtLink to="/admin/articles" class="sidebar-link" active-class="sidebar-link-active"><Icon name="lucide:newspaper" class="w-4 h-4" /> Articles</NuxtLink>
      </nav>
      <div class="p-4 border-t border-gray-100">
        <button @click="logout" class="w-full flex items-center gap-2 px-3 py-2 text-sm text-red-500 hover:bg-red-50 rounded-lg transition-colors">
          <Icon name="lucide:log-out" class="w-4 h-4" /> Déconnexion
        </button>
      </div>
    </aside>

    <main class="pl-[240px] p-8">
      <div class="flex items-center gap-3 mb-8">
        <NuxtLink to="/admin/articles" class="text-gray-400 hover:text-[#00878E] transition-colors">
          <Icon name="lucide:arrow-left" class="w-5 h-5" />
        </NuxtLink>
        <h1 class="font-bold text-2xl text-[#313131]">Nouvel article</h1>
      </div>

      <form @submit.prevent="submit" class="grid grid-cols-3 gap-6">
        <!-- Left col: main content -->
        <div class="col-span-2 space-y-5">
          <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 space-y-4">
            <div>
              <label class="field-label">Titre *</label>
              <input v-model="form.title" @input="autoSlug" required type="text" class="field-input" placeholder="Titre de l'article" />
            </div>
            <div>
              <label class="field-label">Slug</label>
              <input v-model="form.slug" type="text" class="field-input" placeholder="url-de-larticle" />
            </div>
            <div>
              <label class="field-label">Extrait</label>
              <textarea v-model="form.excerpt" rows="3" class="field-input resize-none" placeholder="Résumé court affiché dans la liste..." />
            </div>
            <div>
              <label class="field-label">Contenu *</label>
              <textarea v-model="form.article_contents" rows="14" required class="field-input resize-y font-mono text-sm" placeholder="Contenu de l'article (HTML ou Markdown)..." />
            </div>
          </div>

          <!-- SEO -->
          <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 space-y-4">
            <h2 class="font-semibold text-[15px] text-[#313131]">SEO</h2>
            <div>
              <label class="field-label">Meta title</label>
              <input v-model="form.meta_title" type="text" class="field-input" placeholder="Titre pour les moteurs de recherche" />
            </div>
            <div>
              <label class="field-label">Meta description</label>
              <textarea v-model="form.meta_description" rows="2" class="field-input resize-none" placeholder="Description pour les moteurs de recherche" />
            </div>
          </div>
        </div>

        <!-- Right col: settings -->
        <div class="space-y-5">
          <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 space-y-4">
            <h2 class="font-semibold text-[15px] text-[#313131]">Publication</h2>
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
                <option value="">Tous</option>
                <option value="FR">France</option>
                <option value="TN">Tunisie</option>
                <option value="EG">Égypte</option>
                <option value="CA">Canada</option>
              </select>
            </div>
            <div>
              <label class="field-label">Temps de lecture (min)</label>
              <input v-model.number="form.read_time" type="number" min="1" max="999" class="field-input" placeholder="5" />
            </div>

            <p v-if="error" class="text-sm text-red-500 bg-red-50 px-3 py-2 rounded-lg">{{ error }}</p>
            <button type="submit" :disabled="saving"
              class="w-full py-3 rounded-full bg-[#00878E] hover:bg-[#006b70] text-white font-semibold text-sm transition-colors disabled:opacity-60 flex items-center justify-center gap-2">
              <Icon v-if="saving" name="lucide:loader-circle" class="w-4 h-4 animate-spin" />
              {{ saving ? 'Enregistrement...' : 'Enregistrer' }}
            </button>
          </div>

          <!-- Image -->
          <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 space-y-4">
            <h2 class="font-semibold text-[15px] text-[#313131]">Image</h2>
            <div>
              <label class="field-label">URL de l'image</label>
              <input v-model="form.image_url" type="url" class="field-input" placeholder="https://..." />
            </div>
            <img v-if="form.image_url" :src="form.image_url" class="w-full h-32 object-cover rounded-xl" />
            <div>
              <label class="field-label">Texte alternatif</label>
              <input v-model="form.alt_image" type="text" class="field-input" placeholder="Description de l'image" />
            </div>
          </div>
        </div>
      </form>
    </main>
  </div>
</template>

<script setup lang="ts">
import { slugify } from '~/utils/slugify'
definePageMeta({ layout: false })

const { admin, logout, adminFetch } = useAdminAuth()
const router = useRouter()
const saving = ref(false)
const error = ref('')

const form = reactive({
  title: '', slug: '', excerpt: '', article_contents: '',
  meta_title: '', meta_description: '',
  image_url: '', alt_image: '', country: '', read_time: undefined as number | undefined,
  status: 'draft',
})

function autoSlug() {
  form.slug = slugify(form.title)
}

async function submit() {
  error.value = ''
  saving.value = true
  try {
    const res = await adminFetch<{ data: any }>('/admin/articles', {
      method: 'POST',
      body: { ...form },
    })
    router.push('/admin/articles')
  } catch (e: any) {
    error.value = e?.data?.message || 'Erreur lors de la création.'
  } finally {
    saving.value = false
  }
}
</script>

<style scoped>
@reference "tailwindcss";
.sidebar-link { @apply flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-50 transition-colors; }
.sidebar-link-active { @apply bg-[#00878E]/10 text-[#00878E]; }
.field-label { @apply block text-sm font-medium text-gray-700 mb-1; }
.field-input { @apply w-full px-3 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:border-[#00878E] transition-colors; }
</style>
