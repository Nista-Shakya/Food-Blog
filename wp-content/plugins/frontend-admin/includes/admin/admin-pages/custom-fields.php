<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if( function_exists('acf_add_local_field') ):

acf_add_local_field(
	array(
		'key' => 'fafe_title',
		'label' => __( 'Title', 'frontend-admin' ),
		'required' => true,
		'name' => 'fafe_title',
		'type' => 'text',
		'custom_title' => true,
	)
);	


endif;
