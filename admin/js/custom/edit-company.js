$('#name').bind('input', function() {

    document.getElementById('error_name').innerHTML = "";

});

$('#area_en').bind('input', function() {

    document.getElementById('error_address').innerHTML = "";

});



$('#payment_method').bind('input', function() {

    document.getElementById('error_payment_method').innerHTML = "";

});

$('#team_size').bind('input', function() {

    document.getElementById('error_team_size').innerHTML = "";

});

$('#ordering_deadline_time').bind('input', function() {

    document.getElementById('error_ordering_deadline_time').innerHTML = "";

});

$('#delivery_time').bind('input', function() {

    document.getElementById('error_delivery_time').innerHTML = "";

});

$('#company_address').bind('input', function() {

    document.getElementById('error_company_address').innerHTML = "";

});


$('#contact_name').bind('input', function() {

    document.getElementById('error_contact_name').innerHTML = "";

});

$('#contact_number').bind('input', function() {

    if(!this.value.match(/^\d+$/))
    {
        document.getElementById('error_contact_number').innerHTML = "Wrong Number!";
    }
    else
    {
        document.getElementById('error_contact_number').innerHTML = "";
    }

});


$('#limit_of_restaurants').bind('input', function() {

    if(!this.value.match(/^\d+$/))
    {
        document.getElementById('error_limit_of_restaurants').innerHTML = "Wrong Number!";
    }
    else
    {
        document.getElementById('error_limit_of_restaurants').innerHTML = "";
    }

});



$('#contact_email').bind('input', function() {

    if(!this.value.match(/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/))
    {
        document.getElementById('error_contact_email').innerHTML = "Wrong Email!";
        return;

    }
    else
    {
        document.getElementById('error_contact_email').innerHTML = "";
    }

});

$('#ledger_link').bind('input', function() {

    document.getElementById('error_ledger_link').innerHTML = "";

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

$('#email').bind('input', function() {

    if(!this.value.match(/^\w+@[a-zA-Z_]+?\.[a-zA-Z]{2,3}$/))
    {
        document.getElementById('error_email').innerHTML = "Wrong Email!";

        return;
    }
    else
    {
        document.getElementById('error_email').innerHTML = "";
    }

});


$('#password').bind('input', function() {

    document.getElementById('error_password').innerHTML = "";

});


function delete_company(company_id)
{
    addLoading();
    $.ajax({
        url:"ajax/delete_company.php",
        method:"post",
        data:{company_id:company_id},
        dataType:"json",
        success:function(data)
        {
            hideLoading();
            alert("Company deleted successfully");
            window.location.href = "companies.php";
        }
    });
}




function edit_company(companies_id,urll)
{

    var company_id              = companies_id;
    var url                     =  urll;

    var address                 =  $('#area_en').val();
    var min_order              =  $('#min_order').val();
    var name                    =  $('#name').val();
    var amount                  =  $('#amount').val();
    var discount_type           =  $('#discount_type').val();
    var email                    =  $('#email').val();
    var password                    =  $('#password').val();

    var payment_method                  =  $('#payment_method').val();
    var team_size                  =  $('#team_size').val();
    var ordering_deadline_time                  =  $('#ordering_deadline_time').val();
    var delivery_time                  =  $('#delivery_time').val();
    var limit_of_restaurants                  =  $('#limit_of_restaurants').val();
    var company_address                  =  $('#company_address').val();
    var contact_name                  =  $('#contact_name').val();
    var contact_number                  =  $('#contact_number').val();
    var contact_email                  =  $('#contact_email').val();
    var ledger_link                  =  $('#ledger_link').val();


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
        $('#error_name').html('Name Required*');
        return;
    }

    if($('#lat').val() == "")
    {
        $('#error_address').html('Please Use Suggesstions*');
        return;
    }
    if(address == "")
    {
        $('#error_address').html('Address Required*');
        return;
    }

    if(limit_of_restaurants == "")
    {
        $('#error_limit_of_restaurants').html('Required*');
        return;
    }

    if(payment_method == "")
    {
        $('#error_payment_method').html('Required*');
        return;
    }

    if(team_size == "")
    {
        $('#error_team_size').html('Required*');
        return;
    }

    if(ordering_deadline_time == "")
    {
        $('#error_ordering_deadline_time').html('Required*');
        return;
    }

    if(delivery_time == "")
    {
        $('#error_delivery_time').html('Required*');
        return;
    }


    if(company_address == "")
    {
        $('#error_company_address').html('Required*');
        return;
    }

    if(contact_name == "")
    {
        $('#error_contact_name').html('Required*');
        return;
    }

    if(contact_number == "")
    {
        $('#error_contact_number').html('Required*');
        return;
    }

    if(contact_email == "")
    {
        $('#error_contact_email').html('Required*');
        return;
    }

    if(ledger_link == "")
    {
        $('#error_ledger_link').html('Required*');
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
        $('#error_amount').html('Amount Required*');
        return;
    }


    if(email == "")
    {
        $('#error_email').html('Email Required*');
        return;
    }


    if(password == "")
    {
        $('#error_password').html('Password Required*');
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
        'address'                 :  $('#area_en').val(),
        'name'                    :  $('#name').val(),
        'amount'                  :  $('#amount').val(),


        'payment_method'                  :  $('#payment_method').val(),
        'team_size'                  :  $('#team_size').val(),
        'ordering_deadline_time'                  :  $('#ordering_deadline_time').val(),
        'delivery_time'                  :  $('#delivery_time').val(),
        'limit_of_restaurants'                  :  $('#limit_of_restaurants').val(),
        'company_address'                  :  $('#company_address').val(),
        'contact_name'                  :  $('#contact_name').val(),
        'contact_number'                  :  $('#contact_number').val(),
        'contact_email'                  :  $('#contact_email').val(),
        'ledger_link'                  :  $('#ledger_link').val(),


        'min_order'               :  $('#min_order').val(),
        'discount_type'           :  $('#discount_type').val(),
        'email'                   :  $('#email').val(),
        'password'                :  $('#password').val(),

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
        'lat'       :  $('#lat').val(),
        'lng'       :  $('#lng').val(),
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


