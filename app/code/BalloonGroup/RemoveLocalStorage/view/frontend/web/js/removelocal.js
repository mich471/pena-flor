define([
    'jquery'
], function ($){
    'use strict';
    $.widget('mage.removelocal', {

        _init: function () {

            if (window.localStorage["mage-cache-storage"]) {
                const storageObject = JSON.parse(window.localStorage["mage-cache-storage"]);
                const newShippingAddress = storageObject["checkout-data"]["newCustomerShippingAddress"];

                if (!newShippingAddress) return;

                if (!this.hasAttributeAltura(newShippingAddress["custom_attributes"])) {
                    storageObject["checkout-data"]["newCustomerShippingAddress"] = "";
                    const newStorageObject = JSON.stringify(storageObject);
                    window.localStorage.setItem("mage-cache-storage", newStorageObject);
                }

            }
        },
        hasAttributeAltura: function(customAttributes) {
            if (!customAttributes) return false;

            const attributesArray = Object.entries(customAttributes);
            if (attributesArray.length == 0) return false;

            const isAttributeAltura = ([key, value]) => {
                if (key != "altura") return false;
                if (value.length == 0) return false;
                return true;
            }

            if (!attributesArray.some(isAttributeAltura)) return false;
            
            return true;
        }
    });
    return $.mage.removelocal;
});