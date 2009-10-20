<?php

function dln_printMessage($string, $success=true, $anchor = "message")
{
	if ($success) 	echo '<div id="'.$anchor.'" class="updated fade"><p>'.$string.'</p></div>';
	else	 		echo '<div id="'.$anchor.'" class="error   fade"><p>'.$string.'</p></div>';
}

switch (true)
{
	case ($_POST['formname'] == 'settform'):
		$dashboard_last_news_widget_count = (isset($_POST ['dashboard-last-news-widget-count'])) 	? $_POST ['dashboard-last-news-widget-count'] 	: 1;
		if (update_option('dashboard-last-news-widget-count', $dashboard_last_news_widget_count))
			dln_printMessage(__('Settings updated successfully !','dashboard-last-news'));
		else
			dln_printMessage(__('Settings NOT updated !!','dashboard-last-news'),false);
	break;
	default :
		$dashboard_last_news_widget_count = (get_option('dashboard-last-news-widget-count')) ? get_option('dashboard-last-news-widget-count') : 1;
	break;
}
include('includes/settings.php'); 
?>