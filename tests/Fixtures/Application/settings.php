<?php

/**
 * Stub file for testing App settings.
 */

return array(
	'additional' => array( 'test_key' => 'test_value' ),
	'path'       => array(
		'view' => FIXTURES_PATH . '/views',
	),
	'meta'       => array(
		'post' => array( 'post_meta_1' => 'One Post' ),
		'user' => array( 'user_meta_1' => 'One User' ),
		'term' => array( 'term_meta_1' => 'One Term' ),
	),
	'post_types' => ['cpt' => 'post_type'],
	'taxonomies' => ['tax' => 'taxonomy'],
	'db_tables'  => ['table' => 'db_table'],
);
