$(function() {
    handleLogin();
});


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
        var inputs  = loginForm.find('input, select, button, textarea');
        inputs.prop('disabled', true);

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
    // for now let's just console.log
    console.log("Response Isn \n ", response);
    console.log("Status is \n", status);
    console.log("jqXHR is \n", jqXHR);
}


// our response handler if unsuccessfull
function loginErrorHandler(jqXHR, status, error) {
    console.log("error Isn \n ", error);
    console.log("Status is \n", status);
    console.log("jqXHR is \n", jqXHR);
}
