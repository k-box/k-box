/**
 * Preview module
 */
define(function () {

    console.info('Preview module loaded');

    // using var instead of const as IE10 don't support it
    var SIDEBAR_VISIBLE_CLASS = "preview__sidebar--visible";

    var DETAILS_BUTTON_EXPANDED_CLASS = "preview__button--expanded";

    var detailsBtn = document.querySelector(".js-preview-details-button")
        sidebarOpen = false,
        previewArea = document.querySelector(".js-preview-area"),
        previewAreaOriginalWidth = previewArea.offsetWidth,
        sidebar = document.querySelector(".js-preview-sidebar");

    detailsBtn.addEventListener('click', function(e){

        if(!sidebarOpen){

            detailsBtn.classList.add(DETAILS_BUTTON_EXPANDED_CLASS);
            sidebar.classList.add(SIDEBAR_VISIBLE_CLASS);
            previewArea.style.width = previewAreaOriginalWidth - 344 +"px";

            sidebarOpen = true;
        }
        else {

            detailsBtn.classList.remove(DETAILS_BUTTON_EXPANDED_CLASS);
            sidebar.classList.remove(SIDEBAR_VISIBLE_CLASS);
            previewArea.style.width = "";

            sidebarOpen = false;
        }

    });


    

    function initPlayer(video) {
        // Create a Player instance.
        var manifestUri = $(video).data('dash').trim();

        console.log('dash manifest', manifestUri);

        var player = new shaka.Player(video);

        // Listen for error events.
        player.addEventListener('error', onErrorEvent);

        // Try to load a manifest.
        // This is an asynchronous process.
        player.load(manifestUri).then(function () {
            // This runs if the asynchronous load is successful.
            console.log('The video has now been loaded!');

        }).catch(onError);  // onError is executed if the asynchronous load fails.
    }

    function onErrorEvent(event) {
        // Extract the shaka.util.Error object from the event.
        onError(event.detail);
    }

    function onError(error) {
        // Log the error.
        console.error('Error code', error.code, 'object', error);
    }

    /**
     * 
     * @param {string} selector the DOM selector to get the video tag, or the DOMElement
     * @param {*} options player configuration options
     * @return {Plyr} the player instance
     */
    function _StreamPlayer(selector, options){

        options = options || {};

        selector = typeof selector === 'string' ? document.querySelector(selector) : selector;

        var players = plyr.setup(selector);
        
        shaka.polyfill.installAll();
        
        // Check to see if the browser supports the basic APIs Shaka needs.
        if (shaka.Player.isBrowserSupported()) {
            // Everything looks good!
            initPlayer(selector);

        } else {
            // This browser does not have the minimum set of APIs we need.
            // console.error('Browser not supported!');

            var video = $(selector);

            players[0].source({
                type:       'video',
                sources: [{
                    src:    video.data('source').trim(),
                    type:   video.data('sourceType').trim()
                }]
              });
        }

        return players[0];
    }


    return {
        load: function(){
            var player = document.querySelector("#the-player");

            if(player){
                _StreamPlayer(player);
            }
        }
    }
});