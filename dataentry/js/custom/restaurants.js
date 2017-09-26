
// CHANGE THE RANK OR RESTAURANT (SORT)
function change_rank(id,city_id)
{
    addLoading();
    var rank = "";
    rank = document.getElementById("rank"+id).value;
    $.ajax({
        url:"ajax/change_rank.php",
        method:"post",
        data:{id:id,rank:rank,city_id:city_id},
        dataType:"json",
        success:function(data)
        {
            hideLoading();
            window.location.href="index.php?id="+city_id;
        }
    });
}

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
                window.location.href="index.php?id=1";
            }
        }
    });

});
$('input[name=onoffswitch]').change(function(){
    if($(this).is(':checked')) {
        //RESTAURANT KO ON KARNA HAI
       change_hide_status($(this).attr("id"),'0');
    } else {
        // Checkbox is not checked.
        change_hide_status($(this).attr("id"),'1');

    }
});
function change_hide_status(restaurant_id,val)
{
    addLoading();
    $.ajax({
        url:"ajax/change_hide_status.php",
        method:"post",
        data:{id:restaurant_id,val:val},
        dataType:"json",
        success:function(data)
        {
            hideLoading();
        }
    });
}
function addLoading(){

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