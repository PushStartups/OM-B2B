


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





function edit_address(delivery_id,url)
//ADDING DELIVERY ADDRESS

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


        'address_id'            :    delivery_id,

        'area_en'       :  $('#area_en').val(),
        'area_he'       :  $('#area_he').val(),
        'fee'           :  $('#fee').val(),

    };



    addLoading();
    $.ajax({
        url:"ajax/edit_address.php",
        method:"post",
        data:postForm,
        dataType:"json",
        success:function(data)
        {
            hideLoading();
            alert("Adrress edited successfully");
            window.location.href = url;
        }
    });
}




function delete_delivery_address(delivery_id,url)
{
    addLoading();
    $.ajax({
        url:"ajax/delete-address.php",
        method:"post",
        data:{delivery_id:delivery_id},
        dataType:"text",
        success:function(data)
        {
            hideLoading();


            window.location.href = url;
        }
    });
}
