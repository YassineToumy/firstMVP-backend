<template>
  <div class="min-h-screen bg-gray-100">
    <!-- Sidebar -->
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
        <NuxtLink to="/admin" class="sidebar-link" exact-active-class="sidebar-link-active">
          <Icon name="lucide:layout-dashboard" class="w-4 h-4" /> Dashboard
        </NuxtLink>
        <NuxtLink to="/admin/articles" class="sidebar-link" active-class="sidebar-link-active">
          <Icon name="lucide:newspaper" class="w-4 h-4" /> Articles
        </NuxtLink>
      </nav>
      <div class="p-4 border-t border-gray-100">
        <button @click="logout" class="w-full flex items-center gap-2 px-3 py-2 text-sm text-red-500 hover:bg-red-50 rounded-lg transition-colors">
          <Icon name="lucide:log-out" class="w-4 h-4" /> Déconnexion
        </button>
      </div>
    </aside>

    <!-- Main -->
    <main class="pl-[240px] p-8">
      <div class="flex items-center justify-between mb-8">
        <div>
          <h1 class="font-bold text-2xl text-[#313131]">Articles</h1>
          <p class="text-gray-500 text-sm mt-1">{{ meta?.total || 0 }} article(s) au total</p>
        </div>
        <NuxtLink to="/admin/articles/create" class="flex items-center gap-2 px-5 py-2.5 bg-[#00878E] hover:bg-[#006b70] text-white rounded-full font-semibold text-sm transition-colors">
          <Icon name="lucide:plus" class="w-4 h-4" /> Nouvel article
        </NuxtLink>
      </div>

      <!-- Filters -->
      <div class="flex items-center gap-3 mb-6">
        <select v-model="statusFilter" @change="load(1)" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-[#00878E]">
          <option value="">Tous les statuts</option>
          <option value="draft">Brouillon</option>
          <option value="published">Publié</option>
          <option value="archived">Archivé</option>
        </select>
        <select v-model="countryFilter" @change="load(1)" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-[#00878E]">
          <option value="">Tous les pays</option>
          <option value="FR">France</option>
          <option value="TN">Tunisie</option>
          <option value="EG">Égypte</option>
          <option value="CA">Canada</option>
        </select>
      </div>

      <!-- Table -->
      <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div v-if="loading" class="p-12 text-center text-gray-400">
          <Icon name="lucide:loader-circle" class="w-6 h-6 animate-spin mx-auto mb-2" />
          Chargement...
        </div>

        <table v-else-if="articles.length" class="w-full">
          <thead class="bg-gray-50 border-b border-gray-100">
            <tr>
              <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Titre</th>
              <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Pays</th>
              <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Statut</th>
              <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Date</th>
              <th class="px-6 py-3"></th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr v-for="a in articles" :key="a.id" class="hover:bg-gray-50 transition-colors">
              <td class="px-6 py-4">
                <p class="font-medium text-sm text-[#313131] line-clamp-1">{{ a.title }}</p>
                <p class="text-xs text-gray-400 mt-0.5">{{ a.slug }}</p>
              </td>
              <td class="px-6 py-4 text-sm text-gray-600">{{ a.country || '—' }}</td>
              <td class="px-6 py-4">
                <span class="px-2.5 py-1 rounded-full text-xs font-semibold" :class="statusClass(a.status)">
                  {{ statusLabel(a.status) }}
                </span>
              </td>
              <td class="px-6 py-4 text-sm text-gray-400">{{ formatDate(a.created_at) }}</td>
              <td class="px-6 py-4">
                <div class="flex items-center gap-2 justify-end">
                  <NuxtLink :to="`/admin/articles/${a.id}/edit`" class="p-1.5 text-gray-400 hover:text-[#00878E] hover:bg-[#00878E]/10 rounded-lg transition-colors">
                    <Icon name="lucide:pencil" class="w-4 h-4" />
                  </NuxtLink>
                  <button @click="deleteArticle(a.id)" class="p-1.5 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors">
                    <Icon name="lucide:trash-2" class="w-4 h-4" />
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>

        <div v-else class="p-12 text-center text-gray-400">
          <Icon name="lucide:newspaper" class="w-8 h-8 mx-auto mb-2 opacity-40" />
          <p>Aucun article trouvé</p>
        </div>
      </div>

      <!-- Pagination -->
      <div v-if="meta && meta.last_page > 1" class="flex items-center justify-center gap-2 mt-6">
        <button v-for="p in meta.last_page" :key="p" @click="load(p)"
          class="w-9 h-9 rounded-lg text-sm font-medium transition-colors"
          :class="p === meta.current_page ? 'bg-[#00878E] text-white' : 'bg-white text-gray-600 hover:bg-gray-50 border border-gray-200'">
          {{ p }}
        </button>
      </div>
    </main>
  </div>
</template>

<script setup lang="ts">
definePageMeta({ middleware: 'admin', layout: false })

const { admin, logout, adminFetch, fetchMe } = useAdminAuth()

const articles = ref<any[]>([])
const meta = ref<any>(null)
const loading = ref(true)
const statusFilter = ref('')
const countryFilter = ref('')

async function load(page = 1) {
  loading.value = true
  try {
    const params = new URLSearchParams({ page: String(page), per_page: '20' })
    if (statusFilter.value) params.set('status', statusFilter.value)
    if (countryFilter.value) params.set('country', countryFilter.value)
    const res = await adminFetch<any>(`/admin/articles?${params}`)
    articles.value = res.data
    meta.value = res.meta
  } finally {
    loading.value = false
  }
}

async function deleteArticle(id: number) {
  if (!confirm('Supprimer cet article ?')) return
  await adminFetch(`/admin/articles/${id}`, { method: 'DELETE' })
  load()
}

function statusClass(s: string) {
  return s === 'published' ? 'bg-green-100 text-green-700'
       : s === 'draft'     ? 'bg-yellow-100 text-yellow-700'
                           : 'bg-gray-100 text-gray-500'
}
function statusLabel(s: string) {
  return s === 'published' ? 'Publié' : s === 'draft' ? 'Brouillon' : 'Archivé'
}
function formatDate(d: string) {
  return new Date(d).toLocaleDateString('fr-FR', { day: '2-digit', month: 'short', year: 'numeric' })
}

onMounted(() => { fetchMe(); load() })
</script>

<style scoped>
@reference "tailwindcss";
.sidebar-link { @apply flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-50 transition-colors; }
.sidebar-link-active { @apply bg-[#00878E]/10 text-[#00878E]; }
</style>
