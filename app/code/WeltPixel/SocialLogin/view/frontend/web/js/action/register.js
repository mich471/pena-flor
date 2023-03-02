define([
    'jquery',
    'mage/storage',
    'Magento_Ui/js/model/messageList',
    'Magento_Customer/js/customer-data'
], function ($, storage, globalMessageList, customerData) {
    'use strict';

    var callbacks = [],

        /**
         * @param {Object} customerData
         * @param {String} redirectUrl
         * @param {*} isGlobal
         * @param {Object} messageContainer
         */
        action = function (customerData, redirectUrl, isGlobal, messageContainer) {
            messageContainer = messageContainer || globalMessageList;

            return storage.post(
                'sociallogin/ajax/register',
                JSON.stringify(customerData),
                isGlobal
            ).done(function (response) {
                if (response.errors) {
                    messageContainer.addErrorMessage({
                        'message':response.message
                    });
                    callbacks.forEach(function (callback) {
                        callback(customerData);
                    });
                } else {
                    callbacks.forEach(function (callback) {
                        callback(customerData);
                    });
                    //customerData.invalidate(['customer']);

                    if (redirectUrl) {
                        window.location.href = redirectUrl;
                    } else if (response.redirectUrl) {
                        window.location.href = response.redirectUrl;
                    } else {
                        location.reload();
                    }
                }
            }).fail(function () {
                messageContainer.addErrorMessage({
                    'message': 'Could not create account. Please try again later'
                });
                callbacks.forEach(function (callback) {
                    callback(customerData);
                });
            });
        };

    /**
     * @param {Function} callback
     */
    action.registerRegisterCallback = function (callback) {
        callbacks.push(callback);
    };

    return action;
});