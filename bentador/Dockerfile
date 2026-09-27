# Local Compose targets this stage and mounts the source over /app.
FROM node:20-bookworm-slim AS dev

WORKDIR /app

COPY package*.json ./

RUN npm install

COPY . .

EXPOSE 3000

CMD ["npm", "run", "dev", "--", "--host", "0.0.0.0", "--port", "3000"]

# Build stage
FROM dev AS build

RUN npm run build

# Production stage
FROM nginx:alpine AS production

RUN mkdir /app

COPY nginx.conf /etc/nginx/conf.d/default.conf

COPY --from=build /app/dist /app

EXPOSE 3000

# Start nginx
CMD ["nginx", "-g", "daemon off;"]