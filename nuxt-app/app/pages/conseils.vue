<template>
  <div>
    <!-- Hero -->
    <section class="bg-gradient-to-br from-[#00878E] to-[#006b70] text-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-20">
        <div class="max-w-2xl">
          <span class="inline-block text-xs font-semibold uppercase tracking-widest text-white/80 mb-3">
            {{ $t('tips.heroBadge') }}
          </span>
          <h1 class="text-3xl sm:text-4xl font-bold leading-tight">
            {{ $t('tips.heroTitle') }}
          </h1>
          <p class="mt-4 text-white/80 text-sm sm:text-base leading-relaxed">
            {{ $t('tips.heroSubtitle') }}
          </p>
        </div>
      </div>
    </section>

    <!-- Tips section -->
    <section class="bg-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14 sm:py-20">
        <div class="text-center mb-12">
          <h2 class="text-2xl font-bold text-gray-900">{{ $t('tips.tipsTitle') }}</h2>
          <p class="text-gray-500 text-sm mt-2 max-w-md mx-auto">
            {{ $t('tips.tipsSubtitle') }}
          </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
          <div v-for="tip in tips" :key="tip.titleKey" class="bg-[#FAFAFA] border border-gray-100 rounded-2xl p-6 hover:border-[#00878E]/30 hover:shadow-sm transition-all duration-300">
            <div class="w-10 h-10 rounded-xl bg-[#00878E]/8 flex items-center justify-center mb-4">
              <Icon :name="tip.icon" class="w-5 h-5 text-[#00878E]" />
            </div>
            <h3 class="font-semibold text-gray-900 mb-2">{{ $t(tip.titleKey) }}</h3>
            <p class="text-sm text-gray-500 leading-relaxed">{{ $t(tip.descKey) }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Rental types -->
    <section class="bg-[#FAFAFA]">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14 sm:py-20">
        <div class="text-center mb-12">
          <h2 class="text-2xl font-bold text-gray-900">{{ $t('tips.typesTitle') }}</h2>
          <p class="text-gray-500 text-sm mt-2 max-w-md mx-auto">
            {{ $t('tips.typesSubtitle') }}
          </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
          <div
            v-for="type in rentalTypes"
            :key="type.nameKey"
            class="bg-white border border-gray-100 rounded-2xl p-6 hover:border-[#00878E]/30 hover:shadow-sm transition-all duration-300"
          >
            <div class="flex items-start gap-4">
              <div class="w-11 h-11 rounded-xl bg-[#00878E]/8 border border-[#00878E]/20 flex items-center justify-center flex-shrink-0">
                <Icon :name="type.icon" class="w-6 h-6 text-[#00878E]" />
              </div>
              <div>
                <div class="flex items-center gap-2 mb-1">
                  <h3 class="font-semibold text-gray-900">{{ $t(type.nameKey) }}</h3>
                  <span class="text-[11px] px-2 py-0.5 rounded-full bg-[#00878E]/8 text-[#006b70] font-medium border border-[#00878E]/20">
                    {{ $t(type.badgeKey) }}
                  </span>
                </div>
                <p class="text-sm text-gray-500 leading-relaxed">{{ $t(type.descKey) }}</p>
                <ul class="mt-3 space-y-1">
                  <li v-for="pointKey in type.pointKeys" :key="pointKey" class="flex items-start gap-2 text-xs text-gray-500">
                    <svg class="w-3.5 h-3.5 text-[#00878E] flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                    {{ $t(pointKey) }}
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Legal terms -->
    <section class="bg-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14 sm:py-20">
        <div class="text-center mb-12">
          <h2 class="text-2xl font-bold text-gray-900">{{ $t('tips.legalTitle') }}</h2>
          <p class="text-gray-500 text-sm mt-2 max-w-md mx-auto">
            {{ $t('tips.legalSubtitle') }}
          </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
          <div
            v-for="term in legalTerms"
            :key="term.wordKey"
            class="border border-gray-100 rounded-2xl p-5 hover:border-[#00878E]/30 hover:bg-[#00878E]/8/30 transition-all duration-200 group"
          >
            <div class="flex items-start justify-between gap-2 mb-2">
              <h3 class="font-semibold text-gray-900 group-hover:text-[#006b70] transition-colors">
                {{ $t(term.wordKey) }}
              </h3>
              <span class="text-[10px] px-2 py-0.5 rounded-full bg-gray-100 text-gray-500 font-medium whitespace-nowrap">
                {{ $t(term.categoryKey) }}
              </span>
            </div>
            <p class="text-sm text-gray-500 leading-relaxed">{{ $t(term.defKey) }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA -->
    <section class="bg-[#00878E]">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14 text-center">
        <h2 class="text-2xl font-bold text-white">{{ $t('tips.ctaTitle') }}</h2>
        <p class="mt-2 text-white/80 text-sm max-w-sm mx-auto">
          {{ $t('tips.ctaDesc') }}
        </p>
        <NuxtLink
          to="/listings"
          class="mt-6 inline-flex items-center gap-2 px-8 py-3.5 bg-white text-[#006b70] text-sm font-semibold rounded-xl hover:bg-[#00878E]/8 transition-colors"
        >
          {{ $t('tips.ctaBrowse') }}
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
          </svg>
        </NuxtLink>
      </div>
    </section>
  </div>
</template>

<script setup lang="ts">
const tips = [
  { icon: 'lucide:search', titleKey: 'tips.tip1Title', descKey: 'tips.tip1Desc' },
  { icon: 'lucide:file-text', titleKey: 'tips.tip2Title', descKey: 'tips.tip2Desc' },
  { icon: 'lucide:euro', titleKey: 'tips.tip3Title', descKey: 'tips.tip3Desc' },
  { icon: 'lucide:camera', titleKey: 'tips.tip4Title', descKey: 'tips.tip4Desc' },
  { icon: 'lucide:landmark', titleKey: 'tips.tip5Title', descKey: 'tips.tip5Desc' },
  { icon: 'lucide:handshake', titleKey: 'tips.tip6Title', descKey: 'tips.tip6Desc' },
]

const rentalTypes = [
  {
    icon: 'lucide:sofa',
    nameKey: 'tips.type1Name',
    badgeKey: 'tips.type1Badge',
    descKey: 'tips.type1Desc',
    pointKeys: ['tips.type1Point1', 'tips.type1Point2', 'tips.type1Point3'],
  },
  {
    icon: 'lucide:home',
    nameKey: 'tips.type2Name',
    badgeKey: 'tips.type2Badge',
    descKey: 'tips.type2Desc',
    pointKeys: ['tips.type2Point1', 'tips.type2Point2', 'tips.type2Point3'],
  },
  {
    icon: 'lucide:calendar',
    nameKey: 'tips.type3Name',
    badgeKey: 'tips.type3Badge',
    descKey: 'tips.type3Desc',
    pointKeys: ['tips.type3Point1', 'tips.type3Point2', 'tips.type3Point3'],
  },
  {
    icon: 'lucide:building-2',
    nameKey: 'tips.type4Name',
    badgeKey: 'tips.type4Badge',
    descKey: 'tips.type4Desc',
    pointKeys: ['tips.type4Point1', 'tips.type4Point2', 'tips.type4Point3'],
  },
]

const legalTerms = [
  { wordKey: 'tips.legal1Word', categoryKey: 'tips.legal1Category', defKey: 'tips.legal1Def' },
  { wordKey: 'tips.legal2Word', categoryKey: 'tips.legal2Category', defKey: 'tips.legal2Def' },
  { wordKey: 'tips.legal3Word', categoryKey: 'tips.legal3Category', defKey: 'tips.legal3Def' },
  { wordKey: 'tips.legal4Word', categoryKey: 'tips.legal4Category', defKey: 'tips.legal4Def' },
  { wordKey: 'tips.legal5Word', categoryKey: 'tips.legal5Category', defKey: 'tips.legal5Def' },
  { wordKey: 'tips.legal6Word', categoryKey: 'tips.legal6Category', defKey: 'tips.legal6Def' },
  { wordKey: 'tips.legal7Word', categoryKey: 'tips.legal7Category', defKey: 'tips.legal7Def' },
  { wordKey: 'tips.legal8Word', categoryKey: 'tips.legal8Category', defKey: 'tips.legal8Def' },
  { wordKey: 'tips.legal9Word', categoryKey: 'tips.legal9Category', defKey: 'tips.legal9Def' },
]

useHead({ title: 'Rental Tips — RentGlobe' })
</script>
