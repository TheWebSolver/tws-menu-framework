<?php
/**
 * TheWebSolver\Core\Menu\Admin_Bar class.
 * 
 * Extend functionality of WP_Admin_Bar.
 * Only available on `admin_bar_menu` hook or later.
 * WordPress default hooked menus:
 *      1. User related, aligned right.
 *          1.1. Priority 0  - account Menu.
 *          1.2 Priority 4   - Search Menu.
 *          1.3 Priority 7   - My Account Item.
 *          1.4 Priority 8   - Recovery Mode Menu.
 *      2. Site related.
 *          2.1 Priority 0   - Sidebar Toggle.
 *          2.2 Priority 10  - WP Menu.
 *          2.3 Priority 20  - My Sites Menu.
 *          2.4 Priority 30  - Site Menu.
 *          2.5 Priority 40  - Customize Menu.
 *          2.6 Priority 50  - Updates Menu.
 *      3. Content related. if ( ! is_network_admin() && ! is_user_admin() ).
 *          3.1 Priority 60  - Comments Menu.
 *          3.2 Priority 70  - New Content Menu.
 *      4. Edit menu.
 *          4.1 Priority 80  - Edit Menu.
 *      5. Secondary groups. Howdy and wp-logos.
 *          5.1 Priority 200 - Secondary Groups.
 * 
 * @source wp-includes\class-wp-admin-bar.php  WP_Admin_Bar class API
 * @source wp-includes\admin-bar.php           Top-level Toolbar API
 * 
 * @package TheWebSolver\Core\Menu_Framework\Admin_Bar\Class\API
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

/**
 * Admin_Bar class
 * 
 * @api
 */
final class Admin_Bar {
    /**
     * WordPress Admin Bar instance passed from `admin_bar_menu` hook.
     *
     * @var \WP_Admin_Bar
     * 
     * @since 1.1
     * 
     * @access public
     */
    private $admin_bar;

    /**
     * Debug status.
     *
     * @var bool
     * 
     * @since 1.1
     * 
     * @access private
     */
    private $debug;

    /**
     * The debug admin bar menu (node) ID.
     *
     * @var string
     * 
     * @since 1.1
     * 
     * @access private
     */
    private $debug_id;

    /**
     * The debug admin bar menu (node) prefix.
     *
     * @var string
     * 
     * @since 1.1
     * 
     * @access private
     */
    private $debug_prefix;

    /**
     * Admin bar menu (node) args to be added.
     * 
     * @var array
     * 
     * @since 1.1
     *
     * @access public
     */
    public $add = [];

    /**
     * Admin bar menu (node) IDs to be removed.
     *
     * @var string[]
     * 
     * @since 1.1
     * 
     * @access public
     */
    public $remove = [];

    /**
     * Admin bar menu (node) IDs not to be removed.
     * 
     * @var string[]
     * 
     * @since 1.1
     * 
     * @access public
     */
    public $exclude = [];

    /**
     * Initialize class instance.
     * 
     * @param mixed $admin_bar The WordPress Admin Bar class instance.
     *
     * @return Admin_Bar
     * 
     * @since 1.1
     * 
     * @static
     * 
     * @access public
     */
    public static function init(): Admin_Bar {
        static $adminbar;
        if( ! is_a( $adminbar, get_class() ) ) {
            $adminbar = new self();
        }
        return $adminbar;
    }

    /**
     * Sets property, performs WordPress action hooks.
     *
     * @param \WP_Admin_Bar $admin_bar
     * 
     * @return void
     * 
     * @since 1.1
     * 
     * @access public
     */
    public function set( \WP_Admin_Bar $admin_bar ) {
        $this->admin_bar    = $admin_bar;
        $this->debug        = defined( 'HZFEX_DEBUG_MODE' ) && HZFEX_DEBUG_MODE;
        $this->debug_id     = 'hz_debug_node_ids';
        $this->debug_prefix = 'hz_debug_node_';

        // Default admin menu (node) IDs not to remove.
        $exclude = [
            'top-secondary', // main parent on right side.
            'my-account', // parent to show "Howdy, <username>".
            'user-actions', // group to show user links.
            'site-name' // parent to show site name on left side after logo.
        ];
        $this->exclude = array_combine( $exclude, $exclude ); // make value as key.

        // Turn on admin bar debug mode.
        // This will get all admin bar menu (node) IDs,
        // create a new parent menu called "Node IDs", and
        // add to that "Node IDs" admin bar menu.
        // Useful to find node ID if want to remove any of them.
        if( $this->debug ) {
            $this->exclude[ 'hz_debug_node_ids' ] = 'hz_debug_node_ids';
            add_action( 'wp_before_admin_bar_render', [ $this, 'debug' ], 9997 );
        }

        // Remove admin bar menus (nodes).
        add_action( 'wp_before_admin_bar_render', [ $this, 'remove_menus' ], 9998 );

        // Add new admin bar menus (nodes). Higher priority than removal
        // to prevent removing anything added with this API.
        add_action( 'wp_before_admin_bar_render', [ $this, 'add_menus' ], 9999 );
    }

