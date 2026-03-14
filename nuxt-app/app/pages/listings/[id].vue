<template>
  <div class="min-h-screen bg-[#FAFAFA]">
    <!-- Loading -->
    <div v-if="loading" class="max-w-7xl mx-auto px-4 py-12">
      <div class="bg-white rounded-2xl h-[400px] animate-pulse" />
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
        <div class="lg:col-span-2 bg-white rounded-2xl h-64 animate-pulse" />
        <div class="bg-white rounded-2xl h-64 animate-pulse" />
      </div>
    </div>

    <!-- Content -->
    <div v-else-if="l" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

      <!-- Breadcrumb -->
      <nav class="flex items-center gap-2 text-xs text-gray-400 mb-4">
        <NuxtLink to="/listings" class="hover:text-gray-600 transition-colors">{{ lbl('listings') }}</NuxtLink>
        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
        <span class="text-gray-600">{{ l.location || lbl('details') }}</span>
      </nav>

      <!-- Image gallery -->
      <div class="bg-white rounded-2xl overflow-hidden shadow-sm">
        <div class="relative aspect-[16/9] sm:aspect-[21/9] bg-gray-50 overflow-hidden group">
          <Transition name="image-fade" mode="out-in">
            <img
              v-if="mainImage"
              :key="currentImage"
              :src="mainImage"
              :alt="l.title"
              class="w-full h-full object-cover"
            />
            <div v-else class="w-full h-full flex items-center justify-center text-gray-200">
              <span class="text-sm">No image</span>
            </div>
          </Transition>

          <template v-if="imageList.length > 1">
            <button
              @click="currentImage = (currentImage - 1 + imageList.length) % imageList.length"
              class="absolute left-3 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-black/40 backdrop-blur flex items-center justify-center text-white opacity-0 group-hover:opacity-100 transition-opacity hover:bg-black/60"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
              </svg>
            </button>
            <button
              @click="currentImage = (currentImage + 1) % imageList.length"
              class="absolute right-3 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-black/40 backdrop-blur flex items-center justify-center text-white opacity-0 group-hover:opacity-100 transition-opacity hover:bg-black/60"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
              </svg>
            </button>
            <div class="absolute bottom-3 right-3 px-3 py-1 rounded-full bg-black/50 backdrop-blur text-white text-xs">
              {{ currentImage + 1 }} / {{ imageList.length }}
            </div>
          </template>
        </div>

        <!-- Thumbnails -->
        <div v-if="imageList.length > 1" class="flex gap-1.5 p-3 overflow-x-auto scrollbar-hide">
          <button
            v-for="(img, i) in imageList.slice(0, 12)"
            :key="i"
            @click="currentImage = Number(i)"
            class="flex-shrink-0 w-20 h-14 rounded-lg overflow-hidden border-2 transition-all duration-200"
            :class="currentImage === i ? 'border-[#0a0a0a] ring-1 ring-[#0a0a0a]' : 'border-transparent opacity-60 hover:opacity-100'"
          >
            <img :src="img" class="w-full h-full object-cover" />
          </button>
        </div>
      </div>

      <!-- Two columns -->
      <div class="mt-6 grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Left column -->
        <div class="lg:col-span-2 space-y-6">

          <!-- Title & price card -->
          <div class="bg-white rounded-2xl shadow-sm p-6">
            <div class="flex flex-wrap gap-2 mb-4">
              <span v-if="l.property_type" class="tag">{{ l.property_type }}</span>
              <span v-if="l.property_typology" class="tag">{{ l.property_typology }}</span>
              <span v-if="l.other_features?.is_furnished === true" class="tag tag-green">{{ lbl('furnished') }}</span>
              <span v-if="l.other_features?.is_furnished === false" class="tag">{{ lbl('unfurnished') }}</span>
              <span v-if="l.other_features?.is_new" class="tag tag-violet">{{ lbl('new_build') }}</span>
            </div>

            <h1 class="text-2xl font-bold text-[#0a0a0a] leading-snug">{{ l.title }}</h1>

            <p class="text-sm text-gray-500 mt-2 flex items-center gap-1.5">
              <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
              {{ l.location }}
            </p>

            <!-- Price -->
            <div class="mt-5 pt-5 border-t border-gray-100">
              <p class="text-3xl font-bold text-[#0a0a0a]">
                {{ formatPrice(l.price, l.currency) }}
                <span class="text-base font-normal text-gray-400">
                  {{ l.property_typology === 'rent' ? lbl('per_month') : '' }}
                </span>
              </p>
            </div>

            <!-- Key stats -->
            <div class="flex flex-wrap gap-4 mt-5">
              <div v-if="l.bedrooms" class="stat-box">
                <Icon name="lucide:bed" class="w-5 h-5 text-orange-400 mb-1" />
                <span class="stat-val">{{ l.bedrooms }}</span>
                <span class="stat-label">{{ lbl('bedrooms') }}</span>
              </div>
              <div v-if="l.bathrooms" class="stat-box">
                <Icon name="lucide:bath" class="w-5 h-5 text-orange-400 mb-1" />
                <span class="stat-val">{{ l.bathrooms }}</span>
                <span class="stat-label">{{ lbl('bathrooms') }}</span>
              </div>
              <div v-if="l.interior_features?.rooms" class="stat-box">
                <Icon name="lucide:layout-grid" class="w-5 h-5 text-orange-400 mb-1" />
                <span class="stat-val">{{ l.interior_features.rooms }}</span>
                <span class="stat-label">{{ lbl('rooms') }}</span>
              </div>
              <div v-if="l.interior_features?.surface_m2" class="stat-box">
                <Icon name="lucide:ruler" class="w-5 h-5 text-orange-400 mb-1" />
                <span class="stat-val">{{ l.interior_features.surface_m2 }}</span>
                <span class="stat-label">{{ lbl('surface') }}</span>
              </div>
              <div v-if="l.interior_features?.floor" class="stat-box">
                <Icon name="lucide:layers" class="w-5 h-5 text-orange-400 mb-1" />
                <span class="stat-val">{{ l.interior_features.floor }}</span>
                <span class="stat-label">{{ lbl('floor') }}</span>
              </div>
            </div>
          </div>

          <!-- Description -->
          <div v-if="l.description" class="bg-white rounded-2xl shadow-sm p-6">
            <h2 class="text-base font-semibold text-[#0a0a0a] mb-3">{{ lbl('description') }}</h2>
            <p class="text-sm text-gray-600 leading-relaxed whitespace-pre-line">{{ l.description }}</p>
          </div>
        </div>

        <!-- Right sidebar -->
        <div class="space-y-6">

          <!-- Contact card -->
          <div class="bg-white rounded-2xl shadow-sm p-6">
            <h3 class="text-sm font-semibold text-[#0a0a0a] mb-4">{{ lbl('contact') }}</h3>
            <div class="space-y-2">
              <p v-if="l.seller_name" class="text-sm text-gray-700 font-medium">{{ l.seller_name }}</p>
              <p v-if="l.seller_phone" class="text-xs text-gray-500">{{ l.seller_phone }}</p>
            </div>

            <a
              v-if="l.url"
              :href="l.url"
              target="_blank"
              class="mt-5 w-full py-3 bg-orange-500 text-white text-sm font-semibold rounded-xl hover:bg-orange-600 active:scale-[0.98] transition-all flex items-center justify-center gap-2"
            >
              {{ lbl('view_listing') }}
              <Icon name="lucide:external-link" class="w-4 h-4" />
            </a>

            <button
              v-if="isLoggedIn"
              @click="toggleFav"
              :class="isFav ? 'border-orange-400 text-orange-500 bg-orange-50' : 'border-gray-200 text-gray-500 hover:border-orange-300 hover:bg-orange-50 hover:text-orange-500'"
              class="mt-3 w-full py-2.5 border rounded-xl text-sm font-medium flex items-center justify-center gap-2 transition-all"
            >
              <svg class="w-4 h-4" :class="isFav ? 'fill-orange-500 text-orange-500' : 'fill-none'" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
              </svg>
              {{ isFav ? lbl('saved_favorites') : lbl('save_favorites') }}
            </button>
          </div>

          <!-- Property details -->
          <div v-if="Object.keys(details).length" class="bg-white rounded-2xl shadow-sm p-6">
            <h3 class="text-sm font-semibold text-[#0a0a0a] mb-4">{{ lbl('property_details') }}</h3>
            <dl class="space-y-3">
              <div v-for="(val, key) in details" :key="key" class="flex justify-between text-sm">
                <dt class="text-gray-500">{{ key }}</dt>
                <dd class="text-gray-800 font-medium text-right max-w-[55%]">{{ val }}</dd>
              </div>
            </dl>
          </div>

          <!-- Energy performance -->
          <div v-if="l.other_features?.energy_class || l.other_features?.ghg_class" class="bg-white rounded-2xl shadow-sm p-6">
            <h3 class="text-sm font-semibold text-[#0a0a0a] mb-4">{{ lbl('energy_performance') }}</h3>
            <dl class="space-y-3 text-sm">
              <div v-if="l.other_features.energy_class" class="flex justify-between items-center">
                <dt class="text-gray-500">{{ lbl('energy_class') }}</dt>
                <dd class="text-lg font-bold px-3 py-0.5 rounded bg-green-50 text-green-700">{{ l.other_features.energy_class }}</dd>
              </div>
              <div v-if="l.other_features.energy_value" class="flex justify-between">
                <dt class="text-gray-500">{{ lbl('energy_value') }}</dt>
                <dd class="text-gray-800">{{ l.other_features.energy_value }} kWh/m²/year</dd>
              </div>
              <div v-if="l.other_features.ghg_class" class="flex justify-between items-center">
                <dt class="text-gray-500">{{ lbl('ghg_class') }}</dt>
                <dd class="text-lg font-bold px-3 py-0.5 rounded bg-orange-50 text-orange-600">{{ l.other_features.ghg_class }}</dd>
              </div>
            </dl>
          </div>
        </div>
      </div>
    </div>

    <!-- Not found -->
    <div v-else class="max-w-7xl mx-auto px-4 py-24 text-center">
      <p class="text-gray-500">Listing not found.</p>
      <NuxtLink to="/listings" class="mt-3 inline-block text-sm font-medium text-orange-500 hover:text-orange-600 transition-colors">
        ← Back to listings
      </NuxtLink>
    </div>
  </div>
