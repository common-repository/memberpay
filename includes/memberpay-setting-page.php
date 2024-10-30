<?php
/**
 * @package MemberPay\Functions
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * @internal never define functions inside callbacks.
 * these functions could be run multiple times; this would result in a fatal error.
 */

/**
 * custom option and settings
 */
if(!function_exists('memberpay_settings_init')) {
	function memberpay_settings_init() {
		// Register a new setting for "memberpay" page.
		register_setting( 'memberpay', 'memberpay_options' );

		// Register a new section in the "memberpay" page.
		add_settings_section(
			'memberpay_midtrans_section',
			__( 'Midtrans', 'memberpay' ), 'memberpay_midtrans_section_callback',
			'memberpay'
		);

		// Register a new field in the "memberpay_midtrans_section" section, inside the "memberpay" page.
		add_settings_field(
			'memberpay_midtrans_server_key',
			__( 'Server Key', 'memberpay' ),
			'memberpay_midtrans_server_key_cb',
			'memberpay',
			'memberpay_midtrans_section',
			array(
				'label_for' => 'memberpay_midtrans_server_key',
				'class'			=> 'regular-text'
			)
		);

		add_settings_field(
			'memberpay_midtrans_api_status',
			__( 'Is Development?', 'memberpay' ),
			'memberpay_midtrans_api_status_cb',
			'memberpay',
			'memberpay_midtrans_section',
			array(
				'label_for' => 'memberpay_midtrans_api_status',
				'class' 		=> ''
			)
		);

		add_settings_field(
			'memberpay_endpoint_handling_after_payment',
			__( 'Payment Notification URL', 'memberpay' ),
			'memberpay_endpoint_handling_after_payment_cb',
			'memberpay',
			'memberpay_midtrans_section',
			array(
				'label_for' => 'memberpay_endpoint_handling_after_payment',
				'class' 		=> ''
			)
		);

		// Register a new section in the "memberpay" page.
		add_settings_section(
			'memberpay_payment_link_section',
			__( 'Payment Link', 'memberpay' ), 'memberpay_payment_link_section_callback',
			'memberpay'
		);

		add_settings_field(
			'memberpay_midtrans_payment_method_id',
			__( 'Payment Method ID', 'memberpay' ),
			'memberpay_midtrans_payment_method_id_cb',
			'memberpay',
			'memberpay_payment_link_section',
			array(
				'label_for' => 'memberpay_midtrans_payment_method_id',
				'class'			=> ''
			)
		);

		add_settings_field(
			'memberpay_midtrans_usage_limit',
			__( 'Usage Limit', 'memberpay' ),
			'memberpay_midtrans_usage_limit_cb',
			'memberpay',
			'memberpay_payment_link_section',
			array(
				'label_for' => 'memberpay_midtrans_usage_limit',
				'class'			=> 'small-text'
			)
		);

		add_settings_field(
			'memberpay_midtrans_expiry_days',
			__( 'Expiry Days', 'memberpay' ),
			'memberpay_midtrans_expiry_days_cb',
			'memberpay',
			'memberpay_payment_link_section',
			array(
				'label_for' => 'memberpay_midtrans_expiry_days',
				'class'			=> 'small-text'
			)
		);

		// add_settings_field(
		// 	'memberpay_midtrans_enabled_payments',
		// 	__( 'Enabled Payments', 'memberpay' ),
		// 	'memberpay_midtrans_enabled_payments_cb',
		// 	'memberpay',
		// 	'memberpay_payment_link_section',
		// 	array(
		// 		'label_for' => 'memberpay_midtrans_enabled_payments',
		// 		'class' 		=> ''
		// 	)
		// );
	}

	/**
	 * Register our memberpay_settings_init to the admin_init action hook.
	 */
	add_action( 'admin_init', 'memberpay_settings_init' );
}


/**
 * Custom option and settings:
 *  - callback functions
 */


/**
 * Developers section callback function.
 *
 * @param array $args  The settings array, defining title, id, callback.
 */
