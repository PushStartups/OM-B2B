
function show_delivery_address()
{
    $("#add-delivery-address").show();
}






$('#sunday_start_time').bind('input', function() {

    document.getElementById('error_sunday_start_time').innerHTML = "";

});

$('#sunday_end_time').bind('input', function() {

    document.getElementById('error_sunday_end_time').innerHTML = "";

});

$('#monday_start_time').bind('input', function() {

    document.getElementById('error_monday_start_time').innerHTML = "";

});

$('#monday_end_time').bind('input', function() {

    document.getElementById('error_monday_end_time').innerHTML = "";

});

$('#tuesday_start_time').bind('input', function() {

    document.getElementById('error_tuesday_start_time').innerHTML = "";

});

$('#tuesday_end_time').bind('input', function() {

    document.getElementById('error_tuesday_end_time').innerHTML = "";

});

$('#wednesday_start_time').bind('input', function() {

    document.getElementById('error_wednesday_start_time').innerHTML = "";

});

$('#wednesday_end_time').bind('input', function() {

    document.getElementById('error_wednesday_end_time').innerHTML = "";

});

$('#thursday_start_time').bind('input', function() {

    document.getElementById('error_thursday_start_time').innerHTML = "";

});

$('#thursday_end_time').bind('input', function() {

    document.getElementById('error_thursday_end_time').innerHTML = "";

});

$('#friday_start_time').bind('input', function() {

    document.getElementById('error_friday_start_time').innerHTML = "";

});

$('#friday_end_time').bind('input', function() {

    document.getElementById('error_friday_end_time').innerHTML = "";

});

$('#saturday_start_time').bind('input', function() {

    document.getElementById('error_saturday_start_time').innerHTML = "";

});

$('#saturday_end_time').bind('input', function() {

    document.getElementById('error_saturday_end_time').innerHTML = "";

});






$('#area_en').bind('input', function() {

    document.getElementById('area_en_error').innerHTML = "";

});

$('#area_he').bind('input', function() {

    document.getElementById('area_he_error').innerHTML = "";

});


$('#fee').bind('input', function() {

    if(!this.value.match(/^\d+$/))
    {
        document.getElementById('fee_error').innerHTML = "Wrong Number!";
    }
    else
    {
        document.getElementById('fee_error').innerHTML = "";
    }

});







