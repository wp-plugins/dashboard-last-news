<?php 
/***************************************
Plugin Name:  Dashboard: Last News
Plugin URI: http://wordpress.org/extend/plugins/dashboard-last-news/
Description: This is just a Dashboard widget, to manage Last News 
Author: Andre Renaut
Version: 2.5.0.3
Author URI: http://www.nogent94.com
*/
/***************************************/

class DashboardLastNews {

	function DashboardLastNews() 
	{
		load_plugin_textdomain( 'dashboard-last-news', '/wp-content/plugins/dashboard-last-news/lang' );
		define ("DashboardLastNews_PATH", 	'wp-content/plugins/dashboard-last-news');
		if (is_admin())
		{
			add_action( 'wp_dashboard_setup',   array(&$this, 'register_widget') );
			add_filter( 'wp_dashboard_widgets', array(&$this, 'add_widget') );
			add_action(	'admin_head',		array(&$this, 'admin_head_DashboardLastNews'));
			add_action(	'admin_menu',		array(&$this, 'admin_menu'));
		}
	}

	function admin_menu()
	{
		add_options_page	(__('Last News','dashboard-last-news'),__('Last News','dashboard-last-news'), 	8, './' . DashboardLastNews_PATH . '/php/settings.php');
	}
	function admin_head_DashboardLastNews()
	{
		do_action( 'DashboardLastNews_admin_head' );
	}
	function admin_head()
	{
		$widgets = array();
		$widgets = DashboardLastNews::dashboard_has_widget();
?>
<link rel="stylesheet" href="../<?php echo DashboardLastNews_PATH; ?>/css/lastnewsslider.css" type="text/css" />
<style type='text/css'> 
<?php
		foreach ($widgets as $widget)
		{
			if (!( (isset($_GET['edit']) && ($_GET[edit] == $widget) ) ) ) echo "	div#$widget div { overflow:hidden; }\n";
?>
	div#<?php echo $widget; ?> div.dashboard-widget-content {padding-right:0;}
<?php
		}
?>
</style> 

<script type="text/javascript" src="../<?php echo DashboardLastNews_PATH; ?>/js/ui/jquery.dimensions.js"></script>
<script type="text/javascript" src="../<?php echo DashboardLastNews_PATH; ?>/js/ui/ui.mouse.js"></script>
<script type="text/javascript" src="../<?php echo DashboardLastNews_PATH; ?>/js/ui/ui.slider.js"></script>
<script type="text/javascript" src="../<?php echo DashboardLastNews_PATH; ?>/js/lastnewsslider.js"></script>
<script type="text/javascript">
	jQuery(function() {
<?php
		foreach ($widgets as $widget)
		{
			$num = 1;
			$x = explode('_',$widget);
			if (isset($x[3]))	$num = $x[3];
?>
		jQuery('#<?php echo $widget; ?> div.dashboard-widget-content').not( '.dashboard-widget-control' ).find( '.widget-loading' ).parent().load('<?php echo get_option('siteurl') . '/' . DashboardLastNews_PATH ; ?>/php/ajax.php?jax=<?php echo $num; ?>',{},function(){lastnewsslider ('<?php echo $widget; ?>');});
<?php
		}
?>
	});
</script>
<?php
	}
	function printMessage($string, $success=true, $anchor = "message")
	{
		if ($success) 	echo '<div id="'.$anchor.'" class="updated fade"><p>'.$string.'</p></div>';
		else	 		echo '<div id="'.$anchor.'" class="error   fade"><p>'.$string.'</p></div>';
	}
/***************************************/
	function get_lastnews($options,$widget_id)
	{
		if (is_dir('../wp-content/cache')) 	$dir = '../wp-content/cache';	
		else if (is_dir('../../../cache')) 	$dir = '../../../cache';		/* ajax */ 
			else { echo "<p>" . __( "Sorry ! but there is no cache folder as expected !<br/>Check the installation guidelines of this plugin.", 'dashboard-last-news' ) . "</p>\n"; return true; }

		$feed = new SimplePie();
//SimplePie default cache duration is 3600 sec (1 hour) 			
		$feed->set_cache_duration(3600);
		$feed->set_cache_location($dir);
		$feed->set_feed_url($options ['feeds']);
		$feed->init();

// to reset our feeds only ...
		if ( (isset ($_GET['fake'])) && ('ok' == $_GET['fake']) ) { $feed->__destruct(); unset($feed); return true;	}

		$feed->handle_content_type();
		if ($feed->get_items())
		{
?>
<div style='float:right'><div id='example' class='ui-slider-1' ><div class='ui-slider-handle'></div></div></div><div id='<?php echo $widget_id; ?>_ul' style='position:relative;margin-right:18px;z-index:-1;'><ul >
<?php
			$z = 1;
			$date_format = get_option('date_format') . ' G:i ';
			foreach ($feed->get_items() as $item)
			{
				$img    = ($options['image']) ? DashboardLastNews::get_image($item) : '';
				$style  = (empty($img)) ? 'padding:4px 2px 4px 2px;' : 'margin-left:-13px;padding:2px;list-style-type:none;' ;
?>
<li  style='line-height:100%;margin:0;<?php echo $style ?>'><?php if ($img) echo "<table style='margin:0;padding:0;'><tr style='margin:0;padding:0;'><td style='margin:0;padding:0;'>\n" . $img; ?><span class='lastnews'><a class='lastnews' href='<?php echo $item->get_permalink(); ?>' title='<?php echo $item->get_feed()->get_title(); ?>' target='_blank'><?php echo $item->get_title(); ?></a> &#8212; <abbr title="<?php echo mysql2date($date_format,$item->get_date('Y-m-d H:i:00')); ?>"><?php echo $item->get_date('Y/m/d'); ?></abbr></span><?php if ($img) echo "</tr></td></table>"; ?></li>
<?php
				$z++;
				if ( $z > $options ['maxlines'])  break;
			}
?>
</ul></div>
<?php
		}
		else
		{
			 echo "<p>" . __( "Sorry! no news !", 'dashboard-last-news' ) . "</p>\n";
		}
		$feed->__destruct(); 
		unset($feed);
		return true;
	}
	function get_image($item)
	{
		$img = array();

		$enclosure	=	$item->get_enclosure();
		if (!empty($enclosure))
		{
			$thumbnails = $enclosure->get_thumbnails();
			if ( !empty($thumbnails) ) foreach ( $thumbnails as $thumbnail) 	$img [] =  $thumbnail;
			if ( false !== stripos($enclosure->get_type(),'image') ) 		$img [] =  $enclosure->get_link(); 
			if ( 'image' == $enclosure->get_medium() ) 		 		$img [] =  $enclosure->get_link(); 
			$img [] = $item->get_feed()->get_image_link();
			$img [] = $item->get_feed()->get_image_url();
		}
		if ($img == array())
		{
			$content = $item->get_content();
			$output  = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $content, $matches, PREG_SET_ORDER);
			if (isset($matches [0] [1])) 							$wimg = str_replace(' ', '%20',$matches [0] [1]);

			$needles = array ('bookmark.gif');				/* filter any icon of social bookmarkers ! */
			if (!DashboardLastNews::in_string($wimg,$needles)) 			$img [] = $wimg;
		}

