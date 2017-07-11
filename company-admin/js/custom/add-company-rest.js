function add_restaurant_tab()
{
    $("#wid-id-2").show();
}


function delete_company_restaurant(rest_id,company_id,url)
{
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
