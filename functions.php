<?php
/**
 * @package Weluka
 * @since Weluka Theme 00 1.0
 *
 */

// Theme Update Checker (GitHub automatic updates)
use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

if ( file_exists( get_template_directory() . '/vendor/plugin-update-checker/plugin-update-checker.php' ) ) {
	require_once get_template_directory() . '/vendor/plugin-update-checker/plugin-update-checker.php';
	$welukaThemeUpdateChecker = PucFactory::buildUpdateChecker(
		'https://github.com/shige3work/weluka-theme/',
		get_template_directory() . '/style.css',
		'weluka-theme-00'
	);
	$welukaThemeUpdateChecker->setBranch('main');
}

// config
define( 'WELUKA_THEME_VERSION', '1.4.1' );
define( 'WELUKA_PLUGIN_OFFICIAL_URL', 'https://github.com/shige3work/weluka-plugin' ); //@since 1.0
define( 'WELUKA_PTMP_POST_META_NAME', 'weluka_ptmp_meta' ); //@since 1.0

//for check update v1.2.5
$constDispHeader = 'WelukaTheme::PLACE_PAGE_HEADER';

//revision num
//define('WP_POST_REVISIONS', 15); //20before
/**
 * @since 1.0
 * @update
 * 1.0.2
 * 1.0.4
 */
add_action( 'after_setup_theme', 'weluka_setup_theme' );
if ( ! function_exists( 'weluka_setup_theme' ) ){
	function weluka_setup_theme() {
		global $weluka_themename, $weluka_theme_short, $welukaBuilder, $welukaTheme, $welukaThemeOptions, $welukaThemeColors, $welukaContainerClass,
		$welukaDefaultNoImage, $weluka_copyright, $constDispHeader;

		//error_reporting(0);

		$template_directory = get_template_directory();
		$weluka_themename = get_stylesheet();
		$weluka_theme_short = ""; //mb_strtolower( str_replace( array("-", " "), "", $weluka_themename ) );
		load_theme_textdomain( $weluka_themename, $template_directory . '/languages' );

		// wodpress support functions
		//title tag auto create wp4.1
		//add_theme_support( 'title-tag' );

		// Add default posts and comments RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );

		/*
	 	 * Switch default core markup for search form, comment form, and comments
	     * to output valid HTML5.
	     */
		add_theme_support( 'html5', array(
			'search-form', 'comment-form', 'comment-list', 'gallery', 'caption'
		) );
		
		// Post Thumbnails (eyecatch)
		if ( function_exists( 'add_image_size' ) ) {
			add_theme_support( 'post-thumbnails' );
			add_image_size( 'post-large', 1200, 675, true );
			add_image_size( 'post-medium', 800, 450, true );
			add_image_size( 'post-thumb', 400, 225, true );
		}
		
		if( check_weluka_plugin() === "" ){ 

			require_once( $template_directory . '/includes/class-theme-options.php' );
					
			$weluka					= Weluka::get_instance();

			$welukaBuilder			= WelukaBuilder::get_instance();
			$welukaTheme			= WelukaThemeOptions::get_instance();
			$welukaTheme->init();
			$weluka_theme_short		= $welukaTheme->get_option_name();
//v1.0.2
			//$welukaThemeOptions 	= WelukaAdminSettings::get_site_options( $weluka_theme_short );
			//$welukaThemeColors 	= WelukaAdminSettings::get_site_options( $weluka_theme_short . '_color' );
			$welukaThemeOptions 	= get_option( $weluka_theme_short );
			$welukaThemeColors 		= get_option( $weluka_theme_short . '_color' );
//v1.0.2
			$welukaContainerClass	= WelukaAdminSettings::get_options( WelukaAdminSettings::CONTAINER_TYPE );
			if( ! $welukaContainerClass ) {
				$welukaContainerClass = 'weluka-container';
			}
			
			//$welukaDefaultNoImage = $weluka->set_url( get_template_directory_uri() . '/images/noimage.gif' );
			$welukaDefaultNoImage = Weluka::$settings['noimage'];
			//admin
			add_action( 'admin_enqueue_scripts', 'weluka_admin_load_scripts_styles' );
			add_action( 'admin_init', 'weluka_page_templates_metabox' );
			add_action( 'save_post', 'weluka_page_template_save_meta', 10, 2 );
			add_action( 'wp_enqueue_scripts', 'weluka_theme_load_scripts_styles' );
			
			
			//v1.2.5 add
			if(defined ( $constDispHeader )){
				add_action('admin_menu','weluka_custom_parts_setting');
			}
			
			
			register_nav_menus(
				array(
					'primary-menu'	=> __( 'Primary Menu', $weluka_themename ),
					'footer-menu'	=> __( 'Footer Menu', $weluka_themename )
				)
			);
		
			weluka_register_sidebar();

			add_action( 'pre_get_posts', 'weluka_pre_home_query' );
			add_action( 'pre_get_posts', 'weluka_pre_archive_query' );

			$weluka_copyright = '© 2015 <a href="//www.weluka.me" target="_blank">weluka.</a>';

			add_editor_style('editor-style.css');
			//tinymce body class weluka plugin apply_filter
			add_filter('weluka_tinymce_body_class_args', 'weluka_set_tinymce_body_class_args', 10001);

			add_action( 'wp_head', 'weluka_theme_add_head', 999);
			
			add_filter('image_send_to_editor', 'weluka_image_send_to_editor', 10, 8);

			//ver1.0.1 add
			if( !empty( $welukaThemeOptions[WelukaThemeOptions::HEAD_FIXED_BUILDER_ENABLE] ) ) {
				add_action('wp_footer', 'weluka_theme_add_footer');
			}else{
				if( ! $weluka->is_active_builder() ) { add_action('wp_footer', 'weluka_theme_add_footer'); }
			}
		} //weluka endif
	}
}

/**
 * @since 1.0
 */
function check_weluka_plugin() {
	global $weluka_themename;
	$errmsg = "";
	if ( ! class_exists( 'Weluka', false ) ) {
		$errmsg = 
			'<div class="error"><p>' . sprintf( __('This theme does not work with Weluka plugin is not valid. Please Activate by introducing Weluka plugin. <a href="%s" target="_blank">Please click here for Weluka plugin.</a>', $weluka_themename ), WELUKA_PLUGIN_OFFICIAL_URL ) . '</p></div>';
		echo $errmsg;
	}
	return $errmsg; 
}

/**
 * @since 1.0.4
 * @update
 * v1.0.6
 * v1.1.2
 */
//function weluka_get_the_date( $echo = false ) {
function weluka_get_the_date( $echo = false, $format = '', $modify = '' ) {
	global $welukaThemeOptions;
	$ret = '';

	$const = 'WelukaThemeOptions::POST_DATE_DISPLAY_MODIFY';
	if ( class_exists( 'Weluka' ) && defined ( $const ) ) :
		$_format = '';
		$_modifyDate = 0;
		
		//v1.0.6
		if( $format !== '' ) { $_format = $format; }
		else {
			//v1.3 modify
			//if( $welukaThemeOptions[WelukaThemeOptions::POST_DATE_FORMAT] ) { $_format = $welukaThemeOptions[WelukaThemeOptions::POST_DATE_FORMAT]; }
			if( !empty( $welukaThemeOptions[WelukaThemeOptions::POST_DATE_FORMAT] ) && $welukaThemeOptions[WelukaThemeOptions::POST_DATE_FORMAT] ) { $_format = $welukaThemeOptions[WelukaThemeOptions::POST_DATE_FORMAT]; }
		}
		if( $modify !== '' ) { $_modifyDate = $modify; }
		else {
			if( !empty( $welukaThemeOptions[WelukaThemeOptions::POST_DATE_DISPLAY_MODIFY] )) {
				$_modifyDate = $welukaThemeOptions[WelukaThemeOptions::POST_DATE_DISPLAY_MODIFY];
			} 
		}
		// v 1.2.8 add for post random order view
		$_modifyDate = $_modifyDate == 2 ? 0 : $_modifyDate;
		
		/*
		if( $welukaThemeOptions[WelukaThemeOptions::POST_DATE_FORMAT] ) { $_format = $welukaThemeOptions[WelukaThemeOptions::POST_DATE_FORMAT]; }
		if( !empty( $welukaThemeOptions[WelukaThemeOptions::POST_DATE_DISPLAY_MODIFY] ) ) { $_modifyDate = $welukaThemeOptions[WelukaThemeOptions::POST_DATE_DISPLAY_MODIFY]; } 
		*/
		//v1.0.6 end
		$ret = Weluka::get_instance()->get_the_postdate( $_modifyDate, $_format, false );
	else :
		//$ret = get_the_date();
		$ret = get_post_time();
	endif;
	
	if( $echo ) { echo $ret; } else { return $ret; }
}

if ( class_exists( 'Weluka' ) ) :

/**
 * @since 1.0.1
 */
//function weluka_tagcloud($tags, $onepage = false, $class="weluka-mgtop-s", $echo = true) {
function weluka_tagcloud($tags, $outct = '', $class="weluka-mgtop-s", $echo = true) {
	$ret = '';
	if ($tags) {
		//if( $onepage ) {
		if( $outct !== '' ) {
			$ret .= '<div class="' . $outct . '"><div class="weluka-row clearfix"><div class="weluka-col weluka-col-md-12">';
		}
        $ret .= '<div class="tagcloud ' . $class .'">';
		foreach($tags as $tag) :
			$tag_link = get_tag_link($tag->term_id);
			$ret .= '<a href="' . esc_url( $tag_link ) . '" title="' . esc_attr($tag->name) . '">' . $tag->name . '</a>';
		endforeach;
		$ret .= '</div>';
		if( $outct ) { $ret .= '</div></div></div>'; }
    }
	
	if( $echo ) {
		echo $ret;
	}else{
		return $ret;
	}
}

/**
 * @since 1.0
 */
function weluka_set_tinymce_body_class_args($body_class) {
	global $welukaContainerClass;
	$body_class = $welukaContainerClass;
	return $body_class;
}

/**
 * @since 1.0
 */
function weluka_admin_load_scripts_styles( $hook_suffix ) {
	global $weluka_theme_short, $weluka;
	if ( in_array($hook_suffix, array('post.php','post-new.php')) ) {
		wp_enqueue_script( $weluka_theme_short . '-script', $weluka->set_url( get_template_directory_uri() ) . '/js/admin_script.js', array('jquery'), '1.0', true);	
	}
}

/**
 * @since 1.0
 */
function weluka_theme_load_scripts_styles(){
	global $weluka_theme_short, $weluka;
	$template_url = get_template_directory_uri();

	wp_enqueue_style( $weluka_theme_short . '-style', $weluka->set_url( get_stylesheet_uri() ) );

	$colorKey = get_weluka_theme_color( WelukaThemeOptions::COLOR_TYPE );
	if( isset( $_GET['mode'] ) && $_GET['mode'] === 'cp' ) {
		$colorKey = isset( $_POST[WelukaThemeOptions::COLOR_TYPE] ) ? $_POST[WelukaThemeOptions::COLOR_TYPE] : 'default';
	}

	if( !empty( $colorKey ) && $colorKey !== 'default' ) {
		wp_enqueue_style( $weluka_theme_short . '-style-' . $colorKey, $weluka->set_url( $template_url ) . '/style-' . $colorKey . '.css' );
	}
}

/** 
 * @since 1.0
 * @update
 * ver1.2.3
 */
