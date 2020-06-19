/*
 |--------------------------------------------------------------------------
 | Frontend resources compilation/bundling configuration
 |--------------------------------------------------------------------------
 */

const mix = require('laravel-mix');
require('laravel-mix-purgecss');

// CSS ---------------------------------------------------------------------

mix.postCss('resources/assets/css/app-evolution.css', 'public/css', [
    require('postcss-import'),
    require('tailwindcss'),
    require('postcss-nested'),
    require('autoprefixer'),
])
.styles([
    "node_modules/nprogress/nprogress.css",
    "node_modules/sweetalert2/dist/sweetalert2.css",
    "node_modules/select2/dist/css/select2.css",
    "node_modules/plyr/dist/plyr.css",
    "node_modules/leaflet/dist/leaflet.css",
    "node_modules/leaflet-draw/dist/leaflet.draw.css",
    "node_modules/leaflet.control.opacity/dist/L.Control.Opacity.css"
], "public/css/vendor.css")


// Tasks to run in production ----------------------------------------------

mix.purgeCss({
    enabled: mix.inProduction(),
    whitelistPatterns: [
        /item--selectable/,
        /item--selected/,
        /description/,
        /c-panel/,
        /c-cache/,
        /c-dialog/,
        /select2/,
        /dialog/,
        /dialog--share/,
        /dropzone/,
        /dz-drag/,
        /preview/,
        /leaflet/,
        /map/,
    ],

    // Include any special characters you're using in this regular expression
    defaultExtractor: content => content.match(/[\w-/.:]+(?<!:)/g) || []
})
.version()