<?php 
/*
Plugin Name:  Dashboard: Last News
Plugin URI: http://wordpress.org/extend/plugins/dashboard-last-news/
Description: This is just a Dashboard widget, to manage Last News 
Author: Andre Renaut
Version: 2.8
Author URI: http://www.nogent94.com
*/

define ('DLN_FOLDER', 	basename(dirname(__FILE__)));
define ('DLN_PATH', 	PLUGINDIR . '/' . DLN_FOLDER . '/' );
define ('DLN_TMP', 	dirname(__FILE__) . '/');

class DashboardLastNews {
	
	const screen = 'dln_dashboard';

	function __construct() 
	{
		if (is_admin())
		{
		// for gettext
			load_plugin_textdomain('dashboard-last-news', DLN_PATH . 'dln-content/languages');
			add_action(	'admin_menu',		array(&$this, 'admin_menu'));
			add_action( 'wp_dashboard_setup',   array(&$this, 'wp_dashboard_setup') );
		}
		add_action(	'wp_ajax_dln_ajax',		array(&$this, 'wp_ajax_dln_ajax'));
	}

	function admin_menu()
	{
		add_options_page	(__('Last News','dashboard-last-news'),__('Last News','dashboard-last-news'), 	8, DLN_FOLDER . '/dln-admin/settings.php');
	}

	function wp_dashboard_setup()
	{
		wp_register_style ( self::screen, 		'/' . DLN_PATH . '/dln-includes/css/dln_slider.css' );
		wp_enqueue_style(self::screen);

		$pathcss		= DLN_TMP . 'dln-includes/css/colors_' . get_user_option('admin_color') . '.css';
		$css_url		= '/' . DLN_PATH . 'dln-includes/css/colors_' . get_user_option('admin_color') . '.css';
		$css_url_default 	= '/' . DLN_PATH . 'dln-includes/css/colors_fresh.css';
		$css_url		= (is_file($pathcss)) ? $css_url : $css_url_default;
		wp_register_style ( 'DLN_colors', 	$css_url);
		wp_enqueue_style  ( 'DLN_colors' );

		wp_register_script ( 'ui.slider',		'/' . DLN_PATH . '/dln-includes/js/ui/ui.slider.js', array('jquery-ui-core'), false, 1);
		wp_register_script ( self::screen,		'/' . DLN_PATH . '/dln-includes/js/dln_slider.js', array('ui.slider'), false, 1);
		wp_localize_script ( self::screen, 		'dln_sliderL10n', array( 
			'url' => admin_url('admin-ajax.php')
		));
		wp_enqueue_script(self::screen);

		$count = get_option('dashboard-last-news-widget-count');
		$count = ($count) ? $count : 1;

		require_once(DLN_TMP . '/dln-includes/class/DLN_Widget.class.php');
		for ( $i = 1; $i <= $count ; $i++ ) $dlns[] = new DLN_Widget($i);
	}

	function wp_ajax_dln_ajax()
	{
		require_once(DLN_TMP . '/dln-includes/class/DLN_Widget.class.php');
		$dlns = new DLN_Widget($_POST['i']);
		do_action('DLN_get_content_' . $_POST['i']);
		die();
	}
}
$DashboardLastNews = new DashboardLastNews();
?>