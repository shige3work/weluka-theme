<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the body
 *
 * @package Weluka
 * @since Weluka Theme 00 1.0
 * @update
 * ver 1.1
 */

//if( check_weluka_plugin() !== "" ) return;

global $post, $welukaContainerClass, $welukaPageSetting, $welukaThemeOptions, $welukaLayout, $welukaOnepageMode, $welukaRightSidebarNo, $weluka_copyright;

//case page, single onepage parallax mode check
if( ! $welukaOnepageMode ) { //not onepage
?>
            </div><?php //end #main-content ?>            
<?php
	switch( $welukaLayout ) {
		case WelukaThemeOptions::LAYOUT_THREE_COL :
?>
					<div id="weluka-leftside" class="weluka-sidebar weluka-col weluka-col-md-4" role="complementary">
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
			<div id="weluka-rightside" class="weluka-sidebar weluka-col weluka-col-md-3" role="complementary">
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
			<div id="weluka-rightside" class="weluka-sidebar weluka-col weluka-col-md-3" role="complementary">
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
			<div id="weluka-leftside" class="weluka-sidebar weluka-col weluka-col-md-3" role="complementary">
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
<?php
} else { //onepageMode else
?>
	</div><?php //end #main-content ?>
<?php
} //end onepageMode ifend
?>

</div><?php //end #main ?>

<?php
//v1.1 add modify
if( ! is_singular( array( Weluka::$settings['cpt_hd'], Weluka::$settings['cpt_sd'] ) ) ) {
	$_hideFooter = !empty( $welukaPageSetting['hide_ft'] ) ? $welukaPageSetting['hide_ft'] : 0;
} else {
	$_hideFooter = 0;
}
//v1.1 add modify end
if( ! $_hideFooter ) :

	$cptFt	= get_weluka_custom_footer( $welukaPageSetting ); 

	$ftClass = "";
	if( $welukaOnepageMode ) { $ftClass = "weluka-parallax"; }
	if( ! empty( $cptFt ) ) { $ftClass .= " weluka-custom-footer"; }
?>

<footer id="weluka-main-footer" <?php if( $ftClass ) { echo 'class="' . $ftClass . '"'; } ?>>
<?php
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
					$copyright = ! empty( $welukaThemeOptions[WelukaThemeOptions::FOOTER_COPYRIGHT] ) ? stripslashes( $welukaThemeOptions[WelukaThemeOptions::FOOTER_COPYRIGHT] ) : $weluka_copyright;
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