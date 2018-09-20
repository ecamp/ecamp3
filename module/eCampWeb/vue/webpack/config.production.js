const merge = require('webpack-merge');
const path = require('path');
const webpack = require('webpack');
const ExtractTextPlugin = require( 'extract-text-webpack-plugin' );
const AssetsPlugin = require( 'assets-webpack-plugin' );

function makeStyleLoader( type ) {
    const cssLoader = {
        loader: 'css-loader',
        options: {
            minimize: true
        }
    };
    const loaders = [ cssLoader ];
    if ( type ) {
        loaders.push(type + '-loader');
    }
    return ExtractTextPlugin.extract( {
        use: loaders,
        fallback: 'vue-style-loader'
    } );
}

module.exports = function() {
    process.env.NODE_ENV = 'production';

    return merge(require('./config.base.js')(makeStyleLoader), {
        mode: 'production',

        output: {
            filename: 'js/main.min.js?[chunkhash]'
        },

        plugins: [
            new ExtractTextPlugin( {
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