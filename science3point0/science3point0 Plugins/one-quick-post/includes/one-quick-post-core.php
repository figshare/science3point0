<?php

//TO FIX :
//problem when using quotes in an input field.  Need to convert the content into html chars.
//missing taxonomies message must appear on page load, not only on submit



function oqp_wp() {
	$options = get_option('oqp_options');
	
	require_once( ONEQUICKPOST_PLUGIN_DIR . '/includes/form-template.php');
	require_once( ONEQUICKPOST_PLUGIN_DIR . '/includes/terms-template.php');
	
	require_once( ONEQUICKPOST_PLUGIN_DIR . '/includes/theme.php');
	if ((class_exists('siCaptcha')) && ($options['captcha'])) {
		require_once( ONEQUICKPOST_PLUGIN_DIR . '/includes/si-captcha.php');
	}
}
function oqp_admin() {
	if (!is_admin) return false;
	require_once( ONEQUICKPOST_PLUGIN_DIR . '/includes/admin-settings.php');
}


add_action('wp','oqp_wp');
add_action('init','oqp_admin');


if (!function_exists('bp_core_setup_message') && !is_admin()) {
//if ( !defined( 'BP_VERSION' ) && !did_action( 'bp_init' ) && !is_admin() ) {
	//loads duplicated core messages functions from BP if BP is not enabled (so we can use the same function with or without BP).
	//!is_admin to avoir having FATAL ERROR when activating BP after OQP.
	require_once( ONEQUICKPOST_PLUGIN_DIR . '/includes/bp-core-messages.php'); 
}
//require 'oqp-gallery.php';



class Oqp_Form {
	static $add_script;
	static $options;
	static $oqp_args;
	static $gallery;
 
	function init() {

		add_action('transition_post_status', array(__CLASS__,'transition_pending'), 10, 3);
		add_action('transition_post_status', array(__CLASS__,'transition_approved'), 10, 3);
		add_action('transition_post_status', array(__CLASS__,'transition_deleted'), 10, 3);
	
		if (is_admin()) return false;
	
		self::$options = get_option('oqp_options');
		
		if (self::$options['shortcode']) {
			add_shortcode('oqp_form', array(__CLASS__,'handle_shortcode'));
			add_filter('the_posts', array(__CLASS__,'conditionally_add_scripts_and_styles'));
		}

		add_action('wp_footer', array(__CLASS__,'footer_scripts'));
	
	}
	
	function handle_shortcode($atts) {
		global $post;
		
		$atts['form_id'] = $post->ID;
		
		self::oqp_block($atts);
	
	}
 
	function oqp_block($atts=false) {
	
		add_action('wp_print_styles', array(&$this,'add_styles'));

		global $blog_id;
		global $post;
		
		$default=array(
			'blog_id' => $blog_id,
			'blog_select'=>self::$options['blog_select'], //allow to select blog if user is member of multiple blogs
			'taxonomies'=>'category&post_tag', //array|string
			'post_type'	=> 'post',
			'pictures' => true,
			'gallery'=>true,
			'guest_posting'=>true,
			//when the form is outside a post, you need to specify those :
			'form_id'=>false, //unique ID | needed when not using a shortcode
			'form_url'=>$post->guid //needed when not using a shortcode
		);
		
		
		
		global $oqp_args;
		
		$oqp_args = wp_parse_args( $atts, $default);

		//tiny_mce
		$oqp_args['tiny_mce']=self::$options['tiny_mce'];

 		//only editors and upper roles can use this shortcode
		if ($post) {
			$author_id=$post->post_author;
			if (!oqp_user_can_for_blog('edit_others_posts',$author_id)) return false;
		} else {
			if (!$oqp_args['form_id']) { // if ($post); will be automatically the post ID
				_e('Your OQP form needs an unique form_id attribute','oqp');
				return false;
			}
			if (!$oqp_args['form_url']) { // if ($post); will be automatically the post ID
				_e('Your OQP form needs an unique form_url attribute','oqp');
				return false;
			}
		}

		//taxonomies

		
		if ((!is_array($oqp_args['taxonomies'])) && (!empty($oqp_args['taxonomies']))) { //args are passed as a string; we have to transfrom this into an array
			$oqp_args['taxonomies']=htmlspecialchars_decode($oqp_args['taxonomies']); //eventually converts &amp; to &
			$oqp_args['taxonomies']=explode('&',$oqp_args['taxonomies']);

			foreach ($oqp_args['taxonomies'] as $key=>$taxonomy) {
				//split each arg
				$tax_settings_args=explode('|',$taxonomy);
				
				$tax_slug=$tax_settings_args[0];
				unset($tax_settings_args[0]);

				//split arg slug VS value
				foreach ($tax_settings_args as $tax_settings_arg_str) {
					$tax_settings_arg_arr=explode('=',$tax_settings_arg_str);
					$tax_settings_arg_slug=$tax_settings_arg_arr[0];
					$tax_settings_arg_values=$tax_settings_arg_arr[1];
					
					//split several values
					$tax_settings_arg_values=explode(',',$tax_settings_arg_values);
					
					if (count($tax_settings_arg_values)==1) //we don't need an array as there is only one value
						$tax_settings_arg_values=$tax_settings_arg_values[0];
						
					$new_taxonomy[$tax_settings_arg_slug]=$tax_settings_arg_values;
					
					

				}

				$new_taxonomies[$tax_slug]=$new_taxonomy;

			}
			$oqp_args['taxonomies']=$new_taxonomies;

		}


		//? LOAD SCRIPTS

		if (!empty($oqp_args['taxonomies'])) {
		
			foreach($oqp_args['taxonomies'] as $tax_slug=>$tax_settings) {
			
				if (($tax_settings['hierarchical']) || (oqp_taxonomy_is_hierarchical($tax_slug))) {
					self::$add_script['tree'] = true;
				} else {
					self::$add_script['autocomplete'] = true;
				}

			}
		}

		
		self::$oqp_args = $oqp_args;

		if ($oqp_args['tiny_mce'])
			self::$add_script['tiny_mce'] = true;
			
		if ($oqp_args['pictures'])
			self::$add_script['pictures'] = true;

		if (!oqp_is_multiste()) 
			unset ($oqp_args['blog_id']);
			
		//GENERATE FORM
		do_action('oqp_before_template');
		
		$form_file = apply_filters('oqp_get_template_form','oqp/form.php');
		
		echo oqp_get_template_html($form_file);
		do_action('oqp_after_template');
	}
	
