var rawResponseALlRests   = null;  // LOCAL VARIABLE


var dataObject;                  // DATA OBJECT CONTAIN INFORMATION ABOUT COMPANY, USER & USER ORDER



var listOfRestaurants = null;    // LIST OF ALL RESTAURANTS RECEIVED FROM SERVER
                                 //array restaurants -> {id,city_he,city_he,name_he,name_he,min_amount,tags,logo,description_en,description_he,
                                 // address_he,address_he,hechsher_en,hechsher_he,gallery,rest_lat,rest_lng,timings,today_timings,percentage_discount,}}


var company_open_status;         // BUSINESS ORDERING IS OPEN OR CLOSED NOW




var delivery_time_str = "";      // DELIVERY TIME STRING i.e 12:30 - 1:30



var appox_delivey_time = "";      // DELIVERY TIME TILL ORDER RECEIVED  i.e 1:30




var past_orders_object = null;     // USER'S PAST ORDERS OBJECT




var pending_orders_object = null;  // USER'S PENDING ORDERS OBJECT



var db_tags  = null;               // CUISINE TAGS OF RESTAURNATS i.e MEAT, BURGER, HEALTH



var db_kashrut = null;             // kashrut OF RESTAURANTS i.e Mehardin


var track  = null;


var keepLoaderUntilPageLoad   =  true;


var deliveryClosedTimeInMiliSec = null;


// AFTER DOCUMENTED LOADED
$(document).ready(function() {


    localStorage.setItem("USER_LANGUAGE","HE");

    var user_id   =   localStorage.getItem("user_id_b2b");


    dataObject = {

        'language': 'he',                  // USER LANGUAGE ENGLISH DESKTOP B2B
        'company': '',                     // attributes are {company_id, company_name, company_address,delivery_time}
        'user': '',                        // attributes are {user_id, name, email, contact, userDiscountFromCompany}
        'rests_orders': [],                // ARRAY OF MULTIPLE REST ORDERS
        'actual_total' : 0,                // ACTUAL TOTAL (BILL) WITHOUT ANY DISCOUNT AND COMPENSATION
        'total_paid' : 0,                  // TOTAL AMOUNT PAID BY USER
        "company_contribution" : 0,        // AMOUNT CONTRIBUTED BY COMPANY
        "payment_option" : 'CASH',         // PAYMENT OPTION CASH / CARD  DEFAULT CASH
        "discount": 0,                     // COMPANY DISCOUNT
        "selectedCardId" : null,           // SELECTED CARD ID BY USER FROM EXISTING CARDS
        "transactionId" : ""               // TRANSACTION ID RECEIVED FROM CREDIT GUARD ON CARD PAYMENT ( REUQIRED IN CASE OF CANCEL ORDERS)
    };


    track = localStorage.getItem("order_on_way");


    commonAjaxCall("/restapi/index.php/confirm_user_login",{"user_id" : user_id}, responseCallBackSessionLogin);

});


function responseCallBackSessionLogin(url,response) {

    try
    {
        if (!response.error) {


            dataObject.company = response.company;
            dataObject.user    = response.user;

            if(response.on_way_order_count > 0)
            {

                $('#change-status-ow').addClass('active');

            }
            else
            {

                $('#change-status-ow').removeClass('active');

            }



            // USER CONFIRMED FROM SYSTEM

            commonAjaxCall("/restapi/index.php/get_db_tags_and_kashrut", {
                "company_id": dataObject.company.company_id,
                "user_id": dataObject.user.user_id
            }, responseDBAllTagsKashrut);


        }
        else {


            // SESSION USER VERIFICATION LOGIN FAIL NEED LOGIN AGAIN
            localStorage.setItem('user_id_b2b', '');
            window.location.href='/';

        }


    }
    catch (exp)
    {

        errorHandlerServerResponse(url,"parsing error call back");
        hideLoading();

    }


}



