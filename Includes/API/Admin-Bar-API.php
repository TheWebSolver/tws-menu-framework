<?php
/**
 * Admin Bar: WordPress Toolbar API functions.
 * 
 * @package TheWebSolver\Core\Menu_Framework\Admin_Bar\Functions\API
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

use TheWebSolver\Core\Menu\Admin_Bar;

/**
 * Admin Bar instance.
 * 
 * Alias for:
 * ```
 * \TheWebSolver\Core\Menu\Admin_Bar::init();
 * ```
 * 
 * @return Admin_Bar
 * 
 * @since 1.1
 */
function admin_bar(): Admin_Bar {
    return Admin_Bar::init();
}

/**
 * Adds admin bar menu (node) or group.
 * 
 * Alias for:
 * ```
 * admin_bar()->add();
 * ```
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
 * * @type `mixed` **tabindex**   Numeric value. Whether admin bar is tabbable using keyboard tab key.
 * 
 * @return true
 * 
 * @since 1.1
 */
function add_admin_bar(
    string $id,
    string $title,
    string $href,
    bool $group = false,
    bool $parent = false,
    array $meta = []
): bool {
    return admin_bar()->add( $id, $title, $href, $group, $parent, $meta );
};

/**
 * Removes admin bar menus (nodes).
 * 
 * Alias for:
 * ```
 * admin_bar()->remove();
 * ```
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
 */
function remove_admin_bar (
    $menu_ids = null,
    bool $invert = false,
    bool $sitename = false,
    bool $myaccount = false
): bool {
    return admin_bar()->remove( $menu_ids, $invert, $sitename, $myaccount );
}

/**
 * Adds admin bar menu (node) IDs for exclusion.
 * 
 * Alias for:
 * ```
 * admin_bar()->exclude();
 * ```
 * ### NOTE: -
 * ### Use before {@see `remove_admin_bar()`} so excluded IDs are set first.
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
 */
function exclude_admin_bar( $ids ): bool {
    return admin_bar()->exclude( $ids );
}

/**
 * Gets admin bar menu (node) that has parent set.
 * 
 * Alias for:
 * ```
 * admin_bar()->get_node_with_parent();
 * ```
 *
 * @param string $node_id The admin bar menu (node) ID.
 * 
 * @return array Node data that has valid parent, empty array otherwise.
 * 
 * @since 1.1
 */
function get_admin_bar_node_with_parent( string $node_id ): array {
    return admin_bar()->get_node_with_parent( $node_id );
}