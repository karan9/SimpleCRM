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
        $infoModal.modal('show');
        return;
    }

    if (response.error) {
        $infoModal.html("<div class='col-md-12'><div class='alert alert-danger'>"+ response.message +"</div>");
        $infoModal.modal('show');
        return;
    }

    var $table = "";

    for (var i=0; i<response.transactions.length; i++) {
        $table += "<tr>" + 
            "<td>"+ response.transactions[i].created_at +"</td>"+ 
            "<td data-uid="+response.transactions[i].uid+">"+ response.transactions[i].uid +"</td>"+
            "<td>"+ response.transactions[i].card_cust_name +"</td>"+
            "<td>"+ response.transactions[i].currency +"</td>"+
            "<td>"+ response.transactions[i].amount +"</td>"+
            "<td>"+ response.transactions[i].user +"</td>"+
            "<td>"+ response.transactions[i].source +"</td>"+
        "</tr>";
    }

    $tblBody.html($table);
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
    var $officeModal = $("#officeUserModal");
    var $searchBtn = $("#search-btn");
    var $createOfficeUserBtn = $("#create-user-office-btn");
    var $formModalForm = $("#formModal-form");
    var $officeModalForm = $("#officeUserModal-form");
    var $logoutBtn = $("#logout-btn");

    $searchBtn.click(function() {
        $formModal.modal('show');
    });

    $createOfficeUserBtn.click(function() {
        $officeModal.modal("show");
    });

    // LOG THE FUCK OUT!
    $logoutBtn.click(function() {
        window.sessionStorage.removeItem("username");
        window.sessionStorage.removeItem("role");
        window.sessionStorage.removeItem("uid");
        window.location.href = "../verify-user";
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
            $("#dateInput").datepicker({
                changeMonth: true,
                changeYear: true,
                yearRange: "1900:2017",
                dateFormat: 'yy/mm/dd'
            });
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

        // var $inputs = $formModalForm.find("input, select, button, textarea");
        // $inputs.prop("disabled", true);

        // check if we have nay previous requests
        if (request) {
            request.abort();
        }

        request = $.ajax({
            url: "../php/searchTransaction.php",
            type: "post",
            data: $data
        });

        // if successfully done
        request.done(setupSearchTable);

        // if any error occured
        request.fail(setupSearchTableError);
    });

    $officeModalForm.submit(function(event) {
        // let my js handle event handling
        event.preventDefault();

        $data = $officeModalForm.serialize();

        // check if we have nay previous requests
        if (request) {
            request.abort();
        }

        request = $.ajax({
            url: "../php/testUser.php",
            type: "post",
            data: $data
        });

        // if successfully done
        request.done(successfullNewUser);

        // if any error occured
        request.fail(errorNewUser);
    });
}

