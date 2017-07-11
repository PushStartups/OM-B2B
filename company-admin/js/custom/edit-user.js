

$('#name').bind('input', function() {

    document.getElementById('name_error').innerHTML = "";

});

$('#smooch_id').bind('input', function() {

    document.getElementById('email_error').innerHTML = "";

});


$('#contact').bind('input', function() {

    if(!this.value.match(/^\d+$/))
    {
        document.getElementById('contact_error').innerHTML = "Wrong Number!";
    }
    else
    {
        document.getElementById('contact_error').innerHTML = "";
    }

});

$('#address').bind('input', function() {

    document.getElementById('address_error').innerHTML = "";

});






function edit_user(id,url)
{

    var name                   =  $('#name').val();
    var smooch_id              =  $('#smooch_id').val();
    var contact                =  $('#contact').val();
    var address                =  $('#address').val();
    var discount               =  $('#discount').val();
    var company_discount       =  $('#company_discount').val();


    if(name == "")
    {
        $('#name_error').html('Required*');
        return;
    }

    if(smooch_id == "")
    {
        $('#email_error').html('Required');
        return;
    }

    if(contact == "")
    {
        $('#contact_error').html('Required*');
        return;
    }

    if(address == "")
    {
        $('#address_error').html('Required');
        return;
    }
    if(discount > company_discount)
    {
        $('#discount_error').html('Discount should not be greater than company discount');
        return;

    }




    var postForm =
    { //Fetch form data

        'name'                    :  $('#name').val(),
        'smooch_id'               :  $('#smooch_id').val(),
        'contact'                 :  $('#contact').val(),
        'address'                 :  $('#address').val(),
        'discount'                :  $('#discount').val(),
        'id'                      : id
    };

    addLoading();
    $.ajax({
        url:"ajax/edit_user.php",
        method:"post",
        data:postForm,
        dataType:"json",
        success:function(data)
        {
            hideLoading();
            alert("User edited successfully");
            window.location.href = "manage-users.php";
        }
    });



}




function delete_user(id,url)
{
    $(function(){
        swal({
                title: "Are you sure?",
                text: "You will not be able to recover this User!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes, delete it!",
                cancelButtonText: "No, cancel please!",
                closeOnConfirm: false,
                closeOnCancel: false
            },
            function(isConfirm){
                if (isConfirm) {
                    addLoading();
                    $.ajax({
                        url:"ajax/delete_user.php",
                        method:"post",
                        data:{id:id},
                        dataType:"json",
                        success:function(data)
                        {
                            hideLoading();
                            alert("User deleted successfully");
                            window.location.href = url;
                        }
                    });
                } else {
                    swal("Cancelled", "User is safe :)", "error");
                    window.location.href = url;
                }
            });
    });














}


