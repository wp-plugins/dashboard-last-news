function lastnewsslider (id) 
	{
			var widget 			= jQuery('div#' + id + ' > div');
			var widgetHeight 		= widget.innerHeight();
			var h3Height 		= jQuery('h3', widget).outerHeight();
			var viewportHeight 	= widgetHeight - h3Height;
			var ul 			= jQuery('#' + id + '_ul', widget);
			var ulHeight 		= jQuery('ul', widget).outerHeight();
			var max 			= ulHeight - viewportHeight;
			if (max > 0)
			{	max += 20; // juste pour faire joli
				var container = jQuery('div.dashboard-widget-content',widget);
				jQuery("#example",widget).slider ( { minValue: 0, maxValue: max, handle: '.ui-slider-handle', stop: function (event, ui) { ul.css('top', ui.value * -1);}, slide: function (event, ui) { ul.css('top', ui.value * -1);} } );
			}
			else
			{
				 jQuery("#example",widget).css('display','none'); 
			}

	}