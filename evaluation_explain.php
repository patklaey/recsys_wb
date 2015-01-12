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
 * Create some nice table with ranks to explain the metrics with an example
 */
function recsys_wb_evaluation_example_rank_table() {
  $style = 'text-align:center;vertical-align:middle';
  $header = array(
    array( 'data' => ('Item'), 'style' => $style ),
    array( 'data' => t('Algorithm prediction'),'style' => $style ),
    array( 'data' => t('User rating'),'style' => $style ),
  );
  $rows = array(
    array(
      array( 'data' => 'Item 1', 'style' => $style ),
      array( 'data' => 4.5, 'style' => $style ),
      array( 'data' => 3, 'style' => $style ),
    ),
    array(
      array( 'data' => 'Item 2', 'style' => $style ),
      array( 'data' => 4.3, 'style' => $style ),
      array( 'data' => 4.5, 'style' => $style ),
    ),
    array(
      array( 'data' => 'Item 3', 'style' => $style ),
      array( 'data' => 4, 'style' => $style ),
      array( 'data' => 4.5, 'style' => $style ),
    ),
    array(
      array( 'data' => 'Item 4', 'style' => $style ),
      array( 'data' => 3.5, 'style' => $style ),
      array( 'data' => 4, 'style' => $style ),
    ),
    array(
      array( 'data' => 'Item 5', 'style' => $style ),
      array( 'data' => 3, 'style' => $style ),
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
  $rs_introduction_link = l('[2]', 'aboutthis', array( 'fragment' => 'References') );
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
  return $explanation;
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
. mathInline('r') . " for the user " . mathInline('u') . " on item with 
index " . mathInline('i') . " and " . mathInline('rating(u,i)') . " is the 
actual rating of user " . mathInline('u') . " on item with index" 
. mathInline('i') . ".<br/>Consider the following simple example (1 item, 5 
users):";
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
from 0 to 10 is better than an MAE of 0.9 on a rating scale from 0 to 5. But 
generally one can say, the smaller the MAE the better the algorithm";
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
  $rmse = 'RMSE(r) = \sqrt{\sum_{i=0}^I {\sum_{u=0}^U { (pred(r,u,i) -
rating(u,i))^2 }} \over I + U}';
  $explanation .= mathBlock($rmse);
  $explanation .= "Where " . mathInline('I') . " is the set of items in the test
set, " . mathInline('U') . " is the set of users in the test set, "
. mathInline('pred(r,u,i)') . " is the prediction of the recommender algorithm "
. mathInline('r') . " for the user " . mathInline('u') . " on the item with 
index " . mathInline('i') . " and " . mathInline('rating(u,i)') . " is the 
actual rating of user " . mathInline('u') . " on item with index " 
. mathInline('i') . ".<br/>Consider the following simple example (1 item, 5 
users):";
  $explanation .= recsys_wb_evaluation_example_accuracy_table();
  $explanation .= "So the RSME would be: ";
  $rsme_example = '\sqrt{(4-3)^2 + (2-4)^2 + (3-3)^2 + (5-4)^2 + (2-3)^2 \over';
  $rsme_example .= ' 5} = 1.18';
  $explanation .= mathBlock($rsme_example);
  $explanation .= "As for the MEA, the RSME for itself does not help a lot. In 
our previous example we had an RSME of 1.18. Is it good? Is it bad? Well this 
depends on the rating scale. On a scale from 0 to 100, 1.18 is a very very good 
result (as the algrithm prediction misses the actual rating in average by only 
1.18%). However on a scale from 0 to 2, 1.18 would be very very bad (as the 
algrithm prediction misses the actual rating in average by 59%). So an RSME of 
1.4 on a rating scale from 0 to 10 is better than an RSME of 0.9 on a rating 
scale from 0 to 5. But generally one can say, the smaller the RSME the better 
the algorithm.";
  return $title . $explanation . "</div>";
}

/**
 * Explain the Mean Reciprocal Rank
 */
