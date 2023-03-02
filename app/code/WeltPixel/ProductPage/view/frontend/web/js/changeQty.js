define([
    'ko',
    'uiComponent',
    'jquery'
], function (ko, Component, $) {
    'use strict';
    return Component.extend({
        initialize: function () {
            this._super();
            this.qty = ko.observable($(this.qtyInput).val() * 1);
            this.increment = parseInt(jQuery(".box-tocart #qty").attr("min"));
        },
        decreaseQty: function() {
            var newQty = this.qty() - this.increment;
            if (newQty < this.increment)
            {
                newQty = this.increment;
            }
            this.qty(newQty);
        },
        increaseQty: function() {
            var newQty = this.qty() + this.increment;
            this.qty(newQty);
        }
    });

});
