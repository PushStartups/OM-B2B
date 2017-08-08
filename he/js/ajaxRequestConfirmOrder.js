var dataObject                    =   null;      // DATA OBJECT CONTAIN INFORMATION ABOUT COMPANY, USER & USER ORDER

var selectedRestIndex             =   0;

var foodCartData                  =   null;     // FOOD CART OBJECT

var tempDiscountFromCompanyCal    =   0;

var user_cards                    =   null;

var keepLoaderUntilPageLoad       =   true;

var paymentException              =   true;



$(document).ready(function() {


    dataObject = localStorage.getItem("data_object_he");


    if (dataObject == undefined || dataObject == "" || dataObject == null){

        localStorage.setItem("user_id_b2b","");
        window.location.href = '/';

    }
    else
    {
        dataObject = JSON.parse(localStorage.getItem("data_object_he"));

    }


    tempDiscountFromCompanyCal =  localStorage.getItem("tempDiscountFromCompanyCal");


    foodCartData = dataObject.rests_orders[selectedRestIndex].foodCartData;


    // INITIALIZING ORDER DETAIL POPUP

    if(dataObject.rests_orders[selectedRestIndex].order_detail == null)
    {
        dataObject.rests_orders[selectedRestIndex].order_detail = [];
    }


    $("#rest-title").html(dataObject.rests_orders[selectedRestIndex].selectedRestaurant.name_he);


    $("#rest-address").html(dataObject.rests_orders[selectedRestIndex].selectedRestaurant.address_he);


    $('#user_name').html(dataObject.user.name+", נעים להכיר אותך :)");


    $("#name_company").html(dataObject.user.name+", "+dataObject.company.company_name+" <em> "+dataObject.user.userDiscountFromCompany+' ש"ח '+"</em>");


    $("#contact").html(dataObject.rests_orders[selectedRestIndex].selectedRestaurant.contact);


    $('#name').val(dataObject.user.name);



    displayRestDetail();


    commonAjaxCall("/restapi/index.php/get_all_cards_info", {"user_email": dataObject.user.email}, user_cards_callback);

});



function user_cards_callback(url,response)
{

    try {
        var resp = response;

        user_cards = resp;


        if (resp == 'null') {


            // NO CARDS FOUND AGAINST USER

            $('#card_list_parent').css("visibility","hidden");
            $('#cc_use_other_card').css("visibility","hidden");
            $('#cc_use_other_card').hide();



        }
        else {

            // DISPLAY ALL AVAILABLE CARDS
            $('#cc_use_other_card').css("visibility","visible");
            $('#cc_use_other_card').show();
            $('#card_list_parent').css("visibility","visible");


            str = "";

            for (var x = 0; x < resp.length; x++) {
                str += '<a onclick="cardSelected(' + x + ')" class="dropdown-item" href="#">' + resp[x].card_mask + '</a>';
            }


            $('#choose_card').html(resp[(resp.length-1)].card_mask);
            $("#cards_list").html(str);

            dataObject.selectedCardId = resp[(resp.length-1)].id;

            $('#card-cb').prop('checked', true);
            $('#cash-cb').prop('checked', false);
        }



        updateCartElements();


        setTimeout(function(){

            keepLoaderUntilPageLoad = false;
            hideLoading();


        },500);


    }
    catch (exp)
    {

        errorHandlerServerResponse(url,"parsing error call back");
        setTimeout(function(){

            keepLoaderUntilPageLoad = false;
            hideLoading();


        },500);


    }

}

function cardSelected(index) {

    dataObject.selectedCardId = user_cards[index].id;
    $('#choose_card').html(user_cards[index].card_mask);
    $('#card-option').hide();
    $('#cc_use_other_card').show();

}



