<?php

//Enable for dev! Good code doesn't generate any notices...
//error_reporting(E_ALL);
//ini_set("display_errors",1);

/**
 * Represents the status (success and failures) of a building process
 * @author Arne Brachhold
 * @package sitemap
 * @since 3.0b5
 */
class BPGoogleSitemapGeneratorStatus {

	function BPGoogleSitemapGeneratorStatus() {
		$this->_startTime = $this->GetMicrotimeFloat();
		
		$exists = get_option("bpsm_status");
		
		if($exists === false) add_option("bpsm_status","","Status","no");
		
		$this->Save();
	}
	
	function Save() {
		update_option("bpsm_status",$this);
	}
	
	/**
	 * Returns the last saved status object or null
	 * 
	 * @return BPGoogleSitemapGeneratorStatus
	 */
	function &Load() {
		$status = @get_option("bpsm_status");
		if(is_a($status,"BPGoogleSitemapGeneratorStatus")) return $status;
		else return null;
	}
	
	/**
	 * @var float $_startTime The start time of the building process
	 * @access private
	 */
	var $_startTime = 0;
	
	/**
	 * @var float $_endTime The end time of the building process
	 * @access private
	 */
	var $_endTime = 0;
	
	/**
	 * @var bool $$_hasChanged Indicates if the sitemap content has changed
	 * @access private
	 */
	var $_hasChanged = true;
	
	/**
	 * @var int $_memoryUsage The amount of memory used in bytes
	 * @access private
	 */
	var $_memoryUsage = 0;
	
	/**
	 * @var int $_lastTime The time when the last step-update occured. This value is updated every 50 posts.
	 * @access private
	 */
	var $_lastTime = 0;
	
	function End($hasChanged = true) {
		$this->_endTime = $this->GetMicrotimeFloat();
		
		$this->SetMemoryUsage();
		
		$this->_hasChanged = $hasChanged;
		
		$this->Save();
	}
	
	function SetMemoryUsage() {
		if(function_exists("memory_get_peak_usage")) {
			$this->_memoryUsage = memory_get_peak_usage(true);
		} else if(function_exists("memory_get_usage")) {
			$this->_memoryUsage =  memory_get_usage(true);
		}
	}
	
	function GetMemoryUsage() {
		return round($this->_memoryUsage / 1024 / 1024,2);
	}
	
	var $_childmapssteps = Array();
	
	function SaveChildMapStep($map,$count) {
	
		$this->SetMemoryUsage();

		$this->_childmapssteps[$map]['_lastTime'] = $this->GetMicrotimeFloat();
		$this->_childmapssteps[$map]['_records'] = $count;
		
		$this->Save();
	}
	
	function GetTime() {
		return round($this->_endTime - $this->_startTime,2);
	}
	
	function GetStartTime() {
		return round($this->_startTime, 2);
	}
	
	function GetLastTime() {
		return round($this->_lastTime - $this->_startTime,2);
	}
	
	var $_usedXml = false;
	var $_xmlSuccess = false;
	var $_xmlSuccessMessage = '';
	var $_xmlPath = '';
	var $_xmlUrl = '';
	
	function StartIndexXml($path,$url) {
		$this->_usedXml = true;
		$this->_xmlPath = $path;
		$this->_xmlUrl = $url;
		
		$this->Save();
	}
	
	function EndIndexXml($success,$msg = '') {
		$this->_xmlSuccess = $success;
		if (!$success) $this->_xmlSuccessMessage = $msg;
		
		$this->Save();
	}
	

	//set up our childmap logging
	var $_childmaps = Array();
	
	function StartChildMapXml($map,$path,$url) {
		$this->_childmaps[$map]['usedXml'] = true;
		$this->_childmaps[$map]['xmlPath'] = $path;
		$this->_childmaps[$map]['xmlUrl'] = $url;
		$this->_childmaps[$map]['usedXmlStartTime'] = $this->GetMicrotimeFloat();
		
		$this->Save();
	}
	
	function EndChildMapXml($map,$success,$msg = '') {
		$this->_childmaps[$map]['xmlSuccess'] = $success;
		$this->_childmaps[$map]['usedXmlEndTime'] = $this->GetMicrotimeFloat();
		if (!$success) $this->_childmaps[$map]['xmlSuccessMsg'] = $msg;
		
		$this->Save();
	}
	
	function StartChildMapZip($map,$path,$url) {
		$this->_childmaps[$map]['usedZip'] = true;
		$this->_childmaps[$map]['zipPath'] = $path;
		$this->_childmaps[$map]['zipUrl'] = $url;
		$this->_childmaps[$map]['usedZipStartTime'] = $this->GetMicrotimeFloat();

		
		$this->Save();
	}
	
	function EndChildMapZip($map,$success,$msg = '') {
		$this->_childmaps[$map]['zipSuccess'] = $success;
		$this->_childmaps[$map]['usedZipEndTime'] = $this->GetMicrotimeFloat();
		if (!$success) $this->_childmaps[$map]['xmlSuccessMsg'] = $msg;
		
		$this->Save();
	}
	
	
	//set up our ping logging
	var $_childpings = Array();
	
	function StartChildPing($ping,$url) {
	
		$this->_childpings[$ping]['url'] = $url;
		$this->_childpings[$ping]['used'] = true;
		$this->_childpings[$ping]['starttime'] = $this->GetMicrotimeFloat();
		
		$this->Save();
	}
	
	function EndChildPing($ping,$success,$msg = '') {
		$this->_childpings[$ping]['success'] = $success;
		$this->_childpings[$ping]['endtime'] = $this->GetMicrotimeFloat();
		if (!$success) $this->_childpings[$ping]['msg'] = $msg;
		
		$this->Save();
	}
	
	function GetPingTime($ping) {
		return round($this->_childpings[$ping]['endtime'] - $this->_childpings[$ping]['starttime'],2);
	}
	
	//internal timestamper
	function GetMicrotimeFloat() {
		list($usec, $sec) = explode(" ", microtime());
		return ((float)$usec + (float)$sec);
	}
}
		
/**
 * Represents an item in the page list
 * @author Arne Brachhold
 * @package sitemap
 * @since 3.0
 */
class BPGoogleSitemapGeneratorPage {
	
	/**
	 * @var string $_url Sets the URL or the relative path to the blog dir of the page
	 * @access private
	 */
	var $_url;
	
	/**
	 * @var float $_priority Sets the priority of this page
	 * @access private
	 */
	var $_priority;
	
	/**
	 * @var string $_changeFreq Sets the chanfe frequency of the page. I want Enums!
	 * @access private
	 */
	var $_changeFreq;
	
	/**
	 * @var int $_lastMod Sets the lastMod date as a UNIX timestamp.
	 * @access private
	 */
	var $_lastMod;
	
	/**
	 * Initialize a new page object
	 *
	 * @since 3.0
	 * @access public
	 * @author Arne Brachhold
	 * @param bool $enabled Should this page be included in thesitemap
	 * @param string $url The URL or path of the file
	 * @param float $priority The Priority of the page 0.0 to 1.0
	 * @param string $changeFreq The change frequency like daily, hourly, weekly
	 * @param int $lastMod The last mod date as a unix timestamp
	 */
	function BPGoogleSitemapGeneratorPage($url="",$priority=0.0,$changeFreq="never",$lastMod=0) {
		$this->SetUrl($url);
		$this->SetProprity($priority);
		$this->SetChangeFreq($changeFreq);
		$this->SetLastMod($lastMod);
	}
	
	/**
	 * Returns the URL of the page
	 *
	 * @return string The URL
	 */
	function GetUrl() {
		return $this->_url;
	}
	
	/**
	 * Sets the URL of the page
	 *
	 * @param string $url The new URL
	 */
	function SetUrl($url) {
		$this->_url=(string) $url;
	}
	
	/**
	 * Returns the priority of this page
	 *
	 * @return float the priority, from 0.0 to 1.0
	 */
	function GetPriority() {
		return $this->_priority;
	}
	
	/**
	 * Sets the priority of the page
	 *
	 * @param float $priority The new priority from 0.1 to 1.0
	 */
	function SetProprity($priority) {
		$this->_priority=floatval($priority);
	}
	
	/**
	 * Returns the change frequency of the page
	 *
	 * @return string The change frequncy like hourly, weekly, monthly etc.
	 */
	function GetChangeFreq() {
		return $this->_changeFreq;
	}
	
	/**
	 * Sets the change frequency of the page
	 *
	 * @param string $changeFreq The new change frequency
	 */
	function SetChangeFreq($changeFreq) {
		$this->_changeFreq=(string) $changeFreq;
	}
	
	/**
	 * Returns the last mod of the page
	 *
	 * @return int The lastmod value in seconds
	 */
	function GetLastMod() {
		return $this->_lastMod;
	}
	
	/**
	 * Sets the last mod of the page
	 *
	 * @param int $lastMod The lastmod of the page
	 */
	function SetLastMod($lastMod) {
		$this->_lastMod=intval($lastMod);
	}
	
	function Render() {
		
		if($this->_url == "/" || empty($this->_url)) return '';
		
		$r="";
		$r.= "\t<url>\n";
		$r.= "\t\t<loc>" . $this->EscapeXML($this->_url) . "</loc>\n";
		if($this->_lastMod>0) $r.= "\t\t<lastmod>" . date('Y-m-d\TH:i:s+00:00',$this->_lastMod) . "</lastmod>\n";
		if(!empty($this->_changeFreq)) $r.= "\t\t<changefreq>" . $this->_changeFreq . "</changefreq>\n";
		if($this->_priority!==false && $this->_priority!=="") $r.= "\t\t<priority>" . number_format($this->_priority,1) . "</priority>\n";
		$r.= "\t</url>\n";
		return $r;
	}
	
	function EscapeXML($string) {
		return str_replace ( array ( '&', '"', "'", '<', '>'), array ( '&amp;' , '&quot;', '&apos;' , '&lt;' , '&gt;'), $string);
	}
}

class BPGoogleSitemapGeneratorXmlEntry {
	
	var $_xml;
	
	function BPGoogleSitemapGeneratorXmlEntry($xml) {
		$this->_xml = $xml;
	}
	
	function Render() {
		return $this->_xml;
	}
}

class BPGoogleSitemapGeneratorIndexEntry {
	
	var $_xml;

	function BPGoogleSitemapGeneratorIndexEntry($loc,$now) {
		$this->_xml = '<sitemap><loc>'. $loc .'</loc><lastmod>'. $now .'</lastmod></sitemap>';
	}
	
	function Render() {
		return $this->_xml;
	}
}



class BPGoogleSitemapGeneratorDebugEntry extends BPGoogleSitemapGeneratorXmlEntry {
	
	function Render() {
		return "<!-- " . $this->_xml . " -->\n";
	}
}

/**
 * Class to generate a sitemaps.org Sitemaps compliant sitemap of a WordPress blog.
 *
 * @package sitemap
 * @author Arne Brachhold
 * @since 3.0
*/
class BPGoogleSitemapGenerator {
	/**
	 * @var Version of the generator in SVN
	*/
	var $_svnVersion = '1';
	
	/**
	 * @var array The unserialized array with the stored options
	 */
	var $_options = array();
	
