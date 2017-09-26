<?php
require_once '../inc/initDb.php';
$restaurant_id = $_POST['id'];
$rank          = $_POST['rank'];
$city_id       = $_POST['city_id'];

//CHECK IF RANK OR SORT IS ALREADY EXIST IN DATABASE
//IF YES, SWAP THE SORT OR RANK
$restaurant = DB::queryFirstRow("select id from restaurants where sort = '$rank' and city_id = '$city_id' ");
if(!empty($restaurant))
{
    //GET THE PREVIOUS SORT OF RESTAURANT TO SWAP
    $desire_restaurant = DB::queryFirstRow("select sort,id from restaurants where id = '$restaurant_id' and city_id = '$city_id'");


    //UPDATE PREVIOUS RESTAURANT SORT
    DB::update('restaurants', array(
        "sort" =>  $desire_restaurant['sort'],
    ), "id=%d", $restaurant['id']
    );

    //UPDATE THE DESIRE RESTAURANT SORT
    DB::update('restaurants', array(
        "sort" =>  $rank,
    ), "id=%d", $restaurant_id
    );
}
else
{
    DB::update('restaurants', array(
        "sort" =>  $rank,
    ), "id=%d", $restaurant_id
    );
}
echo json_encode("success");

