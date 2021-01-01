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

  pluginOptions: {
    i18n: {
      locale: 'de',
      fallbackLocale: 'en',
      localeDir: 'locales',
      enableInSFC: true
    }
  },

  css: {
    sourceMap: true
  }
}
