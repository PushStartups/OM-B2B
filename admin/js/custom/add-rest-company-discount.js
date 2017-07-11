

$('#company').bind('input', function() {

    document.getElementById('company_error').innerHTML = "";


});

$('#restaurant').bind('input', function() {

    document.getElementById('restaurant_error').innerHTML = "";

});

$('#discount').bind('input', function() {

    if(!this.value.match(/^\d+$/))
    {
        document.getElementById('discount_error').innerHTML = "Wrong Number!";
    }

    var discount   =  $('#discount').val();

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



function add_rest_company_discount(url)
{

    var company                    =  $('#company').val();
    var restaurant                    =  $('#restaurant').val();
    var discount                    =  $('#discount').val();
    var in_time_discount                    =  $('#in_time_discount').val();



    if(company == "")
    {
        $('#company_error').html('Required*');
        return;
    }

    if(restaurant == "")
    {
        $('#restaurant_error').html('Required');
        return;
    }

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


        'company'                 :  $('#company').val(),
        'restaurant'                 :  $('#restaurant').val(),
        'discount'                 :  $('#discount').val(),
        'in_time_discount'                 :  $('#in_time_discount').val(),


    };

    addLoading();
    $.ajax({
        url:"ajax/add_rest_company_discount.php",
        method:"post",
        data:postForm,
        dataType:"json",
        success:function(data)
        {
            hideLoading();
            alert("Discount added successfully");
            window.location.href = url;
        }
    });




}