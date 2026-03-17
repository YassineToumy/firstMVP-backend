<template>
  <div class="min-h-screen bg-gray-50 pt-[84px]">

    <!-- ── Gallery Lightbox ── -->
    <Teleport to="body">
      <Transition name="lb">
        <div
          v-if="lightbox !== null"
          class="fixed inset-0 z-[200] bg-black/95 flex items-center justify-center"
          @keydown.esc="lightbox = null"
          tabindex="0"
          ref="lbEl"
        >
          <!-- Close button -->
          <button @click="lightbox = null"
            class="absolute top-6 right-6 w-12 h-12 bg-white/10 hover:bg-white/20 rounded-full flex items-center justify-center transition-colors duration-300">
            <Icon name="lucide:x" class="w-6 h-6 text-white" />
          </button>

          <!-- Counter -->
          <div class="absolute top-6 left-6 px-4 py-2 bg-black/50 rounded-full">
            <span class="text-white text-sm">{{ lightbox + 1 }} / {{ imageList.length }}</span>
          </div>

          <!-- Prev -->
          <button v-if="imageList.length > 1" @click="lightbox = (lightbox - 1 + imageList.length) % imageList.length"
            class="absolute left-6 top-1/2 -translate-y-1/2 w-12 h-12 bg-white/10 hover:bg-white/20 rounded-full flex items-center justify-center transition-colors duration-300">
            <Icon name="lucide:chevron-left" class="w-6 h-6 text-white" />
          </button>

          <!-- Main image -->
          <div class="max-w-6xl max-h-[80vh] w-full px-20">
            <Transition name="image-fade" mode="out-in">
              <img :key="lightbox" :src="imageList[lightbox]" :alt="l?.title"
                class="w-full h-full object-contain select-none" />
            </Transition>
          </div>

          <!-- Next -->
          <button v-if="imageList.length > 1" @click="lightbox = (lightbox + 1) % imageList.length"
            class="absolute right-6 top-1/2 -translate-y-1/2 w-12 h-12 bg-white/10 hover:bg-white/20 rounded-full flex items-center justify-center transition-colors duration-300">
            <Icon name="lucide:chevron-right" class="w-6 h-6 text-white" />
          </button>

          <!-- Thumbnails -->
          <div v-if="imageList.length > 1" class="absolute bottom-6 left-1/2 -translate-x-1/2 flex gap-2 max-w-4xl overflow-x-auto px-4">
            <button v-for="(img, i) in imageList" :key="i" @click="lightbox = i"
              class="flex-shrink-0 w-20 h-20 rounded-lg overflow-hidden border-2 transition-all duration-300"
              :class="i === lightbox ? 'border-[#00878E] scale-110' : 'border-transparent opacity-60 hover:opacity-100'">
              <img :src="img" class="w-full h-full object-cover" />
            </button>
          </div>
        </div>
      </Transition>
    </Teleport>

    <!-- ── Share Modal ── -->
    <Teleport to="body">
      <Transition name="modal">
        <div v-if="showShare" class="fixed inset-0 bg-black/50 flex items-center justify-center z-[150] px-4"
          @click.self="showShare = false">
          <div class="bg-white rounded-2xl p-6 max-w-md w-full shadow-2xl">
            <div class="flex items-center justify-between mb-4">
              <h3 class="font-bold text-lg text-[#313131]">Partager cette annonce</h3>
              <button @click="showShare = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                <Icon name="lucide:x" class="w-5 h-5" />
              </button>
            </div>
            <div class="flex items-center gap-2 bg-gray-50 rounded-lg p-3 mb-4">
              <input type="text" readonly :value="currentUrl"
                class="flex-1 bg-transparent text-sm text-gray-700 outline-none truncate" />
            </div>
            <button @click="copyLink"
              class="w-full flex items-center justify-center gap-2 px-6 py-3 rounded-full font-semibold text-white transition-all duration-300"
              :class="copySuccess ? 'bg-green-500' : 'bg-[#00878E] hover:bg-[#006b70]'">
              <Icon :name="copySuccess ? 'lucide:check' : 'lucide:copy'" class="w-5 h-5" />
              {{ copySuccess ? 'Lien copié !' : 'Copier le lien' }}
            </button>
          </div>
        </div>
      </Transition>
    </Teleport>

    <!-- ── Loading skeleton ── -->
    <div v-if="loading" class="py-8 px-8">
      <div class="max-w-[1440px] mx-auto space-y-5">
        <div class="h-10 w-48 bg-gray-200 rounded-lg animate-pulse" />
        <div class="h-[500px] bg-gray-200 rounded-2xl animate-pulse" />
        <div class="grid grid-cols-3 gap-8">
          <div class="col-span-2 space-y-5">
            <div class="h-40 bg-gray-200 rounded-2xl animate-pulse" />
            <div class="h-48 bg-gray-200 rounded-2xl animate-pulse" />
          </div>
          <div class="h-64 bg-gray-200 rounded-2xl animate-pulse" />
        </div>
      </div>
    </div>

    <!-- ── Content ── -->
    <template v-else-if="l">

      <!-- Back bar -->
      <div class="bg-white border-b border-gray-200 py-4 px-8">
        <div class="max-w-[1440px] mx-auto">
          <!-- Breadcrumb -->
          <nav class="flex items-center gap-1.5 text-xs text-gray-400 mb-3">
            <NuxtLink to="/" class="hover:text-[#00878E] transition-colors flex items-center gap-1">
              <Icon name="lucide:home" class="w-3 h-3" />
            </NuxtLink>
            <Icon name="lucide:chevron-right" class="w-3 h-3" />
            <NuxtLink to="/listings" class="hover:text-[#00878E] transition-colors">Annonces</NuxtLink>
            <Icon name="lucide:chevron-right" class="w-3 h-3" />
            <span v-if="l.country" class="hover:text-[#00878E]">{{ l.country }}</span>
            <template v-if="l.city">
              <Icon name="lucide:chevron-right" class="w-3 h-3" />
              <span class="text-gray-600 truncate max-w-[120px]">{{ l.city }}</span>
            </template>
            <template v-if="l.property_type">
              <Icon name="lucide:chevron-right" class="w-3 h-3" />
              <span class="text-gray-600">{{ l.property_type }}</span>
            </template>
          </nav>

          <NuxtLink to="/listings"
            class="inline-flex items-center gap-2 text-[#00878E] font-semibold hover:gap-3 transition-all duration-300">
            <Icon name="lucide:arrow-left" class="w-5 h-5" />
            Retour aux résultats
          </NuxtLink>
        </div>
      </div>

      <!-- Main content -->
      <div class="py-8 px-8">
        <div class="max-w-[1440px] mx-auto">

          <!-- ── Image Gallery ── -->
          <div class="mb-8 relative">
            <!-- Overlay action buttons -->
            <div class="absolute top-4 right-4 z-10 flex items-center gap-2">
              <button @click="showShare = true"
                class="px-4 py-2 bg-white/90 backdrop-blur-sm rounded-full flex items-center gap-2 hover:bg-white transition-colors duration-300 shadow-md">
                <Icon name="lucide:share-2" class="w-4 h-4 text-gray-700" />
                <span class="text-sm text-gray-700">Partager</span>
              </button>

              <button v-if="isLoggedIn" @click="toggleFav"
                class="px-4 py-2 backdrop-blur-sm rounded-full flex items-center gap-2 transition-all duration-300 shadow-md"
                :class="isFav ? 'bg-[#00878E] hover:bg-[#006b70]' : 'bg-white/90 hover:bg-white'">
                <Icon name="lucide:heart"
                  :class="isFav ? 'text-white fill-current' : 'text-gray-700'"
                  class="w-4 h-4" />
                <span class="text-sm" :class="isFav ? 'text-white' : 'text-gray-700'">
                  {{ isFav ? 'Enregistré' : 'Ajouter aux favoris' }}
                </span>
              </button>
            </div>

            <!-- 3-col grid gallery -->
            <div v-if="imageList.length" class="grid grid-cols-3 gap-2 h-[500px] overflow-hidden">
              <!-- Main large image (left, col-span-2) -->
              <div class="col-span-2 relative h-full min-h-0 rounded-2xl overflow-hidden group cursor-zoom-in"
                @click="lightbox = 0">
                <img :src="imageList[0]" :alt="l.title"
                  class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
                <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-colors" />
              </div>

              <!-- Right column: 2 stacked images -->
              <div class="col-span-1 h-full min-h-0 flex flex-col gap-2">
                <!-- Second image -->
                <div v-if="imageList[1]" class="relative flex-1 min-h-0 rounded-2xl overflow-hidden cursor-zoom-in group"
                  @click="lightbox = 1">
                  <img :src="imageList[1]" :alt="l.title + ' 2'"
                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
                  <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-colors" />
                </div>

                <!-- Third image + "Open gallery" overlay -->
                <div v-if="imageList[2]" class="relative flex-1 min-h-0 rounded-2xl overflow-hidden cursor-zoom-in group"
                  @click="lightbox = imageList.length > 3 ? undefined : 2">
                  <img :src="imageList[2]" :alt="l.title + ' 3'"
                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
                  <div v-if="imageList.length > 3" class="absolute inset-0 bg-black/30 flex items-end justify-end p-4">
                    <button @click.stop="lightbox = 0"
                      class="px-4 py-2 bg-white rounded-full flex items-center gap-2 hover:bg-gray-100 transition-colors duration-300 shadow-lg">
                      <Icon name="lucide:image" class="w-4 h-4 text-gray-700" />
                      <span class="text-sm text-gray-700">Ouvrir la galerie</span>
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- No photos placeholder -->
            <div v-else class="h-[500px] rounded-2xl bg-white flex items-center justify-center text-gray-200 shadow-md">
              <Icon name="lucide:image" class="w-20 h-20" />
            </div>
          </div>

          <!-- ── Content Grid ── -->
          <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <!-- Left column (2/3) -->
            <div class="lg:col-span-2 space-y-6">

              <!-- Title & location & stats -->
              <div class="bg-white rounded-2xl p-6 shadow-md">
                <!-- Type badges -->
                <div class="flex flex-wrap gap-2 mb-4">
                  <span v-if="l.property_type" class="badge">{{ l.property_type }}</span>
                  <span v-if="l.property_typology" class="badge">{{ l.property_typology }}</span>
                  <span v-if="l.other_features?.is_furnished === true" class="badge badge-green">{{ lbl('furnished') }}</span>
                  <span v-if="l.other_features?.is_furnished === false" class="badge">{{ lbl('unfurnished') }}</span>
                  <span v-if="l.other_features?.is_new" class="badge badge-teal">{{ lbl('new_build') }}</span>
                  <span v-if="l.other_features?.posted_by_pro" class="badge badge-blue">Pro</span>
                </div>

                <h1 class="font-bold text-[22px] text-[#313131] mb-3 leading-snug">{{ l.title }}</h1>

                <div class="flex items-center gap-2 text-gray-600 mb-4">
                  <Icon name="lucide:map-pin" class="w-4 h-4 text-[#00878E] flex-shrink-0" />
                  <span class="text-sm">{{ l.location }}</span>
                </div>

                <!-- Main stats row -->
                <div class="flex items-center gap-6 pt-4 border-t border-gray-100 flex-wrap">
                  <div v-if="l.bedrooms" class="flex items-center gap-2">
                    <Icon name="lucide:bed-double" class="w-4 h-4 text-gray-500" />
                    <span class="text-sm text-gray-700">{{ l.bedrooms }} {{ (l.bedrooms ?? 0) > 1 ? 'Chambres' : 'Chambre' }}</span>
                  </div>
                  <div v-if="l.bathrooms" class="flex items-center gap-2">
                    <Icon name="lucide:bath" class="w-4 h-4 text-gray-500" />
                    <span class="text-sm text-gray-700">{{ l.bathrooms }} {{ (l.bathrooms ?? 0) > 1 ? 'Salles de bain' : 'Salle de bain' }}</span>
                  </div>
                  <div v-if="l.interior_features?.rooms && !l.bedrooms" class="flex items-center gap-2">
                    <Icon name="lucide:layout-grid" class="w-4 h-4 text-gray-500" />
                    <span class="text-sm text-gray-700">{{ l.interior_features.rooms }} pièces</span>
                  </div>
                  <div v-if="l.interior_features?.surface_m2" class="flex items-center gap-2">
                    <Icon name="lucide:maximize" class="w-4 h-4 text-gray-500" />
                    <span class="text-sm text-gray-700">{{ l.interior_features.surface_m2 }} m²</span>
                  </div>
                </div>
              </div>

              <!-- Description -->
              <div v-if="l.description" class="bg-white rounded-2xl p-6 shadow-md">
                <h2 class="font-bold text-lg text-[#313131] mb-4">Description</h2>
                <p class="leading-relaxed text-[15px] whitespace-pre-line description-text">{{ l.description }}</p>
              </div>

              <!-- Amenities / Features (other_features.features array) -->
              <div v-if="allFeatures.length" class="bg-white rounded-2xl p-6 shadow-md">
                <h2 class="font-bold text-lg text-[#313131] mb-4">Caractéristiques</h2>
                <div class="grid grid-cols-2 gap-4">
                  <div v-for="(feature, i) in allFeatures" :key="i" class="flex items-center gap-2">
                    <div class="w-5 h-5 rounded-full bg-[#00878E]/10 flex items-center justify-center shrink-0">
                      <Icon name="lucide:check" class="w-3 h-3 text-[#00878E]" />
                    </div>
                    <span class="text-sm text-gray-700">{{ feature }}</span>
                  </div>
                </div>
              </div>

              <!-- Interior details (structured) -->
              <div v-if="hasInteriorStructured" class="bg-white rounded-2xl p-6 shadow-md">
                <h2 class="font-bold text-lg text-[#313131] mb-4 flex items-center gap-2">
                  <Icon name="lucide:sofa" class="w-5 h-5 text-[#00878E]" />
                  Intérieur
                </h2>
                <div class="grid grid-cols-2 gap-x-6 gap-y-3">
                  <div v-if="l.interior_features?.surface_m2" class="feat-row">
                    <span class="feat-label">{{ lbl('surface') }}</span>
                    <span class="feat-val">{{ l.interior_features.surface_m2 }} m²</span>
                  </div>
                  <div v-if="l.interior_features?.rooms" class="feat-row">
                    <span class="feat-label">{{ lbl('rooms') }}</span>
                    <span class="feat-val">{{ l.interior_features.rooms }}</span>
                  </div>
                  <div v-if="l.interior_features?.floor !== undefined && l.interior_features.floor !== null" class="feat-row">
                    <span class="feat-label">{{ lbl('floor') }}</span>
                    <span class="feat-val">{{ l.interior_features.floor }}</span>
                  </div>
                  <div v-if="l.interior_features?.heating" class="feat-row">
                    <span class="feat-label">{{ lbl('heating') }}</span>
                    <span class="feat-val">{{ l.interior_features.heating }}</span>
                  </div>
                </div>
              </div>

              <!-- Exterior details (structured) -->
              <div v-if="hasExteriorStructured" class="bg-white rounded-2xl p-6 shadow-md">
                <h2 class="font-bold text-lg text-[#313131] mb-4 flex items-center gap-2">
                  <Icon name="lucide:trees" class="w-5 h-5 text-[#00878E]" />
                  Extérieur
                </h2>
                <div class="grid grid-cols-2 gap-x-6 gap-y-3">
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
                </div>
              </div>

              <!-- Energy info -->
              <div v-if="l.other_features?.energy_class || l.other_features?.ghg_class" class="bg-white rounded-2xl p-6 shadow-md">
                <h2 class="font-bold text-lg text-[#313131] mb-4 flex items-center gap-2">
                  <Icon name="lucide:zap" class="w-5 h-5 text-[#00878E]" />
                  Énergie
                </h2>
                <div class="grid grid-cols-2 gap-x-6 gap-y-3">
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
                    <span class="feat-val font-bold px-2 rounded bg-[#00878E]/10 text-[#006b70]">{{ l.other_features.ghg_class }}</span>
                  </div>
                  <div v-if="l.other_features?.ghg_value" class="feat-row">
                    <span class="feat-label">GHG</span>
                    <span class="feat-val">{{ l.other_features.ghg_value }} kg CO₂/m²/y</span>
                  </div>
                </div>
              </div>

            </div>

            <!-- Right sidebar (1/3) -->
            <div class="lg:col-span-1">
              <div class="bg-white rounded-2xl p-6 shadow-md sticky top-[100px]">

                <!-- Price -->
                <div class="mb-6 pb-6 border-b border-gray-200">
                  <div class="flex items-baseline gap-2 mb-1">
                    <span class="font-bold text-3xl text-[#00878E]">{{ formatPrice(l.price, l.currency) }}</span>
                    <span v-if="l.property_typology === 'rent'" class="text-lg text-gray-500">/ mois</span>
                  </div>
                  <p v-if="l.price_per_m2" class="text-xs text-gray-400">
                    {{ formatPrice(l.price_per_m2, l.currency) }} / m²
                  </p>
                  <div class="flex items-center gap-2 text-sm text-gray-500 mt-2">
                    <Icon name="lucide:building-2" class="w-4 h-4 flex-shrink-0" />
                    <span class="truncate">{{ l.agency_name || l.seller_name || 'Particulier' }}</span>
                  </div>
                </div>

                <!-- Extra costs -->
                <div v-if="extraDetails.length" class="space-y-2 mb-6 pb-6 border-b border-gray-200">
                  <div v-for="item in extraDetails" :key="item.label" class="flex justify-between text-sm">
                    <span class="text-gray-500">{{ item.label }}</span>
                    <span class="text-gray-800 font-medium">{{ item.value }}</span>
                  </div>
                </div>

                <!-- CTA: View on source site -->
                <a v-if="l.url" :href="l.url" target="_blank" rel="noopener noreferrer"
                  class="w-full flex items-center justify-center gap-2 px-6 py-4 rounded-full bg-[#00878E] font-semibold text-base text-white hover:bg-[#006b70] transition-all duration-300 shadow-md hover:shadow-lg hover:scale-[1.02] mb-3">
                  Voir sur le site d'origine
                  <Icon name="lucide:external-link" class="w-5 h-5" />
                </a>

                <!-- Favorite button -->
                <button v-if="isLoggedIn" @click="toggleFav"
                  class="w-full flex items-center justify-center gap-2 px-6 py-3 rounded-full border font-semibold text-sm transition-all duration-300"
                  :class="isFav ? 'border-[#00878E] text-[#00878E] bg-[#00878E]/5' : 'border-gray-200 text-gray-600 hover:border-[#00878E] hover:text-[#00878E] hover:bg-[#00878E]/5'">
                  <Icon name="lucide:heart" class="w-4 h-4" :class="isFav ? 'fill-[#00878E]' : ''" />
                  {{ isFav ? lbl('saved_favorites') : lbl('save_favorites') }}
                </button>

                <!-- Seller contact -->
                <div v-if="l.seller_phone" class="mt-4 pt-4 border-t border-gray-100">
                  <p class="text-xs text-gray-400 mb-2 font-medium uppercase tracking-wide">{{ lbl('contact') }}</p>
                  <p v-if="l.seller_name" class="text-sm font-semibold text-gray-800">{{ l.seller_name }}</p>
                  <a :href="`tel:${l.seller_phone}`"
                    class="mt-1 flex items-center gap-1.5 text-sm text-[#00878E] hover:text-[#006b70] transition-colors">
                    <Icon name="lucide:phone" class="w-3.5 h-3.5" />
                    {{ l.seller_phone }}
                  </a>
                </div>

                <!-- Metadata -->
                <div v-if="metaDetails.length" class="mt-4 pt-4 border-t border-gray-100">
                  <div class="space-y-2">
                    <div v-for="item in metaDetails" :key="item.label" class="flex justify-between text-xs">
                      <span class="text-gray-400">{{ item.label }}</span>
                      <span class="text-gray-600 font-medium text-right max-w-[55%] truncate">{{ item.value }}</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- ── Similar listings ── -->
      <div v-if="similarListings.length" class="py-8 px-8">
        <div class="max-w-[1440px] mx-auto">
          <h2 class="font-bold text-2xl text-[#313131] mb-6">Biens similaires</h2>
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <ListingsListingCard
              v-for="item in similarListings"
              :key="item.id"
              :listing="item"
            />
          </div>
        </div>
      </div>

    </template>

    <!-- ── Not found ── -->
    <div v-else-if="!loading" class="min-h-screen flex items-center justify-center">
      <div class="text-center">
        <h1 class="font-bold text-3xl text-[#313131] mb-4">Annonce non trouvée</h1>
        <NuxtLink to="/listings"
          class="inline-flex items-center gap-2 text-[#00878E] font-semibold hover:underline">
          <Icon name="lucide:arrow-left" class="w-5 h-5" />
          Retour aux annonces
        </NuxtLink>
      </div>
    </div>

  </div>
