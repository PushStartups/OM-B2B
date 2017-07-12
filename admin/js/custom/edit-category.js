

$('#name_en').bind('input', function() {

    document.getElementById('name_en_error').innerHTML = "";

});

$('#name_he').bind('input', function() {

    document.getElementById('name_he_error').innerHTML = "";

});

function delete_category(category_id,url)
{

    $(function(){
        swal({
                title: "Are you sure?",
                text: "You will not be able to recover this Category!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes, delete it!",
                cancelButtonText: "No, cancel please!",
                closeOnConfirm: false,
                closeOnCancel: false
            },
            function(isConfirm){
                if (isConfirm) {
                    addLoading();
                    $.ajax({
                        url:"ajax/delete_category.php",
                        method:"post",
                        data:{category_id:category_id},
                        dataType:"json",
                        success:function(data)
                        {
                            hideLoading();
                           // alert("Category deleted successfully");
                            window.location.href = url;
                        }
                    });
                } else {
                    swal("Cancelled", "Category is safe :)", "error");
                    window.location.href = url;
                }
            });
    });





}


function edit_category(category_id,url)
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
        'image_url'               :  globalEditCategoryLogo,

        'is_discount'             :  0,

        'business_offer'          :  $('#business_offer').val(),

        'category_id'                 :   category_id

    };
    //alert(globalEditCategoryLogo);
    addLoading();
    $.ajax({
        url:"ajax/edit_category.php",
        method:"post",
        data:postForm,
        dataType:"json",
        success:function(data)
        {
            hideLoading();
            alert("Category updated successfully");

        }
    });
}
