var user_id;              // USER ID
var company_name;         // COMPANY NAME


var keepLoaderUntilPageLoad = true;


$(document).ready(function() {


    user_id = localStorage.getItem("user_id_b2b");


    // EXCEPTION IF USER OBJECT NOT RECEIVED UN-DEFINED
    if (user_id != undefined && user_id != "" && user_id != null){


        user_id         =   localStorage.getItem("user_id_b2b");

        company_name    =   localStorage.getItem("company_name_b2b");

        company_name    =   company_name.replace(/\s/g, '');

        window.location.href = '/en/'+company_name+'/restaurants';


    }

});



// PASSWORD VERIFICATION FROM SERVER
function verifyUserPassword() {


    if(errorCheck("loginForm")) // IF NO GENERAL ERROR EXISTS REQUEST SERVER FOR VERIFICATION
    {

        var user_name = $('#user-name').val();
        var password = $('#password').val();

        $('#showhide').show();


        commonAjaxCall("/restapi/index.php/b2b_user_login", {"user_name": user_name, "password": password}, responseUserNamePasswordVerification);

    }
    else {

        $('#showhide').hide();
    }
}




// RESPONSE FROM SERVER AGAINST LOGIN REQUEST
function responseUserNamePasswordVerification(url,response) {

    try {


        if (response.error)
        {

            if(response.field == "user-name") {

                $('#parent-user-name').addClass("error");
                $('#error-user-name').html("invalid username");

            }
            else if(response.field == "password"){

                $('#parent-password').addClass("error");
                $('#error-password').html("invalid password");

                $('#showhide').hide();

            }


        }
        else  {

            $('#showhide').show();

            company_name = response.company_name;
            user_id      = response.user_id;


            localStorage.setItem("user_id_b2b",user_id);
            localStorage.setItem("company_name_b2b",company_name);

            company_name  =  company_name.replace(/\s/g, '');

            window.location.href = '/en/'+company_name+'/restaurants';

        }

    }
    catch (exp)
    {

        errorHandlerServerResponse(url,"parsing error call back");

    }

}



function submitEmailForPasswordRecovery(){

    if(errorCheck("forgetPasswordForm")) // IF NO GENERAL ERROR EXISTS REQUEST SERVER FOR FORGET PASSWORD REQUEST
    {

        var email = $('#email').val();


        commonAjaxCall("/restapi/index.php/forgot_email", {"email": email}, callBackRespForgetPassword);

    }
}



function callBackRespForgetPassword(url,response)
{

    try {


        $('#check-email-popup').modal('show');
        $('#email-popup').modal('hide');

        if (response.error)
        {

            $('#email-sent-message').hide();
            $('#email-error-message').show();

        }
        else {


            $('#email-sent-message').show();
            $('#email-error-message').hide();

        }

    }
    catch (exp)
    {


        errorHandlerServerResponse(url,"parsing error call back");


    }

}