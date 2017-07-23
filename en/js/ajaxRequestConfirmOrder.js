var dataObject          = null;      // DATA OBJECT CONTAIN INFORMATION ABOUT COMPANY, USER & USER ORDER

var selectedRestIndex   = 0;

var foodCartData        = null;     // FOOD CART OBJECT

var tempDiscountFromCompanyCal = 0;

var user_cards  = null;


var keepLoaderUntilPageLoad   =  true;


$(document).ready(function() {


    dataObject = JSON.parse(localStorage.getItem("data_object_en"));


    tempDiscountFromCompanyCal =  localStorage.getItem("tempDiscountFromCompanyCal");


    foodCartData = dataObject.rests_orders[selectedRestIndex].foodCartData;


    // INITIALIZING ORDER DETAIL POPUP

    if(dataObject.rests_orders[selectedRestIndex].order_detail == null)
    {
        dataObject.rests_orders[selectedRestIndex].order_detail = [];
    }


    $("#rest-title").html(dataObject.rests_orders[selectedRestIndex].selectedRestaurant.name_en);


    $("#rest-address").html(dataObject.rests_orders[selectedRestIndex].selectedRestaurant.address_en);


    $('#user_name').html(dataObject.user.name+", Nice to meet you :)");


    $("#name_company").html(dataObject.user.name+", "+dataObject.company.company_name+" <em> "+dataObject.user.userDiscountFromCompany+" NIS</em>");


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

            countItems = countItems +  parseInt(foodCartData[x].qty);


            str +=  '<div class="order-item add"> ' +
                '<div class="row no-gutters">' +
                ' <div class="col-xs-2"> ' +

                '<div class="btn-arrow new-add">'+
                '<a href="#"  class="btn-up"  id="left-btn'+x+'" onclick="onQtyIncreaseButtonClicked(' + x + ')" class="left-btn"><i class="fa fa-angle-up" aria-hidden="true"></i></a>'+
                '<span id="count'+x+'" class="count f black">' + foodCartData[x].qty.toString() + '</span>' +
                '<a href="#" class="btn-down" onclick="onQtyDecreasedButtonClicked(' + x + ')" class="increase-btn"><i class="fa fa-angle-down" aria-hidden="true"></i></a>' +
                '</div>'+

                '</div> ' +
                '<div class="col-xs-10"> ' +
                '<div class="row no-gutters">' +
                '<div class="col-xs-7">'+
                '<p class="f black" style="min-width: 120px; white-space: nowrap; overflow: hidden !important; text-overflow: ellipsis;">' + foodCartData[x].name + '</p>'+
                '</div>'+
                '<div class="col-xs-5">'+
                '<a class="remove new" onclick="removeItem(' + x + ')" href="#"><i class="fa fa-times" aria-hidden="true"></i></a>'+
                '<p class="f black amount">' + foodCartData[x].price  + ' NIS</p>'+
                '</div>'+
                '</div>'+
                '</div>'+
                '</div>'+
                '<div class="row no-gutters hide-row">'+
                '<div class="col-xs-2">'+
                '<div class="btn-arrow">'+
                '<a href="#"  class="btn-up"  id="left-btn'+x+'" class="left-btn"><i class="fa fa-angle-up" aria-hidden="true"></i></a>'+
                '<span id="count'+x+'" class="count f black">' + foodCartData[x].qty.toString() + '</span>' +
                '<a href="#" class="btn-down"  class="increase-btn"><i class="fa fa-angle-down" aria-hidden="true"></i></a>' +
                '</div>'+
                '</div>'+
                '<div class="col-xs-8">';


            if(foodCartData[x].specialRequest != "")
            {

                if(foodCartData[x].detail != "") {

                    str += '<p>' + foodCartData[x].detail + ', special request : ' + foodCartData[x].specialRequest + '</p>';
                }
                else
                {
                    str += '<p>' + foodCartData[x].detail + ' special request : ' + foodCartData[x].specialRequest + '</p>';
                }
            }
            if(foodCartData[x].detail != "") {

                str += '<p>' + foodCartData[x].detail + '</p>';

            }
            else {

                str += '<p>No detail</p>';
            }


            str += '</div>'+
                '<div class="col-xs-2">'+
                '<a class="remove" href="#"><i class="fa fa-times" aria-hidden="true"></i></a>'+
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


        $('#total_paid').html(dataObject.total_paid + " NIS");


        $('#cc_parent').show();


        $('#company_contribution').html(dataObject.company_contribution + " NIS");


        $('#st_parent').show();


        $('#actual_total').html(dataObject.actual_total + " NIS");

    }


    $('.badge').html(countItems);

    $('#card-message').html('Payment '+dataObject.total_paid+' NIS Card No.');

    $('#cash-message').html('Payment '+dataObject.total_paid+' NIS with Cash');

}






