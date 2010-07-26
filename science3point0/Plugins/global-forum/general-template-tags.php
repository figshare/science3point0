<?php
/* 
 * General template tags
 * and open the template in the editor.
 */

/*breadcrum for front end*/
function gf_forum_action() {
	echo gf_get_forum_action();
}
	function gf_get_forum_action() {
		global $topic_template;

		return apply_filters( 'gf_get_forum_action',  attribute_escape( $_SERVER['REQUEST_URI'] ) );
	}

function gf_forum_topic_action() {
	echo gf_get_forum_topic_action();
}
	function gf_get_forum_topic_action() {
		global $bp;

		return apply_filters( 'gf_get_forum_topic_action',  attribute_escape( $_SERVER['REQUEST_URI'] ) );
	}

function gf_get_forum_bread_crumb($args = '') {
	$defaults = array(
		'forum_id' =>  gf_get_current_forum_id(),
		'separator' => ' &raquo; ',
		'class' => null,
                'home_text'=>GF_LINK_TITLE,
                'show_home'=>true
	);
       
	$args = wp_parse_args($args, $defaults);
	extract($args, EXTR_SKIP);
         if(!$forum_id&&  gf_is_topic())
            $forum_id=gf_get_topic_parent_forum_id ();
	$trail = '';
        
       
	$trail_forum = bb_get_forum(get_forum_id($forum_id));
        if(gf_is_topic ())
                  $trail=$separator.gf_get_topic_title();
        

      
	if ($class) {
		$class = ' class="' . $class . '"';
	}
	$current_trail_forum_id = $trail_forum->forum_id;
	while ( $trail_forum && $trail_forum->forum_id !=  gf_get_root_forum_id() ) {
		 $crumb = $separator;
		if ($current_trail_forum_id != $trail_forum->forum_id || !gf_is_forum()) {
			$crumb .= '<a' . $class . ' href="' . gf_get_forum_permalink($trail_forum->forum_id) . '">';
		} elseif ($class) {
			$crumb .= '<span' . $class . '>';
		}
		$crumb .= gf_get_forum_name($trail_forum->forum_id);
		if ($current_trail_forum_id != $trail_forum->forum_id || !gf_is_forum()) {
			$crumb .= '</a>';
		} elseif ($class) {
			$crumb .= '</span>';
		}

              
                
		$trail = $crumb . $trail;
		$trail_forum = bb_get_forum($trail_forum->forum_parent);
               
	}
  if($show_home)
            $trail='<a href="'.gf_get_home_url ().'">'.$home_text."</a>".$trail;
	return apply_filters('gf_get_forum_bread_crumb', $trail, $forum_id );
}



//add a tab to top navigation

function gf_add_to_bp_nav(){
if(apply_filters("gf_add_to_nav", 1)):
?>
<li <?php if(bp_is_page(GF_SLUG)):?> class="selected"<?php endif;?>><a href="<?php echo gf_get_home_url();?>" rel="<?php echo GF_LINK_TITLE;?>"><?php echo GF_LINK_TITLE;?></a></li>
<?php
endif;
}
add_action("bp_nav_items", "gf_add_to_bp_nav");


function gf_is_front(){
    global $bp;
    
    if($bp->current_component==$bp->gf->slug&&$bp->current_action!="admin")
            return true;
   return false;
}
//is current page is tag page
function gf_is_tag(){
    global $bp;
    if($bp->current_component==$bp->gf->slug&&$bp->current_action=="tag"&&!empty($bp->action_variables))//we ma mprove it a little bit by checking existance of tags
        return true;
    
    return false;

}

function gf_get_single_tag_name(){
global $bp;
if(gf_is_tag()&&!empty($bp->action_variables[0])){
//we have the tag slug here
 $tag=   bb_get_tag($bp->action_variables[0]);
if(!empty($tag))
    return $tag->name;
}
return __('No Tag exists by this name','gf');
    
}

function gf_is_search(){
 global $bp;

 if($bp->current_component==$bp->gf->slug&&empty($bp->current_action)&&isset($_GET['gfs']))//we ma mprove it a little bit by checking existance of tags
        return true;

    return false;
}

function gf_get_parent_forum_permalink($topic_slug=null){
  $topic_id = gf_get_topic_id_from_slug( $topic_slug );
  $topic=gf_get_topic_details($topic_id);
  return gf_get_forum_permalink($topic->forum_id);
}

