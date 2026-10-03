# Product Management UI and API's

A full-stack web application built with Laravel (API backend) and Vue.js (SPA frontend).

The app uses Docker for containerized development and SQLite as the database.

## Documentation

| Document | Description |
|----------|-------------|
| [AGENT.md](./AGENT.md) | Guidelines for AI agents and contributors |
| [DESIGN.md](./DESIGN.md) | UI/UX and component standards |
| [ARCHITECTURE.md](./ARCHITECTURE.md) | System design and monorepo layout |
| [API.md](./API.md) | REST API reference |
| [FRONTEND.md](./FRONTEND.md) | Vue SPA routes, stores, and features |
| [BACKEND.md](./BACKEND.md) | Laravel API conventions |

The original single-file frontend mandates live in [bentador/GEMINI.md](./bentador/GEMINI.md) (index to the docs above).

## Getting Started

### 1. Clone the Repository

```bash
git clone https://github.com/cyrusmanatad/fyb-tech-exam.git

cd fyb-tech-exam
```

### 2. Running the Application (Laravel + Vue.js)

Ensure Docker is installed and running on your machine.

Then initialize the project:

```bash
./init.sh
```

### This script will:

- Build and start the Docker containers
- Install backend and frontend dependencies
- Run migrations
- Compile frontend assets
- Once finished, the application will be available at: `http://localhost:8000`

### Tech Stack

- Backend: Laravel 12, PHP 8.2+, JWT, Spatie Permissions
- Frontend: Vue 3, TypeScript, Vite, Pinia, Tailwind CSS 4 (`bentador/`)
- Web Server: NGINX
- Database: SQLite
- Containerization: Docker / Docker Compose

### Recent features

- **Profile Settings** (`/profile`) — Update name and email
- **Account Settings** (`/account`) — Change password, security info, preferences

See [FRONTEND.md](./FRONTEND.md) and [API.md](./API.md) for details.
