import svgLoader from 'vite-svg-loader'

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
  plugins: [{ src: '~/plugins/hal-json-vuex.js' }, { src: '~/plugins/dayjs.js' }],

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

  features: {
    // deactivates injecting nuxt Javascript on client side ==> pure HTML/CSS output only (except explicit head-scripts)
    noScripts: true,
  },
  routeRules: {
    '/**': { experimentalNoScripts: true },
  },

  /**
   * Environment variables available in the server at runtime
   */
  runtimeConfig: {
    basicAuthToken: null,
    browserWsEndpoint: 'ws://browserless:3000',
    printUrl: 'http://print:3003/print',
    internalApiRootUrl: 'http://api:3000/api',
    cookiePrefix: 'localhost_',
    renderHtmlTimeoutMs: null,
    renderPdfTimeoutMs: null,
    sentry: {
      org: 'ecamp',
      project: 'ecamp3-print',
      dsn: null,
      environment: 'local',
      authToken: null,
      releaseName: 'development',
    },
  },

  telemetry: false,

  vite: {
    optimizeDeps: {
      include: [
        'dayjs',
        'dayjs/locale/de',
        'dayjs/locale/de-ch',
        'dayjs/locale/fr',
        'dayjs/locale/it',
        'dayjs/plugin/customParseFormat',
        'dayjs/plugin/duration',
        'dayjs/plugin/isBetween',
        'dayjs/plugin/isSameOrBefore',
        'dayjs/plugin/isSameOrAfter',
        'dayjs/plugin/localizedFormat',
        'dayjs/plugin/timezone',
        'dayjs/plugin/utc',
      ],
    },
    plugins: [
      svgLoader({
        defaultImport: 'raw',
      }),
    ],
  },

  vue: {
    compilerOptions: {
      whitespace: 'preserve',
      isCustomElement: (tag) =>
        tag.startsWith('rdf:') || tag.startsWith('cc:') || tag.startsWith('dc:'), // fixes svg lint errors
    },
  },
})
