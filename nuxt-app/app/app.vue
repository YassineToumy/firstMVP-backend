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
const { fetchUser } = useAuth()
const { locale, locales } = useI18n()
const { loadLabels } = useLabels()

const currentDir = computed((): 'ltr' | 'rtl' | 'auto' => {
  const loc = (locales.value as { code: string; dir?: string }[]).find(l => l.code === locale.value)
  const dir = loc?.dir
  if (dir === 'rtl' || dir === 'auto') return dir
  return 'ltr'
})

useHead({
  htmlAttrs: {
    lang: locale,
    dir: currentDir,
  },
})

watch(locale, () => {
  loadLabels()
})

onMounted(() => {
  darkMode.init()
  favorites.init()
  fetchUser()
  loadLabels()
})
</script>
