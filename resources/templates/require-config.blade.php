<script type="text/javascript">
require.config({
	baseUrl : '{{url('js/')}}/',
	paths: {
        d3: "modules/d3",
        elasticlist:"modules/elasticlist",
		map:"modules/map",
		turf:"modules/turf",
        "leaflet": "//cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.3/leaflet"
    },
    shim: {
        d3: {
          exports: 'd3'
        }
    },
    urlArgs: "bust=" +  (new Date()).getTime(), //for preventing bad caching
    skipDataMain:true

    
});




// -- require js configuration


// -- theese modules are already included in the vendor big js

define('jquery', [], function() {
    return window.$;
});

define('lodash', [], function() {
    return window._;
});

define('nprogress', [], function() {
    return window.NProgress;
});

define('DMS', [], function() {
    return window.DMS;
});

define('combokeys', [], function() {
    return window.Combokeys;
});

define('sweetalert', [], function() {
    return window.swal;
});

define('modernizr', [], function() {
    return window.Modernizr;
});

define('context', [], function() {
    return window.context;
});

// -- Global initializations

@if(app()->environment() === 'production')
    require.onError = function(err){
    
    	swal('Loading problem :(', 'Some functionalities may not be available.', 'error');
    
      	console.error(err);
    }
@endif


require(['jquery', 'DMS'], function($, DMS){

  DMS.initialize();
  // console.info('DMS initialized.');

    $(document).ready(function() {
      $('#document-area .thumbnail img').unveil(undefined, function() {
          console.log('Image loaded');
      });
    });
    
});

</script>