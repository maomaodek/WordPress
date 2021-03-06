<?php

function espresso_display_firstdata_connect_2($payment_data) {
	global $org_options;
	$firstdata_connect_2_settings = get_option('event_espresso_firstdata_connect_2_settings');
	$payment_data = apply_filters('filter_hook_espresso_prepare_payment_data_for_gateways', $payment_data);
	$payment_data = apply_filters('filter_hook_espresso_get_total_cost', $payment_data);
	require_once('Fdggutil.php');
	$fdggutil = new Fdggutil($firstdata_connect_2_settings['storename'],
									$firstdata_connect_2_settings['sharedSecret']);
	$fdggutil->set_timezone($firstdata_connect_2_settings['timezone']);
	$fdggutil->set_chargetotal($payment_data['total_cost']);
	$fdggutil->set_sandbox($firstdata_connect_2_settings['sandbox']);
	$fdggutil->set_returnUrl($org_options['notify_url']);
	$fdggutil->set_cancelUrl($org_options['notify_url']);
	$fdggutil->set_attendee_id($payment_data['attendee_id']);
	$fdggutil->set_dateTime();
	$button_url = $firstdata_connect_2_settings['button_url'];
	if (!empty($firstdata_connect_2_settings['bypass_payment_page']) && $firstdata_connect_2_settings['bypass_payment_page'] == 'Y') {
		echo $fdggutil->submitPayment();
	} else {
		echo $fdggutil->submitButton($button_url);
	}
}

add_action('action_hook_espresso_display_offsite_payment_gateway', 'espresso_display_firstdata_connect_2');