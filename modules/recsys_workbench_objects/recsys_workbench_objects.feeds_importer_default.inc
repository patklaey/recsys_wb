<?php
/**
 * @file
 * recsys_workbench_objects.feeds_importer_default.inc
 */

/**
 * Implements hook_feeds_importer_default().
 */
function recsys_workbench_objects_feeds_importer_default() {
  $export = array();

  $feeds_importer = new stdClass();
  $feeds_importer->disabled = FALSE; /* Edit this to true to make a default feeds_importer disabled initially */
  $feeds_importer->api_version = 1;
  $feeds_importer->id = 'book_import';
  $feeds_importer->config = array(
    'name' => 'Book import',
    'description' => 'Imports books',
    'fetcher' => array(
      'plugin_key' => 'FeedsFileFetcher',
      'config' => array(
        'allowed_extensions' => 'txt csv tsv xml opml',
        'direct' => 0,
        'directory' => 'public://feeds',
        'allowed_schemes' => array(
          'public' => 'public',
        ),
      ),
    ),
    'parser' => array(
      'plugin_key' => 'FeedsCSVParser',
      'config' => array(
        'delimiter' => ',',
        'no_headers' => 0,
      ),
    ),
    'processor' => array(
      'plugin_key' => 'FeedsNodeProcessor',
      'config' => array(
        'expire' => '-1',
        'author' => '1',
        'authorize' => 1,
        'mappings' => array(
          0 => array(
            'source' => 'ID',
            'target' => 'field_book_id',
            'unique' => FALSE,
          ),
          1 => array(
            'source' => 'ISBN',
            'target' => 'field_isbn',
            'unique' => FALSE,
          ),
          2 => array(
            'source' => 'Title',
            'target' => 'title',
            'unique' => FALSE,
          ),
          3 => array(
            'source' => 'Author',
            'target' => 'field_author',
            'unique' => FALSE,
          ),
          4 => array(
            'source' => 'Year of Publication',
            'target' => 'field_year_of_publication',
            'unique' => FALSE,
          ),
          5 => array(
            'source' => 'Publisher',
            'target' => 'field_publisher',
            'unique' => FALSE,
          ),
          6 => array(
            'source' => 'Thumbnail',
            'target' => 'field_thumbnail_url',
            'unique' => FALSE,
          ),
          7 => array(
            'source' => 'Image',
            'target' => 'field_image_url',
            'unique' => FALSE,
          ),
          8 => array(
            'source' => 'Dataset',
            'target' => 'field_dataset',
            'unique' => FALSE,
          ),
        ),
        'update_existing' => '0',
        'input_format' => 'plain_text',
        'skip_hash_check' => 0,
        'bundle' => 'book',
      ),
    ),
    'content_type' => '',
    'update' => 0,
    'import_period' => '-1',
    'expire_period' => 3600,
    'import_on_create' => 1,
    'process_in_background' => 0,
  );
  $export['book_import'] = $feeds_importer;

  $feeds_importer = new stdClass();
  $feeds_importer->disabled = FALSE; /* Edit this to true to make a default feeds_importer disabled initially */
  $feeds_importer->api_version = 1;
  $feeds_importer->id = 'movies_import';
  $feeds_importer->config = array(
    'name' => 'Movies Import',
    'description' => 'Imports movies',
    'fetcher' => array(
      'plugin_key' => 'FeedsFileFetcher',
      'config' => array(
        'allowed_extensions' => 'txt csv tsv xml opml',
        'direct' => 0,
        'directory' => 'public://feeds',
        'allowed_schemes' => array(),
      ),
    ),
    'parser' => array(
      'plugin_key' => 'FeedsCSVParser',
      'config' => array(
        'delimiter' => ',',
        'no_headers' => 0,
      ),
    ),
    'processor' => array(
      'plugin_key' => 'FeedsNodeProcessor',
      'config' => array(
        'expire' => '-1',
        'author' => '1',
        'authorize' => 1,
        'mappings' => array(
          0 => array(
            'source' => 'Title',
            'target' => 'title',
            'unique' => FALSE,
          ),
          1 => array(
            'source' => 'ID',
            'target' => 'field_movie_id',
            'unique' => FALSE,
          ),
          2 => array(
            'source' => 'Year',
            'target' => 'field_year',
            'unique' => FALSE,
          ),
          3 => array(
            'source' => 'Genre',
            'target' => 'field_genres',
            'unique' => FALSE,
          ),
        ),
        'update_existing' => '0',
        'input_format' => 'plain_text',
        'skip_hash_check' => 0,
        'bundle' => 'movie',
      ),
    ),
    'content_type' => '',
    'update' => 0,
    'import_period' => '-1',
    'expire_period' => 3600,
    'import_on_create' => 1,
    'process_in_background' => 0,
  );
  $export['movies_import'] = $feeds_importer;

  $feeds_importer = new stdClass();
  $feeds_importer->disabled = FALSE; /* Edit this to true to make a default feeds_importer disabled initially */
  $feeds_importer->api_version = 1;
  $feeds_importer->id = 'question';
  $feeds_importer->config = array(
    'name' => 'Question',
    'description' => 'Imports stackoverflow question',
    'fetcher' => array(
      'plugin_key' => 'FeedsFileFetcher',
      'config' => array(
        'allowed_extensions' => 'txt csv tsv xml opml',
        'direct' => FALSE,
        'directory' => 'public://feeds',
        'allowed_schemes' => array(
          0 => 'public',
        ),
      ),
    ),
    'parser' => array(
      'plugin_key' => 'FeedsCSVParser',
      'config' => array(
        'delimiter' => ',',
        'no_headers' => 0,
      ),
    ),
    'processor' => array(
      'plugin_key' => 'FeedsNodeProcessor',
      'config' => array(
        'expire' => '-1',
        'author' => 0,
        'authorize' => 1,
        'mappings' => array(
          0 => array(
            'source' => 'ID',
            'target' => 'field_question_id',
            'unique' => FALSE,
          ),
          1 => array(
            'source' => 'Title',
            'target' => 'title',
            'unique' => FALSE,
          ),
          2 => array(
            'source' => 'Body',
            'target' => 'body',
            'unique' => FALSE,
          ),
          3 => array(
            'source' => 'Score',
            'target' => 'field_question_score',
            'unique' => FALSE,
          ),
          4 => array(
            'source' => 'Creation date',
            'target' => 'field_question_creation_date',
            'unique' => FALSE,
          ),
          5 => array(
            'source' => 'View count',
            'target' => 'field_question_view_count',
            'unique' => FALSE,
          ),
          6 => array(
            'source' => 'Answer Count',
            'target' => 'field_question_answer_count',
            'unique' => FALSE,
          ),
          7 => array(
            'source' => 'Accepted Answer',
            'target' => 'field_question_accepted_answer',
            'unique' => FALSE,
          ),
          8 => array(
            'source' => 'Tags',
            'target' => 'field_question_tags',
            'unique' => FALSE,
          ),
          9 => array(
            'source' => 'Dataset',
            'target' => 'field_question_dataset',
            'unique' => FALSE,
          ),
        ),
        'update_existing' => '0',
        'input_format' => 'plain_text',
        'skip_hash_check' => 0,
        'bundle' => 'question',
      ),
    ),
    'content_type' => '',
    'update' => 0,
    'import_period' => '-1',
    'expire_period' => 3600,
    'import_on_create' => 1,
    'process_in_background' => 0,
  );
  $export['question'] = $feeds_importer;

  return $export;
}
