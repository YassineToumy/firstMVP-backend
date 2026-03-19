// composables/useListings.ts

import { useRegionStore } from "~/stores/region"
import { useApi } from "./useApi"

export interface Listing {
  id: number
  title: string
  price: number
  currency: string
  property_type: string
  property_typology: string
  price_per_m2?: number
  url?: string
  description?: string
  photos?: string[]
  interior_features?: {
    surface_m2?: number
    rooms?: number
    bedrooms?: number
    bathrooms?: number
    floor?: number
    heating?: string
    optical_fiber?: string
  }
  exterior_features?: {
    has_elevator?: boolean
    balconies?: number
    terraces?: number
    parking_spots?: number
    cellars?: number
  }
  other_features?: {
    features?: string[]
    is_furnished?: boolean
    is_new?: boolean
    is_accessible?: boolean
    energy_class?: string
    ghg_class?: string
    energy_value?: number
    ghg_value?: number
    posted_by_pro?: boolean
  }
  location: string
  city?: string
  longitude?: number
  latitude?: number
  bedrooms?: number
  bathrooms?: number
  seller_name?: string
  seller_phone?: string
  agency_name?: string
  country: string
  extra_data?: Record<string, any>
  created_at: string
}

export interface ListingFilters {
  country?: string
  property_type?: string
  listing_type?: string
  min_price?: number
  max_price?: number
  bedrooms?: number
  min_surface?: number
  max_surface?: number
  furnished?: boolean | null
  city?: string
  sort?: string
  page?: number
  per_page?: number
}

export interface PaginatedResponse<T> {
  data: T[]
  meta: {
    current_page: number
    last_page: number
    per_page: number
    total: number
  }
}

export interface ListingStats {
  total: number
  avg_price: number
  cities_count: number
  by_type: Record<string, number>
}

export function useListings() {
  const { apiFetch } = useApi()
  const region = useRegionStore()
  const { locale } = useI18n()

  function buildQuery(filters: ListingFilters = {}): string {
    const params = new URLSearchParams()
    const merged = { country: region.currentCode, page: 1, per_page: 20, lang: locale.value, ...filters }

    for (const [key, val] of Object.entries(merged)) {
      if (val !== undefined && val !== null && val !== '') {
        params.set(key, String(val))
      }
    }
    return params.toString()
  }

  async function fetchListings(filters: ListingFilters = {}) {
    const qs = buildQuery(filters)
    return apiFetch<PaginatedResponse<Listing>>(`/listings?${qs}`)
  }

  async function fetchListing(id: number) {
    return apiFetch<{ data: Listing }>(`/listings/${id}?lang=${locale.value}`)
  }

  async function fetchStats() {
    return apiFetch<{ data: ListingStats }>(`/listings/stats?country=${region.currentCode}&lang=${locale.value}`)
  }

  async function fetchCities() {
    return apiFetch<{ data: { city: string; count: number }[] }>(`/cities?country=${region.currentCode}&lang=${locale.value}`)
  }

  async function fetchRegions() {
    return apiFetch<{ data: { code: string; name: string; currency: string; count: number }[] }>('/regions')
  }

  async function fetchPropertyTypes() {
    return apiFetch<{ data: { code: string; label: string }[] }>(`/property-types?lang=${locale.value}`)
  }

  return { fetchListings, fetchListing, fetchStats, fetchCities, fetchRegions, fetchPropertyTypes, buildQuery }
}
