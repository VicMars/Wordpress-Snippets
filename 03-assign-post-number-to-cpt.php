/* 
* Wordpress FSE
* Number posts: Assign a number to each custom post type according to Published Date
* Display number above post
*/

<?php
function assign_post_number_to_cpt($block_content, $block) {
    // Only run for the specific block type (post-template) and CPT archive
    if (isset($block['blockName']) && $block['blockName'] === 'core/post-template' && is_post_type_archive('my_cpt')) {
        
        // Fetch all posts for this CPT, ordered by publication date
        static $post_numbers = null;
        if ($post_numbers === null) {
            $args = [
                'post_type'      => 'my_cpt', // Replace with your custom post type slug
                'posts_per_page' => -1,
                'orderby'        => 'date',
                'order'          => 'ASC',
                'fields'         => 'ids', // Only fetch post IDs
            ];
            $all_posts = get_posts($args);

            if (!empty($all_posts)) {
                // Assign post numbers to posts, starting from 1
                $post_numbers = array_flip($all_posts); // Map post IDs to post numbers (1-based)
            } else {
                return $block_content; // Return original content if no posts
            }
        }

        // Modify block content to add post number to each <li> element with the specific class
        $block_content = preg_replace_callback(
            '/(<li[^>]*class="[^"]*post-(\d+)[^"]*"[^>]*>)/', // Match <li> elements with post-id-<post_id>
            function ($matches) use ($post_numbers) {
                $post_id = (int) $matches[2]; // Extract post ID from the class

                if (isset($post_numbers[$post_id])) {
                    // Assign a 1-based post number
                    $post_number = $post_numbers[$post_id] + 1; // Increment to get 1-based number
                    // Add post number as a div inside the <li> tag
                    return $matches[1] . '<div class="post-number">#' . esc_html($post_number) . '</div>'; 
                }

                return $matches[0]; // Return original match if no post number is found
            },
            $block_content
        );
    }

    return $block_content;
}

add_filter('render_block', 'assign_post_number_to_cpt', 10, 2);
