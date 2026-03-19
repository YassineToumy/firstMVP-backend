<template>
  <div class="bg-white border-b border-gray-200 sticky top-[84px] z-40">
    <div class="max-w-[1440px] mx-auto px-8 py-3">
      <div class="flex items-center gap-2 flex-wrap">

        <!-- Search bar -->
        <div class="relative">
          <Icon name="lucide:search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" />
          <input
            v-model="filters.city"
            @keyup.enter="emit('filter')"
            type="text"
            :placeholder="$t('filters.cityPlaceholder')"
            class="filter-pill pl-9 pr-4 focus:border-[#00878E] outline-none w-[190px]"
            :class="{ 'active-pill': filters.city }"
          />
        </div>

        <!-- Type -->
        <div class="relative" ref="typeRef">
          <button type="button" @click="toggle('type')" class="filter-pill" :class="{ 'active-pill': filters.property_type }">
            {{ selectedTypeLabel }}
            <Icon name="lucide:chevron-down" class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': open === 'type' }" />
          </button>
          <div v-if="open === 'type'" class="filter-dropdown w-[200px]">
            <button type="button"
              @click="selectType('')"
              class="dropdown-item" :class="{ 'selected-item': !filters.property_type }"
            >{{ $t('filters.allTypes') }}</button>
            <button type="button"
              v-for="pt in propertyTypes"
              :key="pt.code"
              @click="selectType(pt.code)"
              class="dropdown-item" :class="{ 'selected-item': filters.property_type === pt.code }"
            >{{ pt.label }}</button>
          </div>
        </div>

        <!-- Rooms (bedrooms) -->
        <div class="relative" ref="roomsRef">
          <button type="button" @click="toggle('rooms')" class="filter-pill" :class="{ 'active-pill': filters.bedrooms }">
            {{ filters.bedrooms ? `${filters.bedrooms}+ ${$t('filters.bedrooms')}` : $t('filters.rooms') }}
            <Icon name="lucide:chevron-down" class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': open === 'rooms' }" />
          </button>
          <div v-if="open === 'rooms'" class="filter-dropdown w-[240px]">
            <div class="mb-4">
              <div class="flex items-center justify-between">
                <span class="text-[14px] text-[#313131] font-medium">{{ $t('filters.bedrooms') }}</span>
                <div class="flex items-center gap-2">
                  <button type="button" @click.stop="decBedrooms" class="counter-btn">
                    <Icon name="lucide:minus" class="w-3.5 h-3.5 text-gray-600" />
                  </button>
                  <span class="font-bold text-[15px] text-[#313131] min-w-[20px] text-center">{{ filters.bedrooms || 0 }}</span>
                  <button type="button" @click.stop="incBedrooms" class="counter-btn">
                    <Icon name="lucide:plus" class="w-3.5 h-3.5 text-gray-600" />
                  </button>
                </div>
              </div>
            </div>
            <button type="button" @click="applyRooms" class="apply-btn">{{ $t('filters.apply') }}</button>
          </div>
        </div>

        <!-- Price -->
        <div class="relative" ref="priceRef">
          <button type="button" @click="toggle('price')" class="filter-pill" :class="{ 'active-pill': filters.min_price || filters.max_price }">
            {{ priceLabel }}
            <Icon name="lucide:chevron-down" class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': open === 'price' }" />
          </button>
          <div v-if="open === 'price'" class="filter-dropdown w-[260px]" @mousedown.stop @click.stop>
            <label class="font-semibold text-[12px] text-gray-400 mb-3 block uppercase tracking-wide">{{ $t('filters.price') }}</label>
            <div class="flex items-center gap-2 mb-4">
              <input
                v-model.number="localMinPrice"
                type="number"
                min="0"
                placeholder="Min"
                class="filter-input"
                @keyup.enter="applyPrice"
              />
              <span class="text-gray-300 font-bold">–</span>
              <input
                v-model.number="localMaxPrice"
                type="number"
                min="0"
                placeholder="Max"
                class="filter-input"
                @keyup.enter="applyPrice"
              />
            </div>
            <button type="button" @click="applyPrice" class="apply-btn">{{ $t('filters.apply') }}</button>
          </div>
        </div>

        <!-- Surface -->
        <div class="relative" ref="surfaceRef">
          <button type="button" @click="toggle('surface')" class="filter-pill" :class="{ 'active-pill': filters.min_surface || filters.max_surface }">
            {{ surfaceLabel }}
            <Icon name="lucide:chevron-down" class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': open === 'surface' }" />
          </button>
          <div v-if="open === 'surface'" class="filter-dropdown w-[260px]" @mousedown.stop @click.stop>
            <label class="font-semibold text-[12px] text-gray-400 mb-3 block uppercase tracking-wide">{{ $t('filters.surface') }} (m²)</label>
            <div class="flex items-center gap-2 mb-4">
              <input
                v-model.number="localMinSurface"
                type="number"
                min="0"
                placeholder="Min"
                class="filter-input"
                @keyup.enter="applySurface"
              />
              <span class="text-gray-300 font-bold">–</span>
              <input
                v-model.number="localMaxSurface"
                type="number"
                min="0"
                placeholder="Max"
                class="filter-input"
                @keyup.enter="applySurface"
              />
            </div>
            <button type="button" @click="applySurface" class="apply-btn">{{ $t('filters.apply') }}</button>
          </div>
        </div>

        <!-- Furnished -->
        <button
          type="button"
          @click="filters.furnished = filters.furnished ? null : true; emit('filter')"
          class="filter-pill transition-colors duration-300"
          :class="filters.furnished ? 'border-[#00878E] bg-[#00878E] text-white' : ''"
        >
          {{ $t('filters.furnished') }}
        </button>

        <div class="flex-1" />

        <!-- Sort -->
        <div class="relative" ref="sortRef">
          <button type="button" @click="toggle('sort')" class="filter-pill">
            <Icon name="lucide:arrow-up-down" class="w-4 h-4" />
            {{ filters.sort ? sortOptions.find(s => s.value === filters.sort)?.label : $t('filters.sortBy') }}
            <Icon name="lucide:chevron-down" class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': open === 'sort' }" />
          </button>
          <div v-if="open === 'sort'" class="filter-dropdown right-0 left-auto w-[200px]">
            <button type="button"
              v-for="s in sortOptions"
              :key="s.value"
              @click="filters.sort = s.value; open = null; emit('filter')"
              class="dropdown-item" :class="{ 'selected-item': filters.sort === s.value }"
            >{{ s.label }}</button>
          </div>
        </div>

        <!-- Reset -->
        <button type="button" v-if="hasActiveFilters" @click="reset" class="text-sm text-gray-400 hover:text-red-500 px-3 py-1.5 rounded-full hover:bg-red-50 transition-all flex items-center gap-1">
          <Icon name="lucide:x" class="w-3.5 h-3.5" />
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
const { t, locale } = useI18n()
const { fetchPropertyTypes } = useListings()

