<?php
require "inc/initDb.php";
DB::query("set names utf8");
if(isset($_POST["Import"])){
    
    $extra_id = $_POST['extra_id'];
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
                DB::insert('subitems', array(
                    "extra_id"          => $extra_id,
                    "name_en"           => $getData[0],
                    "name_he"           => $getData[1],
                    "price"             => $getData[2]

                ));

                // email_to_b2b_users($getData[0],$password,strtolower($getData[1]));
                // ob_end_clean();
            }
            $counter++;
        }

        fclose($file);
        header("location:add-subitems.php?id=".$extra_id);
    }
}
?>

