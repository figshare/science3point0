<?php
/*
Plugin Name: BackType Tweetcount
Plugin URI: http://www.backtype.com/plugins/tweetcount
Description: The BackType Tweetcount plugin shows the number of tweets your posts get and allows users to retweet.
Version: 2.0
Author: BackType <support@backtype.com>
Author URI: http://www.backtype.com/
*/

/*  Copyright 2009  BackType Inc  (email : support@backtype.com)

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

define('BACKTYPE_TWEETCOUNT_API', 'http://backtweets.com/search.php');
define('BTTC_CACHE_INTERVAL', 60);

if (is_admin()) {
	add_action('admin_menu', 'bttc_options');
	add_action('admin_init', 'bttc_init');
	register_activation_hook(__FILE__, 'bttc_activate');
}

add_filter('the_content', 'bttc_update');
add_filter('get_the_excerpt', 'bttc_remove_filter', 9);

function bttc_options() {
	add_options_page('Tweetcount Settings', 'BackType Tweetcount', 8, 'backtype-tweetcount', 'bttc_options_page');
}

// Register these variables (WP 2.7 & newer)
function bttc_init() {
	if (function_exists('register_setting')) {
		register_setting('bttc-options', 'bttc_src');
		register_setting('bttc-options', 'bttc_via');
		register_setting('bttc-options', 'bttc_links');
		register_setting('bttc-options', 'bttc_size');
		register_setting('bttc-options', 'bttc_location');
		register_setting('bttc-options', 'bttc_style');
		register_setting('bttc-options', 'bttc_shortener');
		register_setting('bttc-options', 'bttc_api_key');
		register_setting('bttc-options', 'bttc_login');
		register_setting('bttc-options', 'bttc_background');
		register_setting('bttc-options', 'bttc_border');
		register_setting('bttc-options', 'bttc_text');
	}
}

// default options
function bttc_activate() {
	add_option('bttc_src', '');
	add_option('bttc_via', '');
	add_option('bttc_links', '');
	add_option('bttc_size', '');
	add_option('bttc_location', 'top');
	add_option('bttc_style', 'float:left;margin-right:10px;');
	add_option('bttc_shortener', '');
	add_option('bttc_api_key', '');
	add_option('bttc_login', '');
	add_option('bttc_background', '');
	add_option('bttc_border', '');
	add_option('bttc_text', '');
}

function bttc_update($content) {
	global $post;
	
	if (get_option('bttc_location') == 'manual') {
		return $content;
	}
	
	if (is_feed()) {
		return $content;
	}
	
	if (get_post_meta($post->ID, 'bttc', true) == '') {
		$button = backtype_tweetcount();
		switch (get_option('bttc_location')) {
			case 'topbottom':
				return $button . $content . $button;
			break;
			case 'top':
				return $button . $content;
			break;
			case 'bottom':
				return $content . $button;
			break;
			default:
				return $button . $content;
			break;
		}
	} else {
		return $content;
	}
}

function bttc_remove_filter($content) {
	remove_action('the_content', 'bttc_update');
	return $content;
}

function bttc_options_page() {
	echo '<div class="wrap">';
	if (function_exists('screen_icon')) { screen_icon(); }
	echo'<h2>BackType Tweetcount</h2>';
	echo '<form method="post" action="options.php">';
	wp_nonce_field('update-options');
	echo '<table class="form-table">';
	echo '<tr valign="top"><th scope="row">Leading Text</th><td><input type="text" name="bttc_src" value="' . get_option('bttc_src') . '" /><span class="setting-description">e.g. RT @BackType</span></td></tr>';
	echo '<tr valign="top"><th scope="row">Attribution (via @backtype)</th><td><select name="bttc_via"><option value="">enabled</option><option value="false"' . ((get_option('bttc_via')=='false')?' selected':'') . '>disabled</option></select></td></tr>';
	echo '<tr valign="top"><th scope="row">Links</th><td><input type="checkbox" name="bttc_links"' . ((get_option('bttc_links')=='on')?' checked':'') . ' /> <span class="setting-description">Open links in new windows</span></td></tr>';
	echo '<tr valign="top"><th scope="row">Size</th><td><select name="bttc_size"><option value="">large</option><option value="small"' . ((get_option('bttc_size')=='small')?' selected':'') . '>small</option></select></td></tr>';
	echo '<tr valign="top"><th scope="row">Location</th><td><select name="bttc_location"><option value="top">top</option><option value="bottom"' . ((get_option('bttc_location')=='bottom')?' selected':'') . '>bottom</option><option value="topbottom"' . ((get_option('bttc_location')=='topbottom')?' selected':'') . '>top &amp; bottom</option><option value="manual"' . ((get_option('bttc_location')=='manual')?' selected':'') . '>manual</option></select> <span class="setting-description">For manual positioning, echo backtype_tweetcount(); where you would like the button to appear</span></td></tr>';
	echo '<tr valign="top"><th scope="row">Wrapper Style</th><td><input type="text" name="bttc_style" value="' . get_option('bttc_style') . '" /> <span class="setting-description">CSS for positioning, margins, etc</span></td></tr>';
	echo '<tr valign="top"><th scope="row">Button Background Color</th><td><input type="text" name="bttc_background" value="' . get_option('bttc_background') . '" /> <span class="setting-description">e.g. FFFFFF</span></td></tr>';
	echo '<tr valign="top"><th scope="row">Button Border Color</th><td><input type="text" name="bttc_border" value="' . get_option('bttc_border') . '" /> <span class="setting-description">e.g. 3399CC</span></td></tr>';
	echo '<tr valign="top"><th scope="row">Button Text Color</th><td><input type="text" name="bttc_text" value="' . get_option('bttc_text') . '" /> <span class="setting-description">e.g. 000000</span></td></tr>';
	echo '</table><p>The following options allow you to choose which URL shortener you would like to use:</p><table class="form-table">';
	echo '<tr valign="top"><th scope="row">Shortener</th><td><select name="bttc_shortener"><option value="">bt.io (default)</option><option value="awesm"' . ((get_option('bttc_shortener')=='awesm')?' selected':'') . '>awe.sm (custom)</option><option value="bitly"' . ((get_option('bttc_shortener')=='bitly')?' selected':'') . '>bit.ly</option><option value="tinyurl"' . ((get_option('bttc_shortener')=='tinyurl')?' selected':'') . '>tinyurl.com</option><option value="digg"' . ((get_option('bttc_shortener')=='digg')?' selected':'') . '>digg.com</option><option value="supr"' . ((get_option('bttc_shortener')=='supr')?' selected':'') . '>su.pr</option></select></td></tr>';
	echo '<tr valign="top"><th scope="row">API Key</th><td><input type="text" name="bttc_api_key" value="' . get_option('bttc_api_key') . '" /> <span class="setting-description">Required: bit.ly, awe.sm, optional: su.pr</span></td></tr>';
	echo '<tr valign="top"><th scope="row">Login</th><td><input type="text" name="bttc_login" value="' . get_option('bttc_login') . '" /> <span class="setting-description">Required: bit.ly, optional: su.pr</span></td></tr>';
	echo '</table>';
	echo '<input type="hidden" name="action" value="update" /><input type="hidden" name="page_options" value="bttc_src,bttc_via,bttc_links,bttc_size,bttc_location,bttc_style,bttc_background,bttc_border,bttc_text,bttc_shortener,bttc_api_key,bttc_login" /><p class="submit"><input type="submit" class="button-primary" value="Save Changes" /></p></form></div>';
}

function backtype_tweetcount($src=null, $via=null, $links=null, $size=null, $style=null, $background=null, $border=null, $text=null, $shortener=null, $api_key=null, $login=null) {
	global $post;
	$url = '';
	$cnt = null;
	
	// let users override these vars when calling manually
	$src = ($src === null) ? get_option('bttc_src') : $src;
	$via = ($via === null) ? get_option('bttc_via') : $via;
	$links = ($links === null) ? ((get_option('bttc_links') == 'on') ? 'true' : 'false') : $links;
	$size = ($size === null) ? get_option('bttc_size') : $size;
	$style = ($style === null) ? get_option('bttc_style') : $style;
	$background = ($background === null) ? get_option('bttc_background') : $background;
	$border = ($border === null) ? get_option('bttc_border') : $border;
	$text = ($text === null) ? get_option('bttc_text') : $text;
	$shortener = ($shortener === null) ? get_option('bttc_shortener') : $shortener;
	$api_key = ($api_key === null) ? get_option('bttc_api_key') : $api_key;
	$login = ($login === null) ? get_option('bttc_login') : $login;
	
	if (get_post_status($post->ID) == 'publish') {
		$url = get_permalink();
		$title = $post->post_title;
		
		if ((function_exists('curl_init') || function_exists('file_get_contents')) && function_exists('unserialize')) {
			$meta = get_post_meta($post->ID, 'bttc_cache', true);
			if ($meta != '') {
				$pieces = explode(':', $meta);
				$timestamp = (int)$pieces[0];
				$cnt = (int)$pieces[1];
			}
			// expire cache
			if ($cnt === null || time() > $timestamp + BTTC_CACHE_INTERVAL) {
				$response = bttc_urlopen(BACKTYPE_TWEETCOUNT_API . '?identifier=bttc&since_id=0&refresh=1&q=' . urlencode($url));
				$data = unserialize($response);
				if (isset($data['results_count']) && (int)$data['results_count'] >= $cnt) {
					$cnt = $data['results_count'];
					if ($meta == '') {
						add_post_meta($post->ID, 'bttc_cache', time() . ':' . $cnt);
					} else {
						update_post_meta($post->ID, 'bttc_cache', time() . ':' . $cnt);
					}
				}
			}

			if ($shortener && $shortener != 'awesm' && get_post_meta($post->ID, 'bttc_short_url', true) == '') {
				$short_url = null;
				switch ($shortener) {
					case 'bitly':
						$short_url = bttc_shorten_bitly($url, $api_key, $login);
					break;
					case 'tinyurl':
						$short_url = bttc_shorten_tinyurl($url);
					break;
					case 'digg':
						$short_url = bttc_shorten_digg($url);
					break;
					case 'supr':
						$short_url = bttc_shorten_supr($url, $api_key, $login);
					break;
				}
				if ($short_url) {
					add_post_meta($post->ID, 'bttc_short_url', $short_url);
				}
			}
		}
	}

	$button = '<script type="text/javascript">' .
			'tweetcount_url=\'' . $url . '\';' .
			'tweetcount_title=\'' . wp_specialchars($title, '1') . '\';';
	if ($shortener && $shortener != 'awesm' && get_post_meta($post->ID, 'bttc_short_url', true) != '') {
		$button .= 'tweetcount_short_url=\'' . wp_specialchars(get_post_meta($post->ID, 'bttc_short_url', true), '1') . '\';';
	}
	if ($cnt !== null) {
		$button .= 'tweetcount_cnt=' . (int)$cnt . ';';
	}
	if ($src !== '') {
		$button .= 'tweetcount_src=\'' . wp_specialchars($src, '1') . '\';';
	}
	if ($via === 'false') {
		$button .= 'tweetcount_via=false;';
	}
	if ($links === 'true') {
		$button .= 'tweetcount_links=true;';
	}
	if ($size !== '') {
		$button .= 'tweetcount_size=\'' . wp_specialchars($size, '1') . '\';';
	}
	if ($background !== '') {
		$button .= 'tweetcount_background=\'' . wp_specialchars($background, '1') . '\';';
	}
	if ($border !== '') {
		$button .= 'tweetcount_border=\'' . wp_specialchars($border, '1') . '\';';
	}
	if ($text !== '') {
		$button .= 'tweetcount_text=\'' . wp_specialchars($text, '1') . '\';';
	}
	if ($api_key !== '') {
		$button .= 'tweetcount_api_key=\'' . wp_specialchars($api_key, '1') . '\';';
	}
	$button .= '</script>';

	if ($style !== '') {
		$button .= '<div style="' . $style . '">';
	}

	$button .= '<script type="text/javascript" src="http://widgets.backtype.com/tweetcount.js"></script>';
	
	if ($style !== '') {
		$button .= '</div>';
	}
			 
	return $button;
}

function bttc_urlopen($url) {
	if (function_exists('curl_init')) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, false);
		$result = curl_exec($ch);
		curl_close($ch);
		return $result;
	} else {
		return file_get_contents($url);
	}
}

// Code added by @michaelmontano
function bttc_shorten_bitly($url, $api_key, $login='') {
	if ($api_key && function_exists('json_decode')) {
		$bitly_url = 'http://api.bit.ly/shorten';
		$bitly_version = '2.0.1';
		$bitly_vars = '?version=' . $bitly_version . '&longUrl=' . urlencode($url) .
						'&login=' . $login . '&apiKey=' .$api_key;
						
		$response =  bttc_urlopen($bitly_url . $bitly_vars);
		if ($response) {
			$data = json_decode($response, true);
			if (isset($data['results'])) {
				$keys = array_keys($data['results']);
				if (isset($data['results'][$keys[0]]['shortCNAMEUrl'])) {
					return $data['results'][$keys[0]]['shortCNAMEUrl'];
				} elseif (isset($data['results'][$keys[0]]['shortUrl'])) {
					return $data['results'][$keys[0]]['shortUrl'];
				}
			}
		}
	}
	return false;
}

// Code added by @michaelmontano
function bttc_shorten_digg($url) {
	if (function_exists('curl_init')) {
		class DiggAPIShortURLs {};
		class DiggAPIShortURL {};
		
		$digg_url = 'http://services.digg.com/url/short/create';
		$digg_vars = '?type=php&url=' . urlencode($url) . '&appkey=http%3A%2F%2Fwww.backtype.com%2Fplugins%2Ftweetcount';
		$req_url = $digg_url . $digg_vars;
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $req_url);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_USERAGENT, 'BackType-Tweetcount');
		$response = curl_exec($ch);
		curl_close($ch);
		
		if ($response) {
			$data = unserialize($response);
			if (isset($data->shorturls[0]->short_url)) {
				return $data->shorturls[0]->short_url;
			}
		}
	}
	return false;
}

// Code added by @michaelmontano
function bttc_shorten_tinyurl($url) {
	$tinyurl_url = 'http://tinyurl.com/api-create.php';
	$tinyurl_vars = '?url=' . urlencode($url);
	
	$response = bttc_urlopen($tinyurl_url . $tinyurl_vars);
	if ($response) {
		return $response;
	}
	return false;
}

// Code added by @appdevnet
function bttc_shorten_supr($url, $api_key='', $login='') {
	$su_url = 'http://su.pr/api';
	$su_vars = '?url=' . urlencode($url) . '&login=' . $login . '&apiKey=' .$api_key;
	$req_url = $su_url . $su_vars;

	$curl_handle = curl_init();
	curl_setopt($curl_handle, CURLOPT_URL, $req_url);
	curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
	curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl_handle, CURLOPT_HTTPGET, 1); 
	$buffer = curl_exec($curl_handle);
	curl_close($curl_handle);
	$su_short_url = $buffer;
	// uncomment if hosting off own domain
	//$su_short_url = str_replace('su.pr/', '', $buffer);

	return $su_short_url;
}
