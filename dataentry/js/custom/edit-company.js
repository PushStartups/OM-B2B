$('#name').bind('input', function() {

    document.getElementById('error_name').innerHTML = "";

});

$('#address').bind('input', function() {

    document.getElementById('error_address').innerHTML = "";

});


$('#amount').bind('input', function() {

    if(!this.value.match(/^\d+$/))
    {
        document.getElementById('error_amount').innerHTML = "Wrong Number!";
    }
    else
    {
        document.getElementById('error_amount').innerHTML = "";
    }

});

$('#min_order').bind('input', function() {

    if(!this.value.match(/^\d+$/))
    {
        document.getElementById('error_min_ordert').innerHTML = "Wrong Number!";
    }
    else
    {
        document.getElementById('error_min_order').innerHTML = "";
    }

});





function edit_company(companies_id,urll)
{

    var company_id              = companies_id;
    var url                     =  urll;

    var address                 =  $('#address').val();
    var min_order              =  $('#min_order').val();
    var name                    =  $('#name').val();
    var amount                  =  $('#amount').val();
    var discount_type           =  $('#discount_type').val();

    var sunday_start_time       =  $('#sunday_start_time').val();
    var sunday_end_time         =  $('#sunday_end_time').val();

    var monday_start_time       =  $('#monday_start_time').val();
    var monday_end_time         =  $('#monday_end_time').val();

    var tuesday_start_time      =  $('#tuesday_start_time').val();
    var tuesday_end_time        =  $('#tuesday_end_time').val();


    var wednesday_start_time    =  $('#wednesday_start_time').val();
    var wednesday_end_time      =  $('#wednesday_end_time').val();

    var thursday_start_time     =  $('#thursday_start_time').val();
    var thursday_end_time       =  $('#thursday_end_time').val();

    var friday_start_time       =  $('#friday_start_time').val();
    var friday_end_time         =  $('#friday_end_time').val();

    var saturday_start_time     =  $('#saturday_start_time').val();
    var saturday_end_time       =  $('#saturday_end_time').val();



    if(name == "")
    {
        $('#error-name').html('Name Required*');
        return;
    }

    if(address == "")
    {
        $('#error-address').html('Address Required*');
        return;
    }
    if(min_order == "")
    {
        $('#error_min_order').html('Minimum Amount Required*');
        return;
    }
    if(discount_type == "")
    {
        $('#error-discount-type').html('Discount Type Required*');
        return;
    }
    if(amount == "")
    {
        $('#error-amount').html('Amount Required*');
        return;
    }




    if (sunday_start_time == "")
    {
        $('#error_sunday_start_time').html('Sunday Start Time Required*');
        return;
    }
    if (sunday_end_time == "")
    {
        $('#error_sunday_end_time').html('Sunday End Time Required*');
        return;
    }


    if (monday_start_time == "")
    {
        $('#error_monday_start_time').html('Monday Start Time Required*');
        return;
    }
    if (monday_end_time == "")
    {
        $('#error_monday_end_time').html('Monday End Time Required*');
        return;
    }




    if (tuesday_start_time == "")
    {
        $('#error_tuesday_start_time').html('Tuesday Start Time Required*');
        return;
    }
    if (tuesday_end_time == "")
    {
        $('#error_tuesday_end_time').html('Tuesday End Time Required*');
        return;
    }




    if (wednesday_start_time == "")
    {
        $('#error_wednesday_start_time').html('Wednesday Start Time Required*');
        return;
    }
    if (wednesday_end_time == "")
    {
        $('#error_wednesday_end_time').html('Wednesday End Time Required*');
        return;
    }



    if (thursday_start_time == "")
    {
        $('#error_thursday_start_time').html('Thursday Start Time Required*');
        return;
    }
    if (thursday_end_time == "")
    {
        $('#error_thursday_end_time').html('Thursday End Time Required*');
        return;
    }



    if (friday_start_time == "")
    {
        $('#error_friday_start_time').html('Friday Start Time Required*');
        return;
    }
    if (friday_end_time == "")
    {
        $('#error_friday_end_time').html('Friday End Time Required*');
        return;
    }



    if (saturday_start_time == "")
    {
        $('#error_saturday_start_time').html('Saturday Start Time Required*');
        return;
    }
    if (saturday_end_time == "")
    {
        $('#error_saturday_end_time').html('Saturday End Time Required*');
        return;
    }

    //alert($('#discount_type').val());

    var postForm = { //Fetch form data

        'company_id'              :  companies_id,

        'week1_id'                : $('#week1_id').val(),
        'week2_id'                : $('#week2_id').val(),
        'week3_id'                : $('#week3_id').val(),
        'week4_id'                : $('#week4_id').val(),
        'week5_id'                : $('#week5_id').val(),
        'week6_id'                : $('#week6_id').val(),
        'week7_id'                : $('#week7_id').val(),

        'address'                 :  $('#address').val(),
        'name'                    :  $('#name').val(),
        'amount'                  :  $('#amount').val(),
        'min_order'              :  $('#min_order').val(),
        'discount_type'           :  $('#discount_type').val(),

        'sunday_start_time'       :  $('#sunday_start_time').val(),
        'sunday_end_time'         :  $('#sunday_end_time').val(),

        'monday_start_time'       :  $('#monday_start_time').val(),
        'monday_end_time'         :  $('#monday_end_time').val(),

        'tuesday_start_time'      :  $('#tuesday_start_time').val(),
        'tuesday_end_time'        :  $('#tuesday_end_time').val(),

        'wednesday_start_time'    :  $('#wednesday_start_time').val(),
        'wednesday_end_time'      :  $('#wednesday_end_time').val(),

        'thursday_start_time'     :  $('#thursday_start_time').val(),
        'thursday_end_time'       :  $('#thursday_end_time').val(),

        'friday_start_time'       :  $('#friday_start_time').val(),
        'friday_end_time'         :  $('#friday_end_time').val(),

        'saturday_start_time'     :  $('#saturday_start_time').val(),
        'saturday_end_time'       :  $('#saturday_end_time').val(),
    };



    addLoading();
    $.ajax({
        url:"ajax/edit_company.php",
        method:"post",
        data:postForm,
        dataType:"json",
        success:function(data)
        {
            hideLoading();
            alert("company edited successfully");
            window.location.href = urll;
        }
    });
}


