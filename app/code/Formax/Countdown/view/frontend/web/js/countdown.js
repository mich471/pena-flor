define([
    'jquery',
    'uiComponent'
  ], function(
    $, Component
  ){
    return Component.extend({

        finishTxt: '',
        date: 0,
        timer: null,
        counterBox: null,

        initialize: function (config, element) {
            this.finishTxt = config.finishTxt;
            this.date = config.date * 1000;
            this.counterBox = $(element);

            this.startTimer();
        },

        startTimer: function () {
            const self = this;
            const date = this.date;
            const counterBox = this.counterBox;

            this.timer = setInterval(function() {
                let now = new Date().getTime();
                let diff = date - now;

                let secs   = Math.floor(diff / 1000),
                    mins   = Math.floor(secs / 60),
                    hours = Math.floor(mins / 60),
                    days  = Math.floor(hours / 24);

                mins = mins - (hours * 60);
                secs = secs - (hours * 60 * 60) - (mins * 60);

                let convert = number => number.toLocaleString('en-US', {
                    minimumIntegerDigits: 2,
                  })

                // counterBox.find('.counter-num.days').text(convert(days));
                counterBox.find('.counter-num.hours').text(convert(hours));
                counterBox.find('.counter-num.mins').text(convert(mins));
                counterBox.find('.counter-num.secs').text(convert(secs));

                if (diff < 0) {
                    clearInterval(self.timer);

                    if (self.finishTxt) {
                        counterBox.text(self.finishTxt);
                    } else {
                        counterBox.hide();
                    }
                }
            }, 1000);
        }

    });
  }
);