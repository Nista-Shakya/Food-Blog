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
class Delete_Post_Widget extends Widget_Base
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
        return 'delete_post';
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
        return __( 'Trash/Delete Post', 'frontend-admin' );
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
        return 'fas fa-trash-alt frontend-icon';
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
        return array( 'fafe-posts' );
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
        $this->start_controls_section( 'delete_button_section', [
            'label' => __( 'Trash Button', 'frontend-admin' ),
            'tab'   => Controls_Manager::TAB_CONTENT,
        ] );
        $this->add_control( 'main_action', [
            'type'    => Controls_Manager::HIDDEN,
            'default' => 'delete_post',
        ] );
        $this->add_control( 'delete_button_text', [
            'label'       => __( 'Delete Button Text', 'frontend-admin' ),
            'type'        => Controls_Manager::TEXT,
            'default'     => __( 'Delete', 'frontend-admin' ),
            'placeholder' => __( 'Delete', 'frontend-admin' ),
        ] );
        $this->add_control( 'delete_button_icon', [
            'label' => __( 'Delete Button Icon', 'frontend-admin' ),
            'type'  => Controls_Manager::ICONS,
        ] );
        $this->add_control( 'confirm_delete_message', [
            'label'       => __( 'Confirm Delete Message', 'frontend-admin' ),
            'type'        => Controls_Manager::TEXT,
            'default'     => __( 'The post will be deleted. Are you sure?', 'frontend-admin' ),
            'placeholder' => __( 'The post will be deleted. Are you sure?', 'frontend-admin' ),
        ] );
        $this->add_control( 'show_delete_message', [
            'label'        => __( 'Show Success Message', 'frontend-admin' ),
            'type'         => Controls_Manager::SWITCHER,
            'label_on'     => __( 'Yes', 'frontend-admin' ),
            'label_off'    => __( 'No', 'frontend-admin' ),
            'default'      => 'true',
            'return_value' => 'true',
        ] );
        $this->add_control( 'delete_message', [
            'label'       => __( 'Success Message', 'frontend-admin' ),
            'type'        => Controls_Manager::TEXTAREA,
            'default'     => __( 'You have deleted this post', 'frontend-admin' ),
            'placeholder' => __( 'You have deleted this post', 'frontend-admin' ),
            'dynamic'     => [
            'active'    => true,
            'condition' => [
            'show_delete_message' => 'true',
        ],
        ],
        ] );
        $this->add_control( 'force_delete', [
            'label'        => __( 'Force Delete', 'frontend-admin' ),
            'type'         => Controls_Manager::SWITCHER,
            'default'      => 'true',
            'return_value' => 'true',
            'description'  => __( 'Whether or not to completely delete the posts right away.' ),
        ] );
        $this->add_control( 'delete_redirect', [
            'label'   => __( 'Redirect After Delete', 'frontend-admin' ),
            'type'    => Controls_Manager::SELECT,
            'default' => 'custom_url',
            'options' => [
            'current'     => __( 'Reload Current Url', 'frontend-admin' ),
            'custom_url'  => __( 'Custom Url', 'frontend-admin' ),
            'referer_url' => __( 'Referer', 'frontend-admin' ),
        ],
        ] );
        $this->add_control( 'redirect_after_delete', [
            'label'         => __( 'Custom URL', 'frontend-admin' ),
            'type'          => Controls_Manager::URL,
            'placeholder'   => __( 'Enter Url Here', 'frontend-admin' ),
            'show_external' => false,
            'dynamic'       => [
            'active' => true,
        ],
            'condition'     => [
            'delete_redirect' => 'custom_url',
        ],
        ] );
        $this->end_controls_section();
        $this->start_controls_section( 'post_section', [
            'label' => __( 'Post', 'frontend-admin' ),
            'tab'   => Controls_Manager::TAB_CONTENT,
        ] );
        fafe()->local_actions['post']->action_controls( $this );
        $this->end_controls_section();
        do_action( 'fafe/permissions_section', $this );
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
        $display = false;
        $wg_id = $this->get_id();
        $current_post_id = fafe_get_current_post_id();
        global  $post ;
        $active_user = wp_get_current_user();
        $settings = $this->get_settings_for_display();
        $post_id = $post->ID;
        if ( $settings['post_to_edit'] == 'select_post' ) {
            $post_id = $settings['post_select'];
        }
        if ( $settings['post_to_edit'] == 'url_query' && isset( $_GET[$settings['url_query_post']] ) ) {
            $post_id = $_GET[$settings['url_query_post']];
        }
        $hidden_fields = [
            'screen_id'  => $current_post_id,
            'element_id' => $wg_id,
        ];
        $btn_args = array(
            'post_id'        => $post_id,
            'hidden_fields'  => $hidden_fields,
            'kses'           => true,
            'force_delete'   => $settings['force_delete'],
            'delete_message' => $settings['confirm_delete_message'],
            'delete_icon'    => $settings['delete_button_icon'],
            'delete_text'    => $settings['delete_button_text'],
        );
        $delete_redirect = $settings['delete_redirect'];
        $delete_redirect_url = $settings['redirect_after_delete']['url'];
        
        if ( $delete_redirect ) {
            switch ( $delete_redirect ) {
                case 'custom_url':
                    $delete_redirect = $delete_redirect_url;
                    break;
                case 'current':
                    global  $wp ;
                    $delete_redirect = home_url( $wp->request );
                    break;
                case 'referer_url':
                    $delete_redirect = home_url( add_query_arg( NULL, NULL ) );
                    if ( wp_get_referer() ) {
                        $delete_redirect = wp_get_referer();
                    }
                    break;
            }
            $btn_args['redirect'] = $delete_redirect;
        }
        
        $display = apply_filters(
            'fafe/show_widget',
            $wg_id,
            $settings,
            $btn_args
        );
        if ( $display ) {
            fafe_delete_button( $btn_args );
        }
    }

}