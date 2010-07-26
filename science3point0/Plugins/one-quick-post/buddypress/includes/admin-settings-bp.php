<?php
function oqp_bp_admin_init() {
	register_setting( 'oqp_options', 'oqp_options', 'oqp_bp_options_validate' );
	add_settings_section('oqp_bp', __('BuddyPress','buddypress'), 'oqp_section_buddypress_text', 'oqp');
	add_settings_field('buddypress', __('BuddyPress Integration','oqp'), 'oqp_options_buddypress_text', 'oqp', 'oqp_bp');
}

function oqp_section_buddypress_text() {

}

function oqp_options_buddypress_text() {
	$options = get_option('oqp_options');
	if ($options['buddypress']) $checked=" CHECKED";
	echo "<input id='buddypress' name='oqp_options[buddypress]' type='checkbox' value='1'".$checked."/> - ";
	_e('Integrates One Quick Post into BuddyPress (into the menu, ...)','oqp');
}

function oqp_bp_options_validate($options) {

	return $options;
}

add_action('admin_init', 'oqp_bp_admin_init',11);
?>