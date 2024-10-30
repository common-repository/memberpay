<?php
/**
 * MemberPay Uninstall
 *
 * @package MemberPay\Uninstaller
 * @since 1.0.0
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
  die;
}

delete_option( 'memberpay_options' );
delete_option( 'memberpay_midtrans_server_key' );
delete_option( 'memberpay_midtrans_api_status' );
delete_option( 'memberpay_midtrans_server_key_validation' );
delete_option( 'memberpay_midtrans_payment_method_id' );
delete_option( 'memberpay_midtrans_expiry_days' );
delete_option( 'memberpay_midtrans_enabled_payments' );