	function conditionally_add_scripts_and_styles($posts){
		if (empty($posts)) return $posts;
		
		$shortcode_found = false; // use this flag to see if styles and scripts need to be enqueued
		foreach ($posts as $post) {
			if (stripos($post->post_content, 'oqp_form')) {
				$shortcode_found = true; // bingo!
				break;
			}
		}
	 
		if ($shortcode_found) {
			self::enqueue_styles();
			//wp_enqueue_script('my-script', '/script.js');
		}
	 
		return $posts;
	}
	
	function enqueue_styles() {
		//TO FIX files from theme ?
		
		if (!is_admin()) {
		
			wp_enqueue_style('oqp', ONEQUICKPOST_PLUGIN_URL.'/_inc/css/style.css');
			
			wp_enqueue_style('jquery.collapsibleCheckboxTree', ONEQUICKPOST_PLUGIN_URL.'/themes/oqp/_inc/css/jquery.collapsibleCheckboxTree.css');
		
			wp_enqueue_style('oqp-autocomplete', ONEQUICKPOST_PLUGIN_URL.'/_inc/js/jquery-autocomplete/jquery.autocomplete.css');
		}
	}
 
	function footer_scripts() {
		if (is_feed()) return false;
		if (is_admin()) return false;
		
		//not in function enqueue_scripts because depends of the shortcode args

		if (self::$add_script['tree'] ) {
			wp_register_script( 'jquery.collapsibleCheckboxTree', ONEQUICKPOST_PLUGIN_URL.'/_inc/js/jquery.collapsibleCheckboxTree.js',array('jquery'), '1.0.1' );
			wp_print_scripts('jquery.collapsibleCheckboxTree');
		}
		if (self::$add_script['autocomplete'] ) {
			wp_register_script( 'jquery.autocomplete', ONEQUICKPOST_PLUGIN_URL.'/_inc/js/jquery-autocomplete/jquery.autocomplete.pack.js',array('jquery'), '1.1' );
			wp_print_scripts('jquery.autocomplete');
		}
		if (self::$add_script['pictures'] ) {
			//self::$gallery->enqueue_scripts();
			//self::$gallery->footer_scripts();
		}
		
		
		if (self::$add_script['tiny_mce'] ) {
			//TO FIX to check
			//tiny_mce
			wp_register_script( 'tiny_mce', get_bloginfo('wpurl').'/'.WPINC.'/js/tinymce/tiny_mce.js');
			wp_print_scripts('tiny_mce');
			//tiny_mce lang
			wp_register_script( 'tiny_mce_lang', get_bloginfo('wpurl').'/'.WPINC.'/js/tinymce/langs/wp-langs-en.js');
			wp_print_scripts('tiny_mce_lang');
			?>
			<script type="text/javascript">
			//<![CDATA[
			tinyMCE.init({
				mode : "exact",
				elements : "oqp_desc",
				language : "en",
				theme : "advanced",
				theme_advanced_buttons1 : "formatselect,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,bullist,numlist,|,blockquote,outdent,indent,hr,|,link,unlink",
				theme_advanced_buttons2 : "undo,redo,|,removeformat",
				theme_advanced_buttons3 : "",
				theme_advanced_toolbar_location : "top",
				theme_advanced_toolbar_align : "left"
			});
			//]]>
			</script>
			<?php
		}
		
		if (!empty(self::$add_script)) {
			wp_register_script( 'oqp', ONEQUICKPOST_PLUGIN_URL.'/_inc/js/oqp.js',array('jquery'), ONEQUICKPOST_VERSION );
			wp_print_scripts('oqp');
		};
		
		//MOVE THIS ELSEWHERE
		/*
		
			<script type="text/javascript">
			//<![CDATA[
				jQuery(document).ready( function() {
					jQuery('.oqp-form ul.expandable').expandableTree();
				});
			//]]>
			</script>
		*/

	}
	

