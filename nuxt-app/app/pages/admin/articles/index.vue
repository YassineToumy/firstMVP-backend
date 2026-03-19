<template>
  <div>
    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
      <div>
        <h1 class="font-bold text-2xl text-[#0f172a]">Articles</h1>
        <p class="text-gray-400 text-sm mt-1">{{ meta?.total || 0 }} article(s) au total</p>
      </div>
      <NuxtLink to="/admin/articles/create" class="flex items-center gap-2 px-5 py-2.5 bg-[#00878E] hover:bg-[#006b70] text-white rounded-full font-semibold text-sm transition-colors shadow-sm">
        <Icon name="lucide:plus" class="w-4 h-4" /> Nouvel article
      </NuxtLink>
    </div>

    <!-- Filters -->
    <div class="flex items-center gap-3 mb-5">
      <select v-model="statusFilter" @change="load(1)" class="px-3 py-2 bg-white border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-[#00878E] shadow-sm">
        <option value="">Tous les statuts</option>
        <option value="draft">Brouillon</option>
        <option value="published">Publié</option>
        <option value="archived">Archivé</option>
      </select>
      <select v-model="countryFilter" @change="load(1)" class="px-3 py-2 bg-white border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-[#00878E] shadow-sm">
        <option value="">Tous les pays</option>
        <option value="FR">France</option>
        <option value="TN">Tunisie</option>
        <option value="EG">Égypte</option>
        <option value="CA">Canada</option>
      </select>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
      <div v-if="loading" class="p-16 text-center text-gray-400">
        <Icon name="lucide:loader-circle" class="w-7 h-7 animate-spin mx-auto mb-3 text-[#00878E]" />
        <p class="text-sm">Chargement...</p>
      </div>

      <table v-else-if="articles.length" class="w-full">
        <thead class="bg-gray-50 border-b border-gray-100">
          <tr>
            <th class="text-left px-6 py-3.5 text-xs font-semibold text-gray-400 uppercase tracking-wide">Article</th>
            <th class="text-left px-6 py-3.5 text-xs font-semibold text-gray-400 uppercase tracking-wide">Pays</th>
            <th class="text-left px-6 py-3.5 text-xs font-semibold text-gray-400 uppercase tracking-wide">Statut</th>
            <th class="text-left px-6 py-3.5 text-xs font-semibold text-gray-400 uppercase tracking-wide">Date</th>
            <th class="px-6 py-3.5"></th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
          <tr v-for="a in articles" :key="a.id" class="hover:bg-gray-50/80 transition-colors">
            <td class="px-6 py-4">
              <div class="flex items-center gap-3">
                <div v-if="a.image_url" class="w-10 h-10 rounded-xl overflow-hidden shrink-0 bg-gray-100">
                  <img :src="a.image_url" :alt="a.alt_image || a.title" class="w-full h-full object-cover" />
                </div>
                <div v-else class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center shrink-0">
                  <Icon name="lucide:image" class="w-4 h-4 text-gray-300" />
                </div>
                <div>
                  <p class="font-medium text-sm text-[#0f172a] line-clamp-1">{{ a.title }}</p>
                  <p class="text-xs text-gray-400 mt-0.5">{{ a.slug }}</p>
                </div>
              </div>
            </td>
            <td class="px-6 py-4">
              <span v-if="a.country" class="flex items-center gap-1.5 text-sm text-gray-600">
                <img :src="`https://flagcdn.com/16x12/${a.country.toLowerCase()}.png`" class="w-4 h-auto rounded-sm" />
                {{ a.country }}
              </span>
              <span v-else class="text-gray-300 text-sm">—</span>
            </td>
            <td class="px-6 py-4">
              <span class="px-2.5 py-1 rounded-full text-xs font-semibold" :class="statusClass(a.status)">
                {{ statusLabel(a.status) }}
              </span>
            </td>
            <td class="px-6 py-4 text-sm text-gray-400">{{ formatDate(a.created_at) }}</td>
            <td class="px-6 py-4">
              <div class="flex items-center gap-1 justify-end">
                <NuxtLink :to="`/admin/articles/${a.id}/edit`" class="p-2 text-gray-400 hover:text-[#00878E] hover:bg-[#00878E]/10 rounded-lg transition-colors">
                  <Icon name="lucide:pencil" class="w-4 h-4" />
                </NuxtLink>
                <button @click="deleteArticle(a.id)" class="p-2 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors">
                  <Icon name="lucide:trash-2" class="w-4 h-4" />
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>

      <div v-else class="p-16 text-center text-gray-400">
        <Icon name="lucide:newspaper" class="w-10 h-10 mx-auto mb-3 opacity-30" />
        <p class="font-medium text-sm">Aucun article trouvé</p>
        <NuxtLink to="/admin/articles/create" class="inline-flex items-center gap-1.5 mt-3 text-sm text-[#00878E] hover:underline">
          <Icon name="lucide:plus" class="w-3.5 h-3.5" /> Créer le premier article
        </NuxtLink>
      </div>
    </div>

    <!-- Pagination -->
    <div v-if="meta && meta.last_page > 1" class="flex items-center justify-center gap-2 mt-6">
      <button v-for="p in meta.last_page" :key="p" @click="load(p)"
        class="w-9 h-9 rounded-xl text-sm font-medium transition-colors"
        :class="p === meta.current_page ? 'bg-[#00878E] text-white shadow-sm' : 'bg-white text-gray-600 hover:bg-gray-50 border border-gray-200'">
        {{ p }}
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useAdminAuth } from '~/composables/useAdminAuth'

definePageMeta({ layout: 'admin' as any })

const { adminFetch } = useAdminAuth()

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
  return s === 'published' ? 'bg-emerald-100 text-emerald-700'
       : s === 'draft'     ? 'bg-amber-100 text-amber-700'
                           : 'bg-gray-100 text-gray-500'
}
function statusLabel(s: string) {
  return s === 'published' ? 'Publié' : s === 'draft' ? 'Brouillon' : 'Archivé'
}
function formatDate(d: string) {
  return new Date(d).toLocaleDateString('fr-FR', { day: '2-digit', month: 'short', year: 'numeric' })
}

onMounted(load)
</script>
