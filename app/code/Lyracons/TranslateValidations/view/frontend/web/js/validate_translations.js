define([
    'jquery',
    'jquery/ui',
    'jquery/validate',
    'mage/translate'
], function ($) {
    /**
     * Reload all messages
     */
    $.validator.reloadMessages = function () {
        for (let i in $.validator.messages) {
            if (typeof $.validator.messages[i] === 'string') {
                $.validator.messages[i] = $.mage.__($.validator.messages[i]);
            }
        }
    }
});