	//send the author an email if a  OQP post is pending
	function transition_pending($new_status, $old_status, $post) {

		if ($new_status!='pending') return false;
		//if ($old_status!='trash') return false;

		$is_oqp_post = get_post_meta($post->ID, 'oqp_post_from',true);
		
		if (!$is_oqp_post) return false;
		
		$do_notification = apply_filters('oqp_do_transition_notification_pending',true,$post);
		
		if (!$do_notification) return false;

		require_once( ONEQUICKPOST_PLUGIN_DIR . '/includes/notifications.php');
		
		oqp_notification_post_pending($post);
		
		do_action('oqp_transition_pending',$post);
		
	}
	
	//send the author an email if a pending OQP post is approved
	function transition_approved($new_status, $old_status, $post) {
	
		if ($new_status!='publish') return false;
		if ($old_status=='publish') return false; //it is an update
		
		$is_oqp_post = get_post_meta($post->ID, 'oqp_post_from',true);
		
		if (!$is_oqp_post) return false;
		
		$do_notification = apply_filters('oqp_do_transition_notification_approved',true,$post);
		
		if (!$do_notification) return false;

		require_once( ONEQUICKPOST_PLUGIN_DIR . '/includes/notifications.php');

		oqp_notification_post_approved($post);
		
		do_action('oqp_transition_approved',$post);
		
	}

	//send the author an email if an OQP post is trashed
	function transition_deleted($new_status, $old_status, $post) {

		if ($new_status!='trash') return false;
		if (($old_status!='pending') && ($old_status!='publish')) return false;
		
		$is_oqp_post = get_post_meta($post->ID, 'oqp_post_from',true);
		
		if (!$is_oqp_post) return false;
		
		$do_notification = apply_filters('oqp_do_transition_notification_deleted',true,$post);
		
		if (!$do_notification) return false;

		require_once( ONEQUICKPOST_PLUGIN_DIR . '/includes/notifications.php');

		oqp_notification_post_deleted($post);
		
		do_action('oqp_transition_deleted',$post);
		
	}


}
 
Oqp_Form::init();

//if the poster is a guest; filter guest email
function oqp_notification_mail_to($email,$post) {


	$dummy=oqp_get_dummy_user();
	if (!oqp_user_is_dummy($post->post_author)) return $email;

	$email = get_post_meta($post->ID,'oqp_guest_email', true);

	return $email;
}

function oqp_js_pictures($the_blog_id,$form_id,$post_id) {
	global $blog_id;
	
	if (($the_blog_id) && ($the_blog_id!=$blog_id))
		$siteurl = get_blog_option($the_blog_id,'siteurl');
	else
		$siteurl = get_option('siteurl');


	$js[] = '<script type="text/javascript">';
		$js[] = 'var oqp_form_'.$form_id.'_js=new Object();';
		$js[] = 'oqp_form_'.$form_id.'_js.tb_pathToImage="'.$siteurl.'/wp-includes/js/thickbox/loadingAnimation.gif";';
		$js[] = 'oqp_form_'.$form_id.'_js.tb_closeImage="'.$siteurl.'/wp-includes/js/thickbox/tb-close.png";';
	$js[] = '</script>';
	return implode("\n",$js);
}

function oqp_js_autocomplete($the_blog_id,$form_id,$form_field_id,$taxonomy_name) {
	global $blog_id;
	
	if (($the_blog_id) && ($the_blog_id!=$blog_id))
		$wpurl = get_blog_option($the_blog_id,'siteurl');
	else
		$wpurl = get_bloginfo('wpurl');


	$js[] = '<script type="text/javascript">';
	$js[] = '//<![CDATA[';
	$js[] = 'jQuery(document).ready( function() {';
	$js[] = '	jQuery("#'.$form_id.' #'.$form_field_id.'").autocomplete("'.$wpurl.'/wp-admin/admin-ajax.php?action=ajax-tag-search&tax='.$taxonomy_name.'", {';
	$js[] = '		width: jQuery(this).width,';
	$js[] = '		multiple: true,';
	$js[] = '		matchContains: true,';
	$js[] = '		minChars: 3,';
	$js[] = '	});';
	$js[] = '});';
	$js[] = '//]]>';
	$js[] = '</script>';
	return implode("\n",$js);
}

function oqp_get_blogs_of_user($user_id) {

	$blogs = get_blogs_of_user($user_id);
	
	return $blogs;

}

function oqp_is_multiste() {

	if ( function_exists( 'is_multisite' ) )
		return is_multisite();

	if ( !function_exists( 'wpmu_signup_blog' ) )
		return false;

	return true;
}

function oqp_get_dummy_user() {
	$options=get_option('oqp_options');
	$user_id = $options['guest_poster'];
	
	if (!$user_id) return false;
	
	$user = new WP_User($user_id);

	return $user;
}

function oqp_user_is_dummy($user_id) {
	$dummy = oqp_get_dummy_user();
	
	if (!$dummy) return false;
	
	if ($dummy->ID==$user_id) return true;
	
	return false;
	
}

