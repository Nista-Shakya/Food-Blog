<?php
namespace FrontendAdmin\Actions;

use FrontendAdmin\Plugin;
use FrontendAdmin\Classes\ActionBase;
use FrontendAdmin\Widgets;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
if( ! class_exists( 'ActionOptions' ) ) :

class ActionOptions extends ActionBase {
	
	public function get_name() {
		return 'options';
	}

	public function get_label() {
		return __( 'Options', 'frontend-admin' );
	}
	
	public function get_fields_display( $form_field, $local_field ){
		switch( $form_field['field_type'] ){
			case 'site_title':
				$local_field['type'] = 'text';
				$local_field['custom_site_title'] = true;
			break;
			case 'site_tagline':
				$local_field['type'] = 'text';
				$local_field['custom_site_tagline'] = true;
			break;
			case 'site_logo':
				$local_field['type'] = 'image';
				$local_field['custom_site_logo'] = true;
			break;
		}
		return $local_field;
	}
	

	public function register_settings_section( $widget ) {
		return;
	}
	

	public function options_arg( $form_args, $settings ){
		if( 'edit_options' == $settings['main_action'] ){
			$form_args['post_id'] = 'options';
		}
		return $form_args;
	}
	
	
	public function __construct() {
		add_filter( 'fafe/form_args', [ $this, 'options_arg'], 10, 2 );
		add_filter( 'fafe/step_form_args', [ $this, 'options_arg'], 10, 2 );

	}
	
}

fafe()->local_actions['options'] = new ActionOptions();

endif;	