const open = ref<string | null>(null)
const typeRef    = ref<HTMLElement>()
const roomsRef   = ref<HTMLElement>()
const priceRef   = ref<HTMLElement>()
const surfaceRef = ref<HTMLElement>()
const sortRef    = ref<HTMLElement>()

// Property types from API (translated)
const propertyTypes = ref<{ code: string; label: string }[]>([])

async function loadPropertyTypes() {
  try {
    const res = await fetchPropertyTypes()
    propertyTypes.value = res.data
  } catch {}
}

watch(locale, loadPropertyTypes)
onMounted(loadPropertyTypes)

// Local state for price/surface (apply only on button click — prevents auto-close)
const localMinPrice   = ref<number | undefined>()
const localMaxPrice   = ref<number | undefined>()
const localMinSurface = ref<number | undefined>()
const localMaxSurface = ref<number | undefined>()

function toggle(name: string) {
  if (open.value === name) {
    open.value = null
  } else {
    open.value = name
    if (name === 'price') {
      localMinPrice.value   = filters.value.min_price
      localMaxPrice.value   = filters.value.max_price
    }
    if (name === 'surface') {
      localMinSurface.value = filters.value.min_surface
      localMaxSurface.value = filters.value.max_surface
    }
  }
}

function selectType(code: string) {
  filters.value.property_type = code
  open.value = null
  emit('filter')
}

