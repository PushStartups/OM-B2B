function show_subitems_div()
{
    $("#add-subitem").show();
}



$('#name_en').bind('input', function() {

    document.getElementById('name_en_error').innerHTML = "";

});

$('#name_he').bind('input', function() {

    document.getElementById('name_he_error').innerHTML = "";

});

$('#price').bind('input', function() {

    document.getElementById('price_error').innerHTML = "";

});




function add_subitems(extra_id,url)
{

    var name_en                    =  $('#name_en').val();
    var name_he                    =  $('#name_he').val();
    var price                      =  $('#price').val();

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

    if(price == "")
    {
        $('#price_error').html('Required*');
        return;
    }


    var postForm = { //Fetch form data

        'extra_id'                 :  extra_id,

        'name_en'                 :  $('#name_en').val(),
        'name_he'                 :  $('#name_he').val(),

        'price'                   :  $('#price').val(),


    };

    addLoading();
    $.ajax({
        url:"ajax/insert_new_subitem.php",
        method:"post",
        data:postForm,
        dataType:"json",
        success:function(data)
        {
            hideLoading();
            alert("Subitems added successfully");
            window.location.href = url;
        }
    });





}