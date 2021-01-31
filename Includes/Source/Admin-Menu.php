<?php
/**
 * TheWebSolver\Core\Menu\Admin_Menu class.
 * 
 * The elements in the menu are:
 *     0: Menu item name.
 *     1: Minimum level or capability required.
 *     2: The URL of the item's file.
 *     3: Page title.
 *     4: Classes.
 *     5: ID.
 *     6: Icon for top level menu.
 * 
 * @package TheWebSolver\Core\Menu_Framework\Admin_Menu\Class\API
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

use function is_customize_preview;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Admin_Menu class
 * 
 * @api
 */
final class Admin_Menu {
	/**
	 * The menu hook suffix.
	 *
	 * @var string
	 * 
	 * @since 1.0
	 * 
	 * @access private
	 */
	private $hook_suffix;

	/**
	 * Menu slug.
	 * 
	 * @var string
	 * 
	 * @since 1.0
	 * 
	 * @access private
	 */
    private $menu_slug = HZFEX_ADMIN_MENU;

	/**
	 * Dashboard menu class.
	 *
	 * @var string
	 * 
	 * @since 1.0
	 * 
	 * @access public
	 */
	public $menu_classes;

	/**
	 * Initialize class instance.
	 *
	 * @return Admin_Menu
	 * 
	 * @since 1.0
	 * 
	 * @static
	 * 
	 * @access public
	 */
	public static function init(): Admin_Menu {
		static $menu;
		if( ! is_a( $menu, get_class() ) ) {
			$menu = new self();
			$menu->hook()->_add_class();
		}
		return $menu;
	}

	/**
	 * Private constructor to prevent direct instantiation.
	 * 
	 * @since 1.0
	 * 
	 * @access private
	 */
	private function __construct() {
		/**
		 * WPHOOK: Filter -> Class to be applied to dashboard menu.
		 * 
		 * @param string $class
		 * 
		 * @var string
		 */
		$this->menu_classes = apply_filters( 'hzfex_add_admin_dashboard_classes', 'hz_dashboard_menu' );
	}

	/**
	 * Performs WordPress action hooks.
	 *
	 * @return Admin_Menu;
	 * 
	 * @since 1.0
	 * 
	 * @access public
	 */
	public function hook(): Admin_Menu {
		/**
		 * WPHOOK: Filter -> Add custom dashboard menu.
		 * 
		 * @param bool $add Whether to add/remove welcome menu.
		 * 
		 * @example usage: 
		 * add_filter( 'hzfex_add_admin_dashboard_page', '__return_false' );
		 */
		if( apply_filters( 'hzfex_add_admin_dashboard_page', true ) ) {
			add_action( 'admin_menu', [$this, 'add_dashboard'] );
			add_action( 'admin_menu', [$this, 'remove_dashboard_submenus'], 999 );
		}
		return $this;
	}

	/**
	 * Add class to TWS dashboard menu.
	 *
	 * @return void
	 * 
	 * @since 1.0
	 * 
	 * @access public
	 */
	private function _add_class() {
		add_filter( 'add_menu_classes', function( $menu ) {
			$index  = $this->get_order_index( $this->menu_slug, true );
			$class = $menu[$index][4];
			$menu[$index][4] = $class . ' '. $this->menu_classes;
			return $menu;
		} );
	}

	/**
	 * Adds classes to main menus.
	 *
	 * @param string $menu_name The menu names or menu slugs in an array to be sorted.
	 * @param string $classes   The new classes to add.
	 * @param boolean $is_slug  Whether given param `$menu_name` is slug.
	 * 
	 * @return bool             True if class added, false otherwise.
	 * 
	 * @since 1.0
	 * 
	 * @access public
	 */
	public function add_classes( string $menu_name, string $classes = '', bool $is_slug = false ): bool {
		$slug = $is_slug ? $menu_name : $this->get_slug_by( $menu_name );

		// Bail early if given menu is tws dashboard.
		if( $slug === $this->menu_slug ) {
			return false;
		}

		// Add class to parent menus.
		return add_filter( 'add_menu_classes', function( $menu ) use ( $slug, $classes ) {
			$index  = $this->get_order_index( $slug, true );
			$class = $menu[$index][4];
			$menu[$index][4] = $class . ' ' . $classes;
			return $menu;
		} );
	}

