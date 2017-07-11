<?php

require_once '../inc/initDb.php';

/* retrieve the search term that autocomplete sends */

$term = $_GET['term'];

$qry_name = "select * from restaurants where name_en LIKE '%$term%' order by sort ASC";
$result = db::query($qry_name);

foreach ($result as $row) {
    $data[] = $row['email'];
}
echo json_encode($data);
?>
