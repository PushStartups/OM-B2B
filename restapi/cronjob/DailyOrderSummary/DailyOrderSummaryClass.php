<?php

class DailyOrderSummaryClass {
  function __constructro() {

  }

  public function test() {
    echo 'Test';
  }

  public function query() {
    DB::useDB('orderapp_restaurants_b2b_wui');
    $result = DB::query("SELECT `id`, `name_en`, `email`, `fax_number`, `whatsapp_group_name`, `whatsapp_group_creator` FROM `restaurants`"); 

    return $result;
  }
}