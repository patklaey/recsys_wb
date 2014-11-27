<?php

require_once 'forms/forms_view.php';
require_once 'forms/forms_submit.php';
require_once 'statistics.php';

// Define some constants
define('GOOD_ITEM', 4);
define('DB_EVAL_TABLE','recsys_wb_recommender_evaluation');
define('DB_RUN_TABLE','recsys_wb_evaluation_run');

/**
 * Displays evaluation form or selected recommender algorithm evaluation
 */
function showEvaluation() {
  // The return string
  $return_string = "";
  if ( ( isset($_SESSION['recsys_wb_evaluation_form_submitted']) 
&& $_SESSION['recsys_wb_evaluation_form_submitted'] === TRUE )
|| ( isset( $_SESSION['recsys_wb_evaluate_all_form_submitted'] ) 
&& $_SESSION['recsys_wb_evaluate_all_form_submitted'] === TRUE ) ) {
    // Prepeare the table header
    // The cell style formatting
    $style = 'text-align:center;vertical-align:middle';
    $header = array( 
      array( 
        'data' => t('Recommender algorithm'), 
        'style' => $style 
      ),
      array( 
        'data' => t('Mean Absolute Error'), 
        'field' => 'mae', 
        'style' => $style 
      ),
      array( 
        'data' => t('Root Mean Squared Error'),
        'field' => 'rmse', 
        'style' => $style 
      ),
      array( 
        'data' => t('Mean Reciprocal Rank'), 
        'field' => 'mrr', 
        'style' => $style 
      ),
      array( 
        'data' => t('Normalized DGC'), 
        'field' => 'ndgc', 
        'style' => $style 
      ),
      array( 
        'data' => t('# of prediction records'), 
        'field' => 'predictions', 
        'style' => $style 
      ),
      array( 
        'data' => t('Time spent'), 
        'field' => 'time', 
        'style' => $style 
      )
    );
    $rows = array();
    $recommender_app_ids = array();
    if ( isset( $_SESSION['recsys_wb_evaluate_all_form_submitted'] )
&& $_SESSION['recsys_wb_evaluate_all_form_submitted'] === TRUE ) 
    {
      $results = db_query("Select id from {recommender_app}");
      foreach ( $results AS $result )
      {
        $recommender_app_ids[ $result->id ] = $result->id;
      }
    } 
    else {
      $recommender_app_ids = $_SESSION['recsys_wb_evaluation_app_id'];
    }

    $results = getEvaluationResults( $header );

    foreach ($results as $eval ) {
      if ( $recommender_app_ids[ $eval->app_id ] > 0 ) {
        $rows[] = array(
          'title' => array(
            'data' => getRecommenderAppTitle( $eval->app_id ),
            'style' => $style
          ),
          'mae' => array(
            'data' => $eval->mae,
            'style' => $style
          ),
          'rmse' => array(
            'data' => $eval->rmse,
            'style' => $style
          ),
          'mrr' => array(
            'data' => $eval->mrr,
            'style' => $style
          ),
          'ndgc' => array(
            'data' => $eval->ndgc,
            'style' => $style
          ),
          'predictions' => array(
            'data' => format_integer($eval->predictions),
            'style' => $style
          ),
          'time' => array(
            'data' => $eval->time,
            'style' => $style
          )
        );
      }
    }

      


    // Render the table
    if ( sizeof($rows) > 0 ) {
      $return_string .= theme(
        'table', 
        array( 
          'header' => $header , 
          'rows' => $rows, 
          'attributes' => array('id' => 'sort-table') 
        )
      );  
    }
    else {
      $return_string .= "Unfortunatley there are no evaluations for the ";
      $return_string .= "selected recommender algorithms.<br/>";
    }
    
    
    // Add the reset form
    $return_string .= "<br/>" . drupal_render( 
      drupal_get_form('recsys_wb_reset_form') 
    );
  } 
  else {
    // Simply display the evaluation from
    $return_string .= drupal_render( 
      drupal_get_form('recsys_wb_evaluation_form') 
    );
    
    $return_string .= "<br/><strong>OR</strong><br/><br/>";
    
    $return_string .= drupal_render(
      drupal_get_form('recsys_wb_evaluate_all_form')
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
  
  // Go through all pending evaluations and calculate them 
  foreach( $results as $result ) {
    $logfile = $result->logfile;
    // Log start progress
    $message = "Recommendations calculated, going to evaluate the calulated "
      . "recommendations ...";
    writeLog($logfile, $message );
    $recommender_app_id = $result->app_id;
    // Check if book or movie recommender
    $recommender_app_name = getRecommenderAppName($recommender_app_id);
    if ( preg_match("/^book/", $recommender_app_name) )
      $test_db = BOOK_DB_TEST;

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
      $results[] = array( 
        "rating" => $result->rating, 
        "score" => $result->score
      );
    }

    writeLog($logfile, "Calculating Mean Absolute Error ...");
    $MAE = meanAbsoluteError($results);
    writeLog($logfile, "Mean Absolute Error calculated: $MAE");
    
    writeLog($logfile, "Calculating Root Mean Squared Error ...");
    $RMSE = rootmeanSquaredError($results);
    writeLog($logfile, "Root Mean Squared Error calculated: $RMSE");
    
    writeLog($logfile, "Calculating Mean Reciprocal Rank ...");
    $MRR = meanReciprocalRank($results);
    writeLog($logfile, "Mean Reciprocal Rank calcualted: $MRR");
    
    writeLog($logfile, "Calculating nDCG ...");
    $nDCG = nDCG($results);
    writeLog($logfile, "nDCG calcualted: $nDCG");
    
    // Write the results to the database
    writeEvaluationResults($recommender_app_id, $MAE, $RMSE, $MRR, $nDCG);
    
    // Write finish process
    writeLog($logfile, "Recommendations evaluated. Run finished!");
  }
    
}

/**
 * Write the evaluation results to the database
 */
function writeEvaluationResults( $app_id, $mea, $rmse, $mrr, $ndgc) {
  // Get the statitics first
  $stats = getRecommenderStatistics($app_id);
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
        'ndgc' => $ndgc,
        'predictions' => $stats['predictions'],
        'time' => $stats['time']
      ))->execute();
  }
  else {
    $num = db_update( DB_EVAL_TABLE ) 
      ->fields(array(
        'mae' => $mea,
        'rmse' => $rmse,
        'mrr' => $mrr,
        'ndgc' => $ndgc,
        'predictions' => $stats['predictions'],
        'time' => $stats['time']
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
function getEvaluationResults( $header ) {
  $select = db_select(DB_EVAL_TABLE, 'e')->extend('TableSort');
  $select->fields( 
    'e',
    array( 'app_id', 'mae', 'rmse', 'mrr', 'ndgc', 'predictions', 'time' )
  );
  $select->orderByHeader($header);
  $results = $select->execute();
  return $results;
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
function meanReciprocalRank( $results ) {
  usort($results,"sortByScore");
  $i = 1;
  foreach ($results as $result) {
    if ( $result['rating'] >= GOOD_ITEM )
      return 1/$i;
    $i++;
  }
  return 0;
}

/**
 * Calculate the nDCG
 */
function nDCG( $results ) {
  usort($results,"sortByScore");
  $gain = discountedCumulativeGain( $results );
  usort($results,"sortByRating");
  $perfect_gain = discountedCumulativeGain( $results );
  return ( $gain / $perfect_gain ) ;
} 

/**
 * Calculate the discounted cumulative gain
 */
function discountedCumulativeGain( $array ) {
  $sum = 0;
  for ($i=0; $i < sizeof( $array ); $i++) { 
    $utility = $array[$i]['rating'];
    $discount = logarithmicDiscount( $i + 1 );
    $sum += $utility * $discount; 
  }
  return $sum;
}

/**
 * Calculate the discount based on a logarithmic scale
 */
function logarithmicDiscount( $position ) {
  return ( 1 / max(1,log($position,2)) );  
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