define([
    'jquery'
], function ($) {
    'use strict';

    function processReviews(url, reviewConainer) {
        $.ajax({
            url: url,
            cache: true,
            dataType: 'html',
            showLoader: false,
        }).done(function (data) {
            reviewConainer.html(data).trigger('contentUpdated');
        });
    }

    return function (config) {
        var reviewConainer = $(config.reviewsSelector);
        processReviews(config.productReviewUrl, reviewConainer);
    };
});
