<template>
  <div class="min-h-screen bg-gray-50 pt-[84px]">
    <!-- Filters bar -->
    <ListingsListingFilters v-model:filters="filters" @filter="loadListings(1)" />

    <!-- Header -->
    <div class="bg-white border-b border-gray-200 py-6 px-8">
      <div class="max-w-[1440px] mx-auto flex items-center justify-between">
        <div>
          <h1 class="font-bold text-3xl text-[#313131] mb-1">
            Locations à {{ region.current.name }}
          </h1>
          <p v-if="meta" class="text-gray-600 text-sm">
            {{ meta.total.toLocaleString() }} {{ meta.total > 1 ? 'propriétés disponibles' : 'propriété disponible' }}
          </p>
        </div>
      </div>
    </div>

    <div class="max-w-[1440px] mx-auto px-8 py-12">
      <!-- Skeleton -->
      <div v-if="loading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <div v-for="n in 9" :key="n" class="bg-white rounded-2xl overflow-hidden shadow-md">
          <div class="h-56 bg-gray-100 animate-pulse" />
          <div class="p-4 space-y-3">
            <div class="h-5 bg-gray-100 rounded animate-pulse w-1/2" />
            <div class="h-4 bg-gray-100 rounded animate-pulse w-2/3" />
            <div class="h-4 bg-gray-100 rounded animate-pulse w-1/3" />
          </div>
        </div>
      </div>

      <!-- Grid -->
      <div v-else-if="listings.length" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <ListingsListingCard
          v-for="(l, i) in listings"
          :key="l.source_id"
          :listing="l"
          class="animate-fade-in-up"
          :style="{ animationDelay: `${i * 30}ms` }"
        />
      </div>

      <!-- Empty state -->
      <div v-else class="text-center py-24">
        <div class="w-16 h-16 mx-auto rounded-2xl bg-gray-100 flex items-center justify-center mb-4">
          <Icon name="lucide:search" class="w-8 h-8 text-gray-300" />
        </div>
        <p class="text-gray-600 font-semibold">Aucune annonce ne correspond à vos critères</p>
        <p class="text-sm text-gray-400 mt-1">Essayez de modifier vos filtres de recherche</p>
        <button @click="resetAndLoad" class="mt-4 text-sm font-semibold text-[#00878E] hover:text-[#006b70] transition-colors">
          Réinitialiser les filtres
        </button>
      </div>

      <!-- Pagination -->
      <UiPagination
        v-if="meta && meta.last_page > 1"
        :current-page="meta.current_page"
        :last-page="meta.last_page"
        @update:page="loadListings"
      />
    </div>
  </div>
</template>

<script setup lang="ts">
import type { Listing } from '~/composables/useListings'

const region = useRegionStore()
const { fetchListings } = useListings()
const route = useRoute()

const listings = ref<Listing[]>([])
const meta = ref<{ current_page: number; last_page: number; per_page: number; total: number } | null>(null)
const loading = ref(true)

const filters = ref({
  property_type: (route.query.property_type as string) || '',
  min_price: route.query.min_price ? Number(route.query.min_price) : undefined,
  max_price: route.query.max_price ? Number(route.query.max_price) : undefined,
  bedrooms: route.query.bedrooms ? Number(route.query.bedrooms) : undefined,
  furnished: null as boolean | null,
  city: (route.query.city as string) || '',
  sort: '',
  page: 1,
})

async function loadListings(page = 1) {
  loading.value = true
  filters.value.page = page
  try {
    const res = await fetchListings(filters.value)
    listings.value = res.data
    meta.value = res.meta
  } catch (e) {
    console.error('Failed to load listings:', e)
    listings.value = []
  } finally {
    loading.value = false
  }
}

function resetAndLoad() {
  filters.value = { property_type: '', min_price: undefined, max_price: undefined, bedrooms: undefined, furnished: null, city: '', sort: '', page: 1 }
  loadListings(1)
}

watch(() => region.currentCode, () => loadListings(1))
onMounted(() => loadListings())

useHead({ title: computed(() => `Locations — ${region.current.name} | RentGlobe`) })
</script>
