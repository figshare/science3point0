<?php

##############################################
##                                          ##
##             plugin stuff                 ##
##                                          ##
##############################################


define ( 'BP_TWEETSTREAM_VERSION', '1.4' );
define ( 'BP_TWEETSTREAM_IS_INSTALLED', 1 );

##############################################
##                                          ##
##             local language               ##
##                                          ##
##############################################


add_action ( 'plugins_loaded', 'tweetstream_textdomain', 9 );

function tweetstream_textdomain() {
	$locale = apply_filters ( 'tweetstream_textdomain', get_locale () );
	$mofile = WP_PLUGIN_DIR . "/tweetstream/languages/$locale.mo";
	
	if (file_exists ( $mofile )) {
		load_textdomain ( 'tweetstream_lang', $mofile );
	}
}

################################################
##                                            ##
##  add extra filter option in dropdown       ##
##                                            ##
################################################


add_action ( 'bp_activity_filter_options', 'tweetstream_addFilter', 1 );
add_action ( 'bp_member_activity_filter_options', 'tweetstream_addFilter', 1 );

function tweetstream_addFilter() {
	echo '<option value="tweet">' . __ ( 'Show Tweets', 'tweetstream_lang' ) . '</option>';
}

################################################
##                                            ##
##  add action to add checkbox to update form ##
##                                            ##
################################################


add_action ( 'bp_activity_post_form_options', 'tweetstream_addCheckbox' );

//add twitter checkbox to form
function tweetstream_addCheckbox() {
	
	global $bp;
	$user_id = $bp->loggedin_user->id;
	tweetstream_checkTwitterAuth ( $user_id );
	
	if (get_site_option ( "tweetstream_consumer_key" )) {
		
		if (get_usermeta ( $user_id, 'tweetstream_token' )) {
			
			//add css and js
			echo '<script type="text/javascript" src="' . plugins_url ( "tweetstream/js/tweetstream.js" ) . '"></script>';
			
			$checkbox_on = get_usermeta ( $user_id, 'tweetstream_checkboxon' );
			
			if ($checkbox_on == 1) {
				echo '<div class="tweetstream_checkbox_container"><input type="checkbox" name="activity_to_twitter" id="activity_to_twitter" value="1" checked> ' . __ ( 'To twitter', 'tweetstream_lang' ) . '</div>';
			} else {
				echo '<div class="tweetstream_checkbox_container"><input type="checkbox" name="activity_to_twitter" id="activity_to_twitter" value="1"> ' . __ ( 'To twitter', 'tweetstream_lang' ) . '</div>';
			}
		
		} else {
			if (get_site_option ( 'tweetstream_user_settings_message' ) == 1) {
				echo ' <i>' . __ ( 'Want to tweet this message to? Check your ', 'tweetstream_lang' ) . '<a href="' . $bp->loggedin_user->domain . 'settings/tweetstream">' . __ ( 'tweetstream settings', 'tweetstream_lang' ) . '</a>.</i>';
			}
		}
	}
}

################################################
##                                            ##
##  add action to add checkbox to forum form  ##
##                                            ##
################################################


add_action ( 'groups_forum_new_topic_after', 'tweetstream_addTopicCheckbox' );
add_action ( 'bp_after_group_forum_post_new', 'tweetstream_addTopicCheckbox' );

//add twitter checkbox to form
function tweetstream_addTopicCheckbox() {
	global $bp;
	
	$user_id = $bp->loggedin_user->id;
	tweetstream_checkTwitterAuth ( $user_id );
	
	if (get_site_option ( "tweetstream_consumer_key" )) {
		
		if (get_usermeta ( $user_id, 'tweetstream_token' )) {
			
			//add css and js
			echo '<script type="text/javascript" src="' . plugins_url ( "tweetstream/js/tweetstream.js" ) . '"></script>';
			
			$checkbox_on = get_usermeta ( $user_id, 'tweetstream_checkboxon' );
			
			if ($checkbox_on == 1) {
				echo '<br><br><div class="tweetstream_checkbox_container"><input type="checkbox" name="topic_to_twitter" id="topic_to_twitter" value="1" checked> ' . __ ( 'To twitter', 'tweetstream_lang' ) . '</div>';
			} else {
				echo '<br><br><div class="tweetstream_checkbox_container"><input type="checkbox" name="topic_to_twitter" id="topic_to_twitter" value="1"> ' . __ ( 'To twitter', 'tweetstream_lang' ) . '</div>';
			}
		
		} else {
			if (get_site_option ( 'tweetstream_user_settings_message' ) == 1) {
				echo ' <i>' . __ ( 'Want to tweet this message to? Check your ', 'tweetstream_lang' ) . '<a href="' . $bp->loggedin_user->domain . 'settings/tweetstream">' . __ ( 'tweetstream settings', 'tweetstream_lang' ) . '</a>.</i>';
			}
		}
	}
}

#################################################
##                                             ##
##  add action to add checkbox to forum reply  ##
##                                             ##
#################################################


add_action ( 'groups_forum_new_reply_after', 'tweetstream_addTopicReplyCheckbox' );

//add twitter checkbox to form
function tweetstream_addTopicReplyCheckbox() {
	global $bp;
	
	$user_id = $bp->loggedin_user->id;
	tweetstream_checkTwitterAuth ( $user_id );
	
	if (get_site_option ( "tweetstream_consumer_key" )) {
		
		if (get_usermeta ( $user_id, 'tweetstream_token' )) {
			
			//add css and js
			echo '<link rel="stylesheet" href="' . plugins_url ( "tweetstream/css/style.css" ) . '" type="text/css" media="screen" />';
			echo '<script type="text/javascript" src="' . plugins_url ( "tweetstream/js/tweetstream.js" ) . '"></script>';
			
			$checkbox_on = get_usermeta ( $user_id, 'tweetstream_checkboxon' );
			
			if ($checkbox_on == 1) {
				echo '<div class="tweetstream_checkbox_container"><input type="checkbox" name="topicreply_to_twitter" id="topicreply_to_twitter" value="1" checked> ' . __ ( 'To twitter', 'tweetstream_lang' ) . '</div><br><br>';
			} else {
				echo '<div class="tweetstream_checkbox_container"><input type="checkbox" name="topicreply_to_twitter" id="topicreply_to_twitter" value="1" > ' . __ ( 'To twitter', 'tweetstream_lang' ) . '</div><br><br>';
			}
		
		} else {
			if (get_site_option ( 'tweetstream_user_settings_message' ) == 1) {
				echo ' <i>' . __ ( 'Want to tweet this message to? Check your ', 'tweetstream_lang' ) . '<a href="' . $bp->loggedin_user->domain . 'settings/tweetstream">' . __ ( 'tweetstream settings', 'tweetstream_lang' ) . '</a>.</i>';
			}
		}
	}
}

##############################################
##                                          ##
##        add tweet when new topic          ##
##                                          ##
##############################################


add_filter ( 'group_forum_topic_title_before_save', 'tweetstream_topic' );

//function with params from bp_activity_add
function tweetstream_topic() {
	
	global $bp;
	
	//get loged in user id
	$user_id = $bp->loggedin_user->id;
	$title = $_POST ['topic_title'];
	
	//check if #TWITTER is there if so then kick the content to twitter!
	$pos = strpos ( $title, "#TWITTER" );
	$title = tweetstream_filterTags ( $title );
	
	//twitter tag found
	if ($pos > 0) {
		add_filter ( 'bp_forums_new_topic', 'tweetstream_topicSaveId', 9 );
	}
	
	return $title;
}

function tweetstream_topicSaveId($id) {
	tweetstream_topicToTwitter ( $id );
	return $id;
}

function tweetstream_topicToTwitter($id) {
	
	global $bp;
	
	$topic_info = bp_forums_get_topic_details ( $id );
	
	//get loged in user id
	$user_id = $bp->loggedin_user->id;
	$content = $_POST ['topic_title'];
	
	//check if #TWITTER is there if so then kick the content to twitter!
	$pos = strpos ( $content, "#TWITTER" );
	$content = tweetstream_filterTags ( $content );
	
	//we found #TWITTER tag so push it to twitter
	if ($pos > 0) {
		
		//filter for url ceation
		$backlink = bp_get_group_permalink ( $bp->groups->current_group ) . 'forum/topic/' . $topic_info->topic_slug . '/';
		
		//create short url
		$backlink = tweetstream_getShortUrl ( $backlink );
		
		//make ik small enough for twitter but never shorten the backlink
		$max_length = 113;
		if (strlen ( $content > $max_length )) {
			$content = substr ( $content, 0, $max_length );
		}
		
		$content .= " " . $backlink;
		tweetstream_twitterIt ( $content );
	}

}

