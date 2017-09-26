<?php
require_once '../inc/initDb.php';


$tags_id            =  $_POST['tags_id'];

DB::query("delete from restaurant_tags where  tag_id = '$tags_id' ");


DB::query("delete from tags where  id = '$tags_id' ");

echo json_encode("success");