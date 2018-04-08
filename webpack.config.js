var path = require('path');
var webpack = require('webpack');
var ExtractTextPlugin = require('extract-text-webpack-plugin');
var ManifestPlugin = require('webpack-manifest-plugin');
var isProduction = process.env.NODE_ENV === 'production';

function resolve (dir) {                     
  return path.join(__dirname,  dir)     
} 

module.exports = {
    devtool: isProduction ? false : 'source-map',
    entry: {
        browse: ['./resources/assets/js/browse.js'],
        vendor: ['moment', 'js-url']
    },
    output: {
        path: path.resolve(__dirname, './publishable/assets'),
        publicPath: '/dist/',
        filename: !isProduction ? '[name].js' : '[name]-[hash].js'
        // chunkFilename: !isProduction ? '[name].js' : '[name]-[chunkhash].js',
    },
    module: {
        rules: [{
            test: /\.css$/,
            loader: 'css-loader'
        }, {
            test: /\.scss$/,
            use: ExtractTextPlugin.extract({
                use: [{
                    loader: 'css-loader',
                    options: {
                        sourceMap: isProduction ? false : true
                    }
                }, {
                    loader: 'sass-loader',
                    options: {
                        sourceMap: isProduction ? false : true
                    }
                }],
                fallback: "style-loader"
            }),
        }, {
            test: /\.(png|jpg|svg|gif|eot|woff|woff2|ttf)$/,
            loader: [
                "file-loader",
                {
                    loader: "image-webpack-loader",
                    options: {
                        bypassOnDebug: !isProduction
                    }
                }
            ]
        }, {
            test: /\.(js|vue)$/,
            loader: 'eslint-loader',
            enforce: 'pre',
            options: {
                formatter: require('eslint-friendly-formatter'),
                failOnWarning:true,
                failOnError: true
            },
            exclude: /node_modules/
        }, {
            test: /\.js$/,
            loader: 'babel-loader',
            include: [resolve('resources/assets/js'), resolve('node_modules/vue-awesome')],
            query: {
                compact: false
            }
        }, {
            test: /\.vue$/,
            loader: 'vue-loader',
        }, {
            test: /\.json$/,
            loader: 'json-loader'
        }]
    },
    plugins: [
        // new webpack.optimize.CommonsChunkPlugin({
        //     name: ['vendor']
        // }),
        // new ExtractTextPlugin(!isProduction ? '[name].css' : '[name]-[hash].css'),
        new ManifestPlugin({
            fileName: 'rev-manifest.json', // 该名称不可以改，Laravel 5.3需要引用该名称的文件
            baseName: '/'
        }),
        new webpack.DefinePlugin({
            'process.env': {
                NODE_ENV: isProduction ? '"production"' : '"development"'
            }
        })
    ],
    resolve: {
      extensions: ['.js', '.vue', '.json'],
			alias: {                                 
				'vue$': 'vue/dist/vue.esm.js'
			}
    },
}
