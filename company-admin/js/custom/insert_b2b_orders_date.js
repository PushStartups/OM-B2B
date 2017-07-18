/**
 * Created by ahmad on 7/18/2017.
 */

$('#search_start_date').bind('input', function() {

    document.getElementById('error_search_start_date').innerHTML = "";

});

$('#search_end_date').bind('input', function() {

    document.getElementById('error_search_end_date').innerHTML = "";

});

$('#search-user-email').bind('input', function() {

    document.getElementById('error_search_email').innerHTML = "";

});


function insert_b2b_orders_date(url)
{

    var search_start_date                   =  $('#search_start_date').val();
    var search_end_date              =  $('#search_end_date').val();
    var hidden_email              =  $('#hidden_email').val();


    if(hidden_email == "")
    {
        $('#error_search_email').html('Select From Drop Down List');
        return;
    }

    if(search_start_date == "")
    {
        $('#error_search_start_date').html('Required*');
        return;
    }

    if(search_end_date == "")
    {
        $('#error_search_end_date').html('Required');
        return;
    }



    var postForm =
        { //Fetch form data

            'search_start_date'                    :  $('#search_start_date').val(),
            'search_end_date'               :  $('#search_end_date').val(),
            'search-user-email'               :  $('#search-user-email').val()
        };




    addLoading();
    $.ajax({
        url:"ajax/manual_search.php",
        method:"post",
        data:postForm,
        dataType:"json",
        success:function(data)
        {
            hideLoading();
            $("#target-content").html(data);
        }
    });



}

