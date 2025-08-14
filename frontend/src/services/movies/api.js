import api from '../api'

export const movieService = {
  async searchMovies(query, page = 1) {
    try {
      const response = await api.get('/movies/search', { params: { query, page } })
      return response.data
    } catch (error) {
      throw new Error('Error searching movies: ' + (error.response?.data?.message || error.message))
    }
  },

  async getMovieDetails(id) {
    try {
      const response = await api.get(`/movies/${id}`)
      return response.data
    } catch (error) {
      throw new Error('Error getting movie details: ' + (error.response?.data?.message || error.message))
    }
  },

  async getGenres() {
    try {
      const response = await api.get('/movies/genres')
      return response.data
    } catch (error) {
      throw new Error('Error getting genres: ' + (error.response?.data?.message || error.message))
    }
  },

  async getNowPlayingMovies() {
    try {
      const response = await api.get('/movies/now-playing-movies')
      return response.data
    } catch (error) {
      throw new Error('Error getting now playing movies: ' + (error.response?.data?.message || error.message))
    }
  }
}
