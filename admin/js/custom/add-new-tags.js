function add_tag_tab()
{
    $("#wid-id-2").show();
}

$('#tag_name_en').bind('input', function() {

    document.getElementById('error-tag-name-en').innerHTML = "";

});

$('#tag_name_he').bind('input', function() {

    document.getElementById('error-tag-name-he').innerHTML = "";

});

function auto_hebrew_tags(name_en)
{

    $.ajax({
        url:"ajax/suggest_hebrew_tags.php",
        method:"post",
        data:{name_en:name_en},
        dataType:"json",
        success:function(data)
        {
            $('#tag_name_he').val(data);
        }
    });
}




function add_tag_restaurant(restaurant_id,url)
{
    var name_en                    =  $('#tag_name_en').val();
    var name_he                    =  $('#tag_name_he').val();

    if(name_en == "")
    {
        $('#error-tag-name-en').html('Required*');
        return;
    }

    if(name_he == "")
    {
        $('#error-tag-name-he').html('Required*');
        return;
    }

    var postForm = { //Fetch form data

        'name_en'                 :  name_en,
        'name_he'                 :  name_he,
        'restaurant_id'           :  restaurant_id
    };


    addLoading();
    $.ajax({
        url:"ajax/insert_new_tags.php",
        method:"post",
        data:postForm,
        dataType:"json",
        success:function(data)
        {
            hideLoading();
            alert("Tags added successfully");
            window.location.href = url;
        }
    });
}

$('#name_en').bind('input', function() {

    document.getElementById('name_en_error').innerHTML = "";

});

$('#name_he').bind('input', function() {

    document.getElementById('name_he_error').innerHTML = "";

});

// ADD NEW TAG
function add_new_tag()
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
        url:"ajax/insert_new_tags_only.php",
        method:"post",
        data:postForm,
        dataType:"json",
        success:function(data)
        {
            hideLoading();
            if(data == 1)
            {
                alert("Tags added successfully");
            }
            else
            {
                alert("Tags already added in our system");
            }

            window.location.href = "tags.php";
        }
    });
}
