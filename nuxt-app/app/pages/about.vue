<template>
  <div>
    <!-- Hero -->
    <section class="bg-gradient-to-br from-[#00878E] to-[#006b70] text-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24">
        <div class="max-w-3xl">
          <span class="inline-block text-xs font-semibold uppercase tracking-widest text-white/80 mb-3">
            {{ $t('about.badge') }}
          </span>
          <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold leading-tight">
            {{ $t('about.title') }}
          </h1>
          <p class="mt-5 text-white/80 text-sm sm:text-base leading-relaxed max-w-2xl">
            {{ $t('about.subtitle') }}
          </p>
        </div>
      </div>
    </section>

    <!-- Mission -->
    <section class="bg-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-20">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
          <div>
            <span class="inline-block text-xs font-semibold uppercase tracking-widest text-[#00878E] mb-3">{{ $t('about.missionBadge') }}</span>
            <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 leading-tight">
              {{ $t('about.missionTitle') }}
            </h2>
            <p class="mt-4 text-gray-500 leading-relaxed text-sm sm:text-base">
              {{ $t('about.missionDesc1') }}
            </p>
            <p class="mt-3 text-gray-500 leading-relaxed text-sm sm:text-base">
              {{ $t('about.missionDesc2', { count: 4 }) }}
            </p>
          </div>

          <!-- Stats -->
          <div class="grid grid-cols-2 gap-4">
            <div v-for="stat in stats" :key="stat.key" class="bg-[#FAFAFA] border border-gray-100 rounded-2xl p-6 text-center hover:border-[#00878E]/30 transition-all">
              <p class="text-3xl font-bold text-[#00878E]">{{ stat.value }}</p>
              <p class="text-sm text-gray-500 mt-1">{{ $t(stat.labelKey) }}</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Who we connect -->
    <section class="bg-[#FAFAFA]">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-20">
        <div class="text-center mb-12">
          <h2 class="text-2xl font-bold text-gray-900">{{ $t('about.whoWeConnect') }}</h2>
          <p class="text-gray-500 text-sm mt-2 max-w-md mx-auto">
            {{ $t('about.whoWeConnectDesc') }}
          </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
          <div v-for="actor in actors" :key="actor.titleKey" class="bg-white border border-gray-100 rounded-2xl p-7 text-center hover:border-[#00878E]/30 hover:shadow-sm transition-all duration-300 group">
            <div class="w-14 h-14 mx-auto rounded-2xl bg-[#00878E]/10 border-2 border-[#00878E]/20 flex items-center justify-center mb-4 group-hover:bg-[#00878E]/20 group-hover:border-[#00878E]/50 transition-all">
              <Icon :name="actor.icon" class="w-7 h-7 text-[#00878E]" />
            </div>
            <h3 class="font-bold text-gray-900 group-hover:text-[#00878E] transition-colors">{{ $t(actor.titleKey) }}</h3>
            <p class="text-sm text-gray-500 mt-2 leading-relaxed">{{ $t(actor.descKey) }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Languages -->
    <section class="bg-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-20">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
          <!-- Language cards -->
          <div class="grid grid-cols-2 gap-4">
            <div
              v-for="lang in languages"
              :key="lang.code"
              class="bg-[#FAFAFA] border border-gray-100 rounded-2xl p-5 flex items-center gap-3 hover:border-[#00878E]/30 hover:shadow-sm transition-all duration-200"
            >
              <img :src="`https://flagcdn.com/40x30/${lang.cc}.png`" :alt="$t(lang.nameKey)" class="w-10 h-auto rounded" />
              <div>
                <p class="font-semibold text-gray-900 text-sm">{{ $t(lang.nameKey) }}</p>
                <p class="text-xs text-gray-400">{{ $t(lang.countriesKey) }}</p>
              </div>
            </div>
          </div>

          <div>
            <span class="inline-block text-xs font-semibold uppercase tracking-widest text-[#00878E] mb-3">{{ $t('about.multilingual') }}</span>
            <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 leading-tight">
              {{ $t('about.multilingualTitle') }}
            </h2>
            <p class="mt-4 text-gray-500 leading-relaxed text-sm sm:text-base">
              {{ $t('about.multilingualDesc1') }}
            </p>
            <p class="mt-3 text-gray-500 leading-relaxed text-sm sm:text-base">
              {{ $t('about.multilingualDesc2') }}
            </p>
            <div class="mt-6 flex flex-wrap gap-2">
              <span
                v-for="lang in languages"
                :key="lang.code"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-[#00878E]/10 text-[#006b70] text-xs font-medium rounded-full border border-[#00878E]/20"
              >
                <img :src="`https://flagcdn.com/20x15/${lang.cc}.png`" :alt="$t(lang.nameKey)" class="w-4 rounded-sm" />
                {{ $t(lang.nameKey) }}
              </span>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Values -->
    <section class="bg-[#FAFAFA]">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-20">
        <div class="text-center mb-12">
          <h2 class="text-2xl font-bold text-gray-900">{{ $t('about.ourValues') }}</h2>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
          <div v-for="value in values" :key="value.titleKey" class="bg-white border border-gray-100 rounded-2xl p-6 hover:border-[#00878E]/30 transition-all">
            <div class="w-10 h-10 rounded-xl bg-[#00878E]/10 flex items-center justify-center mb-3">
              <Icon :name="value.icon" class="w-5 h-5 text-[#00878E]" />
            </div>
            <h3 class="font-semibold text-gray-900 mb-2">{{ $t(value.titleKey) }}</h3>
            <p class="text-sm text-gray-500 leading-relaxed">{{ $t(value.descKey) }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA -->
    <section class="bg-[#00878E]">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14 text-center">
        <h2 class="text-2xl font-bold text-white">{{ $t('about.startExploring') }}</h2>
        <p class="mt-2 text-white/80 text-sm max-w-sm mx-auto">
          {{ $t('about.startExploringDesc') }}
        </p>
        <div class="mt-6 flex flex-col sm:flex-row gap-3 justify-center">
          <NuxtLink
            to="/listings"
            class="inline-flex items-center justify-center gap-2 px-8 py-3.5 bg-white text-[#006b70] text-sm font-semibold rounded-xl hover:bg-[#00878E]/10 transition-colors"
          >
            {{ $t('about.browseListings') }}
          </NuxtLink>
          <NuxtLink
            to="/contact"
            class="inline-flex items-center justify-center gap-2 px-8 py-3.5 border-2 border-white/40 text-white text-sm font-medium rounded-xl hover:bg-white/10 transition-colors"
          >
            {{ $t('about.contactUs') }}
          </NuxtLink>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup lang="ts">
const stats = [
  { key: 'countries', value: '4', labelKey: 'about.stat1' },
  { key: 'languages', value: '4', labelKey: 'about.stat2' },
  { key: 'listings', value: '10k+', labelKey: 'about.stat3' },
  { key: 'free', value: '100%', labelKey: 'about.stat4' },
]

const actors = [
  { icon: 'lucide:user', titleKey: 'about.actor1Title', descKey: 'about.actor1Desc' },
  { icon: 'lucide:building-2', titleKey: 'about.actor2Title', descKey: 'about.actor2Desc' },
  { icon: 'lucide:briefcase', titleKey: 'about.actor3Title', descKey: 'about.actor3Desc' },
]

const languages = [
  { code: 'fr', cc: 'fr', nameKey: 'about.langFrName', countriesKey: 'about.langFrCountries' },
  { code: 'ar', cc: 'sa', nameKey: 'about.langArName', countriesKey: 'about.langArCountries' },
  { code: 'en', cc: 'gb', nameKey: 'about.langEnName', countriesKey: 'about.langEnCountries' },
  { code: 'es', cc: 'es', nameKey: 'about.langEsName', countriesKey: 'about.langEsCountries' },
]

const values = [
  { icon: 'lucide:globe', titleKey: 'about.val1Title', descKey: 'about.val1Desc' },
  { icon: 'lucide:search', titleKey: 'about.val2Title', descKey: 'about.val2Desc' },
  { icon: 'lucide:zap', titleKey: 'about.val3Title', descKey: 'about.val3Desc' },
  { icon: 'lucide:handshake', titleKey: 'about.val4Title', descKey: 'about.val4Desc' },
]

useHead({ title: 'About Us — RentGlobe' })
</script>