function responseDBAllTagsKashrut(url, response) {


    try {

        db_tags             = response.db_tags;
        db_kashrut          = response.db_kashrut;

        var str = "";

        for(var x = 0; x < db_tags.length ; x++)
        {

            if(db_tags[x].count != "0") {

                str += '<li id="filter-item-tags-' + x + '" >' +
                    '<label class="control control--checkbox">' +
                    '<input id="cb-tags-' + x + '" onclick="onFilterChange(' + x + ')" type="checkbox">' +
                    '<div class="control__indicator"></div><span id="cb-tags-title' + x + '">' + db_tags[x].name_he + ' [' + db_tags[x].count + ']' + '</span>' +
                    '</label>' +
                    '</li>';
            }
        }

        $('#tags').html(str);


        str = "";

        for(var x = 0; x < db_kashrut.length ; x++)
        {

            if(db_kashrut[x].count != "0") {

                str += '<li id="filter-item-kashrut-' + x + '">' +
                    '<label class="control control--checkbox">' +
                    '<input id="cb-kashrut-' + x + '" onclick="onFilterChange(' + x + ')"   type="checkbox">' +
                    '<div class="control__indicator"></div><span id="cb-kashrut-title' + x + '">' + db_kashrut[x].name_he + ' [' + db_kashrut[x].count + ']' + '</span>' +
                    '</label>' +
                    '</li>';

            }
        }


        $('#kashruts').html(str);

        $('.list-item').show();

        $('#user_name').html(dataObject.user.name+", נעים להכיר אותך :)");

        $("#name_company").html(dataObject.user.name+", "+dataObject.company.company_name+" <em> "+dataObject.user.userDiscountFromCompany+' ש"ח '+"</em>");

        $("#name2").html(' היי '+dataObject.user.name+", "+dataObject.company.company_name);

        $("#name3").html(' היי '+dataObject.user.name+", "+dataObject.company.company_name);


        // REQUEST ALL RESTAURANTS FROM SERVER
        commonAjaxCall("/restapi/index.php/get_all_restaurants", {"company_id":dataObject.company.company_id}, responseListOfRestaurants);

    }
    catch (exp)
    {

        errorHandlerServerResponse(url,"parsing error call back");
        hideLoading();

    }
}


function DeliveryTimeClosedTimerUpdate(milisecTime)
{

    setTimeout(function myFunction() {

        // REDIRECT TO VOTING

        location.reload();


    }, milisecTime)

}


