<?php

require_once '../inc/initDb.php';
$category_id = $_POST['category_id'];

$items  =  DB::query("select * from items where category_id = '$category_id' ");
$output = "";
$output.='<label> Select Item</label>';
$output.='<select id="business_item" name="business_item" class="form-control">';

foreach($items as $item)
{
    $output.='<option value="'.$item['id'].'">'.$item['name_en'].'</option>';

}
$output.='</select>';

echo json_encode($output);
