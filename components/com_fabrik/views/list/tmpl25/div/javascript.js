/**
 * List helper
 *
 * @copyright: Copyright (C) 2005-2015, fabrikar.com - All rights reserved.
 * @license:   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

window.addEvent('fabrik.loaded', function() {
	Array.from($$('.fabrikList tr')).each(function(r){
		document.id(r).addEvent('mouseover', function(e){
			if (r.hasClass('oddRow0') || r.hasClass('oddRow1')){
				r.addClass('fabrikHover');
			}
		}, r);

		document.id(r).addEvent('mouseout', function(e){
			r.removeClass('fabrikHover');
		}, r);

		document.id(r).addEvent('click', function(e){
			if (r.hasClass('oddRow0') || r.hasClass('oddRow1')){
				$$('.fabrikList tr').each(function(rx){
					rx.removeClass('fabrikRowClick');
				});
				r.addClass('fabrikRowClick');
			}
		}, r);
	});
})