// UPDATE FOOD CART
function updateCartElements()
{
    var countItems = 0;

    // DISPLAY FOOD CART IF AT LEAST ONE ITEM TO DISPLAY
    if(foodCartData.length != 0)
    {
        var str = '';

        for (var x = 0; x < foodCartData.length; x++)
        {

            if(foodCartData[x].specialRequest == "" && foodCartData[x].detail == "")
            {
                str +=  '<div class="order-item hide-row-extra"> ';
            }
            else {

                str +=  '<div class="order-item"> ';
            }



            str += '<div class="row no-gutters wd">'+
                '<div class="col-xs-1">'+
                '</div>'+
                '<div class="col-xs-11">'+
                '<div class="row no-gutters">'+
                '<div class="col-xs-9" style="padding: 0 7px 0 0;">'+
                '<p class="f black" style="min-width: 120px; white-space: nowrap; overflow: hidden !important; text-overflow: ellipsis;">'+foodCartData[x].name_he +'</p>'+
                '</div>'+
                '<div class="col-xs-3">'+
                '<p class="f black amount">'+foodCartData[x].price+' ש״ח  '+'</p>'+
                '</div>'+
                '</div>'+
                '</div>'+
                '</div>'+


                '<div class="row no-gutters hide-row">'+
                '<div class="col-xs-1">'+
                '<div class="btn-arrow">'+
                '<a href="#" class="btn-up" id="left-btn'+x+'"  onclick="onQtyIncreaseButtonClicked(' + x + ')" >'+
                '<i class="fa fa-angle-up"  aria-hidden="true"></i>'+
                '</a>'+
                '<span id="count'+x+'" class="count f black">'+ foodCartData[x].qty.toString() +'</span>'+
                '<a href="#" class="btn-down" onclick="onQtyDecreasedButtonClicked(' + x + ')">'+
                '<i class="fa fa-angle-down" aria-hidden="true"></i>'+
                '</a>'+
                '</div>'+
                '</div>'+
                '<div class="col-xs-11">'+
                '<div class="row no-gutters">'+
                '<div class="col-xs-7" style="padding: 0 7px 0 0;">'+
                '<p class="f black" style="min-width: 120px; white-space: nowrap; overflow: hidden !important; text-overflow: ellipsis;">'+foodCartData[x].name_he +'</p>'+
                '</div>'+
                '<div class="col-xs-5">'+
                '<a class="remove" onclick="removeItem(' + x + ')"   href="#"><img class="fa-times" src="/he/images/ic_cancel.png"></a>'+
                '<p class="f black amount">'+foodCartData[x].price+' ש״ח  '+'</p>'+
                '</div>'+
                '</div>'+
                '</div>'+
                '</div>'+


                '<div class="row no-gutters wd">'+
                '<div class="col-xs-1">'+
                '<div class="btn-arrow">'+
                '<a href="#" class="btn-up" id="left-btn'+x+'"  onclick="onQtyIncreaseButtonClicked(' + x + ')" >'+
                '<i class="fa fa-angle-up"  aria-hidden="true"></i>'+
                '</a>'+
                '<span id="count'+x+'" class="count f black">'+ foodCartData[x].qty.toString() +'</span>'+
                '<a href="#" class="btn-down" onclick="onQtyDecreasedButtonClicked(' + x + ')">'+
                '<i class="fa fa-angle-down" aria-hidden="true"></i>'+
                '</a>'+
                '</div>'+
                '</div>'+

                '<div class="col-xs-10" style="padding: 0 7px 0 0;">';


            countItems = countItems +  parseInt(foodCartData[x].qty);


            if(foodCartData[x].specialRequest != "")
            {

                if(foodCartData[x].detail != "") {

                    str += '<p>' + foodCartData[x].detail_he + ', special request : ' + foodCartData[x].specialRequest + '</p>';
                }
                else
                {
                    str += '<p>' + foodCartData[x].detail_he + ' special request : ' + foodCartData[x].specialRequest + '</p>';
                }
            }
            else {

                if(foodCartData[x].detail != "") {

                    str += '<p>' + foodCartData[x].detail_he + '</p>';

                }
                else {

                    str += '<p>No detail</p>';
                }
            }

            str += '</div>'+
                '<div class="col-xs-1">'+
                '<a class="remove" onclick="removeItem(' + x + ')"   href="#"><img class="fa-times" src="/he/images/ic_cancel.png"></a>'+
                '</div>'+
                '</div>'+
                '</div>';




        }

        $('#nested-section').html(str);


        if ( convertFloat(dataObject.total_paid) > convertFloat(tempDiscountFromCompanyCal) )
        {

            dataObject.total_paid = convertFloat(convertFloat(dataObject.total_paid) - convertFloat(tempDiscountFromCompanyCal));
            dataObject.company_contribution = tempDiscountFromCompanyCal;
            tempDiscountFromCompanyCal = 0;

        }
        else
        {

            tempDiscountFromCompanyCal = convertFloat(convertFloat(tempDiscountFromCompanyCal) - convertFloat(dataObject.total_paid));
            dataObject.company_contribution = dataObject.total_paid;
            dataObject.total_paid = 0;

        }


        $('#total_paid').html(dataObject.total_paid + ' ש"ח ');


        $('#cc_parent').show();


        $('#company_contribution').html(dataObject.company_contribution + ' ש"ח ');


        $('#st_parent').show();


        $('#actual_total').html(dataObject.actual_total + ' ש"ח ');


    }


    $('.badge').html(countItems);


    if(dataObject.total_paid == 0)
    {

        $('#hidePaymentOption').hide();
        paymentException = true;
        $('.box-list').addClass('f-size-change');


    }
    else {


        $('#hidePaymentOption').show();
        paymentException = false;
        $('.box-list').removeClass('f-size-change');

    }

}