	/**
	 * @var array The saved additional pages
	 */
	var $_pages = array();

	/**
	 * @var array The values and names of the change frequencies
	 */
	var $_freqNames = array();
	
	/**
	 * @var bool True if init complete (options loaded etc)
	 */
	var $_initiated = false;
	
	/**
	 * @var string Holds the last error if one occurs when writing the files
	 */
	var $_lastError=null;
	
	/**
	 * @var bool Defines if the sitemap building process is active at the moment
	 */
	var $_isActive = false;
	
	/**
	 * @var bool Defines if the sitemap building process has been scheduled via Wp cron
	 */
	var $_isScheduled = false;

	/**
	 * @var object The file handle which is used to write the sitemap file
	 */
	var $_fileHandle = null;
	
	/**
	 * @var object The file handle which is used to write the zipped sitemap file
	 */
	var $_fileZipHandle = null;
	
	/**
	 * Holds the user interface object
	 * 
	 * @since 3.1.1
	 * @var BPBPGoogleSitemapGeneratorUI
	 */
	var $_ui = null;
	
	/**
	 * Returns the path to the blog directory
	 *
	 * @since 3.0
	 * @access private
	 * @author Arne Brachhold
	 * @return string The full path to the blog directory
	*/
	function GetHomePath() {
		
		$res="";
		//Check if we are in the admin area -> get_home_path() is avaiable
		if(function_exists("get_home_path")) {
			$res = get_home_path();
		} else {
			//get_home_path() is not available, but we can't include the admin
			//libraries because many plugins check for the "check_admin_referer"
			//function to detect if you are on an admin page. So we have to copy
			//the get_home_path function in our own...
			$home = get_option( 'home' );
			if ( $home != '' && $home != get_option( 'siteurl' ) ) {
				$home_path = parse_url( $home );
				$home_path = $home_path['path'];
				$root = str_replace( $_SERVER["PHP_SELF"], '', $_SERVER["SCRIPT_FILENAME"] );
				$home_path = trailingslashit( $root.$home_path );
			} else {
				$home_path = ABSPATH;
			}

			$res = $home_path;
		}
		return $res;
	}
	
	/**
	 * Returns the path to the directory where the plugin file is located
	 * @since 3.0b5
	 * @access private
	 * @author Arne Brachhold
	 * @return string The path to the plugin directory
	 */
	function GetPluginPath() {
		$path = dirname(__FILE__);
		return trailingslashit(str_replace("\\","/",$path));
	}
	
	/**
	 * Returns the URL to the directory where the plugin file is located
	 * @since 3.0b5
	 * @access private
	 * @author Arne Brachhold
	 * @return string The URL to the plugin directory
	 */
	function GetPluginUrl() {
		
		//Try to use WP API if possible, introduced in WP 2.6
		if (function_exists('plugins_url')) return trailingslashit(plugins_url(basename(dirname(__FILE__))));
		
		//Try to find manually... can't work if wp-content was renamed or is redirected
		$path = dirname(__FILE__);
		$path = str_replace("\\","/",$path);
		$path = trailingslashit(get_bloginfo('wpurl')) . trailingslashit(substr($path,strpos($path,"wp-content/")));
		return $path;
	}
	
	/**
	 * Returns the URL to default XSLT style if it exists
	 * @since 3.0b5
	 * @access private
	 * @author Arne Brachhold
	 * @return string The URL to the default stylesheet, empty string if not available.
	 */
	function GetDefaultStyle() {
		$p = $this->GetPluginPath();
		if(file_exists($p . "bp-sitemap.xsl")) {
			$url = $this->GetPluginUrl();
			//If called over the admin area using HTTPS, the stylesheet would also be https url, even if the blog frontend is not.
			if(substr(get_bloginfo('url'),0,5) !="https" && substr($url,0,5)=="https") $url="http" . substr($url,5);
			return $url . 'bp-sitemap.xsl';
		}
		return '';
	}
	
	/**
	 * Returns the URL to default XSLT style if it exists
	 * @since 3.0b5
	 * @access private
	 * @author Arne Brachhold
	 * @return string The URL to the default stylesheet, empty string if not available.
	 */
	function GetDefaultIndexStyle() {
		$p = $this->GetPluginPath();
		if(file_exists($p . "bp-sitemapindex.xsl")) {
			$url = $this->GetPluginUrl();
			//If called over the admin area using HTTPS, the stylesheet would also be https url, even if the blog frontend is not.
			if(substr(get_bloginfo('url'),0,5) !="https" && substr($url,0,5)=="https") $url="http" . substr($url,5);
			return $url . 'bp-sitemapindex.xsl';
		}
		return '';
	}
	
	/**
	 * Sets up the default configuration
	 *
	 * @since 3.0
	 * @access private
	 * @author Arne Brachhold
	*/
	function InitOptions() {
		
		$this->_options=array();
		$this->_options["bpsm_b_filename"]="bp-sitemap.xml";		//Name of the Sitemap file
		$this->_options["bpsm_b_debug"]=true;					//Write debug messages in the xml file
		$this->_options["bpsm_b_xml"]=true;					//Create a .xml file
		$this->_options["bpsm_b_gzip"]=true;					//Create a gzipped .xml file(.gz) file
		$this->_options["bpsm_b_location_mode"]="auto";		//Mode of location, auto or manual
		$this->_options["bpsm_b_filename_manual"]="";			//Manuel filename
		$this->_options["bpsm_b_fileurl_manual"]="";			//Manuel fileurl
		
		$this->_options["bpsm_b_ping"]=true;					//Auto ping Google
		$this->_options["bpsm_b_pingyahoo"]=false;			//Auto ping YAHOO
		$this->_options["bpsm_b_yahookey"]='';				//YAHOO Application Key
		$this->_options["bpsm_b_pingask"]=true;				//Auto ping Ask.com
		$this->_options["bpsm_b_pingmsn"]=true;				//Auto ping MSN
		
		$this->_options["bpsm_b_manual_enabled"]=true;		//Allow manual creation of the sitemap via GET request
		$this->_options["bpsm_b_auto_delay"]=true;			//Use WP Cron to execute the building process in the background
		$this->_options["bpsm_b_manual_key"]=md5(microtime());//The secret key to build the sitemap via GET request
		
		$this->_options["bpsm_b_memory"] = '';				//Set Memory Limit (e.g. 16M)
		$this->_options["bpsm_b_time"] = -1;					//Set time limit in seconds, 0 for unlimited, -1 for disabled
		$this->_options["bpsm_b_safemode"] = false;			//Enable MySQL Safe Mode (doesn't use unbuffered results)
		
		$this->_options["bpsm_b_style_default"] = true;		//Use default style
		$this->_options["bpsm_b_style"] = '';					//Include a stylesheet in the XML

		$this->_options["bpsm_b_last_activity"]=true;				//Include the last_activity for members and groups
		$this->_options["bpsm_b_members_onlyif_groups"]=true;		//display member/groups only if they have groups
		$this->_options["bpsm_b_members_onlyif_friends"]=true;		//display member/friends only if they have friends
		$this->_options["bpsm_b_members_onlyif_xprofile"]=true;		//display member/profile only if xprofile data is there
		$this->_options["bpsm_b_members_onlyif_activity"]=true;		//display member/activity ony if they have activity (todo)

		$this->_options["bpsm_in_lastmod"]=true;				//Include the last modification date
	}
	
	/**
	 * Loads the configuration from the database
	 *
	 * @since 3.0
	 * @access private
	 * @author Arne Brachhold
	*/
	function LoadOptions() {
		
		$this->InitOptions();
		
		//First init default values, then overwrite it with stored values so we can add default
		//values with an update which get stored by the next edit.
		$storedoptions=get_option("bpsm_options");
		if($storedoptions && is_array($storedoptions)) {
			foreach($storedoptions AS $k=>$v) {
				$this->_options[$k]=$v;
			}
		} else update_option("bpsm_options",$this->_options); //First time use, store default values
	}
	
	/**
	 * Initializes a new Google Sitemap Generator
	 *
	 * @since 3.0
	 * @access private
	 * @author Arne Brachhold
	*/
	function BPGoogleSitemapGenerator() {


		
		
	}
	
	/**
	 * Returns the version of the generator
	 *
	 * @since 3.0
	 * @access public
	 * @author Arne Brachhold
	 * @return int The version
	*/
	function GetVersion() {
		return BPGoogleSitemapGeneratorLoader::GetVersion();
	}
	
	/**
	 * Returns all parent classes of a class
	 *
	 * @param $className string The name of the class
	 *
	 * @since 3.0
	 * @access private
	 * @author Arne Brachhold
	 * @return array An array which contains the names of the parent classes
	*/
	function GetParentClasses($classname) {
		$parent = get_parent_class($classname);
		$parents = array();
		if (!empty($parent)) {
			$parents = $this->GetParentClasses($parent);
			$parents[] = strtolower($parent);
		}
		return $parents;
	}
	
	/**
	 * Returns if a class is a subclass of another class
	 *
	 * @param $className string The name of the class
	 * @param $$parentName string The name of the parent class
	 *
	 * @since 3.0
	 * @access private
	 * @author Arne Brachhold
	 * @return bool true if the given class is a subclass of the other one
	*/
	function IsSubclassOf($className, $parentName) {
		
		$className = strtolower($className);
		$parentName = strtolower($parentName);
		
		if(empty($className) || empty($parentName) || !class_exists($className) || !class_exists($parentName)) return false;
		
		$parents=$this->GetParentClasses($className);
		
		return in_array($parentName,$parents);
	}
		
	/**
	 * Loads up the configuration and validates the prioity providers
	 *
	 * This method is only called if the sitemaps needs to be build or the admin page is displayed.
	 *
	 * @since 3.0
	 * @access private
	 * @author Arne Brachhold
	*/
	function Initate() {
		if(!$this->_initiated) {
			
			//Loading language file...
			
			//$currentLocale = get_locale();
			//if(!empty($currentLocale)) {
			//	$moFile = dirname(__FILE__) . "/lang/sitemap-" . $currentLocale . ".mo";
			//	if(@file_exists($moFile) && is_readable($moFile)) load_textdomain('bp-sitemap', $moFile);
			//}
			
			$this->_freqNames = array(
				"always"=>__("Always","sitemap"),
				"hourly"=>__("Hourly","sitemap"),
				"daily"=>__("Daily","sitemap"),
				"weekly"=>__("Weekly","sitemap"),
				"monthly"=>__("Monthly","sitemap"),
				"yearly"=>__("Yearly","sitemap"),
				"never"=>__("Never","sitemap")
			);
			
			
			$this->LoadOptions();
			
			$this->_initiated = true;
		}
	}
	
	/**
	 * Returns the instance of the Sitemap Generator
	 *
	 * @since 3.0
	 * @access public
	 * @return BPGoogleSitemapGenerator The instance or null if not available.
	 * @author Arne Brachhold
	*/
	function &GetInstance() {
		if(isset($GLOBALS["bpsm_instance"])) {
			return $GLOBALS["bpsm_instance"];
		} else return null;
	}
	
	/**
	 * Returns if the sitemap building process is currently active
	 *
	 * @since 3.0
	 * @access public
	 * @return bool true if active
	 * @author Arne Brachhold
	*/
	function IsActive() {
		$inst = &BPGoogleSitemapGenerator::GetInstance();
		return ($inst != null && $inst->_isActive);
	}
	
