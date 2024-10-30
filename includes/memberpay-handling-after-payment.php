<?php

/**

 * @package MemberPay\Functions

 * @since 1.0.0

 */



defined( 'ABSPATH' ) || exit;



if(!function_exists('memberpay_handling_after_payment')) {

  add_action('rest_api_init', 'memberpay_handling_after_payment');

  function memberpay_handling_after_payment() {

    register_rest_route(MEMBERPAY_VERSION_API, MEMBERPAY_ENDPOINT_HANDLING_AFTER_PAYMENT, array(

      'methods'  => 'POST',

      'callback' => 'memberpay_api_handling_after_payment'

    ));

  }

}



if(!function_exists('memberpay_api_handling_after_payment')) {

  function memberpay_api_handling_after_payment($request) {
    global $wpdb;

    $memberpay_options = get_option( 'memberpay_options' );



    require_once(MEMBERPAY_PLUGIN_DIR . '/vendor/midtrans-php-master/Midtrans.php');

    \Midtrans\Config::$isProduction = (isset($memberpay_options['memberpay_midtrans_api_status']) && $memberpay_options['memberpay_midtrans_api_status'] == 'true' ? false : true);

    \Midtrans\Config::$serverKey = (isset($memberpay_options['memberpay_midtrans_server_key']) ? $memberpay_options['memberpay_midtrans_server_key'] : '');

    

    try {

      $notif = new \Midtrans\Notification();

      $subscription_id = $notif->transaction_id;
      $status = $notif->transaction_status;

      $type = $notif->payment_type;

      $order_id = $notif->order_id;

      $status_code = $notif->status_code;

      $gross_amount = $notif->gross_amount;

      $fraud = $notif->fraud_status;

      $signature_key = $notif->signature_key;

      $transaction_id = $notif->custom_field1;

      $verifying_signature_key = hash('sha512', $order_id . $status_code . $gross_amount . (isset($memberpay_options['memberpay_midtrans_server_key']) ? $memberpay_options['memberpay_midtrans_server_key'] : ''));

      // Get transaction ID from trans_num
      $transaction = $wpdb->get_row( 
        $wpdb->prepare(

          "SELECT id, user_id, product_id, subscription_id FROM {$wpdb->prefix}mepr_transactions WHERE id = %d",
          $transaction_id
        )
      );

      $mpdt_api_key = get_option('mpdt_api_key');
      $body = array(
        'member'              => (float) $transaction->user_id,
        'membership'          => (float) $transaction->product_id,
        'subscription'        => (float) $transaction->subscription_id,
        'send_welcome_email'  => true,
        'send_receipt_email'  => true
      );
      $update_transaction = false;

      if($verifying_signature_key === $signature_key) {
        // Update email when register from cta oneclick checkout
        $user_id = (float) $transaction->user_id;

        // Update transaction status from midtrans request
        $url = get_rest_url( null, 'mp/v1/transactions/' . $transaction_id );

        if($status == 'capture') {
          // For credit card transaction, we need to check whether transaction is challenge by FDS or not

          if($type == 'credit_card') {
            
            if($fraud == 'challenge') {

              // Silent is gold
            } else {
              // Complete
              $update_transaction = true;
              $body['status'] = "complete";
            }

          }

        } else if($status == 'settlement') {
          // Complete
          $update_transaction = true;
          $body['status'] = "complete";
        } else if($status == 'pending') {
          // Silent is gold

        } else if($status == 'deny') {
          // Failed
          $update_transaction = true;
          $body['status'] = "failed";
        } else if($status == 'expire') {
          // Failed
          $update_transaction = true;
          $body['status'] = "failed";
        } else if($status == 'cancel') {
          // Failed
          $update_transaction = true;
          $body['status'] = "failed";
        }

        if($update_transaction) {
          $request = wp_remote_post($url, array(
            'headers' => array(
              'Accept' => 'application/json',
              'Content-type' => 'application/json',
              'MEMBERPRESS-API-KEY' => $mpdt_api_key
            ),
            'body' => json_encode($body)
          ));
          
          $response = json_decode(wp_remote_retrieve_body($request));
          $httpCode = wp_remote_retrieve_response_code($request);
        }

        $parser = array(
          'status' => 200,
          'success' => true,

          'message' => 'Success.'

        );

      

        $response = new WP_REST_Response($parser, 200);

        $response->set_status(200);

      } else {

        $parser = array(
          'status' => 200,
          'success' => false,

          'message' => 'Signature key not valid.'

        );

      

        $response = new WP_REST_Response($parser, 200);

        $response->set_status(200);

      }

    } catch (Exception $e) {

      $parser = array(
        'status' => 200,
        'success' => false,

        'message' => 'Internal server error.'

      );

    

      $response = new WP_REST_Response($parser, 200);

      $response->set_status(200);

    }

  

    return $response;

  }

}