
//BIND FUNCTIONS



$('#min_amount').bind('input', function() {

    if(!this.value.match(/^\d+$/))
    {
        document.getElementById('min_amount_error').innerHTML = "Wrong Number!";
    }
    else
    {
        document.getElementById('min_amount_error').innerHTML = "";
    }

});


$('#contact').bind('input', function() {

    if(!this.value.match(/^\d+$/))
    {
        document.getElementById('contact_error').innerHTML = "Wrong Number!";
    }
    else
    {
        document.getElementById('contact_error').innerHTML = "";
    }

});

$('#name_en').bind('input', function() {

    document.getElementById('name_en_error').innerHTML = "";

});

$('#name_he').bind('input', function() {

    document.getElementById('name_he_error').innerHTML = "";

});

$('#description_en').bind('input', function() {

    document.getElementById('description_en_error').innerHTML = "";

});

$('#description_he').bind('input', function() {

    document.getElementById('description_he_error').innerHTML = "";

});

$('#area_en').bind('input', function() {

    document.getElementById('address_en_error').innerHTML = "";

});

$('#area_he').bind('input', function() {

    document.getElementById('address_he_error').innerHTML = "";

});

$('#hechsher_en').bind('input', function() {

    document.getElementById('hechsher_en_error').innerHTML = "";

});

$('#hechsher_he').bind('input', function() {

    document.getElementById('hechsher_he_error').innerHTML = "";

});

$('#file').bind('input', function() {

    document.getElementById('file_error').innerHTML = "";

});


function add_business_offer(url)
{
    // alert($("#business_category").val());
    // alert($("#business_item").val());
    // alert($("#day").val());
    // alert($("#week_cycle").val());


    var postForm = { //Fetch form data
        'category_id'   : $('#business_category').val(),
        'item_id'       : $('#business_item').val(),
        'day'           : $('#day').val(),
        'week_cycle'    : $('#week_cycle').val()
    };


    addLoading();
    $.ajax({
        url:"ajax/insert_business_offer.php",
        method:"post",
        data:postForm,
        dataType:"json",
        success:function(data)
        {
            hideLoading();
            alert("business offer added successfully");
             window.location.href = url;
        }
    });

}

function category_change(val)
{
    addLoading();
    $.ajax({
        url: "ajax/business_offers.php",
        type: 'POST',
        data: {category_id:val},
        dataType:"json",
        success: function (data) {
            //alert("restaurant added successfully");
            hideLoading();
            $("#item-div").show();
            $("#week-cycle-div").show();
            $("#week-day-div").show();
            $("#business-offer-div").show();
            $("#item-div").html(data);

            // setTimeout(function(){ window.location.href = "index.php"; }, 5000);
        }
    });
}



function add_restaurant() {

    //alert(globalImg);
    var name_en = $('#name_en').val();
    var name_he = $('#name_he').val();
    var city_id = $('#city').val();

    var contact = $('#contact').val();

    var min_amount = $('#min_amount').val();

    var city = $('#city').val();

    var description_en = $('#description_en').val();
    var description_he = $('#description_he').val();

    var address_en = $('#area_en').val();
    var address_he = $('#area_he').val();

    var hechsher_en = $('#hechsher_en').val();
    var hechsher_he = $('#hechsher_he').val();


    if($('#lat').val() == "")
    {
        $('#address_en_error').html('Please Use Suggestions*');
        return;

    }

    if (name_en == "") {
        $('#name_en_error').html('Required*');
        return;
    }
    if (name_he == "") {
        $('#name_he_error').html('Required');
        return;
    }

    if (contact == "") {
        $('#contact_error').html('Required*');
        return;
    }

    if (min_amount == "") {
        $('#min_amount_error').html('Required*');
        return;
    }

    if (description_en == "") {
        $('#description_en_error').html('Required*');
        return;
    }
    if (description_he == "") {
        $('#description_he_error').html('Required*');
        return;
    }

    if (address_en == "") {
        $('#address_en_error').html('Required*');
        return;
    }
    if (address_he == "") {
        $('#address_he_error').html('Required*');
        return;
    }

    if (hechsher_en == "") {
        $('#hechsher_en_error').html('Required*');
        return;
    }
    if (hechsher_en == "") {
        $('#hechsher_en_error').html('Required*');
        return;
    }

    console.log(globalImg);
    var postForm = { //Fetch form data
        'name_en': $('#name_en').val(),
        'name_he': $('#name_he').val(),
        'city_id': $('#city').val(),
        'logo'   : globalImg,

        'contact': $('#contact').val(),

        'coming_soon': $('#coming_soon').val(),

        'hide': $('#hide').val(),

        'description_en': $('#description_en').val(),
        'description_he': $('#description_he').val(),

        'address_en' : $('#area_en').val(),
        'address_he' : $('#area_he').val(),

        'hechsher_en': $('#hechsher_en').val(),
        'hechsher_he': $('#hechsher_he').val(),
        'pickup_hide': $('#pickup_hide').val(),
        'min_amount' : $('#min_amount').val(),
        'lat': $('#lat').val(),
        'lng': $('#lng').val(),


    };

    addLoading();


    var url      = window.location.href;
    var restapi_url = "";

    substring1 = "dataentry";
    substring2 = "admin";

    // IF URL IS FROM DATAENTRY
    var dataentry = url.includes(substring1);
    if(dataentry == true)
    {
        restapi_url = "http://"+window.location.hostname+"/restapi/index.php/insert_new_restaurant_dataentry";
    }
    else
    {
         restapi_url = "http://"+window.location.hostname+"/restapi/index.php/insert_new_restaurant";
    }

    $.ajax({
        url: "ajax/insert_new_restaurant.php",
        type: 'POST',
        data: postForm,
        success: function (data) {
            save_imagee(data,restapi_url);
            //hideLoading();

        }
    });
}

function save_imagee(rest_id,restapi_url)
{
    $.ajax({
        url: restapi_url,
        type: 'POST',
        data: {rest_id:rest_id},
        success: function (data) {
            alert("restaurant added successfully");
            hideLoading();

           // setTimeout(function(){ window.location.href = "index.php"; }, 5000);
        }
    });
}