    /**
     * Adds admin bar menu (node) or group.
     * 
     * ### NOTE: Use with `admin_bar_menu` hook with high priority `1000`.
     *
     * @param string $id              Unique ID for admin bar menu.
     * @param string $title           Accepts HTML formatted title.
     * @param string $href            Target URL link.
     * @param boolean $group          Set menu as group only. Defaults to `false`.
     * @param boolean $parent         Set menu as child to this. Defaults to `false`.
     * @param array $meta             Accepted values are:
     * * @type `string` **class**     HTML `class` attribute.
     * * @type `string` **lang**      HTML `lang` attribute.
     * * @type `string` **dir**       HTML link `dir` attribute for text directionality.
     * * @type `string` **title**     HTML link `title` attribute.
     * * @type `string` **rel**       HTML link `rel` attribute.
     * * @type `string` **onclick**   HTML link `onclick` attribute.
     * * @type `string` **target**    HTML `target` attribute to display the linked URL.
     * * @type `mixed` **tabindex**   Numeric value. Whether admin bar is tabbable using keyboard   tab key.
     * 
     * @return true
     * 
     * @since 1.1
     * 
     * @access public
     */
    public function add( string $id, string $title, string $href, bool $group = false, bool $parent = false, array $meta = [] ) {
        // Set class.
        $class = 'hz_ab_menu';
        if( isset( $meta['class'] ) && ! empty( $meta['class'] ) ) {
            $class .= ' ' . $meta['class'];

            // Remove class. Add it separately.
            unset( $meta['class'] );
        }
        $menu = [
            'id'        => $id,
            'meta'      => [ 'class' => $class ],
            'parent'    => $parent,
        ];

        if( sizeof( $meta ) > 0 ) {
            foreach( $meta as $key => $arg ) {
                // Only continue if arg has value.
                if( empty( $arg ) ) {
                    continue;
                }
                // Set meta value from arg.
                $menu['meta'][$key] = $arg;
            }
        }
        // Check if is group and set args value accordingly.
        if( $group ) {
            $menu['group']     = true;
        } else {
            $menu['title']     = $title;
            $menu['href']      = $href;
        }
        $this->add[ $menu[ 'id' ] ] = $menu;
        return true;
    }

    /**
     * Removes admin bar menus (nodes).
     * 
     * ### NOTE: -
     * ### Use with `admin_bar_menu` hook with high priority `1000`.
     *
     * @param string|string[]|null $menu_ids   Admin bar menu ID or IDs in an array. Defaults to null. i.e. remove all admin bar menus.
     * @param bool $invert                     Whether to invert param `$menu_ids`. Set to true if not to remove ID/IDs supplied with param `$menu_ids`. Defaults to `false`. {
     * 
     * **_NOTE: If invert is `TRUE`, provide parent menu ID also, if any, so it won't get removed._**
     * }
     * @param bool $sitename                   Whether to remove parent menu **site name** on left side. Defaults to `false`. i.e. don't remove.
     * @param bool $myaccount                  Whether to remove parent menu **my account** on right side. Defaults to `false`. i.e. don't remove.
     * 
     * @return true
     * 
     * @since 1.1
     * 
     * @access public
     */
    public function remove( $menu_ids = null, bool $invert = false, bool $sitename = false, bool $myaccount = false ) {
        $nodes  = $this->admin_bar->get_nodes(); // Get all menus. 
        $ids    = wp_list_pluck( $nodes, 'id' ); // Get menu IDs.

        // Convert given menu IDs to an array.
        $remove = is_array( $menu_ids ) ? (array) $menu_ids : [ $menu_ids ];

        // Remove site-name menu, if set to true.
        if( $sitename && isset( $this->exclude[ 'site-name' ] ) ) {
            unset( $this->exclude[ 'site-name' ] );
        }

        // Remove my-account menu, if set to true.
        if( $myaccount && isset( $this->exclude[ 'my-account' ] ) ) {
            unset( $this->exclude[ 'my-account' ] );
        }

        // Remove given menu/menus if not inverting.
        if( ! $invert ) {
            if( null === $menu_ids ) {
                $this->remove = $ids;
                return true;
            }

            // Remove all menus given.
            foreach( $remove as $node ) {
                // Only continue if not excluded already.
                if( isset( $this->exclude[ $node ] ) ) {
                    continue;
                };
                $this->remove[ $node ] = $node;
            }
            return true;
        }

        // Remove all menus except those given when invert is true.
        foreach( $remove as $node ) {
            $this->exclude[ $node ] = $node;
        }
        $this->remove = $ids;
        return true;
    }

