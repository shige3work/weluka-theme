<?php
/**
 * The template for weluka custom footer 
 *
 * @package Weluka
 * @since Weluka Theme 00 1.0
 * 　
 */

get_header();

global $post, $welukaContainerClass, $welukaPageSetting, $welukaThemeOptions, $welukaLayout, $welukaRightSidebarNo;
?>
				<div class="jumbo">
					<?php _e( 'MAIN CONTENTS AREA', $weluka_themename ); ?>
				</div>
            </div><?php //end #main-content ?>            
<?php
	switch( $welukaLayout ) {
		case WelukaThemeOptions::LAYOUT_THREE_COL :
?>
					<div id="weluka-leftside" class="weluka-col weluka-col-md-4" role="complementary">
                    <?php
                    	$cptSide	= get_weluka_custom_sidebar( $welukaPageSetting, 'main' );
						if( ! empty( $cptSide ) ) : echo $cptSide;
						else :
							if ( is_active_sidebar( 'sidebar-1' ) ) { dynamic_sidebar( 'sidebar-1' ); }
						endif;
					?>
            		</div>
            	</div><!-- end .weluka-row -->
            </div><!-- end .weluka-col-md-9 -->
			<div id="weluka-rightside" class="weluka-col weluka-col-md-3" role="complementary">
            <?php
            	$cptSide	= get_weluka_custom_sidebar( $welukaPageSetting, 'sub' );
				if( ! empty( $cptSide ) ) : echo $cptSide;
				else :
					if ( is_active_sidebar( 'sidebar-2' ) ) { dynamic_sidebar( 'sidebar-2' ); }
				endif;
			?>
            </div>
<?php
			break;
		case WelukaThemeOptions::LAYOUT_TWO_COL_RIGHT :
?>
			<div id="weluka-rightside" class="weluka-col weluka-col-md-3" role="complementary">
            <?php
				$mode = isset( $welukaRightSidebarNo ) ? 'sub' : 'main';
            	$cptSide	= get_weluka_custom_sidebar( $welukaPageSetting, $mode );
				if( ! empty( $cptSide ) ) : echo $cptSide;
				else :
					$bar = isset( $welukaRightSidebarNo ) ? 'sidebar-' . $welukaRightSidebarNo : 'sidebar-1';
					if ( is_active_sidebar( $bar ) ) { dynamic_sidebar( $bar ); }
				endif;
			?>
            </div>
<?php
			break;
		case WelukaThemeOptions::LAYOUT_TWO_COL_LEFT :
?>
			<div id="weluka-leftside" class="weluka-col weluka-col-md-3" role="complementary">
            <?php
            	$cptSide	= get_weluka_custom_sidebar( $welukaPageSetting, 'main' );
				if( ! empty( $cptSide ) ) : echo $cptSide;
				else :
					if ( is_active_sidebar( 'sidebar-1' ) ) { dynamic_sidebar( 'sidebar-1' ); }
				endif;
			?>
            </div>
<?php
			break;
	}
?>
			</div><?php //end .weluka-row ?>
		</div><?php //end $welukaContainerClass; ?>

</div><?php //end #main ?>

<footer id="weluka-main-footer" class="weluka-custom-footer">
<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); the_content(); endwhile; endif; ?>
</footer>

<?php wp_footer(); ?>
</body>
</html>