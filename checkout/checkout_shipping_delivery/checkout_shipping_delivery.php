<?php
require_once JPATH_ADMINISTRATOR . '/components/com_hikashop/helpers/helper.php';
defined('_JEXEC') or die('Restricted access');

class plgHikashopCheckout_shipping_delivery extends JPlugin {
    public function onCheckoutStepList(&$list) {
        $list['plg.delivery.shipping'] = array(
            'name' => JText::_('HIKASHOP_SHIPPING_DELIVERY'),
            'params' => array()
        );
    }

    public function onHikashopPluginController($ctrl) {
        if ($ctrl == 'checkout_shipping_delivery') {
            $app = JFactory::getApplication();
            $isAdmin = version_compare(JVERSION, '4.0', '<') ? $app->isAdmin() : $app->isClient('administrator');

            if ($isAdmin) {
                try {
                    $this->loadLanguage('plg_hikashop_checkout_shipping_delivery', JPATH_ADMINISTRATOR);
                } catch (Exception $e) {
                }
                return array(
                    'type' => 'hikashop',
                    'name' => 'checkout_shipping_delivery',
                    'prefix' => 'ctrl'
                );
            }
        }
    }
    public function onCheckoutStepDisplay($layoutName, &$html, &$view, $pos = null, $options = null) {
        if ($layoutName != 'plg.delivery.shipping') {
            return;
        }
        $session = JFactory::getSession();
        $userId = $session->get('user')->id;
        $shippingMethods = $this->getShippingMethods($userId);
        $html .= '<form method="post" action="" id="shippingForm">'; 
        $html .= '<select name="shipping_method" id="shipping_method">';
        foreach ($shippingMethods as $shippingMethod) {
            $html .= '<option value="' . $shippingMethod->shipping_id . '">' . $shippingMethod->shipping_name . ' $' . $shippingMethod->shipping_price . '</option>';
        }
        $html .= '</select>';
        $html .= '</form>';
    }
    
    public function onAfterCheckoutStep($step, $controller, $method) {
        if ($step == 'shipping') {
            $selectedShippingId = JFactory::getApplication()->input->getInt('shipping_method', 0);
            if ($selectedShippingId > 0) {
                $session = JFactory::getSession();
                $session->set('selected_shipping_method', $selectedShippingId);
            } else {
                throw new Exception('No se seleccionó ningún método de envío.');
            }
        }
    }
    public function OnAfterOrderCreate(&$order) {
        if ($order && isset($order->order_id)) {
            $session = JFactory::getSession();
            $selectedShippingId = $session->get('selected_shipping_method', 0);
            if ($selectedShippingId > 0) {
                $shippingMethods = $this->getShippingMethods($selectedShippingId);
                $selectedShippingMethod = $shippingMethods[0]->shipping_name;
                $selectedShippingPrice = $shippingMethods[0]->shipping_price; 
                if (!empty($shippingMethods)) {
                    try {
                        $db = JFactory::getDbo();
                        $db->transactionStart();
                        $query = $db->getQuery(true);
                        $fields = array(
                            $db->quoteName('order_shipping_method') . ' = ' . $db->quote($selectedShippingMethod),
                            $db->quoteName('order_shipping_price') . ' = ' . (float)$selectedShippingPrice,
                        );
                        $conditions = array(
                            $db->quoteName('order_id') . ' = ' . (int)$order_id
                        );
                        $query->update($db->quoteName('josmwt_hikashop_order'))
                            ->set($fields)
                            ->where($conditions);
                        $db->setQuery($query);
                        if (!$db->execute()) {
                            throw new Exception('La actualización de la orden falló.');
                        }
                        $db->transactionCommit();
                    } catch (Exception $e) {
                        $db->transactionRollback();
                        throw new Exception($e->getMessage());
                    }
                } else {
                    throw new Exception('No se encontraron métodos de envío con el ID proporcionado.');
                }
            } else {
                throw new Exception('No se seleccionó ningún método de envío.');
            }
        } else {
            throw new Exception('No se encontró el ID de la orden en la base de datos.');
        }
    }
    public function getShippingMethods($userId) {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select($db->quoteName(array('c.customer_id', 'c.customer_name', 's.shipping_id', 's.shipping_name', 's.shipping_price')));
        $query->from($db->quoteName('josmwt_customer_shipping', 'cs'));
        $query->leftJoin($db->quoteName('josmwt_customer', 'c') . ' ON (' . $db->quoteName('cs.customer_id') . ' = ' . $db->quoteName('c.customer_id') . ')');
        $query->leftJoin($db->quoteName('josmwt_hikashop_shipping', 's') . ' ON (' . $db->quoteName('cs.shipping_id') . ' = ' . $db->quoteName('s.shipping_id') . ')');
        $query->leftJoin($db->quoteName('josmwt_users', 'u') . ' ON (' . $db->quoteName('cs.customer_id') . ' = ' . $db->quoteName('u.customer_id') . ')');
        $query->where($db->quoteName('u.id') . ' = ' . (int)$userId);
        $db->setQuery($query);
        $results = $db->loadObjectList();
        return $results;
    }
    private function displayView($layout, $params = array()) {
        $app = JFactory::getApplication();
        $doc = JFactory::getDocument();

        $base_path = rtrim(dirname(__FILE__), DS);

        $controller = new hikashopBridgeController(array(
            'name' => 'checkout_shipping_delivery',
            'base_path' => $base_path
        ));

        $viewType = $doc->getType();
        if (empty($viewType))
            $viewType = 'html';

        $view = $controller->getView('', $viewType, '', array('base_path' => $base_path));

        $folder = $base_path . DS . 'views' . DS . $view->getName() . DS . 'tmpl';
        $view->addTemplatePath($folder);

        $folder = JPATH_BASE . DS . 'templates' . DS . $app->getTemplate() . DS . 'html' . DS . HIKASHOP_COMPONENT . DS . $view->getName();
        $view->addTemplatePath($folder);

        $old = $view->setLayout($layout);

        ob_start();
        $view->display(null, $params);

        $js = @$view->js;
        if (!empty($old))
            $view->setLayout($old);
        return ob_get_clean();
    }
}
?>
