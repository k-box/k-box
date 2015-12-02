var elixir = require('laravel-elixir');
var gulp = require("gulp");
var elixirNotify = require("./node_modules/laravel-elixir/ingredients/commands/Notification");
var elixirUtils = require("./node_modules/laravel-elixir/ingredients/commands/Utilities");
require('laravel-elixir-bower');

elixir.extend('copyJsModules', function() {

    var that = this;

    gulp.task('copyAllTask', function() {
        elixirUtils.logTask("Copying modules... ");

        var ret = gulp.src(that.assetsDir + "js/modules/**/*").pipe(gulp.dest( that.jsOutput + "/modules/" ));

        return ret;
    });

    this.registerWatcher('copyAllTask', that.assetsDir + 'js/modules/*.*');

    return this.queueTask('copyAllTask');

});

elixir.config.bowerDir = "resources/assets/vendor";
elixir.config.jsOutput = "public/js/";

elixir(function(mix) {

	

	// run gulp --production for production environment

	//npm install laravel-elixir-imagemin

    mix.less(['app.less', 'ie8.less']) //compile the app.less file into app.css and ie8.less into ie8.css into the public/css folder
        //concat vendor styles and app style in single stylesheet
        .styles([
            "../../"+ elixir.config.bowerDir +"/nprogress/nprogress.css",
            "../../"+ elixir.config.bowerDir +"/sweetalert/lib/sweet-alert.css",
            "../../"+ elixir.config.bowerDir +"/material-design-icons/sprites/css-sprite/sprite-toggle-black.css",
            "../../"+ elixir.config.bowerDir +"/material-design-icons/sprites/css-sprite/sprite-toggle-white.css",
            "../../"+ elixir.config.bowerDir +"/material-design-icons/sprites/css-sprite/sprite-social-black.css",
            "../../"+ elixir.config.bowerDir +"/material-design-icons/sprites/css-sprite/sprite-social-white.css",
            "../../"+ elixir.config.bowerDir +"/material-design-icons/sprites/css-sprite/sprite-file-black.css",
            "../../"+ elixir.config.bowerDir +"/material-design-icons/sprites/css-sprite/sprite-file-white.css",
            "../../"+ elixir.config.bowerDir +"/material-design-icons/sprites/css-sprite/sprite-content-black.css",
            "../../"+ elixir.config.bowerDir +"/material-design-icons/sprites/css-sprite/sprite-content-white.css",
            "../../"+ elixir.config.bowerDir +"/material-design-icons/sprites/css-sprite/sprite-action-black.css",
            "../../"+ elixir.config.bowerDir +"/material-design-icons/sprites/css-sprite/sprite-action-white.css",
            "../../"+ elixir.config.bowerDir +"/material-design-icons/sprites/css-sprite/sprite-navigation-black.css",
            "../../"+ elixir.config.bowerDir +"/material-design-icons/sprites/css-sprite/sprite-navigation-white.css",
            "../../"+ elixir.config.bowerDir +"/material-design-icons/sprites/css-sprite/sprite-device-black.css",
            "../../"+ elixir.config.bowerDir +"/material-design-icons/sprites/css-sprite/sprite-maps-white.css",
            "../../"+ elixir.config.bowerDir +"/klink-map/siris.css",
            "app.css"
        ], elixir.config.cssOutput + "/all.css", elixir.config.cssOutput)
    	//take and concat styles and css from bower packages (vendor.css and vendor.js)
    	// .bower()
    	//ugly, but rebuilding the vendor scripts with only the element I need
    	//and in the correct order
    	.scripts([
                'lodash/lodash.min.js',
    			'jquery/dist/jquery.min.js',
                'jquery.serializeJSON/jquery.serializejson.min.js',
                'jquery-unveil/jquery.unveil.js',
                'sweetalert/lib/sweet-alert.min.js',
    			'nprogress/nprogress.js',
                '../js/deps/modernizr.js',
                '../js/deps/combokeys.js',
                '../js/deps/contextmenu.js',
    			'requirejs/require.js',
//    			'../js/require-config.js',
    			'../js/dms/init.js',
    		],
    		elixir.config.jsOutput + '/vendor.js', //output dir
            elixir.config.bowerDir //base dir
    	)
        .scripts([
                'dropzone/dist/min/dropzone-amd-module.min.js'
            ],
            elixir.config.jsOutput +'/modules/dropzone.js', //output dir
            elixir.config.bowerDir //base dir
        )
//        .scripts([
//                'sightglass/index.js'
//            ],
//            elixir.config.jsOutput +'/sightglass.js', //output dir
//            elixir.config.bowerDir //base dir
//        )
//        .scripts([
//                'rivets/dist/rivets.js'
//            ],
//            elixir.config.jsOutput +'/rivets.js', //output dir
//            elixir.config.bowerDir //base dir
//        )
        .scripts([
                'klink-elastic-list/dist/elasticlist.js'
            ],
            elixir.config.jsOutput +'/modules/elasticlist.js', //output dir
            elixir.config.bowerDir //base dir
        )
        .scripts([
                'klink-map/map.js'
            ],
            elixir.config.jsOutput +'/modules/map.js', //output dir
            elixir.config.bowerDir //base dir
        )
        .scripts([
                'turf/turf.min.js'
            ],
            elixir.config.jsOutput +'/modules/turf.js', //output dir
            elixir.config.bowerDir //base dir
        )
        .scripts([
                'html5shiv/dist/html5shiv.min.js', // for supporting header, footer,... elements
                'es5-shim/es5-shim.min.js', // Shims EcmaScript 5 methods
                'object.observe/dist/object-observe.min.js' //Object.observe for making rivetsjs adapter happy
            ],
            elixir.config.jsOutput +'/ie8-shivm.js', //output dir
            elixir.config.bowerDir //base dir
        )
        .scripts([
                'd3/d3.min.js'
            ],
            elixir.config.jsOutput + '/modules/d3.js', //output dir
            elixir.config.bowerDir //base dir
        )
    	// Copy pure JS modules to public folder
    	.copyJsModules() //'resources/assets/js/modules/', 'public/js/modules/')
    	// Material Design icons files
    	.copy( elixir.config.bowerDir + '/material-design-icons/sprites/css-sprite/sprite-toggle-black.png', 'public/images/sprite-toggle-black.png')
        .copy( elixir.config.bowerDir + '/material-design-icons/sprites/css-sprite/sprite-toggle-white.png', 'public/images/sprite-toggle-white.png')
    	.copy( elixir.config.bowerDir + '/material-design-icons/sprites/css-sprite/sprite-social-black.png', 'public/images/sprite-social-black.png')
    	.copy( elixir.config.bowerDir + '/material-design-icons/sprites/css-sprite/sprite-file-black.png', 'public/images/sprite-file-black.png')
        .copy( elixir.config.bowerDir + '/material-design-icons/sprites/css-sprite/sprite-file-white.png', 'public/images/sprite-file-white.png')
        .copy( elixir.config.bowerDir + '/material-design-icons/sprites/css-sprite/sprite-content-black.png', 'public/images/sprite-content-black.png')
        .copy( elixir.config.bowerDir + '/material-design-icons/sprites/css-sprite/sprite-content-white.png', 'public/images/sprite-content-white.png')
    	.copy( elixir.config.bowerDir + '/material-design-icons/sprites/css-sprite/sprite-action-black.png', 'public/images/sprite-action-black.png')
        .copy( elixir.config.bowerDir + '/material-design-icons/sprites/css-sprite/sprite-action-white.png', 'public/images/sprite-action-white.png')
        .copy( elixir.config.bowerDir + '/material-design-icons/sprites/css-sprite/sprite-social-white.png', 'public/images/sprite-social-white.png')
        .copy( elixir.config.bowerDir + '/material-design-icons/sprites/css-sprite/sprite-navigation-black.png', 'public/images/sprite-navigation-black.png')
        .copy( elixir.config.bowerDir + '/material-design-icons/sprites/css-sprite/sprite-navigation-white.png', 'public/images/sprite-navigation-white.png')
        .copy( elixir.config.bowerDir + '/material-design-icons/sprites/css-sprite/sprite-device-black.png', 'public/images/sprite-device-black.png')
        .copy( elixir.config.bowerDir + '/material-design-icons/sprites/css-sprite/sprite-maps-white.png', 'public/images/sprite-maps-white.png')
	    
	    // make versionable to resolve caching problems
	    .version(["css/all.css", "css/ie8.css", "js/vendor.js"]);
});
