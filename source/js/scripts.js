$(function() {
    handleLogin();
});


function toggleInputViews($val) {
    var $form = $('#loginForm');
    var $inputs = $form.find('input, select, button, textarea');

    // enable inputs for editing
    $inputs.prop('disabled', $val);
}

function showErrorAlert($msg) {
    var $alertDiv = $('#login-alert');
    var $alertdisplay = $("#login-alert > .alert.alert-danger");

    // change the text and show it
    $alertdisplay.text($msg);        
    $alertDiv.slideDown().addClass("is-visible");
}

// TODO: Enable to handle login via this route
function handleLogin() {
    var alertdiv = $('#login-alert');
    var loginForm = $('#loginForm');
    var request; // empty var for any pending requests

    loginForm.submit(function(event) {

        // let us handle form submission
        event.preventDefault();

        // serialize data and disable fields
        var serializedData = loginForm.serialize();

        // disable form inputs
        toggleInputViews(true);

        // check for any pending requests and destory if available
        if (request) {
            request.abort();
        }

        // fireup the login request
        request = $.ajax({
            url: "../php/loginHandler.php",
            type: "post",
            data: serializedData
        });

        // if successfully done
        request.done(loginResponseHandler);

        // if any error occured
        request.fail(loginErrorHandler);
    });
}

/**
 * TODO: Add a login stuff
 * Handle things with localstorage
 */


// our login handler if successfull
function loginResponseHandler(response, status, jqXHR) {
    
    if (typeof response != 'object') {
        showErrorAlert("Please Contact Your Developer, Seems like Error on server");
        return false;
    }

    // check if there is any error
    if (response.error) {
        // change the text and show it
        showErrorAlert(response.message);

        // enable input fields
        toggleInputViews(false);
        return false;
    }

    // store thy data
    localStorage.setItem("role", response.user.role);
    localStorage.setItem("uid", response.user.uid);
    localStorage.setItem("username", response.user.username);

    if (response.user.role == "admin") {
        window.location.href = "../admin-panel/";
    } else if (response.user.role == "client") {
        window.location.href = "../client-panel/";
    }
}


// our response handler if unsuccessfull
function loginErrorHandler(jqXHR, status, error) {
    showErrorAlert("Please Contact Your Developer, Internal Server Error");
}
