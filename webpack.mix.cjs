const mix = require('laravel-mix');
const glob = require('glob-all');
const PurgecssPlugin = require('purgecss-webpack-plugin');
const path = require('path');
const BrotliPlugin = require('brotli-webpack-plugin');

// Define the extractor for purging unused CSS (Tailwind)
class TailwindExtractor {
    static extract(content) {
        return content.match(/[\w-/:]+(?<!:)/g) || [];
    }
}

// JS and CSS Bundling
mix.js('resources/js/app.js', 'public/js')
   .js('resources/js/components/invoiceComponent.js', 'public/js/components')
   .sass('resources/sass/app.scss', 'public/css')
   .postCss('resources/css/app.css', 'public/css')
   .minify('public/css/app.css')      // Minify CSS for production
   .minify('public/js/app.js')        // Minify JS for production
   .minify('public/js/components/invoiceComponent.js')
   .version();                        // Cache-busting for production

// Production-specific Webpack configuration
if (mix.inProduction()) {
    mix.webpackConfig({

        plugins: [
            // Purge unused CSS from Blade and Vue components
            new PurgecssPlugin({
                paths: glob.sync([
                    path.join(__dirname, 'resources/views/**/*.blade.php'),
                    path.join(__dirname, 'resources/js/**/*.vue'),
                    path.join(__dirname, 'resources/js/**/*.js'),   // Also purge JS files
                    path.join(__dirname, 'resources/css/**/*.css'), // Purge additional CSS
                ], { nodir: true }),
                extractors: [
                    {
                        extractor: TailwindExtractor,
                        extensions: ['html', 'js', 'php', 'vue'], // Added Vue support
                    },
                ],
                // Ensure that CSS important for specific elements is not purged
                safelist: ['bg-red-500', 'text-white'],  // Add any classes that must be preserved
            }),
             // New Brotli Plugin
             new BrotliPlugin({
                asset: '[path].br[query]',
                test: /\.(js|css|html|svg)$/,
                threshold: 10240,
                minRatio: 0.8
            })
        ],
        output: {
            chunkFilename: 'js/[name].[chunkhash].js',  // Use chunkhash for better caching
        },
        module: {
            rules: [
                {
                    test: /\.css$/,
                    use: [
                        'style-loader',
                        'css-loader',
                    ],
                },
                {
                    test: /\.(png|jpe?g|gif|svg)$/i,  // Image optimization
                    use: [
                        {
                            loader: 'file-loader',
                            options: {
                                name: '[path][name].[hash].[ext]',
                            },
                        },
                        {
                            loader: 'image-webpack-loader',
                            options: {
                                mozjpeg: {
                                    progressive: true,
                                    quality: 65,
                                },
                                // Optimize PNG and SVG files
                                pngquant: {
                                    quality: [0.65, 0.90],
                                    speed: 4,
                                },
                                svgo: {
                                    plugins: [
                                        { removeViewBox: false },
                                        { cleanupIDs: false },
                                    ],
                                },
                            },
                        },
                    ],
                },
            ],
        },
        // Enable Brotli compression for additional performance boost
        optimization: {
            minimize: true,
            splitChunks: {
                chunks: 'all',  // Extract shared modules into separate files
                maxInitialRequests: 6, // Reduce the number of parallel requests
            },
        },
    });
}

// Enable source maps for development (but not for production)
if (!mix.inProduction()) {
    mix.sourceMaps();
}

// Enable versioning only for production builds
if (mix.inProduction()) {
    mix.version();
}
