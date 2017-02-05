$(function() {
//showPageLoader();
});

function showPageLoader() {
    var $pageLoaderModal = $("#pageLoaderModal");

    // initiae Loading Modal
    $pageLoaderModal.modal({
        backdrop: 'static',  
        keyboard: false,
        show: true
    });
    // start animating
    $(".progress-bar").css("width", "100%");

    // hide after 4secs
    setTimeout(function() {
       $pageLoaderModal.modal("hide")
    }, 4000);
}