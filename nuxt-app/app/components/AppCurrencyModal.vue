<template>
  <Teleport to="body">
    <Transition name="modal">
      <div v-if="modelValue" class="fixed inset-0 z-[110]">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="$emit('update:modelValue', false)" />

        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-[500px] bg-white rounded-2xl shadow-2xl max-h-[80vh] flex flex-col">
          <!-- Header -->
          <div class="flex items-center justify-between p-6 border-b border-gray-200">
            <h2 class="font-bold text-[20px] text-[#313131]">Sélectionner une devise</h2>
            <button @click="$emit('update:modelValue', false)" class="w-8 h-8 rounded-full hover:bg-gray-100 flex items-center justify-center transition-colors">
              <Icon name="lucide:x" class="w-5 h-5 text-gray-600" />
            </button>
          </div>

          <!-- Search -->
          <div class="p-6 border-b border-gray-200">
            <div class="relative">
              <Icon name="lucide:search" class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
              <input v-model="search" type="text" placeholder="Rechercher une devise..." class="w-full pl-11 pr-4 py-3 border border-gray-200 rounded-xl text-[15px] focus:outline-none focus:border-[#00878E] transition-colors" />
            </div>
          </div>

          <!-- List -->
          <div class="overflow-y-auto flex-1 p-4">
            <div class="space-y-1">
              <div v-if="!filtered.length" class="text-center py-12">
                <p class="text-gray-500 text-[15px]">Aucune devise trouvée</p>
              </div>
              <button
                v-for="c in filtered"
                :key="c.code"
                @click="selected = c.code"
                class="w-full flex items-center justify-between p-4 rounded-xl hover:bg-gray-50 transition-colors"
                :class="selected === c.code ? 'bg-[#00878E]/5 border border-[#00878E]' : ''"
              >
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-[16px]"
                    :class="selected === c.code ? 'bg-[#00878E] text-white' : 'bg-gray-100 text-gray-700'"
                  >{{ c.symbol }}</div>
                  <div class="text-left">
                    <div class="font-semibold text-[15px] text-[#313131]">{{ c.code }}</div>
                    <div class="text-[13px] text-gray-500">{{ c.name }}</div>
                  </div>
                </div>
                <div v-if="selected === c.code" class="w-5 h-5 rounded-full bg-[#00878E] flex items-center justify-center">
                  <Icon name="lucide:check" class="w-3 h-3 text-white" />
                </div>
              </button>
            </div>
          </div>

          <!-- Footer -->
          <div class="p-6 border-t border-gray-200">
            <button @click="save" class="w-full bg-[#00878E] text-white py-3 px-6 rounded-xl font-semibold text-[15px] hover:bg-[#006b70] transition-colors">
              Enregistrer
            </button>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup lang="ts">
const props = defineProps<{ modelValue: boolean }>()
const emit = defineEmits<{
  'update:modelValue': [boolean]
  'select': [string]
}>()

const search = ref('')
const selected = ref('EUR')

const currencies = [
  { code: 'EUR', symbol: '€', name: 'Euro' },
  { code: 'USD', symbol: '$', name: 'Dollar américain' },
  { code: 'TND', symbol: 'د.ت', name: 'Dinar tunisien' },
  { code: 'EGP', symbol: '£', name: 'Livre égyptienne' },
  { code: 'CAD', symbol: 'C$', name: 'Dollar canadien' },
  { code: 'GBP', symbol: '£', name: 'Livre sterling' },
  { code: 'AED', symbol: 'د.إ', name: 'Dirham des Émirats' },
  { code: 'SAR', symbol: 'ر.س', name: 'Riyal saoudien' },
  { code: 'CHF', symbol: 'CHF', name: 'Franc suisse' },
  { code: 'AUD', symbol: 'A$', name: 'Dollar australien' },
  { code: 'JPY', symbol: '¥', name: 'Yen japonais' },
  { code: 'CNY', symbol: '¥', name: 'Yuan chinois' },
  { code: 'MAD', symbol: 'د.م', name: 'Dirham marocain' },
  { code: 'DZD', symbol: 'د.ج', name: 'Dinar algérien' },
  { code: 'QAR', symbol: 'ر.ق', name: 'Riyal qatarien' },
  { code: 'KWD', symbol: 'د.ك', name: 'Dinar koweïtien' },
  { code: 'BRL', symbol: 'R$', name: 'Real brésilien' },
  { code: 'MXN', symbol: 'Mex$', name: 'Peso mexicain' },
  { code: 'INR', symbol: '₹', name: 'Roupie indienne' },
  { code: 'ZAR', symbol: 'R', name: 'Rand sud-africain' },
  { code: 'TRY', symbol: '₺', name: 'Livre turque' },
  { code: 'PLN', symbol: 'zł', name: 'Złoty polonais' },
  { code: 'NOK', symbol: 'kr', name: 'Couronne norvégienne' },
  { code: 'SEK', symbol: 'kr', name: 'Couronne suédoise' },
  { code: 'DKK', symbol: 'kr', name: 'Couronne danoise' },
]

const filtered = computed(() => {
  const q = search.value.toLowerCase()
  return q ? currencies.filter(c => c.code.toLowerCase().includes(q) || c.name.toLowerCase().includes(q)) : currencies
})

function save() {
  emit('select', selected.value)
  emit('update:modelValue', false)
}

watch(() => props.modelValue, (v) => {
  if (v) search.value = ''
})
</script>

<style scoped>
.modal-enter-active, .modal-leave-active { transition: opacity 0.2s ease; }
.modal-enter-from, .modal-leave-to { opacity: 0; }
</style>
