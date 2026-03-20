export default defineNuxtPlugin(async (nuxtApp) => {
  const region = useRegionStore()
  region.init()

  // Restore saved locale from localStorage on every page load.
  // Use nuxtApp.$i18n directly — useI18n() composable is not safe to call in plugins.
  try {
    const saved = localStorage.getItem('locale')
    if (saved) {
      const i18n = nuxtApp.$i18n as any
      if (i18n && saved !== i18n.locale.value) {
        await i18n.setLocale(saved)
      }
    }
  } catch {}
})
