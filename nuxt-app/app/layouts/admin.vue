<template>
  <div class="min-h-screen bg-[#f8fafc] flex">

    <!-- Sidebar -->
    <aside class="fixed top-0 left-0 h-full w-[260px] bg-[#0f172a] flex flex-col z-40">

      <!-- Logo -->
      <div class="px-6 py-5 border-b border-white/10">
        <NuxtLink to="/" class="flex items-center gap-3">
          <div class="w-9 h-9 bg-[#00878E] rounded-xl flex items-center justify-center shrink-0">
            <Icon name="lucide:home" class="w-4 h-4 text-white" />
          </div>
          <div>
            <p class="font-bold text-[15px] text-white leading-tight">GlobalRent</p>
            <p class="text-[10px] text-white/40 leading-tight">Administration</p>
          </div>
        </NuxtLink>
      </div>

      <!-- Admin info -->
      <div class="px-4 py-4 border-b border-white/10">
        <div class="flex items-center gap-3 px-3 py-2.5 bg-white/5 rounded-xl">
          <div class="w-8 h-8 rounded-full bg-[#00878E] flex items-center justify-center text-white font-bold text-sm shrink-0">
            {{ adminInitial }}
          </div>
          <div class="min-w-0">
            <p class="text-white text-[13px] font-semibold truncate">{{ admin?.name || 'Admin' }}</p>
            <p class="text-white/40 text-[11px] truncate">{{ admin?.email }}</p>
          </div>
        </div>
      </div>

      <!-- Nav -->
      <nav class="flex-1 px-3 py-4 space-y-1">
        <p class="px-3 text-[10px] font-semibold text-white/30 uppercase tracking-widest mb-2">Navigation</p>
        <NuxtLink to="/admin" class="nav-item" exact-active-class="nav-item-active">
          <Icon name="lucide:layout-dashboard" class="w-4 h-4" />
          <span>Dashboard</span>
        </NuxtLink>
        <NuxtLink to="/admin/articles" class="nav-item" active-class="nav-item-active">
          <Icon name="lucide:newspaper" class="w-4 h-4" />
          <span>Articles</span>
        </NuxtLink>
      </nav>

      <!-- Footer -->
      <div class="px-3 py-4 border-t border-white/10">
        <NuxtLink to="/" class="nav-item mb-1">
          <Icon name="lucide:external-link" class="w-4 h-4" />
          <span>Voir le site</span>
        </NuxtLink>
        <button @click="logout" class="nav-item w-full text-red-400 hover:bg-red-500/10 hover:text-red-300">
          <Icon name="lucide:log-out" class="w-4 h-4" />
          <span>Déconnexion</span>
        </button>
      </div>
    </aside>

    <!-- Content -->
    <div class="flex-1 pl-[260px] flex flex-col min-h-screen">

      <!-- Topbar -->
      <header class="h-[64px] bg-white border-b border-gray-200 flex items-center px-8 sticky top-0 z-30 gap-4">
        <div class="flex-1">
          <slot name="topbar" />
        </div>
        <div class="flex items-center gap-2 text-sm text-gray-500">
          <Icon name="lucide:calendar" class="w-4 h-4" />
          {{ today }}
        </div>
      </header>

      <!-- Page content -->
      <main class="flex-1 p-8">
        <slot />
      </main>

    </div>
  </div>
</template>

<script setup lang="ts">
import { useAdminAuth } from '~/composables/useAdminAuth'

const { admin, logout, fetchMe } = useAdminAuth()

const adminInitial = computed(() => (admin.value?.name?.[0] || 'A').toUpperCase())

const today = new Date().toLocaleDateString('fr-FR', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' })

onMounted(fetchMe)
</script>

<style scoped>
@reference "tailwindcss";
.nav-item {
  @apply flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium text-white/60 hover:bg-white/10 hover:text-white transition-all duration-150 w-full;
}
.nav-item-active {
  @apply bg-[#00878E]/20 text-[#00878E] hover:bg-[#00878E]/25 hover:text-[#00878E];
}
</style>
