const path = require('path')

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
    devtool: 'source-map'
  },

  transpileDependencies: [
    'vuetify'
  ],

  chainWebpack: (config) => {
    // disable resolving symlinks to their real path
    // setting this to 'true' generates issues with usage of "common" folder
    // settings this to 'false', a symlinked folder behaves like a normal subfolder
    config.resolve.symlinks(false)

    // install vue-svg-loader
    const svgRule = config.module.rule('svg')

    svgRule.uses.clear()

    svgRule
      .use('babel-loader')
      .loader('babel-loader')
      .end()
      .use('vue-svg-loader')
      .loader('vue-svg-loader')

    config.module
      .rule('source-map-loader')
      .test(/\.(js)$/)
      .enforce('pre')
      .include
      .add(path.resolve(__dirname, 'node_modules/hal-json-vuex'))
      .end()
      .use('source-map-loader')
      .loader('source-map-loader')
      .end()
  },

  pluginOptions: {
    jestSerializer: {
      attributesToClear: [
        'id',
        'for'
      ],
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
