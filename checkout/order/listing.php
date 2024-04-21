<?php
/**
 * @package	HikaShop for Joomla!
 * @version	5.0.2
 * @author	hikashop.com
 * @copyright	(C) 2010-2024 HIKARI SOFTWARE. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><div id="hikashop_order_listing">
<?php
	$url_itemid = (!empty($this->Itemid) ? '&Itemid=' . $this->Itemid : '');
	echo $this->toolbarHelper->process($this->toolbar, $this->title);
?>
<form action="<?php echo hikashop_completeLink('order'.$url_itemid); ?>" method="post" name="adminForm" id="adminForm">

<div class="hk-row-fluid">
	<div class="hkc-md-6">
		<div class="hikashop_search_block <?php echo HK_GROUP_CLASS; ?>">
			<input type="text" name="search" id="hikashop_search" value="<?php echo $this->escape($this->pageInfo->search);?>" placeholder="<?php echo JText::_('HIKA_SEARCH'); ?>" class="<?php echo HK_FORM_CONTROL_CLASS; ?>" onchange="this.form.submit();" />
			<button class="<?php echo HK_CSS_BUTTON; ?> <?php echo HK_CSS_BUTTON_PRIMARY; ?>" onclick="this.form.submit();"><?php echo JText::_('GO'); ?></button>
<?php
	foreach($this->leftFilters as $name => $filterObj) {
		if(is_string($filterObj))
			echo $filterObj;
		else
			echo $filterObj->displayFilter($name, $this->pageInfo->filter);
	}
?>		</div>
	</div>
	<div class="hkc-md-6">
		<div class="hikashop_order_sort"><?php
	foreach($this->rightFilters as $name => $filterObj) {
		if(is_string($filterObj))
			echo $filterObj;
		else
			echo $filterObj->displayFilter($name, $this->pageInfo->filter);
	}
?>
		</div>
	</div>
</div>

<div class="hikashop_order_listing">
	<div class="hikashop_orders_content">

<?php
	
		$cancel_orders = false;
		$print_invoice = false;
		$cancel_url = '&cancel_url='.base64_encode(hikashop_currentURL());
		$orderIdsToShowDetails = array();
		$i = 0;
		$k = 0;
		$session = JFactory::getSession();
		$userId = $session->get('user')->id;
		$user_id_resultado = getUserById($userId);
		$customerUsers = getCustomerUser($userId);
		$orderIdsToShowDetails = array();
		$processedOrderIds = array();
		if (!empty($customerUsers)) {
			foreach ($customerUsers as $customerUser) {
				$orders = getOrdersByCustomerOrUser($customerUser['customer_id'], $user_id_resultado);
				foreach($orders as $row) {
					if (!in_array($row->order_id, $processedOrderIds)) {
						$order_link = hikashop_completeLink('order&task=show&cid='.$row->order_id.$url_itemid.$cancel_url);	
						$orderIdsToShowDetails[] = $row->order_id;
						$processedOrderIds[] = $row->order_id;
						
?>
		<div class="hk-card hk-card-default hk-card-order" data-order-container="<?php echo (int)$row->order_id; ?>">
			<div class="hk-card-header">
				<a class="hk-row-fluid" href="<?php echo $order_link; ?>">

					<div class="hkc-sm-6 hika_cpanel_date">
<!-- ORDER DATE -->
						<i class="fa fa-clock"></i>
						<?php echo hikashop_getDate((int)$row->order_created, '%d %B %Y %H:%M'); ?>
<!-- EO ORDER DATE -->
					</div>
					<div class="hkc-sm-6 hika_cpanel_price">
<!-- ORDER TOTAL -->
						<i class="fa fa-credit-card"></i>
						<?php echo $this->currencyClass->format($row->order_full_price, $row->order_currency_id); ?>
<!-- EO ORDER TOTAL -->
					</div>
				</a>
			</div>
<!-- END GRID -->
			<div class="hk-card-body">
				<div class="hk-row-fluid">
					<div class="hkc-sm-4 hika_order_left_div">
<!-- TOP LEFT EXTRA DATA -->
<?php if(!empty($row->extraData->topLeft)) { echo implode("\r\n", $row->extraData->topLeft); } ?>
<!-- EO TOP LEFT EXTRA DATA -->
<style>
.invoice-link {
 color: black; /* text color when not hovered */
 font-family: 'PT Serif', serif; /* font type */
}

