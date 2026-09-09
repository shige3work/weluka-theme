<?php
/**
 * The template for weluka custom header 
 *
 * @package Weluka
 * @since Weluka Theme 00 1.0
 * 　
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<title><?php wp_title(); bloginfo('name'); ?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=2.0, user-scalable=1" />
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<?php
global $post, $welukaContainerClass, $welukaPageSetting, $welukaLayout, $welukaOnepageMode, $welukaRightSidebarNo;

$welukaLayout = get_weluka_theme_layout( WelukaThemeOptions::COMMON_LAYOUT );
$welukaPageSetting	= get_weluka_page_setting();
?>

<header id="weluka-main-header" class="weluka-custom-header">
<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); the_content(); endwhile; endif; ?>
</header>

<div id="main">

		<div class="<?php echo $welukaContainerClass; ?>">
			<div class="weluka-row clearfix">
<?php
	//responsive case top main-content
	switch( $welukaLayout ) {
		case WelukaThemeOptions::LAYOUT_TWO_COL_LEFT :
?>
	       	<div id="main-content" class="weluka-col weluka-col-md-9 right">
<?php		
			break;
		case WelukaThemeOptions::LAYOUT_THREE_COL :
?>
			<div class="weluka-col weluka-col-md-9">
				<div class="weluka-row clearfix">
            		<div id="main-content" class="weluka-col weluka-col-md-8 right">
<?php
			break;
		case WelukaThemeOptions::LAYOUT_TWO_COL_RIGHT :
?>
            <div id="main-content" class="weluka-col weluka-col-md-9">
<?php
			break;
		case WelukaThemeOptions::LAYOUT_ONE_COL :
			//case cpt sidebar edit mode => LAYOUT_TWO_COL_LEFT format
			if( !empty($post->post_type) && $post->post_type === Weluka::$settings['cpt_sd'] ) {
?>
			<div id="main-content" class="weluka-col weluka-col-md-9 right">
<?php
			} else {
?>
			<div id="main-content" class="weluka-col weluka-col-md-12">
<?php
			}
			break;
		default :
			break;
	}
?>

<div class="jumbo">
	<?php _e( 'MAIN CONTENTS AREA', $weluka_themename ); ?>
</div>

<?php get_footer(); ?>