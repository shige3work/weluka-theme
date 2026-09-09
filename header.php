<?php
/**
 * The template for displaying the header
 *
 * Displays all of the head element and everything up until the "head".
 *
 * @package Weluka
 * @since Weluka Theme 00 1.0
 * @update
 * ver1.0.1
 * ver1.0.3
 * ver1.0.5
 * ver1.0.6
 * ver1.1
 * ver1.1.2
 * ver1.1.3
 * ver1.1.4
 * ver1.2.9.6
 */

//if( check_weluka_plugin() !== "" ) return;
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<title><?php if(isset( $_GET['mode'] ) && $_GET['mode'] === 'cp'){ echo 'Preview'; }else{ wp_title( '|', true, 'right' ); bloginfo('name'); } ?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=2.0, user-scalable=1" />
	<?php wp_head(); ?>
</head>
<?php
global $post, $welukaContainerClass, $welukaPageSetting, $welukaLayout, $welukaOnepageMode, $welukaRightSidebarNo, $welukaOutContainerType, $welukaThemeOptions;

if( isset( $_GET['mode'] ) && $_GET['mode'] === 'cp' ) {
	$welukaLayout = WelukaThemeOptions::LAYOUT_TWO_COL_RIGHT;
} else {
	//v1.0.6
	$_const = 'WelukaThemeOptions::ARCHIVE_LAYOUT';
	if ( defined ( $_const ) ) :
		if( is_home() || is_front_page() ) :
			$welukaLayout = get_weluka_theme_layout( WelukaThemeOptions::HOME_LAYOUT );
		elseif( is_archive() || is_search() ) :	//v1.0.6
			$welukaLayout = get_weluka_theme_layout( WelukaThemeOptions::ARCHIVE_LAYOUT );
		elseif( is_page() && ! is_attachment() ) :	//v1.0.6
			$welukaLayout = get_weluka_theme_layout( WelukaThemeOptions::PAGE_LAYOUT );
		elseif( is_single() && ! is_attachment() ) :	//v1.0.6
			$welukaLayout = get_weluka_theme_layout( WelukaThemeOptions::POST_LAYOUT );
		else :
			$welukaLayout = get_weluka_theme_layout( WelukaThemeOptions::COMMON_LAYOUT );
		endif;
	else :
		if( is_home() || is_front_page() ) :
			$welukaLayout = get_weluka_theme_layout( WelukaThemeOptions::HOME_LAYOUT );
		else :
			$welukaLayout = get_weluka_theme_layout( WelukaThemeOptions::COMMON_LAYOUT );
		endif;
	endif;
}

//v1.1 add modify
if( ! is_singular( array( Weluka::$settings['cpt_ft'], Weluka::$settings['cpt_sd'] ) ) ) {
	//v1.0.2
	$welukaPageSetting	= get_weluka_page_setting();
	$welukaOnepageMode = !empty( $welukaPageSetting['onepage'] ) ? $welukaPageSetting['onepage'] : 0;
} else {
	$welukaPageSetting	= array();
	$welukaOnepageMode = 0;
}
//v1.1 add modify end

//case top
if( is_home() || is_front_page() ) {
	if( !is_singular() ) {
		$welukaPageSetting	= array();
		$welukaOnepageMode	= 0;
	}
}
//v1.0.2 end

//ver1.0.1
$welukaOutContainerType = !empty( $welukaPageSetting['out_container_type'] ) ? $welukaPageSetting['out_container_type'] : '';
//ver1.0.1 addend
?>
<body <?php body_class(); ?>>
<?php
$_hideHeader = !empty( $welukaPageSetting['hide_hd'] ) ? $welukaPageSetting['hide_hd'] : 0;
if( ! $_hideHeader ) :
	$cptHd			= get_weluka_custom_header( $welukaPageSetting ); 

	$hdClass = "";
	if( $welukaOnepageMode ) { $hdClass = "weluka-parallax"; }
	if( ! empty( $cptHd ) ) { $hdClass .= " weluka-custom-header"; }
	
	//v1.1.2 add
