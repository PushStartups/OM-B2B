<?php
require "inc/initDb.php";
DB::query("set names utf8");
if(isset($_POST["Import"])){

    $item_id = $_POST['item_id'];
    $url = $_POST['url'];


    $filename = $_FILES["file"]["tmp_name"];


    if($_FILES["file"]["size"] > 0)
    {
        $file = fopen($filename, "r");
        $counter = 0;
        while (($getData = fgetcsv($file, 10000, ",")) !== FALSE)
        {

            if($counter != 0)
            {
                DB::query("set names utf8");
                DB::insert('extras', array(
                    "item_id"           => $item_id,
                    "name_en"           => $getData[0],
                    "type"              => $getData[1],
                    "price_replace"     => $getData[2],
                    "name_he"           => $getData[3],
                    "limit"             => $getData[4],
                    "sort"              => $getData[5]

                ));

                // email_to_b2b_users($getData[0],$password,strtolower($getData[1]));
                // ob_end_clean();
            }
            $counter++;
        }

        fclose($file);
        header("location:add-choices-addons.php?id=".$item_id);
    }
}
?>