function weluka_theme_add_head() {
	global $weluka, $weluka_theme_short, $welukaThemeOptions;
	if( isset( $_GET['mode'] ) && $_GET['mode'] === 'cp' ) :
		echo '<meta name="robots" content="noindex,nofollow" />';
		$colorData = $_POST;
	else:
		$colorData = get_weluka_theme_color( WelukaThemeOptions::COLOR_ARRAY );
    endif;

	$customCss = "";
	if( !empty( $welukaThemeOptions[WelukaThemeOptions::CUSTOM_CSS] ) ) { $customCss = wp_unslash( $welukaThemeOptions[WelukaThemeOptions::CUSTOM_CSS] ); }

	if( !empty( $colorData ) ) :
		$style = '<style type="text/css">';

		$body = '';
		$panel = '';
		if( $colorData['body_bg_color'] !== '' ) {
			$body .= 'background-color:' . $colorData['body_bg_color'] . ';';			
		}
		if( $colorData['body_text_color'] !== '' ) {
			$body .= 'color:' . $colorData['body_text_color'] . ';';
		}
		if( $body !== '' ) $style .= 'body { ' . $body . ' }';

		//sitename
		if( $colorData['sitename_color'] !== '' ) {
			$style .= '.logo { color:' . $colorData['sitename_color'] . ' !important; }';			
		}

		//block
		$block = "";
		if( $colorData['block_bg_color'] !== '' ) {
			$block .= 'background-color:' . $colorData['block_bg_color'] . ';';
		}
		if( $colorData['block_border_color'] !== '' ) {
			$block .= 'border-color:' . $colorData['block_border_color'] . ';';
		}
		if( $block !== '' ) {
			$style .= '#main-content article.entry,' .
					'.archive-list .weluka-list-row.medialeft,' .
					'.weluka-sidebar .weluka-list-row.medialeft,' .
					'.archive-list .weluka-list-row.mediaright,' .
					'.weluka-sidebar .weluka-list-row.mediaright,' .
					'.archive-list .weluka-list-row [class*="weluka-col-"] .wrap,' .
					'.weluka-sidebar .weluka-list-row [class*="weluka-col-"] .wrap,' .
					'.weluka-sidebar .textwidget,' .
					'.weluka-sidebar .widget > ul,' .
					'.weluka-sidebar .widget ul.menu, '.
					'.weluka-sidebar .widget .tagcloud,' .
					'.page-title,'.
					'.weluka-sidebar .widgettitle,' .
					'.weluka-sidebar .weluka-text,' .
					/*'.weluka-sidebar #calendar_wrap,' . */
					'#comments {' . $block . ' }';
		}

		//heading
		if( $colorData['heading_text_color'] !== '' ) {
			$style .= 'h1,h2,h3,h4,h5,h6,.page-title { color:' . $colorData['heading_text_color'] . '; }';
			$style .= '.page-title, blockquote { border-color:' . $colorData['heading_text_color'] . '; }';
			$style .= '#wp-calendar thead th { background-color:' . $colorData['heading_text_color'] . '; }';
		}
		
		//link
		if( $colorData['link_color'] !== '' ) {
			//v1.2.3 add #weluka-main-header #weluka-main-footer
			$style .= 'a, #weluka-main-header a, #weluka-main-footer a { color:' . $colorData['link_color'] . '; }';
		}
		if( $colorData['link_hover_color'] !== '' ) {
			//v1.2.3 #weluka-main-header #weluka-main-footer add
			$style .= 'a:hover, #weluka-main-header a:hover, #weluka-main-footer a:hover, a:focus, #weluka-main-header a:focus, #weluka-main-footer a:focus { color:' . $colorData['link_hover_color'] . '; }';
		}

		//header
		$header = '';
		if( $colorData['hd_bg_color'] !== '' ) {
			$header .= 'background-color:' . $colorData['hd_bg_color'] . ';';			
		}
		if( $colorData['hd_text_color'] !== '' ) {
			$header .= 'color:' . $colorData['hd_text_color'] . ';';
		}
		//header border
		if( $colorData['hd_border_color'] !== '' ) {
			$header .= 'border-bottom-color:' . $colorData['hd_border_color'] . ';';
		}
		if( $header !== '' ) $style .= '#weluka-main-header { ' . $header . '; }';

		//header heading
		if( $colorData['hd_heading_text_color'] !== '' ) {
			$style .= '#weluka-main-header h1, #weluka-main-header h2, #weluka-main-header h3, #weluka-main-header h4, #weluka-main-header h5, #weluka-main-header h6 { color:' . $colorData['hd_heading_text_color'] . '; }';
		}
		//header link
		if( $colorData['hd_link_color'] !== '' ) {
			$style .= '#weluka-main-header a { color:' . $colorData['hd_link_color'] . '; }';
		}
		if( $colorData['hd_link_hover_color'] !== '' ) {
			$style .= '#weluka-main-header a:hover, #weluka-main-header a:focus { color:' . $colorData['hd_link_hover_color'] . '; }';
		}
		//main navi
		if( $colorData['mainnav_color'] !== '' ) {
			$style .= '#main-nav button, #main-nav .nav { background-color:' .  $colorData['mainnav_color'] . '; }';
		}
		//main navi border
		if( $colorData['mainnav_border_color'] !== '' ) {
			$style .= '#main-nav button, #main-nav .nav { border-color:' . $colorData['mainnav_border_color'] . '; }';
			$style .= '#main-nav button:hover, #main-nav button:focus, #main-nav a:hover, #main-nav a:focus, #main-nav .open > a, #main-nav .open a:hover, #main-nav .open a:focus { background-color:' .  $colorData['mainnav_border_color'] . '; }';
		}
		//main navi text
		if( $colorData['mainnav_text_color'] !== '' ) {
			$style .= '#main-nav a, #main-nav a:hover, #main-nav a:focus, #main-nav .open > a, #main-nav .open a:hover, #main-nav .open a:focus { color:' . $colorData['mainnav_text_color'] . '; }';
			$style .= '#main-nav button .icon-bar { background-color:' . $colorData['mainnav_text_color'] . '; }';
		}

		//footer
		$footer = '';
		if( $colorData['ft_bg_color'] !== '' ) {
			$footer .= 'background-color:' . $colorData['ft_bg_color'] . ';';			
		}
		if( $colorData['ft_text_color'] !== '' ) {
			$footer .= 'color:' . $colorData['ft_text_color'] . ';';
		}
		//footer border
		if( $colorData['ft_border_color'] !== '' ) {
			$footer .= 'border-top-color:' . $colorData['ft_border_color'] . ';';
		}
		if( $footer !== '' ) $style .= '#weluka-main-footer { ' . $footer . '; }';

		//footer heading
		if( $colorData['ft_heading_text_color'] !== '' ) {
			$style .= '#weluka-main-footer h1, #weluka-main-footer h2, #weluka-main-footer h3, #weluka-main-footer h4, #weluka-main-footer h5, #weluka-main-footer h6 { color:' . $colorData['ft_heading_text_color'] . '; }';
		}
		//footer link
		if( $colorData['ft_link_color'] !== '' ) {
			$style .= '#weluka-main-footer a { color:' . $colorData['ft_link_color'] . '; }';
		}
		if( $colorData['ft_link_hover_color'] !== '' ) {
			$style .= '#weluka-main-footer a:hover, #weluka-main-footer a:focus { color:' . $colorData['ft_link_hover_color'] . '; }';
		}
		
		//primary
		if( $colorData['primary_color'] !== '' ) {
			$style .= '.weluka-text-primary { color:' . $colorData['primary_color'] . '; }';
			$style .= '.weluka-bg-primary { background-color:' . $colorData['primary_color'] . '; }';
			$style .= '.label-primary { background-color:' . $colorData['primary_color'] . '; }';

			//button
			$style .= '.weluka-btn-primary, .weluka-btn-primary .badge, .weluka-btn-primary.disabled, .weluka-btn-primary[disabled], fieldset[disabled] .weluka-btn-primary, .weluka-btn-primary.disabled:hover, .weluka-btn-primary[disabled]:hover, fieldset[disabled] .weluka-btn-primary:hover, .weluka-btn-primary.disabled:focus, .weluka-btn-primary[disabled]:focus, fieldset[disabled] .weluka-btn-primary:focus, .weluka-btn-primary.disabled.focus, .weluka-btn-primary[disabled].focus, fieldset[disabled] .weluka-btn-primary.focus, .weluka-btn-primary.disabled:active, .weluka-btn-primary[disabled]:active, fieldset[disabled] .weluka-btn-primary:active, .weluka-btn-primary.disabled.active, .weluka-btn-primary[disabled].active, fieldset[disabled] .weluka-btn-primary.active { background-color:' . $colorData['primary_color'] . '; }';

			$style .= '.weluka-btn-primary:hover, .weluka-btn-primary:focus, .weluka-btn-primary.focus, .weluka-btn-primary:active, .weluka-btn-primary.active, .open > .dropdown-toggle.weluka-btn-primary { border-color:' .  $colorData['primary_color'] . ' !important; }';

			$style .= '.weluka-btn-link, .weluka-btn-link .badge { color:' . $colorData['primary_color'] . '; }';

			//pagination comment link
			$style .= '.pagination > .active > a, .pagination > .active > span, .pagination > .active > a:hover, .pagination > .active > span:hover, .pagination > .active > a:focus, .pagination > .active > span:focus { background-color:' . $colorData['primary_color'] . ';border-color:' . $colorData['primary_color'] . '; }';
			$style .= '.pagination > li > a, .pagination > li > span { color:' . $colorData['primary_color'] . '; }';
			$style .= '.pagination > li > a:hover, .pagination > li > span:hover, .pagination > li > a:focus, .pagination > li > span:focus { color:' . $colorData['primary_color'] . '; }';

			$style .= '.weluka-pagination .current, .comment-form input[type="submit"], .weluka-pagination a:hover, .post-nav-link a:hover, .comment-navigation a:hover { background-color:' . $colorData['primary_color'] . '; }';
			$style .= '.weluka-pagination span, .weluka-pagination a, .post-nav-link a, .comment-navigation a { color:' . $colorData['primary_color'] . '; }';

			//nav
			$style .= '.weluka-navbar-darkblue, .weluka-navbar-darkblue .dropdown-menu, .weluka-nav-bar-ham .nav.weluka-navbar-darkblue, .weluka-nav-bar-ham .weluka-toggle.weluka-navbar-darkblue { background-color:' . $colorData['primary_color'] . '; }';

			//accordion panel
			$style .= '.panel-primary > .panel-heading { background-color:' . $colorData['primary_color'] . '; }';
			$style .= '.panel-primary > .panel-heading .badge { color:' . $colorData['primary_color'] . '; }';

			//tab
			$style .= '.nav-tabs a, .nav-pills > li > a { color:' . $colorData['primary_color'] . '; }';
			$style .= '.nav-pills > li.active > a, .nav-pills > li.active > a:hover, .nav-pills > li.active > a:focus { background-color:' . $colorData['primary_color'] . '; }';

			//list-gourp
			$style .= '.list-group-item.active, .list-group-item.active:hover, .list-group-item.active:focus { background-color:' . $colorData['primary_color'] . ';border-color:' . $colorData['primary_color'] . '; }';
			
			//tagcloud
			$style .= '.tagcloud a { background-color:' . $colorData['primary_color'] . '; }';
		}
		//primary border
		if( $colorData['primary_border_color'] !== '' ) {
			//bg
			$style .= 'a.weluka-bg-primary:hover { background-color:' . $colorData['primary_border_color'] . '; }';
			//button
			$style .= '.weluka-btn-primary, .weluka-btn-primary.disabled, .weluka-btn-primary[disabled], fieldset[disabled] .weluka-btn-primary, .weluka-btn-primary.disabled:hover, .weluka-btn-primary[disabled]:hover, fieldset[disabled] .weluka-btn-primary:hover, .weluka-btn-primary.disabled:focus, .weluka-btn-primary[disabled]:focus, fieldset[disabled] .weluka-btn-primary:focus, .weluka-btn-primary.disabled.focus, .weluka-btn-primary[disabled].focus, fieldset[disabled] .weluka-btn-primary.focus, .weluka-btn-primary.disabled:active, .weluka-btn-primary[disabled]:active, fieldset[disabled] .weluka-btn-primary:active, .weluka-btn-primary.disabled.active, .weluka-btn-primary[disabled].active, fieldset[disabled] .weluka-btn-primary.active { border-color:' . $colorData['primary_border_color'] . ' !important; }';
			$style .= '.weluka-btn-primary:hover, .weluka-btn-primary:focus, .weluka-btn-primary.focus, .weluka-btn-primary:active, .weluka-btn-primary.active, .open > .dropdown-toggle.weluka-btn-primary { background-color:' . $colorData['primary_border_color'] . '; }';
			
			$style .= '.weluka-btn-link:hover, .weluka-btn-link:focus { color:' . $colorData['primary_border_color'] . '; }';

			//nav
			$style .= '.weluka-navbar-darkblue, .weluka-navbar-darkblue .dropdown-menu, .weluka-nav-bar-ham .nav.weluka-navbar-darkblue, .weluka-navbar-darkblue a:hover, .weluka-navbar-darkblue a:focus, .weluka-navbar-darkblue .open > a, .weluka-navbar-darkblue .open a:hover, .weluka-navbar-darkblue .open a:focus, .weluka-navbar-darkblue .dropdown-menu a:hover, .weluka-navbar-darkblue .dropdown-menu a:focus, .weluka-navbar-darkblue .navbar-toggle, .weluka-nav-bar-ham .weluka-toggle.weluka-navbar-darkblue, .weluka-nav-bar-ham .weluka-toggle.weluka-navbar-darkblue, .weluka-navbar-darkblue .navbar-collapse, .weluka-navbar-darkblue .navbar-form, .weluka-nav-bar-v.weluka-navbar-darkblue .nav, .weluka-nav-bar-v.weluka-navbar-darkblue .nav li, .weluka-nav-bar-ham .nav.weluka-navbar-darkblue { border-color:' . $colorData['primary_border_color'] . ' !important; }';
			$style .= '.weluka-navbar-darkblue a:hover, .weluka-navbar-darkblue a:focus, .weluka-navbar-darkblue .open > a, .weluka-navbar-darkblue .open a:hover, .weluka-navbar-darkblue .open a:focus, .weluka-navbar-darkblue .dropdown-menu a:hover, .weluka-navbar-darkblue .dropdown-menu a:focus, .weluka-navbar-darkblue .navbar-toggle:hover, .weluka-navbar-darkblue .navbar-toggle:focus, .weluka-nav-bar-ham .weluka-toggle.weluka-navbar-darkblue:hover, .weluka-nav-bar-ham .weluka-toggle.weluka-navbar-darkblue:focus { background-color:' . $colorData['primary_border_color'] . '; }';
			
			//accordion panel
			$style .= '.panel-primary > .panel-heading { border-color:' . $colorData['primary_border_color'] . '; }';
			$style .= '.panel-primary > .panel-heading + .panel-collapse > .panel-body { border-top-color:' . $colorData['primary_border_color'] . '; }';
			$style .= '.panel-primary > .panel-footer + .panel-collapse > .panel-body { border-bottom-color:' . $colorData['primary_border_color'] . '; }';
			$style .= '.panel-primary { border-color:' . $colorData['primary_border_color'] . '; }';

			//list-group
			$style .= '.list-group-item.active, .list-group-item.active:hover, .list-group-item.active:focus { background-color:' . $colorData['primary_border_color'] . ';border-color:' . $colorData['primary_border_color'] . '; }';

			//comment
			$style .= '.comment-form input[type="submit"]:hover, .comment-form input[type="submit"]:focus { background:' . $colorData['primary_border_color'] . '; }';
			//tag cloud
			$style .= '.tagcloud a:hover { background-color:' . $colorData['primary_border_color'] . '; }';
		}
		//primary text
		if( $colorData['primary_text_color'] !== '' ) {
			$style .= '.weluka-bg-primary { color:' . $colorData['primary_text_color'] . '; }';
			$style .= '.label-primary { color:' . $colorData['primary_text_color'] . '; }';

			//button, a.bg
			$style .= '.weluka-btn-primary, .weluka-btn-primary:hover, .weluka-btn-primary:focus, .weluka-btn-primary.focus, .weluka-btn-primary:active, .weluka-btn-primary.active, .open > .dropdown-toggle.weluka-btn-primary, a.weluka-bg-primary:hover { color:' . $colorData['primary_text_color'] . ' !important; }';
			//$style .= '.weluka-btn-link { color:' . $colorData['primary_text_color'] . '; }';

			//nav
			$style .= '.weluka-navbar-darkblue, .weluka-navbar-darkblue a, .weluka-navbar-darkblue .dropdown-menu, .weluka-nav-bar-ham .nav.weluka-navbar-darkblue { color: ' . $colorData['primary_text_color'] . ' !important; }';
			$style .= '.weluka-navbar-darkblue .navbar-toggle .icon-bar, .weluka-nav-bar-ham .weluka-toggle.weluka-navbar-darkblue .icon-bar { background-color:' . $colorData['primary_text_color'] . '; }';

			//accordion panel
			$style .= '.panel-primary > .panel-heading { color:' . $colorData['primary_text_color'] . '; }';

			//list group
			$style .= '.list-group-item.active, .list-group-item.active:hover, .list-group-item.active:focus { color:' . $colorData['primary_text_color'] . '; }';
			
			//tagcloud
			$style .= '.tagcloud a, .tagcloud a:hover { color:' . $colorData['primary_text_color'] . '; }';			
		}

		//success
		if( $colorData['success_color'] !== '' ) {
			$style .= '.weluka-text-success { color:' . $colorData['success_color'] . '; }';
			$style .= '.weluka-bg-success { background-color:' . $colorData['success_color'] . '; }';
			$style .= '.label-success { background-color:' . $colorData['success_color'] . '; }';

			//button
			$style .= '.weluka-btn-success, .weluka-btn-success .badge, .weluka-btn-success.disabled, .weluka-btn-success[disabled], fieldset[disabled] .weluka-btn-success, .weluka-btn-success.disabled:hover, .weluka-btn-success[disabled]:hover, fieldset[disabled] .weluka-btn-success:hover, .weluka-btn-success.disabled:focus, .weluka-btn-success[disabled]:focus, fieldset[disabled] .weluka-btn-success:focus, .weluka-btn-success.disabled.focus, .weluka-btn-success[disabled].focus, fieldset[disabled] .weluka-btn-success.focus, .weluka-btn-success.disabled:active, .weluka-btn-success[disabled]:active, fieldset[disabled] .weluka-btn-success:active, .weluka-btn-success.disabled.active, .weluka-btn-success[disabled].active, fieldset[disabled] .weluka-btn-success.active { background-color:' . $colorData['success_color'] . '; }';

			$style .= '.weluka-btn-success:hover, .weluka-btn-success:focus, .weluka-btn-success.focus, .weluka-btn-success:active, .weluka-btn-success.active, .open > .dropdown-toggle.weluka-btn-success { border-color:' .  $colorData['success_color'] . ' !important; }';

			//nav
			$style .= '.weluka-navbar-green, .weluka-navbar-green .dropdown-menu, .weluka-nav-bar-ham .nav.weluka-navbar-green, .weluka-nav-bar-ham .weluka-toggle.weluka-navbar-green { background-color:' . $colorData['success_color'] . '; }';

			//accordion panel
			$style .= '.panel-success > .panel-heading { background-color:' . $colorData['success_color'] . '; }';
			$style .= '.panel-success > .panel-heading .badge { color:' . $colorData['success_color'] . '; }';
			
			//alert
			$style .= '.alert-success { background-color:' . $colorData['success_color'] . '; }';
			
			//list-gourp
			$style .= '.list-group-item-success { background-color:' . $colorData['success_color'] . '; }';
			
			//table
			$style .= '.table > thead > tr > td.success, .table > tbody > tr > td.success, .table > tfoot > tr > td.success, .table > thead > tr > th.success, .table > tbody > tr > th.success, .table > tfoot > tr > th.success, .table > thead > tr.success > td, .table > tbody > tr.success > td, .table > tfoot > tr.success > td, .table > thead > tr.success > th, .table > tbody > tr.success > th, .table > tfoot > tr.success > th { background-color:' .  $colorData['success_color'] . '; }';
		}
		//success border
		if( $colorData['success_border_color'] !== '' ) {
			//bg
			$style .= 'a.weluka-bg-success:hover { background-color:' . $colorData['success_border_color'] . '; }';

			//button
			$style .= '.weluka-btn-success, .weluka-btn-success.disabled, .weluka-btn-success[disabled], fieldset[disabled] .weluka-btn-success, .weluka-btn-success.disabled:hover, .weluka-btn-success[disabled]:hover, fieldset[disabled] .weluka-btn-success:hover, .weluka-btn-success.disabled:focus, .weluka-btn-success[disabled]:focus, fieldset[disabled] .weluka-btn-success:focus, .weluka-btn-success.disabled.focus, .weluka-btn-success[disabled].focus, fieldset[disabled] .weluka-btn-success.focus, .weluka-btn-success.disabled:active, .weluka-btn-success[disabled]:active, fieldset[disabled] .weluka-btn-success:active, .weluka-btn-success.disabled.active, .weluka-btn-success[disabled].active, fieldset[disabled] .weluka-btn-success.active { border-color:' . $colorData['success_border_color'] . ' !important; }';
			$style .= '.weluka-btn-success:hover, .weluka-btn-success:focus, .weluka-btn-success.focus, .weluka-btn-success:active, .weluka-btn-success.active, .open > .dropdown-toggle.weluka-btn-success { background-color:' . $colorData['success_border_color'] . '; }';
			
			//nav
			$style .= '.weluka-navbar-green, .weluka-navbar-green .dropdown-menu, .weluka-nav-bar-ham .nav.weluka-navbar-green, .weluka-navbar-green a:hover, .weluka-navbar-green a:focus, .weluka-navbar-green .open > a, .weluka-navbar-green .open a:hover, .weluka-navbar-green .open a:focus, .weluka-navbar-green .dropdown-menu a:hover, .weluka-navbar-green .dropdown-menu a:focus, .weluka-navbar-green .navbar-toggle, .weluka-nav-bar-ham .weluka-toggle.weluka-navbar-green, .weluka-nav-bar-ham .weluka-toggle.weluka-navbar-green, .weluka-navbar-green .navbar-collapse, .weluka-navbar-green .navbar-form, .weluka-nav-bar-v.weluka-navbar-green .nav, .weluka-nav-bar-v.weluka-navbar-green .nav li, .weluka-nav-bar-ham .nav.weluka-navbar-green { border-color:' . $colorData['success_border_color'] . ' !important; }';
			$style .= '.weluka-navbar-green a:hover, .weluka-navbar-green a:focus, .weluka-navbar-green .open > a, .weluka-navbar-green .open a:hover, .weluka-navbar-green .open a:focus, .weluka-navbar-green .dropdown-menu a:hover, .weluka-navbar-green .dropdown-menu a:focus, .weluka-navbar-green .navbar-toggle:hover, .weluka-navbar-green .navbar-toggle:focus, .weluka-nav-bar-ham .weluka-toggle.weluka-navbar-green:hover, .weluka-nav-bar-ham .weluka-toggle.weluka-navbar-green:focus { background-color:' . $colorData['success_border_color'] . '; }';
			
			//accordion panel
			$style .= '.panel-success > .panel-heading { border-color:' . $colorData['success_border_color'] . '; }';
			$style .= '.panel-success > .panel-heading + .panel-collapse > .panel-body { border-top-color:' . $colorData['success_border_color'] . '; }';
			$style .= '.panel-success > .panel-footer + .panel-collapse > .panel-body { border-bottom-color:' . $colorData['success_border_color'] . '; }';
			$style .= '.panel-success { border-color:' . $colorData['success_border_color'] . '; }';

			//alert
			$style .= '.alert-success { border-color:' . $colorData['success_border_color'] . '; }';

			//list-group
			$style .= 'a.list-group-item-success:hover, a.list-group-item-success:focus { background-color:' . $colorData['success_border_color'] . '; }';
			$style .= 'a.list-group-item-success.active, a.list-group-item-success.active:hover, a.list-group-item-success.active:focus { background-color:' . $colorData['success_border_color'] . ';border-color:' . $colorData['success_border_color'] . '; }';

			//table
			$style .= '.table-hover > tbody > tr > td.success:hover, .table-hover > tbody > tr > th.success:hover, .table-hover > tbody > tr.success:hover > td, .table-hover > tbody > tr:hover > .success, .table-hover > tbody > tr.success:hover > th { background-color:' . $colorData['success_border_color'] . '; }';
		}
		//success text
		if( $colorData['success_text_color'] !== '' ) {
			$style .= '.weluka-bg-success { color:' . $colorData['success_text_color'] . '; }';
			$style .= '.label-success { color:' . $colorData['success_text_color'] . '; }';

			//button, a.bg
			$style .= '.weluka-btn-success, .weluka-btn-success:hover, .weluka-btn-success:focus, .weluka-btn-success.focus, .weluka-btn-success:active, .weluka-btn-success.active, .open > .dropdown-toggle.weluka-btn-success, a.weluka-bg-success:hover { color:' . $colorData['success_text_color'] . ' !important; }';

			//nav
			$style .= '.weluka-navbar-green, .weluka-navbar-green a, .weluka-navbar-green .dropdown-menu, .weluka-nav-bar-ham .nav.weluka-navbar-green { color: ' . $colorData['success_text_color'] . ' !important; }';
			$style .= '.weluka-navbar-green .navbar-toggle .icon-bar, .weluka-nav-bar-ham .weluka-toggle.weluka-navbar-green .icon-bar { background-color:' . $colorData['success_text_color'] . '; }';

			//accordion panel
			$style .= '.panel-success > .panel-heading { color:' . $colorData['success_text_color'] . '; }';

			//alert
			$style .= '.alert-success { color:' . $colorData['success_text_color'] . '; }';

			//list group
			$style .= '.list-group-item-success, a.list-group-item-success, a.list-group-item-success:hover, a.list-group-item-success:focus, a.list-group-item-success.active, a.list-group-item-success.active:hover, a.list-group-item-success.active:focus { color:' . $colorData['success_text_color'] . '; }';

			//table
			$style .= '.table > thead > tr > td.success, .table > tbody > tr > td.success, .table > tfoot > tr > td.success, .table > thead > tr > th.success, .table > tbody > tr > th.success, .table > tfoot > tr > th.success, .table > thead > tr.success > td, .table > tbody > tr.success > td, .table > tfoot > tr.success > td, .table > thead > tr.success > th, .table > tbody > tr.success > th, .table > tfoot > tr.success > th, .table-hover > tbody > tr > td.success:hover, .table-hover > tbody > tr > th.success:hover, .table-hover > tbody > tr.success:hover > td, .table-hover > tbody > tr:hover > .success, .table-hover > tbody > tr.success:hover > th { color:' . $colorData['success_text_color'] . '; }';
		}

		//info
		if( $colorData['info_color'] !== '' ) {
			$style .= '.weluka-text-info { color:' . $colorData['info_color'] . '; }';
			$style .= '.weluka-bg-info { background-color:' . $colorData['info_color'] . '; }';
			$style .= '.label-info { background-color:' . $colorData['info_color'] . '; }';

			//button
			$style .= '.weluka-btn-info, .weluka-btn-info .badge, .weluka-btn-info.disabled, .weluka-btn-info[disabled], fieldset[disabled] .weluka-btn-info, .weluka-btn-info.disabled:hover, .weluka-btn-info[disabled]:hover, fieldset[disabled] .weluka-btn-info:hover, .weluka-btn-info.disabled:focus, .weluka-btn-info[disabled]:focus, fieldset[disabled] .weluka-btn-info:focus, .weluka-btn-info.disabled.focus, .weluka-btn-info[disabled].focus, fieldset[disabled] .weluka-btn-info.focus, .weluka-btn-info.disabled:active, .weluka-btn-info[disabled]:active, fieldset[disabled] .weluka-btn-info:active, .weluka-btn-info.disabled.active, .weluka-btn-info[disabled].active, fieldset[disabled] .weluka-btn-info.active { background-color:' . $colorData['info_color'] . '; }';

			$style .= '.weluka-btn-info:hover, .weluka-btn-info:focus, .weluka-btn-info.focus, .weluka-btn-info:active, .weluka-btn-info.active, .open > .dropdown-toggle.weluka-btn-info { border-color:' .  $colorData['info_color'] . ' !important; }';

			//nav
			$style .= '.weluka-navbar-aqua, .weluka-navbar-aqua .dropdown-menu, .weluka-nav-bar-ham .nav.weluka-navbar-aqua, .weluka-nav-bar-ham .weluka-toggle.weluka-navbar-aqua { background-color:' . $colorData['info_color'] . '; }';

			//accordion panel
			$style .= '.panel-info > .panel-heading { background-color:' . $colorData['info_color'] . '; }';
			$style .= '.panel-info > .panel-heading .badge { color:' . $colorData['info_color'] . '; }';
			
			//alert
			$style .= '.alert-info { background-color:' . $colorData['info_color'] . '; }';
			
			//list-gourp
			$style .= '.list-group-item-info { background-color:' . $colorData['info_color'] . '; }';
			
			//table
			$style .= '.table > thead > tr > td.info, .table > tbody > tr > td.info, .table > tfoot > tr > td.info, .table > thead > tr > th.info, .table > tbody > tr > th.info, .table > tfoot > tr > th.info, .table > thead > tr.info > td, .table > tbody > tr.info > td, .table > tfoot > tr.info > td, .table > thead > tr.info > th, .table > tbody > tr.info > th, .table > tfoot > tr.info > th { background-color:' .  $colorData['info_color'] . '; }';
		}
		//info border
		if( $colorData['info_border_color'] !== '' ) {
			//bg
			$style .= 'a.weluka-bg-info:hover { background-color:' . $colorData['info_border_color'] . '; }';

			//button
			$style .= '.weluka-btn-info, .weluka-btn-info.disabled, .weluka-btn-info[disabled], fieldset[disabled] .weluka-btn-info, .weluka-btn-info.disabled:hover, .weluka-btn-info[disabled]:hover, fieldset[disabled] .weluka-btn-info:hover, .weluka-btn-info.disabled:focus, .weluka-btn-info[disabled]:focus, fieldset[disabled] .weluka-btn-info:focus, .weluka-btn-info.disabled.focus, .weluka-btn-info[disabled].focus, fieldset[disabled] .weluka-btn-info.focus, .weluka-btn-info.disabled:active, .weluka-btn-info[disabled]:active, fieldset[disabled] .weluka-btn-info:active, .weluka-btn-info.disabled.active, .weluka-btn-info[disabled].active, fieldset[disabled] .weluka-btn-info.active { border-color:' . $colorData['info_border_color'] . ' !important; }';
			$style .= '.weluka-btn-info:hover, .weluka-btn-info:focus, .weluka-btn-info.focus, .weluka-btn-info:active, .weluka-btn-info.active, .open > .dropdown-toggle.weluka-btn-info { background-color:' . $colorData['info_border_color'] . '; }';
			
			//nav
			$style .= '.weluka-navbar-aqua, .weluka-navbar-aqua .dropdown-menu, .weluka-nav-bar-ham .nav.weluka-navbar-aqua, .weluka-navbar-aqua a:hover, .weluka-navbar-aqua a:focus, .weluka-navbar-aqua .open > a, .weluka-navbar-aqua .open a:hover, .weluka-navbar-aqua .open a:focus, .weluka-navbar-aqua .dropdown-menu a:hover, .weluka-navbar-aqua .dropdown-menu a:focus, .weluka-navbar-aqua .navbar-toggle, .weluka-nav-bar-ham .weluka-toggle.weluka-navbar-aqua, .weluka-nav-bar-ham .weluka-toggle.weluka-navbar-aqua, .weluka-navbar-aqua .navbar-collapse, .weluka-navbar-aqua .navbar-form, .weluka-nav-bar-v.weluka-navbar-aqua .nav, .weluka-nav-bar-v.weluka-navbar-aqua .nav li, .weluka-nav-bar-ham .nav.weluka-navbar-aqua { border-color:' . $colorData['info_border_color'] . ' !important; }';
			$style .= '.weluka-navbar-aqua a:hover, .weluka-navbar-aqua a:focus, .weluka-navbar-aqua .open > a, .weluka-navbar-aqua .open a:hover, .weluka-navbar-aqua .open a:focus, .weluka-navbar-aqua .dropdown-menu a:hover, .weluka-navbar-aqua .dropdown-menu a:focus, .weluka-navbar-aqua .navbar-toggle:hover, .weluka-navbar-aqua .navbar-toggle:focus, .weluka-nav-bar-ham .weluka-toggle.weluka-navbar-aqua:hover, .weluka-nav-bar-ham .weluka-toggle.weluka-navbar-aqua:focus { background-color:' . $colorData['info_border_color'] . '; }';
			
			//accordion panel
			$style .= '.panel-info > .panel-heading { border-color:' . $colorData['info_border_color'] . '; }';
			$style .= '.panel-info > .panel-heading + .panel-collapse > .panel-body { border-top-color:' . $colorData['info_border_color'] . '; }';
			$style .= '.panel-info > .panel-footer + .panel-collapse > .panel-body { border-bottom-color:' . $colorData['info_border_color'] . '; }';
			$style .= '.panel-info { border-color:' . $colorData['info_border_color'] . '; }';

			//alert
			$style .= '.alert-info { border-color:' . $colorData['info_border_color'] . '; }';

			//list-group
			$style .= 'a.list-group-item-info:hover, a.list-group-item-info:focus { background-color:' . $colorData['info_border_color'] . '; }';
			$style .= 'a.list-group-item-info.active, a.list-group-item-info.active:hover, a.list-group-item-info.active:focus { background-color:' . $colorData['info_border_color'] . ';border-color:' . $colorData['info_border_color'] . '; }';

			//table
			$style .= '.table-hover > tbody > tr > td.info:hover, .table-hover > tbody > tr > th.info:hover, .table-hover > tbody > tr.info:hover > td, .table-hover > tbody > tr:hover > .info, .table-hover > tbody > tr.info:hover > th { background-color:' . $colorData['info_border_color'] . '; }';
		}
		//info text
		if( $colorData['info_text_color'] !== '' ) {
			$style .= '.weluka-bg-info { color:' . $colorData['info_text_color'] . '; }';
			$style .= '.label-info { color:' . $colorData['info_text_color'] . '; }';

			//button, a.bg
			$style .= '.weluka-btn-info, .weluka-btn-info:hover, .weluka-btn-info:focus, .weluka-btn-info.focus, .weluka-btn-info:active, .weluka-btn-info.active, .open > .dropdown-toggle.weluka-btn-info, a.weluka-bg-info:hover { color:' . $colorData['info_text_color'] . ' !important; }';

			//nav
			$style .= '.weluka-navbar-aqua, .weluka-navbar-aqua a, .weluka-navbar-aqua .dropdown-menu, .weluka-nav-bar-ham .nav.weluka-navbar-aqua { color: ' . $colorData['info_text_color'] . ' !important; }';
			$style .= '.weluka-navbar-aqua .navbar-toggle .icon-bar, .weluka-nav-bar-ham .weluka-toggle.weluka-navbar-aqua .icon-bar { background-color:' . $colorData['info_text_color'] . '; }';

			//accordion panel
			$style .= '.panel-info > .panel-heading { color:' . $colorData['info_text_color'] . '; }';

			//alert
			$style .= '.alert-info { color:' . $colorData['info_text_color'] . '; }';

			//list group
			$style .= '.list-group-item-info, a.list-group-item-info, a.list-group-item-info:hover, a.list-group-item-info:focus, a.list-group-item-info.active, a.list-group-item-info.active:hover, a.list-group-item-info.active:focus { color:' . $colorData['info_text_color'] . '; }';

			//table
			$style .= '.table > thead > tr > td.info, .table > tbody > tr > td.info, .table > tfoot > tr > td.info, .table > thead > tr > th.info, .table > tbody > tr > th.info, .table > tfoot > tr > th.info, .table > thead > tr.info > td, .table > tbody > tr.info > td, .table > tfoot > tr.info > td, .table > thead > tr.info > th, .table > tbody > tr.info > th, .table > tfoot > tr.info > th, .table-hover > tbody > tr > td.info:hover, .table-hover > tbody > tr > th.info:hover, .table-hover > tbody > tr.info:hover > td, .table-hover > tbody > tr:hover > .info, .table-hover > tbody > tr.info:hover > th { color:' . $colorData['info_text_color'] . '; }';
		}

		//warning
		if( $colorData['warning_color'] !== '' ) {
			$style .= '.weluka-text-warning { color:' . $colorData['warning_color'] . '; }';
			$style .= '.weluka-bg-warning { background-color:' . $colorData['warning_color'] . '; }';
			$style .= '.label-warning { background-color:' . $colorData['warning_color'] . '; }';

			//button
			$style .= '.weluka-btn-warning, .weluka-btn-warning .badge, .weluka-btn-warning.disabled, .weluka-btn-warning[disabled], fieldset[disabled] .weluka-btn-warning, .weluka-btn-warning.disabled:hover, .weluka-btn-warning[disabled]:hover, fieldset[disabled] .weluka-btn-warning:hover, .weluka-btn-warning.disabled:focus, .weluka-btn-warning[disabled]:focus, fieldset[disabled] .weluka-btn-warning:focus, .weluka-btn-warning.disabled.focus, .weluka-btn-warning[disabled].focus, fieldset[disabled] .weluka-btn-warning.focus, .weluka-btn-warning.disabled:active, .weluka-btn-warning[disabled]:active, fieldset[disabled] .weluka-btn-warning:active, .weluka-btn-warning.disabled.active, .weluka-btn-warning[disabled].active, fieldset[disabled] .weluka-btn-warning.active { background-color:' . $colorData['warning_color'] . '; }';

			$style .= '.weluka-btn-warning:hover, .weluka-btn-warning:focus, .weluka-btn-warning.focus, .weluka-btn-warning:active, .weluka-btn-warning.active, .open > .dropdown-toggle.weluka-btn-warning { border-color:' .  $colorData['warning_color'] . ' !important; }';

			//nav
			$style .= '.weluka-navbar-orange, .weluka-navbar-orange .dropdown-menu, .weluka-nav-bar-ham .nav.weluka-navbar-orange, .weluka-nav-bar-ham .weluka-toggle.weluka-navbar-orange { background-color:' . $colorData['warning_color'] . '; }';

			//accordion panel
			$style .= '.panel-warning > .panel-heading { background-color:' . $colorData['warning_color'] . '; }';
			$style .= '.panel-warning > .panel-heading .badge { color:' . $colorData['warning_color'] . '; }';
			
			//alert
			$style .= '.alert-warning { background-color:' . $colorData['warning_color'] . '; }';
			
			//list-gourp
			$style .= '.list-group-item-warning { background-color:' . $colorData['warning_color'] . '; }';
			
			//table
			$style .= '.table > thead > tr > td.warning, .table > tbody > tr > td.warning, .table > tfoot > tr > td.warning, .table > thead > tr > th.warning, .table > tbody > tr > th.warning, .table > tfoot > tr > th.warning, .table > thead > tr.warning > td, .table > tbody > tr.warning > td, .table > tfoot > tr.warning > td, .table > thead > tr.warning > th, .table > tbody > tr.warning > th, .table > tfoot > tr.warning > th { background-color:' .  $colorData['warning_color'] . '; }';
		}
		//warning border
		if( $colorData['warning_border_color'] !== '' ) {
			//bg
			$style .= 'a.weluka-bg-warning:hover { background-color:' . $colorData['warning_border_color'] . '; }';

			//button
			$style .= '.weluka-btn-warning, .weluka-btn-warning.disabled, .weluka-btn-warning[disabled], fieldset[disabled] .weluka-btn-warning, .weluka-btn-warning.disabled:hover, .weluka-btn-warning[disabled]:hover, fieldset[disabled] .weluka-btn-warning:hover, .weluka-btn-warning.disabled:focus, .weluka-btn-warning[disabled]:focus, fieldset[disabled] .weluka-btn-warning:focus, .weluka-btn-warning.disabled.focus, .weluka-btn-warning[disabled].focus, fieldset[disabled] .weluka-btn-warning.focus, .weluka-btn-warning.disabled:active, .weluka-btn-warning[disabled]:active, fieldset[disabled] .weluka-btn-warning:active, .weluka-btn-warning.disabled.active, .weluka-btn-warning[disabled].active, fieldset[disabled] .weluka-btn-warning.active { border-color:' . $colorData['warning_border_color'] . ' !important; }';
			$style .= '.weluka-btn-warning:hover, .weluka-btn-warning:focus, .weluka-btn-warning.focus, .weluka-btn-warning:active, .weluka-btn-warning.active, .open > .dropdown-toggle.weluka-btn-warning { background-color:' . $colorData['warning_border_color'] . '; }';
			
			//nav
			$style .= '.weluka-navbar-orange, .weluka-navbar-orange .dropdown-menu, .weluka-nav-bar-ham .nav.weluka-navbar-orange, .weluka-navbar-orange a:hover, .weluka-navbar-orange a:focus, .weluka-navbar-orange .open > a, .weluka-navbar-orange .open a:hover, .weluka-navbar-orange .open a:focus, .weluka-navbar-orange .dropdown-menu a:hover, .weluka-navbar-orange .dropdown-menu a:focus, .weluka-navbar-orange .navbar-toggle, .weluka-nav-bar-ham .weluka-toggle.weluka-navbar-orange, .weluka-nav-bar-ham .weluka-toggle.weluka-navbar-orange, .weluka-navbar-orange .navbar-collapse, .weluka-navbar-orange .navbar-form, .weluka-nav-bar-v.weluka-navbar-orange .nav, .weluka-nav-bar-v.weluka-navbar-orange .nav li, .weluka-nav-bar-ham .nav.weluka-navbar-orange { border-color:' . $colorData['warning_border_color'] . ' !important; }';
			$style .= '.weluka-navbar-orange a:hover, .weluka-navbar-orange a:focus, .weluka-navbar-orange .open > a, .weluka-navbar-orange .open a:hover, .weluka-navbar-orange .open a:focus, .weluka-navbar-orange .dropdown-menu a:hover, .weluka-navbar-orange .dropdown-menu a:focus, .weluka-navbar-orange .navbar-toggle:hover, .weluka-navbar-orange .navbar-toggle:focus, .weluka-nav-bar-ham .weluka-toggle.weluka-navbar-orange:hover, .weluka-nav-bar-ham .weluka-toggle.weluka-navbar-orange:focus { background-color:' . $colorData['warning_border_color'] . '; }';
			
			//accordion panel
			$style .= '.panel-warning > .panel-heading { border-color:' . $colorData['warning_border_color'] . '; }';
			$style .= '.panel-warning > .panel-heading + .panel-collapse > .panel-body { border-top-color:' . $colorData['warning_border_color'] . '; }';
			$style .= '.panel-warning > .panel-footer + .panel-collapse > .panel-body { border-bottom-color:' . $colorData['warning_border_color'] . '; }';
			$style .= '.panel-warning { border-color:' . $colorData['warning_border_color'] . '; }';

			//alert
			$style .= '.alert-warning { border-color:' . $colorData['warning_border_color'] . '; }';

			//list-group
			$style .= 'a.list-group-item-warning:hover, a.list-group-item-warning:focus { background-color:' . $colorData['warning_border_color'] . '; }';
			$style .= 'a.list-group-item-warning.active, a.list-group-item-warning.active:hover, a.list-group-item-warning.active:focus { background-color:' . $colorData['warning_border_color'] . ';border-color:' . $colorData['warning_border_color'] . '; }';

			//table
			$style .= '.table-hover > tbody > tr > td.warning:hover, .table-hover > tbody > tr > th.warning:hover, .table-hover > tbody > tr.warning:hover > td, .table-hover > tbody > tr:hover > .warning, .table-hover > tbody > tr.warning:hover > th { background-color:' . $colorData['warning_border_color'] . '; }';
		}
		//warning text
		if( $colorData['warning_text_color'] !== '' ) {
			$style .= '.weluka-bg-warning { color:' . $colorData['warning_text_color'] . '; }';
			$style .= '.label-warning { color:' . $colorData['warning_text_color'] . '; }';

			//button, a.bg
			$style .= '.weluka-btn-warning, .weluka-btn-warning:hover, .weluka-btn-warning:focus, .weluka-btn-warning.focus, .weluka-btn-warning:active, .weluka-btn-warning.active, .open > .dropdown-toggle.weluka-btn-warning, a.weluka-bg-warning:hover { color:' . $colorData['warning_text_color'] . ' !important; }';

			//nav
			$style .= '.weluka-navbar-orange, .weluka-navbar-orange a, .weluka-navbar-orange .dropdown-menu, .weluka-nav-bar-ham .nav.weluka-navbar-orange { color: ' . $colorData['warning_text_color'] . ' !important; }';
			$style .= '.weluka-navbar-orange .navbar-toggle .icon-bar, .weluka-nav-bar-ham .weluka-toggle.weluka-navbar-orange .icon-bar { background-color:' . $colorData['warning_text_color'] . '; }';

			//accordion panel
			$style .= '.panel-warning > .panel-heading { color:' . $colorData['warning_text_color'] . '; }';

			//alert
			$style .= '.alert-warning { color:' . $colorData['warning_text_color'] . '; }';

			//list group
			$style .= '.list-group-item-warning, a.list-group-item-warning, a.list-group-item-warning:hover, a.list-group-item-warning:focus, a.list-group-item-warning.active, a.list-group-item-warning.active:hover, a.list-group-item-warning.active:focus { color:' . $colorData['warning_text_color'] . '; }';

			//table
			$style .= '.table > thead > tr > td.warning, .table > tbody > tr > td.warning, .table > tfoot > tr > td.warning, .table > thead > tr > th.warning, .table > tbody > tr > th.warning, .table > tfoot > tr > th.warning, .table > thead > tr.warning > td, .table > tbody > tr.warning > td, .table > tfoot > tr.warning > td, .table > thead > tr.warning > th, .table > tbody > tr.warning > th, .table > tfoot > tr.warning > th, .table-hover > tbody > tr > td.warning:hover, .table-hover > tbody > tr > th.warning:hover, .table-hover > tbody > tr.warning:hover > td, .table-hover > tbody > tr:hover > .warning, .table-hover > tbody > tr.warning:hover > th { color:' . $colorData['warning_text_color'] . '; }';
		}

		//danger
		if( $colorData['danger_color'] !== '' ) {
			$style .= '.weluka-text-danger { color:' . $colorData['danger_color'] . '; }';
			$style .= '.weluka-bg-danger { background-color:' . $colorData['danger_color'] . '; }';
			$style .= '.label-danger { background-color:' . $colorData['danger_color'] . '; }';

			//button
			$style .= '.weluka-btn-danger, .weluka-btn-danger .badge, .weluka-btn-danger.disabled, .weluka-btn-danger[disabled], fieldset[disabled] .weluka-btn-danger, .weluka-btn-danger.disabled:hover, .weluka-btn-danger[disabled]:hover, fieldset[disabled] .weluka-btn-danger:hover, .weluka-btn-danger.disabled:focus, .weluka-btn-danger[disabled]:focus, fieldset[disabled] .weluka-btn-danger:focus, .weluka-btn-danger.disabled.focus, .weluka-btn-danger[disabled].focus, fieldset[disabled] .weluka-btn-danger.focus, .weluka-btn-danger.disabled:active, .weluka-btn-danger[disabled]:active, fieldset[disabled] .weluka-btn-danger:active, .weluka-btn-danger.disabled.active, .weluka-btn-danger[disabled].active, fieldset[disabled] .weluka-btn-danger.active { background-color:' . $colorData['danger_color'] . '; }';

			$style .= '.weluka-btn-danger:hover, .weluka-btn-danger:focus, .weluka-btn-danger.focus, .weluka-btn-danger:active, .weluka-btn-danger.active, .open > .dropdown-toggle.weluka-btn-danger { border-color:' .  $colorData['danger_color'] . ' !important; }';

			//nav
			$style .= '.weluka-navbar-red, .weluka-navbar-red .dropdown-menu, .weluka-nav-bar-ham .nav.weluka-navbar-red, .weluka-nav-bar-ham .weluka-toggle.weluka-navbar-red { background-color:' . $colorData['danger_color'] . '; }';

			//accordion panel
			$style .= '.panel-danger > .panel-heading { background-color:' . $colorData['danger_color'] . '; }';
			$style .= '.panel-danger > .panel-heading .badge { color:' . $colorData['danger_color'] . '; }';
			
			//alert
			$style .= '.alert-danger { background-color:' . $colorData['danger_color'] . '; }';
			
			//list-gourp
			$style .= '.list-group-item-danger { background-color:' . $colorData['danger_color'] . '; }';
			
			//table
			$style .= '.table > thead > tr > td.danger, .table > tbody > tr > td.danger, .table > tfoot > tr > td.danger, .table > thead > tr > th.danger, .table > tbody > tr > th.danger, .table > tfoot > tr > th.danger, .table > thead > tr.danger > td, .table > tbody > tr.danger > td, .table > tfoot > tr.danger > td, .table > thead > tr.danger > th, .table > tbody > tr.danger > th, .table > tfoot > tr.danger > th { background-color:' .  $colorData['danger_color'] . '; }';
		}
		//danger border
		if( $colorData['danger_border_color'] !== '' ) {
			//bg
			$style .= 'a.weluka-bg-danger:hover { background-color:' . $colorData['danger_border_color'] . '; }';

			//button
			$style .= '.weluka-btn-danger, .weluka-btn-danger.disabled, .weluka-btn-danger[disabled], fieldset[disabled] .weluka-btn-danger, .weluka-btn-danger.disabled:hover, .weluka-btn-danger[disabled]:hover, fieldset[disabled] .weluka-btn-danger:hover, .weluka-btn-danger.disabled:focus, .weluka-btn-danger[disabled]:focus, fieldset[disabled] .weluka-btn-danger:focus, .weluka-btn-danger.disabled.focus, .weluka-btn-danger[disabled].focus, fieldset[disabled] .weluka-btn-danger.focus, .weluka-btn-danger.disabled:active, .weluka-btn-danger[disabled]:active, fieldset[disabled] .weluka-btn-danger:active, .weluka-btn-danger.disabled.active, .weluka-btn-danger[disabled].active, fieldset[disabled] .weluka-btn-danger.active { border-color:' . $colorData['danger_border_color'] . ' !important; }';
			$style .= '.weluka-btn-danger:hover, .weluka-btn-danger:focus, .weluka-btn-danger.focus, .weluka-btn-danger:active, .weluka-btn-danger.active, .open > .dropdown-toggle.weluka-btn-danger { background-color:' . $colorData['danger_border_color'] . '; }';
			
			//nav
			$style .= '.weluka-navbar-red, .weluka-navbar-red .dropdown-menu, .weluka-nav-bar-ham .nav.weluka-navbar-red, .weluka-navbar-red a:hover, .weluka-navbar-red a:focus, .weluka-navbar-red .open > a, .weluka-navbar-red .open a:hover, .weluka-navbar-red .open a:focus, .weluka-navbar-red .dropdown-menu a:hover, .weluka-navbar-red .dropdown-menu a:focus, .weluka-navbar-red .navbar-toggle, .weluka-nav-bar-ham .weluka-toggle.weluka-navbar-red, .weluka-nav-bar-ham .weluka-toggle.weluka-navbar-red, .weluka-navbar-red .navbar-collapse, .weluka-navbar-red .navbar-form, .weluka-nav-bar-v.weluka-navbar-red .nav, .weluka-nav-bar-v.weluka-navbar-red .nav li, .weluka-nav-bar-ham .nav.weluka-navbar-red { border-color:' . $colorData['danger_border_color'] . ' !important; }';
			$style .= '.weluka-navbar-red a:hover, .weluka-navbar-red a:focus, .weluka-navbar-red .open > a, .weluka-navbar-red .open a:hover, .weluka-navbar-red .open a:focus, .weluka-navbar-red .dropdown-menu a:hover, .weluka-navbar-red .dropdown-menu a:focus, .weluka-navbar-red .navbar-toggle:hover, .weluka-navbar-red .navbar-toggle:focus, .weluka-nav-bar-ham .weluka-toggle.weluka-navbar-red:hover, .weluka-nav-bar-ham .weluka-toggle.weluka-navbar-red:focus { background-color:' . $colorData['danger_border_color'] . '; }';
			
			//accordion panel
			$style .= '.panel-danger > .panel-heading { border-color:' . $colorData['danger_border_color'] . '; }';
			$style .= '.panel-danger > .panel-heading + .panel-collapse > .panel-body { border-top-color:' . $colorData['danger_border_color'] . '; }';
			$style .= '.panel-danger > .panel-footer + .panel-collapse > .panel-body { border-bottom-color:' . $colorData['danger_border_color'] . '; }';
			$style .= '.panel-danger { border-color:' . $colorData['danger_border_color'] . '; }';

			//alert
			$style .= '.alert-danger { border-color:' . $colorData['danger_border_color'] . '; }';

			//list-group
			$style .= 'a.list-group-item-danger:hover, a.list-group-item-danger:focus { background-color:' . $colorData['danger_border_color'] . '; }';
			$style .= 'a.list-group-item-danger.active, a.list-group-item-danger.active:hover, a.list-group-item-danger.active:focus { background-color:' . $colorData['danger_border_color'] . ';border-color:' . $colorData['danger_border_color'] . '; }';

			//table
			$style .= '.table-hover > tbody > tr > td.danger:hover, .table-hover > tbody > tr > th.danger:hover, .table-hover > tbody > tr.danger:hover > td, .table-hover > tbody > tr:hover > .danger, .table-hover > tbody > tr.danger:hover > th { background-color:' . $colorData['danger_border_color'] . '; }';
		}
		//danger text
		if( $colorData['danger_text_color'] !== '' ) {
			$style .= '.weluka-bg-danger { color:' . $colorData['danger_text_color'] . '; }';
			$style .= '.label-danger { color:' . $colorData['danger_text_color'] . '; }';

			//button, a.bg
			$style .= '.weluka-btn-danger, .weluka-btn-danger:hover, .weluka-btn-danger:focus, .weluka-btn-danger.focus, .weluka-btn-danger:active, .weluka-btn-danger.active, .open > .dropdown-toggle.weluka-btn-danger, a.weluka-bg-danger:hover { color:' . $colorData['danger_text_color'] . ' !important; }';

			//nav
			$style .= '.weluka-navbar-red, .weluka-navbar-red a, .weluka-navbar-red .dropdown-menu, .weluka-nav-bar-ham .nav.weluka-navbar-red { color: ' . $colorData['danger_text_color'] . ' !important; }';
			$style .= '.weluka-navbar-red .navbar-toggle .icon-bar, .weluka-nav-bar-ham .weluka-toggle.weluka-navbar-red .icon-bar { background-color:' . $colorData['danger_text_color'] . '; }';

			//accordion panel
			$style .= '.panel-danger > .panel-heading { color:' . $colorData['danger_text_color'] . '; }';

			//alert
			$style .= '.alert-danger { color:' . $colorData['danger_text_color'] . '; }';

			//list group
			$style .= '.list-group-item-danger, a.list-group-item-danger, a.list-group-item-danger:hover, a.list-group-item-danger:focus, a.list-group-item-danger.active, a.list-group-item-danger.active:hover, a.list-group-item-danger.active:focus { color:' . $colorData['danger_text_color'] . '; }';

			//table
			$style .= '.table > thead > tr > td.danger, .table > tbody > tr > td.danger, .table > tfoot > tr > td.danger, .table > thead > tr > th.danger, .table > tbody > tr > th.danger, .table > tfoot > tr > th.danger, .table > thead > tr.danger > td, .table > tbody > tr.danger > td, .table > tfoot > tr.danger > td, .table > thead > tr.danger > th, .table > tbody > tr.danger > th, .table > tfoot > tr.danger > th, .table-hover > tbody > tr > td.danger:hover, .table-hover > tbody > tr > th.danger:hover, .table-hover > tbody > tr.danger:hover > td, .table-hover > tbody > tr:hover > .danger, .table-hover > tbody > tr.danger:hover > th { color:' . $colorData['danger_text_color'] . '; }';
		}

//ver1.0.1 add
		//tagcloud
		if( !empty($colorData['tagcloud_color']) ) {
			$style .= '.tagcloud a  { background-color:' . $colorData['tagcloud_color'] . '; }';
		}
		if( !empty($colorData['tagcloud_hoverbg_color']) ) {
			$style .= '.tagcloud a:hover { background-color:' . $colorData['tagcloud_hoverbg_color'] . '; }';
		}
		if( !empty($colorData['tagcloud_text_color']) ) {
			//v1.2.3 #weluka-main-header #weluka-main-footer add
			$style .= '.tagcloud a, #weluka-main-header .tagcloud a, #weluka-main-footer .tagcloud a, .tagcloud a:hover, #weluka-main-header .tagcloud a:hover, #weluka-main-footer .tagcloud a:hover { color:' . $colorData['tagcloud_text_color'] . '; }';			
		}
//ver1.0.1 addend
		$style .= '</style>';		
		echo $style;
	endif;
	
	if( !empty( $customCss ) ) :
		echo '<style type="text/css">' . $customCss . '</style>';
	endif;
}

