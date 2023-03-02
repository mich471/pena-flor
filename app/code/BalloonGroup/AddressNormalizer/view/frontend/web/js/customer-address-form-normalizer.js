define([
    "jquery",
    'mage/translate'
], function ($, $t) {
    "use strict";

    // verify_route :  Const to verify if it was set by route and enables the address field.
    const VERIFY_ROUTE = false;

    const readOnly = (fields) => {
        $(fields).each(function (i, e) {
            $('.form-address-edit [name="' + e + '"]').attr('readonly', 'readonly');
            $('#' + e).attr('readonly', 'readonly');
        });
    }

    const disableReadonly = (fields) => {
        $(fields).each(function (i, e) {
            e = (e === "postcode") ? "zip" : e;
            let input = $('.form-address-edit [id="' + e + '"]');
            let value = input.val();
               /*  input = $('#' + e).val();

            if (typeof input === 'undefined' || self.VERIFY_ROUTE === true) {
                if (e === 'street_1' || e === 'street[0]') {
                    $('.form-address-edit [id="' + e + '"]').removeAttr('readonly');
                    $('#' + e).removeAttr('readonly');
                }
            } */
            if (!input.attr('readonly') && (typeof value !== 'undefined' || value !== '')) {
                input.attr('readonly', 'readonly');
            }
            if (typeof value === 'undefined' || value === '') {
                input.removeAttr('readonly');
            }
            
        });
    }

    return {
        options: {
        },
        _autocomplete: null,
        _create: function () {
            let self = this;
        },
        _init: function () {
            $('.form-address-edit [for="street_1"]').text("Calle")
            $('.form-address-edit .field-altura').insertAfter($('.form-address-edit .street'));

            let self = this;
            this.config = this.options;
            let jsSource = '//maps.googleapis.com/maps/api/js?key=' + self.config.normalizerGooglePlacesApiKey + '&libraries=places&region=AR&language=es';
            require([jsSource], function () {
                //readOnly(self.config.readOnlyFields);
                self._initAutocomplete(self.config);
            });
        },

        _initAutocomplete: function (conf) {
            $('.field.street').before($('#autocomplete'));

            if (conf && conf.readOnlyFields && conf.readOnlyFields.length) {
                readOnly(conf.readOnlyFields);
            }

            // Enable manual complete of fields once done searching with api.
            // If no address found by the api, client should still be able to complete an address
            $('#addressAutocomplete').focusout(function() {
                if ($(this).val()) {
                    disableReadonly(conf.readOnlyFields);
                }
            });

            // Create the autocomplete object, restricting the search predictions to
            // geographical location types.
            let options = {types: ['geocode']};

            if (conf && conf.allowedCountries && conf.allowedCountries.length) {
                options.componentRestrictions = { country: conf.allowedCountries }
            }

            this._autocomplete = new google.maps.places.Autocomplete(
                $('#addressAutocomplete')[0],
                options
            );

            // Avoid paying for data that you don't need by restricting the set of
            // place fields that are returned to just the address components.
            this._autocomplete.setFields(['address_component']);

            // When the user selects an address from the drop-down, populate the
            // address fields in the form.

            let norm = this;
            this._autocomplete.addListener('place_changed', function () {
                let place = norm._autocomplete.getPlace();
                if (place) {
                    norm._fillInAddress(place, conf);
                }
            });
        },

        _fillInAddress: function (place, conf) {
            //  Get the place details from the autocomplete object.
            if (!place) {
                return;
            }
            let componentForm = {
                street_number: 'short_name',
                route: 'short_name',
                locality: 'long_name',
                administrative_area_level_1: 'short_name',
                administrative_area_level_2: 'short_name',
                country: 'long_name',
                postal_code: 'short_name'
            };
            let address = {};

            // Get each component of the address from the place details,
            // and then fill-in the corresponding field on the form.
            for (let i = 0; i < place.address_components.length; i++) {
                let addressType = place.address_components[i].types[0];
                if (componentForm[addressType]) {
                    address[addressType] = place.address_components[i][componentForm[addressType]];
                }
            }

            if (address.route) {
                $('.form-address-edit [id="street_1"]').val(address.route);
                $('.form-address-edit [id="street_1"]').change();
            } else {
                $('.form-address-edit [id="street_1"]').val('');
            }

            if (address.street_number) {
                $('.form-address-edit [name="altura"]').val(address.street_number);
                $('.form-address-edit [name="altura"]').change();
            } else {
                $('.form-address-edit [name="altura"]').val('');
            }

            let city = address.locality;
            if (!city && address.administrative_area_level_1) {
                city = address.administrative_area_level_1;
            }
            if (city) {
                $('.form-address-edit [name="city"]').val(city);
                $('.form-address-edit [name="city"]').change();
            } else {
                $('.form-address-edit [name="city"]').val('');
            }
            if (address.postal_code) {
                $('.form-address-edit [name="postcode"]').val(address.postal_code.replace(/[^0-9]/, ''));
                $('.form-address-edit [name="postcode"]').change();
            } else {
                $('.form-address-edit [name="postcode"]').val('');
            }
            if (address.administrative_area_level_1) {
                if (address.administrative_area_level_1 == 'CABA') {
                    address.administrative_area_level_1 = "Ciudad Autónoma de Buenos Aires";
                }

                if ((address.administrative_area_level_1 == 'Provincia de Buenos Aires')
                    || (address.administrative_area_level_1 == 'Buenos Aires Province')) {
                    address.administrative_area_level_1 = "Buenos Aires";
                }
                let v = $('.form-address-edit [name="region_id"] option').filter(
                    function () {
                        return $(this).html() == address.administrative_area_level_1;
                    }).val();
                if (!v) {
                    v = $('.form-address-edit [name="region_id"] option:contains(' + address.administrative_area_level_1 + ')').val();
                }

                if (v) {
                    $('.form-address-edit [name="region_id"]').val(v);
                    $('.form-address-edit [name="region_id"]').change();
                } else {
                    $('.form-address-edit [name="region_id"]').val('');
                }
            } else {
                $('.form-address-edit [name="region_id"]').val('');
            }

            if (conf && conf.readOnlyFields && conf.readOnlyFields.length) {
                // prevent field modification according to configuration
                //readOnly(conf.readOnlyFields);

                // empty fields must be manually editable
                disableReadonly(conf.readOnlyFields);
            }
        }
    };
});
