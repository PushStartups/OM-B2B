

$('#discount').bind('input', function() {

    if(!this.value.match(/^\d+$/))
    {
        document.getElementById('discount_error').innerHTML = "Wrong Number!";
    }

    var discount                    =  $('#discount').val();

    if(discount < 1 || discount > 99 )
    {
        document.getElementById('discount_error').innerHTML = "Only 1 to 99 is allowed";
    }

    else
    {
        document.getElementById('discount_error').innerHTML = "";
    }

});


$('#in_time_discount').bind('input', function() {

    if(!this.value.match(/^\d+$/))
    {
        document.getElementById('in_time_discount_error').innerHTML = "Wrong Number!";
    }

    var in_time_discount   =  $('#in_time_discount').val();

    if(in_time_discount < 1 || in_time_discount > 99 )
    {
        document.getElementById('in_time_discount_error').innerHTML = "Only 1 to 99 is allowed";
    }

    else
    {
        document.getElementById('in_time_discount_error').innerHTML = "";
    }

});





function edit_b2b_rest_disc(rest_id,url)
{
    var discount                    =  $('#discount').val();
    var in_time_discount                    =  $('#in_time_discount').val();


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
        url:"ajax/edit_b2b_rest_discount.php",
        method:"post",
        data:postForm,
        dataType:"json",
        success:function(data)
        {
            hideLoading();
            alert("Discount edited successfully");
            window.location.href = url;
        }
    });
}





function delete_b2b_rest_disc(rest_id,url)
{
    addLoading();
    $.ajax({
        url:"ajax/delete_b2b_rest_discount.php",
        method:"post",
        data:{rest_id:rest_id},
        dataType:"json",
        success:function(data)
        {
            hideLoading();
            alert("Discount deleted successfully");
            window.location.href = url;
        }
    });
}
