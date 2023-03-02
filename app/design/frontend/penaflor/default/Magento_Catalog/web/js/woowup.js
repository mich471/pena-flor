require([
    'jquery'
], function ($) {
    sku = $("[itemprop='sku']").text();
    specialPrice = $("[data-price-type='finalPrice']").attr('data-price-amount');
    originalPrice =  $("[data-price-type='oldPrice']").attr('data-price-amount');

    let metadata = {
        sku: sku, 
        price: originalPrice,
        offer_price: specialPrice
    };

    WU.track('jJ3tT1Z3B', 'product-view', metadata);
}

)