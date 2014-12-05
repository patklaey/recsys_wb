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
  return theme( 
    'table',
    array( 
      'header' => $header, 
      'rows' => $rows, 
      'caption' => "Rating scale: 1 => Worst, 5 => Best" 
    )
  );
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
Item 3 evaluated. Based on these information, it is now possible to predicts 
Bob's rating for Item 3.<br/>";
  $explanation .= recsys_wb_get_example_table( MARK_USER2USER );
  $explanation .= "In the table above we can see that User 1 is pretty similar 
to Bob. The Items Bob liked were rated as \"good\" by User 1. The items which 
Bob did not like were rated as \"bad\" by User 1. So these two users can be seen 
 as similar. As User 1 liked Item 3, we can infer that Bob will also like Item 
3. A predicted rating would be somewhere between 4 and 5.";
  return $title . $explanation;
}

/**
 * Shortly explain the cosine similarity metrics
 */
function recsys_wb_explain_cosine( $marking = 0 ) {
  $title = "<h2>Cosine similarity</h2>";
  $explanation = "<div class='tex2jax'>";
  $explanation .= "For the cosine similarity the products or users are seen as 
 vectors in n-dimensional space. The similarity of two products or users 
 (vectors) is given by the angle that they form: ";
  $cosine = 'Cosine( \overrightarrow{x},\overrightarrow{y}) = {\overrightarrow{x}
\bullet \overrightarrow{y} \over |\overrightarrow{x}| \times |\overrightarrow{y}
|}';
  $explanation .= mathBlock($cosine);
  $explanation .= "The result will be between 0 and 1 (as normal for a cosine) 
where 0 indicates no similarity and 1 indicates absolute similarity. <br/> Let's
 have a look at a concrete example. Take the table below: ";

   // If marking is user2user or nothing then the example should be user2user
  if ( $marking == MARK_USER2USER || $marking == 0 )
  {
    $explanation .= recsys_wb_get_example_table( MARK_USER2USER );
    $explanation .= "The similarity between User 1 and Bob is:";
    $example = 'Cosine(User 1,Bob) = {4 \times 5 + 1 \times 2 + 2 \times 3 + 4 '
      . '\times 4 \over \sqrt{4^2+1^2+2^2+4^2} \times \sqrt{5^2+2^2+3^2+4^2} } '
      . ' = 0.98';
    $explanation .= mathBlock($example);
    $explanation .= "As we can see a value of 0.98 means that there is some huge 
similarity between User 1 and Bob.<br/>To apply cosine similarity to items the 
process is exactly the same. You just compare the ratings of the given products 
instead of the ratings of the given users (just invert the matrix).";
  }
  else {
    $explanation .= recsys_wb_get_example_table( MARK_ITEM2ITEM );
    $explanation .= "The similarity between Item 1 and Item 5 is:";
    $example = 'Cosine(Item1,Item5) = {4 \times 4 + 2 \times 2 + 5 \times 4 + 3'
      . ' \times 3 \over \sqrt{4^2+2^2+5^2+3^2} \times \sqrt{4^2+2^2+4^2+3^2} }'
      . ' = 0.99';
    $explanation .= mathBlock($example);
    $explanation .= "As we can see a value of 0.99 means that there is some huge 
similarity between Item 1 and Item 5.<br/>To apply cosine similarity to users  
the process is exactly the same. You just compare the ratings of the given users 
 instead of the ratings of the given products (just invert the matrix).";
  }
  return $title . $explanation . "</div>";
}

/**
 * Shortly explain the pearson correlation
 */
function recsys_wb_explain_pearson( $marking = 0 ) {
  $title = "<h2>Pearson similarity</h2>";
  $explanation = "<div class='tex2jax'>";
  $explanation .= "The pearson method calculates the correlation between the 
set of ratings of the users or items with the formula:";
  $pearson = 'Pearson(X,Y) = { \sum_{i=1}^{n} { (X_i - \overline{X})( Y_i - 
\overline{Y}) } \over \sqrt{\sum_{i=1}^{n} { (X_i - \overline{X})^2}} 
\sqrt{\sum_{i=1}^{n} { (Y_i - \overline{Y})^2}}}';
  $explanation .= mathBlock($pearson);
  $explanation .= "Where " . mathInline('\overline{X}') . " and " . 
