<?php

require_once 'util.php';
require_once 'forms/forms_view.php';
require_once 'forms/forms_submit.php';

/**
 * @param user_id int
 * @param recommender_app_id int
 * @param score double
 * @return $query_result
 * Get the recommendations from the recommender api for a given user on a given
 * recommender algorithm ( recommender_app ID) for the given minimal prediction
 * score
 */
function getRecommendationsByScore( $user_id = 0, $app_id = 0, $score = 0 ) {
  
  // Fetch all recommendations from the prediction table
  $query = db_select('recommender_prediction','p');
  $query->fields('p',array('target_eid','score'));
  $query->condition('p.score',$score,'>=');
  $query->condition('p.source_eid',$user_id);
  $query->condition('p.app_id',$app_id);
  $query->orderBy('p.score','DESC');
  $results = $query->execute();
  
  // Return the results
  return $results;
}

/**
 * @param user_id int
 * @param recommender_app_id int
 * @param n int
 * @return $query_result
 * Get the top n recommendations from the recommender api for a given user on a 
 * given recommender algorithm ( recommender_app ID)
 */
function getTopNRecommendations( $user_id = 0, $app_id = 0, $n = 10 ) {
  
  // Fetch all recommendations from the prediction table
  $query = db_select('recommender_prediction','p');
  $query->fields('p',array('target_eid','score'));
  $query->condition('p.source_eid',$user_id);
  $query->condition('p.app_id',$app_id);
  $query->range(0,$n);
  $query->orderBy('p.score','DESC');
  $results = $query->execute();
  
  // Return the results
  return $results;
}

/**
 * @param type String
 * @param user_id int
 * @param recommender_app_id int
 * @param n int
 * @return $recomendations
 * Get book recommendations for a given type an a given app id and user
 */
function getBookRecommendations($type = 'top_n', $user_id = 0, $app_id = 0, $n = 0) {
  
  $recommendations;
  // Check the type and call the corresponding algorithm
  if ( $type === 'top_n' ) {
    $recommendations = getTopNRecommendations($user_id,$app_id,$n);
  } elseif ( $type === 'score' ) {
    $recommendations = getRecommendationsByScore($user_id,$app_id,$n);
  } else {
    return NULL;
  }
    
  $returns = array();
  
  // For each result get the book name by the id
  foreach ( $recommendations AS $recommendation ) {
    $returns[] = array(
      getBookNameById( $recommendation->target_eid ),
      $recommendation->score
    );
  }
  
  return $returns;
}

/**
 * @param type String
 * @param user_id int
 * @param recommender_app_id int
 * @param n int
 * @return $recoomendations
 * Get movie recommendations for a given type an a given app id and user
 */
function getMovieRecommendations($type = 'top_n', $user_id = 0, $app_id = 0, $n = 0) {
  
  $recommendations;
  // Check the type and call the corresponding algorithm
  if ( $type === 'top_n' ) {
    $recommendations = getTopNRecommendations($user_id,$app_id,$n);
  } elseif ( $type === 'score' ) {
    $recommendations = getRecommendationsByScore($user_id,$app_id,$n);
  } else {
    return NULL;
  }
    
  $returns = array();
  
  // For each result get the book name by the id
  foreach ( $recommendations AS $recommendation ) {
    $returns[] = array(
      getMovieNameById( $recommendation->target_eid ),
      $recommendation->score
    );
  }
  
  return $returns;
}

/**
 * Show recommendations with forms etc
 */
