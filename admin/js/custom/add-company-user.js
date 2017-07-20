function show_add_new_user()
{
    $("#add-users").show();
}



$('#name').bind('input', function() {

    document.getElementById('name_error').innerHTML = "";

});

$('#smooch_id').bind('input', function() {

    if(!this.value.match(/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/))
    {
        document.getElementById('email_error').innerHTML = "Wrong Email!";
        return;

    }
    else
    {
        document.getElementById('email_error').innerHTML = "";
    }

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
    var company_id      =  $('#company_id').val();


    if(company_id == null)
    {
        alert("Please Select Company");
        return;
    }
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






function delete_user_db(user_id,url)
{



    $(function(){
        swal({
                title: "Are you sure?",
                text: "You will not be able to recover this User!",
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
                    swal("Deleted!", "User has been deleted.", "success");
                    addLoading();
                    $.ajax({
                        url:"ajax/delete_b2b_users.php",
                        method:"post",
                        data:{user_id:user_id},
                        dataType:"json",
                        success:function(data)
                        {
                            hideLoading();
                            window.location.href = url ;
                        }
                    });
                } else {
                    swal("Cancelled", "User is safe :)", "error");
                    window.location.href = url;
                }
            });
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