/**** [ admin page template metabox ] ****/

/**
 * @since 1.0
 */
function weluka_page_templates_metabox(){
	global $weluka_themename;
	add_meta_box("weluka_ptemp_meta", esc_html__( 'Weluka Page Template Settings', $weluka_themename ), "weluka_ptemp_meta", "page", "side");
}

/**
 * blog temaple metabox input
 * @since 1.0
 * @update
 * ver1.0.6
 * ver1.1
 * ver1.1.1
 * ver1.2
 */
function weluka_ptemp_meta( $param ) {
		global $post, $weluka_themename, $weluka;
		$temp_array = array();

		$temp_array = maybe_unserialize( $weluka->get_postmeta($post->ID, WELUKA_PTMP_POST_META_NAME) );
		
		$blogcats = isset( $temp_array['weluka_ptemp_blogcats'] ) ? (array) $temp_array['weluka_ptemp_blogcats'] : array();
		//$blog_perpage = isset( $temp_array['weluka_ptemp_blog_perpage'] ) ? (int) $temp_array['weluka_ptemp_blog_perpage'] : '';
		$blog_perpage = isset( $temp_array['weluka_ptemp_blog_perpage'] ) ? $temp_array['weluka_ptemp_blog_perpage'] : '';	//v1.1 modify

		//v1.0.6 add
		$blog_date			= isset( $temp_array['weluka_ptemp_blog_date'] ) ? (int) $temp_array['weluka_ptemp_blog_date'] : 9;
		$blog_author		= isset( $temp_array['weluka_ptemp_blog_author'] ) ? (int) $temp_array['weluka_ptemp_blog_author'] : 9;
		$blog_category		= isset( $temp_array['weluka_ptemp_blog_category'] ) ? (int) $temp_array['weluka_ptemp_blog_category'] : 9;
		$blog_comment		= isset( $temp_array['weluka_ptemp_blog_comment'] ) ? (int) $temp_array['weluka_ptemp_blog_comment'] : 9;
		$blog_tagcloud		= isset( $temp_array['weluka_ptemp_blog_tagcloud'] ) ? (int) $temp_array['weluka_ptemp_blog_tagcloud'] : 9;
        $blog_tagcloud_pos	= isset( $temp_array['weluka_ptemp_blog_tagcloud_pos'] ) ? $temp_array['weluka_ptemp_blog_tagcloud_pos'] : '';
		$blog_date_format	= isset( $temp_array['weluka_ptemp_blog_date_format'] ) ? $temp_array['weluka_ptemp_blog_date_format'] : '';
		$blog_date_modify	= isset( $temp_array['weluka_ptemp_blog_date_modify'] ) ? (int) $temp_array['weluka_ptemp_blog_date_modify'] : 9;
		$blog_excerpt_num	= isset( $temp_array['weluka_ptemp_blog_excerpt_num'] ) ? $temp_array['weluka_ptemp_blog_excerpt_num'] : '';
		$blog_format		= isset( $temp_array['weluka_ptemp_blog_format'] ) ? $temp_array['weluka_ptemp_blog_format'] : '';
		$blog_format_sm		= isset( $temp_array['weluka_ptemp_blog_format_sm'] ) ? $temp_array['weluka_ptemp_blog_format_sm'] : '';
		$blog_format_xs		= isset( $temp_array['weluka_ptemp_blog_format_xs'] ) ? $temp_array['weluka_ptemp_blog_format_xs'] : '';
		$blog_cols			= isset( $temp_array['weluka_ptemp_blog_cols'] ) ? $temp_array['weluka_ptemp_blog_cols'] : '';
		$blog_cols_sm		= isset( $temp_array['weluka_ptemp_blog_cols_sm'] ) ? $temp_array['weluka_ptemp_blog_cols_sm'] : '';
		$blog_cols_xs		= isset( $temp_array['weluka_ptemp_blog_cols_xs'] ) ? $temp_array['weluka_ptemp_blog_cols_xs'] : '';
		$blog_button_text	= isset( $temp_array['weluka_ptemp_blog_button_text'] ) ? $temp_array['weluka_ptemp_blog_button_text'] : '';
		//v1.0.6 addend
		//v1.1 add
		$blog_title_num		= isset( $temp_array['weluka_ptemp_blog_title_num'] ) ? $temp_array['weluka_ptemp_blog_title_num'] : '';
		$blog_paging_type	= isset( $temp_array['weluka_ptemp_blog_paging_type'] ) ? $temp_array['weluka_ptemp_blog_paging_type'] : '';
		//v1.1 addend

		//v1.1.1 add
		$blog_title			= isset( $temp_array['weluka_ptemp_blog_title'] ) ? (int) $temp_array['weluka_ptemp_blog_title'] : 9;
		$blog_thumb			= isset( $temp_array['weluka_ptemp_blog_thumb'] ) ? (int) $temp_array['weluka_ptemp_blog_thumb'] : 9;
		$blog_excerpt		= isset( $temp_array['weluka_ptemp_blog_excerpt'] ) ? (int) $temp_array['weluka_ptemp_blog_excerpt'] : 9;
		$blog_readmore		= isset( $temp_array['weluka_ptemp_blog_readmore'] ) ? (int) $temp_array['weluka_ptemp_blog_readmore'] : 9;
		//v1.1.1 modify
		
		//v1.2 add
		$blog_thumb_height_use	= isset( $temp_array['weluka_ptemp_blog_thumb_height_use'] ) ? (int) $temp_array['weluka_ptemp_blog_thumb_height_use'] : 0;
		$blog_thumb_height		= isset( $temp_array['weluka_ptemp_blog_thumb_height'] ) ? $temp_array['weluka_ptemp_blog_thumb_height'] : '';
		$blog_thumb_height_sm	= isset( $temp_array['weluka_ptemp_blog_thumb_height_sm'] ) ? $temp_array['weluka_ptemp_blog_thumb_height_sm'] : '';
		$blog_thumb_height_xs	= isset( $temp_array['weluka_ptemp_blog_thumb_height_xs'] ) ? $temp_array['weluka_ptemp_blog_thumb_height_xs'] : '';
		$blog_height_eqh		= isset( $temp_array['weluka_ptemp_blog_height_eqh'] ) ? (int) $temp_array['weluka_ptemp_blog_height_eqh'] : 0;
		$blog_boxstyle			= isset( $temp_array['weluka_ptemp_blog_boxstyle'] ) ? $temp_array['weluka_ptemp_blog_boxstyle'] : 0;
		$blog_boxshape			= isset( $temp_array['weluka_ptemp_blog_boxshape'] ) ? $temp_array['weluka_ptemp_blog_boxshape'] : 0;
		$blog_boxshadow			= isset( $temp_array['weluka_ptemp_blog_boxshadow'] ) ? $temp_array['weluka_ptemp_blog_boxshadow'] : 0;
		$blog_color_use			= isset( $temp_array['weluka_ptemp_blog_color_use'] ) ? (int)$temp_array['weluka_ptemp_blog_color_use'] : 0;
		//v1.2 addend
		
		wp_nonce_field( 'weluka_ptemp_nonce', '_wpnonce_weluka_ptemp_save' ); ?>
		
		<div style="margin: 13px 0 11px 4px;" class="weluka_pt_info">
			<p><?php esc_html_e( 'If the page attribute "template" is selected, additional settings are displayed here.', $weluka_themename ); ?></p>
		</div>
		
		<div style="margin: 13px 0 11px 4px; display: none;" class="weluka_pt_blog">
			<p style="font-weight:bold;"><?php esc_html_e( 'Number of posts', Weluka::$settings['plugin_name'] ); ?></p>
			<input type="text" class="small-text" value="<?php echo esc_attr( $blog_perpage ); ?>" id="weluka_ptemp_blog_perpage" name="weluka_ptemp_blog_perpage"  />
            <div class="weluka-mgtop-s help-block"><?php _e('Please enter the maximum number to be displayed on one page.', $weluka_themename ); ?></div>
		</div>

		<?php //v1.1 add ?>
		<div id="weluka_paging_type_row" style="margin: 13px 0 11px 4px; display: none;" class="weluka_pt_blog">
			<p style="font-weight:bold;"><?php esc_html_e( 'Paging type', $weluka_themename ); ?></p>
            <?php WelukaBuilder::get_instance()->weluka_display_paging_type_settings( 'weluka_ptemp_blog_paging_type', '', false, true, $blog_paging_type, false, true); ?>
			<div id="both-excerpt-scl" class="weluka-mgtop-s"><?php _e('It is unavailable to set both infinite scroll and "-1" value at excerpt string number.', $weluka_themename);  //v1.2.8 add ?></div>
		</div>
		<?php //v1.1 addend ?>
		
		<div style="margin: 13px 0 11px 4px; display: none;" class="weluka_pt_blog">
			<p style="font-weight:bold;"><?php esc_html_e( 'Select blog categories', $weluka_themename ); ?></p>

			<?php $cats_array = get_categories('hide_empty=0');
			$site_cats = array();
			foreach ($cats_array as $categs) {
				$checked = '';
				
				if (!empty($blogcats)) {
					if (in_array($categs->cat_ID, $blogcats)) $checked = 'checked="checked"';
				} ?>
				
				<label style="padding-bottom: 5px; display: block;" for="<?php echo esc_attr( 'weluka_ptemp_blogcats-' . $categs->cat_ID ); ?>">
					<input type="checkbox" name="weluka_ptemp_blogcats[]" id="<?php echo esc_attr( 'weluka_ptemp_blogcats-' . $categs->cat_ID ); ?>" value="<?php echo esc_attr($categs->cat_ID); ?>" <?php echo $checked; ?> />&nbsp;&nbsp;<?php echo esc_html( $categs->cat_name ); ?>
				</label>							
			<?php } ?>
		</div>

		<?php //v1.0.6 add ?>
        <h4><?php _e( 'List Style', $weluka_themename ); ?></h4>
		<div style="margin: 13px 0 11px 4px; display: none;" class="weluka_pt_blog">
        	<p style="font-weight:bold;"><?php _e( 'Meta', $weluka_themename ); ?></p>
			<?php //v1.1.1 add ?>
			<div style="font-weight:bold;"><?php _e('Title', $weluka_themename); ?></div>
            <label class="selectit" style="margin-right:8px;">
				<input type="radio" name="weluka_ptemp_blog_title" id="weluka_ptemp_blog_title_9" value="9" <?php if( $blog_title === 9 ) { echo 'checked="checked"'; } ?> />&nbsp;&nbsp;<?php _e('Non-select', $weluka_themename); ?>
			</label>
            <label class="selectit" style="margin-right:8px;">
				<input type="radio" name="weluka_ptemp_blog_title" id="weluka_ptemp_blog_title_0" value="0" <?php if( $blog_title === 0 ) { echo 'checked="checked"'; } ?> />&nbsp;&nbsp;<?php _e('Show', $weluka_themename); ?>
			</label>
            <label class="selectit">
				<input type="radio" name="weluka_ptemp_blog_title" id="weluka_ptemp_blog_title_1" value="1" <?php if( $blog_title === 1 ) { echo 'checked="checked"'; } ?> />&nbsp;&nbsp;<?php _e('Hide', $weluka_themename); ?>
			</label>
			<?php //v1.1.1 add end ?>
			<div style="font-weight:bold;"><?php _e('Date', $weluka_themename); ?></div>
            <label class="selectit" style="margin-right:8px;">
				<input type="radio" name="weluka_ptemp_blog_date" id="weluka_ptemp_blog_date_9" value="9" <?php if( $blog_date === 9 ) { echo 'checked="checked"'; } ?> />&nbsp;&nbsp;<?php _e('Non-select', $weluka_themename); ?>
			</label>
            <label class="selectit" style="margin-right:8px;">
				<input type="radio" name="weluka_ptemp_blog_date" id="weluka_ptemp_blog_date_0" value="0" <?php if( $blog_date === 0 ) { echo 'checked="checked"'; } ?> />&nbsp;&nbsp;<?php _e('Show', $weluka_themename); ?>
			</label>
            <label class="selectit">
				<input type="radio" name="weluka_ptemp_blog_date" id="weluka_ptemp_blog_date_1" value="1" <?php if( $blog_date === 1 ) { echo 'checked="checked"'; } ?> />&nbsp;&nbsp;<?php _e('Hide', $weluka_themename); ?>
			</label>

			<div style="font-weight:bold;margin-top:8px;"><?php _e('Author', $weluka_themename); ?></div>
            <label class="selectit" style="margin-right:8px;">
				<input type="radio" name="weluka_ptemp_blog_author" id="weluka_ptemp_blog_author_9" value="9" <?php if( $blog_author === 9 ) { echo 'checked="checked"'; } ?> />&nbsp;&nbsp;<?php _e('Non-select', $weluka_themename); ?>
			</label>
            <label class="selectit" style="margin-right:8px;">
				<input type="radio" name="weluka_ptemp_blog_author" id="weluka_ptemp_blog_author_0" value="0" <?php if( $blog_author === 0 ) { echo 'checked="checked"'; } ?> />&nbsp;&nbsp;<?php _e('Show', $weluka_themename); ?>
			</label>
            <label class="selectit">
				<input type="radio" name="weluka_ptemp_blog_author" id="weluka_ptemp_blog_author_1" value="1" <?php if( $blog_author === 1 ) { echo 'checked="checked"'; } ?> />&nbsp;&nbsp;<?php _e('Hide', $weluka_themename); ?>
			</label>

			<div style="font-weight:bold;margin-top:8px;"><?php _e('Category', $weluka_themename); ?></div>
            <label class="selectit" style="margin-right:8px;">
				<input type="radio" name="weluka_ptemp_blog_category" id="weluka_ptemp_blog_category_9" value="9" <?php if( $blog_category === 9 ) { echo 'checked="checked"'; } ?> />&nbsp;&nbsp;<?php _e('Non-select', $weluka_themename); ?>
			</label>
            <label class="selectit" style="margin-right:8px;">
				<input type="radio" name="weluka_ptemp_blog_category" id="weluka_ptemp_blog_category_0" value="0" <?php if( $blog_category === 0 ) { echo 'checked="checked"'; } ?> />&nbsp;&nbsp;<?php _e('Show', $weluka_themename); ?>
			</label>
            <label class="selectit">
				<input type="radio" name="weluka_ptemp_blog_category" id="weluka_ptemp_blog_category_1" value="1" <?php if( $blog_category === 1 ) { echo 'checked="checked"'; } ?> />&nbsp;&nbsp;<?php _e('Hide', $weluka_themename); ?>
			</label>

			<div style="font-weight:bold;margin-top:8px;"><?php _e('Comment Count', $weluka_themename); ?></div>
            <label class="selectit" style="margin-right:8px;">
				<input type="radio" name="weluka_ptemp_blog_comment" id="weluka_ptemp_blog_comment_9" value="9" <?php if( $blog_comment === 9 ) { echo 'checked="checked"'; } ?> />&nbsp;&nbsp;<?php _e('Non-select', $weluka_themename); ?>
			</label>
            <label class="selectit" style="margin-right:8px;">
				<input type="radio" name="weluka_ptemp_blog_comment" id="weluka_ptemp_blog_comment_0" value="0" <?php if( $blog_comment === 0 ) { echo 'checked="checked"'; } ?> />&nbsp;&nbsp;<?php _e('Show', $weluka_themename); ?>
			</label>
            <label class="selectit">
				<input type="radio" name="weluka_ptemp_blog_comment" id="weluka_ptemp_blog_comment_1" value="1" <?php if( $blog_comment === 1 ) { echo 'checked="checked"'; } ?> />&nbsp;&nbsp;<?php _e('Hide', $weluka_themename); ?>
			</label>            
			<?php //v1.1.1 add ?>
			<div style="font-weight:bold;margin-top:8px;"><?php _e('Thumbnail', $weluka_themename); ?></div>
            <label class="selectit" style="margin-right:8px;">
				<input type="radio" name="weluka_ptemp_blog_thumb" id="weluka_ptemp_blog_thumb_9" value="9" <?php if( $blog_thumb === 9 ) { echo 'checked="checked"'; } ?> />&nbsp;&nbsp;<?php _e('Non-select', $weluka_themename); ?>
			</label>
            <label class="selectit" style="margin-right:8px;">
				<input type="radio" name="weluka_ptemp_blog_thumb" id="weluka_ptemp_blog_thumb_0" value="0" <?php if( $blog_thumb === 0 ) { echo 'checked="checked"'; } ?> />&nbsp;&nbsp;<?php _e('Show', $weluka_themename); ?>
			</label>
            <label class="selectit">
				<input type="radio" name="weluka_ptemp_blog_thumb" id="weluka_ptemp_blog_thumb_1" value="1" <?php if( $blog_thumb === 1 ) { echo 'checked="checked"'; } ?> />&nbsp;&nbsp;<?php _e('Hide', $weluka_themename); ?>
			</label>
			<div style="font-weight:bold;margin-top:8px;"><?php _e('Excerpt', $weluka_themename); ?></div>
            <label class="selectit" style="margin-right:8px;">
				<input type="radio" name="weluka_ptemp_blog_excerpt" id="weluka_ptemp_blog_excerpt_9" value="9" <?php if( $blog_excerpt === 9 ) { echo 'checked="checked"'; } ?> />&nbsp;&nbsp;<?php _e('Non-select', $weluka_themename); ?>
			</label>
            <label class="selectit" style="margin-right:8px;">
				<input type="radio" name="weluka_ptemp_blog_excerpt" id="weluka_ptemp_blog_excerpt_0" value="0" <?php if( $blog_excerpt === 0 ) { echo 'checked="checked"'; } ?> />&nbsp;&nbsp;<?php _e('Show', $weluka_themename); ?>
			</label>
            <label class="selectit">
				<input type="radio" name="weluka_ptemp_blog_excerpt" id="weluka_ptemp_blog_excerpt_1" value="1" <?php if( $blog_excerpt === 1 ) { echo 'checked="checked"'; } ?> />&nbsp;&nbsp;<?php _e('Hide', $weluka_themename); ?>
			</label>
			<div style="font-weight:bold;margin-top:8px;"><?php _e('Readmore button', $weluka_themename); ?></div>
            <label class="selectit" style="margin-right:8px;">
				<input type="radio" name="weluka_ptemp_blog_readmore" id="weluka_ptemp_blog_readmore_9" value="9" <?php if( $blog_readmore === 9 ) { echo 'checked="checked"'; } ?> />&nbsp;&nbsp;<?php _e('Non-select', $weluka_themename); ?>
			</label>
            <label class="selectit" style="margin-right:8px;">
				<input type="radio" name="weluka_ptemp_blog_readmore" id="weluka_ptemp_blog_readmore_0" value="0" <?php if( $blog_readmore === 0 ) { echo 'checked="checked"'; } ?> />&nbsp;&nbsp;<?php _e('Show', $weluka_themename); ?>
			</label>
            <label class="selectit">
				<input type="radio" name="weluka_ptemp_blog_readmore" id="weluka_ptemp_blog_readmore_1" value="1" <?php if( $blog_readmore === 1 ) { echo 'checked="checked"'; } ?> />&nbsp;&nbsp;<?php _e('Hide', $weluka_themename); ?>
			</label>
        </div>
		<div style="margin: 13px 0 11px 4px; display: none;" class="weluka_pt_blog">
        	<p style="font-weight:bold;"><?php _e( 'Tagcloud', $weluka_themename ); ?></p>
            <label class="selectit" style="margin-right:8px;">
				<input type="radio" name="weluka_ptemp_blog_tagcloud" id="weluka_ptemp_blog_tagcloud_9" value="9" <?php if( $blog_tagcloud === 9 ) { echo 'checked="checked"'; } ?> />&nbsp;&nbsp;<?php _e('Non-select', $weluka_themename); ?>
			</label>
            <label class="selectit" style="margin-right:8px;">
				<input type="radio" name="weluka_ptemp_blog_tagcloud" id="weluka_ptemp_blog_tagcloud_0" value="0" <?php if( $blog_tagcloud === 0 ) { echo 'checked="checked"'; } ?> />&nbsp;&nbsp;<?php _e('Show', $weluka_themename); ?>
			</label>
            <label class="selectit">
				<input type="radio" name="weluka_ptemp_blog_tagcloud" id="weluka_ptemp_blog_tagcloud_1" value="1" <?php if( $blog_tagcloud === 1 ) { echo 'checked="checked"'; } ?> />&nbsp;&nbsp;<?php _e('Hide', $weluka_themename); ?>
			</label>            
        </div>
		<div style="margin: 13px 0 11px 4px; display: none;" class="weluka_pt_blog">
        	<p style="font-weight:bold;"><?php _e( 'Tagcloud position to post list', $weluka_themename ); ?></p>
			<?php WelukaBuilder::get_instance()->weluka_display_tagcloud_pos_settings('weluka_ptemp_blog_tagcloud_pos', '', false, true, $blog_tagcloud_pos); ?>
        </div>
		<div style="margin: 13px 0 11px 4px; display: none;" class="weluka_pt_blog">
	       	<p style="font-weight:bold;"><?php _e( 'Post Date Format', $weluka_themename ); ?></p>
            <input type="text" id="weluka_ptemp_blog_date_format" name="weluka_ptemp_blog_date_format" value="<?php echo esc_attr( $blog_date_format ); ?>" />
		</div>
		<div style="margin: 13px 0 11px 4px; display: none;" class="weluka_pt_blog">
        	<p style="font-weight:bold;"><?php _e( 'View order', $weluka_themename ); ?></p>
			<select id="weluka_ptemp_blog_date_modify" name="weluka_ptemp_blog_date_modify" class="form-control">
			<?php $orderWay = array(
				''=> 9,
				 __('Registered', $weluka_themename)=> 0,
				 __('Updated', $weluka_themename)=> 1,
				 __('Random', $weluka_themename)=> 2
				); 
				foreach($orderWay as $k => $v){ 
					$orderSelected = $v === $blog_date_modify ? 'selected ="selected "' : '';
					echo '<option value="' . $v .'" '. $orderSelected . ' > ' . $k . '</option>';
				}
			?></select>
			<div style="margin-top:6px;" class="weluka_pt_blog"><?php _e('If you set the random, registered times will be shown in the list and the list order will be changed every hour at random.', $weluka_themename ); ?></div>
		</div>
		<?php //v1.1 add ?>
		<div style="margin: 13px 0 11px 4px; display: none;" class="weluka_pt_blog">
	       	<p style="font-weight:bold;"><?php _e( 'Title String Number', $weluka_themename ); ?></p>
            <input type="text" class="small-text" id="weluka_ptemp_blog_title_num" name="weluka_ptemp_blog_title_num" value="<?php echo esc_attr( $blog_title_num ); ?>" />
        	<div style="margin-top:6px;" class="weluka_pt_blog"><?php _e('ignore the post title character number set in weluka theme settings by typing and <strong>all</strong>, all characters will be shown.', $weluka_themename ); ?></div>
		</div>
		<?php //v1.1 addend ?>
		<div style="margin: 13px 0 11px 4px; display: none;" class="weluka_pt_blog">
	       	<p style="font-weight:bold;"><?php _e( 'Excerpt String Number', $weluka_themename ); ?></p>
            <input type="text" class="small-text" id="weluka_ptemp_blog_excerpt_num" name="weluka_ptemp_blog_excerpt_num" value="<?php echo esc_attr( $blog_excerpt_num ); ?>" />
			<div style="margin-top:6px;" class="weluka_pt_blog"><?php _e('ignore the excerpt string number set in weluka theme settings by typing and <strong>-1</strong>, all images and characters decorated by HTML will be shown.', $weluka_themename ); ?></div>
		</div>
		<div style="margin: 13px 0 11px 4px; display: none;" class="weluka_pt_blog">
	       	<p style="font-weight:bold;"><?php _e( 'List Format', $weluka_themename ); ?></p>
            <?php WelukaBuilder::get_instance()->weluka_display_listlayout_settings( 'weluka_ptemp_blog_format', '', false, true, $blog_format); ?>
			<div style="margin-top:10px;border:1px solid #ccc; padding:10px;">
            	<p style="font-weight:bold;margin-top:0;"><?php _e( 'Responsive',  $weluka_themename ); ?></p>
				<label><span style="margin-right:8px;"><?php _e('Small View(768px - 991px)', $weluka_themename ); ?></span></label>
        	    <?php WelukaBuilder::get_instance()->weluka_display_listlayout_settings( 'weluka_ptemp_blog_format_sm', '', false, true, $blog_format_sm); ?>
				<label><span style="margin-right:8px;"><?php _e('XSmall View(Less than 768px)', $weluka_themename ); ?></span></label>
        	    <?php WelukaBuilder::get_instance()->weluka_display_listlayout_settings( 'weluka_ptemp_blog_format_xs', '', false, true, $blog_format_xs); ?>
			</div>
		</div>
		<div style="margin: 13px 0 11px 4px; display: none;" class="weluka_pt_blog">
	       	<p style="font-weight:bold;"><?php _e( 'List Row Column', $weluka_themename ); ?></p>
            <?php WelukaBuilder::get_instance()->weluka_display_listlayout_rowcolumn_settings( 'weluka_ptemp_blog_cols', '', false, true, $blog_cols, false, true); ?>
			<div style="margin-top:10px;border:1px solid #ccc; padding:10px;">
             	<p style="font-weight:bold;margin-top:0;"><?php _e( 'Responsive', $weluka_themename ); ?></p>
				<label><span style="margin-right:8px;"><?php _e('Small View(768px - 991px)', $weluka_themename ); ?></span></label>
                <?php WelukaBuilder::get_instance()->weluka_display_listlayout_rowcolumn_settings( 'weluka_ptemp_blog_cols_sm', '', false, true, $blog_cols_sm, false, true); ?>
				<label><span style="margin-right:8px;"><?php _e('XSmall View(Less than 768px)', $weluka_themename ); ?></span></label>
                <?php WelukaBuilder::get_instance()->weluka_display_listlayout_rowcolumn_settings( 'weluka_ptemp_blog_cols_xs', '', false, true, $blog_cols_xs, false, true); ?>
			</div>
		</div>
		<div style="margin: 13px 0 11px 4px; display: none;" class="weluka_pt_blog">
	       	<p style="font-weight:bold;"><?php _e( 'List Button Text', $weluka_themename ); ?></p>
            <input type="text" id="weluka_ptemp_blog_button_text" name="weluka_ptemp_blog_button_text" value="<?php echo esc_attr( $blog_button_text ); ?>" />
		</div>

        <?php //v1.2 add ?>
		<div style="margin: 13px 0 11px 4px; display: none;" class="weluka_pt_blog">
	       	<p style="font-weight:bold;"><?php _e( 'Thumbnail height(px)', $weluka_themename ); ?></p>
            <label class="selectit" style="margin-right:8px;">
				<input type="radio" name="weluka_ptemp_blog_thumb_height_use" id="weluka_ptemp_blog_thumb_height_use_0" value="0" <?php if( $blog_thumb_height_use === 0 ) { echo 'checked="checked"'; } ?> />&nbsp;&nbsp;<?php _e('Theme settings enabled', $weluka_themename); ?>
			</label>
            <label class="selectit" style="margin-right:8px;">
				<input type="radio" name="weluka_ptemp_blog_thumb_height_use" id="weluka_ptemp_blog_thumb_height_use_1" value="1" <?php if( $blog_thumb_height_use === 1 ) { echo 'checked="checked"'; } ?> />&nbsp;&nbsp;<?php _e('Enable this setting', $weluka_themename); ?>
			</label>

            <div style="margin-top:8px;">
            	<input type="text" class="small-text" id="weluka_ptemp_blog_thumb_height" name="weluka_ptemp_blog_thumb_height" value="<?php echo esc_attr( $blog_thumb_height ); ?>" />&nbsp;&nbsp;px
        	</div>
			<div style="margin-top:10px;border:1px solid #ccc; padding:10px;">
             	<p style="font-weight:bold;margin-top:0;"><?php _e( 'Responsive', $weluka_themename ); ?></p>
				<label><span style="margin-right:8px;"><?php _e('Small View(768px - 991px)', $weluka_themename ); ?></span></label>
            	<div style="margin-bottom:6px;">
            		<input type="text" class="small-text" id="weluka_ptemp_blog_thumb_height_sm" name="weluka_ptemp_blog_thumb_height_sm" value="<?php echo esc_attr( $blog_thumb_height_sm ); ?>" />&nbsp;&nbsp;px
        		</div>

				<label><span style="margin-right:8px;"><?php _e('XSmall View(Less than 768px)', $weluka_themename ); ?></span></label>
            	<div>
            		<input type="text" class="small-text" id="weluka_ptemp_blog_thumb_height_xs" name="weluka_ptemp_blog_thumb_height_xs" value="<?php echo esc_attr( $blog_thumb_height_xs ); ?>" />&nbsp;&nbsp;px
        		</div>
			</div>
            <div style="margin-top:13px;" class="weluka_pt_blog"><?php _e('Please enter a height (px) If you want to align the height of the thumbnail. Set the height of responsive (or clear the height) If you want to set List Format is responsive.', $weluka_themename ); ?></div>
		</div>
		<div style="margin: 13px 0 11px 4px; display: none;" class="weluka_pt_blog">
	       	<p style="font-weight:bold;"><?php _e('Align the same line block height', $weluka_themename ); ?></p>
            <select id="weluka_ptemp_blog_height_eqh" name="weluka_ptemp_blog_height_eqh">
            	<option value="0" <?php if( $blog_height_eqh === 0 ) { echo 'selected="selected"'; } ?>><?php _e('Theme settings enabled', $weluka_themename); ?></option>
                <option value="1" <?php if( $blog_height_eqh === 1 ) { echo 'selected="selected"'; } ?>><?php _e('Make same height', $weluka_themename); ?></option>
                <option value="2" <?php if( $blog_height_eqh === 2 ) { echo 'selected="selected"'; } ?>><?php _e('Unjustified high', $weluka_themename); ?></option>
            </select>
            <div style="margin-top:13px;" class="weluka_pt_blog"><?php _e('If you align the height of the list articles block, set the List Format of responsive, customized List Row.', $weluka_themename); ?></div>
        </div>
		<div style="margin: 13px 0 11px 4px; display: none;" class="weluka_pt_blog">
	       	<p style="font-weight:bold;"><?php _e( 'Box Style', $weluka_themename ); ?></p>
            <select id="weluka_ptemp_blog_boxstyle" name="weluka_ptemp_blog_boxstyle">
            	<option value="0" <?php if( $blog_boxstyle == 0 ) { echo 'selected="selected"'; } ?>><?php _e('Theme settings enabled', $weluka_themename); ?></option>
                <option value="1" <?php if( $blog_boxstyle == 1 ) { echo 'selected="selected"'; } ?>><?php _e('Default', $weluka_themename); ?></option>
				<option value="weluka-listblock-allp" <?php if( $blog_boxstyle === 'weluka-listblock-allp' ) { echo 'selected="selected"'; } ?>><?php _e('All padding', $weluka_themename); ?></option>
                <option value="weluka-listblock-notthumbp" <?php if( $blog_boxstyle === 'weluka-listblock-notthumbp' ) { echo 'selected="selected"'; } ?>><?php _e('Thumbnail not padding', $weluka_themename); ?></option>
            </select>
        </div>
		<div style="margin: 13px 0 11px 4px; display: none;" class="weluka_pt_blog">
	       	<p style="font-weight:bold;"><?php _e( 'Box Shape', $weluka_themename ); ?></p>
	      	<select id="weluka_ptemp_blog_boxshape" name="weluka_ptemp_blog_boxshape">
            	<option value="0" <?php if( $blog_boxshape == 0 ) { echo 'selected="selected"'; } ?>><?php _e('Theme settings enabled', $weluka_themename); ?></option>
        		<option value="1" <?php if( $blog_boxshape == 1 ){ echo 'selected="selected"'; } ?>><?php _e('Square', $weluka_themename); ?></option>
        		<option value="img-rounded" <?php if( $blog_boxshape === 'img-rounded' ){ echo 'selected="selected"'; } ?>><?php _e('Round', $weluka_themename); ?></option>
           	</select>
        </div>
		<div style="margin: 13px 0 11px 4px; display: none;" class="weluka_pt_blog">
	       	<p style="font-weight:bold;"><?php _e( 'Box Shadow', $weluka_themename ); ?></p>
	      	<select id="weluka_ptemp_blog_boxshadow" name="weluka_ptemp_blog_boxshadow">
            	<option value="0" <?php if( $blog_boxshadow == 0 ) { echo 'selected="selected"'; } ?>><?php _e('Theme settings enabled', $weluka_themename); ?></option>
        		<option value="1" <?php if( $blog_boxshadow == 1 ){ echo 'selected="selected"'; } ?>><?php _e('No shadow box.', $weluka_themename); ?></option>
        		<option value="2" <?php if( $blog_boxshadow == 2 ){ echo 'selected="selected"'; } ?>><?php _e('Box shadow and', $weluka_themename); ?></option>
           	</select>
        </div>
		<div style="margin: 13px 0 11px 4px; display: none;" class="weluka_pt_blog">
	       	<p style="font-weight:bold;"><?php _e( 'Box Color', $weluka_themename ); ?></p>
	      	<select id="weluka_ptemp_blog_color_use" name="weluka_ptemp_blog_color_use">
            	<option value="0" <?php if( $blog_color_use === 0 ) { echo 'selected="selected"'; } ?>><?php _e('Theme color settings enabled', $weluka_themename); ?></option>
        		<option value="1" <?php if( $blog_color_use === 1 ){ echo 'selected="selected"'; } ?>><?php _e('Default', $weluka_themename); ?></option>
           	</select>
        </div>
        <?php //v1.2 addend ?>

        <div style="margin-top:13px;" class="weluka_pt_blog"><?php _e('Archive if you do not set the list style is set in weluka theme settings list style is applied.', $weluka_themename ); ?></div>
        <?php //v1.0.6 addend ?>
		<?php //v1.2.8 add ?>
		<div style="display:none;"> 
            	<script type="text/javascript">
					jQuery(document).ready(function($) {
						$('#weluka_ptemp_blog_excerpt_num').keyup(function(e) {
							if($(this).val() == -1 && $('#weluka_ptemp_blog_paging_type').val() == 2){
								$('html, body').animate({
								scrollTop: $('#weluka_paging_type_row').offset().top - $('#wpadminbar').height()
								}, 400);
								setTimeout(function(){$('#weluka_ptemp_blog_paging_type').val(1);},1750);
								$('#both-excerpt-scl').css('color','#ffc107');
							}
						});
						$('#weluka_ptemp_blog_paging_type').change(function() {
							if($('#weluka_ptemp_blog_excerpt_num').val() == -1 && $(this).val() == 2){
								$('#weluka_ptemp_blog_paging_type').val(1);
								$('#both-excerpt-scl').css('color','#ffc107');
							}
						});

					});
			</script>
		</div>
<?php
}

