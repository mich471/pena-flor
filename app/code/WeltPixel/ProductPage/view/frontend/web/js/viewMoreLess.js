define([
	'jquery'
], function ($) {
	"use strict";

	var stickyDescriptionTabs = {
        options: {
            mobileThreshold: '786'
        },

        init: function (mobileThreshold) {
            this.options.mobileThreshold = mobileThreshold;
            let that = this;

            that.readMoreLessDesktop();
        },
        readMoreLessDesktop: function () {
            let element, grandParent, totalHeight, parent;

            $('.data.item.content').addClass('view-more-less-wrapper');
            $(".data.item.content .read-more-button").click(function () {
                totalHeight = 0;
                element = $(this);
                parent = element.parent();
                grandParent = parent.parent();

                grandParent.addClass("max-content");

                parent.hide();
                parent.next().show();

                return false;
            });

            $(".data.item.content .read-less-button").click(function() {
                element = $(this);
                parent  = element.parent();
                grandParent = parent.parent();

                grandParent.removeClass("max-content");

                parent.hide();
                parent.prev().show();

                return false;

            });
        },
    }

	return stickyDescriptionTabs;
});