function oqp_post_get_guest_name($post_id) {
	return get_post_meta($post_id,'oqp_guest_name', true);
}
function oqp_post_get_guest_email($post_id) {
	return get_post_meta($post_id,'oqp_guest_email', true);
}

function oqp_user_can_for_blog($cap,$user_id=false,$blog_id=false) {
	global $current_user;

	if ((!$user_id) || ($user_id==$current_user->id)) {
		$user=$current_user;
	}else {
		$user = new WP_User($user_id);
	}

	//TO FIX TO CHECK
	//if ((oqp_is_multiste()) && ($blog_id)) {
		//return $user->has_cap($cap);
	//}else {
		return $user->has_cap($cap);
	//}


}

function oqp_get_post_tags($blog_id=false) {
	global $post;
	
	if (oqp_is_multiste())
		switch_to_blog($blog_id);
		
	$post_tags = wp_get_post_tags($post->ID);
	
	if (oqp_is_multiste())
		restore_current_blog();
	
	return $post_tags;
}

function oqp_taxonomy_is_hierarchical($tax_slug) {
	$tax_obj = get_taxonomy( $tax_slug);
	return (bool)$tax_obj->hierarchical;
}

function oqp_save_post() {
	global $oqp_args;
	global $blog_id;
	
	if ($_POST['oqp-form-id']!=$oqp_args['form_id']) return false; //be sure we handle the good form (if there are several OQP forms on the page)
	if ($_POST['oqp-switch-blog-id']) return false; //only switching the blog
	if ($_POST['oqp-action']!='oqp-save') return false;

	
	//post id - (for edition)
	$edit_post_id = $_POST['oqp-post-id'];

	
	//get plugin options
	$options=get_option('oqp_options');

	//USER ID
	global $current_user;	
	$user_id=$current_user->id;
	
	//SWITCH BLOG before saving if needed
	if (oqp_is_multiste()) {
	
		if ($_REQUEST['oqp-blog-id']) { //form posted
			$oqp_blog_id=$_REQUEST['oqp-blog-id'];
		}else {
			$oqp_blog_id=$oqp_args['blog_id'];
		}
			
		if ($blog_id==$oqp_blog_id)
			unset ($oqp_blog_id);
		
			
		switch_to_blog($oqp_blog_id);
		
	}
	
	//form url
	$form_url = oqp_url_add_args($oqp_args['form_url'],array('oqp-blog-id'=>$oqp_blog_id));

	//edit url
	if ($edit_post_id) {
		$edit_post_url = oqp_post_get_edit_link($edit_post_id,$_REQUEST['oqp-key'],$oqp_blog_id);
	}else{
		$edit_post_url = $form_url;
	}
	
	$edit_post_url = apply_filters('oqp_save_post_edit_post_url',$edit_post_url,$edit_post_id,$_REQUEST['oqp-key'],$blog_id);


	if (!$user_id) { //user is not logged, check if guest posting is enabled
		$dummy=oqp_get_dummy_user();

		
		if (($dummy) && oqp_user_can_for_blog('edit_posts',$dummy->ID,$oqp_blog_id)) {
			$user_id=$dummy->ID;
			$dummy_name = $_POST['oqp_dummy_name'];
			$dummy_email = $_POST['oqp_dummy_email'];
		}else {

			bp_core_add_message(__('You are not allowed to post without being logged.','oqp'), 'error' );
			bp_core_redirect($form_url);
		}
	}

	
	//TITLE + DESC
	$sent_title = trim($_POST['oqp_title']);
	$sent_desc = trim($_POST['oqp_desc']);

	if ((!$sent_title) || (!$sent_desc)) {
		bp_core_add_message(__('Please enter a title and a description','oqp'), 'error' );

			
		bp_core_redirect( $edit_post_url );
	}
	
	//GUEST USER
	if ($dummy) {
		$sent_dummy_name = trim($_POST['oqp_dummy_name']);
		$sent_dummy_email = trim($_POST['oqp_dummy_email']);
		
		if (!$sent_dummy_name)
			$missing_datas_msgs[]=__('Please enter your name','oqp');
		
		if (!is_email($sent_dummy_email))
			$missing_datas_msgs[]=__('Please enter a valid email address.');
	}
	
	//TAXONOMIES
	if (!empty($oqp_args['taxonomies'])) {
		foreach ($oqp_args['taxonomies'] as $tax_slug=>$tax_settings) {
			
			unset($value);
			
			$value = $_POST['oqp_'.$tax_slug];
			
			//check if the taxonomy value can be empty
			//TO FIX can be empty if no taxonomies existing (hierarchical) ?
			if (!$value) {
				if ($tax_settings['required']) {
					$tax_obj=get_taxonomy($tax_slug);
					$missing_datas_msgs[]=sprintf(__('You have to choose a value for the %s','oqp'),''.$tax_obj->label.'');
				}
			}
			
				
			$taxonomies[$tax_slug]['selected']=$value;

		}
	}


	//POST STATUS
	//first, save as draft.
	//we'll publish it once the metas are saved
	//so we can have the metas to be used in the transition hooks

	if ((oqp_user_can_for_blog('publish_posts',$user_id,$oqp_blog_id)) && (!$dummy))
		$post_status_final='publish';
	else
		$post_status_final='pending';
		
	$post_status_final=apply_filters('oqp_save_post_post_status_final',$post_status_final);

	if ((!$edit_post_id) || (!empty($missing_datas_msgs))) { //new post | not valid
		$post_status='draft';
	}else {
		$post_status = $post_status_final;
	}

	$post_status=apply_filters('oqp_save_post_post_status',$post_status);

	//

	$post = array(
		'post_author'	=> $user_id,
		'post_status'	=> $post_status,
		'post_title'	=> $sent_title,
		'post_content'	=> $sent_desc,
		'post_type'		=> $oqp_args['post_type']
	);

	//check we can edit the post
	if ($edit_post_id) {
		$edit_post=oqp_get_the_post($edit_post_id,$oqp_blog_id); //check the post exists
		
		if ((!$edit_post->ID) || ($edit_post->post_type!=$oqp_args['post_type'])) {
			bp_core_add_message(__('Error while trying to get this post','oqp'), 'error' );
			bp_core_redirect($edit_post_url);	
		}
		
		$post['ID'] = $edit_post->ID;
	}
	
	do_action('oqp_save_post_validate',$edit_post_url,$post);


	//SAVE the post
	if ($edit_post) {
		$saved_post_id = wp_update_post( $post );		
	}else{ // new post
		$saved_post_id = wp_insert_post( $post );
		
		update_post_meta($saved_post_id, 'oqp_post_from', $oqp_args['form_url']);
		if ($dummy) { 
			//generate a key to allow guest to edit their posts (mail link)
			$key = substr( md5( uniqid( microtime() ) ), 0, 8);
			update_post_meta($saved_post_id, 'oqp_guest_key', $key);
		}
	}
	
	if (!$saved_post_id) {
		bp_core_add_message(__('Error while trying to save this post','oqp'), 'error' );
		bp_core_redirect($edit_post_url);
	}

	$edit_post_url = oqp_post_get_edit_link($saved_post_id,$key,$oqp_blog_id);
	$edit_post_url = apply_filters('oqp_save_post_edit_post_url',$edit_post_url,$saved_post_id,$key,$oqp_blog_id);

	//TO FIX : only if datas are different
	//save guest name + email
	if ($dummy) {
		update_post_meta($saved_post_id, 'oqp_guest_name', $dummy_name);
		update_post_meta($saved_post_id, 'oqp_guest_email', $dummy_email);
	}

	//now the post metas have been saved; 
	//re-update the post so we can hook our notifications functions
	$resave_post['ID']=$saved_post_id;
	$resave_post['post_status']=$post_status_final;

	wp_update_post( $resave_post );

	
	//SAVE TAXONOMIES
	if ($taxonomies) {
		foreach($taxonomies as $tax_slug=>$tax_settings) {

			if ((!$tax_settings['selected']) && (!$edit_post_id)) continue; //new post & tax has no value

			$taxonomy_value=apply_filters('oqp_save_post_taxonomy_'.$tax_slug,$tax_settings['selected']);

			wp_set_post_terms( $saved_post_id,$taxonomy_value, $tax_slug);
		}
	}
	
	
	//post datas incomplete
	if (!empty($missing_datas_msgs)) {
		bp_core_add_message($missing_datas_msgs[0], 'error' );
		bp_core_redirect($edit_post_url);
	}
	
	//RETRIEVE FRESHLY SAVED POST INFO
	$oqp_post=get_post($saved_post_id); //retrieve saved information

	do_action('oqp_saved_post',$oqp_post, $edit_post->post_status);
	
	//REDIRECT TO FORM EDITION

	$message_saved = sprintf(__('Your post %s has been saved.','oqp'),'"'.$oqp_post->post_title.'"');
	if ($oqp_post->post_status=='pending')
	$message_saved .= __('It is now awaiting moderation.','oqp');
	
	bp_core_add_message($message_saved);

	bp_core_redirect($edit_post_url);
	
	//we don't need to restore the blog as we redirect


}

