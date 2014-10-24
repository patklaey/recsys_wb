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
  $query->condition('id.field_book_id_value',$id);
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

?>
