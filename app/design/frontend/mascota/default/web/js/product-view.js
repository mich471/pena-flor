define([
  "jquery",
], 
function($) {
	// $("#maincontent").prepend('<div class="parallax-image">asdasds</div>');
	// $('.parallax-image').parallax({imageSrc: '/static/frontend/mascota/default/es_AR/images/bg_product_view.jpg'});

	$.fn.moveIt = function(){
	  var $window = $(window);
	  var instances = [];
	  
	  $(this).each(function(){
	    instances.push(new moveItItem($(this)));
	  });
	  
	  window.onscroll = function(){
	    var scrollTop = $window.scrollTop();
	    instances.forEach(function(inst){
	      inst.update(scrollTop);
	    });
	  }
	}

	var moveItItem = function(el){
	  this.el = $(el);
	  this.speed = parseInt(8);
	};

	moveItItem.prototype.update = function(scrollTop){
	  var pos = scrollTop / this.speed;
	  this.el.css('transform', 'translateY(' + -pos + 'px)');
	};

	// Initialization
	$(function(){
	  $('.parallax-image').moveIt();
	});
});