/**
 * @since 1.0
 * @update
 * ver1.0.6
 * ver1.1
 * ver1.1.1
 * ver1.2
 */
function weluka_page_template_save_meta( $post_id, $post ){
	global $pagenow, $weluka;
	
	if ( 'post.php' != $pagenow ) return $post_id;

	if ( 'page' != $post->post_type )
		return $post_id;
			
	if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) 
		return $post_id;
		
	$post_type = get_post_type_object( $post->post_type );
	if ( !current_user_can( $post_type->cap->edit_post, $post_id ) )
		return $post_id;
	
	if ( ! isset( $_POST['_wpnonce_weluka_ptemp_save'] ) || ! wp_verify_nonce( $_POST['_wpnonce_weluka_ptemp_save'], 'weluka_ptemp_nonce' ) )
        return $post_id;

	if ( !isset( $_POST["page_template"] ) )
		return $post_id;
		
	if ( !in_array( $_POST["page_template"], array('page-blog.php') ) )
		return $post_id;
		
	$temp_array = array();	
	if ( 'page-blog.php' == $_POST["page_template"] ) {
		if (isset($_POST["weluka_ptemp_blogcats"])) $temp_array['weluka_ptemp_blogcats'] = (array) $_POST["weluka_ptemp_blogcats"];
		//if (isset($_POST["weluka_ptemp_blog_perpage"])) $temp_array['weluka_ptemp_blog_perpage'] = (int) $_POST["weluka_ptemp_blog_perpage"];
		if (isset($_POST["weluka_ptemp_blog_perpage"])) $temp_array['weluka_ptemp_blog_perpage'] = $_POST["weluka_ptemp_blog_perpage"]; //v1.1 modify

		//v1.0.6 add
		if (isset($_POST["weluka_ptemp_blog_date"])) $temp_array['weluka_ptemp_blog_date'] = (int) $_POST["weluka_ptemp_blog_date"];
		if (isset($_POST["weluka_ptemp_blog_author"])) $temp_array['weluka_ptemp_blog_author'] = (int) $_POST["weluka_ptemp_blog_author"];
		if (isset($_POST["weluka_ptemp_blog_category"])) $temp_array['weluka_ptemp_blog_category'] = (int) $_POST["weluka_ptemp_blog_category"];
		if (isset($_POST["weluka_ptemp_blog_comment"])) $temp_array['weluka_ptemp_blog_comment'] = (int) $_POST["weluka_ptemp_blog_comment"];
		if (isset($_POST["weluka_ptemp_blog_tagcloud"])) $temp_array['weluka_ptemp_blog_tagcloud'] = (int) $_POST["weluka_ptemp_blog_tagcloud"];
		if (isset($_POST["weluka_ptemp_blog_tagcloud_pos"])) $temp_array['weluka_ptemp_blog_tagcloud_pos'] = $_POST["weluka_ptemp_blog_tagcloud_pos"];
		if (isset($_POST["weluka_ptemp_blog_date_format"])) $temp_array['weluka_ptemp_blog_date_format'] = $_POST["weluka_ptemp_blog_date_format"];
		if (isset($_POST["weluka_ptemp_blog_date_modify"])) $temp_array['weluka_ptemp_blog_date_modify'] = (int) $_POST["weluka_ptemp_blog_date_modify"];
		if (isset($_POST["weluka_ptemp_blog_excerpt_num"])) $temp_array['weluka_ptemp_blog_excerpt_num'] = $_POST["weluka_ptemp_blog_excerpt_num"];
		if (isset($_POST["weluka_ptemp_blog_format"])) $temp_array['weluka_ptemp_blog_format'] = $_POST["weluka_ptemp_blog_format"];
		if (isset($_POST["weluka_ptemp_blog_format_sm"])) $temp_array['weluka_ptemp_blog_format_sm'] = $_POST["weluka_ptemp_blog_format_sm"];
		if (isset($_POST["weluka_ptemp_blog_format_xs"])) $temp_array['weluka_ptemp_blog_format_xs'] = $_POST["weluka_ptemp_blog_format_xs"];
		if (isset($_POST["weluka_ptemp_blog_cols"])) $temp_array['weluka_ptemp_blog_cols'] = $_POST["weluka_ptemp_blog_cols"];
		if (isset($_POST["weluka_ptemp_blog_cols_sm"])) $temp_array['weluka_ptemp_blog_cols_sm'] = $_POST["weluka_ptemp_blog_cols_sm"];
		if (isset($_POST["weluka_ptemp_blog_cols_xs"])) $temp_array['weluka_ptemp_blog_cols_xs'] = $_POST["weluka_ptemp_blog_cols_xs"];
		if (isset($_POST["weluka_ptemp_blog_button_text"])) $temp_array['weluka_ptemp_blog_button_text'] = $_POST["weluka_ptemp_blog_button_text"];
		//v1.1 add
		if (isset($_POST["weluka_ptemp_blog_title_num"])) $temp_array['weluka_ptemp_blog_title_num'] = $_POST["weluka_ptemp_blog_title_num"];
		if (isset($_POST["weluka_ptemp_blog_paging_type"])) $temp_array['weluka_ptemp_blog_paging_type'] = $_POST["weluka_ptemp_blog_paging_type"];
		//v1.1 addend
		//v1.1.1 add
		if (isset($_POST["weluka_ptemp_blog_title"])) $temp_array['weluka_ptemp_blog_title'] = (int) $_POST["weluka_ptemp_blog_title"];
		if (isset($_POST["weluka_ptemp_blog_thumb"])) $temp_array['weluka_ptemp_blog_thumb'] = (int) $_POST["weluka_ptemp_blog_thumb"];
		if (isset($_POST["weluka_ptemp_blog_excerpt"])) $temp_array['weluka_ptemp_blog_excerpt'] = (int) $_POST["weluka_ptemp_blog_excerpt"];
		if (isset($_POST["weluka_ptemp_blog_readmore"])) $temp_array['weluka_ptemp_blog_readmore'] = (int) $_POST["weluka_ptemp_blog_readmore"];
		//v1.1.1 addend
		
		//v1.2 add
		if (isset($_POST["weluka_ptemp_blog_thumb_height_use"])) $temp_array['weluka_ptemp_blog_thumb_height_use'] = $_POST["weluka_ptemp_blog_thumb_height_use"];
		if (isset($_POST["weluka_ptemp_blog_thumb_height"])) $temp_array['weluka_ptemp_blog_thumb_height'] = trim( $_POST["weluka_ptemp_blog_thumb_height"] );
		if (isset($_POST["weluka_ptemp_blog_thumb_height_sm"])) $temp_array['weluka_ptemp_blog_thumb_height_sm'] = trim( $_POST["weluka_ptemp_blog_thumb_height_sm"] );
		if (isset($_POST["weluka_ptemp_blog_thumb_height_xs"])) $temp_array['weluka_ptemp_blog_thumb_height_xs'] = trim( $_POST["weluka_ptemp_blog_thumb_height_xs"] );
		if (isset($_POST["weluka_ptemp_blog_height_eqh"])) $temp_array['weluka_ptemp_blog_height_eqh'] = $_POST["weluka_ptemp_blog_height_eqh"];
		if (isset($_POST["weluka_ptemp_blog_boxstyle"])) $temp_array['weluka_ptemp_blog_boxstyle'] = $_POST["weluka_ptemp_blog_boxstyle"];
		if (isset($_POST["weluka_ptemp_blog_boxshape"])) $temp_array['weluka_ptemp_blog_boxshape'] = $_POST["weluka_ptemp_blog_boxshape"];
		if (isset($_POST["weluka_ptemp_blog_boxshadow"])) $temp_array['weluka_ptemp_blog_boxshadow'] = $_POST["weluka_ptemp_blog_boxshadow"];
		if (isset($_POST["weluka_ptemp_blog_color_use"])) $temp_array['weluka_ptemp_blog_color_use'] = $_POST["weluka_ptemp_blog_color_use"];
		//v1.2 addend
	}
	
	$weluka->save_postmeta($post_id, WELUKA_PTMP_POST_META_NAME, $temp_array);
}
/**** [ end admin page template metabox ] ****/

