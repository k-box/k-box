/**
 * Preview module
 */
define(function () {

    console.info('Preview module loaded');

    const SIDEBAR_VISIBLE_CLASS = "preview__sidebar--visible";

    const DETAILS_BUTTON_EXPANDED_CLASS = "preview__button--expanded";

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

    return {
        load: function(){

        }
    }
});