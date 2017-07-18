var dataObject = null;   // DATA OBJECT CONTAIN INFORMATION ABOUT COMPANY, USER & USER ORDER


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


var selectedItemPrice     = 0;  // SELECTED ITEM PRICE AFTER CALCULATION


$(document).ready(function() {


    addLoading();


    dataObject = JSON.parse(localStorage.getItem("data_object_en"));


    // REQUEST SERVER GET CATEGORIES WITH ITEMS
    commonAjaxCall("/restapi/index.php/categories_with_items", {"restaurantId" :  dataObject.rests_orders[selectedRestIndex].selectedRestaurant.id , "company_id" : dataObject.company.company_id}, callBackGetCategoriesWithItems);


});



function callBackGetCategoriesWithItems(url,response) {


    var categorySideMenu = "";
    var str = "";

    allCategoriesWithItemsResp = response.categories_items;

    percentage_discount = response.percentage_discount;


    try {

        for(var x=0;x<allCategoriesWithItemsResp.length;x++)
        {

            categorySideMenu += '<li><a href="#">'+allCategoriesWithItemsResp[x].name_en+'</a></li>';

            str +=  '<li>'+
                '<a href="#" class="opener">'+
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

    }
    catch (exp)
    {


        errorHandlerServerResponse(url,"parsing error call back");
        hideLoading();


    }

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
        var isOneExist = false;


        // DISPLAY ALL AVAILABLE EXTRAS
        for (var x = 0; x < extras.extra_with_subitems.length; x++) {

            // EXTRAS WITH TYPE ONE (SINGLE SELECTABLE)
            // DISPLAY IS DROP DOWN

            if (extras.extra_with_subitems[x].type == "One") {

                var temp = "";
                var minSubItemName = "";
                var minPrice = 0;
                var minY = 0;

                for (var y = 0; y < extras.extra_with_subitems[x].subitems.length; y++) {


                    if (percentage_discount != '0') {

                        extras.extra_with_subitems[x].subitems[y].price = convertFloat(convertFloat(extras.extra_with_subitems[x].subitems[y].price) - convertFloat((convertFloat(extras.extra_with_subitems[x].subitems[y].price) * convertFloat(percentage_discount)) / 100));

                    }

                    if (extras.extra_with_subitems[x].price_replace == 1) {

                        if (convertFloat(extras.extra_with_subitems[x].subitems[y].price) > 0) {


                            temp += '<li onclick="onOneTypeExtraSubItemSelected(' + x + ',' + y + ',' + oneTypeSubItems.length + ',this)"> ' +
                                '<label class="control control--radio"> <div class="chek-box-holder">' +
                                '<input type="radio" name="radio" /> ' +
                                '<div class="control__indicator"></div> </div> <p>' + extras.extra_with_subitems[x].subitems[y].name_en + '  (' + extras.extra_with_subitems[x].subitems[y].price + ') </p> </label> </li>';

                        }
                        else {

                            temp += '<li onclick="onOneTypeExtraSubItemSelected(' + x + ',' + y + ',' + oneTypeSubItems.length + ',this)"> ' +
                                '<label class="control control--radio"> <div class="chek-box-holder">' +
                                '<input type="radio" name="radio" /> ' +
                                '<div class="control__indicator"></div> </div> <p>' + extras.extra_with_subitems[x].subitems[y].name_en + ' </p> </label> </li>';


                        }

                        if (y == 0 || (convertFloat(extras.extra_with_subitems[x].subitems[y].price) < minPrice)) {
                            minPrice = extras.extra_with_subitems[x].subitems[y].price;
                            minSubItemName = extras.extra_with_subitems[x].subitems[y].name_en;
                            minY = y;
                        }
                    }
                    else {

                        if (convertFloat(extras.extra_with_subitems[x].subitems[y].price) > 0) {


                            temp += '<li onclick="onOneTypeExtraSubItemSelected(' + x + ',' + y + ',' + oneTypeSubItems.length + ',this)"> ' +
                                '<label class="control control--radio"> <div class="chek-box-holder">' +
                                '<input type="radio" name="radio" /> ' +
                                '<div class="control__indicator"></div> </div> <p>' + extras.extra_with_subitems[x].subitems[y].name_en + '  (' + extras.extra_with_subitems[x].subitems[y].price + ') </p> </label> </li>';


                        }
                        else {
                            temp += '<li onclick="onOneTypeExtraSubItemSelected(' + x + ',' + y + ',' + oneTypeSubItems.length + ',this)"> ' +
                                '<label class="control control--radio"> <div class="chek-box-holder">' +
                                '<input type="radio" name="radio" /> ' +
                                '<div class="control__indicator"></div> </div> <p>' + extras.extra_with_subitems[x].subitems[y].name_en + ' </p> </label> </li>';

                        }

                    }
                }


                oneTypeStr += '<h3>' + extras.extra_with_subitems[x].name_en + '</h3>' +
                    '<div class="holder">' +
                    '<ul class="control-group">';

                oneTypeStr += temp;

                oneTypeStr += '</div></ul>';
                oneTypeStr += '<span class="error" style="display: none;" id="errorOneType"></span>';


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

                }

                isOneExist = true;

            }

            // // EXTRAS WITH TYPE MULTIPLE (MULTIPLE SELECTABLE)
            // // DISPLAY IS SERIES OF CHECK RADIO BOXES.

            else {
                if (extras.extra_with_subitems[x].subitems.length != 0) {
                    // SUB ITEMS WITH MULTIPLE SELECTABLE OPTIONS

                    multipleTypeStr += '<h3>' + extras.extra_with_subitems[x].name_en + '</h3>' +
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
                                '<input id="checkbox-id-' + x + y + '" onclick="onExtraSubItemSelected(' + x + ',' + y + ',' + multipleTypeSubItems.length + ')" type="checkbox" />' +
                                '<div class="control__indicator"></div> ' +
                                '</div> <p>' + capitalizeFirstLetter(extras.extra_with_subitems[x].subitems[y].name_en) + ' (' + extras.extra_with_subitems[x].subitems[y].price + ')' + '</p> ' +
                                '</label>' +
                                '</li>';

                        }
                        else {
                            // ON CLICK PASSING EXTRA ID AND SUB ITEM ID

                            multipleTypeStr += '<li> <label class="control control--checkbox">' +
                                '<div class="chek-box-holder"> ' +
                                '<input id="checkbox-id-' + x + y + '" onclick="onExtraSubItemSelected(' + x + ',' + y + ',' + multipleTypeSubItems.length + ')" type="checkbox" />' +
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
                        multipleTypeSubItems.push(subItem);
                    }

                    multipleTypeStr += '</ul>';
                    multipleTypeStr += '</div>';
                    multipleTypeStr += '<span class="error" style="display: none;" id="errorMultipleType"></span>';
                }
            }
        }

        if (isOneExist) {

            $('#parent_type_one').show();
            $('#parent_type_one').html(oneTypeStr);
        }
        else {
            $('#parent_type_one').hide();
        }

        $('#parent_type_multiple_2').html(multipleTypeStr);
        $('#parent_type_multiple_2').show();


        $('#my-order').modal('show');

    }
    catch (exp)
    {


        errorHandlerServerResponse(url,"parsing error call back");
        hideLoading();


    }


}



