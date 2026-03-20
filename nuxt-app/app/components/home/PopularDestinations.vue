<template>
  <section class="py-12 px-8 bg-gray-50 border-b border-gray-100">
    <div class="max-w-[1440px] mx-auto">
      <!-- Header -->
      <div class="flex items-end justify-between mb-8">
        <div>
          <h2 class="font-bold text-3xl text-[#313131] mb-2">
            {{ $t('home.exploreByCountry') }}
          </h2>
          <p class="text-base text-gray-600">
            {{ $t('home.exploreByCountrySubtitle') }}
          </p>
        </div>
      </div>

      <!-- Horizontal scroll cards -->
      <div class="relative">
        <div class="destinations-scroll flex gap-6 overflow-x-auto pb-4">
          <NuxtLink
            v-for="dest in destinations"
            :key="dest.id"
            :to="`/listings?country=${dest.countryCode}`"
            class="group flex-shrink-0 w-[280px] block"
            @click="onDestinationClick(dest.countryCode)"
          >
            <div class="relative h-[200px] rounded-2xl overflow-hidden shadow-md hover:shadow-xl transition-all duration-300">
              <img
                :src="dest.imageUrl"
                :alt="dest.name"
                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                loading="lazy"
              />
              <!-- Gradient -->
              <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent" />
              <!-- Content -->
              <div class="absolute bottom-0 left-0 right-0 p-5">
                <div class="flex items-start justify-between">
                  <div>
                    <h3 class="font-bold text-xl text-white mb-1">{{ dest.name }}</h3>
                    <p class="text-sm text-white/90 flex items-center gap-1">
                      <Icon name="lucide:map-pin" class="w-3.5 h-3.5" />
                      {{ dest.name }}
                    </p>
                  </div>
                  <div v-if="countMap[dest.countryCode]" class="bg-white/20 backdrop-blur-sm rounded-full px-3 py-1">
                    <p class="text-xs font-semibold text-white">
                      {{ countMap[dest.countryCode]?.toLocaleString() }}+
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </NuxtLink>
        </div>
      </div>

      <!-- Search location button -->
      <div class="mt-8 flex justify-center">
        <button
          @click="searchOpen = true"
          class="inline-flex items-center gap-2 px-6 py-3 bg-white border-2 border-[#00878E] text-[#00878E] rounded-full font-semibold hover:bg-[#00878E] hover:text-white transition-all duration-300 shadow-md hover:shadow-lg"
        >
          <Icon name="lucide:search" class="w-5 h-5" />
          {{ $t('home.searchLocation') }}
        </button>
      </div>
    </div>

    <!-- Search popup -->
    <Teleport to="body">
      <div
        v-if="searchOpen"
        class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4"
        @mousedown.self="closeSearch"
      >
        <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[80vh] overflow-hidden">
          <!-- Popup header -->
          <div class="p-6 border-b border-gray-200 flex items-center justify-between">
            <h3 class="font-bold text-2xl text-[#313131]">
              {{ $t('home.searchLocation') }}
            </h3>
            <button @click="closeSearch" class="p-2 hover:bg-gray-100 rounded-full transition-colors">
              <Icon name="lucide:x" class="w-6 h-6 text-gray-600" />
            </button>
          </div>

          <!-- Search bar -->
          <div class="p-6 border-b border-gray-200">
            <form @submit.prevent="submitSearch">
              <div class="relative bg-white rounded-[16px] shadow-[0_2px_12px_rgba(0,0,0,0.08)] pl-6 pr-3 py-3 flex items-center gap-4">
                <Icon name="lucide:search" class="w-6 h-6 text-gray-400 shrink-0" />
                <input
                  v-model="searchTerm"
                  type="text"
                  :placeholder="$t('home.searchLocationPlaceholder')"
                  class="flex-1 text-[17px] text-gray-900 placeholder:text-gray-400 outline-none bg-transparent"
                  autofocus
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

          <!-- Results list -->
          <div class="p-6 overflow-y-auto max-h-[50vh]">
            <template v-if="filteredLocations.length">
              <p class="text-sm text-gray-500 mb-3">
                {{ searchTerm ? $t('home.suggestions') : $t('home.popularLocations') }}
              </p>
              <div class="grid grid-cols-2 gap-3">
                <NuxtLink
                  v-for="loc in filteredLocations"
                  :key="loc.id"
                  :to="loc.href"
                  @click="closeSearch"
                  class="flex items-center justify-between p-4 border border-gray-200 rounded-xl hover:border-[#00878E] hover:bg-[#00878E]/5 transition-all duration-200 group"
                >
                  <div class="flex items-center gap-3">
                    <Icon name="lucide:map-pin" class="w-5 h-5 text-[#00878E] shrink-0" />
                    <div>
                      <p class="font-semibold text-[#313131] group-hover:text-[#00878E]">{{ loc.name }}</p>
                      <p v-if="loc.count" class="text-sm text-gray-500">{{ loc.count.toLocaleString() }} {{ $t('home.listings') }}</p>
                    </div>
                  </div>
                  <Icon name="lucide:chevron-right" class="w-5 h-5 text-gray-400 group-hover:text-[#00878E]" />
                </NuxtLink>
              </div>
            </template>
            <div v-else class="text-center py-8">
              <p class="text-gray-600 mb-3">{{ $t('home.noSuggestions') }}</p>
              <button
                @click="submitSearch"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#00878E] text-white rounded-full font-semibold hover:bg-[#006b71] transition-colors"
              >
                <Icon name="lucide:search" class="w-4 h-4" />
                {{ $t('home.searchFor') }} "{{ searchTerm }}"
              </button>
            </div>
          </div>
        </div>
      </div>
    </Teleport>
  </section>
