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
 * TheWebSolver\Core\Menu\Admin_Bar::init();
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
 * Add admin bar menu (node) or group.
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
 * @return void
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
) {
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

    // Add admin bar menu args.
    admin_bar()->add( $menu );
};