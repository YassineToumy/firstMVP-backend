<template>
  <nav class="fixed top-0 left-0 right-0 z-50 h-[84px] px-6 bg-white shadow-sm">
    <div class="max-w-[1440px] mx-auto h-full flex items-center">

      <!-- Logo -->
      <NuxtLink to="/" class="flex items-center gap-2 shrink-0">
        <div class="relative w-[42px] h-[42px] bg-[#00878E] rounded-lg flex items-center justify-center">
          <Icon name="lucide:home" class="w-6 h-6 text-white absolute" />
          <Icon name="lucide:globe" class="w-3 h-3 text-white absolute bottom-1 right-1" />
        </div>
        <div class="flex flex-col">
          <span class="font-bold text-[18px] text-[#00878E] leading-tight">RentGlobe</span>
          <span class="font-normal text-[10px] text-gray-500 leading-tight">Location mondiale</span>
        </div>
      </NuxtLink>

      <!-- Compact search (hidden on landing page) -->
      <div v-if="!isLandingPage" class="ml-4 max-w-[400px] w-full">
        <form @submit.prevent="submitSearch">
          <div class="relative bg-white rounded-xl shadow-[0_2px_8px_rgba(0,0,0,0.06)] pl-4 pr-1.5 py-1.5 flex items-center gap-2.5 border border-gray-200 hover:border-[#00878E] transition-all duration-300">
            <Icon name="lucide:search" class="w-4 h-4 text-gray-400 shrink-0" />
            <input
              v-model="searchCity"
              type="text"
              placeholder="Recherchez par ville..."
              class="flex-1 text-[14px] text-gray-900 placeholder:text-gray-400 outline-none bg-transparent"
            />
            <button
              type="submit"
              class="bg-[#00878E] w-[34px] h-[34px] rounded-full flex items-center justify-center hover:bg-[#006b70] transition-all duration-300 hover:scale-105 shrink-0 shadow-sm"
            >
              <Icon name="lucide:arrow-right" class="w-4 h-4 text-white" />
            </button>
          </div>
        </form>
      </div>

      <!-- Right actions -->
      <div class="flex items-center gap-3 ml-auto">

        <!-- Logged-in state -->
        <template v-if="isLoggedIn">
          <!-- Favorites -->
          <NuxtLink
            to="/account/favorites"
            class="hidden sm:flex items-center gap-2 px-4 py-[10px] rounded-full border border-gray-200 hover:border-[#00878E] transition-all duration-300"
          >
            <Icon name="lucide:heart" class="w-4 h-4 text-gray-600" />
            <span class="font-semibold text-[14px] text-[#313131]">Favoris</span>
            <span v-if="favorites.count > 0" class="text-xs font-bold text-[#00878E]">({{ favorites.count }})</span>
          </NuxtLink>

          <!-- User dropdown -->
          <div class="relative" ref="userDropRef">
            <button
              @click.stop="userDropOpen = !userDropOpen"
              class="flex items-center gap-2 px-4 py-[10px] rounded-full border border-gray-200 hover:border-[#00878E] transition-all duration-300"
            >
              <Icon name="lucide:user" class="w-4 h-4 text-gray-500" />
              <span class="hidden sm:inline font-semibold text-[14px] text-[#313131]">{{ user?.name }}</span>
              <Icon name="lucide:chevron-down" class="w-4 h-4 text-gray-500 transition-transform duration-200" :class="{ 'rotate-180': userDropOpen }" />
            </button>

            <Transition
              enter-active-class="transition duration-150 ease-out"
              enter-from-class="opacity-0 scale-95 -translate-y-1"
              enter-to-class="opacity-100 scale-100 translate-y-0"
              leave-active-class="transition duration-100 ease-in"
              leave-from-class="opacity-100 scale-100 translate-y-0"
              leave-to-class="opacity-0 scale-95 -translate-y-1"
            >
              <div v-if="userDropOpen" class="absolute top-full right-0 mt-2 w-[240px] bg-white rounded-2xl shadow-[0_4px_24px_rgba(0,0,0,0.12)] py-2 border border-gray-100 z-50">
                <div class="px-4 py-2 border-b border-gray-100 mb-1">
                  <p class="text-xs text-gray-500 truncate">{{ user?.email }}</p>
                </div>
                <NuxtLink to="/account/index" @click="userDropOpen = false" class="user-menu-item">
                  <Icon name="lucide:user" class="w-5 h-5 text-gray-600" />
                  <span>Mon profil</span>
                </NuxtLink>
                <NuxtLink to="/account/settings" @click="userDropOpen = false" class="user-menu-item">
                  <Icon name="lucide:settings" class="w-5 h-5 text-gray-600" />
                  <span>Paramètres</span>
                </NuxtLink>
                <NuxtLink to="/account/password" @click="userDropOpen = false" class="user-menu-item">
                  <Icon name="lucide:shield" class="w-5 h-5 text-gray-600" />
                  <span>Sécurité</span>
                </NuxtLink>
                <div class="my-1 border-t border-gray-100" />
                <button @click="doLogout" class="user-menu-item text-red-500 w-full">
                  <Icon name="lucide:log-out" class="w-5 h-5" />
                  <span>Déconnexion</span>
                </button>
              </div>
            </Transition>
          </div>
        </template>

        <!-- Guest state -->
        <template v-else>
          <button
            @click="showAuth = true"
            class="px-6 py-[10px] rounded-full bg-[#00878E] font-semibold text-[14px] text-white hover:bg-[#006b70] transition-all duration-300 shadow-sm hover:shadow-md"
          >
            Connexion
          </button>
        </template>

        <!-- Country/Language selector -->
        <div class="relative" ref="countryDropRef">
          <button
            @click.stop="countryDropOpen = !countryDropOpen"
            class="flex items-center gap-2 px-4 py-[10px] rounded-full border border-gray-200 hover:border-[#00878E] transition-all duration-300"
          >
            <img
              v-if="region.currentCode"
              :src="`https://flagcdn.com/20x15/${region.currentCode.toLowerCase()}.png`"
              :alt="region.current.name"
              class="w-5 h-auto rounded-sm"
            />
            <Icon v-else name="lucide:globe" class="w-4 h-4 text-gray-500" />
            <span class="hidden sm:inline font-semibold text-[14px] text-[#313131]">{{ region.current.name || 'Région' }}</span>
            <Icon name="lucide:chevron-down" class="w-4 h-4 text-gray-500 transition-transform duration-200" :class="{ 'rotate-180': countryDropOpen }" />
          </button>

          <Transition
            enter-active-class="transition duration-150 ease-out"
            enter-from-class="opacity-0 scale-95 -translate-y-1"
            enter-to-class="opacity-100 scale-100 translate-y-0"
            leave-active-class="transition duration-100 ease-in"
            leave-from-class="opacity-100 scale-100 translate-y-0"
            leave-to-class="opacity-0 scale-95 -translate-y-1"
          >
            <div v-if="countryDropOpen" class="absolute top-full right-0 mt-2 w-[260px] bg-white rounded-2xl shadow-[0_4px_24px_rgba(0,0,0,0.12)] p-4 border border-gray-100 z-50">
              <p class="font-semibold text-[13px] text-gray-500 mb-3">Choisir une région</p>
              <div class="space-y-1">
                <button
                  v-for="r in countries"
                  :key="r.code"
                  @click="selectCountry(r.code)"
                  class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-gray-50 transition-colors text-left"
                  :class="region.currentCode === r.code ? 'bg-[#00878E]/5 border border-[#00878E]/20' : ''"
                >
                  <img :src="`https://flagcdn.com/20x15/${r.code.toLowerCase()}.png`" :alt="r.name" class="w-5 rounded-sm" />
                  <span class="font-medium text-[14px] text-[#313131]">{{ r.name }}</span>
                  <span class="ml-auto text-xs text-gray-400">{{ r.currency }}</span>
                </button>
              </div>
              <div class="mt-3 pt-3 border-t border-gray-100">
                <p class="font-semibold text-[13px] text-gray-500 mb-2">Langue</p>
                <LayoutLanguageSelector />
              </div>
            </div>
          </Transition>
        </div>

        <!-- Mobile hamburger -->
        <button @click="mobileOpen = !mobileOpen" class="md:hidden p-2 rounded-lg hover:bg-gray-100 text-gray-600 transition-colors">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path v-if="!mobileOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
    </div>

    <!-- Mobile menu -->
    <Transition
      enter-active-class="transition duration-200 ease-out"
      enter-from-class="opacity-0 -translate-y-2"
      enter-to-class="opacity-100 translate-y-0"
      leave-active-class="transition duration-150 ease-in"
      leave-from-class="opacity-100 translate-y-0"
      leave-to-class="opacity-0 -translate-y-2"
    >
      <div v-if="mobileOpen" class="md:hidden bg-white border-t border-gray-100 px-6 py-4 space-y-2">
        <LayoutLanguageSelector />
        <template v-if="isLoggedIn">
          <NuxtLink to="/account/favorites" @click="mobileOpen = false" class="block px-3 py-2.5 text-sm text-gray-700 rounded-lg hover:bg-gray-50">Favoris</NuxtLink>
          <NuxtLink to="/account/index" @click="mobileOpen = false" class="block px-3 py-2.5 text-sm text-gray-700 rounded-lg hover:bg-gray-50">Mon profil</NuxtLink>
          <NuxtLink to="/account/settings" @click="mobileOpen = false" class="block px-3 py-2.5 text-sm text-gray-700 rounded-lg hover:bg-gray-50">Paramètres</NuxtLink>
          <button @click="doLogout" class="w-full text-left block px-3 py-2.5 text-sm text-red-500 rounded-lg hover:bg-red-50">Déconnexion</button>
        </template>
        <template v-else>
          <button @click="showAuth = true; mobileOpen = false" class="w-full text-center px-3 py-2.5 bg-[#00878E] text-white text-sm font-semibold rounded-full hover:bg-[#006b70]">
            Connexion
          </button>
        </template>
      </div>
    </Transition>
  </nav>
