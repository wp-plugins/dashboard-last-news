<?php
define('DOING_AJAX', true);

require_once('../../../../wp-config.php');
require_once('../../../../wp-admin/includes/admin.php');

//load_plugin_textdomain('dashboard-last-news',DashboardLastNews_PATH . '/lang');

@header('Content-Type: ' . get_option('html_type') . '; charset=' . get_option('blog_charset'));

switch ( $_GET['jax'] ) 
{
	case 1 :
		DashboardLastNews::widget_output('dashboard_last_news');
	break;
	default :
		DashboardLastNews::widget_output('dashboard_last_news_' . $_GET['jax'] );
	break;
}
?>
