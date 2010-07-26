<?php 

/********************************************************************************
 * Screen Functions
 *
 * Screen functions are the controllers of BuddyPress. They will execute when their
 * specific URL is caught. They will first save or manipulate data using business
 * functions, then pass on the user to a template file.
 */

/**
 * bp_album_screen_picture()
 *
 * Single picture
 */ 

function bp_album_screen_single() {
	global $bp,$pictures_template;
	
	if ( $bp->current_component == $bp->album->slug && $bp->album->single_slug == $bp->current_action && $pictures_template->picture_count && isset($bp->action_variables[1]) && $bp->album->edit_slug == $bp->action_variables[1]  ) {
	
		do_action( 'bp_album_screen_edit' );

		add_action( 'bp_template_title', 'bp_album_screen_edit_title' );
		add_action( 'bp_template_content', 'bp_album_screen_edit_content' );
	
		bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
		
		return;
	}
	
	do_action( 'bp_album_screen_single' );

	bp_core_load_template( apply_filters( 'bp_album_template_screen_single', 'album/single' ) );
}

	function bp_album_screen_edit_title() {
		_e( 'Edit Picture', 'bp-album' );
	}

	function bp_album_screen_edit_content() {
		global $bp,$pictures_template; 
		if (bp_album_has_pictures() ) :  bp_album_the_picture();
		$limit_info = bp_album_limits_info();
		
		$priv_str = array(
			0 => __('Public','bp-album'),
			2 => __('Registered members','bp-album'),
			4 => __('Only friends','bp-album'),
			6 => __('Private','bp-album'),
			10 => __('Hidden (admin only)','bp-album')
		);
		?>		
		<h4><?php _e( 'Edit Picture', 'bp-album' ) ?></h4>

		<form method="post" enctype="multipart/form-data" name="bp-album-edit-form" id="bp-album-edit-form" class="standard-form">
            <img id="picture-edit-thumb" src='<?php bp_album_picture_thumb_url() ?>' />
            <p>
                <label><?php _e('Picture Title *', 'bp-album' ) ?><br />
                <input type="text" name="title" id="picture-title" size="100" value="<?php 
                	echo (empty($_POST['title'])) ? bp_album_get_picture_title() : wp_filter_kses($_POST['title']);
                ?>"/></label>
            </p>
            <p>
                <label><?php _e('Picture Description', 'bp-album' ) ?><br />
                <textarea name="description" id="picture-description" rows="15"cols="40" ><?php 
                	echo (empty($_POST['description'])) ? $pictures_template->picture->description : wp_filter_kses($_POST['description']);
                ?></textarea></label>
            </p>
            <p>
                <label><?php _e('Visibility','bp-album') ?></label>
                
				<?php foreach($priv_str as $k => $str){
						if($limit_info[$k]['enabled']) { ?>
				
				<label><input type="radio" name="privacy" value="<?php echo $k ?>" <?php
					if($limit_info[$k]['current']) echo 'checked="checked" ';
					if (!$limit_info[$k]['current'] && !$limit_info[$k]['remaining'])
						echo 'disabled="disabled" />'.$str.' '.__( '(limit reached)', 'bp-album' );
					else
						echo '/>'.$str;
				?></label>
				
				<?php }} ?>
            </p>
            <?php if(bp_is_active('activity') && $bp->album->bp_album_enable_comments ): ?>
            <p>
                <label><?php _e('Picture activity and comments','bp-album') ?></label>
				<label><input type="radio" name="enable_comments" value="1" checked="checked" /><?php _e('Enable','bp-album') ?></label>
				<label><input type="radio" name="enable_comments" value="0" /><?php _e('Disable','bp-album') ?></label>
				<?php _e('If picture already has comments this will delete them','bp-album') ?>
            </p>
            <?php endif; ?>
            <input type="submit" name="submit" id="submit" value="<?php _e( 'Save', 'bp-album' ) ?>"/>

			<?php
			/* This is very important, don't leave it out. */
			wp_nonce_field( 'bp-album-edit' );
			?>
		</form>
		<?php else: ?>
			<p><?php _e( "Either this url is not valid or you can't edit this picture.", 'bp-album' ) ?></p>
		<?php endif;
	}



/**
 * bp_album_screen_pictures()
 *
 * An album page
 */
