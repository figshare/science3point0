<?php

class BPGoogleSitemapGeneratorUI {

	/**
	 * The Sitemap Generator Object
	 *
	 * @var BPGoogleSitemapGenerator
	 */
	var $sg = null;
	
	function BPGoogleSitemapGeneratorUI(&$sitemapBuilder) {
		global $wp_version;
		$this->sg = &$sitemapBuilder;
	}
	
	/**
	 * Displays the option page
	 *
	 * @since 3.0
	 * @access public
	 * @author Arne Brachhold
	 */
	function HtmlShowOptionsPage() {
		global $wp_version;
		
		$snl = false; //SNL
		
		$this->sg->Initate();
			
		$offset = (int) get_option('gmt_offset') * 60 * 60;

			
		$message="";
		
		if(!empty($_REQUEST["bpsm_rebuild"])) { //Pressed Button: Rebuild Sitemap
			check_admin_referer('bp-sitemap');
			
			//Clear any outstanding build cron jobs
			if(function_exists('wp_clear_scheduled_hook')) wp_clear_scheduled_hook('bpsm_build_cron');
			
			if(isset($_GET["bpsm_do_debug"]) && $_GET["bpsm_do_debug"]=="true") {
				
				//Check again, just for the case that something went wrong before
				if(!current_user_can("administrator")) {
					echo '<p>Please log in as admin</p>';
					return;
				}
				
				$oldErr = error_reporting(E_ALL);
				$oldIni = ini_set("display_errors",1);

				echo '<div class="wrap">';
				echo '<h2>' .  __('BuddyPress Sitemap Generator', 'bp-sitemap') .  " " . $this->sg->GetVersion(). '</h2>';
				echo '<p>This is the debug mode of the BuddyPress Sitemap Generator. It will show all PHP notices and warnings as well as the internal logs, messages and configuration.</p>';
				echo '<p style="font-weight:bold; color:red; padding:5px; border:1px red solid; text-align:center;">DO NOT POST THIS INFORMATION ON PUBLIC PAGES LIKE SUPPORT FORUMS AS IT MAY CONTAIN PASSWORDS OR SECRET SERVER INFORMATION!</p>';
				echo "<h3>WordPress and PHP Information</h3>";
				echo '<p>WordPress ' . $GLOBALS['wp_version'] . ' with ' . ' DB ' . $GLOBALS['wp_db_version'] . ' on PHP ' . phpversion() . '</p>';
				echo '<p>Plugin version: ' . $this->sg->GetVersion() . ' (' . $this->sg->_svnVersion . ')';
				echo '<h4>Environment</h4>';
				echo "<pre>";
				$sc = $_SERVER;
				unset($sc["HTTP_COOKIE"]);
				print_r($sc);
				echo "</pre>";

				echo '<h4>Sitemap Config</h4>';
				echo "<pre>";
				print_r($this->sg->_options);
				echo "</pre>";
				echo '<h3>Errors, Warnings, Notices</h3>';
				echo '<div>';
				$status = $this->sg->BuildSitemap();
				echo '</div>';
				echo '<h3>MySQL Queries</h3>';
				if(defined('SAVEQUERIES') && SAVEQUERIES) {
					echo '<pre>';
					var_dump($GLOBALS['wpdb']->queries);
					echo '</pre>';
					
					$total = 0;
					foreach($GLOBALS['wpdb']->queries as $q) {
						$total+=$q[1];
					}
					echo '<h4>Total Query Time</h4>';
					echo '<pre>' . count($GLOBALS['wpdb']->queries) . ' queries in ' . round($total,2) . ' seconds.</pre>';
				} else {
					echo '<p>Please edit wp-db.inc.php in wp-includes and set SAVEQUERIES to true if you want to see the queries.</p>';
				}
				echo "<h3>Build Process Results</h3>";
				echo "<pre>";
				print_r($status);
				echo "</pre>";
				echo '<p>Done. <a href="' . wp_nonce_url($this->sg->GetBackLink() . "&bpsm_rebuild=true&bpsm_do_debug=true",'bp-sitemap') . '">Rebuild</a> or <a href="' . $this->sg->GetBackLink() . '">Return</a></p>';
				echo '<p style="font-weight:bold; color:red; padding:5px; border:1px red solid; text-align:center;">DO NOT POST THIS INFORMATION ON PUBLIC PAGES LIKE SUPPORT FORUMS AS IT MAY CONTAIN PASSWORDS OR SECRET SERVER INFORMATION!</p>';
				echo '</div>';
				@error_reporting($oldErr);
				@ini_set("display_errors",$oldIni);
				return;
			} else {
				$this->sg->BuildSitemap();
				$redirURL = $this->sg->GetBackLink() . '&bpsm_fromrb=true';
				
				//Redirect so the bpsm_rebuild GET parameter no longer exists.
				@header("location: " . $redirURL);
				//If there was already any other output, the header redirect will fail
				echo '<script type="text/javascript">location.replace("' . $redirURL . '");</script>';
				echo '<noscript><a href="' . $redirURL . '">Click here to continue</a></noscript>';
				exit;
			}
		} else if (!empty($_POST['bpsm_update'])) { //Pressed Button: Update Config
			check_admin_referer('bp-sitemap');
			
			if(isset($_POST['bpsm_b_style']) && $_POST['bpsm_b_style'] == $this->sg->getDefaultStyle()) {
				$_POST['bpsm_b_style_default'] = true;
				$_POST['bpsm_b_style'] = '';
			}
			
			foreach($this->sg->_options as $k=>$v) {
				
				//Check vor values and convert them into their types, based on the category they are in
				if(!isset($_POST[$k])) $_POST[$k]=""; // Empty string will get false on 2bool and 0 on 2float
				
				//Options of the category "Basic Settings" are boolean, except the filename and the autoprio provider
				if(substr($k,0,7)=="bpsm_b_") {
					if($k=="bpsm_b_filename" || $k=="bpsm_b_fileurl_manual" || $k=="bpsm_b_filename_manual" || $k=="bpsm_b_manual_key" || $k == "bpsm_b_yahookey"  || $k == "sm_b_style" || $k == "bpsm_b_memory") {
						if($k=="bpsm_b_filename_manual" && strpos($_POST[$k],"\\")!==false){
							$_POST[$k]=stripslashes($_POST[$k]);
						}
						
						$this->sg->_options[$k]=(string) $_POST[$k];
					} else if($k=="bpsm_b_location_mode") {
						$tmp=(string) $_POST[$k];
						$tmp=strtolower($tmp);
						if($tmp=="auto" || $tmp="manual") $this->sg->_options[$k]=$tmp;
						else $this->sg->_options[$k]="auto";
					} else if($k== "bpsm_i_install_date") {
						if($this->sg->GetOption('i_install_date')<=0) $this->sg->_options[$k] = time();
					} else {
						$this->sg->_options[$k]=(bool) $_POST[$k];

					}
					
				}
			}
			
			if($this->sg->SaveOptions()) $message.=__('Configuration updated', 'bp-sitemap') . "<br />";
			else $message.=__('Error while saving options', 'bp-sitemap') . "<br />";
			
		} else if(!empty($_POST["bpsm_reset_config"])) { //Pressed Button: Reset Config
			check_admin_referer('bp-sitemap');
			$this->sg->InitOptions();
			$this->sg->SaveOptions();
			
			$message.=__('The default configuration was restored.','bp-sitemap');
		}
		
		//Print out the message to the user, if any
		if($message!="") { ?>
			<div id="message" class="updated fade">
				<p><?php echo $message; ?></p>
			</div>
		<?php }
		
		if (function_exists("wp_next_scheduled")) {
			$next = wp_next_scheduled('bpsm_build_cron');
			if ($next) {
				$diff = (time()-$next)*-1;
				if ($diff <= 0) {
					$diffMsg = __('Your sitemap is being manually built at the moment. Depending on your buddypress size this might take some time!<br /><small>Due to limitations of the WordPress scheduler, it might take another 60 seconds until the build process is actually started.</small>','bp-sitemap');
				} else {
					$diffMsg = str_replace("%s",$diff,__('Your sitemap will be built in %s seconds. Depending on your buddypress size this might take some time!','bp-sitemap'));
				} ?>
				<div id="message" class="updated">
					<p><?php echo $diffMsg; ?></p>
				</div>
			<?php }
			
			$next = wp_next_scheduled('bpsm_wp_cron');
			if ($next) {
				$diff = (time()-$next)*-1;
				if ($diff <= 0) {
					$diffMsg = __('The WP-Cron is building the BuddyPress Sitemap at the moment.','bp-sitemap'); ?>
					<div id="message" class="updated"><p><?php echo $diffMsg; ?></p></div>
				<?php }	
			} else {
				echo '<div id="message" class="error"><p>'. __('Sitemap WP-Cron scheduled task not found - please reactivate this plugin', 'bp-sitemap') .'</p></div>';
			}
			
		} ?>
		
		<style type="text/css">
		
		li.bpsm_hint {
			color:green;
		}
		
		li.bpsm_optimize {
			color:orange;
		}
		
		li.bpsm_error {
			color:red;
		}
		
		input.bpsm_warning:hover {
			background: #ce0000;
			color: #fff;
		}
		
		a.bpsm_button {
			padding:4px;
			display:block;
			padding-left:25px;
			background-repeat:no-repeat;
			background-position:5px 50%;
			text-decoration:none;
			border:none;
		}
		
		a.bpsm_button:hover {
			border-bottom-width:1px;
		}

		div.bpsm-update-nag p {
			margin:5px;
		}
		
		.bpsm-padded .inside {
			margin:12px!important;
		}
		.bpsm-padded .inside ul {
			margin:6px 0 12px 0;
		}
		
		.bpsm-padded .inside input {
			padding:1px;
			margin:0;
		}
		
#bp-sitemap-status .table {
background:none repeat scroll 0 0 #F9F9F9;
border-bottom:1px solid #ECECEC;
border-top:1px solid #ECECEC;
margin:0 -9px 10px;
padding:0 10px;
}
#bp-sitemap-status table {
width:100%;
}
#bp-sitemap-status .inside {
font-size:12px;
}
#bp-sitemap-status table tr.first td {
border-top:medium none;
}
#bp-sitemap-status td.first, #bp-sitemap-status td.last {
width:1%;
}
#bp-sitemap-status td.b {
font-family:Georgia,"Times New Roman","Bitstream Charter",Times,serif;
font-size:14px;
padding-right:6px;
text-align:right;
}
#bp-sitemap-status table td {
border-top:1px solid #ECECEC;
padding:3px 0;
white-space:nowrap;
}
div.postbox div.inside {
margin:10px;
position:relative;
}
		
		</style>
		
		<div class="wrap buddypress-sitemap-admin-content" style="position: relative">
		
			<form method="post" action="<?php echo $this->sg->GetBackLink(); ?>">
		
			<h2><?php _e('BuddyPress Sitemap Generator', 'bp-sitemap'); ?> <a class="button" href="<?php echo wp_nonce_url($this->sg->GetBackLink() . "&bpsm_rebuild=true&noheader=true",'bp-sitemap'); ?>">Rebuild Now</a> <a class="button" href="<?php echo wp_nonce_url($this->sg->GetBackLink() . "&bpsm_rebuild=true&bpsm_do_debug=true",'bp-sitemap'); ?>">Rebuild <span style="color:green">Debug On</span></a></h2>

			<?php if ( get_option('blog_public')!=1) { ?><div class="error"><p><?php echo str_replace("%s","options-privacy.php",__('Your blog is currently blocking search engines! Visit the <a href="%s">privacy settings</a> to change this.','bp-sitemap') ); ?></p></div><?php } ?>
			<?php if ( (bp_is_active( 'groups' ) && BP_SITEMAP_EXPECTED_GROUPS_DB_VERSION != BP_GROUPS_DB_VERSION) || (bp_is_active( 'xprofile' ) && BP_SITEMAP_EXPECTED_XPROFILE_DB_VERSION != BP_XPROFILE_DB_VERSION) || (bp_is_active( 'activity' ) && BP_SITEMAP_EXPECTED_ACTIVITY_DB_VERSION != BP_ACTIVITY_DB_VERSION) || (bp_is_active( 'friends' ) && BP_SITEMAP_EXPECTED_FRIENDS_DB_VERSION != BP_FRIENDS_DB_VERSION) ) { ?><div class="error"><p><?php _e('Unpredictable results may occur. Database or BuddyPress version out of sync on what Sitemap generator was tested against','bp-sitemap'); ?></p></div><?php } ?>

			<h3><?php _e( 'Sitemap Status', 'bp-sitemap' ); ?></h3>


			<?php $status = &BPGoogleSitemapGeneratorStatus::Load(); 
			
			//what is our cron status
			$next = wp_next_scheduled('bpsm_wp_cron');
			if ($next) {
				$nextRun = date("F j, Y, g:i a", $next + $offset); 
		
				$timebetween = $next-time();
				if($timebetween > 0){
					$timeUntilRun = '<i>'. $this->sg->sec2hms($timebetween) .'</i>';	
				}else{
					$timeUntilRun = '<i>Waiting for WP-Cron</i>';	
				}
			} else {
				$timeUntilRun = '<i>WP-Cron not set</i>';
				$nextRun = '<i>WP-Cron not set</i>';
			}
			?>

