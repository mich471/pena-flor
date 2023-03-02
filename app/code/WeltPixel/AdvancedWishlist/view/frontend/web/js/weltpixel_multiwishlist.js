define([
    'jquery',
    'Magento_Ui/js/modal/modal',
    'Magento_Ui/js/modal/confirm',
    ], function ($, modal, confirmation) {
    "use strict";

    var wpWishlistNameElm = null;
    var wishlistPrivacyElm = null;
    var wpWishlistData = null;
    var wpWishlistPopup = null;
    var wpErrorsContainer = null;
    var wpDeleteWishlistBind = false;
    var wpWishlistDeleteUrl = '';
    var wpWishlistModalPopup = $('#wishlist-popup-modal');

    return {
        init: function(params) {
            wpErrorsContainer = params.errorsContainer;
            wpWishlistDeleteUrl = params.deleteUrl;
        },
        editWishlist: function(params) {
            var wishlistId = params.wishlistId;
            wpWishlistNameElm = params.wishlistNameElm;
            wishlistPrivacyElm = params.wishlistPrivacyElm;
            wpWishlistData = params.wishlistData;

            var editModalOptions = {
                type: 'popup',
                modalClass: 'wishlist-popup-modal',
                responsive: true,
                innerScroll: true,
                buttons: []
            };

            wpWishlistPopup = modal(editModalOptions, wpWishlistModalPopup);
            wpErrorsContainer.html('');
            wpWishlistPopup.openModal();
            $('#wishlist-popup-modal .modal-title').html($.mage.__('Edit Wishlist'));
            $('#wishlist-name').val(wpWishlistNameElm.html());
            $('#wishlist-id').val(wishlistId);
            $('#wishlist-disable-share').val(wpWishlistData.attr('data-wishlist-disable-share'));
            $('#wishlist-disable-public').val(wpWishlistData.attr('data-wishlist-disable-public'));
            $('#wishlist-disable-pricealert').val(wpWishlistData.attr('data-wishlist-disable-pricealert'));
            $('#deletewishlist').show();
            $('#savewishlist').attr('disabled', false);
            if (!wpDeleteWishlistBind) {
                $('#deletewishlist').bind('click', function () {

                    confirmation({
                        title: $.mage.__('Delete Wishlist'),
                        content: $.mage.__('Are you sure you want to delete'),
                        actions: {
                            confirm: function(){
                                $.ajax({
                                    url: wpWishlistDeleteUrl,
                                    method: 'POST',
                                    cache: false,
                                    global: false,
                                    data: {wishlistId: $('#wishlist-id').val() },
                                    success: function (response) {
                                        if (response.result) {
                                            wpWishlistPopup.closeModal();
                                            wpWishlistNameElm.parentsUntil('.multiple-wishlist-element').remove();
                                        } else {
                                            wpWishlistModalPopup.find('.wp-errors').html(response.msg).show();
                                        }
                                    }
                                });
                            }
                        }
                    });
                });
                wpDeleteWishlistBind = true;
            }
        },
        addWishlist: function() {
            var addModalOptions = {
                type: 'popup',
                modalClass: 'wishlist-popup-modal',
                responsive: true,
                innerScroll: true,
                buttons: []
            };

            wpWishlistPopup = modal(addModalOptions, wpWishlistModalPopup);
            wpErrorsContainer.html('');
            wpWishlistPopup.openModal();
            $('#wishlist-popup-modal .modal-title').html($.mage.__('Add Wishlist'));
            $('#wishlist-name').val('');
            $('#wishlist-id').val('');
            $('#deletewishlist').hide();
            $('#savewishlist').attr('disabled', false);
        },
        submitWishlist: function(form) {
            if (form.valid()) {
                var url = form.attr('action');
                $('#savewishlist').attr('disabled','DISABLED');
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
                            wpWishlistPopup.closeModal();
                            wpWishlistNameElm.html(form.find('[name="wishlist-name"]').val());
                            if (wishlistPrivacyElm) {
                                var privacyElmValue = 'Public';
                                if (form.find('[name="wishlist-disable-share"]').val() == 1 ||  form.find('[name="wishlist-disable-public"]').val() == 1) {
                                    privacyElmValue = 'Private';
                                    wishlistPrivacyElm.addClass('private');
                                } else {
                                    wishlistPrivacyElm.removeClass('private');
                                }
                                wishlistPrivacyElm.html(privacyElmValue);
                            }
                            wpWishlistData.attr('data-wishlist-disable-share', form.find('[name="wishlist-disable-share"]').val());
                            wpWishlistData.attr('data-wishlist-disable-public', form.find('[name="wishlist-disable-public"]').val());
                            wpWishlistData.attr('data-wishlist-disable-pricealert', form.find('[name="wishlist-disable-pricealert"]').val());
                        } else {
                            form.find('.wp-errors').html(response.msg).show();
                        }
                    }
                });
            }
        }
    };

});
