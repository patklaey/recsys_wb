<?php

/**
 * Display statistics for the different recommender algorithms
 */
function recsys_wb_display_stats() {
  // Check if the recommender app SESSION variable is set, if yes display its
  // stats, if not display the form to select the recommender app
  if( isset( $_SESSION['stat_recommender_app'] ) ) {
    // Get all the users rating from the database
    $results = db_query("Select description,created,number1,number2,number3," 
      . "number4,message from {async_command} where id1 = :app_id order by "
      . "created DESC", array(':app_id' => $_SESSION['stat_recommender_app'] ) );
      
    // Prepeare the tables headers and rows
    $header = array( 
      t('Run'), 
      t('Description'), 
      t('# of Users'),
      t('# of Items'),
      t('# of similarity records'),
      t('# of prediction records'),
      t('Time spent'),
    );
    $rows = array();
    
    unset ($_SESSION['stat_recommender_app'] );
    
    // Check if there are already some ratings
    if( $results->rowCount() == 0 ) {
      return "<strong>No statistics available yet</strong></br>";
    }
    else {
      // Loop through the results of the DB query and fill in the tables rows
      foreach ($results AS $result)
      {
        $rows[] = array(
          $result->created,
          $result->description,
          $result->number1,
          $result->number2,
          $result->number3,
          $result->number4,
          $result->message,
        );
      }
    
      // Add the table to the result string
      return theme('table', array('header' => $header, 'rows' => $rows) );
    }
  }
  else{
    return drupal_get_form('recsys_wb_show_statistics_form');
  }
}

?>