</template>

<script setup lang="ts">
import type { Listing } from '~/composables/useListings'
import { useFavoritesStore } from '~/stores/favorites'
import { useLabels } from '~/composables/useLabels'

const route = useRoute()
const { fetchListing } = useListings()
const { isLoggedIn } = useAuth()
const favorites = useFavoritesStore()
const { l: lbl } = useLabels()

const id = Number(route.params.id)
const currentImage = ref(0)
const l = ref<Listing | null>(null)
const loading = ref(true)

onMounted(async () => {
  try {
    const res = await fetchListing(id)
    l.value = res.data
  } catch (e) {
    console.error('Failed to load listing:', e)
  } finally {
    loading.value = false
  }
})

const isFav = computed(() => l.value ? favorites.isFavorite(l.value.id) : false)

function toggleFav() {
  if (l.value) favorites.toggle(l.value)
}

const imageList = computed(() => l.value?.photos ?? [])
const mainImage = computed(() => imageList.value[currentImage.value] ?? null)

const details = computed(() => {
  if (!l.value) return {}
  const d: Record<string, any> = {}
  const interior = l.value.interior_features
  const exterior = l.value.exterior_features
  const extra = l.value.extra_data

  if (interior?.rooms) d[lbl('rooms')] = interior.rooms
  if (interior?.surface_m2) d[lbl('surface')] = `${interior.surface_m2} m²`
  if (interior?.floor !== undefined) d[lbl('floor')] = interior.floor
  if (interior?.heating) d[lbl('heating')] = interior.heating
  if (exterior?.has_elevator !== undefined) d[lbl('elevator')] = exterior.has_elevator ? lbl('yes') : lbl('no')
  if (exterior?.balconies) d[lbl('balconies')] = exterior.balconies
  if (exterior?.terraces) d[lbl('terraces')] = exterior.terraces
  if (exterior?.parking_spots) d[lbl('parking')] = exterior.parking_spots
  if (l.value.price_per_m2) d[lbl('price_per_m2')] = `${l.value.price_per_m2} ${l.value.currency}`
  if (extra?.charges) d[lbl('charges')] = `${extra.charges} ${l.value.currency}`
  if (extra?.agency_fee) d[lbl('agency_fee')] = `${extra.agency_fee} ${l.value.currency}`
  if (extra?.reference) d[lbl('reference')] = extra.reference
  if (extra?.postal_code) d[lbl('postal_code')] = extra.postal_code

  return d
})