// ON ONE TYPE EXTRA SELECTED BY USER
function onOneTypeExtraSubItemSelected(extraIndex, subItemIndex, oneTypeIndex , e) {


    // REMOVE ERROR MESSAGES ON SELECTION
    $('#errorOneType').html("");

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
function onExtraSubItemSelected(extraIndex, subItemIndex, index) {

    // REMOVE ERROR MESSAGES ON SELECTION
    $('#errorMultipleType').html("");

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


            multipleTypeSubItems[index][name] = subItem;
        }
        else
        {

            var countSelectedItems = 0;

            for(var x =0;x<multipleTypeSubItems.length;x++)
            {
                for (var key in multipleTypeSubItems[x]) {

                    if (multipleTypeSubItems[x][key] != null && multipleTypeSubItems[x][key] != undefined) {
                        countSelectedItems++;
                    }
                }
            }

            if(countSelectedItems >= limit)
            {
                $('#errorMultipleType').html("Max limit is"+limit);
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


                multipleTypeSubItems[index][name] = subItem;

            }

        }

    }

    // IF CHECK BOX NOT CHECKED REMOVE SUB ITEM

    else
    {
        multipleTypeSubItems[index][name] = null;

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

        for (var key in  multipleTypeSubItems[y]) {

            if ( multipleTypeSubItems[y][key] != null) {

                if (convertFloat( multipleTypeSubItems[y][key].subItemPrice) != 0) {

                    sum   = convertFloat(convertFloat(sum) + convertFloat(multipleTypeSubItems[y][key].subItemPrice));

                }

            }
        }

    }


    selectedItemPrice = convertFloat(convertFloat(sum) + convertFloat(replace));


    $('#itemPopUpPrice').html("Total "+selectedItemPrice+' NIS');
}





function convertFloat(num)
{

    return parseFloat(parseFloat(num).toFixed(2));

}



function capitalizeFirstLetter(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
}