const MomentLocalesPlugin = require('moment-locales-webpack-plugin')
const availableLocales  = require('./src/plugins/i18n/availableLocales')

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
        localesToKeep: availableLocales,
      }),
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
    }
  },

  css: {
    sourceMap: true
  }
}
