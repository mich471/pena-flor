define([
    "jquery"
], function ($) {
    "use strict";
    window.arv = {
        init: function () {
            $('.proceed').on('click', function (event) {
                event.preventDefault();
                $('.arv-content .field.email, .proceed').hide();
                $('.arv-content .field.password').show();
                $('.arv-login-submit, .arv-login-secondary').show();
            })

            $('#arv_change_email').on('click', function () {
                $('.arv-content .field.email, .proceed').show();
                $('.arv-content .field.password').hide();
                $('.arv-login-submit, .arv-login-secondary').hide();
            })

        },
        clearArv: function () {
            $('.arv-clear').on('click', function () {
                if (window.localStorage.product_data_storage) {
                    window.localStorage.removeItem('product_data_storage');
                }
                if (window.localStorage.recently_viewed_product) {
                    window.localStorage.removeItem('recently_viewed_product');
                }
                $('.arv-item-list ol').empty();
                $('.arv-wrapper').hide();
            })
        },
        elementLoaded: function (isready, success, error, count, interval) {
            if (count === undefined) {
                count = 5;
            }
            if (interval === undefined) {
                interval = 1000;
            }
            if (isready()) {
                success();
                return;
            }
            // The call back isn't ready. We need to wait for it
            setTimeout(function () {
                if (!count) {
                    // We have run out of retries
                    if (error !== undefined) {
                        error();
                    }
                } else {
                    // Try again
                    window.arv.elementLoaded(isready, success, error, count - 1, interval);
                }
            }, interval);

        },
        secondBtn: function () {
            window.arv.elementLoaded(function () {
                return $('.arv-item-list .action.towishlist, .arv-item-list .action.tocompare').length > 0;
            }, function () {
                if ($('body').hasClass('theme-pearl')) {
                    $('.arv-item-list .action.towishlist').addClass("icon-line-heart-arv");
                    $('.arv-item-list .action.tocompare').addClass("icon-line-compare-arv");
                }
                if (window.isSlCustomPopupUsed && parseInt(window.isSlCustomPopupUsed)) {
                    $('.arv-item-list .action.towishlist').removeAttr('data-post');
                }

                if (!$('.arv-wrapper button.tocart').length) {
                    $('.actions-secondary').addClass('no-addtocart-btn');
                }

            });
        },
        createArv: function () {
            let scrollPosition = $(window).scrollTop();

            window.arv.elementLoaded(function () {
                return $('.arv-wrapper .product-items').length > 0;
            }, function () {
                $('#arv_btn').css('opacity', '100%');
                $('#arv_btn').slideDown();

                if (scrollPosition + $(window).height() === $(document).height()) {
                    $('#arv_btn').slideUp();
                    $('#arv-content').slideUp();
                }
            });
        },
        arvSlideUp: function () {
            $('.arv-slider-btn').on('click', function () {
                $('.arv-content').slideToggle();
            });
        },

        outsideClick: function () {
            let container = $('.arv-wrapper');

            $(document).mouseup(function (e) {
                if (!container.is(e.target) && container.has(e.target).length === 0) {
                    $('.arv-content').slideUp();
                }
            });
        },
        closeSlide: function () {
            window.onclick = function (event) {
                var container = document.getElementById('arv-content');
                var recentBtn = document.getElementById('arv_btn');

                if (!container.contains(event.target)
                    && !recentBtn.contains(event.target)
                ) {
                    if ($('.arv-content').css('display') == 'block') {
                        $('.arv-content').slideUp();
                    }
                }
            }
        }
    }
});
