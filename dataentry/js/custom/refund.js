function refend_amount(total,order_id,url,transaction_id){
    var refund_amount = "";
    refund_amount = parseInt(document.getElementById("refund").value);
    console.log("REFUND : "+refund_amount);
    console.log("TOTAL : "+total);

    if(parseInt(refund_amount) > parseInt(total))
    {
        $("#refund_message").html("Refund Amount is greater than Total Amount.");
        return false;
    }
    else if(parseInt(refund_amount) <= parseInt(total))
    {
        addLoading();
        $.ajax({
            url:"ajax/refund_amount.php",
            method:"post",
            data:{refund_amount:refund_amount,order_id:order_id,transaction_id:transaction_id},
            dataType:"json",
            success:function(data)
            {
                hideLoading();
                if(data == "success")
                {
                    alert("Refund Successful");
                }
                else{
                    alert("Refund Fail");
                }
                window.location.href = url;
            }
        });
    }
    else{
        $("#refund_message").html("Please enter valid amount");
        return false;
    }
}
function refend_amount_b2b(total,order_id,url,transaction_id){
    var refund_amount = "";
    refund_amount = parseInt(document.getElementById("refund").value);
    console.log("REFUND : "+refund_amount);
    console.log("TOTAL : "+total);

    if(parseInt(refund_amount) > parseInt(total))
    {
        $("#refund_message").html("Refund Amount is greater than Total Amount.");
        return false;
    }
    else if(parseInt(refund_amount) <= parseInt(total))
    {
        addLoading();
        $.ajax({
            url:"ajax/refund_amount_b2b.php",
            method:"post",
            data:{refund_amount:refund_amount,order_id:order_id,transaction_id:transaction_id},
            dataType:"json",
            success:function(data)
            {
                hideLoading();
                if(data == "success")
                {
                    alert("Refund Successful");
                }
                else{
                    alert("Refund Fail");
                }
                window.location.href = url;
            }
        });
    }
    else{
        $("#refund_message").html("Please enter valid amount");
        return false;
    }
}
function cancel_order_b2b(total,order_id,url,transaction_id)
{
    if(total <= 0)
    {
        alert("Total Amount is 0");
        return;
    }
    var refund_amount = total;

    addLoading();
    $.ajax({
        url:"ajax/cancel_amount_b2b.php",
        method:"post",
        data:{refund_amount:parseInt(refund_amount),order_id:order_id,transaction_id:transaction_id},
        dataType:"json",
        success:function(data)
        {
            hideLoading();
            if(data == "success")
            {
                alert("Refund Successful");
            }
            else{
                alert("Refund Fail");
            }
            window.location.href = url;
        }
    });
}
function cancel_order(total,order_id,url,transaction_id)
{
    if(total <= 0)
    {
        alert("Total Amount is 0");
        return;
    }
    var refund_amount = total;

    addLoading();
    $.ajax({
        url:"ajax/cancel_amount.php",
        method:"post",
        data:{refund_amount:parseInt(refund_amount),order_id:order_id,transaction_id:transaction_id},
        dataType:"json",
        success:function(data)
        {
            hideLoading();
            if(data == "success")
            {
                alert("Refund Successful");
            }
            else{
                alert("Refund Fail");
            }
            window.location.href = url;
        }
    });
}

$('#refund').bind('input', function() {
    document.getElementsByClassName("refund_message")[0].innerHTML = "";

});