	/**
	 * Returns if the compressed sitemap was activated
	 *
	 * @since 3.0b8
	 * @access private
	 * @author Arne Brachhold
	 * @return true if compressed
	 */
	function IsGzipEnabled() {
		return ($this->GetOption("b_gzip")===true && function_exists("gzwrite"));
	}

	/**
	 * Enables the Google Sitemap Generator and registers the WordPress hooks
	 *
	 * @since 3.0
	 * @access public
	 * @author Arne Brachhold
	*/
	function Enable() {
		if(!isset($GLOBALS["bpsm_instance"])) {
			$GLOBALS["bpsm_instance"]=new BPGoogleSitemapGenerator();
		}
	}
	
	/**
	 * Checks if sitemap building after content changed is enabled and rebuild the sitemap
	 *
	 * @param int $postID The ID of the post to handle. Used to avoid double rebuilding if more than one hook was fired.
	 * @param bool $external Added in 3.1.9. Skips checking of b_auto_enabled if set to true
	 * @since 3.0
	 * @access public
	 * @author Arne Brachhold
	*/
	function CheckForAutoBuild($postID, $external = false) {
		$this->Initate();

		//if not importing.
		if( $external && ( !defined('WP_IMPORTING') || WP_IMPORTING != true ) ) {
			
			//Build the sitemap directly or schedule it with WP cron
			if($this->GetOption("b_auto_delay")==true) {
				if(!$this->_isScheduled) {
					//Schedule in 30 seconds, this should be enough to catch all changes.
					//Clear all other existing hooks, so the sitemap is only built once.
					wp_clear_scheduled_hook('bpsm_build_cron');
					wp_schedule_single_event(time()+30,'bpsm_build_cron');
					$this->_isScheduled = true;
				}
			} else {
				//Build sitemap only once and never in bulk mode
				if(!isset($_GET["delete"]) || count((array) $_GET['delete']) <= 0 ) {
					$this->BuildSitemap();
				}
			}
		}
		
	}
	
	/**
	 * Builds the sitemap by external request, for example other plugins.
	 * 
	 * @since 3.1.9
	 * @return null
	 */
	function BuildNowRequest() {
		$this->CheckForAutoBuild(null, true);	
	}
	
	/**
	 * Builds the sitemap by wp-cron request
	 * 
	 * @since 3.1.9
	 * @return null
	 */
	function BuildNowCronRequest() {
		
		//lets not run if already scheduled via manual autodelay build
		if(!$this->_isScheduled) {
			$this->_isScheduled = true;
			$this->BuildSitemap();
		}
	}
	
	/**
	 * Checks if the rebuild request was send and starts to rebuilt the sitemap
	 *
	 * @since 3.0
	 * @access public
	 * @author Arne Brachhold
	*/
	function CheckForManualBuild() {
		if(!empty($_GET["bpsm_command"]) && !empty($_GET["bpsm_key"])) {
			$this->Initate();
			if($this->GetOption("b_manual_enabled")===true && $_GET["bpsm_command"]=="build" && $_GET["bpsm_key"]==$this->GetOption("b_manual_key")) {
				$this->BuildSitemap();
				echo "DONE";
				exit;
			}
		}
	}
	
	/**
	 * Returns the URL for the sitemap file
	 *
	 * @since 3.0
	 * @access private
	 * @author Arne Brachhold
	 * @param bool $forceAuto Force the return value to the autodetected value.
	 * @return The URL to the Sitemap file
	*/
	function GetXmlUrl($forceAuto=false) {
		
		if(!$forceAuto && $this->GetOption("b_location_mode")=="manual") {
			return $this->GetOption("b_fileurl_manual");
		} else {
			return trailingslashit(get_bloginfo('siteurl')). $this->GetOption("b_filename");
		}
	}

	/**
	 * Returns the URL for the gzipped sitemap file
	 *
	 * @since 3.0
	 * @access private
	 * @author Arne Brachhold
	 * @param bool $forceAuto Force the return value to the autodetected value.
	 * @return The URL to the gzipped Sitemap file
	*/
	function GetZipUrl($forceAuto=false) {
		return $this->GetXmlUrl($forceAuto) . ".gz";
	}
	
	/**
	 * Returns the file system path to the sitemap file
	 *
	 * @since 3.0
	 * @access private
	 * @author Arne Brachhold
	 * @param bool $forceAuto Force the return value to the autodetected value.
	 * @return The file system path;
	*/
	function GetXmlPath($forceAuto=false) {
		if(!$forceAuto && $this->GetOption("b_location_mode")=="manual") {
			return $this->GetOption("b_filename_manual");
		} else {
			return $this->GetHomePath()  . $this->GetOption("b_filename");
		}
	}
	
	/**
	 * Returns the file system path to the gzipped sitemap file
	 *
	 * @since 3.0
	 * @access private
	 * @author Arne Brachhold
	 * @param bool $forceAuto Force the return value to the autodetected value.
	 * @return The file system path;
	*/
	function GetZipPath($forceAuto=false) {
		return $this->GetXmlPath($forceAuto) . ".gz";
	}
	
	/**
	 * Returns the option value for the given key
	 *
	 * @since 3.0
	 * @access private
	 * @author Arne Brachhold
	 * @param $key string The Configuration Key
	 * @return mixed The value
	 */
	function GetOption($key) {
		$key="bpsm_" . $key;
		if(array_key_exists($key,$this->_options)) {
			return $this->_options[$key];
		} else return null;
	}
	
	/**
	 * Sets an option to a new value
	 *
	 * @since 3.0
	 * @access private
	 * @author Arne Brachhold
	 * @param $key string The configuration key
	 * @param $value mixed The new object
	 */
	function SetOption($key,$value) {
		if(strstr($key,"bpsm_")!==0) $key="bpsm_" . $key;
		
		$this->_options[$key]=$value;
	}
	
	/**
	 * Saves the options back to the database
	 *
	 * @since 3.0
	 * @access private
	 * @author Arne Brachhold
	 * @return bool true on success
	 */
	function SaveOptions() {
		$oldvalue = get_option("bpsm_options");
		if($oldvalue == $this->_options) {
			return true;
		} else return update_option("bpsm_options",$this->_options);
	}
	
	/**
	 * Adds a url to the sitemap. You can use this method or call AddElement directly.
	 *
	 * @since 3.0
	 * @access public
	 * @author Arne Brachhold
	 * @param $loc string The location (url) of the page
	 * @param $lastMod int The last Modification time as a UNIX timestamp
	 * @param $changeFreq string The change frequenty of the page, Valid values are "always", "hourly", "daily", "weekly", "monthly", "yearly" and "never".
	 * @param $priorty float The priority of the page, between 0.0 and 1.0
	 * @see AddElement
	 * @return string The URL node
	 */
	function AddUrl($loc, $lastMod = 0, $changeFreq = "monthly", $priority = 0.5) {
		//Strip out the last modification time if activated
		if($this->GetOption('in_lastmod')===false) $lastMod = 0;
		$page = new BPGoogleSitemapGeneratorPage($loc, $priority, $changeFreq, $lastMod);
		
		$this->AddElement($page);
	}
	
	/**
	 * Adds an element to the sitemap
	 *
	 * @since 3.0
	 * @access private
	 * @author Arne Brachhold
	 * @param $page The element
	 */
	function AddElement(&$page, $force=false) {
		if(empty($page)) return;
		
		$s = $page->Render();
		
		if($this->_fileZipHandle && $this->IsGzipEnabled()) {
			gzwrite($this->_fileZipHandle,$s);
		}
		
		//force = if sitemapindex as we want xml for that.
		if(($this->_fileHandle && $this->GetOption("b_xml")) || ($this->_fileHandle && $force)) {
			fwrite($this->_fileHandle,$s);
		}
	}
	
	/**
	 * Checks if a file is writable and tries to make it if not.
	 *
	 * @since 3.05b
	 * @access private
	 * @author  VJTD3 <http://www.VJTD3.com>
	 * @return bool true if writable
	 */
	function IsFileWritable($filename) {
		//can we write?
		if(!is_writable($filename)) {
			//no we can't.
			if(!@chmod($filename, 0666)) {
				$pathtofilename = dirname($filename);
				//Lets check if parent directory is writable.
				if(!is_writable($pathtofilename)) {
					//it's not writeable too.
					if(!@chmod($pathtoffilename, 0666)) {
						//darn couldn't fix up parrent directory this hosting is foobar.
						//Lets error because of the permissions problems.
						return false;
					}
				}
			}
		}
		//we can write, return 1/true/happy dance.
		return true;
	}
	
