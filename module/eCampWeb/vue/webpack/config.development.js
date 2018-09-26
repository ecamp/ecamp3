const merge = require('webpack-merge')
const webpack = require('webpack')
const WebpackAssetsManifest = require('webpack-assets-manifest')

const WEBPACK_DEV_SERVER = 'http://localhost:8080'

module.exports = function () {
  process.env.NODE_ENV = 'development'

  return merge(require('./config.base.js'), {
    mode: 'development',
    watch: true,

    output: {
      filename: 'js/main.js',
      chunkFilename: 'js/[name].bundle.js',
      publicPath: WEBPACK_DEV_SERVER + '/assets/module/web/'
    },

    module: {
      rules: [
        {
          test: /\.(sass|scss|css)$/,
          use: [
            'vue-style-loader',
            'css-loader',
            'sass-loader'
          ]
        }
      ]
    },

    plugins: [
      new webpack.HotModuleReplacementPlugin(),
      new WebpackAssetsManifest({
        output: 'assets.json',
        writeToDisk: true,
        publicPath: true,
        assets: { 'webpack-hot-reload': WEBPACK_DEV_SERVER + '/webpack-dev-server.js' }
      })
    ],

    devtool: '#cheap-module-eval-source-map',

    devServer: {
      contentBase: false,
      hot: true,
      headers: {
        'Access-Control-Allow-Origin': '*'
      }
    }
  })
}
