export default defineNuxtPlugin(async () => {
  const region = useRegionStore()
  region.init()

  // Restore saved locale from cookie on every page load
  const localeCookie = useCookie('i18n_locale')
  if (localeCookie.value) {
    const { setLocale, locale } = useI18n()
    if (localeCookie.value !== locale.value) {
      await setLocale(localeCookie.value as any)
    }
  }
})
