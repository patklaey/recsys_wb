<?php

define("WHOLE_MATCH",0);

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
 * Create a link to the book or movie by the given name
 */
function createEntityLinkByName( $name ) {
  // Get the books node id by its name and return the link
  $result = db_query("SELECT nid from {node} where title = :title", 
    array(":title" => $name) );

  if ( $result->rowCount() == 0 )
    return NULL;
  
  return l($name, "node/" . $result->fetchField() );    
    
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
  
  // Add the content recommender which will get ID -1
  $algorithms[-1] = "Content Recommender (cosine)"; 
  return $algorithms;
}

/**
 * Generate a universal unique identifier
 */
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

/**
 * Format a given double number nicely
 */
function format_double( $number ) {
  return number_format($number,3,".","`");
}

/**
 * Format a given integer number nicely
 */
function format_integer( $number ) {
  return number_format($number,0,".","`");
}
 
/**
 * Generate a unique logfile
 */
function generateUniqueLogfile() {
  // Generate a UUID
  $uuid = gen_uuid();
  
  // Get the log directory
  $log_dir = DRUPAL_ROOT . DIRECTORY_SEPARATOR 
    . drupal_get_path("module","recsys_wb") . DIRECTORY_SEPARATOR . "log" 
    . DIRECTORY_SEPARATOR;
    
  // Return the unique logfile
  return $log_dir . $uuid . ".log";
}

/**
 * Extracts the UUID from the given filename
 */
function extractUUIDFromFilename( $filename ) {
  $uuid_pattern = "/[a-f0-9]{8}\-[a-f0-9]{4}\-4[a-f0-9]{3}\-(8|9|a|b)[a-f0-9]{3}\-[a-f0-9]{12}/";
  preg_match($uuid_pattern, $filename, $uuid);
  return $uuid[WHOLE_MATCH];
}

/**
 * 
 */
function generateLinkToLogfileTail($logfile_uuid, $link_text) {
  return l(
    $link_text, 
    'tail', 
    array(
      'query' => array('uuid' => $logfile_uuid) , 
      'attributes' => array('target' => '_blank') 
    )
  );
}

/**
 * 
 */
function displayLinkToLogfileTail( $logfile, $message = "" ) {
  // Extract the uuid from the logfile
  $uuid = extractUUIDFromFilename( $logfile );
  
  // Display the message that the recommendation is scheduled for execution
  $link = generateLinkToLogfileTail($uuid, "here"); 
  drupal_set_message( $message . " Click " . $link . " to see the progress.");
}

/**
 * 
 */
function format_time( $time_in_seconds ) {
  $date_formatted = gmdate("H:i:s", $time_in_seconds);
  $date = explode(":", $date_formatted);
  $hours = (int) $date[0];
  $minutes = (int) $date[1];
  $seconds = (int) $date[2];
  return $hours . "h" . $minutes . "m" . $seconds . "s";
}

/**
 * Code for an math inline formula
 */
function mathInline( $formula ) {
  return "\( " . $formula . "\)";
}

/**
 * Code for an math inline formula
 */
function mathBlock( $formula ) {
  return "\[ " . $formula . "\]";
}
?>
