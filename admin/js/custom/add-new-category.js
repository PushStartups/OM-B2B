function show_category_div()
{
    $("#add-category").show();
}

$('#name_en').bind('input', function() {

    document.getElementById('name_en_error').innerHTML = "";

});

$('#name_he').bind('input', function() {

    document.getElementById('name_he_error').innerHTML = "";

});


function go_back_to_restaurant()
{
    window.location.href = "index.php?id=1";
}


function add_new_category(menu_id,url)
{
   // alert(menu_id);
    var name_en                    =  $('#name_en').val();
    var name_he                    =  $('#name_he').val();


    if(name_en == "")
    {
        $('#name_en_error').html('Required*');
        return;
    }

    if(name_he == "")
    {
        $('#name_he_error').html('Required');
        return;
    }



    var postForm = { //Fetch form data

        'name_en'                 :  $('#name_en').val(),
        'name_he'                 :  $('#name_he').val(),
        'image_url'               :  globalImgCat,

        'is_discount'             :  0,

        'business_offer'          :  $('#business_offer').val(),

        'menu_id'                 :   menu_id,

    };

    var url      = window.location.href;
    var restapi_url = "";

    substring1 = "dataentry";
    substring2 = "admin";

    // IF URL IS FROM DATAENTRY
    var dataentry = url.includes(substring1);
    if(dataentry == true)
    {
        restapi_url = "http://"+window.location.hostname+"/restapi/index.php/save_category_image_dataentry";
    }
    else
    {
        restapi_url = "http://"+window.location.hostname+"/restapi/index.php/save_category_image";
    }


    addLoading();
    $.ajax({
        url:"ajax/insert_new_category.php",
        method:"post",
        data:postForm,
        dataType:"json",
        success:function(data)
        {
            save_image(data,menu_id,url,restapi_url);

        }
    });
}

function save_image(cat_id,menu_id,url,restapi_url)
{
    //alert(cat_id);
    //alert(menu_id);
    $.ajax({
        url:  restapi_url,
        type: 'POST',
        data: {cat_id:cat_id,menu_id:menu_id},
        success: function (data) {
            alert("category added successfully");
            hideLoading();
            window.location.href = url;
        }
    });
}
