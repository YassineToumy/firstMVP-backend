<template>
  <div>
    <!-- ── Hero ── -->
    <section class="relative w-full min-h-screen">
      <div class="absolute inset-0">
        <img
          :src="heroImage"
          alt=""
          class="absolute w-full h-full object-cover"
        />
        <div class="absolute inset-0 bg-gradient-to-b from-black/50 via-black/30 to-black/60" />
      </div>

      <div class="relative z-10 h-full min-h-screen flex flex-col items-center justify-center px-6 pt-[84px]">
        <div class="max-w-[1200px] w-full text-center">
          <h1 class="font-bold text-[52px] sm:text-[64px] leading-tight text-white mb-6 animate-fade-in-up">
            Trouvez la location idéale,<br />où que vous soyez
          </h1>
          <p class="font-medium text-[16px] text-white/90 mb-12 animate-fade-in-up animation-delay-100">
            Explorez des milliers de locations dans le monde entier et trouvez celle qui vous correspond.
          </p>

          <!-- Search bar -->
          <form @submit.prevent="goSearch" class="relative max-w-[680px] mx-auto animate-fade-in-up animation-delay-200">
            <div class="relative bg-white rounded-[16px] shadow-[0_2px_12px_rgba(0,0,0,0.08)] pl-6 pr-3 py-3 flex items-center gap-4">
              <Icon name="lucide:search" class="w-6 h-6 text-gray-400 shrink-0" />
              <input
                v-model="searchCity"
                type="text"
                placeholder="Recherchez par pays, ville, région ou code postal"
                class="flex-1 text-[17px] text-gray-900 placeholder:text-gray-400 outline-none bg-transparent"
                @keyup.enter="goSearch"
              />
              <button
                type="submit"
                class="bg-[#00878E] w-[52px] h-[52px] rounded-full flex items-center justify-center hover:bg-[#006b70] transition-all duration-300 hover:scale-105 shrink-0 shadow-sm"
              >
                <Icon name="lucide:arrow-right" class="w-6 h-6 text-white" />
              </button>
            </div>
          </form>
        </div>
      </div>
    </section>

    <!-- ── Recent listings ── -->
    <section class="py-20 px-8 bg-gray-50">
      <div class="max-w-[1440px] mx-auto">
        <div class="mb-12">
          <h2 class="font-bold text-4xl text-[#313131]">
            Locations récemment ajoutées
          </h2>
          <p v-if="region.currentCode" class="text-gray-600 mt-2">
            Les dernières annonces en {{ region.current.name }}
          </p>
        </div>

        <!-- No region selected -->
        <div v-if="!region.currentCode" class="py-16 flex flex-col items-center justify-center gap-4 text-center">
          <div class="w-16 h-16 rounded-2xl bg-[#00878E]/10 border-2 border-[#00878E]/20 flex items-center justify-center">
            <Icon name="lucide:globe" class="w-8 h-8 text-[#00878E]" />
          </div>
          <p class="text-gray-700 font-medium">Choisissez une région pour voir les annonces</p>
          <div class="flex gap-3 flex-wrap justify-center mt-2">
            <button
              v-for="r in quickRegions"
              :key="r.code"
              @click="selectRegion(r.code)"
              class="flex items-center gap-2 px-4 py-2 rounded-full border border-gray-200 hover:border-[#00878E] hover:text-[#00878E] transition-all text-sm font-medium text-gray-700"
            >
              <img :src="`https://flagcdn.com/20x15/${r.code.toLowerCase()}.png`" :alt="r.name" class="w-4 rounded-sm" />
              {{ r.name }}
            </button>
          </div>
        </div>

        <!-- Skeleton -->
        <div v-else-if="loadingListings" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          <div v-for="n in 6" :key="n" class="bg-white rounded-2xl overflow-hidden shadow-md">
            <div class="h-56 bg-gray-100 animate-pulse" />
            <div class="p-4 space-y-3">
              <div class="h-5 bg-gray-100 rounded animate-pulse w-1/2" />
              <div class="h-4 bg-gray-100 rounded animate-pulse w-2/3" />
              <div class="h-4 bg-gray-100 rounded animate-pulse w-1/3" />
            </div>
          </div>
        </div>

        <!-- Grid -->
        <div v-else-if="listings.length" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-12">
          <ListingsListingCard
            v-for="(l, i) in listings"
            :key="l.source_id"
            :listing="l"
            class="animate-fade-in-up"
            :style="{ animationDelay: `${i * 50}ms` }"
          />
        </div>

        <p v-else class="text-center py-12 text-gray-500">Aucune annonce disponible</p>

        <!-- View all -->
        <div v-if="listings.length" class="text-center">
          <NuxtLink to="/listings">
            <button class="px-8 py-4 rounded-full bg-[#00878E] font-semibold text-base text-white hover:bg-[#006b70] transition-all duration-300 shadow-md hover:shadow-lg">
              Voir toutes les locations
            </button>
          </NuxtLink>
        </div>
      </div>
    </section>

    <!-- ── Country explorer ── -->
    <section class="py-20 px-8 bg-white">
      <div class="max-w-[1440px] mx-auto">
        <div class="text-center mb-10">
          <h2 class="font-bold text-4xl text-[#313131] mb-3">Explorer par pays</h2>
          <p class="text-lg text-gray-600">Découvrez les marchés immobiliers mondiaux</p>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-6">
          <button
            v-for="r in countryCards"
            :key="r.code"
            @click="switchRegion(r.code)"
            class="group relative overflow-hidden rounded-2xl border-2 border-gray-100 bg-white p-6 text-left hover:border-[#00878E]/40 hover:shadow-lg transition-all duration-300"
          >
            <img :src="`https://flagcdn.com/40x30/${r.code.toLowerCase()}.png`" :alt="r.name" class="w-10 h-auto mb-3 rounded" />
            <h3 class="font-bold text-gray-900 group-hover:text-[#00878E] transition-colors">{{ r.name }}</h3>
            <p class="text-xs text-gray-500 mt-1">{{ r.currency }}</p>
            <div v-if="regionCountMap[r.code]" class="mt-3 text-sm font-semibold text-[#00878E]">
              {{ regionCountMap[r.code]?.toLocaleString() }} annonces
            </div>
            <div class="absolute bottom-4 right-4 w-8 h-8 rounded-full bg-[#00878E]/10 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
              <Icon name="lucide:arrow-right" class="w-4 h-4 text-[#00878E]" />
            </div>
          </button>
        </div>
      </div>
    </section>

    <!-- ── Blog / Guides ── -->
    <section class="py-20 px-8 bg-gray-50">
      <div class="max-w-[1440px] mx-auto">
        <div class="mb-4">
          <h2 class="font-bold text-4xl text-[#313131] mb-3">Articles récents</h2>
          <p class="text-lg text-gray-600 max-w-xl">
            Explorez nos derniers articles et restez informé des tendances du marché immobilier.
          </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mt-12">
          <NuxtLink
            v-for="article in blogArticles"
            :key="article.id"
            to="/conseils"
            class="group bg-white rounded-2xl overflow-hidden shadow-md hover:shadow-xl transition-all duration-300 block"
          >
            <div class="relative h-56 overflow-hidden">
              <img
                :src="article.imageUrl"
                :alt="article.title"
                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
              />
            </div>
            <div class="p-5">
              <h3 class="font-semibold text-[#313131] mb-2 line-clamp-2 text-lg">{{ article.title }}</h3>
              <p class="text-sm text-gray-600 mb-4 line-clamp-2">{{ article.description }}</p>
              <div class="flex items-center gap-2 text-[#00878E] font-semibold text-sm group-hover:gap-3 transition-all duration-300">
                Lire l'article
                <Icon name="lucide:arrow-right" class="w-4 h-4" />
              </div>
            </div>
          </NuxtLink>
        </div>

        <div class="text-center mt-12">
          <NuxtLink to="/conseils">
            <button class="px-8 py-4 rounded-full bg-[#00878E] font-semibold text-base text-white hover:bg-[#006b70] transition-all duration-300 shadow-md hover:shadow-lg">
              Voir tous les guides
            </button>
          </NuxtLink>
        </div>
      </div>
    </section>

    <!-- ── CTA ── -->
    <section class="bg-[#00878E] py-16 px-8">
      <div class="max-w-[1440px] mx-auto text-center">
        <h2 class="text-2xl sm:text-3xl font-bold text-white">Prêt à trouver votre prochaine location ?</h2>
        <p class="mt-3 text-white/80 max-w-md mx-auto text-sm">
          Explorez des milliers d'annonces dans plusieurs pays et trouvez la propriété idéale.
        </p>
        <div class="mt-8 flex flex-col sm:flex-row gap-3 justify-center">
          <NuxtLink to="/listings" class="inline-flex items-center justify-center px-8 py-3 bg-white text-[#00878E] text-sm font-semibold rounded-full hover:bg-gray-50 transition-colors shadow-md">
            Parcourir les annonces
          </NuxtLink>
          <NuxtLink to="/register" class="inline-flex items-center justify-center px-8 py-3 border-2 border-white/40 text-white text-sm font-medium rounded-full hover:bg-white/10 transition-colors">
            Créer un compte
          </NuxtLink>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup lang="ts">
