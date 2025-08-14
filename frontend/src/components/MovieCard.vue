<template>
  <div class="movie-card card">
    <div class="movie-poster">
      <img :src="posterUrl" :alt="movie.title" @error="handleImageError" class="poster-image" />
      <div class="movie-rating" v-if="movie.vote_average">
        ⭐ {{ Number(movie.vote_average).toFixed(1) }} </div>
    </div>

    <div class="movie-info">
      <h3 class="movie-title">{{ movie.title }}</h3>

      <div class="movie-meta">
        <span class="release-date" v-if="movie.release_date">
          📅 {{ formatDate(movie.release_date) }}
        </span>
        <span class="language" v-if="movie.original_language">
          🌐 {{ movie.original_language.toUpperCase() }}
        </span>
      </div>

      <div class="movie-genres" v-if="genreNames.length > 0">
        <span v-for="genre in genreNames" :key="genre" class="genre-tag">
          {{ genre }}
        </span>
      </div>

      <p class="movie-overview" v-if="movie.overview">
        {{ truncatedOverview }}
      </p>

      <div class="movie-actions">
        <button @click="toggleFavorite" :class="['btn', isFavorite ? 'btn-danger' : 'btn-primary']" :disabled="loading">
          {{ isFavorite ? '❤️ Remover' : '🤍 Favoritar' }}
        </button>
        <button @click="openModal" class="btn btn-secondary">
          Ver Detalhes
        </button>
      </div>
    </div>

    <DetailsModal :show="showModal" :movie="movie" @close="closeModal" />
  </div>
</template>

<script>
import { computed, ref, onMounted } from 'vue'
import { useMoviesStore } from '../stores/movies'
import DetailsModal from './DetailsModal.vue'
import { useRoute } from 'vue-router'

export default {
  name: 'MovieCard',
  components: {
    DetailsModal
  },
  props: {
    movie: {
      type: Object,
      required: true
    },
    showFavoriteButton: {
      type: Boolean,
      default: true
    }
  },
  emits: ['favorite-toggled'],
  setup(props, { emit }) {
    const moviesStore = useMoviesStore()
    const loading = ref(false)
    const imageError = ref(false)
    const showModal = ref(false)
    const route = useRoute()

    const posterUrl = computed(() => {
      if (imageError.value || !props.movie.poster_path) {
        return 'https://via.placeholder.com/300x450/667eea/ffffff?text=Sem+Poster'
      }
      return `https://image.tmdb.org/t/p/w200${props.movie.poster_path}`
    })

    const genreNames = computed(() => {
      return moviesStore.getGenreNames(props.movie.genre_ids)
    })

    const truncatedOverview = computed(() => {
      if (!props.movie.overview) return ''
      return props.movie.overview.length > 150
        ? props.movie.overview.substring(0, 150) + '...'
        : props.movie.overview
    })

    const isFavorite = computed(() => {
      return route.name === 'favorites' || moviesStore.favoriteIds.includes(props.movie.id || props.movie.tmdb_id)
    })

    const formatDate = (dateString) => {
      if (!dateString) return ''
      const date = new Date(dateString)
      return date.toLocaleDateString('pt-BR')
    }

    const handleImageError = () => {
      imageError.value = true
    }

    const toggleFavorite = async () => {
      loading.value = true
      try {
        let result
        if (isFavorite.value) {
          result = await moviesStore.removeFromFavorites(props.movie.tmdb_id || props.movie.id)
        } else {
          result = await moviesStore.addToFavorites(props.movie)
        }
        if (result.success) {
          emit('favorite-toggled', {
            movie: props.movie,
            action: isFavorite.value ? 'removed' : 'added',
            message: result.message
          })
        } else {
          console.error('Erro:', result.message)
        }
      } catch (error) {
        console.error('Erro ao alterar favorito:', error)
      } finally {
        loading.value = false
      }
    }

    const openModal = () => {
      showModal.value = true
      document.body.style.overflow = 'hidden'
    }

    const closeModal = () => {
      showModal.value = false
      document.body.style.overflow = 'auto'
    }

    onMounted(() => {
      if (moviesStore.genres.length === 0) {
        moviesStore.loadGenres()
      }
    })

    return {
      posterUrl,
      genreNames,
      truncatedOverview,
      isFavorite,
      loading,
      showModal,
      formatDate,
      handleImageError,
      toggleFavorite,
      openModal,
      closeModal
    }
  }
}
</script>

<style scoped>
.movie-card {
  display: flex;
  flex-direction: column;
  height: 100%;
  transition: all 0.3s ease;
  background-color: transparent;
  box-shadow: none;
}

.movie-poster {
  position: relative;
  width: 100%;
  height: 300px;
  overflow: hidden;
  justify-content: center;
  display: flex;
}

.poster-image {
  height: 100%;
  object-fit: cover;
  transition: transform 0.3s ease;
}

.movie-card:hover .poster-image {
  transform: scale(1.05);
}

.movie-rating {
  position: absolute;
  top: 10px;
  right: 10px;
  background: rgba(0, 0, 0, 0.8);
  color: white;
  padding: 4px 8px;
  border-radius: 12px;
  font-size: 12px;
  font-weight: 600;
}

.movie-info {
  padding: 20px;
  flex: 1;
  display: flex;
  flex-direction: column;
}

.movie-title {
  font-size: 18px;
  font-weight: 700;
  margin-bottom: 10px;
  color: #fff;
  line-height: 1.3;
  text-align: center;
}

.movie-meta {
  display: flex;
  justify-content: center;
  gap: 15px;
  margin-bottom: 10px;
  font-size: 12px;
}

.movie-genres {
  display: flex;
  justify-content: center;
  flex-wrap: wrap;
  gap: 6px;
  margin-bottom: 12px;
}

.genre-tag {
  background: linear-gradient(135deg, #1a0b2e 0%, #000000 25%, #4c0f60 50%, #000000 75%, #000000 100%);
  color: white;
  padding: 2px 8px;
  border-radius: 12px;
  font-size: 11px;
  font-weight: 500;
}

.language,
.release-date {
  color: #fff;
}

.movie-overview {
  font-size: 14px;
  line-height: 1.5;
  color: #fff;
  margin-bottom: 15px;
  flex: 1;
  text-align: center;
}

.movie-actions {
  margin-top: auto;
  display: flex;
  justify-content: center;
}

.btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

@media (max-width: 768px) {
  .movie-poster {
    height: 250px;
  }

  .movie-info {
    padding: 15px;
  }

  .movie-title {
    font-size: 16px;
  }

  .movie-meta {
    flex-direction: column;
    gap: 5px;
  }
}
</style>