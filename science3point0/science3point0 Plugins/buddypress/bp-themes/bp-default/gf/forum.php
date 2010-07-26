<div class="bcomb"><?php echo gf_get_forum_bread_crumb();?></div>

<div id="discussions">
<?php if ( gf_has_forums() ) : ?>
<h2><?php _e('Forums','gf'); ?></h2>
<table id="forumlist">

<tr>
	<th><?php _e('Main Theme','gf'); ?></th>
	<th><?php _e('Topics','gf'); ?></th>
	<th><?php _e('Posts','gf'); ?></th>
</tr>
<?php while ( gf_forum() ) : ?>
<?php global $gf_forums_loop,$gf_current_forum;?>
<?php if (bb_get_forum_is_category()) : ?>
<tr<?php gf_forum_class('bb-category'); ?>>
	<td colspan="3"><?php gf_forum_pad( '<div class="nest">' ); ?><a href="<?php forum_permalink(); ?>"><?php forum_name(); ?></a>
	<br />
	<?php forum_description( array( 'before' => '<small> &#8211; ', 'after' => '</small>' ) ); ?><?php bb_forum_pad( '</div>' ); ?></td>
</tr>
<?php continue; endif; ?>
<tr<?php gf_forum_class(); ?>>
	<td><?php gf_forum_pad( '<div class="nest">' ); ?><a href="<?php gf_forum_permalink(); ?>"><?php gf_forum_name(); ?></a>
	<br />
	<?php gf_forum_description( array( 'before' => '<small> &#8211; ', 'after' => '</small>' ) ); ?><?php bb_forum_pad( '</div>' ); ?></td>
	<td class="num"><?php echo gf_get_forum_topic_count($gf_current_forum->forum_id); ?></td>
        <td class="num"><?php echo gf_get_forum_posts_count($gf_current_forum->forum_id); ?></td>
</tr>
<?php endwhile; ?>
</table>
<?php endif; // gf_forums() ?>

<?php locate_template(array("gf/topics-loop.php"),true);?>

<?php if(gf_current_user_can_create_topic())locate_template(array("gf/topic-new.php"),true);?>

<div id="view-links">
   <?php if(is_user_logged_in()):?>
    <a href="<?php echo gf_get_unreplied_topics_link();?>" > <?php _e("View all unreplied Topics","gf");?></a>
    <?php endif;?>

</div>
</div><!--- end of discussions-->