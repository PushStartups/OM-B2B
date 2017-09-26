<?php
require_once '../inc/initDb.php';


$tags_id            =  $_POST['tag_id'];

DB::query("delete from restaurant_tags where  id = '$tags_id'   ");


echo json_encode("success");