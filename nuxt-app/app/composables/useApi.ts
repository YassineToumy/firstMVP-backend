// composables/useApi.ts
// Base API helper — all requests go through here

export function useApi() {
  const config = useRuntimeConfig()
  const baseURL = config.public.apiBase || 'http://187.77.168.42/api/v1'

  async function apiFetch<T>(endpoint: string, opts: Record<string, any> = {}): Promise<T> {
    const token = useCookie('auth_token').value

    const headers: Record<string, string> = {
      Accept: 'application/json',
      ...opts.headers,
    }
    if (token) headers.Authorization = `Bearer ${token}`

    return $fetch<T>(`${baseURL}${endpoint}`, {
      ...opts,
      headers,
    })
  }

  return { apiFetch }
}