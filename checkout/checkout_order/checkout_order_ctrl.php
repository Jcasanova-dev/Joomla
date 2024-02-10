<?php
defined('_JEXEC') or die('Restricted access');

class plgHikashopCheckout_order extends JPlugin {
    public function onCheckoutStepList(&$list) {
        $list['plg.order'] = array(
            'name' => JText::_('HIKASHOP_ORDER'),
            'params' => array()
        );
    }

    public function onHikashopPluginController($ctrl) {
        if ($ctrl != 'checkout_order') {
            return;
        }

        $app = JFactory::getApplication();
        $isAdmin = version_compare(JVERSION, '4.0', '<') ? $app->isAdmin() : $app->isClient('administrator');

        if (!$isAdmin) {
            return;
        }

        try {
            $this->loadLanguage('plg_hikashop_checkout_order', JPATH_ADMINISTRATOR);
        } catch (Exception $e) {
            // Manejar errores de carga de idioma aquí si es necesario
        }

        return array(
            'type' => 'hikashop',
            'name' => 'checkout_order',
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
                    'name' => 'Checkout Order',
                    'component' => 'plg_hikashop_checkout_order',
                    'view' => dirname(__FILE__) . DS . 'views' . DS
                );
                break;
        }
    }
}
