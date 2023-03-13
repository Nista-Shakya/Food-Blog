<?php

/**
 * Plugin Name: Frontend Admin
 * Plugin URI: https://wordpress.org/plugins/frontend-admin/
 * Description: An Elementor extension that allows you to easily display frontend forms on your site so your clients can easily edit their posts, pages, and users by themselves all from the frontend.
 * Version:     2.0.1
 * Author:      Shabti Kaplan
 * Author URI:  https://kaplanwebdev.com/
 * Text Domain: frontend-admin
 * Domain Path: /languages/
 *
 */
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( !function_exists( 'fafe' ) ) {
    // Create a helper function for easy SDK access.
    function fafe()
    {
        global  $fafe ;
        
        if ( !isset( $fafe ) ) {
            // Include Freemius SDK.
            require_once dirname( __FILE__ ) . '/includes/freemius/start.php';
            $fafe = fs_dynamic_init( array(
                'id'             => '6146',
                'slug'           => 'frontend-admin',
                'premium_slug'   => 'frontend-admin-pro',
                'type'           => 'plugin',
                'public_key'     => 'pk_35e1719e41770d8d002bf114e2a89',
                'is_premium'     => false,
                'premium_suffix' => 'Pro',
                'has_addons'     => false,
                'has_paid_plans' => true,
                'trial'          => array(
                'days'               => 7,
                'is_require_payment' => true,
            ),
                'menu'           => array(
                'slug'    => 'fafe-settings',
                'support' => false,
            ),
                'is_live'        => true,
            ) );
        }
        
        // Turn off dev mode
        $fafe->dev_mode = apply_filters( 'fafe_dev_mode', false );
        return $fafe;
    }
    
    // Init Freemius.
    fafe();
    // Signal that SDK was initiated.
    do_action( 'fafe_loaded' );
}

define( 'FAFE_VERSION', '2.0.0' );
define( 'FAFE_ASSETS_VERSION', '6.7.17' );
define( 'FAFE_NAME', plugin_basename( __FILE__ ) );
define( 'FAFE_URL', plugin_dir_url( __FILE__ ) );
define( 'FAFE_PLUGIN_DIR', WP_PLUGIN_DIR . '/frontend-admin' );
/**
 * Main Frontend Admin Class
 *
 * The main class that initiates and runs the plugin.
 *
 * @since 1.0.0
 */
final class Frontend_Admin_EL
{
    /**
     * Minimum Elementor Version
     *
     * @since 1.0.0
     *
     * @var string Minimum Elementor version required to run the plugin.
     */
    const  MINIMUM_ELEMENTOR_VERSION = '2.6.0' ;
    /**
     * Minimum PHP Version
     *
     * @since 1.0.0
     *
     * @var string Minimum PHP version required to run the plugin.
     */
    const  MINIMUM_PHP_VERSION = '5.2.4' ;
    /**
     * Instance
     *
     * @since 1.0.0
     *
     * @access private
     * @static
     *
     * @var Frontend_Admin_EL The single instance of the class.
     */
    private static  $_instance = null ;
    /**
     * Instance
     *
     * Ensures only one instance of the class is loaded or can be loaded.
     *
     * @since 1.0.0
     *
     * @access public
     * @static
     *
     * @return Frontend_Admin_EL An instance of the class.
     */
    public static function instance()
    {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    /**
     * Constructor
     *
     * @since 1.0.0
     *
     * @access public
     */
    public function __construct()
    {
        add_action( 'init', [ $this, 'i18n' ] );
        add_action( 'plugins_loaded', [ $this, 'init' ] );
    }
    
    /**
     * Load Textdomain
     *
     * Load plugin localization files.
     *
     * Fired by `init` action hook.
     *
     * @since 1.0.0
     *
     * @access public
     */
    public function i18n()
    {
        load_plugin_textdomain( 'frontend-admin', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
    }
    
    /**
     * Initialize the plugin
     *
     * Load the plugin only after Elementor (and other plugins) are loaded.
     * Checks for basic plugin requirements, if one check fail don't continue,
     * if all check have passed load the files required to run the plugin.
     *
     * Fired by `plugins_loaded` action hook.
     *
     * @since 1.0.0
     *
     * @access public
     */
    public function init()
    {
        if ( !class_exists( 'ACF' ) ) {
            $this->include_acf();
        }
        
        if ( function_exists( 'acfef' ) ) {
            add_action( 'admin_notices', [ $this, 'admin_notice_acf_frontend_active' ] );
            return;
        }
        
        // Check if Elementor installed and activated
        
        if ( !did_action( 'elementor/loaded' ) ) {
            add_action( 'admin_notices', [ $this, 'admin_notice_missing_main_plugin' ] );
            return;
        }
        
        // Check for required Elementor version
        
        if ( !version_compare( ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '>=' ) ) {
            add_action( 'admin_notices', [ $this, 'admin_notice_minimum_elementor_version' ] );
            return;
        }
        
        // Check for required PHP version
        
        if ( version_compare( PHP_VERSION, self::MINIMUM_PHP_VERSION, '<' ) ) {
            add_action( 'admin_notices', [ $this, 'admin_notice_minimum_php_version' ] );
            return;
        }
        
        //add_filter( 'plugin_row_meta', [ $this, 'fafe_row_meta' ], 10, 2 );
        $this->plugin_includes();
    }
    
    public function plugin_includes()
    {
        require_once __DIR__ . '/includes/elementor/module.php';
        require_once __DIR__ . '/includes/frontend/module.php';
        require_once __DIR__ . '/includes/admin/module.php';
    }
    
    /**
     * Admin notice
     *
     * Warning when the site doesn't have Elementor installed or activated.
     *
     * @since 1.0.0
     *
     * @access public
     */
    public function admin_notice_acf_frontend_active()
    {
        if ( isset( $_GET['activate'] ) ) {
            unset( $_GET['activate'] );
        }
        $message = sprintf(
            /* translators: 1: Plugin name 2: Elementor */
            esc_html__( '"%1$s" cannot be activated together with "%2$s".', 'frontend-admin' ),
            '<strong>' . esc_html__( 'Frontend Admin', 'frontend-admin' ) . '</strong>',
            '<strong>' . esc_html__( 'ACF Frontend', 'frontend-admin' ) . '</strong>'
        );
        printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );
    }
    
    /**
     * Admin notice
     *
     * Warning when the site doesn't have Elementor installed or activated.
     *
     * @since 1.0.0
     *
     * @access public
     */
    public function admin_notice_missing_main_plugin()
    {
        if ( isset( $_GET['activate'] ) ) {
            unset( $_GET['activate'] );
        }
        $message = sprintf(
            /* translators: 1: Plugin name 2: Elementor */
            esc_html__( '"%1$s" requires "%2$s" to be installed and activated.', 'frontend-admin' ),
            '<strong>' . esc_html__( 'Frontend Admin', 'frontend-admin' ) . '</strong>',
            '<strong>' . esc_html__( 'Elementor', 'frontend-admin' ) . '</strong>'
        );
        printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );
    }
    
