

function delete_tag_restaurant(tag_id,url)
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
                        url:"ajax/delete_tag_restaurant.php",
                        method:"post",
                        data:{tag_id:tag_id
                        },
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