	/**
	 * Adds plugin dashboard page.
	 * 
	 * This menu will be styled to display as profile card.
	 * 
	 * @return void
	 * 
	 * @since 1.0
	 * 
	 * @access public
	 */
	public function add_dashboard() {
		global $wp_roles;

		$user       = get_user_by( 'id', get_current_user_id() ); // wp_get_current_user() doesn't work to get roles.
		$role_slug  = $user->roles[0]; // Get the highest priority user role slug.
		$avatar     = get_avatar_url( $user );
		$title      = $user->display_name ? $user->display_name : $user->user_login;
		$subtitle   = translate_user_role( $wp_roles->role_names[$role_slug] );

		/**
		 * WPHOOK: Filter -> Change dashbaord menu args.
		 * 
		 * @param string[] $args Sets menu args value.
		 * * @type `string` `title` The menu title.
		 * * @type `string` `subtitle` The menu subtitle.
		 * * @type `string` `avatar` The menu icon as avatar.
		 * * @type `string` `capability` The user capability to show menu.  
		 * @param \WP_User $user The current logged in user.
		 * 
		 * @example usage:
		 * add_filter( 'hzfex_set_dashboard_menu_args', 'change_menu_args', 10, 2 );
		 * function change_menu_args( $args, $user ) {
		 *      if( $user->display_name === 'admin' ) {
		 *          $args['title']      = "Hsehs Z'roc";
		 *          $args['subtitle']   = "Senior Editor";
		 *          $args['capability'] = "edit_posts";
		 *          $args['avatar']     = PLUGIN_URL . '/assets/image/profile.jpg';
		 *      }
		 *      return $args;
		 * }
		 * 
		 * @var string[]
		 * 
		 * @since 1.0
		 */
		$args       = apply_filters( 'hzfex_set_dashboard_menu_args', [], $user );

		// Set vars from filtered args.
		$title      = isset( $args['title'] ) && ! empty( $args['title'] ) ? $args['title'] : $title;
		$subtitle   = isset( $args['subtitle'] ) && ! empty( $args['subtitle'] ) ? $args['subtitle'] : $subtitle;
		$capability = isset( $args['capability'] ) && ! empty( $args['capability'] ) ? $args['capability'] : 'read';
		$avatar     = isset( $args['avatar'] ) && ! empty( $args['avatar'] ) ? $args['avatar'] : $avatar;

		// Register the menu.
		$this->hook_suffix = add_menu_page(
			'Welcome',
			sprintf( '<span class="hz_userName">%s</span><span class="hz_userRole">%s</span>', $title, $subtitle ),
			$capability,
			HZFEX_ADMIN_MENU,
			[$this, 'load_dashboard_content'],
			$avatar,
			0.0001
		);

		// Load page.
		add_action( 'load-' .$this->hook_suffix, [$this, 'load_page'] );
	}

	/**
	 * Removes submenus added to the dashboard page.
	 * 
	 * This is to prevent showing submenus under main menu styled as profile card.
	 *
	 * @return void
	 * 
	 * @since 1.0
	 * 
	 * @access public
	 */
	public function remove_dashboard_submenus() {
		if( $this->exists( HZFEX_ADMIN_MENU, true ) ) {
			$submenus = $this->get_submenus( HZFEX_ADMIN_MENU, 'slug', true );
			if( false !== $submenus ) {
				foreach( $submenus as $submenu_slug ) {
					remove_submenu_page( HZFEX_ADMIN_MENU, $submenu_slug );
				}
			}
		}
	}

	/**
	 * Adds dashboard contents.
	 *
	 * @return void
	 * 
	 * @since 1.0
	 * 
	 * @access public
	 */
	public function load_dashboard_content(){
		/**
		 * WPHOOK: Action -> Add contents by hooking to this action.
		 */
		do_action( 'hzfex_load_admin_dashboard_content' );
	}

	/**
	 * Loads content only on dashboard page.
	 *
	 * @return void
	 * 
	 * @since 1.0
	 * 
	 * @access public
	 */
	public function load_page() {}

