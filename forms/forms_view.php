<?php

/**
 * The rate book form
 */
function recsys_wb_book_rating_form()
{
  $form['user_id'] = array(
    '#type' => 'select',
    '#title' => t('Rating:'),
    '#options' => array(
      '0' => '0',
      '0.5' => '0.5',
      '1' => '1',
      '1.5'=> '1.5',
      '2' => '2',
      '2.5' => '2.5',
      '3' => '3',
      '3.5' => '3.5',
      '4' => '4',
      '4.5' => '4.5',
      '5' => '5',     
    ),
    '#description' => t('Enter the rating for this book'),
    '#required' => TRUE,
  );
  $form['submit'] = array(
    '#type' => 'submit',
    '#value' => t('Submit'),
  );  
  return $form;
}

/**
 * The rate movie form
 */
function recsys_wb_movie_rating_form()
{
  $form['rating'] = array(
    '#type' => 'select',
    '#title' => t('Rating:'),
    '#options' => array(
      '0' => '0',
      '0.5' => '0.5',
      '1' => '1',
      '1.5'=> '1.5',
      '2' => '2',
      '2.5' => '2.5',
      '3' => '3',
      '3.5' => '3.5',
      '4' => '4',
      '4.5' => '4.5',
      '5' => '5',      
    ),
    '#description' => t('Enter the rating for this movie'),
    '#required' => TRUE,
  );
  $form['submit'] = array(
    '#type' => 'submit',
    '#value' => t('Submit'),
  );  
  return $form;
}

/**
 * TODO write some good / useful comment
 */
function recsys_wb_get_recommendations_form() {
  // Create a form which displays a all possible recommender algorithms
  $form['recommender_app'] = array(
    '#type' => 'select',
    '#title' => t('Recommender algorithm:'),
    '#options' => array(
      18 => 'item2item',
      19 => 'user2user',    
    ),
    '#description' => t('Select the recommender algorithm'),
    '#required' => TRUE,
  );
  $form['recommender_type'] = array(
    '#type' => 'radios',
    '#title' => t('Recommendation type:'),
    '#options' => array(
      'top n' => t('Top N'),
      'score' => t('Score'),    
    ),
    '#description' => t('Select the recommendation type'),
    '#required' => TRUE,
  );
  $form['recommender_type_value'] = array(
    '#type' => 'textfield',
    '#title' => t('Value:'),
    '#size' => 5,
    '#description' => t('Enter the value "N" for "Top N" or the score value for'
      .' "Score"'),
    '#required' => TRUE,
  );
  $form['submit'] = array(
    '#type' => 'submit',
    '#value' => t('Submit'),
  );  
  return $form;
}

?>