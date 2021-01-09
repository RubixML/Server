const VueLoaderPlugin = require('vue-loader/lib/plugin');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const TerserJSPlugin = require('terser-webpack-plugin');
const OptimizeCSSAssetsPlugin = require('optimize-css-assets-webpack-plugin');
const MomentLocalesPlugin = require('moment-locales-webpack-plugin');
const CompressionPlugin = require('compression-webpack-plugin');
const CopyPlugin = require('copy-webpack-plugin');

const path = require('path');

const assetsDir = path.resolve(__dirname, 'assets');

module.exports = [{
    name: 'app',
    mode: process.env.NODE_ENV,
    entry: {
        app: './ui/app.ts',
    },
    resolve: {
        extensions: [
            '.ts', '.js', '.vue', '.json',
        ],
        alias: {
            vue: 'vue/dist/vue.esm.js',
        },
    },
    output: {
        path: assetsDir,
        publicPath: '/ui/',
        filename: 'app.js',
    },
    module: {
        rules: [
            { 
                test: /\.(ts|js)$/,
                exclude: /node_modules/,
                loader: 'babel-loader',
                options: {
                    presets: [
                        '@babel/env',
                        '@babel/preset-typescript',
                        'babel-preset-typescript-vue',
                    ],
                    plugins: [
                        '@babel/plugin-transform-typescript',
                    ],
                },
            },
            {
                test: /\.vue$/,
                loader: 'vue-loader',
            },
            {
                test: /\.(scss|sass)$/,
                use: [
                    MiniCssExtractPlugin.loader,
                    {
                        loader: 'css-loader',
                    },
                    {
                        loader: 'sass-loader',
                        options: {
                            sourceMap: process.env.NODE_ENV === 'development',
                        },
                    },
                ],
            },
            {
                test: /\.png$/,
                loader: 'file-loader',
                options: {
                    name: '[name].[ext]',
                    outputPath: '/images/',
                    publicPath: '/images/',
                    esModule: false,
                },
            },
            {
                test: /\.svg$/,
                loader: 'svg-url-loader',
            },
            {
                test: /\.(woff|woff2)$/,
                loader: 'file-loader',
                options: {
                    name: '[name].[ext]',
                    outputPath: '/fonts/',
                    publicPath: '/fonts/',
                },
            },
        ],
    },
    plugins: [
        new VueLoaderPlugin(),
        new MomentLocalesPlugin({
            localesToKeep: ['en'],
        }),
        new MiniCssExtractPlugin('app.css'),
        new CopyPlugin({
            patterns: [
                { from: 'ui/app.html', to: 'app.html' },
                { from: 'ui/manifest.json', to: 'manifest.json' },
                { from: 'ui/images/app-icon-apple-touch.png', to: 'images/' },
                { from: 'ui/images/app-icon-medium.png', to: 'images/' },
                { from: 'ui/images/app-icon-large.png', to: 'images/' },
                { from: 'ui/sounds/sharp.ogg', to: 'sounds/' },
            ],
        }),
        new CompressionPlugin({
            include: [
                'app.js', 'app.css',
            ],
            filename: '[path][base].gz',
            algorithm: 'gzip',
            compressionOptions: {
                level: process.env.NODE_ENV === 'development' ? 1 : 9,
            },
            minRatio: Infinity,
            deleteOriginalAssets: false,
        }),
    ],
    optimization: {
        minimizer: [
            new TerserJSPlugin(),
            new OptimizeCSSAssetsPlugin(),
        ],
    },
    performance: {
        hints: false,
    },
}, {
    name: 'sw',
    mode: process.env.NODE_ENV,
    entry: {
        sw: './ui/sw.ts',
    },
    resolve: {
        extensions: [
            '.ts', '.js', '.json',
        ],
    },
    output: {
        path: assetsDir,
        filename: 'sw.js',
    },
    module: {
        rules: [
            { 
                test: /\.(js|ts)$/,
                exclude: /node_modules/,
                loader: 'babel-loader',
                options: {
                    presets: [
                        '@babel/env',
                        '@babel/preset-typescript',
                    ],
                    plugins: [
                        '@babel/plugin-transform-typescript',
                    ],
                },
            },
        ],
    },
    plugins: [
        new CompressionPlugin({
            include: [
                'sw.js',
            ],
            filename: '[path][base].gz',
            algorithm: 'gzip',
            compressionOptions: {
                level: process.env.NODE_ENV === 'development' ? 1 : 9,
            },
            minRatio: Infinity,
            deleteOriginalAssets: false,
        }),
    ],
    optimization: {
        minimizer: [
            new TerserJSPlugin(),
        ],
    },
    performance: {
        hints: false,
    },
}];