function bp_album_screen_pictures() {

	do_action( 'bp_album_screen_pictures' );

	bp_core_load_template( apply_filters( 'bp_album_template_screen_pictures', 'album/pictures' ) );
}

/**
 * bp_album_screen_upload()
 *
 * Sets up and displays the screen output for the sub nav item "example/screen-two"
 */
function bp_album_screen_upload() {
	global $bp;

	/**
	 * If the user has not Accepted or Rejected anything, then the code above will not run,
	 * we can continue and load the template.
	 */
	do_action( 'bp_album_screen_upload' );

	add_action( 'bp_template_title', 'bp_album_screen_upload_title' );
	add_action( 'bp_template_content', 'bp_album_screen_upload_content' );

	/* Finally load the plugin template file. */
	bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
}

	function bp_album_screen_upload_title() {
		_e( 'Upload new picture', 'bp-album' );
	}

	function bp_album_screen_upload_content() {
		global $bp; 
		$limit_info = bp_album_limits_info();
		
		$priv_str = array(
			0 => __('Public','bp-album'),
			2 => __('Registered members','bp-album'),
			4 => __('Only friends','bp-album'),
			6 => __('Private','bp-album'),
		);
		if($limit_info['all']['enabled']):?>
		
		<h4><?php _e( 'Upload new picture', 'bp-album' ) ?></h4>

		<form method="post" enctype="multipart/form-data" name="bp-album-upload-form" id="bp-album-upload-form" class="standard-form">
       
            <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo CORE_MAX_FILE_SIZE; ?>" />
            <input type="hidden" name="action" value="picture_upload" />

            <p>
                <label><?php _e('Select Picture to Upload *', 'bp-album' ) ?><br />
                <input type="file" name="file" id="file"/></label>
            </p>
            <p>
                <label><?php _e('Visibility','bp-album') ?></label>
                
				<?php $checked=false;
					foreach($priv_str as $k => $str){
						if($limit_info[$k]['enabled']) {?>
				
				<label><input type="radio" name="privacy" value="<?php echo $k ?>" <?php
					if(!$checked){
						 echo 'checked="checked" ';
						 $checked = true;
					}
					if (!$limit_info[$k]['current'] && !$limit_info[$k]['remaining'])
						echo 'disabled="disabled" />'.$str.' '.__( '(limit reached)', 'bp-album' );
					else
						echo '/>'.$str;
				?></label>
				
				<?php }} ?>
            </p>
            <input type="submit" name="submit" id="submit" value="<?php _e( 'Upload picture', 'bp-album' ) ?>"/>

			<?php
			/* This is very important, don't leave it out. */
			wp_nonce_field( 'bp-album-upload' );
			?>
		</form>
		<?php else: ?>
			<p><?php _e( 'You have reached the upload limit, delete some pictures if you want to upload more', 'bp-album' ) ?></p>
		<?php endif;
	}


