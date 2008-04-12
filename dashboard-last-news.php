<?php 
/***************************************************************************
Plugin Name:  Dashboard: Last News
Plugin URI: http://www.nogent94.com/
Description: This is just a Dashboard widget, to manage Last News 
Author: Andre Renaut
Version: 2.5
Author URI: http://a_renaut.club.fr/
*/
/**************************************************************************/

class DashboardLastNews {

	// Class initialization
	function DashboardLastNews() {
		if (is_admin())
		{
			load_plugin_textdomain( 'dashboard-last-news', '/wp-content/plugins/dashboard-last-news/lang' );

		// Add the widget to the dashboard
			add_action( 'wp_dashboard_setup',   array(&$this, 'register_widget') );
			add_filter( 'wp_dashboard_widgets', array(&$this, 'add_widget') );

			define ("DashboardLastNews_PATH", 		'wp-content/plugins/dashboard-last-news');

			add_action(	'admin_head'				,array(&$this, 'admin_head_DashboardLastNews'));
			add_action(	'admin_menu'				,array(&$this, 'admin_menu'));
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
		if($success){
			echo '<div id="'.$anchor.'" class="updated fade"><p>'.$string.'</p></div>';
	 	}else{
	 		echo '<div id="'.$anchor.'" class="error fade"><p>'.$string.'</p></div>';
	 	}
	}
/**************************************************************************/
	function get_lastnews($options,$widget_id)
	{
		if (!($options['feeds'] == array()))
		{
			$maxlines   = $options ['maxlines'];
			
			$feed = new SimplePie();
			$feed->set_feed_url($options ['feeds']);

			switch (true)
			{
				case (is_dir('../wp-content/cache')) :
					$dir = '../wp-content/cache';
				break;
//-- for ajax purpose
				case (is_dir('../../../cache'));
					$dir = '../../../cache';
				break;
//--
				default :
					echo "<p>" . __( "Sorry ! but there is no cache folder as expected !<br/>Check the installation guidelines of this plugin.", 'dashboard-last-news' ) . "</p>\n";
					return true; 
				break;
			}

			$feed->set_cache_location($dir);

//SimplePie default cache duration is 3600 sec (1 hour) 			
			$feed->set_cache_duration(3600);

			$feed->init();

// to reset our feeds only ...
			if ( (isset ($_GET['fake'])) && ('ok' == $_GET['fake']) )
			{
				$feed->__destruct(); 
				unset($feed);
				return true;
			}

			$feed->handle_content_type();

			if ($feed->get_items())
			{
?>
			<div style='float:right'><div id='example' class='ui-slider-1' ><div class='ui-slider-handle'></div></div></div>
			<div id='<?php echo $widget_id; ?>_ul' style='position:relative;margin-right:18px;z-index:-1;'>
				<ul >
<?php
				$z = 1;
				foreach ($feed->get_items() as $item)
				{

					$titl1 = (stripos($item->get_content(),'news.google') && stripos($item->get_title(),'&amp;#')) ? str_replace('&amp;#', '&#', $item->get_title()) : $item->get_title();

					$x      = $item->get_content();

					$img    = ($options['image']) ? DashboardLastNews::get_image($item) : '';

					$style  = (empty($img)) ? 'padding:4px 2px 4px 2px;' : 'margin-left:-13px;padding:2px;list-style-type:none;' ;

					$date   = mysql2date('d/m/Y G:i',$item->get_date('Y') .'-'. $item->get_date('m') .'-'. $item->get_date('d') .' '. $item->get_date('H') .':'. $item->get_date('i') .':00'); 	
					$date2  = mysql2date('Y/m/d',$item->get_date('Y') .'-'. $item->get_date('m') .'-'. $item->get_date('d') .' '. $item->get_date('H') .':'. $item->get_date('i') .':00'); 	
					$fiid   = $item->get_feed(); 	
					$from   = $fiid->get_title();
?>
					<li  style='line-height:100%;margin:0;<?php echo $style ?>'>
						<?php if ($img) echo "\t\t\t\t\t\t\t<table style='margin:0;padding:0;'><tr style='margin:0;padding:0;'><td style='margin:0;padding:0;'>\n" . $img; ?>
							<span style='font-family:"Lucida Grande","Lucida Sans Unicode",Tahoma,Verdana,sans-serif;font-size:11px;margin:0;padding:0;'>
								<a class='alastnews' href='<?php echo $item->get_permalink(); ?>' title='<?php echo $from; ?>' target='_blank'>
										<?php echo $titl1; ?>
								</a> 
								&#8212; 
								<abbr title="<?php echo $date; ?>"><?php echo $date2; ?></abbr>
							</span>
						<?php if ($img) echo "\t\t\t\t\t\t\t</tr></td></table>"; ?>
					</li>
<?php
					$z++;
					if ( $z > $maxlines )  break;
				}
?>
				</ul>
			</div>
<?php
				$feed->__destruct(); 
				unset($feed);
			}
			else
			{
				 echo "<p>" . __( "Sorry! no news !", 'dashboard-last-news' ) . "</p>\n";
			}
		}
		else
		{
			if (	isset($options['feeds']) ) 	echo "<p>" . __('No feed requested, you should edit the control panel&#8230;','dashboard-last-news' ) . "</p>\n";
			else 						echo "<p>" . __('First time ! welcome ! you have to edit the control panel&#8230;','dashboard-last-news' ) . "</p>\n";
		}
	}
	function get_image($item)
	{
		$wimg = $img = array();

		$content	=	$item->get_content();
		$enclosure	=	$item->get_enclosure();

		if (stripos($content,'news.google'))
		{
			$i = 0;
			$output = preg_match_all('/<a.+href=[\'"]([^\'"]+)[\'"].*>([^\'"]+)<\/a><br>/i', $content, $matches, PREG_SET_ORDER); // prend tous tags <a> et texte avant </a>

			if (stripos($matches[0][0],'<img')) 	$i = 1;

			if (isset($matches[$i][0]))
			{
				$content = str_replace($matches[$i][0], '', $content);
				$content = str_replace('<br><table', '<table', $content);
			}
		}
		$output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $content, $match2s, PREG_SET_ORDER);
		if (isset($match2s [0] [1]))
		{
			$wimg [] = str_replace(' ', '%20',$match2s [0] [1]);
		}
		
		if (!empty($enclosure))
		{
			if ( 'image' == $enclosure->get_medium() ) 		$wimg [] =  $enclosure->get_link(); 
			$thumbnails = $enclosure->get_thumbnails();
			if (!empty($thumbnails)) foreach ( $thumbnails as $thumbnail) 	$wimg [] =  $thumbnail;
		}

//--	do not work with ajax call --	$img = array_filter($wimg,array(&$this, 'not_empty'));
		$img = $wimg;


		switch (count($img))
		{
			case 0 :
				return '';
			break;
			case 1 :
				return DashboardLastNews::format_img(reset($img));
			break;
			default :
				$default = reset($img);
//--	do not work with ajax call --	$img = array_filter($img,array(&$this, 'exclude'));
				if ( 0 == count($img) ) return DashboardLastNews::format_img($default);
				else 				return DashboardLastNews::format_img(reset($img));
			break;
		}
	}
	function format_img($url)
	{
		$hmax = 40;

		if ($url === false) 				return '';
		if ((stripos($url,'http://') === false)) 	return '';

		$wh = false;

			$wh = @ getimagesize($url);

		if ($wh === false) $whi = ' height='.$hmax.'px ';
		else
		{
			if ( ($wh [1] >= $hmax) || (stripos($wh['mime'],'png')))
			{
				$h    = $hmax;
				$w    = round ( (($wh [0] * $hmax) / $wh [1]) );
				$whi  = 'width="';
				$whi .= $w;	
				$whi .= 'px" height="';
				$whi .= $h;
				$whi .= 'px"';
			}
			else
			{
				$whi  = $wh [3];
			}
		}
		return  "<img src='" . $url . "' style='float:left;border:0;margin:0;padding:0 8px 0 0;'" .  $whi . "/>";
	}
/**************************************************************************/
	function get_feeds($widget)
	{
		$widgets = DashboardLastNews::dashboard_has_widget();
		foreach ($widgets as $widget)
		{
			$num = 1;
			$x = explode('_',$widget);
			if (isset($x[3]))	$num = $x[3];

			$url = get_option('siteurl') . '/' . DashboardLastNews_PATH . '/php/ajax.php?jax=' . $num . '&fake=ok';
			$result = DashboardLastNews::call_feeds($url); 
			if (!$result) break;
		}
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
	function not_empty($var)
	{
		if (empty($var)) 	return false;
		else 			return true;
	}
/**************************************************************************/
/* not working with ajax
	function exclude($url)
	{
		if ((stripos($url,'http://') === false)) return false;

		$excludes = array('smilies');
		$i = false;
		foreach ($excludes as $exclude) if (!(stripos($url,$exclude)) == false) $i = true ;
		if ($i)return false;

		return true;
	}
*/
/**************************************************************************/
	function register_widget() 
	{
		add_action(	'DashboardLastNews_admin_head'	,array(&$this, 'admin_head'));

		if ( !$widget_options = get_option( 'dashboard_widget_options' ) ) $widget_options = array();

		wp_register_sidebar_widget	( 	'dashboard_last_news',
								 __( 'Last News', 'dashboard-last-news' ), 
								array(&$this, 'dashboard_empty'), 
								array( 'width' => 'half',  'height' => 'single' ),
								array(&$this, 'widget_has_feeds'), 
								array(&$this, 'widget_output')
							);
		wp_register_widget_control	( 	'dashboard_last_news',
								 __( 'Last News', 'dashboard-last-news' ), 
								array(&$this, 'widget_control'), 
								array(), 
								array( 'widget_id' => 'dashboard_last_news', 'widget_prefix'=>'dashboard-last-news' ) 
							);

		$dashboard_last_news_widget_count = (get_option('dashboard-last-news-widget-count')) ? get_option('dashboard-last-news-widget-count') : 1;
		for ( $i = 2; $i <= $dashboard_last_news_widget_count ; $i = $i + 1 )
		{
			wp_register_sidebar_widget( 	'dashboard_last_news_' . $i,
								 __( 'Last News', 'dashboard-last-news' ) . ' - ' . $i, 
								array(&$this, 'dashboard_empty'), 
								array( 'width' => 'half',  'height' => 'single' ),
								array(&$this, 'widget_has_feeds'), 
								array(&$this, 'widget_output')
							  );
			wp_register_widget_control( 	'dashboard_last_news_' . $i, 
								__( 'Last News', 'dashboard-last-news' ) . ' - ' . $i, 
								array(&$this, 'widget_control'), 
								array(), 
								array( 'widget_id' => 'dashboard_last_news_' . $i, 'widget_prefix'=>'dashboard-last-news-' . $i ) 
								);
		}
	}

	function add_widget( $widgets ) 
	{
		global $wp_registered_widgets;

		if ( !isset($wp_registered_widgets['dashboard_last_news']) || !current_user_can( 'manage_options' ) ) return $widgets;

		array_splice( $widgets, 0, 0, 'dashboard_last_news' );

		$dashboard_last_news_widget_count = (get_option('dashboard-last-news-widget-count')) ? get_option('dashboard-last-news-widget-count') : 1;
		for ( $i = 2; $i <= $dashboard_last_news_widget_count ; $i = $i + 1 )
		{
			if ( !isset($wp_registered_widgets['dashboard_last_news_' . $i]) ) return $widgets;

			array_splice( $widgets, 0, 0, 'dashboard_last_news_' . $i );
		}
		return $widgets;
	}

	function dashboard_has_widget()
	{
		$w = array();

// for dashboard-widget-manager plugin

		$x = get_option('dashboard_widget_order');
		if ( ($x) && (is_array($x)) && (0 < count($x)) && (0 < reset($x)) ) 
		{
			$y = reset($x);
			foreach ($y as $z)
			{
				if (false !== stripos($z,'dashboard_last_news')) $w[] = $z;
			}
		}
// end for dashboard-widget-manager plugin
		else 
// for default wp2.5 we are always in !!!
		{
			$dashboard_last_news_widget_count = (get_option('dashboard-last-news-widget-count')) ? get_option('dashboard-last-news-widget-count') : 1;
			for ( $i = 1; $i <= $dashboard_last_news_widget_count ; $i = $i + 1 )
			{
				if ($i == 1) 	$w[] = 'dashboard_last_news';
				else			$w[] = 'dashboard_last_news_' . $i;
			}
		}
		return $w;
	}
/**************************************************************************/
	function dashboard_empty( $args, $callback = false ) 
	{
		extract( $args, EXTR_SKIP );

		if ( !$widget_options = get_option( 'dashboard_widget_options' ) )
			$widget_options = array();
			
		if ( !isset($widget_options[$widget_id]) )
			$widget_options[$widget_id] = array();

		if ( !isset($widget_options[$widget_id]['wtitle']) )
			$widget_options[$widget_id]['wtitle'] = $widget_name;
		if ( ( isset($widget_options[$widget_id]['wtitle']) ) && ( empty($widget_options[$widget_id]['wtitle']) ) )
			$widget_options[$widget_id]['wtitle'] = $widget_name;
		$widget_options[$widget_id]['wtitle'] = stripslashes($widget_options[$widget_id]['wtitle']);

		echo $before_widget;
		
		echo $before_title;
		echo $widget_options[$widget_id]['wtitle'] ;
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
		$widget_options = get_option( 'dashboard_widget_options');

		switch (true)
		{
			case ( ( isset($widget_options[$widget_id]['feeds']) ) && ( 0 < count($widget_options[$widget_id]['feeds']) ) ) :
				echo '<p class="widget-loading">' . __( 'Loading&#8230;' ) . '</p>';
				DashboardLastNews::get_feeds($widget_id); 
				return false;
			break;
			case ( ( isset($widget_options[$widget_id]['feeds']) ) && ( 0 == count($widget_options[$widget_id]['feeds']) ) ) :
				echo '<p class="widget-loading">' .__('No feed requested, you should edit the control panel&#8230;','dashboard-last-news' ) . '</p>';
				return false;
			break;
			case ( !isset($widget_options[$widget_id]['feeds']) ) :
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

		if ( !$widget_options = get_option( 'dashboard_widget_options' ) )
			$widget_options = array();
			
		if ( !isset($widget_options[$widget_id]) )
			$widget_options[$widget_id] = array();

		if ( class_exists('SimplePie' ) )
		{
			DashboardLastNews::get_lastnews( $widget_options [$widget_id],$widget_id);
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

		if ( !$widget_options = get_option( 'dashboard_widget_options' ) )
			$widget_options = array();

		if ( !isset($widget_options[$widget_id]) )
			$widget_options[$widget_id] = array();

		if ( 'POST' == $_SERVER['REQUEST_METHOD'] && isset($_POST[$widget_prefix]) ) 
		{
			$widget_options[$widget_id] = $_POST[$widget_prefix];

			if ( !isset($widget_options[$widget_id]['wtitle']) )
				$widget_options[$widget_id]['wtitle'] = $widget_name;
			$widget_options[$widget_id]['wtitle'] = $widget_options[$widget_id]['wtitle'];

			if ( !isset($widget_options[$widget_id]['image']) )
				$widget_options[$widget_id]['image'] = false;
			$widget_options[$widget_id]['image'] = $widget_options[$widget_id]['image'];

			if ( !isset($widget_options[$widget_id]['maxfeeds']) )
				$widget_options[$widget_id]['maxfeeds'] = 3;
			$widget_options[$widget_id]['maxfeeds'] = (int) $widget_options[$widget_id]['maxfeeds'];

			if ( !isset($widget_options[$widget_id]['maxlines']) )
				$widget_options[$widget_id]['maxlines'] = 10;
			$widget_options[$widget_id]['maxlines'] = (int) $widget_options[$widget_id]['maxlines'];

			if ( !isset($widget_options[$widget_id]['feeds']) )
				$widget_options[$widget_id]['feeds'] = array();
			else
			{
				$feeds  = array_filter($widget_options[$widget_id]['feeds'],array(&$this, 'not_empty'));
				$feeds1 = array_slice ($feeds, 0, $widget_options[$widget_id]['maxfeeds']); 
				$feeds2 = array_filter($feeds1,array(&$this, 'not_empty'));
				$widget_options[$widget_id]['feeds'] = $feeds2;
			}	
			update_option( 'dashboard_widget_options', $widget_options );
		}

		if ( !isset($widget_options[$widget_id]['wtitle']) )
			$widget_options[$widget_id]['wtitle'] = $widget_name;
		$widget_options[$widget_id]['wtitle'] = $widget_options[$widget_id]['wtitle'];

		if ( !isset($widget_options[$widget_id]['image']) )
			$widget_options[$widget_id]['image'] = false;

		if ( !isset($widget_options[$widget_id]['maxfeeds']) )
			$widget_options[$widget_id]['maxfeeds'] = 3;

		if ( !isset($widget_options[$widget_id]['maxlines']) )
			$widget_options[$widget_id]['maxlines'] = 10;

		if ( !isset($widget_options[$widget_id]['feeds']) )
			$widget_options[$widget_id]['feeds'] = array();
?>
	<p>
		<label for="recent-posts-extended-count"><?php _e('Widget Title : ', 'dashboard-last-news' ); ?>
			<input id="recent-posts-extended-count" type="text" size="30" maxlength="30" value="<?php echo $widget_options[$widget_id]['wtitle'] ; ?>" name="<?php echo $widget_prefix; ?>[wtitle]"/>
		</label>
	</p>
	<p style='margin-bottom:3px;'>
		<label for="recent-posts-extended-count">
			<?php _e('Image (y/n) : ', 'dashboard-last-news' ); ?>&nbsp;
			<input type="checkbox" <?php checked(true,$widget_options[$widget_id]['image']); ?> name="<?php echo $widget_prefix; ?>[image]"/>
			&nbsp;&nbsp;&nbsp;<?php _e('Feeds fields ?', 'dashboard-last-news' ); ?>&nbsp;
			<select id="recent-posts-extended-count" name="<?php echo $widget_prefix; ?>[maxfeeds]">
<?php
		for ( $i = 3; $i <= 10; $i = $i + 1 ) echo "<option value='$i'" . ( $widget_options[$widget_id]['maxfeeds'] == $i ? " selected='selected'" : '' ) . ">$i</option>";
?>
			</select>
			&nbsp;&nbsp;&nbsp;<?php _e('Lines to display ?', 'dashboard-last-news' ); ?>&nbsp;
			<select id="recent-posts-extended-count" name="<?php echo $widget_prefix; ?>[maxlines]">
<?php
		for ( $i = 3; $i <= 20; $i = $i + 1 ) echo "<option value='$i'" . ( $widget_options[$widget_id]['maxlines'] == $i ? " selected='selected'" : '' ) . ">$i</option>";
?>
			</select>

		</label>
	</p>
<?php
		$z = 1;
		foreach ( $widget_options[$widget_id]['feeds'] as $feed )
		{
?>
	<p style='margin-top:0;'>
		<label for="rss-url-<?php echo $z; ?>"><?php _e('Fill the RSS or Atom URL here', 'dashboard-last-news' ); ?> :
			<input class="widefat" id="rss-url-<?php echo $z; ?>" name="<?php echo $widget_prefix; ?>[feeds][]" type="text" value="<?php echo $feed; ?>" />
		</label>
	</p>
<?php
			$z++;
			if ($z > $widget_options[$widget_id]['maxfeeds']) break;
		}
		for ( $i = $z; $i <= $widget_options[$widget_id]['maxfeeds']; $i = $i + 1 )
		{
?>
	<p style='margin-top:0;'>
		<label for="rss-url-<?php echo $i; ?>"><?php _e('Fill the RSS or Atom URL here', 'dashboard-last-news' ); ?> :
			<input class="widefat" id="rss-url-<?php echo $i; ?>" name="<?php echo $widget_prefix; ?>[feeds][]" type="text" value="" />
		</label>
	</p>
<?php
		}
	}
}

// Start this plugin once all other plugins are fully loaded
add_action( 'plugins_loaded', create_function( '', 'global $DashboardLastNews; $DashboardLastNews = new DashboardLastNews();' ) );

?>