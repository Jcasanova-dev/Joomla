<?php
defined('_JEXEC') or die('Restricted access');

class plgHikashopCheckout_payment_credit extends JPlugin {
    public function onCheckoutStepList(&$list) {
        $list['plg.payment.credit'] = array(
            'name' => JText::_('HIKASHOP_PAYMENT_CREDIT'),
            'params' => array()
        );
    }

    // Esta función se ejecuta cuando se accede al controlador del plugin
    public function onHikashopPluginController($ctrl) {
        if ($ctrl != 'checkout_payment_credit') {
            return;
        }
    
        $app = JFactory::getApplication();
        $isAdmin = version_compare(JVERSION, '4.0', '<') ? $app->isAdmin() : $app->isClient('administrator');
    
        if (!$isAdmin) {
            return;
        }
    
        try {
            $this->loadLanguage('plg_hikashop_checkout_payment_credit', JPATH_ADMINISTRATOR);
        } catch (Exception $e) {
            // Manejar errores de carga de idioma aquí si es necesario
            JFactory::getApplication()->enqueueMessage(JText::_('LANGUAGE_LOAD_ERROR'), 'error');
        }
    
        return array(
            'type' => 'hikashop',
            'name' => 'checkout_payment_credit',
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
                    'name' => 'Checkout Payment Credit',
                    'component' => 'plg_hikashop_checkout_payment_credit',
                    'view' => dirname(__FILE__) . DS . 'views' . DS
                );
                break;
        }
    }  
}
