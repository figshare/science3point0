<?php
/*
Plugin Name: Sitewide newsletters
Description: Allows site administrators to send a newsletter to all users
Version: 0.3.2
Author: Chris Taylor
Author URI: http://www.stillbreathing.co.uk
Plugin URI: http://www.stillbreathing.co.uk/wordpress/sitewide-newsletter/
*/
// when the admin menu is built
add_action('admin_menu', 'sitewide_newsletters_add_admin');

// add the admin newsletters button
function sitewide_newsletters_add_admin() {
	global $current_user;
	if (version_compare(get_bloginfo('version'), "3") >= 0)	{
		add_submenu_page('ms-admin.php', 'Sitewide newsletters', 'Sitewide newsletters', 'edit_users', 'sitewide_newsletters', 'sitewide_newsletters');
	} else {
		add_submenu_page('wpmu-admin.php', 'Sitewide newsletters', 'Sitewide newsletters', 'edit_users', 'sitewide_newsletters', 'sitewide_newsletters');
	}
}

// build the newsletters form
function sitewide_newsletters()
{
	global $current_user, $wpdb;
	$users = $wpdb->get_var( "select count(user_email) from ".$wpdb->users." where user_activation_key = '' and spam = 0 and deleted = 0" );
	
	$message = "";
	
	$wpmums = "wpmu";
	if (version_compare(get_bloginfo('version'), "3") >= 0)	{
			$wpmums = "ms";
	}
	
	// if sending a newsletter
	if ( @$_POST["newsletter"] != "" && @$_POST["subject"] != "" && @$_POST["fromname"] != "" && @$_POST["fromemail"] != "" )
	{
		try {
	
			$newsletter = stripslashes( trim( $_POST["newsletter"] ) );
			$subject = stripslashes( trim( $_POST["subject"] ) );
			
			$message_headers = 'From: "' . addslashes($_POST["fromname"]) . '" <' . addslashes($_POST["fromemail"]) . '>' . "\r\n" .
			'Reply-To: ' . get_site_option("admin_email") . '' . "\r\n" .
			'X-Mailer: PHP/' . phpversion();
			
			$failed = "";
			$sent = 0;
			
			if (@$_POST["test"] == "")
			{
			
				$emails = $wpdb->get_results( "select user_email from ".$wpdb->users." where user_activation_key = '' and spam = 0 and deleted = 0" );
			
				foreach ($emails as $email)
				{
					try {
				
						$e = $email->user_email;
						if ( wp_mail( $e, $subject, $newsletter, $message_headers ) )
						{
							$sent++;
						} else {
							$failed .= $e . "\r\n";
						}
					
					} catch (Exception $e) {
		
						$failed .= "Error with " . $e . ": " . $e->getMessage() . "\r\n";
					
					}
				}
				
			} else {

				$email = get_site_option("admin_email");
			
				try {
				
					if ( wp_mail( $email, $subject, $newsletter, $message_headers ) ) {

						$sent++;
					} else {
						$failed .= "Error: test message could not be sent to " . $email . "\r\n";			
					}
					
				} catch (Exception $e) {

					$failed .= "Error sending to " . $email . ": " . $e->getMessage() . "\r\n";
				
				}
			
			}
			$message = "<p>Your message has been sent to " . $sent . " email addresses (" . $users . " users in total).</p>";
			if ($failed != "")
			{
				$message .= '<p>Failed addresses:</p><p><textarea cols="30" rows="12">' . $failed . '</textarea></p>';
			}
			
		} catch (Exception $e) {
		
			$message = "<p>An error was encountered: " . $e->getMessage() . "</p>";
		
		}
	}
	
	print '
	<div class="wrap">
	';
	sitewide_newsletters_wp_plugin_standard_header( "GBP", "Sitewide Newsletter", "Chris Taylor", "chris@stillbreathing.co.uk", "http://wordpress.org/extend/plugins/sitewide-newsletter/" );
	print '
	<h2>Sitewide Newsletter</h2>
	
	' . $message . '
	
	<p>Enter your newsletter below which will be emailed to ' . $users . ' users.</p>
	
	<form action="'.$wpmums.'-admin.php?page=sitewide_newsletters" method="post">
	
		<fieldset>
		
		<legend>Send a newsletter</legend>
		
		<p><label for="fromname" style="float: left;width: 15%;">From name</label><input type="text" name="fromname" id="fromname" style="width: 80%" value="' . get_site_option("site_name") . '" /></p>
		
		<p><label for="fromemail" style="float: left;width: 15%;">From email</label><input type="text" name="fromemail" id="fromemail" style="width: 80%" value="' . get_site_option("admin_email") . '" /></p>
			
		<p><label for="subject" style="float: left;width: 15%;">Subject</label><input type="text" name="subject" id="subject" style="width: 80%" /></p>
			
		<p><label for="newsletter" style="float: left;width: 15%;">Newsletter</label><textarea name="newsletter" id="newsletter" cols="30" rows="6" style="width: 80%"></textarea></p>
		
		<p><label for="subject" style="float: left;width: 15%;">Test newsletter</label><input type="checkbox" name="test" id="test" /> This will just send the newsletter to ' . get_site_option("admin_email") . '</p>
		
		<p><label for="send_sitewide_newsletter" style="float: left;width: 15%;">Send newsletter</label><input type="submit" name="send_sitewide_newsletter" id="send_sitewide_newsletter" value="Send newsletter" class="button" /></p>
		
		</fieldset>

	</form>
	';
	sitewide_newsletters_wp_plugin_standard_footer( "GBP", "Sitewide Newsletter", "Chris Taylor", "chris@stillbreathing.co.uk", "http://wordpress.org/extend/plugins/sitewide-newsletter/" );
	print '
	</div>
	';
}

