01/*
* Wordpress FSE
* Navigation Block: add 'current' class to menu item
*/

<?php

/* Add 'current-menu-item' class to category or taxonomy link in Navigation Block */

function add_current_class_to_taxonomy_nav($block_content, $block) {

    // Only modify navigation items on taxonomy or category archive pages
    if ((is_category() || is_tax()) && is_main_query()) {

        // Get the current taxonomy term information
        $current_term = get_queried_object();

        if ($current_term && isset($current_term->term_id) && isset($current_term->taxonomy)) {

            // Get the current term link for comparison
            $current_term_link = untrailingslashit(get_term_link($current_term->term_id, $current_term->taxonomy));

            // Target only navigation block items
            if (isset($block['blockName']) && $block['blockName'] === 'core/navigation') {
                $block_content = preg_replace_callback(
                    '/(<li[^>]*class="[^"]*wp-block-navigation-item[^"]*"[^>]*>.*?<a[^>]+href=["\']([^"\']+)["\'][^>]*>)(.*?)(<\/a>.*?<\/li>)/is',
                    function($matches) use ($current_term_link) {
                        // Check if the link matches the current term URL
                        if (untrailingslashit($matches[2]) === $current_term_link) {
                            // Add 'current-menu-item' to the <li> class attribute
                            $li_opening_tag = preg_replace('/class="([^"]*)"/', 'class="$1 current-menu-item"', $matches[1]);
                            return $li_opening_tag . $matches[3] . $matches[4];
                        }
                        return $matches[0];
                    },
                    $block_content
                );
            }
        }
    }
    return $block_content;
}
add_filter('render_block', 'add_current_class_to_taxonomy_nav', 10, 2);
