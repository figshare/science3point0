<?php
/* 
 * Business functions for admin
 * 
 */

function gf_forum_dropdown($arg=''){
if ( $args && is_string($args) && false === strpos($args, '=') )
		$args = array( 'callback' => $args );
	if ( 1 < func_num_args() )
		$args['callback_args'] = func_get_arg(1);
	echo gf_get_forum_dropdown( $args );
}

function gf_get_forum_dropdown( $args = '' ) {
	$defaults = array( 'callback' => false, 'callback_args' => false, 'id' => 'forum_id', 'none' => false, 'selected' => false, 'tab' => false, 'hierarchical' => 1, 'depth' => 0, 'child_of' => 0, 'disable_categories' => 1, 'options_only' => false );

        if ( $args && is_string($args) && false === strpos($args, '=') )
		$args = array( 'callback' => $args );
	if ( 1 < func_num_args() )
		$args['callback_args'] = func_get_arg(1);

	$args = wp_parse_args( $args, $defaults );

	extract($args, EXTR_SKIP);

	if ( !bb_forums( $args ) )
		return;

	 $forum_id=  gf_get_current_forum_id();


	$name = esc_attr( $id );
	$id = str_replace( '_', '-', $name );
	$tab = (int) $tab;

	if ( $none && 1 == $none )
		$none = __('- None -');

	$r = '';
	if ( !$options_only ) {
		if ( $tab ) {
			$tab = ' tabindex="' . $tab . '"';
		} else {
			$tab = '';
		}
		$r .= '<select name="' . $name . '" id="' . $id . '"' . $tab . '">' . "\n";
	}
	if ( $none )
		$r .= "\n" . '<option value="0">' . $none . '</option>' . "\n";

	$no_option_selected = true;
	$options = array();
        if(gf_has_forums('child_of='.gf_get_root_forum_id()))
	while ( $depth = gf_forum() ) :
		global $gf_current_forum; // Globals + References = Pain
		$pad_left = str_repeat( '&nbsp;&nbsp;&nbsp;', $depth - 1 );
		if ( $disable_categories && isset($gf_current_forum->forum_is_category) && $gf_current_forum->forum_is_category ) {
			$options[] = array(
				'value' => 0,
				'display' => $pad_left . $gf_current_forum->forum_name,
				'disabled' => true,
				'selected' => false
			);
			continue;
		}
		$_selected = false;
		if ( (!$selected && $forum_id == $gf_current_forum->forum_id) || $selected == $gf_current_forum->forum_id ) {
			$_selected = true;
			$no_option_selected = false;
		}
		$options[] = array(
			'value' => $gf_current_forum->forum_id,
			'display' => $pad_left . $gf_current_forum->forum_name,
			'disabled' => false,
			'selected' => $_selected
		);
	endwhile;

	if ( 1 === count( $options ) && !$none ) {
		foreach ( $options as $option_index => $option_value ) {
			if ( $option_value['disabled'] ) {
				return;
			}
			return '<input type="hidden" name="' . $name . '" id="' . $id . '" value="' . esc_attr( $option_value['value'] ) . '" /><span>' . esc_html( $option_value['display'] ) . '</span>';
		}
	}

	foreach ($options as $option_index => $option_value) {
		if (!$none && !$selected && $no_option_selected && !$option_value['disabled']) {
			$option_value['selected'] = true;
			$no_option_selected = false;
		}
		$option_disabled = $option_value['disabled'] ? ' disabled="disabled"' : '';
		$option_selected = $option_value['selected'] ? ' selected="selected"' : '';
		$r .= "\n" . '<option value="' . esc_attr( $option_value['value'] ) . '"' . $option_disabled . $option_selected . '>' . esc_html( $option_value['display'] ) . '</option>' . "\n";
	}

	//$forum = $old_global;
	if ( !$options_only )
		$r .= '</select>' . "\n";

	return $r;
}


//for settings screen
function gf_get_settings(){
    $default=array("enable_activity"=>"no");//currently we have only one option
    $settings=get_site_option("gf_settings",$default);
    return maybe_unserialize($settings);
}

function gf_update_settings($current_settings){
    $current_settings=maybe_serialize($current_settings);
    update_site_option("gf_settings", $current_settings);
}


?>