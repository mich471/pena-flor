/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*jshint browser:true jquery:true*/
/*global alert*/
define(
    [
        'jquery',
        'ko',
        'Magento_Ui/js/form/form',
        'Magento_Customer/js/action/login',
        'Magento_Customer/js/customer-data',
        'Magento_Customer/js/model/authentication-popup',
        'mage/translate',
        'mage/url',
        'Magento_Ui/js/modal/alert',
        'mage/validation'
    ],
    function ($, ko, Component, loginAction, customerData, authenticationPopup, $t, url, alert) {
        'use strict';

        return Component.extend({
            registerUrl: window.authenticationPopup.customerRegisterUrl + 'refer/checkout',
            forgotPasswordUrl: window.authenticationPopup.customerForgotPasswordUrl,
            autocomplete: window.checkout.autocomplete,
            modalWindow: null,
            isLoading: ko.observable(false),

            defaults: {
                template: 'Magento_Customer/authentication-popup'
            },

            /**
             * Init
             */
            initialize: function () {
                var self = this;
                this._super();
                url.setBaseUrl(window.authenticationPopup.baseUrl);
                loginAction.registerLoginCallback(function () {
                    self.isLoading(false);
                });
            },

            /** Init popup login window */
            setModalElement: function (element) {
                if (authenticationPopup.modalWindow == null) {
                    authenticationPopup.createPopUp(element);
                }
            },

            /** Is login form enabled for current customer */
            isActive: function () {
                var customer = customerData.get('customer');

                return customer() == false;
            },

            /** Show login popup window */
            showModal: function () {
                if (this.modalWindow) {
                    $(this.modalWindow).modal('openModal');
                } else {
                    alert({
                        content: $t('Guest checkout is disabled.')
                    });
                }
            },

            /** Provide login action */
            login: function (loginForm) {
                var loginData = {},
                    formDataArray = $(loginForm).serializeArray(),
                    refer = null,
                    self = this;
                formDataArray.forEach(function (entry) {
                    loginData[entry.name] = entry.value;
                });

                if ($(loginForm).find('input[name="context"]').length) {
                    switch ( $(loginForm).find('input[name="context"]').val() ) {
                        case 'checkout':
                            refer = window.checkout.checkoutUrl;
                            break;
                        default:
                            refer = null;
                            break;
                    }
                }

                if ($(loginForm).validation() &&
                    $(loginForm).validation('isValid')
                ) {
                    this.isLoading(true);
                    loginAction(loginData, refer, false);
                }
            }
        });
    }
);