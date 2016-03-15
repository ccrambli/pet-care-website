<?php

class Model_Owner extends Model_Sitter {

  public static function get_all($deleted=true) {
    if (!$deleted) {
      return ORM::factory('Owner')->find_all();
    }
    return ORM::factory('Owner')->where('deleted','=',0)->find_all();
  }
}