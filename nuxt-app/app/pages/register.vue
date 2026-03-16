<template>
  <div class="min-h-[calc(100vh-84px)] flex items-center justify-center px-4 bg-[#FAFAFA]">
    <div class="w-full max-w-sm animate-fade-in-up">
      <!-- Icon -->
      <div class="w-12 h-12 mx-auto rounded-2xl bg-gradient-to-br bg-[#00878E] flex items-center justify-center mb-6">
        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
        </svg>
      </div>

      <h1 class="text-2xl font-bold text-gray-900 text-center">{{ $t('auth.registerTitle') }}</h1>
      <p class="text-sm text-gray-500 text-center mt-1">{{ $t('auth.registerSubtitle') }}</p>

      <form @submit.prevent="handleRegister" class="mt-8 space-y-4">
        <div>
          <label class="block text-xs font-medium text-gray-600 mb-1.5">{{ $t('auth.fullName') }}</label>
          <input v-model="name" type="text" required autocomplete="name"
            class="auth-input" placeholder="John Doe" />
        </div>
        <div>
          <label class="block text-xs font-medium text-gray-600 mb-1.5">{{ $t('auth.emailLabel') }}</label>
          <input v-model="email" type="email" required autocomplete="email"
            class="auth-input" placeholder="you@example.com" />
        </div>
        <div>
          <label class="block text-xs font-medium text-gray-600 mb-1.5">{{ $t('auth.passwordLabel') }}</label>
          <input v-model="password" type="password" required autocomplete="new-password"
            class="auth-input" :placeholder="$t('auth.passwordMin')" />
        </div>
        <div>
          <label class="block text-xs font-medium text-gray-600 mb-1.5">{{ $t('auth.confirmPassword') }}</label>
          <input v-model="passwordConfirm" type="password" required autocomplete="new-password"
            class="auth-input" placeholder="••••••••" />
        </div>

        <Transition enter-active-class="transition duration-200" enter-from-class="opacity-0 -translate-y-1" enter-to-class="opacity-100 translate-y-0">
          <p v-if="error" class="text-xs text-red-600 bg-red-50 px-3 py-2 rounded-lg">{{ error }}</p>
        </Transition>

        <button type="submit" :disabled="loading"
          class="w-full py-3 bg-[#00878E] text-white text-sm font-semibold rounded-xl hover:bg-[#006b70] active:scale-[0.98] transition-all disabled:opacity-50 disabled:cursor-not-allowed">
          {{ loading ? $t('auth.creatingAccount') : $t('auth.createAccount') }}
        </button>
      </form>

      <p class="text-sm text-gray-500 text-center mt-8">
        {{ $t('auth.hasAccount') }}
        <NuxtLink to="/login" class="text-[#00878E] font-semibold hover:text-[#006b70] transition-colors">{{ $t('auth.signIn') }}</NuxtLink>
      </p>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useAuth } from '~/composables/useAuth'

const { t } = useI18n()
const { register } = useAuth()
const router = useRouter()

const name = ref('')
const email = ref('')
const password = ref('')
const passwordConfirm = ref('')
const error = ref('')
const loading = ref(false)

async function handleRegister() {
  error.value = ''
  if (password.value !== passwordConfirm.value) {
    error.value = t('auth.passwordsNoMatch')
    return
  }
  loading.value = true
  try {
    await register(name.value, email.value, password.value, passwordConfirm.value)
    router.push('/')
  } catch (e: any) {
    error.value = e?.data?.message || t('auth.registrationFailed')
  } finally {
    loading.value = false
  }
}

useHead({ title: 'Register | RentGlobe' })
</script>

<style scoped>
@reference "tailwindcss";
.auth-input {
  @apply w-full px-3.5 py-3 bg-white border border-gray-200 rounded-xl text-sm
    focus:outline-none focus:ring-2 focus:ring-[#00878E]/20 focus:border-[#00878E]
    placeholder-gray-400 transition-all duration-150;
}
</style>
