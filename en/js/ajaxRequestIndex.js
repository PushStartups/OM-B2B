var dataObject;   // DATA OBJECT CONTAIN INFORMATION ABOUT COMPANY, USER & USER ORDER


$(document).ready(function() {


    dataObject = localStorage.getItem("data_object_en");


    // EXCEPTION IF USER OBJECT NOT RECEIVED UN-DEFINED
    if (dataObject != undefined && dataObject != "" && dataObject != null){

        dataObject = JSON.parse(localStorage.getItem("data_object_en"));

        var company_name   =   dataObject.company.company_name;
        company_name       =   company_name.replace(/\s/g, '');

        window.location.href = '/en/'+company_name+'/restaurants';

    }
    else
    {
        
        dataObject = {

            'language': 'en',                  // USER LANGUAGE ENGLISH DESKTOP B2B
            'company': '',                     // attributes are {company_id, company_name, company_address,delivery_time}
            'user': '',                        // attributes are {user_id, name, email, contact, userDiscountFromCompany}
            'rests_orders': [],                // ARRAY OF MULTIPLE REST ORDERS
            'actual_total' : 0,                // ACTUAL TOTAL (BILL) WITHOUT ANY DISCOUNT AND COMPENSATION
            'total_paid' : 0,                  // TOTAL AMOUNT PAID BY USER
            "company_contribution" : 0         // AMOUNT CONTRIBUTED BY COMPANY


        };

    }

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


            dataObject.company = response.company;
            dataObject.user    = response.user;

            var company_name   =   dataObject.company.company_name;
            company_name       =   company_name.replace(/\s/g, '');

            localStorage.setItem("data_object_en", JSON.stringify(dataObject));


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