<div class="metabox-holder" id="bp-sitemap-status">

	<div style="width: 49%;float:left; padding-right:0.5%;">

			<div  class="postbox" style="display: block;">
				<h3><span><?php _e( 'Recent Build Status', 'bp-sitemap' ); ?> :: <?php _e( 'Next Build: ', 'bp-sitemap' ); echo $timeUntilRun; ?></span></h3>
				<div class="inside">
					<p>
					<?php if($status == null) {
						echo str_replace("%s",wp_nonce_url($this->sg->GetBackLink() . "&bpsm_rebuild=true&noheader=true",'bp-sitemap'),__('The sitemap wasn\'t built yet. <a href="%s">Click here</a> to build it the first time.','bp-sitemap'));
					}  else {
						$st=$status->GetStartTime();
						echo '<ul>';
						
						echo '<li>'. str_replace("%date%", $nextRun, __("Next scheduled build on: <b>%date%</b>",'bp-sitemap') ) .'</li>';
						
						if($status->_endTime !== 0) {
							if($status->_usedXml) {
								if($status->_xmlSuccess) {
									$ft = is_readable($status->_xmlPath)?filemtime($status->_xmlPath):false;
									if($ft!==false) echo "<li>" . str_replace("%url%",$status->_xmlUrl,str_replace("%date%",date(get_option('date_format'),$ft) . " " . date(get_option('time_format'),$ft + $offset),__("Your <a href=\"%url%\">sitemap</a> was last built on <b>%date%</b>.",'bp-sitemap'))) . "</li>";
									else echo "<li class=\"bpsm_error\">" . __("The last build succeeded, but the file was deleted later or can't be accessed anymore. Did you move your blog to another server or domain?",'bp-sitemap') . "</li>";
								} else {
									echo "<li class=\"bpsm_error\">" . __("There was a problem writing your sitemap file. Make sure the file exists and is writable",'bp-sitemap') . "</li>";
								}
							}
							
							$et = $status->GetTime();
							$mem = $status->GetMemoryUsage();
							
							if($mem > 0) {
								echo "<li>" .str_replace(array("%time%","%memory%"),array($et,$mem),__("The building process took about <b>%time% seconds</b> to complete and used <b>%memory%</b> MB of memory.",'bp-sitemap')). "</li>";
							} else {
								echo "<li>" .str_replace("%time%",$et,__("The building process took about <b>%time% seconds</b> to complete.",'bp-sitemap')). "</li>";
							}
							
							if(!$status->_hasChanged) {
								echo "<li>" . __("The content of your sitemap <strong>didn't change</strong> since the last time so the files were not written and no search engine was pinged.",'bp-sitemap'). "</li>";
							}
											
						} else {
							if($this->sg->GetOption("b_auto_delay")) {
								$st = ($status->GetStartTime() - time()) * -1;
								//If the building process runs in background and was started within the last 45 seconds, the sitemap might not be completed yet...
								if($st < 30) {
									echo '<li class="">'. __("The building process might still be active! Reload the page in a few seconds and check if something has changed.",'bp-sitemap') . '</li>';
								}
							}
							
							if($timebetween > 0){
							}else{
								echo '<li class="">'. __("Waiting for WP-Cron to finish",'bp-sitemap') . '</li>';
							}
							
							if($status->_memoryUsage > 0) {
								echo '<li class="bpsm_error">'. str_replace(array("%memused%","%memlimit%"),array($status->GetMemoryUsage(),ini_get('memory_limit')),__("The last known memory usage of the script was %memused%MB, the limit of your server is %memlimit%.",'bp-sitemap')) . '</li>';
							}
							
							if($status->_lastTime > 0) {
								echo '<li class="bpsm_error">'. str_replace(array("%timeused%","%timelimit%"),array($status->GetLastTime(),ini_get('max_execution_time')),__("The last known execution time of the script was %timeused% seconds, the limit of your server is %timelimit% seconds.",'bp-sitemap')) . '</li>';
							}

						}
						echo '</ul>';

					} ?>
					</p>
					
					<?php if ($status->_childpings) { ?>
						<p><?php _e( 'Search Engine Pings:', 'bp-sitemap' ); ?></p>
						
						<div class="table">
							<table>
								<?php $bpsm_i = 0; foreach ($status->_childpings as $k => $v) {
								if($v['used']) { 
									$gt = round($v['endtime'] - $v['starttime'],2); ?>
									<tr class="<?php if ($bpsm_i == 0) echo 'first '; if ($bpsm_i % 2 == 0) echo 'alt'; ?>">
										<td class="first b b-posts"><?php if ($v['success']) { echo '<span style="color:green">[OK]</span>'; } else { echo '<span style="color:red">[ERR]</span>'; }?></td>
										<td class="t posts"><?php echo ucfirst($k); ?></td>
										<td class="b b-comments">
										<?php if ($gt>4) { //clean up later
											echo str_replace("%time%",$gt,__("<span style='color:red'>%time% seconds</span>",'bp-sitemap'));
										} else if ($gt>2) {
											echo str_replace("%time%",$gt,__("<span style='color:#E66F00'>%time% seconds</span>",'bp-sitemap'));
										} else {
											echo str_replace("%time%",$gt,__("%time% seconds",'bp-sitemap'));
										} ?>
										</td>
										<td class="last t comments"><?php if (!$v['success']) echo str_replace("%s",wp_nonce_url($this->sg->GetBackLink() . "&bpsm_ping_service=". $k ."&noheader=true",'bp-sitemap'),__('<a style="color:red" href="%s">View Error</a>','bp-sitemap')); ?></td>
									</tr>
								<?php $bpsm_i++; }
								} ?>
							</table>
						</div>
					<?php } ?>
					
				</div>
			</div>
	</div>
	
	<div style="width: 49%;float:left; padding-right:0.5%;">

			<div class="postbox" style="display: block;">
				<h3><span><?php _e( 'Childmap Status', 'bp-sitemap' ); ?></span></h3>
				<div class="inside">
					<?php if ($status->_childmaps) { //fugly but we have to rewrite the old wpsitemap code - right now just lazy ?>
						<div class="table">
							<table>
								<?php $bpsm_i = 0; foreach ($status->_childmaps as $k => $v) {
								if($v['usedXml']) { 
									$gt = round($v['usedXmlEndTime'] - $v['usedXmlStartTime'],2); ?>
									<tr class="<?php if ($bpsm_i == 0) echo 'first '; if ($bpsm_i % 2 == 0) echo 'alt'; ?>">
										<td class="first b b-posts"><?php if ($v['xmlSuccess']) { echo '<span style="color:green">[OK]</span>'; } else { echo '<span style="color:red">[ERR]</span>'; }?></td>
										<td class="t posts"><?php echo ucfirst($k); ?> [xml]</td>
										<td class="b b-comments">
										<?php if ($gt) {
											echo str_replace("%time%",$gt,__("%time% seconds",'bp-sitemap'));
										} ?>
										</td>
										<td class="last t comments"><?php if ($v['xmlSuccess']) {
											$fa = is_readable($v['xmlPath'])?filemtime($v['xmlPath']):false;
											if($ft) {
												echo str_replace("%s",$v['xmlUrl'],__('<a href="%s">View XML</a>','bp-sitemap'));
											} else {
												echo "<span style='color:red'>" . __('File Not Found','bp-sitemap') ."</span>";
											}
										} ?>
										</td>
									</tr>
								<?php 
									$bpsm_i++;
								}

								if($v['usedZip']) { 
									$gt = round($v['usedZipEndTime'] - $v['usedZipStartTime'],2); ?>
									<tr class="<?php if ($bpsm_i == 0) echo 'first '; if ($bpsm_i % 2 == 0) echo 'alt'; ?>">
										<td class="first b b-posts"><?php if ($v['zipSuccess']) { echo '<span style="color:green">[OK]</span>'; } else { echo '<span style="color:red">[ERR]</span>'; }?></td>
										<td class="t posts"><?php echo ucfirst($k); ?> [gzip]</td>
										<td class="b b-comments">
										<?php if ($gt) {
											echo str_replace("%time%",$gt,__("%time% seconds",'bp-sitemap'));
										} ?>
										</td>
										<td class="last t comments"><?php if ($v['zipSuccess']) {
											$fa = is_readable($v['zipPath'])?filemtime($v['zipPath']):false;
											if($ft) {
												echo str_replace("%s",$v['zipUrl'],__('<a href="%s">View gzip</a>','bp-sitemap'));
											} else {
												echo "<span style='color:red'>" . __('File Not Found','bp-sitemap') ."</span>";
											}
										} ?>
										</td>
									</tr>
								<?php 
									$bpsm_i++;
								}
								
								} ?>
							</table>
						</div>
					<?php } else {?>
						<p><?php _e( 'No childmaps recently generated.', 'bp-sitemap' ); ?></p>
					<?php } ?>
				</div>
			</div>

	</div>

