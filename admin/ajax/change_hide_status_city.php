<?php
require_once '../inc/initDb.php';
$city_id = $_POST['id'];
DB::update('cities', array(
    "hide" =>  $_POST['val'],
), "id=%d", $city_id
);
echo json_encode("success");