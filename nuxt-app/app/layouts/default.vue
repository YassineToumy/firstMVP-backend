<template>
  <div class="min-h-screen flex flex-col" :dir="dir">
    <LayoutAppHeader />
    <main class="flex-1">
      <slot />
    </main>
    <LayoutAppFooter />
  </div>
</template>

<script setup lang="ts">
const region = useRegionStore()
region.init()

const { locale, locales } = useI18n()
const dir = computed(() => {
  const loc = (locales.value as { code: string; dir?: string }[]).find(l => l.code === locale.value)
  return loc?.dir ?? 'ltr'
})
</script>