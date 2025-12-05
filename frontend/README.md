# Vue Job Listing

A simple and responsive Job Listing CRUD (Create, Read, Update, Delete) application built with Vue 3, Vite, and Tailwind CSS. This project allows users to manage job postings with full CRUD functionality, featuring a clean UI, reactive data handling, and modular component structure. It uses a `json-server` to mock a REST API for jobs data.

## Tech Stack

-   **Frontend:**
    -   [Vue 3](https://v3.vuejs.org/)
    -   [Vue Router](https://router.vuejs.org/) for routing
    -   [Vite](https://vitejs.dev/) as the build tool
    -   [Tailwind CSS](https://tailwindcss.com/) for styling
    -   [Axios](https://axios-http.com/) for making HTTP requests
-   **Backend (Mock API):**
    -   [json-server](https://github.com/typicode/json-server)

## Project Structure

The project is structured as follows:

-   `src/components`: Contains reusable Vue components.
-   `src/views`: Contains the pages for different routes.
-   `src/router`: Contains the Vue Router configuration.
-   `src/jobs.json`: The data source for the mock API.

## Getting Started

### Prerequisites

-   Node.js and npm (or yarn) installed.

### Installation

1.  Clone the repository:
    ```bash
    git clone https://github.com/your-username/vue-job-listing.git
    ```
2.  Navigate to the project directory:
    ```bash
    cd vue-job-listing
    ```
3.  Install the dependencies:
    ```bash
    npm install
    ```

### Development

To run the development server and the mock API concurrently, you can use two terminals:

**Terminal 1: Run the mock API**

```bash
npm run server
```

This will start the `json-server` on `http://localhost:5000`.

**Terminal 2: Run the Vue development server**

```bash
npm run dev
```

This will start the Vite development server, and you can access the application at the URL provided in the terminal (usually `http://localhost:5173`).

### Building for Production

To build the application for production, run:

```bash
npm run build
```

This will create a `dist` directory with the production-ready files.

### Previewing the Production Build

To preview the production build locally, run:

```bash
npm run preview
```