<template>
  <NuxtLayout>
    <NuxtPage />
  </NuxtLayout>
</template>

<script setup lang="ts">
import { useDarkMode } from '~/composables/useDarkMode'
import { useFavoritesStore } from '~/stores/favorites'
import { useLabels } from '~/composables/useLabels'

const darkMode = useDarkMode()
const favorites = useFavoritesStore()
const region = useRegionStore()
const { fetchUser } = useAuth()
const { locale } = useI18n()
const { loadLabels } = useLabels()

// Apply RTL direction when Arabic is selected (client-only)
// Reload labels when locale changes
watch(locale, (newLocale) => {
  if (import.meta.client) {
    document.documentElement.dir = newLocale === 'ar' ? 'rtl' : 'ltr'
    document.documentElement.lang = newLocale
    loadLabels()
  }
}, { immediate: true })

onMounted(() => {
  darkMode.init()
  favorites.init()
  region.init()
  fetchUser()
  loadLabels()
})
</script>