/**
 * @since 1.0
 * @update
 * ver1.0.6
 */
function get_weluka_theme_layout( $name ) {
	global $welukaThemeOptions;
	
	// v1.3 modify
	//$ret = $welukaThemeOptions[$name];
	$ret = !empty( $welukaThemeOptions[$name] ) ? $welukaThemeOptions[$name] : false;

	if( ! $ret ) {
		if( $name === WelukaThemeOptions::HOME_LAYOUT ) {
			$ret = WelukaThemeOptions::LAYOUT_ONE_COL;
		} elseif( $name === WelukaThemeOptions::ARCHIVE_LAYOUT ) {	//v1.0.6
			$ret = get_weluka_theme_layout( WelukaThemeOptions::COMMON_LAYOUT );
		} elseif( $name === WelukaThemeOptions::PAGE_LAYOUT ) {	//v1.0.6
			$ret = get_weluka_theme_layout( WelukaThemeOptions::COMMON_LAYOUT );
		} elseif( $name === WelukaThemeOptions::POST_LAYOUT ) {	//v1.0.6
			$ret = get_weluka_theme_layout( WelukaThemeOptions::COMMON_LAYOUT );
		} else {
			$ret = WelukaThemeOptions::LAYOUT_TWO_COL_LEFT;
		}
	}
	return $ret;
}

