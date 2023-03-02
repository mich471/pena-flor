/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*jshint browser:true jquery:true*/
define([
    "jquery",
    "jquery/ui"
], function($){
    "use strict";
    
    $.widget('mage.shoppingCart', {
        _create: function() {
            $(this.options.emptyCartButton).on('click', $.proxy(function() {
                $(this.options.emptyCartButton).attr('name', 'update_cart_action_temp');
                $(this.options.updateCartActionContainer)
                    .attr('name', 'update_cart_action').attr('value', 'empty_cart');
            }, this));
            var items = $.find("[data-role='cart-item-qty']");
            for (var i = 0; i < items.length; i++) {
                $(items[i]).on('keypress', $.proxy(function(event) {
                    var keyCode = (event.keyCode ? event.keyCode : event.which);
                    if(keyCode == 13) {
                        $(this.options.emptyCartButton).attr('name', 'update_cart_action_temp');
                        $(this.options.updateCartActionContainer)
                            .attr('name', 'update_cart_action').attr('value', 'update_qty');

                    }
                }, this));
            }
            $(this.options.continueShoppingButton).on('click', $.proxy(function() {
                location.href = this.options.continueShoppingUrl;
            }, this));

            $(".plus-cart").click(function (event) {
                event.preventDefault();
                var itemId = $(this).attr('data-cart-item-id');
                var oldValue = parseFloat($('#cart-' + itemId + '-qty').val());
                var increment = parseFloat($(this).attr('data-cart-item-increment'));
                $('#cart-' + itemId + '-qty').val(oldValue + increment);
                $('#cart-' + itemId + '-qty_visible').val(oldValue + increment);
            });

            $(".sub-cart").click(function (event) {
                event.preventDefault();
                var itemId = $(this).attr('data-cart-item-id');
                var oldValue = parseFloat($('#cart-' + itemId + '-qty').val());
                var defaultValue = parseFloat($(this).attr('data-cart-item-increment'));
                if (oldValue > defaultValue) {
                    var newVal = oldValue - defaultValue;
                } else {
                    newVal = defaultValue;
                }
                $('#cart-' + itemId + '-qty').val(newVal);
                $('#cart-' + itemId + '-qty_visible').val(newVal);
            });
        }
    });

    return $.mage.shoppingCart;
});