function setupSearchTable(response, status, jqXHR) {
    var $infoModalBody = $("#info-body-row");
    var $tblBody = $("#transaction-body");
    var $infoModal = $("#infoModal");
    var $infoModalTitle = $("#infoModalTitle");
    var $formModal = $("#formModal");
    var $formModalForm = $("#formModal-form");

    // hide $formModal
    // var $inputs = $formModalForm.find("input, select, button, textarea");
    // $inputs.prop("disabled", false);
    $formModal.modal("hide");
    $infoModalBody.html("");

    if (typeof response != 'object') {
        console.log(response);
        $infoModalBody.html("<div class='col-md-12'><div class='alert alert-danger'>"+ "Response is not a object" +"</div>");
        $infoModal.modal('show');
        return;
    }

    if (response.error) {
        $infoModalBody.html("<div class='col-md-12'><div class='alert alert-danger'>"+ response.message +"</div>");
        $infoModal.modal('show');
        return;
    }

    $infoModalTitle.text("Search Results");

    var $modalData = "";

    for (var i=0; i<response.transactions.length; i++) {
        $modalData += ""+
            "<div class='col-md-12' style='padding: 20px; border-bottom: 1px dotted black;'>" + 
                "<h3>Transaction #" + i + "</h3>" +
                "<p><strong>Transaction ID: </strong>"+ response.transactions[i].uid+"</p>" +
                "<p><strong>Source: </strong>"+ response.transactions[i].source+"</p>" +
                "<p><strong>User: </strong>"+ response.transactions[i].user+"</p>" +
                "<p><strong>Card Type: </strong>"+ response.transactions[i].card_type+"</p>" +
                "<p><strong>Card Number: </strong>"+ response.transactions[i].card_number+"</p>" +
                "<p><strong>Card CVV: </strong>"+ response.transactions[i].card_cvv_number+"</p>" +
                "<p><strong>Card Expiry Date: </strong>"+ response.transactions[i].card_expiry_date+"</p>" +
                "<p><strong>Currency: </strong>"+ response.transactions[i].currency+"</p>" +
                "<p><strong>Amount Charged: </strong>"+ response.transactions[i].amount+"</p>" +
                "<p><strong>Name on Card: </strong>"+ response.transactions[i].card_cust_name+"</p>" +
                "<p><strong>Billing Name: </strong>"+ response.transactions[i].billing_name+"</p>" +
                "<p><strong>Billing Email: </strong>"+ response.transactions[i].billing_email+"</p>" +
                "<p><strong>Billing Phone: </strong>"+ response.transactions[i].billing_phone+"</p>" +
                "<p><strong>Date Of Birth: </strong>"+ response.transactions[i].billing_dob+"</p>" +
                "<p><strong>Billing Company: </strong>"+ response.transactions[i].billing_company+"</p>" +
                "<p><strong>Billing Address: </strong>"+ response.transactions[i].billing_address+"</p>" +
                "<p><strong>Billing City: </strong>"+ response.transactions[i].billing_city+"</p>" +
                "<p><strong>Billing State: </strong>"+ response.transactions[i].billing_state+"</p>" +
                "<p><strong>Billing Country: </strong>"+ response.transactions[i].billing_country+"</p>" +
                "<p><strong>Billing Postal Code: </strong>"+ response.transactions[i].billing_postal_code+"</p>" +
                "<p><strong>Charged At: </strong>"+ response.transactions[i].created_at+"</p>" +
            "</div>";
    }


    $infoModalBody.html($modalData);
    // show data
    $infoModal.modal('show');
    console.log(response);

    $infoModal.on("hide.bs.modal", function() {
        window.location.reload();
    });
}

function setupSearchTableError(jqXHR, status, error) {
    $infoModalBody.html("<div class='col-md-12'><div class='alert alert-danger'>"+ error.message +"</div>");
    $infoModal.modal('show');
}

// new user

function successfullNewUser(response, status, jqXHR) {
    var $infoModalBody = $("#info-body-row");
    var $infoModal = $("#infoModal");
    var $infoModalTitle = $("#infoModalTitle");
    var $officeModal = $("#officeUserModal");

    $officeModal.modal("hide");
    $infoModalBody.html("");

    if (typeof response != 'object') {
        console.log(response);
        $infoModalBody.html("<div class='col-md-12'><div class='alert alert-danger'>"+ "Response is not a object" +"</div>");
        $infoModal.modal('show');
        return;
    }

    if (response.error) {
        $infoModalBody.html("<div class='col-md-12'><div class='alert alert-danger'>"+ response.message +"</div>");
        $infoModal.modal('show');
        return;
    }

    $infoModalBody.html("<div class='col-md-12'><div class='alert alert-success'>"+ response.message +"</div>");
    // show data
    $infoModal.modal('show');
    console.log(response);

    $infoModal.on("hide.bs.modal", function() {
        window.location.reload();
    });
}

function errorNewUser(jqXHR, status, error) {
     $infoModalBody.html("<div class='col-md-12'><div class='alert alert-danger'>"+ error +"</div>");
     $infoModal.modal('show');
}