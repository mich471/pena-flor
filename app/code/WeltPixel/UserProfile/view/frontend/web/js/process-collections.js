define([
  'jquery'
], function ($) {
  'use strict';

  function processCollections(url, htmlContainer, loaderSelector) {
    loaderSelector.show();
    $.ajax({
      url: url,
      cache: true,
      dataType: 'html',
      showLoader: false
    }).success(function (data) {
      htmlContainer.append(data);
      htmlContainer.find('img.lazy').each(function(i, img) {
        jQuery(img).attr('src', jQuery(img).attr('data-original'));
      })
    }).done(function () {
      loaderSelector.remove();
    })
  }

  return function (config) {
    var wishlistContainer = $(config.wishlistsElementSelector),
        loaderSelector = $(config.loaderSelector),
        wishlistCollectionsUrl = config.wishlistCollectionsUrl;

    $(function () {
        processCollections(wishlistCollectionsUrl, wishlistContainer, loaderSelector);
    });
  };
});
