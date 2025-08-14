<template>
  <div class="favorites">
    <div class="container">
      <!-- Header -->
      <section class="favorites-header">
        <h1 class="hero-title">❤️ Meus Filmes Favoritos</h1>
        <p class="page-subtitle">
          Sua coleção pessoal de filmes favoritos
        </p>
      </section>

      <!-- Filters -->
      <section v-if="favorites.length > 0" class="filters-section">
        <div class="filters-container">
          <h3>Filtrar por gênero:</h3>
          <div class="genre-filters">
            <button @click="clearGenreFilter" :class="['filter-btn', 'btn', { active: !selectedGenres.length }]">
              Todos ({{ favorites.length }})
            </button>
            <button v-for="genre in availableGenres" :key="genre.id" @click="toggleGenreFilter(genre.id)"
              :class="['filter-btn', 'btn', { active: selectedGenres.includes(genre.id) }]">
              {{ genre.name }} ({{ genre.count }})
            </button>
          </div>
        </div>
      </section>

      <!-- Loading -->
      <div v-if="loading" class="loading">
        <div class="spinner"></div>
      </div>

      <!-- Error Message -->
      <div v-if="error" class="error-message">
        <p>❌ {{ error }}</p>
        <button @click="clearError" class="btn btn-secondary">Fechar</button>
      </div>

      <!-- Favorites Grid -->
      <section v-if="!loading && filteredFavorites.length > 0" class="favorites-section">
        <div class="favorites-info">
          <p>
            Mostrando {{ filteredFavorites.length }}
            {{ filteredFavorites.length === 1 ? 'filme' : 'filmes' }}
            {{ selectedGenres.length > 0 ? 'filtrado(s)' : '' }}
          </p>
        </div>

        <div class="movies-grid">
          <MovieCard v-for="movie in filteredFavorites" :key="movie.id || movie.tmdb_id" :movie="movie"
            @favorite-toggled="handleFavoriteToggled" />
        </div>
      </section>

      <!-- Empty State -->
      <section v-if="!loading && favorites.length === 0" class="empty-state">
        <div class="empty-content">
          <h2>🎬 Nenhum filme favorito ainda</h2>
          <p>Comece explorando filmes na página de busca e adicione seus favoritos!</p>
          <router-link to="/" class="btn btn-secondary">
            <i class="fa-solid fa-magnifying-glass"></i>
            Buscar Filmes
          </router-link>
        </div>
      </section>

      <!-- No Results for Filter -->
      <section v-if="!loading && favorites.length > 0 && filteredFavorites.length === 0" class="no-results">
        <div class="no-results-content">
          <h3>🎭 Nenhum filme encontrado</h3>
          <p>Não há filmes favoritos para os gêneros selecionados</p>
          <button @click="clearGenreFilter" class="btn btn-secondary">
            Limpar Filtros
          </button>
        </div>
      </section>

      <!-- Success/Error Toast -->
      <Transition name="fade">
        <div v-if="toast.show" :class="['toast', toast.type]">
          {{ toast.message }}
        </div>
      </Transition>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted, watch } from 'vue'
import { useMoviesStore } from '../stores/movies'
import MovieCard from '../components/MovieCard.vue'

export default {
  name: 'Favorites',
  components: {
    MovieCard
  },
  setup() {
    const moviesStore = useMoviesStore()
    const selectedGenres = ref([])
    const toast = ref({ show: false, message: '', type: 'success' })

    const favorites = computed(() => moviesStore.favorites)
    const loading = computed(() => moviesStore.loading)
    const error = computed(() => moviesStore.error)
    const genres = computed(() => moviesStore.genres)

    const availableGenres = computed(() => {
      const genreMap = new Map()

      favorites.value.forEach(movie => {
        if (movie.genre_ids && Array.isArray(movie.genre_ids)) {
          movie.genre_ids.forEach(genreId => {
            const genre = genres.value.find(g => g.id === genreId)
            if (genre) {
              const current = genreMap.get(genreId) || { ...genre, count: 0 }
              current.count++
              genreMap.set(genreId, current)
            }
          })
        }
      })

      return Array.from(genreMap.values()).sort((a, b) => b.count - a.count)
    })

    const filteredFavorites = computed(() => {
      if (selectedGenres.value.length === 0) {
        return favorites.value
      }

      return favorites.value.filter(movie => {
        if (!movie.genre_ids || !Array.isArray(movie.genre_ids)) {
          return false
        }

        return selectedGenres.value.some(genreId =>
          movie.genre_ids.includes(genreId)
        )
      })
    })

    const toggleGenreFilter = (genreId) => {
      const index = selectedGenres.value.indexOf(genreId)
      if (index > -1) {
        selectedGenres.value.splice(index, 1)
      } else {
        selectedGenres.value.push(genreId)
      }
    }

    const clearGenreFilter = () => {
      selectedGenres.value = []
    }

    const clearError = () => {
      moviesStore.clearError()
    }

    const handleFavoriteToggled = (event) => {
      showToast(event.message, event.action === 'removed' ? 'info' : 'success')

      if (event.action === 'removed') {
        loadFavorites()
      }
    }

    const showToast = (message, type = 'success') => {
      toast.value = { show: true, message, type }
      setTimeout(() => {
        toast.value.show = false
      }, 3000)
    }

    const loadFavorites = async () => {
      await moviesStore.loadFavorites()
    }

    watch(selectedGenres, () => {
    }, { deep: true })

    onMounted(async () => {
      if (genres.value.length === 0) {
        await moviesStore.loadGenres()
      }

      await loadFavorites()
    })

    return {
      favorites,
      filteredFavorites,
      loading,
      error,
      availableGenres,
      selectedGenres,
      toast,
      toggleGenreFilter,
      clearGenreFilter,
      clearError,
      handleFavoriteToggled
    }
  }
}
</script>

