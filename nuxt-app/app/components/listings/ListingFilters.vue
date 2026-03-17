<template>
  <div class="bg-white border-b border-gray-200 sticky top-[84px] z-40">
    <div class="max-w-[1440px] mx-auto px-8 py-4">
      <div class="flex items-center gap-3 flex-wrap">

        <!-- Search bar -->
        <div class="relative">
          <Icon name="lucide:search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
          <input
            v-model="filters.city"
            @keyup.enter="emit('filter')"
            type="text"
            :placeholder="$t('filters.cityPlaceholder')"
            class="filter-pill pl-9 pr-4 focus:border-[#00878E] outline-none w-[200px]"
            :class="{ 'active-pill': filters.city }"
          />
        </div>

        <!-- Type -->
        <div class="relative" ref="typeRef">
          <button
            @click.stop="toggle('type')"
            class="filter-pill" :class="{ 'active-pill': filters.property_type }"
          >
            {{ filters.property_type ? propertyTypes.find(t => t.value === filters.property_type)?.label : $t('filters.type') }}
            <Icon name="lucide:chevron-down" class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': open === 'type' }" />
          </button>
          <div v-if="open === 'type'" class="filter-dropdown w-[220px]" @click.stop>
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
            {{ filters.bedrooms ? `${filters.bedrooms}+ ${$t('filters.bedrooms')}` : $t('filters.rooms') }}
            <Icon name="lucide:chevron-down" class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': open === 'rooms' }" />
          </button>
          <div v-if="open === 'rooms'" class="filter-dropdown w-[260px]" @click.stop>
            <div class="mb-4">
              <div class="flex items-center justify-between">
                <span class="text-[15px] text-[#313131]">{{ $t('filters.bedrooms') }}</span>
                <div class="flex items-center gap-3">
                  <button @click="filters.bedrooms = (filters.bedrooms || 0) > 0 ? (filters.bedrooms! - 1) || undefined : undefined" class="counter-btn">
                    <Icon name="lucide:minus" class="w-4 h-4 text-gray-600" />
                  </button>
                  <span class="font-semibold text-[15px] text-[#313131] min-w-[24px] text-center">{{ filters.bedrooms || 0 }}</span>
                  <button @click="filters.bedrooms = (filters.bedrooms || 0) + 1" class="counter-btn">
                    <Icon name="lucide:plus" class="w-4 h-4 text-gray-600" />
                  </button>
                </div>
              </div>
            </div>
            <button @click="open = null; emit('filter')" class="w-full py-2.5 rounded-lg bg-[#00878E] hover:bg-[#006b70] font-semibold text-[14px] text-white transition-colors">
              {{ $t('filters.apply') }}
            </button>
          </div>
        </div>

        <!-- Price -->
        <div class="relative" ref="priceRef">
          <button @click.stop="toggle('price')" class="filter-pill" :class="{ 'active-pill': filters.min_price || filters.max_price }">
            {{ priceLabel }}
            <Icon name="lucide:chevron-down" class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': open === 'price' }" />
          </button>
          <div v-if="open === 'price'" class="filter-dropdown w-[270px]" @click.stop>
            <label class="font-semibold text-[13px] text-gray-500 mb-3 block">{{ $t('filters.monthlyPrice') }}</label>
            <div class="flex items-center gap-3 mb-4">
              <input v-model.number="localMinPrice" type="number" placeholder="Min" class="filter-input" />
              <span class="text-gray-400">–</span>
              <input v-model.number="localMaxPrice" type="number" placeholder="Max" class="filter-input" />
            </div>
            <button @click="applyPrice" class="w-full py-2.5 rounded-lg bg-[#00878E] hover:bg-[#006b70] font-semibold text-[14px] text-white transition-colors">
              {{ $t('filters.apply') }}
            </button>
          </div>
        </div>

        <!-- Surface -->
        <div class="relative" ref="surfaceRef">
          <button @click.stop="toggle('surface')" class="filter-pill" :class="{ 'active-pill': filters.min_surface || filters.max_surface }">
            {{ surfaceLabel }}
            <Icon name="lucide:chevron-down" class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': open === 'surface' }" />
          </button>
          <div v-if="open === 'surface'" class="filter-dropdown w-[270px]" @click.stop>
            <label class="font-semibold text-[13px] text-gray-500 mb-3 block">{{ $t('filters.surface') }} (m²)</label>
            <div class="flex items-center gap-3 mb-4">
              <input v-model.number="localMinSurface" type="number" placeholder="Min" class="filter-input" />
              <span class="text-gray-400">–</span>
              <input v-model.number="localMaxSurface" type="number" placeholder="Max" class="filter-input" />
            </div>
            <button @click="applySurface" class="w-full py-2.5 rounded-lg bg-[#00878E] hover:bg-[#006b70] font-semibold text-[14px] text-white transition-colors">
              {{ $t('filters.apply') }}
            </button>
          </div>
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
            {{ filters.sort ? sortOptions.find(s => s.value === filters.sort)?.label : $t('filters.sortBy') }}
            <Icon name="lucide:chevron-down" class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': open === 'sort' }" />
          </button>
          <div v-if="open === 'sort'" class="filter-dropdown right-0 left-auto w-[220px]" @click.stop>
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
const surfaceRef = ref<HTMLElement>()
const sortRef = ref<HTMLElement>()