// RESTAURANT DETAIL POPUP

function displayRestDetail() {


    var selectedRest = dataObject.rests_orders[selectedRestIndex].selectedRestaurant;

    // SETTING RESTAURANT DETAILS

    var temp =  '<div class="img-frame">'+
        '<a href="#"><img src="'+ selectedRest.logo +'" alt="logo-img"></a>'+
        '</div>'+
        '<h2>'+ selectedRest.name_he +'</h2>'+
        '<p class="f white">'+ selectedRest.address_he +'</p>'+
        '<span class="cart f white">הזמנה מינימלית '+ selectedRest.min_amount +' ש"ח '+'</span>'+
        '<div class="wrap">'+
        '<div class="baron">'+
        '<div id="scrollable-pragraph" class="baron__scroller">'+
        '<p class="f white">'+ selectedRest.description_he +'</p>'+
        '</div>'+
        '<div class="baron__track">'+
        '<div class="baron__bar add"></div>'+
        '</div>'+
        '</div>'+
        '<img class="mobile-sec" src="/he/images/delivery line.png">';

    $('#rest-detail').html(temp);



    // SETTING TIMING OF CURRENT RESTAURANT

    temp = '';

    for (i = 0; i < selectedRest.timings.length; i++)
    {

        temp += '<tr><td>'+ selectedRest.timings[i].week_he +'</td>'+
            '<td>'+ selectedRest.timings[i].opening_time + ' - ' + selectedRest.timings[i].closing_time +'</td></tr>';

    }

    $('#time-detail').html(temp);



    // SETTING GALLERY IMAGES

    temp = '';

    for (i = 0; i < selectedRest.gallery.length; i++)
    {

        if (i == 0)
            temp = '<div class="item active">';
        else
            temp += '<div class="item">';

        temp += '<img src="'+ selectedRest.gallery[i].url +'" alt="Chania" width="50%">'+
            '</div>';

    }

    $('#gallery-images').html(temp);



    // SETTING MINIMUM ORDER

    temp = '<p>Minimum Order '+ selectedRest.min_amount +' ש"ח '+'</p>';

    $('#min-order').html(temp);



}



// PROCESS PAYMENT START


