

function edit_business_offer(business_id,url)
{



    var postForm = { //Fetch form data

        // 'category_id'   : $('#business_category').val(),
        'item_id'       : $('#business_item').val(),
        'day'           : $('#day').val(),
        'week_cycle'    : $('#week_cycle').val(),

        'business_id'   : business_id

    };

    addLoading();
    $.ajax({
        url:"ajax/edit_business_offer.php",
        method:"post",
        data:postForm,
        dataType:"json",
        success:function(data)
        {
            hideLoading();
            alert("Business Offer edited successfully");
            window.location.href = url;
        }
    });
}


function delete_business_offer(business_id,url)
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
                    swal("Deleted!", "Offer has been deleted.", "success");
                    addLoading();
                    $.ajax({
                        url:"ajax/delete_business_offer.php",
                        method:"post",
                        data:{business_id:business_id},
                        dataType:"json",
                        success:function(data)
                        {
                            hideLoading();
                            window.location.href = url;
                        }
                    });
                } else {
                    swal("Cancelled", "Offer is safe :)", "error");
                    window.location.href = url;
                }
            });
    });

}