function responseListOfRestaurants(url,response) {


    try {

        rawResponseALlRests = response;
        listOfRestaurants   = response.restaurants;
        company_open_status = response.company_open_status;
        delivery_time_str   = response.delivery_time_str;
        appox_delivey_time  = response.appox_delivey_time;
        deliveryClosedTimeInMiliSec  = response.delivery_time_milisec;



        if(deliveryClosedTimeInMiliSec != null)
        {
            DeliveryTimeClosedTimerUpdate(deliveryClosedTimeInMiliSec);
        }


        dataObject.company.delivery_time = delivery_time_str;


        var str = '';


        for(var x=0;x<listOfRestaurants.length;x++)
        {

            var kashrutString  =  fromKashrutToString(listOfRestaurants[x]);
            var tagsString     =  fromTagsToString(listOfRestaurants[x]);


            var isShow = false;
            var tagAtLeastOneCheck = false;
            var kashrutAtLeastOneCheck = false;


            for(var y = 0; y < db_tags.length ; y++)
            {

                var cb_id   = "#cb-tags-"+y;

                if($(cb_id).is(':checked'))
                {
                    tagAtLeastOneCheck = true;

                    for (var z = 0; z < listOfRestaurants[x].tags.length; z++) {

                        var tag = listOfRestaurants[x].tags[z]['name_he'];

                        if ((tag.toLowerCase()).includes(db_tags[y].name_he.toLowerCase())) {

                            isShow = true;
                            break;

                        }

                    }

                    if(isShow)
                    {
                        break;
                    }
                }
            }


            for(var y = 0; y < db_kashrut.length ; y++)
            {
                var cb_id   = "#cb-kashrut-"+y;

                if($(cb_id).is(':checked'))
                {
                    kashrutAtLeastOneCheck = true;

                    for (var z = 0; z < listOfRestaurants[x].kashrut.length; z++) {

                        var kasrut = listOfRestaurants[x].kashrut[z]['name_he'];

                        if ((kasrut.toLowerCase()).includes(db_kashrut[y].name_he.toLowerCase())) {

                            isShow = true;
                            break;

                        }

                    }

                    if(isShow)
                    {
                        break;
                    }
                }

            }


            if(isShow || (!(kashrutAtLeastOneCheck || tagAtLeastOneCheck))) {


                // RESTAURANTS ORDERING ENABLE

                if (company_open_status) {

                    str +=
                        '<li>' +
                        '<ul>' +
                        '<li>' +
                        '<div class="img-circle">' +
                        '<img src="' + listOfRestaurants[x].logo + '" alt="images description">' +
                        '</div>' +
                        '<div class="txt">' +
                        '<h1 style="cursor: pointer" onclick="onOrderNowClicked('+x+')" class="light">' + listOfRestaurants[x].name_he + '</h1>' +
                        '<p><em class="f black">Kashrut</em> ' + kashrutString+'<br>'+tagsString+ ' </p>' +
                        '</div>' +
                        '</li>' +
                        '<li>' +
                        '<div class="text">' +
                        '<p><em class="f black">' + listOfRestaurants[x].percentage_discount + ' % הנחה</em> בין השעות  ' + listOfRestaurants[x].today_timings + ' </p>' +
                        '</div>' +
                        '</li>' +
                        '<li class="third">' +
                        '<address class="address">' +
                        '<img class="edit edit1" src="/he/images/ic_checkin.png">' +
                        '<p class="new-adress" >' + listOfRestaurants[x].address_he + '</p>' +
                        '<div class="tooltip-popup popup"><p class="f black">' + listOfRestaurants[x].address_he + '</br><strong>' + listOfRestaurants[x].city_he + '</strong></p></div>' +
                        '</address>' +
                        '</li>' +
                        '<li>' +
                        '<div class="btn-box"><button  class="bt_ordernow" type="button" onclick="onOrderNowClicked('+x+')">'+' הזמן <br> עכשיו '+'</button></div>' +

                        '<time class="time">' +

                        '<div  class="rl-dt-ma-popup-add">' +
                        '<img class="bike" src="/he/images/motorbike-delivery.png">' +
                        '<p>זמן משלוח  ' + appox_delivey_time + '</p>' +
                        '</div>' +
                        '</time>' +
                        '</li>' +
                        '</ul>' +
                        '</li>';


                }

                //  RESTAURANTS ORDERING  DISABLED

                else {


                    str +=

                        '<li class="offline">' +
                        '<ul>' +
                        '<li>' +
                        '<div class="img-circle">' +
                        '<img src="' + listOfRestaurants[x].logo + '" alt="images description">' +
                        '</div>' +
                        '<div class="txt">' +
                        '<h1 class="light">' + listOfRestaurants[x].name_he + '</h1>' +
                        '<p><em class="f black">Kashrut</em> ' + kashrutString + ' </p>' +
                        '</div>' +
                        '</li>' +
                        '<li>' +
                        '<div class="text">' +
                        '<p><em class="f black">' + listOfRestaurants[x].percentage_discount + ' % הנחה</em> בין השעות ' + listOfRestaurants[x].today_timings + ' </p>' +
                        '</div>' +
                        '</li>' +
                        '<li class="third">' +
                        '<address class="address">' +
                        '<img class="edit edit1" src="/he/images/ic_checkin.png">' +
                        '<p class="new-adress" >' + listOfRestaurants[x].address_he + '</p>' +
                        '<div class="tooltip-popup popup"><p class="f black">' + listOfRestaurants[x].address_he + '</br><strong>' + listOfRestaurants[x].city_he + '</strong></p></div>' +
                        '</address>' +
                        '</li>' +
                        '<li>' +
                        '<div class="btn-box"><button class="bt_ordernow" data-toggle="modal" data-target="#business-popup" type="button" onclick="">סגור</button></div>' +
                        '<time class="time">' +
                        '<img class="bike" src="/he/images/motorbike-delivery.png">' +
                        '<p> -- :: -- </p>' +
                        '</time>' +
                        '</li>' +
                        '</ul>' +
                        '</li>';


                }


            }

        }


        str +=  '<li class="last-row"></li>';


        $('#rest-list').html(str);


        if(track == "track")
        {
            $('#rest-list-active').removeClass("active");
            $('#pending-list-active').addClass("active");
            $('#panel43').addClass("active");
            $('#panel41').removeClass("active");


            track = "";


            localStorage.setItem("order_on_way","");

            commonAjaxCall("/restapi/index.php/get_all_pending_orders", {"user_id": dataObject.user.user_id}, responsePendingOrders);

        }


        keepLoaderUntilPageLoad = false;
        hideLoading();



    }
    catch (exp)
    {

        keepLoaderUntilPageLoad = false;
        hideLoading();

        errorHandlerServerResponse(url,"parsing error call back");

    }



}



