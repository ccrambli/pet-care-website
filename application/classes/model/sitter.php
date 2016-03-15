<?php

class Model_Sitter extends ORM {

  protected $_has_many = array(
    'appointments' => array('model' => 'appointment', 'foreign_key' => 'owner_id'),
  );

 /*
   * Get all sitters
   *
  */
  public static function get_all($deleted=true) {
    if (!$deleted) {
      return ORM::factory('Sitter')->find_all();
    }
    return ORM::factory('Sitter')->where('deleted','=',0)->find_all();
  }

  public static function set_rank_all() {
    $sitters = self::get_all();
    foreach ($sitters as $sitter) {
      $sitter->set_rank();
    }

  }

  /*
   * Set overall sitter rank
   *
   * The overall sitter rank is a combo of sitter score and ratings score
   * The sitter score is 5* fraction of distinct letters / numbers in alphabet from sitter name
   * Rating score is average of stays
   * Case 0 stays -> overwall sitter tank is sitter score
   * Case > 0 stays -> weighted avg of sitter score and ratings score, weighted by the number of stays
   * If 10 or more stays, overall sitter tank is equal to the ratings score
   */
  public function set_rank() {
    //first check how many apptmts we have...
    $stays = $this->get_rated_appointment_count();

    //if stays are greater than or equal to ten, use only ratings average
    if ($stays >= 10) {
      $overall_score = $this->get_ratings_score();
    //if there are no stays, use the sitter score
    } else if ($stays == 0) {
      $overall_score = $this->get_sitter_score();
    } else {
      //user has between 1-9 stays...
      $rating_weight = $stays*10;
      $sitter_weight = (10-$stays)*10;

      //get scores
      $sitter_score = $this->get_sitter_score();
      $rating_score = $this->get_ratings_score();
 
      $overall_score = ($sitter_score*$sitter_weight + $rating_score*$rating_weight)/100;

      $this->rating = number_format($overall_score,2);
      $this->save();
    }
    
    return $overall_score;
  }

  public function get_sitter_score() {
    $sitter_name_chars = str_split($this->name);
    //we need all chars in alphabet to do this nicely and not in a super cheap way!
    $alphabet = array("A","B","C","D","E",
                      "F","G","H","I","J",
                      "K","L","M","N","O",
                      "P","Q","R","S","T",
                      "U","V","W","X","Y","Z");

    $distinct_chars = array();
    foreach ($sitter_name_chars as $sitter_name_char) {
      //we may have something that is not in our alphabet
      if (in_array(strtoupper($sitter_name_char), $alphabet)) {
        $distinct_chars[strtoupper($sitter_name_char)] = true;
      }
    }
    $distinct_chars_count = count($distinct_chars);
    //get 5* distinct letters / number of letters in alphabet (26)
    $sitter_score = number_format(5*$distinct_chars_count/26,2);
    return $sitter_score;
  }

  public function get_ratings_score() {
    //this is just the average of the ratings for now...
    $rated_appointments = $this->get_rated_appointments();

    $rating_sum = $this->get_ratings_sum();

    //we should have some sort of util function eventually to divide nicely...
    if (count($rated_appointments) == 0) {
      return 0;
    }
    return number_format($rating_sum/count($rated_appointments),2);
  }

  public function get_rated_appointments() {
     //rating must be > 0
     return $this->appointments->where('rating','>',0)->find_all();
  }

  /*
   * Get the number of appointments that have been rated for this user
   *
   */ 
  public function get_rated_appointment_count() {
     //use get rated appointments
     return $this->get_rated_appointments()->count();
  }

  // function to sum up individual ratings
  public function get_ratings_sum() {
    $rated_appointments_sum = 0;
    $rated_appointments = $this->get_rated_appointments();

    foreach ($rated_appointments as $rated_appointment) {
      $rated_appointments_sum += $rated_appointment->rating;
    }
    return $rated_appointments_sum;
  }
}