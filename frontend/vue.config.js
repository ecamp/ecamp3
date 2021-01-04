const MomentLocalesPlugin = require('moment-locales-webpack-plugin')

module.exports = {
  devServer: {
    useLocalIp: false,
    allowedHosts: [
      'ecamp3',
      'localhost',
      '127.0.0.1'
    ]
  },

  configureWebpack: {
    devtool: 'source-map',
    plugins: [
      new MomentLocalesPlugin({
        localesToKeep: ['de', 'de-CH', 'en', 'fr', 'it']
      })
    ]
  },

  transpileDependencies: [
    'vuetify'
  ],

  chainWebpack: (config) => {
    // install vue-svg-loader
    const svgRule = config.module.rule('svg')

    svgRule.uses.clear()

    svgRule
      .use('babel-loader')
      .loader('babel-loader')
      .end()
      .use('vue-svg-loader')
      .loader('vue-svg-loader')
  },

  pluginOptions: {
    i18n: {
      locale: 'de',
      fallbackLocale: 'en',
      localeDir: 'locales',
      enableInSFC: true
    },
    jestSerializer: {
      attributesToClear: ['id'],
      formatting: {
        indent_char: ' ',
        indent_inner_html: true,
        indent_size: 5,
        inline: [],
        sep: '\n',
        wrap_attributes: 'force-aligned'
      }
    }
  },

  css: {
    sourceMap: true
  }
}
