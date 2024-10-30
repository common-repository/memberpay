<?php
/**
 * @package MemberPay\Functions
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit;

if(!function_exists('memberpay_add_account_subscription_th')) {
  function memberpay_add_account_subscription_th($user, $subs) {
    ?>
    <th><?php _e('Payment Link', 'memberpay'); ?></th>
    <?php
  }
  add_action('mepr-account-subscriptions-th', 'memberpay_add_account_subscription_th', 10, 2);
}

if(!function_exists('memberpay_add_account_subscription_td')) {
  function memberpay_add_account_subscription_td($user, $sub, $txn, $is_recurring) {
    global $wpdb;

    $subscription_id = $sub->id;
    $transaction = $wpdb->get_row( "SELECT id FROM {$wpdb->prefix}mepr_transactions WHERE subscription_id = $subscription_id" );

    $payment_link = $wpdb->get_row( "SELECT meta_value FROM {$wpdb->prefix}mepr_transaction_meta WHERE transaction_id = $transaction->id AND meta_key = 'midtrans_payment_link'" );
    ?>
    <td data-label="<?php _e('Payment Link', 'memberpay'); ?>">
      <div class="memberpay-payment-link"><?php echo ($payment_link != null ? '<a href="' . esc_url($payment_link->meta_value) . '" rel="noopener nofollow" target="_blank">' . __('See Link', 'memberpay') . '</a>' : '' ); ?></div>
    </td>
    <?php
  }
  add_action('mepr-account-subscriptions-td', 'memberpay_add_account_subscription_td', 10, 4);
}