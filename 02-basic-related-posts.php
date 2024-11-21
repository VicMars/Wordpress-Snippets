/*
* Wordpress FSE
* Basic Related Posts: Shortcode +  Dynamic Content
*/


<?php
/* Shortcode to display Related posts anywhere on single post
* --------------------------------------------------------------------- */
function custom_shortcode_related_posts($atts) {
  /* Shortcode: [related-posts] */

    global $post;
    if (is_single()) {
      $categories = get_the_category();
      $current_category_ids = array();

      foreach ( $categories as $category ) {
        $current_category_ids[] = $category->term_id;
      }

      $shortcode_args = shortcode_atts(array(
        'post_type' => 'post',
        'num'     => '3',
        'order'   => 'DESC',
        'orderby' => 'rand',
      ), $atts);
  
      $args = array(
        'post_type' => 'post',
        'category__in' => $current_category_ids,
        'posts_per_page' => $shortcode_args['num'],
        'order'          => $shortcode_args['order'],
        'post__not_in' => array( get_the_ID() ),
      );

      $related_posts_query = new WP_Query( $args );
  
      ob_start();
  
      if( $related_posts_query->have_posts() ) { ?>
      <div id="related-posts" style="display:block;position:relative;left:0;right:0;width:100%;max-width:100vw;margin:20px 0;">
          <h3 style="margin:0 0 10px 0;"><?php echo __('You may also like', 'textdomain'); ?></h3>
          <ul class="related-posts-list" style="display:flex;justify-content:space-between;margin:0;padding:0;gap:20px;list-style:none;">
            <?php while( $related_posts_query->have_posts() ) {
            $related_posts_query->the_post(); ?>
            <li class="related-post">
              <a href="<?php echo esc_url( get_the_permalink() ); ?>" style="text-decoration:none;"><?php echo get_the_post_thumbnail( $post->ID, 'large', array('style' => 'width:100%;max-height:250px;object-fit:cover;margin-bottom:10px;aspect-ratio: 2 / 1;') ); ?><?php echo esc_html( get_the_title() ); ?>
              </a>
            </li>
            <?php
            }?>
          </ul>
      </div>
      <?php
    }
  
    $html = ob_get_contents();
    ob_end_clean();
  
    wp_reset_postdata();
  
    return $html;
  }

}
add_shortcode( 'related-posts', 'custom_shortcode_related_posts' );




/* Display Related posts at the end of single post content
* ---------------------------------------------------------------- */
function custom_related_posts($content) {
    global $post;

    if (is_single()) {

        $args = array(
            'post_type' => get_post_type($post),
            'posts_per_page' => 3,
            'post__not_in' => array($post->ID),
            'orderby' => 'rand',
            'tax_query' => array(
                array(
                    'taxonomy' => 'category',
                    'field'    => 'term_id',
                    'terms'    => wp_get_post_terms($post->ID, 'category', array("fields" => "ids")),
                ),
            ),
        );

        $related_posts = new WP_Query($args);

        if ($related_posts->have_posts()) {
            $content .= '<div id="related-posts">' . '<h3>' . esc_html__('You may also like', 'textdomain') . '</h3><div class="related-posts-list"><ul>';
            while ($related_posts->have_posts()) {
                $related_posts->the_post();
                $content .= '<li class="related-post"><a href="' . get_the_permalink() . '">' . get_the_post_thumbnail() . get_the_title() . '</a></li>';
            }
            $content .= '</ul></div></div>';
            wp_reset_postdata();
        }

        echo '<style>
                #related-posts {
                  display:block;
                  position:relative;
                  left:0;
                  right:0;
                  width:100%;
                  max-width:100vw;
                  margin-top:50px;
                }

                #related-posts h3 {
                   margin-bottom:10px;
                }

                #related-posts ul {
                  display:flex;
                  justify-content:space-between;
                  margin:0;
                  padding:0;
                  gap:20px;
                }

                #related-posts ul li {
                   display:block;
                }

                #related-posts ul li a {
                  text-decoration:none;
                }

                #related-posts ul li .wp-post-image {
                  width: 100%;
                  max-height: 250px;
                  object-fit: cover;
                  margin-bottom: 10px;
                  aspect-ratio: 2 / 1;
                }
              </style>';
    }

    return $content;
}
add_filter('the_content', 'custom_related_posts');
