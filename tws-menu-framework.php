<?php
/**
 * Plugin Name: The Web Solver Admin Menu Framework
 * Plugin URI: https://github.com/TheWebSolver/tws-menu-framework
 * Description: <b>WordPress Admin Menu framework</b> to manage admin menus.
 * Version: 1.0
 * Author: Shesh Ghimire
 * Author URI: https://www.linkedin.com/in/sheshgh/
 * Requires at least: 5.3
 * Requires PHP: 7.1
 * Text Domain: tws-core
 * License: GNU General Public License v3.0 (or later)
 * License URI: https://www.gnu.org/licenses/gpl-3.0.txt
 *
 * @package TheWebSolver\Core\Menu_Framework
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

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * The Web Solver Custom Post Type Framework class
 * 
 * @since 1.0
 */
final class HZFEX_Menu_Framework {
    /**
     * Creates an instance of this class.
     *
     * @since 1.0
     * 
     * @static
     * 
     * @access public
     * 
     * @return HZFEX_Menu_Framework
     */
    public static function activate(): HZFEX_Menu_Framework {
        static $tws_menu;
        if( ! is_a( $tws_menu, get_class() ) ) {
            $tws_menu = new self();
            $tws_menu->define_constants()->require_main_file();
        }
        return $tws_menu;
    }

    /**
     * Define plugin constants.
     *
     * @return HZFEX_Menu_Framework
     * 
     * @since 1.0
     * 
     * @access public
     */
    public function define_constants() {
        // Define plugin textdomain.
        // TWS Core plugin already defines it.
        if( ! defined( 'HZFEX_TEXTDOMAIN' ) ) define( 'HZFEX_TEXTDOMAIN', 'tws-core' );

        // Define plugin debug mode. DEBUG: set to true when needed.
        // TWS Core plugin already defines it.
        if( ! defined( 'HZFEX_DEBUG_MODE' ) ) define( 'HZFEX_DEBUG_MODE', true );

        // Define constants.
        define( 'HZFEX_MENU' , __( 'The Web Solver Admin Menu Framework' , HZFEX_TEXTDOMAIN ) );
        define( 'HZFEX_MENU_FILE' , __FILE__ );
        define( 'HZFEX_MENU_URL', plugin_dir_url( __FILE__ ) );
        define( 'HZFEX_MENU_BASENAME', plugin_basename( __FILE__ ) );
        define( 'HZFEX_MENU_PATH', plugin_dir_path( __FILE__ ) );
        define( 'HZFEX_MENU_VERSION', '1.0' );
        define( 'HZFEX_ADMIN_MENU', 'tws_dashboard' );
        return $this;
    }

    /**
     * Require main plugin file.
     *
     * @return HZFEX_Menu_Framework
     * 
     * @since 1.0
     * 
     * @access public
     */
    public function require_main_file() {
        require_once __DIR__ . '/Includes/Menu.php';
    }

    /**
     * Initialize Plugin class.
     *
     * @return TheWebSolver\Core\Menu\Plugin
     * 
     * @since 1.0
     * 
     * @access public
     */
    public function plugin(): TheWebSolver\Core\Menu\Plugin {
        return TheWebSolver\Core\Menu\Plugin::boot();
    }

    /**
     * Prevent direct instantiation.
     * 
     * @since 1.0
     */
    private function __construct() {}
}

/**
 * Main function to instantiate HZFEX_Menu_Framework class.
 *
 * @return HZFEX_Menu_Framework
 * 
 * @since 1.0
 */
function tws_menu(): HZFEX_Menu_Framework {
    return HZFEX_Menu_Framework::activate();
}

// Initializes the plugin.
tws_menu()->plugin();