/**
 * @since 1.0
 */
function get_weluka_theme_color( $name = "" ) {
	global $welukaThemeColors;
	
	$ret = isset( $welukaThemeColors ) ? $welukaThemeColors : "";
	if( $name !== "" && $ret !== "" ) {
		$ret = isset( $ret[$name] ) ? $ret[$name] : "";
	}
	return $ret;
}

/**
 * home blog style pre_get_posts hook
 * @since 1.0
 */
function get_weluka_page_setting() {
	global $post, $weluka, $welukaBuilder;
	$ret = null;

	if ( is_front_page() || is_page() || is_single() ) {
		$mode = WelukaBuilder::CONTENT_POSTMETA_KEY_PUBLISH;
		if( $weluka->is_active_builder() || is_preview() ) {
			$mode = WelukaBuilder::CONTENT_POSTMETA_KEY_DRAFT;
		}
		$ret = $welukaBuilder->get_builder_data($post->ID, $mode, 'pagesetting' );
	}
	return $ret;
}

/**
 * @since 1.0
 */
function get_weluka_noimage() {
	global $welukaThemeOptions, $welukaDefaultNoImage;
	
	$ret = $welukaDefaultNoImage;
	if( ! empty( $welukaThemeOptions[WelukaThemeOptions::NO_IMAGE_FILE] ) ) {
		$ret = $welukaThemeOptions[WelukaThemeOptions::NO_IMAGE_FILE];
	}
	return $ret;
}

/**
 * @since 1.0
 */
function get_weluka_site_logo() {
	global $welukaThemeOptions;
	
	return ! empty( $welukaThemeOptions[WelukaThemeOptions::LOGO_IMAGE] ) ? $welukaThemeOptions[WelukaThemeOptions::LOGO_IMAGE] : ''; 
}

/**
 * @since 1.0
 * @update
 * ver1.2.5
 * add and modify divide applying custom header by page types.
 */

function get_weluka_custom_header( $setting ) {
	global $welukaBuilder, $welukaTheme, $welukaThemeOptions,$constDispHeader;

	$cptId	= ! empty( $setting['hd_id'] ) ? $setting['hd_id'] : '';
	
	
	if( $cptId === '' ) {
		
		if(defined ( $constDispHeader )){
			$hdTypeKey = get_custom_parts_key();
			$hdDispKey = WelukaTheme::DISP_KEY.constant($constDispHeader);
			
			$cptId = $hdTypeKey !== '' && ! empty( $welukaThemeOptions[$hdDispKey][$hdTypeKey] ) ? $welukaThemeOptions[$hdDispKey][$hdTypeKey] : '';
			
		}
		
		if( $cptId === '' ) {
			$cptId = !empty($welukaThemeOptions[WelukaThemeOptions::COMMON_PAGE_HEADER] ) ? $welukaThemeOptions[WelukaThemeOptions::COMMON_PAGE_HEADER] : '';
		}
		
	}

	$cptHd = '';
	if( $cptId !== '' ) {
		//get publish header content
		$cptHd = do_shortcode( $welukaBuilder->get_builder_data( $cptId, WelukaBuilder::CONTENT_POSTMETA_KEY_PUBLISH, 'content' ) );
	}

	return $cptHd;
}

/**
 * @since 1.0
 * @update
 * ver1.2.5
 * add and modify divide applying custom footer by page types.
 */

function get_weluka_custom_footer( $setting ) {
	global $welukaBuilder, $welukaTheme, $welukaThemeOptions;

	$cptId	= ! empty( $setting['ft_id'] ) ? $setting['ft_id'] : '';
	if( $cptId === '' ) {
		$constFooter = 'WelukaTheme::PLACE_PAGE_FOOTER';
		
		if( defined($constFooter) ){
			$ftTypeKey = get_custom_parts_key();
			$ftDispKey = WelukaTheme::DISP_KEY.constant($constFooter);

			$cptId = $ftTypeKey !== '' && ! empty( $welukaThemeOptions[$ftDispKey][$ftTypeKey] ) ? $welukaThemeOptions[$ftDispKey][$ftTypeKey] : '';
		}

		if( $cptId === '' ) {
			$cptId = !empty($welukaThemeOptions[WelukaThemeOptions::COMMON_PAGE_FOOTER] ) ? $welukaThemeOptions[WelukaThemeOptions::COMMON_PAGE_FOOTER] : '';
		}
	}
		
	$cptFt = '';
	if( $cptId !== '' ) {
		//get publish footer content
		$cptFt = do_shortcode( $welukaBuilder->get_builder_data( $cptId, WelukaBuilder::CONTENT_POSTMETA_KEY_PUBLISH, 'content' ) );
	}
	
	return $cptFt;
}

/**
 * @since 1.0
 * @update
 * ver1.2.5
 * add and modify divide applying custom sidebar by page types.
 */

function get_weluka_custom_sidebar( $setting, $mode = 'main' ) {
	global $welukaBuilder, $welukaTheme, $welukaThemeOptions;

	$settingKey	= $mode === 'sub' ? 'sd_sub_id' : 'sd_main_id';
	$optionKey	= $mode === 'sub' ? WelukaThemeOptions::COMMON_SIDEBAR_SUB : WelukaThemeOptions::COMMON_SIDEBAR_MAIN;
	

	$cptId	= ! empty( $setting[$settingKey] ) ? $setting[$settingKey] : '';
	if( $cptId === '' ) {
		$constSidebar = 'WelukaTheme::PLACE_SIDEBAR_MAIN';
		if(defined($constSidebar)){

			$sdTypeKey = get_custom_parts_key();
			$applyKey	= $mode === 'sub' ? WelukaTheme::DISP_KEY.WelukaTheme::PLACE_SIDEBAR_SUB : WelukaTheme::DISP_KEY.constant($constSidebar) ;

			$cptId = $sdTypeKey !== '' && ! empty( $welukaThemeOptions[$applyKey][$sdTypeKey] ) ? $welukaThemeOptions[$applyKey][$sdTypeKey] : '';
			
		}


		if( $cptId === '' ) {
			$cptId = ! empty( $welukaThemeOptions[$optionKey] ) ? $welukaThemeOptions[$optionKey] : '';
		}
	}
		
	$cptSd = '';
	if( $cptId !== '' ) {
		//get publish sidebar content
		$cptSd = do_shortcode( $welukaBuilder->get_builder_data( $cptId, WelukaBuilder::CONTENT_POSTMETA_KEY_PUBLISH, 'content' ) );
	}
	
	return $cptSd;
}



