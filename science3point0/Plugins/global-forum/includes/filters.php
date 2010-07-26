<?php

/* Apply WordPress defined filters */
add_filter( 'gf_forums_bbconfig_location', 'wp_filter_kses', 1 );
add_filter( 'gf_forums_bbconfig_location', 'attribute_escape', 1 );

add_filter( 'gf_get_the_topic_title', 'wp_filter_kses', 1 );
//add_filter( 'gf_get_the_topic_latest_post_excerpt', 'gf_forums_filter_kses', 1 );
//add_filter( 'gf_get_the_topic_post_content', 'gf_forums_filter_kses', 1 );
add_filter('gf_get_the_topic_post_content', 'trim');
add_filter('gf_get_the_topic_post_content', 'bb_encode_bad');
add_filter('gf_get_the_topic_post_content', 'bb_code_trick');
add_filter('gf_get_the_topic_post_content', 'force_balance_tags');
add_filter('gf_get_the_topic_post_content', 'gf_filter_kses', 50);
add_filter('gf_get_the_topic_post_content', 'bb_autop', 60);

add_filter('gf_get_the_topic_post_content', 'do_shortcode');

add_filter( 'gf_get_the_topic_title', 'wptexturize' );
add_filter( 'gf_get_the_topic_poster_name', 'wptexturize' );
add_filter( 'gf_get_the_topic_last_poster_name', 'wptexturize' );
add_filter( 'gf_get_the_topic_post_content', 'wptexturize' );
add_filter( 'gf_get_the_topic_post_poster_name', 'wptexturize' );

add_filter( 'gf_get_the_topic_title', 'convert_smilies' );
add_filter( 'gf_get_the_topic_latest_post_excerpt', 'convert_smilies' );
add_filter( 'gf_get_the_topic_post_content', 'convert_smilies' );

add_filter( 'gf_get_the_topic_title', 'convert_chars' );
add_filter( 'gf_get_the_topic_latest_post_excerpt', 'convert_chars' );
add_filter( 'gf_get_the_topic_post_content', 'convert_chars' );

add_filter( 'gf_get_the_topic_post_content', 'wpautop',12 );
add_filter( 'gf_get_the_topic_latest_post_excerpt', 'wpautop',15 );

add_filter( 'gf_get_the_topic_post_content', 'stripslashes_deep' );
add_filter( 'gf_get_the_topic_title', 'stripslashes_deep' );
add_filter( 'gf_get_the_topic_latest_post_excerpt', 'stripslashes_deep' );
add_filter( 'gf_get_the_topic_poster_name', 'stripslashes_deep' );
add_filter( 'gf_get_the_topic_last_poster_name', 'stripslashes_deep' );

add_filter( 'gf_get_the_topic_post_content', 'make_clickable',20 );

add_filter( 'gf_get_forum_topic_count_for_user', 'bp_core_number_format' );
add_filter( 'gf_get_forum_topic_count', 'bp_core_number_format' );

add_filter( 'gf_get_the_topic_title', 'gf_make_nofollow_filter' );
add_filter( 'gf_get_the_topic_latest_post_excerpt', 'gf_make_nofollow_filter' );
add_filter( 'gf_get_the_topic_post_content', 'gf_make_nofollow_filter' );
function gf_filter_kses($content){
    $allowedtags = bb_allowed_tags();
    $allowedtags=gf_allow_videotags_in_posts($allowedtags);
   $allowedtags=apply_filters("gf_allowed_tags_in_post",$allowedtags);
    return wp_kses($content, $allowedtags);
}
/*
//not used
function gf_forums_filter_kses( $content ) {
	global $allowedtags;

	$forums_allowedtags = $allowedtags;
	$forums_allowedtags['span'] = array();
	$forums_allowedtags['span']['class'] = array();
	//$forums_allowedtags['div'] = array();
	//$forums_allowedtags['div']['class'] = array();
	//$forums_allowedtags['div']['id'] = array();
	$forums_allowedtags['a']['class'] = array();
	$forums_allowedtags['pre'] = array();
	$forums_allowedtags['img'] = array();
	$forums_allowedtags['br'] = array();
	$forums_allowedtags['p'] = array();
	$forums_allowedtags['img']['src'] = array();
	$forums_allowedtags['img']['alt'] = array();
	$forums_allowedtags['img']['class'] = array();
	$forums_allowedtags['img']['width'] = array();
	$forums_allowedtags['img']['height'] = array();
	$forums_allowedtags['img']['class'] = array();
	$forums_allowedtags['img']['id'] = array();
	$forums_allowedtags['code'] = array();
	$forums_allowedtags['blockquote'] = array();

	$forums_allowedtags = apply_filters( 'gf_forums_allowed_tags', $forums_allowedtags );
	return wp_kses( $content, $forums_allowedtags );
}

*/

