// COMMON AJAX CALLS AND HANDLING

function commonAjaxCall(url,data,callBcak)
{
    addLoading();

    //SERVER HOST DETAIL
    var host = "https://"+window.location.hostname;

    $.ajax({

        url:  host + url,
        type: "post",
        data: data,


        success: function (response) {

            try {

                var res = response;
                callBcak(url,res);
                hideLoading();

            }
            catch (exp)
            {
                errorHandlerServerResponse(url,response);
                hideLoading();
            }

        },
        error: function (jqXHR, textStatus, errorThrown) {


            errorHandlerServerResponse(url,"No Response From Server");
            hideLoading();
        }

    });

}

// SHOW LOADER ON AJAX CALLS
function addLoading(){


    $("#Loader_bg").css("display" , "block");


}


// HIDE LOADING ON AJAX CALLS
function hideLoading(){

    if(!keepLoaderUntilPageLoad) {

        $("#Loader_bg").css("display", "none");

    }

}


function errorHandlerServerResponse(url,message) {


    // THROW ERROR TO USER
    $('#error-info-popup').modal('show');

    // GENERATE EMAIL FOR ADMIN AGAINST ISSUE

    commonAjaxCall("/restapi/index.php/error_report", {"host": window.location.hostname, "url": url, "message" : message},empty);

}

function empty(response) {};