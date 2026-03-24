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

  // Nitro でバックエンドへのリバースプロキシを設定
  // クライアントからのリクエストは同一オリジン(フロントエンド)経由で送られるため
  // CORS問題が発生しない。CookieもSame-Originで動作する。
  nitro: {
    routeRules: {
      '/api/**': { proxy: `${process.env.NUXT_API_BASE_URL || 'http://localhost:8900'}/api/**` },
      '/sanctum/**': { proxy: `${process.env.NUXT_API_BASE_URL || 'http://localhost:8900'}/sanctum/**` },
    },
  },

  devServer: {
    port: 3900,
  },
})
