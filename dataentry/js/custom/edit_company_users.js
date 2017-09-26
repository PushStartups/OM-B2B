

$('#name').bind('input', function() {

    document.getElementById('name_error').innerHTML = "";

});

$('#smooch_id').bind('input', function() {

    document.getElementById('email_error').innerHTML = "";

});


$('#contact').bind('input', function() {

    if(!this.value.match(/^\d+$/))
    {
        document.getElementById('contact_error').innerHTML = "Wrong Number!";
    }
    else
    {
        document.getElementById('contact_error').innerHTML = "";
    }

});

$('#address').bind('input', function() {

    document.getElementById('address_error').innerHTML = "";

});




function edit_company_user(url)
{

    var name            =  $('#name').val();
    var smooch_id       =  $('#smooch_id').val();
    var password        =  $('#password').val();
    var contact         =  $('#contact').val();
    var address         =  $('#address').val();


    if (name == "")
    {
        $('#name_error').html('Required*');
        return;
    }


    if (smooch_id == "")
    {
        $('#email_error').html('Required*');
        return;
    }




    if (contact == "")
    {
        $('#contact_error').html('Required*');
        return;
    }


    if (address == "")
    {
        $('#address_error').html('Required*');
        return;
    }



    var postForm = { //Fetch form data

        'name'           :  $('#name').val(),
        'smooch_id'      :  $('#smooch_id').val(),
        'contact'        :  $('#contact').val(),
        'address'        :  $('#address').val(),

    };



    addLoading();
    $.ajax({
        url:"ajax/edit_company_users.php",
        method:"post",
        data:postForm,
        dataType:"json",
        success:function(data)
        {
            hideLoading();
            alert("User edited successfully");
            window.location.href = url;
        }
    });
}


