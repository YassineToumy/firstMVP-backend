<template>
  <Teleport to="body">
    <Transition name="modal">
      <div
        v-if="modelValue"
        class="fixed inset-0 bg-black/50 z-[100] flex items-center justify-center p-4"
        @click.self="$emit('update:modelValue', false)"
      >
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-[480px] relative max-h-[calc(100vh-2rem)] overflow-y-auto">

          <!-- Close -->
          <button
            @click="$emit('update:modelValue', false)"
            class="absolute top-4 right-4 w-10 h-10 rounded-full hover:bg-gray-100 flex items-center justify-center transition-colors z-20"
          >
            <Icon name="lucide:x" class="w-5 h-5 text-gray-500" />
          </button>

          <!-- ── SIGNIN ── -->
          <div v-if="mode === 'signin'" class="p-8 pt-8">
            <!-- Logo -->
            <div class="flex justify-center mb-5">
              <div class="flex items-center gap-2">
                <div class="relative w-8 h-8 bg-[#00878E] rounded-lg flex items-center justify-center">
                  <Icon name="lucide:home" class="w-4 h-4 text-white absolute" />
                  <Icon name="lucide:globe" class="w-2 h-2 text-white absolute bottom-0.5 right-0.5" />
                </div>
                <span class="font-bold text-[16px] text-[#00878E]">GlobalRent</span>
              </div>
            </div>

            <h2 class="font-bold text-2xl text-center text-[#313131] mb-1">{{ $t('auth.modal_signin') }}</h2>
            <p class="text-sm text-gray-500 text-center mb-6">{{ $t('auth.loginSubtitle') }}</p>

            <form @submit.prevent="doSignin" class="space-y-3">
              <div>
                <label class="text-sm text-gray-700 mb-1.5 block">{{ $t('auth.emailLabel') }}</label>
                <input v-model="email" type="email" placeholder="votre@email.com"
                  class="auth-input" :class="{ 'border-red-400': errors.email }" />
                <p v-if="errors.email" class="text-xs text-red-500 mt-1">{{ errors.email }}</p>
              </div>

              <div>
                <label class="text-sm text-gray-700 mb-1.5 block">{{ $t('auth.passwordLabel') }}</label>
                <div class="relative">
                  <input v-model="password" :type="showPwd ? 'text' : 'password'" placeholder="••••••••"
                    class="auth-input pr-12" :class="{ 'border-red-400': errors.password }" />
                  <button type="button" @click="showPwd = !showPwd"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors">
                    <Icon :name="showPwd ? 'lucide:eye-off' : 'lucide:eye'" class="w-5 h-5" />
                  </button>
                </div>
                <p v-if="errors.password" class="text-xs text-red-500 mt-1">{{ errors.password }}</p>
              </div>

              <p v-if="errors.general" class="text-sm text-red-500 bg-red-50 px-3 py-2 rounded-lg text-center">{{ errors.general }}</p>

              <button type="submit" :disabled="loading"
                class="w-full py-3.5 mt-1 rounded-full bg-[#00878E] font-semibold text-[15px] text-white hover:bg-[#006b70] transition-all duration-300 shadow-md hover:shadow-lg disabled:opacity-60 flex items-center justify-center gap-2">
                <Icon v-if="loading" name="lucide:loader-circle" class="w-4 h-4 animate-spin" />
                {{ loading ? $t('auth.signingIn') : $t('auth.signIn') }}
              </button>
            </form>

            <div class="flex items-center gap-3 my-5">
              <div class="flex-1 h-px bg-gray-200" />
              <span class="text-xs text-gray-400">ou</span>
              <div class="flex-1 h-px bg-gray-200" />
            </div>

            <p class="text-center text-gray-600 text-[13px]">
              {{ $t('auth.noAccount') }}
              <button @click="switchMode('signup')" class="text-[#00878E] font-semibold hover:underline ml-1">{{ $t('auth.createOne') }}</button>
            </p>
          </div>

          <!-- ── SIGNUP ── -->
          <div v-else-if="mode === 'signup'" class="p-8 pt-8">
            <!-- Logo -->
            <div class="flex justify-center mb-5">
              <div class="flex items-center gap-2">
                <div class="relative w-8 h-8 bg-[#00878E] rounded-lg flex items-center justify-center">
                  <Icon name="lucide:home" class="w-4 h-4 text-white absolute" />
                  <Icon name="lucide:globe" class="w-2 h-2 text-white absolute bottom-0.5 right-0.5" />
                </div>
                <span class="font-bold text-[16px] text-[#00878E]">GlobalRent</span>
              </div>
            </div>

            <h2 class="font-bold text-2xl text-center text-[#313131] mb-1">{{ $t('auth.registerTitle') }}</h2>
            <p class="text-sm text-gray-500 text-center mb-6">{{ $t('auth.registerSubtitle') }}</p>

            <form @submit.prevent="doSignup" class="space-y-3">
              <!-- First + Last name side by side -->
              <div class="flex gap-3">
                <div class="flex-1">
                  <label class="text-sm text-gray-700 mb-1.5 block">{{ $t('auth.firstName') }}</label>
                  <input v-model="firstName" type="text" :placeholder="$t('auth.firstName')" class="auth-input" />
                </div>
                <div class="flex-1">
                  <label class="text-sm text-gray-700 mb-1.5 block">{{ $t('auth.lastName') }}</label>
                  <input v-model="lastName" type="text" :placeholder="$t('auth.lastName')" class="auth-input" />
                </div>
              </div>

              <div>
                <label class="text-sm text-gray-700 mb-1.5 block">{{ $t('auth.emailLabel') }}</label>
                <input v-model="email" type="email" placeholder="votre@email.com"
                  class="auth-input" :class="{ 'border-red-400': errors.email }" />
                <p v-if="errors.email" class="text-xs text-red-500 mt-1">{{ errors.email }}</p>
              </div>

              <div>
                <label class="text-sm text-gray-700 mb-1.5 block">{{ $t('auth.passwordLabel') }}</label>
                <div class="relative">
                  <input v-model="password" :type="showPwd ? 'text' : 'password'" placeholder="••••••••"
                    class="auth-input pr-12" :class="{ 'border-red-400': errors.password }" />
                  <button type="button" @click="showPwd = !showPwd"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors">
                    <Icon :name="showPwd ? 'lucide:eye-off' : 'lucide:eye'" class="w-5 h-5" />
                  </button>
                </div>
                <p v-if="errors.password" class="text-xs text-red-500 mt-1">{{ errors.password }}</p>
              </div>

              <div>
                <label class="text-sm text-gray-700 mb-1.5 block">{{ $t('auth.confirmPassword') }}</label>
                <div class="relative">
                  <input v-model="passwordConfirm" :type="showPwd2 ? 'text' : 'password'" placeholder="••••••••"
                    class="auth-input pr-12" />
                  <button type="button" @click="showPwd2 = !showPwd2"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors">
                    <Icon :name="showPwd2 ? 'lucide:eye-off' : 'lucide:eye'" class="w-5 h-5" />
                  </button>
                </div>
              </div>

              <p v-if="errors.general" class="text-sm text-red-500 bg-red-50 px-3 py-2 rounded-lg text-center">{{ errors.general }}</p>

              <button type="submit" :disabled="loading"
                class="w-full py-3.5 mt-1 rounded-full bg-[#00878E] font-semibold text-[15px] text-white hover:bg-[#006b70] transition-all duration-300 shadow-md hover:shadow-lg disabled:opacity-60 flex items-center justify-center gap-2">
                <Icon v-if="loading" name="lucide:loader-circle" class="w-4 h-4 animate-spin" />
                {{ loading ? $t('auth.creatingAccount') : $t('auth.createMyAccount') }}
              </button>

              <p class="text-xs text-gray-400 text-center leading-relaxed">
                {{ $t('auth.termsAcceptance') }}
                <span class="underline font-semibold cursor-pointer hover:text-[#00878E]">{{ $t('auth.termsOfService') }}</span>
                {{ $t('auth.andOur') }}
                <span class="underline font-semibold cursor-pointer hover:text-[#00878E]">{{ $t('auth.privacyPolicy') }}</span>.
              </p>
            </form>

            <div class="flex items-center gap-3 my-5">
              <div class="flex-1 h-px bg-gray-200" />
              <span class="text-xs text-gray-400">ou</span>
              <div class="flex-1 h-px bg-gray-200" />
            </div>

            <p class="text-center text-gray-600 text-[13px]">
              {{ $t('auth.hasAccount') }}
              <button @click="switchMode('signin')" class="text-[#00878E] font-semibold hover:underline ml-1">{{ $t('auth.signIn') }}</button>
            </p>
          </div>

          <!-- ── VERIFY ── -->
          <div v-else-if="mode === 'verify'" class="p-8 py-10">
            <div class="w-16 h-16 bg-[#00878E]/10 rounded-full flex items-center justify-center mx-auto mb-5">
              <Icon name="lucide:mail" class="w-8 h-8 text-[#00878E]" />
            </div>
            <h2 class="font-bold text-2xl text-center text-[#313131] mb-2">{{ $t('auth.verifyEmailTitle') }}</h2>
            <p class="text-center text-gray-500 text-sm mb-6 leading-relaxed">
              {{ $t('auth.verificationCodeSent') }} <span class="font-semibold text-[#313131]">{{ email }}</span>
            </p>

            <div v-if="verifySuccess" class="mb-4 p-3 rounded-xl bg-green-50 border border-green-200">
              <p class="text-center text-green-600 font-semibold text-sm">{{ $t('auth.emailVerifiedSuccess') }}</p>
            </div>

            <div class="mb-4">
              <label class="text-sm text-gray-700 mb-1.5 block text-center">{{ $t('auth.verificationCodeLabel') }}</label>
              <input
                v-model="verifyCode"
                type="text"
                placeholder="000000"
                maxlength="6"
                class="w-full px-4 py-3.5 rounded-xl border border-gray-300 text-[20px] text-center tracking-[0.4em] font-bold focus:outline-none focus:border-[#00878E] transition-colors"
              />
            </div>

            <button
              @click="doVerify"
              :disabled="verifyCode.length !== 6"
              class="w-full py-3.5 rounded-full font-semibold text-[15px] text-white transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed enabled:bg-[#00878E] enabled:hover:bg-[#006b70]"
            >
              {{ $t('auth.verify') }}
            </button>

            <div class="mt-6 pt-4 border-t border-gray-100 text-center">
              <p class="text-gray-500 text-sm mb-3">{{ $t('auth.noCodeReceived') }}</p>
              <button class="w-full py-2.5 rounded-full border border-gray-300 font-semibold text-sm text-[#313131] hover:bg-gray-50 transition-all">
                {{ $t('auth.resendCode') }}
              </button>
              <button @click="switchMode('signin')" class="mt-4 text-[#00878E] hover:underline text-sm font-semibold inline-flex items-center gap-1">
                <Icon name="lucide:arrow-left" class="w-3.5 h-3.5" />
                {{ $t('auth.backToLogin') }}
              </button>
            </div>
          </div>

        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup lang="ts">
