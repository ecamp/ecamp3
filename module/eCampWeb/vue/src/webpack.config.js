const path = require('path');
const webpack = require('webpack');
const ExtractTextPlugin = require( 'extract-text-webpack-plugin' );
const AssetsPlugin = require( 'assets-webpack-plugin' );

module.exports = function (env = {}) {
    if (env.production) {
        process.env.NODE_ENV = 'production';
    }

    function makeStyleLoader( type ) {
        const cssLoader = {
            loader: 'css-loader',
            options: {
                minimize: env.production
            }
        };
        const loaders = [ cssLoader ];
        if ( type )
            loaders.push( type + '-loader' );
        if ( env.production ) {
            return ExtractTextPlugin.extract( {
                use: loaders,
                fallback: 'vue-style-loader'
            } );
        } else {
            return [ 'vue-style-loader' ].concat( loaders );
        }
    }

    return {
        entry: [
            './src/entry.js',
            './src/main.js'
        ],
        output: {
            path: path.resolve(__dirname, '../../assets'),
            filename: env.production ? 'js/main.min.js?[chunkhash]' : 'js/main.js',
            library: 'eCamp'
        },
        plugins: env.production ? [
            new webpack.optimize.UglifyJsPlugin({
                compress: {
                    warnings: false
                }
            }),
            new ExtractTextPlugin( {
                filename: 'css/style.min.css?[contenthash]'
            } ),
            new AssetsPlugin( {
                filename: 'assets.json',
                path: path.resolve( __dirname, '../../assets' ),
                fullPath: false
            } )
        ] : [
            new webpack.HotModuleReplacementPlugin(),
            new AssetsPlugin( {
                filename: 'assets.json',
                path: path.resolve( __dirname, '../../assets' ),
                fullPath: false
            } )
        ],
        devtool: env.production ? false
            : '#cheap-module-eval-source-map',
        module: {
            rules: [
                {
                    test: /\.vue$/,
                    loader: 'vue-loader',
                    options: {
                        loaders: {
                            css: makeStyleLoader(),
                            scss: makeStyleLoader('scss')
                        }
                    }
                },
                {
                    test: /\.js$/,
                    loader: 'babel-loader',
                    exclude: /node_modules/
                },
                {
                    test: /\.css$/,
                    use: makeStyleLoader()
                },
                {
                    test: /\.scss$/,
                    use: makeStyleLoader( 'scss' )
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
            extensions: [ '.js', '.vue', '.json' ],
            alias: {
                '@': path.resolve( __dirname, '..' )
            }
        },
        devServer: {
            contentBase: false,
            hot: true,
            headers: {
                'Access-Control-Allow-Origin': '*'
            }
        }
    };
};