</template>

<script setup lang="ts">
import type { Listing } from '~/composables/useListings'
import { useFavoritesStore } from '~/stores/favorites'
import { useLabels } from '~/composables/useLabels'

const route = useRoute()
const { fetchListing, fetchListings } = useListings()
const { isLoggedIn } = useAuth()
const favorites = useFavoritesStore()
const { l: lbl } = useLabels()

const id = Number(route.params.id)
const l = ref<Listing | null>(null)
const loading = ref(true)
const lightbox = ref<number | null>(null)
const lbEl = ref<HTMLElement | null>(null)
const showShare = ref(false)
const copySuccess = ref(false)
const currentUrl = ref('')
const similarListings = ref<Listing[]>([])

onMounted(async () => {
  currentUrl.value = window.location.href
  try {
    const res = await fetchListing(id)
    l.value = res.data

    // Fetch similar listings (same country + property_type, exclude current)
    if (l.value) {
      const simRes = await fetchListings({
        country: l.value.country,
        property_type: l.value.property_type || undefined,
        per_page: 6,
      })
      similarListings.value = (simRes.data ?? []).filter((item: Listing) => item.id !== id).slice(0, 3)
    }
  } catch (e) {
    console.error('Failed to load listing:', e)
  } finally {
    loading.value = false
  }
})

