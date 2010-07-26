<?php
/*
Plugin Name: BuddyPress Like
Plugin URI: http://bplike.wordpress.com
Description: Gives users of a BuddyPress site the ability to 'like' activities, and soon other social elements of the site.
Author: Alex Hempton-Smith
Version: 0.0.7
Author URI: http://www.alexhemptonsmith.com
*/

/*
 * Make sure BuddyPress is loaded before we do anything.
 */
if ( !function_exists( 'bp_core_install' ) ) {
	require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
	if ( is_plugin_active( 'buddypress/bp-loader.php' ) ) {
		require_once ( WP_PLUGIN_DIR . '/buddypress/bp-loader.php' );
	} else {
		add_action( 'admin_notices', 'bp_like_install_buddypress_notice' );
		return;
	}
}

define ( 'BP_LIKE_VERSION', '0.0.7' );
define ( 'BP_LIKE_DB_VERSION', '5' );

/**
 * bp_like_load_textdomain()
 *
 * Loads the translations for the plugin.
 *
 */
function bp_like_load_textdomain() {
	$mofile = WP_PLUGIN_DIR . '/buddypress-like/_lang/buddypress-like-' . get_locale() . '.mo';

	if ( file_exists( $mofile ) )
		load_plugin_textdomain( 'buddypress-like', $mofile );
}
add_action ( 'plugins_loaded', 'bp_like_load_textdomain', 5 );

/**
 * bp_like_install()
 *
 * Installs or upgrades the database content
 *
 */
