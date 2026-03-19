export default defineNuxtPlugin(async () => {
  const region = useRegionStore()
  region.init()

  // Restore saved locale from localStorage on every page load.
  // We use localStorage (not i18n cookie) to avoid conflicts with detectBrowserLanguage.
  const saved = localStorage.getItem('locale')
  if (saved) {
    const { setLocale, locale } = useI18n()
    if (saved !== locale.value) {
      await setLocale(saved as any)
    }
  }
})