function onOrderNowClicked() {

    if(!paymentException) {

        $('#error-user-name').hide();
        $('#name-error-dot').hide();
        $('#error-card-number').hide();
        $('#card-error-dot').hide();
        $('#dropdownMenuButton').removeClass('add-error');
        $('#dropdownMenuButton2').removeClass('add-error');
        $('#error-cvv').hide();
        $('#error_cvv_dot').hide();
        $('#cvv-card-icon').show();

        // CASH

        if ($('#cash-cb').is(":checked")) {

            dataObject.payment_option = "CASH";
            processOrder();


        }

        // CREDIT CARD

        else {

            dataObject.payment_option = "CARD";

            // NEW CARD ADDITION

            if (dataObject.selectedCardId == null) {

                // SAVE CARD FIRST

                if ($('#check_save_card').is(":checked")) {

                    payment_credit_card("save");

                }

                // USE CARD WITHOUT SAVE

                else {


                    $('#card-errors').html("");
                    payment_credit_card("direct");

                }

            }

            // USER WANT TO USE FROM EXISTING CARDS

            else {

                payment_credit_card("");

            }

        }

    }
    else {

        dataObject.payment_option = "No Payment";
        processOrder();

    }

}


function addNewCard() {


    $('#error-user-name').hide();
    $('#name-error-dot').hide();
    $('#error-card-number').hide();
    $('#card-error-dot').hide();
    $('#dropdownMenuButton').removeClass('add-error');
    $('#dropdownMenuButton2').removeClass('add-error');
    $('#error-cvv').hide();
    $('#error_cvv_dot').hide();
    $('#cvv-card-icon').show();


    if ($('#name').val() == "") {

        $('#error-user-name').show();
        $('#name-error-dot').show();
        return;

    }

    // CARD NO SHOULD NOT BE EMPTY
    if ($('#card_no').val() == "") {

        $('#error-card-number').show();
        $('#error-card-text').html("*שדה נדרש");
        $('#card-error-dot').show();
        return;
    }

    if ((!$('#card_no').val().match(/^\d+$/))) {

        if ($("#card_no").val() != '') {

            $('#error-card-number').show();
            $('#error-card-text').html("מספר כרטיס שגוי!");
            $('#card-error-dot').show();
            return;
        }
    }


    // MONTH SHOULD NOT BE EMPTY
    if ($('#dropdownMenuButton').html() == "יום") {


        $('#dropdownMenuButton').addClass('add-error');
        return;

    }

    // YEAR SHOULD NOT BE EMPTY
    if ($('#dropdownMenuButton2').html() == "שנה") {


        $('#dropdownMenuButton2').addClass('add-error');
        return;

    }

    // CVV SHOULD NOT BE EMPTY
    if ($('#cvv').val() == "") {

        $('#error-cvv').show();
        $('#error-cvv-text').html("* כרטיס CVV חובה");
        $('#error_cvv_dot').show();
        $('#cvv-card-icon').hide();
        return;
    }


    var exp = $('#dropdownMenuButton').html() + $('#dropdownMenuButton2').html();


    commonAjaxCall("/restapi/index.php/store_credit_card_info", {"user_email": dataObject.user.email,"card_no":$('#card_no').val(),"expiry":exp,"cvv":$('#cvv').val()}, addNewCardCallBack);
}


function addNewCardCallBack(url,response) {

    try {

        resp = response;

        if(resp.success == true)
        {

            var str = "";

            for(var x=0;x<resp.cards.length;x++)
            {
                str += '<a onclick="cardSelected('+x+')" class="dropdown-item" href="#">'+resp.cards[x].card_mask+'</a>';
            }

            user_cards = resp.cards;

            $("#cards_list").html(str);

            // DISPLAY ALL AVAILABLE CARDS
            $('#cc_use_other_card').css("visibility","visible");
            $('#cc_use_other_card').show();
            $('#card_list_parent').css("visibility","visible");
            $('#card-option').hide();
            $('#choose_card').html(user_cards[(resp.cards.length-1)].card_mask);

            dataObject.selectedCardId = user_cards[(resp.cards.length-1)].id;

            onCancel();

            $('#card-errors').html("");


        }
        else {


            $('#card-errors').html(resp.error);

        }

    }
    catch (exp)
    {


        errorHandlerServerResponse(url,"parsing error call back");
        hideLoading();


    }


}