// CONVERT ALL RESTAUTANT TAGS TO STRING
function fromKashrutToString (restaurant)
{
    var kashrut = "";

    for (var i=0 ; i < restaurant.kashrut.length ; i++)
    {

        if ( i == 0)
            kashrut += restaurant.kashrut[i]['name_he'];

        else
            kashrut += ", "+restaurant.kashrut[i]['name_he'] ;


    }

    return kashrut;
}





// CONVERT ALL RESTAUTANT TAGS TO STRING
function fromTagsToString (restaurant)
{
    var tags = "";

    for (var i=0 ; i < restaurant.tags.length ; i++)
    {

        if ( i == 0)
            tags += restaurant.tags[i]['name_he'];

        else
            tags += ", "+restaurant.tags[i]['name_he'] ;


    }

    return tags;
}




function onFilterChange() {

    if(listOfRestaurants == null) {

        // REQUEST ALL RESTAURANTS FROM SERVER
        commonAjaxCall("/restapi/index.php/get_all_restaurants", {"company_id":dataObject.company.company_id}, responseListOfRestaurants);
    }
    else {

        responseListOfRestaurants("/restapi/index.php/get_all_restaurants",rawResponseALlRests);

    }
}





// DISPLAY PAST ORDERS

function displayPastOrdersRequest() {


    if (past_orders_object == null)
    {

        if ($('#filter-past-order-cb-lastweek').is(':checked')) {


            commonAjaxCall("/restapi/index.php/get_all_past_orders", {"user_id": dataObject.user.user_id,"filter" : "last_week"}, responsePastOrders);

        }
        else {


            var start_date = $('#datepicker').val();
            var end_date = $('#datepicker2').val();

            if(start_date != "" && end_date != "") {

                commonAjaxCall("/restapi/index.php/get_all_past_orders", {"user_id": dataObject.user.user_id, "filter": "custom", "start_date": start_date, "end_date": end_date}, responsePastOrders);

            }
            else {


                alert("select dates from filter!");

            }
        }

    }
    else {

        responsePastOrders("/restapi/index.php/get_all_past_orders",past_orders_object);
    }

}


