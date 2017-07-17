
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


$('#compnay_email').bind('input', function() {

    if(!this.value.match(/^\w+@[a-zA-Z_]+?\.[a-zA-Z]{2,3}$/))
    {
        document.getElementById('error_compnay_email').innerHTML = "Wrong Email!";

        return false;
    }
    else
    {
        document.getElementById('error_compnay_email').innerHTML = "";
    }

});


$('#company_password').bind('input', function() {

    document.getElementById('error_company_password').innerHTML = "";

});



$('#company_deadline_time').bind('input', function() {

    if(!this.value.match(/^([0-1]?[0-9]|2[0-3])(:[0-5][0-9])?$/))
    {
        document.getElementById('error_company_deadline_time').innerHTML = "Match Format HH:MM";

        return false;
    }
    else
    {
        document.getElementById('error_company_deadline_time').innerHTML = "";

    }

});



function edit_company(companies_id,urll)
{

    var registered_company_number                    =  $('#registered_company_number').val();
    var email                    =  $('#compnay_email').val();
    var password                    =  $('#company_password').val();

    var contact_name                  =  $('#contact_name').val();
    var contact_number                  =  $('#contact_number').val();
    var contact_email                  =  $('#contact_email').val();

    var company_deadline_time          = $('#company_deadline_time').val();


    if(registered_company_number == "")
    {
        $('#error_registered_company_number').html('Required*');
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



    if(email == "")
    {
        $('#error_compnay_email').html('Email Required*');
        return;
    }


    if(password == "")
    {
        $('#error_company_password').html('Password Required*');
        return;
    }


    if(company_deadline_time == "")
    {
        $('#error_company_deadline_time').html('Required*');
        return;
    }



    //alert($('#discount_type').val());

    var postForm = { //Fetch form data

        'company_id'              :  companies_id,

        'registered_company_number'                    :  $('#registered_company_number').val(),

        'contact_name'                  :  $('#contact_name').val(),
        'contact_number'                  :  $('#contact_number').val(),
        'contact_email'                  :  $('#contact_email').val(),

        'email'                   :  $('#compnay_email').val(),
        'password'                :  $('#company_password').val(),

        'company_deadline_time'                :  $('#company_deadline_time').val(),
        'week_en'                :  $('#week_en').val(),

    };



    addLoading();
    $.ajax({
        url:"ajax/company_detail.php",
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


$('#company_deadline_time').on('change', function(){


    var timee = document.getElementById("company_deadline_time").value;

        $.ajax({
        url:"ajax/calc_delivery_time.php",
        method:"post",
        data:{timee:timee} ,
        dataType:"json",
        success:function(data)
        {
            $("#delivery_time").val(data);
        }
    });
});


