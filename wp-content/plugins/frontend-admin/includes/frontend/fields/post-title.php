<?php

if( class_exists('acf_field_text') ) :

class acf_field_post_title extends acf_field_text {
	
	
	/*
	*  initialize
	*
	*  This function will setup the field type data
	*
	*  @type	function
	*  @date	5/03/2014
	*  @since	5.0.0
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function initialize() {
		
		// vars
		$this->name = 'post_title';
		$this->label = __("Post Title",'acf');
        $this->category = 'Post';
		$this->defaults = array(
			'default_value'	=> '',
			'maxlength'		=> '',
			'placeholder'	=> '',
			'prepend'		=> '',
			'append'		=> ''
		);
        add_filter( 'acf/load_field/type=text',  [ $this, 'load_post_title_field'] );
		
	}
    
    function load_post_title_field( $field ){
        if( ! empty( $field['custom_title'] ) ){
            $field['type'] = 'post_title';
        }
        return $field;
    }

    public function load_value( $value, $post_id = false, $field = false ){
        if( $post_id && is_numeric( $post_id ) ){  
            $edit_post = get_post( $post_id );
            $value = $edit_post->post_title;
        }
        return $value;
    }

    public function update_value( $value, $post_id = false, $field = false ){
        if( $post_id && is_numeric( $post_id ) ){  
            $post_to_edit = [
                'ID' => $post_id,
            ];
            $post_to_edit['post_title'] = sanitize_text_field( $value );
            if( isset( $_POST['_acf_post_id'] ) && $_POST['_acf_post_id'] == 'add_post' ){
                $post_to_edit['post_name'] = sanitize_title( $value );
            }
            if( ! empty( $field['custom_slug'] ) ){
                $post_to_edit['post_name'] = sanitize_title( $value );
            }
            remove_filter( 'acf/update_value/type=post_title', [ $this, 'update_value'], 9, 3 );
            remove_filter( 'acf/update_value/key=' .$field['key'], [ $this, 'update_value'], 9, 3 );
            remove_filter( 'acf/update_value/name=' .$field['name'], [ $this, 'update_value'], 9, 3 );
            wp_update_post( $post_to_edit );
            add_filter( 'acf/update_value/type=post_title', [ $this, 'update_value'], 9, 3 );
            add_filter( 'acf/update_value/key=' .$field['key'], [ $this, 'update_value'], 9, 3 );
            add_filter( 'acf/update_value/name=' .$field['name'], [ $this, 'update_value'], 9, 3 );
        }
        return $value;
    }

    function render_field( $field ){
        $field['type'] = 'text';
        acf_render_field( $field );
    }

    	/*
	*  render_field_settings()
	*
	*  Create extra options for your field. This is rendered when editing a field.
	*  The value of $field['name'] can be used (like bellow) to save extra data to the $field
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$field	- an array holding all the field's data
	*/
	
	function render_field_settings( $field ) {
		// default_value
        acf_render_field_setting( $field, array(
            'label'			=> __('Post Slug'),
            'instructions'	=> 'Save value as post slug.',
            'name'			=> 'custom_slug',
            'type'			=> 'true_false',
            'ui'			=> 1,
        ) );
    }

}

// initialize
acf_register_field_type( 'acf_field_post_title' );

endif;
	
?>