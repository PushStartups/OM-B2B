

$('#name_en').bind('input', function() {

    document.getElementById('name_en_error').innerHTML = "";

});

$('#name_he').bind('input', function() {

    document.getElementById('name_he_error').innerHTML = "";

});


function delete_tag(tags_id,url)
{
    $(function(){
        swal({
                title: "Are you sure?",
                text: "You will not be able to recover this!",
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
                    swal("Deleted!", "Tag has been deleted.", "success");
                    addLoading();
                    $.ajax({
                        url:"ajax/delete_tags.php",
                        method:"post",
                        data:{tags_id:tags_id},
                        dataType:"json",
                        success:function(data)
                        {
                            hideLoading();
                            alert("Tags deleted successfully");
                            window.location.href = url;
                        }
                    });
                } else {
                    swal("Cancelled", "Tag is safe :)", "error");
                    window.location.href = url;
                }
            });
    });

}



function edit_tags(tags_id,url)
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

        'tags_id'                 : tags_id,

    };

    addLoading();
    $.ajax({
        url:"ajax/edit_tag.php",
        method:"post",
        data:postForm,
        dataType:"json",
        success:function(data)
        {
            hideLoading();
            alert("Tags edited successfully");
            window.location.href = url;
        }
    });
}
