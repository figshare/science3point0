<?php
function oqp_get_the_terms_list($taxonomy='post_tag',$post_id=false,$blog_id=false) {

	if ((oqp_is_multiste()) && ($blog_id)) {
		switch_to_blog($blog_id);
		$terms=get_the_term_list($post_id,$taxonomy,false,', ');
		restore_current_blog();
	}else {
		$terms=get_the_term_list($post_id,$taxonomy,false,', ');
	}

	return apply_filters( 'oqp_get_the_terms_list',$terms);
}

/**
 * Display or retrieve the HTML list of categories.
 *
 * The list of arguments is below:
 *     'show_option_all' (string) - Text to display for showing all categories.
 *     'orderby' (string) default is 'ID' - What column to use for ordering the
 * categories.
 *     'order' (string) default is 'ASC' - What direction to order categories.
 *     'show_last_update' (bool|int) default is 0 - See {@link
 * walk_category_dropdown_tree()}
 *     'show_count' (bool|int) default is 0 - Whether to show how many posts are
 * in the category.
 *     'hide_empty' (bool|int) default is 1 - Whether to hide categories that
 * don't have any posts attached to them.
 *     'use_desc_for_title' (bool|int) default is 1 - Whether to use the
 * description instead of the category title.
 *     'feed' - See {@link get_categories()}.
 *     'feed_type' - See {@link get_categories()}.
 *     'feed_image' - See {@link get_categories()}.
 *     'child_of' (int) default is 0 - See {@link get_categories()}.
 *     'exclude' (string) - See {@link get_categories()}.
 *     'exclude_tree' (string) - See {@link get_categories()}.
 *     'echo' (bool|int) default is 1 - Whether to display or retrieve content.
 *     'current_category' (int) - See {@link get_categories()}.
 *     'hierarchical' (bool) - See {@link get_categories()}.
 *     'title_li' (string) - See {@link get_categories()}.
 *     'depth' (int) - The max depth.
 *
 * @since 2.1.0
 *
 * @param string|array $args Optional. Override default arguments.
 * @return string HTML content only if 'echo' argument is 0.
 */
function oqp_terms_list($args = '',$blog_id=false) {

	$defaults = array(
		'show_option_all' => '',
		'show_option_none' => __('No categories'),
		'orderby' => 'name',
		'order' => 'ASC',
		'show_last_update' => 0,
		'style' => 'list',
		'show_count' => 0,
		'hide_empty' => 1,
		'use_desc_for_title' => 1,
		'child_of' => 0,
		'feed' => '',
		'feed_type' => '',
		'feed_image' => '',
		'exclude' => '',
		'exclude_tree' => '',
		'current_category' => 0,
		'hierarchical' => true,
		'title_li' => __( 'Categories' ),
		'echo' => 1,
		'depth' => 0,
		'include'=>'',
		'taxonomy' => 'category',
		'selected'=>false, //array of term names
		'type'=>false, //false|radio|checkbox
		'walker'=>false
	);


	$r = wp_parse_args( $args, $defaults );
	$r['walker']=(bool)$r['type'];



	if ( !isset( $r['pad_counts'] ) && $r['show_count'] && $r['hierarchical'] )
		$r['pad_counts'] = true;

	if ( isset( $r['show_date'] ) )
		$r['include_last_update_time'] = $r['show_date'];

	if ( true == $r['hierarchical'] ) {
		$r['exclude_tree'] = $r['exclude'];
		$r['exclude'] = '';
	}
	
	if ( !isset( $r['class'] ) )
		$r['class'] = ( 'category' == $r['taxonomy'] ) ? 'categories' : $r['taxonomy'];

	extract( $r );

	if ( !is_taxonomy($taxonomy) )
		return false;
		
	if ((oqp_is_multiste()) && ($blog_id)) {
		switch_to_blog($blog_id);
		$categories = get_categories( $r );
		restore_current_blog();
	}else {
		$categories = get_categories( $r );
	}
	
	$output = '';
	if ( $title_li && 'list' == $style )
			$output = '<li class="' . $class . '">' . $title_li . '<ul>';

	if ( empty( $categories ) ) {
		if ( ! empty( $show_option_none ) ) {
			if ( 'list' == $style )
				$output .= '<li>' . $show_option_none . '</li>';
			else
				$output .= $show_option_none;
		}
	} else {
		global $wp_query;

		if( !empty( $show_option_all ) )
			if ( 'list' == $style )
				$output .= '<li><a href="' .  get_bloginfo( 'url' )  . '">' . $show_option_all . '</a></li>';
			else
				$output .= '<a href="' .  get_bloginfo( 'url' )  . '">' . $show_option_all . '</a>';

		if ( empty( $r['current_category'] ) && ( is_category() || is_tax() ) )
			$r['current_category'] = $wp_query->get_queried_object_id();

		if ( $hierarchical )
			$depth = $r['depth'];
		else
			$depth = -1; // Flat.

		if (!$r['walker'])
			$output .= walk_category_tree( $categories, $depth, $r );
		else
			$output .= oqp_walk_category_tree( $categories, $depth, $r ); //radio or inputs
	}

	if ( $title_li && 'list' == $style )
		$output .= '</ul></li>';

	$output = apply_filters( 'wp_list_taxonomy_'.$taxonomy, $output );

	if ( $echo )
		echo $output;
	else
		return $output;
}

//
// Helper functions
//

/**
 * Retrieve HTML list content for category list.
 *
 * @uses Walker_Category to create HTML list content.
 * @since 2.1.0
 * @see Walker_Category::walk() for parameters and return description.
 */
function oqp_walk_category_tree() {
	$args = func_get_args();
	// the user's options are the third parameter
	if ( empty($args[2]['walker']) || !is_a($args[2]['walker'], 'Walker') )
		$walker = new Oqp_Walker_Term;
	else
		$walker = $args[2]['walker'];

	return call_user_func_array(array( &$walker, 'walk' ), $args );
}

//
// Category Checklists
//

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 */
class Oqp_Walker_Term extends Walker {

	var $db_fields = array ('parent' => 'parent', 'id' => 'term_id'); //TODO: decouple this

	function start_lvl(&$output, $depth, $args) {
		$indent = str_repeat("\t", $depth);
		$output .= "$indent<ul class='children'>\n";
	}

	function end_lvl(&$output, $depth, $args) {
		$indent = str_repeat("\t", $depth);
		$output .= "$indent</ul>\n";
	}

	function start_el(&$output, $term, $depth, $args) {
	
		if (!$args['selected'])
			$args['selected']=array();

		extract($args);
		if ( empty($taxonomy) )
			$taxonomy = 'category';


		if (!is_array($selected))
			$selected=explode(', ',$selected);

		$output .= "\n".'<li id="'.$taxonomy.'-'.$term->term_id.'" class="'.$class.'">' . '<label class="selectit"><input value="'.$term->term_id.'" type="'.$type.'" name="'.'oqp_'.$taxonomy.'[]" id="in-'.$taxonomy.'-' . $term->term_id . '"' . checked( in_array( $term->name, $selected ), true, false ) . disabled( empty( $args['disabled'] ), false, false ) . ' /> ' . esc_html( apply_filters('the_category', $term->name )) . '</label>';
	}

	function end_el(&$output, $term, $depth, $args) {
		$output .= "</li>\n";
	}
}

?>