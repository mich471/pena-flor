define(
    [
        'jquery',
        'ko',
        'Magento_Checkout/js/view/shipping',
        'Magento_Checkout/js/action/select-shipping-method',
        'Magento_Checkout/js/checkout-data',
        'Lc_ShippingCalendar/js/view/shipping/calendar',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/action/select-shipping-address',
        'Magento_Checkout/js/model/address-converter',
        'Magento_Customer/js/model/customer',
        'Magento_Checkout/js/action/set-shipping-information',
        'Magento_Checkout/js/model/step-navigator'
    ],
    function ($,
              ko,
              Component,
              selectShippingMethodAction,
              checkoutData,
              shippingCalendar,
              quote,
              selectShippingAddress,
              addressConverter,
              customer,
              setShippingInformationAction,
              stepNavigator
    ) {
        'use strict';
        return Component.extend({
            defaults: {
                template: 'Lc_ShippingCalendar/shipping'
            },
            calendarDates: ko.observable(),
            initialize: function () {
                var self = this;
                this._super();

                if ($(".shippingcalendar") !== undefined) {
                    $(".shippingcalendar").hide();
                    $(".shippingcalendar-title").hide();
                }
                quote.shippingMethod.subscribe(function (shippingMethod) {
                    if (shippingMethod) {
                        if (shippingMethod.extension_attributes !== undefined) {
                            if (shippingMethod.extension_attributes.is_shipping_calendar === 1) {
                                $(".shippingcalendar-title").hide();
                                $(".calendar_" + shippingMethod.carrier_code + '_' + shippingMethod.method_code).show();
                                return;
                            }
                        }
                    }
                    return this;
                });
                return this;
            },
            /**
             * @return {Boolean}
             */
            validateShippingInformation: function () {
                var shippingAddress,
                    addressData,
                    loginFormSelector = 'form[data-role=email-with-possible-login]',
                    emailValidationResult = customer.isLoggedIn();

                if (!quote.shippingMethod()) {
                    this.errorValidationMessage('Please specify a shipping method.');

                    return false;
                }

                if (!customer.isLoggedIn()) {
                    $(loginFormSelector).validation();
                    emailValidationResult = Boolean($(loginFormSelector + ' input[name=username]').valid());
                }

                if (this.isFormInline) {
                    this.source.set('params.invalid', false);
                    this.source.trigger('shippingAddress.data.validate');

                    if (this.source.get('shippingAddress.custom_attributes')) {
                        this.source.trigger('shippingAddress.custom_attributes.data.validate');
                    }

                    if (this.source.get('params.invalid') ||
                        !quote.shippingMethod().method_code ||
                        !quote.shippingMethod().carrier_code ||
                        !emailValidationResult
                    ) {
                        return false;
                    }

                    shippingAddress = quote.shippingAddress();
                    addressData = addressConverter.formAddressDataToQuoteAddress(
                        this.source.get('shippingAddress')
                    );

                    //Copy form data to quote shipping address object
                    for (var field in addressData) {

                        if (addressData.hasOwnProperty(field) &&
                            shippingAddress.hasOwnProperty(field) &&
                            typeof addressData[field] != 'function' &&
                            _.isEqual(shippingAddress[field], addressData[field])
                        ) {
                            shippingAddress[field] = addressData[field];
                        } else if (typeof addressData[field] != 'function' &&
                            !_.isEqual(shippingAddress[field], addressData[field])) {
                            shippingAddress = addressData;
                            break;
                        }
                    }

                    if (customer.isLoggedIn()) {
                        shippingAddress.save_in_address_book = 1;
                    }
                    selectShippingAddress(shippingAddress);
                }

                if (!emailValidationResult) {
                    $(loginFormSelector + ' input[name=username]').focus();

                    return false;
                }

                if (quote.shippingMethod().carrier_code === 'lcscld') {
                    var date = $('input[name=calendar_date]:checked').val();
                    if (date === undefined) {
                        quote.shippingMethod().quota_date = 0;
                        quote.shippingMethod().quota_id = 0;
                        return false;
                    }
                    date = date.split('_');
                    quote.shippingMethod().quota_date = date[0];
                    quote.shippingMethod().quota_id = date[1];
                }
                return true;
            },
            /**
             * Set shipping information handler
             */
            setShippingInformation: function () {
                if (this.validateShippingInformation()) {
                    setShippingInformationAction().done(
                        function () {
                            stepNavigator.next();
                        }
                    );
                }
            },
            /**
             * @param {Object} shippingMethod
             * @return {Boolean}
             */
            selectShippingMethod: function (shippingMethod) {
                $('input[name=calendar_date]').attr('checked', false);
                if (shippingMethod.extension_attributes !== undefined) {
                    if (shippingMethod.extension_attributes.is_shipping_calendar === 1) {
                        $(".shippingcalendar-title").show();
                        $(".calendar_" + shippingMethod.carrier_code + '_' + shippingMethod.method_code).show();
                        selectShippingMethodAction(shippingMethod);
                        checkoutData.setSelectedShippingRate(shippingMethod.carrier_code + '_' + shippingMethod.method_code);
                        return true;
                    }
                }
                $(".shippingcalendar").hide();
                $(".shippingcalendar-title").hide();
                selectShippingMethodAction(shippingMethod);
                checkoutData.setSelectedShippingRate(shippingMethod.carrier_code + '_' + shippingMethod.method_code);

                if ($('.retiro-sucursal-row').length) {
                    $('.retiro-sucursal-row').hide();
                    $('#shipping-method-buttons-container .action.continue').show();
                    $("#shipping-method-buttons-container .action.continue").removeAttr("disabled");
                }

                if (shippingMethod.carrier_code == 'andreanisucursal' && shippingMethod.method_code == 'sucursal') {
                    $('.retiro-sucursal-row').show();
                    $('#andreanisucursal-provincia').val('');
                    $('#andreanisucursal-localidad').val('').hide();
                    $('#andreanisucursal-sucursal').val('').hide();
                    $("#shipping-method-buttons-container .action.continue").attr("disabled", "disabled");
                }

                return true;
            },
        });
    }
);