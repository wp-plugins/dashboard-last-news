function dln_slider(id)
{
	this.id 		= id;

	this.slide = function (event, ui) { 
		ul.css('top', ui.value - max);
		hndle.css('top', 100 * ui.value/max + '%');
	}

	this.init = function() {
		this.widget 	= jQuery('div#' + this.id + ' > div.inside');
		this.ul 		= jQuery('#' + this.id + '_ul', this.widget);
		this.hndle		= jQuery('div div a', this.widget);

		this.insideHeight = this.widget.innerHeight();
		this.hiddenHeight = this.ul.outerHeight() - this.insideHeight;

		if (this.hiddenHeight <= 0) {
			jQuery("div div", this.widget).css('display','none');
			return;
		}

		this.slider		= {
			max         : this.hiddenHeight, 
			orientation : 'vertical',
			stop        : this.slide, 
			slide       : this.slide
		}
		jQuery("div div", this.widget).slider( this.slider );
	}

	this.init();

	var ul = this.ul;
	var max = this.hiddenHeight;
	var hndle = this.hndle;
}