function incBedrooms() { filters.value.bedrooms = (filters.value.bedrooms || 0) + 1 }
function decBedrooms() {
  const v = (filters.value.bedrooms || 0) - 1
  filters.value.bedrooms = v > 0 ? v : undefined
}

function applyRooms()   { open.value = null; emit('filter') }
function applyPrice()   { filters.value.min_price = localMinPrice.value || undefined; filters.value.max_price = localMaxPrice.value || undefined; open.value = null; emit('filter') }
function applySurface() { filters.value.min_surface = localMinSurface.value || undefined; filters.value.max_surface = localMaxSurface.value || undefined; open.value = null; emit('filter') }

const selectedTypeLabel = computed(() => {
  if (!filters.value.property_type) return t('filters.type')
  return propertyTypes.value.find(p => p.code === filters.value.property_type)?.label || filters.value.property_type
})

const priceLabel = computed(() => {
  const min = filters.value.min_price
  const max = filters.value.max_price
  if (min && max) return `${min.toLocaleString()} – ${max.toLocaleString()}`
  if (min)        return `≥ ${min.toLocaleString()}`
  if (max)        return `≤ ${max.toLocaleString()}`
  return t('filters.price')
})

const surfaceLabel = computed(() => {
  const min = filters.value.min_surface
  const max = filters.value.max_surface
  if (min && max) return `${min} – ${max} m²`
  if (min)        return `≥ ${min} m²`
  if (max)        return `≤ ${max} m²`
  return t('filters.surface')
})

const sortOptions = computed(() => [
  { value: '',           label: t('filters.newest')    },
  { value: 'price_asc',  label: t('filters.priceAsc')  },
  { value: 'price_desc', label: t('filters.priceDesc') },
])

const hasActiveFilters = computed(() =>
  !!(filters.value.property_type || filters.value.min_price || filters.value.max_price ||
     filters.value.min_surface || filters.value.max_surface || filters.value.bedrooms ||
     filters.value.furnished || filters.value.city || filters.value.sort)
)

function reset() {
  filters.value = {
    property_type: '', min_price: undefined, max_price: undefined,
    min_surface: undefined, max_surface: undefined,
    bedrooms: undefined, furnished: null, city: '', sort: '', page: 1,
  }
  localMinPrice.value = undefined; localMaxPrice.value = undefined
  localMinSurface.value = undefined; localMaxSurface.value = undefined
  open.value = null
  emit('filter')
}

// Close dropdowns on outside click — but NOT when clicking inside the dropdown
onMounted(() => {
  document.addEventListener('mousedown', handleOutsideClick)
})
onUnmounted(() => {
  document.removeEventListener('mousedown', handleOutsideClick)
})
function handleOutsideClick(e: MouseEvent) {
  const allRefs = [typeRef.value, roomsRef.value, priceRef.value, surfaceRef.value, sortRef.value]
  if (!allRefs.some(r => r?.contains(e.target as Node))) {
    open.value = null
  }
}
</script>

<style scoped>
@reference "tailwindcss";
.filter-pill {
  @apply flex items-center gap-2 px-3.5 py-2 rounded-full border border-gray-200 bg-white hover:border-[#00878E] transition-colors duration-200 font-medium text-[13px] text-[#313131] cursor-pointer whitespace-nowrap;
}
.active-pill  { @apply border-[#00878E] text-[#00878E] bg-[#00878E]/5; }
.filter-dropdown {
  @apply absolute top-full left-0 mt-2 bg-white rounded-2xl shadow-[0_8px_32px_rgba(0,0,0,0.12)] p-4 border border-gray-100 z-50 min-w-max;
}
.dropdown-item {
  @apply w-full text-left px-3 py-2 rounded-xl font-medium text-[13px] transition-colors hover:bg-gray-50 text-[#313131];
}
.selected-item  { @apply bg-[#00878E]/10 text-[#00878E] hover:bg-[#00878E]/15; }
.counter-btn    { @apply w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 flex items-center justify-center transition-colors; }
.filter-input   { @apply w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-[#00878E] transition-colors; }
.apply-btn      { @apply w-full py-2.5 rounded-full bg-[#00878E] hover:bg-[#006b70] font-semibold text-[13px] text-white transition-colors; }
</style>