function formatPrice(price: number, currency: string): string {
  const locales: Record<string, string> = { EUR: 'fr-FR', TND: 'fr-TN', EGP: 'en-EG', CAD: 'en-CA' }
  try {
    return new Intl.NumberFormat(locales[currency] || 'en-US', {
      style: 'currency', currency, maximumFractionDigits: 0,
    }).format(price)
  } catch {
    return `${price?.toLocaleString()} ${currency}`
  }
}

useHead({ title: computed(() => l.value ? `${l.value.title || 'Listing'} | RentGlobe` : 'Listing | RentGlobe') })
</script>

<style scoped>
@reference "tailwindcss";
.tag { @apply bg-gray-100 text-gray-600 text-[11px] font-medium px-2.5 py-1 rounded-lg; }
.tag-green { @apply bg-green-50 text-green-700; }
.tag-violet { @apply bg-orange-50 text-orange-600; }
.stat-box { @apply flex flex-col items-center px-5 py-3 bg-[#FAFAFA] rounded-xl; }
.stat-val { @apply text-lg font-bold text-[#0a0a0a]; }
.stat-label { @apply text-[11px] text-gray-500 mt-0.5; }
.image-fade-enter-active, .image-fade-leave-active { transition: opacity 0.25s ease; }
.image-fade-enter-from, .image-fade-leave-to { opacity: 0; }
.scrollbar-hide::-webkit-scrollbar { display: none; }
</style>
