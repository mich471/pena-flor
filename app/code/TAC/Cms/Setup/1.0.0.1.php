<!-- BLOQUES -->

title = 'TAC Menu'
identifier = 'tac-menu-block'
content = '
<div class="cdz-main-menu">{{widget type="Codazon\MegaMenu\Block\Widget\Megamenu" menu="tac-menu"}}</div>'

title = 'TAC Slider'
identifier = 'tac-slider'
content = '
<div class="slideshow-container">{{widget type="Codazon\Slideshow\Block\Widget\Slideshow" slideshow_id="tac-slider"}}</div>'

title = 'TAC Shop our Wines'
identifier = 'tac-shop-our-wines-block'
content = '
<div class="shop-our-wines"><div class="cont-our-wines"><h4>We only sell wines we love</h4><p>We're a small, dedicated team working directly with wine producers to offer you a curated online selection of the best New Zealand wines that you can buy.</p><button class="our-wines-btn">Shop our wines</button></div></div>'

title = 'TAC Bloque Largo'
identifier = 'tac-large-block-home'
content = '
<div class="large-block"><img src="{{view url="images/banners_home/bloque-largo-img.png"}}" alt="Selected Wines" /><div class="large-text-cont"><p>Our wines are hand selected by New Zealand's first and only Master Sommelier, <strong>Cameron J. Douglas</strong> - a man width a great nose and high expetations.</p><button>MEET CAMERON</button></div></div>'


title = 'TAC Bloques Medios'
identifier = 'tac-middle-blocks-home'
content = '
<div class="mid-blocks"><div class="mid-block-left"><img src="{{view url="images/banners_home/block-left.png"}}" alt="Selected Wines" /><div class="mid-text-cont"><p>Bog, coldluscious and award winning!</p><button>BUY ONLINE</button></div></div><div class="mid-multi-block"><a href="" class="mini-block" style="background-image: url({{view url="images/banners_home/multiblock-1.png"}})"><h6>Explorer</h6><span>our wine</span></a><a href="#" class="mini-block" style="background-image: url({{view url="images/banners_home/multiblock-2.png"}})"><h6>Discover</h6><span>the regions</span></a><a href="#" class="mini-block" style="background-image: url({{view url="images/banners_home/multiblock-3.png"}})"><h6>Hear</h6><span>our story</span></a><a href="#" class="mini-block" style="background-image: url({{view url="images/banners_home/multiblock-4.png"}})"><h6>Meet</h6><span>the wine producers</span></a></div>'

title = 'TAC Product list home'
identifier = 'tac-product-list-home'
content = '
<div class="cdz-best-seller-wrap cdz-products text-center cdz-sm-24">
	<div class="row">
		<div class="col-sm-24">
			<div class="cdz-block-title03">
				<p class="b-title h2"><span>Most Popular products</span></p>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-24">{{widget type="Codazon\ProductFilter\Block\Product\ProductsList" display_type="all_products" products_count="14" order_by="name ASC" show="thumb,name,review,price,addtocart,addto" thumb_width="220" thumb_height="220" filter_template="custom" custom_template="product/widget/content/slider_style01.phtml" show_slider="1" slider_item="4" conditions_encoded="a:1:[i:1;a:4:[s:4:`type`;s:50:`Magento|CatalogWidget|Model|Rule|Condition|Combine`;s:10:`aggregator`;s:3:`all`;s:5:`value`;s:1:`1`;s:9:`new_child`;s:0:``;]]"}}</div>
	</div>
</div>'


title = 'TAC footer'
identifier = 'tac-footer'
content = '
<div class="marcas-home">
	<div>
		<a href="#"><img src="{{view url="images/marcas/logos_footer-COSTA_PAMPA.png"}}" alt="Costa & Pampa" /></a>
		<a href="#"><img src="{{view url="images/marcas/logos_footer-EL_ESTECO.png"}}" alt="El Esteco" /></a>
		<a href="#"><img src="{{view url="images/marcas/logos_footer-LAS_MORAS.png"}}" alt="Finca las Moras" /></a>
		<a href="#"><img src="{{view url="images/marcas/logos_footer-MASCOTA.png"}}" alt="Mascota" /></a>
		<a href="#"><img src="{{view url="images/marcas/logos_footer-NAVARRO.png"}}" alt="Navarro Correas" /></a>
		<a href="#"><img src="{{view url="images/marcas/logos_footer-TRES_14.png"}}" alt="Tres" /></a>
	</div>
