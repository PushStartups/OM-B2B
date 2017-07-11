function add_new_user(url)
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

        'company_id'     :  $('#company_id').val(),
        'name'           :  $('#name').val(),
        'smooch_id'      :  $('#smooch_id').val(),
        'contact'        :  $('#contact').val(),
        'address'        :  $('#address').val(),

    };



    addLoading();
    $.ajax({
        url:"ajax/insert_new_user.php",
        method:"post",
        data:postForm,
        dataType:"json",
        success:function(data)
        {
            hideLoading();
            alert("User added successfully");
            window.location.href = "manage-users.php";
        }
    });
}