// Local state for price/surface inputs (apply only on button click)
const localMinPrice = ref<number | undefined>(filters.value.min_price)
const localMaxPrice = ref<number | undefined>(filters.value.max_price)
const localMinSurface = ref<number | undefined>(filters.value.min_surface)
const localMaxSurface = ref<number | undefined>(filters.value.max_surface)

watch(open, (val) => {
  if (val === 'price') {
    localMinPrice.value = filters.value.min_price
    localMaxPrice.value = filters.value.max_price
  }
  if (val === 'surface') {
    localMinSurface.value = filters.value.min_surface
    localMaxSurface.value = filters.value.max_surface
  }
})

function applyPrice() {
  filters.value.min_price = localMinPrice.value || undefined
  filters.value.max_price = localMaxPrice.value || undefined
  open.value = null
  emit('filter')
}

function applySurface() {
  filters.value.min_surface = localMinSurface.value || undefined
  filters.value.max_surface = localMaxSurface.value || undefined
  open.value = null
  emit('filter')
}

const priceLabel = computed(() => {
  if (filters.value.min_price && filters.value.max_price)
    return `${filters.value.min_price} – ${filters.value.max_price}`
  if (filters.value.min_price) return `≥ ${filters.value.min_price}`
  if (filters.value.max_price) return `≤ ${filters.value.max_price}`
  return t('filters.price')
})

const surfaceLabel = computed(() => {
  if (filters.value.min_surface && filters.value.max_surface)
    return `${filters.value.min_surface} – ${filters.value.max_surface} m²`
  if (filters.value.min_surface) return `≥ ${filters.value.min_surface} m²`
  if (filters.value.max_surface) return `≤ ${filters.value.max_surface} m²`
  return t('filters.surface')
})

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
  filters.value = { property_type: '', min_price: undefined, max_price: undefined, min_surface: undefined, max_surface: undefined, bedrooms: undefined, furnished: null, city: '', sort: '', page: 1 }
  localMinPrice.value = undefined
  localMaxPrice.value = undefined
  localMinSurface.value = undefined
  localMaxSurface.value = undefined
  open.value = null
  emit('filter')
}

onMounted(() => {
  document.addEventListener('click', (e: MouseEvent) => {
    const refs = [typeRef.value, roomsRef.value, priceRef.value, surfaceRef.value, sortRef.value]
    const clickedInside = refs.some(r => r?.contains(e.target as Node))
    if (!clickedInside) open.value = null
  })
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