<template>
  <div class="min-h-screen bg-gray-50 pt-[84px] flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-[480px] animate-fade-in-up">

      <!-- Card -->
      <div class="bg-white rounded-3xl shadow-2xl overflow-hidden">
        <div class="p-8">

          <!-- Logo -->
          <div class="flex justify-center mb-6">
            <NuxtLink to="/" class="flex items-center gap-2">
              <div class="relative w-9 h-9 bg-[#00878E] rounded-lg flex items-center justify-center">
                <Icon name="lucide:home" class="w-5 h-5 text-white absolute" />
                <Icon name="lucide:globe" class="w-2.5 h-2.5 text-white absolute bottom-0.5 right-0.5" />
              </div>
              <span class="font-bold text-[18px] text-[#00878E]">GlobalRent</span>
            </NuxtLink>
          </div>

          <!-- Title -->
          <h1 class="font-bold text-2xl text-center text-[#313131] mb-1">{{ $t('auth.registerTitle') }}</h1>
          <p class="text-sm text-gray-500 text-center mb-6">{{ $t('auth.registerSubtitle') }}</p>

          <!-- Form -->
          <form @submit.prevent="handleRegister" class="space-y-3">

            <!-- First + Last name (side by side) -->
            <div class="flex gap-3">
              <div class="flex-1">
                <label class="block text-sm text-gray-700 mb-1.5">{{ $t('auth.firstName') }}</label>
                <input v-model="firstName" type="text" required autocomplete="given-name"
                  class="auth-input" :placeholder="$t('auth.firstName')" />
              </div>
              <div class="flex-1">
                <label class="block text-sm text-gray-700 mb-1.5">{{ $t('auth.lastName') }}</label>
                <input v-model="lastName" type="text" required autocomplete="family-name"
                  class="auth-input" :placeholder="$t('auth.lastName')" />
              </div>
            </div>

            <!-- Email -->
            <div>
              <label class="block text-sm text-gray-700 mb-1.5">{{ $t('auth.emailLabel') }}</label>
              <input v-model="email" type="email" required autocomplete="email"
                class="auth-input" placeholder="votre@email.com" />
            </div>

            <!-- Password -->
            <div>
              <label class="block text-sm text-gray-700 mb-1.5">{{ $t('auth.passwordLabel') }}</label>
              <div class="relative">
                <input v-model="password" :type="showPwd ? 'text' : 'password'" required autocomplete="new-password"
                  class="auth-input pr-12" :placeholder="$t('auth.min8Chars')" />
                <button type="button" @click="showPwd = !showPwd"
                  class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors">
                  <Icon :name="showPwd ? 'lucide:eye-off' : 'lucide:eye'" class="w-5 h-5" />
                </button>
              </div>
            </div>

            <!-- Confirm password -->
            <div>
              <label class="block text-sm text-gray-700 mb-1.5">{{ $t('auth.confirmPassword') }}</label>
              <div class="relative">
                <input v-model="passwordConfirm" :type="showPwd2 ? 'text' : 'password'" required autocomplete="new-password"
                  class="auth-input pr-12" placeholder="••••••••" />
                <button type="button" @click="showPwd2 = !showPwd2"
                  class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors">
                  <Icon :name="showPwd2 ? 'lucide:eye-off' : 'lucide:eye'" class="w-5 h-5" />
                </button>
              </div>
            </div>

            <!-- Error -->
            <Transition enter-active-class="transition duration-200" enter-from-class="opacity-0 -translate-y-1" enter-to-class="opacity-100 translate-y-0">
              <p v-if="error" class="text-xs text-red-600 bg-red-50 px-3 py-2 rounded-lg">{{ error }}</p>
            </Transition>

            <!-- Submit -->
            <button type="submit" :disabled="loading"
              class="w-full py-3.5 mt-2 bg-[#00878E] text-white font-semibold rounded-full hover:bg-[#006b70] active:scale-[0.98] transition-all duration-300 shadow-md hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
              <Icon v-if="loading" name="lucide:loader-circle" class="w-4 h-4 animate-spin" />
              {{ loading ? $t('auth.creatingAccount') : $t('auth.createMyAccount') }}
            </button>

            <!-- Terms -->
            <p class="text-xs text-gray-400 text-center leading-relaxed pt-1">
              {{ $t('auth.termsAcceptance') }}
              <a href="#" class="text-[#00878E] hover:underline">{{ $t('auth.termsOfService') }}</a>
              {{ $t('auth.andOur') }}
              <a href="#" class="text-[#00878E] hover:underline">{{ $t('auth.privacyPolicy') }}</a>.
            </p>

          </form>

          <!-- Divider -->
          <div class="flex items-center gap-3 my-5">
            <div class="flex-1 h-px bg-gray-200" />
            <span class="text-xs text-gray-400">{{ $t('auth2.or') }}</span>
            <div class="flex-1 h-px bg-gray-200" />
          </div>

          <!-- Switch to login -->
          <p class="text-sm text-gray-500 text-center">
            {{ $t('auth.hasAccount') }}
            <NuxtLink to="/login" class="text-[#00878E] font-semibold hover:text-[#006b70] transition-colors ml-1">
              {{ $t('auth.signIn') }}
            </NuxtLink>
          </p>

        </div>
      </div>

    </div>
  </div>
</template>

<script setup lang="ts">
import { useAuth } from '~/composables/useAuth'

const { t } = useI18n()
const { register } = useAuth()
const router = useRouter()

const firstName = ref('')
const lastName = ref('')
const email = ref('')
const password = ref('')
const passwordConfirm = ref('')
const showPwd = ref(false)
const showPwd2 = ref(false)
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
    const fullName = `${firstName.value} ${lastName.value}`.trim()
    await register(fullName, email.value, password.value, passwordConfirm.value)
    router.push('/')
  } catch (e: any) {
    error.value = e?.data?.message || t('auth.registrationFailed')
  } finally {
    loading.value = false
  }
}

useHead({ title: computed(() => `${t('auth.registerTitle')} | GlobalRent`) })
</script>

<style scoped>
@reference "tailwindcss";
.auth-input {
  @apply w-full px-4 py-2.5 bg-white border border-gray-300 rounded-xl text-[15px]
    focus:outline-none focus:border-[#00878E] focus:ring-2 focus:ring-[#00878E]/15
    placeholder-gray-400 transition-all duration-150;
}
</style>