if(!function_exists('memberpay_midtrans_section_callback')) {
	function memberpay_midtrans_section_callback( $args ) {
		?>
		<p id="<?php echo esc_attr( $args['id'] ); ?>"><?php _e( 'Set <strong>Server Key</strong> to communicate with Midtrans.', 'memberpay' ); ?>&nbsp;<a class="text-blue-600 hover:text-blue-900" rel="noopener nofollow" href="https://docs.midtrans.com/en/midtrans-account/overview?id=retrieving-api-access-keys"><?php _e('See Documentation', 'memberpay'); ?></a></p>
		<?php
	}
}

if(!function_exists('memberpay_payment_link_section_callback')) {
	function memberpay_payment_link_section_callback( $args ) {
		?>
		<p id="<?php echo esc_attr( $args['id'] ); ?>"><?php _e( 'Set the condition of the payment link which will be generated automatically from Midtrans.', 'memberpay' ); ?></p>
		<?php
	}
}

/**
 * Pill field callbakc function.
 *
 * WordPress has magic interaction with the following keys: label_for, class.
 * - the "label_for" key value is used for the "for" attribute of the <label>.
 * - the "class" key value is used for the "class" attribute of the <tr> containing the field.
 * Note: you can add custom key value pairs to be used inside your callbacks.
 *
 * @param array $args
 */
if(!function_exists('memberpay_midtrans_server_key_cb')) {
	function memberpay_midtrans_server_key_cb( $args ) {
		// Get the value of the setting we've registered with register_setting()
		$options = get_option( 'memberpay_options' );
		?>
		<input type="text" name="memberpay_options[<?php echo esc_attr( $args['label_for'] ); ?>]" value="<?php echo (isset( $options[ $args['label_for'] ] ) ? esc_html($options[ $args['label_for'] ]) : ''); ?>" class="<?php echo (isset( $args['class'] ) ? esc_html($args['class']) : ''); ?>" required="required" />
		<?php
	}
}

if(!function_exists('memberpay_midtrans_api_status_cb')) {
	function memberpay_midtrans_api_status_cb( $args ) {
		// Get the value of the setting we've registered with register_setting()
		$options = get_option( 'memberpay_options' );

		// Update
		$midtrans_secret_key_validation = get_option('memberpay_midtrans_server_key_validation');
		$url = (isset($options['memberpay_midtrans_api_status']) && $options['memberpay_midtrans_api_status'] == 'true' ? MIDTRANS_API_SANDBOX_BASE_URL : MIDTRANS_API_PRODUCTION_BASE_URL);

		$request = wp_remote_get($url . '/v2/1/status', array(
			'headers' => array(
				'Accept' => 'application/json',
				'Content-type' => 'application/json',
				'Authorization' => 'Basic ' . base64_encode((isset($options['memberpay_midtrans_server_key']) ? $options['memberpay_midtrans_server_key'] : '') . ':')
			)
		));
		
		$response = json_decode(wp_remote_retrieve_body($request));
		$status_code = $response->status_code;

		$memberpay_midtrans_server_key_validation = memberpay_encrypt_decrypt((isset($options['memberpay_midtrans_server_key']) ? $options['memberpay_midtrans_server_key'] : '') . ':' . (isset($options['memberpay_midtrans_api_status']) && $options['memberpay_midtrans_api_status'] == 'true' ? 'sandbox' : 'production') . ':' . ($status_code != 401 ? 'valid' : 'invalid'), 'encrypt');

		if($status_code != 401) {
			$secret_key_status = true;
		} else {
			$secret_key_status = false;
		}
				
		if($midtrans_secret_key_validation === false) {
			add_option('memberpay_midtrans_server_key_validation', $memberpay_midtrans_server_key_validation);
		} else {
			update_option('memberpay_midtrans_server_key_validation', $memberpay_midtrans_server_key_validation);
		}
		// Update
		?>
		<input type="checkbox" name="memberpay_options[<?php echo esc_attr( $args['label_for'] ); ?>]" value="true" <?php echo (isset( $options[ $args['label_for'] ] ) && $options[ $args['label_for'] ] == true ? 'checked="checked"' : ''); ?> class="<?php echo (isset( $args['class'] ) ? esc_html($args['class']) : ''); ?>" />&nbsp;<?php _e("Check this column if it's <strong>Midtrans</strong> you're using for testing.", "memberpay"); ?>
		<?php
		echo ($secret_key_status ? '<p><strong class="text-green-600">Connected</strong></p>' : '<p><strong class="text-red-600">Disconnect</strong></p>');
	}
}

