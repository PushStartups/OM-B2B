function add_restaurant_tab()
{
    $("#wid-id-2").show();
}


function call(val)
{
    document.getElementById("restaurant_name").value = val;
}

function delete_company_restaurant(rest_id,company_id,url)
{

    $(function(){
        swal({
                title: "Are you sure?",
                text: "You will not be able to recover this Restaurant!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes, delete it!",
                cancelButtonText: "No, cancel please!",
                closeOnConfirm: false,
                closeOnCancel: false
            },
            function(isConfirm){
                swal("Deleted!", "Company Restaurant has been deleted.", "success");
                if (isConfirm) {
                    addLoading();
                    $.ajax({
                        url:"ajax/delete_company_restaurant.php",
                        method:"post",
                        data:{rest_id:rest_id,company_id:company_id},
                        dataType:"text",
                        success:function(data)
                        {
                            hideLoading();


                            window.location.href = url;
                        }
                    });
                } else {
                    swal("Cancelled", "Restaurant is safe :)", "error");
                    window.location.href = url;
                }
            });
    });


}

function add_company_restaurant(company_id,url)
{
    var rest_name = $("#rest_name").val();
    if(rest_name == "")
    {
        return;
    }

    addLoading();
    $.ajax({
        url:"ajax/add_new_restaurant_in_company.php",
        method:"post",
        data:{rest_name:rest_name,company_id:company_id},
        dataType:"text",
        success:function(data)
        {
            hideLoading();
            if(data == "already")
            {
                alert("restaurant already added to that company");
            }
            else {
                window.location.href = url;
            }

            // window.location.href = "companies.php";
        }
    });


}
