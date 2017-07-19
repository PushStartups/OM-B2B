var rawResponseALlRests   = null;  // LOCAL VARIABLE


var dataObject;                  // DATA OBJECT CONTAIN INFORMATION ABOUT COMPANY, USER & USER ORDER



var listOfRestaurants = null;    // LIST OF ALL RESTAURANTS RECEIVED FROM SERVER
                                 //array restaurants -> {id,city_en,city_he,name_en,name_he,min_amount,tags,logo,description_en,description_he,
                                 // address_en,address_he,hechsher_en,hechsher_he,gallery,rest_lat,rest_lng,timings,today_timings,percentage_discount,}}



var company_open_status;         // BUSINESS ORDERING IS OPEN OR CLOSED NOW




var delivery_time_str = "";      // DELIVERY TIME STRING i.e 12:30 - 1:30



var appox_delivey_time = "";      // DELIVERY TIME TILL ORDER RECEIVED  i.e 1:30




var past_orders_object = null;     // USER'S PAST ORDERS OBJECT




var pending_orders_object = null;  // USER'S PENDING ORDERS OBJECT



var db_tags  = null;               // CUISINE TAGS OF RESTAURNATS i.e MEAT, BURGER, HEALTH



var db_kashrut = null;             // kashrut OF RESTAURANTS i.e Mehardin


// AFTER DOCUMENTED LOADED
$(document).ready(function() {


    addLoading();


    dataObject = JSON.parse(localStorage.getItem("data_object_en"));

    $("#name_company").html(dataObject.user.name+", "+dataObject.company.company_name+" <em> "+dataObject.user.userDiscountFromCompany+" NIS</em>");


    commonAjaxCall("/restapi/index.php/get_db_tags_and_kashrut",{"company_id":dataObject.company.company_id}, responseDBAllTagsKashrut);

});

function responseDBAllTagsKashrut(url, response) {


    addLoading();

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
                    '<div class="control__indicator"></div><span id="cb-tags-title' + x + '">' + db_tags[x].name_en + ' [' + db_tags[x].count + ']' + '</span>' +
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
                    '<div class="control__indicator"></div><span id="cb-kashrut-title' + x + '">' + db_kashrut[x].name_en + ' [' + db_kashrut[x].count + ']' + '</span>' +
                    '</label>' +
                    '</li>';

            }
        }


        $('#kashruts').html(str);

        $('.list-item').show();

        // REQUEST ALL RESTAURANTS FROM SERVER
        commonAjaxCall("/restapi/index.php/get_all_restaurants", {"company_id":dataObject.company.company_id}, responseListOfRestaurants);

    }
    catch (exp)
    {

        errorHandlerServerResponse(url,"parsing error call back");
        hideLoading();

    }
}


