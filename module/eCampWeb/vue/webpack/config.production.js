const merge = require('webpack-merge');
const path = require('path');
const MiniCssExtractPlugin = require("mini-css-extract-plugin");
const AssetsPlugin = require( 'assets-webpack-plugin' );

module.exports = function() {
    process.env.NODE_ENV = 'production';

    return merge(require('./config.base.js'), {
        mode: 'production',

        output: {
            filename: 'js/main.min.js?[chunkhash]',
        },

        module: {
            rules: [
                {
                    test: /\.(sass|scss|css)$/,
                    use: [
                        MiniCssExtractPlugin.loader,
                        'css-loader',
                        'sass-loader',
                    ]
                },
            ]
        },

        plugins: [
            new MiniCssExtractPlugin( {
                filename: 'css/style.min.css?[contenthash]'
            } ),
            new AssetsPlugin( {
                filename: 'assets.json',
                path: path.resolve( __dirname, '../../assets' ),
                fullPath: false
            } )
        ],

        devtool: false,
    });
};