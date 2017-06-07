process.env.DISABLE_NOTIFIER = true;
var elixir = require('laravel-elixir');
var gulp = require("gulp");
var logger = require("./node_modules/laravel-elixir/Logger");
var Task = elixir.Task;

elixir.extend('copyJsModules', function() {

    var that = this;
    
    new Task('copyAllTask', function() {
            return gulp.src(that.config.assetsPath + "/js/modules/**/*").pipe(gulp.dest( that.config.jsOutput + "/modules/" ));
        })
        .watch(that.config.assetsPath + '/js/modules/*.*');

});

elixir.extend('previewJsModules', function() {

    var that = this;
    
    new Task('copyPreviewModule', function() {
            return gulp.src("packages/contentprocessing/assets/js/**/*").pipe(gulp.dest( that.config.jsOutput + "/modules/" ));
        })
        .watch('packages/contentprocessing/assets/js/modules/*.*');

});

elixir.config.bowerDir = elixir.config.assetsPath + "/vendor/";
elixir.config.npmDir = "./node_modules/";
elixir.config.cssOutput = "public/css/";
elixir.config.jsOutput = "public/js/";
elixir.config.sourcemaps = false;

elixir(function(mix) {

	// run gulp --production for production environment

	//npm install laravel-elixir-imagemin
    
    if (elixir.config.production) {
        // get production app.less and output as app.css
        mix.less( 'app.less', elixir.config.cssOutput + 'app.css' );
    }
    else {
        // get development app_dev.less and output as app.css
        mix.less('app_dev.less', elixir.config.cssOutput + 'app.css');
    }

    mix.less('ie8.less').less('microsite.less') //compile the app.less file into app.css and ie8.less into ie8.css into the public/css folder
        //concat vendor styles and app style in single stylesheet
        .styles([
            "/nprogress/nprogress.css",
            "/sweetalert/lib/sweet-alert.css",
            "/klink-map/siris.css",
            "/hint.css/hint.base.css",
            "/select2/dist/css/select2.css"
        ], elixir.config.cssOutput + "/vendor.css", elixir.config.bowerDir)
    	.scripts([
                'lodash/lodash.min.js',
    			'jquery/dist/jquery.min.js',
                'jquery.serializeJSON/jquery.serializejson.min.js',
                'jquery-unveil/jquery.unveil.js',
                'sweetalert/lib/sweet-alert.min.js',
    			'nprogress/nprogress.js',
    			'clipboard/dist/clipboard.js',
    			'select2/dist/js/select2.js',
                '../js/deps/modernizr.js',
                '../js/deps/combokeys.js',
                '../js/deps/contextmenu.js',
                '../../../node_modules/holmes.js/js/holmes.js',
    			'requirejs/require.js',
    			'../js/dms/init.js',
    		],
    		elixir.config.jsOutput + '/vendor.js', //output dir
            elixir.config.bowerDir //base dir
    	)
        .scripts([
                '../js/deps/i18n.js'
            ],
            elixir.config.jsOutput +'i18n.js', //output dir
            elixir.config.bowerDir //base dir
        )
        .scripts([
                'dropzone/dist/min/dropzone-amd-module.min.js'
            ],
            elixir.config.jsOutput +'/modules/dropzone.js', //output dir
            elixir.config.bowerDir //base dir
        )
        .scripts([
                'klink-map/map.js'
            ],
            elixir.config.jsOutput +'/modules/map.js', //output dir
            elixir.config.bowerDir //base dir
        )
        .copy(elixir.config.bowerDir + 'turf/turf.min.js', elixir.config.jsOutput +'/modules/turf.js')
        .scripts([
                'html5shiv/dist/html5shiv.min.js', // for supporting header, footer,... elements
                'es5-shim/es5-shim.min.js', // Shims EcmaScript 5 methods
                'object.observe/dist/object-observe.min.js' //Object.observe for making rivetsjs adapter happy
            ],
            elixir.config.jsOutput +'/ie8-shivm.js', //output dir
            elixir.config.bowerDir //base dir
        )
        .copy(elixir.config.bowerDir + 'd3/d3.min.js', elixir.config.jsOutput +'/modules/d3.js')
    	// Copy pure JS modules to public folder
    	.copyJsModules() //'resources/assets/js/modules/', 'public/js/modules/')
    	.previewJsModules();
	    
	    // make versionable to resolve caching problems
        if (elixir.config.production) {
	       mix.version( ["public/css/vendor.css", "public/css/app.css", "public/css/ie8.css", "public/js/vendor.js"] );
        }
});
