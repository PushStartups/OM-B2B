$('#name').bind('input', function() {

    document.getElementById('error_name').innerHTML = "";

});

$('#registered_company_number').bind('input', function() {

    if(!this.value.match(/^\d+$/))
    {
        document.getElementById('error_registered_company_number').innerHTML = "Wrong Number!";
        return;
    }
    else
    {
        document.getElementById('error_registered_company_number').innerHTML = "";
    }

});

$('#area_en').bind('input', function() {

    document.getElementById('error_address').innerHTML = "";

});


$('#team_size').bind('input', function() {

    if(!this.value.match(/^\d+$/))
    {
        document.getElementById('error_team_size').innerHTML = "Wrong Number!";
    }
    else
    {
        document.getElementById('error_team_size').innerHTML = "";
    }

});

$('#contact_name').bind('input', function() {

    document.getElementById('error_contact_name').innerHTML = "";

});

$('#contact_number').bind('input', function() {

    if(!this.value.match(/^\d+$/))
    {
        document.getElementById('error_contact_number').innerHTML = "Wrong Number!";
        return;
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
        return;
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

    if(!this.value.match(/^(http[s]?:\/\/){0,1}(www\.){0,1}[a-zA-Z0-9\.\-]+\.[a-zA-Z]{2,5}[\.]{0,1}/))
    {
        document.getElementById('error_ledger_link').innerHTML = "Wrong URL";
        return;
    }
    else
    {
        document.getElementById('error_ledger_link').innerHTML = "";
    }

});


$('#amount').bind('input', function() {

    if(!this.value.match(/^\d+$/))
    {
        document.getElementById('error_amount').innerHTML = "Wrong Number!";
        return;
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
        return;
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
//
// $('#notes').bind('input', function() {
//
//     document.getElementById('error_notes').innerHTML = "";
//
// });


$('#delivery_time').bind('input', function() {

    if(!this.value.match(/^([0-1]?[0-9]|2[0-3])(:[0-5][0-9])?$/))
    {
        document.getElementById('error_delivery_time').innerHTML = "Match Format HH:MM";

        return false;
    }
    else
    {
        document.getElementById('error_delivery_time').innerHTML = "";

    }

});


function delete_company(company_id)
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
                    swal("Deleted!", "Company has been deleted.", "success");
                    addLoading();
                    $.ajax({
                        url:"ajax/delete_company.php",
                        method:"post",
                        data:{company_id:company_id},
                        dataType:"json",
                        success:function(data)
                        {
                            hideLoading();
                            window.location.href = "companies.php";
                        }
                    });
                } else {
                    swal("Cancelled", "Company is safe :)", "error");
                    window.location.href = url;
                }
            });
    });

}




function edit_company(companies_id,urll)
{

    var company_id              = companies_id;
    var url                     =  urll;

    var address                 =  $('#area_en').val();
    var min_order              =  $('#min_order').val();
    var name                    =  $('#name').val();
    var registered_company_number                    =  $('#registered_company_number').val();
    var amount                  =  $('#amount').val();
    var discount_type           =  $('#discount_type').val();
    var email                    =  $('#email').val();
    var password                    =  $('#password').val();
    //var notes                    =  document.getElementById("notes").value;

    var team_size                  =  $('#team_size').val();
    var limit_of_restaurants                  =  $('#limit_of_restaurants').val();
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

    var delivery_time            = $('#delivery_time').val();


    if(name == "")
    {
        $('#error_name').html('Name Required*');
        return;
    }
    if(registered_company_number == "")
    {
        $('#error_registered_company_number').html('Required*');
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

    if(team_size == "")
    {
        $('#error_team_size').html('Required*');
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

    // if(notes == "")
    // {
    //     $('#error_notes').html('Required*');
    //     return;
    // }



    if(delivery_time == "")
    {
        $('#error_delivery_time').html('Required*');
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

    var del_charges = 0;
    if($('#company_delivery_option').val() == '1')
    {
        del_charges = $("#d_charges").val();
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
        'registered_company_number'                    :  $('#registered_company_number').val(),
        'amount'                  :  $('#amount').val(),


        'team_size'                  :  $('#team_size').val(),
        'limit_of_restaurants'                  :  $('#limit_of_restaurants').val(),
        'contact_name'                  :  $('#contact_name').val(),
        'contact_number'                  :  $('#contact_number').val(),
        'contact_email'                  :  $('#contact_email').val(),
        'ledger_link'                  :  $('#ledger_link').val(),


        'min_order'               :  $('#min_order').val(),
        'discount_type'           :  $('#discount_type').val(),
        'email'                   :  $('#email').val(),
        'password'                :  $('#password').val(),

        'notes'                   :  document.getElementById("notes").value,

        'sunday_start_time'       :  $('#sunday_start_time').val(),
        'sunday_end_time'         :  $('#sunday_end_time').val(),

        'monday_start_time'       :  $('#monday_start_time').val(),
        'monday_end_time'         :  $('#monday_end_time').val(),
        'monday_delivery_time'    :  $('#monday_delivery_timing').val(),
        'monday_food_ready_for_pickup'    :  $('#monday_food_ready_for_pickup').val(),

        'tuesday_start_time'      :  $('#tuesday_start_time').val(),
        'tuesday_end_time'        :  $('#tuesday_end_time').val(),
        'tuesday_delivery_time'    :  $('#tuesday_delivery_timing').val(),
        'tuesday_food_ready_for_pickup'    :  $('#tuesday_food_ready_for_pickup').val(),

        'wednesday_start_time'    :  $('#wednesday_start_time').val(),
        'wednesday_end_time'      :  $('#wednesday_end_time').val(),
        'wednesday_delivery_time'    :  $('#wednesday_delivery_timing').val(),
        'wednesday_food_ready_for_pickup'    :  $('#wednesday_food_ready_for_pickup').val(),

        'thursday_start_time'     :  $('#thursday_start_time').val(),
        'thursday_end_time'       :  $('#thursday_end_time').val(),
        'thursday_delivery_time'    :  $('#thursday_delivery_timing').val(),
        'thursday_food_ready_for_pickup'    :  $('#thursday_food_ready_for_pickup').val(),

        'friday_start_time'       :  $('#friday_start_time').val(),
        'friday_end_time'         :  $('#friday_end_time').val(),
        'friday_delivery_time'    :  $('#friday_delivery_timing').val(),
        'friday_food_ready_for_pickup'    :  $('#friday_food_ready_for_pickup').val(),

        'saturday_start_time'     :  $('#saturday_start_time').val(),
        'saturday_end_time'       :  $('#saturday_end_time').val(),
        'saturday_delivery_time'    :  $('#saturday_delivery_timing').val(),
        'saturday_food_ready_for_pickup'    :  $('#saturday_food_ready_for_pickup').val(),

        'lat'                     :  $('#lat').val(),
        'lng'                     :  $('#lng').val(),

        'week_en'                 :  $('#week_en').val(),
        'delivery_time'           :  $('#delivery_time').val(),
        'company_delivery_option' :  $('#company_delivery_option').val(),
        'delivery_charge'         :    del_charges,
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


