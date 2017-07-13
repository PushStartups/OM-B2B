

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




function add_rest_company_discount(url)
{

    var company                    =  $('#company').val();
    var restaurant                    =  $('#restaurant').val();
    var discount                    =  $('#discount').val();




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


    var postForm = { //Fetch form data


        'company'                 :  $('#company').val(),
        'restaurant'                 :  $('#restaurant').val(),
        'discount'                 :  $('#discount').val(),


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