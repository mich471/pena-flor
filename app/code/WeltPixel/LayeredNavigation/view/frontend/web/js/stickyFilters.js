define([
    'jquery',
    'mage/mage'
], function ($) {
    "use strict";

    let stickyFilters = {
        options: {
            mobileThreshold: '786',
            isStickyFiltersEnabled: '0',
        },
        initialPosition: $('.block.filter').offset().top,

        init: function (mobileThreshold, isStickyFiltersEnabled) {
            this.options.mobileThreshold = mobileThreshold;
            this.options.isStickyFiltersEnabled = isStickyFiltersEnabled;

            let that = this;
            $(window).scroll(function () {
                let screenWidth = $(window).width();

                if (isStickyFiltersEnabled === '1' && (screenWidth >= mobileThreshold)) {
                    that.makeStickyFilters();
                }
            });
        },

        makeStickyFilters: function () {
            let that = this,
                horizontalFilters = $('.block.filter'),
                sc = $(window).scrollTop(),
                stickyMenuHeights = that.calculateStickyHeaderHeight(),
                pageHeader = $('.page-wrapper div.sticky-header'),
                containerShowLimit = that.initialPosition - stickyMenuHeights.outerHeight;

            if (sc > containerShowLimit) {
                pageHeader.addClass('no-box-shadow', 100);
                horizontalFilters.addClass("sticky-filters filters-box-shadow");
                horizontalFilters.css('top', stickyMenuHeights.height);
            } else {
                horizontalFilters.removeClass("sticky-filters filters-box-shadow");
                pageHeader.removeClass('no-box-shadow', 100);
                horizontalFilters.css('top', '');
            }
        },

        calculateStickyHeaderHeight: function () {
            let that = this,
                headerSection = $('.page-wrapper div.page-header'),
                navMenuSection = $('.page-wrapper div.sticky-header-nav'),
                pageHeader = $('.page-wrapper div.sticky-header'),
                stickyHeaderHeights = {
                    outerHeight: 0,
                    height: 0
                };

            switch (that.getHeaderVersion(headerSection)) {
                case "v4":
                    if (headerSection.is(':visible') && navMenuSection.is(':visible')) {
                        stickyHeaderHeights.outerHeight = headerSection.outerHeight()  + navMenuSection.outerHeight();
                        stickyHeaderHeights.height = navMenuSection.height() + $(".panel.wrapper").height();
                    }
                    break;
                default :
                    if (pageHeader.is(':visible')) {
                        stickyHeaderHeights.height = pageHeader.height();
                        stickyHeaderHeights.outerHeight = pageHeader.height();
                    }
                    break;
            }

            return stickyHeaderHeights;
        },

        getHeaderVersion: function (headerSection) {
            if (headerSection.hasClass('page-header-v1')) {
                return 'v1';
            } else if (headerSection.hasClass('page-header-v2')) {
                return 'v2';
            } else if (headerSection.hasClass('page-header-v3')) {
                return 'v3';
            } else if (headerSection.hasClass('page-header-v4')) {
                return 'v4';
            } else
                return 'clean';
        }

    }
    return stickyFilters;
});
