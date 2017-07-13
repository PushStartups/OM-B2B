// COMMON AJAX CALLS AND HANDLING

function commonAjaxCall(url,data,callBcak)
{
    //SERVER HOST DETAIL
    var host = "https://"+window.location.hostname+"/backend";

    $.ajax({

        url:  host + url,
        type: "post",
        data: data,


        success: function (response) {

            try {

                var res = JSON.parse(response);
                callBcak(url,res);

            }
            catch (exp)
            {
                errorHandlerServerResponse(url,response);
            }

        },
        error: function (jqXHR, textStatus, errorThrown) {


            errorHandlerServerResponse(url,"No Response From Server");

        }

    });

}

// SHOW LOADER ON AJAX CALLS
function addLoading(){


    $("#Loader_bg").css("display" , "block");
    $("#loader").css("display" , "block");


}


// HIDE LOADING ON AJAX CALLS
function hideLoading(){


    $("#loader").css("display" , "none");
    $("#Loader_bg").css("display" , "none");


}


function errorHandlerServerResponse(url,message) {


    // THROW ERROR TO USER

    alert(url);
    alert(message);

    // GENERATE EMAIL FOR ADMIN AGAINST ISSUE


}