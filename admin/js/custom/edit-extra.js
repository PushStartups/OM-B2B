function show_addons_div()
{
    $("#add-choices").show();
}


$('#name_en').bind('input', function() {

    document.getElementById('name_en_error').innerHTML = "";

});

$('#name_he').bind('input', function() {

    document.getElementById('name_he_error').innerHTML = "";

});

$('#limit').bind('input', function() {

    document.getElementById('limit_error').innerHTML = "";

});

function delete_extras(extra_id,url)
{
    addLoading();
    $.ajax({
        url:"ajax/delete_extra.php",
        method:"post",
        data:{extra_id:extra_id},
        dataType:"json",
        success:function(data)
        {
            hideLoading();
            alert("Extras deleted successfully");
            window.location.href = url;
        }
    });

}

function update_extras(extra_id,url)
{

    var name_en                    =  $('#name_en').val();
    var name_he                    =  $('#name_he').val();
    var limit                    =  $('#limit').val();


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

    if(limit == "")
    {
        $('#limit_error').html('Required*');
        return;
    }


    var postForm = { //Fetch form data

        'extra_id'                :  extra_id,

        'name_en'                 :  $('#name_en').val(),
        'name_he'                 :  $('#name_he').val(),

        'limit'                   :  $('#limit').val(),

        'type'                    :  $('#type').val(),

        'price_replace'           :  $('#price_replace').val(),

    };

    addLoading();
    $.ajax({
        url:"ajax/edit_extras.php",
        method:"post",
        data:postForm,
        dataType:"json",
        success:function(data)
        {
            hideLoading();
            alert("Extras updated successfully");
            window.location.href = url;
        }
    });




}