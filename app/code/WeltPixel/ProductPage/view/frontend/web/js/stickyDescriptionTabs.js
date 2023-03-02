define([
	'jquery'
], function ($) {
	"use strict";

	var stickyDescriptionTabs = {
        options: {
            mobileThreshold: '786',
            stickyTabsColor: '#FFFFF',
        },

        initialPosition: ($('.nav-wrapper').is(":visible") ? $('.nav-wrapper').offset().top : '' ),

        init: function (mobileThreshold, stickyTabsColor) {
            this.options.mobileThreshold = mobileThreshold;
            this.options.stickyTabsColor = stickyTabsColor;
            let that = this,
                screenWidth = $(window).width();

            $('a.data.switch').bind('click', function (e) {
                e.preventDefault();
                let target = $(this).attr("href"),
                    stickyMenuHeights = that.calculateStickyHeaderHeight();

                $('.content-title').each(function () {
                    if (target.substr(1) === $(this).attr('id')) {
                        let offsetPosition = $(this).offset().top - stickyMenuHeights.outerHeight - $('.content-title').outerHeight() -10;
                        $('html, .page-wrapper').stop().animate({
                            scrollTop: offsetPosition
                        }, 600);
                    }
                });
                return false;
            });

             if (screenWidth <= mobileThreshold) {
                 that.makeMobileTabWidget();
             }

            $(window).on('scroll mousedown wheel DOMMouseScroll mousewheel keyup', function (e) {
                that.initialPosition = $('.gallery-placeholder').outerHeight();
            });

            $(window).scroll(function () {
                if ((screenWidth >= mobileThreshold)) {
                    that.makeDescriptionTabsSticky(stickyTabsColor);
                }
            });
        },

        makeMobileTabWidget: function () {
            let contentLinks = $('.content-title'), i,
                contentData = $('.data.item.content');

            contentData.each(function () {
                $(this).addClass('mobile-tabs')
            });

            for (i = 0; i < contentLinks.length; i++) {
                contentLinks[i].addEventListener("click", function () {
                    this.classList.toggle("active");
                    let content = this.nextElementSibling;
                    content.classList.toggle('active');
                    if (content.style.maxHeight) {
                        content.style.maxHeight = null;
                    } else {
                        content.style.maxHeight = content.scrollHeight + "px";
                    }
                });
            }
        },

        makeDescriptionTabsSticky: function (stickyTabsColor) {
            let that = this,
                descriptionsTabs = $('.nav-wrapper'),
                sc = $(window).scrollTop(),
                stickyMenuHeights = that.calculateStickyHeaderHeight(),
                pageHeader = $('.page-wrapper div.sticky-header'),
                containerShowLimit = that.initialPosition + stickyMenuHeights.outerHeight,
                detailsContent = $('.data.item.content'),
                detailsTitle = $('.content-title');

            detailsContent.each(function (i) {
                let tabsPos = $(this).offset().top - stickyMenuHeights.height - detailsContent.outerHeight() - detailsTitle.outerHeight();
                if (tabsPos <= sc) {
                    $('.tabs-title-wrapper div.selected-tab').removeClass('selected-tab');
                    $('.tabs-title-wrapper div').eq(i).addClass('selected-tab');
                }
            });

            if (sc > containerShowLimit) {
                pageHeader.addClass('no-box-shadow', 100);
                descriptionsTabs.addClass("nav-wrapper-sticky");
                descriptionsTabs.css('background-color', stickyTabsColor);
                descriptionsTabs.css('top', stickyMenuHeights.height);
            } else {
                descriptionsTabs.removeClass("nav-wrapper-sticky");
                pageHeader.removeClass('no-box-shadow', 100);
                descriptionsTabs.css('background-color', '');
                descriptionsTabs.css('top', '');
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
                        stickyHeaderHeights.outerHeight = headerSection.outerHeight() + navMenuSection.outerHeight();
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
            }
        },
    }

	return stickyDescriptionTabs;
});
