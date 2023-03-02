define([
    'jquery',
    'underscore'
], function ($) {
    'use strict';
    return function (config) {
        $(config.inputId).autocomplete({
            delay: 500,
            minLength: 3,
            source: function (request, response) {
                if ($('#feed_category_use_taxonomy').val() == '1') {
                    $.ajax({
                        url: config.ajaxUrl,
                        data: {
                            category: request.term,
                            source: $('#feed_category_taxonomy_source').val()
                        },

                        success: function (result) {
                            response(result);
                        }
                    });
                } else {
                    response([]);
                }
            },
            appendTo: '[data-amfeed-js="amfeed-category-list"]',
            messages: {
                results: function () {}
            }
        });
    }
});

