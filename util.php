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
 * Action to take when recsys_wb_movie_rating_form is submitted
 */
function recsys_wb_rate_movie($movie_id, $rating) {
  // The database (add 25% of the users ratings to the test set)
  $database = MOVIE_DB_TRAIN;
  if ( rand(0,100) > 75 )
    $database = MOVIE_DB_TEST;

  // Insert the values into the database
  $result = db_insert( $database )->fields( array(
    'MovieID' => $movie_id,
    'UserID' => user_id,
    'Rating' => $rating,
    'Timestamp' => time(),
  ))->execute();
  
  drupal_set_message("Movie succesfully rated!");
}

function recsys_wb_rate_book($book_id, $rating, $isbn) {
  // The database (add 25% of the users ratings to the test set)
  $database = BOOK_DB_TRAIN;
  if ( rand(0,100) > 75 )
    $database = BOOK_DB_TEST;

  // Insert the values into the database
  $result = db_insert( $database )->fields( array(
    'BookID' => $book_id,
    'UserID' => user_id,
    'Rating' => $rating,
    'ISBN' => $isbn,
  ))->execute();
  
  drupal_set_message("Book succesfully rated!");
}

/**
 * 
 */
function recsys_wb_display_rating($rating) {
  $checked = array( 
    '0.5' => "", 
    '1' => "", 
    '1.5' => "", 
    '2' => "",
    '2.5' => "",
    '3' => "",
    '3.5' => "",
    '4' => "",
    '4.5' => "",
    '5' => "",
  );
  $checked[$rating] = "checked";
  return 'You have already rated this item: <p class="rating">
    <input type="radio" ' . $checked['5'] . ' disabled id="star5" name="rating" value="5" /><label class = "full" for="star5" title="Awesome - 5 stars"></label>
    <input type="radio" ' . $checked['4.5'] . ' disabled id="star4half" name="rating" value="4.5" /><label class="half" for="star4half" title="Pretty good - 4.5 stars"></label>
    <input type="radio" ' . $checked['4'] . ' disabled id="star4" name="rating" value="4" /><label class = "full" for="star4" title="Pretty good - 4 stars"></label>
    <input type="radio" ' . $checked['3.5'] . ' disabled id="star3half" name="rating" value="3.5" /><label class="half" for="star3half" title="Meh - 3.5 stars"></label>
    <input type="radio" ' . $checked['3'] . ' disabled id="star3" name="rating" value="3" /><label class = "full" for="star3" title="Meh - 3 stars"></label>
    <input type="radio" ' . $checked['2.5'] . ' disabled id="star2half" name="rating" value="2.5" /><label class="half" for="star2half" title="Kinda bad - 2.5 stars"></label>
    <input type="radio" ' . $checked['2'] . ' disabled id="star2" name="rating" value="2" /><label class = "full" for="star2" title="Kinda bad - 2 stars"></label>
    <input type="radio" ' . $checked['1.5'] . ' disabled id="star1half" name="rating" value="1.5" /><label class="half" for="star1half" title="Meh - 1.5 stars"></label>
    <input type="radio" ' . $checked['1'] . ' disabled id="star1" name="rating" value="1" /><label class = "full" for="star1" title="Sucks big time - 1 star"></label>
    <input type="radio" ' . $checked['0.5'] . ' disabled id="starhalf" name="rating" value="0.5" /><label class="half" for="starhalf" title="Sucks big time - 0.5 stars"></label>
</p>';
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
  
  return $algorithms;
}

/**
 * Get a link to the question based on its node id
 */
function getQuestionLinkFromNid( $node_id ) {
  $result = db_query("SELECT title from node WHERE nid = :nid", 
    array(":nid" => $node_id)
  );
  
  $link = l( $result->fetchField(), 'node/' . $node_id );
  
  return $link;
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
