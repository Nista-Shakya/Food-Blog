<?php

namespace FrontendAdmin;

use  FrontendAdmin\Plugin ;
use  Elementor\Core\Base\Module ;

if ( !defined( 'ABSPATH' ) ) {
    exit;
    // Exit if accessed directly
}


if ( !class_exists( 'FrontendAdmin_Elementor' ) ) {
    class FrontendAdmin_Elementor
    {
        public  $form_widgets = array() ;
        public  $submit_actions = array() ;
        public  $elementor_categories = array() ;
        public function get_name()
        {
            return 'acf_frontend_form';
        }
        
        public static function find_element_recursive( $elements, $widget_id )
        {
            foreach ( $elements as $element ) {
                if ( $widget_id == $element['id'] ) {
                    return $element;
                }
                
                if ( !empty($element['elements']) ) {
                    $element = self::find_element_recursive( $element['elements'], $widget_id );
                    if ( $element ) {
                        return $element;
                    }
                }
            
            }
            return false;
        }
        
        public function widgets()
        {
            $widget_list = array(
                'general' => array(
                'edit_button' => 'Edit_Button_Widget',
            ),
                'posts'   => array(
                'edit_post'      => 'Edit_Post_Widget',
                'new_post'       => 'New_Post_Widget',
                'duplicate_post' => 'Duplicate_Post_Widget',
                'delete_post'    => 'Delete_Post_Widget',
            ),
                'terms'   => array(
                'edit_term'   => 'Edit_Term_Widget',
                'new_term'    => 'New_Term_Widget',
                'delete_term' => 'Delete_Term_Widget',
            ),
                'users'   => array(
                'edit_user'   => 'Edit_User_Widget',
                'new_user'    => 'New_User_Widget',
                'delete_user' => 'Delete_User_Widget',
            ),
            );
            $elementor = fafe_get_elementor_instance();
            require_once __DIR__ . "/widgets/general/frontend-form.php";
            foreach ( $widget_list as $folder => $widgets ) {
                foreach ( $widgets as $filename => $classname ) {
                    require_once __DIR__ . "/widgets/{$folder}/{$filename}.php";
                    $classname = 'FrontendAdmin\\Widgets\\' . $classname;
                    $elementor->widgets_manager->register_widget_type( new $classname() );
                }
            }
            do_action( 'fafe/widget_loaded' );
        }
        
        public function widget_categories( $elements_manager )
        {
            $categories = array(
                'fafe-general'    => array(
                'title' => __( 'FRONTEND SITE MANAGEMENT', 'frontend-admin' ),
                'icon'  => 'fa fa-plug',
            ),
                'fafe-posts'      => array(
                'title' => __( 'FRONTEND POSTS', 'frontend-admin' ),
                'icon'  => 'fa fa-plug',
            ),
                'fafe-users'      => array(
                'title' => __( 'FRONTEND USERS', 'frontend-admin' ),
                'icon'  => 'fa fa-plug',
            ),
                'fafe-taxonomies' => array(
                'title' => __( 'FRONTEND TAXONOMIES', 'frontend-admin' ),
                'icon'  => 'fa fa-plug',
            ),
                'fafe-templates'  => array(
                'title' => __( 'FORM FIELDS', 'frontend-admin' ),
                'icon'  => 'fa fa-plug',
            ),
            );
            foreach ( $categories as $name => $args ) {
                $this->elementor_categories[$name] = $args;
                $elements_manager->add_category( $name, $args );
            }
        }
        
        public function dynamic_tags( $dynamic_tags )
        {
            
            if ( class_exists( 'ElementorPro\\Modules\\DynamicTags\\Tags\\Base\\Data_Tag' ) ) {
                \Elementor\Plugin::$instance->dynamic_tags->register_group( 'fafe-user-data', [
                    'title' => 'User',
                ] );
                require_once __DIR__ . '/dynamic-tags/user-local-avatar.php';
                require_once __DIR__ . '/dynamic-tags/author-local-avatar.php';
                $dynamic_tags->register_tag( new DynamicTags\User_Local_Avatar_Tag() );
                $dynamic_tags->register_tag( new DynamicTags\Author_Local_Avatar_Tag() );
            }
        
        }
        
        public function document_types()
        {
        }
        
        public function icon_file()
        {
            wp_enqueue_style(
                'fafe-icon',
                FAFE_URL . 'includes/assets/css/icon.css',
                array(),
                FAFE_ASSETS_VERSION
            );
            wp_enqueue_style(
                'fafe-editor',
                FAFE_URL . 'includes/assets/css/editor.min.css',
                array(),
                FAFE_ASSETS_VERSION
            );
            
            if ( fafe()->dev_mode ) {
                $min = '';
            } else {
                $min = '.min';
            }
            
            wp_enqueue_script(
                'fafe-editor',
                FAFE_URL . 'includes/assets/js/editor' . $min . '.js',
                array( 'elementor-editor' ),
                FAFE_ASSETS_VERSION,
                true
            );
            wp_enqueue_style( 'acf-global' );
        }
        
        public function __construct()
        {
            require_once __DIR__ . '/classes/save_fields.php';
            require_once __DIR__ . '/classes/content_tab.php';
            require_once __DIR__ . '/classes/permissions_tab.php';
            add_action( 'elementor/elements/categories_registered', array( $this, 'widget_categories' ) );
            add_action( 'elementor/widgets/widgets_registered', [ $this, 'widgets' ] );
            add_action( 'elementor/dynamic_tags/register_tags', [ $this, 'dynamic_tags' ] );
            add_action( 'elementor/documents/register', [ $this, 'document_types' ] );
            add_action( 'elementor/editor/before_enqueue_scripts', [ $this, 'icon_file' ] );
        }
    
    }
    fafe()->elementor = new FrontendAdmin_Elementor();
}
