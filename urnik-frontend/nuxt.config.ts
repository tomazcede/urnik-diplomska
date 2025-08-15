import tailwindcss from "@tailwindcss/vite";

export default defineNuxtConfig({
  devtools: { enabled: true },
  modules: [
      '@pinia/nuxt',
  ],
  vite: {
    plugins: [
        tailwindcss(),
    ],
  },
  css: [
      'bootstrap/dist/css/bootstrap.min.css',
      '/public/css/main.css'
  ],
  app: {
    head: {
      script: [
        { src: 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js' }
      ]
    }
  },
  pinia: {
    storesDirs: ['./stores/**'],
  },
  runtimeConfig: {
    public: {
      apiUrl: process.env.API_URL || 'http://127.0.0.1:8000'
    }
  }
})