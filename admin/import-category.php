<?php
require "inc/initDb.php";
DB::query("set names utf8");
if(isset($_POST["Import"])){

    $menu_id = $_POST['menu_id'];
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

                $secondlastRestaurant   =   DB::queryFirstRow("SELECT * FROM categories ORDER BY id DESC  LIMIT 1 , 1");
                $secondlastId           =   $secondlastRestaurant['id'];
                $secondlastSortId       =   $secondlastRestaurant['sort'];
                $id                     =   $secondlastId + 1;
                $sort                   =   $secondlastSortId + 1;
                DB::query("set names utf8");
                DB::insert('categories', array(

                    "id"                    =>      $id,
                    "menu_id"               =>      $menu_id,
                    "is_discount"           =>      $getData[0],
                    "name_en"               =>      $getData[1],
                    "name_he"               =>      $getData[2],
                    "business_offer"        =>      $getData[3],
                    "image_url"             =>      $getData[4],
                    "sort"                  =>      $sort

                ));

            }
            $counter++;
        }
        //$u = $_SERVER['HTTP_HOST'];
        fclose($file);
        header("location:add-new-category.php?id=".$menu_id);
    }
}
?>

