<template>
  <div class="bg-white border-b border-gray-200 sticky top-[84px] z-40">
    <div class="max-w-[1440px] mx-auto px-8 py-4">
      <div class="flex items-center gap-3 flex-wrap">

        <!-- Type -->
        <div class="relative" ref="typeRef">
          <button
            @click.stop="toggle('type')"
            class="filter-pill" :class="{ 'active-pill': filters.property_type }"
          >
            {{ $t('filters.type') }}
            <Icon name="lucide:chevron-down" class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': open === 'type' }" />
          </button>
          <div v-if="open === 'type'" class="filter-dropdown w-[220px]">
            <button
              v-for="t in propertyTypes"
              :key="t.value"
              @click="filters.property_type = t.value; open = null; emit('filter')"
              class="dropdown-item" :class="{ 'selected-item': filters.property_type === t.value }"
            >{{ t.label }}</button>
          </div>
        </div>

        <!-- Rooms (bedrooms) -->
        <div class="relative" ref="roomsRef">
          <button @click.stop="toggle('rooms')" class="filter-pill" :class="{ 'active-pill': filters.bedrooms }">
            {{ $t('filters.rooms') }}
            <Icon name="lucide:chevron-down" class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': open === 'rooms' }" />
          </button>
          <div v-if="open === 'rooms'" class="filter-dropdown w-[260px]">
            <div class="mb-4">
              <div class="flex items-center justify-between">
                <span class="text-[15px] text-[#313131]">{{ $t('filters.bedrooms') }}</span>
                <div class="flex items-center gap-3">
                  <button @click="filters.bedrooms = Math.max(0, (filters.bedrooms || 0) - 1) || undefined; emit('filter')" class="counter-btn">
                    <Icon name="lucide:minus" class="w-4 h-4 text-gray-600" />
                  </button>
                  <span class="font-semibold text-[15px] text-[#313131] min-w-[24px] text-center">{{ filters.bedrooms || 0 }}</span>
                  <button @click="filters.bedrooms = (filters.bedrooms || 0) + 1; emit('filter')" class="counter-btn">
                    <Icon name="lucide:plus" class="w-4 h-4 text-gray-600" />
                  </button>
                </div>
              </div>
            </div>
            <button @click="open = null" class="w-full py-2.5 rounded-lg bg-[#00878E] hover:bg-[#006b70] font-semibold text-[14px] text-white transition-colors">
              {{ $t('filters.apply') }}
            </button>
          </div>
        </div>

        <!-- Price -->
        <div class="relative" ref="priceRef">
          <button @click.stop="toggle('price')" class="filter-pill" :class="{ 'active-pill': filters.min_price || filters.max_price }">
            {{ $t('filters.price') }}
            <Icon name="lucide:chevron-down" class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': open === 'price' }" />
          </button>
          <div v-if="open === 'price'" class="filter-dropdown w-[270px]">
            <label class="font-semibold text-[13px] text-gray-500 mb-3 block">{{ $t('filters.monthlyPrice') }}</label>
            <div class="flex items-center gap-3 mb-5">
              <input v-model.number="filters.min_price" @change="emit('filter')" type="number" placeholder="Min" class="filter-input" />
              <span class="text-gray-400">–</span>
              <input v-model.number="filters.max_price" @change="emit('filter')" type="number" placeholder="Max" class="filter-input" />
            </div>
            <button @click="open = null" class="w-full py-2.5 rounded-lg bg-[#00878E] hover:bg-[#006b70] font-semibold text-[14px] text-white transition-colors">
              {{ $t('filters.apply') }}
            </button>
          </div>
        </div>

        <!-- City -->
        <div class="relative">
          <Icon name="lucide:map-pin" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
          <input
            v-model="filters.city"
            @keyup.enter="emit('filter')"
            @change="emit('filter')"
            type="text"
            :placeholder="$t('filters.cityPlaceholder')"
            class="filter-pill pl-9 pr-4 focus:border-[#00878E] outline-none"
          />
        </div>

        <!-- Furnished toggle -->
        <button
          @click="filters.furnished = filters.furnished ? null : true; emit('filter')"
          class="filter-pill transition-colors duration-300"
          :class="filters.furnished ? 'border-[#00878E] bg-[#00878E] text-white' : ''"
        >
          {{ $t('filters.furnished') }}
        </button>

        <div class="flex-1" />

        <!-- Sort -->
        <div class="relative" ref="sortRef">
          <button @click.stop="toggle('sort')" class="filter-pill">
            <Icon name="lucide:arrow-up-down" class="w-4 h-4" />
            {{ $t('filters.sortBy') }}
            <Icon name="lucide:chevron-down" class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': open === 'sort' }" />
          </button>
          <div v-if="open === 'sort'" class="filter-dropdown right-0 left-auto w-[220px]">
            <button
              v-for="s in sortOptions"
              :key="s.value"
              @click="filters.sort = s.value; open = null; emit('filter')"
              class="dropdown-item" :class="{ 'selected-item': filters.sort === s.value }"
            >{{ s.label }}</button>
          </div>
        </div>

        <!-- Reset -->
        <button @click="reset" class="text-sm text-gray-400 hover:text-[#00878E] px-3 py-1.5 rounded-full hover:bg-[#00878E]/5 transition-all">
          {{ $t('filters.reset') }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import type { ListingFilters } from '~/composables/useListings'

const filters = defineModel<ListingFilters>('filters', { required: true })
const emit = defineEmits<{ filter: [] }>()
const { t } = useI18n()

const open = ref<string | null>(null)
const typeRef = ref<HTMLElement>()
const roomsRef = ref<HTMLElement>()
const priceRef = ref<HTMLElement>()
const sortRef = ref<HTMLElement>()

const propertyTypes = computed(() => [
  { value: '', label: t('filters.allTypes') },
  { value: 'apartment', label: t('filters.apartment') },
  { value: 'house', label: t('filters.house') },
  { value: 'villa', label: t('filters.villa') },
  { value: 'studio', label: t('filters.studio') },
])

const sortOptions = computed(() => [
  { value: '', label: t('filters.newest') },
  { value: 'price_asc', label: t('filters.priceAsc') },
  { value: 'price_desc', label: t('filters.priceDesc') },
])

function toggle(name: string) {
  open.value = open.value === name ? null : name
}

function reset() {
  filters.value = { property_type: '', min_price: undefined, max_price: undefined, bedrooms: undefined, furnished: null, city: '', sort: '', page: 1 }
  emit('filter')
}

onMounted(() => {
  document.addEventListener('click', () => { open.value = null })
})
</script>

<style scoped>
@reference "tailwindcss";
.filter-pill {
  @apply flex items-center gap-2 px-4 py-2 rounded-full border border-gray-300 bg-white hover:border-[#00878E] transition-colors duration-300 font-medium text-[13px] text-[#313131] cursor-pointer;
}
.active-pill {
  @apply border-[#00878E] text-[#00878E];
}
.filter-dropdown {
  @apply absolute top-full left-0 mt-2 bg-white rounded-2xl shadow-[0_4px_24px_rgba(0,0,0,0.12)] p-4 border border-gray-100 z-50;
}
.dropdown-item {
  @apply w-full text-left px-3 py-2 rounded-lg font-medium text-sm transition-colors hover:bg-gray-100 text-[#313131];
}
.selected-item {
  @apply bg-[#00878E] text-white hover:bg-[#006b70];
}
.counter-btn {
  @apply w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 flex items-center justify-center transition-colors;
}
.filter-input {
  @apply w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-[#00878E];
}
</style>