function onCancel() {

    $('#card_no').val("");
    $('#cvv').val("");
    $('#dropdownMenuButton2').html("שנה");
    $('#dropdownMenuButton').html("יום");

    $('#error-user-name').hide();
    $('#name-error-dot').hide();
    $('#error-card-number').hide();
    $('#card-error-dot').hide();
    $('#dropdownMenuButton').removeClass('add-error');
    $('#dropdownMenuButton2').removeClass('add-error');
    $('#error-cvv').hide();
    $('#error_cvv_dot').hide();
    $('#cvv-card-icon').show();

    $('#card-errors').html('');
}




// CREDIT CARD PAYMENT
function payment_credit_card(usage) {

    // USER WANT TO USE FROM EXISTING CARDS
    if(usage == "") {

        // CARD ID WILL BE USED
        commonAjaxCall("/restapi/index.php/stripe_payment_request", {"order_data": dataObject,"card_no" : "","expiration":"","cvv":""}, paymentCreditCardCallBack);

    }
    else {

       // usage  -> direct use card without save
       // usage  -> save save card and then use

        if(usage == "direct")
        {


            $('#error-user-name').hide();
            $('#name-error-dot').hide();
            $('#error-card-number').hide();
            $('#card-error-dot').hide();
            $('#dropdownMenuButton').removeClass('add-error');
            $('#dropdownMenuButton2').removeClass('add-error');
            $('#error-cvv').hide();
            $('#error_cvv_dot').hide();
            $('#cvv-card-icon').show();


            if ($('#name').val() == "") {

                $('#error-user-name').show();
                $('#name-error-dot').show();
                return;

            }

            // CARD NO SHOULD NOT BE EMPTY
            if ($('#card_no').val() == "") {

                $('#error-card-number').show();
                $('#error-card-text').html("*שדה נדרש");
                $('#card-error-dot').show();
                return;
            }

            if ((!$('#card_no').val().match(/^\d+$/))) {

                if ($("#card_no").val() != '') {

                    $('#error-card-number').show();
                    $('#error-card-text').html("מספר כרטיס שגוי!");
                    $('#card-error-dot').show();
                    return;
                }
            }


            // MONTH SHOULD NOT BE EMPTY
            if ($('#dropdownMenuButton').html() == "יום") {


                $('#dropdownMenuButton').addClass('add-error');
                return;

            }

            // YEAR SHOULD NOT BE EMPTY
            if ($('#dropdownMenuButton2').html() == "שנה") {


                $('#dropdownMenuButton2').addClass('add-error');
                return;

            }

            // CVV SHOULD NOT BE EMPTY
            if ($('#cvv').val() == "") {

                $('#error-cvv').show();
                $('#error-cvv-text').html("* כרטיס CVV חובה");
                $('#error_cvv_dot').show();
                $('#cvv-card-icon').hide();
                return;
            }



            var exp = $('#dropdownMenuButton').html() + $('#dropdownMenuButton2').html();


            keepLoaderUntilPageLoad = true;

            commonAjaxCall("/restapi/index.php/stripe_payment_request", {"order_data": dataObject, "card_no": $('#card_no').val(), "expiration":exp, "cvv": $('#cvv').val() }, paymentCreditCardCallBack);

        }
        else {

            addNewCard();
        }

    }

}



function paymentCreditCardCallBack(url, response) {

    var resp = response;

    try {

        if(resp.response == "success")
        {

            dataObject.transactionId = resp.trans_id;
            processOrder();
        }
        else
        {
            $('#card-errors').html(resp.response);
            keepLoaderUntilPageLoad = false;
            hideLoading();
        }

    }
    catch (exp)
    {


        errorHandlerServerResponse(url,"parsing error call back");
        keepLoaderUntilPageLoad = false;
        hideLoading();


    }

}


