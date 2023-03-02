/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    //'BalloonGroup_AllowGuestCheckoutPopup/js/model/authentication-popup',
    'Magento_Customer/js/model/authentication-popup',
    'Magento_Customer/js/customer-data'
], function ($, authenticationPopup, customerData) {
    'use strict';

    return function (config, element) {
        $(element).click(function (event) {
            var customer = customerData.get('customer');

            event.preventDefault();

            // we show the popup only when the user isn't loged in
            if (!customer().firstname) {
                console.log("aca");

                const continueAsGuestButton = document.querySelector('.continue-as-guest');
                continueAsGuestButton.style.display = 'flex';
                authenticationPopup.showModal(true);

                return false;
            }
            $(element).attr('disabled', true);
            location.href = config.checkoutUrl;
        });

    };
});
