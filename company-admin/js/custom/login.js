
$('#signin').on('click', function () {

    var email = $("#adminUser").val();
    var password = $("#adminPassword").val();

    $.ajax({
        url:"ajax/login.php",
        method:"post",
        data:{email:email,password:password},
        dataType:"json",
        success:function(data)
        {
            if(data == "false")
            {
                alert("credentials not found");
            }
            else
            {
                window.location.href="index.php";
            }
        }
    });

});


$('#submit').on('click', function () {

    var email = $("#forget-email").val();
    if(email == "")
    {
        return;
    }
    $.ajax({
        url:"ajax/forget-password.php",
        method:"post",
        data:{email:email},
        dataType:"json",
        success:function(data)
        {
            if(data == "false")
            {
                alert("email not found");
            }
            else
            {
                alert("Please check your email");
                window.location.href="login.php";
            }
        }
    });

});

function reset_password(user_id)
{
    var password        =   $("#password").val();
    var retype_password =   $("#retype-password").val();
    $('#error-password-retype').html('');
    $('#error-password').html('');

    if(password == "")
    {
        $('#error-password').html('Password Required*');
        return;
    }
    if(retype_password == "")
    {
        $('#error-password-retype').html('Password Required*');
        return;
    }

    if(password != retype_password)
    {
        $('#error-password-retype').html('Password Do Not Match*');
        return;
    }


    $.ajax({
        url:"ajax/reset-password.php",
        method:"post",
        data:{user_id:user_id,password:password},
        dataType:"json",
        success:function(data)
        {
            if(data == "success")
            {
                alert("Your New Password Has Been Saved");
                window.location.href="index.php";

            }
            else
            {
                alert("Error occured, Please try again");
            }
        }
    });
    
}

function show_forgot_field()
{
    $("#txt-email").hide();
    $("#show-forget-fields").show();

}

function show_login_field()
{
    $("#txt-email").show();
    $("#show-forget-fields").hide();
}

function addLoading()
{
    $("#Loader_bg").css("display" , "block");
    $("#loader").css("display" , "block");
}




// HIDE LOADING ON AJAX CALLS
function hideLoading(){

    setTimeout(function() {

        $("#loader").css("display" , "none");
        $("#Loader_bg").css("display" , "none");

    }, 1000);


}