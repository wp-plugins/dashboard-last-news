var dln_slider = {

	init : function() {
		jQuery('.dln_widget').each(function() {

				var _this = this;
				var i = _this.id.replace('dln_widget_','');
				var id = (1 == i) ? 'dashboard_last_news' : 'dashboard_last_news_' + i;

				var widget = jQuery('div#' + id);

				var w_data = {	action:	'dln_ajax',
							i:  		i	
				};

				jQuery.ajax({
					data: w_data,
					type: "POST",
					url: dln_sliderL10n.url,
					success: function(response) {
						jQuery(_this).parent().html(response);

						var viewport= jQuery('div.inside > div.content', widget);
						var ul     	= jQuery('ul', viewport);
						var handle 	= null;

						var Height = ul.outerHeight() - viewport.innerHeight();

						if ( (Height) <= 0) {
							jQuery('div.inside > div.handle', widget).css('display','none');
							return;
						}
						var w_slider = {
							max         : Height, 
							orientation : 'vertical',
							stop        : function (event, ui) {
								//ul.css('top', ui.value - Height);
								handle.css('top', 100 - (100 * ui.value/Height) + '%');
							}, 
							slide       : function (event, ui) {
								ul.css('top', ui.value - Height);
								handle.css('top', 100 - (100 * ui.value/Height) + '%');
							}
						}
						jQuery('div.inside > div.handle > div', widget).slider( w_slider );
						var handle 	= jQuery('div.inside > div.handle > div > a', widget);
					}
				});
			}
		);
	}
}
jQuery(document).ready( function() { dln_slider.init(); });
