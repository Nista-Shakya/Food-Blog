<?php
namespace FrontendAdmin;

use FrontendAdmin\Plugin;
use Elementor\Core\Base\Module;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class FAFE_Local_Avatar_Settings{

	public function local_avatar_field( $args ) {
		$fields = fafe_get_field_data( 'image' );

		echo '<select name="local_avatar" id="local_avatar">';
		
			$default = ( get_option( 'local_avatar' ) == 'none' ) ? ' selected="selected"' : '';
		    echo '<option value="none"' . $default .  '>None</option>';
		
			$selected = get_option( 'local_avatar' );
			foreach( $fields as $key => $value ){
				$select = '';
				if( $key == $selected ){
					$select = ' selected="selected"';
				}
				
				echo '<option value="' . $key . '"' . $select . '>' . $value . '</option>';
			}
		echo '</select>';
	}
	
	
	public function fafe_local_avatar_section(){	
		register_setting( 'local_avatar_settings', 'local_avatar' );
		add_settings_section(
			'local_avatar_settings_section',
			__( 'Local Gravatar', 'frontend-admin' ),
			'',
			'local-avatar-settings-admin'
		);
		add_settings_field(
			'local_avatar', 
			__( 'Replace Gravatar with Local Avatar', 'frontend-admin' ),
            [ $this, 'local_avatar_field'],
            'local-avatar-settings-admin',
			'local_avatar_settings_section',
			[
				'label_for' => 'local_avatar'
			] 
		);
	}
	
	function fafe_get_local_avatar( $avatar, $id_or_email, $size, $default, $alt ) {
		$user = '';

		// Get user by id or email
		if ( is_numeric( $id_or_email ) ) {
			$id   = (int) $id_or_email;
			$user = get_user_by( 'id' , $id );
		} elseif ( is_object( $id_or_email ) ) {
			if ( ! empty( $id_or_email->user_id ) ) {
				$id   = (int) $id_or_email->user_id;
				$user = get_user_by( 'id' , $id );
			}
		} else {
			$user = get_user_by( 'email', $id_or_email );
		}
		if ( ! $user ) {
			return $avatar;
		}
		// Get the user id
		$user_id = $user->ID;
		
		$img_field_key = get_option( 'local_avatar' );
		
		if( $img_field_key == 'none' ){
			return $avatar;
		}
		
		// Get the file id
		$image_id = get_field( $img_field_key, 'user_' . $user_id ); 
		
		if ( ! $image_id ) {
			return $avatar;
		}
		
		if( is_array( $image_id ) ) {
			$image_id = $image_id['ID'];
		}
		
	   if( filter_var( $image_id, FILTER_VALIDATE_URL ) ) { 
            $avatar_url = $image_id;
       }else{
			$image_url  = wp_get_attachment_image_src( $image_id, 'thumbnail' );
			$avatar_url = $image_url[0];
	   }
		
		// Get the img markup
		$avatar = '<img alt="' . $alt . '" src="' . $avatar_url . '" class="avatar avatar-' . $size . '" height="' . $size . '" width="' . $size . '"/>';
		
		// Return our new avatar
		return $avatar;
	}
    
    public function fafe_hide_gravatar_field( $hook ) {		
		if( get_option( 'local_avatar' ) == 'none' ){
			return;
		}
        echo '<style>
        tr.user-profile-picture{
            display: none;
        }
        </style>';
    }

	public function fafe_get_settings_fields( $field_keys ){
		$default = get_option( 'local_avatar' ) ? get_option( 'local_avatar' ) : 'none';

		$fafe_local_fields = array(
			array(
				'key' => 'local_avatar',
				'label' => __( 'Roles', 'frontend-admin' ),
				'name' => 'local_avatar',
				'type' => 'select',
				'instructions' => '',
				'required' => 0,
				'wrapper' => array(
					'width' => '30',
					'class' => '',
					'id' => '',
				),
				'only_front' => 0,
				'choices' => fafe_get_field_data( 'image' ),
				'value' => $default,
				'allow_null' => 1,
				'ajax' => 0,
				'return_format' => 'value',
				'placeholder' => 'None',
			),
		);

		foreach( $fafe_local_fields as $local_field ){
			$local_field['value'] = get_option( $local_field['key'] );
			acf_add_local_field( $local_field );
			$field_keys[] = $local_field['key'];
		}
		return $field_keys;
	} 

	public function __construct() {
		//add_action( 'admin_init', [ $this, 'fafe_local_avatar_section'] );
        add_filter( 'get_avatar', [ $this, 'fafe_get_local_avatar'], 10, 5 );
        add_action( 'admin_head', [ $this, 'fafe_hide_gravatar_field'] );
		add_filter( 'fafe/local_avatar_fields', [ $this, 'fafe_get_settings_fields'] );
	}
	
}