// Focus lightbox for keyboard nav
watch(lightbox, (v) => {
  if (v !== null) nextTick(() => lbEl.value?.focus())
})

const isFav = computed(() => l.value ? favorites.isFavorite(l.value.id) : false)
function toggleFav() { if (l.value) favorites.toggle(l.value) }

const imageList = computed(() => l.value?.photos ?? [])

// Collect all feature strings from any feature field
const allFeatures = computed((): string[] => {
  if (!l.value) return []
  const features: string[] = []

  // other_features.features array (scraper format)
  if (Array.isArray(l.value.other_features?.features)) {
    features.push(...l.value.other_features.features)
  }
  // other_features as flat array
  if (Array.isArray(l.value.other_features)) {
    features.push(...(l.value.other_features as string[]))
  }
  // interior_features as array
  if (Array.isArray(l.value.interior_features)) {
    features.push(...(l.value.interior_features as string[]))
  }
  // exterior_features as array
  if (Array.isArray(l.value.exterior_features)) {
    features.push(...(l.value.exterior_features as string[]))
  }

  return [...new Set(features)].filter(Boolean)
})

// Only show structured interior section if it's an object (not array)
const hasInteriorStructured = computed(() => {
  const f = l.value?.interior_features
  return f && !Array.isArray(f) && typeof f === 'object' && Object.keys(f).length > 0
})
const hasExteriorStructured = computed(() => {
  const f = l.value?.exterior_features
  return f && !Array.isArray(f) && typeof f === 'object' && Object.keys(f).length > 0
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
  if ((l.value as any).source) items.push({ label: 'Source', value: (l.value as any).source })
  return items
})