function gf_get_topic_parent_forum_id($topic_slug=null){
    global $bp;
    if(gf_is_topic ()&&!$topic_slug)
        $topic_slug=$bp->action_variables[0];
    
     $topic_id = gf_get_topic_id_from_slug( $topic_slug );
     $topic=gf_get_topic_details($topic_id);
    return $topic->forum_id;
}
/* a copyy of bb_remove_tag*/
/**
 * bb_remove_topic_tag() - Removes a single bb_topic_tag by a user from a topic.
 *
 * @param int $tt_id The TT_ID of the bb_topic_tag to be removed(term_taxonomy id)
 * @param int $user_id
 * @param int $topic_id
 * @return array|false The TT_IDs of the users bb_topic_tags on that topic or false on failure
 */
function gf_remove_topic_tag( $tt_id,  $topic_id,$user_id=null ) {
	global $wp_taxonomy_object,$bp;
	$tt_id   = (int) $tt_id;
	$user_id  = (int) $bp->loggedin_user->id;
	$topic_id = (int) $topic_id;
	if ( !$topic = get_topic( $topic_id ) )
		return false;
	if(!apply_filters("gf_user_can_edit_tag", 1))//allow all
        	return false;

	$_tag = bb_get_tag( $tt_id );

	do_action('bb_pre_tag_removed', $tt_id, $user_id, $topic_id);
	$current_tag_ids = $wp_taxonomy_object->get_object_terms( $topic_id, 'bb_topic_tag', array( 'user_id' => $user_id, 'fields' => 'tt_ids' ) );
	if ( !is_array($current_tag_ids) )
		return false;
                
	$current_tag_ids = array_map( 'intval', $current_tag_ids );

	if ( false === $pos = array_search( $tt_id, $current_tag_ids ) )
		return false;
               
	unset($current_tag_ids[$pos]);

	$tt_ids = $wp_taxonomy_object->set_object_terms( $topic_id, array_values($current_tag_ids), 'bb_topic_tag', array( 'user_id' => $user_id ) );
	if ( is_array( $tt_ids ) ) {
		global $bbdb;
		$bbdb->query( $bbdb->prepare(
			"UPDATE $bbdb->topics SET tag_count = %d WHERE topic_id = %d", count( $tt_ids ), $topic_id
		) );
		wp_cache_delete( $topic_id, 'bb_topic' );

		// Count is updated at set_object_terms()
		if ( $_tag && 2 > $_tag->tag_count ) {
			bb_destroy_tag( $_tag->term_taxonomy_id );
		}
	} elseif ( is_wp_error( $tt_ids ) ) {
		return false;
	}
	return $tt_ids;
}

/*add tag form*/
function gf_show_add_tag_form( $args = null )
{
	$defaults = array( 'topic' => 0, 'submit' => __('Add &raquo;'), 'list_id' => 'tags-list' );
	$args = wp_parse_args( $args, $defaults );
	extract( $args, EXTR_SKIP );
        global $gf_forum_topics_template;

		
	if ( !$topic = get_topic( get_topic_id($gf_forum_topics_template->topic->topic_id ) ) ) {
		return false;
	}
    global $bp;
	if ( !is_user_logged_in()||gf_is_user_banned($bp->loggedin_user->id)) 
		return false;
	

	global $page;
?>

<form id="tag-form" method="post" action="<?php echo gf_get_topic_permalink($topic->topic_slug);?>/add-tags">
	<p>
		<input name="tag" type="text" id="tag" />
		<input type="hidden" name="id" value="<?php echo $topic->topic_id; ?>" />
		
		<?php wp_nonce_field( 'add-tag_' . $topic->topic_id ); ?>
		<input type="submit" name="submit" id="tagformsub" value="<?php echo esc_attr( $submit ); ?>" />
	</p>
</form>

<?php
}
function gf_add_topic_tags( $topic_id, $tags ) {
//another posrt of bb, because we do not want to affetc the behaviour of bp forums, otherwise adding a filter was enough
global $wp_taxonomy_object,$bp;
	$topic_id = (int) $topic_id;
	if ( !$topic = get_topic( $topic_id ) )
		return false;

	if ( !is_user_logged_in()||gf_is_user_banned($bp->loggedin_user->id) )
		return false;

	$user_id = $bp->loggedin_user->id;

	$tags = apply_filters( 'bb_add_topic_tags', $tags, $topic_id );

	if ( !is_array( $tags ) )
		$tags = explode(',', (string) $tags);

	$tt_ids = $wp_taxonomy_object->set_object_terms( $topic->topic_id, $tags, 'bb_topic_tag', array( 'append' => true, 'user_id' => $user_id ) );

	if ( is_array($tt_ids) ) {
		global $bbdb;
		$bbdb->query( $bbdb->prepare(
			"UPDATE $bbdb->topics SET tag_count = tag_count + %d WHERE topic_id = %d", count( $tt_ids ), $topic->topic_id
		) );
		wp_cache_delete( $topic->topic_id, 'bb_topic' );
		foreach ( $tt_ids as $tt_id )
			do_action('bb_tag_added', $tt_id, $user_id, $topic_id);
		return $tt_ids;
	}
	return false;
        }

        function gf_get_current_topic_permalink(){
            $topic=gf_get_current_topic();
            return gf_get_topic_permalink($topic->topic_slug);
        }

        function gf_get_current_topic(){
            global $bp;
            if(!($bp->current_component==$bp->gf->slug&&$bp->current_action=="topic"))
                    return false;
            $topic_slug = $bp->action_variables[0];
            $topic_id = gf_get_topic_id_from_slug( $topic_slug );
         $topic=gf_get_topic_details( $topic_id );
         return $topic;
        }