mathInline('\overline{Y}') . "is the average rating of " . mathInline('X') . 
" and " . mathInline('Y') . " respectively. The result will be between -1 and 1 
where 1 means strong positive correlation (similarity), 0 means no correlation (
no similarity) and -1 means strong negative correlation (dissimilarity).<br/>
Let's have a look at a concrete example. Take the table below: ";

  // If user2user is marked (or nothing)
  if ( $marking == MARK_USER2USER || $marking == 0 ) {
    $explanation .= recsys_wb_get_example_table( MARK_USER2USER );
    $explanation .= "To calculate the similarity between User 1 and Bob we 
first calculate " . mathInline('\overline{User 1}') . " and " 
. mathInline('\overline{Bob}') . ".<br/>";
    $precompute = '\overline{User 1} = {4+1+2+4 \over 4} = 2.75';
    $explanation .= mathBlock($precompute);
    $precompute = '\overline{Bob} = {5+2+3+4 \over 4} =  3.5';
    $explanation .= mathBlock($precompute);
    $sum = '(1.75 \times 1.5) + (-1.75 \times -1.5) + ( -0.75 \times -0.5) + '
      . '(1.75 \times 0.5 )';
    $sqrt_x = '\sqrt{ 3.0625 + 3.0625 + 0.5625 + 3.0625 }';
    $sqrt_y = '\sqrt{ 2.25 + 2.25 + 0.25 + 0.25 }';
    $example = 'Pearson(User 1,Bob) = { ' . $sum . ' \over ' . $sqrt_x 
      . ' \times ' . $sqrt_y . '} = 0.82';
    $explanation .= mathBlock($example);
    $explanation .= "As we can see a value of 0.82 means that there is a  
positive correlation (and therefore similarity) between User 1 and Bob. To 
apply the pearson similarity to items the process is exactly the same. You just 
measure the correlation between the ratings of the given items instead of the 
ratings of the given users (just invert the matrix).";
  }
  else {
    $explanation .= recsys_wb_get_example_table( MARK_ITEM2ITEM );
    $explanation .= "To calculate the similarity between Item 1 and Item 5 we 
first calculate " . mathInline('\overline{Item 1}') . " and " 
. mathInline('\overline{Item 5}') . ".<br/>";
    $precompute = '\overline{Item1} = {4+2+5+3 \over 4} = 3.5';
    $explanation .= mathBlock($precompute);
    $precompute = '\overline{Item5} = {4+2+4+3 \over 4} = 3.25';
    $explanation .= mathBlock($precompute);
    $sum = '(0.5 \times 0.75) + (-1.5 \times -1.25) + ( 1.5 \times 0.75) + '
      . '(-0.5 \times -0.25 )';
    $sqrt_x = '\sqrt{ 0.25 + 2.25 + 2.25 + 0.25 }';
    $sqrt_y = '\sqrt{ 0.5625 + 1.5625 + 0.5625 + 0.0625 }';
    $example = 'Pearson(Item1,Item5) = { ' . $sum . ' \over ' . $sqrt_x 
      . ' \times ' . $sqrt_y . '} = 0.94';
    $explanation .= mathBlock($example);
    $explanation .= "As we can see a value of 0.98 means that there is a strong 
positive correlation (and therefore similarity) between Item 1 and Item 5. To 
apply the pearson similarity to users the process is exactly the same. You just 
measure the correlation between the ratings of the given users instead of the 
ratings of the given items (just invert the matrix).";
  }
  return $title . $explanation . "</div>";
}

/**
 * Shortly explain the euclidean similarity
 */
function recsys_wb_explain_euclidean( $marking = 0 ) {
  $title = "<h2>Euclidean similarity</h2>";
  $explanation = "<div class='tex2jax'>";
  $explanation .= "For the euclidean similarity the products or users are seen 
as points in n-dimensional space. The similarity of two products or users 
(points) is given by their distance:";
  $euclidean = 'Euclidean(X,Y) = \sqrt{ \sum_{i=1}^{n}(X_i - Y_i)^2 }';
  $explanation .= mathBlock($euclidean);
  $explanation .= "The smaller the distance, the bigger the similarity between 
the two users or products. <br/>Let's have a look at a concrete example. Take 
the table below: ";
  $explanation .= recsys_wb_get_example_table( MARK_USER2USER );
  $explanation .= "The similarity between User 1 and Bob is:";
  $example = 'Euclidean(User1,Bob) = \sqrt{(-1)^2 + (-1)^2 + (-1)^2 + 0^2 } = ';
  $example .= " 1.73";
  $explanation .= mathBlock($example);
  $explanation .= "At a first glance, a value of 1.73 seems to be a lot, but the
the distnace between Bob and User 2 and User 3 is 4.24 and 2.65 respectively. To
 apply the euclidean similarity to items the process is exactly the same. You 
just measure the distance between the ratings of the items instead of the 
ratings of the users (just invert the matrix)";
  return $title . $explanation . "</div>";
}
?>