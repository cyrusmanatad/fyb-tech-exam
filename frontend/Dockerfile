# Build stage
FROM node:20-bookworm-slim AS build

# Set working directory
WORKDIR /app

# Copy package files
COPY package*.json ./

# Install dependencies
RUN npm install

# Copy project files
COPY . .

# Build the application for production
RUN npm run build

# Production stage
FROM nginx:alpine AS production

RUN mkdir /app

COPY nginx.conf /etc/nginx/conf.d/default.conf

COPY --from=build /app/dist /app

EXPOSE 3000

# Start nginx
CMD ["nginx", "-g", "daemon off;"]