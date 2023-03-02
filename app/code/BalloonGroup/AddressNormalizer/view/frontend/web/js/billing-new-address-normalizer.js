define([
    "jquery",
    'mage/translate'
], function ($, $t) {
    "use strict";

    $.widget('lyracons.BillingAddressNormalizer', {
        options: {
        },
        _autocomplete: null,
        _create: function () {
            var self = this;
        },
        _init: function () {
            var self = this;
            this.config = this.options;
            if (self.config && self.config.isEnabled) {
                var jsSource = '//maps.googleapis.com/maps/api/js?key=' + self.config.normalizerGooglePlacesApiKey + '&libraries=places&region=AR&language=es';
                require([jsSource], function () {
                    self._initAutocomplete(self.config);
                });
            }
        },

        _initAutocomplete: function (conf) {
            
            var template = '<div class="field required" name="billingAddress.autocomplete">';
            template += '    <label class="label" for="billingAddress.autocomplete">';
            template += '        <span>' + $t('Dirección: calle, altura, localidad (elegí alguna de las opciones. Si ninguna aplica, deja el detalle en Comentarios de entrega)') + '</span>';
            template += '    </label>';
            template += '    <div class="control">';
            template += '        <input class="input-text required-entry" type="text" id="addressAutocompleteBilling" aria-required="true" aria-invalid="false" autocomplete="new-password" onfocus="this.setAttribute(`autocomplete`,`new-password`);">';
            template += '    </div>';
            template += '</div>';

            // modify text "Dirección: Line 1" (Couldn't change ir otherwise)
            let innerLabel = $('#billing-new-address-form [name="street[0]"]').closest('.field').children(":first");
            let outerLabel = $('#billing-new-address-form [name="street[0]"]').closest('.street').children(":first");
            innerLabel.remove();
            outerLabel.text('Calle');

            if ($('#billing-new-address-form [name="billingAddress.autocomplete"]').length == 0) {
                $('#billing-new-address-form fieldset.field.street').before(template);
            }

            if (conf && conf.readOnlyFields && conf.readOnlyFields.length) {
                $(conf.readOnlyFields).each(function(i, e) {
                   $('#billing-new-address-form [name="' + e + '"]').attr('disabled', 'disabled');
                });
            }

           
            // Enable manual complete of fields once done searching with api.
            // If no address found by the api, client should still be able to complete an address
            let fill = this;
            $('#addressAutocompleteBilling').focusout(function() {
                if ($(this).val()) {
                    fill._disableReadonly(conf.readOnlyFields, false);
                }
            });

            // Create the autocomplete object, restricting the search predictions to
            // geographical location types.
            let options = {types: ['geocode']};

            if (conf && conf.allowedCountries && conf.allowedCountries.length) {
                options.componentRestrictions = { country: conf.allowedCountries }
            }

            this._autocomplete = new google.maps.places.Autocomplete(
                $('#addressAutocompleteBilling')[0], options
            );

            // Avoid paying for data that you don't need by restricting the set of
            // place fields that are returned to just the address components.
            this._autocomplete.setFields(['address_component']);

            // When the user selects an address from the drop-down, populate the
            // address fields in the form.
            var norm = this;
            this._autocomplete.addListener('place_changed', function () {
                let place = norm._autocomplete.getPlace();
                if (place) {
                    norm._fillInAddress(place, conf);
                }
            });
        },
        _fillInAddress: function (place, conf) {
            // verify_route :  Variable to verify if it was set by route and enables the address field.
            let  verify_route  = false;
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
            }

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
                $('#billing-new-address-form [name="street[0]"]').val(address.route);
                $('#billing-new-address-form [name="street[0]"]').change();
            } else {
                $('#billing-new-address-form [name="street[0]"]').val('');
            }

            if (address.street_number) {
                $('#billing-new-address-form [name="custom_attributes[altura]"]').val(address.street_number);
                $('#billing-new-address-form [name="custom_attributes[altura]"]').change();
            } else {
                $('#billing-new-address-form [name="custom_attributes[altura]"]').val('');
            }

            let city = address.locality;
            if (!city && address.administrative_area_level_1) {
                city = address.administrative_area_level_1;
            }
            if (city) {
                $('#billing-new-address-form [name="city"]').val(city);
                $('#billing-new-address-form [name="city"]').change();
            } else {
                $('#billing-new-address-form [name="city"]').val('');
            }
            if (address.postal_code) {
                $('#billing-new-address-form [name="postcode"]').val(address.postal_code.replace(/[^0-9]/, ''));
                $('#billing-new-address-form [name="postcode"]').change();
            } else {
                $('#billing-new-address-form [name="postcode"]').val('');
            }
            if (address.administrative_area_level_1) {
                // look for province based on administrative_area_level_1
                let region_id = $('#billing-new-address-form [name="region_id"] option').filter(
                    function () {
                        return $(this).html() == address.administrative_area_level_1;
                    }).val();
                if (!region_id) {
                    // region was not found: it may be because of magento labels
                    if (address.administrative_area_level_1 == 'CABA'
                        || address.administrative_area_level_1 == 'Autonomous City of Buenos Aires'
                        || address.administrative_area_level_1 == 'Ciudad Autónoma de Buenos Aires'
                    ) {
                        // Capital: look for options "CABA", "Capital Federal" or "Ciudad Autonoma de Buenos Aires"
                        address.administrative_area_level_1 = "Capital Federal";
                        region_id = $('#billing-new-address-form [name="region_id"] option').filter(function () {
                            return $(this).html() == 'CABA';
                        }).val();
                        if (!region_id) {
                            region_id = $('#billing-new-address-form [name="region_id"] option').filter(function () {
                                return $(this).html() == 'Capital Federal';
                            }).val();
                            if (!region_id) {
                                region_id = $('#billing-new-address-form [name="region_id"] option').filter(function () {
                                    return $(this).html() == 'Ciudad Autónoma de Buenos Aires';
                                }).val();
                            }
                        }
                    }

                    if (address.administrative_area_level_1 == 'Provincia de Buenos Aires'
                        || address.administrative_area_level_1 == 'Buenos Aires Province') {
                        // Provincia de Buenos Aires: look for "Buenos Aires"
                        address.administrative_area_level_1 = "Buenos Aires";
                        region_id = $('#billing-new-address-form [name="region_id"] option').filter(function () {
                            return $(this).html() == 'Buenos Aires';
                        }).val();
                    }
                }
                if (!region_id) {
                    // no region label found: look for option label CONTAINING region name
                    region_id = $('#billing-new-address-form [name="region_id"] option:contains(' + address.administrative_area_level_1 + ')').val();
                }
                if (region_id) {
                    $('#billing-new-address-form [name="region_id"]').val(region_id);
                    $('#billing-new-address-form [name="region_id"]').change();
                } else {
                    $('#billing-new-address-form [name="region_id"]').val('');
                }
            }

            if (conf && conf.readOnlyFields && conf.readOnlyFields.length) {
                $(conf.readOnlyFields).each(function(i, e) {
                   $('#billing-new-address-form [name="' + e + '"]').attr('disabled', 'disabled');
                });
            }
            var fill = this;
            fill._disableReadonly(conf.readOnlyFields, verify_route);
        },

        _disableReadonly: function (conf, verify_route) {
            $(conf).each(function(i, e) {
                let value = $("#billing-new-address-form [name='" + e + "']").val();

                if((verify_route === true)){
                    $('#billing-new-address-form [name="street[0]"]').removeAttr('disabled');
                }
                if (typeof value === 'undefined' || value === '') {
                    $('#billing-new-address-form [name="' + e + '"]').removeAttr('disabled');
                }
            });
        }
    });
    return $.lyracons.AddressNormalizer;
});