if(!function_exists('memberpay_endpoint_handling_after_payment_cb')) {
	function memberpay_endpoint_handling_after_payment_cb( $args ) {
		?>
		<span class="font-semibold bg-green-100 text-green-900"><?php echo esc_url(home_url('/wp-json/' . MEMBERPAY_VERSION_API . MEMBERPAY_ENDPOINT_HANDLING_AFTER_PAYMENT)); ?></span>
		<p class="pt-2"><?php _e('This is the endpoint for handling post-payment notification requests. Copy paste this url in Midtrans dashboard settings.', 'memberpay'); ?>&nbsp;<a class="text-blue-600 hover:text-blue-900" rel="noopener nofollow" href="https://docs.midtrans.com/en/after-payment/http-notification"><?php _e('See Configuration', 'memberpay'); ?></a></p>
		<?php
	}
}

if(!function_exists('memberpay_midtrans_usage_limit_cb')) {
	function memberpay_midtrans_usage_limit_cb( $args ) {
		// Get the value of the setting we've registered with register_setting()
		$options = get_option( 'memberpay_options' );
		?>
		<input type="number" min="1" step="1" name="memberpay_options[<?php echo esc_attr( $args['label_for'] ); ?>]" value="<?php echo (isset( $options[ $args['label_for'] ] ) ? esc_html($options[ $args['label_for'] ]) : '1'); ?>" class="<?php echo (isset( $args['class'] ) ? esc_html($args['class']) : ''); ?>" required="required" />
		<?php
	}
}

if(!function_exists('memberpay_midtrans_expiry_days_cb')) {
	function memberpay_midtrans_expiry_days_cb( $args ) {
		// Get the value of the setting we've registered with register_setting()
		$options = get_option( 'memberpay_options' );
		?>
		<input type="number" min="1" step="1" name="memberpay_options[<?php echo esc_attr( $args['label_for'] ); ?>]" value="<?php echo (isset( $options[ $args['label_for'] ] ) ? esc_html($options[ $args['label_for'] ]) : '1'); ?>" class="<?php echo (isset( $args['class'] ) ? esc_html($args['class']) : ''); ?>" required="required" />
		<?php
	}
}

