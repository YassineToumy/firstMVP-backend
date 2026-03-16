<template>
  <nav v-if="lastPage > 1" class="flex items-center justify-center gap-1 mt-10">
    <button
      @click="go(currentPage - 1)"
      :disabled="currentPage <= 1"
      class="page-btn"
    >
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
      </svg>
    </button>

    <template v-for="p in pages" :key="p">
      <span v-if="p === '...'" class="px-2 text-gray-400 text-xs">...</span>
      <button
        v-else
        @click="go(p as number)"
        class="page-btn w-10"
        :class="p === currentPage ? 'bg-[#00878E] text-white border-[#00878E] hover:bg-[#006b70]' : ''"
      >
        {{ p }}
      </button>
    </template>

    <button
      @click="go(currentPage + 1)"
      :disabled="currentPage >= lastPage"
      class="page-btn"
    >
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
      </svg>
    </button>
  </nav>
</template>

<script setup lang="ts">
const props = defineProps<{ currentPage: number; lastPage: number }>()
const emit = defineEmits<{ 'update:page': [page: number] }>()

function go(page: number) {
  if (page >= 1 && page <= props.lastPage) emit('update:page', page)
}

const pages = computed(() => {
  const items: (number | string)[] = []
  const c = props.currentPage, l = props.lastPage
  if (l <= 7) {
    for (let i = 1; i <= l; i++) items.push(i)
  } else {
    items.push(1)
    if (c > 3) items.push('...')
    for (let i = Math.max(2, c - 1); i <= Math.min(l - 1, c + 1); i++) items.push(i)
    if (c < l - 2) items.push('...')
    items.push(l)
  }
  return items
})
</script>

<style scoped>
@reference "tailwindcss";
.page-btn {
  @apply h-10 px-3 text-sm rounded-lg border border-gray-200 bg-white text-gray-700
    hover:bg-gray-50 hover:border-gray-300 disabled:opacity-30 disabled:cursor-not-allowed
    flex items-center justify-center transition-all duration-150 active:scale-95;
}
</style>