	/**
	 * Builds the sitemap and writes it into a xml file.
	 * 
	 * ATTENTION PLUGIN DEVELOPERS! DONT CALL THIS METHOD DIRECTLY!
	 * The method is probably not available, since it is only loaded when needed.
	 * Use do_action("bpsm_rebuild"); if you want to rebuild the sitemap.
	 * Please refer to the documentation.txt for more details.
	 *
	 * @since 3.0
	 * @access public
	 * @author Arne Brachhold <himself [at] arnebrachhold [dot] de>
	 * @return array An array with messages such as failed writes etc.
	 */
	function BuildSitemap() {
		global $wpdb, $bp, $wp_version;
		$this->Initate();
		
		if($this->GetOption("b_memory")!='') {
			@ini_set("memory_limit",$this->GetOption("b_memory"));
		}
		
		if($this->GetOption("b_time")!=-1) {
			@set_time_limit($this->GetOption("b_time"));
		}
		
		//This object saves the status information of the script directly to the database
		$status = new BPGoogleSitemapGeneratorStatus();
		
		//Other plugins can detect if the building process is active
		$this->_isActive = true;
				
		//Debug mode?
		$debug=$this->GetOption("b_debug");
		
		$styleSheet = ($this->GetDefaultStyle() && $this->GetOption('b_style_default')===true?$this->GetDefaultStyle():$this->GetOption('b_style'));


		$xmlfiles = array();
		$zipfiles = array();

		//Activity
		//how should we handle activity?
		//we should dump all sitewide only activity to the permalink url - even if activity_comment, new_blog_post, new_blog_comment, new_forum_topic, new_forum_post
		if ( bp_is_active( 'activity' ) ) {

			//create our childmap
			if($this->GetOption("b_xml")) {
				$fileName = $this->GetXmlPath();
				$fileName = str_replace(".xml", "-activity.xml", $fileName);
				
				$childUrl = $this->GetXmlUrl();
				$childUrl = str_replace(".xml", "-activity.xml", $childUrl);
				$xmlfiles[] = $childUrl;
				
				$status->StartChildMapXml('activity',$fileName,$childUrl);
				
				if($this->IsFileWritable($fileName)) {
					$this->_fileHandle = fopen($fileName,"w");
					if(!$this->_fileHandle) $status->EndChildMapXml('activity',false,"Not openable");					
				} else $status->EndChildMapXml('activity',false,"not writable");
			}
			
			//Write gzipped sitemap file
			if($this->IsGzipEnabled()) {
				$fileName = $this->GetZipPath();
				$fileName = str_replace(".xml.gz", "-activity.xml.gz", $fileName);
				
				$childUrl = $this->GetZipUrl();
				$childUrl = str_replace(".xml.gz", "-activity.xml.gz", $childUrl);
				$zipfiles[] = $childUrl;
				
				$status->StartChildMapZip('activity',$fileName,$childUrl);
				
				if($this->IsFileWritable($fileName)) {
					$this->_fileZipHandle = gzopen($fileName,"w1");
					if(!$this->_fileZipHandle) $status->EndChildMapZip('activity',false,"Not openable");
				} else $status->EndChildMapZip('activity',false,"not writable");
			}
			
			if(!$this->_fileHandle && !$this->_fileZipHandle) {
				$status->End();
				return;
			}
			
			
			//Content of the XML file
			$this->AddElement(new BPGoogleSitemapGeneratorXmlEntry('<?xml version="1.0" encoding="UTF-8"' . '?' . '>'));
						
			if(!empty($styleSheet)) {
				$this->AddElement(new BPGoogleSitemapGeneratorXmlEntry('<' . '?xml-stylesheet type="text/xsl" href="' . $styleSheet . '"?' . '>'));
			}
			
			$this->AddElement(new BPGoogleSitemapGeneratorDebugEntry("generator=\"buddypress/" . BP_VERSION . "\""));
			$this->AddElement(new BPGoogleSitemapGeneratorDebugEntry("sitemap-generator-url=\"http://etiviti.com\" sitemap-generator-version=\"" . $this->GetVersion() . "\""));
			$this->AddElement(new BPGoogleSitemapGeneratorDebugEntry("generated-on=\"" . date(get_option("date_format") . " " . get_option("time_format")) . "\""));
			
			//Go XML!
			$this->AddElement(new BPGoogleSitemapGeneratorXmlEntry('<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'));


			//main activity always changes, priority is high
			$this->AddUrl($bp->root_domain . '/' . BP_ACTIVITY_SLUG . '/',$this->GetTimestampNow(),'always','1.0');

			//lets loop over the sitewide activity only - we are going direct sql as we don't want to cache or cause too much pain
			
			/* select conditions - all we care about is id and date_recorded */
			$select_sql = "SELECT a.id, a.date_recorded";
			
			/* from conditions */
			$from_sql = " FROM {$bp->activity->table_name} a";
			
			/* Where conditions */
			$where_conditions = array();
			$where_conditions['hidden_sql'] = "a.hide_sitewide = 0";
			$where_sql = ' WHERE ' . join( ' AND ', $where_conditions );

			$activities = $wpdb->get_results( $wpdb->prepare( "{$select_sql} {$from_sql} {$where_sql} ORDER BY a.date_recorded DESC LIMIT 49999" ) );
			
			//dump our activity data to a sitemap - technically an activity would 'never' change
			if ( $activities ) {
				foreach($activities as $act) {
					if($debug) $this->AddElement(new BPGoogleSitemapGeneratorDebugEntry("activity-id:" . $act->id));

					$this->AddUrl($bp->root_domain . '/' . BP_ACTIVITY_SLUG . '/p/' . $act->id . '/',$this->GetTimestampFromMySql($act->date_recorded),'never','0.5');
				}
				
				$status->SaveChildMapStep('activity',count($activities));
			}
			
			$activities = null;
			$select_sql = null;
			$from_sql = null;
			$where_conditions = null;
			$where_sql = null;
	
	
			$this->AddElement(new BPGoogleSitemapGeneratorXmlEntry("</urlset>"));
			
			if($this->GetOption("b_xml")) {
				if($this->_fileHandle && fclose($this->_fileHandle)) {
					$this->_fileHandle = null;
					$status->EndChildMapXml('activity',true);
				} else $status->EndChildMapXml('activity',false,"Could not close the sitemap file.");
			}
			
			if($this->IsGzipEnabled()) {
				if($this->_fileZipHandle && fclose($this->_fileZipHandle)) {
					$this->_fileZipHandle = null;
					$status->EndChildMapZip('activity',true);
				} else $status->EndChildMapZip('activity',false,"Could not close the zipped sitemap file");
			}
			
		} //end Activity


		//Members

		//create our childmap
		if($this->GetOption("b_xml")) {
			$fileName = $this->GetXmlPath();
			$fileName = str_replace(".xml", "-members.xml", $fileName);

			$childUrl = $this->GetXmlUrl();
			$childUrl = str_replace(".xml", "-members.xml", $childUrl);
			$xmlfiles[] = $childUrl;
				
			$status->StartChildMapXml('members',$fileName,$childUrl);
			
			if($this->IsFileWritable($fileName)) {
				$this->_fileHandle = fopen($fileName,"w");
				if(!$this->_fileHandle) $status->EndChildMapXml('members',false,"Not openable");					
			} else $status->EndChildMapXml('members',false,"not writable");
		}
		
		//Write gzipped sitemap file
		if($this->IsGzipEnabled()) {
			$fileName = $this->GetZipPath();
			$fileName = str_replace(".xml.gz", "-members.xml.gz", $fileName);
			
			$childUrl = $this->GetZipUrl();
			$childUrl = str_replace(".xml.gz", "-members.xml.gz", $childUrl);
			$zipfiles[] = $childUrl;
				
			$status->StartChildMapZip('members',$fileName,$childUrl);
			
			if($this->IsFileWritable($fileName)) {
				$this->_fileZipHandle = gzopen($fileName,"w1");
				if(!$this->_fileZipHandle) $status->EndChildMapZip('members',false,"Not openable");
			} else $status->EndChildMapZip('members',false,"not writable");
		}
		
		if(!$this->_fileHandle && !$this->_fileZipHandle) {
			$status->End();
			return;
		}
		
		
		//Content of the XML file
		$this->AddElement(new BPGoogleSitemapGeneratorXmlEntry('<?xml version="1.0" encoding="UTF-8"' . '?' . '>'));
		
		if(!empty($styleSheet)) {
			$this->AddElement(new BPGoogleSitemapGeneratorXmlEntry('<' . '?xml-stylesheet type="text/xsl" href="' . $styleSheet . '"?' . '>'));
		}
		
		$this->AddElement(new BPGoogleSitemapGeneratorDebugEntry("generator=\"buddypress/" . BP_VERSION . "\""));
		$this->AddElement(new BPGoogleSitemapGeneratorDebugEntry("sitemap-generator-url=\"http://etiviti.com\" sitemap-generator-version=\"" . $this->GetVersion() . "\""));
		$this->AddElement(new BPGoogleSitemapGeneratorDebugEntry("generated-on=\"" . date(get_option("date_format") . " " . get_option("time_format")) . "\""));
		
		//Go XML!
		$this->AddElement(new BPGoogleSitemapGeneratorXmlEntry('<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'));


		//main members always changes, priority is high
		$this->AddUrl($bp->root_domain . '/' . BP_MEMBERS_SLUG . '/',$this->GetTimestampNow(),'daily','0.9');

		/* select conditions - all we care about is id, nicename, login */
		$select_sql = "SELECT DISTINCT u.ID as id, u.user_nicename, u.user_login";
		
		/* from conditions */
		$from_sql = " FROM " . CUSTOM_USER_TABLE . " u";

		/* Where conditions */
		$where_conditions = array();
		$where_conditions['status_sql'] = bp_core_get_status_sql( 'u.' );
		$where_sql = ' WHERE ' . join( ' AND ', $where_conditions );

		$members = $wpdb->get_results( $wpdb->prepare( "{$select_sql} {$from_sql} {$where_sql} ORDER BY u.user_registered DESC LIMIT 49999" ) );

		//dump our member data to a sitemap
		if ( $members ) {
			foreach($members as $mem) {
				if($debug) $this->AddElement(new BPGoogleSitemapGeneratorDebugEntry("member-id:" . $mem->id));

				if ( defined( 'BP_ENABLE_USERNAME_COMPATIBILITY_MODE' ) )
					$username = $mem->user_login;
				else
					$username = $mem->user_nicename;
					
				/* If we are using a members slug, include it. */
				if ( !defined( 'BP_ENABLE_ROOT_PROFILES' ) )
					$domain = $bp->root_domain . '/' . BP_MEMBERS_SLUG . '/' . $username . '/';
				else
					$domain = $bp->root_domain . '/' . $username . '/';

				$mem->url = $domain;

				if ($this->GetOption('b_last_activity')) {
					$activity = get_usermeta( $mem->id, 'last_activity' );
					if (!$activity || '' == $activity || is_numeric($activity) ) {
						$activity = 0;
					} else {
						$activity = $this->GetTimestampFromMySql($activity);
					}
				}

				$this->AddUrl($mem->url,$activity,'daily','0.7');
			}
			$status->SaveChildMapStep('members',count($members));
		}
		
		$select_sql = null;
		$from_sql = null;
		$where_conditions = null;
		$where_sql = null;

		$this->AddElement(new BPGoogleSitemapGeneratorXmlEntry("</urlset>"));
		
		if($this->GetOption("b_xml")) {
			if($this->_fileHandle && fclose($this->_fileHandle)) {
				$this->_fileHandle = null;
				$status->EndChildMapXml('members',true);
			} else $status->EndChildMapXml('members',false,"Could not close the sitemap file.");
		}
		
		if($this->IsGzipEnabled()) {
			if($this->_fileZipHandle && fclose($this->_fileZipHandle)) {
				$this->_fileZipHandle = null;
				$status->EndChildMapZip('members',true);
			} else $status->EndChildMapZip('members',false,"Could not close the zipped sitemap file");
		}


		//dump our member data and groups
		if ( $members && bp_is_active( 'groups' )) {
		
			//create our childmap
			if($this->GetOption("b_xml")) {
				$fileName = $this->GetXmlPath();
				$fileName = str_replace(".xml", "-members-g.xml", $fileName);

				$childUrl = $this->GetXmlUrl();
				$childUrl = str_replace(".xml", "-members-g.xml", $childUrl);
				$xmlfiles[] = $childUrl;
					
				$status->StartChildMapXml('members-groups',$fileName,$childUrl);
				
				if($this->IsFileWritable($fileName)) {
					$this->_fileHandle = fopen($fileName,"w");
					if(!$this->_fileHandle) $status->EndChildMapXml('members-groups',false,"Not openable");					
				} else $status->EndChildMapXml('members-groups',false,"not writable");
			}
			
			//Write gzipped sitemap file
			if($this->IsGzipEnabled()) {
				$fileName = $this->GetZipPath();
				$fileName = str_replace(".xml.gz", "-members-g.xml.gz", $fileName);
				
				$childUrl = $this->GetZipUrl();
				$childUrl = str_replace(".xml.gz", "-members-g.xml.gz", $childUrl);
				$zipfiles[] = $childUrl;
					
				$status->StartChildMapZip('members-groups',$fileName,$childUrl);
				
				if($this->IsFileWritable($fileName)) {
					$this->_fileZipHandle = gzopen($fileName,"w1");
					if(!$this->_fileZipHandle) $status->EndChildMapZip('members-groups',false,"Not openable");
				} else $status->EndChildMapZip('members-groups',false,"not writable");
			}
			
			if(!$this->_fileHandle && !$this->_fileZipHandle) {
				$status->End();
				return;
			}
			
			
			//Content of the XML file
			$this->AddElement(new BPGoogleSitemapGeneratorXmlEntry('<?xml version="1.0" encoding="UTF-8"' . '?' . '>'));
			
			if(!empty($styleSheet)) {
				$this->AddElement(new BPGoogleSitemapGeneratorXmlEntry('<' . '?xml-stylesheet type="text/xsl" href="' . $styleSheet . '"?' . '>'));
			}
			
			$this->AddElement(new BPGoogleSitemapGeneratorDebugEntry("generator=\"buddypress/" . BP_VERSION . "\""));
			$this->AddElement(new BPGoogleSitemapGeneratorDebugEntry("sitemap-generator-url=\"http://etiviti.com\" sitemap-generator-version=\"" . $this->GetVersion() . "\""));
			$this->AddElement(new BPGoogleSitemapGeneratorDebugEntry("generated-on=\"" . date(get_option("date_format") . " " . get_option("time_format")) . "\""));
			
			//Go XML!
			$this->AddElement(new BPGoogleSitemapGeneratorXmlEntry('<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'));

			//$_i = 0;
			foreach($members as $mem) {
				if($debug) $this->AddElement(new BPGoogleSitemapGeneratorDebugEntry("member-id:" . $mem->id));

				//we should make sure we have at least one group for this member in the first place
				if ($this->GetOption('b_members_onlyif_groups')) {
					$hasgrp = $wpdb->get_row( $wpdb->prepare( "SELECT date_modified FROM {$bp->groups->table_name_members} WHERE user_id = %d AND is_confirmed = 1 AND is_banned = 0 ORDER BY date_modified DESC", $mem->id ) );

					if ($hasgrp) {
						//we need to generate each member nav points
						$this->AddUrl($mem->url . BP_GROUPS_SLUG .'/',$this->GetTimestampFromMySql($hasgrp->date_modified),'monthly','0.6');
						//$_i++;
					}
				} else {
					$this->AddUrl($mem->url . BP_GROUPS_SLUG .'/',0,'monthly','0.6');
					//$_i++;
				}
			}
			
			//$status->SaveChildMapStep('members-groups',$_i);
			
			$this->AddElement(new BPGoogleSitemapGeneratorXmlEntry("</urlset>"));
			
			if($this->GetOption("b_xml")) {
				if($this->_fileHandle && fclose($this->_fileHandle)) {
					$this->_fileHandle = null;
					$status->EndChildMapXml('members-groups',true);
				} else $status->EndChildMapXml('members-groups',false,"Could not close the sitemap file.");
			}
			
			if($this->IsGzipEnabled()) {
				if($this->_fileZipHandle && fclose($this->_fileZipHandle)) {
					$this->_fileZipHandle = null;
					$status->EndChildMapZip('members-groups',true);
				} else $status->EndChildMapZip('members-groups',false,"Could not close the zipped sitemap file");
			}
		}


		//dump our member data and friends
		if ( $members && bp_is_active( 'friends' )) {
		
			//create our childmap
			if($this->GetOption("b_xml")) {
				$fileName = $this->GetXmlPath();
				$fileName = str_replace(".xml", "-members-f.xml", $fileName);

				$childUrl = $this->GetXmlUrl();
				$childUrl = str_replace(".xml", "-members-f.xml", $childUrl);
				$xmlfiles[] = $childUrl;
					
				$status->StartChildMapXml('members-friends',$fileName,$childUrl);
				
				if($this->IsFileWritable($fileName)) {
					$this->_fileHandle = fopen($fileName,"w");
					if(!$this->_fileHandle) $status->EndChildMapXml('members-friends',false,"Not openable");					
				} else $status->EndChildMapXml('members-friends',false,"not writable");
			}
			
			//Write gzipped sitemap file
			if($this->IsGzipEnabled()) {
				$fileName = $this->GetZipPath();
				$fileName = str_replace(".xml.gz", "-members-f.xml.gz", $fileName);
				
				$childUrl = $this->GetZipUrl();
				$childUrl = str_replace(".xml.gz", "-members-f.xml.gz", $childUrl);
				$zipfiles[] = $childUrl;
					
				$status->StartChildMapZip('members-friends',$fileName,$childUrl);
				
				if($this->IsFileWritable($fileName)) {
					$this->_fileZipHandle = gzopen($fileName,"w1");
					if(!$this->_fileZipHandle) $status->EndChildMapZip('members-friends',false,"Not openable");
				} else $status->EndChildMapZip('members-friends',false,"not writable");
			}
			
			if(!$this->_fileHandle && !$this->_fileZipHandle) {
				$status->End();
				return;
			}
			
			
			//Content of the XML file
			$this->AddElement(new BPGoogleSitemapGeneratorXmlEntry('<?xml version="1.0" encoding="UTF-8"' . '?' . '>'));
			
			if(!empty($styleSheet)) {
				$this->AddElement(new BPGoogleSitemapGeneratorXmlEntry('<' . '?xml-stylesheet type="text/xsl" href="' . $styleSheet . '"?' . '>'));
			}
			
			$this->AddElement(new BPGoogleSitemapGeneratorDebugEntry("generator=\"buddypress/" . BP_VERSION . "\""));
			$this->AddElement(new BPGoogleSitemapGeneratorDebugEntry("sitemap-generator-url=\"http://etiviti.com\" sitemap-generator-version=\"" . $this->GetVersion() . "\""));
			$this->AddElement(new BPGoogleSitemapGeneratorDebugEntry("generated-on=\"" . date(get_option("date_format") . " " . get_option("time_format")) . "\""));
			
			//Go XML!
			$this->AddElement(new BPGoogleSitemapGeneratorXmlEntry('<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'));

			//$_i = 0;
			foreach($members as $mem) {
				if($debug) $this->AddElement(new BPGoogleSitemapGeneratorDebugEntry("member-id:" . $mem->id));

				//we should make sure we have at least one friend for this member in the first place
				if ($this->GetOption('b_members_onlyif_friends')) {
					$friend_sql = $wpdb->prepare ( " WHERE (initiator_user_id = %d OR friend_user_id = %d)", $mem->id, $mem->id );
					$hasfriends = $wpdb->get_row( $wpdb->prepare( "SELECT date_created FROM {$bp->friends->table_name} $friend_sql AND is_confirmed = 1 ORDER BY date_created DESC" ) );

					if ($hasfriends) {
						//we need to generate each member nav points
						$this->AddUrl($mem->url . BP_FRIENDS_SLUG .'/',$this->GetTimestampFromMySql($hasfriends->date_created),'monthly','0.6');
						//$_i++;
					}
				} else {
					$this->AddUrl($mem->url . BP_FRIENDS_SLUG .'/',0,'weekly','0.6');
					//$_i++;
				}
			}
			//$status->SaveChildMapStep('members-friends',$_i);
			
			$this->AddElement(new BPGoogleSitemapGeneratorXmlEntry("</urlset>"));
			
			if($this->GetOption("b_xml")) {
				if($this->_fileHandle && fclose($this->_fileHandle)) {
					$this->_fileHandle = null;
					$status->EndChildMapXml('members-friends',true);
				} else $status->EndChildMapXml('members-friends',false,"Could not close the sitemap file.");
			}
			
			if($this->IsGzipEnabled()) {
				if($this->_fileZipHandle && fclose($this->_fileZipHandle)) {
					$this->_fileZipHandle = null;
					$status->EndChildMapZip('members-friends',true);
				} else $status->EndChildMapZip('members-friends',false,"Could not close the zipped sitemap file");
			}
		}


		//dump our member data and xprofile
		if ( $members && bp_is_active( 'xprofile' )) {
		
			//create our childmap
			if($this->GetOption("b_xml")) {
				$fileName = $this->GetXmlPath();
				$fileName = str_replace(".xml", "-members-x.xml", $fileName);

				$childUrl = $this->GetXmlUrl();
				$childUrl = str_replace(".xml", "-members-x.xml", $childUrl);
				$xmlfiles[] = $childUrl;
					
				$status->StartChildMapXml('members-xprofile',$fileName,$childUrl);
				
				if($this->IsFileWritable($fileName)) {
					$this->_fileHandle = fopen($fileName,"w");
					if(!$this->_fileHandle) $status->EndChildMapXml('members-xprofile',false,"Not openable");					
				} else $status->EndChildMapXml('members-xprofile',false,"not writable");
			}
			
			//Write gzipped sitemap file
			if($this->IsGzipEnabled()) {
				$fileName = $this->GetZipPath();
				$fileName = str_replace(".xml.gz", "-members-x.xml.gz", $fileName);
				
				$childUrl = $this->GetZipUrl();
				$childUrl = str_replace(".xml.gz", "-members-x.xml.gz", $childUrl);
				$zipfiles[] = $childUrl;
					
				$status->StartChildMapZip('members-xprofile',$fileName,$childUrl);
				
				if($this->IsFileWritable($fileName)) {
					$this->_fileZipHandle = gzopen($fileName,"w1");
					if(!$this->_fileZipHandle) $status->EndChildMapZip('members-xprofile',false,"Not openable");
				} else $status->EndChildMapZip('members-xprofile',false,"not writable");
			}
			
			if(!$this->_fileHandle && !$this->_fileZipHandle) {
				$status->End();
				return;
			}
			
			
			//Content of the XML file
			$this->AddElement(new BPGoogleSitemapGeneratorXmlEntry('<?xml version="1.0" encoding="UTF-8"' . '?' . '>'));
			
			if(!empty($styleSheet)) {
				$this->AddElement(new BPGoogleSitemapGeneratorXmlEntry('<' . '?xml-stylesheet type="text/xsl" href="' . $styleSheet . '"?' . '>'));
			}
			
			$this->AddElement(new BPGoogleSitemapGeneratorDebugEntry("generator=\"buddypress/" . BP_VERSION . "\""));
			$this->AddElement(new BPGoogleSitemapGeneratorDebugEntry("sitemap-generator-url=\"http://etiviti.com\" sitemap-generator-version=\"" . $this->GetVersion() . "\""));
			$this->AddElement(new BPGoogleSitemapGeneratorDebugEntry("generated-on=\"" . date(get_option("date_format") . " " . get_option("time_format")) . "\""));
			
			//Go XML!
			$this->AddElement(new BPGoogleSitemapGeneratorXmlEntry('<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'));

			//$_i = 0;
			foreach($members as $mem) {
				if($debug) $this->AddElement(new BPGoogleSitemapGeneratorDebugEntry("member-id:" . $mem->id));

				//we should make sure we have at least one valid xprofile field for this member in the first place
				if ($this->GetOption('b_members_onlyif_xprofile')) {
					$hasxprofile = $wpdb->get_row( $wpdb->prepare( "SELECT last_updated FROM {$bp->profile->table_name_data} x, {$bp->profile->table_name_fields} f WHERE x.user_id = %d AND x.field_id = f.id", $mem->id ) );

					if ($hasxprofile) {
						//we need to generate each member nav points
						$this->AddUrl($mem->url . BP_XPROFILE_SLUG .'/',$this->GetTimestampFromMySql($hasxprofile->last_updated),'monthly','0.6');
						//$_i++;
					}
				} else {
					$this->AddUrl($mem->url . BP_XPROFILE_SLUG .'/',0,'weekly','0.7');
					//$_i++;
				}
			}
			//$status->SaveChildMapStep('members-xprofile',$_i);
			
			$this->AddElement(new BPGoogleSitemapGeneratorXmlEntry("</urlset>"));
			
			if($this->GetOption("b_xml")) {
				if($this->_fileHandle && fclose($this->_fileHandle)) {
					$this->_fileHandle = null;
					$status->EndChildMapXml('members-xprofile',true);
				} else $status->EndChildMapXml('members-xprofile',false,"Could not close the sitemap file.");
			}
			
			if($this->IsGzipEnabled()) {
				if($this->_fileZipHandle && fclose($this->_fileZipHandle)) {
					$this->_fileZipHandle = null;
					$status->EndChildMapZip('members-xprofile',true);
				} else $status->EndChildMapZip('members-xprofile',false,"Could not close the zipped sitemap file");
			}
		}





		//dump our member and subactivity data
		if ( $members && bp_is_active( 'activity' )) {
		
			$choplimit = floor($limit = 50000 / 6);
			if (count($members) > $choplimit) $members = array_slice($members,0,$choplimit);

			//create our childmap
			if($this->GetOption("b_xml")) {
				$fileName = $this->GetXmlPath();
				$fileName = str_replace(".xml", "-members-a.xml", $fileName);

				$childUrl = $this->GetXmlUrl();
				$childUrl = str_replace(".xml", "-members-a.xml", $childUrl);
				$xmlfiles[] = $childUrl;
					
				$status->StartChildMapXml('members-activity',$fileName,$childUrl);
				
				if($this->IsFileWritable($fileName)) {
					$this->_fileHandle = fopen($fileName,"w");
					if(!$this->_fileHandle) $status->EndChildMapXml('members-activity',false,"Not openable");					
				} else $status->EndChildMapXml('members-activity',false,"not writable");
			}
			
			//Write gzipped sitemap file
			if($this->IsGzipEnabled()) {
				$fileName = $this->GetZipPath();
				$fileName = str_replace(".xml.gz", "-members-a.xml.gz", $fileName);
				
				$childUrl = $this->GetZipUrl();
				$childUrl = str_replace(".xml.gz", "-members-a.xml.gz", $childUrl);
				$zipfiles[] = $childUrl;
					
				$status->StartChildMapZip('members-activity',$fileName,$childUrl);
				
				if($this->IsFileWritable($fileName)) {
					$this->_fileZipHandle = gzopen($fileName,"w1");
					if(!$this->_fileZipHandle) $status->EndChildMapZip('members-activity',false,"Not openable");
				} else $status->EndChildMapZip('members-activity',false,"not writable");
			}
			
			if(!$this->_fileHandle && !$this->_fileZipHandle) {
				$status->End();
				return;
			}
			
			
			//Content of the XML file
			$this->AddElement(new BPGoogleSitemapGeneratorXmlEntry('<?xml version="1.0" encoding="UTF-8"' . '?' . '>'));
			
			if(!empty($styleSheet)) {
				$this->AddElement(new BPGoogleSitemapGeneratorXmlEntry('<' . '?xml-stylesheet type="text/xsl" href="' . $styleSheet . '"?' . '>'));
			}
			
			$this->AddElement(new BPGoogleSitemapGeneratorDebugEntry("generator=\"buddypress/" . BP_VERSION . "\""));
			$this->AddElement(new BPGoogleSitemapGeneratorDebugEntry("sitemap-generator-url=\"http://etiviti.com\" sitemap-generator-version=\"" . $this->GetVersion() . "\""));
			$this->AddElement(new BPGoogleSitemapGeneratorDebugEntry("generated-on=\"" . date(get_option("date_format") . " " . get_option("time_format")) . "\""));
			
			//Go XML!
			$this->AddElement(new BPGoogleSitemapGeneratorXmlEntry('<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'));

			//$_i = 0;
			foreach($members as $mem) {
				if($debug) $this->AddElement(new BPGoogleSitemapGeneratorDebugEntry("member-id:" . $mem->id));

				//we need to generate each member nav points
				$this->AddUrl($mem->url . BP_ACTIVITY_SLUG . '/',0,'daily','0.6');
				$this->AddUrl($mem->url . BP_ACTIVITY_SLUG .'/just-me/',0,'daily','0.6');
				$this->AddUrl($mem->url . BP_ACTIVITY_SLUG .'/favorites/',0,'daily','0.6');
				$this->AddUrl($mem->url . BP_ACTIVITY_SLUG .'/mentions/',0,'daily','0.6');
				//$_i = $_i + 4;
				if ( bp_is_active( 'friends' ) ) {
					$this->AddUrl($mem->url . BP_ACTIVITY_SLUG .'/'. BP_FRIENDS_SLUG .'/',0,'daily','0.5');
					//$_i++;
				}
				if ( bp_is_active( 'groups' ) ) {
					$this->AddUrl($mem->url . BP_ACTIVITY_SLUG .'/'. BP_GROUPS_SLUG .'/',0,'daily','0.5');
					//$_i++;
				}
			}
			//$status->SaveChildMapStep('members-activity',$_i);
			
			$this->AddElement(new BPGoogleSitemapGeneratorXmlEntry("</urlset>"));
			
			if($this->GetOption("b_xml")) {
				if($this->_fileHandle && fclose($this->_fileHandle)) {
					$this->_fileHandle = null;
					$status->EndChildMapXml('members-activity',true);
				} else $status->EndChildMapXml('members-activity',false,"Could not close the sitemap file.");
			}
			
			if($this->IsGzipEnabled()) {
				if($this->_fileZipHandle && fclose($this->_fileZipHandle)) {
					$this->_fileZipHandle = null;
					$status->EndChildMapZip('members-activity',true);
				} else $status->EndChildMapZip('members-activity',false,"Could not close the zipped sitemap file");
			}
			
		}

		$members = null;
		//end Members


		//Groups & Forums
		if ( bp_is_active( 'groups' ) ) {

			//create our childmap
			if($this->GetOption("b_xml")) {
				$fileName = $this->GetXmlPath();
				$fileName = str_replace(".xml", "-groups.xml", $fileName);

				$childUrl = $this->GetXmlUrl();
				$childUrl = str_replace(".xml", "-groups.xml", $childUrl);
				$xmlfiles[] = $childUrl;
				
				$status->StartChildMapXml('groups',$fileName,$childUrl);
				
				if($this->IsFileWritable($fileName)) {
					$this->_fileHandle = fopen($fileName,"w");
					if(!$this->_fileHandle) $status->EndChildMapXml('groups',false,"Not openable");					
				} else $status->EndChildMapXml('groups',false,"not writable");
			}
			
			//Write gzipped sitemap file
			if($this->IsGzipEnabled()) {
				$fileName = $this->GetZipPath();
				$fileName = str_replace(".xml.gz", "-groups.xml.gz", $fileName);
					
				$childUrl = $this->GetZipUrl();
				$childUrl = str_replace(".xml.gz", "-groups.xml.gz", $childUrl);
				$zipfiles[] = $childUrl;
			
				$status->StartChildMapZip('groups',$fileName,$childUrl);
				
				if($this->IsFileWritable($fileName)) {
					$this->_fileZipHandle = gzopen($fileName,"w1");
					if(!$this->_fileZipHandle) $status->EndChildMapZip('groups',false,"Not openable");
				} else $status->EndChildMapZip('groups',false,"not writable");
			}
			
			if(!$this->_fileHandle && !$this->_fileZipHandle) {
				$status->End();
				return;
			}
			
			
			//Content of the XML file
			$this->AddElement(new BPGoogleSitemapGeneratorXmlEntry('<?xml version="1.0" encoding="UTF-8"' . '?' . '>'));
						
			if(!empty($styleSheet)) {
				$this->AddElement(new BPGoogleSitemapGeneratorXmlEntry('<' . '?xml-stylesheet type="text/xsl" href="' . $styleSheet . '"?' . '>'));
			}
			
			$this->AddElement(new BPGoogleSitemapGeneratorDebugEntry("generator=\"buddypress/" . BP_VERSION . "\""));
			$this->AddElement(new BPGoogleSitemapGeneratorDebugEntry("sitemap-generator-url=\"http://etiviti.com\" sitemap-generator-version=\"" . $this->GetVersion() . "\""));
			$this->AddElement(new BPGoogleSitemapGeneratorDebugEntry("generated-on=\"" . date(get_option("date_format") . " " . get_option("time_format")) . "\""));
			
			//Go XML!
			$this->AddElement(new BPGoogleSitemapGeneratorXmlEntry('<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'));


			//main groups always changes, priority is high
			$this->AddUrl($bp->root_domain . '/' . BP_GROUPS_SLUG . '/',$this->GetTimestampNow(),'daily','0.9');
			
			/* select conditions - all we care about is id, slug, status, forum */
			$select_sql = "SELECT DISTINCT g.id, g.slug, g.status, g.enable_forum";
			
			/* from conditions */
			$from_sql = " FROM {$bp->groups->table_name} g";

			/* Where conditions */
			$where_conditions = array();
			$where_conditions['status_sql'] = "g.status != 'hidden'";
			$where_sql = ' WHERE ' . join( ' AND ', $where_conditions );

			$groups = $wpdb->get_results( $wpdb->prepare( "{$select_sql} {$from_sql} {$where_sql} ORDER BY g.date_created DESC LIMIT 1000" ) );
			
			
			//dump our group data to a sitemap
			if ( $groups ) {
				//$_i = 0;
				foreach($groups as $grp) {
					if($debug) $this->AddElement(new BPGoogleSitemapGeneratorDebugEntry("group-id:" . $grp->id));

					if ($this->GetOption('b_last_activity')) {
						$activity = $wpdb->get_var( $wpdb->prepare("SELECT meta_value FROM " . $bp->groups->table_name_groupmeta . " WHERE group_id = %d AND meta_key = %s", $grp->id, 'last_activity') );
						if (!$activity || '' == $activity || is_numeric($activity) ) {
							$activity = 0;
						} else {
							$activity = $this->GetTimestampFromMySql($activity);
						}
					}

					$this->AddUrl($bp->root_domain . '/' . BP_GROUPS_SLUG . '/' . $grp->slug . '/',$activity,'daily','0.8');
					$this->AddUrl($bp->root_domain . '/' . BP_GROUPS_SLUG . '/' . $grp->slug . '/members/',0,'weekly','0.6');
					//$_i = $_i + 2;
					//needs to be public and forum enabled (internal and overall)
					if ($grp->status == 'public' && $grp->enable_forum == 1 && bp_is_active( 'forums' )) {
						$this->AddUrl($bp->root_domain . '/' . BP_GROUPS_SLUG . '/' . $grp->slug . '/forum/',0,'daily','0.7');
						//$_i++;
					}
				}
				//$status->SaveChildMapStep('groups',$_i);
			}

			$select_sql = null;
			$from_sql = null;
			$where_conditions = null;
			$where_sql = null;

			
			$this->AddElement(new BPGoogleSitemapGeneratorXmlEntry("</urlset>"));
			
			if($this->GetOption("b_xml")) {
				if($this->_fileHandle && fclose($this->_fileHandle)) {
					$this->_fileHandle = null;
					$status->EndChildMapXml('groups',true);
				} else $status->EndChildMapXml('groups',false,"Could not close the sitemap file.");
			}
			
			if($this->IsGzipEnabled()) {
				if($this->_fileZipHandle && fclose($this->_fileZipHandle)) {
					$this->_fileZipHandle = null;
					$status->EndChildMapZip('groups',true);
				} else $status->EndChildMapZip('groups',false,"Could not close the zipped sitemap file");
			}
			

			if ( bp_is_active( 'forums' ) ) {
				if ( function_exists( 'bp_forums_is_installed_correctly' ) && bp_forums_is_installed_correctly() ) {

					//create our childmap
					if($this->GetOption("b_xml")) {
						$fileName = $this->GetXmlPath();
						$fileName = str_replace(".xml", "-groups-f.xml", $fileName);

						$childUrl = $this->GetXmlUrl();
						$childUrl = str_replace(".xml", "-groups-f.xml", $childUrl);
						$xmlfiles[] = $childUrl;
						
						$status->StartChildMapXml('groups-forums',$fileName,$childUrl);
						
						if($this->IsFileWritable($fileName)) {
							$this->_fileHandle = fopen($fileName,"w");
							if(!$this->_fileHandle) $status->EndChildMapXml('groups-forums',false,"Not openable");					
						} else $status->EndChildMapXml('groups-forums',false,"not writable");
					}
					
					//Write gzipped sitemap file
					if($this->IsGzipEnabled()) {
						$fileName = $this->GetZipPath();
						$fileName = str_replace(".xml.gz", "-groups-f.xml.gz", $fileName);
							
						$childUrl = $this->GetZipUrl();
						$childUrl = str_replace(".xml.gz", "-groups-f.xml.gz", $childUrl);
						$zipfiles[] = $childUrl;
					
						$status->StartChildMapZip('groups-forums',$fileName,$childUrl);
						
						if($this->IsFileWritable($fileName)) {
							$this->_fileZipHandle = gzopen($fileName,"w1");
							if(!$this->_fileZipHandle) $status->EndChildMapZip('groups-forums',false,"Not openable");
						} else $status->EndChildMapZip('groups-forums',false,"not writable");
					}
					
					if(!$this->_fileHandle && !$this->_fileZipHandle) {
						$status->End();
						return;
					}

					//Content of the XML file
					$this->AddElement(new BPGoogleSitemapGeneratorXmlEntry('<?xml version="1.0" encoding="UTF-8"' . '?' . '>'));
								
					if(!empty($styleSheet)) {
						$this->AddElement(new BPGoogleSitemapGeneratorXmlEntry('<' . '?xml-stylesheet type="text/xsl" href="' . $styleSheet . '"?' . '>'));
					}
					
					$this->AddElement(new BPGoogleSitemapGeneratorDebugEntry("generator=\"buddypress/" . BP_VERSION . "\""));
					$this->AddElement(new BPGoogleSitemapGeneratorDebugEntry("sitemap-generator-url=\"http://etiviti.com\" sitemap-generator-version=\"" . $this->GetVersion() . "\""));
					$this->AddElement(new BPGoogleSitemapGeneratorDebugEntry("generated-on=\"" . date(get_option("date_format") . " " . get_option("time_format")) . "\""));
					
					//Go XML!
					$this->AddElement(new BPGoogleSitemapGeneratorXmlEntry('<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'));



					//main forums always changes, priority is high
					if  (!(int) bp_get_option( 'bp-disable-forum-directory' ) )  $this->AddUrl($bp->root_domain . '/' . BP_FORUMS_SLUG . '/',$this->GetTimestampNow(),'daily','0.9');
				
				
					//dump our group data to a sitemap
					if ( $groups ) {
					
						$bb = new stdClass();
						require_once( $bp->forums->bbconfig );

						// Setup the global database connection
						$bbdb = new BPDB ( BBDB_USER, BBDB_PASSWORD, BBDB_NAME, BBDB_HOST );
					
						$l = count($groups);
						if ($l == 1) {
							$limit = '49999';
						} else {
							$limit = floor($limit = 50000 / $l);
						}
					
						//$_i = 0;
						
						foreach($groups as $grp) {
							if($debug) $this->AddElement(new BPGoogleSitemapGeneratorDebugEntry("group-id:" . $grp->id));

							$fid = $wpdb->get_var( $wpdb->prepare("SELECT meta_value FROM " . $bp->groups->table_name_groupmeta . " WHERE group_id = %d AND meta_key = %s", $grp->id, 'forum_id') );

							/* If it turns out there is no forum for this group, return false so we don't fetch all global topics */
							if ( $fid ) {

								/* select conditions - all we care about is id, slug, status */
								$select_sql = "SELECT DISTINCT t.topic_id, t.topic_slug, t.topic_time";
								
								/* from conditions */
								$from_sql = " FROM {$bb_table_prefix}topics t";

								/* Where conditions */
								$where_conditions = array();
								$where_conditions['status_sql'] = "t.topic_status != '0'";
								$where_conditions['status_sql'] = "t.forum_id = ". $fid;
								$where_sql = ' WHERE ' . join( ' AND ', $where_conditions );

								$topics = $wpdb->get_results( $wpdb->prepare( "{$select_sql} {$from_sql} {$where_sql} ORDER BY t.topic_time DESC LIMIT {$limit}" ) );

								if ( $topics ) {
									foreach($topics as $topic) {
										if($debug) $this->AddElement(new BPGoogleSitemapGeneratorDebugEntry("topic-id:" . $topic->topic_id));
										
										$this->AddUrl($bp->root_domain . '/' . BP_GROUPS_SLUG . '/' . $grp->slug . '/forum/topic/'. $topic->topic_slug .'/',$this->GetTimestampFromMySql($topic->topic_time),'daily','0.6');
										//$_i++;
									}
								}

							}
						}
						//$status->SaveChildMapStep('groups-forums',$_i);
					}
				
				
				
					$this->AddElement(new BPGoogleSitemapGeneratorXmlEntry("</urlset>"));
					
					if($this->GetOption("b_xml")) {
						if($this->_fileHandle && fclose($this->_fileHandle)) {
							$this->_fileHandle = null;
							$status->EndChildMapXml('groups-forums',true);
						} else $status->EndChildMapXml('groups-forums',false,"Could not close the sitemap file.");
					}
					
					if($this->IsGzipEnabled()) {
						if($this->_fileZipHandle && fclose($this->_fileZipHandle)) {
							$this->_fileZipHandle = null;
							$status->EndChildMapZip('groups-forums',true);
						} else $status->EndChildMapZip('groups-forums',false,"Could not close the zipped sitemap file");
					}
				}
			}			
			
			$groups = null;
			
		} //end Groups




		//Include plug-in slugs?
		//filter with arrary of slugs



		//Build our sitemapindex
		$fileName = $this->GetXmlPath();
		$status->StartIndexXml($this->GetXmlPath(),$this->GetXmlUrl());
			
		if($this->IsFileWritable($fileName)) {
				
			$this->_fileHandle = fopen($fileName,"w");
			if(!$this->_fileHandle) $status->EndIndexXml(false,"Not openable");
				
		} else $status->EndIndexXml(false,"not writable");
		
		if(!$this->_fileHandle) {
			$status->End();
			return;
		}
		
		
		if($this->IsGzipEnabled()) {
			$files = $zipfiles;
		} else {
			$files = $xmlfiles;
		}
		//Content of the XML file
		$this->AddElement(new BPGoogleSitemapGeneratorXmlEntry('<?xml version="1.0" encoding="UTF-8"' . '?' . '>'),true);
		$this->AddElement(new BPGoogleSitemapGeneratorXmlEntry('<sitemapindex xmlns="http://www.google.com/schemas/sitemap/0.84" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.google.com/schemas/sitemap/0.84 http://www.google.com/schemas/sitemap/0.84/siteindex.xsd">'),true);
		foreach ($files as $fileloc) {
			$this->AddElement(new BPGoogleSitemapGeneratorIndexEntry($fileloc,date("Y-m-d\TH:i:s").'+00:00'),true);
		}
		$this->AddElement(new BPGoogleSitemapGeneratorXmlEntry("</sitemapindex>"),true);

		
		$pingUrl='';
		
		if($this->_fileHandle && fclose($this->_fileHandle)) {
			$this->_fileHandle = null;
			$status->EndIndexXml(true);
			$pingUrl=$this->GetXmlUrl();
		} else $status->EndIndexXml(false,"Could not close the sitemap file.");
	

		//Pings
		
		//Ping Google
		if($this->GetOption("b_ping") && !empty($pingUrl)) {
			$sPingUrl="http://www.google.com/webmasters/sitemaps/ping?sitemap=" . urlencode($pingUrl);
			$status->StartChildPing('google',$sPingUrl);
			$pingres=$this->RemoteOpen($sPingUrl);
									  
			if($pingres==NULL || $pingres===false) {
				$status->EndChildPing('google',false,$this->_lastError);
				trigger_error("Failed to ping Google: " . htmlspecialchars(strip_tags($pingres)),E_USER_NOTICE);
			} else {
				$status->EndChildPing('google',true);
			}
		}
				
		//Ping Ask.com
		if($this->GetOption("b_pingask") && !empty($pingUrl)) {
			$sPingUrl="http://submissions.ask.com/ping?sitemap=" . urlencode($pingUrl);
			$status->StartChildPing('ask',$sPingUrl);
			$pingres=$this->RemoteOpen($sPingUrl);
									  
			if($pingres==NULL || $pingres===false || strpos($pingres,"successfully received and added")===false) { //Ask.com returns 200 OK even if there was an error, so we need to check the content.
				$status->EndChildPing('ask',false,$this->_lastError);
				trigger_error("Failed to ping Ask.com: " . htmlspecialchars(strip_tags($pingres)),E_USER_NOTICE);
			} else {
				$status->EndChildPing('ask',true);
			}
		}
		
		//Ping YAHOO
		if($this->GetOption("b_pingyahoo")===true && $this->GetOption("b_yahookey")!="" && !empty($pingUrl)) {
			$sPingUrl="http://search.yahooapis.com/SiteExplorerService/V1/updateNotification?appid=" . $this->GetOption("b_yahookey") . "&url=" . urlencode($pingUrl);
			$status->StartChildPing('yahoo',$sPingUrl);
			$pingres=$this->RemoteOpen($sPingUrl);

			if($pingres==NULL || $pingres===false || strpos(strtolower($pingres),"success")===false) {
				trigger_error("Failed to ping YAHOO: " . htmlspecialchars(strip_tags($pingres)),E_USER_NOTICE);
				$status->EndChildPing('yahoo',false,$this->_lastError);
			} else {
				$status->EndChildPing('yahoo',true);
			}
		}
		
		//Ping Bing
		if($this->GetOption("b_pingmsn") && !empty($pingUrl)) {
			$sPingUrl="http://www.bing.com/webmaster/ping.aspx?siteMap=" . urlencode($pingUrl);
			$status->StartChildPing('bing',$sPingUrl);
			$pingres=$this->RemoteOpen($sPingUrl);
									  
			if($pingres==NULL || $pingres===false || strpos($pingres,"Thanks for submitting your sitemap")===false) {
				trigger_error("Failed to ping Bing: " . htmlspecialchars(strip_tags($pingres)),E_USER_NOTICE);
				$status->EndChildPing('bing',false,$this->_lastError);
			} else {
				$status->EndChildPing('bing',true);
			}
		}
	
		$status->End();
		
		
		$this->_isActive = false;
	
		//done...
		return $status;
	}
	