##############################################
##                                          ##
##        add tweet when new reply          ##
##                                          ##
##############################################


add_filter ( 'group_forum_post_text_before_save', 'tweetstream_topicReply', 9 );

function tweetstream_topicReply() {
	
	global $bp;
	
	//get loged in user id
	$user_id = $bp->loggedin_user->id;
	
	$content = $_POST ['reply_text'];
	if ($content == "") {
		$content = $_POST ['post_text'];
	}
	
	//check if #TWITTER is there if so then kick the content to twitter!
	$pos = strpos ( $content, "#TWITTER" );
	$content = tweetstream_filterTags ( $content );
	
	//twitter tag found
	if ($pos > 0) {
		add_filter ( 'group_forum_post_topic_id_before_save', 'tweetstream_topicReplySaveId', 9 );
	}
	
	return $content;
}

function tweetstream_topicReplySaveId($id) {
	
	tweetstream_topicReplyToTwitter ( $id );
	return $id;
}

function tweetstream_topicReplyToTwitter($id) {
	global $bp;
	
	$topic_info = bp_forums_get_topic_details ( $id );
	
	//filter for url ceation
	$backlink = bp_get_group_permalink ( $bp->groups->current_group ) . 'forum/topic/' . $topic_info->topic_slug . '/';
	
	//create short url
	$backlink = tweetstream_getShortUrl ( $backlink );
	
	//make ik small enough for twitter but never shorten the backlink
	$max_length = 95;
	if (strlen ( $content > $max_length )) {
		$content = substr ( $topic_info->topic_title, 0, $max_length );
	} else {
		$content = $topic_info->topic_title;
	}
	
	$content = __ ( 'Just responded to:', 'tweetstream_lang' ) . " " . $content . " " . $backlink;
	
	tweetstream_twitterIt ( $content );
}

##############################################
##                                          ##
##        add tweet when new activity       ##
##                                          ##
##############################################


add_filter ( 'bp_activity_content_before_save', 'tweetstream_activityToTwitter' );

//function with params from bp_activity_add
function tweetstream_activityToTwitter($content) {
	
	global $bp;
	
	//get loged in user id
	$user_id = $bp->loggedin_user->id;
	
	//check if #TWITTER is there if so then kick the content to twitter!
	$pos = strpos ( $content, "#TWITTER" );
	$content = tweetstream_filterTags ( $content );
	
	//we found #TWITTER tag so push it to twitter
	if ($pos > 0) {
		// out all html
		$tweet_content = strip_tags ( $content );
		$tweet_content = stripslashes ( $tweet_content );
		
		// how long is the content?
		$lenght_content = strlen ( $tweet_content );
		
		//how long is user domain
		if (get_usermeta ( $user_id, 'tweetstream_profilelink' ) == 1) {
			$lenght_userdomain = strlen ( $bp->loggedin_user->domain ) + 5;
			$max_length = 140 - $lenght_userdomain;
			$tweet_content = substr ( $tweet_content, 0, $max_length );
			
			if (strlen ( $tweet_content ) > 137) {
				$tweet_content .= "...";
			}
			
			$tweet_content .= " " . tweetstream_getShortUrl ( $bp->loggedin_user->domain );
		
		} else {
			$max_length = 137;
			$tweet_content = substr ( $tweet_content, 0, $max_length );
			if (strlen ( $tweet_content ) > 134) {
				$tweet_content .= "...";
			}
		}
		
		tweetstream_twitterIt ( $tweet_content );
		$tweet_content = "";
	}
	return $content;
}

##############################################
##                                          ##
##  function to send content to twitter     ##
##                                          ##
##############################################


function tweetstream_twitterIt($content) {
	
	global $bp;
	
	//get loged in user id
	$user_id = $bp->loggedin_user->id;
	
	//content related things
	$content = mb_convert_encoding ( $content, 'HTML-ENTITIES', "UTF-8" );
	$content = str_replace ( "@", "", $content );
	$content_len = strlen ( $content );
	
	//filter for other plugins
	$content = str_replace ( "#FACEBOOK", "", $content );
	$content = str_replace ( "#TWITTER", "", $content );
	$content = str_replace ( "#MYSPACE", "", $content );
	$content = str_replace ( "#LINKEDIN", "", $content );
	
	//create new twitter instance and login
	$token = get_usermeta ( $user_id, 'tweetstream_token' );
	$secret = get_usermeta ( $user_id, 'tweetstream_tokensecret' );
	
	if ($token && $content_len < 141) {
		
		require_once 'twitter/EpiCurl.php';
		require_once 'twitter/EpiOAuth.php';
		require_once 'twitter/EpiTwitter.php';
		
		$twitterObj = new EpiTwitter ( get_site_option ( "tweetstream_consumer_key" ), get_site_option ( "tweetstream_consumer_secret" ) );
		$twitterObj->setToken ( $token, $secret );
		
		$status_update = $twitterObj->post_statusesUpdate ( array ('status' => $content ) );
		$tweet_info = $status_update->response;
		return true;
	} else {
		return false;
	}

}

##############################################
##                                          ##
##  add tweets to activity stream           ##
##                                          ##
##############################################


add_action ( 'wp', 'tweetstream_runCron' );

function tweetstream_runCron() {
	
	global $bp, $wpdb;
	
	//every 10 minutes we need to update
	$cron_run = 0;
	$last_update = get_site_option ( 'tweetstream_cron' );
	$now = date ( 'dmYhmi' );
	$date_diff = $now - $last_update;
	
	$min = get_site_option ( 'tweetstream_cronrun' );
	if ($min == "") {
		$min = 10;
	}
	
	//may we run the cron?
	if ($date_diff <= - $min) {
		$cron_run = 1;
	} elseif ($date_diff >= $min) {
		$cron_run = 1;
	} 
	
	if ($cron_run == 1) {
		
		//some cron like stuff
		if (get_site_option ( 'tweetstream_user_settings_syncbp' ) == 0) {
			$user_metas = $wpdb->get_results ( $wpdb->prepare ( "SELECT user_id FROM $wpdb->usermeta WHERE meta_key='tweetstream_token';" ) );
			if ($user_metas) {
				foreach ( $user_metas as $user_meta ) {
					
					//is the max import reached for this user today?
					if (get_site_option ( 'tweetstream_user_settings_maximport' ) != '') {
						
						if (get_usermeta ( $user_meta->user_id, 'tweetstream_daycounter' ) <= get_site_option ( 'tweetstream_user_settings_maximport' )) {
							tweetstream_getTweets ( $user_meta->user_id, $bp->root_domain );
						}
					} else {
						tweetstream_getTweets ( $user_meta->user_id, $bp->root_domain );
					}
				}
			}
		}
		//set new date stamp for cron
		update_site_option ( 'tweetstream_cron', trim ( date ( 'dmYhmi' ) ) );
	}
	
	//keeping the memory clean
	unset ( $cron_run );
	unset ( $last_update );
	unset ( $now );
	unset ( $date_diff );
}

//////////////////////
//                  //
// FILTER FUNCTION  //
//                  //
//////////////////////


function filterText($text, $filters) {
	
	$return = 0;
	$text = strip_tags ( $text );
	$text = strtolower ( $text );
	$text = " " . $text . " ";
	
	if ($filters) {
		$arrFilters = explode ( ",", $filters );
		foreach ( $arrFilters as $filter ) {
			if (strpos ( $text, $filter ) > 0) {
				$return = 1;
			}
		}
	} else {
		$return = 2;
	}
	
	//keeping memory clean
	unset ( $pos_filter );
	unset ( $arrFilters );
	return $return;
}

////////////////////////////////////
//                                //
// FUNCTION TO GET TWEETS OF USER //
//                                //
////////////////////////////////////


