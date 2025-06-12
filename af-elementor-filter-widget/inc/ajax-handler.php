<?php
// inc/ajax-handler.php

if (!defined('WPINC')) die;

// Enqueue Scripts and Styles function remains the same...
function af_elementor_widget_assets() {
    // Font Awesome-এর জন্য এনকিউ, যদি আপনার থিমে আগে থেকে না থাকে
    wp_enqueue_style('font-awesome-5', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css', [], '5.15.4');
    
    wp_enqueue_style('af-filter-styles', plugin_dir_url(__FILE__) . '../assets/css/filter-styles.css', [], '1.3.0');
    wp_enqueue_script('af-main-ajax-filter', plugin_dir_url(__FILE__) . '../assets/js/main-ajax-filter.js', ['jquery'], '1.3.0', true);
    wp_localize_script('af-main-ajax-filter', 'af_ajax_params', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('af_complex_filter_nonce')
    ]);
}
add_action('wp_enqueue_scripts', 'af_elementor_widget_assets');


// The AJAX Handler 
function af_complex_filter_ajax_handler() {
    check_ajax_referer('af_complex_filter_nonce', 'security');
    
    $paged = isset($_POST['paged']) ? absint($_POST['paged']) : 1;
    $posts_per_page = isset($_POST['posts_per_page']) ? absint($_POST['posts_per_page']) : 9;
    $today = date('Y-m-d');
    
    $args = ['post_type' => 'event', 'posts_per_page' => $posts_per_page, 'post_status' => 'publish', 'paged' => $paged];
    $meta_query = ['relation' => 'AND'];

    if (isset($_POST['event_status']) && !empty($_POST['event_status'])) {
        $status = sanitize_text_field($_POST['event_status']);
        switch ($status) {
            case 'upcoming': $meta_query[] = ['key' => 'event_start_date', 'value' => $today, 'compare' => '>=']; break;
            case 'past': $meta_query[] = ['key' => 'event_start_date', 'value' => $today, 'compare' => '<']; break;
            case 'ongoing': 
                $meta_query[] = ['key' => 'event_start_date', 'value' => $today, 'compare' => '<=']; 
                $meta_query[] = ['key' => 'event_end_date', 'value' => $today, 'compare' => '>=']; 
                break;
        }
    }
    
    if (isset($_POST['s']) && !empty($_POST['s'])) $args['s'] = sanitize_text_field($_POST['s']);
    if (isset($_POST['event_location']) && !empty($_POST['event_location'])) {
        $meta_query[] = ['key' => 'event_location', 'value' => sanitize_text_field($_POST['event_location']), 'compare' => 'LIKE'];
    }
    if (isset($_POST['event_tag']) && !empty($_POST['event_tag'])) {
        $meta_query[] = ['key' => 'event_tag', 'value' => sanitize_text_field($_POST['event_tag']), 'compare' => '='];
    }
    if (isset($_POST['event_date']) && !empty($_POST['event_date'])) {
        $meta_query[] = ['key' => 'event_start_date', 'value' => sanitize_text_field($_POST['event_date']), 'compare' => 'LIKE'];
    }

    $args['meta_key'] = 'event_start_date';
    $args['orderby'] = 'meta_value';
    $args['order'] = 'ASC';
    
    if (count($meta_query) > 1) $args['meta_query'] = $meta_query;

    $query = new WP_Query($args);

    ob_start();
    if ($query->have_posts()) {
        echo '<div class="af-event-grid">';
        
    
        while ($query->have_posts()) {
            $query->the_post();
            
       
            $event_id = get_the_ID();
            $location = get_post_meta($event_id, 'event_location', true);
            $start_date = get_post_meta($event_id, 'event_start_date', true);
            $event_time = get_post_meta($event_id, 'event_time', true);
            $poster_data = get_post_meta($event_id, '_pods_event_poster', true);
            $ticket_id = get_post_meta($event_id, 'ticket_id', true);

            $image_url = ''; 
            if (is_array($poster_data) && !empty($poster_data) && isset($poster_data[0][0]) && is_numeric($poster_data[0][0])) {
                $poster_id = $poster_data[0][0];
                $image_url = wp_get_attachment_image_url($poster_id, 'large');
            }
            if (empty($image_url)) {
                 $image_url = 'https://via.placeholder.com/450x300.png?text=Event+Poster';
            }
           
            $formatted_date = $start_date ? date("F j, Y", strtotime($start_date)) : '';
            $formatted_time = $event_time ? ' at ' . esc_html($event_time) : '';
            ?>
            <div class="etn-event-item">
                <div class="etn-event-thumb">
                    <a href="<?php the_permalink(); ?>">
                        <img src="<?php echo $image_url; ?>" alt="<?php the_title_attribute(); ?>">
                    </a>
                </div>
                <div class="etn-event-content">
                    <h3 class="etn-event-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                    <div class="etn-event-meta">
                        <div class="etn-meta-info">
                            <i class="fas fa-calendar-alt"></i> <?php echo esc_html($formatted_date . $formatted_time); ?>
                        </div>
                        <?php if ($location): ?>
                        <div class="etn-meta-info">
                            <i class="fas fa-map-marker-alt"></i> <?php echo esc_html($location); ?>
                        </div>
                        <?php endif; ?>
                        <div class="etn-meta-info">
                             <i class="fas fa-user"></i> Organized by Please set vendor
                        </div>
                    </div>
                     <div class="etn-event-footer">
                        <div class="etn-event-des">
                           <?php echo wp_trim_words( get_the_excerpt(), 15, '...' ); ?>
                        </div>
                        <div class="etn-buy-btn">
                            <?php if (!empty($ticket_id)) : ?>
                                <a href="/?add-to-cart=<?php echo esc_attr($ticket_id); ?>" class="etn-btn">Buy Now</a>
                            <?php else: ?>
                                <a href="#" class="etn-btn disabled" onclick="return false;">Not Available</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        }
     

        echo '</div>';
    } else {
        echo '<p>No events found matching your criteria.</p>';
    }
    $html_results = ob_get_clean();
    $big = 999999999;
    $pagination_links = paginate_links(['base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))), 'format' => '?paged=%#%', 'current' => max(1, $paged), 'total' => $query->max_num_pages, 'prev_text' => '‹', 'next_text' => '›']);
    wp_reset_postdata();

    wp_send_json_success(['html' => $html_results, 'count' => $query->found_posts, 'pagination' => $pagination_links]);
}
add_action('wp_ajax_complex_filter_events', 'af_complex_filter_ajax_handler');
add_action('wp_ajax_nopriv_complex_filter_events', 'af_complex_filter_ajax_handler');