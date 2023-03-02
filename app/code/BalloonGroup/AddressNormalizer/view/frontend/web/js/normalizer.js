define([
    "jquery",
    'mage/translate'
], function ($, $t) {
    "use strict";

    $.widget('lyracons.AddressNormalizer', {
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
            var template = '<div class="field required" name="shippingAddress.autocomplete">';
            template += '    <label class="label" for="shippingAddress.autocomplete">';
            template += '        <span>' + $t('Dirección: calle, altura, localidad (elegí alguna de las opciones. Si ninguna aplica, deja el detalle en Comentarios de entrega)') + '</span>';
            template += '    </label>';
            template += '    <div class="control">';
            template += '        <input class="input-text required-entry" type="text" id="addressAutocomplete" aria-required="true" aria-invalid="false" autocomplete="new-password" onfocus="this.setAttribute(`autocomplete`,`new-password`);">';
            template += '    </div>';
            template += '</div>';

            // modify text "Dirección: Line 1" (Couldn't change ir otherwise)
            let innerLabel = $('#shipping-new-address-form [name="street[0]"]').closest('.field').children(":first");
            let outerLabel = $('#shipping-new-address-form [name="street[0]"]').closest('.street').children(":first");
            innerLabel.remove();
            outerLabel.text('Calle');       

            $("fieldset.field.street").before(template);

            if (conf && conf.readOnlyFields && conf.readOnlyFields.length) {
                $(conf.readOnlyFields).each(function(i, e) {
                   $('#shipping-new-address-form [name="' + e + '"]').attr('disabled', 'disabled');
                });
            }

            
            // Enable manual complete of fields once done searching with api.
            // If no address found by the api, client should still be able to complete an address
            let fill = this;
            $('#addressAutocomplete').focusout(function() {
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
                $('#addressAutocomplete')[0], options
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
                $('#shipping-new-address-form [name="street[0]"]').val(address.route);
                $('#shipping-new-address-form [name="street[0]"]').change();
            } else {
                $('#shipping-new-address-form [name="street[0]"]').val('');
            }

            if (address.street_number) {
                $('#shipping-new-address-form [name="custom_attributes[altura]"]').val(address.street_number);
                $('#shipping-new-address-form [name="custom_attributes[altura]"]').change();
            } else {
                $('#shipping-new-address-form [name="custom_attributes[altura]"]').val('');
            }

            let city = address.locality;
            if (!city && address.administrative_area_level_1) {
                city = address.administrative_area_level_1;
            }
            if (city) {
                $('#shipping-new-address-form [name="city"]').val(city);
                $('#shipping-new-address-form [name="city"]').change();
            } else {
                $('#shipping-new-address-form [name="city"]').val('');
            }
            if (address.postal_code) {
                $('#shipping-new-address-form [name="postcode"]').val(address.postal_code.replace(/[^0-9]/, ''));
                $('#shipping-new-address-form [name="postcode"]').change();
            } else {
                $('#shipping-new-address-form [name="postcode"]').val('');
            }
            if (address.administrative_area_level_1) {
                // look for province based on administrative_area_level_1
                let region_id = $('#shipping-new-address-form [name="region_id"] option').filter(
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
                        region_id = $('#shipping-new-address-form [name="region_id"] option').filter(function () {
                            return $(this).html() == 'CABA';
                        }).val();
                        if (!region_id) {
                            region_id = $('#shipping-new-address-form [name="region_id"] option').filter(function () {
                                return $(this).html() == 'Capital Federal';
                            }).val();
                            if (!region_id) {
                                region_id = $('#shipping-new-address-form [name="region_id"] option').filter(function () {
                                    return $(this).html() == 'Ciudad Autónoma de Buenos Aires';
                                }).val();
                            }
                        }
                    }

                    if ((address.administrative_area_level_1 == 'Provincia de Buenos Aires')
                        || (address.administrative_area_level_1 == 'Buenos Aires Province')) {
                        // Provincia de Buenos Aires: look for "Buenos Aires"
                        address.administrative_area_level_1 = "Buenos Aires";
                        region_id = $('#shipping-new-address-form [name="region_id"] option').filter(function () {
                            return $(this).html() == 'Buenos Aires';
                        }).val();
                    }
                }
                if (!region_id) {
                    // no region label found: look for option label CONTAINING region name
                    region_id = $('#shipping-new-address-form [name="region_id"] option:contains(' + address.administrative_area_level_1 + ')').val();
                }
                if (region_id) {
                    $('#shipping-new-address-form [name="region_id"]').val(region_id);
                    $('#shipping-new-address-form [name="region_id"]').change();
                } else {
                    $('#shipping-new-address-form [name="region_id"]').val('');
                }
            }

            if (conf && conf.readOnlyFields && conf.readOnlyFields.length) {
                $(conf.readOnlyFields).each(function(i, e) {
                   $('#shipping-new-address-form [name="' + e + '"]').attr('disabled', 'disabled');
                });
            }
            var fill = this;
            fill._disableReadonly(conf.readOnlyFields, verify_route);
        },

        _disableReadonly: function (conf, verify_route) {
            $(conf).each(function(i, e) {
                let value = $("#shipping-new-address-form [name='" + e + "']").val();

                if (verify_route === true) {
                    $('#shipping-new-address-form [name="street[0]"]').removeAttr('disabled');
                }
                if (typeof value === 'undefined' || value === '') {
                    $('#shipping-new-address-form [name="' + e + '"]').removeAttr('disabled');
                }
            });
        }
    });
    return $.lyracons.AddressNormalizer;
});
