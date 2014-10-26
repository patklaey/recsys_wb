<?php

/**
 * Get the movie name from its ID
 */
function getMovieNameById( $id ){
  // Join the id and node table on the entity_id/nid attribute and select the 
  // nodes title
  $query = db_select('node','node');
  $query->join('field_data_field_movie_id','id','node.nid = id.entity_id');
  $query->condition('id.field_movie_id_value',$id);
  $query->fields('node',array('title'));
  $results = $query->execute();
  
  if ( $results->rowCount() != 1 )
    return "ERROR";
  
  return $results->fetchField();
}

/**
 * Get the book name from its ID
 */
function getBookNameById( $id ){
  // Join the id and node table on the entity_id/nid attribute and select the 
  // nodes title
  $query = db_select('node','node');
  $query->join('field_data_field_book_id','id','node.nid = id.entity_id');
  $query->join('field_data_field_dataset','data','node.nid = data.entity_id');
  $query->condition('id.field_book_id_value',$id);
  $query->condition('data.field_dataset_value','99');
  $query->fields('node',array('title'));
  $results = $query->execute();
  
  if ( $results->rowCount() != 1 )
    return "ERROR";
  
  return $results->fetchField();
}

/**
 * Get the recommender app name by the recommender app id
 */
function getRecommenderAppName( $recommender_app_id = 0 ) {
  // Simply execute a simple database query to get the app name
  $result = db_query("SELECT name from {recommender_app} where id = :id", 
    array(":id" => $recommender_app_id) );
    
  if ( $result->rowCount() == 0 )
    return NULL;
  
  return $result->fetchField();
}

/**
 * Get the recommender app title by the recommender app id
 */
function getRecommenderAppTitle( $recommender_app_id = 0 ) {
  // Simply execute a simple database query to get the app name
  $result = db_query("SELECT title from {recommender_app} where id = :id", 
    array(":id" => $recommender_app_id) );
    
  if ( $result->rowCount() == 0 )
    return NULL;
  
  return $result->fetchField();
}

/**
 * Get the recommender algorithms for form dropdown
 */
function getRecommenderAppsForForm() {
  $results = db_query("Select id,title from {recommender_app}");
  $algorithms = array();
  foreach ( $results AS $result )
  {
    $algorithms[$result->id] = $result->title;
  }
  return $algorithms;
}

function gen_uuid() {
    return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        // 32 bits for "time_low"
        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),

        // 16 bits for "time_mid"
        mt_rand( 0, 0xffff ),

        // 16 bits for "time_hi_and_version",
        // four most significant bits holds version number 4
        mt_rand( 0, 0x0fff ) | 0x4000,

        // 16 bits, 8 bits for "clk_seq_hi_res",
        // 8 bits for "clk_seq_low",
        // two most significant bits holds zero and one for variant DCE1.1
        mt_rand( 0, 0x3fff ) | 0x8000,

        // 48 bits for "node"
        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
    );
}

?>
