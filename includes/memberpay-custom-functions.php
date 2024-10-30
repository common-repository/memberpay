<?php

/**

 * @package MemberPay\Functions

 * @since 1.0.0

 */



defined( 'ABSPATH' ) || exit;



if(!function_exists('memberpay_encrypt_decrypt')) {

  function memberpay_encrypt_decrypt($txt='', $method='encrypt') {

    $output = null;



    $secret_key = "FWbQNLVRTp";

    $secret_iv = "B2gqCDjAUv";

    $key = hash('sha256', $secret_key);

    $iv = substr(hash('sha256', $secret_iv), 0, 16);



    if($method == 'encrypt') {

      $output = base64_encode(openssl_encrypt($txt, 'AES-256-CBC', $key, 0, $iv));

    } else if($method == 'decrypt') {

      $output = openssl_decrypt(base64_decode($txt), 'AES-256-CBC', $key, 0, $iv);

    }



    return $output;

  }

}



if(!function_exists('memberpay_check_secret_key_status')) {

  function memberpay_check_secret_key_status($secret_key, $status, $key) {

    $memberpay_midtrans_server_key_validation = memberpay_encrypt_decrypt($key, 'decrypt');

    $arr_key = explode(':', $memberpay_midtrans_server_key_validation);

    

    if($arr_key[0] === $secret_key && $arr_key[1] === ($status == 'true' ? 'sandbox' : 'production') && $arr_key[2] === 'valid') {

      return true;

    } else {

      return false;

    }

  }

}

if(!function_exists('memberpay_generate_random_str')) {
  function memberpay_generate_random_str($length=8, $characters='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ') {
    $charactersLength = strlen($characters);
    $randomString = '';

    for ($i = 0; $i < $length; $i++) {
      $randomString .= $characters[rand(0, $charactersLength - 1)];
    }

    return $randomString;
  }
}