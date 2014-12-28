<?php

/**
 * Create some nice table with ratings to explain the metrics with an example
 */
function recsys_wb_evaluation_example_accuracy_table() {
  $style = 'text-align:center;vertical-align:middle';
  $header = array(
    array( 'data' => ('User'), 'style' => $style ),
    array( 'data' => t('Algorithm prediction'),'style' => $style ),
    array( 'data' => t('User rating'),'style' => $style ),
  );
  $rows = array(
    array(
      array( 'data' => 'User 1', 'style' => $style ),
      array( 'data' => 4, 'style' => $style ),
      array( 'data' => 3, 'style' => $style ),
    ),
    array(
      array( 'data' => 'User 2', 'style' => $style ),
      array( 'data' => 2, 'style' => $style ),
      array( 'data' => 4, 'style' => $style ),
    ),
    array(
      array( 'data' => 'User 3', 'style' => $style ),
      array( 'data' => 3, 'style' => $style ),
      array( 'data' => 3, 'style' => $style ),
    ),
    array(
      array( 'data' => 'User 4', 'style' => $style ),
      array( 'data' => 5, 'style' => $style ),
      array( 'data' => 4, 'style' => $style ),
    ),
    array(
      array( 'data' => 'User 5', 'style' => $style ),
      array( 'data' => 2, 'style' => $style ),
      array( 'data' => 3, 'style' => $style ),
    ),
  );
  
  return theme( 
    'table',
    array( 
      'header' => $header, 
      'rows' => $rows, 
    )
  );
}

/**
 * Explain why and what evaluation is
 */
function recsys_wb_explain_evaluation() {
  $title = "<strong><h3>Explaining Recommender Systems</h3></strong>";
  $rs_introduction_link = l('2', 'about', array( 'fragment' => 'References') );
  $explanation = "Evaluating recommender algorithms is not an easy task as 
there are simply too many objectiv functions. However since recommender systems 
are widely used and there are a lot of them available, there needs to be 
somthing that determines which one to use<sup>$rs_introduction_link</sup>.<br/>
There are two basic types of evaluation metrics:
<ul>
  <li>Accuracy of prediction<br/>
    Evaluation metrics as Mean Absolute Error (MEA) or Root Mean Squared Error 
    (RMSE) evaluate the algorithms in term of their prediction accuracy.
  </li>
  <li>Accuracy of rank<br/>
    Evaluation metrics as Mean Reciprocal Rank (MRR) or Normalized Discounted 
    Cumulative Gain (NDGC) evalute the algorithms in term of how accurate their 
    predictions are in respect to the rank of the items (the prediction score is
    ignored, only the rank is important).
  </li>
</ul>";
  return $title . $explanation;
}

/**
 * Explain the Mean Absolute Error
 */
function recsys_wb_explain_mae() {
  $title = "<strong><h3>Mean Absolute Error</h3></strong>";
  $explanation = "<div class='tex2jax'>";
  $explanation .= "The Mean Absolut Error metric determines, by how much the 
average prediction is wrong. The formula is straightforward: ";
  $mea = 'MEA(r) = {\sum_{i=0}^I {\sum_{u=0}^U { | pred(r,u,i) - rating(u,i) | 
}} \over I + U}';
  $explanation .= mathBlock($mea);
  $explanation .= "Where " . mathInline('I') . " is the set of items in the test 
set, " . mathInline('U') . " is the set of users in the test set, "
. mathInline('pred(r,u,i)') . " is the prediction of the recommender algorithm "
. mathInline('r') . " for the user " . mathInline('u') . " on the item "
. mathInline('i') . " and " . mathInline('rating(u,i)') . " is the rating of 
user " . mathInline('u') . " on item " . mathInline('i') . ".<br/>Consider the 
following simple example (1 item, 5 users):";
  $explanation .= recsys_wb_evaluation_example_accuracy_table();
  $explanation .= "So the MEA would simply be: ";
  $mea_example = '{|(4-3)| + |(2-4)| + |(3-3)| + |(5-4)| + |(2-3)| \over 5} =';
  $mea_example .= '0.8';
  $explanation .= mathBlock($mea_example);
  $explanation .= "The MEA for itself does not help a lot. In our previous 
example we had an MEA of 0.8. Is it good? Is it bad? Well this depends on the 
rating scale. On a scale from 0 to 100, 0.8 is a very very good result (as the 
algrithm prediction misses the actual rating in average by only 0.8%). However 
on a scale from 0 to 2, 0.8 would be very very bad (as the algrithm prediction 
misses the actual rating in average by 40%). So an MEA of 1.4 on a rating scale 
from 0 to 10 is better than an MAE of 0.9 on a rating scale from 0 to 5.";
  return $title . $explanation . "</div>";
}

/**
 * Explain the Root Mean Squared Error
 */
function recsys_wb_explain_rmse() {
  $title = "<strong><h3>Root Mean Squared Error</h3></strong>";
  $explanation = "<div class='tex2jax'>";
  $explanation .= "The Mean Absolut Error metric determines, by how much the 
average prediction is wrong. The formula is quite similar to the Mean Absolute 
Error, but it penalizes big errors over small ones:";
  $mea = 'RMSE(r) = \sqrt{\sum_{i=0}^I {\sum_{u=0}^U {  (pred(r,u,i) - 
rating(u,i))^2 }} \over I + U}';
  $explanation .= mathBlock($mea);
  $explanation .= "Where " . mathInline('I') . " is the set of items in the test 
set, " . mathInline('U') . " is the set of users in the test set, "
. mathInline('pred(r,u,i)') . " is the prediction of the recommender algorithm "
. mathInline('r') . " for the user " . mathInline('u') . " on the item "
. mathInline('i') . " and " . mathInline('rating(u,i)') . " is the rating of 
user " . mathInline('u') . " on item " . mathInline('i') . ".<br/>Consider the 
following simple example (1 item, 5 users):";
  $explanation .= recsys_wb_evaluation_example_accuracy_table();
  $explanation .= "So the RSME would be: ";
  $mea_example = '\sqrt{(4-3)^2 + (2-4)^2 + (3-3)^2 + (5-4)^2 + (2-3)^2 \over';
  $mea_example .= ' 5} = 1.18';
  $explanation .= mathBlock($mea_example);
  $explanation .= "As for the MEA, the RSME for itself does not help a lot. In 
our previous example we had an RSME of 1.18. Is it good? Is it bad? Well this 
depends on the rating scale. On a scale from 0 to 100, 1.18 is a very very good 
result (as the algrithm prediction misses the actual rating in average by only 
1.18%). However on a scale from 0 to 2, 1.18 would be very very bad (as the 
algrithm prediction misses the actual rating in average by 59%). So an RSME of 
1.4 on a rating scale from 0 to 10 is better than an RSME of 0.9 on a rating 
scale from 0 to 5.";
  return $title . $explanation . "</div>";
}
 
?>