const props = defineProps<{ modelValue: boolean }>()
const emit = defineEmits<{ 'update:modelValue': [boolean] }>()

const { t } = useI18n()
const { login, register } = useAuth()

const mode = ref<'signin' | 'signup' | 'verify'>('signin')
const loading = ref(false)
const email = ref('')
const password = ref('')
const passwordConfirm = ref('')
const firstName = ref('')
const lastName = ref('')
const verifyCode = ref('')
const verifySuccess = ref(false)
const showPwd = ref(false)
const showPwd2 = ref(false)
const errors = ref<Record<string, string>>({})

function switchMode(m: 'signin' | 'signup' | 'verify') {
  mode.value = m
  errors.value = {}
  password.value = ''
  passwordConfirm.value = ''
  showPwd.value = false
  showPwd2.value = false
}

async function doSignin() {
  errors.value = {}
  if (!email.value) { errors.value.email = t('auth.emailRequired'); return }
  if (!password.value) { errors.value.password = t('auth.passwordRequired'); return }
  loading.value = true
  try {
    await login(email.value, password.value)
    emit('update:modelValue', false)
  } catch (e: any) {
    errors.value.general = e?.data?.message || t('auth.invalidCredentials')
  } finally {
    loading.value = false
  }
}

async function doSignup() {
  errors.value = {}
  if (!email.value) { errors.value.email = t('auth.emailRequired'); return }
  if (!password.value || password.value.length < 8) { errors.value.password = t('auth.min8Chars'); return }
  loading.value = true
  try {
    const name = [firstName.value, lastName.value].filter(Boolean).join(' ') || email.value
    await register(name, email.value, password.value, passwordConfirm.value)
    emit('update:modelValue', false)
  } catch (e: any) {
    errors.value.general = e?.data?.message || t('auth.creationError')
  } finally {
    loading.value = false
  }
}

function doVerify() {
  verifySuccess.value = true
  setTimeout(() => {
    emit('update:modelValue', false)
    verifySuccess.value = false
  }, 2000)
}

watch(() => props.modelValue, (v) => {
  if (v) { mode.value = 'signin'; errors.value = {}; showPwd.value = false; showPwd2.value = false }
})
</script>

<style scoped>
@reference "tailwindcss";
.auth-input {
  @apply w-full px-4 py-2.5 rounded-xl border border-gray-300 text-[15px]
    focus:outline-none focus:border-[#00878E] focus:ring-2 focus:ring-[#00878E]/15
    placeholder-gray-400 transition-all duration-150;
}
.modal-enter-active, .modal-leave-active { transition: opacity 0.2s ease; }
.modal-enter-from, .modal-leave-to { opacity: 0; }
</style>