.invoice-link:hover {
 color: purple; /* text color when hovered */
}
.hika_order_number{
 color: black; /* text color when not hovered */
 font-family: 'PT Serif', serif; /* font type */
}

.hika_order_number:hover {
 color: purple; /* text color when hovered */
}
</style>
<!-- ORDER NUMBER -->
<?php if (!empty($order_link)) { ?>
							<a class="hika_order_number" href="<?php echo $order_link; ?>">
							<span class="hika_order_number_title"><?php echo  JText::_('ORDER_NUMBER'); ?> : </span>
							<span class="hika_order_number_value"><?php echo $row->order_number; ?></span>
							</a>
							<?php }?>
							<?php $number_invoice = getinvoice($row->order_number); ?>
							


<?php if(!empty($number_invoice[0]->number_invoice)) { ?>
							<?php $order_number = $row->order_number;
							$number_invoice = $number_invoice[0]->number_invoice;
							$filePathRelative2 = getUploadedFiles8($order_number, $number_invoice); ?>
							  <a href="#" onclick="mostrarArchivo2('<?php echo $filePathRelative2; ?>')" class="invoice-link">
							<span class="hika_invoice_number_title"><?php echo JText::_('INVOICE_NUMBER'); ?> : </span>
							<span class="hika_invoice_number_value"><?php echo $number_invoice; ?></span>
							</a>
							
<?php } ?>
						
<!-- ORDER PURCHASE -->			
<?php $purchase = getNumber($row->order_number);
$order_number = $row->order_number;
$filePathRelative = getUploadedFiles($order_number); ?>
<?php if (!empty($purchase['number_purchase'])) { ?>
    <a class="hika_order_number" href="#" onclick="mostrarArchivo('<?php echo $filePathRelative; ?>')">
        <span class="hika_order_number_title"><?php echo JText::_('OC Number'); ?> : </span>
        <span class="hika_sap_value"><?php echo $purchase['number_purchase']; ?></span>
    </a>
<?php } ?>
<?php $sapData = getsap($row->order_number); ?>
<!-- ORDER SAP -->
<?php if (!empty($sapData['number_sap'])) { ?>
    <a class="hika_order_number2" style="color: black;">
        <span class="hika_order_number_title"><?php echo JText::_('SAP Number'); ?> : </span>
        <span class="hika_sap_value"><?php echo $sapData['number_sap']; ?></span>
    </a>
<?php } ?>
<!-- EO ORDER SAP -->
<!-- ORDER PROFORMA -->
<?php if (!empty($sapData['number_preforma'])) { 
	$order_number = $row->order_number;
	$filePathRelative3= getUploadedFiles($order_number);
	?>
    <a class="hika_order_number" href="#" onclick="mostrarArchivo3('<?php echo $filePathRelative3; ?>')">
        <span class="hika_order_number_title"><?php echo JText::_('Proforma Number'); ?> : </span>
        <span class="hika_sap_value"><?php echo $sapData['number_preforma']; ?></span>
		<br>
    </a>
<?php } ?>
<!-- EO Guia Number -->
<?php $number_guia = getUploadedFiles5($row->order_number); ?>
<?php if(!empty($number_guia[0]->number_guia)) { ?>
							<?php $order_number = $row->order_number;
							$guia= $number_guia[0]->number_guia;
							$filePathRelative4 = getUploadedFiles6($order_number, $guia); ?>
							<a class="hika_order_number" href="#" onclick="mostrarArchivo4('<?php echo $filePathRelative4; ?>')">
							<span class="hika_delivery_number_title"><?php echo JText::_('Delivery Number'); ?> : </span>
							<span class="hika_delivery_number_value"><?php echo $guia; ?></span>
							</a>
							
<?php } ?>
<!-- EO ORDER NUMBER -->
<!-- BOTTOM LEFT EXTRA DATA -->
<?php if(!empty($row->extraData->bottomLeft)) { echo implode("\r\n", $row->extraData->bottomLeft); } ?>
<!-- EO BOTTOM LEFT EXTRA DATA -->
					</div>
					<div class="hkc-sm-3 hika_order_info">
