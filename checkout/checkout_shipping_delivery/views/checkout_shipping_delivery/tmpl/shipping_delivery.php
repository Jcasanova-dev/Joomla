<?php
/**
 * @package    Checkout Shipping Delivery plugin for Joomla! HikaShop
 * @version    1.0.0
 * @author     Obsidev S.A.R.L.
 * @copyright  (C) 2011-2022 OBSIDEV. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?>
<?php if(empty($this->ajax)) { ?>
<div id="hikashop_checkout_plg-shipping-delivery_<?php echo $this->step; ?>_<?php echo $this->module_position; ?>" data-checkout-step="<?php echo $this->step; ?>" data-checkout-pos="<?php echo $this->module_position; ?>" class="hikashop_checkout_shippingdelivery">
<?php } ?>
<div class="hikashop_checkout_loading_elem"></div>
<div class="hikashop_checkout_loading_spinner"></div>
<?php
if(!empty($this->data)) {
?>
<fieldset class="hika_delivery_address_field hikashop_checkout_shipping_delivery_block">
    <legend><?php echo JText::_('DELIVERY_ADDRESS_TITLE'); ?></legend>
    <?php
    foreach($this->data as $key => $entry) {
        if(empty($entry))
            continue;
        echo '<div>';
        if(is_string($entry)) {
            echo $entry;
        } else if(is_array($entry)) {
            echo @$entry['content'];
        } else if(is_object($entry)) {
            echo @$entry->content;
        }
        echo '</div>';
    }
    ?>
</fieldset>
<?php
}

if(empty($this->ajax)) { ?>
</div>
<script type="text/javascript">
// Obtener el campo de selección del método de envío
var shippingSelect = document.querySelector('select[name="shipping_method"]');

// Función para actualizar el bloque de envío y entrega
function updateShippingDelivery() {
    // Obtener el método de envío seleccionado
    var selectedOption = shippingSelect.options[shippingSelect.selectedIndex];
    var shippingMethod = {
        shipping_id: selectedOption.value,
        shipping_name: selectedOption.text,
        shipping_price: selectedOption.getAttribute('data-price')
    };

    // Actualizar el bloque de envío y entrega con el nuevo método de envío
    window.checkout.refreshShippingDelivery(<?php echo (int)$this->step; ?>, <?php echo (int)$this->module_position; ?>, shippingMethod);

    // Actualizar el valor de la columna order_shipping_price en la tabla order
    var order = window.checkout.getOrder();
    order.order_shipping_price = shippingMethod.shipping_price;

    // Actualizar la tabla order
    window.checkout.updateOrder(order);
}

// Asociar la función al evento de cambio en el campo de selección
shippingSelect.addEventListener('change', updateShippingDelivery);

// Llamar a la función una vez para inicializar
updateShippingDelivery();

// Llamar a la función una vez para inicializar cuando se cargue la página
window.addEventListener('load', updateShippingDelivery);
</script>
<?php }