import type { Listing } from '~/composables/useListings'

const region = useRegionStore()
const { fetchListings, fetchRegions } = useListings()
const router = useRouter()

const searchCity = ref('')
const listings = ref<Listing[]>([])
const regionStats = ref<{ code: string; name: string; currency: string; count: number }[]>([])
const loadingListings = ref(false)

const heroImage = 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?w=1920&q=80&auto=format&fit=crop'

const quickRegions = [
  { code: 'FR', name: 'France' },
  { code: 'TN', name: 'Tunisie' },
  { code: 'EG', name: 'Égypte' },
  { code: 'CA', name: 'Canada' },
]

const countryCards = [
  { code: 'FR', name: 'France', currency: 'EUR — Euro' },
  { code: 'TN', name: 'Tunisie', currency: 'TND — Dinar' },
  { code: 'EG', name: 'Égypte', currency: 'EGP — Livre' },
  { code: 'CA', name: 'Canada', currency: 'CAD — Dollar' },
]

const blogArticles = [
  {
    id: 1,
    title: 'Louer un appartement à Paris : Guide complet 2026',
    description: 'Tout ce que vous devez savoir pour trouver et louer un appartement dans la capitale française.',
    imageUrl: 'https://images.unsplash.com/photo-1635198278914-9b88489e4a3f?w=800&q=80',
  },
  {
    id: 2,
    title: 'Location saisonnière à Barcelone : nos conseils',
    description: 'Découvrez comment profiter d\'une location en bord de mer méditerranéenne en toute sérénité.',
    imageUrl: 'https://images.unsplash.com/photo-1711471965650-5ddda764158b?w=800&q=80',
  },
  {
    id: 3,
    title: 'Vivre à Dubaï : le guide de l\'expatrié',
    description: 'Les démarches essentielles et conseils pratiques pour s\'installer et louer aux Émirats.',
    imageUrl: 'https://images.unsplash.com/photo-1613914011280-fc80bd159816?w=800&q=80',
  },
]

const regionCountMap = computed(() => {
  const map: Record<string, number> = {}
  for (const r of regionStats.value) map[r.code.toUpperCase()] = r.count
  return map
})

function goSearch() {
  const q: Record<string, string> = {}
  if (searchCity.value.trim()) q.city = searchCity.value.trim()
  router.push({ path: '/listings', query: q })
}

function selectRegion(code: string) {
  region.setRegion(code)
  loadFeatured()
}

function switchRegion(code: string) {
  region.setRegion(code)
  router.push('/listings')
}

async function loadFeatured() {
  loadingListings.value = true
  try {
    const res = await fetchListings({ per_page: 6 })
    listings.value = res.data
  } catch (e) {
    console.error('Failed to load featured listings:', e)
  } finally {
    loadingListings.value = false
  }
}

async function loadRegions() {
  try {
    const res = await fetchRegions()
    regionStats.value = res.data
  } catch (e) {
    console.error('Failed to load regions:', e)
  }
}

watch(() => region.currentCode, (code) => { if (code) loadFeatured() })

onMounted(() => {
  loadRegions()
  if (region.currentCode) loadFeatured()
})

useHead({ title: 'RentGlobe — Trouvez votre prochaine location' })
</script>
