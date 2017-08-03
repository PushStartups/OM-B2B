
// INPUT TAGS HANDLER

function errorCheck(parentId) {

    var child = document.getElementById(parentId).getElementsByTagName('input');

    for (var x = 0; x < child.length; x++) {

        var $this = $(child[x]);
        var id = $this.attr('id');
        var child_parent = "#parent-"+id;
        var error_id = "#error-" + id;
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

                        $(error_id).html("*Required Field");
                        $(id).addClass('have-error');
                        $(child_parent).addClass('error');
                        $(error_id).addClass('error');
                        return false;
                    }

                }

            }


            if (type == 'password') {

                if (isMandatory) {
                    // EMPTY VALUE ERROR
                    if (value == "") {

                        $(error_id).html("*Required Field");
                        $(id).addClass('have-error');
                        $(error_id).addClass('error');
                        $(child_parent).addClass('error');
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
                        $(error_id).addClass('error');
                        $(id).addClass('have-error');
                        $(child_parent).addClass('error');
                        return false;

                    }

                }
                // CHAR CONTAIN ERROR

                if ((!value.match(/^\d+$/))) {
                    $(error_id).html("Number Only");
                    $(error_id).addClass('error');
                    $(id).addClass('have-error');
                    return false;

                }

            }
            // TYPE EMAIL

            if (type == 'email') {
                if (isMandatory) {
                    // EMPTY VALUE ERROR
                    if (value == "") {

                        $(error_id).html("*Required Field");
                        $(id).addClass('have-error');
                        $(child_parent).addClass('error');
                        $(error_id).addClass('error');
                        return false;

                    }

                }
                // CHAR CONTAIN ERROR

                if (!value.match(/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/)) {
                    $(id).addClass('have-error');
                    $(error_id).addClass('error');
                    $(child_parent).addClass('error');
                    $(error_id).html("Invalid Email");
                    return false;

                }

            }

        }
        else {


            alert("type of field missing contact support");

        }


    }

    return true;
}




// GENERIC ON_BIND FUNCTION

function onChangeRemoveError(id) {

    var errorId = "#error-"+id;
    var child_parent = "#parent-"+id;
    id = "#"+id;

    $(id).bind('input', function () {

        $(errorId).html("");
        $(errorId).removeClass('error');
        $(id).removeClass('have-error');
        $(child_parent).removeClass('error');

    });

}
