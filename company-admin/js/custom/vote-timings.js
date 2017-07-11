$('input[name=onoffswitchcompany]').change(function(){
    if($(this).is(':checked')) {

        change_vote_status($(this).attr("id"),'1');
    } else {

        change_vote_status($(this).attr("id"),'0');

    }
});


$('#start_time').bind('input', function() {

    document.getElementById('start_time_error').innerHTML = "";


});

$('#end_time').bind('input', function() {

    document.getElementById('end_time_error').innerHTML = "";


});

function change_vote_status(company_id,val)
{
    // alert(company_id);
    //alert(val);
    addLoading();
    $.ajax({
        url:"ajax/change_vote_status.php",
        method:"post",
        data:{id:company_id,val:val},
        dataType:"json",
        success:function(data)
        {
            hideLoading();
        }
    });
}


function add_new_vote_timing(url)
{

    var start_time                    =  $('#start_time').val();
    var end_time                    =  $('#end_time').val();



    if(start_time == "")
    {
        $('#start_time_error').html('Required*');
        return;
    }

    if(end_time == "")
    {
        $('#end_time_error').html('Required');
        return;
    }



    var postForm = { //Fetch form data


        'start_time'                 :  $('#start_time').val(),
        'end_time'                 :  $('#end_time').val(),


    };

    addLoading();
    $.ajax({
        url:"ajax/add_vote_timing.php",
        method:"post",
        data:postForm,
        dataType:"json",
        success:function(data)
        {
            hideLoading();
            alert("Vote Timings added successfully");
            window.location.href = "vote-timings.php" ;
        }
    });



}