
function delete_tag_restaurant(tag_id,url)
{
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
}
