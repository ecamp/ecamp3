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
  css: ['~/assets/tailwind.css', '~/assets/typography.css', '~/assets/print-preview.css'],
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

    // Doc: https://i18n.nuxtjs.org/basic-usage
    [
      '@nuxtjs/i18n',
      {
        locales: ['en'], // only 'en' to avoid that Nuxt generates URLs for each locale. We only need the vueI18n library directly
        defaultLocale: 'en',
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
    config: {
      environment: process.env.SENTRY_ENVIRONMENT ?? 'http://localhost:3000',
    },
  },

  /*
   ** vuetify module configuration
   ** https://github.com/nuxt-community/vuetify-module
   */
  vuetify: {
    customVariables: ['~/assets/variables.scss'],
    treeShake: {
      components: ['VCalendar', 'VSheet', 'VContainer', 'VCol', 'VRow'],
    },
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
    // TODO: enable again when webpack supports node 18 with sourcemaps.
    // extend(config, ctx) {
    //   // include source map in development mode
    //   // eslint-disable-next-line no-constant-condition
    //   if (ctx.isDev) {
    //     config.devtool = ctx.isClient ? 'source-map' : 'inline-source-map'
    //   }
    // },
    extend(config) {
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
    },
    postcss: {
      plugins: {
        tailwindcss: {},
        autoprefixer: {},
      },
    },
  },

  /*
   ** Render configuration
   ** See https://nuxtjs.org/api/configuration-render/
   */
  render: {
    // deactivates injecting nuxt Javascript on client side ==> pure HTML/CSS output only (except explicit head-scripts)
    injectScripts: false,
  },

  /**
   * Environment variables available in application
   */
  env: {
    FRONTEND_URL: process.env.FRONTEND_URL || 'http://localhost:3000',
  },
}