	/**
	 * Tries to ping a specific service showing as much as debug output as possible
	 * @since 3.1.9
	 * @return null
	 */
	function ShowPingResult() {
		
		check_admin_referer('bp-sitemap');
		
		if(!current_user_can("administrator")) {
			echo '<p>Please log in as admin</p>';
			return;
		}
		
		$service = !empty($_GET["bpsm_ping_service"])?$_GET["bpsm_ping_service"]:null;
		
		$status = &BPGoogleSitemapGeneratorStatus::Load();
		
		if(!$status) die("No build status yet. Build the sitemap first.");

		
		echo '<html><head><title>Ping Test</title>';
		if(function_exists('wp_admin_css')) wp_admin_css('css/global',true);
		echo '</head><body><h1>Ping Test</h1>';
				
		echo '<p>Trying to ping: <a href="' . $status->_childpings[$service]['url'] . '">' . $status->_childpings[$service]['url'] . '</a>. The sections below should give you an idea whats going on.</p>';
		
		//Try to get as much as debug / error output as possible
		$errLevel = error_reporting(E_ALL);
		$errDisplay = ini_set("display_errors",1);
		if(!defined('WP_DEBUG')) define('WP_DEBUG',true);
		
		echo '<h2>Errors, Warnings, Notices:</h2>';
		
		if(WP_DEBUG == false) echo "<i>WP_DEBUG was set to false somewhere before. You might not see all debug information until you remove this declaration!</i><br />";
		if(ini_get("display_errors")!=1) echo "<i>Your display_errors setting currently prevents the plugin from showing errors here. Please check your webserver logfile instead.</i><br />";
		
		$res = $this->RemoteOpen($status->_childpings[$service]['url']);
		
		echo '<h2>Result (text only):</h2>';

		echo wp_kses($res,array('a' => array('href' => array()),'p' => array(), 'ul' => array(), 'ol' => array(), 'li' => array()));
		
		echo '<h2>Result (HTML):</h2>';
		
		echo htmlspecialchars($res);

		//Revert back old values
		error_reporting($errLevel);
		ini_set("display_errors",$errDisplay);
		echo '</body></html>';
		exit;
	}
	