</template>

<script setup lang="ts">
const router = useRouter()
const region = useRegionStore()

const searchOpen = ref(false)
const searchTerm = ref('')

const props = defineProps<{
  countMap: Record<string, number>
}>()

// Only the 4 countries we actually scrape
const destinations = [
  {
    id: 1,
    countryCode: 'FR',
    name: 'France',
    imageUrl: 'https://images.unsplash.com/photo-1720988583730-1191f37e5fcd?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&w=800&q=80',
  },
  {
    id: 2,
    countryCode: 'TN',
    name: 'Tunisie',
    imageUrl: 'https://images.unsplash.com/photo-1682862841871-553ceb226b2e?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&w=800&q=80',
  },
  {
    id: 3,
    countryCode: 'EG',
    name: 'Égypte',
    imageUrl: 'https://images.unsplash.com/photo-1772791281804-75bd6bcf2ea6?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&w=800&q=80',
  },
  {
    id: 4,
    countryCode: 'CA',
    name: 'Canada',
    imageUrl: 'https://images.unsplash.com/photo-1444723121867-7a241cacace9?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&w=800&q=80',
  },
]

// Searchable cities per country
const allLocations = [
  // France
  { id: 1,  name: 'Paris',         href: '/listings?country=FR&city=Paris',         count: 0 },
  { id: 2,  name: 'Lyon',          href: '/listings?country=FR&city=Lyon',          count: 0 },
  { id: 3,  name: 'Marseille',     href: '/listings?country=FR&city=Marseille',     count: 0 },
  { id: 4,  name: 'Nice',          href: '/listings?country=FR&city=Nice',          count: 0 },
  { id: 5,  name: 'Bordeaux',      href: '/listings?country=FR&city=Bordeaux',      count: 0 },
  { id: 6,  name: 'Toulouse',      href: '/listings?country=FR&city=Toulouse',      count: 0 },
  // Tunisie
  { id: 7,  name: 'Tunis',         href: '/listings?country=TN&city=Tunis',         count: 0 },
  { id: 8,  name: 'Sfax',          href: '/listings?country=TN&city=Sfax',          count: 0 },
  { id: 9,  name: 'Sousse',        href: '/listings?country=TN&city=Sousse',        count: 0 },
  { id: 10, name: 'Hammamet',      href: '/listings?country=TN&city=Hammamet',      count: 0 },
  // Égypte
  { id: 11, name: 'Le Caire',      href: '/listings?country=EG&city=Cairo',         count: 0 },
  { id: 12, name: 'Alexandrie',    href: '/listings?country=EG&city=Alexandria',    count: 0 },
  { id: 13, name: 'Hurghada',      href: '/listings?country=EG&city=Hurghada',      count: 0 },
  { id: 14, name: 'Charm el-Cheikh', href: '/listings?country=EG&city=Sharm el-Sheikh', count: 0 },
  // Canada
  { id: 15, name: 'Montréal',      href: '/listings?country=CA&city=Montreal',      count: 0 },
  { id: 16, name: 'Toronto',       href: '/listings?country=CA&city=Toronto',       count: 0 },
  { id: 17, name: 'Vancouver',     href: '/listings?country=CA&city=Vancouver',     count: 0 },
  { id: 18, name: 'Québec',        href: '/listings?country=CA&city=Quebec',        count: 0 },
]

const filteredLocations = computed(() => {
  if (!searchTerm.value.trim()) return allLocations
  const q = searchTerm.value.toLowerCase()
  return allLocations.filter(l => l.name.toLowerCase().includes(q))
})

function onDestinationClick(code: string) {
  region.setRegion(code)
}

function closeSearch() {
  searchOpen.value = false
  searchTerm.value = ''
}

function submitSearch() {
  if (searchTerm.value.trim()) {
    router.push(`/listings?city=${encodeURIComponent(searchTerm.value.trim())}`)
    closeSearch()
  }
}
</script>

<style scoped>
.destinations-scroll::-webkit-scrollbar {
  height: 8px;
}
.destinations-scroll::-webkit-scrollbar-track {
  background: #f1f5f9;
  border-radius: 10px;
}
.destinations-scroll::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 10px;
}
.destinations-scroll::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}
</style>
