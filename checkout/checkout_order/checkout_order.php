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
        if ($ctrl == 'checkout_order') {
            $app = JFactory::getApplication();
            $isAdmin = version_compare(JVERSION, '4.0', '<') ? $app->isAdmin() : $app->isClient('administrator');
    
            if ($isAdmin) {
                try {
                    $this->loadLanguage('plg_hikashop_checkout_order', JPATH_ADMINISTRATOR);
                } catch (Exception $e) {
                }
                return array(
                    'type' => 'hikashop',
                    'name' => 'checkout_order',
                    'prefix' => 'ctrl'
                );
            }
        }
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
    public function onCheckoutStepDisplay($layoutName, &$html, &$view, $pos = null, $options = null) {
        if ($layoutName != 'plg.order') {
            return;
        }
        $session = JFactory::getSession();
        $userId = $session->get('user')->id;
    }
    
    private function displayView($layout, $params = array()) {
		$app = JFactory::getApplication();
		$doc = JFactory::getDocument();
		$base_path = rtrim(dirname(__FILE__),DS);
		$controller = new hikashopBridgeController(array(
			'name' => 'checkout_order',
			'base_path' => $base_path
		));
		$viewType = $doc->getType();
		if(empty($viewType))
			$viewType = 'html';
		$view = $controller->getView( '', $viewType, '', array('base_path' => $base_path));
		$folder	= $base_path.DS.'views'.DS.$view->getName().DS.'tmpl';
		$view->addTemplatePath($folder);
		$folder	= JPATH_BASE.DS.'templates'.DS.$app->getTemplate().DS.'html'.DS.HIKASHOP_COMPONENT.DS.$view->getName();
		$view->addTemplatePath($folder);
		$old = $view->setLayout($layout);
		ob_start();
		$view->display(null,$params);
		$js = @$view->js;
		if(!empty($old))
			$view->setLayout($old);
		return ob_get_clean();
	}
}

