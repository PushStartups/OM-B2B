
// INPUT TAGS HANDLER


function ErrorCheck(parentId) {

    //var child = $(parentId+" :input");

    var child = document.getElementById(parentId).getElementsByTagName('input');

    for (var x = 0; x < child.length; x++) {

        var $this = $(child[x]);
        var id = $this.attr('id');
        var error_id = "#error-" + id;
        var circle_error = "#circle-error-" + id;
        id = "#" + id;
        var value = $(id).val();
        var type = $(id).attr('ctype');
        var isMandatory = $(id).hasClass("mandatory");

        // TYPE TEXT

        if(type != undefined) {

            if (type == 'text') {

                if (isMandatory) {
                    // EMPTY VALUE ERROR
                    if (value == "") {

                        //$(error_id).html("*Required Field");

                        $(id).addClass('have-error');
                        $(error_id).removeClass('error');
                        $(circle_error).removeClass('error');
                        return false;
                    }

                }

            }


            if (type == 'password') {

                if (isMandatory) {
                    // EMPTY VALUE ERROR
                    if (value == "") {

                        //$(error_id).html("*Required Field");

                        $(id).addClass('have-error');
                        $(error_id).removeClass('error');
                        $(circle_error).removeClass('error');
                        return false;
                    }

                }

            }

            // TYPE NUMBER

            if (type == 'number') {
                if (isMandatory) {
                    // EMPTY VALUE ERROR
                    if (value == "") {

                        $(error_id).html("*Required Field");
                        $(error_id).removeClass('error');
                        $(id).addClass('have-error');
                        $(circle_error).removeClass('error');
                        return false;

                    }

                }
                // CHAR CONTAIN ERROR

                if ((!value.match(/^\d+$/))) {
                    $(error_id).html("Number Only");
                    $(error_id).addClass('error');
                    $(circle_error).removeClass('error');
                    $(id).addClass('have-error');
                    return false;

                }

            }
            // TYPE EMAIL

            if (type == 'email') {
                if (isMandatory) {
                    // EMPTY VALUE ERROR
                    if (value == "") {

                        $(id).addClass('have-error');
                        $(error_id).removeClass('error');
                        $(circle_error).removeClass('error');
                        return false;

                    }

                }
                // CHAR CONTAIN ERROR

                if (!value.match(/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/)) {
                    $(id).addClass('have-error');
                    $(error_id).removeClass('error');
                    $(circle_error).removeClass('error');
                    return false;

                }

            }

        }
        else {
            alert("Your Type Is Undefined");
        }


    }

    onFormSucess();

    return true;
}




//  FOR POPUPS

function ErrorCheckPopUp(popupId) {

    //var child = $(parentId+" :input");

    var child = document.getElementById(popupId).getElementsByTagName('input');

    for (var x = 0; x < child.length; x++) {

        var $this = $(child[x]);
        var id = $this.attr('id');
        var error_id = "#error-" + id;
        var circle_error = "#circle-error-" + id;
        id = "#" + id;
        var value = $(id).val();
        var type = $(id).attr('ctype');
        var isMandatory = $(id).hasClass("mandatory");

        // TYPE TEXT

        if(type != undefined) {

            if (type == 'text') {

                if (isMandatory) {
                    // EMPTY VALUE ERROR
                    if (value == "") {

                        //$(error_id).html("*Required Field");

                        $(id).addClass('have-error');
                        $(error_id).removeClass('error');
                        $(circle_error).removeClass('error');
                        return false;
                    }

                }

            }


            if (type == 'password') {

                if (isMandatory) {
                    // EMPTY VALUE ERROR
                    if (value == "") {

                        //$(error_id).html("*Required Field");

                        $(id).addClass('have-error');
                        $(error_id).removeClass('error');
                        $(circle_error).removeClass('error');
                        return false;
                    }

                }

            }

            // TYPE NUMBER

            if (type == 'number') {
                if (isMandatory) {
                    // EMPTY VALUE ERROR
                    if (value == "") {

                        $(error_id).html("*Required Field");
                        $(error_id).removeClass('error');
                        $(id).addClass('have-error');
                        $(circle_error).removeClass('error');
                        return false;

                    }

                }
                // CHAR CONTAIN ERROR

                if ((!value.match(/^\d+$/))) {
                    $(error_id).html("Number Only");
                    $(error_id).addClass('error');
                    $(circle_error).removeClass('error');
                    $(id).addClass('have-error');
                    return false;

                }

            }
            // TYPE EMAIL

            if (type == 'email') {

                if (isMandatory) {
                    // EMPTY VALUE ERROR
                    if (value == "") {

                        $(id).addClass('have-error');
                        $(error_id).removeClass('error');
                        $(circle_error).removeClass('error');
                        return false;

                    }

                }
                // CHAR CONTAIN ERROR

                if (!value.match(/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/)) {
                    $(id).addClass('have-error');
                    $(error_id).removeClass('error');
                    $(circle_error).removeClass('error');
                    return false;

                }

                $('#check-email-popup').modal('toggle');

            }

        }
        else {
            alert("Your Type Is Undefined");
        }


    }






    return true;
}

// ON SUCCESSFULL FORM VALIDATION >>> GOTO NEXT SCREEN

function goto(location){

    window.location.href = location ;
}




// GENERIC ON_BIND FUNCTION

function onChangeRemoveError(id) {

    var errorId = "#error-"+id;
    var circleError = "#circle-error-" + id;

    id = "#"+id;

    $(id).bind('input', function () {

        //$(errorId).html("");
        $(errorId).addClass('error');
        $(circleError).addClass('error');
        $(id).removeClass('have-error');

    });

}