/* if(!function_exists('memberpay_midtrans_enabled_payments_cb')) {
	function memberpay_midtrans_enabled_payments_cb( $args ) {
		// Get the value of the setting we've registered with register_setting()
		$options = get_option( 'memberpay_options' );
		?>
		<div class="flex flex-col md:flex-row gap-4">
			<div>
				<p><strong>Bank Transfer</strong></p>
				<p><input type="checkbox" name="memberpay_options[<?php echo esc_attr( $args['label_for'] ); ?>][]" value="bca_va" <?php echo (isset( $options[ $args['label_for'] ] ) && in_array('bca_va', $options[$args['label_for']]) ? 'checked="checked"' : ''); ?> class="<?php echo (isset( $args['class'] ) ? esc_html($args['class']) : ''); ?>" />&nbsp;<?php _e('BCA', 'memberpay'); ?></p>

				<p><input type="checkbox" name="memberpay_options[<?php echo esc_attr( $args['label_for'] ); ?>][]" value="bni_va" <?php echo (isset( $options[ $args['label_for'] ] ) && in_array('bni_va', $options[$args['label_for']]) ? 'checked="checked"' : ''); ?> class="<?php echo (isset( $args['class'] ) ? esc_html($args['class']) : ''); ?>" />&nbsp;<?php _e('BNI', 'memberpay'); ?></p>

				<p><input type="checkbox" name="memberpay_options[<?php echo esc_attr( $args['label_for'] ); ?>][]" value="bri_va" <?php echo (isset( $options[ $args['label_for'] ] ) && in_array('bri_va', $options[$args['label_for']]) ? 'checked="checked"' : ''); ?> class="<?php echo (isset( $args['class'] ) ? esc_html($args['class']) : ''); ?>" />&nbsp;<?php _e('BRIVA', 'memberpay'); ?></p>

				<p><input type="checkbox" name="memberpay_options[<?php echo esc_attr( $args['label_for'] ); ?>][]" value="permata_va" <?php echo (isset( $options[ $args['label_for'] ] ) && in_array('permata_va', $options[$args['label_for']]) ? 'checked="checked"' : ''); ?> class="<?php echo (isset( $args['class'] ) ? esc_html($args['class']) : ''); ?>" />&nbsp;<?php _e('Permata Bank', 'memberpay'); ?></p>

				<p><input type="checkbox" name="memberpay_options[<?php echo esc_attr( $args['label_for'] ); ?>][]" value="other_va" <?php echo (isset( $options[ $args['label_for'] ] ) && in_array('other_va', $options[$args['label_for']]) ? 'checked="checked"' : ''); ?> class="<?php echo (isset( $args['class'] ) ? esc_html($args['class']) : ''); ?>" />&nbsp;<?php _e('Other VA', 'memberpay'); ?></p>
			</div>
			<div>
				<p><strong>E-Wallets</strong></p>
				<p><input type="checkbox" name="memberpay_options[<?php echo esc_attr( $args['label_for'] ); ?>][]" value="gopay" <?php echo (isset( $options[ $args['label_for'] ] ) && in_array('gopay', $options[$args['label_for']]) ? 'checked="checked"' : ''); ?> class="<?php echo (isset( $args['class'] ) ? esc_html($args['class']) : ''); ?>" />&nbsp;<?php _e('GoPay', 'memberpay'); ?></p>

				<p><input type="checkbox" name="memberpay_options[<?php echo esc_attr( $args['label_for'] ); ?>][]" value="shopeepay" <?php echo (isset( $options[ $args['label_for'] ] ) && in_array('shopeepay', $options[$args['label_for']]) ? 'checked="checked"' : ''); ?> class="<?php echo (isset( $args['class'] ) ? esc_html($args['class']) : ''); ?>" />&nbsp;<?php _e('ShopeePay', 'memberpay'); ?></p>

				<p><strong>Credit Cards</strong></p>
				<p><input type="checkbox" name="memberpay_options[<?php echo esc_attr( $args['label_for'] ); ?>][]" value="credit_card" <?php echo (isset( $options[ $args['label_for'] ] ) && in_array('credit_cart', $options[$args['label_for']]) ? 'checked="checked"' : ''); ?> class="<?php echo (isset( $args['class'] ) ? esc_html($args['class']) : ''); ?>" />&nbsp;<?php _e('Credit Card', 'memberpay'); ?></p>
				
				<p><strong>Over The Counter</strong></p>
				<p><input type="checkbox" name="memberpay_options[<?php echo esc_attr( $args['label_for'] ); ?>][]" value="indomaret" <?php echo (isset( $options[ $args['label_for'] ] ) && in_array('indomaret', $options[$args['label_for']]) ? 'checked="checked"' : ''); ?> class="<?php echo (isset( $args['class'] ) ? esc_html($args['class']) : ''); ?>" />&nbsp;<?php _e('Indomaret', 'memberpay'); ?></p>
			</div>
			<div>
				<p><strong>Direct Debit</strong></p>
				<p><input type="checkbox" name="memberpay_options[<?php echo esc_attr( $args['label_for'] ); ?>][]" value="bca_klikbca" <?php echo (isset( $options[ $args['label_for'] ] ) && in_array('bca_klikbca', $options[$args['label_for']]) ? 'checked="checked"' : ''); ?> class="<?php echo (isset( $args['class'] ) ? esc_html($args['class']) : ''); ?>" />&nbsp;<?php _e('KlikBCA', 'memberpay'); ?></p>

				<p><input type="checkbox" name="memberpay_options[<?php echo esc_attr( $args['label_for'] ); ?>][]" value="bca_klikpay" <?php echo (isset( $options[ $args['label_for'] ] ) && in_array('bca_klikpay', $options[$args['label_for']]) ? 'checked="checked"' : ''); ?> class="<?php echo (isset( $args['class'] ) ? esc_html($args['class']) : ''); ?>" />&nbsp;<?php _e('BCA KlikPay', 'memberpay'); ?></p>

				<p><input type="checkbox" name="memberpay_options[<?php echo esc_attr( $args['label_for'] ); ?>][]" value="danamon_online" <?php echo (isset( $options[ $args['label_for'] ] ) && in_array('danamon_online', $options[$args['label_for']]) ? 'checked="checked"' : ''); ?> class="<?php echo (isset( $args['class'] ) ? esc_html($args['class']) : ''); ?>" />&nbsp;<?php _e('Danamon Online Banking', 'memberpay'); ?></p>
				
				<p><input type="checkbox" name="memberpay_options[<?php echo esc_attr( $args['label_for'] ); ?>][]" value="cimb_clicks" <?php echo (isset( $options[ $args['label_for'] ] ) && in_array('cimb_clicks', $options[$args['label_for']]) ? 'checked="checked"' : ''); ?> class="<?php echo (isset( $args['class'] ) ? esc_html($args['class']) : ''); ?>" />&nbsp;<?php _e('Cimb Clicks', 'memberpay'); ?></p>
			
				<p><input type="checkbox" name="memberpay_options[<?php echo esc_attr( $args['label_for'] ); ?>][]" value="bri_epay" <?php echo (isset( $options[ $args['label_for'] ] ) && in_array('bri_epay', $options[$args['label_for']]) ? 'checked="checked"' : ''); ?> class="<?php echo (isset( $args['class'] ) ? esc_html($args['class']) : ''); ?>" />&nbsp;<?php _e('BRI Epay', 'memberpay'); ?></p>				
			</div>
			<div>
				<p><strong>Cardless Credit</strong></p>
				<p><input type="checkbox" name="memberpay_options[<?php echo esc_attr( $args['label_for'] ); ?>][]" value="akulaku" <?php echo (isset( $options[ $args['label_for'] ] ) && in_array('akulaku', $options[$args['label_for']]) ? 'checked="checked"' : ''); ?> class="<?php echo (isset( $args['class'] ) ? esc_html($args['class']) : ''); ?>" />&nbsp;<?php _e('Akulaku PayLater', 'memberpay'); ?></p>
				
				<p><strong>Other Payments</strong></p>
				<p><input type="checkbox" name="memberpay_options[<?php echo esc_attr( $args['label_for'] ); ?>][]" value="telkomsel_cash" <?php echo (isset( $options[ $args['label_for'] ] ) && in_array('telkomsel_cash', $options[$args['label_for']]) ? 'checked="checked"' : ''); ?> class="<?php echo (isset( $args['class'] ) ? esc_html($args['class']) : ''); ?>" />&nbsp;<?php _e('Telkomsel Cash', 'memberpay'); ?></p>
				
				<p><input type="checkbox" name="memberpay_options[<?php echo esc_attr( $args['label_for'] ); ?>][]" value="echannel" <?php echo (isset( $options[ $args['label_for'] ] ) && in_array('echannel', $options[$args['label_for']]) ? 'checked="checked"' : ''); ?> class="<?php echo (isset( $args['class'] ) ? esc_html($args['class']) : ''); ?>" />&nbsp;<?php _e('E-Channel', 'memberpay'); ?></p>
			</div>
		</div>

		<p class="pt-4"><?php _e('Customizable list of payment methods that will be shown during payment. If not specified, by default all active payment methods are shown.', 'memberpay'); ?></p>
		<p class="pt-2"><?php _e('Each payment method will be charged for each successful transaction.', 'memberpay'); ?>&nbsp;<a class="text-blue-600 hover:text-blue-900" rel="noopener nofollow" href="https://midtrans.com/id/biaya"><?php _e('See Documentation', 'memberpay'); ?></a></p>
		<?php
	}
} */

