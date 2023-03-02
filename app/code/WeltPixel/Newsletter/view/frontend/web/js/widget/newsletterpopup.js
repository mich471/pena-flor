define([
    'jquery',
    'mage/cookies'
], function ($) {
    'use strict';

    $.widget('weltpixel.newsletterpopup', {
        options: {
            opened: false,
            newsletterPopup: false,
            overlayDivId: 'wpn-lightbox-overlay',
            lightboxDivId: 'wpn-lightbox-content',
            closeDivId:    'wpn-lightbox-close-newsletter',
            closeOnOverlayAlso : false,
            cookieName: 'weltpixel_newsletter',
            cookieNameSubscribed: 'weltpixel_newsletter_subscribed',
            pageCounter: 'weltpixel_pagecounter',
            cookieLifetime: 4,
            visitedPages: 1,
            secondsToDisplay: 1,
            isAjax: false,
            justCountPages: false,
            triggerButtonEnabled: false,
            justTriggerButton: false,
            content: '',
            steps: 1,
            containerClass: 'wp_newsletter_container',
            stepContainerClass: 'weltpixel_newsletter_step_container',
            step1ContainerClass: 'weltpixel_newsletter_step1',
            nextStepButton: 'wp-nl-next-step',
            nextStepCloseButton: 'wp-nl-close-popup',
            triggerButton: 'wp_newsletter_trigger',
            version: 1,
            gtmTracking: false,
            /* ExitIntent Options */
            exitIntent: false,
            exitIntentCookieName: 'weltpixel_exitintent',
            exitIntentCloseOnOverlayAlso: false,
            exitIntendDisplayed: false,
            exitIntentDisplayUserSubscribed: false,
            exitIntentDisplayClosedPopup: false,
            exitIntentContainerClass: 'wp_exitintent_container',
            exitIntentContentId: 'wpx-exitintent-popup',
            exitIntentOverlayDivId: 'wpn-exitintent-lightbox-overlay',
            exitIntentLightboxDivId: 'wpn-exitintent-lightbox-content',
            exitIntentCloseDivId:'wpn-lightbox-close-exitintent',
            exitIntentSteps: 1,
            exitIntentVersion: 1,
            exitIntentStepContainerClass: 'weltpixel_exitintent_newsletter_step_container',
            exitIntentStep1ContainerClass: 'weltpixel_exitintent_newsletter_step1',
            exitIntentNextStepButton: 'wp-ei-next-step',
            exitIntentNextStepCloseButton: 'wp-ei-close-popup',
            exitIntentGtmTracking: false,
            /* Social Login Options*/
            slIntegration: 3,
            slAppliesTo: 3
        },

        _create: function () {

            window.wp_newsletter_opened = window.wp_newsletter_opened || this.options.opened;
            this.popupLabel = 'Popup';
            var that = this;
            if (this.options.newsletterPopup) {
                if (!this.options.justCountPages) {
                    this.options.content = this.element[0];

                    if (!this.options.justTriggerButton) {
                        if (!this.getNewsletterCookie() && (this.getPageCount() >= this.options.visitedPages)) {
                            that.popupLabel = 'Popup';
                            setTimeout(this.showPopup.bind(this), 1000 * this.options.secondsToDisplay);
                        } else if (!this.getNewsletterCookie() && !this.options.isAjax) {
                            this.countPages();
                        }
                    }

                    $('#weltpixel_newsletter').bind('submit', function () {
                        if ($(this).valid()) {
                            that.setSubscribedCookie();
                            that.closeCallback();
                            if (that.options.gtmTracking) {
                                window.dataLayer.push({
                                    'event' : 'newsletterPopupSuccess',
                                    'eventLabel' : that._getNewsletterGtmLabel()
                                });
                            }
                        } else {
                            if (that.options.gtmTracking) {
                                window.dataLayer.push({
                                    'event' : 'newsletterPopupFailed',
                                    'eventLabel' : that._getNewsletterGtmLabel()
                                });
                            }
                        }
                    });
                } else {
                    if (!this.getNewsletterCookie() && (this.getPageCount() < this.options.visitedPages)) {
                        this.countPages();
                    }
                }

                if (this.options.triggerButtonEnabled) {
                    $(this._getElementClassSelector(this.options.triggerButton)).bind('click', function () {
                        that.popupLabel = 'Popup Trigger';
                        that.showPopup();
                        return false;
                    });
                }
            }

            if (this.options.exitIntent) {
                this.enableExitIntent();
            }

            if(that.options.slIntegration == 1) {
                $('.sl-widget .sociallogin-wrapper').hide();
            } else if( that.options.slIntegration == 2) {
                if(that.options.slAppliesTo == 1) {
                    $('div#wpx-exitintent-popup .form-group').hide()
                } else if(that.options.slAppliesTo == 2) {
                    $('div#wpx-newsletter-popup .form-group').hide()
                } else {
                    $('div#wpx-exitintent-popup .form-group').hide();
                    $('div#wpx-newsletter-popup .form-group').hide();
                }
            } else if(that.options.slIntegration == 3) {
                if(that.options.slAppliesTo == 1) {
                    $('div#wpx-exitintent-popup .sociallogin-wrapper').hide();
                } else if(that.options.slAppliesTo == 2) {
                    $('div#wpx-newsletter-popup .sociallogin-wrapper').hide();
                }
            }

            $('.weltpixel_newsletter .show-sl-buttons').bind('click', function(event){
                event.preventDefault();
                $('.weltpixel_newsletter .sl-buttons-wrapper, .weltpixel_newsletter .sl-login-back').show();
                $('.weltpixel_newsletter .sl-buttons-wrapper .block-heading, .weltpixel_newsletter .sl-show-action, .weltpixel_newsletter form#weltpixel_newsletter').hide();
               // $('.arv-cms').css('height', buttonWidgetHeight+'px');
            });

            $('.weltpixel_newsletter .sl-login-back').bind('click', function(){
                $('.weltpixel_newsletter .sl-buttons-wrapper, .weltpixel_newsletter .sl-login-back').hide();
                $('.weltpixel_newsletter .sl-show-action, .weltpixel_newsletter form#weltpixel_newsletter').show();
            })


        },

        enableExitIntent: function() {
            var that = this;

            $('#weltpixel_exitintent').bind('submit', function() {
                if ($(this).valid()) {
                    that.setSubscribedCookie();
                    if (that.options.exitIntentGtmTracking) {
                        window.dataLayer.push({
                            'event' : 'exitIntentSuccess',
                            'eventLabel' : that._getExitIntentGtmLabel()
                        });
                    }
                } else {
                    if (that.options.exitIntentGtmTracking) {
                        window.dataLayer.push({
                            'event' : 'exitIntentFailed',
                            'eventLabel' : that._getExitIntentGtmLabel()
                        });
                    }
                }
            });

            $(window).mouseleave(function(ev) {
                if (window.wp_newsletter_opened) return;
                if (that.getExitIntentSubscribedCookie() || that.getExitIntentDisplayClose() || that.exitIntendDisplayed || ev.clientY > 0 ) {
                    return;
                }

                setTimeout(
                    function() {
                        that.exitIntendDisplayed = true;
                        that.showExitIntentPopup();
                    }, 300
                );
            });
        },

        closeCallback: function () {
            $.cookie(this.options.cookieName, 'true', { expires: parseInt(this.options.cookieLifetime) });
        },

        setSubscribedCookie:  function() {
            $.cookie(this.options.cookieNameSubscribed, 'true', { expires: parseInt(this.options.cookieLifetime) });
        },

        countPages: function() {
            $.cookie(this.options.pageCounter, this.getPageCount() +1, { expires: parseInt(this.options.cookieLifetime) });
        },

        getPageCount: function () {
            return $.cookie(this.options.pageCounter) - 0;
        },

        getNewsletterCookie: function () {
            return $.cookie(this.options.cookieName);
        },

        getExitIntentCookie: function () {
            return $.cookie(this.options.exitIntentCookieName);
        },

        getExitIntentSubscribedCookie: function() {
            if (this.options.exitIntentDisplayUserSubscribed) return false;
            return $.cookie(this.options.cookieNameSubscribed);
        },

        getExitIntentDisplayClose: function() {
            if (this.options.exitIntentDisplayClosedPopup) return false;
            return this.getExitIntentCookie();
        },

        showPopup: function () {
            if (window.wp_newsletter_opened) {
                return;
            }
            this.initPopup();
            this.openPopup();
        },

        initPopup: function () {
            $('<div/>', {id: this.options.overlayDivId}).appendTo('body');
            if (this.options.version != 3) {
                $('<div/>', {id: this.options.lightboxDivId}).appendTo('body');
            } else {
                var backgroundImageUrl = $(this.element[0]).find('.image-background').attr('src');
                $('<div/>', {id: this.options.lightboxDivId}).css("background", 'url(' +  backgroundImageUrl +')').appendTo('body');
            }

            if (this.options.closeOnOverlayAlso) {
                try {
                    this.closeCallback();
                } catch(e) {}
            }

            var that = this;
            $(this._getElementIdSelector(this.options.overlayDivId)).bind('click', function () {
                that.closePopup();
            });
            $(window).resize(function(){
                that.adjustLightbox();
            });
        },

        openPopup: function() {
            if (window.wp_newsletter_opened) {
                this.closePopup();
                window.wp_newsletter_opened = false;
            }

            $(this.options.content).prependTo($(this._getElementIdSelector(this.options.lightboxDivId)));
            $(this._getElementIdSelector(this.options.lightboxDivId)).append("<div id='"+this.options.closeDivId+"'>X</div>");

            var that = this;
            $(this._getElementIdSelector(this.options.closeDivId)).bind('click', function () {
                that.forceClose();
            });

            $(this._getElementClassSelector(this.options.nextStepCloseButton)).bind('click', function () {
                that.forceClose();
            });

            if (this.options.steps != 1) {
                var stepContainerIdentifier = this._getElementClassSelector(this.options.stepContainerClass);
                var step1ContainerIdentifier = this._getElementClassSelector(this.options.step1ContainerClass);
                var newsLetterContainer = $(this._getElementClassSelector(this.options.containerClass));

                $(stepContainerIdentifier).hide();
                $(step1ContainerIdentifier).insertAfter(stepContainerIdentifier);
                $(step1ContainerIdentifier).show();

                if (this.options.gtmTracking) {
                    window.dataLayer.push({
                        'event' : 'newsletterPopupImpressionStep1',
                        'eventLabel' : this._getNewsletterGtmLabel()
                    });
                }

                $(this._getElementClassSelector(this.options.nextStepButton)).bind('click', function () {
                    $(step1ContainerIdentifier).hide().appendTo(newsLetterContainer);
                    $(stepContainerIdentifier).fadeIn("slow");
                    if (that.options.gtmTracking) {
                        window.dataLayer.push({
                            'event' : 'newsletterPopupImpression',
                            'eventLabel' : that._getNewsletterGtmLabel()
                        });
                    }
                });
            } else {
                if (this.options.gtmTracking) {
                    window.dataLayer.push({
                        'event' : 'newsletterPopupImpression',
                        'eventLabel' : this._getNewsletterGtmLabel()
                    });
                }
            }

            $(this.options.content).show();

            $(this._getElementIdSelector(this.options.overlayDivId)).show();
            $(this._getElementIdSelector(this.options.lightboxDivId)).show();

            window.wp_newsletter_opened = true;
            this.adjustLightbox();
        },

        adjustLightbox: function() {
            if (!window.wp_newsletter_opened) {
                return;
            }

            switch (this.options.version) {
                case 1:
                case 3:
                    this._adjustLightBoxV1();
                    break;
                default:
                    return;
            }
        },

        _adjustLightBoxV1: function() {
            var lightboxHeight = $(this._getElementIdSelector(this.options.lightboxDivId)).outerHeight();
            var lightboxWidth = $(this._getElementIdSelector(this.options.lightboxDivId)).outerWidth();

            var leftPos = 0;
            if (document.body.offsetWidth > lightboxWidth) {
                leftPos += (document.body.offsetWidth - lightboxWidth)/2;
            }
            var topPos = window.pageYOffset;
            if (window.innerHeight > lightboxHeight) {
                topPos += (window.innerHeight - lightboxHeight)/2;
            }

            $(this._getElementIdSelector(this.options.lightboxDivId)).css({
                left: leftPos + 'px',
                top: topPos + 'px'
            });
        },

        closePopup: function () {
            var newsLetterContainer = $(this._getElementClassSelector(this.options.containerClass));
            $(this.options.content).hide().appendTo(newsLetterContainer);
            $(this._getElementIdSelector(this.options.lightboxDivId)).remove();
            $(this._getElementIdSelector(this.options.overlayDivId)).remove();
            $(this._getElementIdSelector(this.options.lightboxDivId)).remove();
            window.wp_newsletter_opened = false;
            if (this.options.gtmTracking) {
                window.dataLayer.push({
                    'event' : 'newsletterPopupClosed',
                    'eventLabel' : this._getNewsletterGtmLabel()
                });
            }
        },

        forceClose: function() {
            try {
                this.closeCallback();
            } catch(e) {}
            this.closePopup();
        },

        _getNewsletterGtmLabel: function() {
          return this.popupLabel + " | " + "Version " + this.options.version + " | " + "Pages " + this.options.visitedPages + " | " + "Seconds " + this.options.secondsToDisplay;
        },

        showExitIntentPopup: function() {
            if (window.wp_newsletter_opened) {
                return;
            }
            this.initExitIntentPopup();
            this.openExitIntentPopup();
        },

        initExitIntentPopup: function () {
            $('<div/>', {id: this.options.exitIntentOverlayDivId}).appendTo('body');
            if (this.options.exitIntentVersion != 3) {
                $('<div/>', {id: this.options.exitIntentLightboxDivId}).appendTo('body');
            } else {
                var backgroundImageUrl = $(this._getElementIdSelector(this.options.exitIntentContentId)).find('.image-background').attr('src');
                $('<div/>', {id: this.options.exitIntentLightboxDivId}).css("background", 'url(' +  backgroundImageUrl +')').appendTo('body');
            }

            if (this.options.exitIntentCloseOnOverlayAlso) {
                try {
                    this.exitIntentCloseCallback();
                } catch(e) {}
            }

            var that = this;
            $(this._getElementIdSelector(this.options.exitIntentOverlayDivId)).bind('click', function () {
                that.closeExitIntentPopup();
            });
            $(window).resize(function(){
                that.adjustExitIntentLightbox();
            });
        },

        openExitIntentPopup: function() {
            if (window.wp_newsletter_opened) {
                this.closeExitIntentPopup();
                window.wp_newsletter_opened = false;
            }

            var exitIntentContent = $(this._getElementIdSelector(this.options.exitIntentContentId));

            exitIntentContent.prependTo($(this._getElementIdSelector(this.options.exitIntentLightboxDivId)));
            $(this._getElementIdSelector(this.options.exitIntentLightboxDivId)).append("<div id='"+this.options.exitIntentCloseDivId+"'>X</div>");

            var that = this;
            $(this._getElementIdSelector(this.options.exitIntentCloseDivId)).bind('click', function () {
                that.forceExitIntentClose();
            });

            $(this._getElementClassSelector(this.options.exitIntentNextStepCloseButton)).bind('click', function () {
                that.forceExitIntentClose();
            });

            if (this.options.exitIntentSteps != 1) {
                var stepContainerIdentifier = this._getElementClassSelector(this.options.exitIntentStepContainerClass);
                var step1ContainerIdentifier = this._getElementClassSelector(this.options.exitIntentStep1ContainerClass);
                var newsLetterContainer = $(this._getElementClassSelector(this.options.exitIntentContainerClass));

                $(stepContainerIdentifier).hide();
                $(step1ContainerIdentifier).insertAfter(stepContainerIdentifier);
                $(step1ContainerIdentifier).show();
                if (this.options.exitIntentGtmTracking) {
                    window.dataLayer.push({
                        'event' : 'exitIntentImpressionStep1',
                        'eventLabel' : this._getExitIntentGtmLabel()
                    });
                }

                $(this._getElementClassSelector(this.options.exitIntentNextStepButton)).bind('click', function () {
                    $(step1ContainerIdentifier).hide().appendTo(newsLetterContainer);
                    $(stepContainerIdentifier).fadeIn("slow");
                    if (that.options.exitIntentGtmTracking) {
                        window.dataLayer.push({
                            'event' : 'exitIntentImpression',
                            'eventLabel' : that._getExitIntentGtmLabel()
                        });
                    }
                });
            } else {
                if (this.options.exitIntentGtmTracking) {
                    window.dataLayer.push({
                        'event' : 'exitIntentImpression',
                        'eventLabel' : this._getExitIntentGtmLabel()
                    });
                }
            }

            exitIntentContent.show();

            $(this._getElementIdSelector(this.options.exitIntentOverlayDivId)).show();
            $(this._getElementIdSelector(this.options.exitIntentLightboxDivId)).show();

            window.wp_newsletter_opened = true;
            this.adjustExitIntentLightbox();
        },

        closeExitIntentPopup: function () {
            var newsLetterContainer = $(this._getElementClassSelector(this.options.exitIntentContainerClass));
            var exitIntentContent = $(this._getElementIdSelector(this.options.exitIntentContentId));
            exitIntentContent.hide().appendTo(newsLetterContainer);
            $(this._getElementIdSelector(this.options.exitIntentLightboxDivId)).remove();
            $(this._getElementIdSelector(this.options.exitIntentOverlayDivId)).remove();
            $(this._getElementIdSelector(this.options.exitIntentLightboxDivId)).remove();
            window.wp_newsletter_opened = false;
            if (this.options.exitIntentGtmTracking) {
                window.dataLayer.push({
                    'event' : 'exitIntentClosed',
                    'eventLabel' : this._getExitIntentGtmLabel()
                });
            }
        },

        adjustExitIntentLightbox: function() {
            if (!window.wp_newsletter_opened) {
                return;
            }

            switch (this.options.exitIntentVersion) {
                case 1:
                case 3:
                    this._adjustExitIntentLightBoxV1();
                    break;
                default:
                    return;
            }
        },


        _adjustExitIntentLightBoxV1: function() {
            var lightboxHeight = $(this._getElementIdSelector(this.options.exitIntentLightboxDivId)).outerHeight();
            var lightboxWidth = $(this._getElementIdSelector(this.options.exitIntentLightboxDivId)).outerWidth();

            var leftPos = 0;
            if (document.body.offsetWidth > lightboxWidth) {
                leftPos += (document.body.offsetWidth - lightboxWidth)/2;
            }
            var topPos = window.pageYOffset;
            if (window.innerHeight > lightboxHeight) {
                topPos += (window.innerHeight - lightboxHeight)/2;
            }

            $(this._getElementIdSelector(this.options.exitIntentLightboxDivId)).css({
                left: leftPos + 'px',
                top: topPos + 'px'
            });
        },

        exitIntentCloseCallback: function () {
            $.cookie(this.options.exitIntentCookieName, 'true', { expires: parseInt(this.options.cookieLifetime) });
        },

        forceExitIntentClose: function() {
            try {
                this.exitIntentCloseCallback();
            } catch(e) {}
            this.closeExitIntentPopup();
        },

        _getExitIntentGtmLabel: function() {
            return "Exit Intent | Version " + this.options.exitIntentVersion;
        },


        _getElementIdSelector: function(idName) {
            return '#' + idName;
        },

        _getElementClassSelector: function(className) {
            return '.' + className;
        }
    });

    return $.weltpixel.newsletterpopup;
});