	/**
	 * Reorders menus.
	 *
	 * @param string[] $menu_slugs   The menu names or menu slugs in an array to be sorted.
	 *                               The position is determined relative to each other.
	 * @param bool $is_slug          Whether param `$menu_names` are slugs. Defaults to `false`.
	 * 
	 * @return void
	 * 
	 * @since 1.0
	 * 
	 * @access public
	 */
	public function order_by( array $menu_names, bool $is_slug = false ) {
		// Enable custom menu ordering first.
		add_filter( 'custom_menu_order', '__return_true' );

		// Convert to menu slugs if names given.
		$validate   = array_map( function( $menu ) {
			$slug   = $this->get_slug_by( $menu );
			return $slug ? $slug : '';
		}, $menu_names );

		// Set menu slugs.
		$slugs      = $is_slug ? $menu_names : $validate;

		// Filter to sort the menu position.
		add_filter( 'menu_order', function( $menu_slugs ) use ( $slugs ) {
			return $slugs;
		} );
	}

	/**
	 * Change main menu title and icon.
	 *
	 * @param string $old_name The menu name for which to change title.
	 * @param string $new_name The new menu name.
	 * @param string $new_icon The new menu icon.
	 * @param boolean $is_slug Whether parameter `$menu_name` is given as slug.
	 *                         Defaults to `false`.
	 * 
	 * @return mixed           The changed menu name and icon, false otherwise.
	 * 
	 * @global array $menu
	 * 
	 * @since 1.0
	 * 
	 * @access public
	 */
	public function change( string $old_name, string $new_name, string $new_icon = '', bool $is_slug = false ) {
		global $menu;

		// Get the menu data.
		$index  = $this->get_order_index( $old_name, $is_slug );

		// Bail early if menu doesn't exist.
		if( ! $index ) return false;

		// Replace old menu title with new title. 
		$menu[$index][0]    = $new_name;

		// Replace old menu icon with new icon, if given. 
		if( $new_icon !== '') {
			$menu[$index][6]    = $new_icon;
		}
	}

	/**
	 * Remove Menu/Submenu and redirect.
	 *
	 * @param string|string[] $menu_name                The menu name/names to work for. If array is given,
	 *                                                  `$exclude_submenu` won't work and all submenus
	 *                                                   will be removed if `$remove_submenu` is set to `true`.
	 * @param bool $remove_menu                         Whether remove menu or not. Defaults to `true`.
	 * @param string|string[]|bool $remove_submenus     Whether to remove submenus of the given menu.
	 *                                                  **true** removes all, **false** removes none,
	 *                                                  **string** removes single, **array** removes those in array.
	 * @param array $invert                             Whether to invert `$remove_submenus` value.
	 *                                                  Inverting will only remove not in `$remove_submenus`.
	 *                                                  It doesn't work if `$menu_name` is an array.
	 * @param bool $is_slug                             Whether given menu/submenu names are slugs. Defaults to `false`.
	 * @param string $capability                        User capability to ignore. Defaults to `manage_options`.
	 * @param bool $redirect                            Redirect removed menus/submenus or not. Defaults to `false`.
	 * @param string $redirect_url                      URL to redirect to. Defaults to `admin_url()`.
	 * 
	 * @return void|bool                                Remove/redirect menus/submenus if present, false otherwise.
	 * 
	 * @since 1.0
	 * 
	 * @access public
	 */
	public function remove( $menu_name, bool $remove_menu = true, $remove_submenus = true, bool $invert = false, bool $is_slug = false, string $capability = 'manage_options', bool $redirect = false, string $redirect_url = '' ) {
		// Bail early if user has given capability.
		if( current_user_can( $capability ) ) {
			return false;
		}

		// Execute for menu names in an array. Submenu exclusion doesn't work with this.
		if( is_array( $menu_name ) ) :
			foreach( $menu_name as $name ) :
				// Get the menu slug.
				$menu_slug  = $is_slug ? $name : $this->get_slug_by( $name );

				// Get all to be removed submenu slugs from the menu slug.
				$remove     = $this->get_submenus( $name, 'slug', $is_slug );

				// Remove dashboard pages.
				if( $menu_slug === 'index.php' ) {
					$this->remove_dashboard( $capability, $remove_submenus, $invert, $redirect );
					continue;
				}

				// Remove appearance pages.
				if( $menu_slug === 'themes.php' ) {
					$this->remove_appearance( $capability, $remove_submenus, $invert, $redirect );
					continue;
				}

				// Remove all submenus assigned to the menu.
				if( false !== $remove && true === $remove_submenus  ) :
					// Iterate over all submenus to be removed.
					foreach( $remove as $slug ) :
						// Redirect removed submenus if set.
						if( $redirect ) $this->redirect( $slug, $redirect_url );
						remove_submenu_page( $menu_slug, $slug );
					endforeach;
				endif;

				// Remove menu if set.
				if( $remove_menu ) :
					remove_menu_page( $menu_slug );
				endif;
			endforeach;
		elseif( is_string( $menu_name ) ) :
			// Get the menu slug.
			$menu_slug  = $is_slug ? $menu_name : $this->get_slug_by( $menu_name );

			// Get all submenus assigned to the menu.
			$submenus   = $this->get_submenus( $menu_slug, 'slug', true );

			if( (string) $menu_slug === 'index.php' ) :
				return $this->remove_dashboard( $capability, $remove_submenus, $invert, $redirect );
			elseif( (string) $menu_slug === 'themes.php' ) :
				return $this->remove_appearance( $capability, $remove_submenus, $invert, $redirect );
			elseif( false !== $submenus || false !== $remove_submenus ) :
				// Get submenus that needs to be removed.
				$remove = $this->_get_submenus_to_remove( $submenus, $remove_submenus, $invert );

				// Iterate over all submenus to be removed.
				foreach( $remove as $slug ) :
					// Redirect removed submenus if set.
					if( $redirect ) $this->redirect( $slug, $redirect_url );
					remove_submenu_page( $menu_slug, $slug );
				endforeach;
			endif;

			// Remove menu if set.
			if( $remove_menu ) :
				remove_menu_page( $menu_slug );
			endif;
		else :
			return false;
		endif;
	}

