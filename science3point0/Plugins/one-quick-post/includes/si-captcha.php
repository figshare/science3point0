<?php
// this function adds the captcha to the comment form WP3
function si_captcha_oqp_form() {
    global $si_captcha_url, $si_captcha_opt;
	
	$si_image_captcha = new siCaptcha();

    // skip the captcha if user is logged in and the settings allow
    if (is_user_logged_in() && $si_captcha_opt['si_captcha_perm'] == 'true') {
       // skip the CAPTCHA display if the minimum capability is met
       if ( current_user_can( $si_captcha_opt['si_captcha_perm_level'] ) ) {
               // skip capthca
               return true;
       }
    }

// the captch html
// Test for some required things, print error message right here if not OK.
if ($si_image_captcha->si_captcha_check_requires()) {

  $si_aria_required = ($si_captcha_opt['si_captcha_aria_required'] == 'true') ? ' aria-required="true" ' : '';

// the captcha html - comment form
if (is_user_logged_in()) {
      echo '<br />';
}
echo '<p';
if ($si_captcha_opt['si_captcha_oqp_class'] != '') {
  echo ' class="'.$si_captcha_opt['si_captcha_oqp_class'].'"';
}
echo '><label for="captcha_code">';
echo ($si_captcha_opt['si_captcha_label_captcha'] != '') ? esc_html( $si_captcha_opt['si_captcha_label_captcha'] ) : esc_html( __('CAPTCHA Code', 'si-captcha'));
echo '</label><span class="required">*</span>
<input id="captcha_code" name="captcha_code" type="text" size="6" style="width:65px;" ' . $si_aria_required . ' /></p>';

echo '
<div style="width: 250px; height: 40px; padding-top:10px;">';
$si_image_captcha->si_captcha_captcha_html('si_image_oqp','oqp');
echo '</div>
<br />
';
}
    return true;
} // end function si_captcha_comment_form_wp3


function si_captcha_oqp_validate($redirect_url,$post) {
   global $bp, $si_captcha_path;
   
   //TO FIX check Si-CAPTCHA plugin, this should be uncommented ?
  //  if (!isset($_SESSION['securimage_code_si_oqp']) || empty($_SESSION['securimage_code_si_oqp'])) {
	//		bp_core_add_message(__('Could not read CAPTCHA cookie. Make sure you have cookies enabled and not blocking in your web browser settings. Or another plugin is conflicting. See plugin FAQ.', 'si-captcha'), 'error' );
		//	bp_core_redirect($redirect_url);
   //}else{
      if (empty($_POST['captcha_code']) || $_POST['captcha_code'] == '') {
			bp_core_add_message(__('Please complete the CAPTCHA.', 'si-captcha'), 'error' );
			bp_core_redirect($redirect_url);
      } else {
        $captcha_code = trim(strip_tags($_POST['captcha_code']));
      }
      require_once "$si_captcha_path/securimage.php";
      $img = new Securimage();
      $img->form_id = 'reg'; // makes compatible with multi-forms on same page
      $valid = $img->check("$captcha_code");
      // Check, that the right CAPTCHA password has been entered, display an error message otherwise.
      if($valid == true) {
          // ok can continue
      } else {
			bp_core_add_message(__('That CAPTCHA was incorrect. Make sure you have not disabled cookies.', 'si-captcha'), 'error' );
			bp_core_redirect($redirect_url);
      }
  // }
   return;
}


	add_action( 'oqp_creation_form_after_fields', 'si_captcha_oqp_form');
	add_action('oqp_save_post_validate', 'si_captcha_oqp_validate',10,2);

?>