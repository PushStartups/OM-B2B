$('#card_no').bind('input', function() {

    document.getElementById('card_error').innerHTML = "";

});

$('#cvv').bind('input', function() {

    document.getElementById('cvv_error').innerHTML = "";

});

$('#expiry_year').bind('input', function() {

    document.getElementById('expiry_year_error').innerHTML = "";

});


$('#expiry_month').bind('input', function() {

    document.getElementById('expiry_month_error').innerHTML = "";

});

function delete_card(id,url)
{
    $(function(){
        swal({
                title: "Are you sure?",
                text: "You will not be able to recover this Credit Card",
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

                    $.ajax({
                        url:"ajax/delete_credit_card.php",
                        method:"post",
                        data:{id:id},
                        dataType:"json",
                        success:function(data)
                        {
                            window.location.href  =  url;
                        }
                    });
                } else {
                    swal("Cancelled", "Credit Card is safe :)", "error");
                    window.location.href = url;
                }
            });
    });

}

function add_card(user_id,url)
{
    var card_no                    =    $('#card_no').val();
    var cvv                        =    $('#cvv').val();
    var expiry_year                =    $('#expiry_year').val();
    var expiry_month               =    $('#expiry_month').val();



    if((!$('#card_no').val().match(/^\d+$/)))
    {
        if($("#card_no").val() != '') {

            $('#card_error').html('Invalid Card No*');
            return;
        }

    }

    if(card_no == "")
    {
        $('#card_error').html('Card No Required*');
        return;
    }



    if(expiry_month == "")
    {
        $('#expiry_month_error').html('*Card Expiry Date Month (MM) Required');
        return;
    }

    if(expiry_year == "")
    {
        $('#expiry_year_error').html('*Card Expiry Date Year (YY) Required');
        return;
    }
    if(cvv == "")
    {
        $('#cvv_error').html('CVV Required');
        return;
    }

    var postForm = { //Fetch form data

        'card_no'              :      card_no,
        'cvv'                  :      cvv,
        'expiry_year'          :      expiry_year,
        'expiry_month'         :      expiry_month

    };

    $.ajax({
        url:"ajax/add_credit_card.php",
        method:"post",
        data:postForm,
        dataType:"json",
        success:function(data)
        {
            if(data == "success")
            {
                window.location.href  =  url;
            }
            else
            {
                alert("Something went wrong with your card");
            }

        }
    });
}

function show_card_div()
{
    $("#add-card").show();
}
