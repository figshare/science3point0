<?php

function oqp_admin_init(){
	register_setting( 'oqp_options', 'oqp_options', 'oqp_options_validate' );
	//
	add_settings_section('oqp_main', __('Options','oqp'), 'oqp_section_options_text', 'oqp');
	
	add_settings_field('shortcode', __('Enable shortcode','oqp'), 'oqp_options_shortcode_text', 'oqp', 'oqp_main');
	add_settings_field('guest_poster', __('Enable Guest Posting','oqp'), 'oqp_options_guest_poster_text', 'oqp', 'oqp_main');
	add_settings_field('send_mails', __('Mail Author','oqp'), 'oqp_options_send_mails_text', 'oqp', 'oqp_main');
	add_settings_field('captcha', __('Enable CAPTCHA\'s','oqp'), 'oqp_options_captcha_text', 'oqp', 'oqp_main');
	//
	add_settings_section('oqp_default_behaviour', __('Default Behaviour','oqp'), 'oqp_section_default_behaviour_text', 'oqp');
	
	if (oqp_is_multiste()) {
		add_settings_field('blog_select', __('Enable Blog Selection','oqp'), 'oqp_options_blog_select_text', 'oqp', 'oqp_default_behaviour');
	}
	add_settings_field('tiny_mce', __('Enable WYSIWYG','oqp'), 'oqp_options_tiny_mce_text', 'oqp', 'oqp_default_behaviour');
	
}
// add the admin settings and such
add_action('admin_init', 'oqp_admin_init');

//OPTIONS

function oqp_section_options_text() {
	?>
	<?php
}

function oqp_options_shortcode_text() {
	$options = get_option('oqp_options');
	if ($options['shortcode']) $checked=" CHECKED";
	
	echo "<input id='shortcode' name='oqp_options[shortcode]' type='checkbox' value='1'".$checked."/> - ";
	_e('This will allow admins & editors to use the shortcode [oqp_form] into the posts to insert a Quick Post form','oqp');
}
function oqp_options_guest_poster_text() {
	$options = get_option('oqp_options');

	echo "<input id='guest_poster' name='oqp_options[guest_poster]' type='text' size='3' value='{$options['guest_poster']}'/>";?> - <small><?php _e('Dummy user ID');?></small><br/>
	<?php _e('If you want visitors to be able to send posts; you have to create a dummy user (eg. "Visitor") that will be credited as the post author.','oqp');?><br/>
	<?php _e("Then, don't give this user the correct role :",'oqp');?><br/>
	<small>
		<ul>
			<li><strike><?php printf(__('%s will not be able to create posts','oqp'),'<strong>'.__('Subscriber').'</strong>');?></strike></li>
			<li><?php printf(__('%s will be able to create pending posts','oqp'),'<strong>'.__('Contributor').'</strong>');?></li>
			<li><?php printf(__('%s will be able to publish posts & upload pictures','oqp'),'<strong>'.__('Author').'</strong>');?></li>
		</ul>
	</small>
	<a href="http://codex.wordpress.org/Roles_and_Capabilities#Capability_vs._Role_Table"><?php _e('More info','oqp');?></a>
	<?php
}
 
function oqp_options_send_mails_text() {
	$options = get_option('oqp_options');
	if ($options['send_mails']) $checked=" CHECKED";
	
	echo "<input id='send_mails' name='oqp_options[send_mails]' type='checkbox' value='1'".$checked."/> - ";
	_e('Send an email to the post author when his post has been created / published.  Guest posters always receive those emails.','oqp');
}

function oqp_options_captcha_text() {
	$options = get_option('oqp_options');
	if ($options['captcha']) $checked=" CHECKED";
	
	echo "<input id='captcha' name='oqp_options[captcha]' type='checkbox' value='1'".$checked."/>";
	if (!class_exists('siCaptcha')) {
		echo "<br/><small>".sprintf(__("You'll need the plugin %s to enable %s",'oqp'),'<a href="http://wordpress.org/extend/plugins/si-captcha-for-wordpress/" target="_blank">SI CAPTCHA Anti-Spam</a>',__('the CAPTCHA protection','oqp')).'</small>';
	}
}

function oqp_options_validate($options) {
	//OPTION guest_poster
	//check user exists
	if ($options['guest_poster']) {
		$user = new WP_User($options['guest_poster']);

		if (!$user) {
			$message=sprintf(__( 'The user #%d do not exists', 'oqp' ),$options['guest_poster']);
			add_settings_error('oqp_options','guest_poster',$message,'error');
		}elseif (!$user->has_cap('edit_posts')) {
			$message=sprintf(__( 'The user #%d has not the sufficient role to post.', 'oqp' ),$options['guest_poster']);
			add_settings_error('oqp_options','guest_poster_role',$message,'error');
			unset($user);
		}

		
		if (!$user)
			unset($options['guest_poster']);
	}
	
	$errors = get_settings_errors('oqp_options');

	return $options;
}

//DEFAULT BEHAVIOUR
function oqp_section_default_behaviour_text() {
	?>
	<?php
}
function oqp_options_blog_select_text() {
	//Get Option
	$options = get_option('oqp_options');

	if ($options['blog_select']==1) $checked=" CHECKED";
	
	echo "<input id='blog_select' name='oqp_options[blog_select]' type='checkbox' value='1'".$checked."/> - ";
	_e('Allow users to choose on which of their blogs they want to post on','oqp');
}
function oqp_options_tiny_mce_text() {
	//Get Option
	$options = get_option('oqp_options');

	if ($options['tiny_mce']==1) $checked=" CHECKED";
	
	echo "<input id='tiny_mce' name='oqp_options[tiny_mce]' type='checkbox' value='1'".$checked."/> - ";
	_e('Enable Tiny Mce when writing a new Quick Post','oqp');
}


