define([
    'jquery',
    'ko',
    'Magento_Ui/js/form/form',
    'WeltPixel_SocialLogin/js/action/login',
    'WeltPixel_SocialLogin/js/action/register',
    'Magento_Customer/js/customer-data',
    'WeltPixel_SocialLogin/js/model/ajaxlogin-popup',
    'mage/translate',
    'mage/url',
    'mage/validation'
], function ($, ko, Component, loginAction, registerAction, customerData, ajaxLogin, $t, url) {
    'use strict';

    return Component.extend({
        registerUrl: window.ajaxLogin.customerRegisterUrl,
        forgotPasswordUrl: window.ajaxLogin.customerForgotPasswordUrl,
        autocomplete: window.ajaxLogin.autocomplete,
        modalWindow: null,
        isLoading: ko.observable(false),
        logClicked: ko.observable(1),
        regClicked: ko.observable(0),
        defaults: {
            template: 'WeltPixel_SocialLogin/ajaxlogin-popup'
        },

        /**
         * Init
         */
        initialize: function () {
            var self = this;

            this._super();

            url.setBaseUrl(window.ajaxLogin.baseUrl);
            loginAction.registerLoginCallback(function () {
                self.isLoading(false);
            });
            registerAction.registerRegisterCallback(function () {
                self.isLoading(false);
            });

            $('#registration_section').hide();
        },

        /** Init popup login window */
        setAjaxModelElement: function (element) {
            if (ajaxLogin.modalWindow == null) {
                ajaxLogin.createPopUp(element);
            }
        },

        /** Is login form enabled for current customer */
        isActive: function () {
            var customer = customerData.get('customer');

            return customer() == false; //eslint-disable-line
        },

        /** Show login popup window */
        showModal: function () {
            if (this.modalWindow) {
                $(this.modalWindow).modal('openModal');
            }
        },

        /**
         * Provide login action
         *
         * @return {Boolean}
         */
        login: function (formUiElement, event) {
            var loginData = {},
                formElement = $(event.currentTarget),
                formDataArray = formElement.serializeArray();

            event.stopPropagation();
            event.preventDefault();

            formDataArray.forEach(function (entry) {
                loginData[entry.name] = entry.value;
            });

            if (formElement.validation() &&
                formElement.validation('isValid')
            ) {
                this.isLoading(true);
                loginAction(loginData);
            }

            return false;
        },

        register: function(formUiElement, event) {
            var registerData = {},
                formElement = $(event.currentTarget),
                formDataArray = formElement.serializeArray();

            event.stopPropagation();
            formDataArray.forEach(function (entry) {
                registerData[entry.name] = entry.value;
            });

            if (formElement.validation() &&
                formElement.validation('isValid')
            ) {
                this.isLoading(true);
                registerAction(registerData);
            }

            return false;
        },
        showRegister: function() {
            this.logClicked(0);
            this.regClicked(1);
            $('#login_section').hide();
            $('#registration_section').show();
        },
        showLogin: function() {
            this.regClicked(0);
            this.logClicked(1);
            $('#registration_section').hide();
            $('#login_section').show();

        }
    });
});
