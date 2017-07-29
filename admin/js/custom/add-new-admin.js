
$('#admin_email').bind('input', function() {

    if(!this.value.match(/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/))
    {
        document.getElementById('admin_email_error').innerHTML = "Wrong Email!";
        return;

    }
    else
    {
        document.getElementById('admin_email_error').innerHTML = "";
    }

});

$('#pass').bind('input', function() {

    document.getElementById('pass_error').innerHTML = "";

});

// ADD NEW TAG
function add_new_admin()
{



    var admin_email                    =  $('#admin_email').val();
    var pass                    =  $('#pass').val();
    var user_role                    =  $('#user_role').val();

    if(admin_email == "")
    {
        $('#admin_email_error').html('Required*');
        return;
    }

    if(pass == "")
    {
        $('#pass_error').html('Required*');
        return;
    }

    var postForm = { //Fetch form data

        'email'                 :  admin_email,
        'pass'                 :  pass,
        'user_role'                 :  user_role,
    };

    addLoading();
    $.ajax({
        url:"ajax/add_admin.php",
        method:"post",
        data:postForm,
        dataType:"json",
        success:function(data)
        {
            hideLoading();
            if(data == 1)
            {
                alert("Admin added successfully");
            }
            else
            {
                alert("Admin already added in our system");
            }

            window.location.href = "index.php";
        }
    });
}
