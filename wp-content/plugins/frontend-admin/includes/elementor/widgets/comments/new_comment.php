<?php
namespace FrontendAdmin\Widgets;

use FrontendAdmin\Widgets\ACF_Elementor_Form_Base;


	
/**
 * Elementor Frontend Admin Form Widget.
 *
 * Elementor widget that inserts an ACF frontend form into the page.
 *
 * @since 1.0.0
 */
class New_Comment_Widget extends ACF_Frontend_Form_Widget {
	
	/**
	 * Get widget name.
	 *
	 * Retrieve acf ele form widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'new_comment';
	}

	/**
	* Get widget action.
	*
	* Retrieve acf ele form widget action.
	*
	* @since 1.0.0
	* @access public
	*
	* @return string Widget action.
	*/
    public function get_form_defaults() {
        return [ 
             'main_action' => 'new_comment',
             'form_title' => __( 'Add Comment', 'frontend-admin' ),
			 'submit' => __( 'Submit', 'frontend-admin' ),
			 'success_message' => __( 'Your comment has been added successfully.', 'frontend-admin' ),
			 'field_type' => 'comment',
			 'fields' => [
				[
					'field_type' => 'comment',
					'field_label_on' => 'true',
					'field_required' => 'true',
				],	
			],
        ];
    }

	/**
	 * Get widget title.
	 *
	 * Retrieve acf ele form widget title.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'New Comment Form', 'frontend-admin' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve acf ele form widget icon.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'fas fa-comment frontend-icon';
	}

	/**
	 * Get widget categories.
	 *
	 * Retrieve the list of categories the acf ele form widget belongs to.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return ['fafe-forms'];
	}

}
