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

function delete_subitem(subitem_id,url)
{

    $(function(){
        swal({
                title: "Are you sure?",
                text: "You will not be able to recover this Subitem!",
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
                    swal("Deleted!", "Subitem has been deleted.", "success");
                    addLoading();
                    $.ajax({
                        url:"ajax/delete_subitem.php",
                        method:"post",
                        data:{subitem_id:subitem_id},
                        dataType:"json",
                        success:function(data)
                        {
                            hideLoading();
                            window.location.href = url;
                        }
                    });
                } else {
                    swal("Cancelled", "Subitem is safe :)", "error");
                    window.location.href = url;
                }
            });
    });


}


function edit_subitems(subitem_id,url)
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

        'subitem_id'              :  subitem_id,

        'name_en'                 :  $('#name_en').val(),
        'name_he'                 :  $('#name_he').val(),

        'price'                   :  $('#price').val(),


    };

    addLoading();
    $.ajax({
        url:"ajax/edit_subitem.php",
        method:"post",
        data:postForm,
        dataType:"json",
        success:function(data)
        {
            hideLoading();
            alert("Subitem added successfully");
            window.location.href = url;
        }
    });





}