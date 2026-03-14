import { useApi } from './useApi'

const _labels = ref<Record<string, string>>({})
let _loaded = ''

export function useLabels() {
  const { locale } = useI18n()
  const { apiFetch } = useApi()

  async function loadLabels() {
    if (_loaded === locale.value) return
    const data = await apiFetch<Record<string, string>>(`/labels?lang=${locale.value}`)
    _labels.value = data
    _loaded = locale.value
  }

  const l = (key: string): string => _labels.value[key] ?? key

  return { labels: _labels, loadLabels, l }
}