	/**
	 * Removes WordPress Admin dashboard.
	 *
	 * @param string $capability                      Current user capability to ignore.
	 *                                                Defaults to `manage_options`.
	 * @param string|string[]|bool $remove_submenus   Whether to remove submenus of the given menu.
	 *                                                **true** removes all, **false** removes none,
	 *                                                **string** removes single, **array** removes those in array.
	 *                                                Defaults to `true`.
	 * @param bool $invert                            Whether to invert `$remove_submenus` value.
	 *                                                Inverting will remove submenus except `$remove_submenus`.
	 *                                                Defaults to `false`.
	 * @param bool $redirect                          Whether to redirect removed menus/submenus.
	 *                                                Defaults to `false`.
	 * 
	 * @return void|false                             Remove dashboad and its submenus, false otherwise.
	 * 
	 * @global string $pagenow
	 * 
	 * @since 1.0
	 * 
	 * @access public
	 */
	public function remove_dashboard( string $capability = 'manage_options', $remove_submenus = true, bool $invert = false, bool $redirect = false ) {
		global $pagenow;

		// Bail early if current user has given capability.
		if( current_user_can( $capability ) ) return false;

		// Get all submenus assigned to dashboard menu.
		$submenus   = $this->get_submenus( 'index.php', 'slug', true );

		// Remove submenus if found.
		if( false !== $submenus || false !== $remove_submenus ) :
			// Get submenus that needs to be removed.
			$remove = $this->_get_submenus_to_remove( $submenus, $remove_submenus, $invert );

			// Iterate over submenus and remove.
			foreach( $remove as $r ) :
				if ( $pagenow === (string) $r && $redirect ) {
					wp_redirect( html_entity_decode( esc_url_raw( add_query_arg(
						[
							'http_referrer'	=> rawurlencode_deep( $pagenow ),
							'redirect'      => true
						],
						admin_url( "admin.php?page={$this->menu_slug}" ) )
					) ) ); exit;
				}
				remove_submenu_page( 'index.php', $r );
			endforeach;
		endif;
		remove_menu_page( 'index.php' );
	}