</div>
<div class="footer-copy">
	<div class="footer-extras">
		<a href="#"><img src="{{view url="images/winery_direct.png"}}" alt="Winery Direct" /><span>Winery Direct</span></a>
		<a href="#"><img src="{{view url="images/hand_selected.png"}}" alt="Hand Selected" /><span>Hand Selected</span></a>
		<a href="#"><img src="{{view url="images/official_retailer.png"}}" alt="Official Retailer" /><span>Official Retailer</span></a>
		<div class="tarjetas">
			<i class="fa fa-cc-visa"></i>
			<i class="fa fa-cc-mastercard"></i>
			<i class="fa fa-cc-amex"></i>
		</div>
	</div>
	<div class="copy">
		<div>
			<svg version="1.1" id="lyracons-brand" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="147.464px" height="25px" viewBox="232.268 381.063 147.464 30" enable-background="new 232.268 381.063 147.464 30" xml:space="preserve">
			   <g>
			      <path fill="#FFFFFF" d="M247.862,381.613c-3.452,0.917-5.53,4.46-4.644,7.912c0.917,3.452,4.43,5.53,7.912,4.644c3.452-0.916,5.53-4.43,4.644-7.912C254.858,382.805,251.314,380.727,247.862,381.613z"></path>
			      <path fill="#FFFFFF" d="M271.752,381.063c-8.279,0-15,6.721-15,15s6.721,15,15,15c8.279,0,15-6.721,15-15C286.721,387.784,280.031,381.063,271.752,381.063z M271.752,402.907c-3.788,0-6.843-3.055-6.843-6.843s3.055-6.843,6.843-6.843c3.788,0,6.843,3.055,6.843,6.843C278.595,399.821,275.54,402.907,271.752,402.907z"></path>
			      <path fill="#FFFFFF" d="M237.964,391.023c0.275-1.558-0.764-3.024-2.322-3.33c-1.558-0.275-3.055,0.764-3.33,2.322s0.764,3.055,2.322,3.33C236.192,393.619,237.689,392.581,237.964,391.023z"></path>
			      <path fill="#FFFFFF" d="M243.096,394.811c-2.872-0.489-5.591,1.375-6.079,4.246c-0.519,2.841,1.375,5.56,4.246,6.079c2.872,0.55,5.56-1.375,6.079-4.216C247.831,398.049,245.968,395.33,243.096,394.811z"></path>
			      <path fill="#FFFFFF" d="M314.43,390.839c-0.672,0.336-1.222,0.764-1.65,1.283v-1.619h-3.422v12.251h3.422v-5.285c0-1.191,0.244-2.138,0.764-2.872c0.489-0.733,1.253-1.069,2.291-1.069h0.672l-0.031-3.208C315.774,390.32,315.102,390.503,314.43,390.839z"></path>
			      <path fill="#FFFFFF" d="M326.467,391.542c-0.917-0.825-1.986-1.222-3.208-1.222c-1.589,0-2.963,0.58-4.185,1.711c-1.191,1.13-1.802,2.658-1.802,4.582c0,1.894,0.58,3.452,1.741,4.613c1.161,1.161,2.536,1.741,4.094,1.741c1.253,0,2.352-0.397,3.33-1.191v1.008h3.452v-12.281h-3.452v1.039H326.467z M325.672,399.118c-0.55,0.642-1.222,0.947-2.016,0.947c-0.794,0-1.466-0.306-2.077-0.947c-0.58-0.642-0.886-1.436-0.886-2.444s0.306-1.833,0.855-2.505c0.58-0.672,1.283-0.978,2.077-0.978c0.794,0,1.466,0.336,2.016,1.008c0.55,0.672,0.825,1.497,0.825,2.505C326.497,397.683,326.222,398.477,325.672,399.118z"></path>
			      <path fill="#FFFFFF" d="M363.89,390.289c-1.283,0-2.444,0.519-3.483,1.558v-1.375h-3.422v12.251h3.422v-6.782c0-0.947,0.244-1.68,0.764-2.169c0.519-0.489,1.1-0.733,1.802-0.733c1.375,0,2.077,0.947,2.077,2.811v6.935h3.422v-7.454c0-1.527-0.428-2.75-1.314-3.635C366.273,390.748,365.204,390.289,363.89,390.289z"></path>
			      <path fill="#FFFFFF" d="M378.463,396.369c-0.458-0.275-0.855-0.458-1.161-0.611c-0.336-0.122-0.886-0.305-1.65-0.55c-0.764-0.244-1.314-0.458-1.65-0.642c-0.336-0.183-0.489-0.428-0.489-0.764c0-0.58,0.519-0.855,1.589-0.855s2.108,0.367,3.177,1.1l1.222-2.291c-1.436-0.978-2.902-1.466-4.338-1.466s-2.627,0.367-3.544,1.069c-0.916,0.703-1.375,1.65-1.375,2.78c0,1.13,0.458,1.986,1.405,2.566c0.458,0.275,0.855,0.519,1.222,0.672c0.367,0.153,0.886,0.336,1.558,0.55c0.672,0.214,1.191,0.428,1.558,0.672s0.519,0.519,0.519,0.794c0,0.275-0.122,0.519-0.397,0.703c-0.244,0.183-0.611,0.275-1.039,0.275c-1.436,0-2.811-0.489-4.094-1.497l-1.497,2.139c0.794,0.642,1.68,1.13,2.658,1.436c0.978,0.336,1.925,0.489,2.841,0.489c1.375,0,2.505-0.367,3.391-1.1c0.886-0.733,1.344-1.711,1.344-2.902C379.715,397.744,379.287,396.888,378.463,396.369z"></path>
			      <path fill="#FFFFFF" d="M304.287,396.033c0,0.886-0.214,1.558-0.611,2.047s-1.008,0.733-1.802,0.733c-0.794,0-1.375-0.244-1.68-0.764c-0.336-0.489-0.489-1.375-0.489-2.566v-4.98h-3.452v6.049c0,3.513,1.65,5.285,4.949,5.285c1.222,0,2.291-0.336,3.269-1.008c0.031,0.122,0.031,0.275,0.031,0.397c0,0.978-0.244,1.802-0.764,2.444s-1.222,0.978-2.077,0.978c-0.855,0-1.558-0.367-2.138-0.978l-2.016,2.413c0.183,0.153,0.367,0.306,0.55,0.428c1.039,0.703,2.261,1.039,3.635,1.039c1.772,0,3.238-0.611,4.369-1.802c1.13-1.191,1.711-2.872,1.711-5.01v-10.265h-3.452v5.56H304.287z"></path>
			      <path fill="#FFFFFF" d="M340.703,399.149c-0.764,0.825-1.864,1.283-3.055,1.13c-1.925-0.244-3.299-2.016-3.055-3.941c0.244-1.925,2.016-3.299,3.941-3.055c0.794,0.092,1.497,0.489,2.047,1.008c0.367-1.039,0.947-1.894,1.68-2.627c-0.917-0.764-2.077-1.283-3.361-1.466c-3.605-0.458-6.935,2.108-7.393,5.713s2.108,6.935,5.713,7.393c1.955,0.244,3.849-0.397,5.224-1.619C341.711,400.982,341.1,400.127,340.703,399.149z"></path>
			      <path fill="#FFFFFF" d="M348.829,390.076c-3.635,0-6.599,2.963-6.599,6.599s2.963,6.599,6.599,6.599s6.599-2.963,6.599-6.599S352.465,390.076,348.829,390.076z M348.829,400.218c-1.955,0-3.544-1.589-3.544-3.544s1.589-3.544,3.544-3.544c1.955,0,3.544,1.589,3.544,3.544S350.784,400.218,348.829,400.218z"></path>
			      <rect x="291.12" y="386.196" fill="#FFFFFF" width="3.544" height="16.589"></rect>
			   </g>
			</svg>
			<span>Copyright ® 2017 Grupo Peñaflor - All rights reserved<br/><a href="#">Terms and Conditions</a> - <a href="#">Privacy</a></span>
		</div>
	</div>
