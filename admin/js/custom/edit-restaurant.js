
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

$('#address_en').bind('input', function() {

    document.getElementById('address_en_error').innerHTML = "";

});

$('#address_he').bind('input', function() {

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




function update_restaurant(rest_id)
{
    var name_en                    =  $('#name_en').val();
    var name_he                    =  $('#name_he').val();

    var contact                    =  $('#contact').val();

    var min_amount                 =  $('#min_amount').val();

    var city                       =  $('#city').val();

    var description_en             =  $('#description_en').val();
    var description_he             =  $('#description_he').val();

    var address_en                 =  $('#address_en').val();
    var address_he                 =  $('#address_he').val();

    var hechsher_en                =  $('#hechsher_en').val();
    var hechsher_he                =  $('#hechsher_he').val();
    var rest_id                    =  rest_id;



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

    if(contact == "")
    {
        $('#contact_error').html('Required*');
        return;
    }

    if(min_amount == "")
    {
        $('#min_amount_error').html('Required*');
        return;
    }

    if(description_en == "")
    {
        $('#description_en_error').html('Required*');
        return;
    }
    if(description_he == "")
    {
        $('#description_he_error').html('Required*');
        return;
    }

    if(address_en == "")
    {
        $('#address_en_error').html('Required*');
        return;
    }
    if(address_he == "")
    {
        $('#address_he_error').html('Required*');
        return;
    }

    if(hechsher_en == "")
    {
        $('#hechsher_en_error').html('Required*');
        return;
    }
    if(hechsher_en == "")
    {
        $('#hechsher_en_error').html('Required*');
        return;
    }


    var postForm =
    { //Fetch form data
        'name_en'                 :  $('#name_en').val(),
        'name_he'                 :  $('#name_he').val(),
        'city_id'                 :  $('#city').val(),
        'logo'                    :  globalEditLogo,

        'contact'                 :  $('#contact').val(),

        'coming_soon'             :  $('#coming_soon').val(),

        'hide'                    :  $('#hide').val(),

        'description_en'          :  $('#description_en').val(),
        'description_he'          :  $('#description_he').val(),

        'address_en'              :  $('#address_en').val(),
        'address_he'              :  $('#address_he').val(),

        'hechsher_en'             :  $('#hechsher_en').val(),
        'hechsher_he'             :  $('#hechsher_he').val(),
        'pickup_hide'             :  $('#pickup_hide').val(),
        'min_amount'              :  $('#min_amount').val(),
        'rest_id'                 :  rest_id


    };
    

    // IF URL IS FROM SUPER ADMIN
    addLoading();
    $.ajax({
        url:"ajax/edit_restaurant.php",
        type: 'POST',
        data: postForm,
        success:function(data)
        {

           // alert(globalEditLogo);
           // alert(data);
            if(globalEditLogo != null)
            {
               // save_imagee_edit(data);
            }
            hideLoading();
            alert("restaurant updated successfully");
            //window.location.href = "index.php?id="+city_id;
        }
    });

}

function delete_restaurant(restaurant_id,url)
{
    addLoading();
    $.ajax({
        url:"ajax/delete_restaurant.php",
        method:"post",
        data:{restaurant_id:restaurant_id},
        dataType:"json",
        success:function(data)
        {
            hideLoading();
            alert("Restaurant deleted successfully");
            window.location.href = "index.php";
        }
    });
}


function save_imagee_edit(rest_id)
{
    alert(window.location.hostname);
    $.ajax({
        url: "http://"+window.location.hostname+"/restapi/index.php/update_restaurant_logo",
        type: 'POST',
        data: {rest_id:rest_id},
        success: function (data) {
            alert("restaurant updated successfully");
            hideLoading();
           // window.location.href = "index.php";
        }
    });
}