<!-- BEFORE INFO EXTRA DATA -->
<?php if(!empty($row->extraData->beforeInfo)) { echo implode("\r\n", $row->extraData->beforeInfo); } ?>
<!-- EO BEFORE INFO EXTRA DATA -->
<!-- SHIPPING ADDRESS -->
<?php if(!empty($row->order_shipping_address_id) && !empty($this->address_data[(int)$row->order_shipping_address_id])) { ?>
						<div class="hika_order_shipping_address" data-toggle="hk-tooltip" data-title="<?php echo $this->escape($this->address_html[(int)$row->order_shipping_address_id]); ?>">
							<div class="hika_order_shipping_address_title"><?php echo JText::_('HIKA_LISTING_ORDER_SHIP'); ?></div>
							<span class="hika_order_shipping_address_value">
								<i class="fas fa-map-marker-alt"></i>
								<?php echo $this->address_data[(int)$row->order_shipping_address_id]->address_firstname . ' ' . $this->address_data[(int)$row->order_shipping_address_id]->address_lastname; ?>
							</span>
						</div>
<?php } ?>
<!-- EO SHIPPING ADDRESS -->
<!-- AFTER INFO EXTRA DATA -->
<?php if(!empty($row->extraData->afterInfo)) { echo implode("\r\n", $row->extraData->afterInfo); } ?>
<!-- EO AFTER INFO EXTRA DATA -->
					</div>
					<div class="hkc-sm-2 hika_order_status">
<!-- TOP MIDDLE EXTRA DATA -->
<?php if(!empty($row->extraData->topMiddle)) { echo implode("\r\n", $row->extraData->topMiddle); } ?>
<!-- EO TOP MIDDLE EXTRA DATA -->
<!-- ORDER STATUS -->
						<span class="order-label order-label-<?php echo preg_replace('#[^a-z_0-9]#i', '_', str_replace(' ','_', $row->order_status)); ?>"><?php
							echo hikashop_orderStatus($row->order_status);
						?></span>
<!-- EO ORDER STATUS -->
<!-- BOTTOM MIDDLE EXTRA DATA -->
<?php if(!empty($row->extraData->bottomMiddle)) { echo implode("\r\n", $row->extraData->bottomMiddle); } ?>
<!-- EO BOTTOM MIDDLE EXTRA DATA -->
					</div>
					<div class="hkc-sm-2 hika_order_action">
<!-- TOP RIGHT EXTRA DATA -->
<?php if(!empty($row->extraData->topRight)) { echo implode("\r\n", $row->extraData->topRight); } ?>
<!-- EO TOP RIGHT EXTRA DATA -->
<!-- ACTIONS BUTTON -->
<?php
			$dropData = array();
			$dropData[] = array(
				'name' => '<i class="fas fa-search-plus"></i>'.JText::_('HIKA_DETAILS'),
				'link' => $order_link
			);
			$dropData[] = array(
				'name' => '<i class="fas fa-eye"></i> ' . JText::_('VER STATUS'),
				'link' => 'https://mwt.one/index.php?option=com_sppagebuilder&view=page&id=20&order_number=' . $row->order_number,
				'target' => '_blank',
			);	
			if(!empty($row->show_print_button)) {
				$print_invoice = true;
				$dropData[] = array(
					'name' => '<i class="fas fa-print"></i> '. JText::_('PRINT_INVOICE'),
					'link' => '#print_invoice',
					'click' => 'return window.localPage.printInvoice('.(int)$row->order_id.');',
				);
			}
			if(!empty($row->show_cancel_button)) {
				$cancel_orders = true;
				$dropData[] = array(
					'name' => '<i class="fas fa-ban"></i> '. JText::_('CANCEL_ORDER'),
					'link' => '#cancel_order',
					'click' => 'return window.localPage.cancelOrder('.(int)$row->order_id.',\''.$row->order_number.'\');',
				);
			}
			if(!empty($row->show_payment_button) && bccomp(sprintf('%F',$row->order_full_price), 0, 5) > 0) {
				$url_param = ($this->payment_change) ? '&select_payment=1' : '';
				$url = hikashop_completeLink('order&task=pay&order_id='.$row->order_id.$url_param.$url_itemid);
				if($this->config->get('force_ssl',0) && strpos('https://',$url) === false)
					$url = str_replace('http://','https://', $url);
				$dropData[] = array(
					'name' => '<i class="fas fa-money-bill-alt"></i> '. JText::_('PAY_NOW'),
					'link' => $url
				);
			}
			if($this->config->get('allow_reorder', 0)) {
				$url = hikashop_completeLink('order&task=reorder&order_id='.$row->order_id.$url_itemid);
				if($this->config->get('force_ssl',0) && strpos('https://',$url) === false)
					$url = str_replace('http://','https://', $url);
				$dropData[] = array(
					'name' => '<i class="fas fa-redo-alt"></i> '. JText::_('REORDER'),
					'link' => $url
				);
			}
			if(!empty($row->show_contact_button)) {
				$url = hikashop_completeLink('order&task=contact&order_id='.$row->order_id.$url_itemid);
				$dropData[] = array(
					'name' => '<i class="far fa-envelope"></i> '. JText::_('CONTACT_US_ABOUT_YOUR_ORDER'),
					'link' => $url
				);
			}

			if(!empty($row->actions)) {
				$dropData = array_merge($dropData, $row->actions);
			}

			if(!empty($dropData)) {
				echo $this->dropdownHelper->display(
					JText::_('HIKASHOP_ACTIONS'),
					$dropData,
					array('type' => 'btn',  'right' => true, 'up' => false)
				);
			}
