<?php

/**

 * @package MemberPay\Functions

 * @since 1.0.0

 */



defined( 'ABSPATH' ) || exit;



// Admin Notices

if(!function_exists('memberpay_admin_notices')) {

  add_action( 'admin_notices', 'memberpay_admin_notices' );

  function memberpay_admin_notices(){
    $is_ready = true;

    if( get_transient( 'memberpay-memberpress-is-required' ) ) {

      ?>

      <div class='info notice is-dismissible'>

      <p><?php echo __("Sorry, it looks like you haven't installed or activated the Memberpress plugin yet.", "memberpay"); ?></p>

      </div>



      <?php
      $is_ready = false;
      delete_transient( 'memberpay-memberpress-is-required' );

    }



    if( get_transient( 'memberpay-memberpress-developer-tools-is-required' ) ) {

      ?>

      <div class='info notice is-dismissible'>

      <p><?php echo __("Hey, it looks like you haven't installed or activated the Memberpress Developer Tools plugin yet.", "memberpay"); ?></p>

      </div>



      <?php
      $is_ready = false;
      delete_transient( 'memberpay-memberpress-developer-tools-is-required' );

    }



    if( $is_ready ) {
      // Display notification errors whene minimum requirement memberpay not applied

      $mepr_options = get_option( 'mepr_options' );

      $memberpay_options = get_option( 'memberpay_options' );

      

      // Check SECRET KEY and API STATUS is connected or not

      $memberpay_midtrans_server_key_validation = get_option('memberpay_midtrans_server_key_validation');

      $secret_key_status = ($memberpay_midtrans_server_key_validation == false ? false : memberpay_check_secret_key_status((isset($memberpay_options['memberpay_midtrans_server_key']) ? $memberpay_options['memberpay_midtrans_server_key'] : ''), (isset($memberpay_options['memberpay_midtrans_api_status']) ? $memberpay_options['memberpay_midtrans_api_status'] : ''), $memberpay_midtrans_server_key_validation));



      if($secret_key_status == false) {

        echo '<div class="info notice">';

        echo '<p>' . __("Sorry, it looks like the Midtrans installation can't connect. Please visit and complete the MemberPay settings correctly.", "memberpay") . '&nbsp;<a class="text-blue-600 hover:text-blue-900" rel="noopener nofollow" href="' . admin_url('/admin.php?page=memberpay') . '">' . __('Go to Setting', 'memberpay') . '</a></p>';

        echo '</div>';

      }



      // Check PAYMENT METHOD exists or not

      $memberpay_midtrans_payment_method_id = (isset($memberpay_options['memberpay_midtrans_payment_method_id']) ? $memberpay_options['memberpay_midtrans_payment_method_id'] : '');

      $payment_integrations = $mepr_options['integrations'];



      if($memberpay_midtrans_payment_method_id == '' || array_key_exists($memberpay_midtrans_payment_method_id, $payment_integrations) == false) {

        echo '<div class="info notice">';

        echo '<p>' . __("Hey, it looks like your payment method ID doesn't exist.", "memberpay") . '&nbsp;<a class="text-blue-600 hover:text-blue-900" rel="noopener nofollow" href="' . admin_url('/admin.php?page=memberpay') . '">' . __('Go to Setting', 'memberpay') . '</a></p>';

        echo '</div>';

      } else if($payment_integrations[$memberpay_midtrans_payment_method_id]['gateway'] != 'MeprArtificialGateway') {

        echo '<div class="info notice">';

        echo '<p>' . __("Sorry, please select and set an offline payment method ID.", "memberpay") . '&nbsp;<a class="text-blue-600 hover:text-blue-900" rel="noopener nofollow" href="' . admin_url('/admin.php?page=memberpay') . '">' . __('Go to Setting', 'memberpay') . '</a></p>';

        echo '</div>';

      }



      // Check Developer API KEY exists

      $mpdt_api_key = get_option('mpdt_api_key');



      if($mpdt_api_key == false) {

        echo '<div class="info notice">';

        echo '<p>' . __("Hey, it looks like your API KEY is empty. Please re-generate to get new API KEY.", "memberpay") . '&nbsp;<a class="text-blue-600 hover:text-blue-900" rel="noopener nofollow" href="' . admin_url('/admin.php?page=memberpress-developer-tools') . '">' . __('Go to Setting', 'memberpay') . '</a></p>';

        echo '</div>';

      }
    }
  }

}