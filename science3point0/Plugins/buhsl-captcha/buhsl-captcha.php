<?php
/*
Plugin Name: buhsl-Captcha
Plugin URI: http://buhsl.com/wp-plugins/buhsl-captcha/
Description: Buhsl Captcha is a very simple, but powerful plug-in that helps you to prevent spam. Plug-in don&rsquo;t use COOKIES or SESSION. So there is no annoying messages like "You should enable cookies" for visitors. Plug-in use hashed captcha code value that will be comparing with entered captcha value and check if correspondent image file exists on server. Image of captcha created during request and stored at server.  When user entered right captcha value image file will be removed. If not, garbage collector will remove it after time life is passed.
Version: 1.0
Author: Gennadiy Bukhmatov
Author URI: http://buhsl.com
License: GPL2
*/

/*  Copyright 2009  Gennadiy Bukhmatov  (email : gennady@buhsl.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
set_include_path(dirname(__FILE__) . PATH_SEPARATOR . get_include_path());
if (!class_exists('evgCaptcha')) {
	require_once 'evg/evgCaptcha.php';
}
 
$evg_captcha= evgCaptcha::get();
wp_enqueue_script('prototype');
add_action('comment_form',array( &$evg_captcha, 'getForm'), 1);
add_action('wp_ajax_nopriv_evg_refresh_image', array( &$evg_captcha, 'ajaxUpdate'), 1);
add_filter('preprocess_comment', array(&$evg_captcha, 'processPost'), 1);
add_action('register_form', array( &$evg_captcha, 'registrationForm'), 1);
add_filter('registration_errors', array(&$evg_captcha, 'processRegistration'), 1);
add_action('admin_menu', array(&$evg_captcha,'addAdminMenu'),1);
add_filter( 'plugin_action_links', array(&$evg_captcha,'buhsl_captcha_plugin_action_links'),10,2);
  if ($evg_captcha->wordPressMultiUser) {
	add_action('bp_before_registration_submit_buttons', array( &$evg_captcha, 'bpSignupForm' ));
    add_action('bp_signup_validate', array( &$evg_captcha, 'bpSignupValidate' ));
    add_action('signup_extra_fields', array( &$evg_captcha, 'wpmuSignupForm' ));
	add_filter('wpmu_validate_user_signup', array( &$evg_captcha, 'wpmuSignupPost'));
  }

?>
