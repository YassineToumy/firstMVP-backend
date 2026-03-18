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
      <h1 class="font-bold text-2xl text-[#313131] mb-2">Dashboard</h1>
      <p class="text-gray-500 mb-8">Bienvenue, {{ admin?.name }}</p>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <NuxtLink to="/admin/articles" class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:border-[#00878E] transition-colors group">
          <div class="w-10 h-10 bg-[#00878E]/10 rounded-xl flex items-center justify-center mb-4 group-hover:bg-[#00878E]/20 transition-colors">
            <Icon name="lucide:newspaper" class="w-5 h-5 text-[#00878E]" />
          </div>
          <p class="font-bold text-[18px] text-[#313131]">Articles</p>
          <p class="text-sm text-gray-400 mt-1">Gérer les articles du blog</p>
        </NuxtLink>
      </div>
    </main>
  </div>
</template>

<script setup lang="ts">
definePageMeta({ layout: false })

const { admin, logout, fetchMe } = useAdminAuth()
onMounted(fetchMe)
</script>

<style scoped>
@reference "tailwindcss";
.sidebar-link {
  @apply flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-50 transition-colors;
}
.sidebar-link-active {
  @apply bg-[#00878E]/10 text-[#00878E];
}
</style>