?>
<!-- EO ACTIONS BUTTON -->
<!-- BOTTOM RIGHT EXTRA DATA -->
<?php if(!empty($row->extraData->bottomRight)) { echo implode("\r\n", $row->extraData->bottomRight); } ?>
<!-- EO BOTTOM RIGHT EXTRA DATA -->
					</div>
					<div class="hkc-sm-1 hika_order_more">
<!-- PRODUCTS LISTING BUTTON -->

<?php  if(isset($row->order_id)) { ?>
	<a class="hikabtn hikabtn-default" data-toggle="hk-tooltip" data-title="<?php echo $this->escape(JText::_('DISPLAY_PRODUCTS')); ?>" href="#" onclick="return window.localPage.handleDetails(this, <?php echo (int)$row->order_id; ?>);"><i class="fas fa-angle-down"></i></a>		
<?php } else {?>
	<a class="hikabtn hikabtn-default " data-toggle="hk-tooltip" data-title="<?php echo $this->escape(JText::_('HIDE_PRODUCTS')); ?>" href="#" onclick="return window.localPage.handleDetails(this, <?php echo (int)$row->order_id; ?>);"><i class="fas fa-angle-up"></i></a>				
<?php } ?>
<!-- EO PRODUCTS LISTING BUTTON -->
					</div>
				</div>
			</div>
<!-- END GRID -->
<?php
	if (!empty($row) && !empty($this->row) && isset($row->order_id) && isset($this->row->order_id) && $row->order_id == $this->row->order_id) {
		$this->setLayout('order_products');
		echo $this->loadTemplate();
	}
?>
		</div>
<?php 
		 }
		}
	}
			$i++;
			$k = 1 - $k;
		}
		unset($row);
?>
<!-- PAGINATION -->
		<div class="hikashop_orders_footer">
			<div class="pagination">
				<?php $this->pagination->form = '_bottom'; echo $this->pagination->getListFooter(); ?>
				<?php echo '<span class="hikashop_results_counter">'.$this->pagination->getResultsCounter().'</span>'; ?>
			</div>
		</div>
<!-- EO PAGINATION -->

	</div>

	<input type="hidden" name="Itemid" value="<?php echo $this->Itemid; ?>"/>
	<input type="hidden" name="option" value="<?php echo HIKASHOP_COMPONENT; ?>" />
	<input type="hidden" name="task" value="listing" />
	<input type="hidden" name="ctrl" value="<?php echo hikaInput::get()->getCmd('ctrl'); ?>" />
	<input type="hidden" name="boxchecked" value="0" />
	<?php echo JHTML::_('form.token'); ?>
