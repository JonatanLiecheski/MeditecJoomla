/**
 * @package Matukio
 * @copyright Copyright (C) 2013 Yves Hoppe - compojoom.com. All rights reserved.
 * @license GNU General Public License version 2 or later; see LICENSE.txt
 * @since 3.0.0
 */
(function ($) {
	$(document).ready(function () {
		$('*[rel=tooltip]').tooltip()
		jQuery('.hasTooltip').tooltip({"html": true,"container": "body"});
	})
})(jQuery);
