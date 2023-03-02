/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
var addToCartButtonTextDefaultHack = "";
define([
    'jquery',
    'mage/translate',
    'jquery/ui'
], function ($, $t) {
    "use strict";
    var paso1 = false;
    var prodName = "";
    var itemData = false;
    if ($(".catalog-product-view").length) {
        addToCartButtonTextDefaultHack = "Comprar";
    } else {
        addToCartButtonTextDefaultHack = $.mage.__('Add to Cart');
    }
    $.widget('mage.catalogAddToCart', {
        options: {
            processStart: null,
            processStop: null,
            bindSubmit: true,
            minicartSelector: '[data-block="minicart"]',
            messagesSelector: '[data-placeholder="messages"]',
            productStatusSelector: '.stock.available',
            addToCartButtonSelector: '.action.tocart',
            addToCartButtonDisabledClass: 'disabled',
            addToCartButtonTextWhileAdding: $t('Agregando...'),
            addToCartButtonTextAdded: $t('Agregado'),
            addToCartButtonTextDefault: addToCartButtonTextDefaultHack,
            namespace: 'ajaxcartpro'
        },

        _create: function () {
            if (this.options.bindSubmit) {
                this._bindSubmit();
            }
        },

        _bindSubmit: function () {
            var self = this;
            this.element.on('submit', function (e) {
                e.preventDefault();
                self.submitForm($(this));
            });
        },

        isLoaderEnabled: function () {
            return this.options.processStart && this.options.processStop;
        },

        submitForm: function (form) {
            var self = this;
            if (form.has('input[type="file"]').length && form.find('input[type="file"]').val() !== '') {
                self.element.off('submit');
                form.submit();
            } else {
                self.ajaxSubmit(form);
            }
        },
        getItemData: function (form) {
            var unindexedArray = form.serializeArray();
            var indexedArray = {};
            $.map(unindexedArray, function (n, i) {
                indexedArray[n['name']] = n['value'];
            });
            return indexedArray;
        },
        modifyUrl: function (url) {
            var self = this;
            var terms = ['checkout'];
            $.each(terms, function (key, val) {
                url = url.replace(val, self.options.namespace);
            });
            return url;
        },

        imageAnimation: function (form) {
            $('#footer-mini-cart').slideDown(300, 'linear', function () {
                $('#footer-cart-trigger').addClass('active');
                if (form.parents('.product-item').length > 0) {
                    var $parent = form.parents('.product-item').first();
                    if ($parent.find('.product-image-photo').length > 0) {
                        var src = $parent.find('.product-image-photo').first().attr('src');
                        var width = $parent.find('.product-image-photo').first().width();
                    } else {
                        var src = $parent.find('.product-image').first().attr('src');
                        var width = $parent.find('.product-image').first().width();
                    }
                } else {
                    var $parent = $('.fotorama__stage__shaft').first();
                    var $fotoramaImg = $parent.find('.fotorama__img').first();
                    var src = $fotoramaImg.attr('src');
                    var width = $fotoramaImg.width();
                }
                var $img = $('<img class="adding-product-img" style="display:none;" />'); //$('#adding-product-img');
                $('body').append($img);
                var imgTop = $parent.offset().top;
                var imgLeft = $parent.offset().left;
                $img.attr('src', src);
                $img.css({
                    'opacity': 1,
                    'width': width,
                    'max-width': '300px',
                    'position': 'absolute',
                    'top': imgTop + 'px',
                    'left': imgLeft + 'px',
                    'z-index': 1000,
                });
                var $cart = $('.footer-mini-cart .cart-icon').first();
                $cart.removeClass('tada');
                var productId = form.find('input[name="product"]').val();
                imgTop = $cart.offset().top;
                imgLeft = $cart.offset().left + ($cart.width() - 20) / 2;
                $img.animate({
                    'opacity': 0,
                    'top': imgTop + 'px',
                    'left': imgLeft + 'px',
                    'width': '20px',
                }, 1500, 'linear', function () {
                    $img.replaceWith('');
                    $cart.addClass('animated tada');
                });
            });
        },

        ajaxSubmit: function (form) {

            var self = this;
            $(self.options.minicartSelector).trigger('contentLoading');
            self.disableAddToCartButton(form);


            var ajaxUrl = form.attr('action');
            ajaxUrl = self.modifyUrl(ajaxUrl);
            if ($('body').hasClass('cdz-qs-view')) {
                if (form.attr('id') == 'product_addtocart_form') {
                    self.imageAnimation(form);
                } else {
                    return;
                }
            } else {
                self.imageAnimation(form);
            }
            itemData = self.getItemData(form);
            $.ajax({
                url: ajaxUrl,
                data: form.serialize(),
                type: 'post',
                dataType: 'json',
                beforeSend: function () {
                    if (self.isLoaderEnabled()) {
                        $('body').trigger(self.options.processStart);
                    }
                },
                success: function (res) {
                    if (self.isLoaderEnabled()) {
                        $('body').trigger(self.options.processStop);
                    }

                    if (res.backUrl) {
                        window.location = res.backUrl;
                        return;
                    }
                    if (res.messages) {
                        $(self.options.messagesSelector).html(res.messages);
                    }
                    if (res.minicart) {
                        $(self.options.minicartSelector).replaceWith(res.minicart);
                        $(self.options.minicartSelector).trigger('contentUpdated');
                    }
                    if (res.product && res.product.statusText) {
                        $(self.options.productStatusSelector)
                            .removeClass('available')
                            .addClass('unavailable')
                            .find('span')
                            .html(res.product.statusText);
                    }
                    if (res.product) {
                        if (window.addedItem) window.addedItem(res.product);
                        if (window.crosssell) window.crosssell(res.crosssell);
                    }
                    if (res.cart) {
                        if (window.ajaxcart) window.ajaxcart(res.cart);
                        res.cart.trigger = true;
                        if (window.cartSidebar) window.cartSidebar(res.cart);
                    }
                    self.enableAddToCartButton(form);

                    /*                    jQuery.each(dataLayer, function (i, v) {
                                            if (v.event == "PASO1") {
                                                paso1 = true;
                                                return false;
                                            } else {
                                                paso1 = false;
                                            }
                                        });*/

                    prodName = $.trim($(form).find(self.options.addToCartButtonSelector).closest(".product-item-info").find(".product-item-name").children().text());

                    /*                    if (paso1 == true) {
                                            if (dataLayer[1].producto !== undefined) {
                                                if (dataLayer[1].producto.indexOf(prodName) >= 0) {
                                                    return;
                                                } else {
                                                    dataLayer[1].producto = dataLayer[1].producto + " | " + prodName;
                                                }
                                            }
                                        } else {
                                            dataLayer.push({
                                                'event': 'PASO1',
                                                'producto': prodName,
                                            });
                                        }*/

                    /**
                     * Icommkt Tracking
                     * @see RM #31740
                     **/
                    var itemPrice = parseFloat($($(form).find(self.options.addToCartButtonSelector).closest(".product-item-info").find(".price")[0]).text().replace('$', '').replace(',', '.'));
                    var totalItemPrice = itemPrice * parseInt(itemData.qty);
                    window._imMktOptions = {
                        _setType: 'Lead',
                        _setDomain: 'www.news.mascotavineyards.com.ar',
                        _itemsQuantity: parseInt(itemData.qty),
                        _amount: totalItemPrice,
                        _extraInfo: 'Info Adicional',
                    };
                    (function () {
                        var icomMkt = document.createElement('script');
                        icomMkt.type = 'text/javascript';
                        icomMkt.async = true;
                        icomMkt.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'api.icommarketing.com/js/icomMkt_tracking_jquery.js?time=' + Number(new Date());
                        var s = document.getElementsByTagName('script')[0];
                        s.parentNode.insertBefore(icomMkt, s);
                    })();
                }
            });
        },

        disableAddToCartButton: function (form) {
            var addToCartButton = $(form).find(this.options.addToCartButtonSelector);
            addToCartButton.addClass(this.options.addToCartButtonDisabledClass);
            addToCartButton.attr('title', this.options.addToCartButtonTextWhileAdding);
            addToCartButton.find('span').text(this.options.addToCartButtonTextWhileAdding);
        },

        enableAddToCartButton: function (form) {
            var self = this,
                addToCartButton = $(form).find(this.options.addToCartButtonSelector);

            addToCartButton.find('span').text(this.options.addToCartButtonTextAdded);
            addToCartButton.attr('title', this.options.addToCartButtonTextAdded);

            setTimeout(function () {
                addToCartButton.removeClass(self.options.addToCartButtonDisabledClass);
                addToCartButton.find('span').text(self.options.addToCartButtonTextDefault);
                addToCartButton.attr('title', self.options.addToCartButtonTextDefault);
            }, 1000);
        }
    });

    return $.mage.catalogAddToCart;
});
