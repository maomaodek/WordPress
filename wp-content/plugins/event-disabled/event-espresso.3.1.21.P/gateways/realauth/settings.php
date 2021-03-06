<?php

function event_espresso_realauth_payment_settings() {
	global $espresso_premium, $active_gateways;
	if (!$espresso_premium)
		return;
	if (isset($_POST['update_realauth'])) {
		$realauth_settings['merchant_id'] = $_POST['merchant_id'];
		$realauth_settings['shared_secret'] = $_POST['shared_secret'];
		$realauth_settings['currency_format'] = $_POST['currency_format'];
		$realauth_settings['auto_settle'] = $_POST['auto_settle'];
		$realauth_settings['button_url'] = $_POST['button_url'];
		$realauth_settings['use_sandbox'] = empty($_POST['use_sandbox']) ? false : true;
		$realauth_settings['bypass_payment_page'] = $_POST['bypass_payment_page'];
		if (update_option('event_espresso_realauth_settings', $realauth_settings) == true) {
			echo '<div id="message" class="updated fade"><p><strong>' . __('realauth settings saved.', 'event_espresso') . '</strong></p></div>';
		}
	}
	$realauth_settings = get_option('event_espresso_realauth_settings');
	if (empty($realauth_settings)) {
		if (file_exists(EVENT_ESPRESSO_GATEWAY_DIR . "/realauth/logo.gif")) {
			$button_url = EVENT_ESPRESSO_GATEWAY_URL . "/realauth/logo.gif";
		} else {
			$button_url = EVENT_ESPRESSO_PLUGINFULLURL . "gateways/realauth/logo.gif";
		}
		$realauth_settings['merchant_id'] = '';
		$realauth_settings['shared_secret'] = '';
		$realauth_settings['currency_format'] = 'USD';
		$realauth_settings['auto_settle'] = 'Y';
		$realauth_settings['button_url'] = $button_url;
		$realauth_settings['use_sandbox'] = false;
		$realauth_settings['bypass_payment_page'] = 'N';
		if (add_option('event_espresso_realauth_settings', $realauth_settings, '', 'no') == false) {
			update_option('event_espresso_realauth_settings', $realauth_settings);
		}
	}

	//Open or close the postbox div
	if (empty($_REQUEST['deactivate_realauth'])
					&& (!empty($_REQUEST['activate_realauth'])
					|| array_key_exists('realauth', $active_gateways))) {
		$postbox_style = '';
	} else {
		$postbox_style = 'closed';
	}
	?>

	<a name="realauth" id="realauth"></a>
	<div class="metabox-holder">
		<div class="postbox <?php echo $postbox_style; ?>">
			<div title="Click to toggle" class="handlediv"><br />
			</div>
			<h3 class="hndle">
				<?php _e('Realauth Settings', 'event_espresso'); ?>
			</h3>
			<div class="inside">
				<div class="padding">
					<?php
					if (!empty($_REQUEST['activate_realauth'])) {
						$active_gateways['realauth'] = dirname(__FILE__);
						update_option('event_espresso_active_gateways', $active_gateways);
					}
					if (!empty($_REQUEST['deactivate_realauth'])) {
						unset($active_gateways['realauth']);
						update_option('event_espresso_active_gateways', $active_gateways);
					}
					echo '<ul>';
					if (array_key_exists('realauth', $active_gateways)) {
						echo '<li id="deactivate_realauth" style="width:30%;" onclick="location.href=\'' . get_bloginfo('wpurl') . '/wp-admin/admin.php?page=payment_gateways&deactivate_realauth=true\';" class="red_alert pointer"><strong>' . __('Deactivate realauth Payments?', 'event_espresso') . '</strong></li>';
						event_espresso_display_realauth_settings();
					} else {
						echo '<li id="activate_realauth" style="width:30%;" onclick="location.href=\'' . get_bloginfo('wpurl') . '/wp-admin/admin.php?page=payment_gateways&activate_realauth=true#realauth\';" class="green_alert pointer"><strong>' . __('Activate realauth Payments?', 'event_espresso') . '</strong></li>';
					}
					echo '</ul>';
					?>
				</div>
			</div>
		</div>
	</div>
	<?php
}

