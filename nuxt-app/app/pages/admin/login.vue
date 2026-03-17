<template>
  <div class="min-h-screen bg-gray-100 flex items-center justify-center px-4">
    <div class="w-full max-w-[420px]">
      <div class="bg-white rounded-2xl shadow-xl p-8">
        <!-- Logo -->
        <div class="flex items-center gap-3 mb-8">
          <div class="w-10 h-10 bg-[#00878E] rounded-xl flex items-center justify-center">
            <Icon name="lucide:shield" class="w-5 h-5 text-white" />
          </div>
          <div>
            <p class="font-bold text-[16px] text-[#313131]">GlobalRent Admin</p>
            <p class="text-xs text-gray-400">Tableau de bord</p>
          </div>
        </div>

        <h1 class="font-bold text-[22px] text-[#313131] mb-1">Connexion</h1>
        <p class="text-sm text-gray-500 mb-6">Accès réservé aux administrateurs</p>

        <form @submit.prevent="submit" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input v-model="email" type="email" required autocomplete="email"
              class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:border-[#00878E] transition-colors" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Mot de passe</label>
            <div class="relative">
              <input v-model="password" :type="showPwd ? 'text' : 'password'" required
                class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:border-[#00878E] transition-colors pr-10" />
              <button type="button" @click="showPwd = !showPwd" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                <Icon :name="showPwd ? 'lucide:eye-off' : 'lucide:eye'" class="w-4 h-4" />
              </button>
            </div>
          </div>

          <p v-if="error" class="text-sm text-red-500 bg-red-50 px-3 py-2 rounded-lg">{{ error }}</p>

          <button type="submit" :disabled="loading"
            class="w-full py-3 rounded-full bg-[#00878E] hover:bg-[#006b70] text-white font-semibold text-[15px] transition-colors disabled:opacity-60 flex items-center justify-center gap-2">
            <Icon v-if="loading" name="lucide:loader-circle" class="w-4 h-4 animate-spin" />
            {{ loading ? 'Connexion...' : 'Se connecter' }}
          </button>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({ layout: false })

const { login } = useAdminAuth()
const email = ref('')
const password = ref('')
const showPwd = ref(false)
const loading = ref(false)
const error = ref('')

async function submit() {
  error.value = ''
  loading.value = true
  try {
    await login(email.value, password.value)
    navigateTo('/admin')
  } catch (e: any) {
    error.value = e?.data?.message || 'Identifiants invalides.'
  } finally {
    loading.value = false
  }
}
</script>