	/**
	 * Opens a remote file using the WordPress API or Snoopy
	 * @since 3.0
	 * @param $url The URL to open
	 * @param $method get or post
	 * @param $postData An array with key=>value paris
	 * @param $timeout Timeout for the request, by default 10
	 * @return mixed False on error, the body of the response on success
	 */
	function RemoteOpen($url,$method = 'get', $postData = null, $timeout = 10) {
		global $wp_version;
		
		//Before WP 2.7, wp_remote_fopen was quite crappy so Snoopy was favoured.
		if(floatval($wp_version) < 2.7) {
			if(!file_exists(ABSPATH . 'wp-includes/class-snoopy.php')) {
				trigger_error('Snoopy Web Request failed: Snoopy not found.',E_USER_NOTICE);
				return false; //Hoah?
			}
			
			require_once( ABSPATH . 'wp-includes/class-snoopy.php');
			
			$s = new Snoopy();
			
			$s->read_timeout = $timeout;
			
			if($method == 'get') {
				$s->fetch($url);
			} else {
				$s->submit($url,$postData);
			}
			
			if($s->status != "200") {
				trigger_error('Snoopy Web Request failed: Status: ' . $s->status . "; Content: " . htmlspecialchars($s->results),E_USER_NOTICE);
			}
			
			return $s->results;
	
		} else {
			
			$options = array();
			$options['timeout'] = $timeout;
			
			if($method == 'get') {
				$response = wp_remote_get( $url, $options );
			} else {
				$response = wp_remote_post($url, array_merge($options,array('body'=>$postData)));
			}
			
			if ( is_wp_error( $response ) ) {
				$errs = $response->get_error_messages();
				$errs = htmlspecialchars(implode('; ', $errs));
				trigger_error('WP HTTP API Web Request failed: ' . $errs,E_USER_NOTICE);
				return false;
			}
		
			return $response['body'];
		}
		
		return false;
	}
	
