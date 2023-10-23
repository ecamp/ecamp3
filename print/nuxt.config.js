export default defineNuxtConfig({
  app: {
    baseURL: '/print/',
  },
  css: [
    '~/assets/tailwind.css',
    '~/assets/typography.css',
    '~/assets/print-preview.css',
    '~/assets/calendar/CalendarDaily.sass',
    '~/assets/calendar/CalendarWithEvents.sass',
  ],
  plugins: [
    { src: '~/plugins/axios.js' }, // axios needs to load first, because the configured axios instance is utilized in hal-json-vuex
    { src: '~/plugins/hal-json-vuex.js' },
    { src: '~/plugins/dayjs.js' },
  ],

  components: [
    { path: '~/components/config', prefix: 'Config', global: 'true' },
    { path: '~/components/Toc', prefix: 'Toc', global: 'true' },
    '~/components',
  ],

  modules: [
    // Doc: https://github.com/nuxt-community/eslint-module
    '@nuxtjs/eslint-module',

    // Doc: https://sentry.nuxtjs.org/guide/usage
    // Support for Nuxt3 not yet implemented: https://github.com/nuxt-community/sentry-module/issues/619
    // '@nuxtjs/sentry',

    '@nuxtjs/tailwindcss',

    // Doc: https://i18n.nuxtjs.org/basic-usage
    [
      '@nuxtjs/i18n',
      {
        locales: [
          'en',
          'en-CH-scout',
          'de',
          'de-CH-scout',
          'fr',
          'fr-CH-scout',
          'it',
          'it-CH-scout',
        ],
        defaultLocale: 'en',
        strategy: 'no_prefix',
        vueI18n: '~/locales/vueI18nConfig.js',
      },
    ],
  ],
  router: {
    middleware: 'i18n',
  },

  /*
   ** Sentry module configuration
   ** See https://sentry.nuxtjs.org/sentry/options
   */
  // sentry: {
  //   dsn: import.meta.env.SENTRY_PRINT_DSN || '',
  //   disabled: import.meta.env.NODE_ENV === 'development',
  //   config: {
  //     environment: import.meta.env.SENTRY_ENVIRONMENT ?? 'local',
  //   },
  //   serverIntegrations: {
  //     CaptureConsole: {
  //       levels: ['warn', 'error'],
  //     },
  //   },
  // },
  /*
   ** Build configuration
   ** See https://nuxtjs.org/api/configuration-build/
   */
  build: {
    extend(config, ctx) {
      if (ctx.isDev) {
        config.devtool = ctx.isClient ? 'source-map' : 'inline-source-map'
      }

      // const sentryAuthToken = import.meta.env.SENTRY_AUTH_TOKEN
      // if (sentryAuthToken) {
      //   config.plugins.push(
      //     sentryWebpackPlugin({
      //       authToken: sentryAuthToken,
      //       org: import.meta.env.SENTRY_ORG || 'ecamp',
      //       project: import.meta.env.SENTRY_PRINT_PROJECT || 'ecamp3-print',
      //       telemetry: false,
      //       sourcemaps: {
      //         assets: ['.nuxt/**/*'],
      //       },
      //       release: {
      //         name: import.meta.env.SENTRY_RELEASE_NAME || 'development',
      //       },
      //     })
      //   )
      // }
    },
  },

  experimental: {
    // deactivates injecting nuxt Javascript on client side ==> pure HTML/CSS output only (except explicit head-scripts)
    noScripts: true,
  },

  /**
   * Environment variables available in the server at runtime
   */
  runtimeConfig: {
    basicAuthToken: null,
    browserWsEndpoint: 'ws://browserless:3000',
    printUrl: 'http://print:3003/print',
    internalApiRootUrl: 'http://caddy:3000/api',
    cookiePrefix: 'localhost_',
    renderHtmlTimeoutMs: null,
    renderPdfTimeoutMs: null,
  },

  telemetry: false,

  vue: {
    compilerOptions: {
      isCustomElement: (tag) =>
        tag.startsWith('rdf:') || tag.startsWith('cc:') || tag.startsWith('dc:'), // fixes svg lint errors
    },
  },
})
