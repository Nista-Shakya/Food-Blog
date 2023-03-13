<?php
namespace FrontendAdmin\Actions;

use FrontendAdmin\Plugin;
use FrontendAdmin;
use FrontendAdmin\Classes\ActionBase;
use FrontendAdmin\Widgets;
use Elementor\Controls_Manager;
use ElementorPro\Modules\QueryControl\Module as Query_Module;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
if( ! class_exists( 'ActionUser' ) ) :

class ActionUser extends ActionBase {
	
	public function get_name() {
		return 'user';
	}

	public function get_label() {
		return __( 'User', 'frontend-admin' );
	}

	public function get_fields_display( $form_field, $local_field, $wg_id = '' ){

		switch( $form_field['field_type'] ){
			case 'username':
				$local_field['type'] = 'text';
				$local_field['disabled'] = isset( $form_field['allow_edit'] ) ? ! $form_field['allow_edit'] : 1;
				$local_field['wrapper']['class'] .= ' post-slug-field';
				$local_field['custom_username'] = true;
			break;
			case 'password':
				$local_field['type'] = 'password';
				$local_field['custom_password'] = true;
				if( isset( $local_field['wrapper']['class'] ) ){
					$local_field['wrapper']['class'] .= ' password_main';
				}else{
					$local_field['wrapper']['class'] = 'password_main';
				}	
				$local_field['edit_password'] = isset( $form_field['edit_password'] ) ? $form_field['edit_password'] : 'Edit Password';
				$local_field['cancel_edit_password'] = isset( $form_field['cancel_edit_password'] ) ? $form_field['cancel_edit_password'] : 'Cancel';
			break;				
			case 'confirm_password':
				$local_field['type'] = 'password';
				if( isset( $local_field['wrapper']['class'] ) ){
					$local_field['wrapper']['class'] .= ' password_confirm';
				}else{
					$local_field['wrapper']['class'] = 'password_confirm';
				}
				$local_field['custom_password_confirm'] = true;
			break;			
			case 'email':
				$local_field['type'] = 'email';
				$local_field['custom_email'] = true;
			break;
			case 'first_name':
				$local_field['type'] = 'text';
				$local_field['custom_first_name'] = true;
			break;
			case 'last_name':
				$local_field['type'] = 'text';
				$local_field['custom_last_name'] = true;
			break;					
			case 'nickname':
				$local_field['type'] = 'text';
				$local_field['custom_nickname'] = true;
			break;				
			case 'display_name':
				$local_field['type'] = 'text';
				$local_field['custom_display_name'] = true;
				if( isset( $edit_user ) ){
					$local_field['type'] = 'radio';
					$local_field['user_id'] = true;
				}
			break;			
			case 'bio':
				$local_field['type'] = 'textarea';
				$local_field['custom_user_bio'] = true;
			break;
			case 'bio':
				$local_field['type'] = 'url';
				$local_field['custom_user_url'] = true;
			break;
			case 'role':
				$roles = [];
				$all_roles = fafe_get_user_roles();
				if( isset( $form_field['role_field_options'] ) ){
					foreach( $form_field['role_field_options'] as $role_option ){
						$roles[ $role_option ] = $all_roles[ $role_option ];
					}
				}
				$local_field['type'] = isset( $form_field['role_appearance'] ) ? $form_field['role_appearance'] : 'select';
				$local_field['layout'] = isset( $form_field['role_radio_layout'] ) ? $form_field['role_radio_layout'] : 'vertical';
				$local_field['choices'] = $roles;
				$local_field['default_value'] = isset( $form_field['default_role'] ) ? $form_field['default_role'] : 'subscriber';
				$local_field['custom_user_role'] = true;
			break;
			
		}
		return $local_field;
	}
	