</div>'

<!-- FIN BLOQUES -->


<!-- Menu Widget -->
title = 'TAC menu'
type = 'cms_page_link'
theme = 'TAC'
store view = 'TAC'
sort order = '0'
displa on = 'all_pages'
container = 'menu.container'
template = 'CMS Static Block Default Template'
bloque = 'tac-menu-block'


<!-- Slider Widget -->
title = 'TAC Slider'
type = 'cms_page_link'
theme = 'TAC'
store view = 'TAC'
sort order = '0'
displa on = 'pages'
page = 'cms_index_index'
container = 'header.slidershow'
template = 'CMS Static Block Default Template'
bloque = 'tac-slider'


<!-- Shop Our Wines Widget -->
title = 'TAC Our Wines'
type = 'cms_page_link'
theme = 'TAC'
store view = 'TAC'
sort order = '0'
displa on = 'pages'
page = 'cms_index_index'
container = 'content.top'
template = 'CMS Static Block Default Template'
bloque = 'tac-shop-our-wines-block'

<!-- Widget Largo home -->
title = 'TAC Bloque Largo Home'
type = 'cms_page_link'
theme = 'TAC'
store view = 'TAC'
sort order = '2'
displa on = 'pages'
page = 'cms_index_index'
container = 'pt.perspective'
template = 'CMS Static Block Default Template'
bloque = 'tac-large-block-home'


<!-- Widget Medio home -->
title = 'TAC Bloques medios Home'
type = 'cms_page_link'
theme = 'TAC'
store view = 'TAC'
sort order = '3'
displa on = 'pages'
page = 'cms_index_index'
container = 'pt.perspective'
template = 'CMS Static Block Default Template'
bloque = 'tac-middle-blocks-home'


<!-- Widget Medio home -->
title = 'TAC Product list home''
type = 'cms_page_link'
theme = 'TAC'
store view = 'TAC'
sort order = '4'
displa on = 'pages'
page = 'cms_index_index'
container = 'pt.perspective'
template = 'CMS Static Block Default Template'
bloque = 'tac-product-list-home'


<!-- Footer links Widget -->
title = 'TAC Footer'
type = 'cms_page_link'
theme = 'TAC'
store view = 'TAC'
sort order = '0'
displa on = 'pages'
page = 'cms_index_index'
container = 'footer.container.wrapper'
template = 'CMS Static Block Default Template'
bloque = 'tac-footer'
