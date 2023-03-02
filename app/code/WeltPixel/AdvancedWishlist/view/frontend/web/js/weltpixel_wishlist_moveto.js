define([
    'jquery',
    'Magento_Ui/js/modal/modal',
    'mage/url'
], function ($, modal, urlBuilder) {
    "use strict";

    var wpWishlistMoveTo = {
        getWishlistsUrl: null,
        customerWishlists: null,
        stopEventPropagation: false,
        wishlistElm: null,
        currentWishlist: null
    };

    return {
        initMultiWishlistMoveTo: function(params) {
            wpWishlistMoveTo.getWishlistsUrl = params.getWishlistsUrl;
            wpWishlistMoveTo.currentWishlist = params.currentWishlist;

            var that = this;
            var modalOptions = {
                type: 'popup',
                modalClass: 'wishlist-moveto-popup-modal',
                responsive: true,
                innerScroll: true,
                buttons: [
                    {
                        text: $.mage.__('Move To'),
                        class: 'moveto-add-to-wishlist-button',
                        click: function() {
                            var params = wpWishlistMoveTo.wishlistElm.data('post');
                            params.data.wishlist_id = $('.wp-wishlist-moveto-selector').val();
                            wpWishlistMoveTo.wishlistElm.data('post', params).trigger('click');
                        }
                    }
                ]
            };

            var wpWishlistPopup = modal(modalOptions, $('.multiple-wishlists-moveto-selector-container'));

            if (wpWishlistMoveTo.customerWishlists == null) {
                $.ajax({
                    url: wpWishlistMoveTo.getWishlistsUrl,
                    method: 'GET',
                    cache: false,
                    global: false,
                    data: {},
                    success: function (response) {
                        if (response.result) {
                            wpWishlistMoveTo.customerWishlists = response.wishlists;
                            $.each(wpWishlistMoveTo.customerWishlists, function (index, item) {
                                if (item.id != wpWishlistMoveTo.currentWishlist) {
                                    $('.wp-wishlist-moveto-selector').append($('<option>', {
                                        value: item.id,
                                        text: item.name
                                    }));
                                }
                            });
                            $('.wp-wishlist-moveto-selector').append($('<option>', {
                                value: -1,
                                text : $.mage.__('Create new wishlist')
                            }));
                           $('.wp-wishlist-moveto-selector').trigger('change');
                        } else {
                            wpWishlistMoveTo.stopEventPropagation = true;
                        }
                    }
                });
            }

            $('body').on('change', '.wp-wishlist-moveto-selector', function() {
                if ($(this).val() == -1) {
                    $('.moveto-add-new-wishlist-container').show();
                    $('.wishlist-moveto-popup-modal .modal-footer button').attr('DISABLED', 'DISABLED');
                } else {
                    $('.moveto-add-new-wishlist-container').hide();
                    $('.wishlist-moveto-popup-modal .modal-footer button').attr('DISABLED', false);
                }
            });

            $('body').on('click', 'a.action.btn-moveitem', function() {
                if (!wpWishlistMoveTo.stopEventPropagation) {
                    var params = $(this).data('post');
                    if (params.data.wishlist_id) {
                        that._addtoAjax($(this));
                        params.data.wishlist_id = null;
                        $(this).data('post', params);
                        wpWishlistPopup.closeModal();
                        $('#addmovetonewwishlist').attr('disabled',false);
                        return false;
                    }

                    wpWishlistMoveTo.wishlistElm = $(this);
                    wpWishlistPopup.openModal();
                    return false;
                }
            });
        },
        submitWishlistCreation: function(form) {
            if (form.valid()) {
                var url = form.attr('action');
                $('#addmovetonewwishlist').attr('disabled','DISABLED');
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
                            $('.wp-wishlist-moveto-selector').prepend($('<option>', {
                                value: response.wishlist_id,
                                text : $('#wishlist-moveto-new-name').val()
                            }));
                            $('.wp-wishlist-moveto-selector').val(response.wishlist_id);
                            $('.moveto-add-new-wishlist-container').hide();
                            $('#addmovetonewwishlist').attr('disabled',false);
                            $('.wishlist-moveto-popup-modal .modal-footer button').attr('disabled', false);
                            $('#wishlist-moveto-new-name').val('');
                            $('.moveto-add-to-wishlist-button').trigger('click');
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
                    } else {
                        var itemId = params.data.item_id;
                        $('#item_' + itemId).remove();
                    }
                    that.removeOverlay();
                }
            });
        }
    };

});