function oqp_admin_settings_page(){
	?>
	<div class="wrap">
		<h2><?php _e('One Quick Post','oqp');?></h2>
		<form action="options.php" method="post">
			<?php settings_fields('oqp_options'); ?>
			<?php do_settings_sections('oqp'); ?>
			<input name="Submit" type="submit" value="<?php esc_attr_e('Save Changes'); ?>" />
		</form>
		<h2><?php _e('Help & Support','oqp');?></h2>
		<h3><?php _e('Use OQP with shortcode','oqp');?></h3>
		<p>
			<?php _e('You can insert a Quick Post form inside a post using the shortcode tag','oqp');?> <strong>[oqp_form]</strong>.
		</p>
		<p>
			<?php printf(__('Several attributes can be used to customise your form (see function %s for the complete list)','oqp'),'<em>oqp_block</em>');?><br/>
			<ul>
				<li><strong>blog_select</strong> - <?php _e('If you have multiple blogs enabled, allow the user to choose, among the blogs he is member of, the one he wants to post on.','oqp');?>. <?php printf(__('Default is the setting %s you saved above.'),'<em>'.__('Enable Blog Selection','oqp').'</em>');?></li>
				<li><strong>blog_id</strong> - <?php _e('If you have multiple blogs enabled, you can specify which will be the default blog selected.','oqp');?> <?php printf(__('Default is %s.','oqp'),'<em>false</em> = '.__('the current blog','oqp'));?></li>
				<li><strong>pictures</strong> - <?php _e('Allow pictures upload.','oqp');?> <?php printf(__('Default is %s.','oqp'),'<em>true</em>');?> (not yet implemented)</li>
				<li><strong>gallery</strong> - <?php _e('Displays the post gallery at the top of the post.','oqp');?> <?php printf(__('Default is %s.','oqp'),'<em>true</em>');?> (not yet implemented)</li>
				<li><strong>guest_posting</strong> - <?php _e('Allows guest posting with this form','oqp');?>. <?php printf(__('Default is true if the setting %s above is filled.'),'<em>'.__('Enable Guest Posting','oqp').'</em>');?></li>
			</ul>
		</p>
		<p>
			<h4><?php _e('Expert','oqp');?></h4>
			<ul>
				<li><strong>post_type</strong> - <?php _e('If you want to use OQP with custom post types, you can set it here.','oqp');?> <?php printf(__('Default is %s.','oqp'),'<em>post</em>');?></li>
				<li><strong>taxonomies</strong> - <?php _e('If you want to use OQP with custom taxonomies, set them here.','oqp');?> <?php printf(__('Default is %s.','oqp'),'<em>category&post_tag</em>');?><br/>
				<?php _e('eg.','oqp');?> : <code>[oqp_form taxonomies='post_tag&yclad_category|exclude=1,2|required=1']</code>
				<blockquote>
				<?php
					_e('will load the taxonomies "post_tag" (without arguments) and "yclad_category"; excluding the yclad_category 1 and 2 and forcing a yclad_category choice before sending the form.','oqp');
				?>
				</blockquote>
				<?php _e("This is quite flexible, but you may have to search a little to find how to do what you want",'oqp');?>

				</li>
			</ul>

		</p>
		<h3><?php _e('Use OQP inside templates','oqp');?></h3>
			<p>
				<?php printf(__('Using the same arguments, you can put a QuickPress form anywhere in your templates by calling the function %s where %s in an array of attributes.  Those attributes are the same than for the shortcode.','oqp'),'<strong>oqp_block(<em>$atts</em>)</strong>','<strong><em>$atts</em></strong>');?>
				<br/><br/>
				<?php _e('eg.','oqp');?> : 
				<code>
				$atts=array(
					'blog_select'=>false,
					'taxonomies'=>array(
						'post_tag'=>array(),
						'category'=>array(
							'exclude'=>array('1','12'),
							'required'=>true
					)
				);
				oqp_block($atts);
				</code>
			</p>
			<p>
				<?php _e('You could also do this, which is the same :','oqp');?><br/>
				<code>
					$form = do_shortcode("[oqp_form blog_select=false taxonomies='post_tag&category|exclude=1,12|required=1]");
				</code>
			</p>

		<h3><a href="http://dev.pellicule.org/bbpress/forum/one-click-post" target="_blank"><?php _e('Support Forums','oqp');?></a></h3>
		<h3><?php _e('Donations','oqp');?></h3>
			<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
			<input type="hidden" name="cmd" value="_s-xclick">
			<input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----
			">
			<input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
			<img alt="" border="0" src="https://www.paypal.com/fr_FR/i/scr/pixel.gif" width="1" height="1">
			</form>
	</div>

	<?php
}

function oqp_admin_menu(){
    add_submenu_page('options-general.php',__('One Click Post Settings','oqp'),__('One Click Post','oqp'), 'administrator', 'oqp', 'oqp_admin_settings_page');
}

add_action('admin_menu', 'oqp_admin_menu');
?>