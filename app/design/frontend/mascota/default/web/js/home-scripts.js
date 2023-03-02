define([
  "jquery",
], 
function($) {
    //Modify Order Checkout
    var cartAction = window.location.hash.substr(1);
    if (cartAction == "modify-order"){
        $('#footer-cart-trigger').toggleClass('active');
        $('#footer-mini-cart').slideToggle(300);
    }

    $(document).on("click",".iMenu .groupmenu > li a", function(){
    	$('html').removeClass('nav-open');
    	$('html').removeClass('nav-before-open');
    })
});