function gf_get_single_topic_title(){
if(!gf_is_topic())
    return;
$topic=gf_get_current_topic();
return $topic->topic_title;
}
 /* filter page title*/
    function gf_get_page_title($title){
        global $bp;
        if($bp->current_component!=$bp->gf->slug)
                return $title;
        //forum component
        
       if(gf_is_front()){
           $title=__("Forums","gf");
       if(gf_is_search())
           $title.="&#124;".__("Search","gf");
        else if(gf_is_tag())
         $title.=" &#124; ".__("Tags","gf")."&#124;".gf_get_single_tag_name();
        else if(gf_is_forum_topic_edit())
          $title.=" &#124; ".__("Edit Topic","gf")."&#124;".gf_get_single_topic_title();
        else if(gf_is_post_edit())
             $title.=" &#124; ".gf_get_single_topic_title()."&#124;".__("Edit Post","gf");
    else if(gf_is_topic())
         $title.=" &#124; ".__("Topic","gf")."&#124;".gf_get_single_topic_title();
    else if(gf_is_forum())
        $title.=" &#124; ".gf_get_forum_full_title("&#124;",$bp->gf->current_forum->forum_id);
    else if(gf_is_my_topics())
          $title.=" &#124; ".__("My Topics","gf");
    else if(gf_is_my_favorite())
          $title.=" &#124; ".__("My Favorites","gf");
     else if(gf_is_view("unreplied"))
          $title.=" &#124; ".__("Unreplied Topics","gf");
      else if(gf_is_view("popular"))
          $title.=" &#124; ".__("Popular Topics","gf");
    }
    else{//admin links
    $title=__("Forums","gf");
    if(gf_is_admin())
        $title.="&#124;".__("Admin","gf");
     //mutually exclusive pages
    if(gf_is_manage_forum())
        $title.=" &#124; ".__("Manage Forums","gf");
    else if(gf_is_forum_create())
        $title.=" &#124; ".__("Create Forum","gf");
    else if(gf_is_forum_edit())
        $title.=" &#124; ".__("Edit Forum","gf");
    else if(gf_is_forum_delete())
        $title.=" &#124; ".__("Delete Forum","gf");
    else if(gf_is_manage_users())
        $title.=" &#124; ".__("Manage Users","gf");

    }
    return apply_filters("gf_get_title",get_bloginfo('name')." &#124; ".$title);
    }
    add_filter("bp_page_title","gf_get_page_title",10);

    function gf_is_forum(){
          global $bp;
        if(gf_is_front()&&$bp->gf->current_forum)
                return true;
        return false;
    }
  function gf_get_forum_full_title($separator,$forum_id){
      global $bp;
     $forum=bb_get_forum($forum_id);
    if($parent=get_forum_parent($forum_id))
          return gf_get_forum_full_title($separator, $parent).$separator.$forum->forum_name;


   return $forum->forum_name;
  }