if(!function_exists('memberpay_midtrans_payment_method_id_cb')) {
	function memberpay_midtrans_payment_method_id_cb( $args ) {
		// Get the value of the setting we've registered with register_setting()
		$options = get_option( 'memberpay_options' );
		?>
		<input type="text" name="memberpay_options[<?php echo esc_attr( $args['label_for'] ); ?>]" value="<?php echo (isset( $options[ $args['label_for'] ] ) ? esc_html($options[ $args['label_for'] ]) : ''); ?>" class="<?php echo (isset( $args['class'] ) ? esc_html($args['class']) : ''); ?>" required="required" />

		<p class="pt-2"><?php _e('Set a payment method id that will generate a payment link to Midtrans. It only works for offline payments (custom).', 'memberpay'); ?>&nbsp;<a class="text-blue-600 hover:text-blue-900" rel="noopener nofollow" href="<?php echo admin_url('/admin.php?page=memberpress-options#mepr-integration'); ?>"><?php _e('Get a Payment Method ID', 'memberpay'); ?></a></p>
		<?php
	}
}

/**
 * Add the top level menu page.
 */
if(!function_exists('memberpay_options_page')) {
	function memberpay_options_page() {
		$memberpay_menu = add_menu_page(
			__('MemberPay', 'memberpay'), // Page title
			__('MemberPay', 'memberpay'), // Menu title
			'manage_options', // Capability
			'memberpay', // Menu slug
			'memberpay_options_page_html', // Callback
			'dashicons-smiley', // Icon url
			59
		);

		add_action('admin_print_scripts-' . $memberpay_menu, 'memberpay_options_page_scripts');
	}


	/**
	 * Register our memberpay_options_page to the admin_menu action hook.
	 */
	add_action( 'admin_menu', 'memberpay_options_page' );
}

