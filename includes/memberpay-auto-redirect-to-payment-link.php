<?php
/**
 * @package MemberPay\Functions
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit;

if(!function_exists('memberpay_auto_redirect_to_payment_link')) {
  function memberpay_auto_redirect_to_payment_link() {
    global $post;

    if(is_admin()) {
      return;
    }

    if ( false === ( $memberpay_last_payment_url = get_transient( 'memberpay_last_payment_url_' . get_current_user_id() ) ) ) {
      return true;
    }

    $mepr_options = get_option('mepr_options');
    $current_ID = $post->ID;
    $custom_thankyou_page = get_post_meta($current_ID, '_mepr_product_thank_you_page_id', true);

    if($current_ID != $mepr_options['thankyou_page_id'] && $current_ID != $custom_thankyou_page) {
      return;
    }

    wp_enqueue_script('memberpay-tailwind-js', MEMBERPAY_PLUGIN_URL . 'admin/js/tailwind-3-2-4.js', array('jquery'), filemtime(MEMBERPAY_PLUGIN_DIR . 'admin/js/tailwind-3-2-4.js'), true);
    wp_enqueue_script('memberpay-auto-redirect-js', MEMBERPAY_PLUGIN_URL . 'public/js/auto-redirect.js', array('jquery'), filemtime(MEMBERPAY_PLUGIN_DIR . 'public/js/auto-redirect.js'), true);

    wp_localize_script(
      'memberpay-auto-redirect-js',
      'ajaxObj',
      array(
        'payment_link'      => esc_url($memberpay_last_payment_url),
        'subscription_link' => home_url('/account/?action=subscriptions')
      )
    );

    delete_transient( 'memberpay_last_payment_url_' . get_current_user_id() );
  }
  add_action('wp_head', 'memberpay_auto_redirect_to_payment_link');
}