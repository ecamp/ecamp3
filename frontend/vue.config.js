module.exports = {
  devServer: {
    allowedHosts: [
      'ecamp3',
      'localhost',
      '127.0.0.1'
    ]
  },
  configureWebpack: {
    devtool: 'source-map'
  },
  chainWebpack: (config) => {
    const svgRule = config.module.rule('svg');

    svgRule.uses.clear();

    svgRule
      .use('babel-loader')
      .loader('babel-loader')
      .end()
      .use('vue-svg-loader')
      .loader('vue-svg-loader')
  }
}