function tweetstream_getTweets($user_id, $root_domain) {
	
	//global $wpdb for database access
	global $wpdb;
	
	//twitter API files
	require_once 'twitter/EpiCurl.php';
	require_once 'twitter/EpiOAuth.php';
	require_once 'twitter/EpiTwitter.php';
	
	//allowed to import at all?
	if (get_site_option ( 'tweetstream_user_settings_syncbp' ) == 0) {
		
		//set user fields
		$user_data = $wpdb->get_results ( $wpdb->prepare ( "SELECT user_login,display_name FROM $wpdb->users WHERE id=$user_id;" ) );
		$user_login = $user_data [0]->user_login;
		$user_fullname = bp_core_get_user_displayname ( $user_id );
		
		//keeping the memory clean
		unset ( $user_data );
		
		//check if user wants to sync or not
		if (get_usermeta ( $user_id, 'tweetstream_synctoac' ) == 1) {
			//verify account
			$twitterObj = new EpiTwitter ( get_site_option ( "tweetstream_consumer_key" ), get_site_option ( "tweetstream_consumer_secret" ) );
			$twitterObj->setToken ( get_usermeta ( $user_id, 'tweetstream_token' ), get_usermeta ( $user_id, 'tweetstream_tokensecret' ) );
			$userInfo = $twitterObj->get_accountVerify_credentials ();
			
			//check if we still can accesss this account if not revoke
			if ($userInfo->error) {
				update_usermeta ( $user_id, 'tweetstream_token', '' );
				update_usermeta ( $user_id, 'tweetstream_tokensecret', '' );
			}
			
			
			//check for errors if no errors continue
			if (! isset ( $error )) {
				
				//update the mention name
				update_usermeta ( $user_id, 'tweetstream_mention', "@" . $userInfo->response ['screen_name'] );
				
				//get user timeline, decode json repsonse
				$tweetsobj = $twitterObj->get ( '/statuses/user_timeline.json?count=10' );
				$tweets = json_decode ( $tweetsobj->responseText );
				
				//check if there are tweets if so continue
				if ($tweets) {
					//loop trough tweets
					foreach ( $tweets as $tweet ) {
						
						//set tweet_text and filter some stuff out
						$tweet_text = $tweet->text;
						$tweet_text = strip_tags ( $tweet_text );
						
						//lets make something better for filters to cleanup code
						$filter1 = filterText ( $tweet_text, get_site_option ( 'tweetstream_filter' ) );
						$filter2 = filterText ( $tweet_text, get_site_option ( 'tweetstream_filterexplicit' ) );
						$filter3 = filterText ( $tweet_text, get_usermeta ( $user_id, 'tweetstream_filtergood' ) );
						$filter4 = filterText ( $tweet_text, get_usermeta ( $user_id, 'tweetstream_filterbad' ) );
						
						$filter_pass = 0;
						if ($filter1 == 1 or $filter1 == 2) {
							$filter_pass = 1;
						}
						if ($filter_pass != 0 && $filter2 == 1) {
							$filter_pass = 0;
						}
						if ($filter_pass != 0 && $filter3 == 1) {
							$filter_pass = 1;
						}
						if ($filter_pass != 0 && $filter4 == 1) {
							$filter_pass = 0;
						}
						
						//mentions filtering
						if (get_usermeta ( $user_id, 'tweetstream_filtermentions' ) == 1 && $filter_pass == 1) {
							$pattern = '/[@]+([A-Za-z0-9-_]+)/';
							$found_mention = preg_match ( $pattern, $tweet_text );
							if ($found_mention) {
								$filter_pass = 0;
							}
						}
						
						//we passed the filter lets continue
						if ($filter_pass == 1) {
							
							//check if we already have this tweet
							$activity_info = bp_activity_get ( array ('filter' => array ('secondary_id' => $tweet->id ) ) );
							if ($activity_info ['activities'] [0]->id) {
								$exist = 1;
							}
							
							//tweet from us?
							$pos_source = strpos ( $tweet->source, $root_domain );
							if ($pos_source > 0) {
								$exist = 1;
							}
							
							//its a new one and passed all checks! Lets put it into the database.
							if ($exist == 0) {
								
								//create new activity instance
								$activity = new BP_Activity_Activity ();
								
								//convert tweet time to timestamp
								$date_recorded = strtotime ( $tweet->created_at );
								$date_recorded = gmdate ( 'Y-m-d H:i:s', $date_recorded );
								$screen_name = $tweet->user->screen_name;
								
								$activity = new BP_Activity_Activity ();
								$activity->user_id = $user_id;
								$activity->component = "tweetstream";
								$activity->type = "tweet";
								
								if (BP_ENABLE_ROOT_PROFILES == "true") {
									$activity->action = '<a href="' . $root_domain . '/' . $user_login . '/" title="' . $user_login . '">' . $user_fullname . '</a>&nbsp;<a href="http://www.twitter.com/' . $screen_name . '"><img src="/wp-content/plugins/tweetstream/twitter-icon.png"></a> ' . __ ( 'posted a', 'tweetstream_lang' ) . ' <a href="http://www.twitter.com/' . $screen_name . '">' . __ ( 'tweet', 'tweetstream_lang' ) . '</a>:';
								} else {
									$activity->action = '<a href="' . $root_domain . '/' . BP_MEMBERS_SLUG . '/' . $user_login . '/" title="' . $user_login . '">' . $user_fullname . '</a>&nbsp;<a href="http://www.twitter.com/' . $screen_name . '"><img src="/wp-content/plugins/tweetstream/twitter-icon.png"></a> ' . __ ( 'posted a', 'tweetstream_lang' ) . ' <a href="http://www.twitter.com/' . $screen_name . '">' . __ ( 'tweet', 'tweetstream_lang' ) . '</a>:';
								}
								
								$activity->content = $tweet_text;
								$activity->primary_link = "";
								$activity->secondary_item_id = $tweet->id;
								$activity->date_recorded = $date_recorded;
								$activity->hide_sitewide = 0;
								$activity->save ();
								
								//update day counter
								if (get_usermeta ( $user_id, 'tweetstream_counterdate' ) != date ( 'd-m-Y' )) {
									update_usermeta ( ( int ) $user_id, 'tweetstream_daycounter', 0 );
									update_usermeta ( ( int ) $user_id, 'tweetstream_counterdate', date ( 'd-m-Y' ) );
								}
								
								$cur_counter = get_usermeta ( $user_id, 'tweetstream_daycounter' );
								update_usermeta ( ( int ) $user_id, 'tweetstream_daycounter', $cur_counter + 1 );
							}
						}
						//keeping the memory clean
						unset ( $filter_pass );
						unset ( $activity );
						unset ( $cur_counter );
						unset ( $exist );
						unset ( $date_recorded );
						unset ( $screen_name );
						unset ( $pos_source );
						unset ( $found_mention );
						unset ( $pattern );
						unset ( $user_fullname );
						unset ( $user_login );
						unset ( $userInfo );
					}
				}
			}
		}
	}
	//keeping the memory clean
	unset ( $tweets );
	unset ( $twitterObj );
}

##############################################
##                                          ##
##  delete tweet from twitter when deleting ##
##  activity item (if tweetstream)          ##
##                                          ##
##############################################


add_action ( 'bp_activity_action_delete_activity', 'tweetstream_deletetweet', 10, 2 );

//function with params from bp_activity_delete
function tweetstream_deleteTweet($activity_id, $user_id) {
	
	global $bp;
	
	require_once 'twitter/EpiCurl.php';
	require_once 'twitter/EpiOAuth.php';
	require_once 'twitter/EpiTwitter.php';
	
	//get loged in user id
	$user_id = $bp->loggedin_user->id;
	$deletetweet = get_usermeta ( $user_id, 'tweetstream_deletetweet' );
	
	if ($deletetweet == 1) {
		
		//get activity details
		$activity = new BP_Activity_Activity ( $activity_id );
		
		//get type component and tweet_id
		$type = $activity->type;
		$component = $activity->component;
		$tweet_id = $activity->secondary_item_id;
		
		//componement tweetstream and type tweet?
		if ($component == "tweetstream" && $type == "tweet") {
			//create new twitter instance and login
			$token = get_usermeta ( $user_id, 'tweetstream_token' );
			$secret = get_usermeta ( $user_id, 'tweetstream_tokensecret' );
			
			$twitterObj = new EpiTwitter ( get_site_option ( "tweetstream_consumer_key" ), get_site_option ( "tweetstream_consumer_secret" ) );
			$twitterObj->setToken ( $token, $secret );
			
			$status_delete = $twitterObj->post_statusesDestroy ( array ('id' => $tweet_id ) );
			$status_delete->response;
		}
	}
	
	//keeping the memory clean
	unset ( $user_id );
	unset ( $deletetweet );
	unset ( $activity );
	unset ( $type );
	unset ( $component );
	unset ( $tweet_id );
	unset ( $token );
	unset ( $secret );
	unset ( $twitterObj );
	unset ( $status_delete );
	
	return true;
}

##############################################
##                                          ##
##      Tweetstream user settings page      ##
##                                          ##
##############################################


function tweetstream_settings_screen() {
	
	global $bp;
	
	//security fix
	if ($bp->displayed_user->id != $bp->loggedin_user->id) {
		header ( 'location:' . $bp->root_domain );
	}
	
	add_action ( 'bp_template_title', 'tweetstream_settings_screen_title' );
	add_action ( 'bp_template_content', 'tweetstream_settings_screen_content' );
	bp_core_load_template ( apply_filters ( 'bp_core_template_plugin', 'members/single/plugins' ) );
}