function oqp_get_the_post($post_id,$blog_id=false) {
	if ((oqp_is_multiste()) && ($blog_id)) {
		
		switch_to_blog($blog_id);
		
		$the_post=get_post($post_id);
		
		restore_current_blog();
		
	}else {
		$the_post=get_post($post_id);
	}
	return $the_post;	
}

function oqp_user_can_edit_the_post($post_id,$user_id=false,$oqp_key=false,$blog_id=false) {//user_id must not be the dummy user

	if (!$post_id) return false;

	$oqp_post=oqp_get_the_post($post_id,$blog_id);
	
	if (!$oqp_post) return false;

	if ($user_id) { //user is not a guest
		if ($oqp_post->post_author==$user_id) { //user is the post author 
			return true;
		} else { //user is not the post author 
				
			if (oqp_user_can_for_blog('edit_others_posts',$user_id,$blog_id)) {
				return true;
			}
		}
	}else { //user is a guest
		if ($oqp_key) { //there is a key in the URL
			//TO FIX TO CHECK SWITCH BLOG ?
			$post_key = get_post_meta($post_id,'oqp_guest_key', true);

			if ($post_key==$oqp_key) {
				return true;
			}
		}
	}
	
	return false;
	
}

function oqp_url_add_args($url,$args) {
	//check we have vars
	$url_split=explode('?',$url);
	
	if (count($url_split)>1) { //url = /?...
		$separator='&';
	} else {
		$url = rtrim($url, " /"); //remove trailing slash if any
		$separator='/?';
	}
	
		
		
	$link = $url;
	
	if ($args) {
		$ref_args_str=http_build_query($args);
		$link.=$separator.$ref_args_str;
	}
	
	return $link;
}

