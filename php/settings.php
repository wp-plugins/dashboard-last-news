<?php
switch (true)
{
	case ($_POST['formname'] == 'settform'):
		$dashboard_last_news_widget_count = (isset($_POST ['dashboard-last-news-widget-count'])) 	? $_POST ['dashboard-last-news-widget-count'] 	: 1;
		if (update_option('dashboard-last-news-widget-count', $dashboard_last_news_widget_count))
			DashboardLastNews::printMessage(__('Settings updated successfully !','dashboard-last-news'));
		else
			DashboardLastNews::printMessage(__('Settings NOT updated !!','dashboard-last-news'),false);
	break;
	default :
		$dashboard_last_news_widget_count = (get_option('dashboard-last-news-widget-count')) ? get_option('dashboard-last-news-widget-count') : 1;
	break;
}
?>

	<div class="wrap">
		<h2><?php _e('Dashboard Last News','dashboard-last-news'); ?></h2>
		<br/>
			<div id="fragment-1">
					<br/><?php include('setform.php'); ?><br/>
			</div>
	</div>


