
$('#name_en').bind('input', function() {

    document.getElementById('name_en_error').innerHTML = "";

});

$('#name_he').bind('input', function() {

    document.getElementById('name_he_error').innerHTML = "";

});

// ADD NEW TAG
function add_new_kashrut()
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
        $('#name_he_error').html('Required*');
        return;
    }

    var postForm = { //Fetch form data

        'name_en'                 :  name_en,
        'name_he'                 :  name_he,
    };

    addLoading();
    $.ajax({
        url:"ajax/add_kashrut.php",
        method:"post",
        data:postForm,
        dataType:"json",
        success:function(data)
        {
            hideLoading();
            if(data == 1)
            {
                alert("Kashrut added successfully");
            }
            else
            {
                alert("Kashrut already added in our system");
            }

            window.location.href = "kashrut.php";
        }
    });
}