/**
 * bp_album_screen_notification_settings()
 *
 * Adds notification settings for the component, so that a user can turn off email
 * notifications set on specific component actions.
 *
function bp_album_screen_notification_settings() {
	global $current_user;

	/**
	 * Under Settings > Notifications within a users profile page they will see
	 * settings to turn off notifications for each component.
	 *
	 * You can plug your custom notification settings into this page, so that when your
	 * component is active, the user will see options to turn off notifications that are
	 * specific to your component.
	 */

	 /**
	  * Each option is stored in a posted array notifications[SETTING_NAME]
	  * When saved, the SETTING_NAME is stored as usermeta for that user.
	  *
	  * For example, notifications[notification_friends_friendship_accepted] could be
	  * used like this:
	  *
	  * if ( 'no' == get_usermeta( $bp['loggedin_userid], 'notification_friends_friendship_accepted' ) )
	  *		// don't send the email notification
	  *	else
	  *		// send the email notification.
      *

	?>
	<table class="notification-settings" id="bp-album-notification-settings">
		<tr>
			<th class="icon"></th>
			<th class="title"><?php _e( 'Example', 'bp-album' ) ?></th>
			<th class="yes"><?php _e( 'Yes', 'bp-album' ) ?></th>
			<th class="no"><?php _e( 'No', 'bp-album' )?></th>
		</tr>
		<tr>
			<td></td>
			<td><?php _e( 'Action One', 'bp-album' ) ?></td>
			<td class="yes"><input type="radio" name="notifications[notification_example_action_one]" value="yes" <?php if ( !get_usermeta( $current_user->id,'notification_example_action_one') || 'yes' == get_usermeta( $current_user->id,'notification_example_action_one') ) { ?>checked="checked" <?php } ?>/></td>
			<td class="no"><input type="radio" name="notifications[notification_example_action_one]" value="no" <?php if ( get_usermeta( $current_user->id,'notification_example_action_one') == 'no' ) { ?>checked="checked" <?php } ?>/></td>
		</tr>
		<tr>
			<td></td>
			<td><?php _e( 'Action Two', 'bp-album' ) ?></td>
			<td class="yes"><input type="radio" name="notifications[notification_example_action_two]" value="yes" <?php if ( !get_usermeta( $current_user->id,'notification_example_action_two') || 'yes' == get_usermeta( $current_user->id,'notification_example_action_two') ) { ?>checked="checked" <?php } ?>/></td>
			<td class="no"><input type="radio" name="notifications[notification_example_action_two]" value="no" <?php if ( 'no' == get_usermeta( $current_user->id,'notification_example_action_two') ) { ?>checked="checked" <?php } ?>/></td>
		</tr>

		<?php do_action( 'bp_album_notification_settings' ); ?>
	</table>
<?php
}*/
//add_action( 'bp_notification_settings', 'bp_album_screen_notification_settings' );


/********************************************************************************
 * Action Functions
 *
 * Action functions are exactly the same as screen functions, however they do not
 * have a template screen associated with them. Usually they will send the user
 * back to the default screen after execution.
 */

