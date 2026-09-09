<?php
/**
 * The archive conten template file.
 * blog tyle home or archive
 *
 * @package Weluka
 * @since 1.0
 * @update
 * ver1.0.1
 * ver1.0.4
 * ver1.0.6
 * ver1.1
 * ver1.1.1
 * ver1.1.4
 * ver1.2
 * ver1.2.8
 */
global $weluka_themename, $welukaThemeOptions, $post, $welukaBuilder;

	$rowColumn		= isset($welukaThemeOptions[WelukaThemeOptions::ARCHIVE_LIST_COLS]) && strlen(trim($welukaThemeOptions[WelukaThemeOptions::ARCHIVE_LIST_COLS])) > 0 ? trim($welukaThemeOptions[WelukaThemeOptions::ARCHIVE_LIST_COLS]) : 1;
	$colNum			= WelukaBuilder::MAX_COLUMN / (int)$rowColumn; //$rowColumn = (1 or 2 or 3 or 4 or 6)
	$rowCnt			= 0;
	$colCnt			= 0;
	//v1.1 isset empty modify
	$listFormat		= !empty( $welukaThemeOptions[WelukaThemeOptions::ARCHIVE_LIST_FORMAT] ) ? $welukaThemeOptions[WelukaThemeOptions::ARCHIVE_LIST_FORMAT] : 'mediatop';
	$dispItem		= !empty( $welukaThemeOptions[WelukaThemeOptions::POST_LIST_META_ARRAY] ) ? $welukaThemeOptions[WelukaThemeOptions::POST_LIST_META_ARRAY] : array();

	//ver 1.0.1
	$hide_tagcloud	= isset( $welukaThemeOptions[WelukaThemeOptions::HIDE_POSTLIST_TAGCLOUD] ) ? $welukaThemeOptions[WelukaThemeOptions::HIDE_POSTLIST_TAGCLOUD] : 0;
	$tagcloud_pos	= isset( $welukaThemeOptions[WelukaThemeOptions::POSTLIST_TAGCLOUD_POSITION] ) ? $welukaThemeOptions[WelukaThemeOptions::POSTLIST_TAGCLOUD_POSITION] : 'metabottom';
	
	$ct = "";
	
	//v1.0.6 add
	$listFormatSm	= '';
	$rowColumnSm	= 1;
	$colNumSm		= 12;
	$colCntSm		= 0;
	$ctSm			= '';
	$listFormatXs	= '';
	$rowColumnXs	= 1;
	$colNumXs		= 12;
	$colCntXs		= 0;
	$ctXs			= '';
	$_const			= 'WelukaThemeOptions::ARCHIVE_LIST_FORMAT_SM';
	$_const2		= 'WelukaThemeOptions::TITLE_STRING_NUM'; //v1.1 add

	if ( defined ( $_const ) ) :
		$listFormatSm	= isset( $welukaThemeOptions[WelukaThemeOptions::ARCHIVE_LIST_FORMAT_SM] ) && strlen(trim($welukaThemeOptions[WelukaThemeOptions::ARCHIVE_LIST_FORMAT_SM])) > 0 ? $welukaThemeOptions[WelukaThemeOptions::ARCHIVE_LIST_FORMAT_SM] : '';	
		$rowColumnSm	= isset($welukaThemeOptions[WelukaThemeOptions::ARCHIVE_LIST_COLS_SM]) && strlen(trim($welukaThemeOptions[WelukaThemeOptions::ARCHIVE_LIST_COLS_SM])) > 0 ? trim($welukaThemeOptions[WelukaThemeOptions::ARCHIVE_LIST_COLS_SM]) : 1;
	
		$colNumSm		= WelukaBuilder::MAX_COLUMN / (int)$rowColumnSm;
		$colCntSm		= 0;
		$ctSm = "";

		$listFormatXs	= isset( $welukaThemeOptions[WelukaThemeOptions::ARCHIVE_LIST_FORMAT_XS] ) && strlen(trim($welukaThemeOptions[WelukaThemeOptions::ARCHIVE_LIST_FORMAT_XS])) > 0 ? $welukaThemeOptions[WelukaThemeOptions::ARCHIVE_LIST_FORMAT_XS] : '';
		$rowColumnXs	= isset($welukaThemeOptions[WelukaThemeOptions::ARCHIVE_LIST_COLS_XS]) && strlen(trim($welukaThemeOptions[WelukaThemeOptions::ARCHIVE_LIST_COLS_XS])) > 0 ? trim($welukaThemeOptions[WelukaThemeOptions::ARCHIVE_LIST_COLS_XS]) : 1;
	
		$colNumXs		= WelukaBuilder::MAX_COLUMN / (int)$rowColumnXs;
		$colCntXs		= 0;
		$ctXs = "";
	endif;
	//v1.0.6 addend

	//v1.1 add
	$_titleNum = '';
	if ( defined ( $_const2 ) ) {
		if( !empty( $welukaThemeOptions[WelukaThemeOptions::TITLE_STRING_NUM] ) ) {
			$_titleNum = (int) $welukaThemeOptions[WelukaThemeOptions::TITLE_STRING_NUM];
		}
	}

	//v1.1.1 add
	$_constwk			= 'WelukaThemeOptions::POST_TITLE_HIDE';
	$_flgTitle			= 0;
	if ( defined ( $_constwk ) ) :
		if( !empty( $dispItem[WelukaThemeOptions::POST_TITLE_HIDE] ) ) { $_flgTitle = 1; }
	endif;

	$_constwk			= 'WelukaThemeOptions::POST_THUMB_HIDE';
	$_flgThumb			= 0;
	if ( defined ( $_constwk ) ) :
		if( !empty( $dispItem[WelukaThemeOptions::POST_THUMB_HIDE] ) ) { $_flgThumb = 1; }
	endif;

	$_constwk			= 'WelukaThemeOptions::POST_EXCERPT_HIDE';
	$_flgExcerpt		= 0;
	if ( defined ( $_constwk ) ) :
		if( !empty( $dispItem[WelukaThemeOptions::POST_EXCERPT_HIDE] ) ) { $_flgExcerpt = 1; }
	endif;

	$_constwk			= 'WelukaThemeOptions::POST_READMORE_HIDE';
	$_flgreadmore		= 0;
	if ( defined ( $_constwk ) ) :
		if( !empty( $dispItem[WelukaThemeOptions::POST_READMORE_HIDE] ) ) { $_flgReadmore = 1; }
	endif;
	//v1.1.1 addend

	//v1.2 add
	$_constwk			= 'WelukaThemeOptions::ARCHIVE_LIST_THUMB_HEIGHT';
	$_thumbHeight		= '';
	if ( defined ( $_constwk ) ) :
		if( !empty( $welukaThemeOptions[WelukaThemeOptions::ARCHIVE_LIST_THUMB_HEIGHT] ) ) { $_thumbHeight = trim( $welukaThemeOptions[WelukaThemeOptions::ARCHIVE_LIST_THUMB_HEIGHT] ); }
	endif;

	$_constwk			= 'WelukaThemeOptions::ARCHIVE_LIST_THUMB_HEIGHT_SM';
	$_thumbHeight_sm	= '';
	if ( defined ( $_constwk ) ) :
		if( !empty( $welukaThemeOptions[WelukaThemeOptions::ARCHIVE_LIST_THUMB_HEIGHT_SM] ) ) { $_thumbHeight_sm = trim( $welukaThemeOptions[WelukaThemeOptions::ARCHIVE_LIST_THUMB_HEIGHT_SM] ); }
	endif;

	$_constwk			= 'WelukaThemeOptions::ARCHIVE_LIST_THUMB_HEIGHT_XS';
	$_thumbHeight_xs	= '';
	if ( defined ( $_constwk ) ) :
		if( !empty( $welukaThemeOptions[WelukaThemeOptions::ARCHIVE_LIST_THUMB_HEIGHT_XS] ) ) { $_thumbHeight_xs = trim( $welukaThemeOptions[WelukaThemeOptions::ARCHIVE_LIST_THUMB_HEIGHT_XS] ); }
	endif;

	$_constwk	= 'WelukaThemeOptions::ARCHIVE_LIST_EQH';
	$_blockEqh	= '';
	if ( defined ( $_constwk ) ) :
		if( !empty( $welukaThemeOptions[WelukaThemeOptions::ARCHIVE_LIST_EQH] ) ) { $_blockEqh = 'weluka-list-row-eqh'; }
	endif;

	$_constwk	= 'WelukaThemeOptions::ARCHIVE_LIST_BOX_STYLE';
	$_blockBoxStyle	= '';
	if ( defined ( $_constwk ) ) :
		if( !empty( $welukaThemeOptions[WelukaThemeOptions::ARCHIVE_LIST_BOX_STYLE] ) ) { $_blockBoxStyle = $welukaThemeOptions[WelukaThemeOptions::ARCHIVE_LIST_BOX_STYLE]; }
	endif;

	$_constwk	= 'WelukaThemeOptions::ARCHIVE_LIST_BOX_STYLE_SHAPE';
	$_blockBoxStyleShape	= '';
	if ( defined ( $_constwk ) ) :
		if( !empty( $welukaThemeOptions[WelukaThemeOptions::ARCHIVE_LIST_BOX_STYLE_SHAPE] ) ) { $_blockBoxStyleShape = $welukaThemeOptions[WelukaThemeOptions::ARCHIVE_LIST_BOX_STYLE_SHAPE]; }
	endif;

	$_constwk	= 'WelukaThemeOptions::ARCHIVE_LIST_BOX_STYLE_SHADOW';
	$_blockBoxStyleShadow	= '';
	if ( defined ( $_constwk ) ) :
		if( !empty( $welukaThemeOptions[WelukaThemeOptions::ARCHIVE_LIST_BOX_STYLE_SHADOW] ) ) { $_blockBoxStyleShadow = 'weluka-listblock-boxshadow'; }
	endif;

	//add v1.2.7
	$_constwk	= 'WelukaThemeOptions::LAZY_LOAD';
	$_lazyLoad	=  0;
	if ( defined ( $_constwk ) ) :
		if( !empty( $welukaThemeOptions[WelukaThemeOptions::LAZY_LOAD] ) ) { $_lazyLoad = 1; }
	endif;
	//addend v1.2.7


	
	$_colorData = get_weluka_theme_color( WelukaThemeOptions::COLOR_ARRAY );
	$_blockBoxStyleBgColor	= '';
	if( !empty( $_colorData['list_block_bgcolor'] ) ){ $_blockBoxStyleBgColor = $_colorData['list_block_bgcolor']; }
	
	$_titleColor = '';
	if( !empty( $_colorData['list_title_color'] ) ){ $_titleColor = 'color:' . $_colorData['list_title_color'] . ';'; }
	$_dateColor = '';
	if( !empty( $_colorData['list_date_color'] ) ){ $_dateColor = 'color:' . $_colorData['list_date_color'] . ';'; }
	$_catColor = '';
	if( !empty( $_colorData['list_cat_color'] ) ){ $_catColor = 'color:' . $_colorData['list_cat_color'] . ';'; }
	$_authorColor = '';
	if( !empty( $_colorData['list_author_color'] ) ){ $_authorColor = 'color:' . $_colorData['list_author_color'] . ';'; }
	$_excerptColor = '';
	if( !empty( $_colorData['list_excerpt_color'] ) ){ $_excerptColor = 'color:' . $_colorData['list_excerpt_color'] . ';'; }
	$_morebtnColor = 'weluka-btn-primary';
	if( !empty( $_colorData['list_morebtn_color'] ) ){ $_morebtnColor = $_colorData['list_morebtn_color']; }
	//v1.2 addend

	while ( have_posts() ) : the_post();
		$_ct = "";
		//v1.0.6
		$_ctSm = "";
		$_ctXs = "";

		$_link	= get_the_permalink();
		$_title = get_the_title();

		//v1.1.1 modify		
		$titleHtml = "";
		if( empty( $_flgTitle ) ) {
			//v1.1 add
			$_titleDisp = $_title;
			if( $_titleNum !== '' ) { $_titleDisp = $welukaBuilder->truncate_string( $_title, $_titleNum, false, true ); } 
			//v1.1 addend
			//v1.2 modify
			//$titleHtml = '<h3 class="weluka-list-title">{%TITLE%}</h3>';
			$titleHtml = '<h3 class="weluka-list-title" style="' . $_titleColor . '">{%TITLE%}</h3>';
			
			$s = "";
			if( $_link ) {
				$s = '<a href="' . esc_url($_link) . '" title="' . esc_attr($_title) . '">' . $_titleDisp . '</a>'; //v1.1 $_titleDisp
			} else {
				$s = $_titleDisp; //v1.1 $_titleDisp
			}
			$titleHtml = str_replace("{%TITLE%}", $s, $titleHtml);
		}
		//v1.1.1 modify end
		
		//sticky post
		$stickyHtml = "";
		if ( is_sticky() && ! is_paged() ) {
			$stickyHtml = sprintf( '<span class="weluka-post-sticky">%s</span>', __( 'Featured', Weluka::$settings['plugin_name'] ) );
		}

		$dateHtml = "";
		$catHtml = "";
		$authorHtml = "";
		$commentHtml = "";

		//ver1.0.1 add
		$tagcloud_metabottom = '';
		$tagcloud_bottom = '';
		
		// post ony meta display
		if ( 'post' == get_post_type() ) {
			if( empty( $dispItem[WelukaThemeOptions::POST_META_DATE] ) ) {
				//$_date = get_the_date($welukaThemeOptions[WelukaThemeOptions::POST_DATE_FORMAT]);
				$_date = weluka_get_the_date(); //v1.0.4
				//v1.2 modify
				//$dateHtml = '<span class="weluka-post-date">' . $_date . '</span>';
				$dateHtml = '<span class="weluka-post-date" style="' . $_dateColor . '">' . $_date . '</span>';
			}

			if( empty( $dispItem[WelukaThemeOptions::POST_META_CATEGORY] ) ) {
				//v1.2 modify
				//$_tpl = '<span class="weluka-post-category-name"><a href="%1$s">%2$s</a></span>';
				$_tpl = '<span class="weluka-post-category-name"><a href="%1$s" style="' . $_catColor . '">%2$s</a></span>';
//v1.2.9.5 modify
//				$taxs = get_the_taxonomies( get_the_ID(), array( 'template' => '% %l', 'term_template' => $_tpl ) );
				$taxs = get_the_taxonomies( get_the_ID(), array( 'template' => '%2$l', 'term_template' => $_tpl ) );

				if( !empty( $taxs ) ){
					$n = 0;
					foreach ($taxs as $_key => $_val) {
						if( $_key == 'post_tag' ){ continue; } //tag not display
						$p = $n === 0 ? '&nbsp;|&nbsp;' : ',&nbsp;'; 
						$catHtml .= $p . $_val;
						$n++;
					}
				}
			}

			if( empty( $dispItem[WelukaThemeOptions::POST_META_AUTHOR] ) ) {
				$_author = get_the_author();
				//v1.2 modify
				//$authorHtml = '&nbsp;|&nbsp;<span class="weluka-post-author"><a href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html($_author) . '</a></span>';
				$authorHtml = '&nbsp;|&nbsp;<span class="weluka-post-author"><a href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '" style="' . $_authorColor . '">' . esc_html($_author) . '</a></span>';
			}

			if( empty( $dispItem[WelukaThemeOptions::POST_META_COMMENT] ) ) {
				$_cnt = get_comments_number();
				$commentHtml = '<span class="weluka-post-commentnum"><i class="fa fa-commenting"></i>' . $_cnt . '</span>';
			}
			
			//ver1.0.1 add
			$tagclouds = '';
			$tagclouds = get_the_tags();
			if( ! $hide_tagcloud && ( $tagcloud_pos === 'metabottom' || $tagcloud_pos === 'both' ) ) {
				$tagcloud_metabottom = weluka_tagcloud($tagclouds, '', 'weluka-mgtop-s', false); 
			}
			if( ! $hide_tagcloud && ( $tagcloud_pos === 'bottom' || $tagcloud_pos === 'both' ) ) {
				$tagcloud_bottom = weluka_tagcloud($tagclouds, '', 'weluka-mgtop-m weluka-mgbottom-m', false); 
			}
			//ver1.0.1 add end

		} // posttype=post endif

		//v1.2 modify
		//$mediaHtml = "";
		$mediaHtml = array( 'default' => '', 'md' => '', 'sm' => '', 'xs' => '' );
		//v1.1.1 modify
		if( empty( $_flgThumb ) ) {
			if( has_post_thumbnail() ){
				$size = "post-medium"; //"medium"; //"full"
				$thumbnail =  wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), $size );
				$_thumb_src = esc_url( $thumbnail[0] );

				//Nelio External Featured Image plugin 対応 v1.0.6
				if ( function_exists( 'uses_nelioefi' ) && uses_nelioefi( $post->ID ) ) {
					$_thumb_src = nelioefi_get_thumbnail_src( $post->ID );
				}
				//Nelio v1.0.6 addend
			} else {
				//no-image
				$_thumb_src = esc_url( get_weluka_noimage() );
			}

			//v1.2 modify
			//$mediaHtml = '<div class="weluka-list-media">';
			$_imgHtml = '<div class="weluka-list-media">';
			$media = array();
			if( $_link ) {
				$media['link']['action']	= WelukaBuilder::LINK_ACTION_GOTOLINK;
				$media['link']['href']		= $_link;
				$media['link']['target']	= "";
			}
			$media['alt']			= $_title;
			$media['fitwidth']		= 1;
			$media['title']			= $_title;
			$media['url']			= $_thumb_src;
			$media['border_color']	= "";
			$media['border_size']	= "";
			$media['border_style']	= "";
			$media['shape']			= "";
			$media['lazyload']		= $_lazyLoad; //add v1.2.7

			$align = 'weluka-text-center';
			if( $listFormat === 'medialeft' || $listFormat === 'mediaright' ){  $align = ''; }
			$media['class_name']	= $align;
			$_imgHtml .= $welukaBuilder->widget_img_html($media, false);
			if( $stickyHtml ) { $_imgHtml .= $stickyHtml; }
			if( $commentHtml ) { $_imgHtml .= $commentHtml; }
			$_imgHtml .= '</div>';
			$mediaHtml['default'] = $_imgHtml;

			//v1.2.4.4 modify
			//$_bgimg = '<div class="weluka-list-media"><a href="' . $_link . '" title="' . $_title . '"><span class="weluka-list-media-eqh" style="background-image:url(' .  $_thumb_src . ');height:%spx;"></span></a>';
			$_bgimg = '<div class="weluka-list-media"><a href="' . $_link . '" title="' . $_title . '"><span class="weluka-list-media-eqh" style="background-image:url(' .  $_thumb_src . ');height:{%THUMB_HEIGHT%}px;"></span></a>';
			//end v1.2.4.4 modify
			if( $stickyHtml ) { $_bgimg .= $stickyHtml; }
			if( $commentHtml ) { $_bgimg .= $commentHtml; }
			$_bgimg .= '</div>';

			//v1.2.4.4 modify
			//if( !empty( $_thumbHeight ) ) { $mediaHtml['md'] = sprintf( $_bgimg,  $_thumbHeight ); }
			//if( !empty( $_thumbHeight_sm ) ) { $mediaHtml['sm'] = sprintf( $_bgimg,  $_thumbHeight_sm ); }
			//if( !empty( $_thumbHeight_xs ) ) { $mediaHtml['xs'] = sprintf( $_bgimg,  $_thumbHeight_xs ); }
			if( !empty( $_thumbHeight ) ) { $mediaHtml['md'] = str_replace("{%THUMB_HEIGHT%}", $_thumbHeight,  $_bgimg); }
			if( !empty( $_thumbHeight_sm ) ) { $mediaHtml['sm'] = str_replace("{%THUMB_HEIGHT%}", $_thumbHeight_sm,  $_bgimg); }
			if( !empty( $_thumbHeight_xs ) ) { $mediaHtml['xs'] = str_replace("{%THUMB_HEIGHT%}", $_thumbHeight_xs,  $_bgimg); }
			//end v1.2.4.4 modify

			//if( $stickyHtml ) { $mediaHtml .= $stickyHtml; }
			//if( $commentHtml ) { $mediaHtml .= $commentHtml; }
			//$mediaHtml .= '</div>';
		}
		//v1.1.1 modify end
		
		$metaHtml = '';		
		if( $dateHtml || $catHtml || $authorHtml ) {
//v1.1 modify			$metaHtml = '<div class="weluka-list-meta">' . $dateHtml . $authorHtml . $catHtml . '</div>';
			$metaHtml = '<div class="weluka-list-meta">' . $dateHtml . $catHtml . $authorHtml . '</div>';
		}
		
		$excerptHtml = "";
		$replaceCnt = 0; // add v1.2.8
		//v1.1.1 modify
		if( empty( $_flgExcerpt ) ) {
			$_excerptNum	= !empty( $welukaThemeOptions[WelukaThemeOptions::EXCERPT_STRING_NUM] ) ? (int) $welukaThemeOptions[WelukaThemeOptions::EXCERPT_STRING_NUM] : '';

			//v1.2.7.1 modify
			//if( $_excerptNum !== '' ) { $_excerpt = $welukaBuilder->truncate_content( $post, $_excerptNum, false, true ); }
			//v1.2.8 modify
			//if( $_excerptNum !== '' ) { $_excerpt = $welukaBuilder->truncate_content( $post, $_excerptNum, false, true, 1 ); }
			
			// 検索結果のみ全記事表示から除外
			$_excerptNum = $_excerptNum === -1 && is_search() ? '' : $_excerptNum;
			$allHTMLclass = '';
			if( $_excerptNum !== '' ) { 
				if($_excerptNum === -1 && !$post->post_excerpt){
					// str_replaceと同じようにカウントを参照渡しで返す
					// コピー時のID重複回避
					$_excerpt = weluka_archive_replace_id(
						do_shortcode( $post->post_content ), 'md', $replaceCnt
					);
					$allHTMLclass = ' weluka-list-content-all';
				}else{$_excerpt = $welukaBuilder->truncate_content( $post, $_excerptNum, false, true, 1 ); }
			}else { $_excerpt = get_the_excerpt(); }
			//v1.2 modify
			//$excerptHtml = '<div class="weluka-list-content">' . $_excerpt . '</div>';
			$excerptHtml = '<div class="weluka-list-content'. $allHTMLclass .'" style="' . $_excerptColor . '">' . $_excerpt . '</div>';
		}
		//v1.1.1 modify end		
		$moreHtml = "";
		//v1.1.1 modify
		if( empty( $_flgReadmore ) ) {
			if( $_link ) {
				$button['text']	= !empty( $welukaThemeOptions[WelukaThemeOptions::MORE_BUTTON_TEXT] ) ? $welukaThemeOptions[WelukaThemeOptions::MORE_BUTTON_TEXT] : __('Read more', $weluka_themename);
				$button['link']['action']	= WelukaBuilder::LINK_ACTION_GOTOLINK;
				$button['link']['href']		= $_link;
				$button['link']['target']	= "";
				$button['button_color']		= $_morebtnColor; //v1.2 modify 'weluka-btn-primary';
				$button['class_name']		= 'weluka-text-right';
				$moreHtml	= $welukaBuilder->widget_button_html($button, false);
			}
		}
		//v1.1.1 modify end

