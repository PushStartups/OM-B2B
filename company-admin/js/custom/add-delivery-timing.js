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
        'end_time'                   :  $('#end_time').val()


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

function edit_b2b_rest_disc(rest_id,url)
{
    var discount                    =  $('#discount').val();
    var in_time_discount            =  $('#in_time_discount').val();


    if(discount == "" || discount < 1 || discount > 99 )
    {
        $('#discount_error').html('Correct Value Required');
        return;
    }

    if(in_time_discount == "" || in_time_discount < 1 || in_time_discount > 99 )
    {
        $('#in_time_discount_error').html('Correct Value Required');
        return;
    }




    var postForm = { //Fetch form data

        'discount_percent'                 :  $('#discount').val(),
        'in_time_discount'                 :  $('#in_time_discount').val(),



        'rest_id'                 : rest_id

    };

    addLoading();
    $.ajax({
        url:"ajax/edit_rest_discount.php",
        method:"post",
        data:postForm,
        dataType:"json",
        success:function(data)
        {
            hideLoading();
            alert("Discount edited successfully");
            window.location.href = "manage-restaurant-discounts.php";
        }
    });
}


function show_delivery_div() {
    $("#add-delivery-time").show();
}