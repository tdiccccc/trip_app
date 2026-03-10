// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  compatibilityDate: '2025-05-15',

  future: {
    compatibilityVersion: 4,
  },

  devtools: { enabled: true },

  modules: [
    '@nuxt/eslint',
  ],

  css: ['~/assets/css/main.css'],

  vite: {
    plugins: [
      // @ts-expect-error - tailwindcss vite plugin
      (await import('@tailwindcss/vite')).default(),
    ],
  },

  runtimeConfig: {
    public: {
      apiBase: 'http://localhost:8900',
    },
  },

  devServer: {
    port: 3900,
  },
})