function bp_like_install() {
	
	$default_text_strings = array(
		'like' => array(
			'default'	=> __('Like', 'buddypress-like'), 
			'custom'	=> __('Like', 'buddypress-like')
		),
		'unlike' => array(
			'default'	=> __('Unlike', 'buddypress-like'),
			'custom'	=> __('Unlike', 'buddypress-like')
		),
		'like_this_item' => array(
			'default'	=> __('Like this item', 'buddypress-like'),
			'custom'	=> __('Like this item', 'buddypress-like')
		),
		'unlike_this_item' => array(
			'default'	=> __('Unlike this item', 'buddypress-like'),
			'custom'	=> __('Unlike this item', 'buddypress-like')
		),
		'view_likes' => array(
			'default'	=> __('View likes', 'buddypress-like'),
			'custom'	=> __('View likes', 'buddypress-like')
		),
		'hide_likes' => array(
			'default'	=> __('Hide likes', 'buddypress-like'),
			'custom'	=> __('Hide likes', 'buddypress-like')
		),
		'show_activity_likes' => array(
			'default'	=> __('Show Activity Likes', 'buddypress-like'),
			'custom'	=> __('Show Activity Likes', 'buddypress-like')
		),
		'must_be_logged_in' => array(
			'default'	=> __('Sorry, you must be logged in to like that.', 'buddypress-like'),
			'custom'	=> __('Sorry, you must be logged in to like that.', 'buddypress-like')),
		'record_activity_likes_own' => array(
			'default'	=> __('%user% likes their own <a href="%permalink%">activity</a>', 'buddypress-like'),
			'custom'	=> __('%user% likes their own <a href="%permalink%">activity</a>', 'buddypress-like')
		),
		'record_activity_likes_an' => array(
			'default'	=> __('%user% likes an <a href="%permalink%">activity</a>', 'buddypress-like'),
			'custom'	=> __('%user% likes an <a href="%permalink%">activity</a>', 'buddypress-like')
		),
		'record_activity_likes_users' => array(
			'default'	=> __('%user% likes %author%\'s <a href="%permalink%">activity</a>', 'buddypress-like'),
			'custom'	=> __('%user% likes %author%\'s <a href="%permalink%">activity</a>', 'buddypress-like')
		),
		'get_likes_only_liker' => array(
			'default'	=> __('You are the only person who likes this so far.', 'buddypress-like'),
			'custom'	=> __('You are the only person who likes this so far.', 'buddypress-like')
		),
		'get_likes_you_and_singular' => array(
			'default'	=> __('You and %count% other person like this', 'buddypress-like'),
			'custom'	=> __('You and %count% other person like this', 'buddypress-like')
		),
		'get_likes_you_and_plural' => array(
			'default'	=> __('You and %count% other people like this', 'buddypress-like'),
			'custom'	=> __('You and %count% other people like this', 'buddypress-like')
		),
		'get_likes_count_people_singular' => array(
			'default'	=> __('%count% person likes this', 'buddypress-like'),
			'custom'	=> __('%count% person likes this', 'buddypress-like')
		),
		'get_likes_count_people_plural' => array(
			'default'	=> __('%count people like this', 'buddypress-like'),
			'custom'	=> __('%count people like this', 'buddypress-like')
		),
		'get_likes_and_people_singular' => array(
			'default'	=> __('and %count% other person like this.', 'buddypress-like'),
			'custom'	=> __('and %count% other person like this.', 'buddypress-like')
		),
		'get_likes_and_people_plural' => array(
			'default'	=> __('and %count% other people like this.', 'buddypress-like'),
			'custom'	=> __('and %count% other people like this.', 'buddypress-like')
		),
		'get_likes_likes_this' => array(
			'default'	=> __('likes this.', 'buddypress-like'),
			'custom'	=> __('likes this.', 'buddypress-like')
		),
		'get_likes_like_this' => array(
			'default'	=> __('like this.', 'buddypress-like'),
			'custom'	=> __('like this.', 'buddypress-like')
		),
		'get_likes_no_friends_you_and_singular' => array(
			'default'	=> __('None of your friends like this yet, but you and %count% other person does.', 'buddypress-like'),
			'custom'	=> __('None of your friends like this yet, but you and %count% other person does.', 'buddypress-like')
		),
		'get_likes_no_friends_you_and_plural' => array(
			'default'	=> __('None of your friends like this yet, but you and %count% other people do.', 'buddypress-like'),
			'custom'	=> __('None of your friends like this yet, but you and %count% other people do.', 'buddypress-like')
		),
		'get_likes_no_friends_singular' => array(
			'default'	=> __('None of your friends like this yet, but %count% other person does.', 'buddypress-like'),
			'custom'	=> __('None of your friends like this yet, but %count% other person does.', 'buddypress-like')
		),
		'get_likes_no_friends_plural' => array(
			'default'	=> __('None of your friends like this yet, but %count% other people do.', 'buddypress-like'),
			'custom'	=> __('None of your friends like this yet, but %count% other people do.', 'buddypress-like')
		)
	);

	$default_settings = array(
		'likers_visibility' => 	'friends_names_others_numbers',
		'text_strings'		=>	$default_text_strings
	);

	if ( get_site_option('bp-like-db-version') )
		delete_site_option('bp-like-db-version');

	if ( !get_site_option('bp_like_db_version') || get_site_option('bp_like_db_version') < BP_LIKE_DB_VERSION )
		update_site_option('bp_like_db_version', BP_LIKE_DB_VERSION );

	update_site_option('bp_like_settings', $default_settings );
	add_action( 'admin_notices', 'bp_like_upgrade_notice' );
}

function bp_like_install_buddypress_notice() {
	echo '<div id="message" class="error fade bp-like-upgraded"><p style="line-height: 150%">';
	_e('<strong>BuddyPress Like</strong></a> requires the BuddyPress plugin to work. Please <a href="http://buddypress.org/download">install BuddyPress</a> first, or <a href="plugins.php">deactivate BuddyPress Like</a>.', 'buddypress-like');
	echo '</p></div>';
}

function bp_like_upgrade_notice() {
	if ( !is_site_admin() )
		return false;
	
	echo '<div id="message" class="updated fade bp-like-upgraded"><p style="line-height: 150%">';
	printf(__('<strong>BuddyPress Like</strong> has been successfully upgraded to version %s.', 'buddypress-like'), BP_LIKE_VERSION);
	echo '</p></div>';
}

/**
 * bp_like_check_installed()
 *
 * Checks to see if the DB tables exist or if you are running an old version
 * of the component. If it matches, it will run the installation function.
 *
 */
function bp_like_check_installed() {
	global $wpdb;

	if ( !is_site_admin() )
		return false;

	if ( !get_site_option('bp_like_settings') || get_site_option('bp-like-db-version') )
		bp_like_install();

	if ( get_site_option('bp_like_db_version') < BP_LIKE_DB_VERSION )
		bp_like_install();
}
add_action( 'admin_menu', 'bp_like_check_installed' );