function responseListOfRestaurants(url,response) {

    addLoading();

    try {

        rawResponseALlRests = response;
        listOfRestaurants   = response.restaurants;
        company_open_status = response.company_open_status;
        delivery_time_str   = response.delivery_time_str;
        appox_delivey_time  = response.appox_delivey_time;


        dataObject.company.delivery_time = delivery_time_str;

        var str = '';


        for(var x=0;x<listOfRestaurants.length;x++)
        {

            var kashrutString  =  fromKashrutToString(listOfRestaurants[x]);


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

                        var tag = listOfRestaurants[x].tags[z]['name_en'];

                        if ((tag.toLowerCase()).includes(db_tags[y].name_en.toLowerCase())) {

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

                        var kasrut = listOfRestaurants[x].kashrut[z]['name_en'];

                        if ((kasrut.toLowerCase()).includes(db_kashrut[y].name_en.toLowerCase())) {

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
                        '<h1 class="light">' + listOfRestaurants[x].name_en + '</h1>' +
                        '<p><em class="f black">Kashrut</em> ' + kashrutString + ' </p>' +
                        '</div>' +
                        '</li>' +
                        '<li>' +
                        '<div class="text">' +
                        '<p><em class="f black">' + listOfRestaurants[x].percentage_discount + ' % off</em> Between the hours ' + listOfRestaurants[x].today_timings + ' </p>' +
                        '</div>' +
                        '</li>' +
                        '<li class="third">' +
                        '<address class="address">' +
                        '<img class="edit edit1" src="/en/images/ic_checkin.png">' +
                        '<p class="new-adress" >' + listOfRestaurants[x].address_en + '</p>' +
                        '<div class="tooltip-popup popup"><p class="f black">' + listOfRestaurants[x].address_en + '</br><strong>' + listOfRestaurants[x].city_en + '</strong></p></div>' +
                        '</address>' +
                        '</li>' +
                        '<li>' +
                        '<div class="btn-box"><button  class="bt_ordernow" type="button" onclick="onOrderNowClicked('+x+')">order<br>now</button></div>' +

                        '<time class="time">' +

                        '<div  id="rl-dt-ma-' + x + '"  style="cursor: pointer" class="rl-dt-ma-popup">' +
                        '<img class="bike" src="/en/images/motorbike-delivery.png">' +
                        '<p id="show-popup">Next Delivery ' + appox_delivey_time + '</p>' +
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
                        '<h1 class="light">' + listOfRestaurants[x].name_en + '</h1>' +
                        '<p><em class="f black">Kashrut</em> ' + kashrutString + ' </p>' +
                        '</div>' +
                        '</li>' +
                        '<li>' +
                        '<div class="text">' +
                        '<p><em class="f black">' + listOfRestaurants[x].percentage_discount + ' % off</em> Between the hours ' + listOfRestaurants[x].today_timings + ' </p>' +
                        '</div>' +
                        '</li>' +
                        '<li class="third">' +
                        '<address class="address">' +
                        '<img class="edit edit1" src="/en/images/ic_checkin.png">' +
                        '<p class="new-adress" >' + listOfRestaurants[x].address_en + '</p>' +
                        '<div class="tooltip-popup popup"><p class="f black">' + listOfRestaurants[x].address_en + '</br><strong>' + listOfRestaurants[x].city_en + '</strong></p></div>' +
                        '</address>' +
                        '</li>' +
                        '<li>' +
                        '<div class="btn-box"><button class="bt_ordernow" data-toggle="modal" data-target="#business-popup" type="button" onclick="">Closed</button></div>' +
                        '<time class="time">' +
                        '<img class="bike" src="/en/images/motorbike-delivery.png">' +
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


        hideLoading();


    }
    catch (exp)
    {

        errorHandlerServerResponse(url,"parsing error call back");
        hideLoading();

    }




}




// CONVERT ALL RESTAUTANT TAGS TO STRING
function fromKashrutToString (restaurant)
{
    var kashrut = "";

    for (var i=0 ; i < restaurant.kashrut.length ; i++)
    {

        if ( i == 0)
            kashrut += restaurant.kashrut[i]['name_en'];

        else
            kashrut += ", "+restaurant.kashrut[i]['name_en'] ;


    }

    return kashrut;
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

            $('#last_week_orders_text').html('Last Week  [' + response.length + ']');

        }


        var cancelled_orders_count = 0;

        for(var x=0;x<response.length;x++)
        {


            // IF COMPANY ORDERING OPEN ALLOW REORDERING
            if(company_open_status) {

                // DELIVERED ORDERS

                if (response[x].order_status == 'delivered') {
                    str += '<li>' +
                        '<ul>' +
                        '<li class="new-first">' +
                        '<div class="img-circle">' +
                        '<img src="/en/images/logo-img.png" alt="images description">' +
                        '</div>' +
                        '<div class="txt">' +
                        '<h1>' + response[x].rest_name + '</h1>' +
                        '<div class="order received"><i class="fa fa-check-circle" aria-hidden="true"></i> <p>Order received</p></div>' +
                        '<p>Order from date <em class="f black">'+response[x].date+'</em><br> in the amount of <em class="f black">' + response[x]['actual_total'] + ' NIS</em></p>' +
                        '</div>' +
                        '</li>' +
                        '<li class="last add">' +
                        '<div class="btn-box"><button class="bt_ordernow" data-toggle="modal" data-target="#business-popup" type="button">Reorder</button></div>' +
                        '<div class="text add">';


                    if (response[x].order_detail.length <= 2) {

                        for (var y = 0; y < response[x].order_detail.length; y++) {

                            str += '<p><em class="f black">' + response[x].order_detail[y].item + '</em> ' + response[x].order_detail[y].sub_items + ' </p>';

                        }
                    }
                    else {

                        for (var y = 0; y < 2; y++) {

                            str += '<p><em class="f black">' + response[x].order_detail[y].item + '</em> ' + response[x].order_detail[y].sub_items + ' </p>';

                        }

                    }

                    if (response[x].order_detail.length > 2) {

                        str += '<div id="more-info-' + x + '" style="display: none">';

                        for (var y = 2; y < response[x].order_detail.length; y++) {

                            str += '<p><em class="f black">' + response[x].order_detail[y].item + '</em> ' + response[x].order_detail[y].sub_items + ' </p>';

                        }

                        str += '</div>';
                        str += '<a class="more-info" id="more-info-btn' + x + '" onclick="hideShowMoreInfo(' + x + ')" href="#">more info</a>';
                    }


                    str += '</div>' +
                        '</li>' +
                        '</ul>' +
                        '</li>';

                }

                // CANCELED ORDERS

                else {

                    cancelled_orders_count++;

                    if($("#cb_cancelled_past_orders").is(':checked')) {


                        str += '<li>' +
                            '<ul>' +
                            '<li class="new-first">' +
                            '<div class="img-circle">' +
                            '<img src="/en/images/logo-img.png" alt="images description">' +
                            '</div>' +
                            '<div class="txt">' +
                            '<h1>' + response[x].rest_name + '</h1>' +
                            '<div class="order canceled"><i class="fa fa-times-circle" aria-hidden="true"></i> <p>Cancelled</p></div>' +
                            '<p>Order from date <em class="f black">' + response[x].date + '</em><br> in the amount of <em class="f black">' + response[x]['actual_total'] + ' NIS</em></p>' +
                            '</div>' +
                            '</li>' +
                            '<li class="last add">' +
                            '<div class="btn-box"><button class="bt_ordernow" data-toggle="modal" data-target="#business-popup" type="button">Reorder</button></div>' +
                            '<div class="text add">';


                        if (response[x].order_detail.length <= 2) {

                            for (var y = 0; y < response[x].order_detail.length; y++) {

                                str += '<p><em class="f black">' + response[x].order_detail[y].item + '</em> ' + response[x].order_detail[y].sub_items + ' </p>';

                            }
                        }
                        else {

                            for (var y = 0; y < 2; y++) {

                                str += '<p><em class="f black">' + response[x].order_detail[y].item + '</em> ' + response[x].order_detail[y].sub_items + ' </p>';

                            }

                        }

                        if (response[x].order_detail.length > 2) {

                            str += '<div id="more-info-' + x + '" style="display: none">';

                            for (var y = 2; y < response[x].order_detail.length; y++) {

                                str += '<p><em class="f black">' + response[x].order_detail[y].item + '</em> ' + response[x].order_detail[y].sub_items + ' </p>';

                            }

                            str += '</div>';
                            str += '<a class="more-info" id="more-info-btn' + x + '" onclick="hideShowMoreInfo(' + x + ')" href="#">more info</a>';
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
                    str += '<li class="offline">' +
                        '<ul>' +
                        '<li class="new-first">' +
                        '<div class="img-circle">' +
                        '<img src="/en/images/logo-img.png" alt="images description">' +
                        '</div>' +
                        '<div class="txt">' +
                        '<h1>' + response[x].rest_name + '</h1>' +
                        '<div class="order received"><i class="fa fa-check-circle" aria-hidden="true"></i> <p>Order received</p></div>' +
                        '<p>Order from date <em class="f black">'+response[x].date+'</em><br> in the amount of <em class="f black">' + response[x]['actual_total'] + ' NIS</em></p>' +
                        '</div>' +
                        '</li>' +
                        '<li class="last add">' +
                        '<div class="btn-box"><button class="bt_ordernow" data-toggle="modal" data-target="#business-popup" type="button">Reorder</button></div>' +
                        '<div class="text add">';


                    if (response[x].order_detail.length <= 2) {

                        for (var y = 0; y < response[x].order_detail.length; y++) {

                            str += '<p><em class="f black">' + response[x].order_detail[y].item + '</em> ' + response[x].order_detail[y].sub_items + ' </p>';

                        }
                    }
                    else {

                        for (var y = 0; y < 2; y++) {

                            str += '<p><em class="f black">' + response[x].order_detail[y].item + '</em> ' + response[x].order_detail[y].sub_items + ' </p>';

                        }

                    }

                    if (response[x].order_detail.length > 2) {

                        str += '<div id="more-info-' + x + '" style="display: none">';

                        for (var y = 2; y < response[x].order_detail.length; y++) {

                            str += '<p><em class="f black">' + response[x].order_detail[y].item + '</em> ' + response[x].order_detail[y].sub_items + ' </p>';

                        }

                        str += '</div>';
                        str += '<a class="more-info" id="more-info-btn' + x + '" onclick="hideShowMoreInfo(' + x + ')" href="#">more info</a>';
                    }


                    str += '</div>' +
                        '</li>' +
                        '</ul>' +
                        '</li>';

                }

                // CANCELED ORDERS

                else {

                    cancelled_orders_count++;

                    if($("#cb_cancelled_past_orders").is(':checked')) {



                        str += '<li class="offline">' +
                            '<ul>' +
                            '<li class="new-first">' +
                            '<div class="img-circle">' +
                            '<img src="/en/images/logo-img.png" alt="images description">' +
                            '</div>' +
                            '<div class="txt">' +
                            '<h1>' + response[x].rest_name + '</h1>' +
                            '<div class="order canceled"><i class="fa fa-times-circle" aria-hidden="true"></i> <p>Cancelled</p></div>' +
                            '<p>Order from date <em class="f black">' + response[x].date + '</em><br> in the amount of <em class="f black">' + response[x]['actual_total'] + ' NIS</em></p>' +
                            '</div>' +
                            '</li>' +
                            '<li class="last add">' +
                            '<div class="btn-box"><button class="bt_ordernow" data-toggle="modal" data-target="#business-popup" type="button">Reorder</button></div>' +
                            '<div class="text add">';


                        if (response[x].order_detail.length <= 2) {

                            for (var y = 0; y < response[x].order_detail.length; y++) {

                                str += '<p><em class="f black">' + response[x].order_detail[y].item + '</em> ' + response[x].order_detail[y].sub_items + ' </p>';

                            }
                        }
                        else {

                            for (var y = 0; y < 2; y++) {

                                str += '<p><em class="f black">' + response[x].order_detail[y].item + '</em> ' + response[x].order_detail[y].sub_items + ' </p>';

                            }

                        }

                        if (response[x].order_detail.length > 2) {

                            str += '<div id="more-info-' + x + '" style="display: none">';

                            for (var y = 2; y < response[x].order_detail.length; y++) {

                                str += '<p><em class="f black">' + response[x].order_detail[y].item + '</em> ' + response[x].order_detail[y].sub_items + ' </p>';

                            }

                            str += '</div>';
                            str += '<a class="more-info" id="more-info-btn' + x + '" onclick="hideShowMoreInfo(' + x + ')" href="#">more info</a>';
                        }


                        str += '</div>' +
                            '</li>' +
                            '</ul>' +
                            '</li>';

                    }
                }

            }
        }

        $('#cancelled_order_text').html("Show Canceled orders ["+cancelled_orders_count+"]");

        $('#past-orders').html(str);
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
        $(btnId).html('more info');
    }
    else {

        $(btnId).html('less info');
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

        var str = '<h2 class="light">19/06/17</h2>';


        for(var x=0;x<response.length;x++) {


            str +=

                '<div class="view-order">'+
                '<ul class="rest-list" >'+
                '<li>'+
                '<ul>'+
                '<li>'+
                '<div class="txt-box">'+
                '<h2 class="light">On the way</h2>'+
                '<p>Order Status</p>'+
                '</div>'+
                '</li>'+
                '<li>'+
                '<div class="txt-box">'+
                '<h2 class="light">'+response[x].rest.name_en+'</h2>'+
                '<p>'+response[x].rest.address_en+'</p>'+
                '<div class="tooltip-popup popup"><p class="f black"> '+response[x].rest.address_en+'</br><strong>'+response[x].rest.city_name+'</strong></p></div>'+
                '</div>'+
                '</li>'+
                '<li>'+
                '<div class="txt-box">'+
                '<h2 class="light">'+delivery_time_str+'</h2>'+
                '<p>Delivery time</p>'+
                '</div>'+
                '</li>'+
                '<li>'+
                '<div class="txt-box">'+
                '<h2 class="light">Total '+response[x].total+' NIS</h2>'+
                '<p id="pending-order-detail'+x+'" class="pending-order-detail"><span class="arrow">See the menu order <i class="fa fa-angle-down" aria-hidden="true"></i></span></p>'+

                '</div>'+
                '</li>'+
                '</ul>'+
                '</li>'+
                '</ul>'+



                '<div class="footer-box">'+
                '<button class="f white btn-order" type="button"><img src="/en/images/plus.png"> Add an Order</button>'+
                '<a class="btn-link" href="#">'+
                '<p  id="cancel-order-open-'+x+'" class="cancel-order-open">Cancel Order</p>'+
                '</a>'+
                '</div>'+
                '</div>';


        }

        str +=  '<div class="last-row"></div>';


        pending_orders_object = response;
        $('#pending-orders').html(str);

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


    // PUSHING SELECTED RESTAURANT ORDER OBJECT

    // TEMPORARY REMOVE PREVIOUS ORDER

    dataObject.rests_orders = [];


    dataObject.rests_orders.push(orderObject);


    localStorage.setItem("data_object_en", JSON.stringify(dataObject));


    var company_name     =   dataObject.company.company_name;
    company_name         =   company_name.replace(/\s/g, '');

    var restaurant_name  =    listOfRestaurants[index].name_en;
    restaurant_name      =    restaurant_name.replace(/\s/g, '');

    window.location.href = '/en/'+company_name+"/"+restaurant_name+'/order';



}