function responsePastOrders(url,response) {

    try {

        var str = '';


        if($('#filter-past-order-cb-lastweek').is(':checked')) {

            $('#last_week_orders_text').html('שבוע שעבר  [' + response.length + ']');

        }


        var cancelled_orders_count = 0;
        var emptylist = true;

        for (var x = 0; x < response.length; x++) {


            // IF COMPANY ORDERING OPEN ALLOW REORDERING
            if (company_open_status) {


                // DELIVERED ORDERS

                if (response[x].order_status == 'delivered') {

                    emptylist = false;

                    str += '<li>' +
                        '<ul>' +
                        '<li class="new-first">' +
                        '<div class="img-circle">' +
                        '<img src="' + response[x].logo + '" alt="images description">' +
                        '</div>' +
                        '<div class="txt">' +
                        '<h1>' + response[x].rest_name_he + '</h1>' +
                        '<div class="order received"><i class="fa fa-check-circle" aria-hidden="true"></i> <p>הזמנה התקבלה</p></div>' +
                        '<p>הזמנה מתאריך <em class="f black">' + response[x].date + '</em><br> סך הכל לתשלום <em class="f black">' + response[x]['actual_total']+ ' ש"ח '+'</em></p> ' +
                        '</div>' +
                        '</li>' +
                        '<li class="last add">' +
                        '<div class="btn-box"><button class="bt_ordernow" onclick="requestReOrder(' + x + ')" data-toggle="modal" type="button">הזמן שוב</button></div>' +
                        '<div class="text add">';


                    if (response[x].order_detail.length <= 2) {


                        for (var y = 0; y < response[x].order_detail.length; y++) {

                            if(response[x].order_detail[y].item_he == null)
                            {
                                str += '<p><em class="f black">' + response[x].order_detail[y].item + '</em> ' + response[x].order_detail[y].sub_items + ' </p>';
                            }
                            else {

                                str += '<p><em class="f black">' + response[x].order_detail[y].item_he + '</em> ' + response[x].order_detail[y].sub_items_he + ' </p>';
                            }

                        }

                    }
                    else {

                        for (var y = 0; y < 2; y++) {


                            if(response[x].order_detail[y].item_he == null)
                            {
                                str += '<p><em class="f black">' + response[x].order_detail[y].item + '</em> ' + response[x].order_detail[y].sub_items + ' </p>';
                            }
                            else {

                                str += '<p><em class="f black">' + response[x].order_detail[y].item_he + '</em> ' + response[x].order_detail[y].sub_items_he + ' </p>';
                            }

                        }

                    }

                    if (response[x].order_detail.length > 2) {

                        str += '<div id="more-info-' + x + '" style="display: none">';

                        for (var y = 2; y < response[x].order_detail.length; y++) {


                            if(response[x].order_detail[y].item_he == null)
                            {
                                str += '<p><em class="f black">' + response[x].order_detail[y].item + '</em> ' + response[x].order_detail[y].sub_items + ' </p>';
                            }
                            else {

                                str += '<p><em class="f black">' + response[x].order_detail[y].item_he + '</em> ' + response[x].order_detail[y].sub_items_he + ' </p>';
                            }

                        }

                        str += '</div>';
                        str += '<a class="more-info" id="more-info-btn' + x + '" onclick="hideShowMoreInfo(' + x + ')" href="#">מידע נוסף</a>';
                    }


                    str += '</div>' +
                        '</li>' +
                        '</ul>' +
                        '</li>';

                }

                // CANCELED ORDERS

                else {




                    if ($("#cb_cancelled_past_orders").is(':checked')) {

                        cancelled_orders_count++;
                        emptylist = false;

                        str += '<li>' +
                            '<ul>' +
                            '<li class="new-first">' +
                            '<div class="img-circle">' +
                            '<img src="' + response[x].logo + '" alt="images description">' +
                            '</div>' +
                            '<div class="txt">' +
                            '<h1>' + response[x].rest_name_he + '</h1>' +
                            '<div class="order canceled"><i class="fa fa-times-circle" aria-hidden="true"></i> <p>הזמנה התקבלה</p></div>' +
                            '<p>הזמנה מתאריך <em class="f black">' + response[x].date + '</em><br> סך הכל לתשלום <em class="f black">' + response[x]['actual_total'] + ' ש"ח</em></p>' +
                            '</div>' +
                            '</li>' +
                            '<li class="last add">' +
                            '<div class="btn-box"><button class="bt_ordernow" onclick="requestReOrder(' + x + ')"  data-toggle="modal" type="button">הזמן שוב</button></div>' +
                            '<div class="text add">';


                        if (response[x].order_detail.length <= 2) {

                            for (var y = 0; y < response[x].order_detail.length; y++) {

                                if(response[x].order_detail[y].item_he == null)
                                {
                                    str += '<p><em class="f black">' + response[x].order_detail[y].item + '</em> ' + response[x].order_detail[y].sub_items + ' </p>';
                                }
                                else {

                                    str += '<p><em class="f black">' + response[x].order_detail[y].item_he + '</em> ' + response[x].order_detail[y].sub_items_he + ' </p>';
                                }

                            }
                        }
                        else {

                            for (var y = 0; y < 2; y++) {

                                if(response[x].order_detail[y].item_he == null)
                                {
                                    str += '<p><em class="f black">' + response[x].order_detail[y].item + '</em> ' + response[x].order_detail[y].sub_items + ' </p>';
                                }
                                else {

                                    str += '<p><em class="f black">' + response[x].order_detail[y].item_he + '</em> ' + response[x].order_detail[y].sub_items_he + ' </p>';
                                }


                            }

                        }

                        if (response[x].order_detail.length > 2) {

                            str += '<div id="more-info-' + x + '" style="display: none">';

                            for (var y = 2; y < response[x].order_detail.length; y++) {

                                if(response[x].order_detail[y].item_he == null)
                                {
                                    str += '<p><em class="f black">' + response[x].order_detail[y].item + '</em> ' + response[x].order_detail[y].sub_items + ' </p>';
                                }
                                else {

                                    str += '<p><em class="f black">' + response[x].order_detail[y].item_he + '</em> ' + response[x].order_detail[y].sub_items_he + ' </p>';
                                }


                            }

                            str += '</div>';
                            str += '<a class="more-info" id="more-info-btn' + x + '" onclick="hideShowMoreInfo(' + x + ')" href="#">מידע נוסף</a>';
                        }


                        str += '</div>' +
                            '</li>' +
                            '</ul>' +
                            '</li>';
                    }

                }
            }



            // IF COMPANY ORDERING CLOSED  DO NOT ALLOW REORDERING

            else {

                if (response[x].order_status == 'delivered') {

                    emptylist = false;

                    str += '<li class="offline">' +
                        '<ul>' +
                        '<li class="new-first">' +
                        '<div class="img-circle">' +
                        '<img src="' + response[x].logo + '" alt="images description">' +
                        '</div>' +
                        '<div class="txt">' +
                        '<h1>' + response[x].rest_name_he + '</h1>' +
                        '<div class="order received"><i class="fa fa-check-circle" aria-hidden="true"></i> <p>הזמנה התקבלה</p></div>' +
                        '<p>הזמנה מתאריך <em class="f black">' + response[x].date + '</em><br> סך הכל לתשלום <em class="f black">' + response[x]['actual_total'] + ' ש"ח</em></p>' +
                        '</div>' +
                        '</li>' +
                        '<li class="last add">' +
                        '<div class="btn-box"><button class="bt_ordernow" data-toggle="modal" data-target="#business-popup" type="button">הזמן שוב</button></div>' +
                        '<div class="text add">';


                    if (response[x].order_detail.length <= 2) {

                        for (var y = 0; y < response[x].order_detail.length; y++) {

                            if(response[x].order_detail[y].item_he == null)
                            {
                                str += '<p><em class="f black">' + response[x].order_detail[y].item + '</em> ' + response[x].order_detail[y].sub_items + ' </p>';
                            }
                            else {

                                str += '<p><em class="f black">' + response[x].order_detail[y].item_he + '</em> ' + response[x].order_detail[y].sub_items_he + ' </p>';
                            }


                        }
                    }
                    else {

                        for (var y = 0; y < 2; y++) {

                            if(response[x].order_detail[y].item_he == null)
                            {
                                str += '<p><em class="f black">' + response[x].order_detail[y].item + '</em> ' + response[x].order_detail[y].sub_items + ' </p>';
                            }
                            else {

                                str += '<p><em class="f black">' + response[x].order_detail[y].item_he + '</em> ' + response[x].order_detail[y].sub_items_he + ' </p>';
                            }


                        }

                    }

                    if (response[x].order_detail.length > 2) {

                        str += '<div id="more-info-' + x + '" style="display: none">';

                        for (var y = 2; y < response[x].order_detail.length; y++) {

                            if(response[x].order_detail[y].item_he == null)
                            {
                                str += '<p><em class="f black">' + response[x].order_detail[y].item + '</em> ' + response[x].order_detail[y].sub_items + ' </p>';
                            }
                            else {

                                str += '<p><em class="f black">' + response[x].order_detail[y].item_he + '</em> ' + response[x].order_detail[y].sub_items_he + ' </p>';
                            }


                        }

                        str += '</div>';
                        str += '<a class="more-info" id="more-info-btn' + x + '" onclick="hideShowMoreInfo(' + x + ')" href="#">מידע נוסף</a>';
                    }


                    str += '</div>' +
                        '</li>' +
                        '</ul>' +
                        '</li>';

                }

                // CANCELED ORDERS

                else {



                    if ($("#cb_cancelled_past_orders").is(':checked')) {

                        cancelled_orders_count++;
                        emptylist = false;

                        str += '<li class="offline">' +
                            '<ul>' +
                            '<li class="new-first">' +
                            '<div class="img-circle">' +
                            '<img src="' + response[x].logo + '" alt="images description">' +
                            '</div>' +
                            '<div class="txt">' +
                            '<h1>' + response[x].rest_name_he + '</h1>' +
                            '<div class="order canceled"><i class="fa fa-times-circle" aria-hidden="true"></i> <p>הזמנה התקבלה</p></div>' +
                            '<p>הזמנה מתאריך <em class="f black">' + response[x].date + '</em><br> סך הכל לתשלום <em class="f black">' + response[x]['actual_total'] + ' ש"ח</em></p>' +
                            '</div>' +
                            '</li>' +
                            '<li class="last add">' +
                            '<div class="btn-box"><button class="bt_ordernow" data-toggle="modal" data-target="#business-popup" type="button">הזמן שוב</button></div>' +
                            '<div class="text add">';


                        if (response[x].order_detail.length <= 2) {

                            for (var y = 0; y < response[x].order_detail.length; y++) {

                                if(response[x].order_detail[y].item_he == null)
                                {
                                    str += '<p><em class="f black">' + response[x].order_detail[y].item + '</em> ' + response[x].order_detail[y].sub_items + ' </p>';
                                }
                                else {

                                    str += '<p><em class="f black">' + response[x].order_detail[y].item_he + '</em> ' + response[x].order_detail[y].sub_items_he + ' </p>';
                                }


                            }
                        }
                        else {

                            for (var y = 0; y < 2; y++) {

                                if(response[x].order_detail[y].item_he == null)
                                {
                                    str += '<p><em class="f black">' + response[x].order_detail[y].item + '</em> ' + response[x].order_detail[y].sub_items + ' </p>';
                                }
                                else {

                                    str += '<p><em class="f black">' + response[x].order_detail[y].item_he + '</em> ' + response[x].order_detail[y].sub_items_he + ' </p>';
                                }


                            }

                        }

                        if (response[x].order_detail.length > 2) {

                            str += '<div id="more-info-' + x + '" style="display: none">';

                            for (var y = 2; y < response[x].order_detail.length; y++) {

                                if(response[x].order_detail[y].item_he == null)
                                {
                                    str += '<p><em class="f black">' + response[x].order_detail[y].item + '</em> ' + response[x].order_detail[y].sub_items + ' </p>';
                                }
                                else {

                                    str += '<p><em class="f black">' + response[x].order_detail[y].item_he + '</em> ' + response[x].order_detail[y].sub_items_he + ' </p>';
                                }


                            }

                            str += '</div>';
                            str += '<a class="more-info" id="more-info-btn' + x + '" onclick="hideShowMoreInfo(' + x + ')" href="#">מידע נוסף</a>';
                        }


                        str += '</div>' +
                            '</li>' +
                            '</ul>' +
                            '</li>';

                    }
                }

            }
        }


        str += '<li class="last-row"></li>';

        if(emptylist == true)
        {
            $('#past-orders').hide();
            $('#empty-past-orders').show();

        }
        else {

            $('#past-orders').html(str);
            $('#past-orders').show();
            $('#empty-past-orders').hide();

        }





        $('#cancelled_order_text').html("הצג הזמנות שביטלו ["+cancelled_orders_count+"]");
        past_orders_object = response;

    }





    catch (exp)
    {

        errorHandlerServerResponse(url,"parsing error call back");

    }


}