/**
 * bp_like_get_settings()
 *
 * Returns settings from the database
 *
 */
function bp_like_get_settings( $option = false ) {
	
	$settings = get_site_option('bp_like_settings');
	
	if (!$option)
		return $settings;
		
	else
		return $settings[$option];
}

/**
 * bp_like_get_text()
 *
 * Returns a custom text string from the database
 *
 */
function bp_like_get_text( $text = false, $type = 'custom' ) {
	
	$settings = get_site_option('bp_like_settings');
	$text_strings = $settings['text_strings'];
	$string = $text_strings[$text];
		
	return $string[$type];
}

/**
 * bp_like_process_ajax()
 *
 * Runs the relevant function depending on what Ajax call has been made.
 *
 */
function bp_like_process_ajax() {
	global $bp;

	$id = preg_replace("/\D/","",$_POST['id']); 
	
	if ( $_POST['type'] == 'like' )
		bp_like_activity_add_user_like( $id );
	
	if ( $_POST['type'] == 'unlike' )
		bp_like_activity_remove_user_like( $id );

	if ( $_POST['type'] == 'view-likes' )
		echo bp_like_get_activity_likes( $id );

	die();
}
add_action( 'wp_ajax_activity_like', 'bp_like_process_ajax' );

/**
 * bp_like_activity_add_user_like()
 *
 * Registers that the user likes a given activity item.
 *
 */
function bp_like_activity_add_user_like( $activity_id, $user_id = false ) {
	global $bp;
	
	if (!$activity_id)
		return false;

	if ( !$user_id )
		$user_id = $bp->loggedin_user->id;
	
	if ( $user_id == 0 ) {
		echo bp_like_get_text('must_be_logged_in');
		return false;
	}
	
	/* Add to the users liked activities. */
	$user_likes = get_usermeta( $user_id, 'bp_liked_activities' );
	$user_likes[$activity_id] = 'activity_liked';
	update_usermeta( $user_id, 'bp_liked_activities', $user_likes );
	
	/* Add to the total likes for this activity. */
	$users_who_like = bp_activity_get_meta( $activity_id, 'liked_count' );
	$users_who_like[$user_id] = 'user_likes';
	bp_activity_update_meta( $activity_id, 'liked_count', $users_who_like );
	
	$liked_count = count($users_who_like);
	
	/* Add an update to their activity stream saying they liked this activity */
	$activity = bp_activity_get_specific( array( 'activity_ids' => $activity_id, 'component' => 'buddypress-like' ) );
	$author_id = $activity['activities'][0]->user_id;
	
	if ($user_id == $author_id) :
		$liker = bp_core_get_userlink( $user_id );
		$activity_url = bp_activity_get_permalink($activity_id);
		$action = bp_like_get_text('record_activity_likes_own');
	elseif ($user_id == 0) :
		$liker = bp_core_get_userlink( $user_id );
		$activity_url = bp_activity_get_permalink($activity_id);
		$action = bp_like_get_text('record_activity_likes_an');
	else :
		$liker = bp_core_get_userlink( $user_id );
		$author = bp_core_get_userlink( $author_id );
		$activity_url = bp_activity_get_permalink($activity_id);
		$action = bp_like_get_text('record_activity_likes_users');
	endif;
	
	/* Filter out the placeholders */
	$action = str_replace('%user%', $liker, $action);
	$action = str_replace('%permalink%', $activity_url, $action);
	$action = str_replace('%author%', $author, $action);

	bp_activity_add( array( 'action' => $action, 'component' => 'bp-like', 'type' => 'activity_liked', 'user_id' => $user_id, 'item_id' => $activity_id ) );

	echo bp_like_get_text('unlike');
	if ($liked_count)
		echo ' (' . $liked_count . ')';
}

/**
 * bp_like_activity_remove_user_like()
 *
 * Registers that the user has unliked a given activity item.
 *
 */