/**
 * home blog style pre_get_posts hook
 * @since 1.0
 * ver1.0.4
 * ver1.0.5.2
 */
function weluka_pre_home_query( $query ) {
	global $welukaThemeOptions;

	if( ! is_home() || is_admin() || ! $query->is_main_query() || ! is_a( $query, 'WP_Query' ) ) return;

	//home blog style post_per_page
	$_limit = ! empty( $welukaThemeOptions[WelukaThemeOptions::HOME_POST_NUM] ) ? (int) $welukaThemeOptions[WelukaThemeOptions::HOME_POST_NUM] : '';
	if( $_limit ) {
		$query->set( 'posts_per_page', $_limit );
	}

	$exclude_cats = ! empty( $welukaThemeOptions[WelukaThemeOptions::HOME_EXCLUDE_CATEGORY] ) ? (array) $welukaThemeOptions[WelukaThemeOptions::HOME_EXCLUDE_CATEGORY] : '';
	//if ( $exclude_cats ) $query->set( 'category__not_in', array_map( 'intval', $exclude_cats ) );
	//if ( $exclude_cats ) $query->set( 'category__not_in', $ex );
	//v1.0.5.3
	if ( $exclude_cats ) {
		$ex = explode(",", $exclude_cats[0]);
		$query->set( 'category__not_in', $ex );
	}
	
	//v1.0.4 add
	$const = 'WelukaThemeOptions::POST_DATE_DISPLAY_MODIFY';
	if( defined ( $const ) ) {
		// 0 is empty
		if( ! empty( $welukaThemeOptions[WelukaThemeOptions::POST_DATE_DISPLAY_MODIFY] ) ){
			// v 1.2.8 add 
			if ((int) $welukaThemeOptions[WelukaThemeOptions::POST_DATE_DISPLAY_MODIFY] === 1){
				$query->set( 'orderby', 'modified' );
			}elseif((int) $welukaThemeOptions[WelukaThemeOptions::POST_DATE_DISPLAY_MODIFY] === 2){
				$query->set( 'orderby', 'rand' );
			}
		}
	}
}

/**
 * archive pre_get_posts hook
 * @since 1.0
 * @update
 * ver1.0.4
 */
function weluka_pre_archive_query( $query ) {
	global $welukaThemeOptions;

	if( ( ! is_archive() && ! is_search() && ! is_tax() ) || is_admin() || ! $query->is_main_query() || ! is_a( $query, 'WP_Query' ) ) return;

	//archive or search or taxonomy post_per_page
	$_limit = ! empty( $welukaThemeOptions[WelukaThemeOptions::ARCHIVE_POST_NUM] ) ? (int) $welukaThemeOptions[WelukaThemeOptions::ARCHIVE_POST_NUM] : '';
	if( $_limit ) {
		$query->set( 'posts_per_page', $_limit );
	}

	//v1.0.4 add
	$const = 'WelukaThemeOptions::POST_DATE_DISPLAY_MODIFY';
	if( defined ( $const ) ) {
		if( ! empty( $welukaThemeOptions[WelukaThemeOptions::POST_DATE_DISPLAY_MODIFY] ) ){
			$query->set( 'orderby', 'modified' );
		}
	}
}

/**
 * pagination
 * @since 1.0
 * v1.2.9.6
 */
function weluka_pagination( $query = null, $pos = 'bottom', $echo = true ) {
	global $wp_query;

	$ct = '';
	$big = 999999999; // need an unlikely integer	
	$paging =  paginate_links( array(
//v1.2.9.6 modify
//		'base'    => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
		'base'    => str_replace( $big, '%#%', get_pagenum_link( $big, false ) ),
		'format'  => '?paged=%#%',
		'current' => max( 1, get_query_var( 'paged' ) ),
		'total'   => isset( $query ) ? $query->max_num_pages : $wp_query->max_num_pages
	) );
	
	if( !empty( $paging ) ) {
		$mg = $pos === 'bottom' ? ' weluka-mgtop-l weluka-mgbottom-xl' : ' weluka-mgbottom-l';
		$ct = '<div class="weluka-pagination sp-pad' . $mg . '">' . $paging . '</div>';
	}
	
	if( $echo ) { echo $ct; } else { return $ct; }
}

/**
 * Display navigation to next/previous comments when applicable.
 *
 * @since 1.0
 */
function weluka_comment_nav() {
	global $weluka_themename;
	
	// Are there comments to navigate through?
	if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) :
	?>
	<nav class="comment-navigation clearfix" role="navigation">
		<?php
			if ( $prev_link = get_previous_comments_link( __( '&laquo; Older Comments', $weluka_themename ) ) ) :
				printf( '<div class="prev pull-left">%s</div>', $prev_link );
			endif;

			if ( $next_link = get_next_comments_link( __( 'Newer Comments &raquo;', $weluka_themename ) ) ) :
				printf( '<div class="next pull-right">%s</div>', $next_link );
			endif;
		?>
	</nav><!-- .comment-navigation -->
	<?php
	endif;
}

/**
 * @since 1.0
 * @update
 *  1.2.6
 */
function weluka_register_sidebar() {
	global $weluka_themename;
	register_sidebar( array(
		'name' 			=> __( 'Header Right', $weluka_themename ),
		'id' 			=> 'sidebar-hd-1',
		'before_widget' => '<div id="%1$s" class="headerwidget widget">',
		'after_widget' 	=> '</div>',
		'before_title' 	=> '<h4 class="headerwidgettitle widgettitle">',
		'after_title' 	=> '</h4>',
	) );
	register_sidebar( array(
		'name' 			=> __( 'Footer Sidebar Left', $weluka_themename ),
		'id' 			=> 'sidebar-ft-1',
		'before_widget' => '<div id="%1$s" class="footerwidget widget">',
		'after_widget' 	=> '</div>',
		'before_title' 	=> '<h4 class="footerwidgettitle widgettitle">',
		'after_title' 	=> '</h4>',
	) );
	register_sidebar( array(
		'name' 			=> __( 'Footer Sidebar Center', $weluka_themename ),
		'id' 			=> 'sidebar-ft-2',
		'before_widget' => '<div id="%1$s" class="footerwidget widget">',
		'after_widget' 	=> '</div>',
		'before_title' 	=> '<h4 class="footerwidgettitle widgettitle">',
		'after_title' 	=> '</h4>',
	) );
	register_sidebar( array(
		'name' 			=> __( 'Footer Sidebar Right', $weluka_themename ),
		'id' 			=> 'sidebar-ft-3',
		'before_widget' => '<div id="%1$s" class="footerwidget widget">',
		'after_widget' 	=> '</div>',
		'before_title' 	=> '<h4 class="footerwidgettitle widgettitle">',
		'after_title' 	=> '</h4>',
	) );
	register_sidebar( array(
		'name' 			=> __( 'One Sidebar (left or right)', $weluka_themename ),
		'id' 			=> 'sidebar-1',
		'before_widget' => '<div id="%1$s" class="sidebarwidget widget sp-pad">',
		'after_widget' 	=> '</div>',
		'before_title' 	=> '<h4 class="sidewidgettitle widgettitle">',
		'after_title' 	=> '</h4>',
	) );
	register_sidebar( array(
		'name' 			=> __( '3 Colum Second Sidebar', $weluka_themename ),
		'id' 			=> 'sidebar-2',
		'before_widget' => '<div id="%1$s" class="sidebarwidget2 widget sp-pad">',
		'after_widget' 	=> '</div>',
		'before_title' 	=> '<h4 class="sidewidgettitle widgettitle">',
		'after_title' 	=> '</h4>',
	) );
	//add v1.2.6
	register_sidebar( array(
		'name' 			=> __( 'On Post Title', $weluka_themename ),
		'id' 			=> 'on-post-title',
		'before_widget' => '<div id="%1$s" class="post-titlewidget widget sp-pad">',
		'after_widget' 	=> '</div>',
		'before_title' 	=> '<h4 class="post-titlewidgettitle widgettitle">',
		'after_title' 	=> '</h4>',
	) );
	register_sidebar( array(
		'name' 			=> __( 'Under Post Title(under eyecatch image)', $weluka_themename ),
		'id' 			=> 'under-post-title',
		'before_widget' => '<div id="%1$s" class="u-post-titlewidget widget sp-pad">',
		'after_widget' 	=> '</div>',
		'before_title' 	=> '<h4 class="u-post-titlewidgettitle widgettitle">',
		'after_title' 	=> '</h4>',
	) );
	register_sidebar( array(
		'name' 			=> __( 'On Post commnent', $weluka_themename ),
		'id' 			=> 'on-post-comment',
		'before_widget' => '<div id="%1$s" class="post-commet-widget widget sp-pad">',
		'after_widget' 	=> '</div>',
		'before_title' 	=> '<h4 class="post-commentwidgettitle widgettitle">',
		'after_title' 	=> '</h4>',
	) );
	register_sidebar( array(
		'name' 			=> __( 'Under Post commnent', $weluka_themename ),
		'id' 			=> 'under-post-comment',
		'before_widget' => '<div id="%1$s" class="u-post-commentwidget widget sp-pad">',
		'after_widget' 	=> '</div>',
		'before_title' 	=> '<h4 class="u-post-commentwidgettitle widgettitle">',
		'after_title' 	=> '</h4>',
	) );
	
	register_sidebar( array(
		'name' 			=> __( 'On Page Title', $weluka_themename ),
		'id' 			=> 'on-page-title',
		'before_widget' => '<div id="%1$s" class="page-titlewidget widget sp-pad">',
		'after_widget' 	=> '</div>',
		'before_title' 	=> '<h4 class="page-titlewidgettitle widgettitle">',
		'after_title' 	=> '</h4>',
	) );
	register_sidebar( array(
		'name' 			=> __( 'Under Page Title(under eyecatch image)', $weluka_themename ),
		'id' 			=> 'under-page-title',
		'before_widget' => '<div id="%1$s" class="u-page-titlewidget widget sp-pad">',
		'after_widget' 	=> '</div>',
		'before_title' 	=> '<h4 class="u-page-titlewidgettitle widgettitle">',
		'after_title' 	=> '</h4>',
	) );
	register_sidebar( array(
		'name' 			=> __( 'End of Page', $weluka_themename ),
		'id' 			=> 'end-page',
		'before_widget' => '<div id="%1$s" class="end-pagewidget widget sp-pad">',
		'after_widget' 	=> '</div>',
		'before_title' 	=> '<h4 class="end-pagewidgettitle widgettitle">',
		'after_title' 	=> '</h4>',
	) );

	register_sidebar( array(
		'name' 			=> __( 'Top of Top Page (blog style)', $weluka_themename ),
		'id' 			=> 'top-top',
		'before_widget' => '<div id="%1$s" class="top-topwidget widget sp-pad">',
		'after_widget' 	=> '</div>',
		'before_title' 	=> '<h4 class="top-topwidgettitle widgettitle">',
		'after_title' 	=> '</h4>',
	) );
	register_sidebar( array(
		'name' 			=> __( 'End of Top Page (blog style)', $weluka_themename ),
		'id' 			=> 'end-top',
		'before_widget' => '<div id="%1$s" class="end-topwidget widget sp-pad">',
		'after_widget' 	=> '</div>',
		'before_title' 	=> '<h4 class="end-topwidgettitle widgettitle">',
		'after_title' 	=> '</h4>',
	) );
	register_sidebar( array(
		'name' 			=> __( 'On Other Archive Page Title', $weluka_themename ),
		'id' 			=> 'on-other-archive-title',
		'before_widget' => '<div id="%1$s" class="on-o-archivewidget widget sp-pad">',
		'after_widget' 	=> '</div>',
		'before_title' 	=> '<h4 class="on-o-archivewidgettitle widgettitle">',
		'after_title' 	=> '</h4>',
	) );
	register_sidebar( array(
		'name' 			=> __( 'Under Other Archive Page Title', $weluka_themename ),
		'id' 			=> 'under-other-archive-title',
		'before_widget' => '<div id="%1$s" class="under-o-archivewidget widget sp-pad">',
		'after_widget' 	=> '</div>',
		'before_title' 	=> '<h4 class="under-o-archivewidgettitle widgettitle">',
		'after_title' 	=> '</h4>',
	) );
	register_sidebar( array(
		'name' 			=> __( 'End of Other Archive Page expect Search Page', $weluka_themename ),
		'id' 			=> 'end-other-archive',
		'before_widget' => '<div id="%1$s" class="end-o-archivewidget widget sp-pad">',
		'after_widget' 	=> '</div>',
		'before_title' 	=> '<h4 class="end-o-archivewidgettitle widgettitle">',
		'after_title' 	=> '</h4>',
	) );
}


/**
 * image_send_to_editor hook
 * @since 1.0
 */
function weluka_image_send_to_editor($html, $id, $caption, $title, $align, $url, $size, $alt) {
	$html = get_image_tag($id, $alt, '', $align, $size);

	if ( $url ) {
		//$rel = $rel ? ' "attachment wp-att-' . esc_attr($id).' gallery"' : '"gallery"';
		$rel = '"gallery"';
		$rel = "rel=" . $rel; 
		$html = '<a href="' . esc_url($url) . "\" class=\"fancybox\" $rel>$html</a>";
	}
	return $html;
}

/**
 * get headroom header fixed option
 * @since 1.0.1
 */
function get_weluka_theme_hdfixed_options(){
	global $welukaThemeOptions, $welukaTheme;
	$ret = array(WelukaThemeOptions::HEAD_FIXED => 0, WelukaThemeOptions::HEAD_FIXED_CLASS => '', WelukaThemeOptions::HEAD_FIXED_AUTO_HEIGHT_PADDING => 0 );
	if( !empty( $welukaThemeOptions[WelukaThemeOptions::HEAD_FIXED] ) ) { $ret[WelukaThemeOptions::HEAD_FIXED] = $welukaThemeOptions[WelukaThemeOptions::HEAD_FIXED]; }
	if( !empty( $welukaThemeOptions[WelukaThemeOptions::HEAD_FIXED_CLASS] ) ) { $ret[WelukaThemeOptions::HEAD_FIXED_CLASS] = $welukaThemeOptions[WelukaThemeOptions::HEAD_FIXED_CLASS]; }
	if( !empty( $welukaThemeOptions[WelukaThemeOptions::HEAD_FIXED_AUTO_HEIGHT_PADDING] ) ) { $ret[WelukaThemeOptions::HEAD_FIXED_AUTO_HEIGHT_PADDING] = $welukaThemeOptions[WelukaThemeOptions::HEAD_FIXED_AUTO_HEIGHT_PADDING]; }
	return $ret;
}

/**
 * wp_footer hook
 * @since 1.0.1
 * @update
 * ver1.1.3
 * ver1.2.2
 */
function weluka_theme_add_footer() {
	global $welukaThemeOptions; //v1.1.3

	$opts = get_weluka_theme_hdfixed_options();
	if( !empty( $opts[WelukaThemeOptions::HEAD_FIXED] ) ) {
		//v1.1.3 add
		$_const = 'WelukaThemeOptions::HEAD_FIXED_NO_SCROLL_DISPLAY';
		$_addHdClass = '';
		if ( defined ( $_const ) ) {
			//v1.2.2 modify if( $welukaThemeOptions[WelukaThemeOptions::HEAD_FIXED_NO_SCROLL_DISPLAY] ) { $_addHdClass = "weluka-hdfixed-noscroll"; }
			if( !empty( $welukaThemeOptions[WelukaThemeOptions::HEAD_FIXED_NO_SCROLL_DISPLAY] ) ) { $_addHdClass = "weluka-hdfixed-noscroll"; }
		}
?>
<script type="text/javascript">
	var _target = "<?php if( $opts[WelukaThemeOptions::HEAD_FIXED] == 2 && !empty( $opts[WelukaThemeOptions::HEAD_FIXED_CLASS] ) ){ echo '.' . $opts[WelukaThemeOptions::HEAD_FIXED_CLASS]; }else{ echo '#weluka-main-header'; } ?>";
	jQuery(document).ready(function($) {
		if($(_target).length) {
			$(_target).addClass('weluka-headroom');
			//v1.1.3 add
			<?php if( $_addHdClass ) { ?>
				$(_target).addClass('<?php echo $_addHdClass; ?>');
			<?php } ?>
			$('.weluka-headroom').addClass('navbar-fixed-top');
			$(".weluka-headroom").headroom({
   				"offset": 50, /*205,*/ /* ここで設定した数値だけスクロールしたらアクション */
   				"tolerance": 5, /* offset位置からここで設定した値文動いたらアクション */
   				"classes": {
   					"initial": "weluka-headroom",
   					"pinned": "headroom--pinned",
					"unpinned": "headroom--unpinned"
				}
			});
		}
	});

	<?php if( $opts[WelukaThemeOptions::HEAD_FIXED_AUTO_HEIGHT_PADDING] ) { ?>
	jQuery(window).on('load', welukaHeadroomResize );
	jQuery(window).on('resize', welukaHeadroomResize );
 	function welukaHeadroomResize() {
		var hdHeight = jQuery(_target).outerHeight();
		var _insertDom = '<div class="weluka-headroom-auto-pd" style="height:'  + hdHeight + 'px;"></div>';
		if( jQuery('.weluka-headroom-auto-pd').length ) {
			jQuery('.weluka-headroom-auto-pd').remove();
		}
		jQuery(_insertDom).insertAfter(_target);
	}
	<?php } ?>	
</script>
<?php
	}
}

/**
 * archive list block create
 * @since 1.0.6
 * @update
 * ver1.1.1
 * ver1.2
 * ver1.2
 */
