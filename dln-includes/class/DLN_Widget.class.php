<?php
class DLN_Widget
{
	function __construct($i)
	{
		$this->i = $i;
		$this->id = (1 == $this->i) ? 'dashboard_last_news' : 'dashboard_last_news_' . $this->i;

		$options = get_option( 'dashboard_widget_options' );
		$this->options = $this->clean_options(isset($options[$this->id]) ? $options[$this->id] : array());

		if ( function_exists('wp_add_dashboard_widget') )
			wp_add_dashboard_widget(	$this->id,
								$this->options['wtitle'],
								array(&$this, 'widget'),
								array(&$this, 'control') 
			);

		add_action('DLN_get_content_' . $this->i, array(&$this, 'get_content'), 8);
	}

	function widget() 
	{
		if (	isset($this->options['feeds']) ) 
		{
			if (	0 < count($this->options['feeds']) ) 
			{
				echo '<p class="widget-loading hide-if-no-js dln_widget" id="dln_widget_' . $this->i . '">' . __( 'Loading&#8230;' ) . '</p><p class="describe hide-if-js">' . __('This widget requires JavaScript.') . '</p>';
			}
			else
			{
				echo '<p class="widget-loading hide-if-no-js">' . __('No feed requested, you should edit the control panel&#8230;','dashboard-last-news' ) . "</p>\n";
			}
		}
		else
		{
			echo '<p class="widget-loading hide-if-no-js">' . __('First time ! welcome ! you have to edit the control panel&#8230;','dashboard-last-news' ) . "</p>\n";
		}
	}

	function control()
	{
		if ( 'POST' == $_SERVER['REQUEST_METHOD'] && isset($_POST[$this->id]) )
		{
			$this->options = $this->clean_options($_POST[$this->id]);

			$options = get_option( 'dashboard_widget_options' );
			$options[$this->id] = $this->options;
			update_option( 'dashboard_widget_options', $options );
		}
?>
	<p>
		<label><?php _e('Widget Title : ', 'dashboard-last-news' ); ?>
			<input type="text" size="30" maxlength="30" value="<?php echo $this->options['wtitle'] ; ?>" name="<?php echo $this->id; ?>[wtitle]" autocomplete="off" />
		</label>
	</p>
	<p style='margin-bottom:3px;'>
		<label for="<?php echo $this->id; ?>_image"><?php _e('Image (y/n) : ', 'dashboard-last-news' ); ?>&nbsp;<input type="checkbox" <?php checked(true,$this->options['image']); ?> name="<?php echo $this->id; ?>[image]" id="<?php echo $this->id; ?>_image" /></label>
		&nbsp;&nbsp;&nbsp;<?php _e('Feeds fields ?', 'dashboard-last-news' ); ?>&nbsp;<select name="<?php echo $this->id; ?>[maxfeeds]"><?php for ( $i = 3; $i <= 10; $i++ ) echo "<option value='$i'" . ( $this->options['maxfeeds'] == $i ? " selected='selected'" : '' ) . ">$i</option>";?></select>
		&nbsp;&nbsp;&nbsp;<?php _e('Lines to display ?', 'dashboard-last-news' ); ?>&nbsp;<select name="<?php echo $this->id; ?>[maxlines]"><?php for ( $i = 3; $i <= 40; $i++ ) echo "<option value='$i'" . ( $this->options['maxlines'] == $i ? " selected='selected'" : '' ) . ">$i</option>"; ?></select>
	</p>
<?php
		$z = 1;
		foreach ( $this->options['feeds'] as $feed )
		{
?>
	<p style='margin-top:0;'>
		<label for="rss-url-<?php echo $z; ?>"><?php _e('Fill the RSS or Atom URL here', 'dashboard-last-news' ); ?> :
			<input class="widefat" id="rss-url-<?php echo $z; ?>" name="<?php echo $this->id; ?>[feeds][]" type="text" value="<?php echo clean_url($feed); ?>" />
		</label>
	</p>
<?php
			$z++;
			if ($z > $this->options['maxfeeds']) break;
		}
		for ( $i = $z; $i <= $this->options['maxfeeds']; $i++ )
		{
?>
	<p style='margin-top:0;'>
		<label for="rss-url-<?php echo $i; ?>"><?php _e('Fill the RSS or Atom URL here', 'dashboard-last-news' ); ?> :
			<input class="widefat" id="rss-url-<?php echo $i; ?>" name="<?php echo $this->id; ?>[feeds][]" type="text" value="" />
		</label>
	</p>
<?php
		}
	}

