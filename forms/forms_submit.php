<?php

/**
 * Action to take when recsys_wb_movie_rating_form is submitted
 */
function recsys_wb_movie_rating_form_submit($form, &$form_state) {
  // Get the rating from the form
  $rating = $form_state['values']['rating'];
  
  // Get the node
  $node = menu_get_object();
  
  // Get the movies id
  $movie_id = field_get_items("node", $node, "field_movie_id");
  $movie_id = $movie_id[0]["value"];
  
  // The database (add 25% of the users ratings to the test set)
  $database = MOVIE_DB_TRAIN;
  if ( rand(0,100) > 75 )
    $database = MOVIE_DB_TEST;

  // Insert the values into the database
  $result = db_insert( $database )->fields( array(
    'MovieID' => $movie_id,
    'UserID' => user_id,
    'Rating' => $rating,
    'Timestamp' => time(),
  ))->execute();
  
  drupal_set_message("Movie succesfully rated!");
}


/**
 * Action to take when recsys_wb_book_rating_form is submitted
 */
function recsys_wb_book_rating_form_submit($form, &$form_state) {
  // Get the rating from the form
  $rating = $form_state['values']['rating'];
  
  // Get the node
  $node = menu_get_object();
  
  // Get the books id
  $book_id = field_get_items("node", $node, "field_book_id");
  $book_id = $book_id[0]["value"];
  
  // Get the books isbn
  $isbn = field_get_items("node", $node, "field_isbn");
  $isbn = $isbn[0]["value"];
  
    // The database (add 25% of the users ratings to the test set)
  $database = BOOK_DB_TRAIN;
  if ( rand(0,100) > 75 )
    $database = BOOK_DB_TEST;

  // Insert the values into the database
  $result = db_insert( $database )->fields( array(
    'BookID' => $book_id,
    'UserID' => user_id,
    'Rating' => $rating,
    'ISBN' => $isbn,
  ))->execute();
  
  drupal_set_message("Book succesfully rated!");
}

/**
 * Action to take when recsys_wb_get_recommendations_form is submitted
 */
function recsys_wb_get_recommendations_form_submit($form, &$form_state) {
  // Just set some session variables
  $_SESSION['recommendations_form_submitted'] = TRUE;
  $_SESSION['recommender_app'] = $form_state['values']['recommender_app'];
  $_SESSION['recommender_type'] = $form_state['values']['recommender_type'];
  $_SESSION['recommender_type_value'] = $form_state['values']['recommender_type_value'];
}

/**
 * Action to take when recsys_wb_show_statistics_with_history_form is submitted
 */
function recsys_wb_statistics_with_history_form_submit($form, &$form_state) {
  // Just set some session variables
  $_SESSION['statistics_history_form_submitted'] = TRUE;
  $_SESSION['stat_history_recommender_app'] = $form_state['values']['stats_recommender_app'];
}

/**
 * Action to take when recsys_wb_show_statistics_with_history_form is submitted
 */
function recsys_wb_compare_statistics_form_submit($form, &$form_state) {
  // Just set some session variables
  $_SESSION['statistics_compare_form_submitted'] = TRUE;
  $_SESSION['stat_compare_type'] = $form_state['values']['op'];
  $_SESSION['stat_compare_recommender_apps'] = $form_state['values']['stats_compare_recommender_apps'];
}

/**
 * Action to take when recsys_wb_run_recommender_form is submitted
 */
function recsys_wb_run_recommender_form_submit($form, &$form_state) {
  // Get the recommender app name form the id
  $recommender_app_id = $form_state['values']['run_recommender_app'];
  $recommender_app_name = getRecommenderAppName( $recommender_app_id );
  
  // Simply schedule the recommendation algorithm for execution
  recommender_create_command($recommender_app_name);
  
  // Get the path to the script
  $script_path = DRUPAL_ROOT . DIRECTORY_SEPARATOR 
    . drupal_get_path("module","recommender") . DIRECTORY_SEPARATOR . "run.sh";
    
  // Get the logfile
  $logfile = generateUniqueLogfile();
    
  // Schedule the recommender for evaluation after the run
  scheduleForEvaluation($recommender_app_id, $logfile);
    
  // And execute the system command which will run the recommendation algorithm
  exec("nohup setsid $script_path > $logfile 2>&1 &");
  
  // Display the link to follow the progress
  $message = "The recommender algorithm " . $recommender_app_name
    . " is running now.";
  displayLinkToLogfileTail($logfile, $message );
}

