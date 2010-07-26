<?php
header( 'Content-Type: text/xml; charset=UTF-8' );
echo '<' . '?xml version="1.0" encoding="UTF-8"?' . '>' . "\n";
bb_generator( 'comment' );
global $posts,$title,$link,$description;
?>
<rss version="2.0"
	xmlns:content="http://purl.org/rss/1.0/modules/content/"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	xmlns:atom="http://www.w3.org/2005/Atom">
	<channel>
		<title><?php echo $title; ?></title>
		<link><?php echo $link; ?></link>
		<description><?php echo $description; ?></description>
		<language><?php esc_html(get_bloginfo('language') ); ?></language>
		<pubDate><?php echo gmdate('D, d M Y H:i:s +0000'); ?></pubDate>
		<?php bb_generator( 'rss2' ); ?>
		
		<atom:link href="<?php echo $link_self; ?>" rel="self" type="application/rss+xml" />

<?php foreach ($posts as $post) : ?>
		<item>
                    <title><?php echo bp_core_get_user_displayname($post->poster_id); ?> <?php _e('on','gf')?> "<?php $topic=gf_get_topic_details($post->topic_id);  echo gf_get_the_topic_title($topic); ?>"</title>
			<link><?php echo gf_get_the_post_permalink($post); ?></link>
                        <pubDate><?php echo gf_get_the_topic_post_time_since($post) ?></pubDate>
			<dc:creator><?php echo bp_core_get_user_displayname($post->poster_id); ?></dc:creator>
			<guid isPermaLink="false"><?php echo gf_get_the_post_permalink($post); ?>></guid>
			<description><?php echo gf_get_the_topic_post_content($post); ?></description>
		</item>
<?php endforeach; ?>

	</channel>
</rss>