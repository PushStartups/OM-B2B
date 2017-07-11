function show_add_new_user()
{
    $("#add-users").show();
}



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
            window.location.href = url;
        }
    });
}
















function add_user_tab()
{
    $("#wid-id-2").show();
}

function delete_company_user(email,company_id,url)
{
    addLoading();
    $.ajax({
        url:"ajax/delete_company_user.php",
        method:"post",
        data:{email:email,company_id:company_id},
        dataType:"text",
        success:function(data)
        {
            hideLoading();


           // window.location.href = url;
        }
    });
}
