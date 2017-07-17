<?php
$orig_date = $_POST['timee'];

if(($orig_date == "") || ($orig_date == null) ||($orig_date == "Closed"))
{
    echo json_encode("Closed");
}
else{

$seconds = strtotime($orig_date);

$plus_one_hour = $seconds + 3600;

$next_hour = floor($plus_one_hour / 3600) * 3600;

$mydate =  date("H:i",$next_hour);

$exact_time = explode(':',$mydate);

$old_time = explode(':',$orig_date);

$send_time = $final_time = $exact_time[0].':'.$old_time[1];

echo json_encode($send_time);
}