# Module developed by BalloonGroup

## Developer: Bruno Del Piero

## Function:
    adds the option to continue as guest to magento's authentication popup

# system.xml:
    adds the tab Balloon Group to stores->configuration
    adds the section Allow Guest Checkout Popup which enables the module's functionality

# Plugins:
## BalloonGroup\AllowGuestCheckoutPopup\Plugin\JsLayoutModifier:
    replaces js layout for authentication-popup.html if the functionality is enabled for the current scope

# View Overrides:

# File: layout/checkout_cart_index.xml
## Function:
    replaces the template for checkout.cart.methods.onepage.bottom (block) only if the configuration is set for that scope.

# File: templates/onepage/link.phtml
## Function:
    replaces the js layout "js/proceed-to-checkout".

# File: web/js/proceed-to-checkout.js
## Function:
    modifies the logic that triggers the authentication popup.
    it triggers only when there is no signed up user.

# File: web/js/view/authentication-popup.js
## Function:
    replcaes the html template of the component.
    adds the function proceedToCheckout (which triggers a redirect to the checkout page).

# File: web/template/authentication-popup.html 
## Function:
    adds an extra button which calls the proceedToCheckout function when clicked.