<?php
/*
 * 	Copyright © Buhsl 1987-2009.
 * 	Author: Gennadiy Bukhmatov
 *	Created on Sep 25, 2009
 *  Adopted for WordPress
*/
class evgCaptcha{
	const EVG_CAPTCHA_CODE="evg_captcha_code";
	const EVG_CAPTCHA_HASH_CODE = "evg_captcha_orig_code";
	const SAVE_PATH = "captchaImages";
	const FONT_PATH = "evg/fonts/";
	static $buhsl_default_options = array(
			'evg_captcha_characters'=>6,
			'evg_captcha_level'=>'High',
			'evg_captcha_image_height'=>40,
			'evg_captcha_image_life_time'=>5,
			'evg_captcha_image_font'=>'timesbd.ttf',
			'evg_captcha_lable'=>'Captcha',
			'evg_captcha_comment_style'=>'font-size:24px;padding:3px; margin-top:2px; margin-right:6px; border:1px solid #e5e5e5;background:#fbfbfb;',
			'evg_captcha_container_style'=>'border: solid 1px #aaaaaa;',
			'evg_captcha_image_url'=>'images/refresh-button.png',
			'evg_captcha_image_style'=>''
	);
	static $options_name= array(
			'evg_captcha_characters'=>array('lable'=>'Captcha characters(10 max):','width'=>2),
			'evg_captcha_level'=>array('lable'=>'Background noise level:','width'=>20),
			'evg_captcha_image_height'=>array('lable'=>'Image height:','width'=>4),
			'evg_captcha_image_life_time'=>array('lable'=>'Captcha life time(min):','width'=>4),
			'evg_captcha_lable'=>array('lable'=>'Captcha lable:','width'=>100),
			'evg_captcha_image_font'=>array('lable'=>'Image font:','width'=>20),
			'evg_captcha_comment_style'=> array('lable'=> 'Comment style:','width'=>100),
			'evg_captcha_container_style'=>array('lable'=>'Container style:','width'=>100),
			'evg_captcha_image_url'=>array('lable'=>'Image:','width'=>40),
			'evg_captcha_image_style'=>array('lable'=>'Image style:','width'=>100)
	);
	static $captcha_levels = array('High'=>array('dot_div'=>3,'line_div'=>150),'Medium'=>array('dot_div'=>5,'line_div'=>200),'Low'=>array('dot_div'=>8,'line_div'=>350));
	static $possible = '123456789abcdfghjkmnprqrstvwxyz';
	static $instance = null;
	static $options=array();
	var $imgWidth=100;
	var $wordPressMultiUser = 0;
	var $url;
	var $pluginDir;
	protected function __construct(){
		$this->init($base_dir);
		$file = $this->getSavePath();
		if( !file_exists($file)){
			self::createComponentPath($file , 0755 );
		}
		$this->gc();
		
	}
	public static function setOptions($options=array()){
		self::$options=$options;
	}
	private function init(){
		// WordPress MultiUser detection
		//  0  Regular WordPress installation
		//  1  WordPress MU Forced Activated
		//  2  WordPress MU Optional Activation
		
		$this->wordPressMultiUser = 0;
		extract (pathinfo(dirname(__FILE__ )));
		$dirname= basename($dirname);
		if ($dirname == "mu-plugins"){
			// forced
			$this->wordPressMultiUser = 1;
		}
		else if ( $dirname == "buhsl-captcha" && function_exists('is_site_admin')){ 
			// optionally
			$this->wordPressMultiUser = 2;
		}
		//Load or install options
		if ($this->wordPressMultiUser == 1) {
			if( !get_site_option('buhsl_captcha') ) {
				add_site_option('buhsl_captcha', self::$buhsl_default_options, '', 'yes');
		}
		// load the options from the database
		self::setOptions(get_site_option('buhsl_captcha'));
		}
		else{
			if( !get_option('buhsl_captcha') ){
				add_option('buhsl_captcha', self::$buhsl_default_options, '', 'yes');
			}
			self::setOptions(get_option('buhsl_captcha'));
		}
		$site_uri = parse_url(get_option('home'));
		$home_uri = parse_url(get_option('siteurl'));
		$this->url  = get_option( 'home' );
		$this->pluginDir = PLUGINDIR.'/buhsl-captcha/';
		
		if ($this->wordPressMultiUser == 1){
			$this->pluginDir = MUPLUGINDIR.'/';
			if ($site_uri['host'] == $home_uri['host']) 
				$this->url = get_option('siteurl');
		}
			
		$this->url .= '/'.$this->pluginDir;
		// Set the type of request (secure or not)
		$request_type = ( isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on' ) ? 'SSL' : 'NONSSL';
		if ($request_type == 'SSL' && !preg_match("#^https#i", $this->url)) {
			$this->url = str_replace('http','https',$this->url);
		}
	}
	private function saveOptions(){
		if ($this->wordPressMultiUser == 1) {
			update_site_option('buhsl_captcha', self::$options);
		}
		else{
			update_option('buhsl_captcha', self::$options);
		}
	}
	public function getOption($key){
		$opt = self::$options[$key];
		if( !$opt ){
			$opt = self::$buhsl_default_options[$key];
			if( $opt ){
				self::$options[$key] = $opt;
				$this->saveOptions();
			}
		}
		return $opt;
	}

	private function gc(){
		$dirName = $this->getSavePath();
		$life_time = $this->getOption('evg_captcha_image_life_time');
		if( !$life_time ) $life_time = 5;
		
		if( !is_dir($dirName)) return true; 
		$dir = @opendir( $dirName  );
		while ( $file = readdir($dir)) {
			 if( !is_file( $dirName ."/".$file ) ) continue;
			 if( !preg_match("#^buhsl-cpatcha-(.*)\.(jpg)$#i", $file) ) 
				continue;
			 if( time() - filemtime($dirName."/".$file) > 60*$life_time ){
			 	@unlink ($dirName ."/".$file);
			}	
		}   		
 	}
	public static function get(){
		if( self::$instance == null ){
			self::$instance = new evgCaptcha();
		}
		return self::$instance;
	}
	protected function getSavePath(){
		return getenv('DOCUMENT_ROOT').'/'.$this->pluginDir.self::SAVE_PATH;
	}
	private function getCharactersNumber(){
		$characters = $this->getOption('evg_captcha_characters');
		if( !$characters || $characters == 0 )
			$characters =6;
		return $characters;
	}
	public function creteCuptchaCode(){
		$code = '';
		$i = 0;
		while ($i < $this->getCharactersNumber()) { 
			$code .= substr(self::$possible, mt_rand(0, strlen(self::$possible)-1), 1);
			$i++;
		}
		return $code;		
	}
	/**
	 * Crete directories path of not exists
	 * @param $path
	 * @param $mode
	 * @return unknown_type
	 */
	static function createComponentPath( $path, $mode = 0755 ){
		$pathComponent = explode("/",$path );
		$wholePath ="";
		foreach( $pathComponent as $curDir ){
			if( strlen($curDir) != 0 ){
				$wholePath .= $curDir;
				if( !file_exists($wholePath) ){
				    $ret = mkdir( $wholePath, $mode );
				}
			}
			$wholePath .= "/";
		}
		return $wholePath;
	}
	public static function getHashForCode($code){
		return md5(getenv('REMOTE_ADDR').$code);
	}
	public function getFont(){
		return $this->getFontDir().$this->getOption('evg_captcha_image_font');
	}
	public function getFontDir(){
		return getenv('DOCUMENT_ROOT').'/'.$this->pluginDir.self::FONT_PATH;
	}
	
	private function captchaImage($code) {
	  $height=$this->getOption('evg_captcha_image_height');
	  $font_size = $height * 0.4;
	  $params = array();
	  $x= mt_rand(5, 10);
	  $y= mt_rand( 5, 15);
	  $codeUp = strtoupper($code);
	  for( $i=0; $i< self::getCharactersNumber(); $i++){
		$textbox = imagettfbbox($font_size, 0, $this->getFont(), ''.$codeUp[$i]) or die('Error in imagettfbbox function');
		$param=array('textbox'=>$textbox, 'angle'=>mt_rand(0, 20));
		$param['x']=$x;
		$param['y']=$y;
		$y= mt_rand(5, 15);
		$x+= $textbox[4]-$textbox[0]+mt_rand(3, 7);
		$params[$i] =$param;
	  }
	  $width=$x+20;
	  $this->imgWidth = $width;
      $image = imagecreate($width, $height) or die('Cannot initialize new GD image stream');
      
      $background_color = imagecolorallocate($image, 255, 255, 255);
      $text_color = array( 
		imagecolorallocate($image, 20, 40, 100),
		imagecolorallocate($image, 40, 20, 100),
		imagecolorallocate($image, 100, 20, 40),
		imagecolorallocate($image, 100, 40, 20),
		imagecolorallocate($image, 40, 100, 20));
      $noise_color = imagecolorallocate($image, 100, 120, 180);

	  $dot_div = 3;
	  $line_div = 150;
	  
	  $level = self::$captcha_levels[$this->getOption('evg_captcha_level')];
	  if( isset($level) )
		extract($level);
	  
	  /* generate random dots in background */
      for( $i=0; $i<($width*$height)/$dot_div; $i++ ) {
         imagefilledellipse($image, mt_rand(0,$width), mt_rand(0,$height), 1, 1, $noise_color);
      }
      /* generate random lines in background */
      for( $i=0; $i<($width*$height)/$line_div; $i++ ) {
         imageline($image, mt_rand(0,$width), mt_rand(0,$height), mt_rand(0,$width), mt_rand(0,$height), $noise_color);
      }
      /* create textbox and add text */
	  for( $i=0; $i< self::getCharactersNumber(); $i++){
		$param = $params[$i];
		$angle = $param['angle'];
		imagettftext($image, $font_size, ($i%2==0)?$angle:-$angle, $param['x'], $height-$param['y'], $text_color[mt_rand(0,count($text_color)-1)], $this->getFont() , ''.$codeUp[$i]) or die('Error in imagettftext function');
	  }
      /* output captcha image to file */
      $file = 'buhsl-cpatcha-'.self::getHashForCode($code).'.jpg';
	  if ( !imagejpeg( $image, $this->getSavePath().'/'.$file ) )
			return new WP_Error('captha_image_file_invalid', __( 'No captcha image path' )); 
      imagedestroy($image);
	  return $this->url.self::SAVE_PATH.'/'.$file;
   }
   public function headElementsReorderScript(){
		echo '<script  type="text/javascript">
		function move_post_button (){
			var capt_code=document.getElementById("evg_captcha_id");
			if( !capt_code || !capt_code.form ){
				return false;
			}
			var frm = capt_code.form;
			var childs = frm.getElementsByTagName("input");
			var targetElement = false;
			for( var i=0; i < childs.length; i++ ){
				if( childs[i].type == "submit" ){
					targetElement = childs[i];
					break;
				}
			}
			if( targetElement ){
				var holder = targetElement.parentNode;
				if( holder != frm ){
					var clone = holder.cloneNode(true);
					holder = holder.parentNode;
					holder.removeChild(targetElement.parentNode);
					holder.appendChild(clone);
				}
				else{
					var clone = targetElement.cloneNode(true);
					holder.removeChild(targetElement);
					holder.appendChild(clone);
				}
				
			}
		}
		move_post_button();
		</script>';
   }
   private static function ajaxScript(){
	echo '
	<script  type="text/javascript">
		function updateImage(){
			var url = "'.admin_url('admin-ajax.php').'?action=evg_refresh_image";
			new Ajax.Updater("captchaInputDiv",url,
			{
				method: "get"}
				);
		}
	</script>
	';
   }
   private function getCaptchaHtml($refresh=false, $refreshLink=true){		
		$code = $this->creteCuptchaCode();
		$imgTag = $this->getImage($code );
		$str = '';
		$str .= '<input type="hidden" value="'.self::getHashForCode($code).'" name="'.self::EVG_CAPTCHA_HASH_CODE.'"/>';
		$str .= '<label>'.esc_html($this->getOption('evg_captcha_lable')).'</lable>';
		$str .='<br style="clear: both;"/>';
		$str .='<div style=" float: left; padding: 5px;';
			if ($this->getOption('evg_captcha_container_style') != '') {
				$str .= $this->getOption('evg_captcha_container_style');
			}
		$str .='" >';
		$str .= '<table cellspacing="0" cellpadding="0" style=" display: block; ';
		$str .= '"><tr>';
		$str .= '<td valign="top">';
		$str .= '<input type="text" value="" name="'.self::EVG_CAPTCHA_CODE.'" id="evg_captcha_id" tabindex="4" size="'.$this->getCharactersNumber().'"';
		if ($this->getOption('evg_captcha_comment_style') != '') {
			$str .= 'style="'.$this->getOption('evg_captcha_comment_style').'" ';
		}
		$str .= '/>';
		if( $refresh ){
			$str .= '</td><td valign="top">';
			if( $refreshLink )
				$str .= '<a href="javascript:updateImage();">';
			$str .= '<img id="evg_captcha" src="'.$this->url.$this->getOption('evg_captcha_image_url').'" alt="Refresh"';
			if ($this->getOption('evg_captcha_image_style') != '') {
				$str .= 'style="'.$this->getOption('evg_captcha_image_style').'" ';
			}
			$str .= ' height="'.$this->getOption('evg_captcha_image_height').'"/>';
			if( $refreshLink )
				$str .= '</a>';
		}
		
		$str .= '</td><td valign="top">';
		$str .= $imgTag;
		$str .= '</td></tr></table>';
		$str .='</div>';
		$str .='<br style="clear: both;"/>';
		$str .='<br/>';
		return $str;
   }
   public function ajaxUpdate(){
		header("Cache-Control: no-cache, must-revalidate");
		echo $this->getCaptchaHtml(true);
		exit(0);
   }
	public function getForm(){
		global $user_ID;
		if (isset($user_ID) && intval($user_ID) > 0 ) {
			// skip the CAPTCHA 
			return true;
		}
		self::ajaxScript();
		echo '<div id="captchaInputDiv">';
		
		echo $this->getCaptchaHtml(true);
		echo '</div>';
		$this->headElementsReorderScript();
	}
	public function nonRefreshForm(){
		return $this->getCaptchaHtml(false);
	}
	public function registrationForm(){
		echo $this->nonRefreshForm();
	}
	public function getImage($code ){
		return '<img id="evg_captcha" height="'.$this->getOption('evg_captcha_image_height').'" src="'.$this->captchaImage($code ).'"/>' ;
	}
	private function removeImageFile($postHash){
		$file = $this->getSavePath().'/buhsl-cpatcha-'.$postHash.'.jpg';
		if( !file_exists( $file ))
			return false;
		@unlink ($file);
		return true;
	}
	public function processPost($comment){
		global $user_ID;
		if (isset($user_ID) && intval($user_ID) > 0 ) {
			// skip the CAPTCHA 
			return $comment;
		}
		// Skip captcha for trackback or pingback
		if ( $comment['comment_type'] != '' && $comment['comment_type'] != 'comment' ) {
			return $comment;
		}
		$code = strtolower(trim(strip_tags($_POST[self::EVG_CAPTCHA_CODE])));
		$postHash = trim(strip_tags($_POST[self::EVG_CAPTCHA_HASH_CODE]));
		if( self::getHashForCode($code) != $postHash || !$this->removeImageFile($postHash) )
			wp_die( __('Error: You entered in the wrong Captcha phrase. Press your browsers back button and try again.', 'evg-captcha'));
			
		return $comment;
	}
	private function checkCaptchaCode(){
		$code = strtolower(trim(strip_tags($_POST[self::EVG_CAPTCHA_CODE])));
		$postHash = trim(strip_tags($_POST[self::EVG_CAPTCHA_HASH_CODE]));
		if( self::getHashForCode($code) != $postHash || !$this->removeImageFile($postHash) )
			return false;
		return true;
	}
	public function processRegistration($errors){
		if( !$this->checkCaptchaCode() )
          $errors->add(
			'captcha_wrong', 
			'<strong>'.__('ERROR', 'evg-captcha').'</strong>: '.__('You entered in the wrong Captcha phrase.', 'evg-captcha'));
		return $errors;
	}
	function wpmuSignupPost($result) {
		if ($_POST['stage'] == 'validate-user-signup') {
			$errors = $result['errors'];
			if( !$this->checkCaptchaCode() )
				$errors->add('captcha_wrong', __('<strong>ERROR</strong>: You entered in the wrong Captcha phrase.'));
		}
		return $result;
	}
	function wpmuSignupForm( $errors ) {
		$error = $errors->get_error_message('captcha_wrong');
		if( isset($error) && $error != '') {
			echo '<p class="error">' . $error . '</p>';
		}
		
		echo $this->nonRefreshForm();
	}
	function bpSignupValidate() {
		global $bp;
		if( !$this->checkCaptchaCode() )
          $bp->signup->errors['captcha_wrong'] = __('You entered in the wrong Captcha phrase.', 'evg-captcha');
		
		return;
	}

	function bpSignupForm() {
		global $bp;
		$str = $this->nonRefreshForm();
		echo '<div class="register-section" style="clear:left; margin-top:-10px; width:'.($this->getCharactersNumber()*24+$this->imgWidth+20).'px; overflow:show;">';
		$error = $bp->signup->errors['captcha_wrong'];
		if( isset($error) && $error != '') {
			echo '<div class="error">' . $error . '</div>';
		}
		echo $str;
		echo '</div>';
	}


	function addAdminMenu() {
		if ($this->wordPressMultiUser == 1 && function_exists('is_site_admin') && is_site_admin()) {
			add_submenu_page('wpmu-admin.php', __('Buhsl Captcha Options', 'buhsl-captcha'), __('Buhsl Captcha Options', 'buhsl-captcha'), 'manage_options', 'buhsl-captcha/buhsl-captcha.php',array(&$this,'options_page'));
			add_options_page( __('Buhsl Captcha', 'buhsl-captcha'), __('Buhsl Captcha', 'buhsl-captcha'), 'manage_options', 'buhsl-captcha/buhsl-captcha.php',array(&$this,'options_page'));
		}
		else if ($this->wordPressMultiUser != 1) {
			add_submenu_page('plugins.php', __('Buhsl Captcha', 'buhsl-captcha'), __('Buhsl Captcha', 'buhsl-captcha'), 'manage_options', 'buhsl-captcha/buhsl-captcha.php',array(&$this,'options_page'));
		}
	}
	
	function options_page() {
		 if (isset($_POST['submit'])) {
			if ( function_exists('current_user_can') && !current_user_can('manage_options') )
				die(__('You do not have permissions for managing this option', 'buhsl-captcha'));
			check_admin_referer( 'buhsl-captcha-options_update');
			$this->updateOptions();
		}
		else if (isset($_POST['restore_default'])){
			foreach ( self::$buhsl_default_options as $key => $value ){
				self::$options[$key] = $value;
			}
			$this->saveOptions();
		}
		$this->display();
	}
	function updateOptions(){
		foreach( self::$buhsl_default_options as $key => $value ){
			if( isset($_POST[$key]) ){
				self::$options[$key] = str_replace('&quot;','"',trim($_POST[$key]));
			}
		}
		$this->saveOptions();
	}
	function display(){
		echo "<h2>";
		echo esc_html( __('Buhsl Captcha Options', 'buhsl-captcha'));
		echo "</h2>";
			
			
		if( isset($_POST['submit']) ){
			echo '<div id="message" class="updated fade"><p><strong>';
			echo esc_html( __('Options saved.', 'buhsl-captcha'));
			echo "</strong></p></div>";
		}
		if( isset($_POST['restore_default']) ){
			echo '<div id="message" class="updated fade"><p><strong>';
			echo esc_html( __('Options restored to default.', 'buhsl-captcha'));
			echo "</strong></p></div>";
		}
		echo '<a href="http://buhsl.com/wp-plugins/buhsl-captcha/">Plugin main page.</a>';
		echo "<form name='formoptions' action='";
		if ( $this->wordPressMultiUser == 1 )
			echo admin_url( 'wpmu-admin.php?page=buhsl-captcha.php' );
		else
			echo admin_url( 'plugins.php?page=buhsl-captcha/buhsl-captcha.php' );
		echo "' method='post'>";
		
		echo "<input type='hidden' name='action' value='update' />";
        echo "<input type='hidden' name='form_type' value='upload_options' />";
		
		wp_nonce_field('buhsl-captcha-options_update');
		
		echo "<table>";
		
		foreach( self::$buhsl_default_options as $key => $value ){
			$lable = self::$options_name[$key]['lable'];
			if( !$lable )
				$lable = $key;
			echo "<tr><td><lable>$lable</lable></td><td>";
			if( !isset(self::$options[$key]) )
				self::$options[$key]=$value;
			$this->getSetter( $key, self::$options[$key]);
			echo "</td></tr>";
		}
		echo "</td></tr>";
		echo "<tr><td>&nbsp;</td><td>&nbsp;</td></tr>";
		echo "<tr><td >";
		echo "Preview:</td><td>";
		echo  $this->getCaptchaHtml(true, false);
		echo "</td></tr>";
		echo "<tr><td>&nbsp;</td><td>&nbsp;</td></tr>";

		echo "<tr><td colspan='2' align='center'>";
		echo "<input type='submit' name='submit' value='".esc_html( __('Save', 'buhsl-captcha'))." &raquo;'/>";
		echo "<input type='submit' name='restore_default' value='".esc_html( __('Restore default', 'buhsl-captcha'))." &raquo;'/>";
		echo "</td></tr>";
		echo "</table>";
		echo "</form>";
		
		echo "<hr width='80%' align='left'/><br/><h3>";
		echo esc_html( __('Donate', 'buhsl-captcha'));
		echo "</h3>";
		echo "<table>";
		echo "<tr><td align='left'>";
		_e('If you find this plugin useful to you, and would like to help on further development, please consider making a small donation. Thanks for your support!', 'buhsl-captcha');
		echo "</td></tr>";
		echo "<tr><td align='left'>";
		
		echo '<form action="https://www.paypal.com/cgi-bin/webscr" method="post">';
		echo '<input type="hidden" name="cmd" value="_s-xclick">';
		echo '<input type="hidden" name="hosted_button_id" value="NVVSDREWKGVZG">';
		echo '<input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">';
		echo '<img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">';
		echo '</form>';
		
		echo "</td></tr>";
		echo "</table>";
		
	}
	
	function getSetter( $key, $value ){
		switch($key){
			case 'evg_captcha_image_font':
				echo "<select name='$key'>";
				$fonts = array();
				$dirName = $this->getFontDir();
				if( is_dir($dirName)){
					$dir = @opendir($dirName);
					while ( $file = readdir($dir)) {
						//echo $file;
						if( !is_file( $dirName ."/".$file ) ) continue;
						if( !preg_match("#.ttf$#i", $file) ) continue;
						$fonts[]=$file;
					}
				}		
				foreach( $fonts as $font ){
					echo "<option value='$font' ";
					if( $font == $value )
						echo "selected";
					echo ">".strtoupper(substr($font,0,(stripos($font,'.ttf')>0)?stripos($font,'.ttf'):strlen($font)))."</option>";	
				}
				echo "</select>";
				break;
			case 'evg_captcha_level':
				echo "<select name='$key'>";
				foreach( self::$captcha_levels as $level=>$param ){
					echo "<option value='$level' ";
					if( $level == $value )
						echo "selected";
					echo ">".$level."</option>";	
				}
				echo "</select>";
				
				break;
			default:
				$width = self::$options_name[$key]['width'];
				$slen = strlen($value);
				if( $slen > 0 && $slen < $width ){
					$width = $slen+1;
				}
				echo "<input type='text' name='$key' value='$value' size='".$width."'/>";
		}
	}

	function buhsl_captcha_plugin_action_links( $links, $file ) {
		static $this_plugin;
		if ( ! $this_plugin ) {
			extract (pathinfo(dirname(__FILE__ )));
			$dirname= basename($dirname);
			$this_plugin = plugin_basename($dirname).'/buhsl-captcha.php';
		}
		if ( $file == $this_plugin ){
			$settings_link = '<a href="plugins.php?page=buhsl-captcha/buhsl-captcha.php">' . esc_html( __( 'Settings', 'buhsl-captcha' ) ) . '</a>';
			array_unshift( $links, $settings_link );
		}
		return $links;
	}


}

?>