function oqp_post_get_link($post_id,$blog_id=false) {

	$the_post = oqp_get_the_post($post_id,$blog_id);

	
	$link = $the_post->guid;
	
	return apply_filters('oqp_post_get_link',$link,$the_post,$args);
}

function oqp_post_get_edit_link($post_id,$key=false,$oqp_blog_id=false) {

	$the_post = oqp_get_the_post($post_id,$oqp_blog_id);

	$oqp_from_url = get_post_meta($the_post->ID, 'oqp_post_from',true);
	
	if (!$key)
		$key = get_post_meta($the_post->ID,'oqp_guest_key', true);
	
	if (!$oqp_from_url) {
		$url = get_edit_post_link( $post_id );
	}else {
		$args['oqp-action']='edit';
		$args['oqp-post-id']=$post_id;
		
		if ($oqp_blog_id)
			$args['oqp-blog-id']=$oqp_blog_id;
		
		if (oqp_user_is_dummy($the_post->post_author)) {
			if ($key)
				$args['oqp-key']=$key;
		}
		
		$url = oqp_url_add_args($oqp_from_url,$args);
	}
	
	return apply_filters('oqp_post_get_edit_link',$url,$post,$args);
}

function oqp_post_get_delete_link($post_id,$key=false,$oqp_blog_id=false) {

	$the_post = oqp_get_the_post($post_id,$blog_id);

	$oqp_from_url = get_post_meta($the_post->ID, 'oqp_post_from',true);
	
	if (!$key)
		$key = get_post_meta($the_post->ID,'oqp_guest_key', true);
	
	if (!$oqp_from_url) {
		$url = get_delete_post_link( $post_id );
	}else {
		$args['oqp-action']='delete';
		$args['oqp-post-id']=$post_id;
		
		if ($oqp_blog_id)
			$args['oqp-blog-id']=$oqp_blog_id;
		
		if (oqp_user_is_dummy($the_post->post_author)) {
			if ($key)
				$args['oqp-key']=$key;
		}
		
		$url = oqp_url_add_args($oqp_from_url,$args);
	}
	
	return apply_filters('oqp_post_get_delete_link',$url,$post,$args);
}
/*		
//TO FIX TO REMOVE ?
function oqp_get_post_permalink($post_id,$type='view',$blog_id=false) {

	global $post;
	global $current_user;

	if ((oqp_is_multiste()) && ($blog_id))
		switch_to_blog($blog_id);

	$the_post = oqp_get_the_post($post_id,$blog_id);
	
	if ($type=='view') {
			$permalink = $the_post->guid;
			return $permalink;
	}else if($type=='edit_backend') {
		//needed ?
	}else {
		if ($post) { //the form is inside a post
			$ref=$post->guid;
		}else { //the post is inside a template
			$request_host = $_SERVER['HTTP_HOST'];
			$request_uri = $_SERVER['REQUEST_URI'];
			//TO FIX works only with http ?
			$ref='http://'.$request_host.$request_uri;
		}

		$ref_arr = explode('?',$ref);
		$ref_url=$ref_arr[0];
		parse_str($ref_arr[1],$ref_args_arr);
		//
		$ref_args_arr['oqp-post-id']=$post_id;
		$ref_args_arr['oqp-blog-id']=$blog_id;
		
		if (!$current_user->ID)
			$ref_args_arr['oqp-key'] = get_post_meta($the_post->ID,'oqp_guest_key', true);
		
		if($type=='edit_frontend')  {
			$ref_args_arr['oqp-action']='edit';		
		}else if ($type=='saved_frontend')  {
			$ref_args_arr['oqp-action']='saved';		
		}else if ($type=='updated_frontend')  {
			$ref_args_arr['oqp-action']='updated';		
		}
		
		$ref_args_str=http_build_query($ref_args_arr);
		$permalink = $ref_url.'?'.$ref_args_str;

	}

	$permalink = apply_filters('oqp_get_post_permalink',$permalink,$type,$the_post,$ref_args_arr['oqp-key']);

	if ((oqp_is_multiste()) && ($blog_id))
		restore_current_blog();

	return $permalink;
}


function oqp_send_email($post_status,$post_id,$blog_id=false) {
	global $current_user;
	$post=oqp_get_the_post($post_id);
	$post_title=$post->post_title;
	$post_author=$post->post_author;

	$permalink_view=oqp_get_post_permalink($post_id,'view',$blog_id);
	$permalink_edit_frontend=oqp_get_post_permalink($post_id,'edit_frontend',$blog_id);
	$permalink_delete_frontend=oqp_get_post_permalink($post_id,'delete_frontend',$blog_id);

	if ($current_user->id) {
		$user_name = $current_user->user_nicename;
		$user_email = $current_user->user_email;
	}else {
		$user_name = get_post_meta($post->ID,'oqp_guest_name', true);
		$user_email = get_post_meta($post->ID,'oqp_guest_email', true);
	}
	
	$mail_texts = array(
		'pending_title'=>__('Your post "%s" is awaiting moderation !','oqp'),
		'pending_text'=>__('Your post "%1s" will be read and published by a moderator from "%2s" as soon as possible.','oqp'),
		'pending_confirmation'=>__('You will receive a confirmation email once it will be done.','oqp'),
		'publish_title'=>__('Your post "%s" is now published !','oqp'),
		'publish_text'=>__('Your post "%1s" has been published on our website "%2s".','oqp'),
		'publish_link'=>__('Here\'s the link where you can view it : %s','oqp'),
		'edit_link'=>__('If you want to edit this post, click this link : %s.','oqp'),
		'edit_warn_published'=>__('Once it is published, you will not be able to edit it anymore.','oqp'),
		'delete_link'=>__('If you want to delete this post, click this link : %s.','oqp'),
		'delete_warn_published'=>__('Once it is published, you will not be able to delete it anymore.','oqp')
	);
	$mail_texts = apply_filters('oqp_send_emails_texts',$mail_texts,$post);
	
	switch ($post_status) {
		case 'pending':
			$subject=sprintf($mail_texts['pending_title'],$post_title);
			$body[]=sprintf($mail_texts['pending_text'],$post_title,get_bloginfo('name'));
			$body[]=$mail_texts['pending_confirmation'];
		break;
		case 'publish':
			$subject=sprintf($mail_texts['publish_title'],$post_title);
			$body[]=sprintf($mail_texts['publish_text'],$post_title,get_bloginfo('name'));
			$body[]=sprintf($mail_texts['publish_link'],$permalink_view);
		break;
	}

	//edit link
	$body['edit-link']=sprintf($mail_texts['edit_link'],$permalink_edit_frontend);

		if (!oqp_user_can_for_blog('edit_published_posts',$post_author))
			$body['edit-link'].="  ".$mail_texts['edit_warn_published'];
		
	//delete link
	$body['delete-link']=sprintf($mail_texts['delete_link'],$permalink_delete_frontend);

		if (!oqp_user_can_for_blog('delete_published_posts',$post_author)) 
			$body['delete-link'].="  ".$mail_texts['delete_warn_published'];
	
	if (is_array($body))
		$body = implode("\n\n",$body);

	wp_mail($user_email, $subject, $body);

}
*/