		$img = array_filter($img,array(&$this, 'not_empty'));
		$img = array_filter($img,array(&$this, 'is_url'));

		switch (count($img))
		{
			case 0 :
				return '';
			break;
			case 1 :
				return DashboardLastNews::format_img(reset($img));
			break;
			default :
				$default 	= reset($img);
				$img 		= array_filter($img,array(&$this, 'exclude'));
				if ( 0 == count($img) ) return DashboardLastNews::format_img($default);
				else 				return DashboardLastNews::format_img(reset($img));
			break;
		}
	}
/***************************************/
	function in_string($haystack,$needles) 	{ foreach ($needles as $needle) if ((stripos($haystack,$needle) !== false)) return true; return false; }
	function not_empty($var)			{ if (empty($var)) return false; return true; }
	function is_url   ($var)			{ if (stripos($var,'http://') === false) return false; return true; }
	function exclude  ($var)			{ $excludes = array('smilies'); foreach ($excludes as $exclude) if (stripos($var,$exclude) !== false) return false; return true; }
/***************************************/
	function format_img($url)
	{
		$hmax = 40;
		$wh = false;

		$wh = @ getimagesize($url);

		if ($wh === false) $wh [3] = ' height='.$hmax.'px ';
		else
		{
			if ( ($wh [1] >= $hmax) || (stripos($wh['mime'],'png')))
			{
				$h		= $hmax;
				$w		= round ( (($wh [0] * $hmax) / $wh [1]) );
				$wh [3]	= 'width="' . $w . 'px" height="' . $h . 'px"';
			}
		}
		return  "<img src='" . $url . "' class='lastnews' " .  $wh [3] . "/>";
	}