</template>

<script setup lang="ts">
import { useFavoritesStore } from '~/stores/favorites'

const { user, isLoggedIn, logout } = useAuth()
const favorites = useFavoritesStore()
const region = useRegionStore()
const router = useRouter()
const route = useRoute()

const mobileOpen = ref(false)
const userDropOpen = ref(false)
const countryDropOpen = ref(false)
const showAuth = ref(false)
const searchCity = ref('')
const userDropRef = ref<HTMLElement>()
const countryDropRef = ref<HTMLElement>()

const isLandingPage = computed(() => route.path === '/')

const countries = [
  { code: 'FR', name: 'France', currency: 'EUR' },
  { code: 'TN', name: 'Tunisie', currency: 'TND' },
  { code: 'EG', name: 'Égypte', currency: 'EGP' },
  { code: 'CA', name: 'Canada', currency: 'CAD' },
]

function submitSearch() {
  if (searchCity.value.trim()) {
    router.push({ path: '/listings', query: { city: searchCity.value.trim() } })
    searchCity.value = ''
  }
}

function selectCountry(code: string) {
  region.setRegion(code)
  countryDropOpen.value = false
}

async function doLogout() {
  userDropOpen.value = false
  mobileOpen.value = false
  await logout()
  router.push('/')
}

onMounted(() => {
  document.addEventListener('click', (e: MouseEvent) => {
    if (userDropRef.value && !userDropRef.value.contains(e.target as Node)) userDropOpen.value = false
    if (countryDropRef.value && !countryDropRef.value.contains(e.target as Node)) countryDropOpen.value = false
  })
})
</script>

<style scoped>
@reference "tailwindcss";
.user-menu-item {
  @apply flex items-center gap-3 px-4 py-2.5 hover:bg-gray-50 cursor-pointer transition-colors text-sm font-medium text-[#313131];
}
</style>