function tweetstream_settings_screen_title() {
	__ ( 'Tweetstream', 'tweetstream_lang' );
}

function tweetstream_settings_screen_content() {
	
	global $bp;
	
	//get user id
	$user_id = $bp->loggedin_user->id;
	
	require_once 'twitter/EpiCurl.php';
	require_once 'twitter/EpiOAuth.php';
	require_once 'twitter/EpiTwitter.php';
	
	//create new twitter object
	$twitterObj = new EpiTwitter ( get_site_option ( "tweetstream_consumer_key" ), get_site_option ( "tweetstream_consumer_secret" ) );
	
	//if post
	if ($_POST) {
		
		//update user meta data
		update_usermeta ( $user_id, 'tweetstream_deletetweet', $_POST ['tweetstream_deletetweet'] );
		update_usermeta ( $user_id, 'tweetstream_checkboxon', $_POST ['tweetstream_checkboxon'] );
		update_usermeta ( $user_id, 'tweetstream_synctoac', $_POST ['tweetstream_synctoac'] );
		update_usermeta ( $user_id, 'tweetstream_profilelink', $_POST ['tweetstream_profilelink'] );
		update_usermeta ( $user_id, 'tweetstream_filtermentions', $_POST ['tweetstream_filtermentions'] );
		update_usermeta ( $user_id, 'tweetstream_filtergood', $_POST ['tweetstream_filtergood'] );
		update_usermeta ( $user_id, 'tweetstream_filterbad', $_POST ['tweetstream_filterbad'] );
		
		//show message
		echo ' <div id="message" class="updated fade">
				<p>' . __ ( 'Settings saved', 'tweetstream_lang' ) . '</p>
			</div>
		';
	}
	
	//put some options into variables
	$tweetstream_deletetweet = get_usermeta ( $user_id, 'tweetstream_deletetweet' );
	$tweetstream_checkboxon = get_usermeta ( $user_id, 'tweetstream_checkboxon' );
	$tweetstream_synctoac = get_usermeta ( $user_id, 'tweetstream_synctoac' );
	$tweetstream_profilelink = get_usermeta ( $user_id, 'tweetstream_profilelink' );
	$tweetstream_filtermentions = get_usermeta ( $user_id, 'tweetstream_filtermentions' );
	$tweetstream_filtergood = get_usermeta ( $user_id, 'tweetstream_filtergood' );
	$tweetstream_filterbad = get_usermeta ( $user_id, 'tweetstream_filterbad' );
	
	if (get_usermeta ( $user_id, 'tweetstream_token' )) {
		echo '<form id="settings_form" action="' . $bp->loggedin_user->domain . 'settings/tweetstream/" method="post">
        <h3>' . __ ( 'Tweetstream setting', 'tweetstream_lang' ) . '</h3>
        ';
		echo '<b>' . __ ( 'Permission', 'tweetstream_lang' ) . '</b><br>' . __ ( 'You already gave permission.', 'tweetstream_lang' ) . '<br/> ' . __ ( 'To disallow click the link below and choose "Deny"', 'tweetstream_lang' ) . '</b><br><br>';
		
		?>

<a href="<?php
		echo $twitterObj->getAuthorizationUrl ();
		?>"><?php
		echo __ ( 'Authorize with twitter', 'tweetstream_lang' );
		?></a>
<br />
<br />

<table id="activity-notification-settings" class="notification-settings">
	<tr class="alt">
		<th class="icon"></th>
		<th class="title"><?php
		echo __ ( 'Options', 'tweetstream_lang' );
		?></th>
		<th class="yes"><?php
		echo __ ( 'Yes', 'tweetstream_lang' );
		?></th>
		<th class="no"><?php
		echo __ ( 'No', 'tweetstream_lang' );
		?></th>
	</tr>

	<tr>
		<td></td>
		<td><?php
		echo __ ( 'Delete tweet from twitter when deleted in activity.', 'tweetstream_lang' );
		?></td>
		<td class="yes"><input type="radio" name="tweetstream_deletetweet"
			id="tweetstream_deletetweet" value="1"
			<?php
		if ($tweetstream_deletetweet == 1) {
			echo 'checked';
		}
		?>></td>
		<td class="no"><input type="radio" name="tweetstream_deletetweet"
			id="tweetstream_deletetweet" value="0"
			<?php
		if ($tweetstream_deletetweet == 0) {
			echo 'checked';
		}
		?>></td>
	</tr>


	<?php
		if (get_site_option ( 'tweetstream_user_settings_syncbp' ) == 0) {
			?>

	<tr class="alt">
		<td></td>
		<td><?php
			echo __ ( 'Always check checkbox "To twitter"', 'tweetstream_lang' );
			?></td>
		<td class="yes"><input type="radio" name="tweetstream_checkboxon"
			id="tweetstream_checkboxon" value="1"
			<?php
			if ($tweetstream_checkboxon == 1) {
				echo 'checked';
			}
			?>></td>
		<td class="no"><input type="radio" name="tweetstream_checkboxon"
			id="tweetstream_checkboxon" value="0"
			<?php
			if ($tweetstream_checkboxon == 0) {
				echo 'checked';
			}
			?>></td>
	</tr>

	<tr>
		<td></td>
		<td><?php
			echo __ ( 'Synchronize tweets to my activity', 'tweetstream_lang' );
			?></td>
		<td class="yes"><input type="radio" name="tweetstream_synctoac"
			id="tweetstream_synctoac" value="1"
			<?php
			if ($tweetstream_synctoac == 1) {
				echo 'checked';
			}
			?>></td>
		<td class="no"><input type="radio" name="tweetstream_synctoac"
			id="tweetstream_synctoac" value="0"
			<?php
			if ($tweetstream_synctoac == 0) {
				echo 'checked';
			}
			?>></td>
	</tr>

	<tr class="alt">
		<td></td>
		<td><?php
			echo __ ( 'Import tweets containing @names', 'tweetstream_lang' );
			?></td>
		<td class="yes"><input type="radio" name="tweetstream_filtermentions"
			id="tweetstream_filtermentions" value="0"
			<?php
			if ($tweetstream_filtermentions == 0) {
				echo 'checked';
			}
			?>></td>
		<td class="no"><input type="radio" name="tweetstream_filtermentions"
			id="tweetstream_filtermentions" value="1"
			<?php
			if ($tweetstream_filtermentions == 1) {
				echo 'checked';
			}
			?>></td>
	</tr>

	<?php
		}
		?>

	<tr>
		<td></td>
		<td><?php
		echo __ ( 'Add my profile link after my tweet', 'tweetstream_lang' );
		?></td>
		<td class="yes"><input type="radio" name="tweetstream_profilelink"
			id="tweetstream_profilelink" value="1"
			<?php
		if ($tweetstream_profilelink == 1) {
			echo 'checked';
		}
		?>></td>
		<td class="no"><input type="radio" name="tweetstream_profilelink"
			id="tweetstream_profilelink" value="0"
			<?php
		if ($tweetstream_profilelink == 0) {
			echo 'checked';
		}
		?>></td>
	</tr>
</table>


<?php
		if (get_site_option ( 'tweetstream_user_settings_syncbp' ) == 0) {
			?>

<b><?php
			echo __ ( 'Filters', 'tweetstream_lang' );
			?></b>
<br />
<?php
			echo __ ( 'With filter you can decide what will be imported and what not. ', 'tweetstream_lang' );
			?>
<br />
<?php
			echo __ ( 'By adding words in the "Good" filter only tweets with those words in it will be imported.', 'tweetstream_lang' );
			?>
<br />
<?php
			echo __ ( 'By adding words in the "Bad" filter tweets with those words won\'t be imported.', 'tweetstream_lang' );
			?>
<br />

<br />

<table id="activity-notification-settings" class="notification-settings">
	<tr>
		<th><?php
			echo __ ( 'Filters (tweets to activity)', 'tweetstream_lang' );
			?></th>
		<th></th>
		<th></th>
	</tr>

	<tr>
		<td></td>
		<td><?php
			echo __ ( 'Good filter (comma seperated)', 'tweetstream_lang' );
			?></td>
		<td><input type="text" name="tweetstream_filtergood"
			value="<?php
			echo $tweetstream_filtergood;
			?>" size="50" /></td>
	</tr>

	<tr class="alt">
		<td></td>
		<td><?php
			echo __ ( 'Bad filter (comma seperated)', 'tweetstream_lang' );
			?></td>
		<td><input type="text" name="tweetstream_filterbad"
			value="<?php
			echo $tweetstream_filterbad;
			?>" size="50" /></td>
	</tr>
</table>

<?php
		}
		?>

<input type="submit"
	value="<?php
		echo __ ( 'Save settings', 'tweetstream_lang' );
		?>">
<?php
		
		echo '</form>';
	} else {
		echo '<b>' . __ ( 'Permission', 'tweetstream_lang' ) . '</b><br>' . __ ( 'You can setup your tweetstream over here.', 'tweetstream_lang' ) . '<br>
			' . __ ( 'Before u can see al settings please authorize on twitter, to do so click on the link below.', 'tweetstream_lang' ) . '<br><br>';
		echo '<a href="' . $twitterObj->getAuthorizationUrl () . '">' . __ ( 'Authorize with twitter', 'tweetstream_lang' ) . '</a><br/><br/>';
	}
	
	//keeping the memory clean
	unset ( $tweetstream_deletetweet );
	unset ( $tweetstream_checkboxon );
	unset ( $tweetstream_synctoac );
	unset ( $tweetstream_profilelink );
	unset ( $tweetstream_filtermentions );
	unset ( $tweetstream_filtergood );
	unset ( $tweetstream_filterbad );

}

