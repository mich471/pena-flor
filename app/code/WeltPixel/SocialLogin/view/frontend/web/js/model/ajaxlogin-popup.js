/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'Magento_Ui/js/modal/modal'
], function ($, modal) {
    'use strict';

    return {
        modalWindow: null,

        /**
         * Create popUp window for provided element
         *
         * @param {HTMLElement} element
         */
        createPopUp: function (element) {
            var popupStyle = window.popupStyle;
            var popupClass = popupStyle == 'slide' ? 'popup-authentication slide-popup' : 'popup-authentication';
            var options = {
                'type': popupStyle,
                'modalClass': popupClass,
                'focus': '[name=username]',
                'responsive': true,
                'innerScroll': true,
                'trigger': '.sl-ajax-login, .towishlist, .mailto, .action-auth-toggle',
                'buttons': []
            };

            this.modalWindow = element;
            modal(options, $(this.modalWindow));


            var that = this;
            $(document).on('click', '.sl-ajax-login .towishlist, .mailto', function () {
                that.showModal();
                return false;
            });

        },

        /** Show login popup window */
        showModal: function () {
            $(this.modalWindow).modal('openModal').trigger('contentUpdated');
        }
    };
});
