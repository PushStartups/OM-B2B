var dataObject = null;   // DATA OBJECT CONTAIN INFORMATION ABOUT COMPANY, USER & USER ORDER


var keepLoaderUntilPageLoad   =  true;


// DATA OBJECT rest_orders array containing following Order Object

// dataObject.rests_orders

// orderObject  = {
//
//     selectedRestaurant : listOfRestaurants[index],
//     order_detail : null
//
// };



var allCategoriesWithItemsResp = null; // RESPONSE ALL CATEGORIES WITH ITEMS FROM SERVER


var selectedRestIndex = 0; // SELECTED RESTAURANT INDEX


var percentage_discount = 0; // PERCENTAGE DISCOUNT ON RESTAURANT BY COMPANY


var extras = null; // EXTRAS AND SUB ITEMS FOR SELECTED ITEM  (SERVER RESPONSE)


var oneTypeSubItems = null;  // SUB-ITEMS TYPE ONE


var multipleTypeSubItems   = null;   // SUB-ITEMS TYPE MULTIPLE


var currentCategoryId  = null;  // CURRENT SELECTED CATEGORY


var currentItemIndex   = null;  // CURRENT ITEM SELECTED


var selectedItemPriceOrg  = 0;  // SELECTED ITEM PRICE BEFORE CALCULATION (ORIGINAL)


var selectedItemPrice = 0;  // SELECTED ITEM PRICE AFTER CALCULATION


var foodCartData  = null; // FOOD CART OBJECT


var tempDiscountFromCompanyCal = 0; // TEMPORARY VARIABLE FOR COMPANY COMPENSATION


$(document).ready(function() {


    dataObject = JSON.parse(localStorage.getItem("data_object_en"));



    // INITIALIZING ORDER DETAIL POPUP

    if(dataObject.rests_orders[selectedRestIndex].order_detail == null)
    {
        dataObject.rests_orders[selectedRestIndex].order_detail = [];
    }


    $("#rest-title").html(dataObject.rests_orders[selectedRestIndex].selectedRestaurant.name_en);


    $("#rest-address").html(dataObject.rests_orders[selectedRestIndex].selectedRestaurant.address_en);


    $('#user_name').html(dataObject.user.name+", Nice to meet you :)");

    $("#name_company").html(dataObject.user.name+", "+dataObject.company.company_name+" <em> "+dataObject.user.userDiscountFromCompany+" NIS</em>");


    $("#delivery_time").html("Delivery Time "+dataObject.company.delivery_time);

    $("#contact").html(dataObject.rests_orders[selectedRestIndex].selectedRestaurant.contact);


    displayRestDetail();



    // REQUEST SERVER GET CATEGORIES WITH ITEMS
    commonAjaxCall("/restapi/index.php/categories_with_items", {"restaurantId" :  dataObject.rests_orders[selectedRestIndex].selectedRestaurant.id , "company_id" : dataObject.company.company_id}, callBackGetCategoriesWithItems);


});



