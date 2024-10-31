<?php
/*
Plugin Name: Premise iDevAffiliate Integration
Plugin URI: http://www.eugenoprea.com/wordpress-plugins/premise-idevaffiliate-integration/?utm_source=wp_admin&utm_medium=plugin&utm_campaign=premise_idevaffiliate_integration
Description: Integrates Premise with iDevAffiliate to help you start your affiliate program. It passes the order details and if you activate Optional Variables it will also pass the customer name, email address and the purchased product to iDevAffiliate.
Version: 1.0.2
Author: Eugen Oprea
Author URI: http://www.eugenoprea.com/?utm_source=wp_admin&utm_medium=plugin&utm_campaign=premise_idevaffiliate_integration
License: GPLv2 or later
*/

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

class PremiseIDevAffiliate {

	private $options;

	function __construct() {
		$this->options = (array) get_option('premise-idevaffiliate-settings');

		if (isset($this->options['enabled']) && $this->options['enabled']) {
			add_action('premise_membership_create_order', array($this, 'track'), 10, 3);
		}

		add_action('init', array($this, 'admin_init'));
	}

	function admin_init() {
		if (is_admin())
			require dirname(__FILE__) .'/admin.php';
	}

	function track($member_id, $order_details, $renewal) {
		$product = get_post($order_details['_acp_order_product_id']);
		$customer = get_userdata($member_id);

		$url = trailingslashit($this->options['site_url']) . "sale.php";
		$url = add_query_arg(array(
			'profile' => $this->options['profile'],
			'idev_saleamt' => $order_details['_acp_order_price'] ? $order_details['_acp_order_price'] : 0,
			'idev_ordernum' => $order_details['_acp_order_time'],
			'ip_address' => $_SERVER['REMOTE_ADDR'],
		), $url);

		for ($i = 1; $i <= 3; $i++) {
			if (! isset($this->options['variable_' . $i]) || empty($this->options['variable_' . $i]))
				continue;

			switch ($this->options['variable_' . $i]) {
				case 'customer_name':
					$var = $customer->user_firstname . ' ' . $this->customer->user_lastname;
					break;
				case 'customer_username':
					$var = $customer->user_login;
					break;
				case 'customer_email':
					$var = $customer->user_email;
					break;
				case 'product_name':
					$var = $product->post_title;
					break;
				case 'product_id':
					$var = $product->ID;
					break;
				case 'site_name':
					$var = get_bloginfo('name');
					break;
				default:
					$var = apply_filters('premise_idevaffiliate_optional_variable_value', $var, $this->options['variable_' . $i]);
					break;
			}

			$url = add_query_arg(array(
				'idev_option_' . $i => $var
			), $url);
		}

		wp_remote_get($url);
	}
}

new PremiseIDevAffiliate;