function bp_album_action_upload() {
	global $bp;
	
	if ( $bp->current_component == $bp->album->slug && $bp->album->upload_slug == $bp->current_action && isset( $_POST['submit'] )) {
	
		check_admin_referer('bp-album-upload');
		
		$error_flag = false;
		$feedback_message = array();
		
		// check privacy
		if( !isset($_POST['privacy']) ){
			$error_flag = true;
			$feedback_message[] = __( 'Please select a privacy option.', 'bp-album' );	
		} else {
			$priv_lvl = intval($_POST['privacy']);

                        // TODO: Refactor this, and the bp_album_max_privXX variable as an array.
                        switch ($priv_lvl) {
                            case "0": $pic_limit = $bp->album->bp_album_max_priv0_pictures; break;
                            case "1": $pic_limit = $bp->album->bp_album_max_priv1_pictures; break;
                            case "2": $pic_limit = $bp->album->bp_album_max_priv2_pictures; break;
                            case "3": $pic_limit = $bp->album->bp_album_max_priv3_pictures; break;
                            case "4": $pic_limit = $bp->album->bp_album_max_priv4_pictures; break;
                            case "5": $pic_limit = $bp->album->bp_album_max_priv5_pictures; break;
                            case "6": $pic_limit = $bp->album->bp_album_max_priv6_pictures; break;
                            case "7": $pic_limit = $bp->album->bp_album_max_priv7_pictures; break;
                            case "8": $pic_limit = $bp->album->bp_album_max_priv8_pictures; break;
                            case "9": $pic_limit = $bp->album->bp_album_max_priv9_pictures; break;
                            default: $pic_limit = null;
                        }


			if($priv_lvl == 10 )
				$pic_limit = is_site_admin() ? false : null;
			if( $pic_limit === null){ //costant don't exist
				$error_flag = true;
				$feedback_message[] = __( 'Privacy option is not correct.', 'bp-album' );	
			}elseif( $pic_limit !== false && ( $pic_limit === 0  || $pic_limit <= bp_album_get_picture_count(array('privacy'=>$priv_lvl)) ) ){
				$error_flag = true;
				switch ($priv_lvl){
					case 0 :
						$feedback_message[] = __( 'You reached the limit for public pictures.', 'bp-album' ).' '.__( 'Please select another privacy option.', 'bp-album' );
						break;
					case 2 :
						$feedback_message[] = __( 'You reached the limit for pictures visible to community members.', 'bp-album' ).' '.__( 'Please select another privacy option.', 'bp-album' );
						break;
					case 4 :
						$feedback_message[] = __( 'You reached the limit for pictures visible to friends.', 'bp-album' ).' '.__( 'Please select another privacy option.', 'bp-album' );
						break;
					case 6 :
						$feedback_message[] = __( 'You reached the limit for private pictures.', 'bp-album' ).' '.__( 'Please select another privacy option.', 'bp-album' );
						break;
				}
			}
		}
		
		$uploadErrors = array(
			0 => __("There is no error, the file uploaded with success", 'buddypress'),
			1 => __("Your image was bigger than the maximum allowed file size of: ", 'buddypress') . size_format(CORE_MAX_FILE_SIZE),
			2 => __("Your image was bigger than the maximum allowed file size of: ", 'buddypress') . size_format(CORE_MAX_FILE_SIZE),
			3 => __("The uploaded file was only partially uploaded", 'buddypress'),
			4 => __("No file was uploaded", 'buddypress'),
			6 => __("Missing a temporary folder", 'buddypress')
		);
		if ( isset($_FILES['file']) ){
		
			if ( $_FILES['file']['error'] ) {
				$feedback_message[] = sprintf( __( 'Your upload failed, please try again. Error was: %s', 'buddypress' ), $uploadErrors[$_FILES['file']['error']] );
				$error_flag = true;
			}
		
			if ( $_FILES['file']['size'] > BP_AVATAR_ORIGINAL_MAX_FILESIZE ) {
				$feedback_message[] = sprintf( __( 'The file you uploaded is too big. Please upload a file under %s', 'buddypress'), size_format(CORE_MAX_FILE_SIZE) );
				$error_flag = true;
			}
		
			if ( !bp_core_check_avatar_type( $_FILES['file'] ) ) {
				$feedback_message[] = __( 'Please upload only JPG, GIF or PNG photos.', 'buddypress' );
				$error_flag = true;
			}
		}else{
			$feedback_message[] = sprintf( __( 'Your upload failed, please try again. Error was: %s', 'buddypress' ), $uploadErrors[4] );
			$error_flag = true;
		
		}
		
		if(!$error_flag){  // If everything is ok handle the upload and move to the directory

			add_filter( 'upload_dir', 'bp_album_upload_dir', 10, 0 ); //the upload handle get the upload dir from this filter

			$pic_org = wp_handle_upload( $_FILES['file'],array('action'=>'picture_upload') );

			if ( !empty( $pic_org['error'] ) ) {
				$feedback_message[] = sprintf( __('Your upload failed, please try again. Error was: %s', 'buddypress' ), $pic_org['error'] );
				$error_flag = true;
			}
		}		
		if(!$error_flag){  // If everything is ok resize the picture
		
			$abs_path_to_files = defined( 'BLOGUPLOADDIR' ) ? str_replace('/files/','/',BLOGUPLOADDIR) : ABSPATH;
			
			$pic_org_path = $pic_org['file'];
			$pic_org_url = str_replace($abs_path_to_files,'/',$pic_org_path);
			
			$pic_org_size = getimagesize( $pic_org_path );
			$pic_org_size = ($pic_org_size[0]>$pic_org_size[1])?$pic_org_size[0]:$pic_org_size[1];
			
			if($pic_org_size <= $bp->album->bp_album_middle_size){
                $pic_mid_path = $pic_org_path;
                $pic_mid_url = $pic_org_url;
			} else {
				$pic_mid = wp_create_thumbnail( $pic_org_path, $bp->album->bp_album_middle_size );
                $pic_mid_path = str_replace( '//', '/', $pic_mid );
                $pic_mid_url = str_replace($abs_path_to_files,'/',$pic_mid_path);
                if (!$bp->album->bp_album_keep_original){
                	unlink($pic_org_path);
					$pic_org_url=$pic_mid_url;
					$pic_org_path=$pic_mid_path;
                }
			}
			if($pic_org_size <= $bp->album->bp_album_thumb_size){
                $pic_thumb_path = $pic_org_path;
                $pic_thumb_url = $pic_org_url;
			} else {
				$pic_thumb = image_resize( $pic_mid_path, $bp->album->bp_album_thumb_size, $bp->album->bp_album_thumb_size, true);
                $pic_thumb_path = str_replace( '//', '/', $pic_thumb );
                $pic_thumb_url = str_replace($abs_path_to_files,'/',$pic_thumb);
			}

            $owner_type = 'user';
            $owner_id = $bp->loggedin_user->id;
            $date_uploaded =  gmdate( "Y-m-d H:i:s" );
            $title = $_FILES['file']['name'];
            $description = ' ';
            $privacy = $priv_lvl;
            
            $id=bp_album_add_picture($owner_type,$owner_id,$title,$description,$priv_lvl,$date_uploaded,$pic_org_url,$pic_org_path,$pic_mid_url,$pic_mid_path,$pic_thumb_url,$pic_thumb_path);
            
			if($id)
				$feedback_message[] = __('Picture uploaded. Now you can change picture details.', 'bp-album'); 
			else {
				$error_flag = true;
				$feedback_message[] = __('There were problems saving picture details.', 'bp-album');
            }
		}
		
		if ($error_flag){
			bp_core_add_message( implode('&nbsp;', $feedback_message ),'error');
		} else {
			bp_core_add_message( implode('&nbsp;', $feedback_message ),'success' );
			bp_core_redirect( $bp->loggedin_user->domain . $bp->current_component . '/'.$bp->album->single_slug.'/' . $id.'/'.$bp->album->edit_slug.'/');
			die;
		}
		
	}
	
}
add_action('wp','bp_album_action_upload',3);
 
 