	/**
	 * Removes WordPress apperance menu.
	 *
	 * @param string $capability                      Current user capability to ignore.
	 *                                                Defaults to `manage_options`.
	 * @param string|string[]|bool $remove_submenus   Whether to remove appearance submenus.
	 *                                                **true** removes all, **false** removes none,
	 *                                                **string** removes single submenu,
	 *                                                **array** removes submenus in that array.
	 *                                                Defaults to `customize.php`.
	 * @param bool $invert                            Whether to invert `$remove_submenus` value.
	 *                                                Inverting will remove submenus except those in `$remove_submenus`.
	 *                                                Defaults to `true`. i.e. won't remove **Customizer**.
	 * @param bool $redirect                          Whether to redirect removed menus/submenus.
	 *                                                Defaults to `false`.
	 * 
	 * @return void|false                             Remove appearance menu/submenu, false otherwise.
	 * 
	 * @since 1.0
	 * 
	 * @access public
	 */
	public function remove_appearance( string $capability = 'manage_options', $remove_submenus = 'customize.php', bool $invert = true, bool $redirect = false ) {
		global $pagenow;

		// Bail early if current user has given capability.
		if( current_user_can( $capability ) ) return false;

		// Get all submenus assigned to appearance menu.
		$submenus   = $this->get_submenus( 'themes.php', 'slug', true );

		// Trim customizer submenu slug to "customize.php" for validation.
		$customize  = function( $sm ) {
			return substr( $sm, 0, 13 ) === 'customize.php' ?  'customize.php' : $sm;
		};

		// Finally, set submenus.
		$submenus   = array_map( $customize, $submenus );
		
		if( false !== $submenus || false !== $remove_submenus ) :
			// Get submenus that needs to be removed.
			$remove = $this->_get_submenus_to_remove( $submenus, $remove_submenus, $invert );

			foreach( $remove as $r ) :
				// Redirect and remove pages, if given.
				if( $pagenow === $r || ( is_customize_preview() && $r === 'customize.php' ) ) {
					if( $redirect ) {
						wp_redirect( html_entity_decode( esc_url( add_query_arg(
							[
								'http_referrer'	=> rawurlencode_deep( $_SERVER[ 'REQUEST_URI' ] ),
								'redirect'      => true
							], 
							admin_url( "admin.php?page={$this->menu_slug}" )
						) ) ) ); exit;
					}
				}
				remove_submenu_page( 'themes.php', $r );
			endforeach;
		endif;
		remove_menu_page( 'themes.php' );

	}

	/**
	 * Gets submenus to remove.
	 *
	 * @param string[] $submenus                       An array of submenus that belongs to the parent menu.
	 * @param string|string[]|bool $remove_submenus    Whether to remove submenus.
	 * * @type `bool` **true**                         Removes all submenus that belong to the parent menu.
	 * * @type `bool` **False**                        Won't remove submenus that belong to the parent menu.
	 * * @type `string`                                Remove the particular subemnu that belongs to the parent menu.
	 * * @type `string[]`                              Remove submenus given in array that belongs to the parent menu.
	 * @param bool $invert                             Whether to invert data from parameter `$remove_submenus`.
	 *                                                 Inverting will remove submenus except those in `$remove_submenus`.
	 * 
	 * @return string[]                                An array of submenus to be removed.
	 * 
	 * @since 1.0
	 * 
	 * @access private
	 */
	private function _get_submenus_to_remove( $submenus, $remove_submenus, $invert ) {
		if( is_array( $remove_submenus ) && sizeof( $remove_submenus ) > 0 ) {
			$remove =  $invert ? array_diff( $submenus, $remove_submenus ) : $remove_submenus;
		} elseif( is_string( $remove_submenus ) ) {
			$remove = $invert ? array_diff( $submenus, [$remove_submenus] ) : [$remove_submenus];
		} else {
			$remove = $submenus;
		}
		return $remove;
	}

	/**
	 * Redirect the removed menu/submenu page.
	 *
	 * @param string $slug The menu/submenu slug to redirect.
	 * @param string $url The destination URL.
	 * 
	 * @return void
	 * 
	 * @since 1.0
	 * 
	 * @access public
	 */
	private function redirect( string $slug, string $url ) {
		$url = $url ? $url : admin_url( "admin.php?page={$this->menu_slug}" );
		$uri    = $_SERVER[ 'REQUEST_URI' ];
		
		if ( strpos( $uri, $slug ) !== false ) {
			// $url = add_query_arg('http_referrer', urlencode( remove_query_arg( wp_removable_query_args(), wp_unslash( $_SERVER['REQUEST_URI'] ) ) ), "admin.php?page={$this->menu_slug}" );
			wp_redirect(
				// htmlspecialchars_decode( // FIXME: decoding doesn't work
					esc_url( add_query_arg (
						[
							'http_referrer'	=> rawurlencode_deep( $uri ),
							'redirect'      => true
						], $url
					) )
				// )
			); exit;
		}
		return false;
	}

