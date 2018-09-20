const merge = require('webpack-merge');
const path = require('path');
const webpack = require('webpack');
const AssetsPlugin = require( 'assets-webpack-plugin' );

function makeStyleLoader( type ) {
    const loaders = [ 'vue-style-loader', { loader: 'css-loader' } ];
    if ( type ) {
        loaders.push(type + '-loader');
    }

    return loaders;
}

module.exports = function() {
    process.env.NODE_ENV = 'development';

    return merge(require('./config.base.js')(makeStyleLoader), {
        mode: 'development',
        watch: true,

        output: {
            filename: 'js/main.js'
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