function gf_make_nofollow_filter( $text ) {
	return preg_replace_callback( '|<a (.+?)>|i', 'gf_make_nofollow_filter_callback', $text );
}
	function gf_make_nofollow_filter_callback( $matches ) {
		$text = $matches[1];
		$text = str_replace( array( ' rel="nofollow"', " rel='nofollow'"), '', $text );
		return "<a $text rel=\"nofollow\">";
	}
add_filter("gf_get_the_topic_post_content","gf_enable_oembed",10,2);
        /* r-ay-s's code for bp-oembed, modified for global forum*/
  function gf_enable_oembed($content,$forum_post_id){
         global $bp_oembed;//defined by r-ay's bp-oembed
         if(!function_exists("ray_bp_oembed")||!function_exists('wp_oembed_get')) //if bp-embed is not installed return content
             return $content;
	

	// match URLs - could use some work
        //preg_match_all( '@(https?://([-\w\.]+)+(:\d+)?(/([\w/_\.]*(\?\S+)?)?)?)@', $content, $matches );
	preg_match_all('`.*?((http|https)://[\w#$&+,\/:;=?@.-]+)[^\w#$&+,\/:;=?@.-]*?`i', $content, $matches);

	// if there are no links to parse, return $content now!
	if(empty($matches[0]))
		return $content;

	$whitelist = $bp_oembed['whitelist'];

	for($i=0;$i<count($matches[0]);$i++) {
		$url = $matches[0][$i];

		// check url with whitelist, if url matches any whitelist item, skip from parsing
		foreach ($whitelist as $whitelist_item) {
			if (strpos($url,$whitelist_item) !== false) {
				continue 2;
			}
		}

		$cachekey = '_oembed_' . md5($url);

		// grab oEmbed cache depending on BP component
		$cache = bb_get_postmeta($forum_post_id, $cachekey);
		
		// cache check - no oEmbed, but cached result, skip rest of loop
		if ( $url === $cache ) {
			continue;
		}

		// cache check - yes oEmbed
		if ( !empty($cache) ) {
			$replace = apply_filters( 'embed_oembed_html', $cache, $url, $attr );
		}
		// if no cache, let's start the show!
		else {
			// process url to oEmbed
			$oembed = wp_oembed_get($url); // returns true if link is oEmbed
			//$oembed = file_get_contents("http://autoembed.com/api/?url=".urlencode($url));

			if ($oembed) {
				$replace = apply_filters( 'embed_oembed_html', $oembed, $url, $attr );
				$replace = str_replace('
','',$replace); // fix Viddler line break in <object> tag
			}
			else {
				$replace = $url;
				// unlike WP's oEmbed, I cache the URL if not oEmbed-dable!
				// the URL is more useful in the DB than a string called {{unknown}} ;)
			}

			// save oEmbed cache depending on BP component
			// the same "not prettiness!"
			
				bb_update_postmeta($forum_post_id, $cachekey, $replace);
			
		}

		$content = str_replace($url, $replace, $content);
	}

	return $content;

  }

  //fix the allowed tags too
  
  function gf_allow_videotags_in_posts($allowed_tags){
        $allowed_tags['object'] = array();
	$allowed_tags['object']['width'] = array();
	$allowed_tags['object']['height'] = array();

    $allowed_tags['param'] = array();
	$allowed_tags['param']['name'] = array();
	$allowed_tags['param']['value'] = array();

    $allowed_tags['embed'] = array();
	$allowed_tags['embed']['src'] = array();
	$allowed_tags['embed']['type'] = array();
	$allowed_tags['embed']['allowscriptaccess'] = array();
	$allowed_tags['embed']['wmode'] = array();
	$allowed_tags['embed']['allowfullscreen'] = array();
	$allowed_tags['embed']['width'] = array();
	$allowed_tags['embed']['height'] = array();
	$allowed_tags['embed']['flashvars'] = array();

return $allowed_tags;
}

function gf_enabled_post_activity(){
    $setting=gf_get_settings();
    $allow=false;
    if($setting["enable_activity"]=="yes")
        $allow=true;
    return apply_filters("gf_is_post_to_activity_nabled",$allow);

}
?>