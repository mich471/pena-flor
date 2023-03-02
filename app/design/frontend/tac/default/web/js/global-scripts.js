define([
  "jquery",
], 
function($) {
	if($(".view-more-link").length){
		$(".view-more-link").on("click",function(e){
			e.preventDefault();
			$(".products-grid .product-items").addClass("show-products");
			$(this).hide();

		});	
	}
});