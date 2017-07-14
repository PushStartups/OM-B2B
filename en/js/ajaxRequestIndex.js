var dataObject;   // DATA OBJECT CONTAIN INFORMATION ABOUT COMPANY, USER & USER ORDER


$(document).ready(function() {


    dataObject = {


        'language': 'en',                  // USER LANGUAGE ENGLISH DESKTOP B2B


        'user': null,                      // USER DATABASE OBJECT DB ATTRIBUTES
                                           // {id, smooch_id, name, user_name, password, discount, date, contact, address, language,
                                           // payment_url, company_id, voucherify_id}


        'company' : null                   // COMPANY DATABASE OBJECT DB ATTRIBUTES
                                           // {id, name, delivery_address, discount, discount_type, team_size, contact_name, contact_number,
                                           // contact_email, ledger_link,voting, winner_limit, last_voting_id, last_voting_date, hide_payment,
                                           // min_order, lat, lng , email, password,limit_of_restaurants, registered_company_no,notes}

    };


});


// PASSWORD VERIFICATION FROM SERVER

function verifyUserPassword() {


    if(errorCheck("loginForm")) // IF NO GENERAL ERROR EXISTS REQUEST SERVER FOR VERIFICATION
    {

        var user_name = $('#user-name').val();
        var password = $('#password').val();

        commonAjaxCall("/restapi/index.php/b2b_user_login", {"user_name": user_name, "password": password}, responseUserNamePasswordVerification);

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

            }

        }
        else  {


            localStorage.setItem("data_object", dataObject);
            window.location.href = '/en/'+dataObject.company.name+'/restaurants';

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