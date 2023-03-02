define([
    'jquery',
    'Magento_Ui/js/modal/modal',
    'mage/url'
], function ($, modal, urlBuilder) {
    "use strict";

    var wpWishlistAddTo = {
        getWishlistsUrl: null,
        customerWishlists: null,
        stopEventPropagation: false,
        ajaxWishlist: false,
        wishlistElm: null
    };

    return {
        initMultiWishlist: function(params) {
            wpWishlistAddTo.getWishlistsUrl = params.getWishlistsUrl;
            wpWishlistAddTo.ajaxWishlist = params.ajaxWishlist;

            var that = this;
            var modalOptions = {
                type: 'popup',
                modalClass: 'wishlist-add-popup-modal',
                responsive: true,
                innerScroll: true,
                buttons: [
                    {
                        text: $.mage.__('Add'),
                        class: 'add-to-wishlist-button',
                        click: function() {
                            var params = wpWishlistAddTo.wishlistElm.data('post');
                            params.data.wishlist_id = $('.wp-wishlist-selector').val();
                            wpWishlistAddTo.wishlistElm.data('post', params).trigger('click');
                        }
                    }
                ]
            };

            var wpWishlistPopup = modal(modalOptions, $('.multiple-wishlists-selector-container'));

            if (wpWishlistAddTo.customerWishlists == null) {
                $.ajax({
                    url: wpWishlistAddTo.getWishlistsUrl,
                    method: 'GET',
                    cache: false,
                    global: false,
                    data: {},
                    success: function (response) {
                        if (response.result) {
                            wpWishlistAddTo.customerWishlists = response.wishlists;
                            $.each(wpWishlistAddTo.customerWishlists, function (index, item) {
                                $('.wp-wishlist-selector').append($('<option>', {
                                    value: item.id,
                                    text : item.name
                                }));
                            });
                            $('.wp-wishlist-selector').append($('<option>', {
                                value: -1,
                                text : $.mage.__('Create new wishlist')
                            }));
                        } else {
                            wpWishlistAddTo.stopEventPropagation = true;
                        }
                    }
                });
            }

            $('body').on('change', '.wp-wishlist-selector', function() {
                if ($(this).val() == -1) {
                    $('.add-new-wishlist-container').show();
                    $('.wishlist-add-popup-modal .modal-footer button').attr('DISABLED', 'DISABLED');
                } else {
                    $('.add-new-wishlist-container').hide();
                    $('.wishlist-add-popup-modal .modal-footer button').attr('DISABLED', false);
                }
            });

            $('body').on('click', 'a.action.towishlist, button.action.towishlist', function() {
                if (!wpWishlistAddTo.stopEventPropagation) {
                    var params = $(this).data('post');
                    if (params.data.wishlist_id) {
                        if (wpWishlistAddTo.ajaxWishlist) {
                            //if from cart move to wishlist, no ajax
                            if ($(this).hasClass('action-towishlist')) {
                                wpWishlistPopup.closeModal();
                                return true;
                            }
                            that._addtoAjax($(this));
                            params.data.wishlist_id = null;
                            $(this).data('post', params);
                            wpWishlistPopup.closeModal();
                            return false;
                        } else {
                            return true;
                        }
                    }

                    wpWishlistAddTo.wishlistElm = $(this);
                    wpWishlistPopup.openModal();
                    $('#addnewwishlist').attr('disabled',false);
                    return false;
                }
            });
        },
        initAjaxWishlist :function(params) {
            wpWishlistAddTo.ajaxWishlist = params.ajaxWishlist;
            var that = this;
            if (wpWishlistAddTo.ajaxWishlist) {
                $('body').on('click', 'a.action.towishlist, button.action.towishlist', function() {
                    //if from cart move to wishlist, no ajax
                    if ($(this).hasClass('action-towishlist')) {
                        return true;
                    }
                    that._addtoAjax($(this));
                    return false;
                });
            }
        },
        submitWishlistCreation: function(form) {
            if (form.valid()) {
                var url = form.attr('action');
                $('#addnewwishlist').attr('disabled','DISABLED');
                $.ajax({
                    url: url,
                    method: 'POST',
                    cache: false,
                    global: false,
                    data: form.serialize(),
                    success: function (response) {
                        if (response.reload) {
                            window.location.reload();
                        } else if (response.result) {
                            $('.wp-wishlist-selector').prepend($('<option>', {
                                value: response.wishlist_id,
                                text : $('#wishlist-new-name').val()
                            }));
                            $('.wp-wishlist-selector').val(response.wishlist_id);
                            $('.add-new-wishlist-container').hide();
                            $('#addnewwishlist').attr('disabled',false);
                            $('.wishlist-add-popup-modal .modal-footer button').attr('disabled', false);
                            $('#wishlist-new-name').val('');
                            $('.add-to-wishlist-button').trigger('click');
                        } else {
                            form.find('.wp-errors').html(response.msg).show();
                        }
                    }
                });
            }
        },
        showOverlay: function() {
            $('body').trigger('processStart');
        },
        removeOverlay: function() {
            $('body').trigger('processStop');
        },
        _addtoAjax: function(element) {
            var that = this;

            var formKey = $('input[name="form_key"]').val();
            var params = element.data('post');
            params.data.ajax = 1;

            if (formKey) {
                params.data['form_key'] = formKey;
            }

            that.showOverlay();

            $.ajax({
                url: params.action,
                method: 'POST',
                data: params.data,
                success: function (response) {
                    if(typeof response !='object' || response.result != true)
                    {
                        var url = urlBuilder.build("wishlist");
                        window.location.href = url;
                    }
                    that.removeOverlay();
                }
            });
        }
    };

});
