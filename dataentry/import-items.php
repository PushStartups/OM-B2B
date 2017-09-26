<?php
require "inc/initDb.php";
DB::query("set names utf8");
if(isset($_POST["Import"])){

    $category_id = $_POST['category_id'];
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
                DB::insert('items', array(
                    "category_id"           =>      $category_id,
                    "hide"                  =>      $getData[0],
                    "name_en"               =>      $getData[1],
                    "name_he"               =>      $getData[2],
                    "desc_en"               =>      $getData[3],
                    "desc_he"               =>      $getData[4],
                    "price"                 =>      $getData[5],
                    "sort"                  =>      $getData[6]

                ));


                // email_to_b2b_users($getData[0],$password,strtolower($getData[1]));
                // ob_end_clean();
            }
            $counter++;
        }

        $u = $_SERVER['HTTP_HOST'];
        fclose($file);
       // echo $u.$url;
//        echo '<script type="text/javascript" language="javascript">
//window.open("'.$u.$url.'");

//</script>';
        header("location:add-new-items.php?id=".$category_id);
    }
}
?>