function processOrder() {


    addLoading();

    setTimeout(function() {

        dataObject.platform_info =  "B2B_DESKTOP_HE";
        dataObject.browser_info  =  BrowserInfo();


        commonAjaxCall("/restapi/index.php/b2b_add_order", {"b2b_user_order": dataObject },processOrderCallBack);


    },1000);



}


function processOrderCallBack(url, response)
{

    try {


        var newBalance = dataObject.user.userDiscountFromCompany - dataObject.company_contribution;

        $('#order_complete_message').html( dataObject.user.name+' '+dataObject.company.company_name +' אנחנו בדרך ההגעה המשוער '+ dataObject.company.delivery_time);
        $("#name_company").html(dataObject.user.name+", "+dataObject.company.company_name+" <em> "+newBalance+' ש"ח '+"</em>");


        $(".order-info").hide();
        $(".txt-block").show();


            keepLoaderUntilPageLoad = false;
            hideLoading();


    }
    catch (exp)
    {


        errorHandlerServerResponse(url,"parsing error call back");
        keepLoaderUntilPageLoad = false;
        hideLoading();


    }

}


function convertFloat(num)
{

    return parseFloat(parseFloat(num).toFixed(2));

}




function BrowserInfo() {

    var nVer = navigator.appVersion;
    var nAgt = navigator.userAgent;
    var browserName  = navigator.appName;
    var fullVersion  = ''+parseFloat(navigator.appVersion);
    var majorVersion = parseInt(navigator.appVersion,10);
    var nameOffset,verOffset,ix;

// In Opera, the true version is after "Opera" or after "Version"
    if ((verOffset=nAgt.indexOf("Opera"))!=-1) {
        browserName = "Opera";
        fullVersion = nAgt.substring(verOffset+6);
        if ((verOffset=nAgt.indexOf("Version"))!=-1)
            fullVersion = nAgt.substring(verOffset+8);
    }
// In MSIE, the true version is after "MSIE" in userAgent
    else if ((verOffset=nAgt.indexOf("MSIE"))!=-1) {
        browserName = "Microsoft Internet Explorer";
        fullVersion = nAgt.substring(verOffset+5);
    }
// In Chrome, the true version is after "Chrome"
    else if ((verOffset=nAgt.indexOf("Chrome"))!=-1) {
        browserName = "Chrome";
        fullVersion = nAgt.substring(verOffset+7);
    }
// In Safari, the true version is after "Safari" or after "Version"
    else if ((verOffset=nAgt.indexOf("Safari"))!=-1) {
        browserName = "Safari";
        fullVersion = nAgt.substring(verOffset+7);
        if ((verOffset=nAgt.indexOf("Version"))!=-1)
            fullVersion = nAgt.substring(verOffset+8);
    }
// In Firefox, the true version is after "Firefox"
    else if ((verOffset=nAgt.indexOf("Firefox"))!=-1) {
        browserName = "Firefox";
        fullVersion = nAgt.substring(verOffset+8);
    }
// In most other browsers, "name/version" is at the end of userAgent
    else if ( (nameOffset=nAgt.lastIndexOf(' ')+1) <
        (verOffset=nAgt.lastIndexOf('/')) )
    {
        browserName = nAgt.substring(nameOffset,verOffset);
        fullVersion = nAgt.substring(verOffset+1);
        if (browserName.toLowerCase()==browserName.toUpperCase()) {
            browserName = navigator.appName;
        }
    }
// trim the fullVersion string at semicolon/space if present
    if ((ix=fullVersion.indexOf(";"))!=-1)
        fullVersion=fullVersion.substring(0,ix);
    if ((ix=fullVersion.indexOf(" "))!=-1)
        fullVersion=fullVersion.substring(0,ix);

    majorVersion = parseInt(''+fullVersion,10);
    if (isNaN(majorVersion)) {
        fullVersion  = ''+parseFloat(navigator.appVersion);
        majorVersion = parseInt(navigator.appVersion,10);
    }

    return browserName;

}