if(!function_exists('memberpay_options_page_scripts')) {
	function memberpay_options_page_scripts() {
		wp_enqueue_script('memberpay-tailwind-js', MEMBERPAY_PLUGIN_URL . 'admin/js/tailwind-3-2-4.js', array('jquery'), filemtime(MEMBERPAY_PLUGIN_DIR . 'admin/js/tailwind-3-2-4.js'), true);
	}
}


/**
 * Top level menu callback function
 */
if(!function_exists('memberpay_options_page_html')) {
	function memberpay_options_page_html() {
		// check user capabilities
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// add error/update messages

		// check if the user have submitted the settings
		// WordPress will add the "settings-updated" $_GET parameter to the url
		if ( isset( $_GET['settings-updated'] ) ) {
			// add settings saved message with the class of "updated"
			add_settings_error( 'memberpay_messages', 'memberpay_message', __( 'Settings Saved', 'memberpay' ), 'updated' );
		}

		// show error/update messages
		settings_errors( 'memberpay_messages' );
		?>
		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			<form action="options.php" method="post">
				<?php
				// output security fields for the registered setting "memberpay"
				settings_fields( 'memberpay' );
				// output setting sections and their fields
				// (sections are registered for "memberpay", each field is registered to a specific section)
				do_settings_sections( 'memberpay' );
				// output save settings button
				submit_button( 'Save Settings' );
				?>
			</form>
		</div>
		<?php
	}
}

if(!function_exists('memberpay_set_midtrans_secret_key_validation')) {
	function memberpay_set_midtrans_secret_key_validation($option_name, $old_value, $value) {
		if($option_name === 'memberpay_options') {
			if($old_value !== $value) {
				$memberpay_options = get_option( 'memberpay_options' );
				$midtrans_secret_key_validation = get_option('memberpay_midtrans_server_key_validation');

				// Check secret key
				$url = (isset($memberpay_options['memberpay_midtrans_api_status']) && $memberpay_options['memberpay_midtrans_api_status'] == 'true' ? MIDTRANS_API_SANDBOX_BASE_URL : MIDTRANS_API_PRODUCTION_BASE_URL);

				$request = wp_remote_get($url . '/v2/1/status', array(
					'headers' => array(
						'Accept' => 'application/json',
						'Content-type' => 'application/json',
						'Authorization' => 'Basic ' . base64_encode((isset($memberpay_options['memberpay_midtrans_server_key']) ? $memberpay_options['memberpay_midtrans_server_key'] : '') . ':')
					)
				));
				
				$response = json_decode(wp_remote_retrieve_body($request));
				$status_code = $response->status_code;

				$memberpay_midtrans_server_key_validation = memberpay_encrypt_decrypt((isset($memberpay_options['memberpay_midtrans_server_key']) ? $memberpay_options['memberpay_midtrans_server_key'] : '') . ':' . (isset($memberpay_options['memberpay_midtrans_api_status']) && $memberpay_options['memberpay_midtrans_api_status'] == 'true' ? 'sandbox' : 'production') . ':' . ($status_code != 401 ? 'valid' : 'invalid'), 'encrypt');
				
				if($midtrans_secret_key_validation === false) {
					add_option('memberpay_midtrans_server_key_validation', $memberpay_midtrans_server_key_validation);
				} else {
					update_option('memberpay_midtrans_server_key_validation', $memberpay_midtrans_server_key_validation);
				}
			}
		}
	}
	add_action('updated_option', 'memberpay_set_midtrans_secret_key_validation', 10, 3);
}