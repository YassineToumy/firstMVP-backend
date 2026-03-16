<template>
  <Teleport to="body">
    <Transition name="modal">
      <div v-if="modelValue" class="fixed inset-0 bg-black/50 z-[100] flex items-center justify-center p-4" @click.self="$emit('update:modelValue', false)">
        <div class="bg-white rounded-3xl shadow-2xl max-w-[480px] w-full relative">

          <!-- Close button -->
          <button
            @click="$emit('update:modelValue', false)"
            class="absolute top-4 right-4 w-10 h-10 rounded-full hover:bg-gray-100 flex items-center justify-center transition-colors z-20"
          >
            <Icon name="lucide:x" class="w-5 h-5 text-gray-500" />
          </button>

          <!-- ── SIGNIN ── -->
          <div v-if="mode === 'signin'" class="p-6 pt-8">
            <h2 class="font-bold text-2xl text-center text-[#313131] mb-5">{{ $t('auth.modal_signin') }}</h2>

            <form @submit.prevent="doSignin" class="mb-4">
              <div class="mb-3">
                <label class="text-sm text-gray-700 mb-1.5 block">{{ $t('auth.emailLabel') }}</label>
                <input v-model="email" type="email" placeholder="votre@email.com" class="auth-input" :class="{ 'border-red-400': errors.email }" />
                <p v-if="errors.email" class="text-xs text-red-500 mt-1">{{ errors.email }}</p>
              </div>

              <div class="mb-3">
                <label class="text-sm text-gray-700 mb-1.5 block">{{ $t('auth.passwordLabel') }}</label>
                <input v-model="password" type="password" placeholder="••••••••" class="auth-input" :class="{ 'border-red-400': errors.password }" />
                <p v-if="errors.password" class="text-xs text-red-500 mt-1">{{ errors.password }}</p>
              </div>

              <p v-if="errors.general" class="text-sm text-red-500 text-center mb-3">{{ errors.general }}</p>

              <button type="submit" :disabled="loading" class="w-full py-3 rounded-xl bg-[#00878E] font-semibold text-[15px] text-white hover:bg-[#006b70] transition-all duration-300 shadow-md hover:shadow-lg disabled:opacity-60">
                {{ loading ? $t('auth.signingIn') : $t('auth.signIn') }}
              </button>
            </form>

            <div class="text-center pt-3 border-t border-gray-100">
              <p class="text-gray-600 text-[13px]">
                {{ $t('auth.noAccount') }}
                <button @click="switchMode('signup')" class="text-[#00878E] font-semibold hover:underline ml-1">{{ $t('auth.createOne') }}</button>
              </p>
            </div>
          </div>

          <!-- ── SIGNUP ── -->
          <div v-else-if="mode === 'signup'" class="p-6 pt-8">
            <h2 class="font-bold text-2xl text-center text-[#313131] mb-5">{{ $t('auth.registerTitle') }}</h2>

            <form @submit.prevent="doSignup" class="mb-4">
              <div class="flex gap-3 mb-3">
                <div class="flex-1">
                  <label class="text-sm text-gray-700 mb-1.5 block">{{ $t('auth.firstName') }}</label>
                  <input v-model="firstName" type="text" :placeholder="$t('auth.firstName')" class="auth-input" />
                </div>
                <div class="flex-1">
                  <label class="text-sm text-gray-700 mb-1.5 block">{{ $t('auth.lastName') }}</label>
                  <input v-model="lastName" type="text" :placeholder="$t('auth.lastName')" class="auth-input" />
                </div>
              </div>

              <div class="mb-3">
                <label class="text-sm text-gray-700 mb-1.5 block">{{ $t('auth.emailLabel') }}</label>
                <input v-model="email" type="email" placeholder="votre@email.com" class="auth-input" :class="{ 'border-red-400': errors.email }" />
                <p v-if="errors.email" class="text-xs text-red-500 mt-1">{{ errors.email }}</p>
              </div>

              <div class="mb-4">
                <label class="text-sm text-gray-700 mb-1.5 block">{{ $t('auth.passwordLabel') }}</label>
                <input v-model="password" type="password" placeholder="••••••••" class="auth-input" :class="{ 'border-red-400': errors.password }" />
                <p v-if="errors.password" class="text-xs text-red-500 mt-1">{{ errors.password }}</p>
                <div class="mb-3">
                  <label class="text-sm text-gray-700 mb-1.5 block mt-3">Confirmer le mot de passe</label>
                  <input v-model="passwordConfirm" type="password" placeholder="••••••••" class="auth-input" />
                </div>
              </div>

              <p v-if="errors.general" class="text-sm text-red-500 text-center mb-3">{{ errors.general }}</p>

              <button type="submit" :disabled="loading" class="w-full py-3 rounded-xl bg-[#00878E] font-semibold text-[15px] text-white hover:bg-[#006b70] transition-all duration-300 shadow-md hover:shadow-lg disabled:opacity-60">
                {{ loading ? $t('auth.creatingAccount') : $t('auth.createMyAccount') }}
              </button>
            </form>

            <p class="text-gray-500 text-center mb-3 text-[12px]">
              {{ $t('auth.termsAcceptance') }}
              <span class="underline font-bold cursor-pointer hover:text-[#00878E]">{{ $t('auth.termsOfService') }}</span>
              {{ $t('auth.andOur') }}
              <span class="underline font-bold cursor-pointer hover:text-[#00878E]">{{ $t('auth.privacyPolicy') }}</span>.
            </p>

            <div class="text-center pt-3 border-t border-gray-100">
              <p class="text-gray-600 text-[13px]">
                {{ $t('auth.hasAccount') }}
                <button @click="switchMode('signin')" class="text-[#00878E] font-semibold hover:underline ml-1">{{ $t('auth.signIn') }}</button>
              </p>
            </div>
          </div>

          <!-- ── VERIFY ── -->
          <div v-else-if="mode === 'verify'" class="p-6 py-10">
            <div class="w-16 h-16 bg-[#00878E]/10 rounded-full flex items-center justify-center mx-auto mb-5">
              <Icon name="lucide:mail" class="w-8 h-8 text-[#00878E]" />
            </div>
            <h2 class="font-bold text-2xl text-center text-[#313131] mb-2">{{ $t('auth.verifyEmailTitle') }}</h2>
            <p class="text-center text-gray-600 text-[14px] mb-6 leading-relaxed">
              {{ $t('auth.verificationCodeSent') }} <span class="font-semibold text-[#313131]">{{ email }}</span>
            </p>

            <div v-if="verifySuccess" class="mb-4 p-3 rounded-xl bg-green-50 border border-green-200">
              <p class="text-center text-[#00878E] font-semibold text-[14px]">{{ $t('auth.emailVerifiedSuccess') }}</p>
            </div>

            <div class="mb-3">
              <label class="text-sm font-semibold text-[#313131] mb-1.5 block">{{ $t('auth.verificationCodeLabel') }}</label>
              <input
                v-model="verifyCode"
                type="text"
                placeholder="000000"
                maxlength="6"
                class="w-full px-4 py-3 rounded-xl border border-gray-300 text-[17px] text-center tracking-[0.3em] focus:outline-none focus:border-[#00878E] transition-colors"
              />
            </div>

            <button
              @click="doVerify"
              :disabled="verifyCode.length !== 6"
              class="w-full py-3 rounded-xl font-semibold text-[15px] text-white transition-all duration-300 mt-4 disabled:opacity-50 disabled:cursor-not-allowed enabled:bg-[#00878E] enabled:hover:bg-[#006b70]"
            >
              {{ $t('auth.verify') }}
            </button>

            <div class="text-center mt-6 pt-4 border-t border-gray-100">
              <p class="text-gray-600 text-[13px] mb-2">{{ $t('auth.noCodeReceived') }}</p>
              <button class="w-full py-2.5 rounded-xl border border-gray-300 font-semibold text-[14px] text-[#313131] hover:bg-gray-50 transition-all">
                {{ $t('auth.resendCode') }}
              </button>
            </div>

            <div class="text-center mt-4">
              <button @click="switchMode('signin')" class="text-[#00878E] hover:underline text-[13px] font-semibold inline-flex items-center gap-1">
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
const errors = ref<Record<string, string>>({})

function switchMode(m: 'signin' | 'signup' | 'verify') {
  mode.value = m
  errors.value = {}
  password.value = ''
  passwordConfirm.value = ''
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
  if (v) { mode.value = 'signin'; errors.value = {} }
})
</script>

<style scoped>
@reference "tailwindcss";
.auth-input {
  @apply w-full px-4 py-2.5 rounded-xl border border-gray-300 text-[15px] focus:outline-none focus:border-[#00878E] transition-colors;
}
.modal-enter-active, .modal-leave-active { transition: opacity 0.2s ease; }
.modal-enter-from, .modal-leave-to { opacity: 0; }
</style>
