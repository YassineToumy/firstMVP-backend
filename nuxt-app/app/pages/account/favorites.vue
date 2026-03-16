<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="flex flex-col md:flex-row gap-8">
      <LayoutAccountSidebar />

      <main class="flex-1 min-w-0">
        <div class="mb-6">
          <h1 class="text-2xl font-bold text-gray-900">Mes favoris</h1>
          <p class="text-sm text-gray-500 mt-1">
            {{ favorites.count }} annonce{{ favorites.count !== 1 ? 's' : '' }} sauvegardée{{ favorites.count !== 1 ? 's' : '' }}
          </p>
        </div>

        <!-- Empty state -->
        <div v-if="favorites.count === 0" class="bg-white rounded-2xl border border-gray-100 p-16 text-center">
          <Icon name="lucide:heart" class="w-12 h-12 text-gray-200 mx-auto mb-4" />
          <h3 class="text-base font-semibold text-gray-700 mb-2">Aucun favori pour le moment</h3>
          <p class="text-sm text-gray-400 mb-6">Parcourez les annonces et cliquez sur le coeur pour les sauvegarder.</p>
          <NuxtLink
            to="/listings"
            class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#00878E] text-white text-sm font-medium rounded-xl hover:bg-[#006b70] transition-colors"
          >
            <Icon name="lucide:search" class="w-4 h-4" />
            Parcourir les annonces
          </NuxtLink>
        </div>

        <!-- Listings grid -->
        <div v-else class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">
          <div v-for="listing in favorites.listings" :key="`${listing.source}-${listing.source_id}`" class="relative">
            <ListingsListingCard :listing="listing" />
            <button
              @click="favorites.toggle(listing)"
              class="absolute top-3 right-3 z-10 w-8 h-8 rounded-full bg-white/90 backdrop-blur flex items-center justify-center hover:bg-white shadow-sm transition-all"
              title="Retirer des favoris"
            >
              <Icon name="lucide:heart" class="w-4 h-4 text-[#00878E] fill-[#00878E]" />
            </button>
          </div>
        </div>
      </main>
    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({ middleware: 'auth' })
const favorites = useFavoritesStore()
</script>