function bp_like_activity_remove_user_like( $activity_id, $user_id = false ) {
	global $bp;
	
	if ( !$activity_id )
		return false;

	if ( !$user_id )
		$user_id = $bp->loggedin_user->id;
	
	if ( $user_id == 0 ) {
		echo bp_like_get_text('must_be_logged_in');
		return false;
	}

	/* Remove this from the users liked activities. */
	$user_likes = get_usermeta( $user_id, 'bp_liked_activities' );
	unset( $user_likes[$activity_id] );
	update_usermeta( $user_id, 'bp_liked_activities', $user_likes );

	/* Update the total number of users who have liked this activity. */
	$users_who_like = bp_activity_get_meta( $activity_id, 'liked_count' );
	unset( $users_who_like[$user_id] );
	bp_activity_update_meta( $activity_id, 'liked_count', $users_who_like );
	
	$liked_count = count($users_who_like);

	/* Remove the update on the users profile from when they liked the activity. */
	$update_id = bp_activity_get_activity_id( array( 'item_id' => $activity_id, 'component' => 'buddypress-like' ) );
	bp_activity_delete( array( 'id' => $update_id, 'component' => 'buddypress-like' ) );
	
	$activity = bp_activity_get_specific( array( 'activity_ids' => $activity_id, 'component' => 'buddypress-like' ) );
	$author_id = $activity['activities'][0]->user_id;

	echo bp_like_get_text('like');
	if ($liked_count)
		echo ' (' . $liked_count . ')';
}

/**
 * bp_like_get_activity_likes()
 *
 * Outputs a list of users who have liked a given activity.
 *
 */
function bp_like_get_activity_likes( $activity_id = false, $user_id = false ) {
	global $bp;

	if ( !$activity_id ) 
		return false;
		
	if ( !$user_id )
		$user_id = $bp->loggedin_user->id;

	if ( !bp_activity_get_meta( $activity_id, 'liked_count' ) )
		return false;

	$users_who_like = array_keys(bp_activity_get_meta( $activity_id, 'liked_count' ));
	$liked_count = count(bp_activity_get_meta( $activity_id, 'liked_count' ));
	$users_friends = friends_get_friend_user_ids($user_id);
	
	if (!empty($users_friends))
		$friends_who_like = array_intersect($users_who_like, $users_friends);
	
	$non_friends_who_like = $liked_count-count($friends_who_like);
	
	if (bp_like_get_activity_is_liked($activity_id, $user_id))
		$non_friends_who_like = $non_friends_who_like-1;

		if ( bp_like_get_settings('likers_visibility') == 'show_all' ) :
			
			if ( $liked_count == 1 && bp_like_get_activity_is_liked($activity_id, $user_id)) :
			
				$output .= bp_like_get_text('get_likes_only_liker');
			
			else :
				
				if (bp_like_get_activity_is_liked($activity_id, $user_id)) :
					
					$liked_count = $liked_count-1;
					
					if ( $liked_count == 1 )
						$output .= bp_like_get_text('get_likes_you_and_singular').' &middot ';
					
					else
						$output .= bp_like_get_text('get_likes_you_and_plural').' &middot ';

				else :

					if ( $liked_count == 1 )
						$output .= bp_like_get_text('get_likes_count_people_singular').' &middot ';
					
					else
						$output .= bp_like_get_text('get_likes_count_people_plural').' &middot ';

				endif;
	
				foreach($users_who_like as $user):
						if ($user_id == $user)
							$output .= '';
						else
							$output .= bp_core_get_userlink($user) . ' &middot ';
				endforeach;

			endif;

		elseif ( bp_like_get_settings('likers_visibility') == 'friends_names_others_numbers' ) :

			if ( !empty( $friends_who_like ) ) :
				
				if ( bp_like_get_activity_is_liked( $activity_id, $user_id ) )
					$output .= '<a href="' . bp_core_get_user_domain($user_id) . '" title="' . bp_core_get_user_displayname($user_id) . '">' . __('You', 'buddypress-like') . '</a> &middot ';

				foreach($friends_who_like as $friend) :
					$output .= bp_core_get_userlink($friend) . ' &middot ';
				endforeach;
					
				if ($non_friends_who_like) {
					
					if ( $non_friends_who_like == 1 )
						$output .= bp_like_get_text('get_likes_and_people_singular').' &middot ';
					
					else
						$output .= bp_like_get_text('get_likes_and_people_plural').' &middot ';
									
				} else {
					
					if ( $liked_count ==1 )
						$output .= bp_like_get_text('get_likes_likes_this');
					
					else
						$output .= bp_like_get_text('get_likes_like_this');

				}

			elseif ( !count($friends_who_like) && $liked_count == 1 && bp_like_get_activity_is_liked($activity_id, $user_id)) :
			
				$output .= bp_like_get_text('get_likes_only_liker');

			elseif (empty( $friends_who_like )) :
				
				if (bp_like_get_activity_is_liked($activity_id, $user_id)) :

					$liked_count = $liked_count-1;
					
					if ( $liked_count == 1 )
						$output .= bp_like_get_text('get_likes_no_friends_you_and_singular');
					else
						$output .= bp_like_get_text('get_likes_no_friends_you_and_plural');
				
				else :
					
					if ( $liked_count == 1 )
						$output .= bp_like_get_text('get_likes_no_friends_singular');
					else
						$output .= bp_like_get_text('get_likes_no_friends_plural');

				endif;

			endif;

		elseif ( bp_like_get_settings('likers_visibility') == 'just_numbers' ) :
		
			if ( bp_like_get_activity_is_liked($activity_id, $user_id) ) :
				
				if ( $liked_count == 1 ) :
					$output .= bp_like_get_text('get_likes_only_liker');
				else :
					$liked_count = $liked_count-1;
					if ( $liked_count == 1 )
						$output .= bp_like_get_text('get_likes_you_and_singular');
					else
						$output .= bp_like_get_text('get_likes_you_and_plural');
				endif;
			
			else :
				if ( $liked_count == 1 )
					$output .= bp_like_get_text('get_likes_count_people_singular');
				else
					$output .= bp_like_get_text('get_likes_count_people_plural');
			endif;

		endif;

	$output = str_replace('%count%', $liked_count, $output);

	return $output;
}