	/**
	 * Get all submenus from menu name/menu slug.
	 *
	 * @param string $menu_name     Name of the menu to get submenus.
	 * @param string $filter        An array of submenus data, if not supplied.\
	 * - @type `string` **title**. The submenu title.
	 * - @type `string` **cap**. The submenu user capability.
	 * - @type `string` **slug**. The submenu slug.
	 *                              Defaults to submenu array.
	 * @param bool $is_slug         Whether parameter `$menu_name` is given as slug.
	 *                              Defaults to `false`.
	 * 
	 * @return array|false          An array of submenu data, false otherwise.
	 * 
	 * @global array $submenu
	 * 
	 * @since 1.0
	 * 
	 * @access public
	 */
	public function get_submenus( string $menu_name, string $filter = '', bool $is_slug = false ) {
		global $submenu;

		// Bail early if menu doesn't have any submenu.
		if( ! $this->has_submenu( $menu_name, $is_slug ) ) {
			return false;
		}

		// Get main menu slug.
		$menu_slug  = $is_slug ? $menu_name : $this->get_slug_by( $menu_name );

		// Bail if no submenus assigned to the main menu.
		if( ! is_string( $menu_slug ) ) return false;

		$sub_menus  = $submenu[$menu_slug];

		// Get submenu data by type.
		switch( $filter ) {
			case 'title': $index = 0; break;
			case 'cap' : $index = 1; break;
			case 'slug' : $index = 2; break;
			default : $index = $sub_menus;
		}
		return is_array( $index ) ? $index : array_column( $sub_menus, $index );
	}

	/**
	 * Get order index of a menu's submenu array.
	 * 
	 * The index determines the position of submenu relative to other submenus of a menu.
	 *
	 * @param string $menu_name        The menu name to check for.
	 * @param string $submenu_name     The submenu name to check for.
	 * @param bool $is_slug            Whether parameter `$menu_name` and `$submenu_name`
	 *                                 is given as slug. Defaults to `false`.
	 * 
	 * @return int|false               The submenu array index/key if found, false otherwise.
	 * 
	 * @global array $submenu
	 * 
	 * @since 1.0
	 * 
	 * @access public
	 */
	public function get_submenu_order_index( string $menu_name, string $submenu_name, bool $is_slug = false ) {
		global $submenu;

		// Bail early if menu doesn't have any submenu.
		if( ! $this->has_submenu( $menu_name, $is_slug ) ) {
			return false;
		}

		$menu_slug      = $is_slug ? $menu_name : $this->get_slug_by($menu_name);
		$all_submenus   = $submenu[$menu_slug];

		// Iterate over all submenus and get index/key.
		foreach( $all_submenus as $key => $value ) {
			if( in_array( $submenu_name, $value, true ) ) {
				return $key;
			}
		}
		return false;
	}

	/**
	 * Get the submenu data.
	 * 
	 * #### NOTE: Use inside `hzfex_menu_loaded` hook.
	 * 
	 * @param string $submenu_slug            The submenu slug for which data to retrieve.
	 * @param string $filter                  An array of submenu data, if not supplied.\
	 * Accepted values are:
	 * - @type `string` **key**  - The index of submenu in parent menu array.
	 * - @type `string` **cap**  - The user capability to access submenu.
	 * - @type `string` **menu** - The parent menu slug where submenu belongs.
	 * 
	 * @return int|string|array|false         The submenu data, false otherwise.
	 * - @type `int` if _$filter_ is `key`.
	 * - @type `string` if _$filter_ is `cap`.
	 * - @type `string` if _$filter_ is `menu`.
	 * - @type `array` if _$filter_ not given.
	 * 
	 * @global array $submenu
	 * 
	 * @since 1.0
	 * 
	 * @access public
	 */
	public function get_submenu_data( string $submenu_slug, string $filter = null ) {
		global $submenu;

		// Iterate over all submenus and fetch submenu data.
		foreach( $submenu as $menu => $data ) {
			foreach( $data as $key => $value ) {
				if( in_array( $submenu_slug, $value, true ) ) {
					if( $filter === 'key' ) {
						return (int) $key;
					} elseif( $filter === 'cap' ) {
						return (string) $value[1];
					} elseif( $filter === 'menu' ) {
						return (string) $menu;
					} else {
						return (array) $value;
					}
				}
			}
		}
		return false;
	}

