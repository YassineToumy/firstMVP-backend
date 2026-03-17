export function useAdminAuth() {
  const config = useRuntimeConfig()
  const baseURL = config.public.apiBase || 'http://187.77.168.42/api/v1'
  const token = useCookie('admin_token', { maxAge: 60 * 60 * 24 * 7 })
  const admin = useState<{ id: number; name: string; email: string; role: string } | null>('admin', () => null)

  const isAdminLoggedIn = computed(() => !!token.value)

  async function adminFetch<T>(endpoint: string, opts: Record<string, any> = {}): Promise<T> {
    return $fetch<T>(`${baseURL}${endpoint}`, {
      ...opts,
      headers: {
        Accept: 'application/json',
        Authorization: `Bearer ${token.value}`,
        ...opts.headers,
      },
    })
  }

  async function login(email: string, password: string) {
    const res = await $fetch<{ token: string; admin: any }>(`${baseURL}/admin/login`, {
      method: 'POST',
      body: { email, password },
      headers: { Accept: 'application/json' },
    })
    token.value = res.token
    admin.value = res.admin
  }

  async function logout() {
    try { await adminFetch('/admin/logout', { method: 'POST' }) } catch {}
    token.value = null
    admin.value = null
    navigateTo('/admin/login')
  }

  async function fetchMe() {
    if (!token.value) return
    try {
      const res = await adminFetch<{ data: any }>('/admin/me')
      admin.value = res.data
    } catch {
      token.value = null
      admin.value = null
    }
  }

  return { token, admin, isAdminLoggedIn, adminFetch, login, logout, fetchMe }
}