<template>
  <div class="home">
    <div class="container">
      <!-- Hero Section -->
      <section class="hero">
        <h1 class="hero-title">Descubra Seus Filmes Favoritos</h1>
        <p class="hero-subtitle">
          Explore milhares de filmes e crie sua lista personalizada de favoritos
        </p>
      </section>

      <!-- Welcome State -->
      <section v-if="!loading && !searchQueryDisplay" class="welcome-state">
        <div class="welcome-content">
          <h2>🍿 Bem-vindo ao Catálogo de Filmes!</h2>
          <p>Use a barra de pesquisa abaixo para encontrar seus filmes favoritos</p>
          <div class="suggestions">
            <h3>Sugestões populares:</h3>
            <div class="suggestion-tags">
              <button v-for="suggestion in suggestions" :key="suggestion" @click="searchSuggestion(suggestion)"
                class="suggestion-tag btn btn-secondary">
                {{ suggestion }}
              </button>
            </div>
          </div>
        </div>
      </section>

      <!-- Search Section -->
      <section class="search-section">
        <div class="search-container">
          <div class="search-box">
            <input v-model="searchQuery" @keyup.enter="performSearch" type="text"
              placeholder="Digite o nome do filme..." class="search-input" />
            <button @click="performSearch" :disabled="!searchQuery.trim() || loading"
              class="search-button btn btn-primary">
              <i class="fa-solid fa-magnifying-glass"></i>
              Buscar
            </button>
          </div>
        </div>
      </section>

      <!-- Now Playing Movies -->
      <section v-if="!loading && !searchQueryDisplay && nowPlayingMovies.results?.length > 0"
        class="now-playing-section">
        <div class="welcome-content">
          <h2>🍿 Filmes em Cartaz</h2>
          <p>Confira os filmes que estão em exibição agora!</p>
          <div class="movies-grid">
            <MovieCard v-for="movie in nowPlayingMovies.results" :key="movie.id" :movie="movie"
              @favorite-toggled="handleFavoriteToggled" />
          </div>
        </div>
      </section>
      <section v-else-if="!loading && !searchQueryDisplay" class="welcome-state">
        <div class="welcome-content">
          <h2>🍿 Bem-vindo ao Catálogo de Filmes!</h2>
          <p>Nenhum filme em cartaz disponível no momento. Use a barra de pesquisa para encontrar seus filmes favoritos.
          </p>
        </div>
      </section>

      <!-- Error Message -->
      <div v-if="error" class="error-message">
        <p>❌ {{ error }}</p>
        <button @click="clearError" class="btn btn-secondary">Fechar</button>
      </div>

      <!-- Loading -->
      <div v-if="loading" class="loading">
        <div class="spinner"></div>
      </div>

      <!-- Search Results -->
      <section v-if="searchResults.length > 0" class="results-section">
        <div class="results-header">
          <h2>Resultados da Busca</h2>
          <p class="results-info">
            Página {{ currentPage }} de {{ totalPages }}
            <span v-if="searchQueryDisplay">para "{{ searchQueryDisplay }}"</span>
          </p>
        </div>

        <div class="movies-grid">
          <MovieCard v-for="movie in searchResults" :key="movie.id" :movie="movie"
            @favorite-toggled="handleFavoriteToggled" />
        </div>

        <!-- Pagination -->
        <div v-if="totalPages > 1" class="pagination">
          <button @click="goToPage(currentPage - 1)" :disabled="currentPage <= 1 || loading" class="btn btn-secondary">
            ← Anterior
          </button>

          <span class="page-info">
            Página {{ currentPage }} de {{ totalPages }}
          </span>

          <button @click="goToPage(currentPage + 1)" :disabled="currentPage >= totalPages || loading"
            class="btn btn-secondary">
            Próxima →
          </button>
        </div>
      </section>

      <!-- Empty State -->
      <section v-if="!loading && searchResults.length === 0 && searchQueryDisplay" class="empty-state">
        <div class="empty-content">
          <h3>🎬 Nenhum filme encontrado</h3>
          <p>Tente buscar com palavras-chave diferentes</p>
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
  name: 'Home',
  components: {
    MovieCard
  },
  setup() {
    const moviesStore = useMoviesStore()
    const searchQuery = ref('')
    const toast = ref({ show: false, message: '', type: 'success' })

    const suggestions = [
      'Vingadores', 'Harry Potter', 'Star Wars', 'Senhor dos Anéis',
      'Batman', 'Jurassic Park', 'Matrix', 'Titanic'
    ]

    const nowPlayingMovies = computed(() => moviesStore.nowPlayingMovies)
    const searchResults = computed(() => moviesStore.searchResults)
    const loading = computed(() => moviesStore.loading)
    const error = computed(() => moviesStore.error)
    const currentPage = computed(() => moviesStore.currentPage)
    const totalPages = computed(() => moviesStore.totalPages)
    const searchQueryDisplay = computed(() => moviesStore.searchQuery)

    watch(searchQuery, (newValue) => {
      if (!newValue.trim()) {
        moviesStore.clearSearch()
      }
    })

    const performSearch = async () => {
      if (!searchQuery.value.trim()) {
        moviesStore.clearSearch()
        return
      }

      await moviesStore.searchMovies(searchQuery.value.trim(), 1)
    }

    const goToPage = async (page) => {
      if (page < 1 || page > totalPages.value) return

      await moviesStore.searchMovies(searchQueryDisplay.value, page)
    }

    const searchSuggestion = (suggestion) => {
      searchQuery.value = suggestion
      performSearch()
    }

    const clearError = () => {
      moviesStore.clearError()
    }

    const handleFavoriteToggled = (event) => {
      showToast(event.message, event.action === 'added' ? 'success' : 'info')
    }

    const showToast = (message, type = 'success') => {
      toast.value = { show: true, message, type }
      setTimeout(() => {
        toast.value.show = false
      }, 3000)
    }

    onMounted(() => {
      moviesStore.loadNowPlayingMovies()
      if (moviesStore.genres.length === 0) {
        moviesStore.loadGenres()
      }
      moviesStore.loadFavorites()
    })

    return {
      searchQuery,
      searchResults,
      loading,
      error,
      currentPage,
      nowPlayingMovies,
      totalPages,
      searchQueryDisplay,
      suggestions,
      toast,
      performSearch,
      goToPage,
      searchSuggestion,
      clearError,
      handleFavoriteToggled
    }
  }
}
</script>

