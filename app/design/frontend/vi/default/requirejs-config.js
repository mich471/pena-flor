var config = {
  map: {
        "*": {
			"cdzZoom": "Magento_Catalog/js/cdzZoom",
            /**
             * #46019 - en VI, a diferencia de otros sites, falla en encontrar transparent.js sin este mapeo,
             * Omite en el path "Magento_Payment/js"
             * Workaround a remover si entendemos por qué en este caso es necesario y los demás sitios no.
             * Mapeo copiado de la implementación de Paypal.
             */
            transparent: 'Magento_Payment/js/transparent',
            'Magento_Payment/transparent': 'Magento_Payment/js/transparent',
		//	"mage/validation":"js/validation",
		//	"jquery/validate": "js/jquery.validate"
        }
    }
};