/* v1.1.3 comment out functions.php move
	$_const = 'WelukaThemeOptions::HEAD_FIXED_NO_SCROLL_DISPLAY';
	if ( defined ( $_const ) ) {
		if( $welukaThemeOptions[WelukaThemeOptions::HEAD_FIXED_NO_SCROLL_DISPLAY] ) { $hdClass .= " weluka-hdfixed-noscroll"; }
	}
*/
?>
<header id="weluka-main-header" <?php if( $hdClass ) { echo 'class="' . $hdClass . '"'; } ?>>
<?php
	if( ! empty( $cptHd ) ) :
		//custom header
		echo $cptHd;
	else :
		//theme default header 
?>
<div class="wrapper sp-pad">
	<div class="<?php echo $welukaContainerClass; ?>">
    	<div class="weluka-row clearfix">
        	<div class="weluka-col weluka-col-md-6 weluka-col-sm-12 weluka-col-xs-12">
            	<?php
            		$menu = wp_nav_menu( array(
						'theme_location' => 'primary-menu',
						'container'		=> 'div',
						'container_id'	=> 'primary-nav',
						'container_class'	=> 'weluka-collapse ',
						'fallback_cb' => '',
						'menu_class' => 'nav navbar-nav no-rounded',
						'echo' => false
					) );
				?>
				<div id="main-nav" class="weluka-nav weluka-nav-bar-ham clearfix pull-left">
					<button type="button" class="navbar-toggle weluka-toggle no-rounded" data-target="#primary-nav">
						<span class="sr-only">Navigation</span><span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span>
					</button>
					<?php echo $menu; ?>
				</div>
				<div id="logowp" class="pull-left">
					<a href="<?php bloginfo('url'); ?>" title="<?php echo esc_attr(get_bloginfo('name')); ?>"><h1 class="logo">
						<?php
							$logo = get_weluka_site_logo(); 
						?>
						<?php if( $logo !== '' && ! isset( $_GET['mode'] ) ) : ?>
							<img src="<?php echo esc_url($logo); ?>" alt="<?php echo esc_attr(get_bloginfo('name')); ?>" class="img-responsive" />
						<?php else: ?>
							<?php esc_attr(bloginfo('name')); ?>
						<?php endif; ?>
					</h1></a>
				</div>
            </div>
            <div class="weluka-col weluka-col-md-6 weluka-col-sm-12  weluka-col-xs-12">
				<?php if ( is_active_sidebar( 'sidebar-hd-1' ) ) : ?>
				<div id="weluka-header-widget" >
						<?php dynamic_sidebar( 'sidebar-hd-1' ); ?>
            	</div>
				<?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php endif; //cptHd endif; ?>

</header>

<?php endif; //_hideHeader endif ?>

<div id="main">

