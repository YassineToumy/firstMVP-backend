<template>
  <div class="min-h-screen bg-[#F5F5F7]">

    <!-- ── Lightbox ── -->
    <Teleport to="body">
      <Transition name="lb">
        <div
          v-if="lightbox !== null"
          class="fixed inset-0 z-50 bg-black/95 flex flex-col"
          @keydown.esc="lightbox = null"
          tabindex="0"
          ref="lbEl"
        >
          <!-- top bar -->
          <div class="flex items-center justify-between px-4 py-3 text-white/60 text-sm flex-shrink-0">
            <span>{{ lightbox + 1 }} / {{ imageList.length }}</span>
            <button @click="lightbox = null" class="w-9 h-9 rounded-full hover:bg-white/10 flex items-center justify-center transition-colors">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
          </div>
          <!-- main image -->
          <div class="flex-1 relative flex items-center justify-center min-h-0 px-12">
            <Transition name="image-fade" mode="out-in">
              <img :key="lightbox" :src="imageList[lightbox]" class="max-h-full max-w-full object-contain select-none" />
            </Transition>
            <button v-if="imageList.length > 1" @click="lightbox = (lightbox - 1 + imageList.length) % imageList.length"
              class="absolute left-2 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition-colors">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </button>
            <button v-if="imageList.length > 1" @click="lightbox = (lightbox + 1) % imageList.length"
              class="absolute right-2 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition-colors">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </button>
          </div>
          <!-- thumbnails -->
          <div v-if="imageList.length > 1" class="flex gap-1.5 px-4 py-3 overflow-x-auto scrollbar-hide flex-shrink-0 justify-center">
            <button v-for="(img, i) in imageList" :key="i" @click="lightbox = i"
              class="flex-shrink-0 w-14 h-10 rounded overflow-hidden transition-all"
              :class="lightbox === i ? 'ring-2 ring-orange-400 opacity-100' : 'opacity-40 hover:opacity-70'">
              <img :src="img" class="w-full h-full object-cover" />
            </button>
          </div>
        </div>
      </Transition>
    </Teleport>

    <!-- ── Loading skeleton ── -->
    <div v-if="loading" class="max-w-6xl mx-auto px-4 py-8 space-y-4">
      <div class="bg-white rounded-2xl h-80 animate-pulse" />
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="lg:col-span-2 bg-white rounded-2xl h-56 animate-pulse" />
        <div class="bg-white rounded-2xl h-56 animate-pulse" />
      </div>
    </div>

    <!-- ── Content ── -->
    <div v-else-if="l" class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-5">

      <!-- Breadcrumb -->
      <nav class="flex items-center gap-1.5 text-xs text-gray-400">
        <NuxtLink to="/listings" class="hover:text-orange-500 transition-colors">{{ lbl('listings') }}</NuxtLink>
        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span class="text-gray-600 truncate max-w-xs">{{ l.location || lbl('details') }}</span>
      </nav>

      <!-- ── Photo gallery ── -->
      <div v-if="imageList.length" class="grid gap-1.5 rounded-2xl overflow-hidden"
        :class="imageList.length === 1 ? 'grid-cols-1' : imageList.length === 2 ? 'grid-cols-2' : 'grid-cols-4 grid-rows-2'">
        <!-- Main photo -->
        <div
          class="relative overflow-hidden bg-gray-100 cursor-zoom-in group"
          :class="imageList.length >= 3 ? 'col-span-2 row-span-2' : 'aspect-[16/9]'"
          @click="lightbox = 0"
        >
          <img :src="imageList[0]" :alt="l.title" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
          <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-colors" />
        </div>
        <!-- Side photos -->
        <template v-if="imageList.length >= 3">
          <div
            v-for="(img, i) in imageList.slice(1, 4)"
            :key="i"
            class="relative overflow-hidden bg-gray-100 cursor-zoom-in group"
            @click="lightbox = i + 1"
          >
            <img :src="img" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" style="aspect-ratio:4/3" />
            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-colors" />
            <!-- +N overlay on last visible -->
            <div v-if="i === 2 && imageList.length > 4"
              class="absolute inset-0 bg-black/50 flex items-center justify-center text-white font-semibold text-lg">
              +{{ imageList.length - 4 }}
            </div>
          </div>
        </template>
        <template v-else-if="imageList.length === 2">
          <div class="relative overflow-hidden bg-gray-100 cursor-zoom-in group aspect-[16/9]" @click="lightbox = 1">
            <img :src="imageList[1]" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
          </div>
        </template>
      </div>
      <!-- No photos placeholder -->
      <div v-else class="rounded-2xl bg-white h-64 flex items-center justify-center text-gray-300">
        <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
      </div>

      <!-- ── Main grid ── -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        <!-- Left column -->
        <div class="lg:col-span-2 space-y-5">

          <!-- Title & price -->
          <div class="bg-white rounded-2xl shadow-sm p-6">
            <!-- Badges -->
            <div class="flex flex-wrap gap-2 mb-4">
              <span v-if="l.property_type" class="badge">{{ l.property_type }}</span>
              <span v-if="l.property_typology" class="badge">{{ l.property_typology }}</span>
              <span v-if="l.other_features?.is_furnished === true" class="badge badge-green">{{ lbl('furnished') }}</span>
              <span v-if="l.other_features?.is_furnished === false" class="badge">{{ lbl('unfurnished') }}</span>
              <span v-if="l.other_features?.is_new" class="badge badge-orange">{{ lbl('new_build') }}</span>
              <span v-if="l.other_features?.posted_by_pro" class="badge badge-blue">Pro</span>
            </div>

            <h1 class="text-xl sm:text-2xl font-bold text-gray-900 leading-snug">{{ l.title }}</h1>

            <p class="text-sm text-gray-500 mt-2 flex items-center gap-1.5">
              <svg class="w-4 h-4 text-orange-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
              </svg>
              {{ l.location }}
            </p>

            <div class="mt-5 pt-5 border-t border-gray-100 flex items-end gap-3 flex-wrap">
              <p class="text-3xl font-bold text-gray-900">{{ formatPrice(l.price, l.currency) }}</p>
              <p v-if="l.property_typology === 'rent'" class="text-sm text-gray-400 mb-1">{{ lbl('per_month') }}</p>
              <p v-if="l.price_per_m2" class="text-sm text-gray-400 mb-1 ml-auto">{{ formatPrice(l.price_per_m2, l.currency) }} / m²</p>
            </div>

            <!-- Quick stats -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-5">
              <div v-if="l.bedrooms" class="stat-pill">
                <Icon name="lucide:bed-double" class="w-4 h-4 text-orange-400" />
                <span class="stat-pill-val">{{ l.bedrooms }}</span>
                <span class="stat-pill-label">{{ lbl('bedrooms') }}</span>
              </div>
              <div v-if="l.bathrooms" class="stat-pill">
                <Icon name="lucide:bath" class="w-4 h-4 text-orange-400" />
                <span class="stat-pill-val">{{ l.bathrooms }}</span>
                <span class="stat-pill-label">{{ lbl('bathrooms') }}</span>
              </div>
              <div v-if="l.interior_features?.rooms" class="stat-pill">
                <Icon name="lucide:layout-grid" class="w-4 h-4 text-orange-400" />
                <span class="stat-pill-val">{{ l.interior_features.rooms }}</span>
                <span class="stat-pill-label">{{ lbl('rooms') }}</span>
              </div>
              <div v-if="l.interior_features?.surface_m2" class="stat-pill">
                <Icon name="lucide:move" class="w-4 h-4 text-orange-400" />
                <span class="stat-pill-val">{{ l.interior_features.surface_m2 }}</span>
                <span class="stat-pill-label">m²</span>
              </div>
            </div>
          </div>

          <!-- Interior features -->
          <div v-if="hasInterior" class="bg-white rounded-2xl shadow-sm p-6">
            <h2 class="section-title">
              <Icon name="lucide:sofa" class="w-4 h-4 text-orange-400" />
              Interior
            </h2>
            <div class="grid grid-cols-2 gap-x-6 gap-y-3 mt-4">
              <div v-if="l.interior_features?.surface_m2" class="feat-row">
                <span class="feat-label">{{ lbl('surface') }}</span>
                <span class="feat-val">{{ l.interior_features.surface_m2 }} m²</span>
              </div>
              <div v-if="l.interior_features?.rooms" class="feat-row">
                <span class="feat-label">{{ lbl('rooms') }}</span>
                <span class="feat-val">{{ l.interior_features.rooms }}</span>
              </div>
              <div v-if="l.interior_features?.floor !== undefined && l.interior_features?.floor !== null" class="feat-row">
                <span class="feat-label">{{ lbl('floor') }}</span>
                <span class="feat-val">{{ l.interior_features.floor }}</span>
              </div>
              <div v-if="l.interior_features?.heating" class="feat-row">
                <span class="feat-label">{{ lbl('heating') }}</span>
                <span class="feat-val">{{ l.interior_features.heating }}</span>
              </div>
              <div v-if="l.interior_features?.optical_fiber" class="feat-row">
                <span class="feat-label">Optical fiber</span>
                <span class="feat-val">{{ l.interior_features.optical_fiber }}</span>
              </div>
            </div>
          </div>

          <!-- Exterior features -->
          <div v-if="hasExterior" class="bg-white rounded-2xl shadow-sm p-6">
            <h2 class="section-title">
              <Icon name="lucide:trees" class="w-4 h-4 text-orange-400" />
              Exterior
            </h2>
            <div class="grid grid-cols-2 gap-x-6 gap-y-3 mt-4">
              <div v-if="l.exterior_features?.has_elevator !== undefined" class="feat-row">
                <span class="feat-label">{{ lbl('elevator') }}</span>
                <span class="feat-val" :class="l.exterior_features.has_elevator ? 'text-green-600' : 'text-gray-400'">
                  {{ l.exterior_features.has_elevator ? lbl('yes') : lbl('no') }}
                </span>
              </div>
              <div v-if="l.exterior_features?.balconies" class="feat-row">
                <span class="feat-label">{{ lbl('balconies') }}</span>
                <span class="feat-val">{{ l.exterior_features.balconies }}</span>
              </div>
              <div v-if="l.exterior_features?.terraces" class="feat-row">
                <span class="feat-label">{{ lbl('terraces') }}</span>
                <span class="feat-val">{{ l.exterior_features.terraces }}</span>
              </div>
              <div v-if="l.exterior_features?.parking_spots" class="feat-row">
                <span class="feat-label">{{ lbl('parking') }}</span>
                <span class="feat-val">{{ l.exterior_features.parking_spots }}</span>
              </div>
              <div v-if="l.exterior_features?.cellars" class="feat-row">
                <span class="feat-label">Cellars</span>
                <span class="feat-val">{{ l.exterior_features.cellars }}</span>
              </div>
            </div>
          </div>

          <!-- Other / amenities -->
          <div v-if="hasOther" class="bg-white rounded-2xl shadow-sm p-6">
            <h2 class="section-title">
              <Icon name="lucide:sparkles" class="w-4 h-4 text-orange-400" />
              Amenities
            </h2>
            <div class="flex flex-wrap gap-2 mt-4">
              <span v-if="l.other_features?.is_furnished" class="amenity-chip amenity-green">{{ lbl('furnished') }}</span>
              <span v-if="l.other_features?.is_new" class="amenity-chip amenity-orange">{{ lbl('new_build') }}</span>
              <span v-if="l.other_features?.is_accessible" class="amenity-chip">Accessible</span>
              <span v-if="l.other_features?.posted_by_pro" class="amenity-chip amenity-blue">Pro seller</span>
            </div>
            <!-- Energy -->
            <div v-if="l.other_features?.energy_class || l.other_features?.ghg_class" class="grid grid-cols-2 gap-x-6 gap-y-3 mt-5 pt-5 border-t border-gray-100">
              <div v-if="l.other_features?.energy_class" class="feat-row">
                <span class="feat-label">{{ lbl('energy_class') }}</span>
                <span class="feat-val font-bold px-2 rounded bg-green-50 text-green-700">{{ l.other_features.energy_class }}</span>
              </div>
              <div v-if="l.other_features?.energy_value" class="feat-row">
                <span class="feat-label">{{ lbl('energy_value') }}</span>
                <span class="feat-val">{{ l.other_features.energy_value }} kWh/m²/y</span>
              </div>
              <div v-if="l.other_features?.ghg_class" class="feat-row">
                <span class="feat-label">{{ lbl('ghg_class') }}</span>
                <span class="feat-val font-bold px-2 rounded bg-orange-50 text-orange-600">{{ l.other_features.ghg_class }}</span>
              </div>
              <div v-if="l.other_features?.ghg_value" class="feat-row">
                <span class="feat-label">GHG value</span>
                <span class="feat-val">{{ l.other_features.ghg_value }} kg CO₂/m²/y</span>
              </div>
            </div>
          </div>

          <!-- Description -->
          <div v-if="l.description" class="bg-white rounded-2xl shadow-sm p-6">
            <h2 class="section-title">
              <Icon name="lucide:align-left" class="w-4 h-4 text-orange-400" />
              {{ lbl('description') }}
            </h2>
            <p class="text-sm text-gray-600 leading-relaxed whitespace-pre-line mt-4">{{ l.description }}</p>
          </div>
        </div>

        <!-- Right sidebar -->
        <div class="space-y-4 lg:sticky lg:top-4 lg:self-start">

          <!-- Price summary -->
          <div class="bg-white rounded-2xl shadow-sm p-5">
            <p class="text-2xl font-bold text-gray-900">{{ formatPrice(l.price, l.currency) }}</p>
            <p v-if="l.property_typology === 'rent'" class="text-xs text-gray-400">{{ lbl('per_month') }}</p>

            <div v-if="extraDetails.length" class="mt-4 space-y-2 pt-4 border-t border-gray-100">
              <div v-for="item in extraDetails" :key="item.label" class="flex justify-between text-sm">
                <span class="text-gray-500">{{ item.label }}</span>
                <span class="text-gray-800 font-medium">{{ item.value }}</span>
              </div>
            </div>

            <a v-if="l.url" :href="l.url" target="_blank"
              class="mt-5 w-full py-3 bg-orange-500 text-white text-sm font-semibold rounded-xl hover:bg-orange-600 active:scale-[0.98] transition-all flex items-center justify-center gap-2">
              {{ lbl('view_listing') }}
              <Icon name="lucide:external-link" class="w-4 h-4" />
            </a>

            <button v-if="isLoggedIn" @click="toggleFav"
              :class="isFav ? 'border-orange-400 text-orange-500 bg-orange-50' : 'border-gray-200 text-gray-500 hover:border-orange-300 hover:bg-orange-50 hover:text-orange-500'"
              class="mt-2.5 w-full py-2.5 border rounded-xl text-sm font-medium flex items-center justify-center gap-2 transition-all">
              <svg class="w-4 h-4" :class="isFav ? 'fill-orange-500 text-orange-500' : 'fill-none'" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
              </svg>
              {{ isFav ? lbl('saved_favorites') : lbl('save_favorites') }}
            </button>
          </div>

          <!-- Contact -->
          <div v-if="l.seller_name || l.seller_phone" class="bg-white rounded-2xl shadow-sm p-5">
            <h3 class="section-title mb-3">
              <Icon name="lucide:user" class="w-4 h-4 text-orange-400" />
              {{ lbl('contact') }}
            </h3>
            <p v-if="l.seller_name" class="text-sm font-semibold text-gray-800">{{ l.seller_name }}</p>
            <p v-if="l.seller_phone" class="text-sm text-gray-500 mt-1 flex items-center gap-1.5">
              <Icon name="lucide:phone" class="w-3.5 h-3.5" />
              {{ l.seller_phone }}
            </p>
          </div>

          <!-- Reference / metadata -->
          <div v-if="metaDetails.length" class="bg-white rounded-2xl shadow-sm p-5">
            <h3 class="section-title mb-3">
              <Icon name="lucide:info" class="w-4 h-4 text-orange-400" />
              Info
            </h3>
            <div class="space-y-2">
              <div v-for="item in metaDetails" :key="item.label" class="flex justify-between text-sm">
                <span class="text-gray-500">{{ item.label }}</span>
                <span class="text-gray-700 font-medium text-right max-w-[55%] truncate">{{ item.value }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Not found -->
    <div v-else class="max-w-6xl mx-auto px-4 py-24 text-center">
      <p class="text-gray-400 text-lg mb-3">Listing not found.</p>
      <NuxtLink to="/listings" class="inline-block text-sm font-medium text-orange-500 hover:text-orange-600 transition-colors">
        ← {{ lbl('listings') }}
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
const l = ref<Listing | null>(null)
const loading = ref(true)
const lightbox = ref<number | null>(null)
const lbEl = ref<HTMLElement | null>(null)

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

// Focus lightbox container so keyboard events work
watch(lightbox, (v) => {
  if (v !== null) nextTick(() => lbEl.value?.focus())
})

const isFav = computed(() => l.value ? favorites.isFavorite(l.value.id) : false)
function toggleFav() { if (l.value) favorites.toggle(l.value) }

const imageList = computed(() => l.value?.photos ?? [])

const hasInterior = computed(() => {
  const f = l.value?.interior_features
  return f && (f.surface_m2 || f.rooms || f.floor !== undefined || f.heating || f.optical_fiber)
})
const hasExterior = computed(() => {
  const f = l.value?.exterior_features
  return f && (f.has_elevator !== undefined || f.balconies || f.terraces || f.parking_spots || f.cellars)
})
const hasOther = computed(() => {
  const f = l.value?.other_features
  return f && (f.is_furnished !== undefined || f.is_new || f.is_accessible || f.posted_by_pro || f.energy_class || f.ghg_class)
})

const extraDetails = computed(() => {
  if (!l.value) return []
  const items: { label: string; value: string }[] = []
  const extra = l.value.extra_data
  if (extra?.charges) items.push({ label: lbl('charges'), value: `${extra.charges} ${l.value.currency}` })
  if (extra?.agency_fee) items.push({ label: lbl('agency_fee'), value: `${extra.agency_fee} ${l.value.currency}` })
  return items
})

const metaDetails = computed(() => {
  if (!l.value) return []
  const items: { label: string; value: string }[] = []
  const extra = l.value.extra_data
  if (extra?.reference) items.push({ label: lbl('reference'), value: extra.reference })
  if (extra?.postal_code) items.push({ label: lbl('postal_code'), value: extra.postal_code })
  if (l.value.source) items.push({ label: 'Source', value: l.value.source })
  return items
})

function formatPrice(price: number, currency: string): string {
  const locales: Record<string, string> = { EUR: 'fr-FR', TND: 'fr-TN', EGP: 'en-EG', CAD: 'en-CA' }
  try {
    return new Intl.NumberFormat(locales[currency] || 'en-US', { style: 'currency', currency, maximumFractionDigits: 0 }).format(price)
  } catch {
    return `${price?.toLocaleString()} ${currency}`
  }
}

useHead({ title: computed(() => l.value ? `${l.value.title || 'Listing'} | RentGlobe` : 'Listing | RentGlobe') })
</script>

<style scoped>
@reference "tailwindcss";

/* badges */
.badge       { @apply text-[11px] font-medium px-2.5 py-1 rounded-lg bg-gray-100 text-gray-600; }
.badge-green { @apply bg-green-50 text-green-700; }
.badge-orange{ @apply bg-orange-50 text-orange-600; }
.badge-blue  { @apply bg-blue-50 text-blue-600; }

/* stat pills */
.stat-pill       { @apply flex flex-col items-center gap-0.5 px-4 py-3 rounded-xl bg-gray-50 border border-gray-100; }
.stat-pill-val   { @apply text-base font-bold text-gray-900; }
.stat-pill-label { @apply text-[10px] text-gray-400 uppercase tracking-wide; }

/* section title */
.section-title { @apply flex items-center gap-2 text-sm font-semibold text-gray-800; }

/* feature rows */
.feat-row   { @apply flex items-center justify-between py-1.5 border-b border-gray-50 last:border-0; }
.feat-label { @apply text-sm text-gray-500; }
.feat-val   { @apply text-sm text-gray-800 font-medium; }

/* amenity chips */
.amenity-chip        { @apply text-xs font-medium px-3 py-1.5 rounded-full bg-gray-100 text-gray-600; }
.amenity-chip.amenity-green  { @apply bg-green-50 text-green-700; }
.amenity-chip.amenity-orange { @apply bg-orange-50 text-orange-600; }
.amenity-chip.amenity-blue   { @apply bg-blue-50 text-blue-600; }

/* transitions */
.image-fade-enter-active, .image-fade-leave-active { transition: opacity 0.2s ease; }
.image-fade-enter-from, .image-fade-leave-to       { opacity: 0; }
.lb-enter-active, .lb-leave-active { transition: opacity 0.2s ease; }
.lb-enter-from, .lb-leave-to       { opacity: 0; }

.scrollbar-hide::-webkit-scrollbar { display: none; }
</style>
