/*
 * Matukio Booking; v20140330
 * https://compojoom.com
 * Copyright (c) 2013 - 2014 Yves Hoppe; License: GPL v2 or later
 */

(function ($) {

	var version = "20140728";
	var debug = 0;

	$.fn.mat_booking = function (options, translations) {

		var settings = $.extend({
			// Default settings - see API instructions
			imgpath: 'media/com_matukio/images/',
			steps: 3,
			fees: 0,
			different_fees: 0,
			max_bookings: 0,
			coupon: 1,
			fieldnames: null,
			event_id: 0,
			setting_multiple_places: 1
		}, options);

		var translations = $.extend({
			error_payment: 'Please select a payment method',
			error_required_fields: 'Please fill out all required fields!',
			error_coupon: 'The coupon code is not valid',
			error_max_places: 'You have exceeded the maximum number of bookable places',
			error_agb: 'You need to accept the Terms and Conditions'
		}, translations);

		var holder = $.extend({
			log: $.fn.mat_booking.log,
			form: null,
			intro: null,
			currentstep: 1,
			btn_next: null,
			btn_back: null,
			btn_submit: null,
			btn_addticket: null,
			page_one: null,
			page_two: null,
			page_three: null,
			payment: null,
			agb: null,
			nrbooked: null,
			coupon_code: null,
			mh1: null,
			mh2: null,
			mh3: null,
			count_tickets: 0,
			fields: {},
			conf_fields: {}
		});

		var API = $.extend({
			hideall: function () {
				holder.divmonthly.hide();
				holder.divweekly.hide();
			},

			nextPage: function () {
				holder.log('-- Next Page ' + holder.currentstep + '--');

				if (holder.currentstep == settings.steps) {
					return false;
				}

				holder.currentstep++;

				if (holder.currentstep == 3 && !API.validatePayment()) {
					alert(translations.error_payment);
					holder.currentstep--;
					return false;
				}

				if (!API.validateForm()) {
					alert(translations.error_required_fields);
					holder.currentstep--;
					return false;
				}

				if (holder.currentstep == 3 && settings.coupon == 1 && !API.validateCoupon()) {
					alert(translations.error_coupon);
					holder.currentstep--;
					return false;
				}

				if (settings.different_fees && holder.currentstep == 2) {
					var difpl = 0;

					$(".ticket_places").each(function (index, element) {
						difpl += parseInt($(element).val());
					});

					// Set total places
					holder.nrbooked.val(difpl);
				}

				if (holder.nrbooked.val() > settings.max_bookings) {
					alert(translations.error_max_places + " " + settings.max_bookings + ")");
					holder.currentstep--;
					return false;
				}

				holder.btn_back.css('display', 'inline-block');
				holder.page_one.css('display', 'none');
				holder.mh1.css('display', 'none');

				if (settings.steps == 3 && holder.currentstep == 2) {
					holder.page_two.css('display', 'block');
					holder.mh2.css('display', 'block');
				}

				if (holder.currentstep == settings.steps) {
					holder.page_two.css('display', 'none');
					holder.page_three.css('display', 'block');

					if (settings.steps != 2) {
						holder.mh2.css('display', 'none');
					}

					holder.mh3.css('display', 'block');

					holder.btn_next.css('display', 'none');
					holder.btn_submit.css('display', 'inline-block');

					API.fillConfirmation();

					if (settings.steps == 3) {
						if (settings.different_fees == 1) {
							API.calculateDifferentFees();
						} else {
							API.calculatePayment();
						}
					} else {
						if (settings.fees > 0) {
							if (settings.different_fees == 1) {
								API.calculateDifferentFees();
							} else {
								API.calculatePayment();
							}
						}
					}
				}

				return true;
			},

			prevPage: function () {
				holder.log('-- Prev Page ' + holder.currentstep + ' --');

				if (holder.currentstep == 1) {
					return;
				}

				holder.currentstep--;

				if (settings.steps != 2) {
					holder.mh2.css('display', 'none');
				}

				holder.mh3.css('display', 'none');

				holder.page_three.css('display', 'none');
				holder.btn_submit.css('display', 'none');
				holder.btn_next.css('display', 'inline-block');

				if (settings.steps == 3 && holder.currentstep == 2) {
					holder.page_two.css('display', 'block');
					holder.mh2.css('display', 'block');
				}

				if (holder.currentstep == 1) {
					holder.mh1.css('display', 'block');
					holder.btn_back.css('display', 'none');
					holder.page_two.css('display', 'none');
					holder.page_one.css('display', 'block');
				}

				return true;
			},

			sendPage: function () {
				holder.log('-- Send Page ' + holder.currentstep + ' --');

				if (!API.validateAGB()) {
					alert(translations.error_agb);
					return false;
				}

				$("#BookingForm").submit();
			},

			validateForm: function () {
				var valid = $('#BookingForm').validationEngine('validate');
				return valid;
			},

			validatePayment: function () {
				if (holder.payment.val() == '') {
					return false;
				}
				return true;
			},

			validateCoupon: function () {
				var response = false;
				var code = holder.coupon_code.val();

				if (code == '') {
					return true;
				}

				$.ajax({
					url: 'index.php?option=com_matukio&view=requests&format=raw&task=validate_coupon&code=' + code,
					type: 'get',
					async: false
				}).done(function (data) {
					response = data;
				});

				return (response === 'true');
			},

			fillConfirmation: function () {
				$.each(holder.fields, function (key, value) {
					var conffield = holder.conf_fields["conf_" + key];
					conffield.text(value.val());
				});

				var conf_nrbooked = $("#conf_nrbooked");

				if (conf_nrbooked.length) {
					conf_nrbooked.text(holder.nrbooked.val());
				}

				return true;
			},

			calculateDifferentFees: function () {
				var places = 0;
				var code = "";

				if (holder.coupon_code.length) {
					code = holder.coupon_code.val();
				}

				var conf_payment_type = $("#conf_payment_type");
				var conf_nrbooked = $("#conf_nrbooked");
				var conf_coupon_code = $("#conf_coupon_code");

				if (conf_payment_type.length) {
					// Not using value here, use name of the plugin
					var selc = $("option:selected", holder.payment);
					conf_payment_type.text(selc.text());
				}

				if (conf_coupon_code.length) {
					conf_coupon_code.text(holder.coupon_code.val());
				}

				var conf_payment_total = $("#conf_payment_total");

				var cnt = 0;

				var ticket_places = [], ticket_types = [], ticket_disc_value = [], ticket_percent = [], ticket_discount = [];

				$(".ticket_places").each(function (index, num) {
					var tval = $(num).val();
					ticket_places[cnt] = tval;
					places += parseInt(tval);
					cnt++;
				});

				// Set total places
				holder.nrbooked.val(places);

				if (conf_nrbooked.length) {
					conf_nrbooked.text(places);
				}

				cnt = 0;
				$(".ticket_fees").each(function (index, fee) {
					fee = $(fee);
					ticket_types[cnt] = fee.val();
					var selected = $("option:selected", fee);
					ticket_disc_value[cnt] = selected.attr("discvalue");
					ticket_discount[cnt] = selected.attr("discount");
					ticket_percent[cnt] = selected.attr("percent");
					cnt++;
				});

				$.ajax({
					url: 'index.php?option=com_matukio&view=requests&format=raw&task=get_total_different&code='
						+ code + '&event_id=' + settings.event_id + '&fee=' + settings.fees + '&nrbooked=' + places
						+ '&places=' + ticket_places.join(',') + '&types=' + ticket_types.join(',')
						+ '&disc_value=' + ticket_disc_value.join(',') + '&percent=' + ticket_percent.join(',') + '&discount=' + ticket_discount.join(','),
					type: 'get'
				}).done(function (data) {
					conf_payment_total.text(data);
					holder.nrbooked.val(places);
				});

				return true;
			},

			calculatePayment: function () {
				var conf_payment_type = $("#conf_payment_type");
				var conf_nrbooked = $("#conf_nrbooked");

				var conf_coupon_code = $("#conf_coupon_code");
				var conf_payment_total = $("#conf_payment_total");

				if (conf_payment_type.length) {
					// Not using value here, use name of the plugin
					var selc = $("option:selected", holder.payment);

					conf_payment_type.text(selc.text());
				}

				if (conf_nrbooked.length) {
					conf_nrbooked.text(holder.nrbooked.val());
				}

				if (conf_coupon_code.length) {
					conf_coupon_code.text(holder.coupon_code.val());
				}

				// The tricky part
				if (conf_payment_total.length) {
					var code = "";

					if (holder.coupon_code.length) {
						code = holder.coupon_code.val();
					}

					var places = 1;

					if (holder.nrbooked.length) {
						places = holder.nrbooked.val();
					}

					var types = [];

					if (settings.different_fees == 1) {
						var cnt = 0;

						$(".ticket_fees").each(function (index, fee) {
							types[cnt] = fee.val();
							cnt++;
						});
					}

					$.ajax({
						url: 'index.php?option=com_matukio&view=requests&format=raw&task=get_total&code='
							+ code + '&fee=' + settings.fees + '&nrbooked=' + places + '&types=' + types.join(','),
						type: 'get'
					}).done(function (data) {
						conf_payment_total.text(data);
					});
				}

				return true;
			},

			newTicketRow: function () {
				holder.count_tickets++;

				$.ajax({
					url: 'index.php?option=com_matukio&format=raw&view=requests&task=getnewfeerow&event_id=' + settings.event_id,
					type: 'get',
					encoding: 'utf-8',
					data: {num: holder.count_tickets}
				}).done(function (html) {
					var new_rows = '<div>' + html + '</div>';

					// inject new fields at bottom
					$(new_rows).appendTo('#mat_tickets');

					$("#delticket" + holder.count_tickets).click(function () {
						$("#tickets_" + $(this).attr("num")).remove();
						return false;
					});
				});

				return true;
			},

			validateAGB: function () {
				if (holder.agb.length) { // No AGB, so they are always true..
					if (holder.agb.checked == false) {
						return false;
					}
				}

				return true;
			},

			init: function () {
				// Init fields
				holder.intro = $("#mat_intro");
				holder.btn_next = $("#btn_next");
				holder.btn_back = $("#btn_back");
				holder.btn_submit = $("#btn_submit");
				holder.page_one = $("#mat_pageone");
				holder.page_two = $("#mat_pagetwo");
				holder.page_three = $("#mat_pagethree");
				holder.payment = $("#payment");
				holder.agb = $("#agb");
				holder.nrbooked = $("#nrbooked");
				holder.coupon_code = $("#coupon_code");

				settings.fieldnames.each(function (field, i) {
					var fname = field.field_name;
					var ftype = field.type;

					if (ftype != "spacer" && ftype != "spacertext") {
						holder.fields[fname] = $("#" + fname);

						// Confirmation fields
						holder.conf_fields["conf_" + fname] = $("#conf_" + fname);
					}
				});

				if (debug == 1) {
					console.log(holder.fields);
					console.log(holder.conf_fields);
				}

				// Header
				if (settings.steps == 2) {
					holder.mh1 = $('#mat_h1');
					holder.mh3 = $('#mat_h2');
				} else {
					holder.mh1 = $('#mat_hp1');
					holder.mh2 = $('#mat_hp2');
					holder.mh3 = $('#mat_hp3');
				}

				// Different fees
				if (settings.different_fees == 1 && settings.max_bookings > 1 && settings.setting_multiple_places > 0) {
					holder.btn_addticket = $("#addticket");
					holder.btn_addticket.click(function () {
						API.newTicketRow();
						return false;
					});
				}

				holder.btn_next.click(function () {
					API.nextPage();
					return false;
				});

				holder.btn_back.click(function () {
					API.prevPage();
					return false;
				});

				holder.btn_submit.click(function () {
					API.sendPage();
					return false;
				});

				return true;
			}
		});

		return this.each(function () {
			holder.log('-- Matukio Bookingform Init --');
			holder.bookingform = $(this);

			var success = API.init();
			holder.log('-- Init Status:  ' + success + ' --');

			holder.log('-- Finished loading Matukio Bookingform --');
		});
	}

	// Logging to console
	$.fn.mat_booking.log = function log() {
		if (window.console && console.log && debug == 1)
			console.log('[recurring] ' + Array.prototype.join.call(arguments, ' '));
	}

}(jQuery));