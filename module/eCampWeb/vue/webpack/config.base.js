const path = require('path');

module.exports = function(generateLoader) {
    return {
        entry: [
            './src/entry.js',
            './src/main.js',
        ],
            output: {
        path: path.resolve(__dirname, '../../assets'),
            library: 'eCamp'
    },
        module: {
            rules: [
                {
                    test: /\.vue$/,
                    loader: 'vue-loader',
                    options: {
                        loaders: {
                            css: generateLoader(),
                            scss: generateLoader('sass')
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
                    use: generateLoader()
                },
                {
                    test: /\.scss$/,
                    use: generateLoader( 'sass' )
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
    };
};
