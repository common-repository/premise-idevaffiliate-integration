<?php

class PremiseIDevAffiliate_Admin_Boxes extends Premise_Admin_Boxes {

	function __construct() {
		$menu_options = array(
			'submenu' => array(
				'parent_slug' => 'premise-member',
				'page_title' => 'iDevAffiliate Settings',
				'menu_title' => 'iDevAffiliate',
				'capability' => 'manage_options',
			)
		);

		$default_settings = array(
			'enabled' => 0,
			'site_url' => '',
			'profile' => '',
			'variable_1' => '',
			'variable_2' => '',
			'variable_3' => '',
		);

		$this->create('premise-idevaffiliate', $menu_options, array(), 'premise-idevaffiliate-settings', $default_settings);

		add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_css'));
	}

	function enqueue_admin_css() {
		$screen = get_current_screen();

		if (! $screen || 'member-access_page_premise-idevaffiliate' != $screen->id)
			return;

		wp_enqueue_style( 'premise-admin', PREMISE_RESOURCES_URL . 'premise-admin.css', array( 'thickbox' ), PREMISE_VERSION );
	}

	function metaboxes() {
		add_meta_box('premise-idevaffiliate-settings', 'iDevAffiliate Settings', array($this, 'settings'), $this->pagehook, 'main');
	}

	function settings() {
		$optional_variables = array(
			'customer_name' => __('Customer Name'),
			'customer_email' => __('Customer Email'),
			'product_name' => __('Product Name'),
			'product_id' => __('Product ID'),
			'site_name' => __('Site Name'),
		);

		$optional_variables = apply_filters('premise_idevaffiliate_optional_variables', $optional_variables);
		?>

		<p>
			<label for="<?php echo $this->get_field_id('site_url'); ?>"><?php _e('iDevAffiliate Site URL'); ?></label><br>
			<input type="text" name="<?Php echo $this->get_field_name('site_url'); ?>" id="<?php echo $this->get_field_id('site_url'); ?>" value="<?php echo $this->get_field_value('site_url'); ?>">
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('enabled'); ?>"><?php _e('Enable'); ?> <input type="checkbox" name="<?php echo $this->get_field_name('enabled'); ?>" id="<?php echo $this->get_field_id('enabled'); ?>" value="1" <?php checked($this->get_field_value('enabled'), 1); ?>>
			</label>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('account_id'); ?>"><?php _e('Profile ID'); ?></label><br>
			<input type="text" name="<?php echo $this->get_field_name('profile'); ?>" id="<?php echo $this->get_field_id('profile'); ?>" value="<?php echo $this->get_field_value('profile'); ?>">
		</p>

		<?php for ($i = 1; $i <= 3; $i++) : ?>

			<p>
				<label for="<?php echo $this->get_field_id('variable_' . $i); ?>"><?php printf(__('Optional variable %d'), $i); ?></label>
				<select id="<?php echo $this->get_field_id('variable_' . $i); ?>" name="<?php echo $this->get_field_name('variable_' . $i); ?>">
					<option value=""><?php _e('None'); ?></option>

					<?php foreach ($optional_variables as $k => $v) : ?>
						<option value="<?php echo $k; ?>" <?php selected($this->get_field_value('variable_' . $i), $k); ?>><?php echo $v; ?></option>
					<?php endforeach; ?>
				</select>
			</p>

		<?php endfor;
	}
}

new PremiseIDevAffiliate_Admin_Boxes;
