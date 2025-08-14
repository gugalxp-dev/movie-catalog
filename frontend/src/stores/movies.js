import { defineStore } from 'pinia'
import { movieService } from '../services/movies/api'
import { favoriteService } from '../services/favorites/api'

export const useMoviesStore = defineStore('movies', {
  state: () => ({
    searchResults: [],
    favorites: [],
    genres: [],
    nowPlayingMovies: [],
    loading: false,
    error: null,
    currentPage: 1,
    totalPages: 1,
    searchQuery: ''
  }),

  getters: {
    getGenreNames: (state) => (genreIds) => {
      if (!genreIds || !Array.isArray(genreIds)) return []
      return genreIds.map(id => {
        const genre = state.genres.find(g => g.id === id)
        return genre ? genre.name : 'Desconhecido'
      })
    },

    favoriteIds: (state) => {
      return state.favorites.map(movie => movie.tmdb_id)
    }
  },

  actions: {
    async searchMovies(query, page = 1) {
      this.loading = true
      this.error = null
      
      try {
        const response = await movieService.searchMovies(query, page)
        
        if (response.success) {
          this.searchResults = response.data.results || []
          this.currentPage = response.data.page || 1
          this.totalPages = response.data.total_pages || 1
          this.searchQuery = query
        } else {
          throw new Error(response.message || 'Erro na busca')
        }
      } catch (error) {
        this.error = error.message
        this.searchResults = []
      } finally {
        this.loading = false
      }
    },

    async loadGenres() {
      try {
        const response = await movieService.getGenres()
        
        if (response.success) {
          this.genres = response.data || []
        }
      } catch (error) {
        console.error('Erro ao carregar gêneros:', error.message)
      }
    },

    async loadNowPlayingMovies() {
      try {
        const response = await movieService.getNowPlayingMovies()
        console.log(response);
        
        if (response.success) {
          this.nowPlayingMovies = response.data || []
        }
      } catch (error) {
        console.error('Erro ao carregar gêneros:', error.message)
      }
    },

    async loadFavorites(genreIds = null) {
      this.loading = true
      this.error = null
      
      try {
        const response = await favoriteService.getFavorites(genreIds)
        
        if (response.success) {
          this.favorites = response.data || []
        } else {
          throw new Error(response.message || 'Erro ao carregar favoritos')
        }
      } catch (error) {
        this.error = error.message
        this.favorites = []
      } finally {
        this.loading = false
      }
    },

    async addToFavorites(movie) {
      try {
        const response = await favoriteService.addToFavorites(movie)
        
        if (response.success) {
          this.favorites.push(response.data)
          return { success: true, message: 'Filme adicionado aos favoritos!' }
        } else {
          throw new Error(response.message || 'Erro ao adicionar favorito')
        }
      } catch (error) {
        return { success: false, message: error.message }
      }
    },

    async removeFromFavorites(tmdbId) {
      try {
        const response = await favoriteService.removeFromFavorites(tmdbId)
        
        if (response.success) {
          this.favorites = this.favorites.filter(movie => movie.tmdb_id !== tmdbId)
          return { success: true, message: 'Filme removido dos favoritos!' }
        } else {
          throw new Error(response.message || 'Erro ao remover favorito')
        }
      } catch (error) {
        return { success: false, message: error.message }
      }
    },

    async checkFavorite(tmdbId) {
      try {
        const response = await favoriteService.checkFavorite(tmdbId)
        return response.success ? response.data.is_favorite : false
      } catch (error) {
        console.error('Erro ao verificar favorito:', error.message)
        return false
      }
    },

    clearSearch() {
      this.searchResults = []
      this.searchQuery = ''
      this.currentPage = 1
      this.totalPages = 1
    },

    clearError() {
      this.error = null
    }
  }
})

