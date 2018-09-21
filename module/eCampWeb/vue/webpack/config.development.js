const merge = require('webpack-merge');
const path = require('path');
const webpack = require('webpack');
const AssetsPlugin = require( 'assets-webpack-plugin' );

module.exports = function() {
    process.env.NODE_ENV = 'development';

    return merge(require('./config.base.js'), {
        mode: 'development',
        watch: true,

        output: {
            filename: 'js/main.js',
        },

        module: {
            rules: [
                {
                    test: /\.(sass|scss|css)$/,
                    use: [
                        'vue-style-loader',
                        'css-loader',
                        'sass-loader',
                    ]
                },
            ]
        },

        plugins: [
            new webpack.HotModuleReplacementPlugin(),
            new AssetsPlugin( {
                filename: 'assets.json',
                path: path.resolve( __dirname, '../../assets' ),
                fullPath: false
            } )
        ],

        devtool: '#cheap-module-eval-source-map',

        devServer: {
            contentBase: false,
            hot: true,
            headers: {
                'Access-Control-Allow-Origin': '*'
            }
        }
    });
};
