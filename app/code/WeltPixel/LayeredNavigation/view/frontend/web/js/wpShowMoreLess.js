define([
    "jquery"
], function ($) {
    "use strict";

    window.wpShowMoreLess = {
        initMoreLess: function() {
            $(document).ready(function(){
                $.each($('.wp-ln-actions'), function() {
                    var visibleItems = $(this).attr('data-visibleItems'),
                        visibleItemsStep = $(this).attr('data-visibleItemsStep'),
                        attrId = $(this).attr('data-attrId'),
                        elId = attrId + '_items',
                        ulSize = $('#' + elId + ' li').length,
                        loadMoreId = '#loadMore_' + attrId,
                        showLessId = '#showLess_' + attrId,
                        x = parseInt(visibleItems),
                        initialX = x,
                        xStep = parseInt(visibleItemsStep);
                    if(visibleItems > 0 && visibleItems.length > 0 && visibleItems < 99 && ulSize > visibleItems) {
                        $(loadMoreId).show();
                        $("#"+ elId + " li:lt(" + visibleItems + ")").show();
                        $(showLessId).hide();
                    } else {
                        $("#"+ elId + " li").show();
                        $(loadMoreId).hide();
                        $(showLessId).hide();
                    }

                    $(loadMoreId).click(function () {
                        if(xStep == 99) {
                            $('#' + elId + ' li:lt(' + ulSize + ')').show();
                            $(this).hide();
                            $(showLessId).show();
                        } else {
                            x = ( x + xStep <= ulSize) ? x + xStep : ulSize;
                            $('#' + elId + ' li:lt(' + x + ')').show();
                            if(ulSize == x){
                                $(this).hide();
                                $(showLessId).show();
                            }else{
                                $(showLessId).show();
                            }
                        }
                    });

                    $(showLessId).click(function () {
                        if(xStep == 99) {
                            $('#' + elId + ' li:gt(' + x + ')').hide();
                            $('#' + elId + ' li:eq(' + x + ')').hide();
                            $(this).hide();
                            $(loadMoreId).show();
                        } else {
                            x = ( x - xStep < 0 || x == ulSize) ? initialX : x - xStep;
                            $('#' + elId + ' li').not(':lt(' + x + ')').hide();
                            if(x <= visibleItems){
                                $(this).hide();
                                $(loadMoreId).show();
                            }else{
                                $(loadMoreId).show();
                            }

                        }
                    });
                });
            });
        }
    }
});
