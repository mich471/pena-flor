<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Enable, adjust and copy this code for each store you run
 *
 * Store #0, default one
 *
 * @param string $host
 * @return bool
 */
function isHttpHost(string $host)
{
    if (!isset($_SERVER['HTTP_HOST'])) {
        return false;
    }
    return $_SERVER['HTTP_HOST'] === $host;
}

switch (true) {
    case isHttpHost("mascota.integration-5ojmyuq-zecpriw777svu.us-5.magentosite.cloud"):
    case isHttpHost("mcstaging.mascotavineyards.com.ar"):
    case isHttpHost("mcprod.mascotavineyards.com.ar"):
        $_SERVER["MAGE_RUN_CODE"] = "mascota";
        $_SERVER["MAGE_RUN_TYPE"] = "store";
        break;

    case isHttpHost("penaflor.magento2.docker"):
    case isHttpHost("penaflor.integration-5ojmyuq-zecpriw777svu.us-5.magentosite.cloud"):
    case isHttpHost("penaflor.rediseno-vys-qo2kjka-zecpriw777svu.us-5.magentosite.cloud"):
    case isHttpHost("mcstaging.vinosyspirits.com"):
    case isHttpHost("mcprod.vinosyspirits.com"):
    case isHttpHost("www.vinosyspirits.com"):
    case isHttpHost("vinosyspirits.com"):
    case isHttpHost("www.vinosyspirits.com.ar"):
    case isHttpHost("vinosyspirits.com.ar"):
        $_SERVER["MAGE_RUN_CODE"] = "penaflor";
        $_SERVER["MAGE_RUN_TYPE"] = "store";
        break;

    case isHttpHost("tac.integration-5ojmyuq-zecpriw777svu.us-5.magentosite.cloud"):
    case isHttpHost("mcstaging.eu.theargentinecellar.com"):
    case isHttpHost("mcprod.eu.theargentinecellar.com"):
    case isHttpHost("eu.theargentinecellar.com"):
        $_SERVER["MAGE_RUN_CODE"] = "tac";
        $_SERVER["MAGE_RUN_TYPE"] = "store";
        break;

    case isHttpHost("ventainstitucional.magento2.docker"):
    case isHttpHost("ventainstitucional.integration-5ojmyuq-zecpriw777svu.us-5.magentosite.cloud"):
    
        case isHttpHost("mcstaging.vi.grupo-penaflor.com.ar"):
    case isHttpHost("mcprod.vi.grupo-penaflor.com.ar"):
    case isHttpHost("vi.grupo-penaflor.com.ar"):
    case isHttpHost("vi.grupopenaflor.com.ar"):
        $_SERVER["MAGE_RUN_CODE"] = "ventainstitucional";
        $_SERVER["MAGE_RUN_TYPE"] = "store";
        break;
}