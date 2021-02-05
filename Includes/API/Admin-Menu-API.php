<?php
/**
 * Admin Menu API functions.
 * 
 * NOTE: - 
 * When using the API functions, the order of using the function
 * should be taken into consideration. The result of one can directly
 * impact the result of another function used after it.
 * 
 * For example:
 * "remove_admin_menu('index.php')" is used first, and
 * "menu_exists('index.php')" after above function.
 * This function will always return false because
 * the first function already removes "Dashboard" menu.
 * 
 * @package TheWebSolver\Core\Menu_Framework\Admin_Menu\Functions\API
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

namespace TheWebSolver\Core;

use TheWebSolver\Core\Menu\Admin_Menu;

/**
 * Admin Menu instance.
 * 
 * Alias for:
 * ```
 * \TheWebSolver\Core\Menu\Admin_Menu::init();
 * ```
 *
 * @return Admin_Menu
 * 
 * @since 1.0
 */
function admin_menu(): Admin_Menu {
    return Admin_Menu::init();
}

/**
 * Reorder admin menus.
 * 
 * Alias for:
 * ```
 * admin_menu()->order_by();
 * ```
 * #### NOTE: Use inside `hzfex_menu_loaded` hook.
 *
 * @param string[] $menu_names   The menu names or menu slugs in an array to be sorted.
 *                               The position is determined relative to each other.
 * @param bool $is_slug          Whether param `$menu_names` are slugs.
 *                               Defaults to `false`.
 * @return void
 * 
 * @example usage- 
 * ```
 * use function TheWebSolver\Core\order_admin_menu_by;
 * 
 * // Users will be displayed at TOP and posts at BOTTOM in following sequence.
 * order_admin_menu_by(['Users','Appearance','Media','Pages','Posts']);
 * // Alternatively, use like this, if slug.
 * order_admin_menu_by(
 *  ['users.php','themes.php','upload.php','edit.php?post_type=page','edit.php'],
 *  true
 * );
 * ```
 * 
 * @since 1.0
 */
function order_admin_menu_by( array $menu_names, bool $is_slug = false ) {
    return admin_menu()->order_by( $menu_names, $is_slug );
}

/**
 * Change menu title and icon.
 * 
 * Alias for:
 * ```
 * admin_menu()->change();
 * ```
 * #### NOTE: Use inside `hzfex_menu_loaded` hook.
 *
 * @param string $old_name   The menu name for which to change title.
 * @param string $new_name   The new menu name.
 * @param string $new_icon   The new menu icon.
 * @param boolean $is_slug   Whether parameter `$menu_name` is given as slug.
 *                           Defaults to `false`.
 * 
 * @return mixed             The changed menu name and icon, false otherwise.
 * 
 * @since 1.0
 */
function change_menu_name_icon( string $old_name, string $new_name, string $new_icon = '', bool $is_slug = false ) {
    return admin_menu()->change( $old_name, $new_name, $new_icon, $is_slug );
}

/**
 * Remove Menu/Submenu and redirect.
 * 
 * Alias for:
 * ```
 * admin_menu()->remove();
 * ```
 * #### NOTE: Use inside `hzfex_menu_loaded` hook.
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
 */
function remove_admin_menu(
    $menu_name,
    bool $remove_menu = true,
    $remove_submenus = true,
    bool $invert = false,
    bool $is_slug = false,
    string $capability = 'jpt',
    bool $redirect = false,
    string $redirect_url = ''
) {
    return admin_menu()->remove( $menu_name, $remove_menu, $remove_submenus, $invert, $is_slug, $capability, $redirect, $redirect_url );
}

/**
 * Removes WordPress Admin dashboard.
 * 
 * Alias for:
 * ```
 * admin_menu()->remove_dashboard();
 * ```
 * #### NOTE: Use inside `hzfex_menu_loaded` hook.
 *
 * @param string $capability                      Current user capability to ignore.
 *                                                Defaults to `manage_options`.
 * @param string|string[]|bool $remove_submenus   Whether to remove submenus of the given menu.
 *                                                **true** removes all, **false** removes none,
 *                                                **string** removes single, **array** removes those in array.
 *                                                Defaults to `true`.
 * @param bool $invert                            Whether to invert `$remove_submenus` value.
 *                                                Inverting will remove submenus except `$remove_submenus`.\
 *                                                Defaults to `false`.
 * @param bool $redirect                          Whether to redirect removed menus/submenus.
 *                                                Defaults to `false`.
 * 
 * @return void|false                             Remove dashboad and its submenus, false otherwise.
 * 
 * @since 1.0
 */
function remove_dashboard_menu( string $capability = 'manage_options', $remove_submenus = true, bool $invert = false, bool $redirect = false ) {
    return admin_menu()->remove_dashboard( $capability, $remove_submenus, $invert, $redirect );
}

/**
 * Removes WordPress apperance menu.
 * 
 * Alias for:
 * ```
 * admin_menu()->remove_appearance();
 * ```
 * #### NOTE: Use inside `hzfex_menu_loaded` hook.
 *
 * @param string $capability                      Current user capability to ignore.
 *                                                Defaults to `manage_options`.
 * @param string|string[]|bool $remove_submenus   Whether to remove appearance submenus.
 *                                                **true** removes all, **false** removes none,
 *                                                **string** removes single submenu,
 *                                                **array** removes submenus in that array.
 *                                                Defaults to `customize.php`.
 * @param bool $invert                            Whether to invert `$remove_submenus` value.
 *                                                Inverting will remove submenus except `$remove_submenus`.
 *                                                Defaults to `true`. i.e. won't remove **Customizer**.
 * @param bool $redirect                          Whether to redirect removed menus/submenus.
 *                                                Defaults to `false`.
 * 
 * @return void|false                             Remove appearance menu/submenu, false otherwise.
 * 
 * @since 1.0
 */
