<template>
  <div class="min-h-[calc(100vh-84px)] flex items-center justify-center px-4 bg-[#FAFAFA]">
    <div class="w-full max-w-sm animate-fade-in-up">
      <!-- Icon -->
      <div class="w-12 h-12 mx-auto rounded-2xl bg-gradient-to-br bg-[#00878E] flex items-center justify-center mb-6">
        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
        </svg>
      </div>

      <h1 class="text-2xl font-bold text-gray-900 text-center">{{ $t('auth.loginTitle') }}</h1>
      <p class="text-sm text-gray-500 text-center mt-1">{{ $t('auth.loginSubtitle') }}</p>

      <form @submit.prevent="handleLogin" class="mt-8 space-y-4">
        <div>
          <label class="block text-xs font-medium text-gray-600 mb-1.5">{{ $t('auth.emailLabel') }}</label>
          <input v-model="email" type="email" required autocomplete="email"
            class="auth-input" placeholder="you@example.com" />
        </div>
        <div>
          <label class="block text-xs font-medium text-gray-600 mb-1.5">{{ $t('auth.passwordLabel') }}</label>
          <input v-model="password" type="password" required autocomplete="current-password"
            class="auth-input" placeholder="••••••••" />
        </div>

        <!-- Error -->
        <Transition enter-active-class="transition duration-200" enter-from-class="opacity-0 -translate-y-1" enter-to-class="opacity-100 translate-y-0">
          <p v-if="error" class="text-xs text-red-600 bg-red-50 px-3 py-2 rounded-lg">{{ error }}</p>
        </Transition>

        <button type="submit" :disabled="loading"
          class="w-full py-3 bg-[#00878E] text-white text-sm font-semibold rounded-xl hover:bg-[#006b70] active:scale-[0.98] transition-all disabled:opacity-50 disabled:cursor-not-allowed">
          {{ loading ? $t('auth.signingIn') : $t('auth.signIn') }}
        </button>
      </form>

      <p class="text-sm text-gray-500 text-center mt-8">
        {{ $t('auth.noAccount') }}
        <NuxtLink to="/register" class="text-[#00878E] font-semibold hover:text-[#006b70] transition-colors">{{ $t('auth.createOne') }}</NuxtLink>
      </p>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useAuth } from '~/composables/useAuth'

const { t } = useI18n()
const { login } = useAuth()
const router = useRouter()

const email = ref('')
const password = ref('')
const error = ref('')
const loading = ref(false)

async function handleLogin() {
  error.value = ''
  loading.value = true
  try {
    await login(email.value, password.value)
    router.push('/')
  } catch (e: any) {
    error.value = e?.data?.message || t('auth.invalidCredentials')
  } finally {
    loading.value = false
  }
}

useHead({ title: 'Sign In | RentGlobe' })
</script>

<style scoped>
@reference "tailwindcss";
.auth-input {
  @apply w-full px-3.5 py-3 bg-white border border-gray-200 rounded-xl text-sm
    focus:outline-none focus:ring-2 focus:ring-[#00878E]/20 focus:border-[#00878E]
    placeholder-gray-400 transition-all duration-150;
}
</style>
