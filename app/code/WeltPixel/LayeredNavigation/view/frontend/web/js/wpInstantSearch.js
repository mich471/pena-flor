define([
    "jquery"
], function ($) {
    "use strict";
    window.wpInstantSearch = {
        searchOptions: function() {

            $('.wp-instant-search-inp').keyup(function() {
                var id, input, filter, li, a, i;
                id = $(this).data('attr-id');
                if(id) {
                    input = $('#instant_search_'+id);
                    filter = input.val().toUpperCase();
                    li = $('#'+ id + '_items li');

                    for (i = 0; i < li.length; i++) {
                        a = li[i].getElementsByTagName("a")[0];
                        if (a.innerText.toUpperCase().indexOf(filter) > -1) {
                            li[i].style.display = "";
                        } else {
                            li[i].style.display = "none";
                        }
                    }
                }
            })


        }
    }
});
