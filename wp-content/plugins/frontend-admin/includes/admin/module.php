<?php

namespace FrontendAdmin;


if ( !defined( 'ABSPATH' ) ) {
    exit;
    // Exit if accessed directly
}


if ( !class_exists( 'FrontendAdmin_Settings' ) ) {
    class FrontendAdmin_Settings
    {
        private  $components = array() ;
        public function get_name()
        {
            return 'fafe_settings';
        }
        
        public function fafe_plugin_page()
        {
            global  $fafe_settings ;
            $fafe_settings = add_menu_page(
                'Frontend Admin',
                'Frontend Admin',
                'manage_options',
                'fafe-settings',
                [ $this, 'fafe_admin_settings_page' ],
                'dashicons-feedback',
                '87.87778'
            );
            add_submenu_page(
                'fafe-settings',
                __( 'Settings', 'frontend-admin' ),
                __( 'Settings', 'frontend-admin' ),
                'manage_options',
                'fafe-settings',
                '',
                0
            );
            if ( get_option( 'fafe_payments_active' ) ) {
                add_submenu_page(
                    'fafe-settings',
                    __( 'Payments', 'frontend-admin' ),
                    __( 'Payments', 'frontend-admin' ),
                    'manage_options',
                    'fafe-payments',
                    [ $this, 'fafe_admin_payments_page' ],
                    1
                );
            }
        }
        
        function fafe_admin_settings_page()
        {
            global  $fafe_active_tab ;
            $fafe_active_tab = ( isset( $_GET['tab'] ) ? $_GET['tab'] : 'welcome' );
            ?>

			<h2 class="nav-tab-wrapper">
			<?php 
            do_action( 'fafe_settings_tabs' );
            ?>
			</h2>
			<?php 
            do_action( 'fafe_settings_content' );
        }
        
        function fafe_admin_payments_page()
        {
            require_once __DIR__ . '/admin-pages/payments/payments-list.php';
            $option = 'per_page';
            $args = [
                'label'   => 'Payments',
                'default' => 20,
                'option'  => 'payments_per_page',
            ];
            add_screen_option( $option, $args );
            $payments_obj = new \Payments_List();
            ?>
				<h2><?php 
            echo  __( 'Payments', 'frontend-admin' ) ;
            ?></h2>
				<?php 
            $payments_obj->prepare_items();
            $payments_obj->display();
        }
        
        public function add_tabs()
        {
            add_action( 'fafe_settings_tabs', [ $this, 'fafe_settings_tabs' ], 1 );
            add_action( 'fafe_settings_content', [ $this, 'fafe_settings_render_options_page' ] );
        }
        
        public function fafe_settings_tabs()
        {
            $tabs = [
                'welcome'         => 'Welcome',
                'uploads_privacy' => 'Uploads Privacy',
                'hide_admin'      => 'Hide WP Dashboard',
                'google'          => 'Google APIs',
            ];
            global  $fafe_active_tab ;
            foreach ( $tabs as $name => $label ) {
                ?>
				<a class="nav-tab <?php 
                echo  ( $fafe_active_tab == $name || '' ? 'nav-tab-active' : '' ) ;
                ?>" href="<?php 
                echo  admin_url( '?page=fafe-settings&tab=' . $name ) ;
                ?>"><?php 
                _e( $label, 'frontend-admin' );
                ?> </a>
			<?php 
            }
        }
        
        public function fafe_settings_render_options_page()
        {
            global  $fafe_active_tab ;
            
            if ( '' || 'welcome' == $fafe_active_tab ) {
                ?>
			<style>p.fafe-text{font-size:20px}</style>
			<h3><?php 
                _e( 'Hello and welcome', 'frontend-admin' );
                ?></h3>
			<p class="fafe-text"><?php 
                _e( 'If this is your first time using Frontend Admin, we recommend you watch Paul Charlton from WPTuts beautifully explain how to use it.', 'frontend-admin' );
                ?></p>
			<iframe width="560" height="315" src="https://www.youtube.com/embed/U0yLNGCAVaM" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
			<br>
			<p class="fafe-text"><?php 
                _e( 'If you have any questions at all please feel welcome to email shabti at', 'frontend-admin' );
                ?> <a href="mailto:shabti@frontendadmin.com">shabti@frontendform.com</a> <?php 
                _e( 'or on whatsapp', 'frontend-admin' );
                ?> <a href="https://api.whatsapp.com/send?phone=972584526441">+972-58-452-6441</a></p>
			<?php 
            }
            
            $form_tabs = [
                'hide_admin',
                'google',
                'payments',
                'uploads_privacy'
            ];
            foreach ( $form_tabs as $form_tab ) {
                
                if ( $form_tab == $fafe_active_tab ) {
                    $hide_admin_fields = apply_filters( 'fafe/' . $fafe_active_tab . '_fields', [] );
                    fafe_render_form( [
                        'post_id'        => 'fafe_options',
                        'hidden_fields'  => [
                        'admin_page' => $fafe_active_tab,
                        'screen_id'  => 'options',
                    ],
                        'fields'         => $hide_admin_fields,
                        'submit_value'   => __( 'Save Settings', 'frontend-admin' ),
                        'update_message' => __( 'Settings Saved', 'frontend-admin' ),
                        'return'         => admin_url( '?page=fafe-settings&tab=' . $_GET['tab'] ),
                    ] );
                }
            
            }
        }
        
        public function fafe_configs()
        {
            
            if ( !get_option( 'fafe_hide_wp_dashboard' ) ) {
                add_option( 'fafe_hide_wp_dashboard', true );
                add_option( 'fafe_hide_by', array_map( 'strval', [
                    0 => 'user',
                ] ) );
            }
            
            require_once __DIR__ . '/admin-pages/custom-fields.php';
        }
        
        public function fafe_settings_sections()
        {
            require_once __DIR__ . '/admin-pages/local_avatar/settings.php';
            new FAFE_Local_Avatar_Settings( $this );
            require_once __DIR__ . '/admin-pages/uploads_privacy/settings.php';
            new FAFE_Uploads_Privacy_Settings( $this );
            require_once __DIR__ . '/admin-pages/hide_admin/settings.php';
            new FAFE_Hide_Admin_Settings( $this );
            require_once __DIR__ . '/admin-pages/google/settings.php';
            new FAFE_Google_API_Settings( $this );
        }
        
        public function fafe_validate_save_post()
        {
            if ( isset( $_POST['_acf_post_id'] ) && $_POST['_acf_post_id'] == 'fafe_options' ) {
                
                if ( isset( $_POST['_acf_admin_page'] ) ) {
                    $page_slug = $_POST['_acf_admin_page'];
                    apply_filters( 'fafe/' . $page_slug . '_fields', [] );
                }
            
            }
        }
        
        public function fafe_scripts()
        {
            
            if ( fafe()->dev_mode ) {
                $min = '';
            } else {
                $min = '.min';
            }
            
            wp_register_style(
                'fafe-forms',
                FAFE_URL . 'includes/assets/css/forms.min.css',
                array(),
                FAFE_ASSETS_VERSION
            );
            wp_register_style(
                'fafe-modal',
                FAFE_URL . 'includes/assets/css/modal.min.css',
                array(),
                FAFE_ASSETS_VERSION
            );
            wp_register_script(
                'fafe-forms',
                FAFE_URL . 'includes/assets/js/forms' . $min . '.js',
                array( 'jquery', 'acf-input' ),
                FAFE_ASSETS_VERSION,
                true
            );
            wp_register_script(
                'fafe-modal',
                FAFE_URL . 'includes/assets/js/modal.min.js',
                array( 'jquery' ),
                FAFE_ASSETS_VERSION
            );
            wp_register_script(
                'fafe-password-strength',
                FAFE_URL . 'includes/assets/js/password-strength.min.js',
                array( 'password-strength-meter' ),
                FAFE_ASSETS_VERSION,
                true
            );
        }
        
        public function __construct()
        {
            if ( is_admin() ) {
                acf_enqueue_scripts();
            }
            $this->fafe_settings_sections();
            add_action( 'wp_loaded', [ $this, 'fafe_scripts' ] );
            add_action( 'init', [ $this, 'fafe_configs' ] );
            add_action( 'admin_menu', [ $this, 'fafe_plugin_page' ] );
            add_action( 'acf/validate_save_post', [ $this, 'fafe_validate_save_post' ] );
            $this->add_tabs();
        }
    
    }
    fafe()->settings_tabs = new FrontendAdmin_Settings();
}