function remove_appearance_menu(  string $capability = 'manage_options', $remove_submenus = 'customize.php', bool $invert = true, bool $redirect = false ) {
    return admin_menu()->remove_appearance( $capability, $remove_submenus, $invert, $redirect );
}

/**
 * Get all submenus from menu name/menu slug.
 * 
 * Alias for:
 * ```
 * admin_menu()->get_submenus();
 * ```
 * #### NOTE: Use inside `hzfex_menu_loaded` hook.
 *
 * @param string $menu_name     Name of the menu to get submenus.
 * @param string $filter        An array of submenus data, if not supplied.\
 * Accepted values are:
 * - @type `string` **title**. The submenu title.
 * - @type `string` **cap**. The submenu user capability.
 * - @type `string` **slug**. The submenu slug.
 * @param bool $is_slug         Whether parameter `$menu_name` is given as slug.
 *                              Defaults to `false`.
 * 
 * @return array|false          An array of submenu data, false otherwise.
 * 
 * @since 1.0
 */
function get_submenus_by( string $menu_name, string $filter = '', bool $is_slug = false ) {
    return admin_menu()->get_submenus( $menu_name, $filter, $is_slug );
}

/**
 * Get order index of a menu's submenu array.
 * 
 * Alias for:
 * ```
 * admin_menu()->get_submenu_order_index();
 * ```
 * #### NOTE: Use inside `hzfex_menu_loaded` hook.
 * The index determines the position of submenu relative to other submenus of a menu.
 *
 * @param string $menu_name        The menu name to check for.
 * @param string $submenu_name     The submenu name to check for.
 * @param bool $is_slug            Whether parameter `$menu_name` and `$submenu_name`
 *                                 is given as slug. Defaults to `false`.
 * 
 * @return int|false               The submenu array index/key if found, false otherwise.
 * 
 * @since 1.0
 */
function get_submenu_index_by( string $menu_name, string $submenu_name, bool $is_slug = false ) {
    return admin_menu()->get_submenu_order_index( $menu_name, $submenu_name, $is_slug );
}

/**
 * Get the submenu data.
 * 
 * Alias for:
 * ```
 * admin_menu()->get_submenu_data();
 * ```
 * #### NOTE: Use inside `hzfex_menu_loaded` hook.
 * 
 * @param string $submenu_slug            The submenu slug for which data to retrieve.
 * @param string $filter                  An array of submenu data, if not supplied.\
 * Accepted values are:
 * - @type `string` **key**  - Gets `int` The index of submenu in parent menu array.
 * - @type `string` **cap**  - Gets `string` The user capability to access submenu.
 * - @type `string` **menu** - Gets `string` The parent menu slug where submenu belongs.
 * 
 * @return int|string|array|false         The submenu data, false otherwise.
 * - @type `int` if _$filter_ is `key`.
 * - @type `string` if _$filter_ is `cap`.
 * - @type `string` if _$filter_ is `menu`.
 * - @type `array` if _$filter_ not given.
 * 
 * @since 1.0
 */
function get_submenu_data( string $submenu_slug, string $filter = '' ) {
    return admin_menu()->get_submenu_data( $submenu_slug, $filter );
}

/**
 * Check if menu has submenu.
 * 
 * Alias for:
 * ```
 * admin_menu()->has_submenu();
 * ```
 * #### NOTE: Use inside `hzfex_menu_loaded` hook.
 *
 * @param string $menu_name The menu name to check for.
 * @param bool $is_slug     Whether parameter `$menu_name` is given as slug.
 *                          Defaults to `false`.
 * 
 * @return bool             True if menu has submenus, false otherwise.
 * 
 * @since 1.0
 */
function menu_has_submenu( string $menu_name, bool $is_slug = false ): bool {
    return admin_menu()->has_submenu( $menu_name, $is_slug );
}

/**
 * Checks if main menu exists.
 * 
 * Alias for:
 * ```
 * admin_menu()->exists();
 * ```
 * #### NOTE: Use inside `hzfex_menu_loaded` hook.
 *
 * @param string $menu_name    The menu name to check for.
 * @param bool $is_slug        Whether parameter `$menu_name` is given as slug.
 *                             Defaults to `false`.
 * 
 * @return bool                True if exist, false otherwise.
 * 
 * @since 1.0
 */
function menu_exists( string $menu_name, bool $is_slug = false ): bool {
    return admin_menu()->exists( $menu_name, $is_slug );
}

/**
 * Gets menu slug by menu name.
 * 
 * Alias for:
 * ```
 * admin_menu()->get_slug_by();
 * ```
 * #### NOTE: Use inside `hzfex_menu_loaded` hook.
 *
 * @param string $menu_name The menu name for which slug to get.
 * 
 * @return string|false Menu slug if found, false otherwise.
 * 
 * @since 1.0
 */
function get_menu_slug_by( string $menu_name ) {
    return admin_menu()->get_slug_by( $menu_name );
}

/**
 * Gets menu order index.
 * 
 * Alias for:
 * ```
 * admin_menu()->get_order_index();
 * ```
 * #### NOTE: Use inside `admin_init` hook to get index correctly even after reorder {@see `order_admin_menu_by`}.
 *
 * @param string $menu_name   The menu name for which index to get.
 * @param bool $is_slug       Whether parameter `$menu_name` is given as slug.
 *                            Defaults to `false`.
 * 
 * @return int/false          The menu array Index/Key if found, false otherwise.
 * 
 * @since 1.0
 */
function get_menu_order_index( string $menu_name, bool $is_slug = false ) {
    return admin_menu()->get_order_index( $menu_name, $is_slug );
}