//realauth Settings Form
function event_espresso_display_realauth_settings() {
	$realauth_settings = get_option('event_espresso_realauth_settings');

	$values = array(
			array('id' => 'Y', 'text' => __('Yes', 'event_espresso')),
			array('id' => 'N', 'text' => __('No', 'event_espresso')),
	);
	$uri = $_SERVER['REQUEST_URI'];
	$uri = substr("$uri", 0, strpos($uri, '&activate_realauth=true'));
	?>
	<form method="post" action="<?php echo $uri; ?>#realauth">
		<table class="form-table">
			<tbody>
				<tr>
					<th><label for="merchant_id">
							<?php _e('Merchant I.D.', 'event_espresso'); ?>
						</label></th>
					<td><input class="regular-text" type="text" name="merchant_id" id="merchant_id" size="35" value="<?php echo $realauth_settings['merchant_id']; ?>"></td>
				</tr>
				<tr>
					<th><label for="shared_secret">
							<?php _e('Shared Secret', 'event_espresso'); ?>
						</label></th>
					<td><input class="regular-text" type="text" name="shared_secret" id="shared_secret" size="35" value="<?php echo $realauth_settings['shared_secret']; ?>"></td>
				</tr>
				<tr>
					<th><label for="currency_format">
							<?php _e('Country Currency', 'event_espresso');
							echo apply_filters('espresso_help', 'realauth_currency_info'); ?>
						</label></th>
					<td><select name="currency_format" data-placeholder="Choose a currency..." class="chzn-select wide">
							<option value="<?php echo $realauth_settings['currency_format']; ?>"><?php echo $realauth_settings['currency_format']; ?></option>
							<option value="EUR">
								<?php _e('Euro', 'event_espresso'); ?>
							</option>
							<option value="GBP">
								<?php _e('Pound Sterling', 'event_espresso'); ?>
							</option>
							<option value="USD">
								<?php _e('U.S. Dollar', 'event_espresso'); ?>
							</option>
							<option value="SEK">
								<?php _e('Swedish Krona', 'event_espresso'); ?>
							</option>
							<option value="CHF">
								<?php _e('Swiss Franc', 'event_espresso'); ?>
							</option>
							<option value="HKD">
								<?php _e('Hong Kong Dollar', 'event_espresso'); ?>
							</option>
							<option value="JPY">
								<?php _e('Japanese Yen', 'event_espresso'); ?>
							</option>
						</select></td>
				</tr>
				<tr>
					<th><label for="bypass_payment_page">
							<?php _e('By-pass the payment confirmation page?', 'event_espresso'); ?>
							<?php echo apply_filters('espresso_help', 'bypass_confirmation'); ?>
						</label></th>
					<td><?php echo select_input('bypass_payment_page', $values, $realauth_settings['bypass_payment_page']); ?></td>
				</tr>
				<tr>
					<th><label for="auto_settle">
							<?php _e('Auto settle transactions', 'event_espresso'); ?>
							<?php echo apply_filters('espresso_help', 'auto_settle_info'); ?>
						</label></th>
					<td><?php echo select_input('auto_settle', $values, $realauth_settings['auto_settle']); ?></td>
				</tr>
				<tr>
					<th><label for="button_url">
							<?php _e('Button Image URL: ', 'event_espresso'); ?> <?php echo apply_filters('espresso_help', 'button_image') ?>
						</label></th>
					<td><input class="regular-text" type="text" name="button_url" size="34" value="<?php echo $realauth_settings['button_url']; ?>" />
						<a href="media-upload.php?post_id=0&amp;type=image&amp;TB_iframe=true&amp;width=640&amp;height=580&amp;rel=button_url" id="add_image" class="thickbox" title="Add an Image"><img src="images/media-button-image.gif" alt="Add an Image"></a></td>
				<tr>
					<td>
						<?php _e('Current Button Image:', 'event_espresso'); ?>
						<br />
						<?php echo '<img src="' . $realauth_settings['button_url'] . '" />'; ?></td>
				</tr>
				<tr>
					<td>
						<label for="use_sandbox">
								<?php _e('Use the debugging feature'); ?>
							</label>
							<input name="use_sandbox" type="checkbox" value="1" <?php echo $realauth_settings['use_sandbox'] ? 'checked="checked"' : '' ?> />
					</td>
				</tr>
			</tbody>
		</table>
		<?php /* ?><!-- TABLE TEMPLATE -->
		  <table class="form-table">
		  <tbody>
		  <tr>
		  <th> </th>
		  <td></td>
		  </tr>
		  <tr>
		  <th> </th>
		  <td></td>
		  </tr>
		  <tr>
		  <th> </th>
		  <td></td>
		  </tr>
		  </tbody>
		  </table><?php */ ?>
		<p>
			<input type="hidden" name="update_realauth" value="update_realauth">
			<input class="button-primary" type="submit" name="Submit" value="<?php _e('Update realauth Settings', 'event_espresso') ?>" id="save_realauth_settings" />
		</p>
		<?php wp_nonce_field('espresso_form_check', 'add_realauth_settings'); ?>
	</form>
	<div id="auto_settle_info" style="display:none">
		<h2>
			<?php _e('Realauth auto settle', 'event_espresso'); ?>
		</h2>
		<p>
			<?php _e('Used to signify whether or not you wish the transaction to be captured in the next batch or not. If set to “Y” and assuming the transaction is authorised then it will automatically be settled in the next batch. If set to “N” then the merchant must use the realcontrol application to manually settle the transaction. This option can be used if a merchant wishes to delay the payment until after the goods have been shipped. Transactions can be settled for up to 115% of the original amount.', 'event_espresso'); ?>
		</p>
	</div>
	<div id="realauth_currency_info" style="display:none">
		<h2>
			<?php _e('Realauth Currency', 'event_espresso'); ?>
		</h2>
		<p>
			<?php _e('Realauth uses 3-character ISO-4217 codes for specifying currencies in fields and variables. </p><p>The default currency code is US Dollars (USD). If you want to require or accept payments in other currencies, select the currency you wish to use. The dropdown lists all currencies that Realauth (currently) supports.', 'event_espresso'); ?>
		</p>
	</div>
	<?php
}

add_action('action_hook_espresso_display_gateway_settings','event_espresso_realauth_payment_settings');