<?php
//case page, single onepage (parallax) mode check
if( ! $welukaOnepageMode ) { //not onepage
?>
		<div id="weluka-notparallax" class="<?php echo $welukaContainerClass; ?>"> <?php //v1.1 id="weluka-notparallax" add ?>
			<div class="weluka-row clearfix">
<?php
	$hideMainSide = !empty( $welukaPageSetting['hide_sd_main'] ) ? $welukaPageSetting['hide_sd_main'] : 0;

	switch( $welukaLayout ) {
		case WelukaThemeOptions::LAYOUT_TWO_COL_LEFT :
		case WelukaThemeOptions::LAYOUT_TWO_COL_RIGHT :
			if( $hideMainSide ) { $welukaLayout = WelukaThemeOptions::LAYOUT_ONE_COL; }
			break;
		case WelukaThemeOptions::LAYOUT_THREE_COL :
			$hideSubSide = !empty( $welukaPageSetting['hide_sd_sub'] ) ? $welukaPageSetting['hide_sd_sub'] : 0;
			if( $hideMainSide && $hideSubSide ) { $welukaLayout = WelukaThemeOptions::LAYOUT_ONE_COL; }
			elseif( ! $hideMainSide && $hideSubSide ) { $welukaLayout = WelukaThemeOptions::LAYOUT_TWO_COL_LEFT; }
			elseif( $hideMainSide && ! $hideSubSide ) {
				$welukaLayout			= WelukaThemeOptions::LAYOUT_TWO_COL_RIGHT;
				$welukaRightSidebarNo	= '2';
			}
			break;		
		default :
			break;
	}

	//ver1.0.1 add
	$showMainSide = !empty( $welukaPageSetting['show_sd_main'] ) ? $welukaPageSetting['show_sd_main'] : 0;
	$showMainSidePlace = !empty( $welukaPageSetting['show_sd_main_place'] ) ? $welukaPageSetting['show_sd_main_place'] : 'right';
	$showSubSide = !empty( $welukaPageSetting['show_sd_sub'] ) ? $welukaPageSetting['show_sd_sub'] : 0;
	if( $showMainSide && $showSubSide ) { $welukaLayout = WelukaThemeOptions::LAYOUT_THREE_COL; }
	elseif( ( $showMainSide && ! $showSubSide ) || ( ! $showMainSide && $showSubSide ) ) {
		$welukaLayout = WelukaThemeOptions::LAYOUT_TWO_COL_RIGHT;
		if( $showMainSidePlace === 'left' ) { $welukaLayout = WelukaThemeOptions::LAYOUT_TWO_COL_LEFT; }
	}	
	//ver1.0.1 addend

	//responsive case top main-content
	switch( $welukaLayout ) {
		case WelukaThemeOptions::LAYOUT_TWO_COL_LEFT :
?>
	       	<div id="main-content" class="weluka-col weluka-col-md-9 right">
<?php		
			break;
		case WelukaThemeOptions::LAYOUT_THREE_COL :
?>
			<div id="three-col-left-wrapper" class="weluka-col weluka-col-md-9"> <?php //v1.0.5 id add ?>
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
} else { //onepageMode else
?>
	<div id="main-content" class="weluka-parallax">
<?php
} //end onepageMode endif

if( is_home() || is_front_page() ) :
else:
	$hide_breadcrumb = !empty( $welukaPageSetting['hide_breadcrumb'] ) ? $welukaPageSetting['hide_breadcrumb'] : 0; //v1.1.4 move

	// yoaest seo ver1.0.1 modify
	//v1.2.9.6 modify
	//$yoast_option = get_option( 'wpseo_internallinks' );
    $yoast_breadcrumb_enable = is_callable( 'WPSEO_Options::get' ) ? WPSEO_Options::get( 'breadcrumbs-enable' ) : false;
	$yoast_option            = get_option( 'wpseo_internallinks' ) ? get_option( 'wpseo_internallinks' ) : $yoast_breadcrumb_enable;
	if ( ! is_array( $yoast_option ) ) {
		unset( $yoast_option );
		$yoast_option = array( 'breadcrumbs-enable' => $yoast_breadcrumb_enable );
	}
	// v1.2.9.6 modify end

	if ( function_exists('yoast_breadcrumb') && $yoast_option['breadcrumbs-enable'] ) {
		//$hide_breadcrumb = !empty( $welukaPageSetting['hide_breadcrumb'] ) ? $welukaPageSetting['hide_breadcrumb'] : 0;
		if( ! $hide_breadcrumb ) {
	    	//if( $welukaOnepageMode ) {
			if( $welukaOutContainerType !== '' ) {
				echo '<div class="' . $welukaOutContainerType . '"><div class="weluka-row clearfix"><div class="weluka-col weluka-col-md-12">';
			}

			yoast_breadcrumb('<p id="yoaset_breadcrumbs" class="sp-pad">','</p>');
	
			//if( $welukaOnepageMode ) { echo '</div></div></div>'; }
			if( $welukaOutContainerType !== '' ) {  echo '</div></div></div>'; }
		}
	}
	//v1.1.4 Breadcrumb NavXT
	if( function_exists( 'bcn_display' ) ) {
		if( ! $hide_breadcrumb ) {
			if( $welukaOutContainerType !== '' ) {
				echo '<div class="' . $welukaOutContainerType . '"><div class="weluka-row clearfix"><div class="weluka-col weluka-col-md-12">';
			}
			
			echo '<p class="breadcrumbs_navxt sp-pad">';
        	bcn_display();
			echo '</p>';
			
			if( $welukaOutContainerType !== '' ) {  echo '</div></div></div>'; }
		}
    }
	//v1.1.4 addend
endif;
?>