define([
    'uiComponent',
    'jquery',
    'Magento_Ui/js/modal/modal',
], function (Component, $, modal) {
    'use strict';
    return Component.extend({

        initialize: function () {

            this._super();
            const modalSelector = '[data-role="newlogin-modal"]';
            //const openNewloginModal =
            const options = {
                type: 'popup',
                responsive: true,
                innerScroll: false,
                title: false,
                buttons: false,
                modalClass: 'newlogin-modal--container'
            };

            const popup = modal(options, $(modalSelector));

            if (this.modalDelay) {
                const hasSeenPopup = sessionStorage.getItem('hasSeenPopup');
                setTimeout(function() {
                    console.log("hasSeenPopup", hasSeenPopup)
                    if (hasSeenPopup) return;
                    sessionStorage.setItem('hasSeenPopup', 'true');
                    $(modalSelector).modal('openModal');
                }, this.modalDelay);
            }
        },
    });
});
