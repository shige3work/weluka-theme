<?php
/**
 * The main template file.
 * blog tyle home or archive
 *
 * @package Weluka
 * @since Weluka Theme 00 1.0
 * @update
 * ver1.1
 * ver1.2
 * 　
 */

if( check_weluka_plugin() !== "" ) return;

get_header();
global $weluka_themename, $welukaThemeOptions, $welukaBuilder; //v1.0.8 theme, builder add

if( isset( $_GET['mode'] ) && $_GET['mode'] === 'cp' ) {
	get_template_part( 'content', 'color' );

}elseif ( is_404() ) {
	get_template_part( 'content', 'none' );

} else {
	if( is_home() ) :
		//add 1.2.6
		if(is_front_page() && is_active_sidebar('top-top')) : 				        dynamic_sidebar('top-top'); 
		elseif(is_active_sidebar('on-other-archive-title')): 
			dynamic_sidebar('on-other-archive-title');
		endif;

	else :
	//add 1.2.6
	if(is_active_sidebar('on-other-archive-title') && !is_search() && is_archive()){dynamic_sidebar('on-other-archive-title');}
	
	//add v1.2.7
	$_constwk	 = 'WelukaThemeOptions::ARCHIVE_TITLE_ALIGN';
	$_titleAlign =  '';
		if ( defined ( $_constwk ) ) :
	if( !empty( $welukaThemeOptions[WelukaThemeOptions::ARCHIVE_TITLE_ALIGN] ) ) { 
		$_titleAlign = 	$welukaThemeOptions[WelukaThemeOptions::ARCHIVE_TITLE_ALIGN];}
	endif;
	
	$_constwk	 = 'WelukaThemeOptions::COMMON_TITLE_ALIGN';
	if (empty($_titleAlign) && defined ( $_constwk ) ) :
	if( !empty( $welukaThemeOptions[WelukaThemeOptions::COMMON_TITLE_ALIGN] ) ) {
		$_titleAlign = 	$welukaThemeOptions[WelukaThemeOptions::COMMON_TITLE_ALIGN];}
	endif;
	
?>
	<?php //v1.0.7 sp-pad delete //v1.2.7 add align ?>
	<h2 class="page-title <?php echo $_titleAlign; ?>">
<?php
		if ( is_search() ) :
			printf( __( 'Search Results for: %s', $weluka_themename ), '<span>' . get_search_query() . '</span>' );
	
		elseif ( is_tag() ) :
			single_tag_title();
	
		elseif ( is_tax() || is_category() ) :
			single_term_title();
	
		elseif ( is_author() ) :
			printf( __( 'Author: %s', $weluka_themename ), '<span class="vcard">' . get_the_author() . '</span>' );

		elseif ( is_day() ) :
			printf( __( 'Day: %s', $weluka_themename ), '<span>' . get_the_date() . '</span>' );

		elseif ( is_month() ) :
			printf( __( 'Month: %s', $weluka_themename ), '<span>' . get_the_date( _x( 'F Y', 'monthly archives date format', $weluka_themename ) ) . '</span>' );

		elseif ( is_year() ) :
			printf( __( 'Year: %s', $weluka_themename ), '<span>' . get_the_date( _x( 'Y', 'yearly archives date format', $weluka_themename ) ) . '</span>' );

		else :
			_e( 'Archives', $weluka_themename );

		endif;
?>
	</h2>
<?php
	endif;
	
	//add 1.2.6
	if(!is_search() && is_archive() && is_active_sidebar('under-other-archive-title')){
		dynamic_sidebar('under-other-archive-title');
	}

	if ( have_posts() ) :
		//v1.1 add
		$_pagingTypeConst	= 'WelukaThemeOptions::ARCHIVE_PAGING_TYPE';
		$_pagingType = 1;
		if ( defined ( $_pagingTypeConst ) ) {
			if( isset($welukaThemeOptions[WelukaThemeOptions::ARCHIVE_PAGING_TYPE]) && strlen( $welukaThemeOptions[WelukaThemeOptions::ARCHIVE_PAGING_TYPE] ) > 0 ) {
				 $_pagingType = (int) $welukaThemeOptions[WelukaThemeOptions::ARCHIVE_PAGING_TYPE];
			}
		}
		if( $_pagingType === 2 ) {
			$_url = Weluka::get_instance()->get_current_url();
			/* v1.2 modify <div class="weluka-jscroll" data-url="<?php echo $_url; ?>">*/
?>
			<div class="weluka-jscroll"><div class="weluka-jscroll-selector" data-url="<?php echo $_url; ?>">
<?php  }
		//v1.1 addend
?>
		<article class="archive-list sp-pad clearfix">
			<?php get_template_part( 'content', 'archive' ); ?>
		</article>
<?php
		weluka_pagination();
	   	//v1.1 add
		//v1.2 modify weluka-jscroll-selector div close
		if( $_pagingType === 2 ) : ?>
			</div></div><!-- /.weluka-jscroll -->
		<?php endif;
		//v1.1 addend
	else:
		get_template_part( 'content', 'none' );
	endif;
	
	//add v1.2.6
	if(is_home() && is_front_page()){
		if(is_active_sidebar('end-top')){dynamic_sidebar('end-top');}
	}else if(!is_search() && is_archive()){
		if(is_active_sidebar('end-other-archive')){dynamic_sidebar('end-other-archive');}
	}
	
} //404 endif
get_footer();
?>
