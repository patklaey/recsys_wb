<?php
/**
 * @file
 * recsys_workbench_objects.features.inc
 */

/**
 * Implements hook_ctools_plugin_api().
 */
function recsys_workbench_objects_ctools_plugin_api($module = NULL, $api = NULL) {
  if ($module == "feeds" && $api == "feeds_importer_default") {
    return array("version" => "1");
  }
}

/**
 * Implements hook_node_info().
 */
function recsys_workbench_objects_node_info() {
  $items = array(
    'book' => array(
      'name' => t('Book'),
      'base' => 'node_content',
      'description' => t('Simple book representation'),
      'has_title' => '1',
      'title_label' => t('Title'),
      'help' => '',
    ),
    'movie' => array(
      'name' => t('Movie'),
      'base' => 'node_content',
      'description' => t('Simple movie representation'),
      'has_title' => '1',
      'title_label' => t('Title'),
      'help' => '',
    ),
    'project' => array(
      'name' => t('Project'),
      'base' => 'node_content',
      'description' => t('A project which students have to work on'),
      'has_title' => '1',
      'title_label' => t('Title'),
      'help' => '',
    ),
    'question' => array(
      'name' => t('Question'),
      'base' => 'node_content',
      'description' => t('A stackoverflow question'),
      'has_title' => '1',
      'title_label' => t('Title'),
      'help' => '',
    ),
  );
  drupal_alter('node_info', $items);
  return $items;
}
