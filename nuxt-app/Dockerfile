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

ENV PORT=3001
ENV HOST=0.0.0.0

EXPOSE 3001

CMD ["sh", "-c", "npm run build && npm start"]