/***************************************/
	function get_feeds($widget)
	{
		$num = 1;
		$x = explode('_',$widget);
		if (isset($x[3]))	$num = $x[3];

		$url = get_option('siteurl') . '/' . DashboardLastNews_PATH . '/php/ajax.php?jax=' . $num . '&fake=ok';
		$result = DashboardLastNews::call_feeds($url); 
		if (!$result) return false;
		return true;
	}
	function call_feeds($url)
	{
		switch (true)
		{
			case (function_exists('fsockopen')) : 
				$timeout=1;
				@ $fp = fsockopen($url, $errno, $errstr, $timeout);
			break;
			case (extension_loaded('curl')) :
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_TIMEOUT, 1);
				curl_exec($ch);
				curl_close($ch);
			break;
			default :
				return false;
			break;
		}
		return true;
	}
/***************************************/
	function register_widget() 
	{
		add_action(	'DashboardLastNews_admin_head'	,array(&$this, 'admin_head'));

		$count = (get_option('dashboard-last-news-widget-count')) ? get_option('dashboard-last-news-widget-count') : 1;

		for ( $i = 1; $i <= $count ; $i++ )
		{
			$x = (1 == $i) ? 'dashboard_last_news' : 'dashboard_last_news_' . $i;

			wp_register_sidebar_widget( 	$x,
								 __( 'Last News', 'dashboard-last-news' ) . ' - ' . $i, 
								array(&$this, 'dashboard_empty'), 
								array( 'width' => 'half',  'height' => 'single' ),
								array(&$this, 'widget_has_feeds'), 
								array(&$this, 'widget_output')
							  );
			wp_register_widget_control( 	$x, 
								__( 'Last News', 'dashboard-last-news' ) . ' - ' . $i, 
								array(&$this, 'widget_control'), 
								array(), 
								array( 'widget_id' => $x ) 
								);
		}
	}

	function add_widget( $widgets ) 
	{
		global $wp_registered_widgets;
		$count = (get_option('dashboard-last-news-widget-count')) ? get_option('dashboard-last-news-widget-count') : 1;

		for ( $i = 1; $i <= $count ; $i = $i + 1 )
		{
			$x = (1 == $i) ? 'dashboard_last_news' : 'dashboard_last_news_' . $i;

			if ( !isset($wp_registered_widgets[$x]) ) return $widgets;

			array_splice( $widgets, 0, 0, $x );
		}
		return $widgets;
	}

	function dashboard_has_widget()
	{						// with wp2.5 we are always in !!!
		$w = array();
		$count = (get_option('dashboard-last-news-widget-count')) ? get_option('dashboard-last-news-widget-count') : 1;
		$options = get_option( 'dashboard_widget_options' );

		for ( $i = 1; $i <= $count ; $i++ )
		{
			$x = (1 == $i) ? 'dashboard_last_news' : 'dashboard_last_news_' . $i;

			if ( ( isset($options[$x]['feeds']) ) && ( ( 0 < count($options[$x]['feeds']) ) ) ) $w[] = $x;
		}
		return $w;
	}
