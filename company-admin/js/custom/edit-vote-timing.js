
function edit_vote_timing(id,url)
{

    var postForm = { //Fetch form data


        'start_time'                 :  $('#start_time').val(),
        'end_time'                 :  $('#end_time').val(),
        'id'                        : id


    };

    addLoading();
    $.ajax({
        url:"ajax/edit_vote_timing.php",
        method:"post",
        data:postForm,
        dataType:"json",
        success:function(data)
        {
            hideLoading();
            alert("Vote Timings edited successfully");
            window.location.href = url;
        }
    });



}




function delete_vote_timing(id,url)
{
    addLoading();
    $.ajax({
        url:"ajax/delete_timing.php",
        method:"post",
        data:{id:id},
        dataType:"json",
        success:function(data)
        {
            hideLoading();
            alert("Timing deleted successfully");
            window.location.href = url;
        }
    });
}


