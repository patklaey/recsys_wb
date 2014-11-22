<?php

require_once 'forms/forms_view.php';
require_once 'forms/forms_submit.php';

// Define some constants
define('GOOD_BOOK_ITEM', 8);
define('GOOD_MOVIE_ITEM', 4);
define('DB_EVAL_TABLE','recsys_wb_recommender_evaluation');
define('DB_RUN_TABLE','recsys_wb_evaluation_run');

/**
 * Displays evaluation form or selected recommender algorithm evaluation
 */
function showEvaluation() {
  // The return string
  $return_string = "";
  
  if ( ! isset($_SESSION['recsys_wb_evaluation_form_submitted']) 
|| $_SESSION['recsys_wb_evaluation_form_submitted'] === FALSE ) {
    // Simply display the evaluation from
    $return_string .= drupal_render( 
      drupal_get_form('recsys_wb_evaluation_form') 
    );
    
    $return_string .= "<br/><strong>OR</strong><br/><br/>";
    
    $return_string .= drupal_render(
      drupal_get_form('recsys_wb_evaluate_all_form')
    );
  } 
  else {
    // Prepeare the table header
    $header = array( 
      t('Recommender algorithm'),
      t('Mean Absolute Error'),
      t('Root Mean Squared Error'),
      t('Mean Reciprocal Rank'),
      t('Normalized DGC'),
    );
    $rows = array();
    // The cell style formatting
    $style = 'text-align:center;vertical-align:middle';
    
    if ( isset( $_SESSION['recsys_wb_evaluate_all_form_submitted'] )
&& $_SESSION['recsys_wb_evaluate_all_form_submitted'] === TRUE ) 
    {
      
    } 
    else {
      $recommender_app_ids = $_SESSION['recsys_wb_evaluation_app_id'];
      foreach ($recommender_app_ids as $key => $value) {
        // Get the evaluation results from the database
        $result = getEvaluationResults( $value );
        if ( $value != 0 ) {
          $rows[] = array(
            'title' => array(
              'data' => getRecommenderAppTitle( $value ),
              'style' => $style
            ),
            'mae' => array(
              'data' => $result['mae'],
              'style' => $style
            ),
            'rmse' => array(
              'data' => $result['rmse'],
              'style' => $style
            ),
            'mrr' => array(
              'data' => $result['mrr'],
              'style' => $style
            ),
            'ndgc' => array(
              'data' => $result['ndgc'],
              'style' => $style
            )
          );
        }
      }
    }
    // Render the table
    $return_string .= theme('table', array( 'header' => $header , 'rows' => $rows) );
    
    // Add the reset form
    $return_string .= "<br/>" . drupal_render( 
      drupal_get_form('recsys_wb_reset_form') 
    );
  }
  
  return $return_string;
}

/**
 * Run the scheduled evaluations
 */
function runEvaluations() {
  // Get a list of all pending evaluations
  $query = db_select( 'recsys_wb_evaluation_run' , 'run');
  $query->fields('run', array('app_id','logfile'));
  $query->condition('run.done',0);
  $results = $query->execute();
  
  // Define test database and good item value
  $test_db = MOVIE_DB_TEST;
  $good_item = GOOD_MOVIE_ITEM;
  
  // Go through all pending evaluations and calculate them 
  foreach( $results as $result ) {
    $logfile = $result->logfile;
    // Log start progress
    writeLog($logfile, "Recommendations calculated, going to evaluate the calulated recommendations ...");
    $recommender_app_id = $result->app_id;
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

    writeLog($logfile, "Calculating Mean Absolute Error ...");
    $MAE = meanAbsoluteError($results);
    writeLog($logfile, "Mean Absolute Error calculated: $MAE");
    
    writeLog($logfile, "Calculating Root Mean Squared Error ...");
    $RMSE = rootmeanSquaredError($results);
    writeLog($logfile, "Root Mean Squared Error calculated: $RMSE");
    
    writeLog($logfile, "Calculating Mean Reciprocal Rank ...");
    $MRR = meanReciprocalRank($results, $good_item);
    writeLog($logfile, "Mean Reciprocal Rank calcualted: $MRR");
    
    // Write the results to the database
    writeEvaluationResults($recommender_app_id, $MAE, $RMSE, $MRR, 0);
    
    // Write finish process
    writeLog($logfile, "Recommendations evaluated. Run finished!");
  }
    
}

/**
 * Write the evaluation results to the database
 */
function writeEvaluationResults( $app_id, $mea, $rmse, $mrr, $ndgc) {
  // Check if the entry for the given app_id already exists
  $result = db_query("SELECT app_id from {" . DB_EVAL_TABLE . "} where app_id ="
    . " :app_id", 
    array(":app_id" => $app_id) );
  // If the entry does not exist, add a new one, otherwise just update the old
  if ( $result->rowCount() == 0 ) {
    $result = db_insert( DB_EVAL_TABLE )
      ->fields(array(
        'app_id' => $app_id,
        'mae' => $mea,
        'rmse' => $rmse,
        'mrr' => $mrr,
        'ndgc' => $ndgc
      ))->execute();
  }
  else {
    $num = db_update( DB_EVAL_TABLE ) 
      ->fields(array(
        'mae' => $mea,
        'rmse' => $rmse,
        'mrr' => $mrr,
        'ndgc' => $ndgc
      ))
      ->condition('app_id', $app_id)
      ->execute();
  }
  // Update the schedule table to remember that the evaluation is done
  $num = db_update( DB_RUN_TABLE ) 
    ->fields(array(
      'done' => 1,
    ))
    ->condition('app_id', $app_id)
    ->execute();
}

/**
 * Read the evaluation results for a recommender algorithm from the database
 */
function getEvaluationResults( $app_id ) {
  // Get the evaluation results from the database
  $results = db_query("Select mae,rmse,mrr,ndgc from {" . DB_EVAL_TABLE . "} " 
        . "where app_id = :app_id ",  array(':app_id' => $app_id ) );
  return $results->fetchAssoc();
}
 
/**
 * Schedules an algorithm for evaluation
 */
function scheduleForEvaluation( $recommender_app_id, $logfile ) {
  // Check if the entry for the given app_id already exists
  $result = db_query("SELECT app_id from {" . DB_RUN_TABLE . "} where app_id = "
    . ":app_id", 
    array(":app_id" => $recommender_app_id) );
  // If the entry does not exist, add a new one, otherwise just update the old
  if ( $result->rowCount() == 0 ) {
    $nid = db_insert( DB_RUN_TABLE )
      ->fields(array(
        'app_id' => $recommender_app_id,
        'logfile' => $logfile,
        'done' => 0,
      ))->execute();
  } 
  else {
    $num = db_update( DB_RUN_TABLE ) 
      ->fields(array(
        'done' => 0,
        'logfile' => $logfile
      ))
      ->condition('app_id', $recommender_app_id)
      ->execute();
  }
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

/**
 * Write simple log message
 */
function writeLog( $file, $message ) {
  file_put_contents($file, "INFO: " . $message . "\n", FILE_APPEND | LOCK_EX );
}

?>