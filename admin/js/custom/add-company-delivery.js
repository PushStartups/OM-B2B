

function show_delivery_div() {
    $("#add-delivery-time").show();
}



$('#start_time').bind('input', function() {

    document.getElementById('start_time_error').innerHTML = "";

});

$('#end_time').bind('input', function() {

    document.getElementById('end_time_error').innerHTML = "";

});


function add_delivery_timing(url)
{

    var start_time                    =  $('#start_time').val();
    var end_time                    =  $('#end_time').val();



    if(start_time == "")
    {
        $('#start_time_error').html('Required*');
        return;
    }

    if(end_time == "")
    {
        $('#end_time_error').html('Required');
        return;
    }



    var postForm = { //Fetch form data


        'start_time'                 :  $('#start_time').val(),
        'end_time'                 :  $('#end_time').val(),
        'company_id'                : $('#company_id').val()


    };

    addLoading();
    $.ajax({
        url:"ajax/add_company_delivery.php",
        method:"post",
        data:postForm,
        dataType:"json",
        success:function(data)
        {
            hideLoading();
            alert("Delivery Timings added successfully");
            window.location.href = url;
        }
    });




}