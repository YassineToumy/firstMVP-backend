# RentGlobe — Guide de Déploiement

Application immobilière full-stack composée de deux services indépendants déployés via Docker sur un VPS.

---

## Architecture du projet

```
firstMVP/
├── laravel-api/        → API REST (PHP 8.4 + Apache)
├── nuxt-app/           → Frontend (Nuxt 3 + Node 23)
├── .env.exemple        → Template des variables d'environnement
├── .gitignore          → Fichiers exclus du dépôt git
└── README.md           → Ce fichier
```

### Ports exposés sur le serveur

| Service       | Port interne | Port externe | URL                        |
|---------------|-------------|-------------|----------------------------|
| Laravel API   | 80          | 8000        | `http://187.77.168.42`     |
| Nuxt Frontend | 3001        | 3001        | `http://187.77.168.42:3001`|
| PostgreSQL    | 5432        | 6543        | Accès direct DB            |

---

## Fichiers clés et leur rôle

### `laravel-api/Dockerfile`
Construit le conteneur du backend Laravel.

- Base : `php:8.4-apache`
- Active les modules Apache `rewrite` et `headers` (nécessaires pour Laravel)
- Installe les extensions PHP : `pdo_pgsql`, `zip`, `gd`, `exif`, `opcache`
- Installe Composer depuis l'image officielle
- Configure les limites PHP : upload 100MB, post 150MB, mémoire 512MB
- Configure le VirtualHost Apache pour pointer vers `/var/www/html/public`
- Lance `composer install --no-dev --optimize-autoloader` à la construction
- **Au démarrage du conteneur** : vide les caches Laravel, puis exécute `php artisan migrate --force` automatiquement avant de démarrer Apache

```dockerfile
CMD ["sh", "-c", "php artisan config:clear && php artisan route:clear && php artisan migrate --force; apache2-foreground"]
```

### `laravel-api/docker-compose.yml`
Orchestrateur pour le service Laravel.

- Build depuis le Dockerfile local
- Mappe le port `8000` (hôte) → `80` (conteneur)
- `restart: unless-stopped` : redémarre automatiquement après crash ou reboot serveur

### `nuxt-app/Dockerfile`
Construit le conteneur du frontend Nuxt.

- Base : `node:23-alpine` (image légère)
- Installe `libc6-compat` et `curl` (dépendances système)
- Copie `package.json` et installe les dépendances npm
- Copie le code source
- **Au démarrage** : exécute `npm run build` puis `npm start` (build SSR + lancement du serveur)
- Écoute sur le port `3001`

```dockerfile
CMD ["sh", "-c", "npm run build && npm start"]
```

### `.env.exemple`
Template des variables d'environnement à copier sur le serveur. Ne jamais committer les vraies valeurs.

```env
API_URL=http://187.77.168.42
FRONTEND_URL=http://187.77.168.42:3000

POSTGRES_HOST=187.77.168.42
POSTGRES_PORT=6543
POSTGRES_DB=firstMVP
POSTGRES_USER=root
POSTGRES_PASSWORD=root

APP_KEY=base64:GENERATE_WITH_php_artisan_key:generate
APP_ENV=production
APP_DEBUG=false
SANCTUM_STATEFUL_DOMAINS=187.77.168.42:3000

NUXT_PUBLIC_API_BASE=http://187.77.168.42/api/v1
```

### `.gitignore`
Exclut du dépôt les fichiers sensibles et les dossiers générés :
- `.env` (racine, laravel-api, nuxt-app)
- `laravel-api/vendor/` — dépendances PHP (installées via Composer dans Docker)
- `nuxt-app/node_modules/` — dépendances JS (installées via npm dans Docker)
- `nuxt-app/.nuxt/` et `nuxt-app/.output/` — artefacts de build Nuxt
- Logs Laravel

---

## Procédure de déploiement

### Prérequis sur le serveur
- Docker installé
- Git installé
- Accès SSH au serveur
- PostgreSQL accessible sur le port `6543`

---

### Première installation

**1. Cloner le dépôt**
```bash
git clone https://github.com/YassineToumy/firstMVP-backend.git
cd firstMVP-backend
```

**2. Créer les fichiers `.env`**
```bash
# Laravel
cp laravel-api/.env.example laravel-api/.env
# Remplir les valeurs dans laravel-api/.env (DB_HOST, APP_KEY, etc.)

# Nuxt
cp .env.exemple nuxt-app/.env
# Remplir NUXT_PUBLIC_API_BASE dans nuxt-app/.env
```

**3. Générer la clé Laravel**
```bash
cd laravel-api
docker build -t laravel-api .
docker run --rm laravel-api php artisan key:generate --show
# Copier la clé générée dans laravel-api/.env → APP_KEY=
```

**4. Démarrer le backend Laravel**
```bash
cd laravel-api
docker-compose up -d --build
# Les migrations s'exécutent automatiquement au démarrage
```

**5. Démarrer le frontend Nuxt**
```bash
cd nuxt-app
docker build -t nuxt-app .
docker run -d \
  --name nuxt-app \
  --restart unless-stopped \
  -p 3001:3001 \
  --env-file .env \
  nuxt-app
```

---

### Mise à jour après un push git

```bash
# Se connecter au serveur
ssh user@187.77.168.42

# Récupérer les dernières modifications
cd firstMVP-backend
git pull origin main

# Rebuild et restart Laravel (si des fichiers PHP ont changé)
cd laravel-api
docker-compose down
docker-compose up -d --build

# Rebuild et restart Nuxt (si le frontend a changé)
cd ../nuxt-app
docker stop nuxt-app && docker rm nuxt-app
docker build -t nuxt-app .
docker run -d \
  --name nuxt-app \
  --restart unless-stopped \
  -p 3001:3001 \
  --env-file .env \
  nuxt-app
```

---

### Commandes utiles

```bash
# Voir les logs Laravel
docker-compose -f laravel-api/docker-compose.yml logs -f

# Voir les logs Nuxt
docker logs -f nuxt-app

# Vérifier les conteneurs actifs
docker ps

# Accéder au shell Laravel (artisan, migrations manuelles...)
docker exec -it laravel-api-app-1 bash
php artisan migrate:status
php artisan tinker

# Forcer un rebuild sans cache (si changements de Dockerfile)
docker build --no-cache -t laravel-api ./laravel-api
docker build --no-cache -t nuxt-app ./nuxt-app
```

---

## Stack technique

| Couche      | Technologie              | Version  |
|-------------|--------------------------|---------|
| Backend     | Laravel                  | 11.x    |
| Runtime PHP | PHP + Apache             | 8.4     |
| Frontend    | Nuxt.js (SSR)            | 3.x     |
| Runtime JS  | Node.js Alpine           | 23      |
| Base de données | PostgreSQL           | —       |
| Auth        | Laravel Sanctum          | —       |
| i18n        | @nuxtjs/i18n (fr/en/ar)  | —       |
| CSS         | Tailwind CSS             | v4      |
| Conteneurs  | Docker + Docker Compose  | —       |