//filtering guest poster info

function oqp_guest_the_author($display_name) {
	global $authordata;

	if (!oqp_user_is_dummy($authordata->ID)) return $display_name;
	
	global $post;
	
	$guest_name=oqp_post_get_guest_name($post->ID);
	
	return $guest_name.' ('.__('Guest','oqp').')';

}

add_filter('the_author', 'oqp_guest_the_author');

//filters author link
//adds a variable to be able to filter guest posts loop based on guest email
function oqp_author_link($link, $author_id, $author_nicename) {
	
	if (!oqp_user_is_dummy($author_id)) return $link;

	
	global $post;
	
	$link_split=explode('?',$link);
	
	if (count($link_split)>1)
		$separator='&';
	else
		$separator='?';
		
	$dummy_email = get_post_meta($post->ID,'oqp_guest_email', true);
	$encoded_dummy_email=oqp_simple_encode($dummy_email,'oqp-dummy-email');
	
	$link = $link.$separator.'oqp_gkey='.$encoded_dummy_email;
		
	return apply_filters('oqp_author_link',$link,$author_id, $author_nicename);
	
}

add_filter('author_link', 'oqp_author_link',11,3 );

//add guest email key to query so we can retrieve it after
function oqp_guest_email_add_query_vars($aVars) {
    $aVars[] = "oqp_gkey";
    return $aVars;
}
add_filter('query_vars', 'oqp_guest_email_add_query_vars');

//simple function to encode guest email (not sniffing it from url)
function oqp_simple_encode($string,$key) {
    $key = sha1($key);
    $strLen = strlen($string);
    $keyLen = strlen($key);
    for ($i = 0; $i < $strLen; $i++) {
        $ordStr = ord(substr($string,$i,1));
        if ($j == $keyLen) { $j = 0; }
        $ordKey = ord(substr($key,$j,1));
        $j++;
        $hash .= strrev(base_convert(dechex($ordStr + $ordKey),16,36));
    }
    return $hash;
}
//simple function to decode guest email
function oqp_simple_decode($string,$key) {
    $key = sha1($key);
    $strLen = strlen($string);
    $keyLen = strlen($key);
    for ($i = 0; $i < $strLen; $i+=2) {
        $ordStr = hexdec(base_convert(strrev(substr($string,$i,2)),36,16));
        if ($j == $keyLen) { $j = 0; }
        $ordKey = ord(substr($key,$j,1));
        $j++;
        $hash .= chr($ordStr - $ordKey);
    }
    return $hash;
}