    public function fafe_notice_dismissed()
    {
        $user_id = get_current_user_id();
        if ( isset( $_GET['fafe_notice_dismiss'] ) ) {
            add_user_meta(
                $user_id,
                'fafe_notice_dismiss',
                'true',
                true
            );
        }
    }
    
    /**
     * Admin notice
     *
     * Warning when the site doesn't have a minimum required Elementor version.
     *
     * @since 1.0.0
     *
     * @access public
     */
    public function admin_notice_minimum_elementor_version()
    {
        if ( isset( $_GET['activate'] ) ) {
            unset( $_GET['activate'] );
        }
        $message = sprintf(
            /* translators: 1: Plugin name 2: Elementor 3: Required Elementor version */
            esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'frontend-admin' ),
            '<strong>' . esc_html__( 'Frontend Admin', 'frontend-admin' ) . '</strong>',
            '<strong>' . esc_html__( 'Elementor', 'frontend-admin' ) . '</strong>',
            self::MINIMUM_ELEMENTOR_VERSION
        );
        printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );
    }
    
    /**
     * Admin notice
     *
     * Warning when the site doesn't have a minimum required PHP version.
     *
     * @since 1.0.0
     *
     * @access public
     */
    public function admin_notice_minimum_php_version()
    {
        if ( isset( $_GET['activate'] ) ) {
            unset( $_GET['activate'] );
        }
        $message = sprintf(
            /* translators: 1: Plugin name 2: PHP 3: Required PHP version */
            esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'frontend-admin' ),
            '<strong>' . esc_html__( 'Frontend Admin', 'frontend-admin' ) . '</strong>',
            '<strong>' . esc_html__( 'PHP', 'frontend-admin' ) . '</strong>',
            self::MINIMUM_PHP_VERSION
        );
        printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );
    }
    
    public function fafe_row_meta( $links, $file )
    {
        
        if ( FAFE_NAME == $file ) {
            $row_meta = array(
                'video' => '<a href="' . esc_url( 'https://www.youtube.com/channel/UC8ykyD--K6pJmGmFcYsaD-w/playlists' ) . '" target="_blank" aria-label="' . esc_attr__( 'Video Tutorials', 'frontend-admin' ) . '" >' . esc_html__( 'Video Tutorials', 'frontend-admin' ) . '</a>',
            );
            return array_merge( $links, $row_meta );
        }
        
        return (array) $links;
    }
    
    public function include_acf()
    {
        // Define path and URL to the ACF plugin.
        define( 'FA_ACF_PATH', __DIR__ . '/includes/acf/' );
        define( 'FA_ACF_URL', FAFE_URL . '/includes/acf/' );
        // Include the ACF plugin.
        include_once FA_ACF_PATH . 'acf.php';
        // Customize the url setting to fix incorrect asset URLs.
        add_filter( 'acf/settings/url', function ( $url ) {
            return FA_ACF_URL;
        } );
    }

}
Frontend_Admin_EL::instance();