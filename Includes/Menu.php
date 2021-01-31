<?php
/**
 * TheWebSolver\Core\Menu\Plugin class.
 * 
 * Handles plugin initialization.
 * 
 * @package TheWebSolver\Core\Menu_Framework\Class
 * 
 * -----------------------------------
 * DEVELOPED-MAINTAINED-SUPPPORTED BY
 * -----------------------------------
 * ███║     ███╗   ████████████████
 * ███║     ███║   ═════════██████╗
 * ███║     ███║        ╔══█████═╝
 *  ████████████║      ╚═█████
 * ███║═════███║      █████╗
 * ███║     ███║    █████═╝
 * ███║     ███║   ████████████████╗
 * ╚═╝      ╚═╝    ═══════════════╝
 */

namespace TheWebSolver\Core\Menu;

use function TheWebSolver\Core\admin_menu;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Plugin class.
 */
final class Plugin {
    /**
     * Plugin args.
     *
     * @var array
     * 
     * @since 1.0
     * 
     * @access public
     */
    public $args;

    /**
     * Main menu hook suffix.
     * 
     * @since 1.0
     * 
     * @access public
     */
    var $hook_suffix;

    /**
     * Boot framework.
     *
     * @return Plugin
     * 
     * @since 1.0
     * 
     * @static
     * 
     * @access public
     */
    public static function boot(): Plugin {
        static $menu;
        if( ! is_a( $menu, get_class() ) ) {
            $menu = new self();
            $menu->init();
        }
        return $menu;
    }

    /**
     * Include files, Initialize WordPress actions and hooks.
     * 
     * @since 1.0
     * 
     * @access public
     */
    public function init() {
        // Set args.
        $this->args = [
            'id'        => basename( HZFEX_MENU_BASENAME, '.php' ),
            'name'      => HZFEX_MENU,
            'version'   => HZFEX_MENU_VERSION,
            'activated' => in_array( HZFEX_MENU_BASENAME, get_option( 'active_plugins' ), true ),
            'loaded'    => in_array( HZFEX_MENU_BASENAME, get_option( 'active_plugins' ), true ),
            'scope'     => 'framework'
        ];

        // Register this plugin as extension to TWS Core.
        // Using inside hook so it always fire after core plugin is loaded.
        add_action( 'hzfex_core_loaded', [$this, 'register'] );

        // Execute all necessary codes in admin menu hook.
        if( is_admin() ) {
            add_action( 'admin_menu', [$this, 'init_admin_menu'], -99 );
        }

        /**
         * Introduced admin bar menu API.
         * 
         * Since admin bar can be displayed on frontend also,
         * omit `is_admin()` conditional check.
         * 
         * @since 1.1
         */
        add_action( 'admin_bar_menu', [$this, 'init_admin_bar'], -99 );
    }

    /**
     * Registers this plugin as an extension.
     * 
     * Makes this plugin an extension of **The Web Solver Extended** plugin.
     * 
     * @return void
     * 
     * @link TODO: add later
     * 
     * @since 1.0
     * 
     * @access public
     */
    public function register() {
        // Check if core eixists before registering.
        if( function_exists( 'tws_core' ) ) {
            tws_core()->extensions()->register( $this->args );
        }
    }

    /**
     * Init admin menu.
     *
     * @return void
     * 
     * @since 1.0
     * 
     * @access public
     */
    public function init_admin_menu() {
        require_once __DIR__ . '/Source/Admin-Menu.php';
        require_once __DIR__ . '/API/Admin-Menu-API.php';

        // Initialize admin menu class.
        admin_menu();

        /**
         * WPHOOK: Action -> Fires after admin menu loaded.
         */
        do_action( 'hzfex_menu_loaded' );

        // Add styles and scripts.
        add_action( 'admin_enqueue_scripts', [$this, 'add_scripts'] );
    }

    /**
     * Init admin menu bar.
     * 
     * @param \WP_Admin_Bar $admin_bar Passed as reference from hook.
     *
     * @return void
     * 
     * @since 1.1
     * 
     * @access public
     */
    public function init_admin_bar( \WP_Admin_Bar $admin_bar ) {
        require_once __DIR__ . '/Source/Admin-Bar.php';
        require_once __DIR__ . '/API/Admin-Bar-API.php';

        // Initialize admin bar class.
        Admin_Bar::init()->set( $admin_bar );
    }

    /**
     * Adds styles and scripts.
     *
     * @return void
     * 
     * @since 1.0
     * 
     * @access public
     */
    public function add_scripts() {
        wp_enqueue_style( 'hzfex_admin_menu_style', HZFEX_MENU_URL . '/Includes/Assets/CSS/Admin.css', [], '1.0.0' );
    }

    /**
     * Private constructor to prevent direct instantiation.
     * 
     * @since 1.0
     * 
     * @access private
     */
    private function __construct() {}
}