/* no display
		//editlink
		$editHtml = '';
		$editLink = get_edit_post_link();
		if( $editLink ) { 
			$editHtml = '<span class="edit-link"><a href="' . $editLink . '">'. __( 'Edit', $weluka_themename ) .'</a></span>';
		}
*/

		//v1.0.6
		//v1.2 eqh ... add
		$_arr = array(
			'media'		=> $mediaHtml,
			'title'		=> $titleHtml,
			'meta'		=> $metaHtml,
			'excerpt'	=> $excerptHtml,
			'tag_metabottom'	=> $tagcloud_metabottom,
			'tag_bottom'	=> $tagcloud_bottom,
			'more'			=> $moreHtml,
			'eqh'			=> $_blockEqh,
			'box_style'		=> $_blockBoxStyle,
			'box_bgcolor'	=> $_blockBoxStyleBgColor,
			'box_shape'		=> $_blockBoxStyleShape,
			'box_shadow'	=> $_blockBoxStyleShadow
		);
		//v1.2 $_blockEqh add
		$_ct = weluka_archivelist_block( $listFormat, $_arr, 'md' );
		$_ctSm = weluka_archivelist_block( $listFormatSm, $_arr, 'sm' );
		$_ctXs = weluka_archivelist_block( $listFormatXs, $_arr, 'xs' );

		// add v1.2.8 hidden内と表示しているコンテンツのIDの重複回避
		if($replaceCnt > 0){
			if( $_ctSm ) { $_ctSm = weluka_archive_replace_id($_ctSm, 'sm');}
			if( $_ctXs ) { $_ctXs = weluka_archive_replace_id($_ctXs, 'xs');}
		}
		//v1.2 $_blockEqh ... add
		$ct .= weluka_archivelist( $listFormat, $_ct, $rowColumn, $rowCnt, $colNum, $colCnt, 'md', $_blockEqh, $_blockBoxStyle, $_blockBoxStyleBgColor, $_blockBoxStyleShape, $_blockBoxStyleShadow );
		$ctSm .= weluka_archivelist( $listFormatSm, $_ctSm, $rowColumnSm, $rowCnt, $colNumSm, $colCntSm, 'sm', $_blockEqh, $_blockBoxStyle, $_blockBoxStyleBgColor, $_blockBoxStyleShape, $_blockBoxStyleShadow );
		$ctXs .= weluka_archivelist( $listFormatXs, $_ctXs, $rowColumnXs, $rowCnt, $colNumXs, $colCntXs, 'xs', $_blockEqh, $_blockBoxStyle, $_blockBoxStyleBgColor, $_blockBoxStyleShape, $_blockBoxStyleShadow );
		//v1.0.6 modify end
		
		$rowCnt++;

	endwhile;

	//v1.0.6
	//v1.2 _blockEqh, colNum, colMode add 
	$ct .= weluka_archivelist_end( $listFormat, $rowColumn, $colCnt, $_blockEqh, 'md', $colNum );
	$ctSm .= weluka_archivelist_end( $listFormatSm, $rowColumnSm, $colCntSm, $_blockEqh, 'sm', $colNumSm );
	$ctXs .= weluka_archivelist_end( $listFormatXs, $rowColumnXs, $colCntXs, $_blockEqh, 'xs', $colNumXs );
	$hiddenClass = '';
	if( $listFormatSm ) { $hiddenClass = 'hidden-sm'; }
	if( $listFormatXs ) { $hiddenClass .= ' hidden-xs'; }
	$ct = '<div class="' . $hiddenClass . '">' . $ct . '</div>';

	if( $ctSm ) {
		$hiddenClass = '';
		$hiddenClass = 'hidden-lg hidden-md hidden-xs';
		$ctSm = '<div class="' . $hiddenClass . '">' . $ctSm . '</div>';
		$ct .= $ctSm;
	}
	if( $ctXs ) {
		$hiddenClass = '';
		$hiddenClass = 'hidden-lg hidden-md hidden-sm';
		$ctXs = '<div class="' . $hiddenClass . '">' . $ctXs . '</div>';
		$ct .= $ctXs;
	}

	echo do_shortcode(stripslashes($ct));
	
?>
