<template>
  <div>
    <!-- Page header -->
    <div class="flex items-center justify-between mb-8">
      <div>
        <h1 class="font-bold text-2xl text-[#0f172a]">Dashboard</h1>
        <p class="text-gray-400 text-sm mt-1">Vue d'ensemble de votre plateforme</p>
      </div>
    </div>

    <!-- Welcome banner -->
    <div class="bg-gradient-to-r from-[#00878E] to-[#006b70] rounded-2xl p-6 mb-8 flex items-center justify-between">
      <div>
        <p class="text-white/70 text-sm mb-1">Bienvenue,</p>
        <p class="text-white font-bold text-2xl">{{ admin?.name }}</p>
      </div>
      <div class="w-16 h-16 bg-white/10 rounded-2xl flex items-center justify-center">
        <Icon name="lucide:shield-check" class="w-8 h-8 text-white" />
      </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-8">
      <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm flex items-center gap-4">
        <div class="w-12 h-12 bg-emerald-50 rounded-xl flex items-center justify-center shrink-0">
          <Icon name="lucide:newspaper" class="w-6 h-6 text-emerald-500" />
        </div>
        <div>
          <p class="text-2xl font-bold text-[#0f172a]">{{ stats.articles }}</p>
          <p class="text-sm text-gray-400">Articles publiés</p>
        </div>
      </div>
      <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm flex items-center gap-4">
        <div class="w-12 h-12 bg-amber-50 rounded-xl flex items-center justify-center shrink-0">
          <Icon name="lucide:file-edit" class="w-6 h-6 text-amber-500" />
        </div>
        <div>
          <p class="text-2xl font-bold text-[#0f172a]">{{ stats.drafts }}</p>
          <p class="text-sm text-gray-400">Brouillons</p>
        </div>
      </div>
      <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm flex items-center gap-4">
        <div class="w-12 h-12 bg-[#00878E]/10 rounded-xl flex items-center justify-center shrink-0">
          <Icon name="lucide:layers" class="w-6 h-6 text-[#00878E]" />
        </div>
        <div>
          <p class="text-2xl font-bold text-[#0f172a]">{{ stats.total }}</p>
          <p class="text-sm text-gray-400">Total articles</p>
        </div>
      </div>
    </div>

    <!-- Quick actions -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
      <NuxtLink to="/admin/articles/create" class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm hover:border-[#00878E] hover:shadow-md transition-all group flex items-center gap-4">
        <div class="w-12 h-12 bg-[#00878E]/10 rounded-xl flex items-center justify-center group-hover:bg-[#00878E]/20 transition-colors shrink-0">
          <Icon name="lucide:plus-circle" class="w-6 h-6 text-[#00878E]" />
        </div>
        <div>
          <p class="font-semibold text-[#0f172a]">Nouvel article</p>
          <p class="text-sm text-gray-400">Créer et publier un article</p>
        </div>
        <Icon name="lucide:arrow-right" class="w-4 h-4 text-gray-300 group-hover:text-[#00878E] ms-auto transition-colors" />
      </NuxtLink>

      <NuxtLink to="/admin/articles" class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm hover:border-[#00878E] hover:shadow-md transition-all group flex items-center gap-4">
        <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center group-hover:bg-blue-100 transition-colors shrink-0">
          <Icon name="lucide:list" class="w-6 h-6 text-blue-500" />
        </div>
        <div>
          <p class="font-semibold text-[#0f172a]">Gérer les articles</p>
          <p class="text-sm text-gray-400">Voir, modifier, supprimer</p>
        </div>
        <Icon name="lucide:arrow-right" class="w-4 h-4 text-gray-300 group-hover:text-[#00878E] ms-auto transition-colors" />
      </NuxtLink>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useAdminAuth } from '~/composables/useAdminAuth'

definePageMeta({ layout: 'admin' as any })

const { admin, adminFetch } = useAdminAuth()
const stats = ref({ articles: 0, drafts: 0, total: 0 })

onMounted(async () => {
  try {
    const [pub, draft] = await Promise.all([
      adminFetch<any>('/admin/articles?status=published&per_page=1'),
      adminFetch<any>('/admin/articles?status=draft&per_page=1'),
    ])
    stats.value.articles = pub.meta?.total || 0
    stats.value.drafts   = draft.meta?.total || 0
    stats.value.total    = (pub.meta?.total || 0) + (draft.meta?.total || 0)
  } catch {}
})
</script>