//filtering guest loop through email

/*
//If we look for the ads of the guest user; 
//Rather filter by email
*/
function oqp_guest_pre_get_posts($query) {

	$dummy = oqp_get_dummy_user();
	if (!$dummy) return $query; //guest posting disabled
	
	if ($query->query_vars['author_name'] !=$dummy->user_nicename) return $query; //author is not the dummy user

	$encoded_dummy_email=$query->query_vars['oqp_gkey'];

	if (!$encoded_dummy_email) return $query; //no key to analyze

	$dummy_email = oqp_simple_decode($encoded_dummy_email,'oqp-dummy-email');
	global $wp_query;
	
	$wp_query->set('meta_key', 'oqp_guest_email');
	$wp_query->set('meta_value', $dummy_email);
	
	//query_posts('author='.$dummy->ID.'meta_key=oqp_guest_email&meta_value='.$dummy_email.'&post_type=yclad');
	
}
add_filter('pre_get_posts', 'oqp_guest_pre_get_posts');


function oqp_form_user_message() {

	if ($_REQUEST['oqp-action']=='edit') return false;

	global $current_user;
	global $oqp_args;
	extract($oqp_args);
	
	

	//USER ID
	$user_id=$current_user->id;

	if (!is_user_logged_in()) {
		$dummy=oqp_get_dummy_user();
		if (($guest_posting) && ($dummy) && oqp_user_can_for_blog('edit_posts',$dummy->ID,$blog_id)) {
			$user_id=$dummy->ID;
			$message = __("As you are not logged; you have to give us your name and email.",'oqp');
		}else {
			$message = __("You must be logged to send this form.",'oqp');
		}
	}
	
	if ($message) {
		?>
		<div id="message" class="info">
			<p>
			<?php echo $message;?>
			</p>
		</div>
		<?php		
	}
	
}
add_action('oqp_creation_form_before_fields','oqp_form_user_message');

function oqp_populate_post($post_id,$blog_id=false) {
	global $oqp_args;
	global $oqp_post;
	global $current_user;
	global $post;
	
	$oqp_post=oqp_get_the_post($post_id,$blog_id);
	
	if (!$oqp_post) {
		bp_core_add_message(__('This post do not exists.','oqp'), 'error' );
		bp_core_redirect($oqp_args['form_url']);
	}

	//USER ID
	$user_id=$current_user->ID;

	//check the user can edit this post
	if (!oqp_user_can_edit_the_post($post_id,$user_id,$_REQUEST['oqp-key'],$blog_id)) {
		bp_core_add_message(__('You are not allowed to edit this post.','oqp'), 'error' );
		bp_core_redirect( oqp_post_get_link($post_id,$blog_id) );
	}
	
	
	
	//TO FIX CHECK ARGS IF WE CAN USE DUMMY
	if (!is_user_logged_in()) {
		$dummy=oqp_get_dummy_user();
		$user_id=$dummy->ID;
	}
	
	//check we can edit a published post
	if ($oqp_post->post_status=='publish') {
		if (!oqp_user_can_for_blog('edit_published_posts',$user_id,$oqp_args['blog_id'])) { //user_id is the guest user ID or the current user id
			bp_core_add_message(__('You are not allowed to edit a published post.','oqp'), 'error' );
			bp_core_redirect( oqp_post_get_link($post_id,$oqp_args['blog_id']) );
		}
	}

	//get the selected taxonomies
	if ($oqp_args['taxonomies']) {
		foreach($oqp_args['taxonomies'] as $tax_slug=>$tax_settings) {
			$oqp_args['taxonomies'][$tax_slug]['selected']='';
			$tax_list = oqp_get_the_terms_list($tax_slug,$post_id,$oqp_args['blog_id']); //$cats = the taxonomy, see oqp_block args.
			if (!is_array($tax_list)) { //no taxonomy error
				$oqp_args['taxonomies'][$tax_slug]['selected']=strip_tags($tax_list);
			}
		}
	}
	
}


function oqp_edit_post() {
	global $current_user;
	global $oqp_args;
	global $oqp_post;

	if ($_REQUEST['oqp-action']!='edit') return false;
	
	$post_id = $_REQUEST['oqp-post-id'];
	
	if (!$post_id) {
		bp_core_add_message(__('This post do not exists.','oqp'), 'error' );
		bp_core_redirect($oqp_args['form_url']);
	}
	
	oqp_populate_post($post_id,$_REQUEST['oqp-blog-id']);


	
}

function oqp_destroy_vars() {
	global $oqp_post;
	global $oqp_args;

	unset ($oqp_post);
	unset ($oqp_args);

}




function oqp_form_init() {
	add_action( 'oqp_creation_form_after_fields', 'oqp_destroy_vars',3);
	add_action( 'oqp_before_template', 'oqp_save_post', 3 );
	add_action( 'oqp_before_template', 'oqp_edit_post', 3 );

}
add_action('wp','oqp_form_init');





?>