define([
    'jquery',
    'jquery/ui',
    'jquery/validate',
    'mage/translate'
], function ($) {
    "use strict";
    return function () {
        $.validator.addMethod(
            'validate-underage', function (value) {
                var dateArr = value.split('/');
                if (dateArr.length != 3) {
                    return false;
                }
                var dateObj = new Date(dateArr[2], dateArr[1], dateArr[0]),
                    _calculateAge = function (birthday) {
                        var ageDifMs = Date.now() - birthday.getTime();
                        var ageDate = new Date(ageDifMs); // miliseconds from epoch
                        return Math.abs(ageDate.getUTCFullYear() - 1970)
                    };
                return _calculateAge(dateObj) > 17;
            },
            $.mage.__('Tenes que ser mayor de 18')
        );
    }
});