function recsys_wb_explain_mrr() {
  $title = "<strong><h3>Mean Reciprocal Rank</h3></strong>";
  $explanation = "<div class='tex2jax'>";
  $mrr_citation_link = l('[3]', 'aboutthis', array( 'fragment' => 'References') );
  $explanation .= "The Mean Reciprocal Rank metric determines how good the 
recommender algorithm is in putting good stuff first<sup>$mrr_citation_link
</sup>. The formula is really simple:" . mathBlock('MRR(r) = {1 \over i}');
  $explanation .= "Where " . mathInline('i') . " is the rank of the first item 
considered as good in the list of predictions produced by the recommender 
algorithm " .  mathInline('r') . ".<br/>Consider the following example (5 items,
 1 user):";
  $explanation .= recsys_wb_evaluation_example_rank_table();
  $explanation .= "Let's say, that a 'good' item is everything what is rated 4 
or more. So in this example the MRR would be 0.5: The first item, the 
recommender algorithm thought it would be very good, is actually not that good. 
The user rated this item only with 3. So this is not a good item. The second 
item, the recommender algorithm also thought it is very good, actually is good. 
The user rated this item 4.5. So as the second item is the first 'good' item the
 MRR of this recommender algorithm is " . mathInline('1 \over 2') . ".";
  return $title . $explanation . "</div>";
}

/**
 * Explain the NDGC
 */
function recsys_wb_explain_ndgc() {
  $title = "<strong><h3>Normalized Discounted Cumulative Gain</h3></strong>";
  $explanation = "<div class='tex2jax'>";
  $ndcg_citation_link = l('[3]', 'aboutthis', array( 'fragment' => 'References') );
  $explanation .= "The Normalized Discounted Normalized Discounted Cumulative 
Gain metric determines how correct the algorithms predictions are in respect of 
the rank the items appear in. It compares the ranked list of the recommender 
algorithm with an perfectly sorted list of the items<sup>$ndcg_citation_link
</sup>. The result of this comparision will be between smaller or equal to 1 
where a perfectly sorted list will get 1. So the closer to 1 the result is, the 
better the algorithm sorts the items. The formula is as follows:";
  $ndcg = 'NDCG(r) = {DCG(r) \over DCG(L_{perfect})}';
  $dcg = 'DCG(r) = \sum_{i=0}^I \sum_{u=0}^U { rating(u,i) \times discount(i)}';
  $discount = 'discount(i) = {1 \over max(1,\log_2{i})}';
  $explanation .= mathBlock($ndcg) . mathBlock($dcg) . mathBlock($discount);
  $explanation .= "Where " . mathInline('r') . " is the recommender algorithm, "
. mathInline('L_{perfect}') . " is a perfectly sorted item list by users ratings
, " . mathInline('I') . " is the set of items in the test set, " 
. mathInline('U') . " is the set of users in the test set and "
. mathInline('rating(u,i)') . " is the rating of user " . mathInline('u')
. " on item with index " . mathInline('i') . ".<br/>So as one can see, the first
 ratings are far more important than the last ones (caused by the logarithmic 
discount). This is because the first items are much more likely to be selected 
by a user so they should have more impact on the result.<br/>Let's have a look 
at the following example (5 items, 1 user): ";
  $explanation .= recsys_wb_evaluation_example_rank_table();
  $explanation .= "In this example the rank of items produced by the algorithm 
is obviously Item 1, Item 2, Item 3, Item 4, Item 5. The " 
. mathInline('L_{perfect}') . " however is Item 2, Item 3, Item 4, Item 1, Item 
5. This leads to";
  $dcg_r = 'DCG(r) = 3 \times 1 + 4.5 \times 1 + 4.5 \times 0.68 + 4 \times 
0.5 + 3 \times 0.43 = 13.85';
  $dcg_l_perfect = 'DCG(L_{perfect}) = 4.5 \times 1 + 4.5 \times 1 + 4 \times 
0.68 + 3 \times 0.5 + 3 \times 0.43 = 14.51';
  $ndcg_r = 'NDCG(r) = {13.85 \over 14.51} = 0.95';
  $explanation .= mathBlock($dcg_r) . mathBlock($dcg_l_perfect) 
. mathBlock($ndcg_r);
  return $title . $explanation . "</div>";
}
?>