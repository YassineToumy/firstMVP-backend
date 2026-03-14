<template>
  <NuxtLink
    :to="`/listings/${listing.id}`"
    class="block bg-white rounded-2xl border border-gray-100 overflow-hidden hover:shadow-lg hover:-translate-y-1 transition-all duration-300 group"
  >
    <!-- Thumbnail -->
    <div class="relative aspect-[4/3] bg-gray-50 overflow-hidden">
      <img
        v-if="thumbnail"
        :src="thumbnail"
        :alt="listing.title"
        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 ease-out"
        loading="lazy"
      />
      <div v-else class="w-full h-full flex items-center justify-center text-gray-200">
        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3.75 21h16.5A2.25 2.25 0 0022.5 18.75V5.25A2.25 2.25 0 0020.25 3H3.75A2.25 2.25 0 001.5 5.25v13.5A2.25 2.25 0 003.75 21z" />
        </svg>
      </div>

      <!-- Top badges -->
      <div class="absolute top-3 left-3 flex items-center gap-1.5">
        <span v-if="listing.property_type" class="badge-glass">
          {{ listing.property_type }}
        </span>
        <span v-if="listing.other_features?.is_furnished" class="badge-glass bg-orange-500/80 text-white border-orange-400/30">
          Furnished
        </span>
      </div>

      <!-- Photos count -->
      <span v-if="photosCount" class="absolute bottom-3 right-3 badge-glass flex items-center gap-1">
        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
        </svg>
        {{ photosCount }}
      </span>

      <!-- Favourite button -->
      <button
        v-if="isLoggedIn"
        class="absolute top-3 right-3 w-8 h-8 rounded-full bg-white/80 backdrop-blur flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-200 hover:bg-white shadow-sm"
        :class="{ 'opacity-100': isFav }"
        @click.prevent="toggleFav"
        :title="isFav ? 'Retirer des favoris' : 'Ajouter aux favoris'"
      >
        <svg
          class="w-4 h-4 transition-colors"
          :class="isFav ? 'text-orange-500 fill-orange-500' : 'text-gray-500 fill-none'"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
        </svg>
      </button>
    </div>

    <!-- Content -->
    <div class="p-4">
      <!-- Price -->
      <p class="text-lg font-bold text-gray-900">
        {{ formatPrice(listing.price, listing.currency) }}
        <span class="text-xs font-normal text-gray-400">{{ listing.property_typology === 'rent' ? '/month' : '' }}</span>
      </p>

      <!-- Title -->
      <p class="text-sm font-medium text-gray-700 mt-1 truncate">{{ listing.title }}</p>

      <!-- Location -->
      <p class="text-sm text-gray-500 mt-1 truncate flex items-center gap-1">
        <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
        </svg>
        {{ listing.location }}
      </p>

      <!-- Divider -->
      <div class="my-3 border-t border-gray-100" />

      <!-- Stats row -->
      <div class="flex items-center gap-4 text-xs text-gray-500">
        <span v-if="listing.bedrooms" class="flex items-center gap-1.5">
          <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 7v4a1 1 0 001 1h3m10 0h3a1 1 0 001-1V7M3 7V5a2 2 0 012-2h14a2 2 0 012 2v2M3 7h18M7 12v5m0 0H5a1 1 0 01-1-1v-1h3zm0 0h10m0 0v2a1 1 0 001 1h1v-3h-2z" />
          </svg>
          {{ listing.bedrooms }} Beds
        </span>
        <span v-if="listing.bathrooms" class="flex items-center gap-1.5">
          <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M4 16h16M4 16V8a4 4 0 014-4h1" />
          </svg>
          {{ listing.bathrooms }} Baths
        </span>
        <span v-if="surface" class="flex items-center gap-1.5">
          <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
          </svg>
          {{ surface }} m²
        </span>
      </div>

      <!-- Agency -->
      <p v-if="listing.seller_name" class="text-[11px] text-gray-400 mt-3 truncate">
        {{ listing.seller_name }}
      </p>
    </div>
  </NuxtLink>
</template>

<script setup lang="ts">
import type { Listing } from '~/composables/useListings'
import { useFavoritesStore } from '~/stores/favorites'

const props = defineProps<{ listing: Listing }>()
const { isLoggedIn } = useAuth()
const favorites = useFavoritesStore()

const thumbnail = computed(() => props.listing.photos?.[0])
const photosCount = computed(() => props.listing.photos?.length)
const surface = computed(() => props.listing.interior_features?.surface_m2)
const isFav = computed(() => favorites.isFavorite(props.listing.id))

function toggleFav() {
  favorites.toggle(props.listing)
}

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
</script>

<style scoped>
@reference "tailwindcss";
.badge-glass {
  @apply text-[11px] font-medium px-2.5 py-1 rounded-full bg-white/80 backdrop-blur-sm text-gray-700 border border-white/30;
}
</style>
