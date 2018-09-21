const merge = require('webpack-merge');
const path = require('path');
const AssetsPlugin = require( 'assets-webpack-plugin' );

module.exports = function() {
    process.env.NODE_ENV = 'production';

    return merge(require('./config.base.js'), {
        mode: 'production',

        output: {
            filename: 'js/main.min.js?[chunkhash]'
        },

        plugins: [
            new AssetsPlugin( {
                filename: 'assets.json',
                path: path.resolve( __dirname, '../../assets' ),
                fullPath: false
            } )
        ],

        devtool: false,
    });
};