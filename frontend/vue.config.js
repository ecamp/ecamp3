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
  }
}