// a standard header for your plugins, offers a PayPal donate button and link to a support page
function sitewide_newsletters_wp_plugin_standard_header( $currency = "", $plugin_name = "", $author_name = "", $paypal_address = "", $bugs_page ) {
	$r = "";
	$option = get_option( $plugin_name . " header" );
	if ( ( isset( $_GET[ "header" ] ) && $_GET[ "header" ] != "" ) || ( isset( $_GET[ "thankyou" ] ) && $_GET["thankyou"] == "true" ) ) {
		update_option( $plugin_name . " header", "hide" );
		$option = "hide";
	}
	if ( isset( $_GET["thankyou"] ) && $_GET["thankyou"] == "true" ) {
		$r .= '<div class="updated"><p>' . __( "Thank you for donating" ) . '</p></div>';
	}
	if ( $currency != "" && $plugin_name != "" && ( !isset( $_GET[ "header" ] ) || $_GET[ "header" ] != "hide" ) && $option != "hide" )
	{
		$r .= '<div class="updated">';
		$pageURL = 'http';
		if ( isset( $_SERVER["HTTPS"] ) && $_SERVER["HTTPS"] == "on" ) { $pageURL .= "s"; }
		$pageURL .= "://";
		if ( $_SERVER["SERVER_PORT"] != "80" ) {
			$pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
		} else {
			$pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
		}
		if ( strpos( $pageURL, "?") === false ) {
			$pageURL .= "?";
		} else {
			$pageURL .= "&";
		}
		$pageURL = htmlspecialchars( $pageURL );
		if ( $bugs_page != "" ) {
			$r .= '<p>' . sprintf ( __( 'To report bugs please visit <a href="%s">%s</a>.' ), $bugs_page, $bugs_page ) . '</p>';
		}
		if ( $paypal_address != "" && is_email( $paypal_address ) ) {
			$r .= '
			<form id="wp_plugin_standard_header_donate_form" action="https://www.paypal.com/cgi-bin/webscr" method="post">
			<input type="hidden" name="cmd" value="_donations" />
			<input type="hidden" name="item_name" value="Donation: ' . $plugin_name . '" />
			<input type="hidden" name="business" value="' . $paypal_address . '" />
			<input type="hidden" name="no_note" value="1" />
			<input type="hidden" name="no_shipping" value="1" />
			<input type="hidden" name="rm" value="1" />
			<input type="hidden" name="currency_code" value="' . $currency . '">
			<input type="hidden" name="return" value="' . $pageURL . 'thankyou=true" />
			<input type="hidden" name="bn" value="PP-DonationsBF:btn_donateCC_LG.gif:NonHosted" />
			<p>';
			if ( $author_name != "" ) {
				$r .= sprintf( __( 'If you found %1$s useful please consider donating to help %2$s to continue writing free Wordpress plugins.' ), $plugin_name, $author_name );
			} else {
				$r .= sprintf( __( 'If you found %s useful please consider donating.' ), $plugin_name );
			}
			$r .= '
			<p><input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donate_LG.gif" border="0" name="submit" alt="" /></p>
			</form>
			';
		}
		$r .= '<p><a href="' . $pageURL . 'header=hide" class="button">' . __( "Hide this" ) . '</a></p>';
		$r .= '</div>';
	}
	print $r;
}
function sitewide_newsletters_wp_plugin_standard_footer( $currency = "", $plugin_name = "", $author_name = "", $paypal_address = "", $bugs_page ) {
	$r = "";
	if ( $currency != "" && $plugin_name != "" )
	{
		$r .= '<form id="wp_plugin_standard_footer_donate_form" action="https://www.paypal.com/cgi-bin/webscr" method="post" style="clear:both;padding-top:50px;"><p>';
		$pageURL = 'http';
		if ( isset( $_SERVER["HTTPS"] ) && $_SERVER["HTTPS"] == "on" ) { $pageURL .= "s"; }
		$pageURL .= "://";
		if ( $_SERVER["SERVER_PORT"] != "80" ) {
			$pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
		} else {
			$pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
		}
		if ( strpos( $pageURL, "?") === false ) {
			$pageURL .= "?";
		} else {
			$pageURL .= "&";
		}
		$pageURL = htmlspecialchars( $pageURL );
		if ( $bugs_page != "" ) {
			$r .= sprintf ( __( '<a href="%s">Bugs</a>' ), $bugs_page );
		}
		if ( $paypal_address != "" && is_email( $paypal_address ) ) {
			$r .= '
			<input type="hidden" name="cmd" value="_donations" />
			<input type="hidden" name="item_name" value="Donation: ' . $plugin_name . '" />
			<input type="hidden" name="business" value="' . $paypal_address . '" />
			<input type="hidden" name="no_note" value="1" />
			<input type="hidden" name="no_shipping" value="1" />
			<input type="hidden" name="rm" value="1" />
			<input type="hidden" name="currency_code" value="' . $currency . '">
			<input type="hidden" name="return" value="' . $pageURL . 'thankyou=true" />
			<input type="hidden" name="bn" value="PP-DonationsBF:btn_donateCC_LG.gif:NonHosted" />
			<input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donate_SM.gif" border="0" name="submit" alt="' . __( "Donate" ) . ' ' . $plugin_name . '" />
			';
		}
		$r .= '</p></form>';
	}
	print $r;
}
?>