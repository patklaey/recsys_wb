<?php

/**
 * Display statistics for the different recommender algorithms
 */
function recsys_wb_display_stats() {
  $return_string = "";
  // The cell style formatting
  $style = 'text-align:center;vertical-align:middle';
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
  // Check if the recommender app SESSION variable is set, if yes display its
  // stats, if not display the form to select the recommender app
  if( isset( $_SESSION['statistics_history_form_submitted'] ) 
&& $_SESSION['statistics_history_form_submitted'] === TRUE ) {
    // Get all the users rating from the database
    $results = db_query("Select description,created,number1,number2,"
      . "number3,number4,message from {async_command} where id1 = :app_id "
      . "order by created DESC" ,
      array(':app_id' => $_SESSION['stat_history_recommender_app'] ) );

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
        $description = preg_replace(
          "/Compute recommendations: /",
          "",
          $result->description
        );

        $rows[] = array(
          'date' => array(
            'data' => $date,
            'style' => $style
          ),
          'description' => array(
            'data' => $description,
            'style' => $style
          ),
          'users' => array(
            'data' => format_integer($result->number1),
            'style' => $style
          ),
          'items' => array(
            'data' => format_integer($result->number2),
            'style' => $style
          ),
          'similarity' => array(
            'data' => format_integer($result->number3),
            'style' => $style
          ),
          'predictions' => array(
            'data' => format_integer($result->number4),
            'style' => $style
          ),
          'time' => array(
            'data' => $time_spent[1],
            'style' => $style
          )
        );
      }
      // Display the table
      $return_string .= theme(
        'table',
        array('header' => $header, 'rows' => $rows)
      );
      // Add the reset form
      $return_string .= "<br/>" . drupal_render(
        drupal_get_form('recsys_wb_reset_form')
      );
    }
  }
  elseif (isset( $_SESSION['statistics_compare_form_submitted'] ) 
&& $_SESSION['statistics_compare_form_submitted'] === TRUE ) {
    // Check if comapre all or not
    $recommender_app_ids = array();
    if ( $_SESSION['stat_compare_type'] === "Submit" ) {
      $recommender_app_ids = $_SESSION['stat_compare_recommender_apps'];
    } 
    else {
      foreach ($_SESSION['stat_compare_recommender_apps'] as $key => $value) {
        $recommender_app_ids[ $key ] = $key;
      }
    }
    foreach ($recommender_app_ids as $key => $value) {
      $result = array();
      if( $value != 0 ) {
        $result = getRecommenderStatistics( $value );
        if ( $result == null )
          $result = array(
            'date' => "NA*",
            'description' => getRecommenderAppTitle($key),
            'users' => 0,
            'items' => 0,
            'similarity' => 0,
            'predictions' => 0,
            'time' => "NA*"
          );
        
        $rows[] = array(
          'date' => array(
            'data' => $result['date'],
            'style' => $style
          ),
          'description' => array(
            'data' => $result['description'],
            'style' => $style
          ),
          'users' => array(
            'data' => format_integer($result['users']),
            'style' => $style
          ),
          'items' => array(
            'data' => format_integer($result['items']),
            'style' => $style
          ),
          'similarity' => array(
            'data' => format_integer($result['similarity']),
            'style' => $style
          ),
          'predictions' => array(
            'data' => format_integer($result['predictions']),
            'style' => $style
          ),
          'time' => array(
            'data' => $result['time'],
            'style' => $style
          )
        );
      }
    }
    // Display the table
    $return_string .= theme(
      'table',
      array('header' => $header, 'rows' => $rows)
    );
    $return_string .= "* not available<br/>";
    
    // Add the reset form
    $return_string .= "<br/>" . drupal_render(
      drupal_get_form('recsys_wb_reset_form')
    );
  }
  else {
    // Display the statistics form
    $return_string .= "<h3>Compare statistics:</h3>";
    $return_string .= drupal_render (
      drupal_get_form('recsys_wb_compare_statistics_form')
    );
    $return_string .= "<br/><strong>OR</strong><br/>";    
    $return_string .= "<h3>Show statistics history of an algorithm:</h3>";
    $return_string .= drupal_render (
      drupal_get_form('recsys_wb_statistics_with_history_form')
    );
  }

  return $return_string;
}

/**
 * 
 */
function getRecommenderStatistics( $app_id ) {
  $results = db_query("Select description,created,number1,number2,"
  . "number3,number4,message from {async_command} where id1 = :app_id "
  . "order by created DESC limit 1" ,
  array(':app_id' => $app_id ) );
  $result = $results->fetchAssoc();
  $date = date('r',$result['created']);
  if ( preg_match("/\(Time spent: (.+)\)/", $result['message'], $time_spent) == 0 )
    return null;
  $description = preg_replace(
    "/Compute recommendations: /",
    "",
    $result['description']
  );
  $stats = array(
    'date' => $date,
    'description' => $description,
    'users' => $result['number1'],
    'items' => $result['number2'],
    'similarity' => $result['number3'],
    'predictions' => $result['number4'],
    'time' => $time_spent[1],
  );
  return $stats;
}

?>