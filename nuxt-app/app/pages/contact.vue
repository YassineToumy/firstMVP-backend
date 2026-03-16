<template>
  <div>
    <!-- Hero -->
    <section class="bg-gradient-to-br from-[#00878E] to-[#006b70] text-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-20">
        <div class="max-w-xl">
          <span class="inline-block text-xs font-semibold uppercase tracking-widest text-white/80 mb-3">
            {{ $t('contact.badge') }}
          </span>
          <h1 class="text-3xl sm:text-4xl font-bold leading-tight">
            {{ $t('contact.title') }}
          </h1>
          <p class="mt-4 text-white/80 text-sm sm:text-base leading-relaxed">
            {{ $t('contact.subtitle') }}
          </p>
        </div>
      </div>
    </section>

    <!-- Contact content -->
    <section class="bg-[#FAFAFA]">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14 sm:py-20">
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-10">

          <!-- Info cards -->
          <div class="lg:col-span-2 space-y-4">
            <h2 class="text-lg font-bold text-gray-900 mb-6">{{ $t('contact.getInTouch') }}</h2>

            <div v-for="item in contactInfo" :key="item.labelKey" class="flex items-start gap-4 bg-white border border-gray-100 rounded-2xl p-5 hover:border-[#00878E]/30 hover:shadow-sm transition-all duration-200">
              <div class="w-10 h-10 rounded-xl bg-[#00878E]/8 flex items-center justify-center flex-shrink-0">
                <component :is="item.icon" class="w-5 h-5 text-[#00878E]" />
              </div>
              <div>
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wide mb-0.5">{{ $t(item.labelKey) }}</p>
                <p class="text-sm font-semibold text-gray-900">{{ item.value }}</p>
                <p v-if="item.subKey" class="text-xs text-gray-500 mt-0.5">{{ $t(item.subKey) }}</p>
              </div>
            </div>

            <!-- Map placeholder -->
            <div class="bg-white border border-gray-100 rounded-2xl overflow-hidden h-44 flex items-center justify-center mt-2">
              <div class="text-center text-gray-400">
                <svg class="w-8 h-8 mx-auto mb-2 text-[#00878E]/30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <p class="text-xs">Paris, France</p>
              </div>
            </div>
          </div>

          <!-- Contact form -->
          <div class="lg:col-span-3 bg-white border border-gray-100 rounded-2xl p-7 sm:p-9">
            <h2 class="text-lg font-bold text-gray-900 mb-6">{{ $t('contact.sendMessage') }}</h2>

            <form @submit.prevent="submitForm" class="space-y-5">
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label class="block text-xs font-medium text-gray-700 mb-1.5">{{ $t('contact.firstName') }}</label>
                  <input v-model="form.firstName" type="text" placeholder="John" required class="contact-input" />
                </div>
                <div>
                  <label class="block text-xs font-medium text-gray-700 mb-1.5">{{ $t('contact.lastName') }}</label>
                  <input v-model="form.lastName" type="text" placeholder="Doe" required class="contact-input" />
                </div>
              </div>

              <div>
                <label class="block text-xs font-medium text-gray-700 mb-1.5">{{ $t('contact.email') }}</label>
                <input v-model="form.email" type="email" placeholder="john@example.com" required class="contact-input" />
              </div>

              <div>
                <label class="block text-xs font-medium text-gray-700 mb-1.5">{{ $t('contact.subject') }}</label>
                <select v-model="form.subject" class="contact-input">
                  <option value="">{{ $t('contact.selectSubject') }}</option>
                  <option value="listing">{{ $t('contact.subjectListing') }}</option>
                  <option value="account">{{ $t('contact.subjectAccount') }}</option>
                  <option value="agency">{{ $t('contact.subjectAgency') }}</option>
                  <option value="bug">{{ $t('contact.subjectBug') }}</option>
                  <option value="other">{{ $t('contact.subjectOther') }}</option>
                </select>
              </div>

              <div>
                <label class="block text-xs font-medium text-gray-700 mb-1.5">{{ $t('contact.message') }}</label>
                <textarea
                  v-model="form.message"
                  rows="5"
                  :placeholder="$t('contact.messagePlaceholder')"
                  required
                  class="contact-input resize-none"
                />
              </div>

              <!-- Success / error feedback -->
              <div v-if="sent" class="flex items-center gap-2 text-sm text-green-700 bg-green-50 border border-green-100 rounded-xl px-4 py-3">
                <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
                {{ $t('contact.successMessage') }}
              </div>
              <div v-if="error" class="flex items-center gap-2 text-sm text-red-700 bg-red-50 border border-red-100 rounded-xl px-4 py-3">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ $t('contact.errorMessage') }}
              </div>

              <button
                type="submit"
                :disabled="sending"
                class="w-full py-3.5 bg-[#00878E] text-white text-sm font-semibold rounded-xl hover:bg-[#006b70] active:scale-[0.98] transition-all duration-200 disabled:opacity-60 disabled:cursor-not-allowed flex items-center justify-center gap-2"
              >
                <svg v-if="sending" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z" />
                </svg>
                {{ sending ? $t('contact.sending') : $t('contact.send') }}
              </button>
            </form>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup lang="ts">
const PhoneIcon = defineComponent({
  render: () =>
    h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
      h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z' }),
    ]),
})
const MailIcon = defineComponent({
  render: () =>
    h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
      h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z' }),
    ]),
})
const MapPinIcon = defineComponent({
  render: () =>
    h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
      h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0zM15 11a3 3 0 11-6 0 3 3 0 016 0z' }),
    ]),
})

const contactInfo = [
  { labelKey: 'contact.infoPhone', value: '+33 1 23 45 67 89', subKey: 'contact.infoPhoneSub', icon: PhoneIcon },
  { labelKey: 'contact.infoEmail', value: 'contact@rentglobe.com', subKey: 'contact.infoEmailSub', icon: MailIcon },
  { labelKey: 'contact.infoOffice', value: '12 Rue de Rivoli, Paris 75001', subKey: 'contact.infoOfficeSub', icon: MapPinIcon },
]

const form = reactive({ firstName: '', lastName: '', email: '', subject: '', message: '' })
const sending = ref(false)
const sent = ref(false)
const error = ref(false)

async function submitForm() {
  sending.value = true
  sent.value = false
  error.value = false
  await new Promise(r => setTimeout(r, 1200))
  sending.value = false
  sent.value = true
  form.firstName = ''
  form.lastName = ''
  form.email = ''
  form.subject = ''
  form.message = ''
}

useHead({ title: 'Contact Us — RentGlobe' })
</script>

<style scoped>
@reference "tailwindcss";
.contact-input {
  @apply w-full px-4 py-3 text-sm text-gray-900 bg-[#FAFAFA] border border-gray-200 rounded-xl placeholder-gray-400 focus:outline-none focus:border-[#00878E] focus:ring-2 focus:ring-[#00878E]/15 transition-all duration-200;
}
</style>
