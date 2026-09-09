<?php
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Weluka Plugin Theme Options class
 * @package Weluka
 * @since Weluka Theme 00 1.0
 * 　
 */
#[\AllowDynamicProperties]
class WelukaThemeOptions extends WelukaTheme {
	
	/**
	 * Holds the singleton instance of this class
	 * @var WelukaUpdater
	 */

	/**
	 * @since 1.0
	 */
	private static $instance = false;

	/**
 	 * layout pattern
     * @since 1.0
     */
	const LAYOUT_ONE_COL		= '1col';
	const LAYOUT_TWO_COL_LEFT	= '2col_left';
	const LAYOUT_TWO_COL_RIGHT	= '2col_right';
	const LAYOUT_THREE_COL		= '3col';

	/**
	 * constructor
	 * @since 1.0
	 */
	function __construct() {
		global $weluka_themename;
		$theme_url = get_template_directory_uri();
		// layouts
		$this->theme_layouts = array(
			self::LAYOUT_ONE_COL		=> array( 'img' => Weluka::get_instance()->set_url( $theme_url . '/images/layout/layout_onecolum.gif' ) ),
			self::LAYOUT_TWO_COL_LEFT	=> array( 'img' => Weluka::get_instance()->set_url( $theme_url . '/images/layout/layout_twocolum_left.gif' ) ),
			self::LAYOUT_TWO_COL_RIGHT	=> array( 'img' => Weluka::get_instance()->set_url( $theme_url . '/images/layout/layout_twocolum_right.gif' ) ),
			self::LAYOUT_THREE_COL		=> array( 'img' => Weluka::get_instance()->set_url( $theme_url . '/images/layout/layout_threecolum.gif' ) )
		);
		$this->default_home_layout = self::LAYOUT_ONE_COL;
		$this->default_common_layout = self::LAYOUT_TWO_COL_RIGHT;

		// colors
		$this->theme_colors = array(
			'default'		=> array( 'lbl' => __('Default', $weluka_themename ), 'img' => Weluka::get_instance()->set_url( $theme_url . '/images/color/default.jpg' ) ),
			'blackboard'	=> array( 'lbl' => __('Blackboard', $weluka_themename), 'img' => Weluka::get_instance()->set_url( $theme_url . '/images/color/blackboard.jpg' ) ),
			'sportyflat'	=> array( 'lbl' => __('Sporty Flat', $weluka_themename), 'img' => Weluka::get_instance()->set_url( $theme_url . '/images/color/sportyflat.jpg' ) ),
			'sportyblock'	=> array( 'lbl' => __('Sporty Block', $weluka_themename), 'img' => Weluka::get_instance()->set_url( $theme_url . '/images/color/sportyblock.jpg' ) ),
			'elegantflat'	=> array( 'lbl' => __('Elegant Flat', $weluka_themename), 'img' => Weluka::get_instance()->set_url( $theme_url . '/images/color/elegantflat.jpg' ) ),
			'elegantblock'	=> array( 'lbl' => __('Elegant Block', $weluka_themename), 'img' => Weluka::get_instance()->set_url( $theme_url . '/images/color/elegantblock.jpg' ) ),
			'pastelflat'	=> array( 'lbl' => __('Pastel Flat', $weluka_themename), 'img' => Weluka::get_instance()->set_url( $theme_url . '/images/color/pastelflat.jpg' ) ),
			'pastelblock'	=> array( 'lbl' => __('Pastel Block', $weluka_themename), 'img' => Weluka::get_instance()->set_url( $theme_url . '/images/color/pastelblock.jpg' ) ),
			'greenflat'		=> array( 'lbl' => __('Green Flat', $weluka_themename), 'img' => Weluka::get_instance()->set_url( $theme_url . '/images/color/greenflat.jpg' ) ),
			'greenblock'	=> array( 'lbl' => __('Green Block', $weluka_themename), 'img' => Weluka::get_instance()->set_url( $theme_url . '/images/color/greenblock.jpg' ) ),
			'passionflat'	=> array( 'lbl' => __('Passion Flat', $weluka_themename), 'img' => Weluka::get_instance()->set_url( $theme_url . '/images/color/passionflat.jpg' ) ),
			'passionblock'	=> array( 'lbl' => __('Passion Block', $weluka_themename), 'img' => Weluka::get_instance()->set_url( $theme_url . '/images/color/passionblock.jpg' ) )
		);
		$this->default_color_type = 'default';

 	}

	/**
	 * singloton instance
	 * @since 1.0
	 */
	public static function get_instance() {
		if ( ! self::$instance ) {
			self::$instance = new WelukaThemeOptions;
		}
		return self::$instance;
	}

}
