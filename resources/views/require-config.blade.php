<script type="text/javascript">
require.config({
	baseUrl : '{{url('js/')}}/',
	paths: {
        language: "modules/language",
    },
    urlArgs: "bust={{ app()->environment() === 'production' ? Config::get('dms.build') : \Carbon\Carbon::now()->format('U') }}",
    skipDataMain:true,
    config: {
        //Set the config for the i18n
        //module ID
        i18n: {
            locale: '{{ app()->getLocale() }}',
            nls: 'localization'
        }
    }

    
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

define('plyr', [], function() {
    return window.plyr;
});

// -- Global initializations

@if(app()->environment() === 'production')
    require.onError = function(err){
    
    	swal("{{trans('errors.page_loading_title')}}", "{{trans('errors.page_loading_text')}}", 'error');
    
      	console.error(err);
    }
@endif


require(['jquery', 'DMS', 'language'], function($, DMS, Lang){

  DMS.initialize(Lang);

    $(document).ready(function() {
      $('#document-area .item__thumbnail img').unveil(undefined, function() {});
    });
    
});

</script>