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
    
    $args = ['post_type' => 'event', 'posts_per_page' => $posts_per_page, 'post_status' => 'publish', 'paged' => $paged, 'ignore_sticky_posts' => true];
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
        
        /*111 */
        while ($query->have_posts()) {
            $query->the_post();
            
        
            $event_id = get_the_ID();
            $location = get_post_meta($event_id, 'event_location', true);
            $start_date = get_post_meta($event_id, 'event_start_date', true);
            $end_date = get_post_meta($event_id, 'event_end_date', true); // ডেমোর মতো করে শেষ হওয়ার তারিখ আনা হয়েছে
            $ticket_id = get_post_meta($event_id, 'ticket_id', true);

            /*Event Poster render */
            $image_url = '';

            // প্রথমে চেক করা হচ্ছে Pods ফ্রেমওয়ার্ক চালু আছে কিনা
            if (function_exists('pods')) {
                // 'event' পডের অবজেক্ট তৈরি করা হচ্ছে
                $pod = pods('event', $event_id);
                if ($pod->exists()) {
                    // সঠিক ফিল্ডের নাম 'event_poster' থেকে ছবির সব তথ্য আনা হচ্ছে
                    $poster_field_data = $pod->field('event_poster');
                    
                    // যদি ফিল্ডে ডেটা থাকে এবং ছবির লিঙ্ক (guid) পাওয়া যায়
                    if (!empty($poster_field_data) && isset($poster_field_data['guid'])) {
                        $image_url = $poster_field_data['guid'];
                    }
                }
            }
            
            // যদি কোনো কারণে ছবি না পাওয়া যায়, তাহলে একটি ফলব্যাক ইমেজ দেখানো হবে
            if (empty($image_url)) {
                 $image_url = 'https://via.placeholder.com/450x300.png?text=Event+Poster';
            }
            
            // তারিখ ফরম্যাট করা
            $formatted_date = '';
            if ($start_date) {
                $formatted_date = date("D, d M Y", strtotime($start_date));
            }
            if ($end_date) {
                // ডেমোর মতো করে তারিখের ফরম্যাট
                $formatted_date .= ' - ' . date("D, d M Y", strtotime($end_date));
            }
            ?>

            <div class="etn-event-item">
                <div class="etn-event-thumb">
                    <a href="<?php the_permalink(); ?>">
                        <img src="<?php echo esc_url($image_url); ?>" alt="<?php the_title_attribute(); ?>">
                    </a>
                </div>
                <div class="etn-event-content">
                    <h3 class="etn-event-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                    <div class="etn-event-meta">
                        <div class="etn-meta-info">
                            <i class="fas fa-calendar-alt"></i> <?php echo esc_html($formatted_date); ?>
                        </div>
                         <?php if ($location): ?>
                        <div class="etn-meta-info">
                            <i class="fas fa-map-marker-alt"></i> <?php echo esc_html($location); ?>
                        </div>
                        <?php endif; ?>
                    </div>
                     <div class="etn-event-footer">
                        <div class="etn-organizer">
                            <span class="organizer-label">Organized By</span>
                            <span class="organizer-name">Please Set Vendor</span>
                        </div>
                        <div class="etn-buy-btn">
                            <?php if (!empty($ticket_id)) : ?>
                                <a href="/?add-to-cart=<?php echo esc_attr($ticket_id); ?>" class="etn-btn">BUY NOW</a>
                            <?php else: ?>
                                <a href="#" class="etn-btn disabled" onclick="return false;">BUY NOW</a>
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