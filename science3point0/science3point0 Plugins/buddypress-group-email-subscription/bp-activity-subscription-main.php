<?php

require_once( WP_PLUGIN_DIR.'/buddypress-group-email-subscription/bp-activity-subscription-functions.php' );
require_once( WP_PLUGIN_DIR.'/buddypress-group-email-subscription/bp-activity-subscription-digest.php' );


class Group_Activity_Subscription extends BP_Group_Extension {	
		
	function group_activity_subscription() {
		global $bp;
		
		$this->name = 'Email Options';
		$this->slug = 'notifications';
		
		// Only enable the notifications nav item if the user is a member of the group
		if ( groups_is_user_member( $bp->loggedin_user->id , $bp->groups->current_group->id )  ) {
			$this->enable_nav_item = true;
		} else {
			$this->enable_nav_item = false;
		}

		$this->nav_item_position = 91;
		$this->enable_create_step = false;
		
		if ( get_option('ass-admin-can-send-email') == 'no' )
			$this->enable_edit_item = false;
					
		// hook in the css and js
		add_action ( 'wp_print_styles' , array( &$this , 'add_settings_stylesheet' ) );
		add_action( 'wp_head', array( &$this , 'ass_add_javascript' ),1 );
	}

	public function add_settings_stylesheet() {
        $style_url = WP_PLUGIN_URL . '/buddypress-group-email-subscription/css/bp-activity-subscription-css.css';
        $style_file = WP_PLUGIN_DIR . '/buddypress-group-email-subscription/css/bp-activity-subscription-css.css';
        if (file_exists($style_file)) {
            wp_register_style('activity-subscription-style', $style_url);
            wp_enqueue_style('activity-subscription-style');
        }
    }
    
	public function ass_add_javascript() {
		global $bp;
		if ( $bp->current_component == $bp->groups->slug ) {
			wp_register_script('bp-activity-subscription-js', WP_PLUGIN_URL . '/buddypress-group-email-subscription/bp-activity-subscription-js.js');
			wp_enqueue_script( 'bp-activity-subscription-js' );
		}
	}
	
	// Display the notification settings form
	function display() {
		ass_group_subscribe_settings();
	}

	
	// The remaining group API functions aren't used for this plugin but have to be overriden or api won't work
	
	function create_screen() {
		return false;
	}

	function create_screen_save() {
		return false;
	}

	function edit_screen() {
		// if ass-admin-can-send-email = no this won't show
		ass_admin_notice_form();
		return true;
	}

	function edit_screen_save() {
		return false;
	}

	function widget_display() { 
		return false;
	}
}

bp_register_group_extension( 'Group_Activity_Subscription' );

?>