<template>
  <teleport to="body">
    <div v-if="show" class="modal-overlay" @click.self="closeModal">
      <div class="modal-content">
        <button class="modal-close" @click="closeModal">×</button>
        <div class="modal-body">
          <div class="modal-poster">
            <img :src="posterUrl" :alt="movie.title" @error="handleImageError" class="modal-poster-image" />
          </div>
          <div class="modal-info">
            <h2 class="modal-title">{{ movie.title }}</h2>
            <div class="modal-meta">
              <span v-if="movie.release_date">
                📅 Lançamento: {{ formatDate(movie.release_date) }}
              </span>
              <span v-if="movie.runtime">
                ⏱️ Duração: {{ formatRuntime(movie.runtime) }}
              </span>
              <span v-if="movie.original_language">
                🌐 Idioma: {{ movie.original_language.toUpperCase() }}
              </span>
            </div>
            <div class="modal-genres" v-if="genreNames.length > 0">
              <span v-for="genre in genreNames" :key="genre" class="genre-tag">
                {{ genre }}
              </span>
            </div>
            <div class="modal-rating" v-if="movie.vote_average">
              ⭐ Avaliação: {{ Number(movie.vote_average).toFixed(1) }} ({{ movie.vote_count }} votos)
            </div>
            <p class="modal-overview" v-if="movie.overview">
              {{ movie.overview }}
            </p>
          </div>
        </div>
      </div>
    </div>
  </teleport>
</template>

<script>
import { computed, ref } from 'vue'
import { useMoviesStore } from '../stores/movies'

export default {
  name: 'MovieModal',
  props: {
    show: {
      type: Boolean,
      default: false
    },
    movie: {
      type: Object,
      required: true
    }
  },
  emits: ['close'],
  setup(props, { emit }) {
    const moviesStore = useMoviesStore()
    const imageError = ref(false)

    const posterUrl = computed(() => {
      if (imageError.value || !props.movie.poster_path) {
        return 'https://via.placeholder.com/400x600/667eea/ffffff?text=Sem+Poster'
      }
      return `https://image.tmdb.org/t/p/w400${props.movie.poster_path}`
    })

    const genreNames = computed(() => {
      return moviesStore.getGenreNames(props.movie.genre_ids)
    })

    const formatDate = (dateString) => {
      if (!dateString) return ''
      const date = new Date(dateString)
      return date.toLocaleDateString('pt-BR')
    }

    const formatRuntime = (minutes) => {
      if (!minutes) return ''
      const hours = Math.floor(minutes / 60)
      const mins = minutes % 60
      return `${hours}h ${mins}m`
    }

    const handleImageError = () => {
      imageError.value = true
    }

    const closeModal = () => {
      emit('close')
      document.body.style.overflow = 'auto'
    }

    return {
      posterUrl,
      genreNames,
      formatDate,
      formatRuntime,
      handleImageError,
      closeModal
    }
  }
}
</script>

<style scoped>
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.7);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  animation: fadeIn 0.3s ease;
}

.modal-content {
  background: #1a0b2e;
  border-radius: 8px;
  max-width: 800px;
  width: 90%;
  max-height: 90vh;
  overflow-y: auto;
  position: relative;
  animation: slideIn 0.3s ease;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
}

.modal-close {
  position: absolute;
  top: 10px;
  right: 10px;
  background: none;
  border: none;
  font-size: 20px;
  color: #fff;
  cursor: pointer;
  transition: color 0.2s;
}

.modal-close:hover {
  color: #ff4d4d;
}

.modal-body {
  display: flex;
  gap: 20px;
  padding: 20px;
}

.modal-poster {
  flex: 0 0 auto;
}

.modal-poster-image {
  width: 200px;
  height: auto;
  border-radius: 8px;
  object-fit: cover;
}

.modal-info {
  flex: 1;
  color: #fff;
}

.modal-title {
  font-size: 24px;
  font-weight: 700;
  margin-bottom: 15px;
}

.modal-meta {
  display: flex;
  flex-wrap: wrap;
  gap: 15px;
  margin-bottom: 15px;
  font-size: 14px;
}

.modal-genres {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  margin-bottom: 15px;
}

.genre-tag {
  background: linear-gradient(135deg, #1a0b2e 0%, #000000 25%, #4c0f60 50%, #000000 75%, #000000 100%);
  color: white;
  padding: 2px 8px;
  border-radius: 12px;
  font-size: 11px;
  font-weight: 500;
}

.modal-rating {
  margin-bottom: 15px;
  font-size: 14px;
  font-weight: 600;
}

.modal-overview {
  font-size: 14px;
  line-height: 1.6;
}

@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

@keyframes slideIn {
  from { transform: translateY(50px); opacity: 0; }
  to { transform: translateY(0); opacity: 1; }
}

@media (max-width: 768px) {
  .modal-body {
    flex-direction: column;
  }

  .modal-poster-image {
    width: 100%;
    max-width: 300px;
    margin: 0 auto;
  }

  .modal-content {
    width: 95%;
  }
}
</style>