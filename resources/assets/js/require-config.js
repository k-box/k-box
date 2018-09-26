
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

// define('rivets', [], function() {
//     return window.rivets;
// });

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

//require.onError = function(err){
//
//	swal('Loading problem :(', 'Some functionalities may not be available.', 'error');
//
//  	console.error(err);
//}


require(['jquery', 'DMS'], function($, DMS){

  DMS.initialize();

});