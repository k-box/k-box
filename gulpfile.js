process.env.DISABLE_NOTIFIER = true;
var elixir = require('laravel-elixir');
var gulp = require("gulp");
const postcss = require('gulp-postcss');
const purgecss = require('@fullhuman/postcss-purgecss')({

    // Specify the paths to all of the template files in your project 
    content: [
      './app/**/*.php',
      './workbench/**/*.blade.php',
      './workbench/**/*.js',
      './resources/**/*.blade.php',
      './resources/**/*.js',
      './resources/**/*.less',
      './resources/**/*.css',
      './public/css/vendor.css',
      './public/js/vendor.js',
    ],

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
var Task = elixir.Task;


elixir.extend('copyJsModules', function() {

    var _elixir = this;
    
    new Task('copyAllTask', function() {
            return gulp.src(_elixir.config.assetsPath + "/js/modules/**/*").pipe(gulp.dest( _elixir.config.jsOutput + "/modules/" ));
        })
        .watch(_elixir.config.assetsPath + '/js/modules/*.*');

});

elixir.extend('previewJsModules', function() {

    var _elixir = this;
    
    new Task('copyPreviewModule', function() {
            return gulp.src("packages/contentprocessing/assets/js/**/*").pipe(gulp.dest( _elixir.config.jsOutput + "/modules/" ));
        })
        .watch('packages/contentprocessing/assets/js/*.*');

});

elixir.extend('postCss', function (file) {
    const postcss = require('gulp-postcss');

    var _elixir = this;
    
    new Task('postCss', function() {
        return gulp.src(file)
            .pipe(postcss([
                require('postcss-import'),
                require('tailwindcss'),
                require('postcss-nested'),
                require('autoprefixer'),
                ..._elixir.config.production ? [purgecss] : [],
                ..._elixir.config.production ? [require('cssnano')({
                    preset: ['default', {
                        calc: false,
                    }],
                })] : [] 
            ]))
            .pipe(gulp.dest(_elixir.config.cssOutput));
    }).watch(['tailwind.config.js', file]);
})
  

elixir.config.npmDir = "./node_modules/";
elixir.config.vendorDir = "./vendor/";
elixir.config.cssOutput = "public/css/";
elixir.config.jsOutput = "public/js/";
elixir.config.sourcemaps = false;

elixir(function(mix) {

     mix.styles([
            "/nprogress/nprogress.css",
            "/sweetalert2/dist/sweetalert2.css",
            "/hint.css/hint.base.css",
            "/select2/dist/css/select2.css",
            "/plyr/dist/plyr.css",
            "/leaflet/dist/leaflet.css",
            "/leaflet-draw/dist/leaflet.draw.css",
            "/leaflet.control.opacity/dist/L.Control.Opacity.css"
        ], elixir.config.cssOutput + "/vendor.css", elixir.config.npmDir)
        .postCss('resources/assets/css/app-evolution.css') // generate the functional css that will be the evolution of system
    	.scripts([
                'lodash/lodash.min.js',
    			'jquery/dist/jquery.min.js',
                'jquery-serializejson/jquery.serializejson.min.js',
                'promise-polyfill/dist/polyfill.js',
                'sweetalert2/dist/sweetalert2.min.js',
                'jquery-unveil/jquery.unveil.js',
    			'nprogress/nprogress.js',
    			'clipboard/dist/clipboard.js',
    			'select2/dist/js/select2.js',
                '../resources/assets/js/deps/modernizr.js',
                '../resources/assets/js/deps/combokeys.js',
                '../resources/assets/js/deps/contextmenu.js',
                'holmes.js/js/holmes.js',
                'plyr/dist/plyr.js',
                'shaka-player/dist/shaka-player.compiled.js',
                'handlebars/dist/handlebars.js',
                "../vendor/oneofftech/laravel-tus-upload/public/js/tusuploader.js",
    			'requirejs/require.js',
    			'../resources/assets/js/dms/init.js',
    		],
    		elixir.config.jsOutput + '/vendor.js', //output dir
            elixir.config.npmDir //base dir
    	)
        .scripts([
                'js/deps/i18n.js'
            ],
            elixir.config.jsOutput +'i18n.js', //output dir
            "./resources/assets/" //base dir
        )
        .scripts([
                'dropzone/dist/min/dropzone-amd-module.min.js'
            ],
            elixir.config.jsOutput +'/modules/dropzone.js', //output dir
            elixir.config.npmDir //base dir
        )
        .scripts([
                "/axios/dist/axios.min.js"
            ],
            elixir.config.jsOutput +'/modules/axios.js', //output dir
            elixir.config.npmDir //base dir
        )
        .scripts([
                "/leaflet/dist/leaflet.js"
            ],
            elixir.config.jsOutput +'/modules/leaflet.js', //output dir
            elixir.config.npmDir //base dir
        )
        .scripts([
                "/leaflet-draw/dist/leaflet.draw.js"
            ],
            elixir.config.jsOutput +'/modules/leaflet-draw.js', //output dir
            elixir.config.npmDir //base dir
        )
        .scripts([
                "/leaflet.control.opacity/dist/L.Control.Opacity.js"
            ],
            elixir.config.jsOutput +'/modules/leaflet-control-opacity.js', //output dir
            elixir.config.npmDir //base dir
        )
        .scripts([
                "/leaflet.wms/dist/leaflet.wms.js"
            ],
            elixir.config.jsOutput +'/modules/leaflet-wms.js', //output dir
            elixir.config.npmDir //base dir
        )
    	// Copy pure JS modules to public folder
    	.copyJsModules()
        .previewJsModules();
	    
	    // make versionable to resolve caching problems
        if (elixir.config.production) {
	        mix.version( ["public/css/vendor.css", "public/css/app-evolution.css", "public/js/vendor.js"] );
        }
});
