//
// NOTE TO SELF: REFACTOR THY MESS!
//

// empty var to hold our requests
var request;
// modal on page load
var $pageLoaderModal = $("#pageLoaderModal");

$(function() {
    //start pageloader on ASAP
    showPageLoader();
    checkForUsage();
    authenticateUser(); 
});

function showErrorlert($msg) {
    var $alertDiv = $('#alert-error-div');
    var $alertdisplay = $("#alert-error-div > .alert.alert-danger");

    // change the text and show it
    $alertdisplay.text($msg);        
    $alertDiv.slideDown().addClass("is-visible");
}

function showPageLoader() {
    // init Loading Modal
    $pageLoaderModal.modal({
        backdrop: 'static',  
        keyboard: false,
        show: true
    });
    
    // start animating
    $(".progress-bar").css("width", "100%");
}


function authenticateUser() {
    // abort any pending requests
    if (request) {
        request.abort();
    }

    // check if login is sucessfull
    var $data = {};
    $data.role = sessionStorage.getItem("role");
    $data.username = sessionStorage.getItem("username");
    $data.uid = sessionStorage.getItem("uid");

    if ($data.role == "client", $data.uid.indexOf("ZO") >= 0) {
        request = $.ajax({
            url: "../php/clientValidator.php",
            type: "post",
            data: $data
        });

        // if successfully done
        request.done(accessValidSuccess);

        // if any error occured
        request.fail(accessValidError);
    }
}

function checkAdminDetails(response) {

    var $flag = 0;

    if (sessionStorage.getItem("username") != response.user.username) {
        $flag++;
    } else if (sessionStorage.getItem("role") != response.user.role) {
        $flag++;
    } else if (sessionStorage.getItem("uid") != response.user.uid) {
        $flag++;
    }
    

    // if everything checks out
    if ($flag == 0) {
        return true;
    } else {
        return false;
    }
}

function accessValidSuccess(response, status, jqXHR) {
    if (typeof response != 'object') {
        showErrorAlert("Seems Like an error on server end, Please Contact Developer");
        return;
    } 

    if (response.error) {
        // change the text and show it
        showErrorAlert(response.message);
        return;
    }

    if (checkAdminDetails(response)) {
        setupCrmPage(response, function() {
            $pageLoaderModal.modal('hide');
        });
    }
}

function accessValidError(jqXHR, status, error) {
    showErrorAlert("Please Check Your Internet Connection");
}


function setupCrmPage(response, callback) {
    var navUsername = $("a#username");
    var navUid = $("a#uid");
    var navRole = $("a#role");

    // let's update the navbar
    navUsername.text(response.user.username);
    navUid.text(response.user.uid);
    navRole.text(response.user.role);
    
    // once page setup is done show CRM PAGE
    callback();
}


function checkForUsage() {
    // enable month-picker for expiry date
    // default system mm/yyyy
    $("#card-exp-dat").monthpicker();

    // enable date-picker
    // default mm/dd/yyyy
    $("#billing-dob").datepicker({
        changeMonth: true,
        changeYear: true,
        yearRange: "1900:2017"
    }); 

    // #goHome trigger
    $(".goHome").click(function() {
        window.sessionStorage.removeItem("username");
        window.sessionStorage.removeItem("role");
        window.sessionStorage.removeItem("uid");
        window.location.href = "../verify-user";
    });

    $('#checkbox').mousedown(function() {
        if (!$(this).is(':checked')) {
            this.checked = confirm("This is to confirm that billing address is same as shipping address?");
            $(this).trigger("change");
            $("#shipping-name").val($("#billing-name").val());
            $("#shipping-email").val($("#billing-email").val());
            $("#shipping-phone").val($("#billing-phone").val());
            $("#shipping-company").val($("#billing-company").val());
            $("#shipping-address").val($("#billing-address").val());
            $("#shipping-city").val($("#billing-city").val());
            $("#shipping-state").val($("#billing-state").val());
            $("#shipping-country").val($("#billing-country").val());
            $("#shipping-postal-code").val($("#billing-postal-code").val());
        }
    });

    // holding any pending request
    var request;

    $("#purchase-form").submit(function(event) {
        // stop from sending request let us handle it at front-end
        event.preventDefault();

        // check for form fields

        // grab the form
        var $form = $(this);

        // serialize the sdata
        var $serializeData = $form.serialize();

        // take inputs and disable them
        var inputs = $form.find('input, select, button, textarea');
        inputs.prop('disabled', true); 

        // abort any pending request
        if (request) {
            request.abort();
        }

        request = $.ajax({
            url: "../php/testClientTransaction.php",
            type: "post",
            data: $serializeData
        });

        // on request done
        request.done(function(response, textStatus, jqXHR) {
            // if error show error message
            if (response.indexOf("error") >= 0) {
                $("#error-message-box").text(response.substr(response.indexOf("error"), response.length));
                $("#error-box").slideDown().addClass("is-visible");
            } else if (response.indexOf("success") >= 0) { // if success let it through
                console.log("Successfully Submitted");
                console.log(response.substr(response.indexOf("success"), response.length));
                // Show Our Modal
                $("#myModal").modal({
                    backdrop: 'static', 
                    keyboard: false,
                    show: true
                });
            } else {
                console.log(response + "  ELSE COMMAND")
            }
        });

        // on error
        request.fail(function(jqXHR, textStatus, error) {
            console.log(error);
        });
    });
}