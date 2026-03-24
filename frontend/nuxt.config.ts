// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  compatibilityDate: '2025-05-15',

  future: {
    compatibilityVersion: 4,
  },

  devtools: { enabled: true },

  modules: [
    '@nuxt/eslint',
    '@nuxt/test-utils/module',
  ],

  css: ['~/assets/css/main.css'],

  vite: {
    plugins: [
      // @ts-expect-error - tailwindcss vite plugin
      (await import('@tailwindcss/vite')).default(),
    ],
  },

  runtimeConfig: {
    // サーバーサイドのみ: Nitro proxy がバックエンドへ転送する際のURL
    // 環境変数 NUXT_API_BASE_URL で上書き可能
    apiBaseUrl: 'http://localhost:8900',
  },

  // バックエンドへのリバースプロキシは server/routes/ で実装
  // runtimeConfig.apiBaseUrl をランタイムで参照するため、
  // Cloud Run のように環境変数がランタイムで注入される環境でも正しく動作する
  // （routeRules.proxy はビルド時に値が確定するため本番では使えなかった）

  devServer: {
    port: 3900,
  },
})
