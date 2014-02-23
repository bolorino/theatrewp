/**
* Controls the behaviours of custom metabox fields.
*
* @author Andrew Norcross
* @author Jared Atchison
* @author Bill Erickson
* @author Justin Sternberg
* @see https://github.com/jaredatch/Custom-Metaboxes-and-Fields-for-WordPress
*/

/*jslint browser: true, devel: true, indent: 4, maxerr: 50, sub: true */
/*global jQuery, tb_show, tb_remove */

/**
* Custom jQuery for Custom Metaboxes and Fields
*/
jQuery(document).ready(function ($) {
	'use strict';

	var formfield;

	/**
	* Initialize timepicker (this will be moved inline in a future release)
	*/

	$('.twp_timepicker').each(function () {
		$('#' + jQuery(this).attr('id')).timePicker({
			startTime: "08:00",
			endTime: "23:00",
			show24Hours: true,
			separator: ':',
			step: 30
		});
	});

	/**
	* Initialize jQuery UI datepicker (this will be moved inline in a future release)
	*/
	$('.twp_datepicker').each(function () {
		$('#' + jQuery(this).attr('id')).datepicker(
			{ showButtonPanel: true,
		    closeText: objectL10n.closeText,
		    currentText: objectL10n.currentText,
		    monthNames: objectL10n.monthNames,
		    monthNamesShort: objectL10n.monthNamesShort,
		    monthStatus: objectL10n.monthStatus,
		    dayNames: objectL10n.dayNames,
		    dayNamesShort: objectL10n.dayNamesShort,
		    dayNamesMin: objectL10n.dayNamesMin,
		    dateFormat: objectL10n.dateFormat,
		    firstDay: objectL10n.firstDay,
		    isRTL: objectL10n.isRTL,}
    	);
	});
	// Wrap date picker in class to narrow the scope of jQuery UI CSS and prevent conflicts
	$("#ui-datepicker-div").wrap('<div class="twp_element" />');
});