function hideShowMoreInfo(index) {

    var id = '#more-info-'+index;
    var btnId = '#more-info-btn'+index;

    $(id).toggle();

    if($(id).is(':hidden'))
    {
        $(btnId).html('עוד מידע');
    }
    else {

        $(btnId).html('פחות מידע');
    }
}




function requestReOrder(index) {


    commonAjaxCall("/restapi/index.php/get_reorder", {"order_id": past_orders_object[index].id}, responseReOrderObject);

}


function responseReOrderObject(url,response) {

    try{


        dataObject = JSON.parse(response);

        localStorage.setItem("data_object_he", JSON.stringify(dataObject));


        // REST VALUES
        dataObject.payment_option = "CASH";
        dataObject.selectedCardId = null;
        dataObject.transactionId = "";
        dataObject.company_contribution = 0;
        dataObject.total_paid = dataObject.actual_total;


        var company_name     =   dataObject.company.company_name;
        company_name         =   company_name.replace(/\s/g, '');

        var restaurant_name  =    dataObject.rests_orders[0].selectedRestaurant.name_he;
        restaurant_name      =    restaurant_name.replace(/\s/g, '');

        window.location.href = '/he/'+company_name+"/"+restaurant_name+'/order';


    }

    catch (exp)
    {

        errorHandlerServerResponse(url,"parsing error call back");

    }

}



