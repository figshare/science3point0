<?php
/*
if a shortcode is found in a post; scripts & styles will be auto-loaded.

if you want to use oqp_gallery_block directly in a template/function, be sure you have loaded those scripts with


	$oqp_gallery->enqueue_styles();
	$oqp_gallery->footer_scripts();
	
	$oqp_gallery->oqp_gallery_block();
	
shortcode [oqp_gallery] / oqp_gallery_block() args :

-'post_id' - default is the current post

*/

class Oqp_Gallery {
 
	function oqp_Gallery($shortcode=true,$post_id=false,$atts=false) {
		global $current_user;

		//process gallery shortcode
		add_shortcode('oqp_gallery', array(__CLASS__,'handle_shortcode'));
		
		//check if there is a gallery shortcode then load scripts
		add_filter('the_posts', array(__CLASS__,'shortcode_load_scripts_and_styles'));
		

		//media uploder popup hooks (acts on backend)
		if ((!self::popup_check_is_backend()) && ($current_user->has_cap('upload_files'))) {
			//add_action('admin_print_scripts-media-upload-popup',array(__CLASS__,'popup_frontend_print_scripts'));
			add_action('admin_print_styles-media-upload-popup',array(__CLASS__,'popup_frontend_print_styles'));
			add_filter('media_upload_tabs',  array(__CLASS__,'popup_frontend_unset_upload_tabs'),11);
		}

		//should be loaded only if a post_id is returned but we have no way to check
		if ($_POST['oqp-action']=='oqp-save') {
			self::footer_scripts();
			self::enqueue_styles();
		}
		
	}
	//check the url to see if we are on the front end or back end
	function popup_check_is_backend() {
		$referer = wp_get_referer();

		return strpos($referer, "wp-admin");
	}
	
	//media upload popup scripts
	function popup_frontend_print_scripts() {
	}

	//media upload popup styles
	function popup_frontend_print_styles() {
		wp_enqueue_script('oqp-gallery-popup-style',ONEQUICKPOST_PLUGIN_URL . '/_inc/css/oqp-gallery-tb.css');
	}
	

	
	//media upload popup tabs
	function popup_frontend_unset_upload_tabs($tabs){
		unset($tabs['library']);
		unset($tabs['type_url']);
		return $tabs;
	}
	
	
	function shortcode_load_scripts_and_styles($posts){
		if (empty($posts)) return $posts;
		
		$shortcode_found = false; // use this flag to see if styles and scripts need to be enqueued
		foreach ($posts as $post) {
			if (stripos($post->post_content, 'oqp_gallery')) {
				$shortcode_found = true; // bingo!
				break;
			}
		}
	 
		if ($shortcode_found) {
			self::footer_scripts();
			self::enqueue_styles();
		}
	 
		return $posts;
	}
	
	function enqueue_styles() {
		global $current_user;
		if (!$current_user->has_cap('upload_files')) return false;
		
		wp_enqueue_style('thickbox');
	}
	
	function footer_scripts() {
		global $current_user;
		if (!$current_user->has_cap('upload_files')) return false;

		wp_print_scripts('thickbox');
		wp_print_scripts('media_upload');
		
		wp_register_script('jquery.livequery',ONEQUICKPOST_PLUGIN_URL . '/_inc/js/jquery.livequery.js', array('jquery'),'1.0.3');
		wp_register_script('oqp-pictures',ONEQUICKPOST_PLUGIN_URL . '/_inc/js/oqp-pictures.js', array('jquery'),ONEQUICKPOST_VERSION);
		
		wp_print_scripts('jquery.livequery');
		wp_print_scripts('oqp-pictures');
		
		?>
			<script type="text/javascript">
			//<![CDATA[
			oqp_tb_pathToImage = "<?php echo get_blog_option($bp->classifieds->options['blog_id'],'siteurl ');?>/wp-includes/js/thickbox/loadingAnimation.gif";
			oqp_tb_closeImage = "<?php echo get_blog_option($bp->classifieds->options['blog_id'],'siteurl ');?>/wp-includes/js/thickbox/tb-close.png";
			//]]>
			</script>
		<?php
		
	}

	function handle_shortcode($atts) {
		global $post;
		self::oqp_gallery_block($atts);
	
	}
	
	function oqp_gallery_block($atts) {
		global $current_user;

		//GET POST ID
		if (!$atts['post_id']) {
			global $post;
			$oqp_post_id=$post->ID;
		}else {
			$oqp_post_id=$atts['post_id'];
		}
		
		

		if (!$oqp_post_id) return false;
		

		if (!$current_user->has_cap('upload_files')) return false;


		?>
		
		<div id="oqp_gallery<?php echo $oqp_post_id;?>" class="oqp_gallery_block" rel="<?php echo $oqp_post_id;?>">
		<label>gallery</label>
		<a class="oqp-pictures-upload" href="<?php echo get_blog_option($atts['blog_id'],'siteurl');?>/wp-admin/media-upload.php?post_id=<?php echo $oqp_post_id;?>&type=image&TB_iframe=true&width=640&height=618"><?php _e('Would you like to add pictures ?','oqp');?></a>

		<?php echo self::get_gallery($oqp_post_id);?>
		</div>
		
		<?php
	
	}

	function get_gallery($oqp_post_id,$atts=false){
		global $bp;
		
		$default=array(
			'link'=>'file',
			'size'=>'thumbnail'
		);
		
		$args = wp_parse_args( $atts, $default);

		if ((!$oqp_post_id) || (!is_numeric($oqp_post_id)))return false;

		$gallery = do_shortcode('[gallery id="'.$oqp_post_id.'" link="'.$args['link'].'" size="'.$args['size'].'"]');

		return $gallery;
	}

	
}

//AJAX
function oqp_gallery_ajax_fetch_gallery() {

	$pid = $_POST['pid'];

	echo $pid;
	
	if (!$pid) echo '';

	echo Oqp_Gallery::get_gallery($pid);

}
add_action( 'wp_ajax_oqp_gallery','oqp_gallery_ajax_fetch_gallery');


function oqp_gallery_init() {
	global $oqp_gallery; 
	$oqp_gallery = new Oqp_Gallery();
}

add_action("init", "oqp_gallery_init");

//refresh gallery if new pic has been uploaded


?>