# Dockerfile simple pour Nuxt.js
FROM node:23-alpine

# Installer les dépendances système nécessaires
RUN apk add --no-cache libc6-compat curl

WORKDIR /app

# Copier les fichiers de dépendances
COPY package*.json ./

# Installer les dépendances
RUN npm install

# Copier le code source
COPY . .

# Exposer le port
EXPOSE 3000

# Configuration du Health Check
#HEALTHCHECK --interval=120s --timeout=30s --retries=5 \
#CMD curl -f http://127.0.0.1:3000/ || exit 1

# Build dynamique au démarrage pour utiliser les variables d'environnement
CMD ["sh", "-c", "npm run build && npm start"]