function showRecommendations() {
  // Define the return string variable
  $return_string = "";
  
  // Check if the session variable form submitted is set. If yes display the 
  // results, otherwise display the form
  if ( isset( $_SESSION['recommendations_form_submitted'] )
&& $_SESSION['recommendations_form_submitted'] === TRUE ) {
      
    // Initialize some variables;
    $compare = FALSE;
    $compare_results = array();
    $compare_app = "";
    $results = array();
    $header = array();
    $rows = array();
    
    // Get the recommender app session var
    $recommender_app = $_SESSION['recommender_app'];
    
    // Get the recommender_type_value session var
    $value = $_SESSION['recommender_type_value'];
    
    // Get the recommender app name
    $recommender_app_name = getRecommenderAppName($recommender_app);
    
    // Check if the user wants to compare two algorithms
    if ( isset($_SESSION['recsys_wb_compare_form_submitted']) 
&& $_SESSION['recsys_wb_compare_form_submitted'] === TRUE) {
      $compare = TRUE;
    }
    
    // Check which recommender type if is
    $type = 'score';
    if ( $_SESSION['recommender_type'] === 'top n' )
      $type = 'top_n';
    
    if ( preg_match("/^book/", $recommender_app_name) ) {
      $results = getBookRecommendations(
        $type, 
        user_id, 
        $recommender_app, 
        $value 
      );
      $entity = 'Book';
      
      // Check if compare is set
      if ( $compare ) {
        $compare_app = $_SESSION['recsys_wb_compare_app_id'];
        $compare_results = getBookRecommendations(
          $type, 
          user_id, 
          $compare_app, 
          $value 
        );
      }
    } 
    else {
      // Get movie recommendations
      $results = getMovieRecommendations(
        $type, 
        user_id, 
        $recommender_app, 
        $value 
      );
      $entity = 'Movie';
      
      // Check if compare is set
      if ( $compare ) {
        $compare_app = $_SESSION['recsys_wb_compare_app_id'];
        $compare_results = getMovieRecommendations(
          $type, 
          user_id, 
          $compare_app, 
          $value 
        );
      }
    } 
  
    if ( sizeof($results) == 0 )
    {
      unset( $_SESSION['recommendations_form_submitted'] );
      return "You have no recommendations yet. Make sure you rate some movies "
             . " and/or books and come back later!" . drupal_render( 
             drupal_get_form('recsys_wb_reset_form') );
    }

    if ( $compare ) {
      // Prepare the table headers and rows
      $header = array(
        getRecommenderAppTitle($recommender_app),
        t('Score'),
        getRecommenderAppTitle($compare_app),
        t('Score'),
      );
      $rows = array();
      
      if ( sizeof($compare_results) > sizeof($results) )
      {
        $max = sizeof($compare_results);
      }
      else {
        $max = sizeof($results);
      }
      
      // Loop through all results and fill up the list
      for ($i=0; $i < $max; $i++) { 
        $orig = array("-","-");
        $comp = array("-","-");
        
        if ( array_key_exists($i, $results) ) {
          $orig = $results[$i];
          $orig[0] = createEntityLinkByName($orig[0]);
        }
        
        if ( array_key_exists($i, $compare_results) ) {
          $comp = $compare_results[$i];
          $comp[0] = createEntityLinkByName($comp[0]);
        }
        
        $rows[] = array($orig[0],$orig[1],$comp[0],$comp[1]);
      }
    }
    else {
      // Prepare the table headers and rows
      $header = array( $entity, t('Score') );
      $rows = array();
      
      // Loop through the db query results and add each of them to the rows 
      // array
      foreach ($results as $result ) {
        $result[0] = createEntityLinkByName($result[0]);
        $rows[] = $result;
      }
    }
  
    // Add some description
    if ( $type === 'score' ) {
      if ( ! $compare )
      {
        $return_string .= "<h4>Recommendations based on prediction score >= ";
        $return_string .= $value . " and algorithm ";
        $return_string .=  getRecommenderAppTitle($recommender_app) . "</h4>";
      }
      else {
        $return_string .= "<h4>Recommendations based on prediction score >= ";
        $return_string .= $value . "<br/>";
        $return_string .= getRecommenderAppTitle($recommender_app) . " vs. ";
        $return_string .= getRecommenderAppTitle($compare_app) . "</h4>";
      }
    }
    else {
      if ( ! $compare )
      {
        $return_string .= "<h4>Top " . $value . " recommendations based on ";
        $return_string .= getRecommenderAppTitle($recommender_app) . "</h4>";
      }
      else {
        $return_string .= "<h4>Top " . $value . " recommendations<br/>";
        $return_string .= getRecommenderAppTitle($recommender_app) . " vs. ";
        $return_string .= getRecommenderAppTitle($compare_app) . "</h4>";
      }
    }  
  
    // Assign the renderable array to the return string 
    $return_string .= theme(
      'table', 
      array( 'header' => $header, 'rows' => $rows ) 
    );
    
    // Add the compare_to form
    if ( ! $compare ) {
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
    $return_string .= drupal_render( 
      drupal_get_form('recsys_wb_get_recommendations_form')
    );
  }

  return $return_string;
}
?>