<?php
/**
 * @package    Checkout Payment Credit plugin for Joomla! HikaShop
 * @version    1.0.0
 * @author     Tu Nombre
 * @copyright  (C) Año-Actual Tu Empresa. Todos los derechos reservados.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?>

<?php if(empty($this->ajax)) { ?>
<div id="hikashop_checkout_plg-payment-credit_<?php echo $this->step; ?>_<?php echo $this->module_position; ?>" data-checkout-step="<?php echo $this->step; ?>" data-checkout-pos="<?php echo $this->module_position; ?>" class="hikashop_checkout_paymentcredit">
<?php } ?>

<div class="hikashop_checkout_loading_elem"></div>
<div class="hikashop_checkout_loading_spinner"></div>

<?php
if(!empty($this->data)) {
?>
<fieldset class="hika_payment_credit_field hikashop_checkout_payment_credit_block">
    <legend><?php echo JText::_('HIKASHOP_PAYMENT_CREDIT'); ?></legend>
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
// Obtener el campo de selección del método de pago
var paymentSelect = document.querySelector('select[name="payment_method"]');

// Función para actualizar el bloque de pago y crédito
function updatePaymentCredit() {
    // Obtener el método de pago seleccionado
    var selectedOption = paymentSelect.options[paymentSelect.selectedIndex];
    var paymentMethod = {
        payment_id: selectedOption.value,
        payment_name: selectedOption.text,
        payment_amount: selectedOption.getAttribute('data-amount')
    };

    // Actualizar el bloque de pago y crédito con el nuevo método de pago
    window.checkout.refreshPaymentCredit(<?php echo (int)$this->step; ?>, <?php echo (int)$this->module_position; ?>, paymentMethod);

    // Actualizar el valor del método de pago y el monto en la tabla order
    var order = window.checkout.getOrder();
    order.payment_method_id = paymentMethod.payment_id;
    order.payment_name = paymentMethod.payment_name;
    order.order_payment = paymentMethod.payment_amount;

    // Actualizar la tabla order
    window.checkout.updateOrder(order);
}

// Asociar la función al evento de cambio en el campo de selección
paymentSelect.addEventListener('change', updatePaymentCredit);

// Llamar a la función una vez para inicializar
updatePaymentCredit();

// Llamar a la función una vez para inicializar cuando se cargue la página
window.addEventListener('load', updatePaymentCredit);
</script>
<?php } ?>
