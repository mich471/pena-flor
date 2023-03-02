define(
    [
        'jquery',
        'Magento_Ui/js/form/form',
        'Magento_Customer/js/action/login',
        'Magento_Customer/js/model/customer',
        'mage/validation',
        'Magento_Checkout/js/model/authentication-messages',
        'Magento_Checkout/js/model/full-screen-loader'
    ],
    function($, Component, loginAction, customer, validation, messageContainer, fullScreenLoader) {
        'use strict';
        var checkoutConfig = window.checkoutConfig;

        return Component.extend({
            isGuestCheckoutAllowed: checkoutConfig.isGuestCheckoutAllowed,
            isCustomerLoginRequired: checkoutConfig.isCustomerLoginRequired,
            registerUrl: checkoutConfig.registerUrl,
            forgotPasswordUrl: checkoutConfig.forgotPasswordUrl,
            autocomplete: checkoutConfig.autocomplete,
            defaults: {
                template: 'WeltPixel_SocialLogin/checkout/authentication'
            },

            socialloginButtons: window.socialloginButtons,

            /** Is login form enabled for current customer */
            isActive: function() {
                return !customer.isLoggedIn();
            },

            /** Is SocialLogin extension enabled */
            isSlEnabled: function() {
                return window.isEnabled;
            },

            isPopup: function() {
                var popupStyle = window.popupStyle;
                return popupStyle == 'slide' ? false : true;
            },

            /** Provide login action */
            login: function(loginForm) {
                var loginData = {},
                    formElement = $(event.currentTarget),
                    formDataArray = formElement.serializeArray();

                formDataArray.forEach(function (entry) {
                    loginData[entry.name] = entry.value;
                });

                if (formElement.validation() &&
                    formElement.validation('isValid')
                ) {
                    fullScreenLoader.startLoader();
                    loginAction(loginData, checkoutConfig.checkoutUrl, undefined, messageContainer).always(function() {
                        fullScreenLoader.stopLoader();
                    });
                }
            }
        });
    }
);