/**
 * bp_like_get_activity_is_liked()
 *
 * Checks to see whether the user has liked a given activity.
 *
 */
function bp_like_get_activity_is_liked( $activity_id = false, $user_id = false ) {
	global $bp;

	if (!$activity_id)
		$activity_id = bp_get_activity_id();
	
	if (!$user_id)
		$user_id = $bp->loggedin_user->id;

	$user_likes = get_usermeta( $bp->loggedin_user->id, 'bp_liked_activities' );
	
	if (!$user_likes){
		return false;
	} elseif (!array_key_exists($activity_id, $user_likes)) {
		return false;
	} else {
		return true;
	};
}

/**
 * bp_like_activity_button()
 *
 * Inserts the 'Like/Unlike' and 'View likes/Hide likes' buttons into activities.
 *
 */
function bp_like_activity_button() {
	$activity = bp_activity_get_specific( array( 'activity_ids' => bp_get_activity_id() ) );
	$activity_type = $activity['activities'][0]->type;
	
	if ( is_user_logged_in() && $activity_type !== 'activity_liked' ) :
	if (bp_activity_get_meta( bp_get_activity_id(), 'liked_count' )) {
		$users_who_like = array_keys(bp_activity_get_meta( bp_get_activity_id(), 'liked_count' ));
		$liked_count = count($users_who_like);
	}
		if ( !bp_like_get_activity_is_liked() ) : ?>
		<a href="" class="like" id="like-activity-<?php bp_activity_id(); ?>" title="<?php echo bp_like_get_text('like_this_item'); ?>"><?php echo bp_like_get_text('like'); if ($liked_count) echo ' (' . $liked_count . ')'; ?></a>
				<?php else : ?>
		<a href="" class="unlike" id="unlike-activity-<?php bp_activity_id(); ?>" title="<?php echo bp_like_get_text('unlike_this_item'); ?>"><?php echo bp_like_get_text('unlike'); if ($liked_count) echo ' (' . $liked_count . ')'; ?></a>
		<?php endif;
		if ($users_who_like): ?>
		<a href="" class="view-likes" id="view-likes-<?php bp_activity_id(); ?>"><?php echo bp_like_get_text('view_likes'); ?></a>
		<p class="users-who-like" id="users-who-like-<?php bp_activity_id(); ?>"></p>
		<?php
		endif;
	endif;
};
add_filter('bp_activity_entry_meta', 'bp_like_activity_button');

