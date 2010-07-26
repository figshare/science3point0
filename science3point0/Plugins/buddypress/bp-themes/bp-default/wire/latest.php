<?php get_header() ?>
<div id="container">
	<div id="contents">
		<div class="padder">

			<?php do_action( 'bp_before_member_home_content' ) ?>

			<div id="item-header">
				<?php locate_template( array( 'members/single/member-header.php' ), true ) ?>
			</div><!-- #item-header -->

			<div id="item-nav">
				<div class="item-list-tabs no-ajax" id="object-nav">
					<ul>
						<?php bp_get_displayed_user_nav() ?>

						<?php do_action( 'bp_members_profile_member_types' ) ?>
					</ul>
				</div>
			</div><!-- #item-nav -->

			<div id="item-body">

				<div class="item-list-tabs" id="subnav">
				<ul>
					<?php bp_get_options_nav() ?>
		
				</ul>
			</div><!-- .item-list-tabs -->

		<?php do_action( 'bp_before_member_blogs_content' ) ?>

		<div class="wire mywire">
			<?php do_action( 'bp_before_profile_wire_latest_content' ) ?>
					<?php if ( function_exists('bp_wire_get_post_list') ) : ?>
						<?php bp_wire_get_post_list( bp_current_user_id(), bp_word_or_name( __( "Your Wire", 'buddypress' ), __( "%s's Wire", 'buddypress' ), true, false ), bp_word_or_name( __( "No one has posted to your wire yet.", 'buddypress' ), __( "No one has posted to %s's wire yet.", 'buddypress' ), true, false ), bp_profile_wire_can_post() ) ?>
					<?php endif; ?>
					<?php do_action( 'bp_after_profile_wire_latest_content' ) ?>
		</div><!-- .blogs -->

	<?php do_action( 'bp_after_member_wire_content' ) ?>
	
	<?php do_action( 'bp_after_member_body' ) ?>

	</div><!-- #item-body -->

		<?php do_action( 'bp_after_member_home_content' ) ?>

  </div><!-- .padder -->
</div><!-- #content -->

	<?php locate_template( array( 'sidebar.php' ), true ) ?>
	<br class="clear" />
</div>
<?php get_footer() ?>