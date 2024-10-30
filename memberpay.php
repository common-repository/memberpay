<?php

/**

 * Plugin Name

 *

 * @package           MemberPay

 * @author            Fuad Hadi Nugroho

 * @copyright         2022 Fuad Hadi Nugroho

 * @license           GPL-3.0-or-later

 *

 * @wordpress-plugin

 * Plugin Name:       MemberPay

 * Plugin URI:        -

 * Description:       A Memberpress payment gateway integration with Midtrans for membership subscriptions.

 * Version:           1.3.5

 * Requires at least: 6.1.1

 * Requires PHP:      8.1.9

 * Author:            Fuad Hadi Nugroho

 * Author URI:        https://www.instagram.com/fuad.hd/

 * License:           GPL v2 or later

 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt

 * Text Domain:       memberpay

 * Domain Path:       /languages

 * Network:           false

 */



 /*

MemberPay is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.
 
MemberPay is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
 
You should have received a copy of the GNU General Public License
along with MemberPay. If not, see https://www.gnu.org/licenses/gpl-3.0.txt.

*/



// Constant

defined('MEMBERPAY_PLUGIN_DIR') or define('MEMBERPAY_PLUGIN_DIR', plugin_dir_path( __FILE__ ));

defined('MEMBERPAY_PLUGIN_URL') or define('MEMBERPAY_PLUGIN_URL', plugin_dir_url( __FILE__ ));

defined('MIDTRANS_API_SANDBOX_BASE_URL') or define('MIDTRANS_API_SANDBOX_BASE_URL', 'https://api.sandbox.midtrans.com');

defined('MIDTRANS_API_PRODUCTION_BASE_URL') or define('MIDTRANS_API_PRODUCTION_BASE_URL', 'https://api.midtrans.com');



defined('MEMBERPAY_VERSION_API') or define('MEMBERPAY_VERSION_API', 'memberpay/v1');

defined('MEMBERPAY_ENDPOINT_HANDLING_AFTER_PAYMENT') or define('MEMBERPAY_ENDPOINT_HANDLING_AFTER_PAYMENT', '/transaction/handling-payment');



// Includes

require_once( MEMBERPAY_PLUGIN_DIR . 'includes/memberpay-check-requirements.php');

require_once( MEMBERPAY_PLUGIN_DIR . 'includes/memberpay-admin-notice-functions.php');

require_once( MEMBERPAY_PLUGIN_DIR . 'includes/memberpay-setting-page.php');

require_once( MEMBERPAY_PLUGIN_DIR . 'includes/memberpay-custom-functions.php');

require_once( MEMBERPAY_PLUGIN_DIR . 'includes/memberpay-generate-payment-link.php');

require_once( MEMBERPAY_PLUGIN_DIR . 'includes/memberpay-custom-columns-member-area.php');

require_once( MEMBERPAY_PLUGIN_DIR . 'includes/memberpay-auto-redirect-to-payment-link.php');

require_once( MEMBERPAY_PLUGIN_DIR . 'includes/memberpay-handling-after-payment.php');


/**

 * Activate the plugin.

 */

function memberpay_activate() { 

  // Do action here

}

register_activation_hook( __FILE__, 'memberpay_activate' );



/**

 * Deactivation hook.

 */

function memberpay_deactivate() {

	// Do action here

}

register_deactivation_hook( __FILE__, 'memberpay_deactivate' );