	function clean_options($options)
	{
		if ( !isset($options['wtitle']) ) 	$options['wtitle'] = __( 'Last News', 'dashboard-last-news' ) . ' - ' . $this->i ;;
		if ( !isset($options['image']) )	$options['image'] = false;
		if ( !isset($options['maxfeeds']) )	$options['maxfeeds'] = 3;
		$options['maxfeeds'] = (int) $options['maxfeeds'];
		if ( !isset($options['maxlines']) )	$options['maxlines'] = 10;
		$options['maxlines'] = (int) $options['maxlines'];
		if ( !isset($options['feeds']) )
			$options['feeds'] = array();
		else
		{
			$feeds  = array_filter($options['feeds'],array(&$this, 'not_empty'));
			$feeds  = array_slice ($feeds, 0, $options['maxfeeds']);
			$options['feeds'] = $feeds;
		}
		return $options;
	}

////  ////
////  ////
////  ////


	function get_content()
	{
		require_once  (ABSPATH . WPINC . '/class-feed.php');

		$feed = new SimplePie();
		$feed->set_cache_class('WP_Feed_Cache');
		$feed->set_cache_duration(apply_filters('wp_feed_cache_transient_lifetime', 43200));
		$feed->set_feed_url($this->options['feeds']);
		$feed->init();
		$feed->handle_content_type();
		if ($feed->get_items())
		{
?>
<div class='handle'>
	<div style='height:<?php echo $this->options['maxlines']; ?>em;'>
	</div>
</div>
<div class='content' style='height:<?php echo $this->options['maxlines']; ?>em;'>
	<ul>
<?php
			$z = 1;
			$date_format = get_option('date_format') . ' G:i ';
			foreach ($feed->get_items() as $item)
			{
				$img    = ($this->options['image']) ? $this->get_image($item) : '';
				$class  = (empty($img)) ? 'noimg' : 'img' ;
?>
		<li class='<?php echo $class; ?>'><?php if ($img) echo "<table><tr><td>\n" . $img; ?><span class='lastnews'><a class='lastnews' href='<?php echo $item->get_permalink(); ?>' title='<?php echo $item->get_feed()->get_title(); ?>' target='_blank'><?php echo $item->get_title(); ?></a> &#8212; <abbr title="<?php echo mysql2date($date_format,$item->get_date('Y-m-d H:i:00')); ?>"><?php echo $item->get_date('Y/m/d'); ?></abbr></span><?php if ($img) echo "</tr></td></table>"; ?></li>
<?php
				$z++;
				if ( $z > $this->options ['maxlines'])  break;
			}
?>
	</ul>
</div>
<?php
		}
		else
		{
			 echo "<p>" . __( "Sorry! no news !", 'dashboard-last-news' ) . "</p>\n";
		}
		$feed->__destruct(); 
		unset($feed);
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
			$wimg = '';
			if (isset($matches [0] [1])) $wimg = str_replace(' ', '%20',$matches [0] [1]);

			$needles = array ('bookmark.gif');				/* filter any icon of social bookmarkers ! */
			if (!$this->in_string($wimg,$needles)) 			$img [] = $wimg;
		}

		$img = array_filter($img,array(&$this, 'not_empty'));
		$img = array_filter($img,array(&$this, 'is_url'));

		switch (count($img))
		{
			case 0 :
				return '';
			break;
			case 1 :
				return $this->format_img(reset($img));
			break;
			default :
				$default 	= reset($img);
				$img 		= array_filter($img,array(&$this, 'exclude'));
				if ( 0 == count($img) ) return $this->format_img($default);
				else 				return $this->format_img(reset($img));
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
		$hmin = 10; $hmax = 150;
		$wh = false;

		$wh = @ getimagesize($url);
		if ( ($wh [1] < $hmin) || ($wh [1] > $hmax) )
			return  '';
		return  "<img src='$url' class='lastnews' />";
	}
}
?>