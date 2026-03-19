<template>
  <NuxtLink :to="`/listings/${listing.id}`" class="block">
    <div class="group relative bg-white rounded-2xl overflow-hidden shadow-md hover:shadow-xl transition-all duration-300 cursor-pointer">
      <!-- Image -->
      <div class="relative h-56 overflow-hidden">
        <img
          v-if="thumbnail"
          :src="thumbnail"
          :alt="listing.title"
          class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
          loading="lazy"
        />
        <div v-else class="w-full h-full bg-gray-100 flex items-center justify-center text-gray-300">
          <Icon name="lucide:image" class="w-12 h-12" />
        </div>

        <!-- Property type badge -->
        <span v-if="listing.property_type" class="absolute top-4 left-4 text-[11px] font-medium px-2.5 py-1 rounded-full bg-white/80 backdrop-blur-sm text-gray-700 border border-white/30">
          {{ listing.property_type }}
        </span>

        <!-- Favorite button -->
        <button
          v-if="isLoggedIn"
          @click.prevent="toggleFav"
          class="absolute top-4 right-4 w-10 h-10 rounded-full flex items-center justify-center transition-all duration-300 shadow-md z-10"
          :class="isFav ? 'bg-[#00878E] text-white' : 'bg-white text-gray-600 hover:bg-[#00878E] hover:text-white'"
        >
          <Icon name="lucide:heart" class="w-5 h-5" :class="isFav ? 'fill-white' : ''" />
        </button>
      </div>

      <!-- Content -->
      <div class="p-4">
        <!-- Price -->
        <div class="mb-2">
          <span class="font-bold text-[20px] text-[#00878E]">
            {{ formatPrice(listing.price, listing.currency) }}
          </span>
          <span v-if="listing.property_typology === 'rent'" class="text-sm text-gray-500 ml-1">/ {{ l('per_month') }}</span>
        </div>

        <!-- Title -->
        <h3 class="font-semibold text-[#313131] mb-1 line-clamp-1 text-[15px]">{{ listing.title }}</h3>

        <!-- Location -->
        <div class="flex items-center gap-1 mb-3 text-gray-600">
          <Icon name="lucide:map-pin" class="w-4 h-4 shrink-0" />
          <span class="text-[13px] truncate">{{ listing.location }}</span>
        </div>

        <!-- Features row -->
        <div class="flex items-center gap-4 pt-3 border-t border-gray-100">
          <div v-if="bedroomsDisplay" class="flex items-center gap-1">
            <Icon name="lucide:bed" class="w-4 h-4 text-gray-500" />
            <span class="text-sm text-gray-700">{{ bedroomsDisplay }}</span>
          </div>
          <div v-if="bathroomsDisplay" class="flex items-center gap-1">
            <Icon name="lucide:bath" class="w-4 h-4 text-gray-500" />
            <span class="text-sm text-gray-700">{{ bathroomsDisplay }}</span>
          </div>
          <div v-if="surface" class="flex items-center gap-1">
            <Icon name="lucide:maximize" class="w-4 h-4 text-gray-500" />
            <span class="text-sm text-gray-700">{{ surface }}m²</span>
          </div>
        </div>

        <!-- Agency -->
        <div v-if="listing.seller_name" class="mt-3">
          <span class="text-xs text-gray-400">{{ listing.seller_name }}</span>
        </div>
      </div>
    </div>
  </NuxtLink>
</template>

<script setup lang="ts">
import type { Listing } from '~/composables/useListings'
import { useFavoritesStore } from '~/stores/favorites'
import { useLabels } from '~/composables/useLabels'

const props = defineProps<{ listing: Listing }>()
const { isLoggedIn } = useAuth()
const favorites = useFavoritesStore()
const { l } = useLabels()

const thumbnail = computed(() => props.listing.photos?.[0])

const surface = computed(() => {
  const f = props.listing.interior_features as any
  return f?.surface_m2 ?? f?.surface ?? f?.area ?? f?.living_area ?? f?.superficie ?? null
})

const bedroomsDisplay = computed(() => {
  const f = props.listing.interior_features as any
  return props.listing.bedrooms ?? f?.bedrooms ?? f?.chambres ?? null
})

const bathroomsDisplay = computed(() => {
  const f = props.listing.interior_features as any
  return props.listing.bathrooms ?? f?.bathrooms ?? f?.salle_bains ?? null
})

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
