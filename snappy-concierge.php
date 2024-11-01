<?php
/**
 * Plugin Name: Snappy Concierge
 * Plugin URI: http://besnappy.com/concierge
 * Description: Snappy Concierge makes it easy for agencies and consultancies to organize ongoing change and support requests across multiple clients.
 * Version: 1.1
 * Author: Userscape, Inc.
 * Author URI: http://www.besnappy.com
 * License: GPL2
 */


// On install ...
register_activation_hook(__FILE__, 'on_sc_install');
function on_sc_install()
{
	// add_option('sc_widget_code', '');
}



// On deactivation/uninstall ...
register_deactivation_hook(__FILE__, 'on_sc_uninstall');
function on_sc_uninstall()
{
	// delete_option('sc_widget_code');
}


// Place Snappy widget code into the WP admin area.
add_action('admin_footer', 'snappy_widget_code');
function snappy_widget_code()
{
	$snappy_widget_code = trim(get_option('sc_widget_code'));
	if ( empty($snappy_widget_code) ) {
		return;
	}

	$current_user_info = wp_get_current_user(); 
	if ( $current_user_info )
	{
		$first_name = $current_user_info->user_firstname;
		$last_name = $current_user_info->user_lastname;
		$email = $current_user_info->user_email;
		$display_name = $current_user_info->display_name;

		// First name is good.
		$name = $first_name;
		if ( !empty($name) ) {
			// First name and last name is better.
			// Last name without first name is weird; skip that.
			$name .= " $last_name";
		}
		$name = trim($name);
		
		// But if not even the first name, we'll try the display name.
		if ( empty($name) ) {
			$name = $display_name;
		}

		if ( !empty($name) ) {
			$snappy_widget_code = str_replace("<script", "<script data-name='$name' ", $snappy_widget_code);		
		}

		if ( !empty($email) ) {
			$snappy_widget_code = str_replace("<script", "<script data-email='$email' ", $snappy_widget_code);
		}
	}

	echo $snappy_widget_code;
}




// Add menu item under the Settings menu.
add_action ( 'admin_menu', 'sc_admin_menu' );
function sc_admin_menu()
{
	add_options_page ( 'Snappy Concierge', 
			'Snappy Concierge', 
			'manage_options', 
			'snappy-concierge-wordpress-plugin', 
			'sc_settings_page' );

	add_action ( 'admin_init', 'sc_register_settings' );
}



function sc_settings_page()
{
	if ( !current_user_can('manage_options') ) {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}

	echo 
	'
		<div class="wrap">
			<h2>Snappy Concierge</h2>
			<p>
				Note: These settings are for your agency or software developer. 
				Generally, as a user of the site, you will not need to edit this screen.
			</p>
			<p>
				Paste the widget code from the <a href="https://app.besnappy.com/#widget">Snappy widget screen</a>.
			</p>
			<form method="post" action="options.php">
				' . wp_nonce_field('update-options') . '
				<textarea name="sc_widget_code" style="width:50%; height:200px;">' . get_option('sc_widget_code') . '</textarea>
				<p>
					You can find all the details on configuring the widgets title, colors, and more 
					here: <a href="https://help.besnappy.com/administrator-guide#widget-864">https://help.besnappy.com/administrator-guide#widget-864</a>
				</p>	
				<p>
					<input type="hidden" name="action" value="update" />
					<input type="hidden" name="page_options" value="sc_widget_code" />
					<input type="submit" class="button-primary" value="Save Changes" />
				</p>
			</form>		
		</div>
	';
}



function sc_register_settings()
{
	register_setting('sc_settings', 'sc_widget_code');
}