// DISPLAY PENDING ORDERS ON THE WAY

function displayPendingOrdersRequest() {



    if (pending_orders_object == null)
    {

        commonAjaxCall("/restapi/index.php/get_all_pending_orders", {"user_id": dataObject.user.user_id}, responsePendingOrders);

    }

}

function responsePendingOrders(url, response) {

    try {


        var str = '<h2 class="light"></h2>';
        var emptyList = true;

        for(var x=0;x<response.length;x++) {


            emptyList = false;
            str +=

                '<div class="view-order">'+
                '<ul class="rest-list" >'+
                '<li>'+
                '<ul>'+
                '<li>'+
                '<div class="txt-box">'+
                '<h2 class="light">הזמנה בדרך</h2>'+
                '<p>סטטוס הזמנה</p>'+
                '</div>'+
                '</li>'+
                '<li>'+
                '<div class="txt-box">'+
                '<h2 class="light">'+response[x].rest.name_he+'</h2>'+
                '<p>'+response[x].rest.address_he+'</p>'+
                '<div class="tooltip-popup popup"><p class="f black"> '+response[x].rest.address_he+'</br><strong>'+response[x].rest.city_name_he+'</strong></p></div>'+
                '</div>'+
                '</li>'+
                '<li>'+
                '<div class="txt-box">'+
                '<h2 class="light">'+delivery_time_str+'</h2>'+
                '<p>זמן קבלת המשלוח</p>'+
                '</div>'+
                '</li>'+
                '<li>'+
                '<div class="txt-box">'+
                '<h2 class="light">סה"כ '+response[x].total+' ש"ח</h2>'+
                '<p id="pending-order-detail'+x+'" class="pending-order-detail"><span class="arrow"> פרוט הזמנה <i class="fa fa-angle-down" aria-hidden="true"></i></span></p>'+

                '</div>'+
                '</li>'+
                '</ul>'+
                '</li>'+
                '</ul>'+



                '<div class="footer-box">';

                if (company_open_status) {


                str +=  '<button class="f white btn-order" onclick="addOrder(' + x + ')" type="button"><img src="/he/images/plus.png"> הוסף הזמנה</button>';

                }
                else {

                    str +=  '<button class="f white btn-order"  data-toggle="modal" data-target="#business-popup" type="button"><img src="/he/images/plus.png"> הוסף הזמנה</button>';

                }


                str += '<a class="btn-link" href="#">'+
                '<p  id="cancel-order-open-'+x+'" class="cancel-order-open">ביטול הזמנה</p>'+
                '</a>'+
                '</div>'+
                '</div>';


        }

        str +=  '<div class="last-row"></div>';


        pending_orders_object = response;


        if(emptyList == false)
        {
            $('#pending-orders').html(str);
            $('#pending-orders').show();
            $('#empty-pending-orders').hide();
            $('#change-status-ow').addClass('active');
        }
        else {

            $('#pending-orders').hide();
            $('#empty-pending-orders').show();
            $('#change-status-ow').removeClass('active');
        }

    }
    catch (exp)
    {

        errorHandlerServerResponse(url,"parsing error call back");

    }

}


