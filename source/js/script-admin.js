//
// NOTE TO SELF: REFACTOR THY MESS!
//

// empty var to hold our requests
var request;
// modal on page load
var $pageLoaderModal = $("#pageLoaderModal");

$(function() {
    //start pageloader on ASAP
    //showPageLoader();
    checkForUsage();
    authenticateUser(); 
});

function showErrorAlert($msg) {
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

    if ($data.role == "admin", $data.uid.indexOf("AD") >= 0) {
        request = $.ajax({
            url: "../php/adminValidator.php",
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

// ------------ page setup -------------- //

function setupTable(response, status, jqXHR) {
    var $infoModal = $("#info-body-row");
    var $tblBody = $("#transaction-body");
    
    if (typeof response != 'object') {
        $infoModal.html("<div class='col-md-12'><div class='alert alert-danger'>"+ response.message +"</div>");
    }

    if (response.error) {
        $infoModal.html("<div class='col-md-12'><div class='alert alert-danger'>"+ response.message +"</div>");
    }

    var $table = "";

    for (var i=0; i<response.transactions.length; i++) {
        x += "<tr>" + 
            "<td>"+ response.transactions[i].created_at +"</td>"+ 
            "<td data-uid="+response.transactions[i].uid+">"+ response.transactions[i].uid +"</td>"+
            "<td>"+ response.transactions[i].card_cust_name +"</td>"+
            "<td>"+ response.transactions[i].amount +"</td>"+
            "<td>"+ response.transactions[i].user +"</td>"+
            "<td>"+ response.transactions[i].source +"</td>"+
        "</tr>";
    }

    $tblBody.html($table);
    console.log(response.transactions);
}


function setupTableError(jqXHR, status, error) {
    var $infoModal = $("#info-body-row");
    $infoModal.html("<div class='col-md-12'><div class='alert alert-danger'>"+ response.message +"</div>");
}


function setupCrmPage(response, callback) {
    var navUsername = $("a#username");
    var navUid = $("a#uid");
    var navRole = $("a#role");

    // let's update the navbar
    navUsername.text(response.user.username);
    navUid.text(response.user.uid);
    navRole.text(response.user.role);

    // check if any previous request

    request = $.ajax({
        url: "../php/getTransactions.php",
        type: "get"
    });

    // if successfully done
    request.done(setupTable);

    // if any error occured
    request.fail(setupTableError);

    // once page setup is done show CRM PAGE
    callback();
}



//---------- check for usage ------------- //

function checkForUsage() {
    var $infoModal = $("#infoModal");
    var $formModal = $("#formModal");
    var $searchBtn = $("#search-btn");
    var $updateBtn = $("#update-btn");
    var $formModalForm = $("#formModal-form");

    $searchBtn.click(function() {
        $formModal.modal('show');
    });

    $updateBtn.click(function() {
        $infoModal.modal('show');
    });

    $("#formModalSelect").change(function() {
        if (this.value == "UID") {
            $("#transaction-term").slideDown().addClass("is-visible");
            $("#card-number").hide();
            $("#date").hide();
        } else if (this.value == "CARD") {
            $("#card-number").slideDown().addClass("is-visible");
            $("#transaction-term").hide();
            $("#date").hide();
        } else if (this.value == "DAY") {
            $("#date").slideDown().addClass("is-visible");
            $("#card-number").hide();
            $("#transaction-term").hide();
        } else {
            $("#transaction-term").slideDown().addClass("is-visible");
        }
    });

    $formModalForm.submit(function(event) {
        // let my js handle event handling
        event.preventDefault();

        $data = $formModalForm.serialize();

        var $inputs = $formModalForm.find("input, select, button, textarea");
        $inputs.prop("disabled", true);

        // check if we have nay previous requests
        if (request) {
            request.abort();
        }

        request = $.ajax({
            url: "../php/searchTransaction.php",
            type: "get"
        });

        // if successfully done
        request.done(setupSearchTable);

        // if any error occured
        request.fail(setupSearchTableError);
    });
}

function setupSearchTable(response, status, jqXHR) {

}

function setupSearchTableError(jqXHR, status, error) {
    
}

// search button handler
