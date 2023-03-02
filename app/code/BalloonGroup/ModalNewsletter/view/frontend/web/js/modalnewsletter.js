/**
 * Default Module logic
 */

define([
    "uiComponent",
    "jquery",
    "ko",
    "Magento_Ui/js/modal/modal",
    "mage/url",
    "Magento_Catalog/js/product/remaining-characters"
], function (Component, $, ko, modal, $url) {
    "use strict";

    var self;
    return Component.extend({
        defaults: {
            element: {
                modalContent: "#custom-modal-newsletter",
                button: ".save-newsletter"
            }
        },

        timeCookies: ko.observable(0),
        responseData: ko.observable({}),
        classResponse: ko.observable(""),

        initialize: function (config) {
            self = this;
            this._super();
            $(".btnAgeYes").click(function(e){
                e.preventDefault();
                self.openModalCooki()
            });
            self.addEvents();
            self.timeCookies(config.time_cookies)
            //return self;
        },

        addEvents: function () {
            $(".btn-newsletter-modal a").on("click", self.openModal.bind(this));
            self.sendAjaxForm();
            $("#checkbox_cookie").change(function (e) {
                if ($(this.checked)) {
                    self.setActiveCookie();
                }
            });
        },


        openModalCooki: function () {

            if(window.location.pathname !== "/") {
                return;
            }

            var options = {
                type: "popup",
                modalClass: "custom-modal-newsletter",
                title: "",
                responsive: true,
                innerScroll: false,
                buttons: []
            }

            var popup = modal(options, self.element.modalContent);

            var temp = $.cookie("newsletter_modal");//Get the cookies

            if (temp == null) {
                $(self.element.modalContent).modal("openModal");
                var date = new Date();

                date.setTime(date.getTime() + (1 * 60 * 1000)); // 1 - minutes
                $.cookie("newsletter_modal", date, {path: "/", expires: date});//Set the cookies
            }
        },


        setActiveCookie: function () {
            var date = new Date();
            var minutes = 2;
            date.setTime(date.getTime() + (minutes * 60 * self.timeCookies()));
            $.cookie("newsletter_modal", date, {path: "/", expire: date}); //Set the cookies
        },

        sendAjaxForm: function () {
            $("#custom-modal-newsletter .form.subscribe").submit(function (e) {
                e.preventDefault();
                if ($(this).valid()) {
                    var url = $(this).attr("action");
                    var postData = $(this).serializeArray();

                    try {
                        $.ajax({
                            url: url,
                            dataType: "json",
                            type: "POST",
                            showLoader: true,
                            data: $.param(postData),
                            complete: function (data) {
                                if (typeof data === "object") {
                                    data = data.responseJSON;
                                    self.responseData(data);
                                    var _class = parseInt(data.status) > 0 ? "success" : "error";

                                    var content = $("#response-data");
                                    content.show("slow");
                                    content.removeClass();
                                    content.addClass(_class);
                                    content.text(data.msg);

                                    setTimeout(function () {
                                        content.hide(1000);
                                        if (parseInt(data.status) === 1) {
                                            $(self.element.modalContent).modal("closeModal");
                                            self.setActiveCookie();
                                        }
                                    }, 5000);

                                } else {
                                    //Unknown Error
                                }
                            }
                        });
                    } catch (e) {
                        //check for errors
                        console.log(e)
                    }
                }
            });
        },

        openModal: function (event) {

            var options = {
                type: "popup",
                modalClass: "custom-modal-newsletter",
                title: "Recibe nuestras noticias y ofertas",
                responsive: true,
                innerScroll: false,
                buttons: [{
                    text: $.mage.__("Close"),
                    class: "",
                    click: function () {
                        this.closeModal();
                    }
                }]
            }

            var popup = modal(options, self.element.modalContent);

            $(self.element.modalContent).modal("openModal");

            event.preventDefault();
            event.stopPropagation();
        }
    });
});
