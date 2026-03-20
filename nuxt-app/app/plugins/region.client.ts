export default defineNuxtPlugin(() => {
  const region = useRegionStore()
  region.init()
})
