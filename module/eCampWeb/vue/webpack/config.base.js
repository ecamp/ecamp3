const path = require('path');
const VueLoaderPlugin = require('vue-loader/lib/plugin');
const WebpackAssetsManifest = require('webpack-assets-manifest');

module.exports = {
    entry: './src/main.js',
    output: {
        path: path.resolve(__dirname, '../../assets'),
    },
    module: {
        rules: [
            {
                test: /\.vue$/,
                loader: 'vue-loader',
            },
            {
                test: /\.js$/,
                loader: 'babel-loader',
            },
            {
                test: /\.(png|jpg)$/,
                loader: 'file-loader',
                options: {
                    name: 'images/[name].[hash].[ext]'
                }
            }
        ]
    },
    resolve: {
        extensions: ['.js', '.vue', '.json'],
        alias: {
            '@': path.resolve(__dirname, '..')
        }
    },

    plugins: [
        new VueLoaderPlugin(),
        new WebpackAssetsManifest( {
            output: 'assets.json',
            writeToDisk: true,
        } )
    ]
};
