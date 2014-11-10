<?php

/**
 * Display statistics for the different recommender algorithms
 */
function recsys_wb_display_stats() {
  $return_string = "";
  $compare = "";
  // Check if the recommender app SESSION variable is set, if yes display its
  // stats, if not display the form to select the recommender app
  if( isset( $_SESSION['stat_recommender_app'] ) ) {      
    // Check if the user wants to compare two stats
    if ( isset($_SESSION['recsys_wb_compare_form_submitted']) 
&& $_SESSION['recsys_wb_compare_form_submitted'] === TRUE) {
      $compare = TRUE;
    } 

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
  
    if( ! $compare ) {
      // Get all the users rating from the database
      $results = db_query("Select description,created,number1,number2," 
        . "number3,number4,message from {async_command} where id1 = :app_id "
        . "order by created DESC" , 
        array(':app_id' => $_SESSION['stat_recommender_app'] ) );
        
          // Check if there are already some ratings
      if( $results->rowCount() == 0 ) {
        $return_string .= "<strong>No statistics available yet</strong></br>";
      }
      else {
        // Loop through the results of the DB query and fill in the tables rows
        foreach ($results AS $result)
        {
          $date = date('r',$result->created);
          preg_match("/\(Time spent: (.+)\)/", $result->message, $time_spent);
          
          $rows[] = array(
            $date,
            $result->description,
            $result->number1,
            $result->number2,
            $result->number3,
            $result->number4,
            $time_spent[1],
          );
        }
      }
    }
    else {
      // Get the statistics from the last run
      $results = db_query("Select description,created,number1,number2," 
        . "number3,number4,message from {async_command} where id1 = :app_id "
        . "order by created DESC limit 1" , 
        array(':app_id' => $_SESSION['stat_recommender_app'] ) );
      // Also get the statistics from the last run from the compare algorithm
      $compare_res = db_query("Select description,created,number1,number2," 
        . "number3,number4,message from {async_command} where id1 = :app_id "
        . "order by created DESC limit 1" , 
        array(':app_id' => $_SESSION['recsys_wb_compare_app_id'] ) );
      $result = $results->fetchAssoc();
      $compare_result = $compare_res->fetchAssoc();
      $date = date('r',$result['created']);
      preg_match("/\(Time spent: (.+)\)/", $result['message'], $time_spent);
      $rows[] = array(
        $date,
        $result['description'],
        $result['number1'],
        $result['number2'],
        $result['number3'],
        $result['number4'],
        $time_spent[1],
      );
      $date = date('r',$compare_result['created']);
      preg_match("/\(Time spent: (.+)\)/", $compare_result['message'], $time_spent);
      $rows[] = array(
        $date,
        $compare_result['description'],
        $compare_result['number1'],
        $compare_result['number2'],
        $compare_result['number3'],
        $compare_result['number4'],
        $time_spent[1],
      );
    }
    

    // Add a decent title
    $return_string .= "<h4>Statistics"; 
    
    if ( ! $compare ) {
      $return_string .= " for ";
      $return_string .= getRecommenderAppTitle(
        $_SESSION['stat_recommender_app']
      ) . "</h4>";
    }
    else {
      $return_string .= "<br/>" . getRecommenderAppTitle(
        $_SESSION['stat_recommender_app']
      ) . " vs ";
      $return_string .= getRecommenderAppTitle(
        $_SESSION['recsys_wb_compare_app_id']
      ) . "</h4>";
    }
      
    // Add the table to the result string
    $return_string .= theme(
      'table', 
      array('header' => $header, 'rows' => $rows) 
    );
    
    if ( ! $compare )
    {
      // Add the compare_to form
      $return_string .= drupal_render(
        drupal_get_form('recsys_wb_compare_form')
      );
      
      $return_string .= "<br/><strong>OR</strong><br/>";
    }
    // Add the reset form
    $return_string .= "<br/>" . drupal_render( 
      drupal_get_form('recsys_wb_reset_form') 
    );
  }
  else{
    // Display the statistics form 
    $return_string = drupal_render (
      drupal_get_form('recsys_wb_show_statistics_form')
    );
  }

  return $return_string;
}

?>