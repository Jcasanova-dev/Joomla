<?php
/**
 * @package    Checkout Payment Credit plugin for Joomla! HikaShop
 * @version    1.0.0
 * @author     Tu Nombre
 * @copyright  (C) Año-Actual Tu Empresa. Todos los derechos reservados.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');

class Checkout_payment_creditViewCheckout_payment_credit extends HikaShopView {
    var $type = 'main';
    var $ctrl = 'checkout_payment_credit';
    var $module = false;
    public function display($tpl = null, $params = array()) {
        $this->paramBase = HIKASHOP_COMPONENT.'.'.$this->getName();
        $function = $this->getLayout();
        $this->params =& $params;

        if(method_exists($this, $function))
            $this->$function();
        parent::display($tpl);
    }

    public function payment_credit() {
        $app = JFactory::getApplication();
        $hikashop_config = hikashop_config();
    
        $tmpl = hikaInput::get()->getCmd('tmpl', '');
        $this->ajax = (in_array($tmpl, array('ajax', 'raw')));
    
        if ($hikashop_config->get('checkout_legacy', 0)) {
            $this->cart = $this->params->view->initCart();
            $this->legacy = true;
        } else {
            $this->checkoutHelper = hikashopCheckoutHelper::get();
            $this->cart = $this->params->view->checkoutHelper->getCart();
    
            $this->step = $this->params->view->step;
            $this->module_position = $this->params->pos;
            $this->options = $this->params->options;
        }
    
        $this->data = array();
        $app->triggerEvent('onCheckoutPaymentCreditDisplay', array(&$this->cart, &$this->data));
    }
    
}
