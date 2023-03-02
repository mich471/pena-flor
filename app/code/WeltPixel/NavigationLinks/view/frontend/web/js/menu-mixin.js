define(['jquery'], function($) {
    'use strict';

    return function(navigationMenu) {
        $.widget('mage.menu', navigationMenu.menu, {
            options: {
                mediaBreakpoint: '(max-width: ' + window.widthThreshold + 'px)'
            },
            _toggleMobileMode: function () {
                this._super();
                $('.navigation ul > li.level0.mm-no-children > ul.hide-all-category').remove();
            },
            /**
             * Toggle.
             */
            toggle: function () {
                if ($(window).width() <= window.widthThreshold || window.widthThreshold === undefined) {
                    var html = $('html');
                    if (html.hasClass('nav-open')) {
                        html.removeClass('nav-open');
                        setTimeout(function () {
                            html.removeClass('nav-before-open');
                        }, this.options.hideDelay);
                    } else {
                        html.addClass('nav-before-open');
                        setTimeout(function () {
                            html.addClass('nav-open');
                        }, this.options.showDelay);
                    }
                }
            }
        });
        return {
            menu: $.mage.menu,
            navigation: $.mage.navigation
        }
    }
});
