<?php

/**
 * An example table which will illustrate the explanations
 */
function recsys_wb_get_example_table() {
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
  return $title . $explanation . recsys_wb_get_example_table();
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
  return $title . $explanation . recsys_wb_get_example_table();
}
 
?>