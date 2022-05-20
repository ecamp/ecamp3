export default {
  /*
   ** Nuxt target
   ** See https://nuxtjs.org/api/configuration-target
   */
  target: 'server',
  /*
   ** Headers of the page
   ** See https://nuxtjs.org/api/configuration-head
   */
  head: {
    titleTemplate: '%s - ' + process.env.npm_package_name,
    title: process.env.npm_package_name || '',
    meta: [
      { charset: 'utf-8' },
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
    '~/assets/fonts.css',
    '~/assets/toc.css',
    '~/assets/typography.css',
  ],
  /*
   ** Plugins to load before mounting the App
   ** https://nuxtjs.org/guide/plugins
   */
  plugins: [
    { src: '~/plugins/hal-json-vuex.js' },
    { src: '~/plugins/i18n.js' },
    { src: '~/plugins/dayjs.js' },
    { src: '~/plugins/axios.js' },
  ],
  /*
   ** Auto import components
   ** See https://nuxtjs.org/api/configuration-components
   */
  components: true,
  /*
   ** Nuxt.js dev-modules
   */
  buildModules: [
    // Doc: https://github.com/nuxt-community/eslint-module
    '@nuxtjs/eslint-module',
    // Doc: https://github.com/nuxt-community/stylelint-module
    '@nuxtjs/stylelint-module',
    '@nuxtjs/vuetify',
    '@nuxt/postcss8', // used for tailwind
  ],
  /*
   ** Nuxt.js modules
   */
  modules: [
    // Doc: https://axios.nuxtjs.org/usage
    '@nuxtjs/axios',
    // Doc: https://sentry.nuxtjs.org/guide/usage
    '@nuxtjs/sentry',
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
   ** Axios module configuration
   ** See https://axios.nuxtjs.org/options
   */
  axios: {
    baseURL: process.env.INTERNAL_API_ROOT_URL || 'http://caddy:3001/',
    credentials: true,
  },
  /*
   ** Sentry module configuration
   ** See https://sentry.nuxtjs.org/sentry/options
   */
  sentry: {
    dsn: process.env.SENTRY_PRINT_DSN || '',
    disabled: process.env.NODE_ENV === 'development',
  },

  /*
   ** vuetify module configuration
   ** https://github.com/nuxt-community/vuetify-module
   */
  vuetify: {
    customVariables: ['~/assets/variables.scss'],
    treeShake: true,
    theme: {
      dark: false,
    },
    defaultAssets: false,
  },
  /*
   ** Build configuration
   ** See https://nuxtjs.org/api/configuration-build/
   */
  build: {
    extend(config, ctx) {
      // include source map in development mode
      if (ctx.isDev) {
        config.devtool = ctx.isClient ? 'source-map' : 'inline-source-map'
      }
    },
    postcss: {
      plugins: {
        tailwindcss: {},
        autoprefixer: {},
        cssnano: {
          // minifySelectors changes double colon :: to single colon (https://cssnano.co/docs/optimisations/minifyselectors/)
          // which throws an error in pagedjs (https://gitlab.coko.foundation/pagedjs/pagedjs/-/issues/305)
          preset: ['default', { minifySelectors: false }],
        },
      },
    },
  },

  /*
   ** Render configuration
   ** See https://nuxtjs.org/api/configuration-render/
   */
  render: {
    // in production: FALSE: deactivates injecting any Javascript on client side ==> pure HTML/CSS output only (except explicit head-scripts)
    // in development: TRUE: enable javascript injection in dev mode to support hot reloading
    // injectScripts: process.env.NODE_ENV === 'development',
    injectScripts: false,

    csp: {
      reportOnly: false,
      policies: {
        // allow embedding in iFrames
        'frame-ancestors': [
          process.env.FRONTEND_URL || 'http://localhost:3000',
        ],

        // allow script loading script from Unkpg (used for PagedJS)
        'script-src': ["'self'", "'unsafe-inline'", 'https://unpkg.com'],
      },
    },
  },

  /**
   * Environment variables available in application
   */
  env: {
    FRONTEND_URL: process.env.FRONTEND_URL || 'http://localhost:3000',
  },
}
