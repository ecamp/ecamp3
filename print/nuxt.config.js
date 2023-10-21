import { sentryWebpackPlugin } from '@sentry/webpack-plugin'

export default defineNuxtConfig({
  /*
   ** Nuxt target
   ** See https://nuxtjs.org/api/configuration-target
   */
  target: 'server',
  devServer: {
    port: 3021,
  },
  /*
   ** Headers of the page
   ** See https://nuxtjs.org/api/configuration-head
   */
  meta: {
    titleTemplate: '%s - ' + process.env.npm_package_name,
    title: process.env.npm_package_name || '',
    meta: [
      { charset: 'utf-8' },
      { name: 'robots', content: 'noindex, nofollow' },
      { name: 'viewport', content: 'width=device-width, initial-scale=1' },
      {
        hid: 'description',
        name: 'description',
        content: process.env.npm_package_description || '',
      },
    ],
    link: [{ rel: 'icon', type: 'image/x-icon', href: '/favicon.ico' }],
  },
  /*
   ** Global CSS
   */
  css: [
    '~/assets/tailwind.css',
    '~/assets/typography.css',
    '~/assets/print-preview.css',
    '~/assets/calendar/CalendarDaily.sass',
    '~/assets/calendar/CalendarWithEvents.sass',
  ],
  /*
   ** Plugins to load before mounting the App
   ** https://nuxtjs.org/guide/plugins
   */
  plugins: [
    { src: '~/plugins/hal-json-vuex.js' },
    { src: '~/plugins/dayjs.js' },
    { src: '~/plugins/axios.js' },
  ],

  /*
   ** Nuxt.js modules
   */
  modules: [
    // Doc: https://github.com/nuxt-community/eslint-module
    '@nuxtjs/eslint-module',

    // Doc: https://sentry.nuxtjs.org/guide/usage
    // Support for Nuxt3: https://github.com/nuxt-community/sentry-module/issues/619
    // '@nuxtjs/sentry',

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

  /*
   ** Server Middleware
   */
  serverMiddleware: [{ path: '/server', handler: '~/server-middleware' }],

  /**
   * Router config
   * See https://nuxtjs.org/api/configuration-router/
   */
  router: {
    middleware: 'i18n',
  },

  /*
   ** Sentry module configuration
   ** See https://sentry.nuxtjs.org/sentry/options
   */
  sentry: {
    dsn: process.env.SENTRY_PRINT_DSN || '',
    disabled: process.env.NODE_ENV === 'development',
    config: {
      environment: process.env.SENTRY_ENVIRONMENT ?? 'local',
    },
    serverIntegrations: {
      CaptureConsole: {
        levels: ['warn', 'error'],
      },
    },
  },
  /*
   ** Build configuration
   ** See https://nuxtjs.org/api/configuration-build/
   */
  build: {
    extend(config, ctx) {
      if (ctx.isDev) {
        config.devtool = ctx.isClient ? 'source-map' : 'inline-source-map'
      }

      // TODO: remove once we update to nuxt 3
      config.module.rules.push({
        test: /colorjs\.io/,
        include: /node_modules/,
        loader: {
          loader: 'babel-loader',
          options: {
            presets: [['@babel/preset-env', { targets: { chrome: '58', ie: '11' } }]],
          },
        },
      })

      const sentryAuthToken = process.env.SENTRY_AUTH_TOKEN
      if (sentryAuthToken) {
        config.plugins.push(
          sentryWebpackPlugin({
            authToken: sentryAuthToken,
            org: process.env.SENTRY_ORG || 'ecamp',
            project: process.env.SENTRY_PRINT_PROJECT || 'ecamp3-print',
            telemetry: false,
            sourcemaps: {
              assets: ['.nuxt/**/*'],
            },
            release: {
              name: process.env.SENTRY_RELEASE_NAME || 'development',
            },
          })
        )
      }
    },
    transpile: ['vue-icon'],
    postcss: {
      postcssOptions: {
        plugins: {
          tailwindcss: {},
          autoprefixer: {},
        },
      },
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
    BASIC_AUTH_TOKEN: process.env.BASIC_AUTH_TOKEN,
  },

  telemetry: false,

  hooks: {
    /**
     * this may not work anymore with nuxt3
     */
    'render:routeDone'(url, result, context) {
      const fetchErrors = Object.entries(context.nuxt.fetch)
      const errors = fetchErrors.map(([, entry]) => entry._error).filter((error) => error)
      if (errors.length > 0) {
        console.error(`fetch errors during rendering: `, errors)
      }
    },
  },
})
