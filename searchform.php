<?php
/**
 * The template for displaying search forms
 *
 * @package Weluka
 * @since Weluka Theme 00 1.0
 * 　
 */
global $weluka_themename;
?>
<form method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<input type="text" class="search-field form-control" value="<?php echo esc_attr( get_search_query() ); ?>" name="s" placeholder="<?php _e( 'Search ...', $weluka_themename ); ?>" />
	<input type="submit" class="search-submit" value="<?php echo esc_attr_x( 'Search', 'submit button', $weluka_themename ); ?>"/>
</form>
