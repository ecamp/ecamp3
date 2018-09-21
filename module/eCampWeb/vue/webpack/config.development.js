const merge = require('webpack-merge');
const webpack = require('webpack');

module.exports = function() {
    process.env.NODE_ENV = 'development';

    return merge(require('./config.base.js'), {
        mode: 'development',
        watch: true,

        output: {
            filename: 'js/main.js',
            publicPath: 'http://localhost:8080/',
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