function add_timing(restaurant_id,url)
{

    var sunday_start_time       =  $('#sunday_start_time').val();
    var sunday_end_time         =  $('#sunday_end_time').val();

    var monday_start_time       =  $('#monday_start_time').val();
    var monday_end_time         =  $('#monday_end_time').val();

    var tuesday_start_time      =  $('#tuesday_start_time').val();
    var tuesday_end_time        =  $('#tuesday_end_time').val();


    var wednesday_start_time    =  $('#wednesday_start_time').val();
    var wednesday_end_time      =  $('#wednesday_end_time').val();

    var thursday_start_time     =  $('#thursday_start_time').val();
    var thursday_end_time       =  $('#thursday_end_time').val();

    var friday_start_time       =  $('#friday_start_time').val();
    var friday_end_time         =  $('#friday_end_time').val();

    var saturday_start_time     =  $('#saturday_start_time').val();
    var saturday_end_time       =  $('#saturday_end_time').val();




    if (sunday_start_time == "")
    {
        $('#error_sunday_start_time').html('Sunday Start Time Required*');
        return;
    }
    if (sunday_end_time == "")
    {
        $('#error_sunday_end_time').html('Sunday End Time Required*');
        return;
    }


    if (monday_start_time == "")
    {
        $('#error_monday_start_time').html('Monday Start Time Required*');
        return;
    }
    if (monday_end_time == "")
    {
        $('#error_monday_end_time').html('Monday End Time Required*');
        return;
    }




    if (tuesday_start_time == "")
    {
        $('#error_tuesday_start_time').html('Tuesday Start Time Required*');
        return;
    }
    if (tuesday_end_time == "")
    {
        $('#error_tuesday_end_time').html('Tuesday End Time Required*');
        return;
    }




    if (wednesday_start_time == "")
    {
        $('#error_wednesday_start_time').html('Wednesday Start Time Required*');
        return;
    }
    if (wednesday_end_time == "")
    {
        $('#error_wednesday_end_time').html('Wednesday End Time Required*');
        return;
    }



    if (thursday_start_time == "")
    {
        $('#error_thursday_start_time').html('Thursday Start Time Required*');
        return;
    }
    if (thursday_end_time == "")
    {
        $('#error_thursday_end_time').html('Thursday End Time Required*');
        return;
    }



    if (friday_start_time == "")
    {
        $('#error_friday_start_time').html('Friday Start Time Required*');
        return;
    }
    if (friday_end_time == "")
    {
        $('#error_friday_end_time').html('Friday End Time Required*');
        return;
    }



    if (saturday_start_time == "")
    {
        $('#error_saturday_start_time').html('Saturday Start Time Required*');
        return;
    }
    if (saturday_end_time == "")
    {
        $('#error_saturday_end_time').html('Saturday End Time Required*');
        return;
    }



    var postForm = { //Fetch form data

        'restaurant_id'           :  restaurant_id,

        'week1_id'                : $('#week1_id').val(),
        'week2_id'                : $('#week2_id').val(),
        'week3_id'                : $('#week3_id').val(),
        'week4_id'                : $('#week4_id').val(),
        'week5_id'                : $('#week5_id').val(),
        'week6_id'                : $('#week6_id').val(),
        'week7_id'                : $('#week7_id').val(),

        'sunday_start_time'       :  $('#sunday_start_time').val(),
        'sunday_end_time'         :  $('#sunday_end_time').val(),

        'monday_start_time'       :  $('#monday_start_time').val(),
        'monday_end_time'         :  $('#monday_end_time').val(),

        'tuesday_start_time'      :  $('#tuesday_start_time').val(),
        'tuesday_end_time'        :  $('#tuesday_end_time').val(),

        'wednesday_start_time'    :  $('#wednesday_start_time').val(),
        'wednesday_end_time'      :  $('#wednesday_end_time').val(),

        'thursday_start_time'     :  $('#thursday_start_time').val(),
        'thursday_end_time'       :  $('#thursday_end_time').val(),

        'friday_start_time'       :  $('#friday_start_time').val(),
        'friday_end_time'         :  $('#friday_end_time').val(),

        'saturday_start_time'     :  $('#saturday_start_time').val(),
        'saturday_end_time'       :  $('#saturday_end_time').val(),
    };



    addLoading();
    $.ajax({
        url:"ajax/insert-timing.php",
        method:"post",
        data:postForm,
        dataType:"json",
        success:function(data)
        {
            hideLoading();
            alert("Timing added successfully");
           // window.location.href = url;
        }
    });
}















//ADDING DELIVERY ADDRESS

function add_delivery_address(restaurant_id,url)
{

    var area_en       =  $('#area_en').val();
    var area_he       =  $('#area_he').val();
    var fee           =  $('#fee').val();



    if (area_en == "")
    {
        $('#area_en_error').html('Required*');
        return;
    }
    if (area_he == "")
    {
        $('#area_he_error').html('Required*');
        return;
    }
    if (fee == "")
    {
        $('#fee_error').html('Required*');
        return;
    }


    var postForm = { //Fetch form data

        'restaurant_id'           :  restaurant_id,

        'area_en'       :  $('#area_en').val(),
        'area_he'       :  $('#area_he').val(),
        'fee'           :  $('#fee').val(),

    };



    addLoading();
    $.ajax({
        url:"ajax/insert_delivery_address.php",
        method:"post",
        data:postForm,
        dataType:"json",
        success:function(data)
        {
            hideLoading();
            alert("Adrress added successfully");
            window.location.href = url;
        }
    });
}