##############################################
##                                          ##
##             setup navigation             ##
##                                          ##
##############################################


function tweetstream_setup_nav() {
	global $bp;
	
	if (get_site_option ( "tweetstream_consumer_key" )) {
		bp_core_new_subnav_item ( array ('name' => __ ( 'Tweetstream', 'tweetstream_lang' ), 'slug' => 'tweetstream', 'parent_url' => $bp->loggedin_user->domain . 'settings/', 'parent_slug' => 'settings', 'screen_function' => 'tweetstream_settings_screen', 'position' => 40 ) );
	}
}

tweetstream_setup_nav ();

##############################################
##                                          ##
##               admin pages                ##
##                                          ##
##############################################


add_action ( 'admin_menu', 'tweetstream_admin' );

function tweetstream_admin() {
	if (is_admin ()) {
		
		/* Add the administration tab under the "Site Admin" tab for site administrators */
		bp_core_add_admin_menu_page ( array ('menu_title' => __ ( 'Tweetstream', 'tweetstream_lang' ), 'page_title' => __ ( 'Tweetstream', 'tweetstream_lang' ), 'access_level' => 10, 'file' => 'tweetstream-admin', 'function' => 'tweetstream_welcome', 'icon_url' => plugins_url ( 'images/icon-small.png', __FILE__ ) ) );
		
		add_submenu_page ( 'tweetstream-admin', __ ( 'General Settings', 'tweetstream_lang' ), __ ( 'General Settings', 'tweetstream_lang' ), 'manage_options', 'tweetstream-settings', 'tweetstream_settings' );
		add_submenu_page ( 'tweetstream-admin', __ ( 'Filter Settings', 'tweetstream_lang' ), __ ( 'Filter Settings', 'tweetstream_lang' ), 'manage_options', 'tweetstream-filters', 'tweetstream_filters' );
		add_submenu_page ( 'tweetstream-admin', __ ( 'Users', 'tweetstream_lang' ), __ ( 'Users', 'tweetstream_lang' ), 'manage_options', 'tweetstream-users', 'tweetstream_users' );
		add_submenu_page ( 'tweetstream-admin', __ ( 'Statitics', 'tweetstream_lang' ), __ ( 'Statitics', 'tweetstream_lang' ), 'manage_options', 'tweetstream-statitics', 'tweetstream_statitics' );
		add_submenu_page ( 'tweetstream-admin', __ ( 'Help / Future updates', 'tweetstream_lang' ), __ ( 'Help / Future updates', 'tweetstream_lang' ), 'manage_options', 'tweetstream-help', 'tweetstream_help' );
	}
}

##############################################
##                                          ##
##                 welcome                  ##
##                                          ##
##############################################


function tweetstream_welcome() {
	
	?>
<div class="wrap"><br />
<img src="<?php
	echo plugins_url ( 'images/icon.png', __FILE__ );
	?>"
	style="float: left;">
<h2 style="float: left; line-height: 5px; padding-left: 5px;"><?php
	echo __ ( 'Tweetstream' );
	?></h2>
<br />

<br />
<br />
<b><?php
	echo __ ( 'Welcome to tweetstream!', 'tweetstream_lang' );
	?></b><br />
<br />
<?php
	echo __ ( 'With this plugin you can let our users synchronise there activity updates with twitter and back.', 'tweetstream_lang' );
	?><br />
<?php
	echo __ ( 'Have a lot of fun!', 'tweetstream_lang' );
	?><br />
<br />
<?php
	echo __ ( 'Greetings', 'tweetstream_lang' );
	?>,<br />
<?php
	echo __ ( 'Peter Hofman', 'tweetstream_lang' );
	?><br />
<?php
	echo __ ( 'Developer', 'tweetstream_lang' );
	?><br />

<br/>

<img src="<?= plugins_url ( 'screenshot-1.png', __FILE__ );?>" width="300">
<img src="<?= plugins_url ( 'screenshot-2.png', __FILE__ );?>" width="300">
<img src="<?= plugins_url ( 'screenshot-3.png', __FILE__ );?>" width="300">
<img src="<?= plugins_url ( 'screenshot-4.png', __FILE__ );?>" width="300">
<img src="<?= plugins_url ( 'screenshot-5.png', __FILE__ );?>" width="300">
<img src="<?= plugins_url ( 'screenshot-6.png', __FILE__ );?>" width="300">
<img src="<?= plugins_url ( 'screenshot-7.png', __FILE__ );?>" width="300">
<img src="<?= plugins_url ( 'screenshot-8.png', __FILE__ );?>" width="300">
<img src="<?= plugins_url ( 'screenshot-9.png', __FILE__ );?>" width="300">

<h2><?php
	echo __ ( 'Donate', 'tweetstream_lang' );
	?></h2>

			<?php
	echo __ ( 'Do do like this plugin? Please donate, so i can stay improving this plugin and give support.', 'tweetstream_lang' );
	?>
<br />
<br />
<a
	href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=TKBY4JM6WDSD2"
	target="_blank"><img
	src="https://www.paypal.com/en_US/i/btn/btn_donate_LG.gif"></a> <br />
<br />

</div>
<?php
}


##############################################
##                                          ##
##                 help                  ##
##                                          ##
##############################################


function tweetstream_help() {
	
	?>
<div class="wrap"><br />
<img src="<?php
	echo plugins_url ( 'images/icon.png', __FILE__ );
	?>"
	style="float: left;">
<h2 style="float: left; line-height: 5px; padding-left: 5px;"><?php
	echo __ ( 'Tweetstream help / future update','tweetstream_lang' );
	?></h2>
<br />
<br />
<br />
<b><?= __ ( 'Need some help?', 'tweetstream_lang' ); ?></b><br />
<br />
<?= __ ( 'For any help, questions,translations or feauture requests please goto:', 'tweetstream_lang' );?><br />
<a href="http://buddypress.org/community/groups/tweetstream/">http://buddypress.org/community/groups/tweetstream/</a><br />
<br />
<?=  __ ( 'P.S. This page will be updated with an F.A.Q in time.', 'tweetstream_lang' );?>,<br />
<br/>

<b><?= __ ( 'What to come...', 'tweetstream_lang' ); ?></b><br />

<br/>
 <li>Translation downloads (outside the standard updates)</li>	
 <li>Mention conversion</li>
 <li>Complete new Twitter client for stability</li>
 <li>F.A.Q page</li>




<br />


</div>
<?php
}

##############################################
##                                          ##
##                 users                    ##
##                                          ##
##############################################


