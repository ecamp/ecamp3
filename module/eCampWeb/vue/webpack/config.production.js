const merge = require('webpack-merge');
const MiniCssExtractPlugin = require("mini-css-extract-plugin");

module.exports = function() {
    process.env.NODE_ENV = 'production';

    return merge(require('./config.base.js'), {
        mode: 'production',

        output: {
            filename: 'js/main.min.[contenthash].js',
            publicPath: '../',
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
                filename: 'css/style.min.[contenthash].css'
            } ),
        ],

        devtool: false,
    });
};