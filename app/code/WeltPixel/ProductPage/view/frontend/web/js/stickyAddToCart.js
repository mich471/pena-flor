define([
	'jquery'
], function ($) {
	"use strict";

	var stickyAddToCart = {
        options: {
            mobileTreshold: '768',
            isOnDesktopEnabled: '1',
            isOnMobileEnabled: '1',
            isStickyScrollUpEnabled: '0',
            stickyAddToCartDisplayMode: '',
            stickyAddToCartDisplayModeMobile: '',
        },

		init: function (mobileTreshold, isOnDesktopEnabled, isOnMobileEnabled, isStickyScrollUpEnabled, stickyAddToCartDisplayMode, stickyAddToCartDisplayModeMobile) {
            this.options.mobileTreshold = mobileTreshold;
            this.options.isOnDesktopEnabled = isOnDesktopEnabled;
            this.options.isOnMobileEnabled = isOnMobileEnabled;
            this.options.isStickyScrollUpEnabled = isStickyScrollUpEnabled;
            this.options.stickyAddToCartDisplayMode = stickyAddToCartDisplayMode;
            this.options.stickyAddToCartDisplayModeMobile = stickyAddToCartDisplayModeMobile;

            if ($('.price-configured_price').length) {
                $('.fixed-cart-container .wp-st-product-price').html($('.price-configured_price').html());
            } else {
                $('.fixed-cart-container .wp-st-product-price').html($('.product-info-main .product-info-price .price-box').html());
            }

            if (isOnMobileEnabled == '1') {
                $('.btt-button').css("bottom", parseInt($('.btt-button').css("bottom")) + 60 + "px")
            }

            $('.wp-st-addtocart-container > a').bind('click', function () {
                $("#product-addtocart-button").trigger('click');
            });

            var that = this;
            $(window).scroll(function (event) {
                var screenWidth = $(window).width();
                if ($('.price-configured_price').length) {
                    $('.fixed-cart-container .wp-st-product-price').html($('.price-configured_price').html());
                } else {
                    $('.fixed-cart-container .wp-st-product-price').html($('.product-info-main .product-info-price .price-box').html());
                }
                if ((isOnDesktopEnabled == "1") && (screenWidth >= mobileTreshold)) {
                    that.makeStickyCart(stickyAddToCartDisplayMode, isStickyScrollUpEnabled);
                } else if (isOnMobileEnabled == "1")  {
                    that.makeStickyCart();
                    that.makeStickyCart(stickyAddToCartDisplayMode, isStickyScrollUpEnabled, isOnMobileEnabled, stickyAddToCartDisplayModeMobile);
                } else {
                    $('.fixed-cart-container').hide();
                }

            });
		},


        makeStickyCart: function(stickyAddToCartDisplayMode, isStickyScrollUpEnabled, isOnMobileEnabled, stickyAddToCartDisplayModeMobile) {
            let that = this,
                element = $('#product-addtocart-button'),
                fixedCartContainer = $('.fixed-cart-container'),
                stickyMenu = $('.page-header.sticky-header'),
                navSection = $('.sections.nav-sections-4')


            if ($('#bundleSummary') && $('#bundleSummary').is(":visible")) {
                element = $('#bundleSummary');
            }

            if (this.getHeaderVersion($('div.page-header')) === 'v4') {
                stickyMenu = $('.sections.nav-sections-4');
            }

            var containerShowLimit = 600;
            if (element.length) {
                containerShowLimit = element.position().top;
            }
            let sc = $(window).scrollTop();


            if (sc > containerShowLimit) {
                if (isOnMobileEnabled === '1') {
                    switch (stickyAddToCartDisplayModeMobile) {
                        case 'default':
                            if (sc + $(window).height() === $(document).height()) {
                                fixedCartContainer.removeClass("sticky-slide-up-mobile");
                            } else {
                                fixedCartContainer.addClass("sticky-slide-up-mobile")
                            }
                            that.lastScrollPosition = sc;
                            break;
                        case 'scroll-up':
                            if (sc < that.lastScrollPosition && sc !== 0) {
                                fixedCartContainer.addClass("sticky-slide-up-mobile");
                            } else {
                                fixedCartContainer.removeClass("sticky-slide-up-mobile");
                            }
                            that.lastScrollPosition = sc;
                            break;
                    }
                }

                fixedCartContainer.addClass("sticky-slide-up-desktop");

                if (fixedCartContainer.is(':visible') && stickyAddToCartDisplayMode === 'replace') {
                    stickyMenu.addClass('sticky-header-fade-out');
                    navSection.addClass('sticky-header-fade-out');
                } else if(fixedCartContainer.is(':visible') && stickyAddToCartDisplayMode === 'under' && isStickyScrollUpEnabled === '1') {
                    fixedCartContainer.css('margin-top', 0);
                    stickyMenu.addClass('sticky-menu-on-top');
                } else {
                    fixedCartContainer.css('margin-top', that.calculateStickyMenu());
                    stickyMenu.addClass('sticky-menu-on-top');
                    fixedCartContainer.addClass('sticky-custom-index');
                }
            } else {
                fixedCartContainer.removeClass("sticky-slide-up-desktop sticky-slide-up-mobile");
                stickyMenu.removeClass("sticky-header-fade-out");
                navSection.removeClass("sticky-header-fade-out");
            }
        },
        lastScrollPosition: 0,
        calculateStickyMenu: function () {
            let that = this,
                stickyMenu = $('.page-header.sticky-header'),
                headerSection = $('.page-wrapper div.page-header'),
                heightOfStickyMenu = 0;
            switch (that.getHeaderVersion(headerSection)) {
                case "v4":
                    if (stickyMenu.is(':visible')) {
                        heightOfStickyMenu = stickyMenu.outerHeight() + $('.section-items').outerHeight();
                        return heightOfStickyMenu;
                    }
                    break;
                default :
                    if (stickyMenu.is(':visible')) {
                        heightOfStickyMenu = stickyMenu.outerHeight();
                        return heightOfStickyMenu;
                    }
                    break;
            }
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
	};

	return stickyAddToCart;
});