function gf_forums_search_form() {
	global $bp;

	$search_value = __( 'Search Forum...', 'gf' );
	if ( !empty( $_REQUEST['gfs'] ) )
	 	$search_value = $_REQUEST['gfs'];

?>
	<form action="<?php echo gf_get_home_url();?>" method="get" id="search-forums-form">
		<label><input type="text" name="gfs" id="forums_search" value="<?php echo attribute_escape($search_value) ?>" onblur="if (this.value == '') {this.value = 'Search Forum...';}" onfocus="if (this.value == 'Search Forum...') {this.value = '';}"  /></label>
		<input type="submit" id="gforums_search_submit" name="gforums_search_submit" value="<?php _e( 'Search', 'gf' ) ?>" />
	</form>
<?php
}



function gf_forums_tag_heat_map( $args = '' ) {
	$defaults = array(
		'smallest' => '10',
		'largest' => '42',
		'sizing' => 'px',
		'limit' => '50'
	);

	$r = wp_parse_args( $args, $defaults );
	extract( $r, EXTR_SKIP );

	bb_tag_heat_map( $smallest, $largest, $sizing, $limit );
}




        /** other tags*/





/*prepare forum fields*/

function gf_forum_prepare_fields( $forum_id = 0 ) {
	$forum_id = (int) $forum_id;
	if ( $forum_id && !$forum = bb_get_forum( $forum_id ) ) {
		return;
	}

	$forum_name = '';
	$forum_slug = '';
	$forum_description = '';
	$forum_position = '';

	if ( $forum_id ) {
		$forum_name = get_forum_name( $forum_id );
		$forum_slug = $forum->forum_slug;
		$forum_description = get_forum_description( $forum_id );
		$forum_position = get_forum_position( $forum_id );
		$legend = __( 'Edit Forum' );
		$submit = __( 'Save Changes' );
		$action = 'update';
	} else {
		$legend = __( 'Add Forum' );
		$submit = __( 'Add Forum' );
		$action = 'add';
	}

	$forum_options = array(
		'forum_name' => array(
			'title' => __( 'Name' ),
			'value' => $forum_name
		),
		'forum_slug' => array(
			'title' => __( 'Slug' ),
			'value' => $forum_slug
		),
		'forum_desc' => array(
			'title' => __( 'Description' ),
			'value' => $forum_description,
			'class' => 'long'
		),
		'forum_parent' => array(
			'title' => __( 'Parent' ),
			'type' => 'select',
			'options' => gf_get_forum_dropdown( array(
				'cut_branch' => $forum_id,
				'id' => 'forum_parent',
				'none' => true,
				'selected' => $forum_id ? get_forum_parent( $forum_id ) : 0,
				'disable_categories' => 0,
				'options_only' => true
			) )
		),
		'forum_order' => array(
			'title' => __( 'Position' ),
			'value' => $forum_position,
			'class' => 'short'
		),
		'forum_is_category' => array(
			'title' => __( 'Category' ),
			'type' => 'checkbox',
			'options' => array(
				1 => array(
					'label' => __( 'Make this forum a category' ),
					'value' => bb_get_forum_is_category( $forum_id ),
				)
			),
			'note' => __( 'Categories are forums where new topics cannot be created. Categories usually contain a group of sub-forums.' )
		)
	);

	if ( !$forum_id ) {
		unset( $forum_options['forum_slug'] );
		unset( $forum_options['forum_order'] );
	}
     return $forum_options;
}

//post to activity
function gf_record_activity( $args = '' ) {
	global $bp;

	if ( !function_exists( 'bp_activity_add' ) )
		return false;

	

	$defaults = array(
		'id' => false,
		'user_id' =>$bp->loggedin_user->id,
		'action' => '',
		'content' => '',
		'primary_link' => '',
		'component' => "gf",
		'type' => false,
		'item_id' => false,
		'secondary_item_id' => false,
		'recorded_time' => gmdate( "Y-m-d H:i:s" ),
		'hide_sitewide' => 0
	);

	$r = wp_parse_args( $args, $defaults );
	extract( $r );

	return bp_activity_add( array( 'id' => $id, 'user_id' => $user_id, 'action' => $action, 'content' => $content, 'primary_link' => $primary_link, 'component' => $component, 'type' => $type, 'item_id' => $item_id, 'secondary_item_id' => $secondary_item_id, 'recorded_time' => $recorded_time, 'hide_sitewide' => $hide_sitewide ) );
}
?>