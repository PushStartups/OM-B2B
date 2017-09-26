<?php
require_once '../inc/initDb.php';
$restaurant_id = $_POST['id'];
DB::update('restaurants', array(
    "hide" =>  $_POST['val'],
), "id=%d", $restaurant_id
);
echo json_encode("success");