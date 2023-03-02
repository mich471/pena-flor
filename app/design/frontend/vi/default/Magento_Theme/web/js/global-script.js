require([
    'jquery',
    'mage/calendar'
], function ( $ ) {
    $.extend(true, $, {
        calendarConfig: {
            closeText: 'Cerrar',
            prevText: '< Ant',
            nextText: 'Sig >',
            currentText: 'Hoy',
            monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
            monthNamesShort: ['Ene','Feb','Mar','Abr', 'May','Jun','Jul','Ago','Sep', 'Oct','Nov','Dic'],
            dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
            dayNamesShort: ['Dom','Lun','Mar','Mié','Juv','Vie','Sáb'],
            dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','Sá'],
            weekHeader: 'Sm'
        }
    });

    //Menu
    $('.item.level1').each(function(){
        var classColumn = $(this).attr('class').split(' ')[2];
        if (classColumn.indexOf("col") >= 0){
            $(this).parent().addClass(classColumn);
            $(this).parent().parent().addClass(classColumn);
        }
    });


    //Mobile
    if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
        $("body").addClass("mobile-device");

        if($("body").hasClass("catalog-product-view")){
            $(".product.media").prepend("<div class='name-container'></div>");
            $(".product-info-main .bodega").appendTo(".name-container");
            $(".product-info-main .page-title-wrapper.product").appendTo(".name-container");
            $(".product-info-main .varietal").appendTo(".name-container");
        }

        if($("body").hasClass("page-products")){
            $(".products.wrapper").removeClass("grid").removeClass("products-grid").addClass("list").addClass("products-list");
        }
    }


});

require([
    'jquery',
    'jquery/ui',
    'jquery/validate',
    'mage/translate'
], function ( $ ) {
    $.validator.addMethod(
        'validate-underage', function (value) {
            var dateArr = value.split('/');
            if (dateArr.length != 3) {
                return false;
            }
            var dateObj = new Date(dateArr[2], dateArr[1], dateArr[0]),
                _calculateAge = function(birthday) {
                    var ageDifMs = Date.now() - birthday.getTime();
                    var ageDate = new Date(ageDifMs); // miliseconds from epoch
                    return Math.abs(ageDate.getUTCFullYear() - 1970)
                };
            return _calculateAge(dateObj) > 17;
        },
        $.mage.__('Tenes que ser mayor de 18')
    );
    $(document).on("click", ".plus-cart, .sub-cart", function(){
        $("button[name='update_cart_action']").trigger("click")
    })
    $(document).on("mouseup", "button.increasing-qty, button.decreasing-qty", function(event){
        event.preventDefault();
        setTimeout(function(){$(".update-cart-item").trigger("click")},1000)
    })
    $(document).on("click", "#mp-custom-save-payment", function(){
        if($("#mpCardNumber").val()!='' && !$("#mpCardNumber").hasClass("mp-form-control-error") &&
        $("#mpCardExpirationDate").val()!='' && !$("#mpCardExpirationDate").hasClass("mp-form-control-error") &&
        $("#mpSecurityCode").val()!='' && !$("#mpSecurityCode").hasClass("mp-form-control-error") &&
        !$("#mpInstallments").hasClass("mp-form-control-error")
        ){
            $("#mp-custom-save-payment").prop("disabled",true);
            setTimeout(function(){
                $("#mp-custom-save-payment").prop("disabled",false);
            },8000)
        }
        setTimeout(function(){
            if($("div[data-role='checkout-messages'] .message.error").length){
                $("#mp-custom-save-payment").prop("disabled",false);
            }
        },4000)

    })

    $(document).on("keyup", "#mpCardNumber", function(){
        $("#mp-custom-save-payment").prop("disabled",false);
    })
    $(document).on("keyup", "#mpCardExpirationDate", function(){
        $("#mp-custom-save-payment").prop("disabled",false);
    })
    $(document).on("keyup", "#mpSecurityCode", function(){
        $("#mp-custom-save-payment").prop("disabled",false);
    })
    $(document).on("change", "#mpInstallments", function(){
        $("#mp-custom-save-payment").prop("disabled",false);
    })
    $(document).on("change", "#mpDocNumber", function(){
        $("#mp-custom-save-payment").prop("disabled",false);
    })
});
