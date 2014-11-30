<?php

define("MARK_USER2USER",1);
define("MARK_ITEM2ITEM",2);

/**
 * An example table which will illustrate the explanations
 */
function recsys_wb_get_example_table( $marking = 0 ) {
  $style = 'text-align:center;vertical-align:middle';
  $header = array(
    array( 'data' => ('User'), 'style' => $style ),
    array( 'data' => t('Item 1'),'style' => $style ),
    array( 'data' => t('Item 2'),'style' => $style ),
    array( 'data' => t('Item 3'),'style' => $style ),
    array( 'data' => t('Item 4'),'style' => $style ),
    array( 'data' => t('Item 5'),'style' => $style ),
  );
  $rows = array(
    array( 
      array( 'data' => 'Alice', 'style' => $style ),
      array( 'data' => '?', 'style' => $style ),
      array( 'data' => 2, 'style' => $style ),
      array( 'data' => 4, 'style' => $style ),
      array( 'data' => 3, 'style' => $style ),
      array( 'data' => 5, 'style' => $style ),
    ),
    array(
      array( 'data' => 'User 1', 'style' => $style ),
      array( 'data' => 4, 'style' => $style ),
      array( 'data' => 1, 'style' => $style ),
      array( 'data' => 4, 'style' => $style ),
      array( 'data' => 2, 'style' => $style ),
      array( 'data' => 4, 'style' => $style ),
    ),
    array(
      array( 'data' => 'User 2', 'style' => $style ),
      array( 'data' => 2, 'style' => $style ),
      array( 'data' => 4, 'style' => $style ),
      array( 'data' => 1, 'style' => $style ),
      array( 'data' => 4, 'style' => $style ),
      array( 'data' => 2, 'style' => $style ),
    ),
    array(
      array( 'data' => 'Bob', 'style' => $style ),
      array( 'data' => 5, 'style' => $style ),
      array( 'data' => 2, 'style' => $style ),
      array( 'data' => '?', 'style' => $style ),
      array( 'data' => 3, 'style' => $style ),
      array( 'data' => 4, 'style' => $style ),
    ),
    array(
      array( 'data' => 'User 3', 'style' => $style ),
      array( 'data' => 3, 'style' => $style ),
      array( 'data' => 1, 'style' => $style ),
      array( 'data' => 4, 'style' => $style ),
      array( 'data' => 4, 'style' => $style ),
      array( 'data' => 3, 'style' => $style ),
    ),
  );
  
  // Append the red color to the style to mark ratings
  $style_mark = $style . ";color:red";
  
  // Mark user2user => Similar users: Bob and User 1
  if( $marking == MARK_USER2USER ) {
    // Mark Bobs ratings
    foreach ($rows[3] as &$row ) {
      $row['style'] = $style_mark;
    }    
    
    // Mark User1s ratings
    foreach ($rows[1] as &$row ) {
      $row['style'] = $style_mark;
    }
  }
  
  // Mark item2item => Similar items: Item 1 and Item 5
  if ( $marking == MARK_ITEM2ITEM ) {
    // Mark Item 1
    foreach ($rows as &$row) {
      $row[1]['style'] = $style_mark;   
    }

    // Mark Item 5
    foreach ($rows as &$row) {
      $row[5]['style'] = $style_mark;   
    }
  }
  return theme('table',array( 'header' => $header, 'rows' => $rows ) );
}

/**
 * Shortly explain what item2item algorithms do
 */
function recsys_wb_explain_item2item() {
  $title = "<h2>Item - Item Recommendation</h2>";
  $explanation = "Item-Item recommender algorithms analyze the similarity 
between the different items in the given dataset. To predict how much Alice 
might like Item 1, the n most similar items to Item1 (which the user has already
 rated) are selected and the users ratings are evaluated. Based on these 
information, it is now possible to predict Alice's rating for Item 1.<br/>";
  $explanation .= recsys_wb_get_example_table( MARK_ITEM2ITEM );
  $explanation .= "In the table above we can see that Item 1 is similar to Item
 5. The users which liked Item 1 (User 1 and Bob) also liked Item 5. User 2 did 
not like both. And User 3 could' not really decide weather or not he liked those
 two Items. Generally we can say that users who liked Item 1 also liked Item 5 
and users who didn't like Item 1 neither liked Item 5. As Alice liked Item 5, we
 can assume that she will also like Item 1, a predicted rating would be 
somewhere between 4 and 5.";
  return $title . $explanation;
}

/**
 * Shortly explain what user2user algorithms do
 */
function recsys_wb_explain_user2user() {
  $title = "<h2>User - User Recommendation</h2>";
  $explanation = "User-User recommender algorithms analyze the similarity 
between the different users in the given dataset. To predict how much Bob might 
like Item 3, the n most similar users to Bob are selected and their ratings of 
Item 3 evaluated. Based on these infomration, it is now possible to predicts 
Bob's rating for Item 3.<br/>";
  return $title . $explanation . recsys_wb_get_example_table( MARK_USER2USER );
}
 
?>