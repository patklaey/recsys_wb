<?php

require_once 'forms/forms_view.php';
require_once 'forms/forms_submit.php';

// Define some constants
define('GOOD_BOOK_ITEM', 8);
define('GOOD_MOVIE_ITEM', 4);

/**
 * Displays evaluation form or selected recommender algorithm evaluation
 */
function showEvaluation() {
  // The return string
  $return_string = "";
  // The test database
  $test_db = MOVIE_DB_TEST;
  $good_item = GOOD_MOVIE_ITEM;
  
  if ( ! isset($_SESSION['recsys_wb_evaluation_form_submitted']) 
|| $_SESSION['recsys_wb_evaluation_form_submitted'] === FALSE ) {
    // Simply display the evaluation from
    $return_string .= drupal_render( drupal_get_form('recsys_wb_evaluation_form') );
  } 
  else {
    $recommender_app_id = $_SESSION['recsys_wb_evaluation_app_id'];
    // Check if book or movie recommender
    $recommender_app_name = getRecommenderAppName($recommender_app_id);
    if ( preg_match("/^book/", $recommender_app_name) ) {
      $test_db = BOOK_DB_TEST;
      $good_item = GOOD_BOOK_ITEM;
    }
    // Get the apps recommendations and the corresponding test entries
    $query = db_select($test_db, 'test');
    $query->join(
      'recommender_prediction',
      'prediction',
      'test.UserID = prediction.source_eid'
    );
    $query->fields('test',array('Rating'));
    $query->fields('prediction',array('score'));
    $query->condition('prediction.app_id',$recommender_app_id);
    $query_result = $query->execute();

    // Save the query results in an array
    $results = array();    
    foreach ($query_result as $result) {
      $results[] = array("rating" => $result->rating, "score" => $result->score);
    }

    $MAE = meanAbsoluteError($results);
    $RMSE = rootmeanSquaredError($results);
    $MRR = meanReciprocalRank($results, $good_item);
    $return_string .= "Mean Absolute Error: " . $MAE . "<br/>";
    $return_string .= "Root Mean Squared Error: " . $RMSE. "<br/>";
    $return_string .= "Mean Reciprocal Rank: " . $MRR;
    
    $return_string .= "<br/><strong>OR</strong><br/>";
    
    // Add the reset form
    $return_string .= "<br/>" . drupal_render( 
      drupal_get_form('recsys_wb_reset_form') 
    );
  }
  
  return $return_string;
}

/**
 * Calculate the mean absolute error
 */
function meanAbsoluteError( $results ) {
  $total_diff = 0;
  $total_count = 0;
  foreach ($results as $result) {
    $diff = $result['rating'] - $result['score'];
    $total_diff += abs($diff);
    $total_count++;
  }
  return $total_diff / $total_count;
}

/**
 * Calculate the root mean squared error
 */
function rootmeanSquaredError( $results ) {
  $total_diff = 0;
  $total_count = 0;
  foreach ($results as $result) {
    $diff = $result['rating'] - $result['score'];
    $total_diff += pow($diff, 2);
    $total_count++;
  }
  return sqrt($total_diff / $total_count);
}

/**
 * Calculate the mean reciprocal rank
 */
function meanReciprocalRank( $results, $good) {
  usort($results,"sortByScore");
  $i = 1;
  foreach ($results as $result) {
    if ( $result['rating'] >= $good )
      return 1/$i;
    $i++;
  }
}

/**
 * Sort the result array by rating
 */
function sortByRating( $a, $b) {
  if ($a['rating'] == $b['rating']) {
    return 0;
  }
  return ($a['rating'] < $b['rating']) ? 1 : -1;
}

/**
 * Sort the result array by score
 */
function sortByScore( $a, $b) {
  if ($a['score'] == $b['score']) {
    return 0;
  }
  return ($a['score'] < $b['score']) ? 1 : -1;
}

?>