<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="flex flex-col md:flex-row gap-8">
      <LayoutAccountSidebar />

      <main class="flex-1 min-w-0">
        <div class="mb-6">
          <h1 class="text-2xl font-bold text-gray-900">{{ $t('settings.title') }}</h1>
          <p class="text-sm text-gray-500 mt-1">{{ $t('settings.subtitle') }}</p>
        </div>

        <div class="space-y-4 max-w-lg">
          <!-- Appearance -->
          <div class="bg-white rounded-2xl border border-gray-100 p-6">
            <h2 class="text-sm font-semibold text-gray-700 mb-4 flex items-center gap-2">
              <Icon name="lucide:palette" class="w-4 h-4 text-gray-400" />
              {{ $t('settings.appearance') }}
            </h2>

            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-gray-900">{{ $t('settings.darkMode') }}</p>
                <p class="text-xs text-gray-400 mt-0.5">{{ $t('settings.darkModeDesc') }}</p>
              </div>
              <!-- Toggle switch -->
              <button
                @click="darkMode.toggle()"
                :class="darkMode.isDark.value ? 'bg-[#00878E]' : 'bg-gray-200'"
                class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none"
              >
                <span
                  :class="darkMode.isDark.value ? 'translate-x-6' : 'translate-x-1'"
                  class="inline-block h-4 w-4 transform rounded-full bg-white shadow transition-transform"
                />
              </button>
            </div>
          </div>

          <!-- Language -->
          <div class="bg-white rounded-2xl border border-gray-100 p-6">
            <h2 class="text-sm font-semibold text-gray-700 mb-4 flex items-center gap-2">
              <Icon name="lucide:globe" class="w-4 h-4 text-gray-400" />
              {{ $t('settings.language') }}
            </h2>

            <div class="grid grid-cols-2 gap-2">
              <button
                v-for="lang in languages"
                :key="lang.code"
                @click="setLocale(lang.code as 'fr' | 'en' | 'ar' | 'es')"
                :class="locale === lang.code
                  ? 'border-[#00878E] bg-[#00878E]/8 text-[#006b70]'
                  : 'border-gray-200 text-gray-600 hover:border-gray-300 hover:bg-gray-50'"
                class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl border text-sm font-medium transition-all"
              >
                <img :src="`https://flagcdn.com/20x15/${lang.cc}.png`" :alt="lang.label" class="w-5 rounded-sm" />
                {{ lang.label }}
              </button>
            </div>
          </div>

          <!-- Notifications -->
          <div class="bg-white rounded-2xl border border-gray-100 p-6">
            <h2 class="text-sm font-semibold text-gray-700 mb-4 flex items-center gap-2">
              <Icon name="lucide:bell" class="w-4 h-4 text-gray-400" />
              {{ $t('settings.notifications') }}
            </h2>

            <div class="space-y-4">
              <div v-for="notif in notifications" :key="notif.key" class="flex items-center justify-between">
                <div>
                  <p class="text-sm font-medium text-gray-900">{{ $t(notif.labelKey) }}</p>
                  <p class="text-xs text-gray-400 mt-0.5">{{ $t(notif.descKey) }}</p>
                </div>
                <button
                  @click="notif.value = !notif.value"
                  :class="notif.value ? 'bg-[#00878E]' : 'bg-gray-200'"
                  class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none shrink-0"
                >
                  <span
                    :class="notif.value ? 'translate-x-6' : 'translate-x-1'"
                    class="inline-block h-4 w-4 transform rounded-full bg-white shadow transition-transform"
                  />
                </button>
              </div>
            </div>
          </div>

          <!-- Danger zone -->
          <div class="bg-white rounded-2xl border border-red-100 p-6">
            <h2 class="text-sm font-semibold text-red-600 mb-4 flex items-center gap-2">
              <Icon name="lucide:alert-triangle" class="w-4 h-4" />
              {{ $t('settings.dangerZone') }}
            </h2>
            <p class="text-sm text-gray-500 mb-4">{{ $t('settings.dangerZoneDesc') }}</p>
            <button class="px-4 py-2 text-sm text-red-600 border border-red-200 rounded-xl hover:bg-red-50 transition-colors">
              {{ $t('settings.deleteAccount') }}
            </button>
          </div>
        </div>
      </main>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useDarkMode } from '~/composables/useDarkMode'

definePageMeta({ middleware: 'auth' })

const darkMode = useDarkMode()
const { locale, setLocale } = useI18n()

const languages = [
  { code: 'fr', cc: 'fr', label: 'Français' },
  { code: 'en', cc: 'gb', label: 'English' },
  { code: 'ar', cc: 'sa', label: 'العربية' },
  { code: 'es', cc: 'es', label: 'Español' },
]

const notifications = reactive([
  { key: 'new_listings', labelKey: 'settings.notif1Label', descKey: 'settings.notif1Desc', value: true },
  { key: 'price_drops', labelKey: 'settings.notif2Label', descKey: 'settings.notif2Desc', value: false },
  { key: 'newsletter', labelKey: 'settings.notif3Label', descKey: 'settings.notif3Desc', value: true },
])
</script>
