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

             alert("wrong username & password");

        }
        else {

            alert("success true");

        }

    }
    catch (exp)
    {

        errorHandlerServerResponse(url,"parsing error call back");

    }


}