	/**
	 * Check if menu has submenu.
	 *
	 * @param string $menu_name The menu name to check for.
	 * @param bool $is_slug     Whether parameter `$menu_name` is given as slug.
	 *                          Defaults to `false`.
	 * 
	 * @return bool             True if menu has submenus, false otherwise.
	 * 
	 * @global array $submenu
	 * 
	 * @since 1.0
	 * 
	 * @access public
	 */
	public function has_submenu( string $menu_name, bool $is_slug = false ): bool {
		global $submenu;

		// Bail early if submenu isn't set.
		if( ! is_array( $submenu ) ) return false;

		// Get menu slug.
		$menu = $is_slug ? $menu_name : $this->get_slug_by( $menu_name );

		// Bail if not a valid menu slug.
		if( ! is_string( $menu ) ) return false;
		return array_key_exists( $menu, $submenu );
	}

	/**
	 * Checks if main menu exists.
	 *
	 * @param string $menu_name    The menu name to check for.
	 * @param bool $is_slug        Whether parameter `$menu_name` is given as slug.
	 *                             Defaults to `false`.
	 * 
	 * @return bool                True if exist, false otherwise.
	 * 
	 * @global array $menu
	 * 
	 * @since 1.0
	 * 
	 * @access public
	 */
	public function exists( string $menu_name, bool $is_slug = false ): bool {
		global $menu;

		// Bail early if menu isn't set.
		if( ! is_array( $menu ) ) return false;

		$slug = $is_slug ? $menu_name : $this->get_slug_by( $menu_name );

		// Get menu slugs from array.
		$menu_slugs = array_column( $menu, 2 );
		return in_array( $slug, $menu_slugs, true );
	}

	/**
	 * Gets menu slug by menu name.
	 *
	 * @param string $menu_name The menu name for which slug to get.
	 * 
	 * @return string|false Menu slug if found, false otherwise.
	 * 
	 * @global array $menu
	 * 
	 * @since 1.0
	 * 
	 * @access public
	 */
	public function get_slug_by( string $menu_name ) {
		global $menu;

		// Bail early if menus don't exist.
		if( ! is_array( $menu ) ) return false;

		// Iterate over menus and check if given menu name exists.
		$found = array_filter( $menu, function($val) use ($menu_name) {
			return $menu_name === $val[0];
		} );

		// Check if found only one menu with given name, and
		// Get the slug of that menu. 
		$found = sizeof( $found ) === 1 ? array_shift( $found ) : false;
		return ! $found ? $found : $found[2];
	}

	/**
	 * Gets menu order index.
	 *
	 * @param string $menu_name   The menu name for which index to get.
	 * @param bool $is_slug       Whether parameter `$menu_name` is given as slug.
	 *                            Defaults to `false`.
	 * 
	 * @return int/false          The menu array Index/Key if found, false otherwise.
	 * 
	 * @global array $menu
	 * 
	 * @since 1.0
	 * 
	 * @access public
	 */
	public function get_order_index( string $menu_name, bool $is_slug = false ) {
		global $menu;

		// Bail early if menu isn't set.
		if( ! is_array( $menu ) ) return false;

		// Iterate over menus and check if given menu name exists.
		$found = array_filter( $menu, function($val) use ($menu_name, $is_slug) {
			if( ! $is_slug ) {
				return $menu_name === $val[0];
			} else {
				return $menu_name === $val[2];
			}
		} );

		// Check if found only one menu with given name, and
		// Get the index of that menu. 
		$found = sizeof( $found ) === 1 ? array_keys( $found ): false;
		return ! $found ? $found : $found[0];
	}
}