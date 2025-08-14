# 🎬 Catálogo de Filmes

Uma aplicação completa para buscar e gerenciar filmes favoritos, desenvolvida com Vue.js no frontend e Laravel no backend, integrada com a API do The Movie Database (TMDB).

## 🚀 Tecnologias Utilizadas

### Backend
- **Laravel 10** - Framework PHP
- **MySQL 8.0** - Banco de dados
- **PHP 8.2** - Linguagem de programação
- **Composer** - Gerenciador de dependências

### Frontend
- **Vue.js 3** - Framework JavaScript
- **Vite** - Build tool
- **Pinia** - Gerenciamento de estado
- **Axios** - Cliente HTTP
- **CSS3** - Estilização moderna

### Infraestrutura
- **Docker & Docker Compose** - Containerização
- **phpMyAdmin** - Interface de administração do banco

## 📋 Funcionalidades

### ✅ Implementadas
- Buscar filmes pelo nome usando a API do TMDB
- Adicionar filmes aos favoritos
- Listar filmes favoritos
- Remover filmes da lista de favoritos
- Filtrar filmes favoritos por gênero
- Interface responsiva e moderna
- Tratamento de erros e loading states

### 🎯 Características Técnicas
- Arquitetura limpa com separação de responsabilidades
- Services e Controllers separados
- Tratamento de erros com try/catch
- Soft Delete
- Componentização
- Validação de dados
- API RESTful
- Código limpo e bem documentado

## 🔑 API TMDB

Para usar a aplicação, você precisa de uma chave da API do TMDB. Siga os passos abaixo para criar sua conta e gerar o token:

1. **Acesse o site oficial**  
   Vá para [https://www.themoviedb.org](https://www.themoviedb.org)

2. **Crie uma conta**  
   - Clique em **Entrar** no canto superior direito.  
   - Clique em **Cadastre-se** e preencha os campos solicitados (nome, e-mail, senha, etc.).  
   - Confirme o e-mail através do link enviado.

3. **Acesse as configurações de API**  
    Vá para [https://www.themoviedb.org/settings/api](https://www.themoviedb.org/settings/api)

4. **Solicite uma chave de API**  
   - Clique em **Criar** nova chave.  
   - Escolha o tipo **Chave de API para desenvolvedores (Developer)**.  
   - Preencha as informações do projeto (nome, URL, uso previsto, etc.).  
   - Após aprovação, sua **API Key** será exibida.

5. **Adicione ao seu `.env`**  
   Conforme instruído na seção "Como Rodar o Projeto", adicione a chave ao arquivo `.env` do backend.
   
## 🛠️ Como Rodar o Projeto

### Pré-requisitos
- Docker e Docker Compose instalados
- Git instalado

### 1. Clone o repositório

git clone [https://github.com/gugalxp-dev/movie-catalog.git](https://github.com/gugalxp-dev/movie-catalog.git)

```bash
cd movie-catalog
```
### 2. Configure as variáveis de ambiente

#### No Frontend, acesse (`frontend/.env`)
```bash
VITE_API_BASE_URL=http://localhost:8000/api
```

#### No Backend, acesse (`backend/.env.exemple`)
 - Renomeie ```.env.exemple``` para ```.env```
  
Em seguida, faça as seguintes mudanças:

```bash
# TMDB Key
TMDB_API_KEY=sua_chave_TMDB_aqui

# Credenciais DB
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=movie_catalog
DB_USERNAME=root
DB_PASSWORD=a90f4beccbfcb567812eb08c292
```

### 3. Suba os containers
```bash
docker-compose up -d --build
```

#### Em seguida, gere a *APP_KEY* do Laravel:
```
 docker exec -it laravel-app php artisan key:generate
```

### 4. Execute as migrations
```bash
docker exec -it laravel-app php artisan migrate
```

Importante, após rodar o docker, o docker composer subirá 4 containers (Frontend, Backend, Mysql e Phpadmin). Então, não precisará de comandos externos para rodar o Vue.js ou qualquer outra coisa.

A partir daqui, a aplicação já está 100% acessível.

### 5. Acesse a aplicação
- **Frontend**: http://localhost:3000
- **Backend API**: http://localhost:8000/api
- **phpMyAdmin**: http://localhost:8080

## 📂 Onde Encontrar as Principais Partes do Código

### **Backend (Laravel)**

- **Controllers**  
  Local: `backend/app/Http/Controllers`  
  - `MovieController.php` → Rotas para busca de filmes e listagem de gêneros.  
  - `FavoriteMovieController.php` → Rotas para gerenciar favoritos (adicionar, remover, listar, verificar).

- **Services**  
  Local: `backend/app/Services`  
  - `TMDBService.php` → Conexão com a API do TMDB para buscar filmes e gêneros.  
  - `FavoriteMovieService.php` → Lógica de negócios para manipulação de favoritos.

- **Rotas da API**  
  Local: `backend/routes/api.php`  
  - Define todos os endpoints listados na seção **Documentação da API**.

- **Models**  
  Local: `backend/app/Models`  
  - `FavoriteMovie.php` → Representa os filmes salvos como favoritos no banco de dados.

- **Configurações**  
    Local: `backend/.env` 

### **Frontend (Vue.js)**

- **Páginas e Componentes**  
  Local: `frontend/src`  
  - `pages/Home.vue` → Página principal de busca de filmes.  
  - `pages/Favorites.vue` → Página de listagem e filtro de favoritos.  
  - `components/MovieCard.vue` → Exibição individual de filmes.  
  - `components/SearchBar.vue` → Campo de busca.

- **Stores (Pinia)**  
  Local: `frontend/src/stores`  
  - `movies.js` → Gerencia estado da busca de filmes e lista de favoritos.  

- **Services (Axios)**  
  Local: `frontend/src/services`  
  - `/movies/api.js` → Chamadas à API para buscar filmes e gêneros.  
  - `/favorites/api.js` → Chamadas à API para gerenciar favoritos.

- **Configurações**  
  Local: `frontend/.env`  
  - Variáveis de ambiente do frontend, incluindo `VITE_API_BASE_URL`.

## 📚 Documentação da API

### Endpoints Principais

#### Rotas de Filmes

```
GET /api/movies/search?query={termo}&page={pagina}   # Buscar filmes por nome
GET /api/movies/genres                               # Listar gêneros
GET /api/movies/now-playing-movies                   # Filmes em cartaz
GET /api/movies/{id}                                 # Detalhes de um filme
```

#### Rotas de Favoritos

```
GET /api/favorites                                   # Listar favoritos
POST /api/favorites                                  # Adicionar aos favoritos
DELETE /api/favorites/{tmdbId}                       # Remover dos favoritos
GET /api/favorites/check/{tmdbId}                    # Verificar se é favorito
```

## 🧪 Como Testar

### Testes Manuais
1. Acesse o frontend
2. Busque por um filme (ex: "Batman")
3. Adicione filmes aos favoritos
4. Acesse a página de favoritos
5. Teste o filtro por gênero
6. Remova filmes dos favoritos

# Obrigado!

