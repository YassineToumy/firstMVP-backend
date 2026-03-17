<template>
  <Teleport to="body">
    <Transition name="modal">
      <div v-if="modelValue" class="fixed inset-0 z-[110]">
        <!-- Overlay -->
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="$emit('update:modelValue', false)" />

        <!-- Modal -->
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-[600px] bg-white rounded-2xl shadow-2xl max-h-[85vh] flex flex-col">
          <!-- Header -->
          <div class="flex items-center justify-between p-6 border-b border-gray-200">
            <h2 class="font-bold text-[20px] text-[#313131]">{{ $t('preferences.title') }}</h2>
            <button @click="$emit('update:modelValue', false)" class="w-8 h-8 rounded-full hover:bg-gray-100 flex items-center justify-center transition-colors">
              <Icon name="lucide:x" class="w-5 h-5 text-gray-600" />
            </button>
          </div>

          <!-- Content -->
          <div class="overflow-y-auto flex-1 p-6">

            <!-- Country selection -->
            <div class="mb-6">
              <label class="block font-semibold text-[15px] text-[#313131] mb-3">{{ $t('preferences.country') }}</label>

              <button
                @click="countryOpen = !countryOpen"
                class="w-full flex items-center justify-between px-4 py-3 border border-gray-200 rounded-xl hover:border-[#00878E] transition-colors bg-white"
              >
                <div class="flex items-center gap-3">
                  <img v-if="tempRegion" :src="`https://flagcdn.com/20x15/${tempRegion.toLowerCase()}.png`" :alt="tempRegionName" class="w-6 h-auto rounded-sm" />
                  <span class="font-medium text-[14px] text-[#313131]">{{ tempRegionName }}</span>
                </div>
                <Icon name="lucide:chevron-down" class="w-4 h-4 text-gray-400 transition-transform" :class="{ 'rotate-180': countryOpen }" />
              </button>

              <div v-if="countryOpen" class="mt-2 border border-gray-200 rounded-xl bg-white shadow-lg overflow-hidden">
                <div class="p-2 border-b border-gray-200">
                  <div class="relative">
                    <Icon name="lucide:search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                    <input v-model="countrySearch" type="text" :placeholder="$t('preferences.searchCountry')" class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl text-[14px] focus:outline-none focus:border-[#00878E] transition-colors" />
                  </div>
                </div>
                <div class="max-h-[250px] overflow-y-auto p-2">
                  <button
                    v-for="r in filteredRegions"
                    :key="r.code"
                    @click="tempRegion = r.code; countryOpen = false; countrySearch = ''"
                    class="w-full flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 transition-colors"
                    :class="tempRegion === r.code ? 'bg-[#00878E]/10' : ''"
                  >
                    <img :src="`https://flagcdn.com/20x15/${r.code.toLowerCase()}.png`" :alt="r.name" class="w-6 h-auto rounded-sm" />
                    <span class="font-medium text-[14px] text-[#313131]">{{ r.name }}</span>
                    <div v-if="tempRegion === r.code" class="ml-auto w-5 h-5 rounded-full bg-[#00878E] flex items-center justify-center">
                      <Icon name="lucide:check" class="w-3 h-3 text-white" />
                    </div>
                  </button>
                </div>
              </div>
            </div>

            <!-- Language selection -->
            <div>
              <label class="block font-semibold text-[15px] text-[#313131] mb-3">{{ $t('preferences.language') }}</label>

              <button
                @click="langOpen = !langOpen"
                class="w-full flex items-center justify-between px-4 py-3 border border-gray-200 rounded-xl hover:border-[#00878E] transition-colors bg-white"
              >
                <span class="font-medium text-[14px] text-[#313131]">{{ currentLocaleName }}</span>
                <Icon name="lucide:chevron-down" class="w-4 h-4 text-gray-400 transition-transform" :class="{ 'rotate-180': langOpen }" />
              </button>

              <div v-if="langOpen" class="mt-2 border border-gray-200 rounded-xl bg-white shadow-lg overflow-hidden">
                <div class="max-h-[250px] overflow-y-auto p-2">
                  <button
                    v-for="loc in locales"
                    :key="loc.code"
                    @click="tempLocale = loc.code as any; langOpen = false"
                    class="w-full text-left px-4 py-2.5 rounded-lg hover:bg-gray-50 transition-colors flex items-center justify-between"
                    :class="tempLocale === loc.code ? 'bg-[#00878E]/10' : ''"
                  >
                    <span class="font-medium text-[14px] text-[#313131]">{{ loc.name }}</span>
                    <span v-if="tempLocale === loc.code" class="text-[#00878E] font-bold">✓</span>
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Footer -->
          <div class="p-6 border-t border-gray-200">
            <button @click="save" class="w-full bg-[#00878E] text-white py-3 px-6 rounded-xl font-semibold text-[15px] hover:bg-[#006b70] transition-colors">
              {{ $t('preferences.save') }}
            </button>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup lang="ts">
const props = defineProps<{ modelValue: boolean }>()
const emit = defineEmits<{ 'update:modelValue': [boolean] }>()

const region = useRegionStore()
const { locale, setLocale, locales: i18nLocales } = useI18n()

const countryOpen = ref(false)
const langOpen = ref(false)
const countrySearch = ref('')
const tempRegion = ref(region.currentCode || 'FR')
const tempLocale = ref(locale.value)

const allRegions = [
  { code: 'FR', name: 'France' },
  { code: 'TN', name: 'Tunisie' },
  { code: 'EG', name: 'Égypte' },
  { code: 'CA', name: 'Canada' },
]

const locales = computed(() =>
  (i18nLocales.value as { code: string; name: string }[]).map(l => ({ code: l.code, name: l.name }))
)

const filteredRegions = computed(() => {
  const q = countrySearch.value.toLowerCase()
  const sorted = [
    ...allRegions.filter(r => r.code === tempRegion.value),
    ...allRegions.filter(r => r.code !== tempRegion.value),
  ]
  return q ? sorted.filter(r => r.name.toLowerCase().includes(q) || r.code.toLowerCase().includes(q)) : sorted
})

const tempRegionName = computed(() => allRegions.find(r => r.code === tempRegion.value)?.name || tempRegion.value)
const currentLocaleName = computed(() => locales.value.find(l => l.code === tempLocale.value)?.name || tempLocale.value)

async function save() {
  region.setRegion(tempRegion.value)
  await setLocale(tempLocale.value as any)
  emit('update:modelValue', false)
}

watch(() => props.modelValue, (v) => {
  if (v) {
    tempRegion.value = region.currentCode || 'FR'
    tempLocale.value = locale.value
    countryOpen.value = false
    langOpen.value = false
    countrySearch.value = ''
  }
})
</script>

<style scoped>
.modal-enter-active, .modal-leave-active { transition: opacity 0.2s ease; }
.modal-enter-from, .modal-leave-to { opacity: 0; }
</style>
