define(['jquery'], function ($) {
    "use strict";

    var navigationJs =
        {
            init: function() {
                var navigation = $('.navigation'),
                    isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent),
                    scroll = isMobile ? 0 : 15,
                    ww = $(window).width() + scroll;

                navigationJs.waitUntilExists(function() {
                    return navigation.length > 0;
                }, function() {
                    navigationJs.adjustLevelTopFullwidth(navigation);
                    navigationJs.adjustMenuBlockSize(navigation);

                    navigation.find('.level0.submenu').on('mouseenter', function() {
                        navigationJs.updateBold($(this));
                    });

                    /** stop redirect at first click on mobile */
                    if (isMobile) {
                        var clickedEl = false,
                            clickedCount = 0,
                            sizeChanged = false;
                        $('.nav-sections').addClass('nav-mobile');
                        /** detect orientation change (same as resize) */
                        window.addEventListener('resize', function() {
                            sizeChanged = true;
                        }, false);

                        navigation.find('a.level-top').on('click', function(e) {
                            if ($(this).parent().hasClass('parent') && !$('html').hasClass('nav-open')) {
                                if (clickedEl && $(this).is(clickedEl)) clickedCount++;
                                else clickedCount = 1;

                                if (clickedCount < 2) {
                                    e.preventDefault();
                                } else {
                                    /** quick fix if the mobile device was flipped
                                     *  and nav menu was toggled from mobile to desktop */
                                    if (sizeChanged) {
                                        window.location.href = $(this).attr('href');
                                    }
                                }
                            }
                            clickedEl = $(this);
                        });
                    }

                }, function() {
                    /** on error do nothing */
                }, 100, 300);

                if (!navigationJs.isCheckoutPage()) {
                    var searchBlock = $('.page-header-v2 .block-search').not('.minisearch-v2'),
                        languageBlock = $('#switcher-language');
                    if (ww >= window.screenM && ww <= parseInt(window.widthThreshold)) {
                        $('body').addClass('mobile-nav');
                        if (languageBlock.length) languageBlock.show();
                        if (searchBlock.length) searchBlock.css({'right': $('.header_right').outerWidth() + 'px'});
                    } else {
                        $('body').removeClass('mobile-nav');
                        if (languageBlock.length) {
                          if ($('.nav-toggle').is(':visible')) {
                              languageBlock.hide();
                          } else {
                              languageBlock.show();
                          }
                        }
                        if (searchBlock.length) searchBlock.css({'right': ''});
                    }
                }

                $('.action.nav-toggle').on('click', function() {
                    var is_safari =  navigator.userAgent.indexOf('Safari') != -1 && navigator.userAgent.indexOf('Chrome') == -1 &&  navigator.userAgent.indexOf('Android') == -1
                    if (is_safari) {
                        if ($('html').hasClass('nav-open')) {
                            $('.page-wrapper').css('overflow','hidden');
                        } else {
                            $('.page-wrapper').css('overflow','visible');
                        }
                    }
                });

                if (parseInt(window.menuFocusOverlay) && parseInt(designSettingsEnabled)) {
                    setTimeout(function () {
                        navigationJs.createFocusOverlay(navigation);
                    },1000)
                }

                navigationJs.adjustHoverOnLastElement();
            },
            isCheckoutPage: function() {
                return $('body').hasClass('checkout-index-index');
            },
            adjustLevelTopFullwidth: function(navigation) {
                var pageWrapperW = $('.page-wrapper').width(),
                    headerContentW = $('.header.content').outerWidth(),
                    fullWidthWrapper = navigation.find('.fullwidth-wrapper'),
                    i = 1;

                fullWidthWrapper.hide();
                fullWidthWrapper.each(function() {
                    $(this)
                        .css({'width': pageWrapperW + 'px'})
                        .find('.fullwidth-wrapper-inner')
                        .css({'width': headerContentW + 'px'});

                    if (i == fullWidthWrapper.length) {
                        fullWidthWrapper.show();
                    }
                    i++;
                });
            },
            adjustMenuBlockSize: function(nav) {
                var menuBlocks = nav.find('.menu-block');
                menuBlocks.each(function() {
                    var parent = $(this).closest('.level0.submenu');
                    /** apply only for sectioned and boxed */
                    if (!$(this).hasClass('no-child') && !parent.hasClass('fullwidth') && !parent.hasClass('default')) {
                        var style = 'display: block; position: absolute !important; top: -10000px !important;',
                            copy = parent.clone().attr('style', style).appendTo(parent.parent()),
                            topBlock = copy.find('.menu-block.top-block'),
                            rightBlock = copy.find('.menu-block.right-block'),
                            bottomBlock = copy.find('.menu-block.bottom-block'),
                            leftBlock = copy.find('.menu-block.left-block'),
                            totalWidth = 0,
                            levelCount = 0;

                        if ($(this).hasClass('top-block') || $(this).hasClass('bottom-block')) {
                            if (parent.hasClass('sectioned')) {
                                /** width calc for sectioned */
                                copy.find('.megamenu.level1').each(function() {
                                    totalWidth += Math.ceil($(this).outerWidth());
                                    levelCount++;
                                });
                            } else {
                                /** width calc for boxed and default */
                                totalWidth = Math.ceil(copy.find('.columns-group').outerWidth());
                                /** overwrite totalWidth if the parent has a min-width bigger than calculated totalWidth */
                                var minWidth = parseInt(parent.css('min-width'));
                                if (minWidth && minWidth > totalWidth) {
                                    totalWidth = Math.ceil(minWidth);
                                }
                            }

                            /** apply proper width */
                            $(this).closest('.submenu-child').width(totalWidth);

                            /** adjust with of top and bottom blocks if whatever left or/and right blocks exists */
                            if (parent.hasClass('sectioned') || parent.hasClass('boxed')) {
                                if (rightBlock.length || leftBlock.length) {
                                    var topBottomWidth = totalWidth,
                                        elCount = levelCount ? levelCount : 1,
                                        columnWidth = totalWidth / elCount;

                                    if (rightBlock.length) {
                                        topBottomWidth += columnWidth;
                                        parent.find('.menu-block.right-block').closest('.submenu-child').width(Math.ceil(columnWidth));
                                    }
                                    if (leftBlock.length) {
                                        topBottomWidth += columnWidth;
                                        parent.find('.menu-block.left-block').closest('.submenu-child').width(Math.ceil(columnWidth));
                                    }
                                }

                                if (topBlock.length) {
                                    parent.find('.menu-block.top-block').closest('.submenu-child').width(Math.ceil(topBottomWidth));
                                }
                                if (bottomBlock.length) {
                                    parent.find('.menu-block.bottom-block').closest('.submenu-child').width(Math.ceil(topBottomWidth));
                                }
                            }
                        }

                        /** always adjust width of left and right blocks for sectioned menu */
                        if (parent.hasClass('sectioned') || parent.hasClass('boxed')) {
                            if (rightBlock.length || leftBlock.length) {
                                var columnWidth = copy.find('.columns-group.starter').outerWidth();
                                if (rightBlock.length) {
                                    parent.find('.menu-block.right-block').width(Math.ceil(columnWidth));
                                }
                                if (leftBlock.length) {
                                    parent.find('.menu-block.left-block').width(Math.ceil(columnWidth));
                                }
                            }
                        }

                        copy.remove();
                    }
                });

                menuBlocks.show();
            },
            updateBold: function(el) {
                var parent = el.closest('.megamenu').find('a.level-top');
                parent.addClass('bold-menu');
                el.on('mouseleave', function() {
                    parent.removeClass('bold-menu');
                });
            },
            waitUntilExists: function (isready, success, error, count, interval) {
                if (count === undefined) count = 300;
                if (interval === undefined) interval = 20;

                if (isready()) {
                    success();
                    return;
                }
                setTimeout(function(){
                    if (!count) {
                        if (error !== undefined) {
                            error();
                        }
                    } else {
                        navigationJs.waitUntilExists(isready, success, error, count -1, interval);
                    }
                }, interval);
            },
            createFocusOverlay: function (navigation) {
                let that = this,
                    globalPromo = $('.page-wrapper .page-header').find('.header-global-promo'),
                    headerPlaceholder = $('.header-placeholder'),
                    megaMenuBackgroundOverlay = $('.overlay-focus-megamenu'),
                    globalNotificationWrapper = globalPromo.find('.global-notification-wrapper');

                let checkHeight = setInterval(function () {
                    if (globalNotificationWrapper.is(':visible') && globalNotificationWrapper.height()) {
                        if (headerPlaceholder.length) {
                            that.setFocusOverlayPosition(megaMenuBackgroundOverlay, headerPlaceholder);
                            globalNotificationWrapper.on('click', '.close-global-notification', function() {
                                that.setFocusOverlayPosition(megaMenuBackgroundOverlay, headerPlaceholder);
                            });
                            clearInterval(checkHeight);
                        }
                    } else {
                        that.setFocusOverlayPosition(megaMenuBackgroundOverlay, headerPlaceholder);
                        clearInterval(checkHeight);
                    }
                }, 500);

                navigation.find('.megamenu.level0.mm-has-children').on('mouseenter', function() {
                    megaMenuBackgroundOverlay.addClass('overlay-focus-megamenu-show');

                });
                navigation.find('.megamenu.level0.mm-has-children').on('mouseleave', function() {
                    megaMenuBackgroundOverlay.removeClass('overlay-focus-megamenu-show');
                });
            },
            setFocusOverlayPosition: function (megaMenuBackgroundOverlay, headerPlaceholder) {
                let that = this,
                    megaMenuNavi = $('.navigation'),
                    headerSection = $('.page-wrapper div.page-header'),
                    headerContent =  $('.header.content'),
                    overlayPosition;

                switch (that.getHeaderVersion(headerSection)) {
                    case "clean" :
                        overlayPosition = parseInt(headerContent.outerHeight() + megaMenuNavi.innerHeight());
                        break;
                    case "v3" :
                        overlayPosition = parseInt(headerSection.innerHeight());
                        break;
                    default :
                        overlayPosition = parseInt(headerSection.outerHeight() + megaMenuNavi.innerHeight());
                        break;
                }
                megaMenuBackgroundOverlay.css('top', overlayPosition);
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
            },
            adjustHoverOnLastElement: function () {
                $('.megamenu.level0.last').hover(
                    function onMouseEnter() {
                        $('.megamenu.level0.last ul').css('display', '');
                    },
                    function onMouseLeave() {
                        $('.megamenu.level0.last a:first-child').removeClass('ui-state-focus');
                        $('.megamenu.level0.last ul').css('display', 'none');
                    }
                )
            },
        };

    return navigationJs;
});
