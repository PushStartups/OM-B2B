

$('#name_en').bind('input', function() {

    document.getElementById('name_en_error').innerHTML = "";

});

$('#name_he').bind('input', function() {

    document.getElementById('name_he_error').innerHTML = "";

});




function add_new_city(url)
{

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


    };

    addLoading();
    $.ajax({
        url:"ajax/add_new_city.php",
        method:"post",
        data:postForm,
        dataType:"json",
        success:function(data)
        {
            hideLoading();
            alert("City added successfully");
            window.location.href = url;
        }
    });




}