<style scoped>
.now-playing-section {
  margin-bottom: 40px;
  text-align: center;
  padding: 40px 20px;
}

.now-playing-section h2 {
  font-size: 32px;
  font-weight: 700;
  color: white;
  margin-bottom: 10px;
}

.now-playing-section p {
  color: rgba(255, 255, 255, 0.8);
  font-size: 18px;
  margin-bottom: 30px;
}

.home {
  min-height: calc(100vh - 80px);
}

.hero {
  text-align: center;
  padding: 60px 0;
  background: rgba(255, 255, 255, 0.1);
  border-radius: 20px;
  margin-bottom: 40px;
  backdrop-filter: blur(10px);
}

.hero-subtitle {
  font-size: 20px;
  color: rgba(255, 255, 255, 0.9);
  max-width: 600px;
  margin: 0 auto;
  line-height: 1.6;
}

.search-section {
  margin-bottom: 40px;
}

.search-container {
  max-width: 600px;
  margin: 0 auto;
}

.search-box {
  display: flex;
  gap: 15px;
  background: white;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}

.search-input {
  flex: 1;
  border: none;
  padding: 15px 20px;
  font-size: 16px;
  border-radius: 12px;
  outline: none;
  background: transparent;
}

.search-input::placeholder {
  color: #adb5bd;
}

.search-button {
  white-space: nowrap;
}

.error-message {
  background: linear-gradient(135deg, #ff4848 0%, #ee5a52 100%);
  color: white;
  padding: 20px;
  margin-bottom: 30px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.results-section {
  margin-bottom: 40px;
}

.results-header {
  margin-bottom: 30px;
  text-align: center;
}

.results-header h2 {
  font-size: 32px;
  font-weight: 700;
  color: white;
  margin-bottom: 10px;
}

.results-info {
  color: rgba(255, 255, 255, 0.8);
  font-size: 16px;
}

.movies-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 30px;
  margin-bottom: 40px;
}

.pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 20px;
  margin-top: 40px;
}

.page-info {
  color: white;
  font-weight: 500;
}

.empty-state,
.welcome-state {
  text-align: center;
  padding: 30px 20px;
}

.empty-content h3,
.welcome-content h2 {
  font-size: 28px;
  color: white;
  margin-bottom: 15px;
}

.empty-content p,
.welcome-content p {
  color: rgba(255, 255, 255, 0.8);
  font-size: 18px;
  margin-bottom: 30px;
}

.suggestions {
  margin-top: 40px;
}

.suggestions h3 {
  font-size: 20px;
  color: white;
  margin-bottom: 20px;
}

.suggestion-tags {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  gap: 15px;
}

.suggestion-tag {
  background: rgba(255, 255, 255, 0.2);
  color: white;
  border: 1px solid rgba(255, 255, 255, 0.3);
  transition: all 0.3s ease;
}

.suggestion-tag:hover {
  background: rgba(255, 255, 255, 0.3);
  transform: translateY(-2px);
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

@media (max-width: 390px) {
  .hero-title {
    font-size: 32px;
  }

  .hero-subtitle {
    font-size: 16px;
  }

  .search-box {
    flex-direction: column;
    margin-bottom: 1em;
  }

  .movies-grid {
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 20px;
  }

  .pagination {
    flex-direction: column;
    gap: 15px;
  }

  .suggestion-tags {
    gap: 10px;
  }

  .toast {
    right: 10px;
    left: 10px;
    top: 90px;
  }
}
</style>
