var mecPHPPlugin = new Class({

    Implements: [Options, Events],
    options: {
        cEvents:[]
    },

    initialize: function(options){
        this.setOptions(options);
    },

    getEvents: function(that,eventRangeStart,eventRangeEnd){

        var thisObj = this;
        new Request.JSON({
            method: 'get',
            url: 'index.php?option=com_matukio&view=requests&format=raw&task=getcalendar',
            onComplete: function(cevents){
                //alert("loaded");
                that.options.cEvents = cevents;
                that.gotEvents = true;
                document.id('loading').fade('out');
                that.loadCalEvents();
            }
        }).send('startDate=' + eventRangeStart.ymd() + '&endDate=' + eventRangeEnd.ymd());

        // {"title":"Breakfast","start":"2012-07-30T06:00:00-05:00","end":"2012-07-30T07:00:00-05:00","location":""}
        // Manual event entry without an AJAX request (used for troubleshooting)
        /*that.options.cEvents = [
         {
         title:'Dad\'s Birthday',
         start:'2009-01-12',
         end:'2009-01-13',
         location:''
         },
         {
         title:'MooTools Events Calendar v0.2.0',
         start:'2012-07-02',
         end:'2009-03-02',
         location:'DansNetwork.com'
         },
         {
         title:'Hair Cut',
         start:'2012-07-05T13:00:00-06:00',
         end:'2012-07-06T13:30:00-06:00',
              2013-03-29T15:00:00-18:00

              2012-07-30T19:00:00-19:00
         location:''
         },
         {
         title:'<a href="http://dansnetwork.com/mootools/events-calendar/">MooTools Events Calendar v0.2.1</a>',
         start:'2012-07-09',
         end:'2012-07-09',
         location:'DansNetwork.com'
         },
         {
         title:'Hair Cut',
         start:'2012-07-17T09:00:00-06:00',
         end:'2012-07-17T09:30:00-06:00',
         location:''
         },
         {
         title:'Oil Change',
         start:'2012-07-17T11:00:00-06:00',
         end:'2012-07-17T11:30:00-06:00',
         location:'Jiffy Lube'
         },
         {
         title:'Gym',
         start:'2012-07-17T13:00:00-06:00',
         end:'2012-07-17T13:30:00-06:00',
         location:''
         },
         {
         title:'Dinner',
         start:'2012-07-17T17:00:00-06:00',
         end:'2012-07-17T18:30:00-06:00',
         location:'Bob Chinns'
         },
         {
         title:'Movie',
         start:'2012-07-17T19:00:00-06:00',
         end:'2012-07-17T21:30:00-06:00',
         location:'Marcus'
         },
         {
         title:'Bedtime',
         start:'2012-07-17T23:00:00-06:00',
         end:'2012-07-17T23:00:00-06:00',
         location:'Home'
         }
         ];
         that.gotEvents = true;
         $('loading').fade('out');
         that.loadCalEvents(); */
    }
});