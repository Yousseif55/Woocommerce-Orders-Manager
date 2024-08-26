<?php

/*
Plugin Name: Orders Manager 

Description: Full orders management according to ready to ship,cover status, Tracking Printed Books.

Author: Yousseif Ahmed 

Version: 1.1
*/


function codecruz_orders_menu_page()
{
    add_menu_page(
        'Orders Manager',
        'Orders Manager',
        'manage_options',
        'orders-manager',
        'display_codecruz_order_details'
    );
}

function display_codecruz_order_details()
{
    // Retrieve orders
    $orders = wc_get_orders(array(
        'limit' => -1,
        'status' => array('processing', 'on-hold')
    ));
    // Loop through orders
    foreach ($orders as $order) {
        $order_id = $order->get_id();
        $order_edit_link = admin_url("post.php?post=$order_id&action=edit");
        $products = $order->get_items();
        $all_available = true; // Assume all products are available
        $all_ready_to_ship = get_post_meta($order_id, '_ready_to_ship', true);

        // Check product availability
        foreach ($products as $product) {
            $product_id = $product->get_product_id();
            $product_meta = get_post_meta($product_id, 'codecruz_cover', true);
            $product_object = wc_get_product($product_id);
            $printed_orders = get_post_meta($product_id, 'printed_orders', true);
            $fetched = explode(', ', $printed_orders);
            if ($product_meta !== 'Ready' ||  !in_array($order_id, $fetched) && $all_ready_to_ship == 0 && (!$product_object->is_in_stock() || !$product_object->managing_stock() || $product_object->is_on_backorder()) ) {
                $all_available = false; // At least one product is not available
                break; // No need to continue checking
            }
        }

        // Determine background class based on availability
        $order_id_class = $all_available ? 'order-id order-id-green' : 'order-id order-id-red';

        // Update ready to ship status if all products are available
        if ($all_available && $all_ready_to_ship == 0) {
            update_post_meta($order_id, '_ready_to_ship', 1);
        }

        // Output Order ID with background class
        echo "<h2 class='$order_id_class'>Order <a href='$order_edit_link' target='_blank'>#$order_id</a></h2>";
        // Display table headers
        echo "<div class='order-table'>";
        echo "<table>";
        echo "<tr><th>Available</th><th>Cover Needed</th><th>Printing Needed</th></tr>";

        // Loop through products
        foreach ($products as $product) {
            $product_id = $product->get_product_id();
            $product_meta = get_post_meta($product_id, 'codecruz_cover', true);
            $product_object = wc_get_product($product_id);
            $product_quantity = $product->get_quantity();
            $product_edit_link = admin_url("post.php?post=$product_id&action=edit");
            $printed_orders = get_post_meta($product_id, 'printed_orders', true);
            $fetched = explode(', ', $printed_orders);

            // Apply conditions
            if ($product_meta !== 'Ready') {

                echo "<tr><td></td><td class='cover-needed'><span class='product-name' data-product-id='{$product_id}'><a href='$product_edit_link' target='_blank'>{$product->get_name()} x$product_quantity </a></span><button style='margin-left:10px;' class='cover-to-available'>Done  &#x2713;</button></td><td></td></tr>";
            } elseif (!in_array($order_id, $fetched) && $all_ready_to_ship == 0 && (!$product_object->is_in_stock() || !$product_object->managing_stock() || $product_object->is_on_backorder())) {
                echo "<tr><td></td><td></td><td class='printing-needed'><span class='product-name' data-product-id='{$product_id}' data-order-id='$order_id'><a href='$product_edit_link' target='_blank'>{$product->get_name()} x$product_quantity </a></span><button style='margin-left:10px;' class='printing-to-available'>Done  &#x2713;</button></td></tr>";
            } else {
                echo "<tr><td class='available'><a href='$product_edit_link' target='_blank'>{$product->get_name()} x$product_quantity </a></td><td></td><td></td></tr>";
            }
        }

        echo "</table>";
        echo "</div>";
    }

}


function cover_to_available()
{
    $product_id = isset($_POST['product_id']) ? $_POST['product_id'] : 0;
    if ($product_id) {

        // Update product meta 'codecruz_cover' to 'Ready'
        update_post_meta($product_id, 'codecruz_cover', 'Ready');

        wp_send_json_success();
    } else {
        wp_send_json_error();
    }

    wp_die();
}


