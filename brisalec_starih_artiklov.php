<?php
/**
 * Plugin Name: Brisi_stare_artikle
 * Description: Briše artikle starejše od 60 dni.
 * Version: 1.1
 * Author: PhartBox
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('woocommerce_init', 'brisi_stare_artikle');

function brisi_stare_artikle() {
    // Laufaj samo iz admina da ne bo obremenilo serverja preveč
    if (!is_admin()) {
        return;
    }

    $args = array(
        'post_type'      => 'product',
        'posts_per_page' => -1,
        'meta_query'     => array(
            array(
                'key'   => '_stock',
                'value' => 0,
            ),
        ),
    );

    $products = get_posts($args);

    foreach ($products as $product) {
        $last_order_date = get_last_purchase_date($product->ID);

        if ($last_order_date && (current_time('timestamp') - strtotime($last_order_date)) > 60 * DAY_IN_SECONDS) {
            // Briši produktne slike
            $attachments = get_attached_media('image', $product->ID);
            foreach ($attachments as $attachment) {
                wp_delete_attachment($attachment->ID, true);
            }

            // Briši ID artikla
            wp_delete_post($product->ID, true);
        }
    }
}

function get_last_purchase_date($product_id) {
    global $wpdb;

    $query = "
        SELECT posts.post_date
        FROM {$wpdb->prefix}posts AS posts
        INNER JOIN {$wpdb->prefix}woocommerce_order_items AS items ON posts.ID = items.order_id
        INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS itemmeta ON items.order_item_id = itemmeta.order_item_id
        WHERE itemmeta.meta_key = '_product_id' AND itemmeta.meta_value = %d
        ORDER BY posts.post_date DESC
        LIMIT 1
    ";

    return $wpdb->get_var($wpdb->prepare($query, $product_id));
}

?>