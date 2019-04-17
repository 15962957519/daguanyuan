var path = require('path')
var webpack = require('webpack')
var ImageminPlugin = require('imagemin-webpack-plugin').default
//import ImageminPlugin from 'imagemin-webpack-plugin'
var ExtractTextPlugin = require('extract-text-webpack-plugin');
var OptimizeCSSPlugin = require('optimize-css-assets-webpack-plugin')
var current = './src'
var HtmlWebpackPlugin = require('html-webpack-plugin')
var ssss = path.resolve(__dirname, '../public')
const vuxLoader = require('vux-loader')

let extractCSS = new ExtractTextPlugin(ssss + '/css/[name].css');
let extractLESS = new ExtractTextPlugin(ssss + '/css/[name].less');
let webpackConfig = {
    entry: {
        main: current + '/main.js',
        index: current + '/index.js',
        newsindex: current + '/newsindex.js'
    },
    output: {
        path: path.resolve(__dirname, '../public'),
        filename: 'static/js/plugin/[name].[chunkhash].js',
        chunkFilename:'static/js/plugin/[id].[chunkhash].js',
        //publicPath: 'http://w.datacdn.cn/', // This is used to generate URLs
        // publicPath: 'http://localhost/', // This is used to generate URLs
        publicPath: '/', // This is used to generate URLs
    },
    module: {
        rules: [
            {
                test: /\.less$/,
                use: [{
                    loader: "style-loader" // creates style nodes from JS strings
                }, {
                    loader: "css-loader" // translates CSS into CommonJS
                }, {
                    loader: "less-loader" // compiles Less to CSS
                }],
                test: /\.vue$/,
                loader: 'vue-loader',
                options: {
                    loaders: {
                        // Since sass-loader (weirdly) has SCSS as its default parse mode, we map
                        // the "scss" and "sass" values for the lang attribute to the right configs here.
                        // other preprocessors should work out of the box, no loader config like this nessessary.
                        'scss': 'vue-style-loader!css-loader!sass-loader',
                        'sass': 'vue-style-loader!css-loader!sass-loader?indentedSyntax',
                        // css: ExtractTextPlugin.extract({
                        //     loader: 'css-loader',
                        //     fallbackLoader: 'vue-style-loader' // <- this is a dep of vue-loader, so no need to explicitly install if using npm3
                        // })
                    }
                    // other vue-loader options go here
                }
            },
            {
                test: /\.css$/,
                use: [
                    'style-loader',
                    {
                        loader: 'css-loader',
                        //options: { root: '.' }
                    }
                ]
            },
            // {
            //     test: /\.css$/, loader: ExtractTextPlugin.extract({
            //     fallbackLoader: "style-loader",
            //     loader: "css-loader?sourceMap"
            // })
            // },
            {
                test: /\.js$/,
                exclude: /(node_modules|bower_components)/,
                loader: 'babel-loader',
                query: {
                    presets: ['es2015'],
                    plugins: ['transform-runtime']
                }
            },
            {
                test: /\.(png|jpg|gif|svg)$/,
                loader: 'url-loader?limit=8192',
                options: {
                    name: '/static/img/[name].[ext]?[hash]'
                }
            }
        ]
    },
    resolve: {
        alias: {
            'vue$': 'vue/dist/vue.common.js'
        }
    },
    devServer: {
        historyApiFallback: true,
        noInfo: true
    },
    plugins: [
        extractCSS
       //  extractLESS
    ],
    /*   performance: {
     hints: false
     }, */
    devtool: '#eval-source-map'
}

module.exports = webpackConfig
// require('shelljs/global')
//  rm('-rf', module.exports.output.path)
//  mkdir('-p', module.exports.output.path)
//  cp('-R', 'static/js', module.exports.output.path)

function production() {
    module.exports.devtool = '#source-map'
    // http://vue-loader.vuejs.org/en/workflow/production.html
    module.exports.plugins = (module.exports.plugins || []).concat([
        new webpack.DefinePlugin({
            'process.env': {
                NODE_ENV: '"production"'
            }
        }),
        // Make sure that the plugin is after any plugins that add images
        /* new ImageminPlugin({
         test: 'img/**',
         disable: false, // Disable during development
         pngquant: {
         quality: '95-100'
         }
         }), */
        new webpack.optimize.UglifyJsPlugin({
            sourceMap: true,
            compress: {
                warnings: false
            }
        }),
        //new ExtractTextPlugin(utils.assetsPath('css/[name].[contenthash].css')),
        new webpack.LoaderOptionsPlugin({
            minimize: true
        }),
        new HtmlWebpackPlugin({
            filename: path.resolve(__dirname, '../public/index.html'),
            template: 'index.html',
            inject: true,
            chunks: ['index'],
            minify: {
                removeComments: true,
                collapseWhitespace: true,
                removeAttributeQuotes: true
                // more options:
                // https://github.com/kangax/html-minifier#options-quick-reference
            },
            // necessary to consistently work with multiple chunks via CommonsChunkPlugin
            chunksSortMode: 'dependency'
        }),
        new HtmlWebpackPlugin({
            filename: path.resolve(__dirname, '../public/fabuindex.html'),
            template: 'fabuindex.html',
            chunks: ['main'],
            inject: true,
            minify: {
                removeComments: true,
                collapseWhitespace: true,
                removeAttributeQuotes: true
                // more options:
                // https://github.com/kangax/html-minifier#options-quick-reference
            },
            // necessary to consistently work with multiple chunks via CommonsChunkPlugin
            chunksSortMode: 'dependency'
        }),
        new HtmlWebpackPlugin({
            filename: path.resolve(__dirname, '../public/newsindex.html'),
            template: 'newsindex.html',
            chunks: ['newsindex'],
            inject: true,
            minify: {
                removeComments: true,
                collapseWhitespace: true,
                removeAttributeQuotes: true
                // more options:
                // https://github.com/kangax/html-minifier#options-quick-reference
            },
            // necessary to consistently work with multiple chunks via CommonsChunkPlugin
            chunksSortMode: 'dependency'
        }),
        new OptimizeCSSPlugin(),
    ])


}

module.exports = {
    postcss: [
        require('autoprefixer')({
            browsers: ['iOS >= 7', 'Android >= 4.1']
        })
    ]
}

module.exports = vuxLoader.merge(webpackConfig, {
    plugins: [
        {
            name: 'duplicate-style'
        }
    ],
    babel: {
        presets: ['es2015']
    },
})
production();