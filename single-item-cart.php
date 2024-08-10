<?php
/*
Plugin Name: Custom Checkout Cart
Description: Displays the cart table on the checkout page and limits the cart to specific conditions.
Version: 1.3
Author: Rohit Kumar
Author URI: https://iamrohit.net/
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class Custom_Checkout_Cart {
    
    public function __construct() {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_custom_styles'));
        add_action('woocommerce_before_checkout_form', array($this, 'display_cart_on_checkout'), 5);
        add_action('woocommerce_add_to_cart', array($this, 'limit_cart_to_specific_products'), 10, 6);
        add_action('wp_ajax_clear_cart', array($this, 'clear_cart'));
        add_action('wp_ajax_nopriv_clear_cart', array($this, 'clear_cart'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_custom_scripts'));
    }
    
    // Limit cart to specific products
    public function limit_cart_to_specific_products($cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data) {
        $additional_product_id = 1572152;
        $cart = WC()->cart->get_cart();
        $contains_additional_product = false;

        foreach ($cart as $key => $item) {
            if ($item['product_id'] == $additional_product_id) {
                $contains_additional_product = true;
                break;
            }
        }

        if ($contains_additional_product && count($cart) > 2) {
            // Remove the oldest non-additional product
            foreach ($cart as $key => $item) {
                if ($item['product_id'] != $additional_product_id) {
                    WC()->cart->remove_cart_item($key);
                    break;
                }
            }
        } elseif (!$contains_additional_product && count($cart) > 1) {
            // Ensure only one product if the additional product is not present
            foreach ($cart as $key => $item) {
                if ($key != $cart_item_key) {
                    WC()->cart->remove_cart_item($key);
                }
            }
        }
    }

    // Display cart in checkout
    public function display_cart_on_checkout() {
        if (WC()->cart->is_empty()) {
            return; // Do not display cart if it's empty
        }

        echo '<h2>Your Cart</h2>';
        echo '<form class="woocommerce-cart-form" action="' . esc_url( wc_get_cart_url() ) . '" method="post">';
        echo '<table class="shop_table shop_table_responsive cart woocommerce-cart-form__contents" cellspacing="0">';
        echo '<thead>';
        echo '<tr>';
        echo '<th class="product-remove">Remove</th>';
        echo '<th class="product-name">Product</th>';
        echo '<th class="product-price">Price</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        
        foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
            $_product   = $cart_item['data'];
            $product_id = $cart_item['product_id'];
            
            echo '<tr class="woocommerce-cart-form__cart-item cart_item">';
            
            // Product Remove
            echo '<td class="product-remove">';
            echo '<a href="' . esc_url(wc_get_cart_remove_url($cart_item_key)) . '" class="remove" aria-label="' . __('Remove this item', 'custom-checkout-cart') . '">&times;</a>';
            echo '</td>';

            // Product Name
            echo '<td class="product-name" data-title="' . __('Product', 'custom-checkout-cart') . '">';
            echo '<strong>' . $_product->get_name() . '</strong>';
            echo '</td>';
            
            // Product Price
            echo '<td class="product-price" data-title="' . __('Price', 'custom-checkout-cart') . '">';
            echo WC()->cart->get_product_price($_product);
            echo '</td>';

            echo '</tr>';
        }

        echo '</tbody>';
        echo '</table>';
        echo '</form>';
    }

    // Clear cart function
    public function clear_cart() {
        WC()->cart->empty_cart();
        wp_die();
    }

    // Enqueue custom styles
    public function enqueue_custom_styles() {
        wp_enqueue_style('custom-checkout-cart-styles', plugins_url('custom-checkout-cart.css', __FILE__));
    }

    // Enqueue custom scripts
    public function enqueue_custom_scripts() {
        wp_enqueue_script('custom-checkout-cart-scripts', plugins_url('custom-checkout-cart.js', __FILE__), array('jquery'), null, true);
        wp_localize_script('custom-checkout-cart-scripts', 'clearCartAjax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
        ));
    }
}

new Custom_Checkout_Cart();

add_filter('woocommerce_add_to_cart_validation', 'check_duplicate_product_in_cart', 10, 3);
function check_duplicate_product_in_cart($passed, $product_id, $quantity) {
    // Get the cart contents
    $cart = WC()->cart->get_cart();
    
    // Loop through the cart items
    foreach ($cart as $cart_item_key => $cart_item) {
        // Check if the product being added already exists in the cart
        if ($cart_item['product_id'] == $product_id) {
            // If product already in cart, remove it
            WC()->cart->remove_cart_item($cart_item_key);
        }
    }
    
    // Allow new product addition to proceed
    return $passed;
}

add_action('woocommerce_add_to_cart', 'add_product_to_cart_updated', 10, 6);
function add_product_to_cart_updated($cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data) {
    // Get the product
    $product = wc_get_product($product_id);

    // Set the quantity to 1 since only 1 quantity is allowed
    WC()->cart->set_quantity($cart_item_key, 1);
}