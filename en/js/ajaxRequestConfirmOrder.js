var dataObject = null;   // DATA OBJECT CONTAIN INFORMATION ABOUT COMPANY, USER & USER ORDER

var selectedRestIndex  = 0;


$(document).ready(function() {


    addLoading();


    dataObject = JSON.parse(localStorage.getItem("data_object_en"));


    // INITIALIZING ORDER DETAIL POPUP

    if(dataObject.rests_orders[selectedRestIndex].order_detail == null)
    {
        dataObject.rests_orders[selectedRestIndex].order_detail = [];
    }


    $("#rest-title").html(dataObject.rests_orders[selectedRestIndex].selectedRestaurant.name_en);


    $("#rest-address").html(dataObject.rests_orders[selectedRestIndex].selectedRestaurant.address_en);


    $("#name_company").html(dataObject.user.name+", "+dataObject.company.company_name+" <em> "+dataObject.user.userDiscountFromCompany+" NIS</em>");


    $("#contact").html(dataObject.rests_orders[selectedRestIndex].selectedRestaurant.contact);


    commonAjaxCall("/restapi/index.php/get_all_cards_info", {"user_email": dataObject.user.email}, user_cards_callback);

});



function user_cards_callback(url,response)
{


    try {
        var resp = response;

        user_cards = resp;

        //
        // if (resp == 'null') {
        //
        //
        //     // NO CARDS FOUND AGAINST USER
        //
        //     $('#card_list_parent').hide();
        //     $('#cc_payment-proceed').hide();
        //
        //
        // }
        // else {
        //
        //     // DISPLAY ALL AVAILABLE CARDS
        //     $('#cc_payment-proceed').show();
        //     $('#card_list_parent').show();
        //
        //
        //     str = "";
        //
        //     for (var x = 0; x < resp.length; x++) {
        //         str += '<a onclick="cardSelected(' + x + ')" class="dropdown-item" href="#">' + resp[x].card_mask + '</a>';
        //     }
        //
        //
        //     $("#cards_list").html(str);
        //
        //
        // }


    }
    catch (exp)
    {


        errorHandlerServerResponse(url,"parsing error call back");
        hideLoading();


    }

}
