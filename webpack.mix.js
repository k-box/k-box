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

// Javascript --------------------------------------------------------------

let publicJsFolder = "public/js";
let publicModulesFolder = publicJsFolder + "/modules";

// application modules

mix.copyDirectory('resources/assets/js/modules', publicModulesFolder)
   .copyDirectory('packages/contentprocessing/assets/js', publicModulesFolder);

// vendored dependencies

mix.scripts([
        'node_modules/lodash/lodash.min.js',
        'node_modules/jquery/dist/jquery.min.js',
        'node_modules/jquery-serializejson/jquery.serializejson.min.js',
        'node_modules/promise-polyfill/dist/polyfill.js',
        'node_modules/sweetalert2/dist/sweetalert2.min.js',
        'node_modules/jquery-unveil/jquery.unveil.js',
        'node_modules/nprogress/nprogress.js',
        'node_modules/clipboard/dist/clipboard.js',
        'node_modules/select2/dist/js/select2.js',
        'resources/assets/js/deps/modernizr.js',
        'resources/assets/js/deps/combokeys.js',
        'resources/assets/js/deps/contextmenu.js',
        'node_modules/holmes.js/js/holmes.js',
        'node_modules/plyr/dist/plyr.js',
        'node_modules/shaka-player/dist/shaka-player.compiled.js',
        'node_modules/handlebars/dist/handlebars.js',
        "vendor/oneofftech/laravel-tus-upload/public/js/tusuploader.js",
        'node_modules/requirejs/require.js',
        'resources/assets/js/dms/init.js',
    ], publicJsFolder + '/vendor.js')
    .scripts([
        'resources/assets/js/deps/i18n.js'
    ], publicJsFolder +'/i18n.js')
    .scripts([
        'node_modules/dropzone/dist/min/dropzone-amd-module.min.js'
    ], publicModulesFolder + '/dropzone.js')
    .scripts([
        "node_modules/axios/dist/axios.min.js"
    ], publicModulesFolder + '/axios.js')
    .scripts([
        "node_modules/leaflet/dist/leaflet.js"
    ], publicModulesFolder + '/leaflet.js')
    .scripts([
        "node_modules/leaflet-draw/dist/leaflet.draw.js"
    ], publicModulesFolder + '/leaflet-draw.js')
    .scripts([
        "node_modules/leaflet.control.opacity/dist/L.Control.Opacity.js"
    ], publicModulesFolder + '/leaflet-control-opacity.js')
    .scripts([
        "node_modules/leaflet.wms/dist/leaflet.wms.js"
    ], publicModulesFolder + '/leaflet-wms.js')

mix.js("resources/assets/js/evolution.js", "public/js");

mix.js("resources/assets/js/evolution-ie11.js", "public/js");

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