    /**
     * Adds admin bar menu (node) IDs for exclusion.
     * 
     * ### NOTE: -
     * ### Use before {@see @method `Admin_Bar::remove()`} so excluded IDs are set first. Excluded IDs are set at {@see @property `Admin_Bar::$exclude`}.
     * ### Use with `admin_bar_menu` hook with high priority `1000`.
     * 
     * When providing node IDs, keep in mind to add desired IDs\
     * that may be parent, childern or grand-children. This means\
     * excluding parent IDs will not prevent it's child node IDs\
     * to get removed. Include child IDs also to get excluded and vice-versa.
     *
     * @param string|string[] $ids  Single ID in string or multiple in an array.
     * 
     * @return true
     * 
     * @since 1.1
     * 
     * @access public
     */
    public function exclude( $ids ) {
        $nodes = is_array( $ids ) ? (array) $ids : [ $ids ];

        // Iterate over given IDs and add for exclusion.
        foreach( $nodes as $node ) {
            $this->exclude[ $node ] = $node;
        }
        return true;
    }

    /**
     * Adds admin bar menus (nodes).
     *
     * @return bool True on success, false otherwise.
     * 
     * @since 1.1
     * 
     * @access public
     */
    public function add_menus() {
        // Bail early if no menus (nodes) found to add to admin bar.
        if( sizeof( $this->add ) === 0 ) {
            return false;
        }

        // Iterate over all set node args and add to admin bar.
        foreach( $this->add as $node ) {
            $this->admin_bar->add_node( $node );
        }
        return true;
    }

    /**
     * Removes admin bar menus (nodes).
     * 
     * @return bool True on success, false otherwise.
     * 
     * @since 1.1
     * 
     * @access public
     */
    public function remove_menus() {
        // Bail early if admin bar menu IDs to be removed not found.
        if( sizeof( $this->remove ) === 0 ) {
            return false;
        }

        // Generate unique IDs and ignore removing those in exclusion list.
        $remove  = array_unique( $this->remove );
        $exclude = array_unique( $this->exclude );
        $remove  = array_diff( $remove, $exclude );

        // Iterate over all nodes and remove.
        foreach ( $remove as $id ) {
            $this->admin_bar->remove_node( $id );
        }
        return true;
    }

    /**
     * Gets admin bar menu (node) that has parent set.
     *
     * @param string $node_id The admin bar menu (node) ID.
     * 
     * @return array Node data that has valid parent, empty array otherwise.
     * 
     * @since 1.1
     * 
     * @access public
     */
    public function get_node_with_parent( string $node_id ): array {
        $nodes  = $this->admin_bar->get_nodes();
        return array_filter( $nodes, function( $node ) use ( $node_id ) {
            return $node->id === $node_id && isset( $node->parent ) && $node->parent;
        } );
    }

    /**
     * Adds all node IDs to admin bar for debugging purpose.
     *
     * @return void
     * 
     * @since 1.1
     * 
     * @access public
     */
    public function debug() {
        // Get all admin bar menu (nodes).
        $nodes  = $this->admin_bar->get_nodes();

        // Add a top-level menu to admin bar.
        $parent_menu = [
            'id'    => $this->debug_id,
            'title' => 'Node IDs'
        ];

        // Add parent admin bar menu (node).
        $this->admin_bar->add_node( $parent_menu );

        // Iterate and set all node IDs as child to parent menu.
        foreach ( $nodes as $node ) {
            // Prepare child menu args.
            $child_menu = [
                'id'     => $this->debug_prefix . $node->id,
                'title'  => $node->id,
                'meta'   => [ 'class' => 'hz_debug_node ' . $this->debug_prefix . $node->id ],
            ];

            // Set parents according to node parent status.
            if ( isset( $node->parent ) && $node->parent ) {
                // Default parents if child node already has parent.
                $child_menu[ 'parent' ] = $this->debug_prefix . $node->parent;
            } else {
                // Debug parent if defaults is the parent.
                $child_menu[ 'parent' ] = $this->debug_id;
            }

            // Add child admin bar menus (nodes).
            $this->admin_bar->add_node( $child_menu );
        }
    }

    /**
     * Private constructor to prevent direct instantiation.
     * 
     * @return void
     * 
     * @since 1.1
     * 
     * @access private
     */
    private function __construct() {}
}