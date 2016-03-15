<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Sitters extends Controller {

  public function action_index() {

    $sitters = Model_Sitter::get_all();
    $this->response->body(View::factory('sitters/index')->bind('sitters', $sitters));
  }

  public function action_set_rank() {
    Model_Sitter::set_rank_all();
    return;
  }

}