<?php
/** 
 * The base configurations of bbPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys and bbPress Language. You can get the MySQL settings from your
 * web host.
 *
 * This file is used by the installer during installation.
 *
 * @package bbPress
 */



/**
 * bbPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$bb_table_prefix = 'wp_1bb_';

/**
 * bbPress Localized Language, defaults to English.
 *
 * Change this to localize bbPress. A corresponding MO file for the chosen
 * language must be installed to a directory called "my-languages" in the root
 * directory of bbPress. For example, install de.mo to "my-languages" and set
 * BB_LANG to 'de' to enable German language support.
 */
define( 'BB_LANG', '' );

$bb->custom_user_table = 'wp_1users';
$bb->custom_user_meta_table = 'wp_1usermeta';

$bb->uri = 'http://www.science2point0.com/wp-content/plugins/buddypress/bp-forums/bbpress/';
$bb->name = ' Forums';
$bb->wordpress_mu_primary_blog_id = 1;



define('WP_AUTH_COOKIE_VERSION', 2);

?>