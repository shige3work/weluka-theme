<?php
/**
 * The template for weluka custom sidebar 
 *
 * @package Weluka
 * @since Weluka Theme 00 1.0
 * 　
 */

get_header();

global $post, $welukaContainerClass, $welukaPageSetting, $welukaThemeOptions, $welukaLayout, $welukaRightSidebarNo, $weluka_copyright;
?>
				<div class="jumbo">
					<?php _e( 'MAIN CONTENTS AREA', $weluka_themename ); ?>
				</div>
            </div><?php //end #main-content ?>            
<?php
	switch( $welukaLayout ) {
		case WelukaThemeOptions::LAYOUT_THREE_COL :
?>
					<div id="weluka-leftside" class="weluka-sidebar weluka-col weluka-col-md-4" role="complementary">
						<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); the_content(); endwhile; endif; ?>
            		</div>
            	</div><!-- end .weluka-row -->
            </div><!-- end .weluka-col-md-9 -->
			<div id="weluka-rightside" class="weluka-sidebar weluka-col weluka-col-md-3" role="complementary">
            	<?php //case 3colum rightsidebar not edit mode ?> 
				<div class="jumbo">
					<?php _e( 'RIGHT SIDE CONTENTS AREA', $weluka_themename ); ?>
				</div>
            </div>
<?php
			break;
		case WelukaThemeOptions::LAYOUT_TWO_COL_RIGHT :
?>
			<div id="weluka-rightside" class="weluka-sidebar weluka-col weluka-col-md-3" role="complementary">
				<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); the_content(); endwhile; endif; ?>
            </div>
<?php
			break;
		case WelukaThemeOptions::LAYOUT_TWO_COL_LEFT :
		case WelukaThemeOptions::LAYOUT_ONE_COL :
?>
			<div id="weluka-leftside" class="weluka-sidebar weluka-col weluka-col-md-3" role="complementary">
				<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); the_content(); endwhile; endif; ?>
            </div>
<?php
			break;
	}
?>
			</div><?php //end .weluka-row ?>
		</div><?php //end $welukaContainerClass; ?>

</div><?php //end #main ?>

<?php
$_hideFooter = !empty( $welukaPageSetting['hide_ft'] ) ? $welukaPageSetting['hide_ft'] : 0; 
if( ! $_hideFooter ) : ?>

<footer id="weluka-main-footer">
<?php
	$cptFt	= get_weluka_custom_footer( $welukaPageSetting ); 

	if( ! empty( $cptFt ) ) :
		echo $cptFt;
	else :
?>
<div class="wrapper sp-pad">
	<div class="<?php echo $welukaContainerClass; ?>">
    	<div class="weluka-row clearfix">
		<?php
            $wd[0] = is_active_sidebar( 'sidebar-ft-1' );
            $wd[1] = is_active_sidebar( 'sidebar-ft-2' );
            $wd[2] = is_active_sidebar( 'sidebar-ft-3' );
				
			$trueCnt = 0;
			foreach( $wd as $val ) {
				if( $val ) { $trueCnt++; }
			}
			$col = $trueCnt > 0 ? (int) WelukaBuilder::MAX_COLUMN / (int) $trueCnt : 12;
			
			if ( $wd[0] ) { ?>
            	<div class="weluka-col weluka-col-md-<?php echo $col; ?>"><?php dynamic_sidebar( 'sidebar-ft-1' ); ?></div>
			<?php } ?>

			<?php if ( $wd[1] ) { ?>
            	<div class="weluka-col weluka-col-md-<?php echo $col; ?>"><?php dynamic_sidebar( 'sidebar-ft-2' ); ?></div>
			<?php } ?>

			<?php if ( $wd[2] ) { ?>
            	<div class="weluka-col weluka-col-md-<?php echo $col; ?>"><?php dynamic_sidebar( 'sidebar-ft-3' ); ?></div>
			<?php } ?>

        	<?php
            $menu = wp_nav_menu( array(
				'theme_location' => 'footer-menu',
				'fallback_cb' => '',
				'echo' => false
			) );

			if ( ! empty( $menu ) ) { ?>
            <div id="footer-nav" class="weluka-col weluka-col-md-12"><?php echo $menu; ?></div>
			<?php } ?>

        	<div class="weluka-col weluka-col-md-12 copyright">
            	<?php
					$copyright = ! empty( $welukaThemeOptions[WelukaThemeOptions::FOOTER_COPYRIGHT] ) ? $welukaThemeOptions[WelukaThemeOptions::FOOTER_COPYRIGHT] : $weluka_copyright;
					echo $copyright;
				?>
            </div>
        </div>
    </div>
</div><!-- /.wrapper -->
<?php
	endif; //cptFt endif
?>
</footer>

<?php endif; //_hideFooter endif ?>

<?php wp_footer(); ?>
</body>
</html>