function callBackGetCategoriesWithItems(url,response) {


    var categorySideMenu = "";
    var str = "";

    allCategoriesWithItemsResp = response.categories_items;


    percentage_discount = response.percentage_discount;


    dataObject.discount = percentage_discount;

    try {

        for(var x=0;x<allCategoriesWithItemsResp.length;x++)
        {

            categorySideMenu += '<li onclick="openTabCategories('+x+')" ><a href="#">'+allCategoriesWithItemsResp[x].name_en+'</a></li>';

            str +=  '';

            str += '<li class="accordin-header active" id="acc-'+x+'">';


            str += '<a href="#" class="opener">'+
                '<h3 class="light">'+allCategoriesWithItemsResp[x].name_en+'</h3>'+
                '</a>';


            for(var y=0;y<allCategoriesWithItemsResp[x].items.length ; y++) {


                var oldPrice = 0;

                oldPrice = convertFloat(allCategoriesWithItemsResp[x].items[y].price);

                if(percentage_discount != '0') {

                    allCategoriesWithItemsResp[x].items[y].price = convertFloat(convertFloat(allCategoriesWithItemsResp[x].items[y].price) - convertFloat((convertFloat(oldPrice) * convertFloat(percentage_discount)) / 100));

                }

                str += '<div class="slide">' +
                    '<div class="add-row discount" onclick="onItemSelected('+x+','+y+')" >' +
                    '<div class="row">' +
                    '<div class="col-xs-8">' +
                    '<h4>'+allCategoriesWithItemsResp[x].items[y].name_en+'</h4>' +
                    '<p>'+allCategoriesWithItemsResp[x].items[y].desc_en+'</p>' +
                    '</div>' +
                    '<div class="col-xs-4 pull-right">' +
                    '<div class="price-holder">' +
                    '<span class="new-dis"><i class="fa fa-tag" aria-hidden="true"></i>'+ allCategoriesWithItemsResp[x].items[y].price+' NIS</span>' +
                    '<span class="price">'+oldPrice+' NIS </span>' +
                    '</div>' +
                    '<img class="img-plus" src="/en/images/plus-icon.png">' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '</div>';

            }

            str +='</li>';
        }


        $('#category-side-menu').html(categorySideMenu);
        $('#main-categories-items').html(str);

        initAccordion();



        foodCartData = dataObject.rests_orders[selectedRestIndex].foodCartData;


        if (foodCartData == undefined || foodCartData == "" || foodCartData == null){


            foodCartData = null;


        }
        else {

            generateTotalUpdateFoodCart();
            updateCartElements();
        }


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


function openTabCategories(index) {

    var id = '#acc-'+index;

    $(id).addClass('active');

    initAccordion();


    var autoScroll = false;
    var scrollTop = 0;
    var scrollTopDiff = 0;

    var container = $('#scrollable3');
    var scrollTo = $(id);

    setTimeout(function(){

        // Or you can animate the scrolling:
        container.animate({

            scrollTop: ((scrollTo.offset().top - container.offset().top + container.scrollTop() - 20 ))

        },400)

    }, 300);

}



// ON ITEM SELECTED BY USER

function onItemSelected (x,y)
{

    currentCategoryId     = x;
    currentItemIndex      = y;                         // SELECTED ITEM INDEX
    oneTypeSubItems       = [];                       // REINITIALIZE ALL SUB ITEMS SELECTED BY USER TYPE ONE (SINGLE SELECTION)
    multipleTypeSubItems  = [];                       // REINITIALIZE ALL SUB ITEMS SELECTED BY USER TYPE MULTIPLE (MULTIPLE SELECTION)

    $('#special_request').val("");


    // DISPLAY ITEM (PRODUCT) DETAIL CARD

    // UPDATE ITEM NAME
    $('#itemPopUpTitle').html(allCategoriesWithItemsResp[currentCategoryId].items[currentItemIndex].name_en);


    selectedItemPrice = allCategoriesWithItemsResp[currentCategoryId].items[currentItemIndex].price;


    selectedItemPriceOrg = selectedItemPrice;


    $('#itemPopUpPrice').html("Total "+selectedItemPrice+' NIS');


    // UPDATE DESCRIPTION
    $('#itemPopUpDesc').html(allCategoriesWithItemsResp[currentCategoryId].items[currentItemIndex].desc_en);



    // CALL SERVER GET SELECTED ITEM EXTRAS WITH SUB ITEMS
    commonAjaxCall("/restapi/index.php/extras_with_subitems", {"itemId" :  allCategoriesWithItemsResp[currentCategoryId].items[currentItemIndex].id},onItemSelectedCallBack);

}






function onItemSelectedCallBack(url,response)
{

    try {


        extras = response;

        var oneTypeStr = "";
        var multipleTypeStr = "";

        var minx_holder = [];
        var miny_holder = [];

        // DISPLAY ALL AVAILABLE EXTRAS
        for (var x = 0; x < extras.extra_with_subitems.length; x++) {

            // EXTRAS WITH TYPE ONE (SINGLE SELECTABLE)
            // DISPLAY IS DROP DOWN

            if (extras.extra_with_subitems[x].type == "One") {

                var temp = "";
                var minSubItemName = "";
                var minPrice = 0;
                var minY = 0;
                var minX = 0;



                for (var y = 0; y < extras.extra_with_subitems[x].subitems.length; y++) {


                    if (percentage_discount != '0') {

                        extras.extra_with_subitems[x].subitems[y].price = convertFloat(convertFloat(extras.extra_with_subitems[x].subitems[y].price) - convertFloat((convertFloat(extras.extra_with_subitems[x].subitems[y].price) * convertFloat(percentage_discount)) / 100));

                    }

                    if (extras.extra_with_subitems[x].price_replace == 1) {

                        if (convertFloat(extras.extra_with_subitems[x].subitems[y].price) > 0) {


                            temp += '<li onclick="onOneTypeExtraSubItemSelected(' + x + ',' + y + ',' + oneTypeSubItems.length + ',this)"> ' +
                                '<label class="control control--radio"> <div class="chek-box-holder">' +
                                '<input type="radio" class="radio-one-type-'+x+'" name="radio-' + x + y + '" id="radio-id-' + x + y + '" /> ' +
                                '<div class="control__indicator"></div> </div> <p>' + extras.extra_with_subitems[x].subitems[y].name_en + '  (' + extras.extra_with_subitems[x].subitems[y].price + ') </p> </label> </li>';

                        }
                        else {

                            temp += '<li onclick="onOneTypeExtraSubItemSelected(' + x + ',' + y + ',' + oneTypeSubItems.length + ',this)"> ' +
                                '<label class="control control--radio"> <div class="chek-box-holder">' +
                                '<input type="radio" class="radio-one-type-'+x+'" name="radio-' + x + y + '" id="radio-id-' + x + y + '" /> ' +
                                '<div class="control__indicator"></div> </div> <p>' + extras.extra_with_subitems[x].subitems[y].name_en + ' </p> </label> </li>';


                        }

                        if (y == 0 || (convertFloat(extras.extra_with_subitems[x].subitems[y].price) < minPrice)) {

                            minPrice = extras.extra_with_subitems[x].subitems[y].price;
                            minSubItemName = extras.extra_with_subitems[x].subitems[y].name_en;
                            minY = y;
                            minX = x;


                        }
                    }
                    else {

                        if (convertFloat(extras.extra_with_subitems[x].subitems[y].price) > 0) {


                            temp += '<li onclick="onOneTypeExtraSubItemSelected(' + x + ',' + y + ',' + oneTypeSubItems.length + ',this)"> ' +
                                '<label class="control control--radio"> <div class="chek-box-holder">' +
                                '<input type="radio" class="radio-one-type-'+x+'" name="radio' + x + y + '"  id="radio-id-' + x + y + '"  /> ' +
                                '<div class="control__indicator"></div> </div> <p>' + extras.extra_with_subitems[x].subitems[y].name_en + '  (' + extras.extra_with_subitems[x].subitems[y].price + ') </p> </label> </li>';


                        }
                        else {
                            temp += '<li onclick="onOneTypeExtraSubItemSelected(' + x + ',' + y + ',' + oneTypeSubItems.length + ',this)"> ' +
                                '<label class="control control--radio"> <div class="chek-box-holder">' +
                                '<input type="radio" class="radio-one-type-'+x+'" name="radio' + x + y + '"  id="radio-id-' + x + y + '"  /> ' +
                                '<div class="control__indicator"></div> </div> <p>' + extras.extra_with_subitems[x].subitems[y].name_en + ' </p> </label> </li>';

                        }

                    }


                }




                oneTypeStr +=

                    '<div class="holder">' +'<div class="heading-holder"><h3 class="pull-left">' + extras.extra_with_subitems[x].name_en + '</h3><span  style="padding-left: 20px" class="error pull-left"  id="errorOneType-' + oneTypeSubItems.length +'"></span></div>' +
                    '<div class="holder">' +
                    '<ul class="control-group">';

                oneTypeStr += temp;

                oneTypeStr += '</div></ul>';

                if (minSubItemName == "") {
                    // CREATE SUB ITEM DEFAULT OBJECT AND PUSH IN ONE TYPE ARRAY EMPTY AS DEFAULT
                    // UPDATE VALUE FROM SUB ITEM SELECTION FROM DROP DOWN TYPE ONE
                    var subItem = {};

                    subItem[extras.extra_with_subitems[x].name_en] = null;
                    oneTypeSubItems.push(subItem);

                }
                else {
                    // SUB ITEM OBJECT

                    var temp = {

                        "subItemId": extras.extra_with_subitems[x].subitems[minY].id,
                        "replace_price": extras.extra_with_subitems[x].price_replace,
                        "subItemPrice": extras.extra_with_subitems[x].subitems[minY].price,
                        "subItemName": extras.extra_with_subitems[x].subitems[minY].name_en,
                        "subItemNameHe": extras.extra_with_subitems[x].subitems[minY].name_he,
                        "qty": 1
                    };   // QUANTITY OF SUB-ITEM BY DEFAULT 1


                    var subItem = {};

                    subItem[extras.extra_with_subitems[x].name_en] = temp;

                    oneTypeSubItems.push(subItem);

                    minx_holder.push(minX);
                    miny_holder.push(minY);

                }


            }

            // // EXTRAS WITH TYPE MULTIPLE (MULTIPLE SELECTABLE)
            // // DISPLAY IS SERIES OF CHECK RADIO BOXES.

            else {



                if (extras.extra_with_subitems[x].subitems.length != 0) {
                    // SUB ITEMS WITH MULTIPLE SELECTABLE OPTIONS

                    var multiTypeItemSet = [];


                    multipleTypeStr += '<div class="heading-holder"><h3 class="pull-left">' + extras.extra_with_subitems[x].name_en + '</h3><span class="error pull-left" style="padding-left: 20px"  id="errorMultipleType-' + x +'"></span></div>' +
                        '<div class="holder">' +
                        '<ul class="control-group">';

                    for (var y = 0; y < extras.extra_with_subitems[x].subitems.length; y++) {
                        if (percentage_discount != '0') {

                            extras.extra_with_subitems[x].subitems[y].price = convertFloat(convertFloat(extras.extra_with_subitems[x].subitems[y].price) - convertFloat((convertFloat(extras.extra_with_subitems[x].subitems[y].price) * convertFloat(percentage_discount)) / 100));

                        }

                        if (convertFloat(extras.extra_with_subitems[x].subitems[y].price) > 0) {
                            // ON CLICK PASSING EXTRA ID AND SUB ITEM ID

                            multipleTypeStr += '<li> <label class="control control--checkbox">' +
                                '<div class="chek-box-holder"> ' +
                                '<input id="checkbox-id-' + x + y + '" onclick="onExtraSubItemSelected('+multipleTypeSubItems.length+',' + x + ',' + y + ',' + multiTypeItemSet.length + ')" type="checkbox" />' +
                                '<div class="control__indicator"></div> ' +
                                '</div> <p>' + capitalizeFirstLetter(extras.extra_with_subitems[x].subitems[y].name_en) + ' (' + extras.extra_with_subitems[x].subitems[y].price + ')' + '</p> ' +
                                '</label>' +
                                '</li>';

                        }
                        else {
                            // ON CLICK PASSING EXTRA ID AND SUB ITEM ID

                            multipleTypeStr += '<li> <label class="control control--checkbox">' +
                                '<div class="chek-box-holder"> ' +
                                '<input id="checkbox-id-' + x + y + '" onclick="onExtraSubItemSelected('+multipleTypeSubItems.length+',' + x + ',' + y + ',' + multiTypeItemSet.length + ')" type="checkbox" />' +
                                '<div class="control__indicator"></div> ' +
                                '</div><p>' + capitalizeFirstLetter(extras.extra_with_subitems[x].subitems[y].name_en) + '</p> ' +
                                '</label>' +
                                '</li>';


                        }


                        // CREATE SUB ITEM OBJECT FOR ALL SUB ITEMS AVAILABLE AND SAVE VALUE ON USER SELECTION
                        // DEFAULT VALUE IS NULL
                        // UPDATE VALUE FROM CHECK BOX SELECTION
                        var subItem = {};
                        subItem[extras.extra_with_subitems[x].subitems[y].name_en] = null;
                        multiTypeItemSet.push(subItem);
                    }

                    multipleTypeSubItems.push(multiTypeItemSet);

                    multipleTypeStr += '</ul>';
                    multipleTypeStr += '</div>';

                }
            }
        }

        if (oneTypeSubItems.length != 0) {

            $('#parent_type_one').show();
            $('#parent_type_one').html(oneTypeStr);
        }
        else {
            $('#parent_type_one').hide();
        }

        $('#parent_type_multiple_2').html(multipleTypeStr);
        $('#parent_type_multiple_2').show();


        $('#my-order').modal('show');

        // CHECK DEFAULT SELECTED MINIMUM ITEMS

        for(var x=0;x<minx_holder.length;x++)
        {

            var radioId = '#radio-id-'+minx_holder[x]+miny_holder[x];

            $(radioId).prop('checked', true);

        }


    }
    catch (exp)
    {


        errorHandlerServerResponse(url,"parsing error call back");
        hideLoading();


    }


}



// ON ONE TYPE EXTRA SELECTED BY USER
function onOneTypeExtraSubItemSelected(extraIndex, subItemIndex, oneTypeIndex , e) {


    $('.radio-one-type-'+extraIndex).prop('checked', false);
    $('#radio-id-'+extraIndex+subItemIndex).prop('checked', true);




    // REMOVE ERROR MESSAGES ON SELECTION
    $('#errorOneType-'+oneTypeIndex).html("");

    // SUB ITEM OBJECT

    var subItem = {

        "subItemId"       : extras.extra_with_subitems[extraIndex].subitems[subItemIndex].id,
        "replace_price"   : extras.extra_with_subitems[extraIndex].price_replace,
        "subItemPrice"    : extras.extra_with_subitems[extraIndex].subitems[subItemIndex].price,
        "subItemName"     : extras.extra_with_subitems[extraIndex].subitems[subItemIndex].name_en,
        "subItemNameHe"   : extras.extra_with_subitems[extraIndex].subitems[subItemIndex].name_he,
        "qty"             : 1};   // QUANTITY OF SUB-ITEM BY DEFAULT 1


    // AS ONE TYPE EXTRA OVER RIDE TO EXISTING VALUE
    oneTypeSubItems[oneTypeIndex][extras.extra_with_subitems[extraIndex].name_en] =  subItem;

    updatedSelectedItemPrice();
}




// ON MULTIPLE TYPE EXTRA SELECTED
function onExtraSubItemSelected(ex, extraIndex, subItemIndex, index) {

    // REMOVE ERROR MESSAGES ON SELECTION
    $('#errorMultipleType-'+extraIndex).html("");

    var id = '#checkbox-id-'+extraIndex+subItemIndex;

    var name = extras.extra_with_subitems[extraIndex].subitems[subItemIndex].name_en;

    // IF CHECK BOX SET CHECKED ADD SUB ITEM

    if($(id).is(':checked'))
    {

        var limit = parseInt(extras.extra_with_subitems[extraIndex].limit);

        if(limit == 0) {

            // SUB ITEM OBJECT

            var subItem = {

                "subItemId": extras.extra_with_subitems[extraIndex].subitems[subItemIndex].id,
                "subItemPrice": extras.extra_with_subitems[extraIndex].subitems[subItemIndex].price,
                "subItemName": extras.extra_with_subitems[extraIndex].subitems[subItemIndex].name_en,
                "subItemNameHe": extras.extra_with_subitems[extraIndex].subitems[subItemIndex].name_he,
                "qty": 1
            }; // QUANTITY OF SUB-ITEM BY DEFAULT 1


            multipleTypeSubItems[ex][index][name] = subItem;
        }
        else
        {

            var countSelectedItems = 0;
            var countSelectedItems = 0;

            for(var x =0;x<multipleTypeSubItems[ex].length;x++)
            {
                for (var key in multipleTypeSubItems[ex][x]) {

                    if (multipleTypeSubItems[ex][x][key] != null && multipleTypeSubItems[ex][x][key] != undefined) {
                        countSelectedItems++;
                    }
                }
            }

            if(countSelectedItems >= limit)
            {
                $('#errorMultipleType-'+extraIndex).html("Max limit "+limit);
                $(id).prop('checked', false);
            }
            else {

                var subItem = {

                    "subItemId": extras.extra_with_subitems[extraIndex].subitems[subItemIndex].id,
                    "subItemPrice": extras.extra_with_subitems[extraIndex].subitems[subItemIndex].price,
                    "subItemName": extras.extra_with_subitems[extraIndex].subitems[subItemIndex].name_en,
                    "subItemNameHe": extras.extra_with_subitems[extraIndex].subitems[subItemIndex].name_he,
                    "qty": 1
                }; // QUANTITY OF SUB-ITEM BY DEFAULT 1


                multipleTypeSubItems[ex][index][name] = subItem;

            }

        }

    }

    // IF CHECK BOX NOT CHECKED REMOVE SUB ITEM

    else
    {
        multipleTypeSubItems[ex][index][name] = null;
        $('#errorMultipleType-'+extraIndex).html("");

    }


    updatedSelectedItemPrice();
}




function updatedSelectedItemPrice() {

    var replace = selectedItemPriceOrg;
    var sum = 0;

    for (var y = 0; y < oneTypeSubItems.length; y++)
    {
        for (var key in oneTypeSubItems[y])
        {

            if(oneTypeSubItems[y][key] != null)
            {
                // ITEM PRICE DEPENDS ON SUB ITEM CHOICE
                // REPLACE THE ORDER AMOUNT IF AMOUNT NEED TO BE REPLACE DUE TO EXTRA TYPE ONE REPLACE PRICE

                if (convertFloat(oneTypeSubItems[y][key].replace_price) == 0) {

                    sum = convertFloat(convertFloat(sum) + convertFloat(oneTypeSubItems[y][key].subItemPrice));
                }
                else {

                    replace = oneTypeSubItems[y][key].subItemPrice;

                }
            }
        }
    }

    for (var y = 0; y <  multipleTypeSubItems.length; y++) {

        for (var t = 0; t <  multipleTypeSubItems[y].length; t++) {

            for (var key in  multipleTypeSubItems[y][t]) {

                if (multipleTypeSubItems[y][t][key] != null) {

                    if (convertFloat(multipleTypeSubItems[y][t][key].subItemPrice) != 0) {

                        sum = convertFloat(sum) + convertFloat(multipleTypeSubItems[y][t][key].subItemPrice);

                    }

                }
            }
        }
    }


    selectedItemPrice = convertFloat(convertFloat(sum) + convertFloat(replace));


    $('#itemPopUpPrice').html("Total "+selectedItemPrice+' NIS');
}



// ADD USER ORDER  (ADD TO MY ORDER CLICKED)
function addUserOrder()
{
    // CHECK IF USER SELECTED ON TYPE SUB ITEMS OR NOT (REQUIRED)
    for(var x=0;x<oneTypeSubItems.length;x++)
    {

        // GET ONE TYPE SUB ITEM NAME AS KEY
        for (var key in oneTypeSubItems[x])
        {

            // IF ONE TYPE SUB ITEM NULL
            if(oneTypeSubItems[x][key] == null || oneTypeSubItems[x][key] == "" )
            {

                $('#errorOneType-'+x).html("please select one!");

                return;
            }

        }
    }


    $('#parent_type_one').hide();
    $('#parent_type_multiple_2').hide();



    // Convert Multi Type 2D array to One Type Array

    var multiItemsArray = [];

    for(var x =0;x<multipleTypeSubItems.length;x++)
    {
        for(var y=0;y<multipleTypeSubItems[x].length;y++)
        {
            multiItemsArray.push(multipleTypeSubItems[x][y]);
        }
    }

    multipleTypeSubItems = multiItemsArray;



    // SAVE ORDER TO SERVER AGAINST USER
    var order = {
        "itemId"             : allCategoriesWithItemsResp[currentCategoryId].items[currentItemIndex].id,
        "itemPrice"          : allCategoriesWithItemsResp[currentCategoryId].items[currentItemIndex].price,
        "itemName"           : allCategoriesWithItemsResp[currentCategoryId].items[currentItemIndex].name_en,
        "itemNameHe"         : allCategoriesWithItemsResp[currentCategoryId].items[currentItemIndex].name_he,
        "qty"                : 1 ,
        "subItemsOneType"    : oneTypeSubItems,
        "multiItemsOneType"  : multipleTypeSubItems,
        "specialRequest"     : $('#special_request').val()};


    $('#special_request').val("");

    dataObject.rests_orders[selectedRestIndex].order_detail.push(order);
    generateTotalUpdateFoodCart();
    updateCartElements();


    $('#my-order').modal('hide');
}



// COMPUTATION TO GENERATE TOTAL AND UPDATED FOOD ITEM CART DATA
function generateTotalUpdateFoodCart()
{
    foodCartData = [];

    var total = 0;

    tempDiscountFromCompanyCal = dataObject.user.userDiscountFromCompany;

    for(var x=0; x<dataObject.rests_orders[selectedRestIndex].order_detail.length ;x++)
    {

        var order          = dataObject.rests_orders[selectedRestIndex].order_detail[x]; // GET USER ORDERS ONE BY ONE
        var orderAmount    = convertFloat(convertFloat(order.itemPrice) * convertFloat(order.qty)); // SET DEFAULT ITEM PRICE FOR ORDER
        var sumTotalAmount = 0;  // TOTAL AMOUNT


        // FOOD CARD ITEM  FOR MAIN ITEM
        var cartItem = {
            "name": order.itemName,
            "name_he": order.itemNameHe,
            "price" : order.itemPrice ,
            "price_without_subItems" : order.itemPrice,
            "detail" : "" ,
            "detail_he" : "" ,
            "orderIndex" : x ,
            "qty" : order.qty,
            "specialRequest" : order.specialRequest ,
            "subItemOneIndex" : null,
            "subItemMultipleIndex" : null};



        // PUSH MAIN ITEM  FOOD CART OBJECT
        foodCartData.push(cartItem);


        // CHECK ONE TYPE SUB ITEMS IF ANY

        for (var y = 0; y < order.subItemsOneType.length; y++)
        {

            for (var key in order.subItemsOneType[y])
            {

                // ITEM PRICE DEPENDS ON SUB ITEM CHOICE
                // REPLACE THE ORDER AMOUNT IF AMOUNT NEED TO BE REPLACE DUE TO EXTRA TYPE ONE REPLACE PRICE

                if (convertFloat(order.subItemsOneType[y][key].replace_price) != 0)
                {
                    orderAmount = convertFloat(convertFloat(order.subItemsOneType[y][key].subItemPrice) * convertFloat(order.qty));
                    cartItem.price = convertFloat(orderAmount);
                    if(cartItem.detail == "")
                    {
                        cartItem.detail +=  key+":"+order.subItemsOneType[y][key].subItemName;
                        cartItem.detail_he +=  key+":"+order.subItemsOneType[y][key].subItemNameHe;
                    }
                    else
                    {
                        cartItem.detail +=  ", "+key+":"+order.subItemsOneType[y][key].subItemName;
                        cartItem.detail_he +=  ", "+key+":"+order.subItemsOneType[y][key].subItemNameHe;
                    }

                    cartItem.price_without_subItems = convertFloat(order.subItemsOneType[y][key].subItemPrice);


                }
                // SUM THE SUB ITEM AMOUNT
                // SUM THE AMOUNT
                else
                {
                    if(convertFloat(order.subItemsOneType[y][key].subItemPrice) != 0) {

                        sumTotalAmount = convertFloat(convertFloat(sumTotalAmount) +( convertFloat(order.subItemsOneType[y][key].subItemPrice) * convertFloat(order.subItemsOneType[y][key].qty)));

                        if(cartItem.detail == "")
                        {
                            cartItem.detail +=  order.subItemsOneType[y][key].subItemName+" (+"+order.subItemsOneType[y][key].subItemPrice+")";
                            cartItem.detail_he +=  order.subItemsOneType[y][key].subItemNameHe+" (+"+order.subItemsOneType[y][key].subItemPrice+")";
                        }
                        else
                        {
                            cartItem.detail +=  ", "+order.subItemsOneType[y][key].subItemName+" (+"+order.subItemsOneType[y][key].subItemPrice+")";
                            cartItem.detail_he += ", "+order.subItemsOneType[y][key].subItemNameHe+" (+"+order.subItemsOneType[y][key].subItemPrice+")";

                        }


                    }
                    else
                    {

                        // THOSE ITEMS HAVE PRICE ZERO WILL NOT DISPLAY AS CART ITEM AND DISPLAY AS

                        if(cartItem.detail == "")
                        {
                            cartItem.detail +=  order.subItemsOneType[y][key].subItemName;
                            cartItem.detail_he +=  order.subItemsOneType[y][key].subItemNameHe;
                        }
                        else
                        {
                            cartItem.detail +=  ", "+order.subItemsOneType[y][key].subItemName;
                            cartItem.detail_he +=  ", "+order.subItemsOneType[y][key].subItemNameHe;
                        }

                    }


                }

            }

        }

        // CHECK MULTIPLE SELECTABLE SUB ITEMS


        for (var y = 0; y < order.multiItemsOneType.length; y++)
        {

            for (var key in order.multiItemsOneType[y])
            {
                if(order.multiItemsOneType[y][key] != null && order.multiItemsOneType[y][key] != "")
                {
                    if(convertFloat(order.multiItemsOneType[y][key].subItemPrice) != 0)
                    {
                        sumTotalAmount = convertFloat(convertFloat(sumTotalAmount) + (convertFloat(order.multiItemsOneType[y][key].subItemPrice) * convertFloat(order.multiItemsOneType[y][key].qty)));

                        if(cartItem.detail == "")
                        {
                            cartItem.detail +=  order.multiItemsOneType[y][key].subItemName+" (+"+order.multiItemsOneType[y][key].subItemPrice+")";
                            cartItem.detail_he +=  order.multiItemsOneType[y][key].subItemNameHe+" (+"+order.multiItemsOneType[y][key].subItemPrice+")";

                        }
                        else
                        {
                            cartItem.detail +=  ", "+order.multiItemsOneType[y][key].subItemName+" (+"+order.multiItemsOneType[y][key].subItemPrice+")";
                            cartItem.detail_he +=  ", "+order.multiItemsOneType[y][key].subItemNameHe+" (+"+order.multiItemsOneType[y][key].subItemPrice+")";

                        }
                    }
                    else
                    {
                        // THOSE ITEMS HAVE PRICE ZERO WILL NOT DISPLAY AS CART ITEM AND DISPLAY AS

                        if(cartItem.detail == "")
                        {
                            cartItem.detail +=  order.multiItemsOneType[y][key].subItemName;
                            cartItem.detail_he +=  order.multiItemsOneType[y][key].subItemNameHe;
                        }
                        else
                        {
                            cartItem.detail +=  ", "+order.multiItemsOneType[y][key].subItemName;
                            cartItem.detail_he +=  ", "+order.multiItemsOneType[y][key].subItemNameHe;
                        }
                    }
                }


            }


        }


        // TOTAL OF ITEM WITH SUB ITEMS
        cartItem.price = convertFloat(((convertFloat(orderAmount) + convertFloat(sumTotalAmount)) / convertFloat(order.qty)));

        total = convertFloat(convertFloat(total) +  ( convertFloat(orderAmount) + convertFloat(sumTotalAmount)));
    }


    dataObject.total_paid = total;
    dataObject.actual_total  = total;
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
                '<a href="#"  class="btn-up"  id="left-btn'+x+'" onclick="onQtyIncreaseButtonClicked(' + x + ')" class="left-btn"><i class="fa fa-angle-up" aria-hidden="true"></i></a>'+
                '<span id="count'+x+'" class="count f black">' + foodCartData[x].qty.toString() + '</span>' +
                '<a href="#" class="btn-down" onclick="onQtyDecreasedButtonClicked(' + x + ')" class="increase-btn"><i class="fa fa-angle-down" aria-hidden="true"></i></a>' +
                '</div>'+

                '</div>'+
                '<div class="col-xs-8">';


            countItems = countItems +  parseInt(foodCartData[x].qty);


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
            else {

                if(foodCartData[x].detail != "") {

                    str += '<p>' + foodCartData[x].detail + '</p>';

                }
                else {

                    str += '<p>No detail</p>';
                }
            }


            str += '</div>'+
                '<div class="col-xs-2">'+
                '<a class="remove" onclick="removeItem(' + x + ')" href="#"><i class="fa fa-times" aria-hidden="true"></i></a>'+
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



        $('.col-second').css("visibility","visible");
        $('.col-one').hide();

    }
    else {


        $('.col-second').css("visibility","hidden");
        $('.col-one').show();


        $('#total_paid').html("0  NIS");


        $('#cc_parent').hide();


        $('#company_contribution').html("0 NIS");


        $('#st_parent').hide();


        $('#actual_total').html("0 NIS");


    }

    $('.badge').html(countItems);


    // if(convertFloat(userObject.totalWithoutDiscount) < convertFloat(minOrderLimit) )
    // {
    //     $("#minLimit").show();
    // }
    // else {
    //
    //     $("#minLimit").hide();
    // }

}



// USER WANT TO INCREASE THE QUANTITY OF ITEMS FROM ORDER

function onQtyIncreaseButtonClicked(index) {


    var orders = dataObject.rests_orders[selectedRestIndex].order_detail;

    // UPDATE ITEM MAIN
    orders[foodCartData[index].orderIndex].qty = convertFloat(orders[foodCartData[index].orderIndex].qty) + 1;

    // INCREASE THE QTY OF SUB ITEMS ONE TYPE
    for(var x=0;x<orders[foodCartData[index].orderIndex].subItemsOneType.length;x++)
    {
        for (var key in orders[foodCartData[index].orderIndex].subItemsOneType[x])
        {
            if(orders[foodCartData[index].orderIndex].subItemsOneType[x][key] != null) {

                orders[foodCartData[index].orderIndex].subItemsOneType[x][key].qty =
                    convertFloat(orders[foodCartData[index].orderIndex].subItemsOneType[x][key].qty) + 1;
            }
        }
    }

    // INCREASE THE QTY OF SUB ITEMS MULTIPLE TYPE
    for(var x=0;x<orders[foodCartData[index].orderIndex].multiItemsOneType.length;x++)
    {
        for (var key in orders[foodCartData[index].orderIndex].multiItemsOneType[x])
        {
            if(orders[foodCartData[index].orderIndex].multiItemsOneType[x][key] != null) {

                orders[foodCartData[index].orderIndex].multiItemsOneType[x][key].qty =
                    (convertFloat(orders[foodCartData[index].orderIndex].multiItemsOneType[x][key].qty) + 1);
            }
        }
    }

    foodCartData[index].qty = convertFloat(foodCartData[index].qty) + 1;


    dataObject.actual_total = convertFloat(convertFloat(dataObject.actual_total) - convertFloat(foodCartData[index].price));
    dataObject.total_paid = dataObject.actual_total;

    tempDiscountFromCompanyCal = dataObject.user.userDiscountFromCompany;


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



    var itemCountId = "#count"+index;

    $(itemCountId).html(foodCartData[index].qty.toString());

    $('.badge').html(parseInt($('.badge').html()) + 1);


    generateTotalUpdateFoodCart();
    updateCartElements();

}


// USER WANT TO DECREASE THE QUANTITY OR REMOVE OF ITEMS FROM ORDER

function onQtyDecreasedButtonClicked(index) {


    var orders = dataObject.rests_orders[selectedRestIndex].order_detail;

    // UPDATE ITEM MAIN

    // DECREASE QUANTITY
    if(convertFloat(orders[foodCartData[index].orderIndex].qty) != 1)
    {

        orders[foodCartData[index].orderIndex].qty = convertFloat(orders[foodCartData[index].orderIndex].qty) - 1;

        // DECREASE THE QTY OF SUB ITEMS ONE TYPE
        for(var x=0;x< orders[foodCartData[index].orderIndex].subItemsOneType.length;x++)
        {
            for (var key in orders[foodCartData[index].orderIndex].subItemsOneType[x])
            {

                if(orders[foodCartData[index].orderIndex].subItemsOneType[x][key] != null) {


                    orders[foodCartData[index].orderIndex].subItemsOneType[x][key].qty =
                        convertFloat(orders[foodCartData[index].orderIndex].subItemsOneType[x][key].qty) -  1;


                }

            }

        }

        // DECREASE THE QTY OF SUB ITEMS MULTIPLE TYPE
        for(var x=0;x<orders[foodCartData[index].orderIndex].multiItemsOneType.length;x++)
        {
            for (var key in orders[foodCartData[index].orderIndex].multiItemsOneType[x])
            {
                if(orders[foodCartData[index].orderIndex].multiItemsOneType[x][key] != null) {

                    orders[foodCartData[index].orderIndex].multiItemsOneType[x][key].qty =
                        (convertFloat(orders[foodCartData[index].orderIndex].multiItemsOneType[x][key].qty) - 1);
                }
            }
        }


    }


    if(foodCartData.length != 0 && foodCartData[index] != undefined  && (convertFloat(foodCartData[index].qty) != 1))
    {

        foodCartData[index].qty = convertFloat(foodCartData[index].qty) - 1;

        dataObject.actual_total = convertFloat(convertFloat(dataObject.actual_total) - convertFloat(foodCartData[index].price));
        dataObject.total_paid = dataObject.actual_total;

        tempDiscountFromCompanyCal = dataObject.user.userDiscountFromCompany;


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



        var itemCountId = "#count"+index;
        $(itemCountId).html(foodCartData[index].qty.toString());


        $('.badge').html(parseInt($('.badge').html()) - 1);


    }

    generateTotalUpdateFoodCart();
    updateCartElements();
}



function removeItem(index) {

    var orders = dataObject.rests_orders[selectedRestIndex].order_detail;

    // IF MAIN ITEM DELETED ALL SUB ITEMS ALSO DELETED

    dataObject.total_paid = convertFloat(convertFloat(dataObject.total_paid) - convertFloat(foodCartData[index].price));

    orders.splice(foodCartData[index].orderIndex, 1);

    generateTotalUpdateFoodCart();

    if(foodCartData.length == 0)
    {
        $('.col-second').css("visibility","hidden");
        $('.col-one').show();

    }
    else
    {
        generateTotalUpdateFoodCart();
        updateCartElements();

    }


    if(foodCartData.length != 0 && foodCartData[index] != undefined  && (convertFloat(foodCartData[index].qty) != 1))
    {

        foodCartData[index].qty = convertFloat(foodCartData[index].qty) - 1;

        dataObject.actual_total = convertFloat(convertFloat(dataObject.actual_total) - convertFloat(foodCartData[index].price));
        dataObject.total_paid = dataObject.actual_total;

        tempDiscountFromCompanyCal = dataObject.user.userDiscountFromCompany;


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



        var itemCountId = "#count"+index;
        $(itemCountId).html(foodCartData[index].qty.toString());


        $('.badge').html(parseInt($('.badge').html()) - 1);



    }


    generateTotalUpdateFoodCart();
    updateCartElements();
}



// USER CLICKED ORDER NOW
function OnOrderNowClicked() {


    if(foodCartData.length != 0) {


        generateTotalUpdateFoodCart();


        dataObject.rests_orders[selectedRestIndex].foodCartData = foodCartData;


        localStorage.setItem("data_object_en", JSON.stringify(dataObject));
        localStorage.setItem("tempDiscountFromCompanyCal", tempDiscountFromCompanyCal);


        window.location.href = '/en/confirm-order';

    }


}


function convertFloat(num)
{

    return parseFloat(parseFloat(num).toFixed(2));

}



function capitalizeFirstLetter(string) {

    return string.charAt(0).toUpperCase() + string.slice(1);


}