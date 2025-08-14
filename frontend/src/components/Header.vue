<template>
  <header class="header">
    <div class="container">
      <div class="header-content">
        <div class="logo">
          <h1>🎬 Catálogo de Filmes</h1>
        </div>
        
        <nav class="nav">
          <router-link to="/" class="nav-link" :class="{ active: $route.name === 'Home' }">
            Buscar Filmes
          </router-link>
          <router-link to="/favorites" class="nav-link" :class="{ active: $route.name === 'Favorites' }">
            Meus Favoritos
            <span v-if="favoritesCount > 0" class="badge">{{ favoritesCount }}</span>
          </router-link>
        </nav>
      </div>
    </div>
  </header>
</template>

<script>
import { computed } from 'vue'
import { useMoviesStore } from '../stores/movies'

export default {
  name: 'Header',
  setup() {
    const moviesStore = useMoviesStore()
    
    const favoritesCount = computed(() => moviesStore.favorites.length)
    
    return {
      favoritesCount
    }
  }
}
</script>

<style scoped>
.header {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(10px);
  border-bottom: 1px solid rgba(255, 255, 255, 0.2);
  position: sticky;
  top: 0;
  z-index: 100;
  height: 80px;
}

.header-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
  height: 80px;
}

.logo h1 {
  font-size: 24px;
  font-weight: 700;
  background: linear-gradient(135deg, #1a0b2e 0%, #000000 25%, #4c0f60 50%, #000000 75%, #000000 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.nav {
  display: flex;
  gap: 30px;
}

.nav-link {
  text-decoration: none;
  color: #495057;
  font-weight: 500;
  padding: 8px 16px;
  border-radius: 8px;
  transition: all 0.3s ease;
  position: relative;
  display: flex;
  align-items: center;
  gap: 8px;
}

.nav-link:hover {
  color: #4c0f60;
  background: rgba(102, 126, 234, 0.1);
}

.nav-link.active {
  color: #4c0f60;
  background: rgba(102, 126, 234, 0.15);
}

.badge {
  background: linear-gradient(135deg, #000000 0%, #4c0f60 100%);
  color: white;
  font-size: 12px;
  font-weight: 600;
  padding: 2px 6px;
  border-radius: 10px;
  min-width: 18px;
  text-align: center;
}

@media (max-width: 768px) {
  .header-content {
    flex-direction: column;
    height: auto;
    padding: 15px 0;
  }
  
  .logo h1 {
    font-size: 20px;
    margin-bottom: 10px;
  }
  
  .nav {
    gap: 20px;
  }
  
  .nav-link {
    font-size: 14px;
    padding: 6px 12px;
  }
}
</style>

