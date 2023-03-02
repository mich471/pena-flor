define([
    'jquery',
    'Magento_Customer/js/customer-data',
    'Magento_Customer/js/model/authentication-popup',
    'Magento_Ui/js/modal/modal',
    'accordion',
    'domReady!'
], function ($, customerData, authenticationPopup, modal) {
    'use strict';
    window.sl = {
        socialLoginClick: function() {
            $(document).on('click', '.ajax-login', function() {
                $('#sl_login_tab_title strong').addClass('active');
                $('#sl_register_tab_title strong').removeClass('active');
                $('#login_section').show();
                $('#registration_section').hide();
            });

            /* $(document).on('click', '.ajax-register', function() {
                $('#sl_register_tab_title strong').addClass('active');
                $('#sl_login_tab_title strong').removeClass('active');
                $('#login_section').hide();
                $('#registration_section').show();
            }); */

            $(document).on('click', '.ajax-register', function(){
                $('#sl_login_tab_title strong').removeClass('active');
                $('#login_section').hide();
                $('.modals-overlay').hide();
                window.location.href = 'customer/account/create';
                });

            $(".checkout-cart-index .cart-summary .sociallogin-block-title").unbind('click');
            if(window.cartPageAccordionState == 1) {
                $('.cart-summary .sociallogin-padding').show();
                $(this).toggleClass('open');
            }
            $('.checkout-cart-index .cart-summary .sociallogin-block-title').on("click", function(){
                $('.cart-summary .sociallogin-padding').slideToggle('slow');
                $(this).toggleClass('open');
            });
            $('#sl_buttons').accordion();
            $(document).on('click', '.sociallogin-button', function () {
                 var $this = $(this);
                 if ($this.hasClass('button-twitter')) {
                    var twitterOauthUrl = window.twitterOauthUrl;
                    if (!window.twitterOauthCall) {
                        window.twitterOauthCall = true;
                        $.ajax({
                            url: twitterOauthUrl,
                            type: 'POST'
                        }).done(function (response) {
                            if (response.success && response.oauthLink) {
                                setTimeout(window.sl.socialLogin(response.oauthLink), 5000);
                            }
                            window.twitterOauthCall = false;
                        });
                    }
                 } else {
                     setTimeout(window.sl.socialLogin($this.data('href')), 5000);
                 }
                return false;
            });

            if(!window.isCustomer && window.isSlCustomPopupUsed && parseInt(window.isSlCustomPopupUsed)) {
                $('.towishlist').removeAttr('data-post');// remove add to wishlist link
                $('.mailto').attr("href","#");// remove mailto link
                $('.action-auth-toggle').removeAttr('data-trigger');
            }
        },

        slWidgetInit: function(buttonsCount) {
            var arvHeight = $('.arv-cms').height();
            var buttonDefaultHeight = 30;
            var buttonWidgetMinHeight = 250;
            var buttonWidgetHeight = 250;
            var minButtonCountH = 3;
            var isSet = false;

            if(buttonsCount > minButtonCountH) {
                buttonWidgetHeight = 200 + (buttonsCount - minButtonCountH) * buttonDefaultHeight;
                $('.arv-cms').css('height', buttonWidgetMinHeight+'px');
                isSet = true;
            }

            if(buttonsCount >= 1 && buttonsCount <= minButtonCountH) {
                buttonWidgetHeight = 250 + buttonsCount * buttonDefaultHeight;
                $('.arv-cms').css('height', buttonWidgetHeight+'px');
                isSet = true;
            }

            if(arvHeight < buttonWidgetMinHeight && !isSet) {
                $('.arv-cms').css('height', buttonWidgetMinHeight+'px');
            }

            $('.arv-wrapper .show-sl-buttons').on('click', function(event){
                event.preventDefault();
                $('.arv-wrapper .sl-buttons-wrapper,.arv-wrapper .sl-login-back').show();
                $('.arv-wrapper .sl-buttons-wrapper .block-heading, .arv-wrapper .sl-show-action, .arv-wrapper .sl-login-section').hide();
                $('.arv-cms').css('height', buttonWidgetHeight+'px');
            });

            $('.arv-wrapper .sl-login-back').on('click', function(){
                $('.arv-wrapper .sl-buttons-wrapper, .arv-wrapper .sl-login-back').hide();
                $('.arv-wrapper .sl-buttons-wrapper .block-heading, .arv-wrapper .sl-show-action, .arv-wrapper .sl-login-section').show();
                if(arvHeight < buttonWidgetMinHeight) {
                    $('.arv-cms').css('height', buttonWidgetMinHeight+'px');
                }
            })
        },

        sendEmailClick: function() {
            $('#sociallogin_send_email').unbind('click').on('click', window.sl.setEmail);
            var formElement = $('#sociallogin_form_email');
            var inputeEl = $('#real_email_address');
            formElement.find(inputeEl).keypress(function (event) {
                var code = event.keyCode || event.which;
                if (code === 13) {
                    window.sl.setEmail();
                    event.preventDefault();
                    return false;
                }
            });

        },

        setEmail: function() {
            window.sl.appendLoading($('.sl-submit-email'));
            var formElement = $('#sociallogin_form_email');

            if (!formElement.valid()) {
                return;
            }

            var input = $("<input>")
                .attr("type", "hidden")
                .attr("name", "type").val('instagram');
            formElement.append($(input));

            var parameters = formElement.serialize();
            var ajaxUrl = window.emailFormUrl;

            window.sl.removeMsg($('#email_modal'), 'message-error error message');

            return $.ajax({
                url: ajaxUrl,
                type: 'POST',
                data: parameters
            }).done(function (response) {
                window.sl.addMsg($('#email_modal'),response);
                if (response.success) {
                    if (response.url == '' || response.url == null) {
                        setTimeout(function(){
                            window.sl.removeLoading($('.sl-submit-email'));
                            $("#email_modal").modal('closeModal');
                            window.location.reload(true);
                        },2000);
                    } else {
                        setTimeout(function(){
                            window.sl.removeLoading($('.sl-submit-email'));
                            $("#email_modal").modal('closeModal');
                            window.location.href = response.url;
                        },2000);
                    }
                }
                window.sl.removeLoading($('.sl-submit-email'));
            });

        },
        socialLogin: function (href) {
            var screen = null,
                params = [
                    'resizable=yes',
                    'scrollbars=no',
                    'toolbar=no',
                    'menubar=no',
                    'location=no',
                    'directories=no',
                    'status=yes',
                    'left=400',
                    'top=200',
                    'width=400',
                    'height=400'
                ];

            if (screen) {
                screen.close();
            }
            if (href) {
                screen = window.open(href, 'sociallogin_popup', params.join(','));
                screen.focus();
            } else {
                alert('Error occured, please try again!');
            }
            return false;
        },
        emailPopUp: function() {
            var modalContainer = $('#email_modal');
            if(modalContainer.length) {
                var options = {
                        type: 'popup',
                        responsive: true,
                        innerScroll: false,
                        focus: '.sl-input-email',
                        buttons: [{
                            text: $.mage.__('Close'),
                            class: '',
                            click: function () {
                                this.closeModal();
                            }
                        }]
                    },
                    popup = modal(options, $('#email_modal'));
            }
        },

        appendLoading: function (block) {
            var loadingClass = 'sl-loader';
            block.css('position', 'relative');
            block.prepend($("<div></div>", {"class": loadingClass}))
        },

        removeLoading: function (block) {
            var loadingClass = 'sl-loader';
            block.css('position', '');
            block.find("." + loadingClass).remove();
        },
        /**
         * @param block
         * @param response
         */
        addMsg: function (block, response) {
            var errorMsgClass = 'message-error error message';
            var successMsgClass = 'message-success success message';
            var message = response.message,
                messageClass = response.success ? successMsgClass : errorMsgClass;

            if (typeof(message) === 'object' && message.length > 0) {
                message.forEach(function (msg) {
                    this._appendMessage(block, msg, messageClass);
                }.bind(this));
            } else if (typeof(message) === 'string') {
                this._appendMessage(block, message, messageClass);
            }
        },

        removeMsg: function (block, messageClass) {
            block.find('.' + messageClass.replace(/ /g, '.')).remove();
        },

        _appendMessage: function (block, message, messageClass) {
            var currentMessage = null;
            var messageSection = block.find("." + messageClass.replace(/ /g, '.'));
            if (!messageSection.length) {
                block.prepend($('<div></div>', {'class': messageClass}));
                currentMessage = block.children().first();
            } else {
                currentMessage = messageSection.first();
            }

            currentMessage.append($('<div>' + message + '</div>'));
        }
    };

    window.emailCallback = function () {
        $("#email_modal").modal('openModal');
        authenticationPopup.modalWindow = null;
    };

});
