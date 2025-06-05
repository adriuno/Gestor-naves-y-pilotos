// https://nuxt.com/docs/api/configuration/nuxt-config

export default defineNuxtConfig({
  devtools: { enabled: true },

  modules: [
    '@nuxt/ui',
    '@nuxt/eslint',
    '@nuxt/image',
    '@nuxt/icon',
    '@nuxt/fonts',
    '@nuxtjs/color-mode',
  ],

  css: ['~/assets/css/main.css'],

  future: {
    compatibilityVersion: 4
  },

  compatibilityDate: '2024-11-27',

  runtimeConfig: {
    public: {
      // @ts-expect-error: process.env is used for runtimeConfig in Nuxt
      // ahora si por lo que sea no existe esa variable, tirará de localhost !!
      API_URL: process.env.NUXT_API_URL || 'http://localhost:8000'
    }
  },

  // ✅ Añadimos Font Awesome aquí
  app: {
    head: {
      link: [
        {
          rel: 'stylesheet',
          href: 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css',
        },
      ],
    },
  },
})