/**
 * bp_like_activity_filter()
 *
 * Adds 'Show Activity Likes' to activity stream filters.
 *
 */
function bp_like_activity_filter() {
	echo '<option value="activity_liked">' . bp_like_get_text('show_activity_likes') . '</option>';
}
add_action('bp_activity_filter_options', 'bp_like_activity_filter');
add_action('bp_member_activity_filter_options', 'bp_like_activity_filter');
add_action('bp_group_activity_filter_options', 'bp_like_activity_filter');

/**
 * bp_like_list_scripts()
 *
 * Includes the Javascript required for Ajax etc.
 *
 */
function bp_like_list_scripts() {
  wp_enqueue_script( "bp-like", path_join(WP_PLUGIN_URL, basename( dirname( __FILE__ ) )."/_inc/bp-like.min.js"), array( 'jquery' ) );
}
add_action('wp_print_scripts', 'bp_like_list_scripts');

/**
 * bp_like_insert_head()
 *
 * Includes any CSS and/or Javascript needed in the <head>.
 *
 */
function bp_like_insert_head() {
?>
<style type="text/css">
	.bp-like.activity_liked .activity-meta, .bp-like.activity_liked a.view, .users-who-like, .mini a.view-likes, .mini a.hide-likes { display: none; }
	
	/* For the default theme */
	#bp-default .users-who-like {
		margin: 10px 0 -10px 0;
		background: #eee;
		border-bottom: 1px solid #ddd;
		border-right: 1px solid #ddd;
		-moz-border-radius: 4px;
		-webkit-border-radius: 4px;
		border-radius: 4px;
		padding: 8px 8px 8px 12px;
		color: #888;
	}
	#bp-default .users-who-like a { color: #777; padding: 0; background: none; border: none; text-decoration: underline; font-size: 12px; }
	#bp-default .users-who-like a:hover { color: #222; }
	#bp-default .mini .users-who-like {
		width: 100%;
		position: absolute;
		top: 0;
		left: 0;
	}
	
</style>
<script type="text/javascript">
	var bp_like_terms_like = '<?php echo bp_like_get_text('like'); ?>';
	var bp_like_terms_like_message = '<?php echo bp_like_get_text('like_this_item'); ?>';
	var bp_like_terms_unlike_message = '<?php echo bp_like_get_text('unlike_this_item'); ?>';
	var bp_like_terms_view_likes = '<?php echo bp_like_get_text('view_likes'); ?>';
	var bp_like_terms_hide_likes = '<?php echo bp_like_get_text('hide_likes'); ?>';
	var bp_like_terms_unlike_1 = '<?php echo bp_like_get_text('unlike'); ?> (1)';
</script>
<?php	
}
add_action('wp_head', 'bp_like_insert_head');

/**
 * bp_like_add_admin_page_menu()
 *
 * Adds "BuddyPress Like" to the main BuddyPress admin menu.
 *
 */
function bp_like_add_admin_page_menu() {
    add_submenu_page('bp-general-settings', 'BuddyPress Like', 'BuddyPress Like', 'manage_options', 'bp-like-settings', 'bp_like_admin_page');
}
add_action('admin_menu', 'bp_like_add_admin_page_menu');

/**
 * bp_like_admin_page_verify_nonce()
 *
 * When the settings form is submitted, verifies the nonce to ensure security.
 *
 */
function bp_like_admin_page_verify_nonce() {
	if( isset($_POST['_wpnonce']) && isset($_POST['bp_like_updated']) ) {
		$nonce = $_REQUEST['_wpnonce'];
		if ( !wp_verify_nonce($nonce, 'bp-like-admin') )
			wp_die( __('You do not have permission to do that.') );
	}
}
add_action('init', 'bp_like_admin_page_verify_nonce');

/**
 * bp_like_admin_page()
 *
 * Outputs the admin settings page.
 *
 */
