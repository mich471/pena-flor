define(
    [
        'jquery',
        'uiComponent'
    ],
    function ($, Component) {
        'use strict';

        return function(optionConfig){
            $.ajax({
                url: optionConfig.ajaxUrl,
                method: 'POST',
                cache: false,
                data: {
                    is_ajax: 1,
                    request_type: optionConfig.requestType
                },
                success: function (result) {
                    $('#' + optionConfig.requestType).html(result.block);

                }
            });
        };
    });