const VueLoaderPlugin = require('vue-loader/lib/plugin');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const TerserJSPlugin = require('terser-webpack-plugin');
const OptimizeCSSAssetsPlugin = require('optimize-css-assets-webpack-plugin');
const MomentLocalesPlugin = require('moment-locales-webpack-plugin');
const CompressionPlugin = require("compression-webpack-plugin");
const path = require('path');

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
        path: path.resolve(__dirname, 'assets'),
        publicPath: '/',
        filename: '[name].js',
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
                test: /\.css$/,
                use: [
                    process.env.NODE_ENV === 'development'
                        ? 'vue-style-loader'
                        : MiniCssExtractPlugin.loader,
                    'css-loader',
                ],
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
                test: /\.svg$/,
                loader: 'svg-url-loader',
            },
            {
                test: /\.(woff|woff2)$/,
                loader: 'file-loader',
                options: {
                    name: '[name].[ext]',
                    outputPath: 'fonts',
                    publicPath: 'fonts',
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
        path: path.resolve(__dirname, 'assets'),
        publicPath: '/',
        filename: '[name].js',
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
            new TerserJSPlugin({}),
        ],
    },
    performance: {
        hints: false,
    },
}];