function tweetstream_users() {
	
	?>
	<div class="wrap"><br />
<img src="<?php
	echo plugins_url ( 'images/icon.png', __FILE__ );
	?>"
	style="float: left;">
<h2 style="float: left; line-height: 5px; padding-left: 5px;"><?php
	echo __ ( 'Tweetstream users' );
	?></h2>
<br /><br /><br />
	<?php 
	global $bp, $wpdb;
	
	//reset a user (delete all tweetstream user meta data for user
	if($_GET['user_id']){
		if($_GET['action'] == "reset") {
			if($_GET['confirmed'] == "1") {
			
			delete_user_meta($_GET['user_id'],"tweetstream_tweetstream_synctoac"); 
			delete_user_meta($_GET['user_id'],"tweetstream_mention");
			delete_user_meta($_GET['user_id'],"tweetstream_lastupdate");
			delete_user_meta($_GET['user_id'],"tweetstream_deletetweet");
			delete_user_meta($_GET['user_id'],"tweetstream_checkboxon");
			delete_user_meta($_GET['user_id'],"tweetstream_counterdate");
			delete_user_meta($_GET['user_id'],"tweetstream_tokensecret");
			delete_user_meta($_GET['user_id'],"tweetstream_filtermentions");
			delete_user_meta($_GET['user_id'],"tweetstream_synctoac");
			delete_user_meta($_GET['user_id'],"tweetstream_counterdate");
			delete_user_meta($_GET['user_id'],"tweetstream_checkboxon");
			delete_user_meta($_GET['user_id'],"tweetstream_daycounter");
			delete_user_meta($_GET['user_id'],"tweetstream_deletetweet");
			delete_user_meta($_GET['user_id'],"tweetstream_filtergood");
			delete_user_meta($_GET['user_id'],"tweetstream_filterbad");
			delete_user_meta($_GET['user_id'],"tweetstream_filtertoactivity");
			delete_user_meta($_GET['user_id'],"tweetstream_filtertotwitter");
			delete_user_meta($_GET['user_id'],"tweetstream_profilelink");
			delete_user_meta($_GET['user_id'],"tweetstream_screenname");
			delete_user_meta($_GET['user_id'],"tweetstream_token");
			
			//show message
			echo ' <div id="message" class="updated fade">
					<p>' . __ ( 'User resetted', 'tweetstream_lang' ) . '</p>
				</div>
			';
			}else{
				//show message
			echo ' <div id="message" class="updated fade">
					<p>' . __ ( 'Are you sure ?', 'tweetstream_lang' ) . '
					<a href="?page=tweetstream-users&action=reset&user_id='.$_GET['user_id'].'&confirmed=1">Yes</a> | <a href="?page=tweetstream-users">No</a></p>
				</div>
			';
			}
		}
	}
	
	
echo __('Below is a list of all users who are using tweetstream, you can also reset there tweetstream settings.','tweetstream_lang')."<br/>";
echo __('When you reset a user they need to re-authenticate on twitter.','tweetstream_lang')."<br/>"; 
echo __('Already imported tweets will not be deleted!','tweetstream_lang')."<br/><br/>"; 
	
	?>




<table class="widefat fixed" cellspacing="0">

<thead>
<tr class="thead">
	<th scope="col" id="cb" class="manage-column column-cb check-column" style=""></th>
	<th scope="col" id="username" class="manage-column column-username" style="">Username</th>
	<th scope="col" id="email" class="manage-column column-email" style="">E-mail</th>
	<th scope="col" id="email" class="manage-column column-name" style=""><?= __('Twitter','tweetstream_lang');?></th>
	<th scope="col" id="role" class="manage-column column-role" style=""><?= __('Tweets imported','tweetstream_lang');?></th>
</tr>
</thead>

<tfoot>
<tr class="thead">
	<th scope="col" id="cb" class="manage-column column-cb check-column" style=""></th>
	<th scope="col" id="username" class="manage-column column-username" style="">Username</th>
	<th scope="col" id="email" class="manage-column column-email" style="">E-mail</th>
	<th scope="col" id="email" class="manage-column column-name" style=""><?= __('Twitter','tweetstream_lang');?></th>
	<th scope="col" id="role" class="manage-column column-role" style=""><?= __('Tweets imported','tweetstream_lang');?></th>
</tr>
</tfoot>
<tbody id="users" class="list:user user-list">
<?php 
//get all users who have set-up there tweetstream


			$user_metas = $wpdb->get_results ( $wpdb->prepare ( "SELECT user_id FROM $wpdb->usermeta WHERE meta_key='tweetstream_token';" ) );
			if ($user_metas) {
				foreach ( $user_metas as $user_meta ) {
				
				//get userdata
				$user_data = $wpdb->get_results ( $wpdb->prepare ( "SELECT * FROM $wpdb->users WHERE id=$user_meta->user_id;" ) );
				$user_data = $user_data[0];
				
				
				$twitter_profile = str_replace("@","http://www.twitter.com/",get_usermeta($user_data->ID,'tweetstream_mention'));
				
				//count imported tweets
  			    $imported_tweets = count($wpdb->get_results ( $wpdb->prepare ( "SELECT * FROM ".$bp->activity->table_name." WHERE user_id=$user_meta->user_id AND type='tweet';" )));
					echo " 
					<tr id='user-29'>
						<th scope='row' class='check-column'></th>
						<td class='username column-username'>
						".get_avatar($user_data->ID,32)." 
							<strong><a href='".$bp->root_domain."/".BP_MEMBERS_SLUG."/".$user_data->user_login."'>".$user_data->user_login."</a></strong><br />
							<span class='delete'><a href='?page=tweetstream-users&action=reset&user_id=".$user_data->ID."'>Reset</a></span></div>
						</td>
						<td class='email column-email'>
							<a href='mailto:".$user_data->user_email."' title='E-mail: ".$user_data->user_email."'>".$user_data->user_email."</a>
						</td>
						
						<td class='email column-email'>
							<a href='".$twitter_profile."' title='".$twitter_profile."' target='_blanc'>".$twitter_profile."</a>
						</td>
						
						<td class='posts column-posts num'>".$imported_tweets."</td>
						</tr>
					";
					
			}
		}
?>



	
</tbody>

</table>
</div>
<?php
}

##############################################
##                                          ##
##                 statitics                ##
##                                          ##
##############################################


