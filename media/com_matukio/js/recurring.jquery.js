/*
 * Matukio Recurring; v20140130
 * https://compojoom.com
 * Copyright (c) 2013 - 2014 Yves Hoppe; License: GPL v2 or later
 */

(function( $ ) {

    var version = "20140130";
    var debug = 0;

    $.fn.mat_recurring = function( options ) {

        var settings = $.extend({
            // Default settings - see API instructions
            imgpath: 'media/com_matukio/images/'
        }, options );

        var holder = $.extend({
            log: $.fn.mat_recurring.log,
            recurring_type: null,
            divmonthly: null,
            divweekly: null,
            form: null
        });

        var API = $.extend({

            hideall: function() {
                holder.divmonthly.hide();
                holder.divweekly.hide();
            },

            update: function() {
                var type = $("input:radio[name='recurring_type']:checked").val();

                API.hideall();

                if (type == "weekly")
                {
                    holder.divweekly.show();
                }
                else if (type == "monthly")
                {
                    holder.divmonthly.show();
                    holder.divweekly.show();
                }
            },

            updateSelection: function() {
                $("input:checkbox[name='recurring_week_day[]']")
            },

            init: function() {
                holder.divmonthly = $("#recurring_monthly");
                holder.divweekly = $("#recurring_weekly");

                API.update();

	            $("input[name='recurring']").click(function(){
		            if ($(this).val() == 1){
			            $("#reccuring-gen").show();
		            } else {
			            $("#reccuring-gen").hide();
		            }
		            return false;
	            });

                $("input[name='recurring_type']").click(function(){
                    API.update();
                    return false;
                });

                $("#generateRecurring").click(function(){
                    $(this).attr("disabled", "disabled");

                    // var activated = $("input[name='recurring']:checked").val();

                    var recurring_month_week = [];
                    $("input[name='recurring_month_week[]']:checked").each(function(i)
                    {
                        recurring_month_week[i] = $(this).val();
                    });

                    var recurring_week_day = [];
                    $("input[name='recurring_week_day[]']:checked").each(function(i)
                    {
                        recurring_week_day[i] = $(this).val();
                    });

                    $.ajax({
                        type: "POST",
                        url: 'index.php?option=com_matukio&format=raw&view=requests&task=generate_recurring',
                        data:  {
                            begin: $("#_begin_date").val(),
                            end: $("#_end_date").val(),
                            repeat_type: $("input:radio[name='recurring_type']:checked").val(),
                            recurring_month_week: recurring_month_week.join(","),
                            recurring_week_day: recurring_week_day.join(","),
                            recurring_count: $("#recurring_count").val(),
                            recurring_until: $("#recurring_until").val()
                        },

                        success: function(){}
                    }).done(function(data) {
                        $("#generated_events").html(data);
                        $("#generateRecurring").removeAttr("disabled");
                        $("#recurring_edited").val("1");
                    });

                    return false;
                });

                return true;
            }
        });

        return this.each(function(){
            holder.log('-- Matukio recurring Init --');
            holder.rechnerform = $(this);

            var success = API.init();
            holder.log('-- Init Status:  '  + success + ' --');

            holder.log('-- Finished loading Matukio recurring --');
        });
    }

    // Logging to console
    $.fn.mat_recurring.log = function log() {
        if (window.console && console.log && debug == 1 )
            console.log('[recurring] ' + Array.prototype.join.call(arguments, ' ') );
    }

}( jQuery ));