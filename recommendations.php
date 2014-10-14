<?php

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
  $query->join('field_data_field_movie_id','id',
    'p.target_eid = id.field_movie_id_value');
  $query->fields('p',array('target_eid','score'));
  $query->fields('id',array('entity_id'));
  $query->condition('p.score',$score,'>');
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
  $query->join('field_data_field_movie_id','id',
    'p.target_eid = id.field_movie_id_value');
  $query->fields('p',array('target_eid','score'));
  $query->fields('id',array('entity_id'));
  $query->condition('p.source_eid',$user_id);
  $query->condition('p.app_id',$app_id);
  $query->range(0,$n);
  $query->orderBy('p.score','DESC');
  $results = $query->execute();
  
  // Return the results
  return $results;
}

?>