	/**
	 * Echos option fields for an select field containing the valid change frequencies
	 *
	 * @since 3.0
	 * @access private
	 * @author Arne Brachhold
	 * @param $currentVal The value which should be selected
	 * @return all valid change frequencies as html option fields
	 */
	function HtmlGetFreqNames($currentVal) {
				
		foreach($this->_freqNames AS $k=>$v) {
			echo "<option value=\"$k\" " . $this->HtmlGetSelected($k,$currentVal) .">" . $v . "</option>";
		}
	}
	
	/**
	 * Echos option fields for an select field containing the valid priorities (0- 1.0)
	 *
	 * @since 3.0
	 * @access private
	 * @author Arne Brachhold
	 * @param $currentVal string The value which should be selected
	 * @return 0.0 - 1.0 as html option fields
	 */
	function HtmlGetPriorityValues($currentVal) {
		$currentVal=(float) $currentVal;
		for($i=0.0; $i<=1.0; $i+=0.1) {
			$v = number_format($i,1,".","");
			//number_format_i18n is there since WP 2.3
			$t = function_exists('number_format_i18n')?number_format_i18n($i,1):number_format($i,1);
			echo "<option value=\"" . $v . "\" " . $this->HtmlGetSelected("$i","$currentVal") .">";
			echo $t;
			echo "</option>";
		}
	}
	