/***************************************/
	function dashboard_empty( $args, $callback = false ) 
	{
		extract( $args, EXTR_SKIP );

		if ( !$options = get_option( 'dashboard_widget_options' ) )
			$options = array();
		if ( !isset($options[$widget_id]) )
			$options[$widget_id] = array();
		if ( !isset($options[$widget_id]['wtitle']) )
			$options[$widget_id]['wtitle'] = $widget_name;
		if ( ( isset($options[$widget_id]['wtitle']) ) && ( empty($options[$widget_id]['wtitle']) ) )
			$options[$widget_id]['wtitle'] = $widget_name;
		$options[$widget_id]['wtitle'] = stripslashes($options[$widget_id]['wtitle']);

		echo $before_widget;
		
		echo $before_title;
		echo $options[$widget_id]['wtitle'] ;
		echo $after_title;

// When in edit mode, the callback passed to this function is the widget_control callback
		if ( $callback && is_callable( $callback ) ) 
		{
			$args = array_slice( func_get_args(), 2 );
			array_unshift( $args, $widget_id );
			call_user_func_array( $callback, $args );
		}

		echo $after_widget;
	}
	
	function widget_has_feeds ( $widget_id, $callback ) 
	{
		$options = get_option( 'dashboard_widget_options');

		switch (true)
		{
			case ( ( isset($options[$widget_id]['feeds']) ) && ( 0 < count($options[$widget_id]['feeds']) ) ) :
				echo '<p class="widget-loading">' . __( 'Loading&#8230;' ) . '</p>';
				DashboardLastNews::get_feeds($widget_id); 
				return false;
			break;
			case ( ( isset($options[$widget_id]['feeds']) ) && ( 0 == count($options[$widget_id]['feeds']) ) ) :
				echo '<p class="widget-loading">' .__('No feed requested, you should edit the control panel&#8230;','dashboard-last-news' ) . '</p>';
				return false;
			break;
			case ( !isset($options[$widget_id]['feeds']) ) :
				echo '<p class="widget-loading">' . __('First time ! welcome ! you have to edit the control panel&#8230;','dashboard-last-news' ) . '</p>';
				return false;
			break;
			default :
			break;
		}

		if ( $callback && is_callable( $callback ) ) 
		{
			$args = array_slice( func_get_args(), 2 );
			 array_unshift( $args, $widget_id );
			 call_user_func_array( $callback, $args );
     		}
		return true;
	}

	function widget_output( $widget_id ) 
	{
		$options = get_option( 'dashboard_widget_options');
			
		if ( class_exists('SimplePie' ) )
		{
			if (	isset($options [$widget_id]['feeds']) ) 
			{
				if (	0 < count($options [$widget_id]['feeds']) ) 
				{
					DashboardLastNews::get_lastnews( $options [$widget_id],$widget_id);
				}
				else
				{
					echo "<p>" . __('No feed requested, you should edit the control panel&#8230;','dashboard-last-news' ) . "</p>\n";
				}
			}
			else
			{
				echo "<p>" . __('First time ! welcome ! you have to edit the control panel&#8230;','dashboard-last-news' ) . "</p>\n";
			}
		}
		else
		{
			echo sprintf( __('You need to add this <a href=\'%s\'>SimplePie plugin</a> to use this widget','dashboard-last-news' ), 'http://wordpress.org/extend/plugins/simplepie-core/download/');
		}
	}

	function widget_control( $args ) 
	{
		extract( $args );

		if ( !$widget_id )
			return false;

		if ( !$options = get_option( 'dashboard_widget_options' ) )
			$options = array();
		if ( !isset($options[$widget_id]) )
			$options[$widget_id] = array();

		if ( 'POST' == $_SERVER['REQUEST_METHOD'] && isset($_POST[$widget_id]) ) $options[$widget_id] = $_POST[$widget_id];

			if ( !isset($options[$widget_id]['wtitle']) ) 	$options[$widget_id]['wtitle'] = $widget_name;

			if ( !isset($options[$widget_id]['image']) )	$options[$widget_id]['image'] = false;

			if ( !isset($options[$widget_id]['maxfeeds']) )	$options[$widget_id]['maxfeeds'] = 3;
			$options[$widget_id]['maxfeeds'] = (int) $options[$widget_id]['maxfeeds'];

			if ( !isset($options[$widget_id]['maxlines']) )	$options[$widget_id]['maxlines'] = 10;
			$options[$widget_id]['maxlines'] = (int) $options[$widget_id]['maxlines'];

			if ( !isset($options[$widget_id]['feeds']) )	
				$options[$widget_id]['feeds'] = array();
			else
			{
				$feeds  = array_filter($options[$widget_id]['feeds'],array(&$this, 'not_empty'));
				$feeds  = array_slice ($feeds, 0, $options[$widget_id]['maxfeeds']); 
				$options[$widget_id]['feeds'] = $feeds;
			}

		if ( 'POST' == $_SERVER['REQUEST_METHOD'] && isset($_POST[$widget_id]) ) 
		{	
			update_option( 'dashboard_widget_options', $options );
			return;
		}
?>
	<p>
		<label for="recent-posts-extended-count"><?php _e('Widget Title : ', 'dashboard-last-news' ); ?>
			<input id="recent-posts-extended-count" type="text" size="30" maxlength="30" value="<?php echo $options[$widget_id]['wtitle'] ; ?>" name="<?php echo $widget_id; ?>[wtitle]" autocomplete="off"/>
		</label>
	</p>
	<p style='margin-bottom:3px;'>
		<label for="recent-posts-extended-count">
			<?php _e('Image (y/n) : ', 'dashboard-last-news' ); ?>&nbsp;
			<input type="checkbox" <?php checked(true,$options[$widget_id]['image']); ?> name="<?php echo $widget_id; ?>[image]"/>
			&nbsp;&nbsp;&nbsp;<?php _e('Feeds fields ?', 'dashboard-last-news' ); ?>&nbsp;
			<select id="recent-posts-extended-count" name="<?php echo $widget_id; ?>[maxfeeds]">
<?php
		for ( $i = 3; $i <= 10; $i++ ) echo "<option value='$i'" . ( $options[$widget_id]['maxfeeds'] == $i ? " selected='selected'" : '' ) . ">$i</option>";
?>
			</select>
			&nbsp;&nbsp;&nbsp;<?php _e('Lines to display ?', 'dashboard-last-news' ); ?>&nbsp;
			<select id="recent-posts-extended-count" name="<?php echo $widget_id; ?>[maxlines]">
<?php
		for ( $i = 3; $i <= 20; $i++ ) echo "<option value='$i'" . ( $options[$widget_id]['maxlines'] == $i ? " selected='selected'" : '' ) . ">$i</option>";
?>
			</select>
		</label>
	</p>
<?php
		$z = 1;
		foreach ( $options[$widget_id]['feeds'] as $feed )
		{
?>
	<p style='margin-top:0;'>
		<label for="rss-url-<?php echo $z; ?>"><?php _e('Fill the RSS or Atom URL here', 'dashboard-last-news' ); ?> :
			<input class="widefat" id="rss-url-<?php echo $z; ?>" name="<?php echo $widget_id; ?>[feeds][]" type="text" value="<?php echo $feed; ?>" />
		</label>
	</p>
<?php
			$z++;
			if ($z > $options[$widget_id]['maxfeeds']) break;
		}
		for ( $i = $z; $i <= $options[$widget_id]['maxfeeds']; $i++ )
		{
?>
	<p style='margin-top:0;'>
		<label for="rss-url-<?php echo $i; ?>"><?php _e('Fill the RSS or Atom URL here', 'dashboard-last-news' ); ?> :
			<input class="widefat" id="rss-url-<?php echo $i; ?>" name="<?php echo $widget_id; ?>[feeds][]" type="text" value="" />
		</label>
	</p>
<?php
		}
	}
}
// Start this plugin once all other plugins are fully loaded
add_action( 'plugins_loaded', create_function( '', 'global $DashboardLastNews; $DashboardLastNews = new DashboardLastNews();' ) );
?>