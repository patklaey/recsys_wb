<?php

/**
 * The rate book form
 */
function recsys_wb_book_rating_form()
{
  $form['rating'] = array(
    '#type' => 'select',
    '#title' => t('Rating:'),
    '#options' => array(
      '0' => '0',
      '1' => '1',
      '2' => '2',
      '3' => '3',
      '4' => '4',
      '5' => '5',
      '6' => '6',
      '7' => '7',
      '8' => '8',
      '9' => '9',
      '10' => '10',
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
  // Get all different recommender algorithms currently registered
  $results = db_query("Select id,title from {recommender_app}");
  $algorithms = array();
  
  foreach ( $results AS $result )
  {
    $algorithms[$result->id] = $result->title;
  }
  
  // Create a form which displays a all possible recommender algorithms
  $form['recommender_app'] = array(
    '#type' => 'select',
    '#title' => t('Recommender algorithm:'),
    '#options' => $algorithms,
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

/**
 * Display all recommender algorithms to be able to display some useful 
 * statistics about it
 */
function recsys_wb_show_statistics_form() {
  // Get all different recommender algorithms currently registered
  $results = db_query("Select id,title from {recommender_app}");
  $algorithms = array();
  
  foreach ( $results AS $result )
  {
    $algorithms[$result->id] = $result->title;
  }
  
  // Create a simple form which only lets the user select the recommender 
  // algorithm
    $form['stats_recommender_app'] = array(
    '#type' => 'select',
    '#title' => t('Recommender algorithm:'),
    '#options' => $algorithms,
    '#description' => t('Select the recommender algorithm'),
    '#required' => TRUE,
  );
  $form['submit'] = array(
    '#type' => 'submit',
    '#value' => t('Submit'),
  );  
  return $form;  
}
?>