// RESTAURANT DETAIL POPUP

function displayRestDetail() {


    var selectedRest = dataObject.rests_orders[selectedRestIndex].selectedRestaurant;

    // SETTING RESTAURANT DETAILS

    var temp =  '<div class="img-frame">'+
        '<a href="#"><img src="'+ selectedRest.logo +'" alt="logo-img"></a>'+
        '</div>'+
        '<h2>'+ selectedRest.name_en +'</h2>'+
        '<p class="f white">'+ selectedRest.address_en +'</p>'+
        '<span class="cart f white">Minimum Order '+ selectedRest.min_amount +' NIS</span>'+
        '<div class="wrap">'+
        '<div class="baron">'+
        '<div id="scrollable-pragraph" class="baron__scroller">'+
        '<p class="f white">'+ selectedRest.description_en +'</p>'+
        '</div>'+
        '<div class="baron__track">'+
        '<div class="baron__bar add"></div>'+
        '</div>'+
        '</div>'+
        '<img class="mobile-sec" src="/en/images/delivery line.png">';

    $('#rest-detail').html(temp);



    // SETTING TIMING OF CURRENT RESTAURANT

    temp = '';

    for (i = 0; i < selectedRest.timings.length; i++)
    {

        temp += '<tr><td>'+ selectedRest.timings[i].week_en +'</td>'+
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

    temp = '<p>Minimum Order '+ selectedRest.min_amount +' NIS</p>';

    $('#min-order').html(temp);



}






function addNewCard() {


    $('#error-user-name').hide();
    $('#error-card-number').hide();
    $('#dropdownMenuButton').removeClass('add-error');
    $('#dropdownMenuButton2').removeClass('add-error');
    $('#error-cvv').hide();


    if ($('#name').val() == "") {

        $('#error-user-name').show();
        return;

    }

    // CARD NO SHOULD NOT BE EMPTY
    if ($('#card_no').val() == "") {

        $('#error-card-number').show();
        $('#error-card-text').html("*Required Field");
        return;
    }


    if ((!$('#card_no').val().match(/^\d+$/))) {

        if ($("#card_no").val() != '') {

            $('#error-card-number').show();
            $('#error-card-text').html("Invalid Card Number!");
            return;
        }
    }


    // MONTH SHOULD NOT BE EMPTY
    if ($('#dropdownMenuButton').html() == "MM") {


       $('#dropdownMenuButton').addClass('add-error');
        return;

    }

    // YEAR SHOULD NOT BE EMPTY
    if ($('#dropdownMenuButton2').html() == "YY") {


        $('#dropdownMenuButton2').addClass('add-error');
        return;

    }

    // CVV SHOULD NOT BE EMPTY
    if ($('#cvv').val() == "") {

        $('#error-cvv').show();
        $('#error-cvv-text').html("*Card CVV Required");

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
    $('#dropdownMenuButton2').html("YY");
    $('#dropdownMenuButton').html("MM");


    $('#error-user-name').hide();
    $('#error-card-number').hide();
    $('#dropdownMenuButton').removeClass('add-error');
    $('#dropdownMenuButton2').removeClass('add-error');
    $('#error-cvv').hide();

    $('#card-errors').html('');
}


function onOrderNowClicked() {


    $('#error-user-name').hide();
    $('#error-card-number').hide();
    $('#error-month-year').hide();
    $('#error-cvv').hide();


    // CASH

    if($('#cash-cb').is(":checked"))
    {

        dataObject.payment_option = "CASH";
        processOrder();


    }

    // CREDIT CARD

    else {


        dataObject.payment_option = "CARD";


        if(dataObject.selectedCardId == null) {


            $('#card-errors').html("Add Card!");

        }
        else {

            $('#card-errors').html("");
            payment_credit_card();
        }
    }

}



// CREDIT CARD PAYMENT
function payment_credit_card() {


    commonAjaxCall("/restapi/index.php/stripe_payment_request", {"order_data": dataObject },paymentCreditCardCallBack);


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

        }

    }
    catch (exp)
    {


        errorHandlerServerResponse(url,"parsing error call back");
        hideLoading();


    }

}


function processOrder() {


    dataObject.platform_info =  "B2B_DESKTOP_EN";
    dataObject.browser_info  =  BrowserInfo();


    commonAjaxCall("/restapi/index.php/b2b_add_order", {"b2b_user_order": dataObject },processOrderCallBack);


}


function processOrderCallBack(url, response)
{

    try {

        $('#order_complete_message').html( dataObject.user.name+' '+dataObject.company.company_name +' We are on the way estimated arrival '+ dataObject.company.delivery_time);

         $(".txt-block").show();
         $(".order-info").hide();

    }
    catch (exp)
    {


        errorHandlerServerResponse(url,"parsing error call back");
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