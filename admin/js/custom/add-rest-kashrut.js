function add_kashrut_tab()
{
    $("#add_kash").show();
}

$('#kashrut_name_en').bind('input', function() {

    document.getElementById('error-tag-name-en').innerHTML = "";

});

$('#kashrut_name_he').bind('input', function() {

    document.getElementById('error-tag-name-he').innerHTML = "";

});



function delete_kasrut(kashruts_id,url)
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
                    swal("Deleted!", "Kashrut has been deleted.", "success");
                    addLoading();
                    $.ajax({
                        url:"ajax/delete_rest_kashrut.php",
                        method:"post",
                        data:{kashruts_id:kashruts_id},
                        dataType:"json",
                        success:function(data)
                        {
                            hideLoading();
                            alert("kashrut deleted successfully");
                            window.location.href = url;
                        }
                    });
                } else {
                    swal("Cancelled", "kashrut is safe :)", "error");
                    window.location.href = url;
                }
            });
    });

}




function auto_hebrew_kashrut(name_en)
{

    $.ajax({
        url:"ajax/suggest_hebrew_tags.php",
        method:"post",
        data:{name_en:name_en},
        dataType:"json",
        success:function(data)
        {
            $('#kashrut_name_he').val(data);
        }
    });
}




// function add_kashrut_restaurant(restaurant_id,url)
// {
//     var name_en                    =  $('#kashrut_name_en').val();
//     var name_he                    =  $('#kashrut_name_he').val();
//
//     if(name_en == "")
//     {
//         $('#error-tag-name-en').html('Required*');
//         return;
//     }
//
//     if(name_he == "")
//     {
//         $('#error-tag-name-he').html('Required*');
//         return;
//     }
//
//     var postForm = { //Fetch form data
//
//         'name_en'                 :  name_en,
//         'name_he'                 :  name_he,
//         'restaurant_id'           :  restaurant_id
//     };
//
//
//     addLoading();
//     $.ajax({
//         url:"ajax/add_rest_kashrut.php",
//         method:"post",
//         data:postForm,
//         dataType:"json",
//         success:function(data)
//         {
//             hideLoading();
//             alert("Kashrut added successfully");
//             window.location.href = url;
//         }
//     });
// }
