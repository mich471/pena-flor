# Lyracons_WebsiteRestrictionsExceptions

Agrega excepciones necesarias al activar Magento_WebsiteRestrictions ya que
algunos módulos de terceros (MercadoPago) no tienen en cuenta esta funcionalidad.

## Referencias

Agregar acá links a los tickets de Redmine que correspondan o a la documentación en Drive

* [#48070](https://lyracons.easyredmine.com/issues/48070) - Mercado Pago / Método Custom / No se acreditan las órdenes para sitio VI

### Extensiones

El módulo contiene un archivo `etc/websiterestrictions.xml` donde indicar las
rutas que deben ser exceptuadas.

```xml
<config>
    <!-- Explicitly allow access to MercadoPago IPN when website restrictions are enabled -->
    <action path="mercadopago_core_notifications_basic" type="generic"/>
    <action path="mercadopago_core_notifications_custom" type="generic"/>
</config>
```

En cada caso se debe indicar el full action name (no el route). Para obtenerlo
se puede consultar al objeto request desde el controller correspondiente

```php
public function execute()
{
    echo $this->getRequest()->getFullActionName();
    die();
    // ...
}
```

En caso de incorporar nuevas excepciones, acompañar las mismas de un comentario
indicando el módulo que incorpora las rutas excepcuadas y una breve 
explicación de la razón.
