<?php
/**
 * @package MemberPay\Functions
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit;

// Check requirements
if(!function_exists('memberpay_check_requirements')) {
  function memberpay_check_requirements() {
    if ( !in_array( 'memberpress/memberpress.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
      $plugins = array(
        'memberpay/memberpay.php'
      );

      require_once(ABSPATH . 'wp-admin/includes/plugin.php');
      deactivate_plugins($plugins);

      set_transient( 'memberpay-memberpress-is-required', true, 5 );
    }

    if ( !in_array( 'memberpress-developer-tools/main.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
      $plugins = array(
        'memberpay/memberpay.php'
      );

      require_once(ABSPATH . 'wp-admin/includes/plugin.php');
      deactivate_plugins($plugins);

      set_transient( 'memberpay-memberpress-developer-tools-is-required', true, 5 );
    }
  }

  add_action( 'admin_init', 'memberpay_check_requirements' );
}