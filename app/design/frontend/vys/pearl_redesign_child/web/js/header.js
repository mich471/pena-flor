define([
    'jquery',
    'domReady!'
], function ($) {
    'use strict';

    $('.sign-wrapper').click(function(){

        if($(this).hasClass('open')){
            $(this).find('.sign-container').hide();
            $(this).removeClass('open');
        }else{
            $(this).find('.sign-container').show();
            $(this).addClass('open');
        }

        $(document).mouseup(function(e) 
        {
            var container = $(".sign-container");
            if (!container.is(e.target) && container.has(e.target).length === 0) 
            {
                container.hide();
                $('.sign-wrapper').removeClass('open');
            }
        });
    });

});