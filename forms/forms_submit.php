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

  // Insert the values into the database
  $result = db_insert( MOVIE_DB )->fields( array(
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

  // Insert the values into the database
  $result = db_insert( BOOK_DB )->fields( array(
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
 * Action to take when recsys_wb_show_statistics_form is submitted
 */
function recsys_wb_show_statistics_form_submit($form, &$form_state) {
  // Just set some session variables
  $_SESSION['stat_recommender_app'] = $form_state['values']['stats_recommender_app'];
}

/**
 * Action to take when recsys_wb_run_recommender_form is submitted
 */
function recsys_wb_run_recommender_form_submit($form, &$form_state) {
  // Get the recommender app name form the id
  $recommender_app_name = getRecommenderAppName(
    $form_state['values']['run_recommender_app']
  );
  
  // Simply schedule the recommendation algorithm for execution
  recommender_create_command($recommender_app_name);
    
  // Generate a UUID
  $uuid = gen_uuid();
  
  // Get the path to the script
  $script_path = DRUPAL_ROOT . DIRECTORY_SEPARATOR 
    . drupal_get_path("module","recommender") . DIRECTORY_SEPARATOR . "run.sh";
    
  // Get the log directory
  $log_dir = DRUPAL_ROOT . DIRECTORY_SEPARATOR 
    . drupal_get_path("module","recsys_wb") . DIRECTORY_SEPARATOR . "runs" 
    . DIRECTORY_SEPARATOR;
    
  // And execute the system command which will run the recommendation algorithm
  exec("nohup setsid $script_path > $log_dir$uuid.log 2>&1 &");
  
  // Display the message that the recommendation is scheduled for execution
  $link = l(
    'here', 
    'tail', 
    array(
      'query' => array('uuid' => $uuid) , 
      'attributes' => array('target' => '_blank') 
    )
  ); 
  drupal_set_message("The recommender algorithm " . $recommender_app_name
    . " is running now. Click " . $link . " to see the progress.");
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
  
  if ( isset( $_SESSION['stat_recommender_app'] ) )
    unset( $_SESSION['stat_recommender_app'] );
}
?>