async function copyLink() {
  try {
    await navigator.clipboard.writeText(currentUrl.value)
    copySuccess.value = true
    setTimeout(() => {
      copySuccess.value = false
      showShare.value = false
    }, 1500)
  } catch { /* fallback: nothing */ }
}

function formatPrice(price: number, currency: string): string {
  const locales: Record<string, string> = { EUR: 'fr-FR', TND: 'fr-TN', EGP: 'en-EG', CAD: 'en-CA' }
  try {
    return new Intl.NumberFormat(locales[currency] || 'en-US', { style: 'currency', currency, maximumFractionDigits: 0 }).format(price)
  } catch {
    return `${price?.toLocaleString()} ${currency}`
  }
}

useHead({ title: computed(() => l.value ? `${l.value.title || 'Annonce'} | GlobalRent` : 'Annonce | GlobalRent') })
</script>

<style scoped>
@reference "tailwindcss";

.badge       { @apply text-[11px] font-medium px-2.5 py-1 rounded-lg bg-gray-100 text-gray-600; }
.badge-green { @apply bg-green-50 text-green-700; }
.badge-teal  { @apply bg-[#00878E]/10 text-[#006b70]; }
.badge-blue  { @apply bg-blue-50 text-blue-600; }

.description-text { color: #374151 !important; }

.feat-row   { @apply flex items-center justify-between py-1.5 border-b border-gray-50 last:border-0; }
.feat-label { @apply text-sm text-gray-500; }
.feat-val   { @apply text-sm text-gray-800 font-medium; }

.image-fade-enter-active, .image-fade-leave-active { transition: opacity 0.2s ease; }
.image-fade-enter-from,   .image-fade-leave-to     { opacity: 0; }

.lb-enter-active,    .lb-leave-active    { transition: opacity 0.2s ease; }
.lb-enter-from,      .lb-leave-to        { opacity: 0; }

.modal-enter-active, .modal-leave-active { transition: opacity 0.2s ease; }
.modal-enter-from,   .modal-leave-to     { opacity: 0; }
</style>
