define([
  "jquery","slick"
], 
function($) {
	$(document).on("click","a.action.showcart",function(e){
	    e.preventDefault();
	    $('#footer-cart-trigger').toggleClass('active');
	    $('#footer-mini-cart').slideToggle(300);
	});
	//age check
	$(document).on("click", "div.age-container > div.age-message > div > a", function(e) {
		e.preventDefault();
		var getQueryString = function() {
			var vars = [], hash;
			var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
			for(var i = 0; i < hashes.length; i++)
			{
				hash = hashes[i].split('=');
				vars.push(hash[0]);
				vars[hash[0]] = hash[1];
			}
			return vars;
		};

		$(this).parent().addClass('disabled loading');
		$.post(
            location.protocol + '//' + location.hostname + '/agecheckfrontend/cookie/create',
			{},
			function (res) {
				if (res == 'created') {
					var urlEncoded = getQueryString()['backUrl'];
					if (urlEncoded == '') {
                        location.assign(location.protocol + '//' + location.hostname);
					}
					else {
						location.assign(decodeURIComponent(urlEncoded));
					}
				}
				else {
					location.reload();
				}
            }
		);
	});

	/*Envios*/
	if($("body.cms-medios-de-pago").length){
		$(".zona-items .item").on("click, mouseover",function(){
			mapa = $(this).data("image");
			$(".zona-items .item").removeClass("active");
			$(this).addClass("active");
			$(".zona-images .box-img").removeClass("active");
			$(".zona-images #"+mapa).addClass("active");

		});
	}
	// Cierra Success Messages
	$('.page.messages').bind("DOMSubtreeModified",function(){
	  	if($(".message.success").length != 0){
	  		setTimeout(function() {
	  		    $(".message.success").slideUp();
	  		}, 5000);
	      }
	});
	
	if($("body.cms-creacion").length){
		var slickPrimarySecondary = {
		   slidesToShow: 1,
		   slidesToScroll: 1,
		   arrows: false,
		   fade: true,
		 };
		 var slickNavigator = {
		   asNavFor: '.second-state',
		   autoplay: true,
		   autoplaySpeed: 7000,
		   mobileFirst: true,
		   dots: true,
		   focusOnSelect:true,
		   centerMode: false,
		 };

		 $('.slidertwo').slick(slickPrimarySecondary);
		 $('.slider').slick(slickNavigator);
	}

	window.onload = function() {
		$("head").append('<style class="popup-style"> #newspopup_up_bg_7 { display: none !important;} </style>');
		setTimeout(function(){
			if($('.modal-age-block._show').length == 0) {
				$(".popup-style").remove();
			}
		}, 3500);
	}

});