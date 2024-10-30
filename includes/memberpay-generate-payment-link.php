<?php

/**

 * @package MemberPay\Functions

 * @since 1.0.0

 */



defined( 'ABSPATH' ) || exit;



if(!function_exists('memberpay_generate_payment_link')) {

  function memberpay_generate_payment_link($txn) {

    global $wpdb;



    $mepr_options = get_option( 'mepr_options' );

    $memberpay_options = get_option( 'memberpay_options' );

    $current_user = get_user_by('ID', $txn->user_id);

    $payment_integrations = $mepr_options['integrations'];



    // Check if gateway same with payment method id memberpay

    if(isset($memberpay_options['memberpay_midtrans_payment_method_id']) && $memberpay_options['memberpay_midtrans_payment_method_id'] == $txn->gateway && $payment_integrations[$memberpay_options['memberpay_midtrans_payment_method_id']]['gateway'] == 'MeprArtificialGateway') {

      // Generate payment link for transaction

      $url = (isset($memberpay_options['memberpay_midtrans_api_status']) && $memberpay_options['memberpay_midtrans_api_status'] == 'true' ? MIDTRANS_API_SANDBOX_BASE_URL : MIDTRANS_API_PRODUCTION_BASE_URL);

  

      $body = array(

        'transaction_details' => array(

          'order_id'        => $txn->trans_num,

          'gross_amount'    => $txn->total,

          'payment_link_id' => 'payment-for-' . get_post_field('post_name', $txn->product_id) . '-' . $txn->id

        ),

        'credit_card' => array(

          'secure' => true

        ),

        'usage_limit' => (isset($memberpay_options['memberpay_midtrans_usage_limit']) ? $memberpay_options['memberpay_midtrans_usage_limit'] : 1),

        'expiry' => array(

          'start_time'  => date('Y-m-d H:i +0700', current_time( 'timestamp', 0 )),

          'duration'    => (isset($memberpay_options['memberpay_midtrans_expiry_days']) ? $memberpay_options['memberpay_midtrans_expiry_days'] : 1),

          'unit'        => 'days'

        ),

        'item_details' => array(

          array(

            'id'        => $txn->product_id,

            'name'      => get_post_meta($txn->product_id, '_mepr_product_pricing_title', true),

            'price'     => $txn->total,

            'quantity'  => 1

          )

        ),

        'customer_details' => array(

          'first_name'  => (!empty(get_user_meta($txn->user_id, 'first_name', true)) ? get_user_meta($txn->user_id, 'first_name', true) : 'John Doe'),

          'last_name'   => (!empty(get_user_meta($txn->user_id, 'last_name', true)) ? get_user_meta($txn->user_id, 'last_name', true) : ''),

          'email'       => $current_user->user_email,

          'phone'       => '+6281',

          'notes'       => __('Thank you for your purchase. Please follow the instructions to pay.', 'memberpay')

        ),

        'custom_field1' => $txn->id

      );



      if(isset($memberpay_options['memberpay_midtrans_enabled_payments']) && ($memberpay_options['memberpay_midtrans_enabled_payments'] != '' || $memberpay_options['memberpay_midtrans_enabled_payments'] != null)) {

        $body['enabled_payments'] = $memberpay_options['memberpay_midtrans_enabled_payments'];

      }

      $request = wp_remote_post($url . '/v1/payment-links', array(
        'headers' => array(
          'Accept' => 'application/json',
          'Content-type' => 'application/json',
          'Authorization' => 'Basic ' . base64_encode((isset($memberpay_options['memberpay_midtrans_server_key']) ? $memberpay_options['memberpay_midtrans_server_key'] : '') . ':')
        ),
        'body' => json_encode($body)
      ));
      
      $response = json_decode(wp_remote_retrieve_body($request));
      $httpCode = wp_remote_retrieve_response_code($request);

      if($httpCode == 200) {

        // Check transaction meta exists or not

        $check_transaction_meta = $wpdb->get_row(
          $wpdb->prepare(

            "SELECT COUNT(*) AS total FROM {$wpdb->prefix}mepr_transaction_meta WHERE transaction_id = %d AND meta_key = %s GROUP BY transaction_id",
            $txn->id,
            'midtrans_payment_link'
            
          )
        );

        

        if(!is_null($check_transaction_meta) && $check_transaction_meta->total != 0) {

          // Update metadata

          $wpdb->query(

            $wpdb->prepare(

              "UPDATE {$wpdb->prefix}mepr_transaction_meta SET meta_key = %s, meta_value = %s WHERE transaction_id = %d",

              'midtrans_payment_link',

              $response->payment_url,

              $txn->id

            )

          );

        } else {

          // Insert new metadata

          $wpdb->query(

            $wpdb->prepare(

              "INSERT INTO {$wpdb->prefix}mepr_transaction_meta

              ( transaction_id, meta_key, meta_value )

              VALUES ( %d, %s, %s )",

              $txn->id,

              'midtrans_payment_link',

              $response->payment_url

            )

          );

        }


        set_transient( 'memberpay_last_payment_url_' . $txn->user_id, $response->payment_url, 180 );
      }

    }

  }

  add_action('mepr-signup', 'memberpay_generate_payment_link');

}