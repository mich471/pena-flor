var config = {
  map: {
        "*": {
            "cdz_slider": "js/owlcarousel/owlslider",
            "modal" : "Magento_Ui/js/modal/modal",
			"cdz_menu": "js/menu/cdz_menu",
            "isotope":  "js/isotope.pkgd.min",
            // "mage/validation":"js/validation",
            // "jquery/validate": "js/jquery.validate",
            "slick": "Magento_Theme/js/slick.min"
            }
    },
    paths:  {
        "owlslider" : "js/owlcarousel/owl.carousel.min",

    },
    "shim": {
    "js/owlcarousel/owl.carousel.min": ["jquery"]
  },
  deps: [
        "Magento_Theme/js/fastest",
        "Magento_Theme/js/fastest_jewelry"
    ]
  
};

