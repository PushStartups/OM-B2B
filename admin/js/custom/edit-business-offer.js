

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
    addLoading();
    $.ajax({
        url:"ajax/delete_business_offer.php",
        method:"post",
        data:{business_id:business_id},
        dataType:"json",
        success:function(data)
        {
            hideLoading();
            alert("Offer deleted successfully");
            window.location.href = url;
        }
    });
}