</div>
</form>
<script type="text/javascript">
if(!window.localPage) window.localPage = {};
window.localPage.handleDetails = function(btn, id) {
	var d = document, details = d.getElementById('hika_order_'+id+'_details');

	if(details) {
		details.style.display = (details.style.display == 'none' ? '' : 'none');
		if(details.style.display) {
			btn.innerHTML = '<i class="fas fa-angle-down"></i>';
			btn.setAttribute('data-original-title','<?php echo $this->escape(JText::_('DISPLAY_PRODUCTS')); ?>');
		} else{
			btn.innerHTML = '<i class="fas fa-angle-up"></i>';
			btn.setAttribute('data-original-title','<?php echo $this->escape(JText::_('HIDE_PRODUCTS')); ?>');
		}
		return false;
	}

	return window.localPage.loadOrderDetails(btn, id);
};
window.localPage.loadOrderDetails = function(btn, id) {
	var d = document, o = window.Oby, el = d.querySelector('[data-order-container="'+id+'"]');
	if(!el) return false;
	btn.classList.add('hikadisabled');
	btn.disabled = true;
	btn.blur();
	btn.innerHTML = '<i class="fas fa-spinner fa-pulse"></i>';
	var c = d.createElement('div');
	o.xRequest("<?php echo hikashop_completeLink('order&task=order_products', 'ajax', false, true); ?>", {mode:'POST',data:'cid='+id},function(xhr){
		if(!xhr.responseText || xhr.status != 200) {
			btn.innerHTML = '<i class="fas fa-angle-down"></i>';
			return;
		}
		btn.classList.remove('hikadisabled');
		btn.disabled = false;
		var resp = o.trim(xhr.responseText);
		c.innerHTML = resp;
		el.appendChild(c.querySelector('#hika_order_'+id+'_details'));
		btn.innerHTML = '<i class="fas fa-angle-up"></i>';
		btn.setAttribute('data-original-title','<?php echo $this->escape(JText::_('HIDE_PRODUCTS')); ?>');
	});
	return false;
};
</script>
<?php

