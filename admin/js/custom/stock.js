

function delete_stock(stock_id,urll)
{
    $(function() {
        swal({
                title: "Are you sure?",
                text: "You will not be able to recover this Stock file!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes, delete it!",
                cancelButtonText: "No, cancel please!",
                closeOnConfirm: false,
                closeOnCancel: false
            },
            function (isConfirm) {
                if (isConfirm) {
                    swal("Deleted!", "File has been deleted.", "success");
                    addLoading();
                    $.ajax({
                        url: "ajax/delete_stock_file.php",
                        method: "post",
                        data: {id: stock_id},
                        dataType: "json",
                        success: function (data) {
                            hideLoading();
                            //alert("Restaurant deleted successfully");
                            window.location.href = urll;
                        }
                    });
                } else {
                    swal("Cancelled", "File is safe :)", "error");
                    window.location.href = urll;
                }
            });
    });
}