<?php
/**
 * The archive conten template file.
 * blog tyle home or archive
 *
 * @package Weluka
 * @since Weluka Theme 00 1.0
 * 　
 */
global $weluka_themename;

if( is_404() ) : ?>

	<h2 class="page-title">
		<?php _e( 'Oops! That page can&rsquo;t be found.',  $weluka_themename ); ?>
	</h2>

	<div class="weluka-mgtop-xl">
		<?php _e( 'It looks like nothing was found at this location. Maybe try a search?', $weluka_themename ); ?>
	</div>
	<div class="weluka-mgtop-s"><?php get_search_form(); ?></div>
<?php elseif( is_search() ) : ?>
	<div class="weluka-mgtop-xl">
		<?php _e( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', $weluka_themename ); ?>
	</div>
	<div class="weluka-mgtop-s"><?php get_search_form(); ?></div>
<?php else: ?>
	<h2 class="page-title">
		<?php _e( 'Nothing Found.',  $weluka_themename ); ?>
	</h2>
<?php endif; ?>