function bp_album_upload_dir() {
	global $bp;

	$user_id = $bp->loggedin_user->id;
	
	$dir = BP_ALBUM_UPLOAD_PATH;

	$siteurl = trailingslashit( get_blog_option( 1, 'siteurl' ) );
	$url = str_replace(ABSPATH,$siteurl,$dir);
	
	$bdir = $dir;
	$burl = $url;
	
	$subdir = '/' . $user_id;
	
	$dir .= $subdir;
	$url .= $subdir;

	if ( !file_exists( $dir ) )
		@wp_mkdir_p( $dir );

	return apply_filters( 'bp_album_upload_dir', array( 'path' => $dir, 'url' => $url, 'subdir' => $subdir, 'basedir' => $bdir, 'baseurl' => $burl, 'error' => false ) );

}

function bp_album_action_edit() {
	global $bp,$pictures_template;
	
	if ( $bp->current_component == $bp->album->slug && $bp->album->single_slug == $bp->current_action && $pictures_template->picture_count && isset($bp->action_variables[1]) && $bp->album->edit_slug == $bp->action_variables[1] &&  isset( $_POST['submit'] )) {
	
		check_admin_referer('bp-album-edit');
		
		$error_flag = false;
		$feedback_message = array();
		
		$id = $pictures_template->pictures[0]->id;
		
		// check title
		if(empty($_POST['title'])){
			$error_flag = true;
			$feedback_message[] = __( 'Picture Title can not be blank.', 'bp-album' );
		}
		
		// check description
		if( $bp->album->bp_album_require_description && empty($_POST['description'])){
			$error_flag = true;
			$feedback_message[] = __( 'Picture Description can not be blank.', 'bp-album' );
		}
		
		// check privacy
		if( !isset($_POST['privacy']) ){
			$error_flag = true;
			$feedback_message[] = __( 'Please select a privacy option.', 'bp-album' );	
		} else {
			$priv_lvl = intval($_POST['privacy']);
                       
                        // TODO: Refactor this, and the bp_album_max_privXX variable as an array.
                        switch ($priv_lvl) {
                            case "0": $pic_limit = $bp->album->bp_album_max_priv0_pictures; break;
                            case "1": $pic_limit = $bp->album->bp_album_max_priv1_pictures; break;
                            case "2": $pic_limit = $bp->album->bp_album_max_priv2_pictures; break;
                            case "3": $pic_limit = $bp->album->bp_album_max_priv3_pictures; break;
                            case "4": $pic_limit = $bp->album->bp_album_max_priv4_pictures; break;
                            case "5": $pic_limit = $bp->album->bp_album_max_priv5_pictures; break;
                            case "6": $pic_limit = $bp->album->bp_album_max_priv6_pictures; break;
                            case "7": $pic_limit = $bp->album->bp_album_max_priv7_pictures; break;
                            case "8": $pic_limit = $bp->album->bp_album_max_priv8_pictures; break;
                            case "9": $pic_limit = $bp->album->bp_album_max_priv9_pictures; break;
                            default: $pic_limit = null;
                        }


			if($priv_lvl == 10 )
				$pic_limit = is_site_admin() ? false : null;
			if( $pic_limit === null){ //costant don't exist
				$error_flag = true;
				$feedback_message[] = __( 'Privacy option is not correct.', 'bp-album' );	
			}elseif( $pic_limit !== false && $priv_lvl !== $pictures_template->pictures[0]->privacy && ( $pic_limit === 0|| $pic_limit <= bp_album_get_picture_count(array('privacy'=>$priv_lvl)) ) ){
				$error_flag = true;
				switch ($priv_lvl){
					case 0 :
						$feedback_message[] = __( 'You reached the limit for public pictures.', 'bp-album' ).' '.__( 'Please select another privacy option.', 'bp-album' );
						break;
					case 2 :
						$feedback_message[] = __( 'You reached the limit for pictures visible to community members.', 'bp-album' ).' '.__( 'Please select another privacy option.', 'bp-album' );
						break;
					case 4 :
						$feedback_message[] = __( 'You reached the limit for pictures visible to friends.', 'bp-album' ).' '.__( 'Please select another privacy option.', 'bp-album' );
						break;
					case 6 :
						$feedback_message[] = __( 'You reached the limit for private pictures.', 'bp-album' ).' '.__( 'Please select another privacy option.', 'bp-album' );
						break;
				}
			}
		}
		
		// check enable_comments
		if(bp_is_active('activity') && $bp->album->bp_album_enable_comments)
			if(!isset($_POST['enable_comments']) || ($_POST['enable_comments']!= 0 && $_POST['enable_comments']!= 1)){
				$error_flag = true;
				$feedback_message[] = __( 'Comments option is not correct.', 'bp-album' );
			}
		else
			$_POST['enable_comments']==0;
			
		
		if( !$error_flag ){
			if( bp_album_edit_picture($id,stripslashes($_POST['title']),stripslashes($_POST['description']),$priv_lvl,$_POST['enable_comments']) ){
				$feedback_message[] = __('Picture details saved.', 'bp-album');
			}else{
				$error_flag = true;
				$feedback_message[] = __('There were problems saving picture details.', 'bp-album');
			}
		}
		if ($error_flag){
			bp_core_add_message( implode('&nbsp;', $feedback_message ),'error');
		} else {
			bp_core_add_message( implode('&nbsp;', $feedback_message ),'success' );
			bp_core_redirect( $bp->loggedin_user->domain . $bp->current_component . '/'.$bp->album->single_slug.'/' . $id.'/');
			die;
		}
		
	}
	
}
add_action('wp','bp_album_action_edit',3);