	public function register_settings_section( $widget ) {
		
					
		$widget->start_controls_section(
			'section_edit_user',
			[
				'label' => $this->get_label(),
				'tab' => Controls_Manager::TAB_CONTENT,
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => 'main_action',
							'operator' => 'in',
							'value' => ['new_user', 'edit_user'],
						],	
						
					],
				],
			]
		);
		
		$widget->add_control(
			'user_settings',
			[
				'label' => __( 'User Settings', 'frontend-admin' ),
				'type' => \Elementor\Controls_Manager::HEADING,
			]
		);

		$this->action_controls( $widget );
				
		$widget->end_controls_section();
	}
	
	
	public function action_controls( $widget, $step = false ){
		$condition = [
			'main_action' => [ 'edit_user', 'delete_user'],
		];
		if( $step ){
			$condition['field_type'] = 'step';
			$condition['overwrite_settings'] = 'true';
		}

		$widget->add_control(
			'user_to_edit',
			[
				'label' => __( 'Select User', 'frontend-admin' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'current_user',
				'options' => [
					'current_user'  => __( 'Current User', 'frontend-admin' ),
					'current_author'  => __( 'Current Author', 'frontend-admin' ),
					'url_query' => __( 'URL Query', 'frontend-admin' ),
					'select_user' => __( 'Select User', 'frontend-admin' ),
				],
				'condition' => $condition,
			]
		);
		$condition['user_to_edit'] = 'url_query';
		$widget->add_control(
			'url_query_user',
			[
				'label' => __( 'URL Query', 'frontend-admin' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'user_id', 'frontend-admin' ),
				'default' => __( 'user_id', 'frontend-admin' ),
				'description' => __( 'Enter the URL query parameter containing the id of the user you want to edit', 'frontend-admin' ),
				'condition' => $condition,
			]
		);	
		$condition['user_to_edit'] = 'select_user';
		if( ! class_exists( 'ElementorPro\Modules\QueryControl\Module' ) ){		
			$widget->add_control(
				'user_select',
				[
					'label' => __( 'User', 'frontend-admin' ),
					'type' => Controls_Manager::TEXT,
					'placeholder' => __( '18', 'frontend-admin' ),
					'default' => get_current_user_id(),
					'description' => __( 'Enter user id', 'frontend-admin' ),
					'condition' => $condition,
				]
			);		
		}else{			
			$widget->add_control(
				'user_select',
				[
					'label' => __( 'User', 'frontend-admin' ),
					'label_block' => true,
					'type' => Query_Module::QUERY_CONTROL_ID,
					'autocomplete' => [
						'object' => Query_Module::QUERY_OBJECT_USER,
						'display' => 'detailed',
					],				
					'default' => get_current_user_id(),
					'condition' => $condition,
				]
			);
		}

		unset( $condition['user_to_edit'] );
		$condition['main_action'] = 'new_user';

		$widget->add_control(
			'username_default',
			[
				'label' => __( 'Default Username', 'frontend-admin' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'generate',
				'options' => [
					'generate' => __( 'Generate Random Number', 'frontend-admin' ),
					'id' => __( 'Generate From ID', 'frontend-admin' )
				],
				'description' => __( 'Will be overwritten if your form has a "Username" field', 'frontend-admin' ),
				'condition' => $condition,
			]
		);
		$widget->add_control(
			'username_prefix',
			[
				'label' => __( 'Username Prefix', 'frontend-admin' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => 'user_',
				'description' => __( 'Please enter only lowercase latin letters, numbers, @, -, . and _', 'frontend-admin' ),
				'condition' => $condition,
			]
		);
		$widget->add_control(
			'username_suffix',
			[
				'label' => __( 'Username Suffix', 'frontend-admin' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'description' => __( 'Please enter only lowercase latin letters, numbers, @, -, . and _', 'frontend-admin' ),
				'condition' => $condition,
			]
		);
		$widget->add_control(
			'display_name_default',
			[
				'label' => __( 'Default Display Name', 'frontend-admin' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'user_login',
				'options' => [
					'default' => __( 'Default', 'frontend-admin' ),
					'user_login' => __( 'Username', 'frontend-admin' ),
					'user_email' => __( 'Email', 'frontend-admin' ),
					'first_name' => __( 'First Name', 'frontend-admin' ),
					'last_name' => __( 'Last Name', 'frontend-admin' ),
					'first_last' => __( 'First and Last Name', 'frontend-admin' ),
					'nickname' => __( 'Nickname', 'frontend-admin' ),
				],
				'description' => __( 'Will be overwritten if your form has a "Display Name" field', 'frontend-admin' ),
				'condition' => $condition,
			]
		);

		$widget->add_control(
			'new_user_role',
			[
				'label' => __( 'New User Role', 'frontend-admin' ),
				'type' => \Elementor\Controls_Manager::SELECT2,
				'label_block' => true,
				'default' => 'subscriber',
				'options' => fafe_get_user_roles( ['administrator'] ),
				'condition' => $condition,
			]
		);
		
		$widget->add_control(
			'hide_admin_bar',
			[
				'label' => __( 'Hide WordPress Admin Area?', 'frontend-admin' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __( 'Hide', 'frontend-admin' ),
				'label_off' => __( 'Show','frontend-admin' ),
				'return_value' => 'true',
				'condition' => $condition,
			]
		);
		
		$widget->add_control(
			'login_user',
			[
				'label' => __( 'Log in as new user?', 'frontend-admin' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'frontend-admin' ),
				'label_off' => __( 'No','frontend-admin' ),
				'return_value' => 'true',
				'condition' => $condition,			
			]
		);			
		
		$condition['main_action'] = ['new_user', 'edit_user'];

		$widget->add_control(
			'user_manager',
			[
				'label' => __( 'Managing User', 'frontend-admin' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'none',
				'options' => [
					'none' => __( 'No Manager','frontend-admin' ),
					'current_user' => __( 'Current User','frontend-admin' ),
					//'current_author' => __( 'Current Post Author','frontend-admin' ),
					'select_user' => __( 'Select User','frontend-admin' ),
				],
				'description' => __( 'Who will be in charge of editing this user\'s data?', 'frontend-admin' ),
				'condition' => $condition,
			]
		);
		$condition['user_manager'] = 'select_user';
		if( ! class_exists( 'ElementorPro\Modules\QueryControl\Module' ) ){		
			$widget->add_control(
				'manager_select',
				[
					'label' => __( 'User', 'frontend-admin' ),
					'type' => Controls_Manager::TEXT,
					'placeholder' => __( '18', 'frontend-admin' ),
					'default' => get_current_user_id(),
					'description' => __( 'Enter user id', 'frontend-admin' ),
					'condition' => $condition,
				]
			);		
		}else{			
			$widget->add_control(
				'manager_select',
				[
					'label' => __( 'User', 'frontend-admin' ),
					'label_block' => true,
					'type' => Query_Module::QUERY_CONTROL_ID,
					'autocomplete' => [
						'object' => Query_Module::QUERY_OBJECT_USER,
						'display' => 'detailed',
					],				
					'default' => get_current_user_id(),
					'condition' => $condition,
				]
			);
		}
		
	}
		
	public function on_submit( $post_id, $form ){	
		if( $post_id != 'user_0' ) return $post_id; 
		
		$username_generated = false;
		$user_to_insert = [];								 
					
		if( isset( $_POST['custom_username'] ) ){ 
			$user_to_insert['user_login'] = $_POST['acf'][ $_POST['custom_username'] ];	
			unset( $_POST['acf'][ $_POST['custom_username'] ] );		
		}else{
			$prefix = sanitize_title( $form['username_prefix'] );
			$suffix = sanitize_title( $form['username_suffix'] );
			$user_to_insert['user_login'] = $this->fafe_generate_username( $prefix, $suffix );
			$username_generated = true;
		}

		if( isset( $_POST['custom_email'] ) ){ 
			$user_to_insert['user_email'] = $_POST['acf'][ $_POST['custom_email'] ];	
			unset( $_POST['acf'][ $_POST['custom_email'] ] );
		}

		if( isset( $_POST['custom_password'] ) ){ 
			$user_to_insert['user_pass'] = $_POST['acf'][ $_POST['custom_password'] ];
			unset( $_POST['acf'][ $_POST['custom_password'] ] );
			if( isset( $_POST['custom_password_confirm'] ) ){
				unset( $_POST['acf'][ $_POST['custom_password_confirm'] ] );
			}
		}else{
			$user_to_insert['user_pass'] = wp_generate_password();
		}	
		
		$user_to_insert['role'] = $form['new_user_role'];
		$user_to_insert['show_admin_bar_front'] = $form['hide_admin_bar'];

		if( isset( $_POST['custom_user_role'] ) ){ 
			$user_to_insert['role'] = $_POST['acf'][ $_POST['custom_user_role'] ];	
			unset( $_POST['acf'][ $_POST['custom_user_role'] ] );				
		}
		
		$user_to_insert = apply_filters( 'fafe/user_fields_save', $user_to_insert, $form );
		
		$user_id = wp_insert_user( $user_to_insert );  

		if( $username_generated && $form['username_default'] == 'id' ){
			$prefix = sanitize_title( $form['username_prefix'] );
			$suffix = sanitize_title( $form['username_suffix'] );
			$new_username = sprintf( '%s%s%s', $prefix, $user_id, $suffix );
			if ( ! username_exists( $new_username ) ) {
				global $wpdb;
				$wpdb->update( $wpdb->users, array( 'user_login' => $new_username ), ['ID' => $user_id ] );
				update_user_meta( $user_id, 'nickname', $new_username );
				wp_update_user( ['ID' => $user_id, 'display_name' => $new_username ] );
			}
		}

		if ( ! is_wp_error( $user_id ) ) {
			update_user_meta( $user_id, 'hide_admin_area', $form['hide_admin_bar'] );
			if( isset( $_POST['_acf_element_id'] ) ){
				update_user_meta( $user_id, 'fafe_form_source', $_POST['_acf_element_id'] );
			}
			if( $form['login_user'] ){
				$user_login = $user_to_insert['user_login'];
				wp_set_current_user( $user_id, $user_login );
				wp_set_auth_cookie( $user_id );
			}
			$_POST['fafe_registered_user'] = 1;
			return 'user_' . $user_id;
		}else{	
			return '-1';
		}
	
	}  
	
	public function fafe_generate_username( $prefix, $suffix ) {	
		static $i;
		if ( null === $i ) {
			$i = 1;
		} else {
			$i ++;
		}
		$new_username = sprintf( '%s%s%s', $prefix, $i, $suffix );
		if ( ! username_exists( $new_username ) ) {
			return $new_username;
		} else {
			return $this->fafe_generate_username( $prefix, $suffix );
		}
	}
	

	public function __construct(){
		add_filter( 'acf/pre_save_post', array( $this, 'on_submit' ), 4, 2 );
	}
}
fafe()->local_actions['user'] = new ActionUser();

endif;	