</div>

			<br class="clear"/>



				<h3><?php _e('Basic Options', 'bp-sitemap'); ?></h3>

				<table class="form-table">
					<tr valign="top">
						<th scope="row"><?php _e( 'Last Activity', 'bp-sitemap' ); ?></th>
						<td>
							<label for="bpsm_b_last_activity"><input type="checkbox"  id="bpsm_b_last_activity" name="bpsm_b_last_activity" <?php if ($this->sg->GetOption("b_last_activity")) echo "checked=\"checked\""; ?> /> <?php _e('Enable lastmod element on members and groups childmap', 'bp-sitemap'); ?></label>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e( 'Filter Member Subnav Urls', 'bp-sitemap' ); ?></th>
						<td>
							<fieldset>
								<legend class="screen-reader-text"><span><?php _e( 'Filter Member Subnav Urls', 'bp-sitemap' ); ?></span></legend>
								<label for="bpsm_b_members_onlyif_groups">
								<input type="checkbox"  id="bpsm_b_members_onlyif_groups" name="bpsm_b_members_onlyif_groups" <?php if ($this->sg->GetOption("b_members_onlyif_groups")) echo "checked=\"checked\""; ?> /> <?php _e('Filter loc elements if member has joined a group', 'bp-sitemap'); ?></label>
								<br>
								<label for="bpsm_b_members_onlyif_friends">
								<input type="checkbox"  id="bpsm_b_members_onlyif_friends" name="bpsm_b_members_onlyif_friends" <?php if ($this->sg->GetOption("b_members_onlyif_friends")) echo "checked=\"checked\""; ?> /> <?php _e('Filter loc elements if member has friends', 'bp-sitemap'); ?></label>
								<br>
								<label for="bpsm_b_members_onlyif_xprofile">
								<input type="checkbox"  id="bpsm_b_members_onlyif_xprofile" name="bpsm_b_members_onlyif_xprofile" <?php if ($this->sg->GetOption("b_members_onlyif_xprofile")) echo "checked=\"checked\""; ?> /> <?php _e('Filter loc elements if member has xprofile data saved', 'bp-sitemap'); ?></label>
								<br>
								<small><em>(<?php _e('If disabled - all possible member sunbav url locs will be generated.)', 'bp-sitemap'); ?></em></small>
							</fieldset>
						</td>
					</tr>
				</table>

				<br class="clear"/>

				<h3><?php _e('Ping Search Engine Options', 'bp-sitemap'); ?></h3>

				<table class="form-table">
					<tr valign="top">
						<th scope="row"><label for="bpsm_b_ping"><?php _e( 'Google', 'bp-sitemap' ); ?></label></th>
						<td>
							<input type="checkbox" id="bpsm_b_ping" name="bpsm_b_ping" <?php echo ($this->sg->GetOption("b_ping")==true?"checked=\"checked\"":""); ?> /><br />
							<a href="https://www.google.com/webmasters/tools/home">Google Webmaster Tools</a>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="bpsm_b_pingmsn"><?php _e( 'Bing', 'bp-sitemap' ); ?></label></th>
						<td>
							<input type="checkbox" id="bpsm_b_pingmsn" name="bpsm_b_pingmsn" <?php echo ($this->sg->GetOption("b_pingmsn")==true?"checked=\"checked\"":""); ?> /><br />
							<a href="http://www.bing.com/webmaster">Bing Webmaster Center</a>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="bpsm_b_pingask"><?php _e( 'Ask', 'bp-sitemap' ); ?></label></th>
						<td>
							<input type="checkbox" id="bpsm_b_pingask" name="bpsm_b_pingask" <?php echo ($this->sg->GetOption("b_pingask")==true?"checked=\"checked\"":""); ?> /><br />
							<a href="http://about.ask.com/docs/about/webmasters.shtml#22">Ask.com Webmasters</a>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="bpsm_b_pingyahoo"><?php _e( 'Yahoo', 'bp-sitemap' ); ?></label></th>
						<td>
							<input type="checkbox" id="bpsm_b_pingyahoo" name="bpsm_b_pingyahoo" <?php echo ($this->sg->GetOption("b_pingyahoo")==true?"checked=\"checked\"":""); ?> /> 
							<label for="bpsm_b_yahookey"><?php _e('Your Application ID:', 'bp-sitemap'); ?> <input type="text" name="bpsm_b_yahookey" id="bpsm_b_yahookey" value="<?php echo $this->sg->GetOption("b_yahookey"); ?>" /></label><br />
							<a href="http://developer.yahoo.net/about/">Web Services by Yahoo! - Request API Key</a>
						</td>
					</tr>
				</table>

				<br class="clear"/>

				<h3><?php _e('Sitemap Configuration', 'bp-sitemap'); ?></h3>

				<br class="clear"/>

				<h4><?php _e('SitemapIndex Location (file submitted to search engines - unique to BuddyPress)', 'bp-sitemap'); ?></h4>

				<table class="form-table">
					<tr valign="top">
						<th scope="row"><label for="bpsm_location_useauto"><?php _e( 'Auto Sitemapindex Location', 'bp-sitemap' ); ?></label></th>
						<td>
							<input type="radio" id="bpsm_location_useauto" name="bpsm_b_location_mode" value="auto" <?php echo ($this->sg->GetOption("b_location_mode")=="auto"?"checked=\"checked\"":""); ?> /> <?php _e('99% of the time you want to use this.', 'bp-sitemap'); ?>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="bpsm_b_filename"><?php _e( 'Filename of sitemapindex', 'bp-sitemap' ); ?></label></th>
						<td>
							<input type="text" id="bpsm_b_filename" name="bpsm_b_filename" value="<?php echo $this->sg->GetOption("b_filename"); ?>" /> <?php _e( '(expects .xml filetype)', 'bp-sitemap' ); ?><br />
							<?php _e('Detected Path', 'bp-sitemap'); ?>: <?php echo $this->sg->getXmlPath(true); ?><br /><?php _e('Detected URL', 'bp-sitemap'); ?>: <a href="<?php echo $this->sg->getXmlUrl(true); ?>"><?php echo $this->sg->getXmlUrl(true); ?></a>
						</td>
					</tr>
					
					
					<tr valign="top">
						<th scope="row"><label for="bpsm_location_usemanual"><?php _e( 'Custom sitemapindex location', 'bp-sitemap' ); ?></label></th>
						<td>
							<input type="radio" id="bpsm_location_usemanual" name="bpsm_b_location_mode" value="manual" <?php echo ($this->sg->GetOption("b_location_mode")=="manual"?"checked=\"checked\"":""); ?>  />
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="bpsm_b_filename_manual"><?php _e( 'Server Path to sitemapindex', 'bp-sitemap' ); ?></label></th>
						<td>
							<input style="width:70%" type="text" id="bpsm_b_filename_manual" name="bpsm_b_filename_manual" value="<?php echo (!$this->sg->GetOption("b_filename_manual")?$this->sg->getXmlPath():$this->sg->GetOption("b_filename_manual")); ?>" /> <br />
							<?php _e('Absolute or relative web server path to the sitemapindex file, including name. (expects .xml filetype)','bp-sitemap'); 
							echo "<br />"; _e('Example','bp-sitemap'); echo ": /var/www/htdocs/wordpress/bp-sitemap.xml"; ?><br />
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="bpsm_b_fileurl_manual"><?php _e( 'URL to sitemapindex', 'bp-sitemap' ); ?></label></th>
						<td>
							<input style="width:70%" type="text" id="bpsm_b_fileurl_manual" name="bpsm_b_fileurl_manual" value="<?php echo (!$this->sg->GetOption("b_fileurl_manual")?$this->sg->getXmlUrl():$this->sg->GetOption("b_fileurl_manual")); ?>" /> <br />
							<?php _e('Complete URL to the sitemap file, including name.','bp-sitemap');
							echo "<br />"; _e('Example','bp-sitemap'); echo ": http://www.yourdomain.com/bp-sitemap.xml"; ?><br />
							<small><em><?php _e('Proper sitemap protocol - use the highest level.', 'bp-sitemap'); ?></em></small>
						</td>
					</tr>
				</table>
				
				<h4><?php _e('Child Sitemap Files', 'bp-sitemap'); ?></h4>

				<table class="form-table">

					<tr valign="top">
						<th scope="row"><?php _e( 'File Types', 'bp-sitemap' ); ?></th>
						<td>
							<fieldset>
								<legend class="screen-reader-text"><span><?php _e( 'File Types', 'bp-sitemap' ); ?></span></legend>
								<label for="bpsm_b_xml">
									<input type="checkbox" id="bpsm_b_xml" name="bpsm_b_xml" <?php echo ($this->sg->GetOption("b_xml")==true?"checked=\"checked\"":""); ?> /> <?php _e('Write normal XML childmap files (yourfilename-bpslugs.xml) - not required', 'bp-sitemap'); ?>
								</label>
								<br>
								<label for="bpsm_b_gzip">
									<input type="checkbox" id="bpsm_b_gzip" name="bpsm_b_gzip" <?php if(function_exists("gzencode")) { echo ($this->sg->GetOption("b_gzip")==true?"checked=\"checked\"":""); } else echo "disabled=\"disabled\"";  ?> /> <?php _e('Write gzip childmap files (yourfilename-bpslugs.xml.gz) - recommended', 'bp-sitemap'); ?>
								</label>
								<br>
								<small><em>(<?php _e('Several sitemap files are generated for each BuddyPress component slug.)', 'bp-sitemap'); ?></em></small>
							</fieldset>
						</td>
					</tr>
					<?php $useDefStyle = ($this->sg->GetDefaultStyle() && $this->sg->GetOption('b_style_default')===true); ?>
					<tr valign="top">
						<th scope="row"><label for="bpsm_b_style"><?php _e( 'XSLT stylesheet on childmaps', 'bp-sitemap' ); ?></label></th>
						<td>
							<?php if($this->sg->GetDefaultStyle()): ?><label for="bpsm_b_style_default"><input <?php echo ($useDefStyle?'checked="checked" ':''); ?> type="checkbox" id="bpsm_b_style_default" name="bpsm_b_style_default" onclick="document.getElementById('bpsm_b_style').disabled = this.checked;" /> <?php _e('Use default', 'bp-sitemap'); ?> <?php endif; ?> <input <?php echo ($useDefStyle?'disabled="disabled" ':''); ?> type="text" name="bpsm_b_style" id="bpsm_b_style"  value="<?php echo $this->sg->GetOption("b_style"); ?>" /></label><br />
							(<?php _e('Full or relative URL to your .xsl file', 'bp-sitemap'); ?>)
						</td>
					</tr>					
				</table>

				<br class="clear"/>

				<h3><?php _e('Advanced Options', 'bp-sitemap'); ?></h3>

				<table class="form-table">
					<tr valign="top">
						<th scope="row"><label for="bpsm_b_manual_enabled"><?php _e( 'Build Sitemap via GET Url Request', 'bp-sitemap' ); ?></label><a href="javascript:void(document.getElementById('bpsm_manual_help').style.display='');">[?]</a></th>
						<td>
							<input type="hidden" name="bpsm_b_manual_key" value="<?php echo $this->sg->GetOption("b_manual_key"); ?>" />
							<input type="checkbox" id="bpsm_b_manual_enabled" name="bpsm_b_manual_enabled" <?php echo ($this->sg->GetOption("b_manual_enabled")==true?"checked=\"checked\"":""); ?> /> <?php _e('Allow a special url to generate a manual build of this sitemap', 'bp-sitemap'); ?>
							<span id="bpsm_manual_help" style="display:none;"><br /><?php echo str_replace("%1",trailingslashit(get_bloginfo('siteurl')) . "?bpsm_command=build&amp;bpsm_key=" . $this->sg->GetOption("b_manual_key"),__('This will allow you to refresh your sitemap externally. Use the following URL to start the process:<br /> <a href="%1">%1</a>', 'bp-sitemap')); ?></span>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="bpsm_b_memory"><?php _e( 'PHP Memory limit', 'bp-sitemap' ); ?></label></th>
						<td>
							<input type="text" name="bpsm_b_memory" id="bpsm_b_memory" style="width:40px;" value="<?php echo $this->sg->GetOption("b_memory"); ?>" /> <?php echo htmlspecialchars(__('e.g. "4M", "16M"', 'bp-sitemap')); ?>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="bpsm_b_time"><?php _e( 'PHP Execution time limit', 'bp-sitemap' ); ?></label></th>
						<td>
							<input type="text" name="bpsm_b_time" id="bpsm_b_time" style="width:40px;" value="<?php echo ($this->sg->GetOption("b_time")===-1?'':$this->sg->GetOption("b_time")); ?>" /> <?php echo htmlspecialchars(__('in seconds, e.g. "60" or "0" for unlimited', 'bp-sitemap')); ?>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="bpsm_b_auto_delay"><?php _e('Build the sitemap in a background process', 'bp-sitemap'); ?></label></th>
						<td>
							<input type="checkbox"  id="bpsm_b_auto_delay" name="bpsm_b_auto_delay" <?php echo ($this->sg->GetOption("b_auto_delay")==true&&!$forceDirect?"checked=\"checked\"":""); ?> /><br />
						</td>
					</tr>
				</table>

				<p class="submit">
					<?php wp_nonce_field('bp-sitemap'); ?>
					<input type="submit" name="bpsm_update" value="<?php _e('Update options', 'bp-sitemap'); ?>" />
					<input type="submit" onclick='return confirm("Do you really want to reset your configuration?");' class="bpsm_warning" name="bpsm_reset_config" value="<?php _e('Reset options', 'bp-sitemap'); ?>" />
				</p>

			</form>
		</div>
		
	<?php }
} ?>