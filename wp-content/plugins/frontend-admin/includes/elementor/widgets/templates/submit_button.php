<?php

namespace FrontendAdmin\Widgets;

use  FrontendAdmin\Plugin ;
use  FrontendAdmin\Classes ;
use  Elementor\Controls_Manager ;
use  Elementor\Widget_Base ;
use  ElementorPro\Modules\QueryControl\Module as Query_Module ;
/**
 * Elementor Frontend Admin Form Widget.
 *
 * Elementor widget that inserts an ACF frontend form into the page.
 *
 * @since 1.0.0
 */
class Submit_Post_Widget extends Widget_Base
{
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
    public function get_name()
    {
        return 'submit_post';
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
    public function get_title()
    {
        return __( 'Submit Button', 'frontend-admin' );
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
    public function get_icon()
    {
        return 'fas fa-check frontend-icon';
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
    public function get_categories()
    {
        return [ 'form_fields' ];
    }
    
    /**
     * Register acf ele form widget controls.
     *
     * Adds different input fields to allow the user to change and customize the widget settings.
     *
     * @since 1.0.0
     * @access protected
     */
    protected function _register_controls()
    {
        //get all user role choices
        $user_roles = fafe_get_user_roles();
        $this->start_controls_section( 'submit_button_section', [
            'label' => __( 'Submit Button', 'frontend-admin' ),
            'tab'   => Controls_Manager::TAB_CONTENT,
        ] );
        $this->add_control( 'update_button_text', [
            'label'       => __( 'Update Button Text', 'frontend-admin' ),
            'type'        => Controls_Manager::TEXT,
            'default'     => __( 'Update', 'frontend-admin' ),
            'placeholder' => __( 'Update', 'frontend-admin' ),
        ] );
        $this->add_control( 'submit_button_text', [
            'label'       => __( 'Submit Button Text', 'frontend-admin' ),
            'type'        => Controls_Manager::TEXT,
            'default'     => __( 'Submit', 'frontend-admin' ),
            'placeholder' => __( 'Submit', 'frontend-admin' ),
        ] );
        $this->end_controls_section();
        $this->start_controls_section( 'style_promo_section', [
            'label' => __( 'Styles', 'frontend-admins' ),
            'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
        ] );
        $this->add_control( 'styles_promo', [
            'type'            => Controls_Manager::RAW_HTML,
            'raw'             => __( '<p><a target="_blank" href="https://www.frontendform.com/"><b>Go Pro</b></a> to unlock styles.</p>', 'frontend-admin' ),
            'content_classes' => 'acf-fields-note',
        ] );
        $this->end_controls_section();
    }
    
    /**
     * Render acf ele form widget output on the frontend.
     *
     *
     * @since 1.0.0
     * @access protected
     */
    protected function render()
    {
        $current_post_id = fafe_get_current_post_id();
        $post_id = ( isset( $_POST['form_action'] ) ? $_POST['form_action'] : $current_post_id );
        $settings = $this->get_settings_for_display();
        
        if ( is_numeric( $post_id ) ) {
            $submit_value = $settings['update_button_text'];
        } else {
            $submit_value = $settings['submit_button_text'];
        }
        
        $submit_button_html = '<div class="fafe-submit-buttons"><input type="submit" class="fafe-submit-button acf-button button button-primary" data-state="publish" value="%s" /></div>';
        ?>
        <div class="acf-form-submit">
            <?php 
        printf( $submit_button_html, $submit_value );
        ?>
        </div>
        <?php 
    }

}