<style scoped>
.favorites {
  min-height: calc(100vh - 80px);
}

.favorites-header {
  text-align: center;
  padding: 40px 0;
  background: rgba(255, 255, 255, 0.1);
  border-radius: 20px;
  margin-bottom: 40px;
  backdrop-filter: blur(10px);
}

.page-title {
  font-size: 42px;
  font-weight: 800;
  margin-bottom: 15px;
  background: linear-gradient(135deg, #1a0b2e 0%, #000000 25%, #4c0f60 50%, #000000 75%, #000000 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.page-subtitle {
  font-size: 18px;
  color: rgba(255, 255, 255, 0.9);
  max-width: 500px;
  margin: 0 auto;
}

.filters-section {
  margin-bottom: 40px;
}

.filters-container {
  background: rgba(255, 255, 255, 0.1);
  padding: 25px;
  border-radius: 16px;
  backdrop-filter: blur(10px);
}

.filters-container h3 {
  color: white;
  font-size: 18px;
  font-weight: 600;
  margin-bottom: 20px;
}

.genre-filters {
  display: flex;
  flex-wrap: wrap;
  gap: 12px;
}

.filter-btn {
  background: rgba(255, 255, 255, 0.2);
  color: white;
  border: 1px solid rgba(255, 255, 255, 0.3);
  padding: 8px 16px;
  border-radius: 20px;
  font-size: 14px;
  font-weight: 500;
  transition: all 0.3s ease;
  cursor: pointer;
}

.filter-btn:hover {
  background: rgba(255, 255, 255, 0.3);
  transform: translateY(-1px);
}

.filter-btn.active {
  background: linear-gradient(135deg, #1a0b2e 0%, #000000 25%, #4c0f60 50%, #000000 75%, #000000 100%);
  border-color: transparent;
  box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
}

.error-message {
  background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%);
  color: white;
  padding: 20px;
  border-radius: 12px;
  margin-bottom: 30px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.favorites-section {
  margin-bottom: 40px;
}

.favorites-info {
  text-align: center;
  margin-bottom: 30px;
}

.favorites-info p {
  color: rgba(255, 255, 255, 0.8);
  font-size: 16px;
  font-weight: 500;
}

.movies-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 30px;
}

.empty-state,
.no-results {
  text-align: center;
  padding: 80px 20px;
}

.empty-content h2,
.no-results-content h3 {
  font-size: 32px;
  color: white;
  margin-bottom: 20px;
}

.empty-content p,
.no-results-content p {
  color: rgba(255, 255, 255, 0.8);
  font-size: 18px;
  margin-bottom: 30px;
  max-width: 500px;
  margin-left: auto;
  margin-right: auto;
}

.toast {
  position: fixed;
  top: 100px;
  right: 20px;
  padding: 15px 25px;
  border-radius: 12px;
  color: white;
  font-weight: 500;
  z-index: 1000;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
}

.toast.success {
  background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
}

.toast.info {
  background: linear-gradient(135deg, #17a2b8 0%, #6f42c1 100%);
}

@media (max-width: 768px) {
  .page-title {
    font-size: 28px;
  }

  .page-subtitle {
    font-size: 16px;
  }

  .filters-container {
    padding: 20px;
  }

  .genre-filters {
    gap: 8px;
  }

  .filter-btn {
    font-size: 12px;
    padding: 6px 12px;
  }

  .movies-grid {
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 20px;
  }

  .empty-content h2,
  .no-results-content h3 {
    font-size: 24px;
  }

  .empty-content p,
  .no-results-content p {
    font-size: 16px;
  }

  .toast {
    right: 10px;
    left: 10px;
    top: 90px;
  }
}
</style>