function onOrderNowClicked(index)
{

    // CREATING ORDER OBJECT

    var orderObject  = {

        'selectedRestaurant' : listOfRestaurants[index],
        'order_detail' : null,
        'foodCartData' : null

    };


    // PUShiNG SELECTED RESTAURANT ORDER OBJECT

    // TEMPORARY REMOVE PREVIOUS ORDER

    dataObject.rests_orders = [];


    dataObject.rests_orders.push(orderObject);


    localStorage.setItem("data_object_he", JSON.stringify(dataObject));


    var company_name     =   dataObject.company.company_name;
    company_name         =   company_name.replace(/\s/g, '');

    var restaurant_name  =    listOfRestaurants[index].name_en;
    restaurant_name      =    restaurant_name.replace(/\s/g, '');

    window.location.href = '/he/'+company_name+"/"+restaurant_name+'/order';



}


function requestCancelOrder()
{
    var current_index = parseInt($(".cancle-order").attr('current-index'));

    var order_id = pending_orders_object[current_index].id;

    commonAjaxCall("/restapi/index.php/cancel_order", {"order_id": order_id}, cancelOrderResponseRequest);

}


function cancelOrderResponseRequest(url,response) {

    try {

        if(response != "false")
        {
            dataObject.user.userDiscountFromCompany = response;

            $("#name_company").html(dataObject.user.name+", "+dataObject.company.company_name+" <em> "+dataObject.user.userDiscountFromCompany+'ש"ח'+"</em>");

            $('.cancle-order').hide();
            past_orders_object = null;
            pending_orders_object = null;
            displayPendingOrdersRequest();

        }
        else {

            $('#message-cancel').css("color","red");

        }

    }
    catch (exp)
    {

        errorHandlerServerResponse(url,"parsing error call back");

    }

}
function addOrder(index) {


    for(var x=0;x<listOfRestaurants.length;x++)
    {

        if(listOfRestaurants[x].id == pending_orders_object[index].rest.id)
        {

            onOrderNowClicked(x);
            break;
        }

    }

}

