<?php

require_once 'util.php';

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
 * @return $recoomendations
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

?>