function bp_album_action_delete() {
	global $bp,$pictures_template;;
	
	if ( $bp->current_component == $bp->album->slug && $bp->album->single_slug == $bp->current_action && $pictures_template->picture_count && isset($bp->action_variables[1]) && $bp->album->delete_slug == $bp->action_variables[1] ) {
		check_admin_referer('bp-album-delete-pic');
		
				
		if(!$pictures_template->picture_count){
			bp_core_add_message( __( 'This url is not valid.', 'bp-album' ), 'error' );
			return;
		}else{
			
			if ( !bp_is_my_profile() ) {
				bp_core_add_message( __( 'You don\'t have permission to delete this picture', 'bp-album' ), 'error' );
			} elseif (bp_album_delete_picture($pictures_template->pictures[0]->id)){
				bp_core_add_message( __( 'Picture deleted.', 'bp-album' ), 'success' );
				bp_core_redirect( $bp->displayed_user->domain . $bp->album->slug . '/'. $bp->album->pictures_slug .'/');
				die;
			}else{
				bp_core_add_message( __( 'There were problems deleting the picture.', 'bp-album' ), 'error' );
			}
		}
		bp_core_redirect( $bp->displayed_user->domain . $bp->album->slug . '/'. $bp->album->single_slug .'/'.$pictures_template->pictures[0]->id. '/');
		die;
	}
}
add_action('wp','bp_album_action_delete',3);

?>