if(!empty($this->rows) && ($print_invoice || $cancel_orders)) {
	echo $this->popupHelper->display(
		'',
		'INVOICE',
		hikashop_completeLink('order&task=invoice'.$url_itemid,true),
		'hikashop_print_popup',
		760, 480, '', '', 'link'
	);
?>
<script>
if(!window.localPage) window.localPage = {};
window.localPage.cancelOrder = function(id, number) {
	var d = document, form = d.getElementById('hikashop_cancel_order_form');
	if(!form || !form.elements['order_id']) {
		console.log('Error: Form not found, cannot cancel the order');
		return false;
	}
	if(!confirm('<?php echo JText::_('HIKA_CONFIRM_CANCEL_ORDER', true); ?>'.replace(/ORDER_NUMBER/, number)))
		return false;
	form.elements['order_id'].value = id;
	form.submit();
	return false;
};
window.localPage.printInvoice = function(id) {
	hikashop.openBox('hikashop_print_popup','<?php
		$u = hikashop_completeLink('order&task=invoice'.$url_itemid,true);
		echo $u;
		echo (strpos($u, '?') === false) ? '?' : '&';
	?>order_id='+id);
	return false;
};
</script>
<form action="<?php echo hikashop_completeLink('order&task=cancel_order&email=1'); ?>" name="hikashop_cancel_order_form" id="hikashop_cancel_order_form" method="POST">
	<input type="hidden" name="Itemid" value="<?php echo $this->Itemid; ?>"/>
	<input type="hidden" name="option" value="<?php echo HIKASHOP_COMPONENT; ?>" />
	<input type="hidden" name="task" value="cancel_order" />
	<input type="hidden" name="email" value="1" />
	<input type="hidden" name="order_id" value="" />
	<input type="hidden" name="ctrl" value="order" />
	<input type="hidden" name="redirect_url" value="<?php echo hikashop_currentURL(); ?>" />
	<?php echo JHTML::_('form.token'); ?>
</form>
<?php
}
?>
<?php
//funcion para obtener el numero SAP
function getsap($order_number){
	try {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select($db->quoteName(array('s.number_sap', 's.number_preforma', 's.number_purchase')));
		$query->from($db->quoteName('josmwt_sap', 's'));
		$query->where($db->quoteName('s.order_number') . ' = ' . $db->quote($order_number));
		$db->setQuery($query);
		$fecha = $db->loadAssoc();
		if ($fecha === null) {
			return null;
		}
		return $fecha;
	} catch (Exception $e) {
		JFactory::getApplication()->enqueueMessage(JText::_('ERROR_FETCHING_ORDER_DATA'), 'error');
		return null; 
	}
}
 //funcion para obtener el numero de orden de compra
 function getNumber($order_number) {
    try {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select($db->quoteName('o.number_purchase'));
        $query->from($db->quoteName('josmwt_preforma', 'o'));
        $query->where($db->quoteName('o.order_number') . ' = ' . $db->quote($order_number));
        $db->setQuery($query);
        $numberPurchase = $db->loadResult();

        if ($numberPurchase === null) {
            return null;
        }

        return array('number_purchase' => $numberPurchase);
    } catch (Exception $e) {
        JFactory::getApplication()->enqueueMessage(JText::_('ERROR_FETCHING_ORDER_DATA'), 'error');
        return null;
    }
}
//funcion para tener el numero de factura
function getinvoice($order_number) {
	try {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('i.number_invoice');
		$query->from($db->quoteName('josmwt_invoice','i'));
		$query->where($db->quoteName('order_number')." = ".$db->quote($order_number));
		$db->setQuery($query);
		$result = $db->loadObjectList();
		if ($result === null) {
			throw new Exception($db->getErrorMsg());
		}
		return $result;
	} catch (Exception $e) {
		JFactory::getApplication()->enqueueMessage(JText::_('ERROR_FETCHING_UPLOADED_FILES'), 'error');
		throw $e;
	}
}
//función para mostrar la PO
function getUploadedFiles($order_number) {
	try {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('preformar');
		$query->from($db->quoteName('josmwt_preforma','p'));
		$query->where($db->quoteName('order_number') . ' = ' . $db->quote($order_number));
		$db->setQuery($query);
		$result = $db->loadResult();
		if ($result === null) {
			return null;
		}
		return $result;
	} catch (Exception $e) {
		JFactory::getApplication()->enqueueMessage(JText::_('ERROR_FETCHING_UPLOADED_FILES'), 'error');
		throw $e;
	}
}
//funcion para mostrar la factura
function getUploadedFiles8($order_number, $number_invoice) {
	try {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('i.invoice');
		$query->from($db->quoteName('josmwt_invoice', 'i'));
		$query->where($db->quoteName('i.order_number') . ' = ' . $db->quote($order_number) . ' AND ' . $db->quoteName('i.number_invoice') . ' = ' . $db->quote($number_invoice));
		$db->setQuery($query);
		$result = $db->loadResult();
		if ($result === null) {
			return null;
		}
		return $result;
	} catch (Exception $e) {
		JFactory::getApplication()->enqueueMessage(JText::_('ERROR_FETCHING_UPLOADED_FILES'), 'error');
		throw $e;
	}
}
//funcion para mostrar el archivo proforma
function getUploadedFiles4($order_number) {
	try {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('s.preforma');
		$query->from($db->quoteName('josmwt_sap','s'));
		$query->where($db->quoteName('order_number') . ' = ' . $db->quote($order_number));
		$db->setQuery($query);
		$result = $db->loadResult();
		if ($result === null) {
			return null;
		}
		return $result;
	} catch (Exception $e) {
		JFactory::getApplication()->enqueueMessage(JText::_('ERROR_FETCHING_UPLOADED_FILES'), 'error');
		throw $e;
	}
}
//funcion para el numero de guia
function getUploadedFiles5($order_number) {
	try {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('s.number_guia, s.guia');
		$query->from($db->quoteName('josmwt_shipping','s'));
	    $query->where($db->quoteName('s.order_number') . ' = ' . $db->quote($order_number));
		$db->setQuery($query);
		$result = $db->loadObjectList();
		if ($result === null) {
			return null;
		}
		return $result;
	} catch (Exception $e) {
		JFactory::getApplication()->enqueueMessage(JText::_('ERROR_FETCHING_UPLOADED_FILES'), 'error');
		throw $e;
	}
}
function getUploadedFiles6($order_number, $guia) {
	try {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('s.guia');
		$query->from($db->quoteName('josmwt_shipping', 's'));
		$query->where($db->quoteName('s.order_number') . ' = ' . $db->quote($order_number) . ' AND ' . $db->quoteName('s.number_guia') . ' = ' . $db->quote($guia));
		$db->setQuery($query);
		$result = $db->loadResult();
		if ($result === null) {
			return null;
		}
		return $result;
	} catch (Exception $e) {
		JFactory::getApplication()->enqueueMessage(JText::_('ERROR_FETCHING_UPLOADED_FILES'), 'error');
		throw $e;
	}
}
function isSameCustomer($order_number, $userId) {
	$db = JFactory::getDbo();
	$query = $db->getQuery(true);
	$customers = getCustomers($order_number);
	if (empty($customers)) {
		
		return false;
	}
	$customerUsers = getCustomerUser($userId);
	if (empty($customerUsers)) {
		JFactory::getApplication()->enqueueMessage('No customer_id found in josmwt_customer_user for user_id: ' . $userId, 'error');
		return false;
	}
	$isSameCustomer = false;
	foreach ($customers as $customer) {
		foreach ($customerUsers as $customerUser) {
			if ($customer['customer_id'] == $customerUser['customer_id']) {
				$isSameCustomer = true;
				break 2;
			}
		}
	}
	return $isSameCustomer;
}
function getOrdersByCustomerOrUser($customerId, $user_id_resultado) {
    $db = JFactory::getDbo();
    $query = $db->getQuery(true);
    $query->select('*');
    $query->from($db->quoteName('josmwt_hikashop_order', 'o'));
    $query->where($db->quoteName('o.customer') . ' = ' . $db->quote($customerId));
    $query->orWhere($db->quoteName('o.order_user_id') . ' = ' . $db->quote($user_id_resultado));
    $db->setQuery($query);

    try {
        $result = $db->loadObjectList();
        $uniqueResults = [];
        foreach ($result as $order) {
            $cleanOrderId = trim($order->order_id);
            if (!isset($uniqueResults[$cleanOrderId])) {
                $uniqueResults[$cleanOrderId] = $order;
            }
        }
        return array_values($uniqueResults);

    } catch (Exception $e) {
        JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
        return array();
    }
}


function getUserById($userId) {
    try {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select($db->quoteName('user_id'));
        $query->from($db->quoteName('josmwt_hikashop_user'));
        $query->where($db->quoteName('user_cms_id') . ' = ' . $db->quote($userId));
        $db->setQuery($query);
        $result = $db->loadResult();
        if ($result === null) {
            return null;
        }

        return $result;
    } catch (Exception $e) {
        JFactory::getApplication()->enqueueMessage(JText::_('ERROR_FETCHING_USER_DATA'), 'error');
        return null;
    }
}
//función para obtener los dias de credito del cliente
function getCustomers($order_number) {
	$db = JFactory::getDbo();
	$query = $db->getQuery(true);
	$query->select($db->quoteName(['c.customer_id', 'c.customer_name', 'c.customer_payment_time', 'c.customer_credit']));  
	$query->from($db->quoteName('josmwt_customer', 'c'));
	$query->innerJoin(
		$db->quoteName('josmwt_hikashop_order', 'o') . ' ON ' . $db->quoteName('c.customer_id') . ' = ' . $db->quoteName('o.customer')
	);
	$query->where($db->quoteName('o.order_number') . ' = ' . $db->quote($order_number));
	$db->setQuery($query);

	try {
		$result = $db->loadAssocList();
		return $result;
	} catch (Exception $e) {
		JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		return array();
	}
}


//funcion para obtener el cliente
function getCustomerUser($userId) {
	$db = JFactory::getDbo();
	$query = $db->getQuery(true);
	$query->select($db->quoteName(['c.customer_id', 'customer.customer_name']));  
	$query->from($db->quoteName('josmwt_customer_user', 'c'));
	$query->leftJoin(
		$db->quoteName('josmwt_customer', 'customer') . ' ON ' . $db->quoteName('c.customer_id') . ' = ' . $db->quoteName('customer.customer_id')
	);
	$query->where($db->quoteName('c.user_id') . ' = ' . (int)$userId);
	$db->setQuery($query);
	try {
		$result = $db->loadAssocList();
		return $result;
	} catch (Exception $e) {
		JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		return array();
	}
}
?>
</div>
<script>
function mostrarArchivo(filePath) {
    window.open("https://mwt.one" + filePath, "_blank");
}
function mostrarArchivo2(filePath2) {
    window.open("https://mwt.one" + filePath2, "_blank");
}
function mostrarArchivo3(filePath3) {
    window.open("https://mwt.one" + filePath3, "_blank");
}
function mostrarArchivo4(filePath4) {
    window.open("https://mwt.one" + filePath4, "_blank");
}
</script>
</div>