function weluka_archivelist_block( $format, $data, $colMode = 'md' ) {
	$ret = '';
	//v1.2 add	
	$_eqh =  !empty( $data['eqh'] ) ? $data['eqh'] : '';
	$_media = !empty( $data['media'][$colMode] ) ? $data['media'][$colMode] : $data['media']['default'];
	$_boxStyle = '';
	if( !empty( $data['box_style'] ) ) { $_boxStyle = ' ' . $data['box_style']; }
	$_wrapStyle = '';
	if( !empty( $data['box_bgcolor'] ) ) { $_wrapStyle = ' style="background-color:' . $data['box_bgcolor'] . ';"'; }
	$_boxShape = '';
	if( !empty( $data['box_shape'] ) ) { $_boxShape = ' ' . $data['box_shape']; }
	$_boxShadow = '';
	if( !empty( $data['box_shadow'] ) ) { $_boxShadow = ' ' . $data['box_shadow']; }
	//v1.2 addend

	if($format === 'mediatop') {
		//v1.2 modify $ret = $data['media'];
		$ret = $_media;
		$ret .= $data['title'];
		$ret .= $data['meta'];
		$ret .= $data['tag_metabottom'];
		$ret .= $data['excerpt'];
		$ret .= $data['tag_bottom'];
		
		//v1.2 add modify
		if( !empty( $_eqh ) && !empty( $data['more'] ) ) {
			$ret .= '<div class="weluka-list-row-eqh-btmwrap">' . $data['more'] . '</div>';
		} else {
			$ret .= $data['more'];
		}

	} elseif ($format === 'mediamiddle') {
		$ret = $data['title'];
		$ret .= $data['meta'];
		$ret .= $data['tag_metabottom'];
		//v1.2 modify$ret .= $data['media'];
		$ret .= $_media;
		$ret .= $data['excerpt'];
		$ret .= $data['tag_bottom'];

		//v1.2 add modify
		if( !empty( $_eqh ) && !empty( $data['more'] ) ) {
			$ret .= '<div class="weluka-list-row-eqh-btmwrap">' . $data['more'] . '</div>';
		} else {
			$ret .= $data['more'];
		}

	} elseif ($format === 'mediabottom') {
		$ret = $data['title'];
		$ret .= $data['meta'];
		$ret .= $data['tag_metabottom'];
		$ret .= $data['excerpt'];
		$ret .= $data['tag_bottom'];
		//v1.2 modify $ret .= $data['more'];
		//v1.2 modify $ret .= $data['media'];
		//v1.2 add modify
		if( !empty( $_eqh ) && ( !empty( $_media ) || !empty( $data['more'] ) ) ) {
			$ret .= '<div class="weluka-list-row-eqh-btmwrap">' . $data['more'] . $_media . '</div>';
		} else {
			$ret .= $data['more'] . $_media;
		}

	} elseif ($format === 'mediaright') {
		//v1.2 wrapper add
		$ret = '<div class="weluka-col weluka-col-xs-12"><div class="wrap' . $_boxStyle . $_boxShape . $_boxShadow . '"' . $_wrapStyle . '><div class="weluka-row clearfix ' . $_eqh . '">';

		//v1.1.1 modify
		//v1.2 modify if( $data['media'] ) {
		if( $_media ) {
			$ret .= '<div class="weluka-col weluka-col-' . $colMode . '-8 lt">';
		} else {
			$ret .= '<div class="weluka-col weluka-col-' . $colMode . '-12 lt">';
		}
		//v1.1.1 modify end
		$ret .= $data['title'];
		$ret .= $data['meta'];
		$ret .= $data['tag_metabottom'];
		$ret .= $data['excerpt'];
		$ret .= $data['tag_bottom'];
		//v1.2 add modify
		//$ret .= $data['more'];
		if( !empty( $_eqh ) && !empty( $data['more'] ) ) {
			$ret .= '<div class="weluka-list-row-eqh-btmwrap">' . $data['more'] . '</div>';
		} else {
			$ret .= $data['more'];
		}
		$ret .= '</div>';
		//v1.1.1 modify
		//v1.2 modify if( $data['media'] ) {
		if( $_media ) {
			//v1.2 modify $ret .= '<div class="weluka-col weluka-col-' . $colMode .'-4 rt">' . $data['media'] . '</div>';
			$ret .= '<div class="weluka-col weluka-col-' . $colMode .'-4 rt">' . $_media . '</div>';
		}
		$ret .= '</div></div></div>'; //v1.2 wrapper

	} elseif ($format === 'medialeft') {
		//v1.2 wrapper add
		$ret = '<div class="weluka-col weluka-col-xs-12"><div class="wrap' . $_boxStyle . $_boxShape . $_boxShadow . '"' . $_wrapStyle . '><div class="weluka-row clearfix ' . $_eqh . '">';
		
		//v1.1.1 modify
		//v1.2 modify if( $data['media'] ) {
		if( $_media ) {
			//v1.2 modify $ret = '<div class="weluka-col weluka-col-' . $colMode . '-4 lt">' . $data['media'] . '</div>';
			$ret .= '<div class="weluka-col weluka-col-' . $colMode . '-4 lt">' . $_media . '</div>';
			$ret .= '<div class="weluka-col weluka-col-' . $colMode . '-8 rt">';
		} else {
			$ret .= '<div class="weluka-col weluka-col-' . $colMode . '-12 rt">';
		}
		//v1.1.1 modify end
		$ret .= $data['title'];
		$ret .= $data['meta'];
		$ret .= $data['tag_metabottom'];
		$ret .= $data['excerpt'];
		$ret .= $data['tag_bottom'];

		//v1.2 add modify
		//$ret .= $data['more'];
		if( !empty( $_eqh ) && !empty( $data['more'] ) ) {
			$ret .= '<div class="weluka-list-row-eqh-btmwrap">' . $data['more'] . '</div>';
		} else {
			$ret .= $data['more'];
		}

		$ret .= '</div>';

		$ret .= '</div></div></div>'; //v1.2 wrapper
	}
	return $ret;
}

/**
 * archivelist cerate
 * @since 1.0.6
 * @update
 * ver1.2
 * $eqh ... add
 */
function weluka_archivelist( $format, $ct, $rowColumn, $rowCnt, $colNum, &$colCnt, $colMode = 'md', $eqh = '', $boxStyle = '', $boxBgColor = '', $boxShape = '', $boxShadow = '' ) {
	$ret = '';
	$topNoMargin = (int)$rowCnt === 0 ? ' top-nomargin' : '';

	//v1.2 add
	$_eqh = '';
	if( !empty( $eqh ) ) { $_eqh = ' ' . $eqh; }
		
	$_boxStyle = '';
	if( $boxStyle ) { $_boxStyle = ' ' . $boxStyle; }

	$_wrapStyle = '';
	if( $boxBgColor ) { $_wrapStyle = ' style="background-color:' . $boxBgColor . ';"'; }

	$_boxShape = '';
	if( $boxShape ) { $_boxShape = ' ' . $boxShape; }

	$_boxShadow = '';
	if( $boxShadow ) { $_boxShadow = ' ' . $boxShadow; }
	//v1.2 addend

 	if( $format === 'mediatop' ) {
		$colCnt++;
		if((int)$colCnt === 1) {
			$ret .= '<div class="weluka-list-row weluka-row clearfix' . $topNoMargin . ' ' . $format . ' ' . $_eqh . '">';
		}
		$ret .= '<div class="weluka-col weluka-col-' . $colMode . '-' . $colNum . '"><div class="wrap' . $_boxStyle . $_boxShape . $_boxShadow . '"' . $_wrapStyle . '>' . $ct . '</div></div>';
		
		if((int)$colCnt === (int)$rowColumn) {
			$ret .= '</div>';	//row end div
			$colCnt = 0;
		}

	}elseif( $format === 'mediamiddle' ) {
		$colCnt++;
		if((int)$colCnt === 1) {
			$ret .= '<div class="weluka-list-row weluka-row clearfix' . $topNoMargin . ' ' . $format . ' ' . $_eqh . '">';
		}
		$ret .= '<div class="weluka-col weluka-col-' . $colMode . '-' . $colNum . '"><div class="wrap' . $_boxStyle . $_boxShape . $_boxShadow . '"' . $_wrapStyle . '>' . $ct . '</div></div>';

		if((int)$colCnt === (int)$rowColumn) {
			$ret .= '</div>';	//row end div
			$colCnt = 0;
		}

	}elseif( $format === 'mediabottom' ) {
		$colCnt++;
		if((int)$colCnt === 1) {
			$ret .= '<div class="weluka-list-row weluka-row clearfix' . $topNoMargin . ' ' . $format . ' ' . $_eqh . '">';
		}
					
		$ret .= '<div class="weluka-col weluka-col-' . $colMode . '-' . $colNum . '"><div class="wrap' . $_boxStyle . $_boxShape . $_boxShadow . '"' . $_wrapStyle . '>' . $ct . '</div></div>';

		if((int)$colCnt === (int)$rowColumn) {
			$ret .= '</div>';	//row end div
			$colCnt = 0;
		}

	}elseif( $format === 'mediaright' ) {
		$ret = '<div class="weluka-list-row weluka-row clearfix' . $topNoMargin . ' ' . $format . '">' . $ct . '</div>';

	}elseif( $format === 'medialeft' ) {
		$ret = '<div class="weluka-list-row weluka-row clearfix' . $topNoMargin . ' ' . $format . '">' . $ct . '</div>';
	}
	return $ret;
}

/**
 * @update
 * ver1.2
 * eqh, colMode, colNum add
 */
function weluka_archivelist_end( $format, $rowColumn, $colCnt,  $eqh = '', $colMode = 'md', $colNum = 12) {
	$ret = '';
	if( $format !== 'medialeft' && $format !== 'mediaright' ) :
		if((int)$colCnt !== 0 && (int)$colCnt !== (int)$rowColumn) {
			//v1.2
			if( !empty( $eqh ) ) {
				//equal heightの際に最後の行でカラム数が足りないとカラムがフル幅になるのでダミーカラムを追加する。
				$addCnt = (int)$rowColumn - (int)$colCnt;
				for( $i = 0 ; $i < $addCnt; $i++ ) {
					$ret .= '<div class="weluka-col-' . $colMode . '-' . $colNum . ' weluka-list-blank-col"></div>';
				}
			}
			//$ret = '</div>';
			$ret .= '</div>';
		}
	endif;
	return $ret;
}

/**
 * @since
 * ver1.2.8
 * this function is enable to use bootstarp click event in archive lists.
 */

function weluka_archive_replace_id($ct, $colMode='md', &$count=0){
	global $weluka;
	$searchArray  = array('weluka-accordion-', 'weluka-collapse-', 'weluka-tab-content-');
	$replaceArray = $searchArray;
	$nodeId = $colMode === 'md' ? $weluka->create_node_id() : $colMode;
	foreach($replaceArray as &$ra){ $ra .= $nodeId . '-'; }
	unset($ra);
	$ret = str_replace($searchArray, $replaceArray, $ct, $count);
	return $ret;
}

/**
 * @update
 * ver1.2.5
 * add metabox to cutom heaader and things like that. 
 */
$applyCommmonKey = 'apply_common_';
$customdispNonce = 'weluka_apply_cst';

function apply_cst_page_array(){
	global $weluka_themename;
	$applyPages =  array(
		'blogtoppage'  =>__("Top page(blog style)",$weluka_themename),
		'categorypage' =>__("All categories",$weluka_themename),
		'searchpage'   =>__("Search",$weluka_themename),
		'otherwisepage'=>__("Otherwise archive(include custom taxonomy)",$weluka_themename),
		'fixedpage'    =>__("Fixed pages",$weluka_themename),
		'poster'       =>__("Post pages(include custom post pages)",$weluka_themename),
	);

	return $applyPages;
}

//for plugin check update v1.2.5
if (defined( $constDispHeader )){

	function get_custom_parts_key(){
		if ( is_front_page() && is_home() ) {
			return 'blogtoppage';
		} elseif ( is_category() ) {
			return 'categorypage';
		} elseif ( is_search()  ) {
			return 'searchpage';
		} elseif ( is_archive() ) {
			return 'otherwisepage';
		} elseif ( is_page() ) {
			return 'fixedpage';
		} elseif ( is_single() ) {
			return 'poster';
		} else {
			return '';
		}
	}


	function weluka_custom_parts_setting(){
		global $weluka_themename;


		add_action('save_post', 'weluka_cst_parts_save_opt');

		//Custom Header
		add_filter( 'manage_'.Weluka::$settings['cpt_hd'].'_posts_columns',  'custom_parts_columns');
		add_action( 'manage_'.Weluka::$settings['cpt_hd'].'_posts_custom_column' ,  'cst_parts_a_column', 10, 2 );
		//callbackの引数は必ず小文字
		add_meta_box( 'weluka_custom_metabox', __('Header setting',$weluka_themename),'weluka_custom_options_inbox', Weluka::$settings['cpt_hd'], 'normal', 'high' , array('place' => WelukaTheme::PLACE_PAGE_HEADER) );

		//Custom Footer
		add_filter( 'manage_'.Weluka::$settings['cpt_ft'].'_posts_columns',  'custom_parts_columns');
		add_action( 'manage_'.Weluka::$settings['cpt_ft'].'_posts_custom_column' ,  'cst_parts_a_column', 10, 2 );
		add_meta_box( 'weluka_custom_metabox', __('Footer setting',$weluka_themename),'weluka_custom_options_inbox', Weluka::$settings['cpt_ft'], 'normal', 'high', array('place' => WelukaTheme::PLACE_PAGE_FOOTER) );

		//Custom Sidebar
		add_filter( 'manage_'.Weluka::$settings['cpt_sd'].'_posts_columns',  'custom_parts_columns');
		add_action( 'manage_'.Weluka::$settings['cpt_sd'].'_posts_custom_column' ,  'cst_parts_a_column', 10, 2 );
		add_meta_box( 'weluka_custom_metabox_mainside', __('Main sidebar setting',$weluka_themename),'weluka_custom_options_inbox', Weluka::$settings['cpt_sd'], 'normal', 'high' ,array('place' => WelukaTheme::PLACE_SIDEBAR_MAIN) );

		add_meta_box( 'weluka_custom_metabox_subside', __('Sub sidebar setting',$weluka_themename),'weluka_custom_options_inbox', Weluka::$settings['cpt_sd'], 'normal', 'core' ,array('place' => WelukaTheme::PLACE_SIDEBAR_SUB) );


	}

	function custom_parts_columns($columns) {
		global $weluka_themename;

		$copyclms = array();
		$nowPostType = get_post_type();

		//change columns order
		foreach($columns as $key => $val){
			if($key === 'author'){
				if($nowPostType === Weluka::$settings['cpt_hd']){
					$copyclms['setpage'] =  __('Header setting',$weluka_themename);
				}elseif($nowPostType === Weluka::$settings['cpt_ft']){
					$copyclms['setpage'] =  __('Footer setting',$weluka_themename);
				}elseif($nowPostType === Weluka::$settings['cpt_sd']){
					$copyclms['setpage']     = __('Main sidebar setting',$weluka_themename);
					$copyclms['setpage_sub'] = __('Sub sidebar setting',$weluka_themename);
				}
			}
			$copyclms[$key] = $val;
		}


		return $copyclms;
	}


	function cst_parts_a_column( $column, $post_id ) {
		global $post;
		global $weluka_themename;

		if($column !== 'setpage' && $column !== 'setpage_sub'){return '';}

		$cstOptionName = WelukaTheme::get_instance() -> get_option_name();
		$options       = get_option($cstOptionName);
		$applyPages    = apply_cst_page_array();
		$nowPostType   = get_post_type();

		$ret = '';
		$mainSub = '';

		if($nowPostType === Weluka::$settings['cpt_hd']){
			$place = WelukaTheme::PLACE_PAGE_HEADER;
		}elseif($nowPostType === Weluka::$settings['cpt_ft']){
			$place = WelukaTheme::PLACE_PAGE_FOOTER;
		}elseif($nowPostType === Weluka::$settings['cpt_sd']){
			if($column === 'setpage'){
				$place =  WelukaTheme::PLACE_SIDEBAR_MAIN;
			}else{
				$place =  WelukaTheme::PLACE_SIDEBAR_SUB;
			}
		}else{
			return '';
		}

		$dispkeyPlace = WelukaTheme::DISP_KEY.$place;
		$commonKey  = constant("WelukaTheme::COMMON_".strtoupper($place));

		$ret = isset($options[$commonKey]) &&  $options[$commonKey] == $post_id ? '<b>'.__("Common in the Site",$weluka_themename).'</b><br>' : '';
		if(isset($options[$dispkeyPlace])){
			foreach($options[$dispkeyPlace] as $key => $val){
				$ret .=  $val == $post_id ? $applyPages[$key] . '<br>' : '';
			}
		}


		echo rtrim($ret,'<br>');;

	}

	function weluka_custom_options_inbox($customPost, $metabox){
		global $applyCommmonKey;
		global $customdispNonce;
		global $weluka_themename;


		wp_nonce_field(wp_create_nonce(__FILE__), $customdispNonce );
		$place         = $metabox['args']['place'];
		$commonKey     = constant("WelukaTheme::COMMON_".strtoupper($place));
		
		//modify way to replace v 1.2.7
		$defaultString = __('Default '.ucwords(str_replace("_", " ", $place )).' : ',Weluka::$settings['plugin_name'] );
		$defaultString = rtrim($defaultString,' : ');

		$applyPages = apply_cst_page_array();


		$cstOptionName = WelukaTheme::get_instance() -> get_option_name();
		$options       = get_option($cstOptionName);

		//サイト共通のチェクボックス作成
		$applyCKeyPlace = $applyCommmonKey.$place;
		$checked = isset($options[$commonKey]) && $options[$commonKey] == $customPost -> ID  ? ' checked="checked"' : '' ;
		echo '<label class="selectit" for="'.$commonKey.'"><input type="checkbox" id="'.$commonKey.'" style="vertical-align: -0.3em;" class="customchecker" name="'.$applyCKeyPlace.'" value="1"'.$checked.'>&nbsp;'.__("Common in the Site",$weluka_themename).'('.$defaultString.')</label><br>';

		//その他チェクボックス作成
		$dispkeyPlace = WelukaTheme::DISP_KEY.$place;
		foreach($applyPages as $pageKey => $val){
			$checked = isset($options[$dispkeyPlace][$pageKey]) && $options[$dispkeyPlace][$pageKey] == $customPost -> ID ? ' checked="checked"' : '' ;	
			echo '<label class="selectit" for="'.$place.'_'.$pageKey.'"><input type="checkbox" style="vertical-align: -0.3em;" id="'.$place.'_'.$pageKey.'" class="customchecker" name="'.$dispkeyPlace.'['.$pageKey.']" value="1"'.$checked.'>&nbsp;'.$val.'</label><br>';
		}

	}



	function weluka_cst_parts_save_opt(){
		global $post_id;
		global $applyCommmonKey;
		global $customdispNonce;


		$my_nonce = isset($_POST[$customdispNonce]) ? $_POST[$customdispNonce] : null;
		if(!wp_verify_nonce($my_nonce, wp_create_nonce(__FILE__))) {
			return $post_id;
		}
		if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) { return $post_id; }
		if(!current_user_can('edit_post', $post_id)) { return $post_id; }
		$errmsg = "";


		try{
			$applyPages = apply_cst_page_array();

			$cstOptionName = WelukaTheme::get_instance() -> get_option_name();
			$options = get_option($cstOptionName);

			$nowPostType =  get_post_type();
			if($nowPostType === Weluka::$settings['cpt_hd']){
				$place = WelukaTheme::PLACE_PAGE_HEADER;
			}elseif($nowPostType === Weluka::$settings['cpt_ft']){
				$place = WelukaTheme::PLACE_PAGE_FOOTER;
			}elseif($nowPostType === Weluka::$settings['cpt_sd']){
				$place = WelukaTheme::PLACE_SIDEBAR_MAIN;
				$applyCKeyPlace = $applyCommmonKey.$place;
				$dispkeyPlace = WelukaTheme::DISP_KEY.$place;
				$commonKey  = constant("WelukaTheme::COMMON_".strtoupper($place));

				//サイト共通
				if(isset($_POST[$applyCKeyPlace]) &&  $_POST[$applyCKeyPlace] === '1'){
					$options[$commonKey] = $post_id;
				}elseif(isset($options[$commonKey]) && $options[$commonKey] == $post_id){
					$options[$commonKey] = '';
				}

				//その他
				$applyMain = isset($_POST[$dispkeyPlace]) ? $_POST[$dispkeyPlace] : array();
				foreach(array_keys($applyPages) as $data){
					if(isset($applyMain[$data]) && $applyMain[$data] === '1'){
						$options[$dispkeyPlace][$data] = $post_id;
					}elseif(isset($options[$dispkeyPlace][$data]) && $options[$dispkeyPlace][$data] == $post_id){
						$options[$dispkeyPlace][$data] = '';
					}
				}

				$place =  WelukaTheme::PLACE_SIDEBAR_SUB;
			}else{
				return $post_id;
			}


			$applyCKeyPlace = $applyCommmonKey.$place;
			$dispkeyPlace   = WelukaTheme::DISP_KEY.$place;
			$commonKey      = constant("WelukaTheme::COMMON_".strtoupper($place));
			$applyMain      = isset($_POST[$dispkeyPlace]) ? $_POST[$dispkeyPlace] : array();


			if(isset($_POST[$applyCKeyPlace])   && $_POST[$applyCKeyPlace] === '1'){
				$options[$commonKey] = $post_id;
			}elseif(isset($options[$commonKey]) && $options[$commonKey] == $post_id){
				$options[$commonKey] = '';
			}

			foreach(array_keys($applyPages) as $data){
				if(isset($applyMain[$data]) && $applyMain[$data] === '1'){
					$options[$dispkeyPlace][$data] = $post_id;
				}elseif(isset($options[$dispkeyPlace][$data]) && $options[$dispkeyPlace][$data] == $post_id){
					$options[$dispkeyPlace][$data] = '';
				}
			}

			Weluka::get_instance()->update_option( $cstOptionName, $options );

		}catch(Exception $e){
			$errmsg = sprintf( __("option registory error! (%s)", Weluka::$settings['plugin_name'] ), $e->getMessage() );
		}

		return $errmsg;
	}
} //for plugin check update v1.2.5 end

endif; //Weluka Plugin check endif;