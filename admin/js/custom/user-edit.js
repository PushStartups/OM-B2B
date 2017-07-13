$('#name').bind('input', function() {

    document.getElementById('error_name').innerHTML = "";

});

$('#address').bind('input', function() {

    document.getElementById('error_address').innerHTML = "";

});

$('#contact').bind('input', function() {

    if(!this.value.match(/^\d+$/))
    {
        document.getElementById('error_contact').innerHTML = "Wrong Number!";
        return;
    }
    else
    {
        document.getElementById('error_contact').innerHTML = "";
    }

});


$('#smooch_id').bind('input', function() {

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










function edit_user(url)
{

    var name            =  $('#name').val();
    var smooch_id       =  $('#smooch_id').val();
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

        'users_id'     :  $('#users_id').val(),
        'name'           :  $('#name').val(),
        'smooch_id'      :  $('#smooch_id').val(),
        'contact'        :  $('#contact').val(),
        'address'        :  $('#address').val(),

    };



    addLoading();
    $.ajax({
        url:"ajax/user_edit.php",
        method:"post",
        data:postForm,
        dataType:"json",
        success:function(data)
        {
            hideLoading();
            alert("User Edited successfully");
            window.location.href = "manage-users.php";
        }
    });
}
