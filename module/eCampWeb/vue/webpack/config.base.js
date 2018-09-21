const path = require('path');
const MiniCssExtractPlugin = require("mini-css-extract-plugin");
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
                    name: 'images/[name].[ext]?[hash]'
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
        new MiniCssExtractPlugin({
            filename: 'css/style.min.css?[contenthash]'
        }),
        new VueLoaderPlugin(),
        new WebpackAssetsManifest( {
            output: 'assets.json',
            writeToDisk: true,
        } )
    ]
};