function printing_to_available()
{
    $product_id = isset($_POST['product_id']) ? $_POST['product_id'] : 0;
    $order_id = isset($_POST['order_id']) ? $_POST['order_id'] : 0;

    if ($product_id &&  $order_id) {

        // Get existing meta value or set as an empty string if it doesn't exist
        $existing_meta = get_post_meta($product_id, 'printed_orders', true) ?: '';

        // Check if the order ID already exists
        $order_ids = explode(', ', $existing_meta);

        if (!in_array($order_id, $order_ids)) {

            // Append the new order ID
            $new_meta = $existing_meta ? $existing_meta . ', ' .  $order_id :  $order_id;

            // Update product meta 'printed_orders' with new value
            update_post_meta($product_id, 'printed_orders', $new_meta);
        }

        wp_send_json_success();
    } else {
        wp_send_json_error();
    }

    wp_die();
}

function codecruz_printed_products_page()
{
    add_submenu_page(
        'orders-manager', // Parent slug
        'Printed Products', // Page title
        'Printed Products', // Menu title
        'manage_options',
        'printed-products',
        'display_printed_products'
    );
}



function display_printed_products()
{
    // Retrieve products with non-empty 'printed_orders' meta
    $products = new WP_Query(array(
        'post_type' => 'product',
        'meta_key' => 'printed_orders',
        'meta_compare' => 'EXISTS',
        'posts_per_page' => -1
    ));

    if ($products->have_posts()) {
        echo "<div class='wrap'>";
        echo "<h1>Printed Products</h1><hr>";

        $found_printed_products = false;

        while ($products->have_posts()) {
            $products->the_post();
            $product_id = get_the_ID();
            $product_name = get_the_title();
            $printed_orders = get_post_meta($product_id, 'printed_orders', true);

            if (!empty($printed_orders)) {
                $found_printed_products = true;

                echo "<div class='order-table'>";

                echo "<div class='order-header' style='display: flex; justify-content: space-between; align-items: center;'>
                      <h2 class='order-id' style='margin: 0;'>
                      <h2 style='display: inline-block; padding: 5px 10px; font-weight: bold; border-radius: 5px; font-size: 1.2em;'>Product Name: $product_name</h2>
                      <button class='clear-history' data-product-id='$product_id' style='cursor: pointer; background-color: #dc3545; color: #fff; border: none; padding: 5px 10px;'>Clear History</button>
                      </h2>
                      </div>";

                echo "<table class='wp-list-table widefat fixed striped'>";
                echo "<thead><tr><th>Order ID</th><th>Customer</th><th>Action</th></tr></thead><tbody>";

                $order_ids = explode(', ', $printed_orders);

                foreach ($order_ids as $order_id) {

                    $order = wc_get_order($order_id);

                    if ($order) {

                        $order_name = $order->get_billing_first_name() . ' ' . $order->get_billing_last_name();
                        $order_edit_link = admin_url("post.php?post=$order_id&action=edit");

                        echo "<tr><td><a href='$order_edit_link' target='_blank'>$order_id</a></td><td>$order_name</td><td><button class='delete-history' data-product-id='$product_id' data-order-id='$order_id'>Not Done</button></td></tr>";
                    }
                }

                echo "</tbody></table>";
                echo "</div>";
            }
        }

        if (!$found_printed_products) {
            echo "<div class='notice notice-info'><p>No printed products found.</p></div>";
        }

        echo "</div>";

        wp_reset_postdata();

    } else {
        echo "<div class='wrap'>";
        echo "<h1>Printed Products</h1>";
        echo "<div class='notice notice-info'><p>No printed products found.</p></div>";
        echo "</div>";
    }
            }

            function delete_order_from_product_meta()
            {
                $product_id = isset($_POST['product_id']) ? $_POST['product_id'] : 0;
                $order_id = isset($_POST['order_id']) ? $_POST['order_id'] : 0;

                if ($product_id && $order_id) {
                    $printed_orders = get_post_meta($product_id, 'printed_orders', true);
                    $order_ids = explode(', ', $printed_orders);

                    // Remove the order ID from the array
                    $order_ids = array_diff($order_ids, array($order_id));

                    // Update product meta 'printed_orders' with the modified array
                    update_post_meta($product_id, 'printed_orders', implode(', ', $order_ids));
                    
                    $order = wc_get_order($order_id);
                    $order_status = $order->get_status();

                if (in_array($order_status, array('processing', 'on-hold'))) {
                        // Get the ready to ship status
                        $ready_to_ship = get_post_meta($order_id, '_ready_to_ship', true);
        
                        // Update ready to ship status if true
                        if ($ready_to_ship) {
                            update_post_meta($order_id, '_ready_to_ship', 0);
                        }
                    }
                    wp_send_json_success();

                } 

                else {
                    wp_send_json_error();
                }

                wp_die();
            }

            function clear_history()
            {
                $product_id = isset($_POST['product_id']) ? $_POST['product_id'] : 0;

                if ($product_id) {
                    // Clear history for the product
                    update_post_meta($product_id, 'printed_orders', '');

                    wp_send_json_success();
                }
                 else {
                    wp_send_json_error();
                }

                wp_die();
            }
