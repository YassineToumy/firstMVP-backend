<template>
  <aside class="w-full md:w-64 shrink-0">
    <!-- User info -->
    <div class="bg-white rounded-2xl border border-gray-100 p-5 mb-4">
      <div class="flex items-center gap-3">
        <div class="w-12 h-12 rounded-full bg-[#00878E] flex items-center justify-center">
          <span class="text-lg font-bold text-white">{{ user?.name?.[0]?.toUpperCase() }}</span>
        </div>
        <div class="min-w-0">
          <p class="font-semibold text-gray-900 truncate">{{ user?.name }}</p>
          <p class="text-sm text-gray-400 truncate">{{ user?.email }}</p>
        </div>
      </div>
    </div>

    <!-- Navigation -->
    <nav class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
      <NuxtLink
        v-for="item in navItems"
        :key="item.to"
        :to="item.to"
        class="flex items-center gap-3 px-4 py-3.5 text-sm text-gray-600 hover:bg-gray-50 hover:text-gray-900 transition-all border-b border-gray-50 last:border-0"
        active-class="bg-[#00878E]/8 text-[#006b70] font-medium"
      >
        <Icon :name="item.icon" class="w-4 h-4 shrink-0" />
        <span>{{ item.label }}</span>
        <span v-if="item.badge" class="ml-auto text-xs font-semibold bg-[#00878E]/10 text-[#006b70] px-2 py-0.5 rounded-full">
          {{ item.badge }}
        </span>
      </NuxtLink>
    </nav>

    <!-- Logout -->
    <button
      @click="logout"
      class="mt-4 w-full flex items-center gap-3 px-4 py-3 text-sm text-gray-500 hover:text-red-600 hover:bg-red-50 rounded-2xl border border-gray-100 transition-all"
    >
      <Icon name="lucide:log-out" class="w-4 h-4" />
      <span>Se déconnecter</span>
    </button>
  </aside>
</template>

<script setup lang="ts">
const { user, logout } = useAuth()
const favorites = useFavoritesStore()

const navItems = computed(() => [
  {
    to: '/account/favorites',
    icon: 'lucide:heart',
    label: 'Mes favoris',
    badge: favorites.count > 0 ? favorites.count : undefined,
  },
  { to: '/account/password', icon: 'lucide:lock', label: 'Changer le mot de passe' },
  { to: '/account/social', icon: 'lucide:share-2', label: 'Réseaux sociaux' },
  { to: '/account/settings', icon: 'lucide:settings', label: 'Paramètres' },
])
</script>