function bp_like_admin_page() {

	/* Update our options if the form has been submitted */
    if( isset($_POST['_wpnonce']) && isset($_POST['bp_like_updated']) ) {
		
		/* Add each text string to the $strings_to_save array */
		foreach ($_POST as $key => $value) {
			if (preg_match("/text_string_/i", $key)) {
				$default = bp_like_get_text(str_replace('bp_like_admin_text_string_', '', $key), 'default');
				$strings_to_save[str_replace('bp_like_admin_text_string_', '', $key)] = array('default' => $default, 'custom' => stripslashes($value));
			}
		}
		
		/* Now actually save the data to the options table */
		update_site_option( 'bp_like_settings', array('likers_visibility' => $_POST['bp_like_admin_likers_visibility'], 'text_strings' => $strings_to_save ) );
		
		/* Let the user know everything's cool */
		echo '<div class="updated"><p><strong>';
		_e('Settings saved.', 'wordpress');
		echo '</strong></p></div>';
	}
	
	$text_strings = bp_like_get_settings('text_strings');

?>
<style type="text/css">
#icon-bp-like-settings {
	background: url('<?php echo plugins_url('/_inc/bp-like-icon32.png', __FILE__); ?>') no-repeat top left;
}
table input {
	width: 100%;
}
table label {
	display: block;
}
</style>

<div class="wrap">
  <div id="icon-bp-like-settings" class="icon32"><br /></div>
  <h2><?php _e('BuddyPress Like Settings', 'buddypress-like'); ?></h2>
  <form action="" method="post" id="bp-like-admin-form">
    <input type="hidden" name="bp_like_updated" value="updated">
    <h3><?php _e("'View Likes' Visibility", "buddypress-like"); ?></h3>
    <p><?php _e("You can choose how much information about the 'likers' of a particular item is shown;", "buddypress-like"); ?></p>
    <p>
      <input type="radio" name="bp_like_admin_likers_visibility" value="show_all" <?php if ( bp_like_get_settings('likers_visibility') == 'show_all' ) { echo 'checked="checked""'; }; ?> /> <?php _e('Show the name and profile link of all likers', 'buddypress-like'); ?><br />
      <input type="radio" name="bp_like_admin_likers_visibility" value="friends_names_others_numbers" <?php if ( bp_like_get_settings('likers_visibility') == 'friends_names_others_numbers' ) { echo 'checked="checked""'; }; ?> /> <?php _e('Show the names of friends, and the number of non-friends', 'buddypress-like'); ?><br />
      <input type="radio" name="bp_like_admin_likers_visibility" value="just_numbers" <?php if ( bp_like_get_settings('likers_visibility') == 'just_numbers' ) { echo 'checked="checked""'; }; ?> /> <?php _e('Show only the number of likers', 'buddypress-like'); ?>
    </p>
    <h3><?php _e('Custom Messages', 'buddypress-like'); ?></h3>
    <p><?php _e("Change what messages are shown to users. For example, they can 'love' or 'dig' items instead of liking them.", "buddypress-like"); ?><br /><br /></p>
    
    <table class="widefat fixed" cellspacing="0">
	  <thead>
	    <tr>
	      <th scope="col" id="default" class="column-name" style="width: 43%;"><?php _e('Default', 'buddypress-like'); ?></th>
	      <th scope="col" id="custom" class="column-name" style=""><?php _e('Custom', 'buddypress-like'); ?></th>
	    </tr>
	  </thead>
	  <tfoot>
	    <tr>
	      <th colspan="2" id="default" class="column-name"></th>
	    </tr>
	  </tfoot>

      <?php foreach ( $text_strings as $key => $string ) : ?>
      <tr valign="top">
          <th scope="row" style="width:400px;"><label for="bp_like_admin_text_string_<?php echo $key; ?>"><?php echo htmlspecialchars($string['default']); ?></label></th>
          <td><input name="bp_like_admin_text_string_<?php echo $key; ?>" id="bp_like_admin_text_string_<?php echo $key; ?>" value="<?php echo htmlspecialchars($string['custom']); ?>" class="regular-text" type="text"></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>

    <p class="submit">
      <input class="button-primary" type="submit" name="bp-like-admin-submit" id="bp-like-admin-submit" value="<?php _e('Save Changes', 'wordpress'); ?>"/>
    </p>
    <?php wp_nonce_field( 'bp-like-admin' ) ?>
  </form>
</div>
<?php
}