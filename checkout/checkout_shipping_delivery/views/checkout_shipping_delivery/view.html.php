<?php
/**
 * @package    Checkout Shipping Delivery plugin for Joomla! HikaShop
 * @version    1.0.0
 * @author     Obsidev S.A.R.L.
 * @copyright  (C) 2011-2022 OBSIDEV. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php
class Checkout_shipping_deliveryViewCheckout_shipping_delivery extends HikaShopView {
	var $type = 'main';
	var $ctrl = 'checkout_shipping_delivery';
	var $module = false;

	public function display($tpl = null, $params = array()) {
		$this->paramBase = HIKASHOP_COMPONENT.'.'.$this->getName();
		$function = $this->getLayout();
		$this->params =& $params;

		if(method_exists($this, $function))
			$this->$function();
		parent::display($tpl);
	}

	public function shipping_delivery() {
		$app = JFactory::getApplication();
		$hikashop_config = hikashop_config();

		$tmpl = hikaInput::get()->getCmd('tmpl', '');
		$this->ajax = (in_array($tmpl, array('ajax', 'raw')));

		if($hikashop_config->get('checkout_legacy', 0)) {
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
		$app->triggerEvent('onCheckoutShippingDeliveryDisplay', array(&$this->cart, &$this->data));
	}
}
