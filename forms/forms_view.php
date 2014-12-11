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
  $algorithms = getRecommenderAppsForForm();
  
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
    '#description' => t('Enter the value N for "Top N" or the minimal '
      .'prediction score value for "Score"'),
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
function recsys_wb_statistics_with_history_form() {
  // Get all different recommender algorithms currently registered
  $algorithms = getRecommenderAppsForForm();
  
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

/**
 * Display all recommender algorithms to be able to display some useful 
 * statistics about it
 */
function recsys_wb_compare_statistics_form() {  
  // Get all different recommender algorithms currently registered
  $algorithms = getRecommenderAppsForForm();
  
  // Create a simple form which only lets the user select the recommender 
  // algorithm
  $form['stats_compare_recommender_apps'] = array(
    '#type' => 'checkboxes',
    '#title' => t('Recommender algorithm:'),
    '#options' => $algorithms,
    '#description' => t('Select the recommender algorithm')
  );
  $form['submit'] = array(
    '#type' => 'submit',
    '#value' => t('Submit'),
  );
  $form['compare_all'] = array(
    '#type' => 'submit',
    '#value' => t('Compare all'),
  );
  return $form;  
}

/**
 * Display all recommender apps to be able to run it
 */
function recsys_wb_run_recommender_form() {
  // Get all different recommender algorithms currently registered
  $algorithms = getRecommenderAppsForForm();
  
  // Create a simple form which only lets the user select the recommender 
  // algorithm
  $form['run_recommender_app'] = array(
    '#type' => 'select',
    '#title' => t('Recommender algorithm:'),
    '#options' => $algorithms,
    '#description' => t('Select the recommender algorithm to run'),
    '#required' => TRUE,
  );
  $form['submit'] = array(
    '#type' => 'submit',
    '#value' => t('Submit'),
  );  
  return $form;  
}

/**
 * Display a form which lets the user select another recommendation algorithm
 * which will be then used to compare it to the previously selected one
 */
function recsys_wb_compare_form() {
  // Get all different recommender algorithms currently registered
  $algorithms = getRecommenderAppsForForm();  
  
  // Create a simple form which only lets the user select the recommender 
  // algorithm
  $form['compare_recommender_app'] = array(
    '#type' => 'select',
    '#title' => t('Compare to:'),
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

/**
 * Display a single button to reset the recommendation properties
 */
function recsys_wb_reset_form() {
  // A simple reset submit button
  $form['submit'] = array(
    '#type' => 'submit',
    '#value' => t('Reset'),
  );  
  return $form; 
}

/**
 * Display all recommender algorithms to be able to display some useful 
 * evaluation about it
 */
function recsys_wb_evaluation_form() {
  // Get all different recommender algorithms currently registered
  $algorithms = getRecommenderAppsForForm();
  
  // Create a simple form which only lets the user select the recommender 
  // algorithm
  $form['evaluation_recommender_app'] = array(
    '#type' => 'checkboxes',
    '#title' => t('Recommender algorithm:'),
    '#options' => $algorithms,
    '#description' => t('Select the recommender algorithms to show evaluation'),
    '#required' => TRUE,
  );
  $form['submit'] = array(
    '#type' => 'submit',
    '#value' => t('Submit'),
  );  
  return $form;  
}

function recsys_wb_evaluate_all_form() {

  $form['submit'] = array(
    '#type' => 'submit',
    '#value' => t('Compare all'),
  );  
  return $form;  
}

/**
 * 
 */
function recsys_wb_evaluate_algorithms_form() {
  // Get all different recommender algorithms currently registered
  $algorithms = getRecommenderAppsForForm();
  
  // Create a simple form which only lets the user select the recommender 
  // algorithm
  $form['evalute_algorithms'] = array(
    '#type' => 'checkboxes',
    '#title' => t('Recommender algorithm:'),
    '#options' => $algorithms,
    '#description' => t('Select the recommender algorithms to evaluate')
  );
  $form['submit'] = array(
    '#type' => 'submit',
    '#value' => t('Evaluate'),
  );
  $form['evaluate_all'] = array(
    '#type' => 'submit',
    '#value' => t('Evaluate all'),
  );
  return $form;  
}

/**
 * 
 */
function recsys_wb_run_content_recommender_form() {
  $form['submit'] = array(
    '#type' => 'submit',
    '#value' => t('Run'),
  );
  return $form;
}
?>