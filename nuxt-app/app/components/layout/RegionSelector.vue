<template>
  <div class="relative" ref="wrapper">
    <button
      @click="open = !open"
      class="flex items-center gap-2 text-sm px-3 py-1.5 rounded-lg border border-gray-200 hover:border-[#00878E]/40 hover:bg-[#00878E]/8 transition-all duration-200"
    >
      <span class="text-gray-700">{{ regionFlag }} {{ region.current.name }}</span>
      <svg
        class="w-3.5 h-3.5 text-gray-400 transition-transform duration-200"
        :class="{ 'rotate-180': open }"
        fill="none" stroke="currentColor" viewBox="0 0 24 24"
      >
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
      </svg>
    </button>

    <Transition
      enter-active-class="transition duration-150 ease-out"
      enter-from-class="opacity-0 scale-95 -translate-y-1"
      enter-to-class="opacity-100 scale-100 translate-y-0"
      leave-active-class="transition duration-100 ease-in"
      leave-from-class="opacity-100 scale-100 translate-y-0"
      leave-to-class="opacity-0 scale-95 -translate-y-1"
    >
      <div v-if="open" class="absolute top-full left-0 mt-2 w-52 bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden z-50">
        <div class="p-1">
          <button
            v-for="r in regions"
            :key="r.code"
            @click="select(r.code)"
            class="w-full text-left px-3 py-2.5 text-sm rounded-lg flex items-center justify-between transition-all duration-150"
            :class="r.code === region.currentCode
              ? 'text-[#006b70] bg-[#00878E]/8 font-medium'
              : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'"
          >
            <span class="flex items-center gap-2.5">
              <span>{{ flags[r.code] || '' }}</span>
              <span>{{ r.name }}</span>
            </span>
            <span class="text-[10px] text-gray-400 font-mono">{{ r.currency }}</span>
          </button>
        </div>
      </div>
    </Transition>
  </div>
</template>

<script setup lang="ts">
import { REGIONS } from '~/stores/region'

const region = useRegionStore()
const regions = REGIONS
const open = ref(false)
const wrapper = ref<HTMLElement>()

const flags: Record<string, string> = {
  fr: '🇫🇷',
  tn: '🇹🇳',
  eg: '🇪🇬',
  ca: '🇨🇦',
}

const regionFlag = computed(() => flags[region.currentCode] || '')

function select(code: string) {
  region.setRegion(code)
  open.value = false
}

onMounted(() => {
  document.addEventListener('click', (e: MouseEvent) => {
    if (wrapper.value && !wrapper.value.contains(e.target as Node)) {
      open.value = false
    }
  })
})
</script>
