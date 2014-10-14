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
  
  drupal_set_message("Insert done!");
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

?>