	/**
	 * Returns the checked attribute if the given values match
	 *
	 * @since 3.0
	 * @access private
	 * @author Arne Brachhold
	 * @param $val string The current value
	 * @param $equals string The value to match
	 * @return The checked attribute if the given values match, an empty string if not
	 */
	function HtmlGetChecked($val,$equals) {
		if($val==$equals) return $this->HtmlGetAttribute("checked");
		else return "";
	}
	
	/**
	 * Returns the selected attribute if the given values match
	 *
	 * @since 3.0
	 * @access private
	 * @author Arne Brachhold
	 * @param $val string The current value
	 * @param $equals string The value to match
	 * @return The selected attribute if the given values match, an empty string if not
	 */
	function HtmlGetSelected($val,$equals) {
		if($val==$equals) return $this->HtmlGetAttribute("selected");
		else return "";
	}
	
	/**
	 * Returns an formatted attribute. If the value is NULL, the name will be used.
	 *
	 * @since 3.0
	 * @access private
	 * @author Arne Brachhold
	 * @param $attr string The attribute name
	 * @param $value string The attribute value
	 * @return The formatted attribute
	 */
	function HtmlGetAttribute($attr,$value=NULL) {
		if($value==NULL) $value=$attr;
		return " " . $attr . "=\"" . $value . "\" ";
	}
	
	/**
	 * Converts a mysql datetime value into a unix timestamp
	 * 
	 * @param The value in the mysql datetime format
	 * @return int The time in seconds
	 */
	function GetTimestampFromMySql($mysqlDateTime) {
		list($date, $hours) = split(' ', $mysqlDateTime);
		list($year,$month,$day) = split('-',$date);
		list($hour,$min,$sec) = split(':',$hours);
		return mktime(intval($hour), intval($min), intval($sec), intval($month), intval($day), intval($year));
	}
	
	/**
	 * Converts a mysql datetime value into a unix timestamp
	 * 
	 * @param The value in the mysql datetime format
	 * @return int The time in seconds
	 */
	function GetTimestampNow() {
		//WTH - well i'm testing on windoze so lets cheat.
		return $this->GetTimestampFromMySql(gmdate( "Y-m-d H:i:s" ));
	}
	
	/**
	 * Returns a link pointing back to the plugin page in WordPress
	 * 
	 * @since 3.0
	 * @return string The full url
	 */
	function GetBackLink() {
		global $wp_version;
		$url = '';
		//admin_url was added in WP 2.6.0
		if(function_exists("admin_url")) $url = admin_url("options-general.php?page=" .  BPGoogleSitemapGeneratorLoader::GetBaseName());
		else $url = $_SERVER['PHP_SELF'] . "?page=" .  BPGoogleSitemapGeneratorLoader::GetBaseName();
		
		//Some browser cache the page... great! So lets add some no caching params depending on the WP and plugin version
		$url.='&bpsm_wpv=' . $wp_version . '&bpsm_pv=' . BPGoogleSitemapGeneratorLoader::GetVersion();
		
		return $url;
	}
	
	// Function from http://www.laughing-buddha.net/jon/php/sec2hms/
	function sec2hms($sec, $padHours = false) {

		// holds formatted string
		$hms = "";
		
		// there are 3600 seconds in an hour, so if we
		// divide total seconds by 3600 and throw away
		// the remainder, we've got the number of hours
		$hours = intval(intval($sec) / 3600); 

		// add to $hms, with a leading 0 if asked for
		if ($hours != 0 ) $hms .= ($padHours) ? str_pad($hours, 2, "0", STR_PAD_LEFT). ' Hours, ' : $hours. ' Hours, ';
		 
		// dividing the total seconds by 60 will give us
		// the number of minutes, but we're interested in 
		// minutes past the hour: to get that, we need to 
		// divide by 60 again and keep the remainder
		$minutes = intval(($sec / 60) % 60); 

		// then add to $hms (with a leading 0 if needed)
		if ($minutes != 0 ) $hms .= str_pad($minutes, 2, "0", STR_PAD_LEFT). ' Mins, and ';

		// seconds are simple - just divide the total
		// seconds by 60 and keep the remainder
		$seconds = intval($sec % 60); 

		// add to $hms, again with a leading 0 if needed
		$hms .= str_pad($seconds, 2, "0", STR_PAD_LEFT);

		// done!
		return $hms.' Secs';
		
	}

	
	/**
	 * Shows the option page of the plugin. Before 3.1.1, this function was basically the UI, afterwards the UI was outsourced to another class
	 * 
	 * @see BPGoogleSitemapGeneratorUI
	 * @since 3.0
	 * @return bool
	 */
	function HtmlShowOptionsPage() {
		
		$ui = $this->GetUI();
		if($ui) {
			$ui->HtmlShowOptionsPage();
			return true;
		}
		
		return false;
	}
	
	/**
	 * Includes the user interface class and intializes it
	 * 
	 * @since 3.1.1
	 * @see BPGoogleSitemapGeneratorUI
	 * @return BPGoogleSitemapGeneratorUI
	 */
	function GetUI() {

		global $wp_version;
		
		if($this->_ui === null) {
			
			$className='BPGoogleSitemapGeneratorUI';
			$fileName='bp-sitemap-ui.php';

			if(!class_exists($className)) {
				
				$path = trailingslashit(dirname(__FILE__));
				
				if(!file_exists( $path . $fileName)) return false;
				require_once($path. $fileName);
			}
	
			$this->_ui = new $className($this);
			
		}
		
		return $this->_ui;
	}
	
	function HtmlShowHelp() {
		
		
	}
}