import api from '../api'

export const favoriteService = {
  async getFavorites(genreIds = null) {
    try {
      const params = genreIds ? { genre_ids: genreIds } : {}
      const response = await api.get('/favorites', { params })
      return response.data
    } catch (error) {
      throw new Error('Error listing favorites: ' + (error.response?.data?.message || error.message))
    }
  },

  async addToFavorites(movie) {
    try {
      const response = await api.post('/favorites', movie)
      return response.data
    } catch (error) {
      throw new Error('Error adding to favorites: ' + (error.response?.data?.message || error.message))
    }
  },

  async removeFromFavorites(tmdbId) {
    try {
      const response = await api.delete(`/favorites/${tmdbId}`)
      return response.data
    } catch (error) {
      throw new Error('Error removing from favorites: ' + (error.response?.data?.message || error.message))
    }
  },

  async checkFavorite(tmdbId) {
    try {
      const response = await api.get(`/favorites/check/${tmdbId}`)
      return response.data
    } catch (error) {
      throw new Error('Error checking favorite: ' + (error.response?.data?.message || error.message))
    }
  }
}