/**
 * Action to take when recsys_wb_compare_form is submitted
 */
function recsys_wb_compare_form_submit($form, &$form_state) {
  // Simply set some SESSION vars
  $_SESSION['recsys_wb_compare_form_submitted'] = TRUE;
  $_SESSION['recsys_wb_compare_app_id'] = $form_state['values']['compare_recommender_app'];
}

/**
 * Action to take when recsys_wb_reset_recommendations_form is submitted
 */
function recsys_wb_reset_form_submit($form, &$form_state) {
  // Simply unset some SESSION vars
  if ( isset( $_SESSION['recommendations_form_submitted'] ) )
    unset( $_SESSION['recommendations_form_submitted'] );
  
  if ( isset( $_SESSION['recsys_wb_compare_form_submitted'] ) )
    unset( $_SESSION['recsys_wb_compare_form_submitted'] );
  
  if ( isset( $_SESSION['statistics_history_form_submitted'] ) )
    unset( $_SESSION['statistics_history_form_submitted'] );
  
  if ( isset ( $_SESSION['statistics_compare_form_submitted'] ) )
    unset( $_SESSION['statistics_compare_form_submitted'] );
  
  if ( isset( $_SESSION['recsys_wb_evaluation_form_submitted'] ) )
    unset( $_SESSION['recsys_wb_evaluation_form_submitted'] );
    
  if ( isset ( $_SESSION['recsys_wb_evaluate_all_form_submitted'] ) )
    unset( $_SESSION['recsys_wb_evaluate_all_form_submitted'] );
  
  if ( isset( $_SESSION['stat_compare_type'] ) )
    unset( $_SESSION['stat_compare_type'] );
 
}

/**
 * Action to take when recsys_wb_evaluation_form is submitted
 */
function recsys_wb_evaluation_form_submit($form, &$form_state) {
  // Simply set some SESSION vars
  $_SESSION['recsys_wb_evaluation_form_submitted'] = TRUE;
  $_SESSION['recsys_wb_evaluation_app_id'] = $form_state['values']['evaluation_recommender_app'];
}

/**
 * Action to take when recsys_wb_evalutae_all_form is submitted
 */
function recsys_wb_evaluate_all_form_submit($form, &$form_state) {
  // Simply set a SESSION var
  $_SESSION['recsys_wb_evaluate_all_form_submitted'] = TRUE;
  print_r($_SESSION);
}

/**
 * Action to take when recsys_wb_evaluate_algorithms_form is submitted
 */
function recsys_wb_evaluate_algorithms_form_submit( $form, &$form_state) {
  // Get the logfile
  $logfile = generateUniqueLogfile();
  
  // Schedule all the selected apps for evaluation
  foreach ($form_state['values']['evalute_algorithms'] as $key => $value) {
    if ( $value != 0 ) {
      // Schedule the recommender for evaluation after the run
      scheduleForEvaluation($value, $logfile);
    }
  }
  
  // And display the link to the logfile where the user can track the progress
  displayLinkToLogfileTail($logfile, "Evaluation in progress.");
  
  // Get the path to the script
  $script_path = DRUPAL_ROOT . DIRECTORY_SEPARATOR 
    . drupal_get_path("module","recsys_wb") . DIRECTORY_SEPARATOR 
    . "scripts" . DIRECTORY_SEPARATOR . "evaluate.sh";
    
  // And execute the evaluation in the background
  exec("nohup setsid $script_path > $logfile 2>&1 &");
}
 
?>