<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="flex flex-col md:flex-row gap-8">
      <LayoutAccountSidebar />

      <main class="flex-1 min-w-0">
        <div class="mb-6">
          <h1 class="text-2xl font-bold text-gray-900">Changer le mot de passe</h1>
          <p class="text-sm text-gray-500 mt-1">Choisissez un mot de passe fort et unique.</p>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 p-6 max-w-lg">
          <!-- Success message -->
          <div v-if="success" class="mb-6 flex items-center gap-3 px-4 py-3 bg-green-50 text-green-700 rounded-xl border border-green-100 text-sm">
            <Icon name="lucide:check-circle" class="w-4 h-4 shrink-0" />
            Mot de passe modifié avec succès.
          </div>

          <!-- Error message -->
          <div v-if="error" class="mb-6 flex items-center gap-3 px-4 py-3 bg-red-50 text-red-600 rounded-xl border border-red-100 text-sm">
            <Icon name="lucide:alert-circle" class="w-4 h-4 shrink-0" />
            {{ error }}
          </div>

          <form @submit.prevent="submit" class="space-y-5">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1.5">Mot de passe actuel</label>
              <input
                v-model="form.current"
                type="password"
                placeholder="••••••••"
                required
                class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-[#00878E] focus:ring-2 focus:ring-[#00878E]/10 transition"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1.5">Nouveau mot de passe</label>
              <input
                v-model="form.password"
                type="password"
                placeholder="••••••••"
                required
                minlength="8"
                class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-[#00878E] focus:ring-2 focus:ring-[#00878E]/10 transition"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1.5">Confirmer le nouveau mot de passe</label>
              <input
                v-model="form.confirm"
                type="password"
                placeholder="••••••••"
                required
                class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-[#00878E] focus:ring-2 focus:ring-[#00878E]/10 transition"
              />
            </div>

            <button
              type="submit"
              :disabled="sending"
              class="w-full py-2.5 bg-[#00878E] text-white text-sm font-medium rounded-xl hover:bg-[#006b70] active:scale-[0.98] transition-all disabled:opacity-60"
            >
              {{ sending ? 'Modification...' : 'Changer le mot de passe' }}
            </button>
          </form>
        </div>
      </main>
    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({ middleware: 'auth' })

const { apiFetch } = useApi()
const form = reactive({ current: '', password: '', confirm: '' })
const sending = ref(false)
const success = ref(false)
const error = ref('')

async function submit() {
  if (form.password !== form.confirm) {
    error.value = 'Les mots de passe ne correspondent pas.'
    return
  }
  sending.value = true
  error.value = ''
  success.value = false
  try {
    await apiFetch('/auth/password', {
      method: 'PUT',
      body: {
        current_password: form.current,
        password: form.password,
        password_confirmation: form.confirm,
      },
    })
    success.value = true
    form.current = ''
    form.password = ''
    form.confirm = ''
  } catch (e: any) {
    error.value = e?.data?.message || 'Une erreur est survenue. Veuillez réessayer.'
  } finally {
    sending.value = false
  }
}
</script>
