define([
    'jquery',
    'priceUtils'
], function ($, priceUtils) {
    'use strict';

    return function (widget) {

        $.widget('mage.SwatchRenderer', widget, {
            _create: function () {
                this._super();
                var options = this.options,
                productData = this._determineProductData(),
                $main = productData.isInProductView ?
                        this.element.parents('.column.main') :
                        this.element.parents('.product-item-info');


                var lazyLoadActivated = $main.find('.product-image-photo').attr('data-original');
                if (lazyLoadActivated && !productData.isInProductView) {
                    options.mediaGalleryInitial = [{
                        'img': lazyLoadActivated
                    }];
                }
            },

            _setPreSelectedGallery: function () {
                this._super();
                if ($('body').hasClass('catalog-product-view')) {
                    var swatchPreselectLimit = 0,
                        applySwatchPreselect = false;
                    if ($('body').hasClass('wp-swatch-onlyone')) {
                        swatchPreselectLimit = 1;
                        applySwatchPreselect = true;
                    }
                    if ($('body').hasClass('wp-swatch-first')) {
                        swatchPreselectLimit = 'all';
                        applySwatchPreselect = true;
                    }
                    if (applySwatchPreselect) {
                        $(this.element).find('.swatch-attribute').each(function () {
                            var displayedSwatches = $(this).find('.swatch-option').length;
                            if (displayedSwatches == swatchPreselectLimit || swatchPreselectLimit == 'all') {
                                $(this).find('.swatch-option').first().trigger('click');
                            }
                        });
                    }
                }
            },


            _UpdatePrice: function () {
                this._super();
                var $widget = this;
                $widget._UpdateSalePrice();
            },

            _UpdateSalePrice: function () {
                var $widget = this,
                    wpSalesMsgWrapperSelector = '.wp-sales-off-msg',
                    wpSalesDiscountPercentSelector = '#wp-discount-percent',
                    wpSalesDiscountValueSelector = '#wp-discount-value',
                    result = $widget._getNewPrices();

                if ($(wpSalesMsgWrapperSelector).length) {
                    if (typeof result != 'undefined' && result.oldPrice.amount !== result.finalPrice.amount) {
                        var discountPercent =  100 - Math.round((result.finalPrice.amount * 100) / result.oldPrice.amount);
                        var discountValue =  priceUtils.formatPrice(result.oldPrice.amount -  result.finalPrice.amount);
                        $(wpSalesDiscountPercentSelector).html(discountPercent);
                        $(wpSalesDiscountValueSelector).html(discountValue);
                        $(wpSalesMsgWrapperSelector).show();
                    } else {
                        $(wpSalesMsgWrapperSelector).hide();
                    }
                }
            }
        });

        return $.mage.SwatchRenderer;
    }
});