function tweetstream_statitics() {
	
	?>
	<div class="wrap"><br />
<img src="<?php
	echo plugins_url ( 'images/icon.png', __FILE__ );
	?>"
	style="float: left;">
<h2 style="float: left; line-height: 5px; padding-left: 5px;"><?php
	echo __ ( 'Tweetstream statitics' );
	?></h2>
<br /><br /><br />
	<?php 
	global $bp, $wpdb;
	
	
echo __('Below is a list of all users who are using tweetstream, you can also reset there tweetstream settings.','tweetstream_lang')."<br/>";
echo __('When you reset a user they need to re-authenticate on twitter.','tweetstream_lang')."<br/>"; 
echo __('Already imported tweets will not be deleted!','tweetstream_lang')."<br/><br/>"; 
	
	?>


<table class="widefat fixed" cellspacing="0">

<thead>
<tr class="thead">
	<th scope="col" id="cb" class="manage-column column name-column" style="">Statitics</th>
	<th scope="col" id="cb" class="manage-column column name-column" style=""></th>
</tr>
</thead>

<tfoot>
<tr class="thead">
	<th scope="col" id="cb" class="manage-column column name-column" style="">Statitics</th>
	<th scope="col" id="cb" class="manage-column column name-column" style=""></th>
</tr>
</tfoot>
<tbody id="users" class="list:user user-list">
<?php 
//get all users who have set-up there tweetstream

			//total users
			$count_users = count($wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->users" )));

			//total users using tweetstream
			$count_tweetstreamusers = count($wpdb->get_results($wpdb->prepare( "SELECT user_id FROM $wpdb->usermeta WHERE meta_key='tweetstream_token';" )));
			
			//percentage of users using tweetstream
			$perc_tweetstreamusers = round(($count_tweetstreamusers/$count_users)*100);

			//total imported tweets
			$count_tweets = count($wpdb->get_results($wpdb->prepare("SELECT type FROM ".$bp->activity->table_name." WHERE type='tweet';" )));
			
			//total activity updates
			$count_activity  = count($wpdb->get_results($wpdb->prepare("SELECT id FROM ".$bp->activity->table_name)));
			
			//percentage of tweets vs activity updates
			$perc_tweetupdates = round(($count_tweets/$count_activity*100));
			
			//avarage tweets per day
			$average_tweets_day = round($count_tweets/24);
			
			//avarage tweets per day
			$average_tweets_week = $average_tweets_day*7;
			
			//avarage tweets per day
			$average_tweets_month = $average_tweets_day*30;
			
			$average_tweets_year = $average_tweets_day*365;

					echo " 
					<tr id='stats'>
						<th scope='row' class='column'>Ammount of users:</th>
						<td scope='row' class='column'>".$count_users."</th>
					</tr>
					<tr id='stats'>
						<th scope='row' class='column'>Ammount of user using tweetstream:</th>
						<td scope='row' class='column'>".$count_tweetstreamusers."</th>
					</tr>
					<tr id='stats'>
						<th scope='row' class='column'>Percentage of users using tweetstream:</th>
						<td scope='row' class='column'>".$perc_tweetstreamusers."%</th>
					</tr>
					<tr id='stats'>
						<th scope='row' class='column'>Ammount of activity updates:</th>
						<td scope='row' class='column'>".$count_activity."</th>
					</tr>
					<tr id='stats'>
						<th scope='row' class='column'>Ammount of tweet updates:</th>
						<td scope='row' class='column'>".$count_tweets."</th>
					</tr>
					<tr id='stats'>
						<th scope='row' class='column'>Percentage of tweets:</th>
						<td scope='row' class='column'>".$perc_tweetupdates."%</th>
					</tr>
					<tr id='stats'>
						<th scope='row' class='column'>Average tweets import per day:</th>
						<td scope='row' class='column'>".$average_tweets_day."</th>
					</tr>
					<tr id='stats'>
						<th scope='row' class='column'>Average tweets import per week:</th>
						<td scope='row' class='column'>".$average_tweets_week."</th>
					</tr>
					<tr id='stats'>
						<th scope='row' class='column'>Average tweets import per month:</th>
						<td scope='row' class='column'>".$average_tweets_month."</th>
					</tr>
					<tr id='stats'>
						<th scope='row' class='column'>Average tweets import per year:</th>
						<td scope='row' class='column'>".$average_tweets_year."</th>
					</tr>
					";
		
?>



	
</tbody>

</table>
</div>
<?php
}

##############################################
##                                          ##
##           general settings               ##
##                                          ##
##############################################


function tweetstream_settings() {
	global $bp, $wpdb;
	
	if ($_POST) {
		
		update_site_option ( 'tweetstream_consumer_key', trim ( strip_tags ( $_POST ['tweetstream_consumer_key'] ) ) );
		update_site_option ( 'tweetstream_consumer_secret', trim ( strip_tags ( $_POST ['tweetstream_consumer_secret'] ) ) );
		update_site_option ( 'tweetstream_user_settings_message', trim ( strip_tags ( $_POST ['tweetstream_user_settings_message'] ) ) );
		update_site_option ( 'tweetstream_user_settings_syncbp', trim ( strip_tags ( strtolower ( $_POST ['tweetstream_user_settings_syncbp'] ) ) ) );
		update_site_option ( 'tweetstream_user_settings_maximport', trim ( strip_tags ( strtolower ( $_POST ['tweetstream_user_settings_maximport'] ) ) ) );
		update_site_option ( 'tweetstream_cronrun', $_POST ['tweetstream_cronrun'] );
		
		echo '<div class="updated" style="margin-top:50px;"><p><strong>' . __ ( 'Settings saved.', 'tweetstream_lang' ) . '</strong></p></div>';
	}
	
	?>
<div class="wrap"><br />
<img src="<?php
	echo plugins_url ( 'images/icon.png', __FILE__ );
	?>"
	style="float: left;">
<h2 style="float: left; line-height: 5px; padding-left: 5px;"><?php
	echo __ ( 'Tweetstream general settings' );
	?></h2>
<br />

<form method="post" action="">
<table class="form-table">
<?php
	echo __ ( '', 'tweetstream_lang' );
	?>
	<tr>
		<td colspan="2" scope="row"><b><?php
	echo __ ( 'Twitter API', 'tweetstream_lang' );
	?></b><br />

		<?php
	echo __ ( 'For the plugin to work you need to get an API key from twitter.', 'tweetstream_lang' );
	?><br />
		<br />
		<?php
	echo __ ( 'To get one follow the next steps:', 'tweetstream_lang' );
	?><br />
		<?php
	echo __ ( '&nbsp;&nbsp;&nbsp;1. Go to ', 'tweetstream_lang' );
	?>"<a
			href="http://www.twitter.com/apps" target="_blank">http://www.twitter.com/apps</a>"
			<?php
	echo __ ( ' and login with your twitter account.', 'tweetstream_lang' );
	?><br />
			<?php
	echo __ ( '&nbsp;&nbsp;&nbsp;2. Create a new app wich has read/write settings, type browser (not client) and a callback url to: ', 'tweetstream_lang' );
	?><b><?php
	echo $bp->root_domain;
	?></b><br />
			<?php
	echo __ ( '&nbsp;&nbsp;&nbsp;3. Fill in the consumer key and consumer secret below.', 'tweetstream_lang' );
	?><br />

		</td>
	</tr>
	<tr valign="top">
		<th scope="row"><?php
	echo __ ( 'Consumer key:', 'tweetstream_lang' );
	?></th>
		<td><input type="text" name="tweetstream_consumer_key"
			value="<?php
	echo get_site_option ( 'tweetstream_consumer_key' );
	?>"
			size="50" /></td>
	</tr>

	<tr valign="top">
		<th scope="row"><?php
	echo __ ( 'Consumer secret key:', 'tweetstream_lang' );
	?></th>
		<td><input type="text" name="tweetstream_consumer_secret"
			value="<?php
	echo get_site_option ( 'tweetstream_consumer_secret' );
	?>"
			size="50" /></td>
	</tr>

	<tr valign="top">
		<th scope="row"><?php
	echo __ ( 'Run import every:', 'tweetstream_lang' );
	?></th>
		<td><input type="text" name="tweetstream_cronrun"
			value="<?php
	echo get_site_option ( 'tweetstream_cronrun' );
	?>"
			size="4" /> minutes</td>
	</tr>

	<tr valign="top">
		<th scope="row">
		<h2><?php
	echo __ ( 'User options', 'tweetstream_lang' );
	?></h2>
		</th>
		<td></td>
	</tr>

	<tr valign="top">
		<th><?php
	echo __ ( 'Show "Want to tweet this message to?..." message on user page.', 'tweetstream_lang' );
	?></th>
		<th><input type="radio" name="tweetstream_user_settings_message"
			id="tweetstream_user_settings_message" value="1"
			<?php
	if (get_site_option ( 'tweetstream_user_settings_message' ) == 1) {
		echo 'checked';
	}
	?>>
			<?php
	echo __ ( 'Yes', 'tweetstream_lang' );
	?> <input type="radio"
			name="tweetstream_user_settings_message"
			id="tweetstream_user_settings_message" value="0"
			<?php
	if (get_site_option ( 'tweetstream_user_settings_message' ) == 0) {
		echo 'checked';
	}
	?>>
			<?php
	echo __ ( 'No', 'tweetstream_lang' );
	?></th>
	</tr>

	<tr valign="top">
		<th><?php
	echo __ ( 'Allow users to sync to buddypress.', 'tweetstream_lang' );
	?></th>
		<th><input type="radio" name="tweetstream_user_settings_syncbp"
			id="tweetstream_user_settings_syncbp" value="0"
			<?php
	if (get_site_option ( 'tweetstream_user_settings_syncbp' ) == 0) {
		echo 'checked';
	}
	?>>
			<?php
	echo __ ( 'Yes', 'tweetstream_lang' );
	?> <input type="radio"
			name="tweetstream_user_settings_syncbp"
			id="tweetstream_user_settings_syncbp" value="1"
			<?php
	if (get_site_option ( 'tweetstream_user_settings_syncbp' ) == 1) {
		echo 'checked';
	}
	?>>
			<?php
	echo __ ( 'No', 'tweetstream_lang' );
	?></th>
	</tr>

	<tr valign="top">
		<th><?php
	echo __ ( 'Max tweet import per user, per day (empty = unlimited).', 'tweetstream_lang' );
	?></th>
		<th><input type="text" name="tweetstream_user_settings_maximport"
			value="<?php
	echo get_site_option ( 'tweetstream_user_settings_maximport' );
	?>"
			size="5" /></th>
	</tr>
</table>
<p class="submit"><input type="submit" class="button-primary"
	value="<?php
	echo __ ( 'Save Changes' )?>" /></p>
</form>

</div>
<?php
}

##############################################
##                                          ##
##           filters settings               ##
##                                          ##
##############################################


function tweetstream_filters() {
	global $bp, $wpdb;
	
	if ($_POST) {
		
		update_site_option ( 'tweetstream_filter', trim ( strip_tags ( strtolower ( $_POST ['tweetstream_filter'] ) ) ) );
		update_site_option ( 'tweetstream_filter_show', trim ( strip_tags ( $_POST ['tweetstream_filter_show'] ) ) );
		update_site_option ( 'tweetstream_filterexplicit', trim ( strip_tags ( strtolower ( $_POST ['tweetstream_filterexplicit'] ) ) ) );
		echo '<div class="updated" style="margin-top:50px;"><p><strong>' . __ ( 'Filters saved.', 'tweetstream_lang' ) . '</strong></p></div>';
	}
	
	?>
<div class="wrap"><br />
<img src="<?php
	echo plugins_url ( 'images/icon.png', __FILE__ );
	?>"
	style="float: left;">
<h2 style="float: left; line-height: 5px; padding-left: 5px;"><?php
	echo __ ( 'Tweetstream filters (optional)' );
	?></h2>
<br />

<form method="post" action="">
<table class="form-table">
<?php
	echo __ ( '', 'tweetstream_lang' );
	?>
	

	<tr>
		<td colspan="2"><br>
		<?php
	echo __ ( 'Filters preventing to get a really messy activity streams.', 'tweetstream_lang' );
	?><br>
		<?php
	echo __ ( 'Example: You have an social network wich focus is on soccer, you don\'t want all tweets of your users showing up that hasn\'t to do anything with soccer.', 'tweetstream_lang' );
	?><br>
		<br>
		<?php
	echo __ ( 'you can set the filter to "soccer", now only tweets with "soccer" in it will be shown of the users tweets".', 'tweetstream_lang' );
	?><br>
		<?php
	echo __ ( 'By comma seperating words you can set-up multiple filters.', 'tweetstream_lang' );
	?>
		(<?php
	echo __ ( 'No filter = all tweets.', 'tweetstream_lang' );
	?>)<br>
		<?php
	echo __ ( 'The explicit words filter blocks messages with those words in it.', 'tweetstream_lang' );
	?>

		</td>
	</tr>

	<tr valign="top">
		<th scope="row"><?php
	echo __ ( 'Filters (comma seperated)', 'tweetstream_lang' );
	?></th>
		<td><input type="text" name="tweetstream_filter"
			value="<?php
	echo get_site_option ( 'tweetstream_filter' );
	?>"
			size="50" /></td>
	</tr>

	<tr valign="top">
		<th scope="row"><?php
	echo __ ( 'Show filters in tweets.', 'tweetstream_lang' );
	?></th>
		<th><input type="radio" name="tweetstream_filter_show"
			id="tweetstream_filter_show" value="1"
			<?php
	if (get_site_option ( 'tweetstream_filter_show' ) == 1) {
		echo 'checked';
	}
	?>>
			<?php
	echo __ ( 'Yes', 'tweetstream_lang' );
	?> <input type="radio"
			name="tweetstream_filter_show" id="tweetstream_filter_show" value="0"
			<?php
	if (get_site_option ( 'tweetstream_filter_show' ) == 0) {
		echo 'checked';
	}
	?>>
			<?php
	echo __ ( 'No', 'tweetstream_lang' );
	?></th>
	</tr>

	<tr valign="top">
		<th scope="row"><?php
	echo __ ( 'Explicit words (comma seperated)', 'tweetstream_lang' );
	?></th>
		<td><input type="text" name="tweetstream_filterexplicit"
			value="<?php
	echo get_site_option ( 'tweetstream_filterexplicit' );
	?>"
			size="50" /></td>
	</tr>



</table>
<p class="submit"><input type="submit" class="button-primary"
	value="<?php
	echo __ ( 'Save Changes' )?>" /></p>
</form>


</div>
<?php
}

##############################################
##                                          ##
##          check auth on twitter           ##
##                                          ##
##############################################
function tweetstream_checkTwitterAuth($user_id) {
	
	//do we still have access on twitter? else empty user keys.
	$token = get_usermeta ( $user_id, 'tweetstream_token' );
	$secret = get_usermeta ( $user_id, 'tweetstream_tokensecret' );
	
	require_once 'twitter/EpiCurl.php';
	require_once 'twitter/EpiOAuth.php';
	require_once 'twitter/EpiTwitter.php';
	
	$twitterObj = new EpiTwitter ( get_site_option ( "tweetstream_consumer_key" ), get_site_option ( "tweetstream_consumer_secret" ) );
	$twitterObj->setToken ( $token, $secret );
	
	$twitterInfo = $twitterObj->get_accountVerify_credentials ();
	$twitterInfo = $twitterInfo->responseText;
	$twitterInfo = json_decode ( $twitterInfo );
	if ($twitterInfo->error) {
		update_usermeta ( $user_id, 'tweetstream_token', '' );
		update_usermeta ( $user_id, 'tweetstream_tokensecret', '' );
	}
	
	//keeping the memory clean
	unset ( $twitterObj );
	unset ( $twitterInfo );
}

##############################################
##                                          ##
##          oauth back from twitter         ##
##                                          ##
##############################################


add_action ( 'wp', 'oauthcheck' );

function oauthcheck() {
	global $bp;
	
	if ($_GET ['oauth_token'] && $_GET ['soc'] == '') {
		
		require_once 'twitter/EpiCurl.php';
		require_once 'twitter/EpiOAuth.php';
		require_once 'twitter/EpiTwitter.php';
		
		$twitterObj = new EpiTwitter ( get_site_option ( "tweetstream_consumer_key" ), get_site_option ( "tweetstream_consumer_secret" ) );
		$twitterObj->setToken ( $_GET ['oauth_token'] );
		$token = $twitterObj->getAccessToken ();
		$twitterObj->setToken ( $token->oauth_token, $token->oauth_token_secret );
		$twitterInfo = $twitterObj->get_accountVerify_credentials ();
		$twitterInfo->response;
		
		update_usermeta ( ( int ) $bp->loggedin_user->id, 'tweetstream_token', $token->oauth_token );
		update_usermeta ( ( int ) $bp->loggedin_user->id, 'tweetstream_tokensecret', $token->oauth_token_secret );
		update_usermeta ( ( int ) $bp->loggedin_user->id, 'tweetstream_synctoac', 1 );
		
		//redirect to tweetstream settings
		header ( 'location:' . $bp->loggedin_user->domain . "settings/tweetstream" );
	}
	
	if ($_GET ['denied']) {
		
		update_usermeta ( ( int ) $bp->loggedin_user->id, 'tweetstream_token', '' );
		update_usermeta ( ( int ) $bp->loggedin_user->id, 'tweetstream_tokensecret', '' );
		update_usermeta ( ( int ) $bp->loggedin_user->id, 'tweetstream_synctoac', '' );
		
		//redirect to tweetstream settings
		header ( 'location:' . $bp->loggedin_user->domain . "settings/tweetstream" );
	}
	
	//keeping the memory clean
	unset ( $twitterInfo );
	unset ( $twitterObj );
	unset ( $token );

}

//filter tags
add_filter ( 'bp_get_activity_latest_update', 'tweetstream_filterTags', 9 );
function tweetstream_filterTags($content) {
	$content = str_replace ( "#TWITTER", "", $content );
	return $content;
}

##############################################
##                                          ##
##      shorten url functions               ##
##                                          ##
##############################################


function tweetstream_getShortUrl($url) {
	global $bp;
	if ($url) {
		$input = date ( 'dmyhis' );
		$index = "abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
		$base = strlen ( $index );
		
		for($t = floor ( log ( $input, $base ) ); $t >= 0; $t --) {
			$bcp = bcpow ( $base, $t );
			$a = floor ( $input / $bcp ) % $base;
			$out = $out . substr ( $index, $a, 1 );
			$input = $input - ($a * $bcp);
		}
		$shortId = strrev ( $out );
		
		update_usermeta ( $bp->loggedin_user->id, 'tweetstream_' . $shortId, $url );
		$url = $bp->root_domain . '/' . $shortId;
		
		return $url;
	} else {
		return false;
	}
}

add_action ( 'wp', 'tweetstream_resolveShortUrl' );

function tweetstream_resolveShortUrl($url) {
	
	global $wpdb;
	
	//resolving hooked to 404
	if (is_404 ()) {
		$short_id = str_replace ( "/", "", $_SERVER ['REQUEST_URI'] );
		if ($short_id) {
			$usermeta = $wpdb->get_row ( "SELECT * FROM {$wpdb->usermeta} WHERE meta_key='tweetstream_" . $short_id . "'" );
			$url = $usermeta->meta_value;
			if ($url) {
				header ( 'location:' . $url );
			}
		}
	}
}

##############################################
##                                          ##
##             styling stuff                ##
##                                          ##
##############################################


add_action ( 'wp_print_styles', 'add_tweetstream_style' );

function add_tweetstream_style() {
	$myStyleUrl = WP_PLUGIN_URL . '/tweetstream/css/style.css';
	$myStyleFile = WP_PLUGIN_DIR . '/tweetstream/css/style.css';
	if (file_exists ( $myStyleFile )) {
		wp_register_style ( 'tweetstreamcss', $myStyleUrl );
		wp_enqueue_style ( 'tweetstreamcss' );
	}
}
?>