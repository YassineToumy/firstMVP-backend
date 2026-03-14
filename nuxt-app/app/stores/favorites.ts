import { defineStore } from 'pinia'
import type { Listing } from '~/composables/useListings'

export const useFavoritesStore = defineStore('favorites', {
  state: () => ({
    listings: [] as Listing[],
  }),

  getters: {
    isFavorite: (state) => (id: number) =>
      state.listings.some(l => l.id === id),
    count: (state) => state.listings.length,
  },

  actions: {
    toggle(listing: Listing) {
      const idx = this.listings.findIndex(l => l.id === listing.id)
      if (idx === -1) {
        this.listings.push(listing)
      } else {
        this.listings.splice(idx, 1)
      }
      if (import.meta.client) {
        localStorage.setItem('favorites', JSON.stringify(this.listings))
      }
    },
    init() {
      if (import.meta.client) {
        const saved = localStorage.getItem('favorites')
        if (saved) {
          try { this.listings = JSON.parse(saved) } catch {}
        }
      }
    },
  },
})
