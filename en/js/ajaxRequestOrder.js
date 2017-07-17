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

            str +=  '<li class="active">'+
                '<a href="#" class="opener">'+
                '<h3 class="light">'+allCategoriesWithItemsResp[x].name_en+'</h3>'+
                '</a>';


            for(var y=0;y<allCategoriesWithItemsResp[x].items.length ; y++) {


                var oldPrice = 0;

                oldPrice = convertFloat(allCategoriesWithItemsResp[x].items[y].price);

                allCategoriesWithItemsResp[x].items[y].price =  convertFloat(convertFloat(allCategoriesWithItemsResp[x].items[y].price) -  convertFloat((convertFloat(oldPrice) * convertFloat(percentage_discount)) / 100));


                str += '<div class="slide">' +
                '<div class="add-row discount" data-toggle="modal" data-target="#my-order">' +
                '<div class="row">' +
                '<div class="col-xs-8">' +
                '<h4>Shawarma</h4>' +
                '<p>Double Angus Sandwich: 300 grams of quality Angus entrecote meat with mayon…</p>' +
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

