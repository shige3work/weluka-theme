<?php
/**
 * The template for displaying comments
 *
 * @package Weluka
 * @since Weluka Theme 00 1.0
 * 　
 */

if ( post_password_required() ) {
	return;
}

global $weluka_themename;
?>

<div id="comments" class="comments-area sp-pad">

	<?php if ( have_comments() ) : ?>
		<h3 class="comments-title">
		<?php
			printf( __( '%1$s thoughts on &ldquo;%2$s&rdquo;', $weluka_themename ), number_format_i18n( get_comments_number() ), get_the_title() );
		?>
		</h3>

		<?php //weluka_comment_nav(); ?>

		<ol class="comment-list">
			<?php
				wp_list_comments( array(
					'style'       => 'ol',
					'short_ping'  => true,
					'avatar_size' => 56,
				) );
			?>
		</ol><!-- .comment-list -->

		<?php weluka_comment_nav(); ?>

	<?php endif; // have_comments() ?>

	<?php
		// If comments are closed and there are comments, let's leave a little note, shall we?
		if ( ! comments_open() && get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) :
	?>
		<p class="no-comments"><?php _e( 'Comments are closed.', $weluka_themename ); ?></p>
	<?php endif; ?>

	<?php comment_form(); ?>

</div><!-- .comments-area -->
