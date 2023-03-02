/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'jquery',
    'mage/mage',
    'Magento_Catalog/product/view/validation',
    'catalogAddToCart'
], function ($) {
    'use strict';

    $.widget('mage.productValidate', {
        options: {
            bindSubmit: false,
            radioCheckboxClosest: '.nested'
        },

        /**
         * Uses Magento's validation widget for the form object.
         * @private
         */
        _create: function () {
            var bindSubmit = this.options.bindSubmit;

            this.element.validation({
                radioCheckboxClosest: this.options.radioCheckboxClosest,

                /**
                 * Uses catalogAddToCart widget as submit handler.
                 * @param {Object} form
                 * @returns {Boolean}
                 */
                submitHandler: function (form) {
                    var jqForm = $(form).catalogAddToCart({
                        bindSubmit: bindSubmit
                    });

                    jqForm.catalogAddToCart('submitForm', jqForm);

                    return false;
                }
            });

            $(".plus-cart").click(function (event) {
                event.preventDefault();
                let inputQtyElement = $(`#qty`);
                let oldValue = parseFloat(inputQtyElement.val());
                let increment = parseFloat($(this).attr('data-increment'));
                inputQtyElement.val(oldValue + increment);
                $(`#qty_visible`).val(oldValue + increment);
            });

            $(".sub-cart").click(function (event) {
                event.preventDefault();
                let qtyElement = $(`#qty`);
                let oldValue = parseFloat(qtyElement.val());
                let defaultValue = parseFloat($(this).attr('data-increment'));
                let newVal = defaultValue;
                if (oldValue > defaultValue) {
                    newVal = oldValue - defaultValue;
                }
                qtyElement.val(newVal);
                $(`#qty_visible`).val(newVal);
            });
        }
    });

    return $.mage.productValidate;
});
