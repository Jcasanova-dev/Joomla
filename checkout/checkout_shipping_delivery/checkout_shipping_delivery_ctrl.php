<?php
defined('_JEXEC') or die('Restricted access');

class plgHikashopCheckout_shipping_delivery extends JPlugin {
    public function onCheckoutStepList(&$list) {
        $list['plg.delivery.shipping'] = array(
            'name' => JText::_('HIKASHOP_SHIPPING_DELIVERY'),
            'params' => array()
        );
    }

    public function onHikashopPluginController($ctrl) {
        if ($ctrl != 'checkout_shipping_delivery') {
            return;
        }

        $app = JFactory::getApplication();
        $isAdmin = version_compare(JVERSION, '4.0', '<') ? $app->isAdmin() : $app->isClient('administrator');

        if (!$isAdmin) {
            return;
        }

        try {
            $this->loadLanguage('plg_hikashop_checkout_shipping_delivery', JPATH_ADMINISTRATOR);
        } catch (Exception $e) {
            // Manejar errores de carga de idioma aquí si es necesario
        }

        return array(
            'type' => 'hikashop',
            'name' => 'checkout_shipping_delivery',
            'prefix' => 'ctrl'
        );
    }

    public function onViewsListingFilter(&$views, $client) {
        switch ($client) {
            case 1:
                break;
            case 0:
            default:
                $views[] = array(
                    'client_id' => 0,
                    'name' => 'Checkout Shipping Delivery',
                    'component' => 'plg_hikashop_checkout_shipping_delivery',
                    'view' => dirname(__FILE__) . DS . 'views' . DS
                );
                break;
        }
    }

    public function onAfterOrderCreate(&$order) {
        // Verificar si se ha enviado el formulario
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Obtener la opción seleccionada del select
            $selectedShippingId = hikaInput::get()->getInt('shipping_method', 0);
        
            // Obtener el nombre del método de envío
            $shippingMethods = $this->getShippingMethods();
            $selectedShippingName = '';
            $shippingPrice = 0; // Valor predeterminado
            foreach ($shippingMethods as $shippingMethod) {
                if ($shippingMethod->shipping_id == $selectedShippingId) {
                    $selectedShippingName = $shippingMethod->shipping_name;
                    $shippingPrice = $shippingMethod->shipping_price;
                    break;
                }
            }
        
            // Verificar si hay un pedido en curso
            if ($order->cart->order_id) {
                // Asignar el shipping_id al campo personalizado del pedido
                $order->cart->order_custom->shipping_id = $selectedShippingId;
                // Asignar el shipping_name al campo personalizado del pedido
                $order->cart->order_custom->shipping_name = $selectedShippingName;
                // Asignar el precio del envío al pedido
                $order->order_shipping_price = $shippingPrice;
        
                // Guardar el pedido para que se actualicen los cambios
                $order->saveCart();
            }
        }
    }

    public function getShippingMethods() {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        // Selecciona las columnas "shipping_id", "shipping_name" y "shipping_price" en la consulta SQL
        $query->select($db->quoteName(array('shipping_id', 'shipping_name', 'shipping_price')));
        $query->from($db->quoteName('josmwt_hikashop_shipping'));

        $db->setQuery($query);
        $results = $db->loadObjectList();
        return $results;
    }
}
