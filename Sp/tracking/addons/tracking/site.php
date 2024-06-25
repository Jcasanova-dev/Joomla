
<?php

/**
 * @package SP Page Builder
 * @author JoomShaper http://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2022 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
//no direct access
defined('_JEXEC') or die('Restricted access');
session_start();
class SppagebuilderAddonTracking extends SppagebuilderAddons
{
    public function render()
    {
        $settings = $this->addon->settings;
        $class = (isset($settings->class) && $settings->class) ? ' ' . $settings->class : '';
        $title = (isset($settings->title) && $settings->title) ? $settings->title : '';
        $heading_selector = (isset($settings->heading_selector) && $settings->heading_selector) ? $settings->heading_selector : 'h2';
        $content = (isset($settings->content) && $settings->content) ? $settings->content : '';
        $button = (isset($settings->button) && $settings->button) ? $settings->button : '';
        $button_type = (isset($settings->type) && $settings->type) ? $settings->type : '';
        $output = '';
        $output .= $this->generateHeadContent();
        list($link, $target) = AddonHelper::parseLink($settings, 'url');
        $output .= '<div class="sppb-addon sppb-addon-rich_text' . $class . '">';
        $session = JFactory::getSession();
        $userId = $session->get('user')->id;
        $db = JFactory::getDbo();
        $order_number = JFactory::getApplication()->input->get('order_number', '', 'string');
        $customer = $this->getCustomers($order_number);
        $output .= '<div>';
        $output .= '<form action="" method="post" enctype="multipart/form-data">';
        $output .= '<input type="hidden" name="order_number" value="' . htmlspecialchars($order_number) . '">';
        $output .= '<div class="form-group">';
        $output .= '<br>';
        $titulo = $this->obtenerTituloPorUserId($userId);
        $status = $this->getStatus($order_number);
        if ($status == 'confirmed') {
            $number_purchase = $this->getNumber($order_number);
            $filePathRelatives = $this->getUploadedFiles($order_number);
            $output .= '<div style="display: flex; justify-content: space-around;">';
            $output .= '<div class="tab blue-tab active3" id="tab1" style="text-align: center;">';
            $output .= '<span>Creacion/Credito</span><br>';
            $output .= '<div style="display: inline-block; margin-left: 3px;">'; // Contenedor para centrar el contenido y mover la imagen
            if (empty($number_purchase) && empty($filePathRelatives)) {
                // Si ambos están vacíos, mostrar la imagen roja
                $output .= '<img src="https://mwt.one/images/estatus/rojo.png" alt="Rojo" title="Sin Información" style="width: 20px; height: 20px;">';
            } elseif (!empty($number_purchase) || !empty($filePathRelatives)) {
                $output .= '<img src="https://mwt.one/images/estatus/amarilloo.png" alt="Amarillo" title="Incompleto" style="width: 20px; height: 20px;">';
            } else {
                $output .= '<img src="https://mwt.one/images/estatus/verde.png" alt="Verde" title="Completo" style="width: 20px; height: 20px;">';
            }
            $output .= '</div>'; // Cierre del contenedor para centrar el contenido y mover la imagen
            $output .= '</div>';
            $output .= '<div class="tab green-tab" id="tab2">Elaboración</div>';
            $output .= '<div class="tab purple-tab" id="tab3">Preparación</div>';
            $output .= '<div class="tab pink-tab" id="tab4">Despacho</div>';
            $output .= '<div class="tab orange-tab" id="tab5">Transito</div>';
            $output .= '<div class="tab yellow-tab" id="tab6">Status/Pago</div>';
            $output .= '</div>';
            $output .= '<script>';
            $output .= 'let currentTab = null;';
            $output .= 'const addActiveClass = (tab) => {';
            $output .= '  if (currentTab) {';
            $output .= '    currentTab.classList.remove("active3");';
            $output .= '  }';
            $output .= '  tab.classList.add("active3");';
            $output .= '  currentTab = tab;';
            $output .= '};';
            $output .= 'document.getElementById("tab1").addEventListener("click", function() {';
            $output .= '  addActiveClass(this);';
            $output .= '});';
            $output .= 'document.addEventListener("DOMContentLoaded", function() {';
            $output .= '  const tab1 = document.getElementById("tab1");';
            $output .= '  addActiveClass(tab1);';
            $output .= '});';
            $output .= '</script>';
            if (empty($filePathRelatives)) {
                $output .= '<section id="section1" class="tab-content">';
            } else {
                $output .= '<section id="section1" class="tab-content-1">';
            }
            $output .= '<div>';
            $output .= '<br>';
            $output .= '<label for="number_pedido">Número de Pedido</label>';
            $output .= '<input type="text" id="number_pedido" name="number_pedido" value="' . htmlspecialchars($order_number) . '"readonly';
            $output .= '</div>';
            $output .= '<div>';
            $output .= '<br>';
            $output .= '<label for="number_purchase">Número de Orden de Compra</label>';

            $output .= '<input type="text" id="number_purchase" name="number_purchase" value="' . htmlspecialchars($number_purchase) . '"';
            $output .= ' oninput="validateNumberPurchase(this)" required>';
            $output .= '<span id="number_purchase_error" style="color: red;"></span>';
            $output .= '</div>';
            $output .= '<script>';
            $output .= 'function validateNumberPurchase(input) {';
            $output .= '    var pattern = /^[A-Za-z0-9$-]+$/;';
            $output .= '    var errorMessage = "Solo se permiten letras, números, el guion \'-\' y el símbolo \'$\';";';
            $output .= '    var errorElement = document.getElementById("number_purchase_error");';
            $output .= '    if (!pattern.test(input.value)) {';
            $output .= '        errorElement.textContent = errorMessage;';
            $output .= '    } else {';
            $output .= '        errorElement.textContent = "";';
            $output .= '    }';
            $output .= '}';
            $output .= '</script>';
            $output .= '<div>';
            $output .= '<br>';
            $output .= '<label for="customer_select">Seleccionar Cliente</label>';
            $output .= '<select id="customer_select" name="customer_select">';
            $customers = $this->getCustomerUser($userId); // Obtener la lista de clientes
            if (!empty($customers)) {
                foreach ($customers as $customer) {
                    $output .= '<option value="' . $customer['customer_id'] . '">' . $customer['customer_name'] . '</option>';
                }
            } else {
                $output .= '<option value="">No customers found</option>';
            }
            $output .= '</select>';
            $output .= '</div>';

            if (empty($filePathRelatives)) {
                $output .= '<div>';
                $output .= '<br>';
                $output .= '<label for="document_upload">Subir Orden de Compra </label>';
                $output .= '<input type="file" id="document_upload" name="document_upload" accept=".pdf" >';
                $output .= '<p>Solo se permiten archivos .pdf</p>';
                $output .= '</div>';
            }
            $output .= '<div class="' . $addon_id . ' container">'; // Inicio del div contenedor
            if (!empty($filePathRelatives)) {
                $output .= '<br>';
                $output .= '<div class="' . $addon_id . ' img-container2">' .
                    '<span class="invoice-number">OC</span>' .
                    '<a href="https://mwt.one' . $filePathRelatives . '" target="_blank">' .
                    '<img src="https://mwt.one/images/estatus/documento.png" alt="Icono de Guia">' .
                    '</a>' .
                    '<div class="button-group">' .
                    '<button type="submit" name="delete_files" class="btn btn-danger">' .
                    '<i class="fas fa-times"></i>' .
                    '</button>' .
                    '</div>' .
                    '</div>';
            }
            $output .= '<div class="button-group">';

            $output .= '<input type="submit" name="upload_form" class="btn btn-primary" value="Enviar">';
            $output .= '</div>';

            $output .= '</section>';
            $output .= '<script>';
            $output .= 'document.addEventListener("DOMContentLoaded", function() {';
            $output .= '    var tab1 = document.getElementById("tab1");'; // Corregido: selecciona por ID
            $output .= '    var section1 = document.getElementById("section1");';
            $output .= '    tab1.addEventListener("click", function() {';
            $output .= '        section1.style.display = "block";';
            $output .= '    });';
            $output .= '});';
            $output .= '</script>';
        } elseif ($status == 'credito') {
            if ($this->isSameCustomer($order_number, $userId)) {
                $number_purchase = $this->getNumber($order_number);
                $filePathRelatives = $this->getUploadedFiles($order_number);
                $output .= '<div style="display: flex; justify-content: space-around;">';
                $output .= '<div class="tab blue-tab active3" id="tab1" style="text-align: center;">';
                $output .= '<span>Creacion/Credito</span><br>';
                $output .= '<div style="display: inline-block; margin-left: 3px;">'; // Contenedor para centrar el contenido y mover la imagen
                if (empty($number_purchase) && empty($filePathRelatives)) {
                    // Si ambos están vacíos, mostrar la imagen roja
                    $output .= '<img src="https://mwt.one/images/estatus/rojo.png" alt="Rojo" title="Sin Información" style="width: 20px; height: 20px;">';
                } elseif (!empty($number_purchase) || !empty($filePathRelatives)) {
                    $output .= '<img src="https://mwt.one/images/estatus/amarilloo.png" alt="Amarillo" title="Incompleto" style="width: 20px; height: 20px;">';
                } else {
                    $output .= '<img src="https://mwt.one/images/estatus/verde.png" alt="Verde" title="Completo" style="width: 20px; height: 20px;">';
                }
                $output .= '</div>'; // Cierre del contenedor para centrar el contenido y mover la imagen
                $output .= '</div>';
                $output .= '<div class="tab green-tab" id="tab2">Elaboración</div>';
                $output .= '<div class="tab purple-tab" id="tab3">Preparación</div>';
                $output .= '<div class="tab pink-tab" id="tab4">Despacho</div>';
                $output .= '<div class="tab orange-tab" id="tab5">Transito</div>';
                $output .= '<div class="tab yellow-tab" id="tab6">Status/Pago</div>';
                $output .= '</div>';
                $output .= '<script>';
                $output .= 'let currentTab = null;';
                $output .= 'const addActiveClass = (tab) => {';
                $output .= '  if (currentTab) {';
                $output .= '    currentTab.classList.remove("active3");';
                $output .= '  }';
                $output .= '  tab.classList.add("active3");';
                $output .= '  currentTab = tab;';
                $output .= '};';
                $output .= 'document.getElementById("tab1").addEventListener("click", function() {';
                $output .= '  addActiveClass(this);';
                $output .= '});';
                $output .= 'document.addEventListener("DOMContentLoaded", function() {';
                $output .= '  const tab1 = document.getElementById("tab1");';
                $output .= '  addActiveClass(tab1);';
                $output .= '});';
                $output .= '</script>';
                if (empty($filePathRelatives)) {
                    $output .= '<section id="section1" class="tab-content">';
                } else {
                    $output .= '<section id="section1" class="tab-content-1">';
                }
                $output .= '<div>';
                $output .= '<br>';
                $output .= '<label for="number_pedido">Número de Pedido</label>';
                $output .= '<input type="text" id="number_pedido" name="number_pedido" value="' . htmlspecialchars($order_number) . '"readonly';
                $output .= '</div>';
                $output .= '<div>';
                $output .= '<br>';
                $output .= '<label for="number_purchase">Número de Orden de Compra</label>';
                $number_purchase = $this->getNumber($order_number);
                $output .= '<input type="text" id="number_purchase" name="number_purchase" value="' . htmlspecialchars($number_purchase) . '" readonly>';
                $output .= '</div>';
                $filePathRelatives = $this->getUploadedFiles($order_number);
                $output .= '<div class="' . $addon_id . ' container">'; // Inicio del div contenedor
                if (!empty($filePathRelatives)) {
                    $output .= '<br>';
                    $output .= '<div class="' . $addon_id . ' img-container2">' .
                        '<span class="invoice-number">OC</span>' .
                        '<a href="https://mwt.one' . $filePathRelatives . '" target="_blank">' .
                        '<img src="https://mwt.one/images/estatus/documento.png" alt="Icono de Guia">' .
                        '</a>' .
                        '</div>';
                }
                $output .= '</div>';
                $output .= '<div>';
                $output .= '<br>';
                $customer = $this->getCustomers($order_number);
                $credit = $this->compareCreditAndPrice($order_number);
                $price = $this->getOrder($order_number);
                $customername = $customer[0]['customer_name'];
                $customerPaymentTime = $customer[0]['customer_payment_time'];
                $customerCredit = $customer[0]['customer_credit'];
                $orderFullPrice = $price[0]->order_full_price;
                if ($titulo == 'Administrator') {
                    $output .= '<div>';
                    $output .= '<br>';
                    $output .= '<label for="tyme_credit">Cliente</label>';
                    $output .= '<input type="text" id="tyme_credit" name="tyme_credit" value="' . htmlspecialchars($customername) . '" readonly>';
                    $output .= '</div>';
                    $output .= '<div>';
                    $output .= '<br>';
                    $output .= '<label for="tyme_credit">Días de Crédito Cliente</label>';
                    $output .= '<input type="text" id="tyme_credit" name="tyme_credit" value="' . htmlspecialchars($customerPaymentTime) . '" readonly>';
                    $output .= '</div>';
                    $output .= '<div>';
                    $output .= '<br>';
                    $output .= '<label for="tyme_credit">Crédito Disponible</label>';
                    $output .= '<input type="text" id="tyme_credit" name="tyme_credit" value="' . htmlspecialchars($customerCredit) . '" readonly>';
                    $output .= '</div>';
                    $output .= '<div>';
                    $output .= '<br>';
                    $output .= '<label for="tyme_credit">Valor del Pedido</label>';
                    $output .= '<input type="text" id="tyme_credit" name="tyme_credit" value="' . htmlspecialchars($orderFullPrice) . '" readonly>';
                    $output .= '</div>';
                    $output .= '<div id="credit_div">';
                    $output .= '<br>';
                    $output .= '<label for="credit_result">Observaciones</label>';
                    $output .= '<input type="text" id="credit_result" name="credit_result" value="' . htmlspecialchars($credit) . '" readonly>';
                    $output .= '</div>';
                    $output .= '<script>';
                    $output .= 'window.onload = function() {';
                    $output .= '    var credit_result = document.getElementById("credit_result");';
                    $output .= '    var credit_div = document.getElementById("credit_div");';
                    $output .= '    if (credit_result.value != "Este pedido en este momento excede el límite de crédito. Por favor, prosiga el proceso y consulte con su agente") {';
                    $output .= '        credit_div.style.display = "none";';
                    $output .= '    } else {';
                    $output .= '        credit_result.style.color = "red";';
                    $output .= '    }';
                    $output .= '}';
                    $output .= '</script>';
                } else {
                    $output .= '<div>';
                    $output .= '<br>';
                    $output .= '<label for="tyme_credit">Cliente</label>';
                    $output .= '<input type="text" id="tyme_credit" name="tyme_credit" value="' . htmlspecialchars($customername) . '" readonly>';
                    $output .= '</div>';
                    $output .= '<div>';
                    $output .= '<br>';
                    $output .= '<label for="tyme_credit">Días de Crédito Cliente</label>';
                    $output .= '<input type="text" id="tyme_credit" name="tyme_credit" value="' . htmlspecialchars($customerPaymentTime) . '" readonly>';
                    $output .= '</div>';
                    $output .= '<div>';
                    $output .= '<br>';
                    $output .= '<label for="tyme_credit">Crédito Disponible</label>';
                    $output .= '<input type="text" id="tyme_credit" name="tyme_credit" value="' . htmlspecialchars($customerCredit) . '" readonly>';
                    $output .= '</div>';
                    $output .= '<div>';
                    $output .= '<br>';
                    $output .= '<label for="tyme_credit">Valor del Pedido</label>';
                    $output .= '<input type="text" id="tyme_credit" name="tyme_credit" value="' . htmlspecialchars($orderFullPrice) . '" readonly>';
                    $output .= '</div>';
                    $output .= '<div id="credit_div">';
                    $output .= '<br>';
                    $output .= '<label for="credit_result">Observaciones</label>';
                    $output .= '<input type="text" id="credit_result" name="credit_result" value="' . htmlspecialchars($credit) . '" readonly>';
                    $output .= '</div>';
                    $output .= '<script>';
                    $output .= 'window.onload = function() {';
                    $output .= '    var credit_result = document.getElementById("credit_result");';
                    $output .= '    var credit_div = document.getElementById("credit_div");';
                    $output .= '    if (credit_result.value != "Este pedido en este momento excede el límite de crédito. Por favor, prosiga el proceso y consulte con su agente") {';
                    $output .= '        credit_div.style.display = "none";';
                    $output .= '    } else {';
                    $output .= '        credit_result.style.color = "red";';
                    $output .= '    }';
                    $output .= '}';
                    $output .= '</script>';
                }
                $output .= '<div class="button-group">';
                $output .= '<br>';
                $output .= '<input type="submit" name="credit_form" class="btn btn-primary" value="Enviar">';
                $output .= '</div>';
                $output .= '</section>';
                $output .= '<script>';
                $output .= 'document.addEventListener("DOMContentLoaded", function() {';
                $output .= '    var tab1 = document.getElementById("tab1");'; // Corregido: selecciona por ID
                $output .= '    var section1 = document.getElementById("section1");';
                $output .= '    tab1.addEventListener("click", function() {';
                $output .= '        section1.style.display = "block";';
                $output .= '    });';
                $output .= '});';
                $output .= '</script>';
            } else {
                $output .= '<div>';
                $output .= '<p>Lo siento, no tienes permiso para ver este pedido.</p>';
                $output .= '</div>';
            }
        } elseif ($status == 'produccion') {
            if ($this->isSameCustomer($order_number, $userId)) {
                $number_purchase = $this->getNumber($order_number);
                $filePathRelatives = $this->getUploadedFiles($order_number);
                $number = $this->getsap($order_number);
                $fecha = $this->getfechapro($order_number);
                $output .= '<div style="display: flex; justify-content: space-around;">';
                $output .= '<div class="tab blue-tab" id="tab1" style="text-align: center;">';
                $output .= '<span>Creacion/Credito</span><br>';
                $output .= '<div style="display: inline-block; margin-left: 3px;">';
                $output .= '<img src="https://mwt.one/images/estatus/verde.png" alt="Verde" title="Completo" style="width: 20px; height: 20px;">';
                $output .= '</div>';
                $output .= '</div>';
                $output .= '<div class="tab green-tab" id="tab2" style="text-align: center;">';
                $output .= '<span>Elaboración</span><br>';
                $output .= '<div style="display: inline-block; margin-left: 3px;">';
                if (!empty($number) && !empty($fecha)) {
                    // Si ambos tienen datos, mostrar la imagen verde
                    $output .= '<img src="https://mwt.one/images/estatus/verde.png" alt="Verde" title="Completo" style="width: 20px; height: 20px;">';
                } elseif (empty($number) && empty($fecha)) {
                    // Si ambos están vacíos, mostrar la imagen roja
                    $output .= '<img src="https://mwt.one/images/estatus/rojo.png" alt="Rojo" title="Sin Información" style="width: 20px; height: 20px;">';
                } else {
                    // Si al menos uno de ellos tiene datos, mostrar la imagen amarilla
                    $output .= '<img src="https://mwt.one/images/estatus/amarilloo.png" alt="Amarillo" title="Incompleto" style="width: 30px; height: 20px;">';
                }
                $output .= '</div>';
                $output .= '</div>';
                $output .= '<div class="tab purple-tab" id="tab3">Preparación</div>';
                $output .= '<div class="tab pink-tab" id="tab4">Despacho</div>';
                $output .= '<div class="tab orange-tab" id="tab5">Transito</div>';
                $output .= '<div class="tab yellow-tab" id="tab6">Status/Pago</div>';
                $output .= '</div>';
                $output .= '<script>';
                $output .= 'let currentImage = null;';
                $output .= 'const addActiveClass = (tab) => {';
                $output .= '  if (currentImage) {';
                $output .= '    currentImage.classList.remove("active3");';
                $output .= '  }';
                $output .= '  tab.classList.add("active3");';
                $output .= '  currentImage = tab;';
                $output .= '};';
                $output .= 'document.getElementById("tab1").addEventListener("click", function() {';
                $output .= '  addActiveClass(this);';
                $output .= '});';
                $output .= 'document.getElementById("tab2").addEventListener("click", function() {';
                $output .= '  addActiveClass(this);';
                $output .= '});';
                $output .= 'document.addEventListener("DOMContentLoaded", function() {';
                $output .= '  const tab2 = document.getElementById("tab2");';
                $output .= '  addActiveClass(tab2);';
                $output .= '});';
                $output .= '</script>';
                $output .= '<section id="section1" class="tab-content-1" style="display:none;">';
                $output .= '<div>';
                $output .= '<br>';
                $output .= '<label for="number_pedido">Número de Pedido</label>';
                $output .= '<input type="text" id="number_pedido" name="number_pedido" value="' . htmlspecialchars($order_number) . '"readonly';
                $output .= '</div>';
                $output .= '<div>';
                $output .= '<br>';
                $output .= '<label for="number_purchase">Número de Orden de Compra</label>';
                $number_purchase = $this->getNumber($order_number);
                $output .= '<input type="text" id="number_purchase" name="number_purchase" value="' . htmlspecialchars($number_purchase) . '" readonly>';
                $output .= '</div>';
                $filePathRelatives = $this->getUploadedFiles($order_number);
                $output .= '<div class="' . $addon_id . ' container">'; // Inicio del div contenedor
                if (!empty($filePathRelatives)) {
                    $output .= '<br>';
                    $output .= '<div class="' . $addon_id . ' img-container2">' .
                        '<span class="invoice-number">OC</span>' .
                        '<a href="https://mwt.one' . $filePathRelatives . '" target="_blank">' .
                        '<img src="https://mwt.one/images/estatus/documento.png" alt="Icono de Guia">' .
                        '</a>' .
                        '</div>';
                }
                $output .= '</div>';
                $customer = $this->getCustomers($order_number);
                $credit = $this->compareCreditAndPrice($order_number);
                $price = $this->getOrder($order_number);
                $orderFullPrice = $price[0]->order_full_price;
                $customerPaymentTime = $customer[0]['customer_payment_time'];
                $customerCredit = $customer[0]['customer_credit'];
                $customername = $customer[0]['customer_name'];
                $output .= '<div>';
                $output .= '<br>';
                $output .= '<label for="tyme_credit">Client:</label>';
                $output .= '<input type="text" id="tyme_credit" name="tyme_credit" value="' . htmlspecialchars($customername) . '" readonly>';
                $output .= '</div>';
                $output .= '<div>';
                $output .= '<br>';
                $output .= '<label for="tyme_credit">Días de Crédito Cliente</label>';
                $output .= '<input type="text" id="tyme_credit" name="tyme_credit" value="' . htmlspecialchars($customerPaymentTime) . '" readonly>';
                $output .= '</div>';
                $output .= '<div>';
                $output .= '<br>';
                $output .= '<label for="tyme_credit">Crédito Disponible</label>';
                $output .= '<input type="text" id="tyme_credit" name="tyme_credit" value="' . htmlspecialchars($customerCredit) . '" readonly>';
                $output .= '</div>';
                $output .= '<div>';
                $output .= '<br>';
                $output .= '<label for="tyme_credit">Valor del Pedido</label>';
                $output .= '<input type="text" id="tyme_credit" name="tyme_credit" value="' . htmlspecialchars($orderFullPrice) . '" readonly>';
                $output .= '</div>';
                $output .= '</section>';
                $filePathRelative2 = $this->getUploadedFiles4($order_number);
                if (empty($filePathRelative2)) {
                    $output .= '<section id="section2" class="tab-content2">';
                } else {
                    $output .= '<section id="section2" class="tab-content2-1">';
                }
                $output .= '<div>';
                $output .= '<br>';
                if ($titulo == 'Administrator') {
                    $output .= '<div>';
                    $output .= '<br>';
                    $output .= '<label for="number_sap">Número de SAP</label>';
                    $number = $this->getsap($order_number);
                    $output .= '<input type="text" id="number_sap" name="number_sap" value="' . htmlspecialchars($number['number_sap']) . '"';
                    $output .= ' oninput="validateNumberSAP(this)" required>';
                    $output .= '<span id="number_sap_error" style="color: red;"></span>';
                    $output .= '<br>';
                    $output .= '<label for="number_preforma">Número de Proforma</label>';
                    $output .= '<input type="text" id="number_preforma" name="number_preforma" value="' . htmlspecialchars($number['number_preforma']) . '"';
                    $output .= ' oninput="validateNumberProforma(this)">';
                    $output .= '<span id="number_preforma_error" style="color: red;"></span>';
                    $output .= '</div>';
                    $output .= '<script>';
                    $output .= 'function validateNumberSAP(input) {';
                    $output .= '    var pattern = /^[A-Za-z0-9$-]+$/;';
                    $output .= '    var errorMessage = "Solo se permiten letras, números, el guion \'-\' y el símbolo \'$\';";';
                    $output .= '    var errorElement = document.getElementById("number_sap_error");';
                    $output .= '    if (!pattern.test(input.value)) {';
                    $output .= '        errorElement.textContent = errorMessage;';
                    $output .= '    } else {';
                    $output .= '        errorElement.textContent = "";';
                    $output .= '    }';
                    $output .= '}';
                    $output .= 'function validateNumberProforma(input) {';
                    $output .= '    var pattern = /^[A-Za-z0-9$-]+$/;';
                    $output .= '    var errorMessage = "Solo se permiten letras, números, el guion \'-\' y el símbolo \'$\';";';
                    $output .= '    var errorElement = document.getElementById("number_preforma_error");';
                    $output .= '    if (!pattern.test(input.value)) {';
                    $output .= '        errorElement.textContent = errorMessage;';
                    $output .= '    } else {';
                    $output .= '        errorElement.textContent = "";';
                    $output .= '    }';
                    $output .= '}';
                    $output .= '</script>';
                    $filePathRelative2 = $this->getUploadedFiles4($order_number);
                    if (empty($filePathRelative2)) {
                        $output .= '<div>';
                        $output .= '<br>';
                        $output .= '<label for="document_upload">Subir Proformar </label>';
                        $output .= '<input type="file" id="document_upload" name="document_upload" accept=".pdf" >';
                        $output .= '<p>Solo se permiten archivos .pdf</p>';
                        $output .= '</div>';
                    }
                    $output .= '<div class="' . $addon_id . ' container">'; // Inicio del div contenedor
                    if (!empty($filePathRelative2)) {
                        $output .= '<br>';
                        $output .= '<div class="' . $addon_id . ' img-container2">' .
                            '<span class="invoice-number">Proforma</span>' .
                            '<a href="https://mwt.one' . $filePathRelative2 . '" target="_blank">' .
                            '<img src="https://mwt.one/images/estatus/documento.png" alt="Icono de Guia">' .
                            '</a>' .
                            '<div class="button-group">' .
                            '<button type="submit" name="delete_preforma" class="btn btn-danger">' .
                            '<i class="fas fa-times"></i>' .
                            '</button>' .
                            '</div>' .
                            '</div>';
                    }
                    $output .= '</div>';
                    $output .= '<div>';
                    $output .= '<br>';
                    $output .= '<label for="fechai">Fecha Inicio de Elaboración</label>';
                    $output .= '<input type="date" id="fechai" name="fechai" value="' . htmlspecialchars($fecha['fechai']) . '" onchange="updateMinDate()">';
                    $output .= '</div>';
                    $output .= '<div>';
                    $output .= '<br>';
                    $output .= '<label for="fechaf">Fecha Final de Elaboración</label>';
                    $output .= '<input type="date" id="fechaf" name="fechaf" value="' . htmlspecialchars($fecha['fechaf']) . '" onchange="updateMaxDate()">';
                    $output .= '</div>';
                    $output .= '<script>';
                    $output .= 'function updateMinDate() {';
                    $output .= '  var startDate = document.getElementById("fechai").value;';
                    $output .= '  var endDate = new Date(startDate);';
                    $output .= '  endDate.setDate(endDate.getDate() + 60);';
                    $output .= '  document.getElementById("fechaf").value = endDate.toISOString().split(\'T\')[0];';
                    $output .= '  document.getElementById("fechaf").min = startDate;';
                    $output .= '}';
                    $output .= 'function updateMaxDate() {';
                    $output .= '  var endDate = document.getElementById("fechaf").value;';
                    $output .= '  document.getElementById("fechai").max = endDate;';
                    $output .= '}';
                    $output .= 'window.onload = function(){';
                    $output .= '    document.getElementsByName("fechai")[0].onchange = updateMinDate;';
                    $output .= '};';
                    $output .= '</script>';
                    $output .= '<div class="button-group">';
                    $output .= '<br>';
                    $output .= '<input type="submit" name="producion_form" class="btn btn-warning" value="Guardar">';
                    $output .= '</div>';
                    $output .= '<input type="hidden" name="status" value="preparacion">';
                    $output .= '<div class="button-group">';
                    $output .= '<br>';
                    $output .= '<input type="submit" name="status_form" class="btn btn-primary" value="Enviar">';
                    $output .= '</div>';
                } else {
                    $output .= '<div>';
                    $output .= '<br>';
                    $number = $this->getsap($order_number);
                    $output .= '<label for="number_purchase">Número de SAP</label>';
                    $output .= '<input type="text" id="number_sap" name="number_sap" value="' . htmlspecialchars($number['number_sap']) . '" readonly title="La Modificacion de este campo la realiza el administrador">';
                    if (!empty($number['number_preforma'])) {
                        $output .= '<br>';
                        $output .= '<label for="number_purchase">Número de Proforma:</label>';
                        $output .= '<input type="text" id="number_preforma" name="number_preforma" value="' . htmlspecialchars($number['number_preforma']) . '"readonly title="La Modificacion de este campo la realiza el administrador">';
                    }
                    $output .= '</div>';
                    $filePathRelative2 = $this->getUploadedFiles4($order_number);
                    $output .= '<div class="' . $addon_id . ' container">'; // Inicio del div contenedor
                    if (!empty($filePathRelative2)) {
                        $output .= '<br>';
                        $output .= '<div class="' . $addon_id . ' img-container2">' .
                            '<span class="invoice-number">Proforma</span>' .
                            '<a href="https://mwt.one' . $filePathRelative2 . '" target="_blank">' .
                            '<img src="https://mwt.one/images/estatus/documento.png" alt="Icono de Guia">' .
                            '</a>' .
                            '</div>';
                    }
                    $output .= '</div>';
                    $output .= '<div>';
                    $output .= '<br>';
                    $output .= '<label for="fecha_inicio">Fecha Inicio de Elaboración</label>';
                    $output .= '<input type="date" id="fechai" name="fechai" value="' . htmlspecialchars($fecha['fechai']) . '" readonly title="La Modificacion de este campo la realiza el administrador">';
                    $output .= '</div>';
                    $output .= '<div>';
                    $output .= '<br>';
                    $output .= '<label for="fecha_final">Fecha Final de Elaboración</label>';
                    $output .= '<input type="date" id="fechaf" name="fechaf" value="' . htmlspecialchars($fecha['fechaf']) . '" readonly title="La Modificacion de este campo la realiza el administrador">';
                    $output .= '</div>';
                }
                $output .= '</section>';
                $output .= '<script>';
                $output .= 'document.addEventListener("DOMContentLoaded", function() {';
                $output .= '    var tab1 = document.querySelector(".tab.blue-tab#tab1");';
                $output .= '    var tab2 = document.querySelector(".tab.green-tab#tab2");';
                $output .= '    var section1 = document.getElementById("section1");';
                $output .= '    var section2 = document.getElementById("section2");';
                $output .= '    ';
                $output .= '    tab1.addEventListener("click", function() {';
                $output .= '        section1.style.display = "block";';
                $output .= '        section2.style.display = "none";';
                $output .= '    });';
                $output .= '    ';
                $output .= '    tab2.addEventListener("click", function() {';
                $output .= '        section1.style.display = "none";';
                $output .= '        section2.style.display = "block";';
                $output .= '    });';
                $output .= '});';
                $output .= '</script>';
            } else {
                $output .= '<div>';
                $output .= '<p>Lo siento, no tienes permiso para ver este pedido.</p>';
                $output .= '</div>';
            }
        } elseif ($status == 'preparacion') {
            if ($this->isSameCustomer($order_number, $userId)) {
                $number_purchase = $this->getNumber($order_number);
                $filePathRelatives = $this->getUploadedFiles($order_number);
                $number = $this->getsap($order_number);
                $fecha = $this->getfechapro($order_number);
                $packs = $this->getUploadedFiles10($order_number);
                $datos = $this->getmethodoshipping($order_number);
                $envios = $this->getUploadedFiles15($order_number);
                $output .= '<div style="display: flex; justify-content: space-around;">';
                $output .= '<div class="tab blue-tab" id="tab1" style="text-align: center;">';
                $output .= '<span>Creacion/Credito</span><br>';
                $output .= '<div style="display: inline-block; margin-left: 3px;">';
                $output .= '<img src="https://mwt.one/images/estatus/verde.png" alt="Verde" title="Completo" style="width: 20px; height: 20px;">';
                $output .= '</div>';
                $output .= '</div>';
                $output .= '<div class="tab green-tab" id="tab2" style="text-align: center;">';
                $output .= '<span>Elaboración</span><br>';
                $output .= '<div style="display: inline-block; margin-left: 3px;">';
                if (!empty($number) && !empty($fecha)) {
                    // Si ambos tienen datos, mostrar la imagen verde
                    $output .= '<img src="https://mwt.one/images/estatus/verde.png" alt="Verde" title="Completo" style="width: 20px; height: 20px;">';
                } elseif (empty($number) && empty($fecha)) {
                    // Si ambos están vacíos, mostrar la imagen roja
                    $output .= '<img src="https://mwt.one/images/estatus/rojo.png" alt="Rojo" title="Sin Información" style="width: 20px; height: 20px;">';
                } else {
                    // Si al menos uno de ellos tiene datos, mostrar la imagen amarilla
                    $output .= '<img src="https://mwt.one/images/estatus/amarilloo.png" alt="Amarillo" title="Incompleto" style="width: 30px; height: 20px;">';
                }
                $output .= '</div>';
                $output .= '</div>';
                $output .= '<div class="tab purple-tab" id="tab3"  style="text-align: center;">';
                $output .= '<span>Preparación</span><br>';
                $output .= '<div style="display: inline-block; margin-left: 3px;">';
                if (!empty($packs) && !empty($envios)) {
                    // Si ambos tienen datos, mostrar la imagen verde
                    $output .= '<img src="https://mwt.one/images/estatus/verde.png" alt="Verde" title="Completo" style="width: 20px; height: 20px;">';
                } elseif (empty($packs) && empty($envios)) {
                    // Si ambos están vacíos, mostrar la imagen roja
                    $output .= '<img src="https://mwt.one/images/estatus/rojo.png" alt="Rojo" title="Sin Información" style="width: 20px; height: 20px;">';
                } else {
                    // Si al menos uno de ellos tiene datos, mostrar la imagen amarilla
                    $output .= '<img src="https://mwt.one/images/estatus/amarilloo.png" alt="Amarillo" title="Incompleto" style="width: 30px; height: 20px;">';
                }
                $output .= '</div>';
                $output .= '</div>';
                $output .= '<div class="tab pink-tab" id="tab4">Despacho</div>';
                $output .= '<div class="tab orange-tab" id="tab5">Transito</div>';
                $output .= '<div class="tab yellow-tab" id="tab6">Status/Pago</div>';
                $output .= '</div>';
                $output .= '<script>';
                $output .= 'let currentImage = null;';
                $output .= 'const addActiveClass = (tab) => {';
                $output .= '  if (currentImage) {';
                $output .= '    currentImage.classList.remove("active3");';
                $output .= '  }';
                $output .= '  tab.classList.add("active3");';
                $output .= '  currentImage = tab;';
                $output .= '};';
                $output .= 'document.getElementById("tab1").addEventListener("click", function() {';
                $output .= '  addActiveClass(this);';
                $output .= '});';
                $output .= 'document.getElementById("tab2").addEventListener("click", function() {';
                $output .= '  addActiveClass(this);';
                $output .= '});';
                $output .= 'document.getElementById("tab3").addEventListener("click", function() {';
                $output .= '  addActiveClass(this);';
                $output .= '});';
                $output .= 'document.addEventListener("DOMContentLoaded", function() {';
                $output .= '  const tab3 = document.getElementById("tab3");';
                $output .= '  addActiveClass(tab3);';
                $output .= '});';
                $output .= '</script>';
                $output .= '<section id="section1" class="tab-content-1" style="display:none;">';
                $output .= '<div>';
                $output .= '<br>';
                $output .= '<label for="number_pedido">Número de Pedido</label>';
                $output .= '<input type="text" id="number_pedido" name="number_pedido" value="' . htmlspecialchars($order_number) . '"readonly';
                $output .= '</div>';
                $fecha = $this->getfechapro($order_number);
                $output .= '<div>';
                $output .= '<br>';
                $output .= '<label for="number_purchase">Número de Orden de Compra</label>';
                $number_purchase = $this->getNumber($order_number);
                $output .= '<input type="text" id="number_purchase" name="number_purchase" value="' . htmlspecialchars($number_purchase) . '" readonly>';
                $output .= '</div>';
                $filePathRelatives = $this->getUploadedFiles($order_number);
                $output .= '<div class="' . $addon_id . ' container">'; // Inicio del div contenedor
                if (!empty($filePathRelatives)) {
                    $output .= '<br>';
                    $output .= '<div class="' . $addon_id . ' img-container2">' .
                        '<span class="invoice-number">OC</span>' .
                        '<a href="https://mwt.one' . $filePathRelatives . '" target="_blank">' .
                        '<img src="https://mwt.one/images/estatus/documento.png" alt="Icono de Guia">' .
                        '</a>' .
                        '</div>';
                }
                $output .= '</div>';
                $customer = $this->getCustomers($order_number);
                $credit = $this->compareCreditAndPrice($order_number);
                $price = $this->getOrder($order_number);
                $orderFullPrice = $price[0]->order_full_price;
                $customerPaymentTime = $customer[0]['customer_payment_time'];
                $customerCredit = $customer[0]['customer_credit'];
                $customername = $customer[0]['customer_name'];
                $output .= '<div>';
                $output .= '<br>';
                $output .= '<label for="tyme_credit">Cliente</label>';
                $output .= '<input type="text" id="tyme_credit" name="tyme_credit" value="' . htmlspecialchars($customername) . '" readonly>';
                $output .= '</div>';
                $output .= '<div>';
                $output .= '<br>';
                $output .= '<label for="tyme_credit">Días de Crédito Cliente</label>';
                $output .= '<input type="text" id="tyme_credit" name="tyme_credit" value="' . htmlspecialchars($customerPaymentTime) . '" readonly>';
                $output .= '</div>';
                $output .= '<div>';
                $output .= '<br>';
                $output .= '<label for="tyme_credit">Crédito Disponible</label>';
                $output .= '<input type="text" id="tyme_credit" name="tyme_credit" value="' . htmlspecialchars($customerCredit) . '" readonly>';
                $output .= '</div>';
                $output .= '<div>';
                $output .= '<br>';
                $output .= '<label for="tyme_credit">Valor del Pedido</label>';
                $output .= '<input type="text" id="tyme_credit" name="tyme_credit" value="' . htmlspecialchars($orderFullPrice) . '" readonly>';
                $output .= '</div>';
                $output .= '</section>';
                $filePathRelative2 = $this->getUploadedFiles4($order_number);
                if (empty($filePathRelative2)) {
                    $output .= '<section id="section2" class="tab-content2-2" style="display:none;">';
                } else {
                    $output .= '<section id="section2" class="tab-content2-1" style="display:none;">';
                }
                $output .= '<div>';
                $output .= '<br>';
                $number = $this->getsap($order_number);
                $output .= '<label for="number_purchase">Número de SAP</label>';
                $output .= '<input type="text" id="number_sap" name="number_sap"  value="' . htmlspecialchars($number['number_sap']) . '"readonly>';
                if (!empty($number['number_preforma'])) {
                    $output .= '<label for="number_purchase">Número de Proforma</label>';
                    $output .= '<input type="text" id="number_preforma" name="number_preforma" value="' . htmlspecialchars($number['number_preforma']) . '"readonly>';
                }
                $output .= '</div>';
                $output .= '<div class="' . $addon_id . ' container">'; // Inicio del div contenedor
                if (!empty($filePathRelative2)) {
                    $output .= '<br>';
                    $output .= '<div class="' . $addon_id . ' img-container2">' .
                        '<span class="invoice-number">Proforma</span>' .
                        '<a href="https://mwt.one' . $filePathRelative2 . '" target="_blank">' .
                        '<img src="https://mwt.one/images/estatus/documento.png" alt="Icono de Guia">' .
                        '</a>' .
                        '</div>';
                }
                $output .= '</div>';
                $output .= '<div>';
                $output .= '<br>';
                $output .= '<label for="fecha_inicio">Fecha Inicio de Elaboración</label>';
                $output .= '<input type="date" id="fechai" name="fechai" value="' . htmlspecialchars($fecha['fechai']) . '" readonly>';
                $output .= '</div>';
                $output .= '<div>';
                $output .= '<br>';
                $output .= '<label for="fecha_final">Fecha Final de Elaboración</label>';
                $output .= '<input type="date" id="fechaf" name="fechaf" value="' . htmlspecialchars($fecha['fechaf']) . '" readonly>';
                $output .= '</div>';
                $output .= '</section>';
                $packs = $this->getUploadedFiles10($order_number);
                $tabContentClass = empty($packs) ? 'tab-content3empty' : 'tab-content3';
                $output .= '<section id="section3" class="' . $tabContentClass . '">';
                $output .= '<div>';
                $output .= '<br>';
                if ($titulo == 'Administrator') {
                    $output .= '<div>';
                    $output .= '<br>';
                    session_start(); // Asegúrate de que la sesión esté iniciada
                    // Verificar si se ha enviado una solicitud POST con el método de envío seleccionado
                    if (isset($_POST['selectedOption'])) {
                        // Procesar la opción seleccionada
                        $selectedMethod = $_POST['selectedOption'];
                        // Guardar en sesión
                        $_SESSION['selected_shipping_method'] = $selectedMethod;

                        // Obtener los incoterms basado en el método de envío seleccionado
                        $incoterms = $this->getincoterms($selectedMethod);

                        // Construir el HTML para los incoterms
                        $output = '<label for="incoterms">Seleccionar Incoterms:</label>';
                        $output .= '<select name="incoterms" id="incoterms">';
                        foreach ($incoterms as $incoterm) {
                            $output .= '<option value="' . $incoterm['Code'] . '|' . $incoterm['Nombre'] . '">';
                            $output .= $incoterm['Code'] . '-' . $incoterm['Nombre'] . '</option>';
                        }
                        $output .= '</select>';
                        $output .= '<br>';
                        $output .= '<div id="incoterm_description" class="incoterm_description"></div>'; // Lugar para mostrar la descripción

                        // Devolver la respuesta
                        echo $output;
                        exit;
                    }

                    // Verificar si se ha enviado una solicitud POST con el incoterm seleccionado
                    if (isset($_POST['selectedIncoterm'])) {
                        $selectedIncoterm = $_POST['selectedIncoterm'];

                        // Dividir el valor del incoterm en Code y Nombre
                        list($code, $nombre) = explode('|', $selectedIncoterm);

                        $db = JFactory::getDbo();
                        $query = $db->getQuery(true);
                        $query->select($db->quoteName('descripcionES'))
                            ->from($db->quoteName('josmwt_incoterms'))
                            ->where($db->quoteName('Code') . ' = ' . $db->quote($code))
                            ->where($db->quoteName('Nombre') . ' = ' . $db->quote($nombre));

                        $db->setQuery($query);
                        $descripcion = $db->loadResult();

                        // Devolver la descripción sin HTML
                        echo htmlspecialchars($descripcion);
                        exit;
                    }

                    // Si no se ha enviado una solicitud POST, usar el valor de la sesión
                    $selectedMethod = isset($_SESSION['selected_shipping_method']) ? $_SESSION['selected_shipping_method'] : $datos['order_shipping_method'];
                    $datos = $this->getmethodoshipping($order_number);
                    $shippingMethods = $this->getShippingMethods($userId);
                    $output .= '<label for="shipping_method">Seleccionar Método de Envío</label>';
                    $output .= '<select id="shipping_method" name="shipping_method">';
                    if ($datos['order_shipping_method'] == 'Aereo') {
                        $output .= '<option value="Aereo" selected>Aereo</option>';
                        $output .= '<option value="Maritimo">Maritimo</option>';
                    } else {
                        $output .= '<option value="Aereo">Aereo</option>';
                        $output .= '<option value="Maritimo" selected>Maritimo</option>';
                    }
                    $output .= '</select>';
                    $output .= '<div id="selected_method">Método de envío seleccionado: ' . htmlspecialchars($datos['order_shipping_method']) . '</div>';
                    $output .= '<div id="incoterms_container">';
                    $output .= '</div>';
                    $output .= '</div>';
                    $output .= '<script>
                            document.addEventListener("DOMContentLoaded", function() {
                                var shippingMethodSelect = document.getElementById("shipping_method");
                                var selectedMethodDisplay = document.getElementById("selected_method");
                                var incotermsContainer = document.getElementById("incoterms_container");

                                function updateSelectedMethod() {
                                    var selectedOption = shippingMethodSelect.value;
                                    selectedMethodDisplay.textContent = "Método de envío seleccionado: " + selectedOption;

                                    var xhr = new XMLHttpRequest();
                                    xhr.open("POST", "", true);
                                    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                                    xhr.onload = function() {
                                        if (xhr.status === 200) {
                                            console.log("Respuesta del servidor:", xhr.responseText);
                                            incotermsContainer.innerHTML = xhr.responseText;
                                            var incotermsSelect = document.getElementById("incoterms");
                                            incotermsSelect.addEventListener("change", updateSelectedIncoterm);
                                        }
                                    };
                                    xhr.send("selectedOption=" + encodeURIComponent(selectedOption));
                                }

                                function updateSelectedIncoterm() {
                                    var incotermsSelect = document.getElementById("incoterms");
                                    var selectedIncoterm = incotermsSelect.value;
                                    var incotermDescription = document.getElementById("incoterm_description");

                                    var xhr = new XMLHttpRequest();
                                    xhr.open("POST", "", true);
                                    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                                    xhr.onload = function() {
                                        if (xhr.status === 200) {
                                            // Ocultar elementos no deseados
                                            var responseText = xhr.responseText;
                                            var parser = new DOMParser();
                                            var doc = parser.parseFromString(responseText, "text/html");
                                            var descriptionText = doc.body.textContent || "";

                                            // Actualizar descripción
                                            incotermDescription.textContent = "Descripción: " + descriptionText;

                                            // Ocultar elementos no deseados
                                            var spPageBuilder = doc.getElementById("sp-page-builder");
                                            if (spPageBuilder) {
                                                spPageBuilder.style.display = "none";
                                            }
                                        }
                                    };
                                    xhr.send("selectedIncoterm=" + encodeURIComponent(selectedIncoterm));
                                }

                                shippingMethodSelect.addEventListener("change", function() {
                                    updateSelectedMethod();
                                });

                                updateSelectedMethod();
                            });
                        </script>';

                    $packs = $this->getUploadedFiles10($order_number);
                    if (empty($packs)) {
                        $output .= '<div class="empty-packs">';
                        $output .= '<br>';
                        $output .= '<label for="document_upload">Subir Packing Detallado Caja Por Caja </label>';
                        $output .= '<input type="file" id="document_upload" name="document_upload" accept=".pdf" >';
                        $output .= '<p>Solo se permiten archivos .pdf</p>';
                        $output .= '</div>';
                    }
                    //funcion para agregar mas packing list
                    $output .= '<div id="guias">';
                    $output .= '</div>';
                    $output .= '<br>';
                    $output .= '<button type="button" id="agregar_guia">Agregar Packing List</button>';
                    $output .= '<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></scrip>';
                    $output .= '<script>';
                    $output .= '$(document).ready(function() {';
                    $output .= '    $("#agregar_guia").on("click", function() {';
                    $output .= '        var nuevoCampo = \'<div>\';';
                    $output .= '        nuevoCampo += \'<br>\';';
                    $output .= '        nuevoCampo += \'<label for="document_upload">Subir Packing List Caja </label>\';';
                    $output .= '        nuevoCampo += \'<input type="file" class="document_upload" name="document_upload_nuevo[]" accept=".pdf">\';';
                    $output .= '        nuevoCampo += \'</div>\';';
                    $output .= '        nuevoCampo += \'<button type="button" class="eliminar_packing">X</button>\';';
                    $output .= '        $("#guias").append(nuevoCampo);';
                    $output .= '    });';
                    $output .= '    $("body").on("click", ".eliminar_packing", function() {';
                    $output .= '        $(this).parent().remove();';
                    $output .= '    });';
                    $output .= '});';
                    $output .= '</script>';
                    //fin
                    $output .= '<br>';
                    $output .= '<div class="' . $addon_id . ' container">'; // Inicio del div contenedor
                    if (!empty($packs)) {
                        foreach ($packs as $pack) {
                            $filePathRelative3 = $this->getUploadedFiles11($order_number, $pack->caja);
                            $output .= '<br>';
                            $output .= '<div class="img-container2">';
                            if ($filePathRelative3) {
                                $output .= '<div class="img-wrapper">';
                                $output .= '<span class="invoice-number">' . $pack->caja . '</span>';
                                $output .= '<a href="https://mwt.one' . $filePathRelative3 . '" target="_blank">';
                                $output .= '<img src="https://mwt.one/images/estatus/documento.png" alt="Icono de Guia">';
                                $output .= '</a>';
                                $output .= '</div>';
                            }
                            $output .= '<div class="button-container">';
                            $output .= '<button type="submit" name="delete_pack_detallado" class="btn btn-danger" value="' . $pack->caja . '">';
                            $output .= '<i class="fas fa-times"></i>';

                            $output .= '</button>';
                            $output .= '</div>';
                            $output .= '</div>';
                        }
                    }
                    $output .= '</div>';
                    $output .= '<div>';
                    $output .= '<br>';
                    $output .= '<br>';
                    $output .= '<label for="address_selection">Dirección de Envío</label>';
                    $output .= '<div id="address_selection_div">';
                    $output .= '<select id="address_selection" name="address_selection" onchange="showAddress(this.value)">';
                    $shippingAddresses = $this->getAddres($userId);
                    foreach ($shippingAddresses as $shippingAddress) {
                        $selected = '';
                        if ($shippingAddress->address_id == htmlspecialchars($datos['order_billing_address_id'])) {
                            $selected = ' selected';
                        }
                        $output .= '<option value="' . $shippingAddress->address_id . '|' . $shippingAddress->address_street . '|' . $shippingAddress->address_city . '|' . $shippingAddress->address_state . '|' . $shippingAddress->address_country . '|' . $shippingAddress->address_post_code . '"' . $selected . '>';
                        $output .= $shippingAddress->address_firstname . ' ' . $shippingAddress->address_lastname . ', ' . $shippingAddress->address_street . ', ' . $shippingAddress->address_city . ', ' . $shippingAddress->address_state . ', ' . $shippingAddress->address_country . ', ' . $shippingAddress->address_telephone . ', ' . $shippingAddress->address_post_code . '</option>';
                    }
                    $output .= '</select>';
                    $output .= '</div>';
                    $output .= '<div>';
                    $output .= '<br>';
                    $operator = $this->getoperator($order_number);
                    $output .= '<label for="operator">Gestor de Carga</label>';
                    $output .= '<select id="operator" name="operator" onchange="showTextArea(this.value)">';
                    if ($operator['operator'] == 'Cliente') {
                        $output .= '<option value="Fabrica">Fabrica</option>';
                        $output .= '<option value="Cliente"selected>Cliente</option>';
                    } else {
                        $output .= '<option value="Fabrica" selected>Fabrica</option>';
                        $output .= '<option value="Cliente">Cliente</option>';
                    }
                    $output .= '</select>';
                    $output .= '</div>';
                    $output .= '<br>';
                    $output .= '<div id="textAreaDiv" style="' . ($operator['operator'] == 'Cliente' ? 'display: block;' : 'display: none;') . '">';
                    $output .= '<label for="details">Detalles:</label>';
                    $output .= '<textarea class="form-control" id="details" name="details" oninput="validateAdditionalInfo(this)">' . htmlspecialchars($operator['details']) . '</textarea>';
                    $output .= '<span id="details_error" style="color: red;"></span>';
                    $output .= '<span id="details_counter"></span>';
                    $output .= '</div>';
                    //script para opcion operador
                    $output .= '<script>
                    var previousDetails = ""; // Variable para almacenar los detalles anteriores
                    document.getElementById("operator").addEventListener("change", function() {
                        var textAreaDiv = document.getElementById("textAreaDiv");
                        var detailsTextArea = document.getElementById("details");
                        if (this.value === "Cliente") {
                            textAreaDiv.style.display = "block";
                            detailsTextArea.value = previousDetails; // Recuperar los detalles anteriores
                        } else{
                            textAreaDiv.style.display = "none";
                               previousDetails = detailsTextArea.value; // Almacenar los detalles actuales
                            detailsTextArea.value = ""; // Vaciar el campo de detalles
                        }
                    });
                </script>';
                    $output .= '<script>';
                    $output .= 'function validateAdditionalInfo(input) {';
                    $output .= '    var maxLength = 500;';
                    $output .= '    var errorMessage = "La información adicional no debe exceder los " + maxLength + " caracteres.";';
                    $output .= '    var errorElement = document.getElementById("details_error");';
                    $output .= '    var counterElement = document.getElementById("details_counter");';
                    $output .= '    if (input.value.length > maxLength) {';
                    $output .= '        input.setCustomValidity(errorMessage);';
                    $output .= '        errorElement.textContent = errorMessage;';
                    $output .= '    } else {';
                    $output .= '        input.setCustomValidity("");';
                    $output .= '        errorElement.textContent = "";';
                    $output .= '    }';
                    $output .= '    counterElement.textContent = "Caracteres restantes: " + (maxLength - input.value.length);';
                    $output .= '}';
                    $output .= '</script>';
                    $output .= '<div>';
                    $output .= '<br>';
                    $output .= '<label for="valor">Valor de Envío</label>';
                    $output .= '<input type="text" id="valorEnvio" name="valorEnvio" value="' . htmlspecialchars($datos['order_shipping_price']) . '"';
                    $output .= ' oninput="validateShippingValue(this)" required>';
                    $output .= '<span id="valor_error" style="color: red;"></span>';
                    $output .= '</div>';
                    $output .= '<script>';
                    $output .= 'function validateShippingValue(input) {';
                    $output .= '    var pattern = /^[0-9]+$/;';
                    $output .= '    var errorMessage = "Solo se permiten números.";';
                    $output .= '    var errorElement = document.getElementById("valor_error");';
                    $output .= '    if (!pattern.test(input.value)) {';
                    $output .= '        errorElement.textContent = errorMessage;';
                    $output .= '    } else {';
                    $output .= '        errorElement.textContent = "";';
                    $output .= '    }';
                    $output .= '}';
                    $output .= '</script>';
                    $envios = $this->getUploadedFiles15($order_number);
                    if (empty($envios)) {
                        $output .= '<div>';
                        $output .= '<br>';
                        $output .= '<label for="document_upload">Subir Cotización Envio</label>';
                        $output .= '<input type="file" id="document_upload3" name="document_upload3" accept=".pdf">';
                        $output .= '<p>Solo se permiten archivos .pdf</p>';
                        $output .= '</div>';
                    }
                    $output .= '<br>';
                    $output .= '<div class="' . $addon_id . ' container">'; // Inicio del div contenedor
                    if (!empty($envios)) {
                        foreach ($envios as $envio) {
                            $filePathRelative4 = $this->getUploadedFiles16($order_number);
                            $output .= '<div class="img-container2">';
                            if ($filePathRelative4) {
                                $output .= '<div class="img-wrapper">';
                                $output .= '<span class="invoice-number">Envio</span>';
                                $output .= '<a href="https://mwt.one' . $filePathRelative4 . '" target="_blank">';
                                $output .= '<img src="https://mwt.one/images/estatus/documento.png" alt="Icono de Guia">';
                                $output .= '</a>';
                                $output .= '</div>';
                            }
                            $output .= '<div class="button-container">';
                            $output .= '<button type="submit" name="delete_cotizacion" class="btn btn-danger">';
                            $output .= '<i class="fas fa-times"></i>';
                            $output .= '</button>';
                            $output .= '</div>';
                            $output .= '</div>';
                        }
                    }
                    $output .= '</div>';
                } else {
                    $output .= '<div>';
                    $output .= '<br>';
                    $datos = $this->getmethodoshipping($order_number);
                    $output .= '<label for="incoterms">Seleccionar Incoterms</label>';
                    $incoterms = $this->getincoterms();
                    $output .= '<select name="incoterms" id="incoterms">';
                    foreach ($incoterms as $incoterm) {
                        $selected = '';
                        if ($incoterm['Code'] == htmlspecialchars($datos['Code_incoterms']) && $incoterm['Nombre'] == htmlspecialchars($datos['Incoterms'])) {
                            $selected = ' selected';
                        }
                        $output .= '<option value="' . $incoterm['Code'] . '|' . $incoterm['Nombre'] . '"' . $selected . '>';
                        $output .= $incoterm['Code'] . '-' . $incoterm['Nombre'] . '</option>';
                    }
                    $output .= '</select>';
                    $output .= '</div>';
                    $output .= '<div>';
                    $output .= '<br>';
                    $datos = $this->getmethodoshipping($order_number);
                    $output .= '<label for="shipping_method">Seleccionar Método de Envío</label>';
                    $shippingMethods = $this->getShippingMethods($userId);
                    $output .= '<select name="shipping_method" id="shipping_method">';
                    foreach ($shippingMethods as $shippingMethod) {
                        $selected = '';
                        if ($shippingMethod->shipping_name == htmlspecialchars($datos['order_shipping_method'])) {
                            $selected = ' selected';
                        }
                        $output .= '<option value="' . $shippingMethod->shipping_id . '|' . $shippingMethod->shipping_name . '"' . $selected . '>';
                        $output .= $shippingMethod->shipping_name . '</option>';
                    }
                    $output .= '</select>';
                    $output .= '</div>';
                    $packs = $this->getUploadedFiles10($order_number);
                    $output .= '<div class="' . $addon_id . ' container">'; // Inicio del div contenedor
                    if (!empty($packs)) {
                        foreach ($packs as $pack) {
                            $filePathRelative3 = $this->getUploadedFiles11($order_number, $pack->caja);
                            $output .= '<div class="img-container2">';
                            if ($filePathRelative3) {
                                $output .= '<div class="img-wrapper">';
                                $output .= '<span class="invoice-number">' . $pack->caja . '</span>';
                                $output .= '<a href="https://mwt.one' . $filePathRelative3 . '" target="_blank">';
                                $output .= '<img src="https://mwt.one/images/estatus/documento.png" alt="Icono de Guia">';
                                $output .= '</a>';
                                $output .= '</div>';
                            }
                            $output .= '<div class="button-container">';
                            $output .= '<button type="submit" name="delete_pack_detallado" class="btn btn-danger" value="' . $pack->caja . '">';
                            $output .= '<i class="fas fa-times"></i>';
                            $output .= '</button>';
                            $output .= '</div>';
                            $output .= '</div>';
                        }
                    }

                    $output .= '</div>';
                    if (!empty($packs)) {
                        $output .= '<br>';
                    }
                    $output .= '<div>';
                    $output .= '<br>';
                    $output .= '<label for="address_selection">Dirección de Envío</label>';
                    $output .= '<div id="address_selection_div">';
                    $output .= '<select id="address_selection" name="address_selection" onchange="showAddress(this.value)">';
                    $shippingAddresses = $this->getAddres($userId);
                    foreach ($shippingAddresses as $shippingAddress) {
                        $selected = '';
                        if ($shippingAddress->address_id == htmlspecialchars($datos['order_billing_address_id'])) {
                            $selected = ' selected';
                        }
                        $output .= '<option value="' . $shippingAddress->address_id . '|' . $shippingAddress->address_street . '|' . $shippingAddress->address_city . '|' . $shippingAddress->address_state . '|' . $shippingAddress->address_country . '|' . $shippingAddress->address_post_code . '"' . $selected . '>';
                        $output .= $shippingAddress->address_firstname . ' ' . $shippingAddress->address_lastname . ', ' . $shippingAddress->address_street . ', ' . $shippingAddress->address_city . ', ' . $shippingAddress->address_state . ', ' . $shippingAddress->address_country . ', ' . $shippingAddress->address_telephone . ', ' . $shippingAddress->address_post_code . '</option>';
                    }
                    $output .= '</select>';
                    $output .= '</div>';
                    $output .= '<div>';
                    $output .= '<br>';
                    $operator = $this->getoperator($order_number);
                    $output .= '<label for="operator">Gestor de Carga</label>';
                    $output .= '<select id="operator" name="operator" onchange="showTextArea(this.value)">';
                    if ($operator['operator'] == 'Cliente') {
                        $output .= '<option value="Fabrica">Fabrica</option>';
                        $output .= '<option value="Cliente"selected>Cliente</option>';
                    } else {
                        $output .= '<option value="Fabrica" selected>Fabrica</option>';
                        $output .= '<option value="Cliente">Cliente</option>';
                    }
                    $output .= '</select>';
                    $output .= '</div>';
                    $output .= '<br>';
                    $output .= '<div id="textAreaDiv" style="' . ($operator['operator'] == 'Cliente' ? 'display: block;' : 'display: none;') . '">';
                    $output .= '<label for="details">Detalles:</label>';
                    $output .= '<textarea class="form-control" id="details" name="details" oninput="validateAdditionalInfo(this)">' . htmlspecialchars($operator['details']) . '</textarea>';
                    $output .= '<span id="details_error" style="color: red;"></span>';
                    $output .= '<span id="details_counter"></span>';
                    $output .= '</div>';
                    //script para opcion operador
                    $output .= '<script>
                    var previousDetails = ""; // Variable para almacenar los detalles anteriores
                    document.getElementById("operator").addEventListener("change", function() {
                        var textAreaDiv = document.getElementById("textAreaDiv");
                        var detailsTextArea = document.getElementById("details");
                        if (this.value === "Cliente") {
                            textAreaDiv.style.display = "block";
                            detailsTextArea.value = previousDetails; // Recuperar los detalles anteriores
                        } else{
                            textAreaDiv.style.display = "none";
                            previousDetails = detailsTextArea.value; // Almacenar los detalles actuales
                            detailsTextArea.value = ""; // Vaciar el campo de detalles
                        }
                    });
                </script>';
                    $output .= '<script>';
                    $output .= 'function validateAdditionalInfo(input) {';
                    $output .= '    var maxLength = 500;';
                    $output .= '    var errorMessage = "La información adicional no debe exceder los " + maxLength + " caracteres.";';
                    $output .= '    var errorElement = document.getElementById("details_error");';
                    $output .= '    var counterElement = document.getElementById("details_counter");';
                    $output .= '    if (input.value.length > maxLength) {';
                    $output .= '        input.setCustomValidity(errorMessage);';
                    $output .= '        errorElement.textContent = errorMessage;';
                    $output .= '    } else {';
                    $output .= '        input.setCustomValidity("");';
                    $output .= '        errorElement.textContent = "";';
                    $output .= '    }';
                    $output .= '    counterElement.textContent = "Caracteres restantes: " + (maxLength - input.value.length);';
                    $output .= '}';
                    $output .= '</script>';
                    $envios = $this->getUploadedFiles15($order_number);
                    if (!empty($envios)) {
                        $output .= '<div>';
                        $output .= '<label for="valor">Valor de Envío</label>';
                        $output .= '<input type="text" id="valorEnvio" name="valorEnvio" value="' . htmlspecialchars($datos['order_shipping_price']) . '"readonly>';
                        $output .= '</div>';
                    }
                    $output .= '<div class="' . $addon_id . ' container">'; // Inicio del div contenedor
                    if (!empty($envios)) {
                        foreach ($envios as $envio) {
                            $filePathRelative4 = $this->getUploadedFiles16($order_number);
                            $output .= '<div class="img-container2">';
                            if ($filePathRelative4) {
                                $output .= '<div class="img-wrapper">';
                                $output .= '<span class="invoice-number">Envio</span>';
                                $output .= '<a href="https://mwt.one' . $filePathRelative4 . '" target="_blank">';
                                $output .= '<img src="https://mwt.one/images/estatus/documento.png" alt="Icono de Guia">';
                                $output .= '</a>';
                                $output .= '</div>';
                            }
                            $output .= '<div class="button-container">';
                            $output .= '<button type="submit" name="delete_cotizacion" class="btn btn-danger">';
                            $output .= '<i class="fas fa-times"></i>';
                            $output .= '</button>';
                            $output .= '</div>';
                            $output .= '</div>';
                        }
                    }
                    $output .= '</div>';
                }
                $output .= '</div>';
                $output .= '<br>';
                $output .= '<div class="button-group">';
                $output .= '<br>';
                $output .= '<input type="submit" name="preparacion_form" class="btn btn-warning" value="Guardar">';
                $output .= '</div>';
                $output .= '<input type="hidden" name="status" value="' . ($operator['operator'] === 'Cliente' ? 'pagado' : 'despacho') . '">';
                $output .= '<div class="button-group">';
                $output .= '<br>';
                $output .= '<input type="submit" name="status_form2" class="btn btn-primary" value="Enviar">';
                $output .= '</div>';
                $output .= '</section>';
                $output .= '<script>';
                $output .= 'document.addEventListener("DOMContentLoaded", function() {';
                $output .= '    var tab1 = document.querySelector(".tab.blue-tab#tab1");';
                $output .= '    var tab2 = document.querySelector(".tab.green-tab#tab2");';
                $output .= '    var tab3 = document.querySelector(".tab.purple-tab#tab3");';
                $output .= '    var section1 = document.getElementById("section1");';
                $output .= '    var section2 = document.getElementById("section2");';
                $output .= '    var section3 = document.getElementById("section3");';
                $output .= '    ';
                $output .= '    tab1.addEventListener("click", function() {';
                $output .= '        section1.style.display = "block";';
                $output .= '        section2.style.display = "none";';
                $output .= '        section3.style.display = "none";';
                $output .= '    });';
                $output .= '    ';
                $output .= '    tab2.addEventListener("click", function() {';
                $output .= '        section1.style.display = "none";';
                $output .= '        section2.style.display = "block";';
                $output .= '        section3.style.display = "none";';
                $output .= '    });';
                $output .= '    ';
                $output .= '    tab3.addEventListener("click", function() {';
                $output .= '        section1.style.display = "none";';
                $output .= '        section2.style.display = "none";';
                $output .= '        section3.style.display = "block";';
                $output .= '    });';
                $output .= '});';
                $output .= '</script>';
            } else {
                $output .= '<div>';
                $output .= '<p>Lo siento, no tienes permiso para ver este pedido.</p>';
                $output .= '</div>';
            }
        } elseif ($status == 'despacho') {
            if ($this->isSameCustomer($order_number, $userId)) {
                $number_purchase = $this->getNumber($order_number);
                $filePathRelatives = $this->getUploadedFiles($order_number);
                $number = $this->getsap($order_number);
                $fecha = $this->getfechapro($order_number);
                $packs = $this->getUploadedFiles10($order_number);
                $datos = $this->getmethodoshipping($order_number);
                $envios = $this->getUploadedFiles15($order_number);
                $despacho = $this->getdespacho($order_number);
                $guias = $this->getUploadedFiles5($order_number);
                $number_invoice = $this->getUploadedFiles7($order_number);
                $number_invoice2 = $number_invoice[0]->number_invoice;
                $output .= '<div style="display: flex; justify-content: space-around;">';
                $output .= '<div class="tab blue-tab" id="tab1" style="text-align: center;">';
                $output .= '<span>Creacion/Credito</span><br>';
                $output .= '<div style="display: inline-block; margin-left: 3px;">';
                $output .= '<img src="https://mwt.one/images/estatus/verde.png" alt="Verde" title="Completo" style="width: 20px; height: 20px;">';
                $output .= '</div>';
                $output .= '</div>';
                $output .= '<div class="tab green-tab" id="tab2" style="text-align: center;">';
                $output .= '<span>Elaboración</span><br>';
                $output .= '<div style="display: inline-block; margin-left: 3px;">';
                if (!empty($number) && !empty($fecha)) {
                    // Si ambos tienen datos, mostrar la imagen verde
                    $output .= '<img src="https://mwt.one/images/estatus/verde.png" alt="Verde" title="Completo" style="width: 20px; height: 20px;">';
                } elseif (empty($number) && empty($fecha)) {
                    // Si ambos están vacíos, mostrar la imagen roja
                    $output .= '<img src="https://mwt.one/images/estatus/rojo.png" alt="Rojo" title="Sin Información" style="width: 20px; height: 20px;">';
                } else {
                    // Si al menos uno de ellos tiene datos, mostrar la imagen amarilla
                    $output .= '<img src="https://mwt.one/images/estatus/amarilloo.png" alt="Amarillo" title="Incompleto" style="width: 30px; height: 20px;">';
                }
                $output .= '</div>';
                $output .= '</div>';
                $output .= '<div class="tab purple-tab" id="tab3"  style="text-align: center;">';
                $output .= '<span>Preparación</span><br>';
                $output .= '<div style="display: inline-block; margin-left: 3px;">';
                if (!empty($packs) && !empty($envios)) {
                    // Si ambos tienen datos, mostrar la imagen verde
                    $output .= '<img src="https://mwt.one/images/estatus/verde.png" alt="Verde" title="Completo" style="width: 20px; height: 20px;">';
                } elseif (empty($packs) && empty($envios)) {
                    // Si ambos están vacíos, mostrar la imagen roja
                    $output .= '<img src="https://mwt.one/images/estatus/rojo.png" alt="Rojo" title="Sin Información" style="width: 20px; height: 20px;">';
                } else {
                    // Si al menos uno de ellos tiene datos, mostrar la imagen amarilla
                    $output .= '<img src="https://mwt.one/images/estatus/amarilloo.png" alt="Amarillo" title="Incompleto" style="width: 30px; height: 20px;">';
                }
                $output .= '</div>';
                $output .= '</div>';
                $output .= '<div class="tab pink-tab" id="tab4">';
                $output .= '<span>Despacho</span><br>';
                $output .= '<div style="display: inline-block; margin-left: 3px;">';
                if (!empty($despacho) && !empty($number_invoice2)) {
                    // Si ambos tienen datos, mostrar la imagen verde
                    $output .= '<img src="https://mwt.one/images/estatus/verde.png" alt="Verde" title="Completo" style="width: 20px; height: 20px;">';
                } elseif (empty($despacho) && empty($number_invoice2)) {
                    // Si ambos están vacíos, mostrar la imagen roja
                    $output .= '<img src="https://mwt.one/images/estatus/rojo.png" alt="Rojo" title="Sin Información" style="width: 20px; height: 20px;">';
                } else {
                    // Si al menos uno de ellos tiene datos, mostrar la imagen amarilla
                    $output .= '<img src="https://mwt.one/images/estatus/amarilloo.png" alt="Amarillo" title="Incompleto" style="width: 30px; height: 20px;">';
                }
                $output .= '</div>';
                $output .= '</div>';
                $output .= '<div class="tab orange-tab" id="tab5">Transito</div>';
                $output .= '<div class="tab yellow-tab" id="tab6">Status/Pago</div>';
                $output .= '</div>';
                $output .= '<script>';
                $output .= 'let currentImage = null;';
                $output .= 'const addActiveClass = (tab) => {';
                $output .= '  if (currentImage) {';
                $output .= '    currentImage.classList.remove("active3");';
                $output .= '  }';
                $output .= '  tab.classList.add("active3");';
                $output .= '  currentImage = tab;';
                $output .= '};';
                $output .= 'document.getElementById("tab1").addEventListener("click", function() {';
                $output .= '  addActiveClass(this);';
                $output .= '});';

                $output .= 'document.getElementById("tab2").addEventListener("click", function() {';
                $output .= '  addActiveClass(this);';
                $output .= '});';

                $output .= 'document.getElementById("tab3").addEventListener("click", function() {';
                $output .= '  addActiveClass(this);';
                $output .= '});';

                $output .= 'document.getElementById("tab4").addEventListener("click", function() {';
                $output .= '  addActiveClass(this);';
                $output .= '});';

                // Set the active3 class for the fourth image when the page is loaded
                $output .= 'document.addEventListener("DOMContentLoaded", function() {';
                $output .= '  const tab4 = document.getElementById("tab4");';
                $output .= '  addActiveClass(tab4);';
                $output .= '});';

                $output .= '</script>';
                $output .= '<section id="section1" class="tab-content-1" style="display:none;">';
                $output .= '<div>';
                $output .= '<br>';
                $output .= '<label for="number_pedido">Número de Pedido</label>';
                $output .= '<input type="text" id="number_pedido" name="number_pedido" value="' . htmlspecialchars($order_number) . '"readonly';
                $output .= '</div>';
                $fecha = $this->getfechapro($order_number);
                $output .= '<div>';
                $output .= '<br>';
                $output .= '<label for="number_purchase">Número de Orden de Compra</label>';
                $number_purchase = $this->getNumber($order_number);
                $output .= '<input type="text" id="number_purchase" name="number_purchase" value="' . htmlspecialchars($number_purchase) . '" readonly>';
                $output .= '</div>';
                $filePathRelatives = $this->getUploadedFiles($order_number);
                $output .= '<div class="' . $addon_id . ' container">'; // Inicio del div contenedor
                if (!empty($filePathRelatives)) {
                    $output .= '<br>';
                    $output .= '<div class="' . $addon_id . ' img-container2">' .
                        '<span class="invoice-number">OC</span>' .
                        '<a href="https://mwt.one' . $filePathRelatives . '" target="_blank">' .
                        '<img src="https://mwt.one/images/estatus/documento.png" alt="Icono de Guia">' .
                        '</a>' .
                        '</div>';
                }
                $output .= '</div>';
                $customer = $this->getCustomers($order_number);
                $credit = $this->compareCreditAndPrice($order_number);
                $price = $this->getOrder($order_number);
                $orderFullPrice = $price[0]->order_full_price;
                $customerPaymentTime = $customer[0]['customer_payment_time'];
                $customerCredit = $customer[0]['customer_credit'];
                $customername = $customer[0]['customer_name'];
                $output .= '<div>';
                $output .= '<br>';
                $output .= '<label for="tyme_credit">Cliente</label>';
                $output .= '<input type="text" id="tyme_credit" name="tyme_credit" value="' . htmlspecialchars($customername) . '" readonly>';
                $output .= '</div>';
                $output .= '<div>';
                $output .= '<br>';
                $output .= '<label for="tyme_credit">Días de Crédito Cliente</label>';
                $output .= '<input type="text" id="tyme_credit" name="tyme_credit" value="' . htmlspecialchars($customerPaymentTime) . '" readonly>';
                $output .= '</div>';
                $output .= '<div>';
                $output .= '<br>';
                $output .= '<label for="tyme_credit">Crédito Disponible</label>';
                $output .= '<input type="text" id="tyme_credit" name="tyme_credit" value="' . htmlspecialchars($customerCredit) . '" readonly>';
                $output .= '</div>';
                $output .= '<div>';
                $output .= '<br>';
                $output .= '<label for="tyme_credit">Valor del Pedido</label>';
                $output .= '<input type="text" id="tyme_credit" name="tyme_credit" value="' . htmlspecialchars($orderFullPrice) . '" readonly>';
                $output .= '</div>';
                $output .= '</section>';
                $filePathRelative2 = $this->getUploadedFiles4($order_number);
                if (empty($filePathRelative2)) {
                    $output .= '<section id="section2" class="tab-content2-2" style="display:none;">';
                } else {
                    $output .= '<section id="section2" class="tab-content2-1" style="display:none;">';
                }
                $output .= '<div>';
                $output .= '<br>';
                $number = $this->getsap($order_number);
                $output .= '<label for="number_purchase">Número de SAP</label>';
                $output .= '<input type="text" id="number_sap" name="number_sap"  value="' . htmlspecialchars($number['number_sap']) . '"readonly>';
                if (!empty($number['number_preforma'])) {
                    $output .= '<label for="number_purchase">Número de Proforma</label>';
                    $output .= '<input type="text" id="number_preforma" name="number_preforma" value="' . htmlspecialchars($number['number_preforma']) . '"readonly>';
                }
                $output .= '</div>';
                $output .= '<div class="' . $addon_id . ' container">'; // Inicio del div contenedor
                if (!empty($filePathRelative2)) {
                    $output .= '<br>';
                    $output .= '<div class="' . $addon_id . ' img-container2">' .
                        '<span class="invoice-number">Proforma</span>' .
                        '<a href="https://mwt.one' . $filePathRelative2 . '" target="_blank">' .
                        '<img src="https://mwt.one/images/estatus/documento.png" alt="Icono de Guia">' .
                        '</a>' .
                        '</div>';
                }
                $output .= '</div>';
                $output .= '<div>';
                $output .= '<br>';
                $output .= '<label for="fecha_inicio">Fecha Inicio de Elaboración</label>';
                $output .= '<input type="date" id="fechai" name="fechai" value="' . htmlspecialchars($fecha['fechai']) . '" readonly>';
                $output .= '</div>';
                $output .= '<div>';
                $output .= '<br>';
                $output .= '<label for="fecha_final">Fecha Final de Elaboración</label>';
                $output .= '<input type="date" id="fechaf" name="fechaf" value="' . htmlspecialchars($fecha['fechaf']) . '" readonly>';
                $output .= '</div>';
                $output .= '</section>';
                $output .= '<section id="section3" class="tab-content3-1" style="display:none;">';
                $datos = $this->getmethodoshipping($order_number);
                $output .= '<div>';
                $output .= '<br>';
                $output .= '<label for="number_purchase">Nombre Completo</label>';
                $output .= '<input type="text" id="address_firstname" name="address_firstname" value="' . htmlspecialchars($datos['address_firstname']) . ' ' . htmlspecialchars($datos['address_lastname']) . '" readonly>';
                $output .= '</div>';
                $output .= '<div>';
                $output .= '<br>';
                $output .= '<label for="number_purchase">Telefono</label>';
                $output .= '<input type="text" id="address_telephone" name="address_telephone" value="' . htmlspecialchars($datos['address_telephone']) . '" readonly>';
                $output .= '</div>';
                $output .= '<div>';
                $output .= '<br>';
                $output .= '<label for="number_purchase">Dirección</label>';
                $country_parts = explode('_', $datos['address_country']);
                $country_name = $country_parts[1];
                $state_parts = explode('_', $datos['address_state']);
                $state_name = $state_parts[1];
                $state_name2 = $state_parts[2];
                $state_name3 = $state_parts[3];
                $output .= '<input type="text" id="address_country" name="address_country" value="' . htmlspecialchars($country_name) . ' ' . htmlspecialchars($state_name) . ' ' . htmlspecialchars($state_name2) . ' ' . htmlspecialchars($state_name3) . ' ' . htmlspecialchars($datos['address_city']) . ' ' . htmlspecialchars($datos['address_street']) . ' Postal Cod: ' . htmlspecialchars($datos['address_post_code']) . '" readonly>';
                $output .= '</div>';
                $output .= '<div>';
                $output .= '<br>';
                $output .= '<label for="number_purchase">Método de Envío</label>';
                $output .= '<input type="text" id="order_shipping_method" name="order_shipping_method" value="' . htmlspecialchars($datos['order_shipping_method']) . ' " readonly>';
                $output .= '</div>';
                $output .= '<div>';
                $output .= '<br>';
                $output .= '<label for="number_purchase">Incoterms</label>';
                $output .= '<input type="text" id="incoterms" name="incoterms" value="' . htmlspecialchars($datos['Code_incoterms']) . '' . ' - ' . ' ' . htmlspecialchars($datos['Incoterms']) . '" readonly>';
                $output .= '</div>';
                $packs = $this->getUploadedFiles10($order_number);
                $output .= '<div class="' . $addon_id . ' container">'; // Inicio del div contenedor
                if (!empty($packs)) {
                    foreach ($packs as $pack) {
                        $filePathRelative3 = $this->getUploadedFiles11($order_number, $pack->caja);
                        $output .= '<br>';
                        $output .= '<div class="' . $addon_id . ' img-container2">' .
                            '<span class="invoice-number">' . $pack->caja . '</span>' .
                            '<a href="https://mwt.one' . $filePathRelative3 . '" target="_blank">' .
                            '<img src="https://mwt.one/images/estatus/documento.png" alt="Icono de Guia">' .
                            '</a>' .
                            '</div>';
                    }
                }
                $output .= '</div>';
                $output .= '<div>';
                $output .= '<br>';
                $operator = $this->getoperator($order_number);
                $output .= '<label for="operator">Gestor de carga</label>';
                $output .= '<input type="text" id="address_telephone" name="address_telephone" value="' . htmlspecialchars($operator['operator']) . '" readonly>';
                $output .= '</div>';
                $output .= '<div>';
                $output .= '<br>';
                if ($operator['operator'] == 'Cliente') {
                    $output .= '<label for="operator">Detalles</label>';
                    $output .= '<input type="text" id="details" name="details" value="' . htmlspecialchars($operator['details']) . '" readonly>';
                }
                $output .= '</div>';
                $output .= '<div>';
                $output .= '<br>';
                $output .= '<label for="valor">Valor de Envío</label>';
                $output .= '<input type="text" id="valor" name="valor" value="' . htmlspecialchars($datos['order_shipping_price']) . '"readonly>';
                $output .= '</div>';
                $envios = $this->getUploadedFiles15($order_number);
                $output .= '<br>';
                $output .= '<div class="' . $addon_id . ' container">'; // Inicio del div contenedor
                if (!empty($envios)) {
                    foreach ($envios as $envio) {
                        $filePathRelative4 = $this->getUploadedFiles16($order_number);
                        $output .= '<div class="' . $addon_id . ' img-container2">' .
                            '<span class="invoice-number">Envio</span>' .
                            '<a href="https://mwt.one' . $filePathRelative4 . '" target="_blank">' .
                            '<img src="https://mwt.one/images/estatus/documento.png" alt="Icono de Guia">' .
                            '</a>' .
                            '</div>';
                    }
                }
                $output .= '</div>';
                $output .= '</section>';
                $guias = $this->getUploadedFiles5($order_number);
                $number_invoice = $this->getUploadedFiles7($order_number);
                $filePathRelatives2 = $this->getUploadedFiles9($order_number);
                if (!empty($guias) && !empty($guias->number_guia)) {
                    $filePathRelative3 = $this->getUploadedFiles6($order_number, $guias->number_guia);
                }
                if (!empty($filePathRelatives2) && !empty($filePathRelatives2->certificado)) {
                    $certificadoPath = $filePathRelatives2->certificado;
                }
                if (!empty($guias)) {
                    $sectionClass = 'tab-content4-1';
                } else {
                    $sectionClass = 'tab-content4';
                }
                if (!empty($guias) && !empty($number_invoice[0]->invoice) && !empty($number_invoice[0]->certificado)) {
                    $sectionClass = 'tab-content4-2';
                } elseif (!empty($number_invoice[0]->invoice) && !empty($guias)) {
                    $sectionClass = 'tab-content4-3';
                } elseif (!empty($number_invoice[0]->certificado) && !empty($guias)) {
                    $sectionClass = 'tab-content4-4';
                } elseif (!empty($number_invoice[0]->invoice) && !empty($number_invoice[0]->certificado)) {
                    $sectionClass = 'tab-content4-5';
                }
                $output .= '<section id="section4" class="' . $sectionClass . '">';

                $methodo = $this->getShipping($order_number);
                if ($operator['operator'] == 'Fabrica') {
                    if ($methodo == 'Aereo') {
                        if ($titulo == 'Administrator') {
                            $despacho = $this->getdespacho($order_number);
                            if (empty($despacho['nomber'])) {
                                $output .= '<div>';
                                $output .= '<br>';
                                $output .= '<label for="nomber">Nombre Aerolínea</label>';
                                $output .= '<input type="text" id="nomber" name="nomber" value="' . htmlspecialchars($despacho['nomber']) . '"';
                                $output .= ' oninput="validateAirlineName(this)" required>';
                                $output .= '<span id="nomber_error" style="color: red;"></span>';
                                $output .= '</div>';
                            } else {
                                $output .= '<div style="display: flex; justify-content: center; align-items: center;">';
                                $output .= '<div>';
                                $output .= '<label for="nomber">Aerolínea</label>';
                                $output .= '<br>';
                                $output .= $despacho['nomber'];
                                $output .= '</div>';
                                $output .= '</div>';
                            }
                            $output .= '<script>';
                            $output .= 'function validateAirlineName(input) {';
                            $output .= '    var pattern = /^[a-zA-ZáéíóúÁÉÍÓÚüÜñÑ\s]+$/;';
                            $output .= '    var errorMessage = "Solo se permiten letras.";';
                            $output .= '    var errorElement = document.getElementById("nomber_error");';
                            $output .= '    if (!pattern.test(input.value)) {';
                            $output .= '        errorElement.textContent = errorMessage;';
                            $output .= '    } else {';
                            $output .= '        errorElement.textContent = "";';
                            $output .= '    }';
                            $output .= '}';
                            $output .= '</script>';
                            $output .= '<div>';
                            $output .= '<br>';
                            $output .= '<label for="number_guia">Número de Air Waybill (AWB)</label>';
                            $output .= '<input type="text" id="number_guia" name="number_guia" value="' . htmlspecialchars($despacho['number_guia']) . '"';
                            $output .= ' oninput="validateAWBNumber(this)" required>';
                            $output .= '<span id="number_guia_error" style="color: red;"></span>';
                            $output .= '</div>';
                            $output .= '<script>';
                            $output .= 'function validateAWBNumber(input) {';
                            $output .= '    var pattern = /^[0-9]+$/;';
                            $output .= '    var errorMessage = "Solo se permiten números.";';
                            $output .= '    var errorElement = document.getElementById("number_guia_error");';
                            $output .= '    if (!pattern.test(input.value)) {';
                            $output .= '        errorElement.textContent = errorMessage;';
                            $output .= '    } else {';
                            $output .= '        errorElement.textContent = "";';
                            $output .= '    }';
                            $output .= '}';
                            $output .= '</script>';
                            $output .= '<div>';
                            $output .= '<label for="country">Aeropuerto Origen</label>';
                            $output .= '<input type="text" list="nomber_despacho" id="country" name="country" value="' . htmlspecialchars($despacho['nomber_despacho']) . '">';
                            $output .= '<datalist id="nomber_despacho"></datalist>';
                            $output .= '<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
                    <script>
                    $(document).ready(function() {
                        $("#country").on("input", function() {
                            var query = $(this).val();
                            if (query != "") {
                                $.ajax({
                                    url: window.location.href,
                                    method: "POST",
                                    data: "query=" + query, // Modify the data format
                                    success: function(data) {
                                        console.log("Response:", data);
                                        $("#nomber_despacho").html(data);
                                    },
                                    error: function(jqXHR, textStatus, errorThrown) {
                                        console.error("AJAX error:", textStatus, errorThrown);
                                        console.log("Response:", jqXHR.responseText);
                                    }
                                });
                            }
                        });
                    });
                    </script>';
                            function getaeropuerto($query2)
                            {
                                $db = JFactory::getDbo();
                                $query = $db->getQuery(true);
                                $query->select($db->quoteName(array('a.Code', 'a.Airport', 'a.Country')));
                                $query->from($db->quoteName('josmwt_airport', 'a'));
                                $query->where(
                                    $db->quoteName('a.Code') . " LIKE " . $db->quote('%' . $query2 . '%') . " OR " .
                                        $db->quoteName('a.Airport') . " LIKE " . $db->quote('%' . $query2 . '%') . " OR " .
                                        $db->quoteName('a.Country') . " LIKE " . $db->quote('%' . $query2 . '%')
                                );
                                $db->setQuery($query);
                                try {
                                    $results = $db->loadAssocList();
                                } catch (Exception $e) {
                                    error_log('Database error: ' . $e->getMessage());
                                    return;
                                }
                                $output = '';
                                if (!empty($results)) {
                                    foreach ($results as $result) {
                                        $output .= '<option value="' . $result['Code'] . ' - ' . $result['Airport'] . ' (' . $result['Country'] . ')">' . $result['Airport'] . ' (' . $result['Country'] . ')</option>';
                                    }
                                } else {
                                    $output .= '<option value="">No results found</option>';
                                }

                                echo $output;
                            }
                            if (isset($_POST['query'])) {
                                error_log('Query received: ' . $_POST['query']);
                                $query2 = $_POST['query'];
                                getaeropuerto($query2);
                                exit();
                            }
                            $output .= '</div>';
                            $output .= '<div>';
                            $output .= '<label for="country2">Aeropuerto Arribo</label>';
                            $output .= '<input  type="text"list="nomber_arribo" id="country2" name="country2" value="' . htmlspecialchars($despacho['nomber_arribo']) . '">';
                            $output .= '<datalist id="nomber_arribo"></datalist>';
                            $output .= '<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
                    <script>
                    $(document).ready(function() {
                        $("#country2").on("input", function() {
                            var query = $(this).val();
                            if (query != "") {
                                $.ajax({
                                    url: window.location.href,
                                    method: "POST",
                                    data: "query=" + query, // Modify the data format
                                    success: function(data) {
                                        console.log("Response:", data);
                                        $("#nomber_arribo").html(data);
                                    },
                                    error: function(jqXHR, textStatus, errorThrown) {
                                        console.error("AJAX error:", textStatus, errorThrown);
                                        console.log("Response:", jqXHR.responseText);
                                    }
                                });
                            }
                        });
                    });
                    </script>';
                            if (isset($_POST['query'])) {
                                error_log('Query received: ' . $_POST['query']);
                                $query2 = $_POST['query'];
                                getaeropuerto($query2);
                                exit();
                            }
                            $output .= '</div>';
                            $output .= '<div>';
                            $output .= '<br>';
                            $output .= '<label for="fechas">Fecha de Embarque</label>';
                            $output .= '<input type="date" id="fechas" name="fechas" value="' . htmlspecialchars($despacho['fechas']) . '"style="width: 480px; margin: 0 auto; text-align: center;">';
                            $output .= '</div>';
                            $guias = $this->getUploadedFiles5($order_number);
                            if (empty($guias)) {
                                $output .= '<div>';
                                $output .= '<br>';
                                $output .= '<label for="document_upload">Subir Guía Aerolinea </label>';
                                $output .= '<input type="file" id="document_upload" name="document_upload" accept=".pdf">';
                                $output .= '<p>Solo se permiten archivos .pdf</p>';
                                $output .= '</div>';
                            }

                            $output .= '<div>';
                            $output .= '<br>';
                            $output .= '<label for="tyme_credit">Valor del Pedido</label>';
                            $output .= '<input type="text" id="tyme_credit" name="tyme_credit" value="' . htmlspecialchars($orderFullPrice) . '" readonly>';
                            $output .= '</div>';
                            //Proceso de factura
                            $guias = $this->getUploadedFiles5($order_number);
                            $customer = $this->getCustomers($order_number);
                            $number_invoice = $this->getUploadedFiles7($order_number);
                            $output .= '<div>';
                            $output .= '<br>';
                            $output .= '<label for="number_invoice">Número de Factura</label>';
                            $output .= '<input type="text" id="number_invoice" name="number_invoice" value="' . htmlspecialchars($number_invoice[0]->number_invoice) . '"';
                            $output .= ' oninput="validateInvoiceNumber(this)" ';
                            $output .= '>';
                            $output .= '<span id="number_invoice_error" style="color: red;"></span>';
                            $output .= '</div>';
                            $output .= '<script>';
                            $output .= 'function validateInvoiceNumber(input) {';
                            $output .= '    var pattern = /^[0-9a-zA-Z-]+$/;';
                            $output .= '    var errorMessage = "Solo se permiten números y el guion \'-\'.";';
                            $output .= '    var errorElement = document.getElementById("number_invoice_error");';
                            $output .= '    if (!pattern.test(input.value)) {';
                            $output .= '        errorElement.textContent = errorMessage;';
                            $output .= '    } else {';
                            $output .= '        errorElement.textContent = "";';
                            $output .= '    }';
                            $output .= '}';
                            $output .= '</script>';
                            $customerPaymentTime = $customer[0]['customer_payment_time'];
                            $output .= '<div>';
                            $output .= '<br>';
                            $output .= '<label for="tyme_credit">Días de Crédito Cliente</label>';
                            $output .= '<input type="text" id="tyme_credit" name="tyme_credit" value="' . htmlspecialchars($customerPaymentTime) . '"readonly>';
                            $output .= '</div>';
                            $output .= '<div>';
                            $output .= '<br>';
                            $output .= '<label for="nueva_fecha">Fecha de Pago</label>';
                            $output .= '<input type="date" id="nueva_fecha" name="nueva_fecha" value="' . htmlspecialchars($newDate) . '"style="width: 480px; margin: 0 auto; text-align: center;" readonly>';
                            $output .= '</div>';
                            $output .= '<script>';
                            $output .= 'document.addEventListener("DOMContentLoaded", calcularFechaPago);';
                            $output .= 'document.getElementById("fechas").addEventListener("change", calcularFechaPago);';
                            $output .= 'function calcularFechaPago() {';
                            $output .= '    var fechaEmbarque = document.getElementById("fechas").value;';
                            $output .= '    var daysToAdd = ' . intval($customerPaymentTime) . ';';
                            $output .= '    var timestampFechaArribo = Date.parse(fechaEmbarque);';
                            $output .= '    var newTimestamp = timestampFechaArribo + daysToAdd * 24 * 60 * 60 * 1000;';
                            $output .= '    var newDate = new Date(newTimestamp).toISOString().split("T")[0];';
                            $output .= '    document.getElementById("nueva_fecha").value = newDate;';
                            $output .= '}';
                            $output .= '</script>';
                            if (empty($number_invoice[0]->invoice)) {
                                $output .= '<div>';
                                $output .= '<br>';
                                $output .= '<label for="document_upload2">Subir Factura</label>';
                                $output .= '<br>';
                                $output .= '<input type="file" id="document_upload2" name="document_upload2" accept=".pdf">';
                                $output .= '<p>Solo se permiten archivos .pdf</p>';
                                $output .= '</div>';
                            }
                            $filePathRelatives2 = $this->getUploadedFiles9($order_number);
                            if (empty($filePathRelatives2) || empty($filePathRelatives2[0]->certificado)) {
                                $output .= '<div>';
                                $output .= '<br>';
                                $output .= '<label for="document_upload3">Subir Certificado Origen</label>';
                                $output .= '<br>';
                                $output .= '<input type="file" id="document_upload3" name="document_upload3" accept=".pdf">';
                                $output .= '<p>Solo se permiten archivos .pdf</p>';
                                $output .= '</div>';
                            }
                            $output .= '<div class="' . $addon_id . ' container">';
                            if (!empty($number_invoice[0]->invoice)) {
                                $output .= '<br>';
                                foreach ($number_invoice as $invoice) {
                                    $filePathRelative = $this->getUploadedFiles8($order_number, $invoice->number_invoice);
                                    $invoicePath = $invoice->invoice;
                                    if (!empty($invoicePath)) {
                                        $output .= '<div class="' . $addon_id . ' img-container2">' .
                                            '<span class="invoice-number">Factura</span>' .
                                            '<a href="https://mwt.one' . $invoicePath . '" target="_blank">' .
                                            '<img src="https://mwt.one/images/estatus/documento.png" alt="Icono de factura" width="180" height="180">' .
                                            '</a>' .
                                            '<div class="button-group">' .
                                            '<button type="submit" name="delete_invoice" class="btn btn-danger">' .
                                            '<i class="fas fa-times"></i>' .
                                            '</button>' .
                                            '</div>' .
                                            '</div>';
                                    }
                                }
                            }
                            if (!empty($filePathRelatives2)) {
                                $output .= '<br>';
                                foreach ($filePathRelatives2 as $filePathRelative) {
                                    $certificadoPath = $filePathRelative->certificado;
                                    if (!empty($certificadoPath)) {
                                        $output .= '<div class="' . $addon_id . ' img-container2">' .
                                            '<span class="invoice-number">Certificado</span>' .
                                            '<a href="https://mwt.one' . $certificadoPath . '" target="_blank">' .
                                            '<img src="https://mwt.one/images/estatus/documento.png" alt="Icono de certificado" width="180" height="180">' .
                                            '</a>' .
                                            '<div class="button-group">' .
                                            '<button type="submit" name="delete_certificado" class="btn btn-danger">' .
                                            '<i class="fas fa-times"></i>' .
                                            '</button>' .
                                            '</div>' .
                                            '</div>';
                                    }
                                }
                            }
                            $guias = $this->getUploadedFiles5($order_number);
                            if (!empty($guias)) {
                                $output .= '<br>';
                                foreach ($guias as $guia) {
                                    $filePathRelative3 = $this->getUploadedFiles6($order_number, $guia->number_guia);
                                    if (!empty($filePathRelative3)) {
                                        $output .= '<div class="' . $addon_id . ' img-container2">' .
                                            '<span class="invoice-number">Guia</span>' .
                                            '<a href="https://mwt.one' . $filePathRelative3 . '" target="_blank">' .
                                            '<img src="https://mwt.one/images/estatus/documento.png" alt="Icono de Guia" width="180" height="180">' .
                                            '</a>' .
                                            '<div class="button-group">' .
                                            '<button type="submit" name="delete_guia" class="btn btn-danger">' .
                                            '<i class="fas fa-times"></i>' .
                                            '</button>' .
                                            '</div>' .
                                            '</div>';
                                    }
                                }
                            }
                            $output .= '</div>';
                            //fin de proceso
                            $output .= '<div class="button-group">';
                            $output .= '<br>';
                            $output .= '<input type="submit" name="despacho_form" class="btn btn-warning" value="Guardar">';
                            $output .= '</div>';
                            $output .= '<input type="hidden" name="status" value="transito">';
                            $output .= '<div class="button-group">';
                            $output .= '<br>';
                            $output .= '<input type="submit" name="status_form" class="btn btn-primary" value="Enviar">';
                            $output .= '</div>';
                        } else {
                            $despacho = $this->getdespacho($order_number);
                            $output .= '<div style="display: flex; justify-content: center; align-items: center;">';
                            $output .= '<div>';
                            $output .= '<label for="nomber">Aerolínea</label>';
                            $output .= '<br>';
                            $output .= $despacho['nomber'];
                            $output .= '</div>';
                            $output .= '</div>';
                            $output .= '<div>';
                            $output .= '<br>';
                            $output .= '<label for="number_guia">Número de Air Waybill (AWB)</label>';
                            $output .= '<input type="text" id="number_guia" name="number_guia" value="' . htmlspecialchars($despacho['number_guia']) . '" readonly title="La Modificacion de este campo la realiza el administrador">';
                            $output .= '</div>';
                            $output .= '<div>';
                            $output .= '<br>';
                            $output .= '<label for="nomber_despacho">Aeropuerto de Despacho</label>';
                            $output .= '<input type="text" id="nomber_despacho" name="nomber_despacho" value="' . htmlspecialchars($despacho['nomber_despacho']) . '" readonly title="La Modificacion de este campo la realiza el administrador">';
                            $output .= '</div>';
                            $output .= '<div>';
                            $output .= '<br>';
                            $output .= '<label for="nomber_arribo">Aeropuerto de Arribo</label>';
                            $output .= '<input type="text" id="nomber_arribo" name="nomber_arribo" value="' . htmlspecialchars($despacho['nomber_arribo']) . '" readonly title="La Modificacion de este campo la realiza el administrador">';
                            $output .= '</div>';
                            $output .= '<div>';
                            $output .= '<br>';
                            $output .= '<label for="fechas">Fecha de Embarque</label>';
                            $output .= '<input type="date" id="fechas" name="fechas" value="' . htmlspecialchars($despacho['fechas']) . '"style="width: 480px; margin: 0 auto; text-align: center;" readonly title="La Modificacion de este campo la realiza el administrador">';
                            $output .= '</div>';
                            $output .= '<div>';
                            $output .= '<br>';
                            $output .= '<label for="tyme_credit">Valor del Pedido</label>';
                            $output .= '<input type="text" id="tyme_credit" name="tyme_credit" value="' . htmlspecialchars($orderFullPrice) . '" readonly title="La Modificacion de este campo la realiza el administrador">';
                            $output .= '</div>';
                            //proceso de facturación
                            $number_invoice = $this->getUploadedFiles7($order_number);
                            $customer = $this->getCustomers($order_number);
                            $output .= '<div>';
                            $output .= '<br>';
                            $output .= '<label for="number_invoice">Número de Factura</label>';
                            $output .= '<input type="text" id="number_invoice" name="number_invoice"  value="' . htmlspecialchars($number_invoice[0]->number_invoice) . '" readonly title="La Modificacion de este campo la realiza el administrador">';
                            $output .= '</div>';
                            date_default_timezone_set('America/Bogota');
                            $daysToAdd = intval($customerPaymentTime);
                            $timestampFechaArribo = strtotime($despacho['fechas']);
                            $newTimestamp = strtotime("+$daysToAdd days +1 day", $timestampFechaArribo);
                            $newDate = date('Y-m-d', $newTimestamp);
                            $output .= '<div>';
                            $output .= '<br>';
                            $output .= '<label for="nueva_fecha">Fecha de Pago</label>';
                            $output .= '<input type="date" id="nueva_fecha" name="nueva_fecha" value="' . htmlspecialchars($newDate) . '"style="width: 480px; margin: 0 auto; text-align: center;" readonly title="La Modificacion de este campo la realiza el administrador">';
                            $output .= '</div>';
                            $output .= '<div class="' . $addon_id . ' container">';
                            if (!empty($number_invoice[0]->invoice)) {
                                $output .= '<br>';
                                foreach ($number_invoice as $invoice) {
                                    $filePathRelative = $this->getUploadedFiles8($order_number, $invoice->number_invoice);
                                    $invoicePath = $invoice->invoice;
                                    if (!empty($invoicePath)) {
                                        $output .= '<div class="' . $addon_id . ' img-container2">' .
                                            '<span class="invoice-number">Factura</span>' .
                                            '<a href="https://mwt.one' . $invoicePath . '" target="_blank">' .
                                            '<img src="https://mwt.one/images/estatus/documento.png" alt="Icono de factura"  width="180" height="180">' .
                                            '</a>' .
                                            '</div>';
                                    }
                                }
                            }
                            if (!empty($filePathRelatives2)) {
                                $output .= '<br>';
                                foreach ($filePathRelatives2 as $filePathRelative) {
                                    $certificadoPath = $filePathRelative->certificado;
                                    if (!empty($certificadoPath)) {
                                        $output .= '<div class="' . $addon_id . ' img-container2">' .
                                            '<span class="invoice-number">Certificado</span>' .
                                            '<a href="https://mwt.one' . $certificadoPath . '" target="_blank">' .
                                            '<img src="https://mwt.one/images/estatus/documento.png" alt="Icono de certificado"  width="180" height="180">' .
                                            '</a>' .
                                            '</div>';
                                    }
                                }
                            }
                            $guias = $this->getUploadedFiles5($order_number);
                            if (!empty($guias)) {
                                $output .= '<br>';
                                foreach ($guias as $guia) {
                                    $filePathRelative3 = $this->getUploadedFiles6($order_number, $guia->number_guia);
                                    if (!empty($filePathRelative3)) {
                                        $output .= '<div class="' . $addon_id . ' img-container2">' .
                                            '<span class="invoice-number">Guia</span>' .
                                            '<a href="https://mwt.one' . $filePathRelative3 . '" target="_blank">' .
                                            '<img src="https://mwt.one/images/estatus/documento.png" alt="Icono de Guia"  width="180" height="180">' .
                                            '</a>' .
                                            '</div>';
                                    }
                                }
                            }
                            $output .= '</div>';
                        }
                    } elseif ($methodo == 'Maritimo') {
                        if ($titulo == 'Administrator') {
                            $despacho = $this->getdespacho($order_number);
                            $output .= '<div>';
                            $output .= '<br>';
                            $output .= '<label for="nombre_naviera">Nombre Naviera</label>';
                            $output .= '<input type="text" id="nombre_naviera" name="nomber" value="' . htmlspecialchars($despacho['nomber']) . '"';
                            $output .= ' oninput="validateNavieraName(this)" required>';
                            $output .= '<span id="nombre_naviera_error" style="color: red;"></span>';
                            $output .= '</div>';
                            $output .= '<script>';
                            $output .= 'function validateNavieraName(input) {';
                            $output .= '    var pattern = /^[a-zA-ZáéíóúÁÉÍÓÚüÜñÑ\s]+$/;';
                            $output .= '    var errorMessage = "Solo se permiten letras.";';
                            $output .= '    var errorElement = document.getElementById("nombre_naviera_error");';
                            $output .= '    if (!pattern.test(input.value)) {';
                            $output .= '        errorElement.textContent = errorMessage;';
                            $output .= '    } else {';
                            $output .= '        errorElement.textContent = "";';
                            $output .= '    }';
                            $output .= '}';
                            $output .= '</script>';
                            $output .= '<div>';
                            $output .= '<br>';
                            $output .= '<label for="number_bl">Número de Bill of Lading (B/L)</label>';
                            $output .= '<input type="text" id="number_bl" name="number_guia" value="' . htmlspecialchars($despacho['number_guia']) . '"';
                            $output .= ' oninput="validateBOLNumber(this)" required>';
                            $output .= '<span id="number_bl_error" style="color: red;"></span>';
                            $output .= '</div>';
                            $output .= '<script>';
                            $output .= 'function validateBOLNumber(input) {';
                            $output .= '    var pattern = /^[0-9]+$/;';
                            $output .= '    var errorMessage = "Solo se permiten números.";';
                            $output .= '    var errorElement = document.getElementById("number_bl_error");';
                            $output .= '    if (!pattern.test(input.value)) {';
                            $output .= '        errorElement.textContent = errorMessage;';
                            $output .= '    } else {';
                            $output .= '        errorElement.textContent = "";';
                            $output .= '    }';
                            $output .= '}';
                            $output .= '</script>';
                            $output .= '<div>';
                            $output .= '<label for="country">Puerto Origen</label>';
                            $output .= '<input type="text" list="nomber_despacho" id="country" name="country" value="' . htmlspecialchars($despacho['nomber_despacho']) . '">';
                            $output .= '<datalist id="nomber_despacho"></datalist>';
                            $output .= '<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
                                <script>
                                var delayTimer;
                                $(document).ready(function() {
                                    $("#country").on("input", function() {
                                        var query = $(this).val();
                                        if (query != "") {
                                            clearTimeout(delayTimer);
                                            delayTimer = setTimeout(function() {
                                                $.ajax({
                                                    url: window.location.href,
                                                    method: "POST",
                                                    data: "query=" + query, // Modify the data format
                                                    success: function(data) {
                                                        console.log("Response:", data);
                                                        $("#nomber_despacho").html(data);
                                                    },
                                                    error: function(jqXHR, textStatus, errorThrown) {
                                                        console.error("AJAX error:", textStatus, errorThrown);
                                                        console.log("Response:", jqXHR.responseText);
                                                    }
                                                });
                                            }, 1000); // Will do the ajax stuff after 1000 ms, or 1 s
                                        }
                                    });
                                });
                                </script>';

                            function getpuerto($query2)
                            {
                                $db = JFactory::getDbo();
                                $query = $db->getQuery(true);
                                $query->select($db->quoteName(array('p.Code', 'p.Name', 'p.Country')));
                                $query->from($db->quoteName('josmwt_puertos', 'p'));
                                $query->where(
                                    $db->quoteName('p.Code') . " LIKE " . $db->quote('%' . $query2 . '%') . " OR " .
                                        $db->quoteName('p.Name') . " LIKE " . $db->quote('%' . $query2 . '%') . " OR " .
                                        $db->quoteName('p.Country') . " LIKE " . $db->quote('%' . $query2 . '%')
                                );
                                $db->setQuery($query);
                                try {
                                    $results = $db->loadAssocList();
                                } catch (Exception $e) {
                                    error_log('Database error: ' . $e->getMessage());
                                    return;
                                }
                                $output = '';
                                if (!empty($results)) {
                                    foreach ($results as $result) {
                                        $output .= '<option value="' . $result['Code'] . ' - ' . $result['Name'] . ' (' . $result['Country'] . ')">' . $result['Name'] . ' (' . $result['Country'] . ')</option>';
                                    }
                                } else {
                                    $output .= '<option value="">No results found</option>';
                                }

                                echo $output;
                            }

                            if (isset($_POST['query'])) {
                                error_log('Query received: ' . $_POST['query']);
                                $query2 = $_POST['query'];
                                getpuerto($query2);
                                exit();
                            }
                            $output .= '</div>';
                            $output .= '<div>';
                            $output .= '<label for="country2">Puerto Arribo</label>';
                            $output .= '<input  type="text" list="nomber_arribo" id="country2" name="country2" value="' . htmlspecialchars($despacho['nomber_arribo']) . '">';
                            $output .= '<datalist id="nomber_arribo"></datalist>';
                            $output .= '<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
                    <script>
                    $(document).ready(function() {
                        $("#country2").on("input", function() {
                            var query2 = $(this).val();
                            if (query2 != "") {
                                $.ajax({
                                    url: window.location.href,
                                    method: "POST",
                                    data: "query2=" + query2, // Modify the data format
                                    success: function(data) {
                                        console.log("Response:", data);
                                        $("#nomber_arribo").html(data);
                                    },
                                    error: function(jqXHR, textStatus, errorThrown) {
                                        console.error("AJAX error:", textStatus, errorThrown);
                                        console.log("Response:", jqXHR.responseText);
                                    }
                                });
                            }
                        });
                    });
                    </script>';
                            if (isset($_POST['query2'])) {
                                error_log('Query received: ' . $_POST['query2']);
                                $query2 = $_POST['query2'];
                                getpuerto($query2);
                                exit();
                            }
                            $output .= '</div>';
                            $output .= '<div>';
                            $output .= '<br>';
                            $output .= '<label for="fechas">Fecha de Embarque</label>';
                            $output .= '<input type="date" id="fechas" name="fechas" value="' . htmlspecialchars($despacho['fechas']) . '"style="width: 480px; margin: 0 auto; text-align: center;">';
                            $output .= '</div>';
                            $guias = $this->getUploadedFiles5($order_number);
                            if (empty($guias)) {
                                $output .= '<div>';
                                $output .= '<br>';
                                $output .= '<label for="document_upload">Subir Guía Naviera </label>';
                                $output .= '<input type="file" id="document_upload" name="document_upload" accept=".pdf">';
                                $output .= '<p>Solo se permiten archivos .pdf</p>';
                                $output .= '</div>';
                            }

                            $output .= '<div>';
                            $output .= '<br>';
                            $output .= '<label for="additional_info">Información adicional</label>';
                            $output .= '<textarea class="form-control" id="additional_info" name="additional_info" oninput="validateAdditionalInfo(this)" required>';
                            $output .= htmlspecialchars($despacho['adiccional']);
                            $output .= '</textarea>';
                            $output .= '<span id="additional_info_error" style="color: red;"></span>';
                            $output .= '<span id="additional_info_counter"></span>';
                            $output .= '</div>';
                            $output .= '<script>';
                            $output .= 'function validateAdditionalInfo(input) {';
                            $output .= '    var maxLength = 500;';
                            $output .= '    var errorMessage = "La información adicional no debe exceder los " + maxLength + " caracteres.";';
                            $output .= '    var errorElement = document.getElementById("additional_info_error");';
                            $output .= '    var counterElement = document.getElementById("additional_info_counter");';
                            $output .= '    if (input.value.length > maxLength) {';
                            $output .= '        input.setCustomValidity(errorMessage);';
                            $output .= '        errorElement.textContent = errorMessage;';
                            $output .= '    } else {';
                            $output .= '        input.setCustomValidity("");';
                            $output .= '        errorElement.textContent = "";';
                            $output .= '    }';
                            $output .= '    counterElement.textContent = "Caracteres restantes: " + (maxLength - input.value.length);';
                            $output .= '}';
                            $output .= '</script>';
                            $output .= '<div>';
                            $output .= '<br>';
                            $output .= '<label for="tyme_credit">Valor del Pedido</label>';
                            $output .= '<input type="text" id="tyme_credit" name="tyme_credit" value="' . htmlspecialchars($orderFullPrice) . '" readonly>';
                            $output .= '</div>';
                            //Proceso de factura
                            $number_invoice = $this->getUploadedFiles7($order_number);
                            $customer = $this->getCustomers($order_number);
                            $output .= '<div>';
                            $output .= '<br>';
                            $output .= '<label for="number_invoice">Número de Factura</label>';
                            $output .= '<input type="text" id="number_invoice" name="number_invoice" value="' . htmlspecialchars($number_invoice[0]->number_invoice) . '"';
                            $output .= ' oninput="validateInvoiceNumber(this)" ';
                            $output .= '>';
                            $output .= '<span id="number_invoice_error" style="color: red;"></span>';
                            $output .= '</div>';
                            $output .= '<script>';
                            $output .= 'function validateInvoiceNumber(input) {';
                            $output .= '    var pattern = /^[0-9a-zA-Z-]+$/;';
                            $output .= '    var errorMessage = "Solo se permiten números y el guion \'-\'.";';
                            $output .= '    var errorElement = document.getElementById("number_invoice_error");';
                            $output .= '    if (!pattern.test(input.value)) {';
                            $output .= '        errorElement.textContent = errorMessage;';
                            $output .= '    } else {';
                            $output .= '        errorElement.textContent = "";';
                            $output .= '    }';
                            $output .= '}';
                            $output .= '</script>';
                            $customerPaymentTime = $customer[0]['customer_payment_time'];
                            $output .= '<div>';
                            $output .= '<br>';
                            $output .= '<label for="tyme_credit">Días de Crédito Cliente</label>';
                            $output .= '<input type="text" id="tyme_credit" name="tyme_credit" value="' . htmlspecialchars($customerPaymentTime) . '"readonly>';
                            $output .= '</div>';
                            $output .= '<div>';
                            $output .= '<br>';
                            $output .= '<label for="nueva_fecha">Fecha de Pago</label>';
                            $output .= '<input type="date" id="nueva_fecha" name="nueva_fecha" value="' . htmlspecialchars($newDate) . '"style="width: 480px; margin: 0 auto; text-align: center;" readonly>';
                            $output .= '</div>';
                            $output .= '<script>';
                            $output .= 'document.addEventListener("DOMContentLoaded", calcularFechaPago);';
                            $output .= 'document.getElementById("fechas").addEventListener("change", calcularFechaPago);';
                            $output .= 'function calcularFechaPago() {';
                            $output .= '    var fechaEmbarque = document.getElementById("fechas").value;';
                            $output .= '    var daysToAdd = ' . intval($customerPaymentTime) . ';';
                            $output .= '    var timestampFechaArribo = Date.parse(fechaEmbarque);';
                            $output .= '    var newTimestamp = timestampFechaArribo + daysToAdd * 24 * 60 * 60 * 1000;';
                            $output .= '    var newDate = new Date(newTimestamp).toISOString().split("T")[0];';
                            $output .= '    document.getElementById("nueva_fecha").value = newDate;';
                            $output .= '}';
                            $output .= '</script>';
                            if (empty($number_invoice[0]->invoice)) {
                                $output .= '<div>';
                                $output .= '<br>';
                                $output .= '<label for="document_upload2">Subir Factura</label>';
                                $output .= '<input type="file" id="document_upload2" name="document_upload2" accept=".pdf">';
                                $output .= '<p>Solo se permiten archivos .pdf</p>';
                                $output .= '</div>';
                            }
                            $filePathRelatives2 = $this->getUploadedFiles9($order_number);
                            if (empty($filePathRelatives2) || empty($filePathRelatives2[0]->certificado)) {
                                $output .= '<div>';
                                $output .= '<br>';
                                $output .= '<label for="document_upload3">Subir Certificado Origen</label>';
                                $output .= '<input type="file" id="document_upload3" name="document_upload3" accept=".pdf">';
                                $output .= '<p>Solo se permiten archivos .pdf</p>';
                                $output .= '</div>';
                            }
                            $output .= '<div class="' . $addon_id . ' container">'; // Inicio del div contenedor
                            if (!empty($number_invoice[0]->invoice)) {
                                $output .= '<br>';
                                foreach ($number_invoice as $invoice) {
                                    $filePathRelative = $this->getUploadedFiles8($order_number, $invoice->number_invoice);
                                    $invoicePath = $invoice->invoice;
                                    if (!empty($invoicePath)) {
                                        $output .= '<div class="' . $addon_id . ' img-container2">' .
                                            '<span class="invoice-number">Factura</span>' .
                                            '<a href="https://mwt.one' . $invoicePath . '" target="_blank">' .
                                            '<img src="https://mwt.one/images/estatus/documento.png" alt="Icono de factura"  width="180" height="180">' .
                                            '</a>' .
                                            '<div class="button-group">' .
                                            '<button type="submit" name="delete_invoice" class="btn btn-danger">' .
                                            '<i class="fas fa-times"></i>' .
                                            '</button>' .
                                            '</div>' .
                                            '</div>';
                                    }
                                }
                            }
                            if (!empty($filePathRelatives2)) {
                                $output .= '<br>';
                                foreach ($filePathRelatives2 as $filePathRelative) {
                                    $certificadoPath = $filePathRelative->certificado;
                                    if (!empty($certificadoPath)) {
                                        $output .= '<div class="' . $addon_id . ' img-container2">' .
                                            '<span class="invoice-number">Certificado</span>' .
                                            '<a href="https://mwt.one' . $certificadoPath . '" target="_blank">' .
                                            '<img src="https://mwt.one/images/estatus/documento.png" alt="Icono de certificado"  width="180" height="180">' .
                                            '</a>' .
                                            '<div class="button-group">' .
                                            '<button type="submit" name="delete_certificado" class="btn btn-danger">' .
                                            '<i class="fas fa-times"></i>' .
                                            '</button>' .
                                            '</div>' .
                                            '</div>';
                                    }
                                }
                            }
                            $guias = $this->getUploadedFiles5($order_number);
                            if (!empty($guias)) {
                                $output .= '<br>';
                                foreach ($guias as $guia) {
                                    $filePathRelative3 = $this->getUploadedFiles6($order_number, $guia->number_guia);
                                    if (!empty($filePathRelative3)) {
                                        $output .= '<div class="' . $addon_id . ' img-container2">' .
                                            '<span class="invoice-number">Guia</span>' .
                                            '<a href="https://mwt.one' . $filePathRelative3 . '" target="_blank">' .
                                            '<img src="https://mwt.one/images/estatus/documento.png" alt="Icono de Guia"  width="180" height="180">' .
                                            '</a>' .
                                            '<div class="button-group">' .
                                            '<button type="submit" name="delete_guia" class="btn btn-danger">' .
                                            '<i class="fas fa-times"></i>' .
                                            '</button>' .
                                            '</div>' .
                                            '</div>';
                                    }
                                }
                            }
                            $output .= '</div>';
                            //fin de proceso
                            $output .= '<div class="button-group">';
                            $output .= '<br>';
                            $output .= '<input type="submit" name="despacho_form" class="btn btn-warning" value="Guardar">';
                            $output .= '</div>';
                            $output .= '<input type="hidden" name="status" value="transito">';
                            $output .= '<div class="button-group">';
                            $output .= '<br>';
                            $output .= '<input type="submit" name="status_form" class="btn btn-primary" value="Enviar">';
                            $output .= '</div>';
                        } else {
                            $despacho = $this->getdespacho($order_number);
                            $output .= '<div>';
                            $output .= '<br>';
                            $output .= '<label for="nomber">Nombre Naviera</label>';
                            $output .= '<input type="text" id="nomber" name="nomber" value="' . htmlspecialchars($despacho['nomber']) . '" readonly title="La Modificacion de este campo la realiza el administrador">';
                            $output .= '</div>';
                            $output .= '<div>';
                            $output .= '<br>';
                            $output .= '<label for="number_guia">Número de Air Waybill (AWB)</label>';
                            $output .= '<input type="text" id="number_guia" name="number_guia" value="' . htmlspecialchars($despacho['number_guia']) . '"readonly title="La Modificacion de este campo la realiza el administrador">';
                            $output .= '</div>';
                            $output .= '<div>';
                            $output .= '<br>';
                            $output .= '<label for="nomber_despacho">Puerto de Despacho</label>';
                            $output .= '<input type="text" id="nomber_despacho" name="nomber_despacho" value="' . htmlspecialchars($despacho['nomber_despacho']) . '"readonly title="La Modificacion de este campo la realiza el administrador">';
                            $output .= '</div>';
                            $output .= '<div>';
                            $output .= '<br>';
                            $output .= '<label for="nomber_arribo">Puerto de Arribo</label>';
                            $output .= '<input type="text" id="nomber_arribo" name="nomber_arribo" value="' . htmlspecialchars($despacho['nomber_arribo']) . '"readonly title="La Modificacion de este campo la realiza el administrador">';
                            $output .= '</div>';
                            $output .= '<div>';
                            $output .= '<br>';
                            $output .= '<label for="additional_info">Información adicional</label>';
                            $output .= '<input type="text" id="adicional" name="adicional" value="' . htmlspecialchars($despacho['adiccional']) . '" readonly title="La Modificacion de este campo la realiza el administrador">';
                            $output .= '</div>';
                            $output .= '<div>';
                            $output .= '<br>';
                            $output .= '<label for="fechas">Fecha de Embarque</label>';
                            $output .= '<input type="date" id="fechas" name="fechas" value="' . htmlspecialchars($despacho['fechas']) . '"style="width: 480px; margin: 0 auto; text-align: center;" readonly title="La Modificacion de este campo la realiza el administrador">';
                            $output .= '</div>';
                            $output .= '<div>';
                            $output .= '<br>';
                            $output .= '<label for="tyme_credit">Valor del Pedido</label>';
                            $output .= '<input type="text" id="tyme_credit" name="tyme_credit" value="' . htmlspecialchars($orderFullPrice) . '" readonly title="La Modificacion de este campo la realiza el administrador">';
                            $output .= '</div>';
                            //proceso de facturación
                            $number_invoice = $this->getUploadedFiles7($order_number);
                            $customer = $this->getCustomers($order_number);
                            $output .= '<div>';
                            $output .= '<br>';
                            $output .= '<label for="number_invoice">Número de Factura</label>';
                            $output .= '<input type="text" id="number_invoice" name="number_invoice"  value="' . htmlspecialchars($number_invoice[0]->number_invoice) . '" readonly title="La Modificacion de este campo la realiza el administrador">';
                            $output .= '</div>';
                            date_default_timezone_set('America/Bogota');
                            $daysToAdd = intval($customerPaymentTime);
                            $timestampFechaArribo = strtotime($despacho['fechas']);
                            $newTimestamp = strtotime("+$daysToAdd days +1 day", $timestampFechaArribo);
                            $newDate = date('Y-m-d', $newTimestamp);
                            $output .= '<div>';
                            $output .= '<br>';
                            $output .= '<label for="nueva_fecha">Fecha de Pago</label>';
                            $output .= '<input type="date" id="nueva_fecha" name="nueva_fecha" value="' . htmlspecialchars($newDate) . '"style="width: 480px; margin: 0 auto; text-align: center;" readonly title="La Modificacion de este campo la realiza el administrador">';
                            $output .= '</div>';
                            $output .= '<div class="' . $addon_id . ' container">'; // Inicio del div contenedor
                            if (!empty($number_invoice[0]->invoice)) {
                                $output .= '<br>';
                                foreach ($number_invoice as $invoice) {
                                    $filePathRelative = $this->getUploadedFiles8($order_number, $invoice->number_invoice);
                                    $invoicePath = $invoice->invoice;
                                    if (!empty($invoicePath)) {
                                        $output .= '<div class="' . $addon_id . ' img-container2">' .
                                            '<span class="invoice-number">Factura</span>' .
                                            '<a href="https://mwt.one' . $invoicePath . '" target="_blank">' .
                                            '<img src="https://mwt.one/images/estatus/documento.png" alt="Icono de factura"  width="180" height="180">' .
                                            '</a>' .
                                            '</div>';
                                    }
                                }
                            }
                            $filePathRelatives2 = $this->getUploadedFiles9($order_number);
                            if (!empty($filePathRelatives2)) {
                                $output .= '<br>';
                                foreach ($filePathRelatives2 as $filePathRelative) {
                                    $certificadoPath = $filePathRelative->certificado;
                                    if (!empty($certificadoPath)) {
                                        $output .= '<div class="' . $addon_id . ' img-container2">' .
                                            '<span class="invoice-number">Certificado</span>' .
                                            '<a href="https://mwt.one' . $certificadoPath . '" target="_blank">' .
                                            '<img src="https://mwt.one/images/estatus/documento.png" alt="Icono de certificado"  width="180" height="180">' .
                                            '</a>' .
                                            '</div>';
                                    }
                                }
                            }
                            $guias = $this->getUploadedFiles5($order_number);
                            if (!empty($guias)) {
                                $output .= '<br>';
                                foreach ($guias as $guia) {
                                    $filePathRelative3 = $this->getUploadedFiles6($order_number, $guia->number_guia);
                                    if (!empty($filePathRelative3)) {
                                        $output .= '<div class="' . $addon_id . ' img-container2">' .
                                            '<span class="invoice-number">Guia</span>' .
                                            '<a href="https://mwt.one' . $filePathRelative3 . '" target="_blank">' .
                                            '<img src="https://mwt.one/images/estatus/documento.png" alt="Icono de Guia"  width="180" height="180">' .
                                            '</a>' .
                                            '</div>';
                                    }
                                }
                            }
                            $output .= '</div>';
                        }
                    }
                }
                $output .= '</section>';
                $output .= '<script>';
                $output .= 'document.addEventListener("DOMContentLoaded", function() {';
                $output .= '    var tab1 = document.querySelector(".tab.blue-tab#tab1");';
                $output .= '    var tab2 = document.querySelector(".tab.green-tab#tab2");';
                $output .= '    var tab3 = document.querySelector(".tab.purple-tab#tab3");';
                $output .= '    var tab4 = document.querySelector(".tab.pink-tab#tab4");';
                $output .= '    var section1 = document.getElementById("section1");';
                $output .= '    var section2 = document.getElementById("section2");';
                $output .= '    var section3 = document.getElementById("section3");';
                $output .= '    var section4 = document.getElementById("section4");';
                $output .= '    ';
                $output .= '    tab1.addEventListener("click", function() {';
                $output .= '        section1.style.display = "block";';
                $output .= '        section2.style.display = "none";';
                $output .= '        section3.style.display = "none";';
                $output .= '        section4.style.display = "none";';
                $output .= '    });';
                $output .= '    ';
                $output .= '    tab2.addEventListener("click", function() {';
                $output .= '        section1.style.display = "none";';
                $output .= '        section2.style.display = "block";';
                $output .= '        section3.style.display = "none";';
                $output .= '        section4.style.display = "none";';
                $output .= '    });';
                $output .= '    ';
                $output .= '    tab3.addEventListener("click", function() {';
                $output .= '        section1.style.display = "none";';
                $output .= '        section2.style.display = "none";';
                $output .= '        section3.style.display = "block";';
                $output .= '        section4.style.display = "none";';
                $output .= '    });';
                $output .= '    ';
                $output .= '    tab4.addEventListener("click", function() {';
                $output .= '        section1.style.display = "none";';
                $output .= '        section2.style.display = "none";';
                $output .= '        section3.style.display = "none";';
                $output .= '        section4.style.display = "block";';
                $output .= '    });';
                $output .= '});';
                $output .= '</script>';
            } else {
                $output .= '<div>';
                $output .= '<p>Lo siento, no tienes permiso para ver este pedido.</p>';
                $output .= '</div>';
            }
        } elseif ($status == 'transito') {
            if ($this->isSameCustomer($order_number, $userId)) {
                $number_purchase = $this->getNumber($order_number);
                $filePathRelatives = $this->getUploadedFiles($order_number);
                $number = $this->getsap($order_number);
                $fecha = $this->getfechapro($order_number);
                $packs = $this->getUploadedFiles10($order_number);
                $datos = $this->getmethodoshipping($order_number);
                $envios = $this->getUploadedFiles15($order_number);
                $despacho = $this->getdespacho($order_number);
                $guias = $this->getUploadedFiles5($order_number);
                $number_invoice = $this->getUploadedFiles7($order_number);
                $number_invoice2 = $number_invoice[0]->number_invoice;
                $packs2 = $this->getUploadedFiles3($order_number);
                $output .= '<div style="display: flex; justify-content: space-around;">';
                $output .= '<div class="tab blue-tab" id="tab1" style="text-align: center;">';
                $output .= '<span>Creacion/Credito</span><br>';
                $output .= '<div style="display: inline-block; margin-left: 3px;">';
                $output .= '<img src="https://mwt.one/images/estatus/verde.png" alt="Verde" title="Completo" style="width: 20px; height: 20px;">';
                $output .= '</div>';
                $output .= '</div>';
                $output .= '<div class="tab green-tab" id="tab2" style="text-align: center;">';
                $output .= '<span>Elaboración</span><br>';
                $output .= '<div style="display: inline-block; margin-left: 3px;">';
                if (!empty($number) && !empty($fecha)) {
                    // Si ambos tienen datos, mostrar la imagen verde
                    $output .= '<img src="https://mwt.one/images/estatus/verde.png" alt="Verde" title="Completo" style="width: 20px; height: 20px;">';
                } elseif (empty($number) && empty($fecha)) {
                    // Si ambos están vacíos, mostrar la imagen roja
                    $output .= '<img src="https://mwt.one/images/estatus/rojo.png" alt="Rojo" title="Sin Información" style="width: 20px; height: 20px;">';
                } else {
                    // Si al menos uno de ellos tiene datos, mostrar la imagen amarilla
                    $output .= '<img src="https://mwt.one/images/estatus/amarilloo.png" alt="Amarillo" title="Incompleto" style="width: 30px; height: 20px;">';
                }
                $output .= '</div>';
                $output .= '</div>';
                $output .= '<div class="tab purple-tab" id="tab3"  style="text-align: center;">';
                $output .= '<span>Preparación</span><br>';
                $output .= '<div style="display: inline-block; margin-left: 3px;">';
                if (!empty($packs) && !empty($envios)) {
                    // Si ambos tienen datos, mostrar la imagen verde
                    $output .= '<img src="https://mwt.one/images/estatus/verde.png" alt="Verde" title="Completo" style="width: 20px; height: 20px;">';
                } elseif (empty($packs) && empty($envios)) {
                    // Si ambos están vacíos, mostrar la imagen roja
                    $output .= '<img src="https://mwt.one/images/estatus/rojo.png" alt="Rojo" title="Sin Información" style="width: 20px; height: 20px;">';
                } else {
                    // Si al menos uno de ellos tiene datos, mostrar la imagen amarilla
                    $output .= '<img src="https://mwt.one/images/estatus/amarilloo.png" alt="Amarillo" title="Incompleto" style="width: 30px; height: 20px;">';
                }
                $output .= '</div>';
                $output .= '</div>';
                $output .= '<div class="tab pink-tab" id="tab4">';
                $output .= '<span>Despacho</span><br>';
                $output .= '<div style="display: inline-block; margin-left: 3px;">';
                if (!empty($despacho) && !empty($number_invoice2)) {
                    // Si ambos tienen datos, mostrar la imagen verde
                    $output .= '<img src="https://mwt.one/images/estatus/verde.png" alt="Verde" title="Completo" style="width: 20px; height: 20px;">';
                } elseif (empty($despacho) && empty($number_invoice2)) {
                    // Si ambos están vacíos, mostrar la imagen roja
                    $output .= '<img src="https://mwt.one/images/estatus/rojo.png" alt="Rojo" title="Sin Información" style="width: 20px; height: 20px;">';
                } else {
                    // Si al menos uno de ellos tiene datos, mostrar la imagen amarilla
                    $output .= '<img src="https://mwt.one/images/estatus/amarilloo.png" alt="Amarillo" title="Incompleto" style="width: 30px; height: 20px;">';
                }
                $output .= '</div>';
                $output .= '</div>';
                $output .= '<div class="tab orange-tab" id="tab5">';
                $output .= '<span>Transito</span><br>';
                $output .= '<div style="display: inline-block; margin-left: 3px;">';
                if (empty($despacho['fecha_arribo']) && empty($despacho['puerto_intermedio']) && empty($packs2)) {
                    // Si todos están vacíos, mostrar la imagen roja
                    $output .= '<img src="https://mwt.one/images/estatus/rojo.png" alt="Rojo" title="Sin Información" style="width: 20px; height: 20px;">';
                } elseif (!empty($despacho['fecha_arribo']) && !empty($despacho['puerto_intermedio']) && !empty($packs2)) {
                    // Si al menos uno tiene datos, mostrar la imagen amarilla
                    $output .= '<img src="https://mwt.one/images/estatus/verde.png" alt="Verde" title="Completo" style="width: 20px; height: 20px;">';
                } elseif (!empty($despacho['fecha_arribo']) || !empty($despacho['puerto_intermedio']) || !empty($packs2)) {
                    // Si todos tienen datos, mostrar la imagen verde
                    $output .= '<img src="https://mwt.one/images/estatus/amarilloo.png" alt="Amarillo" title="Incompleto" style="width: 30px; height: 20px;">';
                }



                $output .= '</div>';
                $output .= '</div>';
                $output .= '<div class="tab yellow-tab" id="tab6">Status/Pago</div>';
                $output .= '</div>';
                $output .= '<script>';
                $output .= 'let currentImage = null;';
                $output .= 'const addActiveClass = (tab) => {';
                $output .= '  if (currentImage) {';
                $output .= '    currentImage.classList.remove("active3");';
                $output .= '  }';
                $output .= '  tab.classList.add("active3");';
                $output .= '  currentImage = tab;';
                $output .= '};';
                $output .= 'document.getElementById("tab1").addEventListener("click", function() {';
                $output .= '  addActiveClass(this);';
                $output .= '});';
                $output .= 'document.getElementById("tab2").addEventListener("click", function() {';
                $output .= '  addActiveClass(this);';
                $output .= '});';
                $output .= 'document.getElementById("tab3").addEventListener("click", function() {';
                $output .= '  addActiveClass(this);';
                $output .= '});';
                $output .= 'document.getElementById("tab4").addEventListener("click", function() {';
                $output .= '  addActiveClass(this);';
                $output .= '});';
                $output .= 'document.getElementById("tab5").addEventListener("click", function() {';
                $output .= '  addActiveClass(this);';
                $output .= '});';
                $output .= 'document.addEventListener("DOMContentLoaded", function() {';
                $output .= '  const tab5 = document.getElementById("tab5");';
                $output .= '  addActiveClass(tab5);';
                $output .= '});';
                $output .= '</script>';

                $output .= '<section id="section1" class="tab-content-1" style="display:none;">';
                $fecha = $this->getfechapro($order_number);
                $output .= '<div>';
                $output .= '<br>';
                $output .= '<label for="number_pedido">Número de Pedido</label>';
                $output .= '<input type="text" id="number_pedido" name="number_pedido" value="' . htmlspecialchars($order_number) . '"readonly';
                $output .= '</div>';
                $output .= '<div>';
                $output .= '<br>';
                $output .= '<label for="number_purchase">Número de Orden de Compra</label>';
                $number_purchase = $this->getNumber($order_number);
                $output .= '<input type="text" id="number_purchase" name="number_purchase" value="' . htmlspecialchars($number_purchase) . '" readonly>';
                $output .= '</div>';
                $filePathRelatives = $this->getUploadedFiles($order_number);
                $output .= '<div class="' . $addon_id . ' container">'; // Inicio del div contenedor
                if (!empty($filePathRelatives)) {
                    $output .= '<br>';
                    $output .= '<div class="' . $addon_id . ' img-container2">' .
                        '<span class="invoice-number">OC</span>' .
                        '<a href="https://mwt.one' . $filePathRelatives . '" target="_blank">' .
                        '<img src="https://mwt.one/images/estatus/documento.png" alt="Icono de Guia">' .
                        '</a>' .
                        '</div>';
                }
                $output .= '</div>';
                $customer = $this->getCustomers($order_number);
                $credit = $this->compareCreditAndPrice($order_number);
                $price = $this->getOrder($order_number);
                $orderFullPrice = $price[0]->order_full_price;
                $customerPaymentTime = $customer[0]['customer_payment_time'];
                $customerCredit = $customer[0]['customer_credit'];
                $customername = $customer[0]['customer_name'];
                $output .= '<div>';
                $output .= '<br>';
                $output .= '<label for="tyme_credit">Cliente</label>';
                $output .= '<input type="text" id="tyme_credit" name="tyme_credit" value="' . htmlspecialchars($customername) . '" readonly>';
                $output .= '</div>';
                $output .= '<div>';
                $output .= '<br>';
                $output .= '<label for="tyme_credit">Días de Crédito Cliente</label>';
                $output .= '<input type="text" id="tyme_credit" name="tyme_credit" value="' . htmlspecialchars($customerPaymentTime) . '" readonly>';
                $output .= '</div>';
                $output .= '<div>';
                $output .= '<br>';
                $output .= '<label for="tyme_credit">Crédito Disponible</label>';
                $output .= '<input type="text" id="tyme_credit" name="tyme_credit" value="' . htmlspecialchars($customerCredit) . '" readonly>';
                $output .= '</div>';
                $output .= '<div>';
                $output .= '<br>';
                $output .= '<label for="tyme_credit">Valor del Pedido</label>';
                $output .= '<input type="text" id="tyme_credit" name="tyme_credit" value="' . htmlspecialchars($orderFullPrice) . '" readonly>';
                $output .= '</div>';
                $output .= '</section>';
                $filePathRelative2 = $this->getUploadedFiles4($order_number);
                if (empty($filePathRelative2)) {
                    $output .= '<section id="section2" class="tab-content2-2" style="display:none;">';
                } else {
                    $output .= '<section id="section2" class="tab-content2-1" style="display:none;">';
                }
                $output .= '<div>';
                $output .= '<br>';
                $number = $this->getsap($order_number);
                $output .= '<label for="number_purchase">Número de SAP</label>';
                $output .= '<input type="text" id="number_sap" name="number_sap"  value="' . htmlspecialchars($number['number_sap']) . '"readonly>';
                if (!empty($number['number_preforma'])) {
                    $output .= '<label for="number_purchase">Número de Proforma</label>';
                    $output .= '<input type="text" id="number_preforma" name="number_preforma" value="' . htmlspecialchars($number['number_preforma']) . '"readonly>';
                }
                $output .= '</div>';
                $output .= '<div class="' . $addon_id . ' container">'; // Inicio del div contenedor
                if (!empty($filePathRelative2)) {
                    $output .= '<br>';
                    $output .= '<div class="' . $addon_id . ' img-container2">' .
                        '<span class="invoice-number">Proforma</span>' .
                        '<a href="https://mwt.one' . $filePathRelative2 . '" target="_blank">' .
                        '<img src="https://mwt.one/images/estatus/documento.png" alt="Icono de Guia">' .
                        '</a>' .
                        '</div>';
                }
                $output .= '</div>';
                $output .= '<div>';
                $output .= '<br>';
                $output .= '<label for="fecha_inicio">Fecha Inicio de Elaboración</label>';
                $output .= '<input type="date" id="fechai" name="fechai" value="' . htmlspecialchars($fecha['fechai']) . '" readonly>';
                $output .= '</div>';
                $output .= '<div>';
                $output .= '<br>';
                $output .= '<label for="fecha_final">Fecha Final de Elaboración</label>';
                $output .= '<input type="date" id="fechaf" name="fechaf" value="' . htmlspecialchars($fecha['fechaf']) . '" readonly>';
                $output .= '</div>';
                $output .= '</section>';
                $output .= '<section id="section3" class="tab-content3-1" style="display:none;">';
                $datos = $this->getmethodoshipping($order_number);
                $output .= '<div>';
                $output .= '<br>';
                $output .= '<label for="number_purchase">Nombre Completo</label>';
                $output .= '<input type="text" id="address_firstname" name="address_firstname" value="' . htmlspecialchars($datos['address_firstname']) . ' ' . htmlspecialchars($datos['address_lastname']) . '" readonly>';
                $output .= '</div>';
                $output .= '<div>';
                $output .= '<br>';
                $output .= '<label for="number_purchase">Telefono</label>';
                $output .= '<input type="text" id="address_telephone" name="address_telephone" value="' . htmlspecialchars($datos['address_telephone']) . '" readonly>';
                $output .= '</div>';
                $output .= '<div>';
                $output .= '<br>';
                $output .= '<label for="number_purchase">Dirección</label>';
                $country_parts = explode('_', $datos['address_country']);
                $country_name = $country_parts[1];
                $state_parts = explode('_', $datos['address_state']);
                $state_name = $state_parts[1];
                $state_name2 = $state_parts[2];
                $state_name3 = $state_parts[3];
                $output .= '<input type="text" id="address_country" name="address_country" value="' . htmlspecialchars($country_name) . ' ' . htmlspecialchars($state_name) . ' ' . htmlspecialchars($state_name2) . ' ' . htmlspecialchars($state_name3) . ' ' . htmlspecialchars($datos['address_city']) . ' ' . htmlspecialchars($datos['address_street']) . ' Postal Cod: ' . htmlspecialchars($datos['address_post_code']) . '" readonly>';
                $output .= '</div>';
                $output .= '<div>';
                $output .= '<br>';
                $output .= '<label for="number_purchase">Método de Envío</label>';
                $output .= '<input type="text" id="order_shipping_method" name="order_shipping_method" value="' . htmlspecialchars($datos['order_shipping_method']) . ' " readonly>';
                $output .= '</div>';
                $output .= '<div>';
                $output .= '<br>';
                $output .= '<label for="number_purchase">Incoterms</label>';
                $output .= '<input type="text" id="incoterms" name="incoterms" value="' . htmlspecialchars($datos['Code_incoterms']) . '' . ' - ' . ' ' . htmlspecialchars($datos['Incoterms']) . '" readonly>';
                $output .= '</div>';
                $packs = $this->getUploadedFiles10($order_number);
                $output .= '<div class="' . $addon_id . ' container">'; // Inicio del div contenedor
                if (!empty($packs)) {
                    foreach ($packs as $pack) {
                        $filePathRelative3 = $this->getUploadedFiles11($order_number, $pack->caja);
                        $output .= '<br>';
                        $output .= '<div class="' . $addon_id . ' img-container2">' .
                            '<span class="invoice-number">' . $pack->caja . '</span>' .
                            '<a href="https://mwt.one' . $filePathRelative3 . '" target="_blank">' .
                            '<img src="https://mwt.one/images/estatus/documento.png" alt="Icono de Guia">' .
                            '</a>' .
                            '</div>';
                    }
                }
                $output .= '</div>';
                $output .= '<div>';
                $output .= '<br>';
                $operator = $this->getoperator($order_number);
                $output .= '<label for="operator">Gestor de carga</label>';
                $output .= '<input type="text" id="address_telephone" name="address_telephone" value="' . htmlspecialchars($operator['operator']) . '" readonly>';
                $output .= '</div>';
                $output .= '<div>';
                $output .= '<br>';
                if ($operator['operator'] == 'Cliente') {
                    $output .= '<label for="operator">Detalles</label>';
                    $output .= '<input type="text" id="details" name="details" value="' . htmlspecialchars($operator['details']) . '" readonly>';
                }
                $output .= '</div>';
                $output .= '<div>';
                $output .= '<br>';
                $output .= '<label for="valor">Valor de Envío</label>';
                $output .= '<input type="text" id="valor" name="valor" value="' . htmlspecialchars($datos['order_shipping_price']) . '"readonly>';
                $output .= '</div>';
                $envios = $this->getUploadedFiles15($order_number);
                $output .= '<br>';
                $output .= '<div class="' . $addon_id . ' container">'; // Inicio del div contenedor
                if (!empty($envios)) {
                    foreach ($envios as $envio) {
                        $filePathRelative4 = $this->getUploadedFiles16($order_number);
                        $output .= '<div class="' . $addon_id . ' img-container2">' .
                            '<span class="invoice-number">Envio</span>' .
                            '<a href="https://mwt.one' . $filePathRelative4 . '" target="_blank">' .
                            '<img src="https://mwt.one/images/estatus/documento.png" alt="Icono de Guia">' .
                            '</a>' .
                            '</div>';
                    }
                }
                $output .= '</div>';
                $output .= '</section>';
                $guias = $this->getUploadedFiles5($order_number);
                $number_invoice = $this->getUploadedFiles7($order_number);
                $filePathRelatives2 = $this->getUploadedFiles9($order_number);

                if (!empty($guias) && !empty($guias->number_guia)) {
                    $filePathRelative3 = $this->getUploadedFiles6($order_number, $guias->number_guia);
                }

                if (!empty($filePathRelatives2) && !empty($filePathRelatives2->certificado)) {
                    $certificadoPath = $filePathRelatives2->certificado;
                }

                if (!empty($guias)) {
                    $sectionClass = 'tab-content4-1-1';
                } else {
                    $sectionClass = 'tab-content4';
                }

                if (!empty($guias) && !empty($number_invoice[0]->invoice) && !empty($number_invoice[0]->certificado)) {
                    $sectionClass = 'tab-content4-2-1';
                } elseif (!empty($number_invoice[0]->invoice) && !empty($guias)) {
                    $sectionClass = 'tab-content4-3-1';
                } elseif (!empty($number_invoice[0]->certificado) && !empty($guias)) {
                    $sectionClass = 'tab-content4-4-1';
                } elseif (!empty($number_invoice[0]->invoice) && !empty($number_invoice[0]->certificado)) {
                    $sectionClass = 'tab-content4-5-1';
                }
                $output .= '<section id="section4" class="' . $sectionClass . '" style="display:none;">';

                $methodo = $this->getShipping($order_number);
                if ($operator['operator'] == 'Fabrica') {
                    if ($methodo == 'Aereo') {
                        $despacho = $this->getdespacho($order_number);
                        $output .= '<div style="display: flex; justify-content: center; align-items: center;">';
                        $output .= '<div>';
                        $output .= '<label for="nomber">Aerolínea</label>';
                        $output .= '<br>';
                        $output .= $despacho['nomber'];
                        $output .= '</div>';
                        $output .= '</div>';
                        $output .= '<div>';
                        $output .= '<br>';
                        $output .= '<label for="number_guia">Número de Air Waybill (AWB)</label>';
                        $output .= '<input type="text" id="number_guia" name="number_guia" value="' . htmlspecialchars($despacho['number_guia']) . '" readonly>';
                        $output .= '</div>';
                        $output .= '<div>';
                        $output .= '<br>';
                        $output .= '<label for="nomber_despacho">Aeropuerto de Despacho</label>';
                        $output .= '<input type="text" id="nomber_despacho" name="nomber_despacho" value="' . htmlspecialchars($despacho['nomber_despacho']) . '" readonly>';
                        $output .= '</div>';
                        $output .= '<div>';
                        $output .= '<br>';
                        $output .= '<label for="nomber_arribo">Aeropuerto de Arribo</label>';
                        $output .= '<input type="text" id="nomber_arribo" name="nomber_arribo" value="' . htmlspecialchars($despacho['nomber_arribo']) . '" readonly>';
                        $output .= '</div>';
                        $output .= '<div>';
                        $output .= '<br>';
                        $output .= '<label for="fechas">Fecha de Embarque</label>';
                        $output .= '<input type="date" id="fechas" name="fechas" value="' . htmlspecialchars($despacho['fechas']) . '"style="width: 480px; margin: 0 auto; text-align: center;" readonly>';
                        $output .= '</div>';
                    } elseif ($methodo == 'Maritimo') {
                        $despacho = $this->getdespacho($order_number);
                        $output .= '<div>';
                        $output .= '<br>';
                        $output .= '<label for="nomber">Nombre Naviera</label>';
                        $output .= '<input type="text" id="nomber" name="nomber" value="' . htmlspecialchars($despacho['nomber']) . '" readonly>';
                        $output .= '</div>';
                        $output .= '<div>';
                        $output .= '<br>';
                        $output .= '<label for="number_guia">Número de Bill of Lading (B/L)</label>';
                        $output .= '<input type="text" id="number_guia" name="number_guia" value="' . htmlspecialchars($despacho['number_guia']) . '"readonly>';
                        $output .= '</div>';
                        $output .= '<div>';
                        $output .= '<br>';
                        $output .= '<label for="nomber_despacho">Puerto de Despacho</label>';
                        $output .= '<input type="text" id="nomber_despacho" name="nomber_despacho" value="' . htmlspecialchars($despacho['nomber_despacho']) . '"readonly>';
                        $output .= '</div>';
                        $output .= '<div>';
                        $output .= '<br>';
                        $output .= '<label for="nomber_arribo">Puerto de Arribo</label>';
                        $output .= '<input type="text" id="nomber_arribo" name="nomber_arribo" value="' . htmlspecialchars($despacho['nomber_arribo']) . '"readonly>';
                        $output .= '</div>';
                        $output .= '<div>';
                        $output .= '<br>';
                        $output .= '<label for="additional_info">Información adicional</label>';
                        $output .= '<input type="text" id="adicional" name="adicional" value="' . htmlspecialchars($despacho['adiccional']) . '" readonly>';
                        $output .= '</div>';
                        $output .= '<div>';
                        $output .= '<br>';
                        $output .= '<label for="fechas">Fecha de Embarque</label>';
                        $output .= '<input type="date" id="fechas" name="fechas" value="' . htmlspecialchars($despacho['fechas']) . '"style="width: 480px; margin: 0 auto; text-align: center;" readonly>';
                        $output .= '</div>';
                    }
                }
                $output .= '<div>';
                $output .= '<br>';
                $output .= '<label for="tyme_credit">Valor del Pedido</label>';
                $output .= '<input type="text" id="tyme_credit" name="tyme_credit" value="' . htmlspecialchars($orderFullPrice) . '" readonly>';
                $output .= '</div>';
                //proceso de facturación
                $number_invoice = $this->getUploadedFiles7($order_number);
                $customer = $this->getCustomers($order_number);
                $output .= '<div>';
                $output .= '<br>';
                $output .= '<label for="number_invoice">Número de Factura</label>';
                $output .= '<input type="text" id="number_invoice" name="number_invoice"  value="' . htmlspecialchars($number_invoice[0]->number_invoice) . '" readonly>';
                $output .= '</div>';
                date_default_timezone_set('America/Bogota');
                $daysToAdd = intval($customerPaymentTime);
                $timestampFechaArribo = strtotime($despacho['fechas']);
                $newTimestamp = strtotime("+$daysToAdd days +1 day", $timestampFechaArribo);
                $newDate = date('Y-m-d', $newTimestamp);
                $output .= '<div>';
                $output .= '<br>';
                $output .= '<label for="nueva_fecha">Fecha de Pago</label>';
                $output .= '<input type="date" id="nueva_fecha" name="nueva_fecha" value="' . htmlspecialchars($newDate) . '"style="width: 480px; margin: 0 auto; text-align: center;" readonly>';
                $output .= '</div>';
                $output .= '<div class="' . $addon_id . ' container">'; // Inicio del div contenedor
                if (!empty($number_invoice[0]->invoice)) {
                    $output .= '<br>';
                    foreach ($number_invoice as $invoice) {
                        $filePathRelative = $this->getUploadedFiles8($order_number, $invoice->number_invoice);
                        $invoicePath = $invoice->invoice;
                        if (!empty($invoicePath)) {
                            $output .= '<div class="' . $addon_id . ' img-container2">' .
                                '<span class="invoice-number">Factura</span>' .
                                '<a href="https://mwt.one' . $invoicePath . '" target="_blank">' .
                                '<img src="https://mwt.one/images/estatus/documento.png" alt="Icono de factura" width="180" height="180">' .
                                '</a>' .
                                '</div>';
                        }
                    }
                }
                $filePathRelatives2 = $this->getUploadedFiles9($order_number);
                if (!empty($filePathRelatives2)) {
                    $output .= '<br>';
                    foreach ($filePathRelatives2 as $filePathRelative) {
                        $certificadoPath = $filePathRelative->certificado;
                        if (!empty($certificadoPath)) {
                            $output .= '<div class="' . $addon_id . ' img-container2">' .
                                '<span class="invoice-number">Certificado</span>' .
                                '<a href="https://mwt.one' . $certificadoPath . '" target="_blank">' .
                                '<img src="https://mwt.one/images/estatus/documento.png" alt="Icono de certificado" width="180" height="180">' .
                                '</a>' .
                                '</div>';
                        }
                    }
                }
                $guias = $this->getUploadedFiles5($order_number);
                if (!empty($guias)) {
                    $output .= '<br>';
                    foreach ($guias as $guia) {
                        $filePathRelative3 = $this->getUploadedFiles6($order_number, $guia->number_guia);
                        if (!empty($filePathRelative3)) {
                            $output .= '<div class="' . $addon_id . ' img-container2">' .
                                '<span class="invoice-number">Guia</span>' .
                                '<a href="https://mwt.one' . $filePathRelative3 . '" target="_blank">' .
                                '<img src="https://mwt.one/images/estatus/documento.png" alt="Icono de Guia" width="180" height="180">' .
                                '</a>' .
                                '</div>';
                        }
                    }
                }
                $output .= '</div>';
                //fin
                $output .= '</section>';
                $sectionClass2 = 'tab-content5';
                $packs2 = $this->getUploadedFiles3($order_number);
                if (!empty($packs2)) {
                    $sectionClass2 = 'tab-content5-1';
                }
                $output .= '<section id="section5" class="' . $sectionClass2 . '">';
                if ($titulo == 'Administrator') {
                    $packs2 = $this->getUploadedFiles3($order_number);
                    if (empty($packs2)) {
                        $output .= '<div>';
                        $output .= '<br>';
                        $output .= '<label for="document_upload">Subir Packing List Caja </label>';
                        $output .= '<input type="file" id="document_upload" name="document_upload" accept=".pdf">';
                        $output .= '<p>Solo se permiten archivos .pdf</p>';
                        $output .= '</div>';
                    }
                    //funcion para agregar mas packing list
                    $output .= '<div id="guias">';
                    $output .= '</div>';
                    $output .= '<br>';
                    $output .= '<button type="button" id="agregar_guia">Agregar Packing List</button>';
                    $output .= '<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>';
                    $output .= '<script>';
                    $output .= '$(document).ready(function() {';
                    $output .= '    $("#agregar_guia").on("click", function() {';
                    $output .= '        var nuevoCampo = \'<div>\';';
                    $output .= '        nuevoCampo += \'<br>\';';
                    $output .= '        nuevoCampo += \'<label for="document_upload">Subir Packing List Caja: </label>\';';
                    $output .= '        nuevoCampo += \'<input type="file" class="document_upload" name="document_upload_nuevo[]" accept=".pdf">\';';
                    $output .= '        nuevoCampo += \'</div>\';';
                    $output .= '        nuevoCampo += \'<button type="button" class="eliminar_packing">X</button>\';';
                    $output .= '        $("#guias").append(nuevoCampo);';
                    $output .= '    });';
                    $output .= '    $("body").on("click", ".eliminar_packing", function() {';
                    $output .= '        $(this).parent().remove();';
                    $output .= '    });';
                    $output .= '});';
                    $output .= '</script>';
                    //fin
                    $packs2 = $this->getUploadedFiles3($order_number);
                    $output .= '<br>';
                    $output .= '<div class="' . $addon_id . ' container">'; // Inicio del div contenedor
                    if (!empty($packs2)) {
                        foreach ($packs2 as $pack) {
                            $filePathRelative4 =  $this->getUploadedFiles2($order_number, $pack->nomb_pack);
                            $output .= '<br>';
                            $output .= '<div class="' . $addon_id . ' img-container2">' .
                                '<span class="invoice-number">' . $pack->nomb_pack . '</span>' .
                                '<a href="https://mwt.one' . $filePathRelative4 . '" target="_blank">' .
                                '<img src="https://mwt.one/images/estatus/documento.png" alt="Icono de Guia">' .
                                '</a>' .
                                '<form method="post">' .
                                '<input type="hidden" name="pack_to_delete" value="' . $pack->nomb_pack . '">' .
                                '<input type="submit" name="delete_pack" class="btn btn-danger" value="X">' .
                                '</form>' .
                                '</div>';
                        }
                    }
                    $output .= '</div>';
                    //Tracking puertos o aeropuertos intermedios
                    if ($operator['operator'] == 'Fabrica') {
                        if ($methodo == 'Aereo') {
                            $output .= '<br>';
                            $output .= '<div>';
                            if (!empty($despacho['puerto_intermedio'])) {
                                $state_parts2 = explode(',', $despacho['puerto_intermedio']);
                                if (!empty($state_parts2)) {
                                    foreach ($state_parts2 as $part) {
                                        $output .= '<div>';
                                        if ($methodo == 'Aereo') {
                                            $output .= '<label for="fechaa">Aeropuerto Intermedio</label>';
                                        } elseif ($methodo == 'Maritimo') {
                                            $output .= '<label for="fechaa">Puerto Intermedio</label>';
                                        }
                                        $output .= '<input type="text" id="puerto_intermedio" name="puerto_intermedio[]" value="' . htmlspecialchars($part) . '" readonly>';
                                        $output .= '</div>';
                                    }
                                }
                            } else {
                                $output .= '<div>';
                                $output .= '<label for="country">Aeropuerto Intermedio</label>';
                                $output .= '<input type="text" class="puerto_intermedio" name="puerto_intermedio[]" list="nomber_despacho_list">';
                                $output .= '<datalist id="nomber_despacho_list"></datalist>';
                                $output .= '</div>';
                                $output .= '<br>';
                            }
                            $output .= '</div>';
                            $output .= '<div id="puertos_intermedios_container">';
                            $output .= '</div>';
                            $output .= '<br>';
                            $output .= '<button type="button" id="agregar_puerto_intermedio">Agregar Aeropuerto Intermedio</button>';
                            $output .= '<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>';
                            $output .= '<script>';
                            $output .= '$(document).ready(function() {';
                            $output .= '    $("body").on("input", ".puerto_intermedio", function() {';
                            $output .= '        var query = $(this).val();';
                            $output .= '        var datalist = $(this).siblings("datalist");';
                            $output .= '        if (query != "") {';
                            $output .= '            $.ajax({';
                            $output .= '                url: window.location.href,';
                            $output .= '                method: "POST",';
                            $output .= '                data: {query: query},';
                            $output .= '                success: function(data) {';
                            $output .= '                    console.log("Response:", data);';
                            $output .= '                    updateDatalist(data, datalist);';
                            $output .= '                },';
                            $output .= '                error: function(jqXHR, textStatus, errorThrown) {';
                            $output .= '                    console.error("AJAX error:", textStatus, errorThrown);';
                            $output .= '                    console.log("Response:", jqXHR.responseText);';
                            $output .= '                }';
                            $output .= '            });';
                            $output .= '        }';
                            $output .= '    });';
                            $output .= '    $("#agregar_puerto_intermedio").on("click", function() {';
                            $output .= '        var nuevoCampo = \'<div><label for="puerto_intermedio_nuevo">Aeropuerto Intermedio:</label>\';';
                            $output .= '        nuevoCampo += \'<input type="text" class="puerto_intermedio" name="puerto_intermedio_nuevo[]" list="nomber_despacho_list_\' + Date.now() + \'">\';';
                            $output .= '        nuevoCampo += \'<datalist id="nomber_despacho_list_\' + Date.now() + \'"></datalist>\';';
                            $output .= '        nuevoCampo += \'<button type="button" class="eliminar_aeropuerto_intermedio">X</button></div>\';';
                            $output .= '        $("#puertos_intermedios_container").append(nuevoCampo);';
                            $output .= '    });';
                            $output .= '    $("body").on("click", ".eliminar_aeropuerto_intermedio", function() {';
                            $output .= '        $(this).parent().remove();';
                            $output .= '    });';
                            $output .= '});';
                            $output .= 'function updateDatalist(data, datalist) {';
                            $output .= '    datalist.empty();';
                            $output .= '    var options = data.split(",");';
                            $output .= '    options.forEach(function(option, index) {';
                            $output .= '        if (index > 0) {';
                            $output .= '            datalist.append("<option value=\\"" + option.trim() + "\\"></option>");';
                            $output .= '        }';
                            $output .= '    });';
                            $output .= '}';
                            $output .= '</script>';
                            function getpuerto($query2)
                            {
                                ob_start();
                                $db = JFactory::getDbo();
                                $query = $db->getQuery(true);
                                $query->select($db->quoteName(array('p.Code', 'p.Airport', 'p.Country')));
                                $query->from($db->quoteName('josmwt_airport', 'p'));
                                $query->where(
                                    $db->quoteName('p.Code') . " LIKE " . $db->quote('%' . $query2 . '%') . " OR " .
                                        $db->quoteName('p.Airport') . " LIKE " . $db->quote('%' . $query2 . '%') . " OR " .
                                        $db->quoteName('p.Country') . " LIKE " . $db->quote('%' . $query2 . '%')
                                );
                                $db->setQuery($query);

                                try {
                                    $results = $db->loadAssocList();
                                } catch (Exception $e) {
                                    error_log('Database error: ' . $e->getMessage());
                                    return '';
                                }

                                $output = [];
                                if (!empty($results)) {
                                    foreach ($results as $result) {
                                        $output[] = $result['Code'] . ' - ' . $result['Airport'] . ' (' . $result['Country'] . ')';
                                    }
                                } else {
                                    $output[] = 'No results found';
                                }
                                $output = implode(',', $output);
                                $output = preg_replace('/<div id=/', '', $output);
                                ob_end_clean();
                                echo $output;
                                exit();
                            }
                            if (isset($_POST['query'])) {
                                $query2 = $_POST['query'];
                                echo getpuerto($query2);
                                exit();
                            }
                        } elseif ($methodo == 'Maritimo') {
                            $output .= '<br>';
                            $output .= '<div>';
                            if (!empty($despacho['puerto_intermedio'])) {
                                $state_parts2 = explode(',', $despacho['puerto_intermedio']);
                                if (!empty($state_parts2)) {
                                    foreach ($state_parts2 as $part) {
                                        $output .= '<div>';
                                        if ($methodo == 'Aereo') {
                                            $output .= '<label for="fechaa">Puerto Intermedio</label>';
                                        } elseif ($methodo == 'Maritimo') {
                                            $output .= '<label for="fechaa">Puerto Intermedio</label>';
                                        }
                                        $output .= '<input type="text" id="puerto_intermedio" name="puerto_intermedio[]" value="' . htmlspecialchars($part) . '" readonly>';
                                        $output .= '</div>';
                                    }
                                }
                            } else {
                                $output .= '<div>';
                                $output .= '<label for="country">Puerto Intermedio</label>';
                                $output .= '<input type="text" class="puerto_intermedio" name="puerto_intermedio[]" list="nomber_despacho_list">';
                                $output .= '<datalist id="nomber_despacho_list"></datalist>';
                                $output .= '</div>';
                                $output .= '<br>';
                            }
                            $output .= '</div>';
                            $output .= '<div id="puertos_intermedios_container">';
                            $output .= '</div>';
                            $output .= '<br>';
                            $output .= '<button type="button" id="agregar_puerto_intermedio">Agregar Puerto Intermedio</button>';
                            $output .= '<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>';
                            $output .= '<script>';
                            $output .= '$(document).ready(function() {';
                            $output .= '    $("body").on("input", ".puerto_intermedio", function() {';
                            $output .= '        var query = $(this).val();';
                            $output .= '        var datalist = $(this).siblings("datalist");';
                            $output .= '        if (query != "") {';
                            $output .= '            $.ajax({';
                            $output .= '                url: window.location.href,';
                            $output .= '                method: "POST",';
                            $output .= '                data: {query: query},';
                            $output .= '                success: function(data) {';
                            $output .= '                    console.log("Response:", data);';
                            $output .= '                    updateDatalist(data, datalist);';
                            $output .= '                },';
                            $output .= '                error: function(jqXHR, textStatus, errorThrown) {';
                            $output .= '                    console.error("AJAX error:", textStatus, errorThrown);';
                            $output .= '                    console.log("Response:", jqXHR.responseText);';
                            $output .= '                }';
                            $output .= '            });';
                            $output .= '        }';
                            $output .= '    });';
                            $output .= '    $("#agregar_puerto_intermedio").on("click", function() {';
                            $output .= '        var nuevoCampo = \'<div><label for="puerto_intermedio_nuevo">Puerto Intermedio:</label>\';';
                            $output .= '        nuevoCampo += \'<input type="text" class="puerto_intermedio" name="puerto_intermedio_nuevo[]" list="nomber_despacho_list_\' + Date.now() + \'">\';';
                            $output .= '        nuevoCampo += \'<datalist id="nomber_despacho_list_\' + Date.now() + \'"></datalist>\';';
                            $output .= '        nuevoCampo += \'<button type="button" class="eliminar_puerto_intermedio">X</button></div>\';';
                            $output .= '        $("#puertos_intermedios_container").append(nuevoCampo);';
                            $output .= '    });';
                            $output .= '    $("body").on("click", ".eliminar_puerto_intermedio", function() {';
                            $output .= '        $(this).parent().remove();';
                            $output .= '    });';
                            $output .= '});';
                            $output .= 'function updateDatalist(data, datalist) {';
                            $output .= '    datalist.empty();';
                            $output .= '    var options = data.split(",");';
                            $output .= '    options.forEach(function(option, index) {';
                            $output .= '        if (index > 0) {';
                            $output .= '            datalist.append("<option value=\\"" + option.trim() + "\\"></option>");';
                            $output .= '        }';
                            $output .= '    });';
                            $output .= '}';
                            $output .= '</script>';
                            function getpuerto($query2)
                            {
                                ob_start();
                                $db = JFactory::getDbo();
                                $query = $db->getQuery(true);
                                $query->select($db->quoteName(array('p.Code', 'p.Name', 'p.Country')));
                                $query->from($db->quoteName('josmwt_puertos', 'p'));
                                $query->where(
                                    $db->quoteName('p.Code') . " LIKE " . $db->quote('%' . $query2 . '%') . " OR " .
                                        $db->quoteName('p.Name') . " LIKE " . $db->quote('%' . $query2 . '%') . " OR " .
                                        $db->quoteName('p.Country') . " LIKE " . $db->quote('%' . $query2 . '%')
                                );
                                $db->setQuery($query);

                                try {
                                    $results = $db->loadAssocList();
                                } catch (Exception $e) {
                                    error_log('Database error: ' . $e->getMessage());
                                    return '';
                                }

                                $output = [];
                                if (!empty($results)) {
                                    foreach ($results as $result) {
                                        $output[] = $result['Code'] . ' - ' . $result['Name'] . ' (' . $result['Country'] . ')';
                                    }
                                } else {
                                    $output[] = 'No results found';
                                }
                                $output = implode(',', $output);
                                $output = preg_replace('/<div id=/', '', $output);
                                ob_end_clean();
                                echo $output;
                                exit();
                            }
                            if (isset($_POST['query'])) {
                                $query2 = $_POST['query'];
                                echo getpuerto($query2);
                                exit();
                            }
                        }
                    }
                    //Fin
                    $despacho = $this->getdespacho($order_number);
                    $output .= '<div>';
                    $output .= '<br>';
                    $output .= '<label for="fechaa">Fecha de Arribo</label>';
                    $output .= '<input type="date" id="fechaa" name="fechaa" value="' . htmlspecialchars($despacho['fecha_arribo']) . '" min="' . date('Y-m-d') . '">';
                    $output .= '</div>';
                    $output .= '<div class="button-group">';
                    $output .= '<br>';
                    $output .= '<input type="submit" name="transito_form" class="btn btn-warning" value="Guardar">';
                    $output .= '</div>';
                    $output .= '<input type="hidden" name="status" value="pagado">';
                    $output .= '<div class="button-group">';
                    $output .= '<br>';
                    $output .= '<input type="submit" name="status_form" class="btn btn-primary" value="Enviar">';
                    $output .= '</div>';
                } else {
                    $output .= '<div>';
                    $state_parts2 = explode(',', $despacho['puerto_intermedio']);
                    if (!empty($state_parts2)) {
                        foreach ($state_parts2 as $part) {
                            if ($methodo == 'Aereo') {
                                $output .= '<br>';
                                $output .= '<label for="fechaa">Aeropuerto Intermedio</label>';
                                $output .= '<input type="text" id="adicional" name="adicional" value="' . htmlspecialchars($part) . '" readonly title="La Modificacion de este campo la realiza el administrador">';
                            } elseif ($methodo == 'Maritimo') {
                                $output .= '<br>';
                                $output .= '<label for="fechaa">Puerto Intermedio</label>';
                                $output .= '<input type="text" id="adicional" name="adicional" value="' . htmlspecialchars($part) . '" readonly title="La Modificacion de este campo la realiza el administrador">';
                            }
                        }
                    }
                    $output .= '</div>';
                    $output .= '<div>';
                    $output .= '<br>';
                    $output .= '<label for="fechaa">Fecha de Arribo</label>';
                    $output .= '<input type="text" id="fechaa" name="fechaa" value="' . htmlspecialchars($despacho['fecha_arribo']) . '"min="' . date('Y-m-d') . '"readonly title="La Modificacion de este campo la realiza el administrador">';
                    $output .= '</div>';
                    $packs2 = $this->getUploadedFiles3($order_number);
                    $output .= '<div class="' . $addon_id . ' container">'; // Inicio del div contenedor
                    if (!empty($packs2)) {
                        foreach ($packs2 as $pack) {
                            $filePathRelative4 =  $this->getUploadedFiles2($order_number, $pack->nomb_pack);
                            $output .= '<br>';
                            $output .= '<div class="' . $addon_id . ' img-container2">' .
                                '<span class="invoice-number">' . $pack->nomb_pack . '</span>' .
                                '<a href="https://mwt.one' . $filePathRelative4 . '" target="_blank">' .
                                '<img src="https://mwt.one/images/estatus/documento.png" alt="Icono de Guia">' .
                                '</a>' .
                                '</div>';
                        }
                    }
                    $output .= '</div>';
                    $output .= '<br>';
                }
                $output .= '</section>';
                $output .= '<script>';
                $output .= 'document.addEventListener("DOMContentLoaded", function() {';
                $output .= '    var tab1 = document.querySelector(".tab.blue-tab#tab1");';
                $output .= '    var tab2 = document.querySelector(".tab.green-tab#tab2");';
                $output .= '    var tab3 = document.querySelector(".tab.purple-tab#tab3");';
                $output .= '    var tab4 = document.querySelector(".tab.pink-tab#tab4");';
                $output .= '    var tab5 = document.querySelector(".tab.orange-tab#tab5");';
                $output .= '    var section1 = document.getElementById("section1");';
                $output .= '    var section2 = document.getElementById("section2");';
                $output .= '    var section3 = document.getElementById("section3");';
                $output .= '    var section4 = document.getElementById("section4");';
                $output .= '    var section5 = document.getElementById("section5");';
                $output .= '    ';
                $output .= '    tab1.addEventListener("click", function() {';
                $output .= '        section1.style.display = "block";';
                $output .= '        section2.style.display = "none";';
                $output .= '        section3.style.display = "none";';
                $output .= '        section4.style.display = "none";';
                $output .= '        section5.style.display = "none";';
                $output .= '    });';
                $output .= '    ';
                $output .= '    tab2.addEventListener("click", function() {';
                $output .= '        section1.style.display = "none";';
                $output .= '        section2.style.display = "block";';
                $output .= '        section3.style.display = "none";';
                $output .= '        section4.style.display = "none";';
                $output .= '        section5.style.display = "none";';
                $output .= '    });';
                $output .= '    ';
                $output .= '    tab3.addEventListener("click", function() {';
                $output .= '        section1.style.display = "none";';
                $output .= '        section2.style.display = "none";';
                $output .= '        section3.style.display = "block";';
                $output .= '        section4.style.display = "none";';
                $output .= '        section5.style.display = "none";';
                $output .= '    });';
                $output .= '    ';
                $output .= '    tab4.addEventListener("click", function() {';
                $output .= '        section1.style.display = "none";';
                $output .= '        section2.style.display = "none";';
                $output .= '        section3.style.display = "none";';
                $output .= '        section4.style.display = "block";';
                $output .= '        section5.style.display = "none";';
                $output .= '    });';
                $output .= '    ';
                $output .= '    tab5.addEventListener("click", function() {';
                $output .= '        section1.style.display = "none";';
                $output .= '        section2.style.display = "none";';
                $output .= '        section3.style.display = "none";';
                $output .= '        section4.style.display = "none";';
                $output .= '        section5.style.display = "block";';
                $output .= '    });';
                $output .= '});';
                $output .= '</script>';
            } else {
                $output .= '<div>';
                $output .= '<p>Lo siento, no tienes permiso para ver este pedido.</p>';
                $output .= '</div>';
            }
        } elseif ($status == 'pagado') {
            if ($this->isSameCustomer($order_number, $userId)) {
                $number_purchase = $this->getNumber($order_number);
                $filePathRelatives = $this->getUploadedFiles($order_number);
                $number = $this->getsap($order_number);
                $fecha = $this->getfechapro($order_number);
                $packs = $this->getUploadedFiles10($order_number);
                $datos = $this->getmethodoshipping($order_number);
                $envios = $this->getUploadedFiles15($order_number);
                $despacho = $this->getdespacho($order_number);
                $guias = $this->getUploadedFiles5($order_number);
                $number_invoice = $this->getUploadedFiles7($order_number);
                $number_invoice2 = $number_invoice[0]->number_invoice;
                $packs2 = $this->getUploadedFiles3($order_number);
                $tipo = $this->getpago($order_number);
                $tipos = $this->getpago2($order_number);
                $filePathRelative6 = $this->getUploadedFiles12($order_number);
                $comprobantes = $this->getUploadedFiles13($order_number);
                $price = $this->getOrder($order_number);
                $orderFullPrice = $price[0]->order_full_price;
                $output .= '<div style="display: flex; justify-content: space-around;">';
                $output .= '<div class="tab blue-tab" id="tab1" style="text-align: center;">';
                $output .= '<span>Creacion/Credito</span><br>';
                $output .= '<div style="display: inline-block; margin-left: 3px;">';
                $output .= '<img src="https://mwt.one/images/estatus/verde.png" alt="Verde" title="Completo" style="width: 20px; height: 20px;">';
                $output .= '</div>';
                $output .= '</div>';
                $output .= '<div class="tab green-tab" id="tab2" style="text-align: center;">';
                $output .= '<span>Elaboración</span><br>';
                $output .= '<div style="display: inline-block; margin-left: 3px;">';
                if (!empty($number) && !empty($fecha)) {
                    // Si ambos tienen datos, mostrar la imagen verde
                    $output .= '<img src="https://mwt.one/images/estatus/verde.png" alt="Verde" title="Completo" style="width: 20px; height: 20px;">';
                } elseif (empty($number) && empty($fecha)) {
                    // Si ambos están vacíos, mostrar la imagen roja
                    $output .= '<img src="https://mwt.one/images/estatus/rojo.png" alt="Rojo" title="Sin Información" style="width: 20px; height: 20px;">';
                } else {
                    // Si al menos uno de ellos tiene datos, mostrar la imagen amarilla
                    $output .= '<img src="https://mwt.one/images/estatus/amarilloo.png" alt="Amarillo" title="Incompleto" style="width: 30px; height: 20px;">';
                }
                $output .= '</div>';
                $output .= '</div>';
                $output .= '<div class="tab purple-tab" id="tab3"  style="text-align: center;">';
                $output .= '<span>Preparación</span><br>';
                $output .= '<div style="display: inline-block; margin-left: 3px;">';
                if (!empty($packs) && !empty($envios)) {
                    // Si ambos tienen datos, mostrar la imagen verde
                    $output .= '<img src="https://mwt.one/images/estatus/verde.png" alt="Verde" title="Completo" style="width: 20px; height: 20px;">';
                } elseif (empty($packs) && empty($envios)) {
                    // Si ambos están vacíos, mostrar la imagen roja
                    $output .= '<img src="https://mwt.one/images/estatus/rojo.png" alt="Rojo" title="Sin Información" style="width: 20px; height: 20px;">';
                } else {
                    // Si al menos uno de ellos tiene datos, mostrar la imagen amarilla
                    $output .= '<img src="https://mwt.one/images/estatus/amarilloo.png" alt="Amarillo" title="Incompleto" style="width: 30px; height: 20px;">';
                }
                $output .= '</div>';
                $output .= '</div>';
                $output .= '<div class="tab pink-tab" id="tab4">';
                $output .= '<span>Despacho</span><br>';
                $output .= '<div style="display: inline-block; margin-left: 3px;">';
                if (!empty($despacho) && !empty($number_invoice2)) {
                    // Si ambos tienen datos, mostrar la imagen verde
                    $output .= '<img src="https://mwt.one/images/estatus/verde.png" alt="Verde" title="Completo" style="width: 20px; height: 20px;">';
                } elseif (empty($despacho) && empty($number_invoice2)) {
                    // Si ambos están vacíos, mostrar la imagen roja
                    $output .= '<img src="https://mwt.one/images/estatus/rojo.png" alt="Rojo" title="Sin Información" style="width: 20px; height: 20px;">';
                } else {
                    // Si al menos uno de ellos tiene datos, mostrar la imagen amarilla
                    $output .= '<img src="https://mwt.one/images/estatus/amarilloo.png" alt="Amarillo" title="Incompleto" style="width: 30px; height: 20px;">';
                }
                $output .= '</div>';
                $output .= '</div>';
                $output .= '<div class="tab orange-tab" id="tab5">';
                $output .= '<span>Transito</span><br>';
                $output .= '<div style="display: inline-block; margin-left: 3px;">';
                if (empty($despacho['fecha_arribo']) && empty($despacho['puerto_intermedio']) && empty($packs2)) {
                    // Si todos están vacíos, mostrar la imagen roja
                    $output .= '<img src="https://mwt.one/images/estatus/rojo.png" alt="Rojo" title="Sin Información" style="width: 20px; height: 20px;">';
                } elseif (!empty($despacho['fecha_arribo']) && !empty($despacho['puerto_intermedio']) && !empty($packs2)) {
                    // Si al menos uno tiene datos, mostrar la imagen amarilla
                    $output .= '<img src="https://mwt.one/images/estatus/verde.png" alt="Verde" title="Completo" style="width: 20px; height: 20px;">';
                } elseif (!empty($despacho['fecha_arribo']) || !empty($despacho['puerto_intermedio']) || !empty($packs2)) {
                    // Si todos tienen datos, mostrar la imagen verde
                    $output .= '<img src="https://mwt.one/images/estatus/amarilloo.png" alt="Amarillo" title="Incompleto" style="width: 30px; height: 20px;">';
                }

                $output .= '</div>';
                $output .= '</div>';
                $output .= '<div class="tab yellow-tab" id="tab6">';
                $output .= '<span>Status/Pago</span><br>';
                $output .= '<div style="display: inline-block; margin-left: 3px;">';
                if ($tipo['tipo_pago'] === 'Completo') {
                    if (!empty($tipo['fecha_pago']) && !empty($filePathRelative6)) {
                        // Si todos tienen datos, mostrar la imagen verde
                        $output .= '<img src="https://mwt.one/images/estatus/verde.png" alt="Verde" title="Completo" style="width: 20px; height: 20px;">';
                    } elseif (empty($tipo['fecha_pago']) && empty($filePathRelative6)) {
                        // Si todos están vacíos, mostrar la imagen roja
                        $output .= '<img src="https://mwt.one/images/estatus/rojo.png" alt="Rojo" title="Sin Información" style="width: 20px; height: 20px;">';
                    } else {
                        // Si al menos uno de ellos tiene datos, mostrar la imagen amarilla
                        $output .= '<img src="https://mwt.one/images/estatus/amarilloo.png" alt="Amarillo" title="Incompleto" style="width: 30px; height: 20px;">';
                    }
                } elseif ($tipo['tipo_pago'] === 'Parcial') {
                    if (!empty($comprobantes) && !empty($tipos)) {
                        // Si ambos tienen datos, verificar si el pago está completo
                        if ($this->checkPaymentStatus($order_number, $orderFullPrice)) {
                            // Si el pago está completo, mostrar la imagen verde
                            $output .= '<img src="https://mwt.one/images/estatus/verde.png" alt="Verde" title="Completo" style="width: 20px; height: 20px;">';
                        } else {
                            // Si el pago no está completo, mostrar la imagen amarilla
                            $output .= '<img src="https://mwt.one/images/estatus/amarilloo.png" alt="Amarillo" title="Incompleto" style="width: 30px; height: 20px;">';
                        }
                    } elseif (empty($comprobantes) && empty($tipos['cantidad_pago'])) {
                        // Si ambos están vacíos, mostrar la imagen roja
                        $output .= '<img src="https://mwt.one/images/estatus/rojo.png" alt="Rojo" title="Sin Información" style="width: 20px; height: 20px;">';
                    } else {
                        // Si al menos uno de ellos tiene datos, mostrar la imagen amarilla
                        $output .= '<img src="https://mwt.one/images/estatus/amarilloo.png" alt="Amarillo" title="Incompleto" style="width: 30px; height: 20px;">';
                    }
                } else {
                    // Si el tipo de pago no es 'Completo' ni 'Parcial', mostrar la imagen roja
                    $output .= '<img src="https://mwt.one/images/estatus/rojo.png" alt="Rojo" title="Sin Información" style="width: 20px; height: 20px;">';
                }
                $output .= '</div>';
                $output .= '</div>';
                $output .= '</div>';
                $output .= '<script>';
                $output .= 'let currentImage = null;';
                $output .= 'const addActiveClass = (tab) => {';
                $output .= '  if (currentImage) {';
                $output .= '    currentImage.classList.remove("active3");';
                $output .= '  }';
                $output .= '  tab.classList.add("active3");';
                $output .= '  currentImage = tab;';
                $output .= '};';
                $output .= 'document.getElementById("tab1").addEventListener("click", function() {';
                $output .= '  addActiveClass(this);';
                $output .= '});';
                $output .= 'document.getElementById("tab2").addEventListener("click", function() {';
                $output .= '  addActiveClass(this);';
                $output .= '});';
                $output .= 'document.getElementById("tab3").addEventListener("click", function() {';
                $output .= '  addActiveClass(this);';
                $output .= '});';
                $output .= 'document.getElementById("tab4").addEventListener("click", function() {';
                $output .= '  addActiveClass(this);';
                $output .= '});';
                $output .= 'document.getElementById("tab5").addEventListener("click", function() {';
                $output .= '  addActiveClass(this);';
                $output .= '});';
                $output .= 'document.getElementById("tab6").addEventListener("click", function() {';
                $output .= '  addActiveClass(this);';
                $output .= '});';
                $output .= 'document.addEventListener("DOMContentLoaded", function() {';
                $output .= '  const tab6 = document.getElementById("tab6");';
                $output .= '  addActiveClass(tab6);';
                $output .= '});';

                $output .= '</script>';
                $output .= '<section id="section1" class="tab-content-1" style="display:none;">';
                $fecha = $this->getfechapro($order_number);
                $output .= '<div>';
                $output .= '<br>';
                $output .= '<label for="number_pedido">Número de Pedido</label>';
                $output .= '<input type="text" id="number_pedido" name="number_pedido" value="' . htmlspecialchars($order_number) . '"readonly';
                $output .= '</div>';
                $output .= '<div>';
                $output .= '<br>';
                $output .= '<label for="number_purchase">Número de Orden de Compra</label>';
                $number_purchase = $this->getNumber($order_number);
                $output .= '<input type="text" id="number_purchase" name="number_purchase" value="' . htmlspecialchars($number_purchase) . '" readonly>';
                $output .= '</div>';
                $filePathRelatives = $this->getUploadedFiles($order_number);
                $output .= '<div class="' . $addon_id . ' container">'; // Inicio del div contenedor
                if (!empty($filePathRelatives)) {
                    $output .= '<br>';
                    $output .= '<div class="' . $addon_id . ' img-container2">' .
                        '<span class="invoice-number">OC</span>' .
                        '<a href="https://mwt.one' . $filePathRelatives . '" target="_blank">' .
                        '<img src="https://mwt.one/images/estatus/documento.png" alt="Icono de Guia">' .
                        '</a>' .
                        '</div>';
                }
                $output .= '</div>';
                $customer = $this->getCustomers($order_number);
                $credit = $this->compareCreditAndPrice($order_number);
                $price = $this->getOrder($order_number);
                $orderFullPrice = $price[0]->order_full_price;
                $customerPaymentTime = $customer[0]['customer_payment_time'];
                $customerCredit = $customer[0]['customer_credit'];
                $customername = $customer[0]['customer_name'];
                $output .= '<div>';
                $output .= '<br>';
                $output .= '<label for="tyme_credit">Cliente</label>';
                $output .= '<input type="text" id="tyme_credit" name="tyme_credit" value="' . htmlspecialchars($customername) . '" readonly>';
                $output .= '</div>';
                $output .= '<div>';
                $output .= '<br>';
                $output .= '<label for="tyme_credit">Días de Crédito Cliente</label>';
                $output .= '<input type="text" id="tyme_credit" name="tyme_credit" value="' . htmlspecialchars($customerPaymentTime) . '" readonly>';
                $output .= '</div>';
                $output .= '<div>';
                $output .= '<br>';
                $output .= '<label for="tyme_credit">Crédito Disponible</label>';
                $output .= '<input type="text" id="tyme_credit" name="tyme_credit" value="' . htmlspecialchars($customerCredit) . '" readonly>';
                $output .= '</div>';
                $output .= '<div>';
                $output .= '<br>';
                $output .= '<label for="tyme_credit">Valor del Pedido</label>';
                $output .= '<input type="text" id="tyme_credit" name="tyme_credit" value="' . htmlspecialchars($orderFullPrice) . '" readonly>';
                $output .= '</div>';
                $output .= '</section>';
                $filePathRelative2 = $this->getUploadedFiles4($order_number);
                if (empty($filePathRelative2)) {
                    $output .= '<section id="section2" class="tab-content2-2" style="display:none;">';
                } else {
                    $output .= '<section id="section2" class="tab-content2-1" style="display:none;">';
                }
                $output .= '<div>';
                $output .= '<br>';
                $number = $this->getsap($order_number);
                $output .= '<label for="number_purchase">Número de SAP</label>';
                $output .= '<input type="text" id="number_sap" name="number_sap"  value="' . htmlspecialchars($number['number_sap']) . '"readonly>';
                if (!empty($number['number_preforma'])) {
                    $output .= '<label for="number_purchase">Número de Proforma</label>';
                    $output .= '<input type="text" id="number_preforma" name="number_preforma" value="' . htmlspecialchars($number['number_preforma']) . '"readonly>';
                }
                $output .= '</div>';
                $output .= '<div class="' . $addon_id . ' container">'; // Inicio del div contenedor
                if (!empty($filePathRelative2)) {
                    $output .= '<br>';
                    $output .= '<div class="' . $addon_id . ' img-container2">' .
                        '<span class="invoice-number">Proforma</span>' .
                        '<a href="https://mwt.one' . $filePathRelative2 . '" target="_blank">' .
                        '<img src="https://mwt.one/images/estatus/documento.png" alt="Icono de Guia">' .
                        '</a>' .
                        '</div>';
                }
                $output .= '</div>';
                $output .= '<div>';
                $output .= '<br>';
                $output .= '<label for="fecha_inicio">Fecha Inicio de Elaboración</label>';
                $output .= '<input type="date" id="fechai" name="fechai" value="' . htmlspecialchars($fecha['fechai']) . '" readonly>';
                $output .= '</div>';
                $output .= '<div>';
                $output .= '<br>';
                $output .= '<label for="fecha_final">Fecha Final de Elaboración</label>';
                $output .= '<input type="date" id="fechaf" name="fechaf" value="' . htmlspecialchars($fecha['fechaf']) . '" readonly>';
                $output .= '</div>';
                $output .= '</section>';
                $output .= '<section id="section3" class="tab-content3-1" style="display:none;">';
                $datos = $this->getmethodoshipping($order_number);
                $output .= '<div>';
                $output .= '<br>';
                $output .= '<label for="number_purchase">Nombre Completo</label>';
                $output .= '<input type="text" id="address_firstname" name="address_firstname" value="' . htmlspecialchars($datos['address_firstname']) . ' ' . htmlspecialchars($datos['address_lastname']) . '" readonly>';
                $output .= '</div>';
                $output .= '<div>';
                $output .= '<br>';
                $output .= '<label for="number_purchase">Telefono</label>';
                $output .= '<input type="text" id="address_telephone" name="address_telephone" value="' . htmlspecialchars($datos['address_telephone']) . '" readonly>';
                $output .= '</div>';
                $output .= '<div>';
                $output .= '<br>';
                $output .= '<label for="number_purchase">Dirección</label>';
                $country_parts = explode('_', $datos['address_country']);
                $country_name = $country_parts[1];
                $state_parts = explode('_', $datos['address_state']);
                $state_name = $state_parts[1];
                $state_name2 = $state_parts[2];
                $state_name3 = $state_parts[3];
                $output .= '<input type="text" id="address_country" name="address_country" value="' . htmlspecialchars($country_name) . ' ' . htmlspecialchars($state_name) . ' ' . htmlspecialchars($state_name2) . ' ' . htmlspecialchars($state_name3) . ' ' . htmlspecialchars($datos['address_city']) . ' ' . htmlspecialchars($datos['address_street']) . ' Postal Cod: ' . htmlspecialchars($datos['address_post_code']) . '" readonly>';
                $output .= '</div>';
                $output .= '<div>';
                $output .= '<br>';
                $output .= '<label for="number_purchase">Método de Envío</label>';
                $output .= '<input type="text" id="order_shipping_method" name="order_shipping_method" value="' . htmlspecialchars($datos['order_shipping_method']) . ' " readonly>';
                $output .= '</div>';
                $output .= '<div>';
                $output .= '<br>';
                $output .= '<label for="number_purchase">Incoterms</label>';
                $output .= '<input type="text" id="incoterms" name="incoterms" value="' . htmlspecialchars($datos['Code_incoterms']) . '' . ' - ' . ' ' . htmlspecialchars($datos['Incoterms']) . '" readonly>';
                $output .= '</div>';
                $packs = $this->getUploadedFiles10($order_number);
                $output .= '<div class="' . $addon_id . ' container">'; // Inicio del div contenedor
                if (!empty($packs)) {
                    foreach ($packs as $pack) {
                        $filePathRelative3 = $this->getUploadedFiles11($order_number, $pack->caja);
                        $output .= '<br>';
                        $output .= '<div class="' . $addon_id . ' img-container2">' .
                            '<span class="invoice-number">' . $pack->caja . '</span>' .
                            '<a href="https://mwt.one' . $filePathRelative3 . '" target="_blank">' .
                            '<img src="https://mwt.one/images/estatus/documento.png" alt="Icono de Guia">' .
                            '</a>' .
                            '</div>';
                    }
                }
                $output .= '</div>';
                $output .= '<div>';
                $output .= '<br>';
                $operator = $this->getoperator($order_number);
                $output .= '<label for="operator">Gestor de carga</label>';
                $output .= '<input type="text" id="address_telephone" name="address_telephone" value="' . htmlspecialchars($operator['operator']) . '" readonly>';
                $output .= '</div>';
                $output .= '<div>';
                $output .= '<br>';
                if ($operator['operator'] == 'Cliente') {
                    $output .= '<label for="operator">Detalles</label>';
                    $output .= '<input type="text" id="details" name="details" value="' . htmlspecialchars($operator['details']) . '" readonly>';
                }
                $output .= '</div>';
                $output .= '<div>';
                $output .= '<br>';
                $output .= '<label for="valor">Valor de Envío</label>';
                $output .= '<input type="text" id="valor" name="valor" value="' . htmlspecialchars($datos['order_shipping_price']) . '"readonly>';
                $output .= '</div>';
                $envios = $this->getUploadedFiles15($order_number);
                $output .= '<br>';
                $output .= '<div class="' . $addon_id . ' container">'; // Inicio del div contenedor
                if (!empty($envios)) {
                    foreach ($envios as $envio) {
                        $filePathRelative4 = $this->getUploadedFiles16($order_number);
                        $output .= '<div class="' . $addon_id . ' img-container2">' .
                            '<span class="invoice-number">Envio</span>' .
                            '<a href="https://mwt.one' . $filePathRelative4 . '" target="_blank">' .
                            '<img src="https://mwt.one/images/estatus/documento.png" alt="Icono de Guia">' .
                            '</a>' .
                            '</div>';
                    }
                }
                $output .= '</div>';
                $output .= '</section>';
                $guias = $this->getUploadedFiles5($order_number);
                $number_invoice = $this->getUploadedFiles7($order_number);
                $filePathRelatives2 = $this->getUploadedFiles9($order_number);

                if (!empty($guias) && !empty($guias->number_guia)) {
                    $filePathRelative3 = $this->getUploadedFiles6($order_number, $guias->number_guia);
                }

                if (!empty($filePathRelatives2) && !empty($filePathRelatives2->certificado)) {
                    $certificadoPath = $filePathRelatives2->certificado;
                }

                if (!empty($guias)) {
                    $sectionClass = 'tab-content4-1-1';
                } else {
                    $sectionClass = 'tab-content4';
                }

                if (!empty($guias) && !empty($number_invoice[0]->invoice) && !empty($number_invoice[0]->certificado)) {
                    $sectionClass = 'tab-content4-2-1';
                } elseif (!empty($number_invoice[0]->invoice) && !empty($guias)) {
                    $sectionClass = 'tab-content4-3-1';
                } elseif (!empty($number_invoice[0]->certificado) && !empty($guias)) {
                    $sectionClass = 'tab-content4-4-1';
                } elseif (!empty($number_invoice[0]->invoice) && !empty($number_invoice[0]->certificado)) {
                    $sectionClass = 'tab-content4-5-1';
                }

                $output .= '<section id="section4" class="' . $sectionClass . '" style="display:none;">';
                $methodo = $this->getShipping($order_number);
                if ($operator['operator'] == 'Fabrica') {
                    if ($methodo == 'Aereo') {
                        $despacho = $this->getdespacho($order_number);
                        $output .= '<div style="display: flex; justify-content: center; align-items: center;">';
                        $output .= '<div>';
                        $output .= '<label for="nomber">Aerolínea</label>';
                        $output .= '<br>';
                        $output .= $despacho['nomber'];
                        $output .= '</div>';
                        $output .= '</div>';
                        $output .= '<div>';
                        $output .= '<br>';
                        $output .= '<label for="number_guia">Número de Air Waybill (AWB)</label>';
                        $output .= '<input type="text" id="number_guia" name="number_guia" value="' . htmlspecialchars($despacho['number_guia']) . '" readonly>';
                        $output .= '</div>';
                        $output .= '<div>';
                        $output .= '<br>';
                        $output .= '<label for="nomber_despacho">Aeropuerto de Despacho</label>';
                        $output .= '<input type="text" id="nomber_despacho" name="nomber_despacho" value="' . htmlspecialchars($despacho['nomber_despacho']) . '" readonly>';
                        $output .= '</div>';
                        $output .= '<div>';
                        $output .= '<br>';
                        $output .= '<label for="nomber_arribo">Aeropuerto de Arribo</label>';
                        $output .= '<input type="text" id="nomber_arribo" name="nomber_arribo" value="' . htmlspecialchars($despacho['nomber_arribo']) . '" readonly>';
                        $output .= '</div>';
                        $output .= '<div>';
                        $output .= '<br>';
                        $output .= '<label for="fechas">Fecha de Embarque</label>';
                        $output .= '<input type="date" id="fechas" name="fechas" value="' . htmlspecialchars($despacho['fechas']) . '"style="width: 480px; margin: 0 auto; text-align: center;" readonly>';
                        $output .= '</div>';
                    } elseif ($methodo == 'Maritimo') {
                        $despacho = $this->getdespacho($order_number);
                        $output .= '<div>';
                        $output .= '<br>';
                        $output .= '<label for="nomber">Nombre Naviera</label>';
                        $output .= '<input type="text" id="nomber" name="nomber" value="' . htmlspecialchars($despacho['nomber']) . '" readonly>';
                        $output .= '</div>';
                        $output .= '<div>';
                        $output .= '<br>';
                        $output .= '<label for="number_guia">Número de Bill of Lading (B/L)</label>';
                        $output .= '<input type="text" id="number_guia" name="number_guia" value="' . htmlspecialchars($despacho['number_guia']) . '"readonly>';
                        $output .= '</div>';
                        $output .= '<div>';
                        $output .= '<br>';
                        $output .= '<label for="nomber_despacho">Puerto de Despacho</label>';
                        $output .= '<input type="text" id="nomber_despacho" name="nomber_despacho" value="' . htmlspecialchars($despacho['nomber_despacho']) . '"readonly>';
                        $output .= '</div>';
                        $output .= '<div>';
                        $output .= '<br>';
                        $output .= '<label for="nomber_arribo">Puerto de Arribo</label>';
                        $output .= '<input type="text" id="nomber_arribo" name="nomber_arribo" value="' . htmlspecialchars($despacho['nomber_arribo']) . '"readonly>';
                        $output .= '</div>';
                        $output .= '<div>';
                        $output .= '<br>';
                        $output .= '<label for="additional_info">Información adicional</label>';
                        $output .= '<input type="text" id="adicional" name="adicional" value="' . htmlspecialchars($despacho['adiccional']) . '" readonly>';
                        $output .= '</div>';
                        $output .= '<div>';
                        $output .= '<br>';
                        $output .= '<label for="fechas">Fecha de Embarque</label>';
                        $output .= '<input type="date" id="fechas" name="fechas" value="' . htmlspecialchars($despacho['fechas']) . '" style="width: 480px; margin: 0 auto; text-align: center;" readonly>';
                        $output .= '</div>';
                    }
                }
                $output .= '<div>';
                $output .= '<br>';
                $output .= '<label for="tyme_credit">Valor del Pedido</label>';
                $output .= '<input type="text" id="tyme_credit" name="tyme_credit" value="' . htmlspecialchars($orderFullPrice) . '" readonly>';
                $output .= '</div>';
                //proceso de facturación
                $number_invoice = $this->getUploadedFiles7($order_number);
                $customer = $this->getCustomers($order_number);
                $output .= '<div>';
                $output .= '<br>';
                $output .= '<label for="number_invoice">Número de Factura</label>';
                $output .= '<input type="text" id="number_invoice" name="number_invoice"  value="' . htmlspecialchars($number_invoice[0]->number_invoice) . '" readonly>';
                $output .= '</div>';
                date_default_timezone_set('America/Bogota');
                $daysToAdd = intval($customerPaymentTime);
                $timestampFechaArribo = strtotime($despacho['fechas']);
                $newTimestamp = strtotime("+$daysToAdd days +1 day", $timestampFechaArribo);
                $newDate = date('Y-m-d', $newTimestamp);
                $output .= '<div>';
                $output .= '<br>';
                $output .= '<label for="nueva_fecha">Fecha de Pago</label>';
                $output .= '<input type="date" id="nueva_fecha" name="nueva_fecha" value="' . htmlspecialchars($newDate) . '" readonly>';
                $output .= '</div>';
                $output .= '<div class="' . $addon_id . ' container">'; // Inicio del div contenedor
                if (!empty($number_invoice[0]->invoice)) {
                    $output .= '<br>';
                    foreach ($number_invoice as $invoice) {
                        $filePathRelative = $this->getUploadedFiles8($order_number, $invoice->number_invoice);
                        $invoicePath = $invoice->invoice;
                        if (!empty($invoicePath)) {
                            $output .= '<div class="' . $addon_id . ' img-container2">' .
                                '<span class="invoice-number">Factura</span>' .
                                '<a href="https://mwt.one' . $invoicePath . '" target="_blank">' .
                                '<img src="https://mwt.one/images/estatus/documento.png" alt="Icono de factura" width="180" height="180">' .
                                '</a>' .
                                '</div>';
                        }
                    }
                }
                $filePathRelatives2 = $this->getUploadedFiles9($order_number);
                if (!empty($filePathRelatives2)) {
                    $output .= '<br>';
                    foreach ($filePathRelatives2 as $filePathRelative) {
                        $certificadoPath = $filePathRelative->certificado;
                        if (!empty($certificadoPath)) {
                            $output .= '<div class="' . $addon_id . ' img-container2">' .
                                '<span class="invoice-number">Certificado</span>' .
                                '<a href="https://mwt.one' . $certificadoPath . '" target="_blank">' .
                                '<img src="https://mwt.one/images/estatus/documento.png" alt="Icono de certificado" width="180" height="180">' .
                                '</a>' .
                                '</div>';
                        }
                    }
                }
                $guias = $this->getUploadedFiles5($order_number);
                if (!empty($guias)) {
                    $output .= '<br>';
                    foreach ($guias as $guia) {
                        $filePathRelative3 = $this->getUploadedFiles6($order_number, $guia->number_guia);
                        if (!empty($filePathRelative3)) {
                            $output .= '<div class="' . $addon_id . ' img-container2">' .
                                '<span class="invoice-number">Guia</span>' .
                                '<a href="https://mwt.one' . $filePathRelative3 . '" target="_blank">' .
                                '<img src="https://mwt.one/images/estatus/documento.png" alt="Icono de Guia" width="180" height="180">' .
                                '</a>' .
                                '</div>';
                        }
                    }
                }
                $output .= '</div>';
                //fin
                $output .= '</section>';

                //fin
                $sectionClass2 = 'tab-content5';
                $packs2 = $this->getUploadedFiles3($order_number);
                if (!empty($packs2)) {
                    $sectionClass2 = 'tab-content5-1';
                }
                $output .= '<section id="section5" class="' . $sectionClass2 . '" style="display:none;">';
                $output .= '<div>';
                $state_parts2 = explode(',', $despacho['puerto_intermedio']);
                if (!empty($state_parts2)) {
                    foreach ($state_parts2 as $part) {
                        if ($methodo == 'Aereo') {
                            $output .= '<br>';
                            $output .= '<label for="fechaa">Aeropuerto Intermedio</label>';
                            $output .= '<input type="text" id="adicional" name="adicional" value="' . htmlspecialchars($part) . '" readonly>';
                        } elseif ($methodo == 'Maritimo') {
                            $output .= '<br>';
                            $output .= '<label for="fechaa">Puerto Intermedio</label>';
                            $output .= '<input type="text" id="adicional" name="adicional" value="' . htmlspecialchars($part) . '" readonly >';
                        }
                    }
                }
                $output .= '</div>';
                $output .= '<div>';
                $output .= '<br>';
                $output .= '<label for="fechaa">Fecha de Arribo</label>';
                $output .= '<input type="text" id="fechaa" name="fechaa" value="' . htmlspecialchars($despacho['fecha_arribo']) . '"readonly>';
                $output .= '</div>';
                $packs2 = $this->getUploadedFiles3($order_number);
                $output .= '<div class="' . $addon_id . ' container">'; // Inicio del div contenedor
                if (!empty($packs2)) {
                    foreach ($packs2 as $pack) {
                        $filePathRelative4 =  $this->getUploadedFiles2($order_number, $pack->nomb_pack);
                        $output .= '<br>';
                        $output .= '<div class="' . $addon_id . ' img-container2">' .
                            '<span class="invoice-number">' . $pack->nomb_pack . '</span>' .
                            '<a href="https://mwt.one' . $filePathRelative4 . '" target="_blank">' .
                            '<img src="https://mwt.one/images/estatus/documento.png" alt="Icono de Guia">' .
                            '</a>' .
                            '</div>';
                    }
                }
                $output .= '</div>';
                $output .= '</section>';
                $comprobantes = $this->getUploadedFiles13($order_number);
                if ($comprobantes === null) {
                    $num_comprobantes = 0;
                    $output .= '<section id="section6" class="tab-content6" >';
                } else {
                    $num_comprobantes = count($comprobantes);
                    if ($num_comprobantes > 1) {
                        $output .= '<section id="section6" class="tab-content6-1-1" >';
                    } else {
                        $output .= '<section id="section6" class="tab-content6-1" >';
                    }
                }
                $number_invoice = $this->getUploadedFiles7($order_number);
                $tipo = $this->getpago($order_number);
                $tipos = $this->getpago2($order_number);
                $customer = $this->getCustomers($order_number);
                $output .= '<div>';
                $output .= '<br>';
                $output .= '<label for="tipo">Tipo de Pago</label>';
                $output .= '<select id="tipo" name="tipo" onchange="handleTipoPagoChange(this)">';
                if ($tipo['tipo_pago'] == 'Parcial') {
                    $output .= '<option value="Completo">Completo</option>';
                    $output .= '<option value="Parcial" selected>Parcial</option>';
                } else {
                    $output .= '<option value="Completo" selected>Completo</option>';
                    $output .= '<option value="Parcial">Parcial</option>';
                }
                $output .= '</select>';
                $output .= '</div>';
                $output .= '<script>';
                $output .= 'document.addEventListener("DOMContentLoaded", function() {';
                $output .= '    var tipoPagoSelect = document.getElementById("tipo");';
                $output .= '    handleTipoPagoChange(tipoPagoSelect);';
                $output .= '});';
                $output .= 'function handleTipoPagoChange(select) {';
                $output .= '    var selectedValue = select.value;';
                $output .= '    var additionalFieldsDiv = document.getElementById("additionalFieldsDiv");';
                $output .= '    var additionalDisplayDiv = document.getElementById("additionalDisplayDiv");';
                $output .= '    if (selectedValue === "Completo") {';
                $output .= '        additionalFieldsDiv.style.display = "block";';
                $output .= '        additionalDisplayDiv.style.display = "none";';
                $output .= '    } else if (selectedValue === "Parcial") {';
                $output .= '        additionalFieldsDiv.style.display = "none";';
                $output .= '        additionalDisplayDiv.style.display = "block";';
                $output .= '    } else {';
                $output .= '        additionalFieldsDiv.style.display = "none";';
                $output .= '        additionalDisplayDiv.style.display = "none";';
                $output .= '    }';
                $output .= '}';
                $output .= '</script>';
                //pago completo
                $cantidad_pago = intval($cantidad_pago);
                $output .= '<div id="additionalFieldsDiv" style="display: ' . ($tipo['tipo_pago'] == 'Completo' ? 'block' : 'none') . ';">';
                $output .= '<div>';
                $output .= '<br>';
                $output .= '<label for="tyme_credit">Valor Pagado</label>';
                $output .= '<input type="number" id="tyme_credit" name="tyme_credit" value="' . htmlspecialchars($orderFullPrice) . '">';
                $output .= '</div>';
                $output .= '<div>';
                $output .= '<br>';
                $output .= '<label for="metodo">Método de Pago</label>';
                $output .= '<select id="metodo" name="metodo">';
                if ($tipo['metodo_pago'] == 'Consignacion') {
                    $output .= '<option value="Consignacion" selected>Transferencia Bancaria</option>';
                    $output .= '<option value="Nota">Nota Credito</option>';
                } else {
                    $output .= '<option value="Consignacion"selected>Transferencia Bancaria</option>';
                    $output .= '<option value="Nota" >Nota Credito</option>';
                }
                $output .= '</select>';
                $output .= '</div>';
                $filePathRelative6 = $this->getUploadedFiles12($order_number);
                if (empty($filePathRelative6)) {
                    $output .= '<div>';
                    $output .= '<br>';
                    $output .= '<label for="document_upload">Subir Comprobantes </label>';
                    $output .= '<input type="file" id="document_upload" name="document_upload" accept=".pdf">';
                    $output .= '<p>Solo se permiten archivos .pdf</p>';
                    $output .= '</div>';
                }
                $output .= '<div class="' . $addon_id . ' container">'; // Inicio del div contenedor
                if (!empty($filePathRelative6)) {
                    $output .= '<br>';
                    $output .= '<div class="' . $addon_id . ' img-container2">' .
                        '<span class="invoice-number">Comprobante</span>' .
                        '<a href="https://mwt.one' . $filePathRelative6 . '" target="_blank">' .
                        '<img src="https://mwt.one/images/estatus/documento.png" alt="Icono de Guia">' .
                        '</a>' .
                        '<div class="button-group">' .
                        '<button type="submit" name="delete_comprobante_completo" class="btn btn-danger">' .
                        '<i class="fas fa-times"></i>' .
                        '</button>' .
                        '</div>' .
                        '</div>';
                }
                $output .= '</div>';
                $output .= '<div>';
                $output .= '<br>';
                $output .= '<label for="fechap">Fecha de Pago</label>';
                $output .= '<input type="date" id="fechap" name="fechap" value="' . htmlspecialchars($tipo['fecha_pago']) . '" max="' . date('Y-m-d') . '">';
                $output .= '</div>';
                $output .= '<div>';
                $output .= '<br>';
                $output .= '<label for="additional_info">Información adicional</label>';
                $output .= '<textarea class="form-control" id="additional_info" name="additional_info" oninput="validateAdditionalInfo2(this)">';
                $output .= htmlspecialchars($tipo['adiccional']);
                $output .= '</textarea>';
                $output .= '<span id="additional_info_error" style="color: red;"></span>';
                $output .= '<span id="additional_info_counter"></span>';
                $output .= '</div>';
                $output .= '<script>';
                $output .= 'function validateAdditionalInfo2(input) {';
                $output .= '    var maxLength = 500;';
                $output .= '    var errorMessage = "La información adicional no debe exceder los " + maxLength + " caracteres.";';
                $output .= '    var errorElement = document.getElementById("additional_info_error");';
                $output .= '    var counterElement = document.getElementById("additional_info_counter");';
                $output .= '    if(errorElement && counterElement) {';
                $output .= '        if (input.value.length > maxLength) {';
                $output .= '            input.setCustomValidity(errorMessage);';
                $output .= '            errorElement.textContent = errorMessage;';
                $output .= '        } else {';
                $output .= '            input.setCustomValidity("");';
                $output .= '            errorElement.textContent = "";';
                $output .= '        }';
                $output .= '        counterElement.textContent = "Caracteres restantes: " + (maxLength - input.value.length);';
                $output .= '    } else {';
                $output .= '        console.error("Element not found");';
                $output .= '    }';
                $output .= '}';
                $output .= '</script>';
                if ($tipo['status'] != "Credito Liberado") {
                    $output .= '<div class="button-group">';
                    $output .= '<br>';
                    $output .= '<input type="submit" id="completo_form" name="completo_form" class="btn btn-primary" value="Enviar">';
                    $output .= '</div>';
                }
                $output .= '</div>';
                //fin pago completo
                //pago parcial
                $output .= '<div id="additionalDisplayDiv" style="display: ' . (($tip['tipo_pago'] == 'Parcial') ? 'block' : 'none') . ';">';
                if (!empty($tipos)) {
                    foreach ($tipos as $tip) {
                        $cantidad_pago = intval($tip['cantidad_pago']);
                        $output .= '<input type="hidden" id="nombre" name="nombre" value="' . htmlspecialchars($tip['nombre']) . '">';
                        if (empty($tip['cantidad_pago'])) {
                            $output .= '<div>';
                            $output .= '<br>';
                            $output .= '<label for="tyme_credit2">Valor Pagado</label>';
                            $output .= '<input type="number" id="tyme_credit2" name="tyme_credit2" value="' . htmlspecialchars($tip['cantidad_pago']) . '">';
                            $output .= '</div>';
                        } else {
                            $output .= '<div>';
                            $output .= '<br>';
                            $output .= '<label for="tyme_credit2">Valor Pagado</label>';
                            $output .= '<input type="number" id="tyme_credit2" name="tyme_credit2" value="' . htmlspecialchars($tip['cantidad_pago']) . '"readonly>';
                            $output .= '</div>';
                        }
                        $output .= '<div>';
                        $output .= '<br>';
                        $output .= '<label for="metodo2">Método de Pago</label>';
                        $output .= '<select id="metodo2" name="metodo2">';
                        if ($tip['metodo_pago'] == 'Consignacion') {
                            $output .= '<option value="Consignacion" selected>Transferencia Bancaria</option>';
                            $output .= '<option value="Nota">Nota Credito</option>';
                        } else {
                            $output .= '<option value="Consignacion">Transferencia Bancaria</option>';
                            $output .= '<option value="Nota" selected>Nota Credito</option>';
                        }
                        $output .= '</select>';
                        $output .= '</div>';
                        $comprobantes = $this->getUploadedFiles13($order_number);
                        if (empty($comprobantes)) {
                            $output .= '<div>';
                            $output .= '<br>';
                            $output .= '<label for="document_upload2">Subir Comprobante </label>';
                            $output .= '<input type="file" id="document_upload2" name="document_upload2" accept=".pdf">';
                            $output .= '<p>Solo se permiten archivos .pdf</p>';
                            $output .= '</div>';
                        }

                        $output .= '<div>';
                        $output .= '<br>';
                        $output .= '<label for="fechap2">Fecha de Pago</label>';
                        $output .= '<input type="date" id="fechap2" name="fechap2" value="' . htmlspecialchars($tipo['fecha_pago']) . '" max="' . date('Y-m-d') . '">';
                        $output .= '</div>';
                        $output .= '<div>';
                        $output .= '<br>';
                        $output .= '<label for="additional_info3">Información adicional</label>';
                        $output .= '<textarea class="form-control additional_info3" name="additional_info3" oninput="validateAdditionalInfo3(this)">';
                        $output .= htmlspecialchars($tip['adiccional']);
                        $output .= '</textarea>';
                        $output .= '<span class="additional_info3_error" style="color: red;"></span>';
                        $output .= '<span class="additional_info3_counter"></span>';
                        $output .= '</div>';
                        $output .= '<script>';
                        $output .= 'document.addEventListener("DOMContentLoaded", function() {';
                        $output .= '    function validateAdditionalInfo3(input) {';
                        $output .= '        var maxLength = 500;';
                        $output .= '        var errorMessage = "La información adicional no debe exceder los " + maxLength + " caracteres.";';
                        $output .= '        var errorElement = document.getElementById("additional_info3_error");';
                        $output .= '        var counterElement = document.getElementById("additional_info3_counter");';
                        $output .= '        if(errorElement && counterElement) {';
                        $output .= '            if (input.value.length > maxLength) {';
                        $output .= '                input.setCustomValidity(errorMessage);';
                        $output .= '                errorElement.textContent = errorMessage;';
                        $output .= '            } else {';
                        $output .= '                input.setCustomValidity("");';
                        $output .= '                errorElement.textContent = "";';
                        $output .= '            }';
                        $output .= '            counterElement.textContent = "Caracteres restantes: " + (maxLength - input.value.length);';
                        $output .= '        } else {';
                        $output .= '            console.error("Element not found");';
                        $output .= '        }';
                        $output .= '    }';
                        $output .= '});';
                        $output .= '</script>';
                    }
                } else {
                    $output .= '<div>';
                    $output .= '<br>';
                    $output .= '<label for="tyme_credit2">Valor Pagado</label>';
                    $output .= '<input type="number" id="tyme_credit2" name="tyme_credit2" value="' . htmlspecialchars($tipo['cantidad_pago']) . '">';
                    $output .= '</div>';
                    $output .= '<div>';
                    $output .= '<br>';
                    $output .= '<label for="metodo2">Método de Pago</label>';
                    $output .= '<select id="metodo2" name="metodo2">';
                    if ($tipo['metodo_pago'] == 'Consignacion') {
                        $output .= '<option value="Consignacion" selected>Transferencia Bancaria</option>';
                        $output .= '<option value="Nota">Nota Credito</option>';
                    } else {
                        $output .= '<option value="Consignacion">Transferencia Bancaria</option>';
                        $output .= '<option value="Nota" selected>Nota Credito</option>';
                    }
                    $output .= '</select>';
                    $output .= '</div>';
                    $output .= '<div>';
                    $output .= '<br>';
                    $output .= '<label for="document_upload2">Subir Comprobante </label>';
                    $output .= '<input type="file" id="document_upload2" name="document_upload2" accept=".pdf">';
                    $output .= '<p>Solo se permiten archivos .pdf</p>';
                    $output .= '</div>';
                    $output .= '<div>';
                    $output .= '<br>';
                    $output .= '<label for="fechap2">Fecha de Pago</label>';
                    $output .= '<input type="date" id="fechap2" name="fechap2" value="' . htmlspecialchars($tipo['fecha_pago']) . '" max="' . date('Y-m-d') . '">';
                    $output .= '</div>';
                    $output .= '<div>';
                    $output .= '<br>';
                    $output .= '<label for="additional_info3">Información adicional</label>';
                    $output .= '<textarea class="form-control additional_info3" name="additional_info3" oninput="validateAdditionalInfo3(this)">';
                    $output .= htmlspecialchars($tip['adiccional']);
                    $output .= '</textarea>';
                    $output .= '<span id="additional_info3_error" style="color: red;"></span>';
                    $output .= '<span id="additional_info3_counter"></span>';
                    $output .= '</div>';
                    $output .= '<script>';
                    $output .= 'function validateAdditionalInfo3(input) {';
                    $output .= '    var maxLength = 500;';
                    $output .= '    var errorMessage = "La información adicional no debe exceder los " + maxLength + " caracteres.";';
                    $output .= '    var errorElement = document.getElementById("additional_info3_error");';
                    $output .= '    var counterElement = document.getElementById("additional_info3_counter");';
                    $output .= '    if(errorElement && counterElement) {';
                    $output .= '        if (input.value.length > maxLength) {';
                    $output .= '            input.setCustomValidity(errorMessage);';
                    $output .= '            errorElement.textContent = errorMessage;';
                    $output .= '        } else {';
                    $output .= '            input.setCustomValidity("");';
                    $output .= '            errorElement.textContent = "";';
                    $output .= '        }';
                    $output .= '        counterElement.textContent = "Caracteres restantes: " + (maxLength - input.value.length);';
                    $output .= '    } else {';
                    $output .= '        console.error("Element not found");';
                    $output .= '    }';
                    $output .= '}';
                    $output .= '</script>';
                }
                //funcion agregar pago parcial
                $output .= '<div id="pagos_parciales">';
                $output .= '</div>';
                $output .= '<br>';
                $output .= '<button type="button" id="agregar_pago_parcial">Agregar Pago parcial</button>';
                $output .= '<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>';
                $output .= '<script>';
                $output .= '$(document).ready(function() {';
                $output .= '    var opcionesMetodoPago = "<option value=\'Consignacion\'>Transferencia Bancaria</option><option value=\'Nota\'>Nota Credito</option>";';
                $output .= '    $("#agregar_pago_parcial").on("click", function() {';
                $output .= '        var nuevoCampo = \'<div>\';';
                $output .= '        nuevoCampo += \'<br>\';';
                $output .= '        nuevoCampo += \'<label for="tyme_credit">Valor Pagado</label>\';';
                $output .= '        nuevoCampo += \'<input type="number" class="tyme_credit" name="tyme_credit_nuevo[]">\';';
                $output .= '        nuevoCampo += \'<br>\';';
                $output .= '        nuevoCampo += \'<label for="metodo">Método de Pago</label>\';';
                $output .= '        nuevoCampo += \'<select class="metodo" name="metodo_nuevo[]">\';';
                $output .= '        nuevoCampo += opcionesMetodoPago;';
                $output .= '        nuevoCampo += \'</select>\';';
                $output .= '        nuevoCampo += \'<br>\';';
                $output .= '        nuevoCampo += \'<label for="document_upload">Subir Comprobante de Pago </label>\';';
                $output .= '        nuevoCampo += \'<input type="file" class="document_upload" name="document_upload_nuevo[]" accept=".pdf">\';';
                $output .= '        nuevoCampo += \'<br>\';';
                $output .= '        nuevoCampo += \'<label for="fechas">Fecha de Pago</label>\';';
                $output .= '        nuevoCampo += \'<input type="date" class="fechas" name="fechas_nuevo[]" max="\' + new Date().toISOString().split("T")[0] + \'">\';';
                $output .= '        nuevoCampo += \'<br>\';';
                $output .= '        nuevoCampo += \'<label for="additional_info">Información adicional</label>\';';
                $output .= '        nuevoCampo += \'<textarea class="form-control additional_info" name="additional_info_nuevo[]"></textarea>\';';
                $output .= '        nuevoCampo += \'<span class="additional_info3_error" style="color: red;"></span>\';';
                $output .= '        nuevoCampo += \'<span class="additional_info3_counter"></span>\';';
                $output .= '        nuevoCampo += \'</div>\';';
                $output .= '        nuevoCampo += \'<button type="button" class="eliminar_pago_parcial">X</button>\';';
                $output .= '        $("#pagos_parciales").append(nuevoCampo);';
                $output .= '    });';
                $output .= '    $("body").on("click", ".eliminar_pago_parcial", function() {';
                $output .= '        $(this).parent().remove();';
                $output .= '    });';
                $output .= '});';
                $output .= '</script>';
                //fin pago parcial
                $comprobantes = $this->getUploadedFiles13($order_number);
                $output .= '<br>';
                $output .= '<div class="' . $addon_id . ' container">'; // Inicio del div contenedor
                if (!empty($comprobantes)) {
                    foreach ($comprobantes as $comprobante) {
                        $filePathRelative5 = $this->getUploadedFiles14($order_number, $comprobante->nombre);
                        $output .= '<br>';
                        $output .= '<div class="' . $addon_id . ' img-container2">' .
                            '<span class="invoice-number">' . $comprobante->nombre . '</span>' .
                            '<a href="https://mwt.one' . $filePathRelative5 . '" target="_blank">' .
                            '<img src="https://mwt.one/images/estatus/documento.png" alt="Icono de Guia" >' .
                            '</a>' .
                            '<button type="submit" name="delete_comprobante_parcial" class="btn btn-danger" value="' . $comprobante->nombre . '">X</button>' .
                            '</div>';
                    }
                }
                $output .= '</div>';

                $output .= '<div class="button-group">';
                $output .= '<br>';
                if (!$this->checkPaymentStatus($order_number, $orderFullPrice)) {
                    $output .= '<input type="submit" id="parcial_form" name="parcial_form" class="btn btn-primary" value="Enviar">';
                }
                $output .= '</div>';
                $output .= '<script>';
                $output .= 'function submitForm() {';
                $output .= '    var selectedValue = document.getElementById("tipo").value;';
                $output .= '    if (selectedValue === "Completo") {';
                $output .= '        document.getElementById("completo_form").submit();';
                $output .= '    } else if (selectedValue === "Parcial") {';
                $output .= '        document.getElementById("parcial_form").submit();';
                $output .= '    }';
                $output .= '}';
                $output .= '</script>';
                $output .= '</div>';
                $output .= '</section>';
                $output .= '<script>';
                $output .= 'document.addEventListener("DOMContentLoaded", function() {';
                $output .= '    var tab1 = document.querySelector(".tab.blue-tab#tab1");';
                $output .= '    var tab2 = document.querySelector(".tab.green-tab#tab2");';
                $output .= '    var tab3 = document.querySelector(".tab.purple-tab#tab3");';
                $output .= '    var tab4 = document.querySelector(".tab.pink-tab#tab4");';
                $output .= '    var tab5 = document.querySelector(".tab.orange-tab#tab5");';
                $output .= '    var tab6 = document.querySelector(".tab.yellow-tab#tab6");';
                $output .= '    var section1 = document.getElementById("section1");';
                $output .= '    var section2 = document.getElementById("section2");';
                $output .= '    var section3 = document.getElementById("section3");';
                $output .= '    var section4 = document.getElementById("section4");';
                $output .= '    var section5 = document.getElementById("section5");';
                $output .= '    var section6 = document.getElementById("section6");';
                $output .= '    ';
                $output .= '    tab1.addEventListener("click", function() {';
                $output .= '        section1.style.display = "block";';
                $output .= '        section2.style.display = "none";';
                $output .= '        section3.style.display = "none";';
                $output .= '        section4.style.display = "none";';
                $output .= '        section5.style.display = "none";';
                $output .= '        section6.style.display = "none";';
                $output .= '    });';
                $output .= '    ';
                $output .= '    tab2.addEventListener("click", function() {';
                $output .= '        section1.style.display = "none";';
                $output .= '        section2.style.display = "block";';
                $output .= '        section3.style.display = "none";';
                $output .= '        section4.style.display = "none";';
                $output .= '        section5.style.display = "none";';
                $output .= '        section6.style.display = "none";';
                $output .= '    });';
                $output .= '    ';
                $output .= '    tab3.addEventListener("click", function() {';
                $output .= '        section1.style.display = "none";';
                $output .= '        section2.style.display = "none";';
                $output .= '        section3.style.display = "block";';
                $output .= '        section4.style.display = "none";';
                $output .= '        section5.style.display = "none";';
                $output .= '        section6.style.display = "none";';
                $output .= '    });';
                $output .= '    ';
                $output .= '    tab4.addEventListener("click", function() {';
                $output .= '        section1.style.display = "none";';
                $output .= '        section2.style.display = "none";';
                $output .= '        section3.style.display = "none";';
                $output .= '        section4.style.display = "block";';
                $output .= '        section5.style.display = "none";';
                $output .= '        section6.style.display = "none";';
                $output .= '    });';
                $output .= '    ';
                $output .= '    tab5.addEventListener("click", function() {';
                $output .= '        section1.style.display = "none";';
                $output .= '        section2.style.display = "none";';
                $output .= '        section3.style.display = "none";';
                $output .= '        section4.style.display = "none";';
                $output .= '        section5.style.display = "block";';
                $output .= '        section6.style.display = "none";';
                $output .= '    });';
                $output .= '    ';
                $output .= '    tab6.addEventListener("click", function() {';
                $output .= '        section1.style.display = "none";';
                $output .= '        section2.style.display = "none";';
                $output .= '        section3.style.display = "none";';
                $output .= '        section4.style.display = "none";';
                $output .= '        section5.style.display = "none";';
                $output .= '        section6.style.display = "block";';
                $output .= '    });';
                $output .= '});';
                $output .= '</script>';
            } else {
                $output .= '<div>';
                $output .= '<p>Lo siento, no tienes permiso para ver este pedido.</p>';
                $output .= '</div>';
            }
        } elseif ($status == 'cancel') {
        }

        $output .= '</div>';
        $output .= '<div>';
        $output .= '<br>';
        $output .= '<a href="index.php/es/history" class="btn btn-success">Volver</a>';
        $output .= '</div>';
        $output .= '</form>';
        $output .= '</div>';


        //Procesador de formulario
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (isset($_POST['upload_form'])) {
                $order_number = $_POST['order_number'];
                $status = 'credito';
                $customer_id = isset($_POST['customer_select']) ? $_POST['customer_select'] : null;
                if (empty($_POST['number_purchase'])) {
                    echo '<script type="text/javascript">
                                alert("El campo Número de Orden de Compra es obligatorio");
                                window.location.href = window.location.href;
                              </script>';
                    exit();
                }
                $number_purchase = $_POST['number_purchase'];
                if (isset($_FILES['document_upload']) && $_FILES['document_upload']['error'] == UPLOAD_ERR_OK) {
                    $documentUpload = $_FILES['document_upload']['tmp_name'];
                    try {
                        $this->uploadAndSaveDocument($documentUpload, $userId, $order_number, $number_purchase, $status, $customer_id);
                        echo '<script type="text/javascript">
                                    alert("Archivo subido con éxito");
                                    window.location.href = window.location.href;
                                </script>';
                        exit();
                    } catch (Exception $e) {
                        echo '<script type="text/javascript">
                                    alert("' . $e->getMessage() . '");
                                    window.location.href = window.location.href;
                                </script>';
                        exit();
                    }
                } else {
                    $documentUpload = $this->getUploadedFiles($order_number);
                    $this->saveDataToPreforma($documentUpload, $userId, $order_number, $number_purchase, $status, $customer_id);
                }
            } elseif (isset($_POST['delete_files'])) {
                $order_number = $_POST['order_number'];
                $this->deleteUploadedFiles($order_number);
                echo '<script type="text/javascript">window.location.href = window.location.href;</script>';
                exit();
            }
            if (isset($_POST['credit_form'])) {
                $order_number = $_POST['order_number'];
                $status = 'produccion';
                $this->updateCustomerCredit($order_number, $status);
                echo '<script type="text/javascript">
                                    alert("Verificación Completa");
                                    window.location.href = window.location.href;
                                </script>';
            } elseif (isset($_POST['delete_preforma'])) {
                $order_number = $_POST['order_number'];
                $this->deleteUploadedFiles3($order_number);
                echo '<script type="text/javascript">window.location.href = window.location.href;</script>';
                exit();
            } elseif (isset($_POST['preparacion_form'])) {
                $order_number = $_POST['order_number'];
                $nomb_pack = $_POST['nomb_pack'];
                $shipping = $_POST['shipping_method'];
                $adrres = $_POST['address_selection'];
                $address_parts = explode(',', $adrres);
                $address_id = trim($address_parts[0]);
                $shipping_parts = explode('|', $shipping);
                $shipping_id = $shipping_parts[0];
                $shipping_name = $shipping_parts[1];
                $valorEnvio = $_POST['valorEnvio'];
                $shipping_price = (int)$valorEnvio;
                $operator = $_POST['operator'];
                $details = $_POST['details'];
                $selectedIncoterms = $_POST['incoterms'];
                $incotermsArray = explode('|', $selectedIncoterms);
                $selectedCode = $incotermsArray[0];
                $selectedNombre = $incotermsArray[1];
                $status = 'preparacion';
                $caja = 'PL';
                if ((isset($_FILES['document_upload']) && $_FILES['document_upload']['error'] == UPLOAD_ERR_OK) ||
                    (isset($_FILES['document_upload3']) && $_FILES['document_upload3']['error'] == UPLOAD_ERR_OK) || (isset($_FILES['document_upload_nuevo']) && is_array($_FILES['document_upload_nuevo']['error']) &&
                        count(array_filter($_FILES['document_upload_nuevo']['error'], function ($err) {
                            return $err == UPLOAD_ERR_OK;
                        })) > 0)
                ) {
                    $documentUpload = $_FILES['document_upload']['tmp_name'];
                    $documentUploadNuevo = isset($_FILES['document_upload_nuevo']) ? $_FILES['document_upload_nuevo'] : null;
                    $documentUpload3 = $_FILES['document_upload3']['tmp_name'];
                    try {
                        if (!empty($documentUpload)) {
                            $this->uploadAndSaveDocument6($documentUpload, $order_number, $caja, $status, $operator, $details, $shipping_id, $shipping_name, $shipping_price, $address_id, $userId, $selectedNombre, $selectedCode);
                        }
                        if (!empty($documentUpload3)) {
                            $this->uploadAndSaveDocument10($documentUpload3, $order_number, $status, $shipping_price);
                        }
                        if (!empty($documentUploadNuevo) && is_array($documentUploadNuevo['tmp_name'])) {
                            foreach ($documentUploadNuevo['tmp_name'] as $key => $tmpName) {

                                if ($documentUploadNuevo['error'][$key] == UPLOAD_ERR_OK) {
                                    $this->uploadAndSaveDocument9($tmpName, $order_number, $caja, $status);
                                }
                            }
                        }
                        exit();
                    } catch (Exception $e) {
                        echo '<script type="text/javascript">
                                    alert("' . $e->getMessage() . '");
                                    window.location.href = window.location.href;
                                </script>';
                        exit();
                    }
                } else {
                    $this->actualizarOrden($operator, $details, $order_number, $shipping_id, $shipping_name, $shipping_price, $address_id, $status, $userId, $selectedNombre, $selectedCode);
                    echo '<script type="text/javascript">
                        alert("Proceso Completado");
                        window.location.href = window.location.href;
                    </script>';
                }
            } elseif (isset($_POST['delete_pack_detallado'])) {
                $order_number = $_POST['order_number'];
                $caja = $_POST['delete_pack_detallado'];
                $this->deleteUploadedFiles7($order_number, $caja);
                echo '<script type="text/javascript">window.location.href = window.location.href;</script>';
                exit();
            } elseif (isset($_POST['delete_cotizacion'])) {
                $order_number = $_POST['order_number'];
                $this->deleteUploadedFiles10($order_number);
                echo '<script type="text/javascript">window.location.href = window.location.href;</script>';
                exit();
            } elseif (isset($_POST['producion_form'])) {
                $order_number = $_POST['order_number'];
                $fechai = isset($_POST['fechai']) ? $_POST['fechai'] : null;
                $fechaf = isset($_POST['fechaf']) ? $_POST['fechaf'] : null;
                $status = 'produccion';
                $number_purchase = $_POST['number_purchase'];
                $number_sap = $_POST['number_sap'];
                $number_preforma = $_POST['number_preforma'];
                if (isset($_FILES['document_upload']) && $_FILES['document_upload']['error'] == UPLOAD_ERR_OK) {
                    $documentUpload = $_FILES['document_upload']['tmp_name'];
                } else {
                    $documentUpload = $this->getUploadedFiles4($order_number);
                }
                try {
                    $this->uploadAndSaveDocument3($documentUpload, $order_number, $number_sap, $number_preforma, $status, $number_purchase, $userId);
                    if (!empty($fechai)) {
                        $this->statusprodu($order_number, $fechai, $fechaf, $status, $userId);
                    }
                    echo '<script type="text/javascript">
                                    alert("Proceso completado con éxito");
                                    window.location.href = window.location.href;
                                </script>';
                    exit();
                } catch (Exception $e) {
                    echo '<script type="text/javascript">
                                    alert("' . $e->getMessage() . '");
                                    window.location.href = window.location.href;
                                </script>';
                    exit();
                }
            } elseif (isset($_POST['despacho_form'])) {
                $order_number = $_POST['order_number'];
                $nomber = isset($_POST['nomber']) ? $_POST['nomber'] : '';
                $number_guia = isset($_POST['number_guia']) ? $_POST['number_guia'] : '';
                $nomber_despacho = $_POST['country'];
                $nomber_arribo = $_POST['country2'];
                $fechas = $_POST['fechas'];
                $adiccional = isset($_POST['additional_info']) ? $_POST['additional_info'] : null;
                $status = 'transito';
                $number_invoice = $_POST['number_invoice'];
                if ((isset($_FILES['document_upload']) && $_FILES['document_upload']['error'] == UPLOAD_ERR_OK) ||
                    (isset($_FILES['document_upload2']) && $_FILES['document_upload2']['error'] == UPLOAD_ERR_OK) ||
                    (isset($_FILES['document_upload3']) && $_FILES['document_upload3']['error'] == UPLOAD_ERR_OK) ||
                    (isset($_FILES['document_upload_nuevo']) && is_array($_FILES['document_upload_nuevo']['error']) &&
                        count(array_filter($_FILES['document_upload_nuevo']['error'], function ($err) {
                            return $err == UPLOAD_ERR_OK;
                        })) > 0)
                ) {
                    $documentUpload = $_FILES['document_upload']['tmp_name'];
                    $documentUpload1 = isset($_FILES['document_upload2']) ? $_FILES['document_upload2']['tmp_name'] : null;
                    $documentUpload2 = isset($_FILES['document_upload3']) ? $_FILES['document_upload3']['tmp_name'] : null;
                    $documentUploadNuevo = isset($_FILES['document_upload_nuevo']) ? $_FILES['document_upload_nuevo'] : null;
                    try {
                        if (!empty($documentUpload)) {
                            $this->uploadAndSaveDocument4($documentUpload, $nomber, $number_guia, $nomber_despacho, $order_number, $nomber_arribo, $status, $adiccional, $userId, $fechas);
                        }
                        $contador = 2;
                        if (!empty($documentUploadNuevo) && is_array($documentUploadNuevo['tmp_name'])) {
                            foreach ($documentUploadNuevo['tmp_name'] as $key => $tmpName) {
                                if ($documentUploadNuevo['error'][$key] == UPLOAD_ERR_OK) {
                                    $number_guia2 = $number_guia . "-" . $contador;
                                    $this->uploadAndSaveDocument4($tmpName, $nomber, $number_guia2, $nomber_despacho, $order_number, $nomber_arribo, $status, $adiccional, $userId, $fechas);
                                }
                                $contador++;
                            }
                        }
                        if (!empty($documentUpload1) || !empty($documentUpload2)) {
                            $this->uploadAndSaveDocument5($documentUpload1, $documentUpload2, $order_number, $number_invoice, $status, $userId);
                        }
                        exit();
                    } catch (Exception $e) {
                        echo '<script type="text/javascript">
                                    alert("' . $e->getMessage() . '");
                                    window.location.href = window.location.href;
                                </script>';
                        exit();
                    }
                } else {
                    JFactory::getApplication()->enqueueMessage(JText::_('ERROR_UPLOADING_FILE'), 'error');
                }
            } elseif (isset($_POST['delete_guia'])) {
                $number_guia = $_POST['number_guia'];
                $order_number = $_POST['order_number'];
                $this->deleteUploadedFiles4($order_number, $number_guia);
                echo '<script type="text/javascript">window.location.href = window.location.href;</script>';
                exit();
            } elseif (isset($_POST['completo_form'])) {
                $order_number = $_POST['order_number'];
                $tipo_pago = $_POST['tipo'];
                $price = $this->getOrder($order_number);
                $orderFullPrice = $price[0]->order_full_price;
                $cantidad_pago = $orderFullPrice;
                $metodo_pago = $_POST['metodo'];
                $fecha_pago = $_POST['fechap'];
                $adiccional = $_POST['additional_info'];
                $status = 'pagado';
                $nombreArchivo = "pago_completo";
                if ($tipo_pago === 'Completo') {
                    if (isset($_FILES['document_upload']) && $_FILES['document_upload']['error'] == UPLOAD_ERR_OK) {
                        $documentUpload = $_FILES['document_upload']['tmp_name'];
                        try {
                            $this->uploadAndSaveDocument8($documentUpload, $order_number, $tipo_pago, $cantidad_pago, $metodo_pago, $fecha_pago, $adiccional, $status, $userId, $nombreArchivo);
                            echo '<script type="text/javascript">
                                        alert("Archivo subido con éxito");
                                        window.location.href = window.location.href;
                                    </script>';
                            exit();
                        } catch (Exception $e) {
                            echo '<script type="text/javascript">
                                        alert("' . $e->getMessage() . '");
                                        window.location.href = window.location.href;
                                    </script>';
                            exit();
                        }
                    } else {
                        JFactory::getApplication()->enqueueMessage(JText::_('ERROR_UPLOADING_FILE'), 'error');
                    }
                } else {
                    JFactory::getApplication()->enqueueMessage(JText::_('ERROR_UPLOADING_FILE'), 'error');
                }
            } elseif (isset($_POST['parcial_form'])) {
                $tipo_pago = $_POST['tipo'];
                $tyme_credit2 = isset($_POST['tyme_credit2']) ? intval($_POST['tyme_credit2']) : 0;
                $metodo2 = $_POST['metodo2'];
                $fechap2 = $_POST['fechap2'];
                $additional_info3 = $_POST['additional_info3'];
                $tyme_credit_nuevo = $_POST['tyme_credit_nuevo'];
                $metodo_nuevo = $_POST['metodo_nuevo'];
                $fechas_nuevo = $_POST['fechas_nuevo'];
                $additional_info_nuevo = $_POST['additional_info_nuevo'];
                $status = 'pagado';
                $nombreArchivo = "pago";
                if ($tipo_pago === 'Parcial') {
                    if ((isset($_FILES['document_upload2']) && $_FILES['document_upload2']['error'] == UPLOAD_ERR_OK) || (isset($_FILES['document_upload_nuevo']) && array_filter($_FILES['document_upload_nuevo']['error'], function ($error) {
                        return $error == UPLOAD_ERR_OK;
                    }))) {
                        $documentUpload = $_FILES['document_upload2']['tmp_name'];
                        try {
                            if (isset($_FILES['document_upload2']) && $_FILES['document_upload2']['error'] == UPLOAD_ERR_OK)
                                try {


                                    $this->uploadAndSaveDocument8($documentUpload, $order_number, $tipo_pago, $tyme_credit2, $metodo2, $fechap2, $additional_info3, $status, $userId, $nombreArchivo);
                                    echo '<script type="text/javascript">
                                        alert("Archivo subido con éxito");
                                        window.location.href = window.location.href;
                                    </script>';
                                } catch (Exception $e) {
                                    echo '<script type="text/javascript">
                                            alert("' . $e->getMessage() . '");
                                            window.location.href = window.location.href;
                                        </script>';
                                    exit();
                                }

                            foreach ($_FILES['document_upload_nuevo']['tmp_name'] as $key => $tmp_name) {
                                $documentUploadNuevo = $tmp_name;
                                $tyme_credit_nuevo = $_POST['tyme_credit_nuevo'][$key];
                                $metodo_nuevo = $_POST['metodo_nuevo'][$key];
                                $fechas_nuevo = $_POST['fechas_nuevo'][$key];
                                $additional_info_nuevo = $_POST['additional_info_nuevo'][$key];
                                $this->uploadAndSaveDocument7($documentUploadNuevo, $order_number, $tipo_pago, $tyme_credit_nuevo, $metodo_nuevo, $fechas_nuevo, $additional_info_nuevo, $status, $userId, $nombreArchivo);
                            }
                            echo '<script type="text/javascript">
                                        alert("Archivos subidos con éxito");
                                        window.location.href = window.location.href;
                                    </script>';
                            exit();
                        } catch (Exception $e) {
                            echo '<script type="text/javascript">
                                        alert("' . $e->getMessage() . '");
                                        window.location.href = window.location.href;
                                    </script>';
                            exit();
                        }
                    } else {
                        JFactory::getApplication()->enqueueMessage(JText::_('ERROR_UPLOADING_FILE'), 'error');
                    }
                } else {
                    JFactory::getApplication()->enqueueMessage(JText::_('ERROR_UPLOADING_FILE'), 'error');
                }
            } elseif (isset($_POST['delete_comprobante_completo'])) {
                $order_number = $_POST['order_number'];
                $price = $this->getOrder($order_number);
                $orderFullPrice = $price[0]->order_full_price;
                $cantidad_pago = $orderFullPrice;
                $this->deleteUploadedFiles8($order_number, $cantidad_pago);
                echo '<script type="text/javascript">window.location.href = window.location.href;</script>';
                exit();
            } elseif (isset($_POST['delete_comprobante_parcial'])) {
                $order_number = $_POST['order_number'];
                $nombre = $_POST['delete_comprobante_parcial'];
                $this->deleteUploadedFiles9($order_number, $nombre);
                echo '<script type="text/javascript">window.location.href = window.location.href;</script>';
                exit();
            } elseif (isset($_POST['delete_invoice'])) {
                $number_invoice = $_POST['number_invoice'];
                $order_number = $_POST['order_number'];
                $this->deleteUploadedFiles5($order_number, $number_invoice);
                echo '<script type="text/javascript">window.location.href = window.location.href;</script>';
                exit();
            } elseif (isset($_POST['delete_certificado'])) {
                $number_invoice = $_POST['number_invoice'];
                $order_number = $_POST['order_number'];
                $this->deleteUploadedFiles6($order_number, $number_invoice);
                echo '<script type="text/javascript">window.location.href = window.location.href;</script>';
                exit();
            } elseif (isset($_POST['transito_form'])) {
                $order_number = $_POST['order_number'];
                $fechaa = isset($_POST['fechaa']) ? $_POST['fechaa'] : null;
                $status = 'facturacion';
                $nomb_pack2 = 'PL';
                if (
                    (isset($_POST['puerto_intermedio']) && is_array($_POST['puerto_intermedio'])) ||
                    (isset($_POST['puerto_intermedio_nuevo']) && is_array($_POST['puerto_intermedio_nuevo'])) ||
                    (empty($_POST['puerto_intermedio']) && empty($_POST['puerto_intermedio_nuevo'])) ||
                    (isset($_FILES['document_upload']) && $_FILES['document_upload']['error'] == UPLOAD_ERR_OK) ||
                    (isset($_FILES['document_upload_nuevo']) &&
                        is_array($_FILES['document_upload_nuevo']['error']) &&
                        count(array_filter($_FILES['document_upload_nuevo']['error'], function ($err) {
                            return $err == UPLOAD_ERR_OK;
                        })) > 0)
                ) {

                    $puertos_intermedios2 = $_POST['puerto_intermedio'];
                    $puertos_intermedios_nuevos = $_POST['puerto_intermedio_nuevo'];
                    if (!empty($puertos_intermedios2)) {
                        $cadena_puertos_intermedios = implode(',', $puertos_intermedios2);
                    }
                    $documentUpload = $_FILES['document_upload']['tmp_name'];
                    $documentUploadNuevo = isset($_FILES['document_upload_nuevo']) ? $_FILES['document_upload_nuevo'] : null;
                    try {
                        if (!empty($documentUpload)) {

                            $this->uploadAndSaveDocument2($documentUpload, $order_number, $nomb_pack2, $cadena_puertos_intermedios, $status, $userId, $fechaa);
                        }

                        $contador = 2;
                        if (!empty($documentUploadNuevo) && is_array($documentUploadNuevo['tmp_name'])) {
                            foreach ($documentUploadNuevo['tmp_name'] as $key => $tmpName) {
                                if ($documentUploadNuevo['error'][$key] == UPLOAD_ERR_OK) {
                                    $documentUploadNuevo = $tmpName;

                                    $this->uploadAndSaveDocument13($documentUploadNuevo, $order_number, $nomb_pack2);
                                }
                                $contador++;
                            }
                        }
                        if (!empty($puertos_intermedios_nuevos)) {
                            $cadena_puertos_intermedios_nuevos = implode(',', $puertos_intermedios_nuevos);
                            $puertos_intermedios = $cadena_puertos_intermedios . "," . $cadena_puertos_intermedios_nuevos;
                            $this->savedatatointermedio($order_number, $puertos_intermedios, $status, $userId, $fechaa);
                        } else {
                            $puertos_intermedios = $cadena_puertos_intermedios;
                            $this->savedatatointermedio($order_number, $puertos_intermedios, $status, $userId, $fechaa);
                        }

                        exit();
                    } catch (Exception $e) {
                        echo '<script type="text/javascript">
                                    alert("' . $e->getMessage() . '");
                                    window.location.href = window.location.href;
                                </script>';
                        exit();
                    }
                } else {
                    JFactory::getApplication()->enqueueMessage(JText::_('ERROR_UPLOADING_FILE'), 'error');
                }
            } elseif (isset($_POST['delete_pack'])) {
                $pack_listing = $_POST['pack_to_delete'];
                $order_number = $_POST['order_number'];
                $this->deleteUploadedFiles2($order_number, $pack_listing);
                echo '<script type="text/javascript">window.location.href = window.location.href;</script>';
                exit();
            } elseif (isset($_POST['status_form'])) {
                $order_number = $_POST['order_number'];
                $status = $_POST['status'];
                $this->updateOrder($order_number, $status);
                echo '<script type="text/javascript">
                    alert("Proceso completado con éxito");
                    window.location.href = window.location.href;
                </script>';
            } elseif (isset($_POST['status_form2'])) {
                $order_number = $_POST['order_number'];
                $status = $_POST['status'];
                $this->updateOrder($order_number, $status);
                echo '<script type="text/javascript">
                    alert("Proceso completado con éxito");
                    window.location.href = window.location.href;
                </script>';
            }
        }

        return $output;
    }
    //Funcion para subir orden de compra
    protected function uploadAndSaveDocument($documentUpload, $userId, $order_number, $number_purchase, $status, $customer_id)
    {
        try {
            $uploadDir = '/images/orders/';
            $filename = 'pdf_' . time() . '.pdf';
            $filePath = JPATH_ROOT . $uploadDir . $filename;

            if (!file_exists(JPATH_ROOT . $uploadDir)) {
                mkdir(JPATH_ROOT . $uploadDir, 0755, true);
            }

            if (move_uploaded_file($documentUpload, $filePath)) {
                $this->saveDataToPreforma($uploadDir . $filename, $userId, $order_number, $number_purchase, $status, $customer_id);
            } else {
                throw new Exception('Error moving uploaded file');
            }
        } catch (Exception $e) {
            throw new Exception('Error uploading and saving document: ' . $e->getMessage());
        }
    }
    //Funcion para subir packinglist caja por caja
    protected function uploadAndSaveDocument2($documentUpload, $order_number, $nomb_pack2, $puertos_intermedios, $status, $userId, $fechaa)
    {
        try {
            $uploadDir = '/images/pack/';
            $filename = 'pdf_' . time() . '.pdf';
            $filePath = JPATH_ROOT . $uploadDir . $filename;

            if (!file_exists(JPATH_ROOT . $uploadDir)) {
                mkdir(JPATH_ROOT . $uploadDir, 0755, true);
            }
            if ($documentUpload != null && move_uploaded_file($documentUpload, $filePath)) {
                $filePath = $uploadDir . $filename;
            } else {
                $filePath = null;
            }
            $this->savedatatointermedio($order_number, $puertos_intermedios, $status, $userId, $fechaa);
            $this->saveDataToPack($filePath, $order_number, $nomb_pack2);
        } catch (Exception $e) {
            throw new Exception('Error uploading and saving document: ' . $e->getMessage());
        }
    }
    protected function uploadAndSaveDocument13($documentUploadNuevo, $order_number, $nomb_pack2)
    {
        try {
            $uploadDir = '/images/pack/';
            $filename = 'pdf_' . time() . '.pdf';
            $filePath = JPATH_ROOT . $uploadDir . $filename;

            if (!file_exists(JPATH_ROOT . $uploadDir)) {
                mkdir(JPATH_ROOT . $uploadDir, 0755, true);
            }
            if ($documentUploadNuevo != null && move_uploaded_file($documentUploadNuevo, $filePath)) {
                $filePath = $uploadDir . $filename;
            } else {
                $filePath = null;
            }
            $this->saveDataToPack($filePath, $order_number, $nomb_pack2);
            echo '<script type="text/javascript">
                                    alert("Proceso Completado");
                                    window.location.href = window.location.href;
                                </script>';
        } catch (Exception $e) {
            throw new Exception('Error uploading and saving document: ' . $e->getMessage());
        }
    }
    public function generateHeadContent()
    {
        return '
        <link href="https://fonts.googleapis.com/css2?family=PT+Serif&family=Work+Sans:wght@400&display=swap" rel="stylesheet">
        <style>' . $this->css() . '</style>
    ';
    }

    //Funcion para subir preforma
    protected function uploadAndSaveDocument3($documentUpload, $order_number, $number_sap, $number_preforma, $status, $number_purchase, $userId)
    {
        try {
            $uploadDir = '/images/preformar/';
            $filename = 'pdf_' . time() . '.pdf';
            $filePath = JPATH_ROOT . $uploadDir . $filename;
            if (!file_exists(JPATH_ROOT . $uploadDir)) {
                mkdir(JPATH_ROOT . $uploadDir, 0755, true);
            }
            if ($documentUpload && move_uploaded_file($documentUpload, $filePath)) {
                $this->saveDataToPreforma2($uploadDir . $filename, $order_number, $number_sap, $number_preforma, $number_purchase, $status, $userId);
            } else {
                $documentUpload = $this->getUploadedFiles4($order_number);
                $this->saveDataToPreforma2($documentUpload, $order_number, $number_sap, $number_preforma, $number_purchase, $status, $userId);
            }
        } catch (Exception $e) {
            throw new Exception('Error uploading and saving document: ' . $e->getMessage());
        }
    }
    //Funcion para subir guia despacho
    protected function uploadAndSaveDocument4($documentUpload, $nomber, $number_guia, $nomber_despacho, $order_number, $nomber_arribo, $status, $adiccional, $userId, $fechas)
    {
        try {
            $uploadDir = '/images/guias/';
            $filename = 'pdf_' . time() . '.pdf';
            $filePath = JPATH_ROOT . $uploadDir . $filename;

            if (!file_exists(JPATH_ROOT . $uploadDir)) {
                mkdir(JPATH_ROOT . $uploadDir, 0755, true);
            }
            if ($documentUpload != null && move_uploaded_file($documentUpload, $filePath)) {
                $filePath = $uploadDir . $filename;
            } else {
                $filePath = null;
            }
            $this->savedatatoshipping($filePath, $nomber, $number_guia, $nomber_despacho, $order_number, $nomber_arribo, $adiccional, $status, $userId, $fechas);
            echo '<script type="text/javascript">
                                    alert("Archivo subido con éxito");
                                    window.location.href = window.location.href;
                                </script>';
        } catch (Exception $e) {
            throw new Exception('Error uploading and saving document: ' . $e->getMessage());
        }
    }
    //Funcion para subir facturas y certificado
    protected function uploadAndSaveDocument5($documentUpload1, $documentUpload2, $order_number, $number_invoice, $status, $userId)
    {
        try {
            // Para el documento de factura
            $uploadDir1 = '/images/invoice/';
            $filename1 = 'pdf_invoice_' . time() . '.pdf';
            $filePath1 = JPATH_ROOT . $uploadDir1 . $filename1;

            // Para el documento de certificado
            $uploadDir2 = '/images/certificado/';
            $filename2 = 'pdf_certificado_' . time() . '.pdf';
            $filePath2 = JPATH_ROOT . $uploadDir2 . $filename2;

            if (!file_exists(JPATH_ROOT . $uploadDir1)) {
                mkdir(JPATH_ROOT . $uploadDir1, 0755, true);
            }

            if (!file_exists(JPATH_ROOT . $uploadDir2)) {
                mkdir(JPATH_ROOT . $uploadDir2, 0755, true);
            }

            if ($documentUpload1 != null && move_uploaded_file($documentUpload1, $filePath1)) {
                $filePath1 = $uploadDir1 . $filename1;
            } else {
                $filePath1 = null;
            }

            if ($documentUpload2 != null && move_uploaded_file($documentUpload2, $filePath2)) {
                $filePath2 = $uploadDir2 . $filename2;
            } else {
                $filePath2 = null;
            }

            $this->savedatatoinvoice($filePath1, $filePath2, $order_number, $number_invoice, $status, $userId);
            echo '<script type="text/javascript">
                                    alert("Archivo subido con éxito");
                                    window.location.href = window.location.href;
                                </script>';
        } catch (Exception $e) {
            throw new Exception('Error uploading and saving document: ' . $e->getMessage());
        }
    }

    //Funcion para subir packing LIST DETALLADO
    protected function uploadAndSaveDocument6($documentUpload, $order_number, $caja, $status, $operator, $details, $shipping_id, $shipping_name, $shipping_price, $address_id, $userId, $selectedNombre, $selectedCode)
    {
        try {
            $uploadDir = '/images/pack/';
            $filename = 'pdf_' . time() . '.pdf';
            $filePath = JPATH_ROOT . $uploadDir . $filename;

            if (!file_exists(JPATH_ROOT . $uploadDir)) {
                mkdir(JPATH_ROOT . $uploadDir, 0755, true);
            }

            if (move_uploaded_file($documentUpload, $filePath)) {
                $this->saveDataToPack2($uploadDir . $filename, $order_number, $caja, $status);
            } else {
                throw new Exception('Error moving uploaded file');
            }
            $this->actualizarOrden($operator, $details, $order_number, $shipping_id, $shipping_name, $shipping_price, $address_id, $status, $userId, $selectedNombre, $selectedCode);
            echo '<script type="text/javascript">
                    alert("Proceso Completado");
                    window.location.href = window.location.href;
                </script>';
        } catch (Exception $e) {
            throw new Exception('Error uploading and saving document: ' . $e->getMessage());
        }
    }
    protected function uploadAndSaveDocument9($documentUploadNuevo, $order_number, $caja, $status)
    {
        try {
            $uploadDir = '/images/pack/';
            $filename = 'pdf_' . time() . '.pdf';
            $filePath = JPATH_ROOT . $uploadDir . $filename;

            if (!file_exists(JPATH_ROOT . $uploadDir)) {
                mkdir(JPATH_ROOT . $uploadDir, 0755, true);
            }

            if (move_uploaded_file($documentUploadNuevo, $filePath)) {
                $this->saveDataToPack2($uploadDir . $filename, $order_number, $caja, $status);
            } else {
                throw new Exception('Error moving uploaded file');
            }
            echo '<script type="text/javascript">
                    alert("Proceso Completado");
                    window.location.href = window.location.href;
                </script>';
        } catch (Exception $e) {
            throw new Exception('Error uploading and saving document: ' . $e->getMessage());
        }
    }
    protected function uploadAndSaveDocument10($documentUpload3, $order_number, $status, $shipping_price)
    {
        try {
            $uploadDir = '/images/cotizacion/';
            $filename = 'pdf_' . time() . '.pdf';
            $filePath = JPATH_ROOT . $uploadDir . $filename;

            if (!file_exists(JPATH_ROOT . $uploadDir)) {
                mkdir(JPATH_ROOT . $uploadDir, 0755, true);
            }

            if (move_uploaded_file($documentUpload3, $filePath)) {
                $this->saveDataToCotizacion($uploadDir . $filename, $order_number, $status, $shipping_price);
            } else {
                throw new Exception('Error moving uploaded file');
            }
            echo '<script type="text/javascript">
                    alert("Proceso Completado");
                    window.location.href = window.location.href;
                </script>';
        } catch (Exception $e) {
            throw new Exception('Error uploading and saving document: ' . $e->getMessage());
        }
    }
    //fin
    //Funcion para subir el comprobante pago completo
    protected function uploadAndSaveDocument8($documentUpload, $order_number, $tipo_pago, $cantidad_pago, $metodo_pago, $fecha_pago, $adiccional, $status, $userId, $nombreArchivo)
    {
        try {
            $uploadDir = '/images/comprobantes/';
            $filename = 'pdf_' . time() . '.pdf';
            $filePath = JPATH_ROOT . $uploadDir . $filename;

            if (!file_exists(JPATH_ROOT . $uploadDir)) {
                mkdir(JPATH_ROOT . $uploadDir, 0755, true);
            }

            if (move_uploaded_file($documentUpload, $filePath)) {
                $this->saveDataToPagoCom($uploadDir . $filename, $order_number, $tipo_pago, $cantidad_pago, $metodo_pago, $fecha_pago, $adiccional, $status, $userId, $nombreArchivo);
            } else {
                throw new Exception('Error moving uploaded file');
            }
        } catch (Exception $e) {
            throw new Exception('Error uploading and saving document: ' . $e->getMessage());
        }
    }
    //Funcion para subir el comprobante pago parcial
    protected function uploadAndSaveDocument7($documentUploadNuevo, $order_number, $tipo_pago, $tyme_credit_nuevo, $metodo_nuevo, $fechas_nuevo, $additional_info_nuevo, $status, $userId, $nombreArchivo)
    {
        try {
            $uploadDir = '/images/comprobantes/';
            $filename = 'pdf_' . time() . '_' . uniqid() . '.pdf';
            $filePath = JPATH_ROOT . $uploadDir . $filename;
            if (!file_exists(JPATH_ROOT . $uploadDir)) {
                mkdir(JPATH_ROOT . $uploadDir, 0755, true);
            }
            if (!move_uploaded_file($documentUploadNuevo, $filePath)) {
                $lastError = error_get_last();
                throw new Exception('Error moving uploaded file: ' . $lastError['message']);
            }
            $this->saveDataToPagoPar($uploadDir . $filename, $order_number, $tipo_pago, $tyme_credit_nuevo, $metodo_nuevo, $fechas_nuevo, $additional_info_nuevo, $status, $userId, $nombreArchivo);
        } catch (Exception $e) {
            throw new Exception('Error uploading and saving document: ' . $e->getMessage() . ' Code: ' . $e->getCode());
        }
    }


    //funcion para guardar el nuimero de orden de compra
    protected function saveDataToPreforma($filePath, $userId, $order_number, $number_purchase, $status, $customer_id)
    {
        $db = JFactory::getDbo();

        try {
            $query = $db->getQuery(true);
            $query->select($db->quoteName('id'))
                ->from($db->quoteName('josmwt_preforma'))
                ->where($db->quoteName('order_number') . ' = ' . $db->quote($order_number));
            $db->setQuery($query);
            $existingOrderId = $db->loadResult();
            if ($existingOrderId) {
                $query = $db->getQuery(true);
                $query->update($db->quoteName('josmwt_preforma'))
                    ->set($db->quoteName('preformar') . ' = ' . $db->quote($filePath))
                    ->where($db->quoteName('id') . ' = ' . (int) $existingOrderId);
                $db->setQuery($query);
                $db->execute();
            } else {
                $query = $db->getQuery(true);
                $columns = array('preformar', 'order_number', 'number_purchase');
                $values = array($db->quote($filePath), $db->quote($order_number), $db->quote($number_purchase));
                $query->insert($db->quoteName('josmwt_preforma'))
                    ->columns($db->quoteName($columns))
                    ->values(implode(',', $values));
                $db->setQuery($query);
                $db->execute();
            }
            $this->updateCustomerOrder($order_number, $customer_id);
            $this->updateOrder($order_number, $status);
            $userEmail = $this->getUserId($userId);
            $this->sendEmail($userEmail, 'El estado de tu pedido ha cambiado', 'Tu pedido ahora está en proceso.', $order_number);
            echo '<script type="text/javascript">
           alert("Proceso Completado");
           window.location.href = window.location.href;
       </script>';
        } catch (Exception $e) {
            throw new Exception('Error saving data to preforma: ' . $e->getMessage());
        }
    }


    //funcion para guardar el nuimero de SAP
    protected function saveDataToPreforma2($filePath, $order_number, $number_sap, $number_preforma, $number_purchase, $status, $userId)
    {
        try {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->select($db->quoteName(array('order_number', 'preforma')))
                ->from($db->quoteName('josmwt_sap'))
                ->where($db->quoteName('order_number') . ' = ' . $db->quote($order_number));
            $db->setQuery($query);
            $existingOrder = $db->loadAssoc();
            if ($existingOrder) {
                $existingFilePath = $_SERVER['DOCUMENT_ROOT'] . $existingOrder['preforma'];
                if (file_exists($existingFilePath)) {
                    unlink($existingFilePath);
                }
                $query = $db->getQuery(true);
                $fields = array(
                    $db->quoteName('preforma') . ' = ' . $db->quote($filePath),
                    $db->quoteName('number_sap') . ' = ' . $db->quote($number_sap),
                    $db->quoteName('number_preforma') . ' = ' . $db->quote($number_preforma),
                    $db->quoteName('number_purchase') . ' = ' . $db->quote($number_purchase)
                );
                $conditions = array(
                    $db->quoteName('order_number') . ' = ' . $db->quote($order_number)
                );
                $query->update($db->quoteName('josmwt_sap'))->set($fields)->where($conditions);
            } else {
                $query = $db->getQuery(true);
                $columns = array('preforma', 'order_number', 'number_sap', 'number_preforma', 'number_purchase');
                $values = array($db->quote($filePath), $db->quote($order_number), $db->quote($number_sap), $db->quote($number_preforma), $db->quote($number_purchase));
                $query->insert($db->quoteName('josmwt_sap'))
                    ->columns($db->quoteName($columns))
                    ->values(implode(',', $values));
            }
            $db->setQuery($query);
            $db->execute();
            JFactory::getApplication()->enqueueMessage(JText::_('SUCCESSFUL_ORDER_UPDATE'), 'success');
        } catch (Exception $e) {
            throw new Exception('Error saving data to preforma: ' . $e->getMessage());
        }
    }

    //funcion para guardar los packing list
    protected function saveDataToPack($filePath, $order_number, $nomb_pack)
    {
        try {
            $db = JFactory::getDbo();

            // Verificar si ya existe un registro con el mismo order_number
            $queryCheck = $db->getQuery(true)
                ->select($db->quoteName('id'))
                ->from($db->quoteName('josmwt_pack'))
                ->where($db->quoteName('order_number') . ' = ' . $db->quote($order_number));

            $db->setQuery($queryCheck);
            $existingRecord = $db->loadResult();

            if ($existingRecord) {
                // Si existe, genera un contador y concaténalo con $nomb_pack
                $counter = $this->generateCounterForDuplicateOrderNumber($order_number);
                $nomb_pack = $nomb_pack . '_' . $counter;
            }

            // Insertar los datos en la base de datos
            $query = $db->getQuery(true);
            $columns = array('pack', 'order_number', 'nomb_pack');
            $values = array($db->quote($filePath), $db->quote($order_number), $db->quote($nomb_pack));

            $query->insert($db->quoteName('josmwt_pack'))
                ->columns($db->quoteName($columns))
                ->values(implode(',', $values));

            $db->setQuery($query);
            $db->execute();
        } catch (Exception $e) {
            throw new Exception('Error saving data to preforma: ' . $e->getMessage());
        }
    }

    // Función para generar un contador para order_number duplicados
    private function generateCounterForDuplicateOrderNumber($order_number)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('COUNT(*)')
            ->from($db->quoteName('josmwt_pack'))
            ->where($db->quoteName('order_number') . ' = ' . $db->quote($order_number));

        $db->setQuery($query);
        $count = $db->loadResult();

        return $count + 1;
    }

    //funcion para guardar los packing list detalladoo
    protected function saveDataToPack2($filePath, $order_number, $caja, $status)
    {
        try {
            $db = JFactory::getDbo();

            // Verificar si ya existe un registro con el mismo order_number
            $queryCheck = $db->getQuery(true)
                ->select($db->quoteName('id'))
                ->from($db->quoteName('josmwt_pack_detallado'))
                ->where($db->quoteName('order_number') . ' = ' . $db->quote($order_number));

            $db->setQuery($queryCheck);
            $existingRecord = $db->loadResult();

            if ($existingRecord) {
                // Si existe, genera un contador y concaténalo con $caja
                $counter = $this->generateCounterForDuplicateOrderNumber2($order_number);
                $caja = $caja . '_' . $counter;
            }

            // Insertar los datos en la base de datos
            $query = $db->getQuery(true);
            $columns = array('pack', 'order_number', 'caja');
            $values = array($db->quote($filePath), $db->quote($order_number), $db->quote($caja));

            $query->insert($db->quoteName('josmwt_pack_detallado'))
                ->columns($db->quoteName($columns))
                ->values(implode(',', $values));

            $db->setQuery($query);
            $db->execute();

            $this->updateOrder($order_number, $status);

            echo '<script type="text/javascript">
                        alert("Archivo subido con éxito");
                        window.location.href = window.location.href;
                    </script>';
        } catch (Exception $e) {
            throw new Exception('Error saving data to preforma: ' . $e->getMessage());
        }
    }

    // Función para generar un contador para order_number duplicados
    private function generateCounterForDuplicateOrderNumber2($order_number)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('COUNT(*)')
            ->from($db->quoteName('josmwt_pack_detallado'))
            ->where($db->quoteName('order_number') . ' = ' . $db->quote($order_number));

        $db->setQuery($query);
        $count = $db->loadResult();

        return $count + 1;
    }

    //funcion para guardar cotizaciones
    protected function saveDataToCotizacion($filePath, $order_number, $status, $shipping_price)
    {
        try {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $columns = array('cotizacion', 'order_number');
            $values = array($db->quote($filePath), $db->quote($order_number));
            $query->insert($db->quoteName('josmwt_cotizacion'))
                ->columns($db->quoteName($columns))
                ->values(implode(',', $values));
            $db->setQuery($query);
            $db->execute();
            $query = $db->getQuery(true);
            $query->update($db->quoteName('josmwt_hikashop_order'));
            $query->set($db->quoteName('order_shipping_price') . ' = ' . $db->quote($shipping_price));
            $query->where($db->quoteName('order_number') . ' = ' . $db->quote($order_number));
            $db->setQuery($query);
            $db->execute();
            $this->updateOrder($order_number, $status);
            echo '<script type="text/javascript">
                        alert("Archivo subido con éxito");
                        window.location.href = window.location.href;
                    </script>';
        } catch (Exception $e) {
            throw new Exception('Error saving data to preforma: ' . $e->getMessage());
        }
    }


    //funcion para guardar las guias de despacho
    protected function savedatatoshipping($filePath, $nomber, $number_guia, $nomber_despacho, $order_number, $nomber_arribo, $adiccional, $status, $userId, $fechas)
    {
        try {
            $fechas = $fechas ? date('Y-m-d', strtotime($fechas)) : '0000-00-00';
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->select($db->quoteName('order_number'))
                ->from($db->quoteName('josmwt_shipping'))
                ->where($db->quoteName('order_number') . ' = ' . $db->quote($order_number));
            $db->setQuery($query);
            $exists = $db->loadResult();
            $query = $db->getQuery(true);
            if ($exists) {
                $fields = array(
                    $db->quoteName('guia') . ' = ' . $db->quote($filePath),
                    $db->quoteName('nomber') . ' = ' . $db->quote($nomber),
                    $db->quoteName('number_guia') . ' = ' . $db->quote($number_guia),
                    $db->quoteName('nomber_despacho') . ' = ' . $db->quote($nomber_despacho),
                    $db->quoteName('nomber_arribo') . ' = ' . $db->quote($nomber_arribo),
                    $db->quoteName('adiccional') . ' = ' . $db->quote($adiccional),
                    $db->quoteName('fechas') . ' = ' . $db->quote($fechas),
                );
                $conditions = array(
                    $db->quoteName('order_number') . ' = ' . $db->quote($order_number)
                );

                $query->update($db->quoteName('josmwt_shipping'))
                    ->set($fields)
                    ->where($conditions);
            } else {
                $columns = array('guia', 'order_number', 'nomber', 'number_guia', 'nomber_despacho', 'nomber_arribo', 'adiccional', 'fechas');
                $values = array($db->quote($filePath), $db->quote($order_number), $db->quote($nomber), $db->quote($number_guia), $db->quote($nomber_despacho), $db->quote($nomber_arribo), $db->quote($adiccional), $db->quote($fechas));
                $query->insert($db->quoteName('josmwt_shipping'))
                    ->columns($db->quoteName($columns))
                    ->values(implode(',', $values));
            }
            $db->setQuery($query);
            $db->execute();
            $userEmail = $this->getUserId($userId);
            $this->sendEmail($userEmail, 'El estado de tu pedido ha cambiado', 'Tu pedido está facturado y en transito a destino.', $order_number);
        } catch (Exception $e) {
            throw new Exception('Error saving data to despacho: ' . $e->getMessage());
        }
    }
    //funcion para guardar puertos intermedios
    protected function savedatatointermedio($order_number, $puertos_intermedios, $status, $userId, $fechaa)
    {
        try {
            $fechaa = $fechaa ? date('Y-m-d', strtotime($fechaa)) : '0000-00-00';
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->select($db->quoteName('order_number'))
                ->from($db->quoteName('josmwt_shipping'))
                ->where($db->quoteName('order_number') . ' = ' . $db->quote($order_number));
            $db->setQuery($query);
            $exists = $db->loadResult();
            $query = $db->getQuery(true);
            if ($exists) {
                $fields = array(
                    $db->quoteName('puerto_intermedio') . ' = ' . $db->quote($puertos_intermedios),
                    $db->quoteName('fecha_arribo') . ' = ' . $db->quote($fechaa)
                );
                $conditions = array(
                    $db->quoteName('order_number') . ' = ' . $db->quote($order_number)
                );
                $query->update($db->quoteName('josmwt_shipping'))->set($fields)->where($conditions);
            } else {
                throw new Exception('Error saving data to transito: Existing record found.');
            }
            $db->setQuery($query);
            $db->execute();
            echo '<script type="text/javascript">
                                    alert("Proceso Completado");
                                    window.location.href = window.location.href;
                                </script>';
            $userEmail = $this->getUserId($userId);
            $this->sendEmail($userEmail, 'El estado de tu pedido ha cambiado', 'Tu pedido se actualizo.', $order_number);
        } catch (Exception $e) {
            // Manejar la excepción aquí
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
    }


    //funcion para guardar las facturas
    protected function savedatatoinvoice($invoicePath, $certificadoPath, $order_number, $number_invoice, $status, $userId)
    {
        try {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);

            // Verificar si el order_number ya existe
            $query->select($db->quoteName('order_number'))
                ->from($db->quoteName('josmwt_invoice'))
                ->where($db->quoteName('order_number') . ' = ' . $db->quote($order_number));
            $db->setQuery($query);
            $exists = $db->loadResult();

            $query = $db->getQuery(true);

            if ($exists) {
                // Si existe, actualizamos el registro
                $fields = array(
                    $db->quoteName('number_invoice') . ' = ' . $db->quote($number_invoice)
                );
                if ($invoicePath !== null) {
                    $fields[] = $db->quoteName('invoice') . ' = ' . $db->quote($invoicePath);
                }
                if ($certificadoPath !== null) {
                    $fields[] = $db->quoteName('certificado') . ' = ' . $db->quote($certificadoPath);
                }
                $conditions = array(
                    $db->quoteName('order_number') . ' = ' . $db->quote($order_number)
                );
                $query->update($db->quoteName('josmwt_invoice'))->set($fields)->where($conditions);
            } else {
                // Si no existe, insertamos un nuevo registro
                $columns = array('invoice', 'certificado', 'order_number', 'number_invoice');
                $values = array($db->quote($invoicePath), $db->quote($certificadoPath), $db->quote($order_number), $db->quote($number_invoice));
                $query->insert($db->quoteName('josmwt_invoice'))
                    ->columns($db->quoteName($columns))
                    ->values(implode(',', $values));
            }

            $db->setQuery($query);
            $db->execute();
            //$this->updateOrder($order_number, $status);
            $userEmail = $this->getUserId($userId);
            $this->sendEmail($userEmail, 'El estado de tu pedido ha cambiado', 'Tu pedido ahora está facturado.', $order_number);
        } catch (Exception $e) {
            throw new Exception('Error saving data to despacho: ' . $e->getMessage());
        }
    }

    //funcion para guardar la informacion del estado de produccion
    public function statusprodu($order_number, $fechai, $fechaf, $status, $userId)
    {
        try {
            $fechai = $fechai ? date('Y-m-d', strtotime($fechai)) : null;
            $fechaf = $fechaf ? date('Y-m-d', strtotime($fechaf)) : '0000-00-00';
            $status2 = ($fechaf == '0000-00-00') ? 'No liberado' : 'Liberado';
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->select($db->quoteName('order_number'))
                ->from($db->quoteName('josmwt_produccion'))
                ->where($db->quoteName('order_number') . ' = ' . $db->quote($order_number));
            $db->setQuery($query);
            $existingOrderNumber = $db->loadResult();
            if ($existingOrderNumber) {
                $query = $db->getQuery(true);
                $fields = array(
                    $db->quoteName('fechai') . ' = ' . $db->quote($fechai),
                    $db->quoteName('fechaf') . ' = ' . $db->quote($fechaf),
                    $db->quoteName('status') . ' = ' . $db->quote($status2)
                );
                $conditions = array(
                    $db->quoteName('order_number') . ' = ' . $db->quote($order_number)
                );
                $query->update($db->quoteName('josmwt_produccion'))->set($fields)->where($conditions);
                if ($fechaf != '0000-00-00') {
                    $this->updateOrder($order_number, $status);
                    $userEmail = $this->getUserId($userId);
                    $this->sendEmail($userEmail, 'El estado de tu pedido ha cambiado', 'Tu pedido ahora está en preparación.', $order_number);
                }
            } else {
                $query = $db->getQuery(true);
                $columns = array('order_number', 'fechai', 'fechaf', 'status');
                $values = array($db->quote($order_number), $db->quote($fechai), $db->quote($fechaf), $db->quote($status2));
                $query->insert($db->quoteName('josmwt_produccion'))
                    ->columns($db->quoteName($columns))
                    ->values(implode(',', $values));
                if ($fechaf != '0000-00-00') {
                    $this->updateOrder($order_number, $status);
                }
            }
            $db->setQuery($query);
            $db->execute();
        } catch (Exception $e) {
            JFactory::getDocument()->addScriptDeclaration('
                document.addEventListener("DOMContentLoaded", function() {
                    alert("Error saving data to produccion: ' . $e->getMessage() . '");
                });
            ');
            throw new Exception('Error saving data to produccion: ' . $e->getMessage());
        }
    }

    //funcion para guardar el pago completo
    protected function saveDataToPagoCom($filePath, $order_number, $tipo_pago, $cantidad_pago, $metodo_pago, $fecha_pago, $adiccional, $status, $userId, $nombreArchivo)
    {
        try {
            $fecha_pago = $fecha_pago ? date('Y-m-d', strtotime($fecha_pago)) : '0000-00-00';
            $cantidad_pago = intval($cantidad_pago);
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $status2 = ($tipo_pago == 'Completo') ? 'Credito Liberado' : 'Pago Incompleto';
            $query->select($db->quoteName('order_number'))
                ->from($db->quoteName('josmwt_pago_order'))
                ->where($db->quoteName('order_number') . ' = ' . $db->quote($order_number));
            $db->setQuery($query);
            $exists = $db->loadResult();
            $query = $db->getQuery(true);
            if ($exists) {
                $fields = array(
                    $db->quoteName('tipo_pago') . ' = ' . $db->quote($tipo_pago),
                    $db->quoteName('cantidad_pago') . ' = ' . $db->quote($cantidad_pago),
                    $db->quoteName('metodo_pago') . ' = ' . $db->quote($metodo_pago),
                    $db->quoteName('adiccional') . ' = ' . $db->quote($adiccional),
                    $db->quoteName('fecha_pago') . ' = ' . $db->quote($fecha_pago),
                    $db->quoteName('status') . ' = ' . $db->quote($status2),
                    $db->quoteName('nombre') . ' = ' . $db->quote($nombreArchivo)
                );
                if ($filePath !== null) {
                    $fields[] = $db->quoteName('comprobante') . ' = ' . $db->quote($filePath);
                }
                $conditions = array(
                    $db->quoteName('order_number') . ' = ' . $db->quote($order_number)
                );
                $query->update($db->quoteName('josmwt_pago_order'))->set($fields)->where($conditions);
            } else {
                $columns = array('comprobante', 'order_number', 'tipo_pago', 'cantidad_pago', 'metodo_pago', 'fecha_pago', 'adiccional', 'status', 'nombre');
                $values = array($db->quote($filePath), $db->quote($order_number), $db->quote($tipo_pago), $db->quote($cantidad_pago), $db->quote($metodo_pago), $db->quote($fecha_pago), $db->quote($adiccional), $db->quote($status2), $db->quote($nombreArchivo));
                $query->insert($db->quoteName('josmwt_pago_order'))
                    ->columns($db->quoteName($columns))
                    ->values(implode(',', $values));
            }
            $db->setQuery($query);
            $db->execute();
            $query = $db->getQuery(true);
            $query->select($db->quoteName('customer'));
            $query->from($db->quoteName('josmwt_hikashop_order'));
            $query->where($db->quoteName('order_number') . ' = ' . $db->quote($order_number));
            $db->setQuery($query);
            $customer_id = $db->loadResult();
            $query->clear();
            $query->select($db->quoteName('customer_credit'));
            $query->from($db->quoteName('josmwt_customer'));
            $query->where($db->quoteName('customer_id') . ' = ' . $db->quote($customer_id));
            $db->setQuery($query);
            $customer_credit = $db->loadResult();
            $new_credit = $customer_credit + $cantidad_pago;
            $query->clear();
            $query->update($db->quoteName('josmwt_customer'));
            $query->set($db->quoteName('customer_credit') . ' = ' . $db->quote($new_credit));
            $query->where($db->quoteName('customer_id') . ' = ' . $db->quote($customer_id));
            $db->setQuery($query);
            $db->execute();
            $userEmail = $this->getUserId($userId);
            $this->sendEmail($userEmail, 'El estado de tu pedido ha cambiado', 'Tu pedido recibió un pago', $order_number);
        } catch (Exception $e) {
            throw new Exception('Error saving data to despacho: ' . $e->getMessage());
        }
    }
    //funcion para guardar el pago parcial
    protected function saveDataToPagoPar($filePath, $order_number, $tipo_pago, $tyme_credit_nuevo, $metodo_nuevo, $fechas_nuevo, $additional_info_nuevo, $status, $userId, $nombreArchivo)
    {
        try {
            $fecha_pago = $fechas_nuevo ? date('Y-m-d', strtotime($fechas_nuevo)) : '0000-00-00';
            $cantidad_pago = intval($tyme_credit_nuevo);
            $db = JFactory::getDbo();

            // Verificar si ya existe un registro con el mismo order_number
            $queryCheck = $db->getQuery(true)
                ->select($db->quoteName('id'))
                ->from($db->quoteName('josmwt_pago_order'))
                ->where($db->quoteName('order_number') . ' = ' . $db->quote($order_number));

            $db->setQuery($queryCheck);
            $existingRecord = $db->loadResult();

            if ($existingRecord) {
                // Si existe, genera un contador y concaténalo con $additional_info_nuevo
                $counter = $this->generateCounterForDuplicateOrderNumber3($order_number);
                $nombreArchivo = $nombreArchivo . '_' . $counter;
            }

            // Insertar datos en la tabla josmwt_pago_order
            $status2 = ($tipo_pago == 'Parcial') ? 'Pago Parcial' : 'Pago Completo';

            // Verificar si la cantidad total pagada excede el precio total de la orden
            if (!$this->checkPaymentStatus($order_number, $cantidad_pago)) {
                throw new Exception('La cantidad total pagada excede el precio total de la orden.');
            }

            $columns = array('comprobante', 'order_number', 'tipo_pago', 'cantidad_pago', 'metodo_pago', 'fecha_pago', 'adiccional', 'status', 'nombre');
            $values = array(
                $db->quote($filePath),
                $db->quote($order_number),
                $db->quote($tipo_pago),
                $db->quote($cantidad_pago),
                $db->quote($metodo_nuevo),
                $db->quote($fecha_pago),
                $db->quote($additional_info_nuevo),
                $db->quote($status2),
                $db->quote($nombreArchivo)
            );
            $query = $db->getQuery(true);
            $query->insert($db->quoteName('josmwt_pago_order'))
                ->columns($db->quoteName($columns))
                ->values(implode(',', $values));
            $db->setQuery($query);
            $db->execute();
            $query = $db->getQuery(true);
            $query->select($db->quoteName('customer'));
            $query->from($db->quoteName('josmwt_hikashop_order'));
            $query->where($db->quoteName('order_number') . ' = ' . $db->quote($order_number));
            $db->setQuery($query);
            $customer_id = $db->loadResult();
            $query->clear();
            $query->select($db->quoteName('customer_credit'));
            $query->from($db->quoteName('josmwt_customer'));
            $query->where($db->quoteName('customer_id') . ' = ' . $db->quote($customer_id));
            $db->setQuery($query);
            $customer_credit = $db->loadResult();
            $new_credit = $customer_credit + $cantidad_pago;
            $query->clear();
            $query->update($db->quoteName('josmwt_customer'));
            $query->set($db->quoteName('customer_credit') . ' = ' . $db->quote($new_credit));
            $query->where($db->quoteName('customer_id') . ' = ' . $db->quote($customer_id));
            $db->setQuery($query);
            $db->execute();
            $userEmail = $this->getUserId($userId);
            $this->sendEmail($userEmail, 'El estado de tu pedido ha cambiado', 'Tu pedido recibió un pago', $order_number);
        } catch (Exception $e) {
            throw new Exception('Error saving data to despacho: ' . $e->getMessage());
        }
    }

    private function generateCounterForDuplicateOrderNumber3($order_number)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('COUNT(*)')
            ->from($db->quoteName('josmwt_pago_order'))
            ->where($db->quoteName('order_number') . ' = ' . $db->quote($order_number));

        $db->setQuery($query);
        $count = $db->loadResult();

        return $count + 1;
    }

    // Función para buscar pagos parciales
    function checkPaymentStatus($order_number, $orderFullPrice)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('SUM(cantidad_pago) AS total_pago');
        $query->from($db->quoteName('josmwt_pago_order'));
        $query->where($db->quoteName('order_number') . ' = ' . $db->quote($order_number));
        $db->setQuery($query);
        $total_pago = $db->loadResult();
        if ($total_pago === null) {
            $total_pago = 0;
        }
        if ($total_pago != $orderFullPrice) {
            return false;
        } else {
            return true;
        }
    }


    //funcion para obtener la informacion despacho
    public function getdespacho($order_number)
    {
        try {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->select($db->quoteName(array('s.nomber', 's.number_guia', 's.nomber_despacho', 's.nomber_arribo', 's.adiccional', 's.fecha_arribo', 's.fechas', 'puerto_intermedio')));
            $query->from($db->quoteName('josmwt_shipping', 's'));
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
    //funcion para obtener la informacion de pago
    public function getpago($order_number)
    {
        try {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->select($db->quoteName(array('p.status', 'p.tipo_pago', 'p.cantidad_pago', 'p.metodo_pago', 'p.adiccional', 'p.fecha_pago')));
            $query->from($db->quoteName('josmwt_pago_order', 'p'));
            $query->where($db->quoteName('p.order_number') . ' = ' . $db->quote($order_number));
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
    public function getpago2($order_number)
    {
        try {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->select($db->quoteName(array('p.status', 'p.tipo_pago', 'p.cantidad_pago', 'p.metodo_pago', 'p.adiccional', 'p.fecha_pago')));
            $query->from($db->quoteName('josmwt_pago_order', 'p'));
            $query->where($db->quoteName('p.order_number') . ' = ' . $db->quote($order_number));
            $db->setQuery($query);
            $fecha = $db->loadAssocList();
            if ($fecha === null) {
                return null;
            }
            return $fecha;
        } catch (Exception $e) {
            JFactory::getApplication()->enqueueMessage(JText::_('ERROR_FETCHING_ORDER_DATA'), 'error');
            return null;
        }
    }

    //funcion para obtener la fechas de produccion
    public function getfechapro($order_number)
    {
        try {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->select($db->quoteName(array('p.fechai', 'p.fechaf')));
            $query->from($db->quoteName('josmwt_produccion', 'p'));
            $query->where($db->quoteName('p.order_number') . ' = ' . $db->quote($order_number));
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
    //funcion para obtener el operador
    public function getoperator($order_number)
    {
        try {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->select($db->quoteName(array('o.operator', 'o.details')));
            $query->from($db->quoteName('josmwt_hikashop_order', 'o'));
            $query->where($db->quoteName('o.order_number') . ' = ' . $db->quote($order_number));
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
    //funcion para obtener el Número SAP y Preforma
    public function getsap($order_number)
    {
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
    //funcion para obtener los archivos orden de compra
    public function getUploadedFiles($order_number)
    {
        try {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->select($db->quoteName('preformar'));
            $query->from($db->quoteName('josmwt_preforma', 'p'));
            $query->where($db->quoteName('order_number') . ' = :order_number');
            $query->bind(':order_number', $order_number);
            $db->setQuery($query);
            $result = $db->loadResult();
            if ($result === null) {
                return null;
            }
            return $result;
        } catch (Exception $e) {
            JFactory::getApplication()->enqueueMessage(JText::_('ERROR_FETCHING_UPLOADED_FILES'), 'error');
            JLog::add($e->getMessage(), JLog::ERROR, 'my-component');
            // Decide whether to rethrow the exception or return a default value
            // throw $e;
            return null;
        }
    }

    //funcion para obtener los archivos preforma
    public function getUploadedFiles4($order_number)
    {
        try {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->select('s.preforma');
            $query->from($db->quoteName('josmwt_sap', 's'));
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
    //funcion para obtener el nombre de los archivos packing list
    public function getUploadedFiles3($order_number)
    {
        try {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->select('p.nomb_pack');
            $query->from($db->quoteName('josmwt_pack', 'p'));
            $query->where($db->quoteName('p.order_number') . ' = ' . $db->quote($order_number));
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

    //funcion para obtener los archivos packing list y verlos
    public function getUploadedFiles2($order_number, $nomb_pack)
    {
        try {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->select('p.pack');
            $query->from($db->quoteName('josmwt_pack', 'p'));
            $query->where($db->quoteName('p.order_number') . ' = ' . $db->quote($order_number) . ' AND ' . $db->quoteName('p.nomb_pack') . ' = ' . $db->quote($nomb_pack));
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
    //funcion para obtener el Número de guia
    public function getUploadedFiles5($order_number)
    {
        try {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->select('s.number_guia');
            $query->from($db->quoteName('josmwt_shipping', 's'));
            $query->where($db->quoteName('s.order_number') . ' = ' . $db->quote($order_number));
            $db->setQuery($query);
            $result = $db->loadObjectList();

            // Check if there are no results
            if (count($result) === 0) {
                return null;
            }

            return $result;
        } catch (Exception $e) {
            JFactory::getApplication()->enqueueMessage(JText::_('ERROR_FETCHING_UPLOADED_FILES'), 'error');
            throw $e;
        }
    }

    //funcion para obtener los archivos guias y verlos
    public function getUploadedFiles6($order_number, $number_guia)
    {
        try {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->select('s.guia');
            $query->from($db->quoteName('josmwt_shipping', 's'));
            $query->where($db->quoteName('s.order_number') . ' = ' . $db->quote($order_number) . ' AND ' . $db->quoteName('s.number_guia') . ' = ' . $db->quote($number_guia));
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
    //funcion para obtener el Número de factura
    public function getUploadedFiles7($order_number)
    {
        try {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->select('i.number_invoice, i.itinerario, i.invoice, i.certificado');
            $query->from($db->quoteName('josmwt_invoice', 'i'));
            $query->where($db->quoteName('i.order_number') . ' = ' . $db->quote($order_number));
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
    //funcion para obtener las facturas
    public function getUploadedFiles8($order_number, $number_invoice)
    {
        try {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->select('i.invoice');
            $query->from($db->quoteName('josmwt_invoice', 'i'));
            $query->where($db->quoteName('i.order_number') . ' = ' . $db->quote($order_number));
            $query->where($db->quoteName('i.number_invoice') . ' = ' . $db->quote($number_invoice)); // Add the additional condition
            $db->setQuery($query);
            $result = $db->loadObjectList();  // Use loadObjectList to retrieve multiple results

            return $result;
        } catch (Exception $e) {
            JFactory::getApplication()->enqueueMessage(JText::_('ERROR_FETCHING_UPLOADED_FILES'), 'error');
            throw $e;
        }
    }


    //funcion para obtener los certificados
    public function getUploadedFiles9($order_number)
    {
        try {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->select('i.certificado');
            $query->from($db->quoteName('josmwt_invoice', 'i'));
            $query->where($db->quoteName('i.order_number') . ' = ' . $db->quote($order_number));
            $db->setQuery($query);
            $result = $db->loadObjectList();  // Use loadObjectList to retrieve multiple results

            return $result;
        } catch (Exception $e) {
            JFactory::getApplication()->enqueueMessage(JText::_('ERROR_FETCHING_UPLOADED_FILES'), 'error');
            throw $e;
        }
    }

    //funcion para obtener el packing list detallado
    public function getUploadedFiles10($order_number)
    {
        try {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->select('p.caja');
            $query->from($db->quoteName('josmwt_pack_detallado', 'p'));
            $query->where($db->quoteName('p.order_number') . ' = ' . $db->quote($order_number));
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
    //funcion para obtener los archivos packing detallados
    public function getUploadedFiles11($order_number, $caja)
    {
        try {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->select('p.pack');
            $query->from($db->quoteName('josmwt_pack_detallado', 'p'));
            $query->where($db->quoteName('p.order_number') . ' = ' . $db->quote($order_number) . ' AND ' . $db->quoteName('p.caja') . ' = ' . $db->quote($caja));
            $db->setQuery($query);
            $result = $db->loadResult() ?? null;

            return $result;
        } catch (Exception $e) {
            JFactory::getApplication()->enqueueMessage(JText::_('ERROR_FETCHING_UPLOADED_FILES'), 'error');
            throw $e;
        }
    }

    //funcion para ver comprobante de pago completo
    public function getUploadedFiles12($order_number)
    {
        try {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->select('p.comprobante');
            $query->from($db->quoteName('josmwt_pago_order', 'p'));
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
    //funciones para ver comprobante de pago parcial
    public function getUploadedFiles14($order_number, $nombre)
    {
        try {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->select('p.comprobante');
            $query->from($db->quoteName('josmwt_pago_order', 'p'));
            $query->where($db->quoteName('p.order_number') . ' = ' . $db->quote($order_number) . ' AND ' . $db->quoteName('p.nombre') . ' = ' . $db->quote($nombre));
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
    public function getUploadedFiles13($order_number)
    {
        try {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->select('p.nombre');
            $query->from($db->quoteName('josmwt_pago_order', 'p'));
            $query->where($db->quoteName('order_number') . ' = ' . $db->quote($order_number));
            $db->setQuery($query);
            $result = $db->loadObjectList();
            if (empty($result)) {
                return null;
            }
            return $result;
        } catch (Exception $e) {
            JFactory::getApplication()->enqueueMessage(JText::_('ERROR_FETCHING_UPLOADED_FILES'), 'error');
            throw $e;
        }
    }
    //funcion para obtener la cotizacion de envio
    public function getUploadedFiles15($order_number)
    {
        try {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->select('p.order_number');
            $query->from($db->quoteName('josmwt_cotizacion', 'p'));
            $query->where($db->quoteName('p.order_number') . ' = ' . $db->quote($order_number));
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
    //funcion para obtener los archivos cotizacion envio
    public function getUploadedFiles16($order_number)
    {
        try {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->select('p.cotizacion');
            $query->from($db->quoteName('josmwt_cotizacion', 'p'));
            $query->where($db->quoteName('p.order_number') . ' = ' . $db->quote($order_number));
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
    //fin
    //funcion para eliminar los archivos ordenes de compra
    public function deleteUploadedFiles($order_number)
    {
        $filePathRelative = $this->getUploadedFiles($order_number);
        $status = 'confirmed';
        if ($filePathRelative !== null) {
            $filePathFull = $_SERVER['DOCUMENT_ROOT'] . $filePathRelative;
            if (file_exists($filePathFull)) {
                if (unlink($filePathFull)) {
                    JFactory::getApplication()->enqueueMessage(JText::_('ARCHIVO_ELIMINADO_CORRECTAMENTE'), 'message');
                    $db = JFactory::getDbo();
                    $query = $db->getQuery(true);
                    $conditions = array(
                        $db->quoteName('order_number') . ' = ' . $db->quote($order_number)
                    );
                    $query->delete($db->quoteName('josmwt_preforma'));
                    $query->where($conditions);
                    $db->setQuery($query);
                    $result = $db->execute();
                    if ($result) {
                        $this->updateOrder($order_number, $status);
                        JFactory::getApplication()->enqueueMessage(JText::_('REGISTRO_ELIMINADO_CORRECTAMENTE'), 'message');
                    } else {
                        JFactory::getApplication()->enqueueMessage(JText::_('ERROR_AL_ELIMINAR_REGISTRO'), 'error');
                    }
                } else {
                    JFactory::getApplication()->enqueueMessage(JText::_('ERROR_AL_ELIMINAR_ARCHIVO'), 'error');
                }
            } else {
                JFactory::getApplication()->enqueueMessage(JText::_('ARCHIVO_NO_ENCONTRADO'), 'error');
            }
        } else {
            JFactory::getApplication()->enqueueMessage(JText::_('ARCHIVO_NO_EXISTE'), 'error');
        }
    }
    //funcion para eliminar los packing list
    public function deleteUploadedFiles2($order_number, $pack_listing)
    {
        $filePathRelative = $this->getUploadedFiles2($order_number, $pack_listing);
        $status = 'transito';
        if ($filePathRelative !== null) {
            $filePathFull = $_SERVER['DOCUMENT_ROOT'] . $filePathRelative;
            if (file_exists($filePathFull)) {
                if (unlink($filePathFull)) {
                    JFactory::getApplication()->enqueueMessage(JText::_('ARCHIVO_ELIMINADO_CORRECTAMENTE'), 'message');
                    $db = JFactory::getDbo();
                    $query = $db->getQuery(true);
                    $conditions = array(
                        $db->quoteName('order_number') . ' = ' . $db->quote($order_number),
                        $db->quoteName('nomb_pack') . ' = ' . $db->quote($pack_listing)
                    );
                    $query->delete($db->quoteName('josmwt_pack'));
                    $query->where($conditions);
                    $db->setQuery($query);
                    $result = $db->execute();
                    if ($result) {
                        $this->updateOrder($order_number, $status);
                        JFactory::getApplication()->enqueueMessage(JText::_('REGISTRO_ELIMINADO_CORRECTAMENTE'), 'message');
                    } else {
                        JFactory::getApplication()->enqueueMessage(JText::_('ERROR_AL_ELIMINAR_REGISTRO'), 'error');
                    }
                } else {
                    JFactory::getApplication()->enqueueMessage(JText::_('ERROR_AL_ELIMINAR_ARCHIVO'), 'error');
                }
            } else {
                JFactory::getApplication()->enqueueMessage(JText::_('ARCHIVO_NO_ENCONTRADO'), 'error');
            }
        } else {
            JFactory::getApplication()->enqueueMessage(JText::_('ARCHIVO_NO_EXISTE'), 'error');
        }
    }
    //funcion para eliminar los archivos preforma
    public function deleteUploadedFiles3($order_number)
    {
        $filePathRelative = $this->getUploadedFiles4($order_number);
        $status = 'produccion';
        if ($filePathRelative !== null) {
            $filePathFull = $_SERVER['DOCUMENT_ROOT'] . $filePathRelative;
            if (file_exists($filePathFull)) {
                if (!unlink($filePathFull)) {
                    JFactory::getApplication()->enqueueMessage(JText::_('ERROR_AL_ELIMINAR_ARCHIVO'), 'error');
                }
            } else {
                JFactory::getApplication()->enqueueMessage(JText::_('ARCHIVO_NO_ENCONTRADO'), 'error');
            }
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $conditions = array(
                $db->quoteName('order_number') . ' = ' . $db->quote($order_number)
            );
            $query->delete($db->quoteName('josmwt_sap'));
            $query->where($conditions);
            $db->setQuery($query);
            $result = $db->execute();
            if ($result) {
                $this->updateOrder($order_number, $status);
                JFactory::getApplication()->enqueueMessage(JText::_('REGISTRO_ELIMINADO_CORRECTAMENTE'), 'message');
            } else {
                JFactory::getApplication()->enqueueMessage(JText::_('ERROR_AL_ELIMINAR_REGISTRO'), 'error');
            }
        } else {
            JFactory::getApplication()->enqueueMessage(JText::_('ARCHIVO_NO_EXISTE'), 'error');
        }
    }
    //funcion para eliminar la guia
    public function deleteUploadedFiles4($order_number, $number_guia)
    {
        $filePathRelative = $this->getUploadedFiles6($order_number, $number_guia);
        $status = 'despacho';

        if ($filePathRelative !== null) {
            $filePathFull = $_SERVER['DOCUMENT_ROOT'] . $filePathRelative;

            if (file_exists($filePathFull)) {
                if (!unlink($filePathFull)) {
                    JFactory::getApplication()->enqueueMessage(JText::_('ERROR_AL_ELIMINAR_ARCHIVO'), 'error');
                }
            } else {
                JFactory::getApplication()->enqueueMessage(JText::_('ARCHIVO_NO_ENCONTRADO'), 'error');
            }

            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $conditions = array(
                $db->quoteName('order_number') . ' = ' . $db->quote($order_number),
                $db->quoteName('number_guia') . ' = ' . $db->quote($number_guia)
            );
            $query->delete($db->quoteName('josmwt_shipping'))->where($conditions);
            $db->setQuery($query);
            $result = $db->execute();

            if ($result) {
                $this->updateOrder($order_number, $status);
                JFactory::getApplication()->enqueueMessage(JText::_('REGISTRO_ELIMINADO_CORRECTAMENTE'), 'message');
            } else {
                JFactory::getApplication()->enqueueMessage(JText::_('ERROR_AL_ELIMINAR_REGISTRO'), 'error');
            }
        } else {
            JFactory::getApplication()->enqueueMessage(JText::_('ARCHIVO_NO_EXISTE'), 'error');
        }
    }



    //funcion para eliminar la factura
    public function deleteUploadedFiles5($order_number, $number_invoice)
    {
        $filePathRelative = $this->getUploadedFiles8($order_number, $number_invoice);
        $status = 'despacho';
        if ($filePathRelative !== null) {
            $filePathFull = $_SERVER['DOCUMENT_ROOT'] . $filePathRelative;
            if (file_exists($filePathFull)) {
                if (!unlink($filePathFull)) {
                    JFactory::getApplication()->enqueueMessage(JText::_('ERROR_AL_ELIMINAR_ARCHIVO'), 'error');
                }
            } else {
                JFactory::getApplication()->enqueueMessage(JText::_('ARCHIVO_NO_ENCONTRADO'), 'error');
            }
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $fields = array(
                $db->quoteName('invoice') . ' = NULL',
                $db->quoteName('number_invoice') . ' = NULL'
            );

            $conditions = array(
                $db->quoteName('order_number') . ' = ' . $db->quote($order_number),
                $db->quoteName('number_invoice') . ' = ' . $db->quote($number_invoice)
            );

            $query->update($db->quoteName('josmwt_invoice'))->set($fields)->where($conditions);
            $db->setQuery($query);
            $result = $db->execute();
            if ($result) {
                $this->updateOrder($order_number, $status);
                JFactory::getApplication()->enqueueMessage(JText::_('REGISTRO_ACTUALIZADO_CORRECTAMENTE'), 'message');
            } else {
                JFactory::getApplication()->enqueueMessage(JText::_('ERROR_AL_ACTUALIZAR_REGISTRO'), 'error');
            }
        } else {
            JFactory::getApplication()->enqueueMessage(JText::_('ARCHIVO_NO_EXISTE'), 'error');
        }
    }
    //funcion para eliminar los packing list detallado
    public function deleteUploadedFiles7($order_number, $caja)
    {
        $filePathRelative = $this->getUploadedFiles11($order_number, $caja);
        $status = 'preparacion';
        if ($filePathRelative !== null) {
            $filePathFull = $_SERVER['DOCUMENT_ROOT'] . $filePathRelative;

            if (file_exists($filePathFull)) {
                if (unlink($filePathFull)) {
                    JFactory::getApplication()->enqueueMessage(JText::_('ARCHIVO_ELIMINADO_CORRECTAMENTE'), 'message');
                    $db = JFactory::getDbo();
                    $query = $db->getQuery(true);
                    $conditions = array(
                        $db->quoteName('order_number') . ' = ' . $db->quote($order_number),
                        $db->quoteName('caja') . ' = ' . $db->quote($caja)
                    );

                    $query->delete($db->quoteName('josmwt_pack_detallado'));
                    $query->where($conditions);
                    $db->setQuery($query);

                    $result = $db->execute();

                    if ($result) {
                        $this->updateOrder($order_number, $status);
                        JFactory::getApplication()->enqueueMessage(JText::_('REGISTRO_ELIMINADO_CORRECTAMENTE'), 'message');
                    } else {
                        JFactory::getApplication()->enqueueMessage(JText::_('ERROR_AL_ELIMINAR_REGISTRO'), 'error');
                    }
                } else {
                    JFactory::getApplication()->enqueueMessage(JText::_('ERROR_AL_ELIMINAR_ARCHIVO'), 'error');
                }
            } else {
                JFactory::getApplication()->enqueueMessage(JText::_('ARCHIVO_NO_ENCONTRADO'), 'error');
            }
        } else {
            JFactory::getApplication()->enqueueMessage(JText::_('ARCHIVO_NO_EXISTE'), 'error');
        }
    }
    //funcion para eliminar los certificados
    public function deleteUploadedFiles6($order_number)
    {
        $filePathRelative = $this->getUploadedFiles9($order_number);
        $status = 'despacho';
        if ($filePathRelative !== null) {
            $filePathFull = $_SERVER['DOCUMENT_ROOT'] . $filePathRelative;
            if (file_exists($filePathFull)) {
                if (!unlink($filePathFull)) {
                    JFactory::getApplication()->enqueueMessage(JText::_('ERROR_AL_ELIMINAR_ARCHIVO'), 'error');
                }
            } else {
                JFactory::getApplication()->enqueueMessage(JText::_('ARCHIVO_NO_ENCONTRADO'), 'error');
            }
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $fields = array(
                $db->quoteName('certificado') . ' = NULL'
            );
            $conditions = array(
                $db->quoteName('order_number') . ' = ' . $db->quote($order_number)
            );
            $query->update($db->quoteName('josmwt_invoice'))->set($fields)->where($conditions);
            $db->setQuery($query);
            $result = $db->execute();
            if ($result) {
                $this->updateOrder($order_number, $status);
                JFactory::getApplication()->enqueueMessage(JText::_('REGISTRO_ACTUALIZADO_CORRECTAMENTE'), 'message');
            } else {
                JFactory::getApplication()->enqueueMessage(JText::_('ERROR_AL_ACTUALIZAR_REGISTRO'), 'error');
            }
        } else {
            JFactory::getApplication()->enqueueMessage(JText::_('ARCHIVO_NO_EXISTE'), 'error');
        }
    }
    //funcion para elminar cotizacion
    public function deleteUploadedFiles10($order_number)
    {
        $filePathRelative = $this->getUploadedFiles16($order_number);
        $status = 'preparacion';
        if ($filePathRelative !== null) {
            $filePathFull = $_SERVER['DOCUMENT_ROOT'] . $filePathRelative;
            if (file_exists($filePathFull)) {
                if (unlink($filePathFull)) {
                    JFactory::getApplication()->enqueueMessage(JText::_('ARCHIVO_ELIMINADO_CORRECTAMENTE'), 'message');
                    $db = JFactory::getDbo();
                    $query = $db->getQuery(true);
                    $conditions = array(
                        $db->quoteName('order_number') . ' = ' . $db->quote($order_number)
                    );
                    $query->delete($db->quoteName('josmwt_cotizacion'));
                    $query->where($conditions);
                    $db->setQuery($query);

                    $result = $db->execute();

                    if ($result) {
                        $this->updateOrder($order_number, $status);
                        JFactory::getApplication()->enqueueMessage(JText::_('REGISTRO_ELIMINADO_CORRECTAMENTE'), 'message');
                    } else {
                        JFactory::getApplication()->enqueueMessage(JText::_('ERROR_AL_ELIMINAR_REGISTRO'), 'error');
                    }
                } else {
                    JFactory::getApplication()->enqueueMessage(JText::_('ERROR_AL_ELIMINAR_ARCHIVO'), 'error');
                }
            } else {
                JFactory::getApplication()->enqueueMessage(JText::_('ARCHIVO_NO_ENCONTRADO'), 'error');
            }
        } else {
            JFactory::getApplication()->enqueueMessage(JText::_('ARCHIVO_NO_EXISTE'), 'error');
        }
    }
    //funcion para eliminar el comprobante de pago completo
    public function deleteUploadedFiles8($order_number, $cantidad_pago)
    {
        $filePathRelative = $this->getUploadedFiles12($order_number);
        $status = 'pagado';
        if ($filePathRelative !== null) {
            $filePathFull = $_SERVER['DOCUMENT_ROOT'] . $filePathRelative;

            if (file_exists($filePathFull)) {
                if (!unlink($filePathFull)) {
                    JFactory::getApplication()->enqueueMessage(JText::_('ERROR_AL_ELIMINAR_ARCHIVO'), 'error');
                }
            } else {
                JFactory::getApplication()->enqueueMessage(JText::_('ARCHIVO_NO_ENCONTRADO'), 'error');
            }
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $conditions = array(
                $db->quoteName('order_number') . ' = ' . $db->quote($order_number),
            );
            $query->delete($db->quoteName('josmwt_pago_order'))->where($conditions);

            $db->setQuery($query);
            $result = $db->execute();
            $query = $db->getQuery(true);
            $query->select($db->quoteName('customer'));
            $query->from($db->quoteName('josmwt_hikashop_order'));
            $query->where($db->quoteName('order_number') . ' = ' . $db->quote($order_number));
            $db->setQuery($query);
            $customer_id = $db->loadResult();
            $query->clear();
            $query->select($db->quoteName('customer_credit'));
            $query->from($db->quoteName('josmwt_customer'));
            $query->where($db->quoteName('customer_id') . ' = ' . $db->quote($customer_id));
            $db->setQuery($query);
            $customer_credit = $db->loadResult();
            $new_credit =  $customer_credit - $cantidad_pago;
            $query->clear();
            $query->update($db->quoteName('josmwt_customer'));
            $query->set($db->quoteName('customer_credit') . ' = ' . $db->quote($new_credit));
            $query->where($db->quoteName('customer_id') . ' = ' . $db->quote($customer_id));
            $db->setQuery($query);
            $db->execute();
            if ($result) {
                $this->updateOrder($order_number, $status);
                JFactory::getApplication()->enqueueMessage(JText::_('REGISTRO_ELIMINADO_CORRECTAMENTE'), 'message');
            } else {
                JFactory::getApplication()->enqueueMessage(JText::_('ERROR_AL_ELIMINAR_REGISTRO'), 'error');
            }
        } else {
            JFactory::getApplication()->enqueueMessage(JText::_('ARCHIVO_NO_EXISTE'), 'error');
        }
    }

    //funcion para eliminar el comprobante de pago parcial
    public function deleteUploadedFiles9($order_number, $nombre)
    {
        $filePathRelative = $this->getUploadedFiles14($order_number, $nombre);
        $status = 'pagado';

        if ($filePathRelative !== null) {
            $filePathFull = $_SERVER['DOCUMENT_ROOT'] . $filePathRelative;

            if (file_exists($filePathFull)) {
                if (!unlink($filePathFull)) {
                    JFactory::getApplication()->enqueueMessage(JText::_('ERROR_AL_ELIMINAR_ARCHIVO'), 'error');
                    return; // Detener la ejecución si no se pudo eliminar el archivo
                }
            } else {
                JFactory::getApplication()->enqueueMessage(JText::_('ARCHIVO_NO_ENCONTRADO'), 'error');
            }


            $this->deleteComprobanteRecord($order_number, $nombre);

            JFactory::getApplication()->enqueueMessage(JText::_('ARCHIVO_ELIMINADO_CORRECTAMENTE'), 'message');
        } else {
            JFactory::getApplication()->enqueueMessage(JText::_('ARCHIVO_NO_EXISTE'), 'error');
        }
    }

    protected function deleteComprobanteRecord($order_number, $nombre)
    {
        try {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->select($db->quoteName('cantidad_pago'));
            $query->from($db->quoteName('josmwt_pago_order'));
            $query->where($db->quoteName('order_number') . ' = ' . $db->quote($order_number));
            $query->where($db->quoteName('nombre') . ' = ' . $db->quote($nombre));
            $db->setQuery($query);
            $cantidad_pago = $db->loadResult();
            $query->clear();
            $query->select($db->quoteName('customer'));
            $query->from($db->quoteName('josmwt_hikashop_order'));
            $query->where($db->quoteName('order_number') . ' = ' . $db->quote($order_number));
            $db->setQuery($query);
            $customer_id = $db->loadResult();
            $query->clear();
            $query->select($db->quoteName('customer_credit'));
            $query->from($db->quoteName('josmwt_customer'));
            $query->where($db->quoteName('customer_id') . ' = ' . $db->quote($customer_id));
            $db->setQuery($query);
            $customer_credit = $db->loadResult();
            $new_credit =   $customer_credit - $cantidad_pago;
            $query->clear();
            $query->update($db->quoteName('josmwt_customer'));
            $query->set($db->quoteName('customer_credit') . ' = ' . $db->quote($new_credit));
            $query->where($db->quoteName('customer_id') . ' = ' . $db->quote($customer_id));
            $db->setQuery($query);
            $db->execute();
            $query->clear();
            $query->delete($db->quoteName('josmwt_pago_order'));
            $query->where($db->quoteName('order_number') . ' = ' . $db->quote($order_number));
            $query->where($db->quoteName('nombre') . ' = ' . $db->quote($nombre));
            $db->setQuery($query);
            $db->execute();
        } catch (Exception $e) {
            JFactory::getApplication()->enqueueMessage(JText::_('ERROR_ELIMINAR_COMPROBANTE'), 'error');
            throw $e;
        }
    }


    //fin    

    //funcion para agregar el Método de Envío al pedido
    public function actualizarOrden($operator, $details, $order_number, $shipping_id, $shipping_name, $shipping_price, $address_id, $status, $userId, $selectedNombre, $selectedCode)
    {
        try {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $fields = array(
                $db->quoteName('order_billing_address_id') . ' = ' . (int)$address_id,
                $db->quoteName('order_shipping_id') . ' = ' . (int)$shipping_id,
                $db->quoteName('order_shipping_method') . ' = ' . $db->quote($shipping_name),
                $db->quoteName('operator') . ' = ' . $db->quote($operator),
                $db->quoteName('details') . ' = ' . $db->quote($details),
                $db->quoteName('incoterms') . ' = ' . $db->quote($selectedNombre),
                $db->quoteName('Code_incoterms') . ' = ' . $db->quote($selectedCode),
                $db->quoteName('order_shipping_price') . ' = ' . (int)$shipping_price
            );

            $conditions = array(
                $db->quoteName('order_number') . ' = ' . $db->quote($order_number)
            );
            $query->update($db->quoteName('josmwt_hikashop_order'))->set($fields)->where($conditions);
            $db->setQuery($query);
            $db->execute();
            $this->updateOrder($order_number, $status);
            $userEmail = $this->getUserId($userId);
            $this->sendEmail($userEmail, 'El estado de tu pedido ha cambiado', 'Tu pedido ahora está en despacho.', $order_number);
            JFactory::getApplication()->enqueueMessage(JText::_('SUCCESSFUL_ORDER_UPDATE'), 'success');
            return true;
        } catch (Exception $e) {
            JFactory::getApplication()->enqueueMessage(JText::_('ERROR_FETCHING_UPDATE_ORDER') . ': ' . $e->getMessage(), 'error');
            return false;
        }
    }
    public function updateCustomerOrder($order_number, $customer_id)
    {
        try {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $fields = array(
                $db->quoteName('customer') . ' = ' . (int)$customer_id
            );

            $conditions = array(
                $db->quoteName('order_number') . ' = ' . $db->quote($order_number)
            );
            $query->update($db->quoteName('josmwt_hikashop_order'))->set($fields)->where($conditions);
            $db->setQuery($query);
            $db->execute();
        } catch (Exception $e) {
            JFactory::getApplication()->enqueueMessage(JText::_('ERROR_FETCHING_UPDATE_ORDER') . ': ' . $e->getMessage(), 'error');
            return false;
        }
    }

    //funcion para cambiar el estado  del pedido en automatico
    public function updateOrder($order_number, $status)
    {
        try {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $fields = array(
                $db->quoteName('order_status') . ' = ' . $db->quote($status)
            );
            $conditions = array(
                $db->quoteName('order_number') . ' = ' . $db->quote($order_number)
            );
            $query->update($db->quoteName('josmwt_hikashop_order'))->set($fields)->where($conditions);
            $db->setQuery($query);
            $db->execute();

            JFactory::getApplication()->enqueueMessage(JText::_('SUCCESSFUL_ORDER_UPDATE'), 'success');
            return true;  // La actualización fue exitosa
        } catch (Exception $e) {
            JFactory::getApplication()->enqueueMessage(JText::_('ERROR_FETCHING_UPDATE_ORDER') . ': ' . $e->getMessage(), 'error');
            return false;  // Indica que hubo un error
        }
    }
    //funcion para obtener el estado en el que esta el pedido
    public function getStatus($order_number)
    {
        try {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->select($db->quoteName('o.order_status'));
            $query->from($db->quoteName('josmwt_hikashop_order', 'o'));
            $query->where($db->quoteName('o.order_number') . ' = ' . $db->quote($order_number));
            $db->setQuery($query);
            $shippingMethod = $db->loadResult();
            if ($shippingMethod === null) {
                throw new Exception($db->getErrorMsg());
            }
            return $shippingMethod;
        } catch (Exception $e) {
            JFactory::getApplication()->enqueueMessage(JText::_('ERROR_FETCHING_ORDER_DATA'), 'error');
            return null;
        }
    }
    //funcion para obtener el Número de orden de compra
    public function getNumber($order_number)
    {
        try {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->select($db->quoteName('o.number_purchase'));
            $query->from($db->quoteName('josmwt_preforma', 'o'));
            $query->where($db->quoteName('o.order_number') . ' = ' . $db->quote($order_number));
            $db->setQuery($query);
            $shippingMethod = $db->loadResult();
            if ($shippingMethod === null) {
                return null;
            }
            return $shippingMethod;
        } catch (Exception $e) {
            JFactory::getApplication()->enqueueMessage(JText::_('ERROR_FETCHING_ORDER_DATA'), 'error');
            return null;
        }
    }
    //funcion para obtener el userid de hikashop
    public function getUserId($userId)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select($db->quoteName('user_email'));
        $query->from($db->quoteName('josmwt_hikashop_user'));
        $query->where($db->quoteName('user_cms_id') . ' = ' . $db->quote($userId));
        $db->setQuery($query);
        $result = $db->loadAssoc();
        return $result;
    }
    //funcion para enviar email
    public function sendEmail($to, $subject, $body, $orderNumber)
    {
        try {
            $mail = JFactory::getMailer();
            $config = JFactory::getConfig();
            $sender = array(
                $config->get('mailfrom'),
                $config->get('fromname')
            );
            $to3 = 'alejandro@muitowork.com';
            $to2 = 'alvaro@muitowork.com';
            $mail->setSender($sender);
            $mail->addRecipient($to);
            $mail->addRecipient($to2);
            $mail->addRecipient($to3);
            $subject .= ' - ' . htmlspecialchars($orderNumber);
            $mail->setSubject($subject);
            $body .= '<br><br>Order Number: ' . htmlspecialchars($orderNumber);
            $body .= '<br>Order Link: <a href="https://mwt.one/index.php/en/?option=com_sppagebuilder&view=page&id=18&order_number=' . htmlspecialchars($orderNumber) . '">View Order</a>';
            $mail->setBody($body);
            $mail->isHtml(true);
            $mail->send();
        } catch (Exception $e) {
            JFactory::getDocument()->addScriptDeclaration('
                    document.addEventListener("DOMContentLoaded", function() {
                        alert("Error sending email: ' . $e->getMessage() . '");
                    });
                ');
            throw new Exception('Error sending email: ' . $e->getMessage());
        }
    }


    //funcion para obtener el Número del pedido
    public function getOrder($order_number)
    {
        try {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->select($db->quoteName('o.order_full_price'));
            $query->from($db->quoteName('josmwt_hikashop_order', 'o'));
            $query->where($db->quoteName('o.order_number') . ' = ' . $db->quote($order_number));
            $db->setQuery($query);
            $results = $db->loadObjectList();
            if ($results === null) {
                throw new Exception($db->getErrorMsg());
            }
            return $results;
        } catch (Exception $e) {
            JFactory::getApplication()->enqueueMessage(JText::_('ERROR_FETCHING_ORDER_DATA'), 'error');
            return [];
        }
    }
    //funcion para obtener el tipo de envio registrada en el pedido
    public function getShipping($order_number)
    {
        try {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->select($db->quoteName('o.order_shipping_method'));
            $query->from($db->quoteName('josmwt_hikashop_order', 'o'));
            $query->where($db->quoteName('o.order_number') . ' = ' . $db->quote($order_number));
            $db->setQuery($query);
            $shippingMethod = $db->loadResult();
            if ($shippingMethod === null) {
                throw new Exception($db->getErrorMsg());
            }
            return $shippingMethod;
        } catch (Exception $e) {
            JFactory::getApplication()->enqueueMessage(JText::_('ERROR_FETCHING_ORDER_DATA'), 'error');
            return null;
        }
    }
    //funcion para obtener la direccion de envio registrada en hikashop
    public function getAddres($userId)
    {
        try {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->select('a.address_id, a.address_firstname, a.address_lastname, a.address_telephone, a.address_country, a.address_state, a.address_city, a.address_street, a.address_post_code');
            $query->from($db->quoteName('josmwt_hikashop_address', 'a'));
            $query->innerJoin($db->quoteName('josmwt_hikashop_user', 'u') . ' ON a.address_user_id = u.user_id');
            $query->where('u.user_cms_id = ' . $db->quote($userId));
            $db->setQuery($query);
            $results = $db->loadObjectList();
            if ($results === null) {
                JError::raiseWarning(500, 'Error fetching address data: ' . $db->getErrorMsg());
                return [];
            }
            return $results;
        } catch (Exception $e) {
            JError::raiseWarning(500, 'Error fetching address data: ' . $e->getMessage());
            return [];
        }
    }
    //funcion para obtener los metodos de envio asociados al usuario
    public function getShippingMethods($userId)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select($db->quoteName(array('s.shipping_id', 's.shipping_name', 's.shipping_price')));
        $query->from($db->quoteName('josmwt_hikashop_shipping', 's'));
        $db->setQuery($query);
        $results = $db->loadObjectList();
        return $results;
    }
    //funcion para obtener los metodos de envio registrados en el pedido
    public function getmethodoshipping($order_number)
    {
        try {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->select($db->quoteName(array('o.order_billing_address_id', 'o.Incoterms', 'o.Code_incoterms', 'o.order_shipping_id', 'o.order_shipping_method', 'o.order_shipping_price', 'a.address_id', 'a.address_firstname', 'a.address_lastname', 'a.address_telephone', 'a.address_country', 'a.address_state', 'a.address_city', 'a.address_street', 'a.address_post_code', 'o.order_shipping_price')));
            $query->from($db->quoteName('josmwt_hikashop_order', 'o'));
            $query->join('LEFT', $db->quoteName('josmwt_hikashop_address', 'a') . ' ON (' . $db->quoteName('o.order_billing_address_id') . ' = ' . $db->quoteName('a.address_id') . ')');
            $query->where($db->quoteName('o.order_number') . ' = ' . $db->quote($order_number));
            $db->setQuery($query);
            $methodshipping = $db->loadAssoc();

            if ($methodshipping === null) {
                throw new Exception($db->getErrorMsg());
            }

            return $methodshipping;
        } catch (Exception $e) {
            JFactory::getApplication()->enqueueMessage(JText::_('ERROR_FETCHING_ORDER_DATA'), 'error');
            return null;
        }
    }
    //funcion para obtener los incoterms            
    public function getincoterms($selectedMethod)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        // Construir la consulta
        $query->select($db->quoteName(array('Code', 'Nombre', 'descripcionES')))
            ->from($db->quoteName('josmwt_incoterms'))
            ->where($db->quoteName('metodo') . ' = ' . $db->quote($selectedMethod) . ' OR ' . $db->quoteName('metodo2') . ' = ' . $db->quote($selectedMethod));

        $db->setQuery($query);
        $results = $db->loadAssocList();
        return $results;
    }



    public function isSameCustomer($order_number, $userId)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $customers = $this->getCustomers($order_number);
        if (empty($customers)) {
            JFactory::getApplication()->enqueueMessage('No customers found for order_number: ' . $order_number, 'error');
            return false;
        }
        $customerUsers = $this->getCustomerUser($userId);
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

    //función para obtener los dias de credito del cliente
    public function getCustomers($order_number)
    {
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
    public function getCustomerUser($userId)
    {
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

    //funcion para comprar credito con valor del pedido
    public function compareCreditAndPrice($order_number)
    {
        $db = JFactory::getDbo();
        try {
            $query = $db->getQuery(true);
            $query->select($db->quoteName(array('order_full_price', 'order_shipping_price', 'customer')));
            $query->from($db->quoteName('josmwt_hikashop_order'));
            $query->where($db->quoteName('order_number') . ' = ' . $db->quote($order_number));
            $db->setQuery($query);
            $orderInfo = $db->loadAssoc();
            $order_price = $orderInfo['order_full_price'];
            $customer_id = $orderInfo['customer'];
            $query = $db->getQuery(true);
            $query->select($db->quoteName('customer_credit'));
            $query->from($db->quoteName('josmwt_customer'));
            $query->where($db->quoteName('customer_id') . ' = ' . $db->quote($customer_id));
            $db->setQuery($query);
            $customer_credit = $db->loadResult();
            if ($order_price <= $customer_credit) {
                return;
            } else {
                return "Este pedido en este momento excede el límite de crédito. Por favor, prosiga el proceso y consulte con su agente";
            }
        } catch (Exception $e) {
            return "Error en la base de datos: " . $e->getMessage();
        }
    }

    public function updateCustomerCredit($order_number, $status)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select($db->quoteName(array('order_full_price', 'customer')));
        $query->from($db->quoteName('josmwt_hikashop_order'));
        $query->where($db->quoteName('order_number') . ' = ' . $db->quote($order_number));
        $db->setQuery($query);
        $result = $db->loadAssoc();
        $order_price = $result['order_full_price'];
        $customer_id = $result['customer'];
        $query->clear();
        $query->select($db->quoteName('customer_credit'));
        $query->from($db->quoteName('josmwt_customer'));
        $query->where($db->quoteName('customer_id') . ' = ' . $db->quote($customer_id));
        $db->setQuery($query);
        $customer_credit = $db->loadResult();
        $new_credit = $customer_credit - $order_price;
        $query->clear();
        $query->update($db->quoteName('josmwt_customer'));
        $query->set($db->quoteName('customer_credit') . ' = ' . $db->quote($new_credit));
        $query->where($db->quoteName('customer_id') . ' = ' . $db->quote($customer_id));
        $db->setQuery($query);
        try {
            $db->execute();
            JFactory::getApplication()->enqueueMessage(JText::_('SUCCESSFUL_ORDER_UPDATE'), 'success');
            $this->updateOrder($order_number, $status);
        } catch (Exception $e) {
            JFactory::getApplication()->enqueueMessage(JText::_('ERROR_ORDER_UPDATE') . ': ' . $e->getMessage(), 'error');
        }
    }
    /*public function updateCustomerCredit2($order_number, $status, $nombreArchivo) {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->select($db->quoteName('customer'));
            $query->from($db->quoteName('josmwt_hikashop_order'));
            $query->where($db->quoteName('order_number') . ' = ' . $db->quote($order_number));
            $db->setQuery($query);
            $customer_id = loadResult();
            $query->clear();
            $query->select($db->quoteName('customer_credit'));
            $query->from($db->quoteName('josmwt_customer'));
            $query->where($db->quoteName('customer_id') . ' = ' . $db->quote($customer_id));
            $db->setQuery($query);
            $customer_credit = $db->loadResult();
            $new_credit = $customer_credit + $cantidad_pago;
            $query->clear();
            $query->update($db->quoteName('josmwt_customer'));
            $query->set($db->quoteName('customer_credit') . ' = ' . $db->quote($new_credit));
            $query->where($db->quoteName('customer_id') . ' = ' . $db->quote($customer_id));
            $db->setQuery($query);
            try {
                $db->execute();
                JFactory::getApplication()->enqueueMessage(JText::_('SUCCESSFUL_ORDER_UPDATE'), 'success');
                $this->updateOrder($order_number, $status);
            } catch (Exception $e) {
                JFactory::getApplication()->enqueueMessage(JText::_('ERROR_ORDER_UPDATE') . ': ' . $e->getMessage(), 'error');
            }
        }*/

    public function obtenerTituloPorUserId($userId)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('ug.title')
            ->from('josmwt_user_usergroup_map AS uum')
            ->innerJoin('josmwt_usergroups AS ug ON uum.group_id = ug.id')
            ->where('uum.user_id = ' . (int) $userId)
            ->where('ug.title = ' . $db->quote('Administrator'));

        $db->setQuery($query);
        $titulo = $db->loadResult();
        return $titulo;
    }
    public function css()
    {
        $addon_id = '#sppb-addon-' . $this->addon->id;
        $cssHelper = new CSSHelper($addon_id);
        $css = '';
        $settings = $this->addon->settings;
        $settings->alignment = CSSHelper::parseAlignment($settings, 'alignment');
        $alignment = $cssHelper->generateStyle('.sppb-addon.sppb-addon-rich_text', $settings, ['alignment' => 'text-align'], false);
        $css .= $alignment;
        $css .= '
                ' . $addon_id . ' {
                    color: black; /* Color de texto negro */
                    font-family: "Work Sans", sans-serif;
                }
            ';
        $css .= '
            ' . $addon_id . ' .sppb-addon {
                max-width: 1280px;
                margin: auto;
                padding: 20px;
                border-radius: 10px;
                box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
                background-color: #ffffff; 
                transition: all 0.3s ease-in-out; 
                text-align: center;
            }
            ' . $addon_id . ' .sppb-addon:hover {
                box-shadow: 0 0 30px rgba(0, 0, 0, 0.2);
            }
            
            ';
        $css .= '
            ' . $addon_id . ' .sppb-addon input[type="text"],
            ' . $addon_id . ' .sppb-addon select,
            ' . $addon_id . ' .sppb-addon input[type="date"] {
                text-align: center; 
            }
            ';


        $css .= "
                .tab {
                    position: relative;
                    background-color: #f0f0f0; /* Color de fondo de las pestañas */
                    border-radius: 20px 20px 0 0; /* Bordes redondeados en la parte superior */
                    padding: 10px 40px; /* Espacio interno */
                    cursor: pointer;
                    transition: background-color 0.3s ease;
                }

                .tab.active {
                    background-color: #e0e0e0; /* Color de fondo cuando está activa */
                }

                /* Colores de las pestañas */
                .blue-tab {
                    background-color: #cfe2ff; /* Azul */
                }

                .green-tab {
                    background-color: #d3f4d2; /* Verde */
                }

                .purple-tab {
                    background-color: #e9d8fd; /* Purpura */
                }

                .pink-tab {
                    background-color: #fdd0e9; /* Rosado */
                }

                .orange-tab {
                    background-color: #ffd8b2; /* Naranja */
                }

                .yellow-tab {
                    background-color: #fff2a9; /* Amarillo */
                }
                .tab:before {
                    content: '';
                    position: absolute;
                    bottom: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-bottom: 10px solid #f0f0f0; 
                }

                .tab.active:before {
                    border-bottom-color: #e0e0e0; 
                }
                .tab.active3 {
                    border: 2px solid rgba(41, 41, 41, 0.3); 
                }
                
                ";
        $css .= "
                    .tab-content {
                        position: relative;
                        background-color: #DBE9FF; 
                        border-radius: 0 0 20px 20px;
                        padding: 10px 225px; 
                        transition: background-color 0.3s ease;
                        border: 1px solid #ccc; 
                        box-shadow: 0 0 10px rgba(0,0,0,0.15);
                        margin-top: -12px;
                    }

                    .tab-content:before {
                        content: '';
                        position: absolute;
                        top: 0px;
                        left: 0;
                        border-left: 10px solid transparent;
                        border-right: 10px solid transparent;
                        border-top: 10px solid #DBE9FF;
                    }
                    .tab-content .img-container2 img {
                        width: 540px;
                        height: auto;
                        max-width: 100%; 
                        border-radius: 10px;
                        box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
                        transition: all 0.3s ease-in-out;    
                    }
                ";
        $css .= "
                    .incoterm_description {
                        font-size: 14px;
                        text-align: justify;
                        width: 400px; /* Ancho fijo en píxeles */
                        margin: 0 auto; /* Centra el div horizontalmente */
                    }
                ";
        $css .= "
                    .tab-content-1 {
                        position: relative;
                        background-color: #DBE9FF; 
                        border-radius: 0 0 20px 20px; 
                        padding: 10px 200px; 
                        transition: background-color 0.3s ease;
                        border: 1px solid #ccc; 
                        box-shadow: 0 0 10px rgba(0,0,0,0.15); 
                        margin-top: -12px;
                    }

                    .tab-content-1:before {
                        content: '';
                        position: absolute;
                        top: 0px;
                        left: 0;
                        border-left: 10px solid transparent;
                        border-right: 10px solid transparent;
                        border-top: 10px solid #DBE9FF; 
                    }
                ";

        $css .= "
                    .tab-content2 {
                        position: relative;
                        background-color: #E2FAE1; 
                        border-radius: 0 0 20px 20px; 
                        padding: 10px 252px; 
                        transition: background-color 0.3s ease;
                        border: 1px solid #ccc; 
                        box-shadow: 0 0 10px rgba(0,0,0,0.15); 
                        margin-top: -12px;
                    }
                    .tab-content2 .img-container2 img {
                        width: 486px;
                        height: auto;
                        max-width: 100%; 
                        border-radius: 10px;
                        box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
                        transition: all 0.3s ease-in-out;    
                    }                   

                    .tab-content2:before {
                        content: '';
                        position: absolute;
                        top: 0px;
                        left: 0;
                        border-left: 10px solid transparent;
                        border-right: 10px solid transparent;
                        border-top: 10px solid #E2FAE1; 
                    }
                    .tab-content2 input[type='text'],
                    .tab-content2 input[type='date'] {
                        width: 100%; /* Define el ancho deseado */
                        height: 30px; /* Define la altura deseada */
                    }
                ";
        $css .= "
                    .tab-content2-2 {
                        position: relative;
                        background-color: #E2FAE1;
                        border-radius: 0 0 20px 20px; 
                        padding: 10px 250px; 
                        transition: background-color 0.3s ease;
                        border: 1px solid #ccc; 
                        box-shadow: 0 0 10px rgba(0,0,0,0.15); 
                        margin-top: -12px;
                    }

                    .tab-content2-2:before {
                        content: '';
                        position: absolute;
                        top: 0px;
                        left: 0;
                        border-left: 10px solid transparent;
                        border-right: 10px solid transparent;
                        border-top: 10px solid #E2FAE1; 
                    }
                    .tab-content2-2 .img-container2 img {
                        width: 420px;
                        height: auto;
                        max-width: 100%; 
                        border-radius: 10px;
                        box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
                        transition: all 0.3s ease-in-out;    
                    }      
                    
                ";
        $css .= "
                .tab-content2-2 {
                    position: relative;
                    background-color: #E2FAE1;
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 370px; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15); 
                    margin-top: -12px;
                }

                .tab-content2-2:before {
                    content: '';
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #E2FAE1; 
                }
                .tab-content2-2 .img-container2 img {
                    width: 420px;
                    height: auto;
                    max-width: 100%; 
                    border-radius: 10px;
                    box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
                    transition: all 0.3s ease-in-out;    
                }      
                
            ";
        $css .= "
                    .tab-content3 {
                        position: relative;
                        background-color: #F1E8FC; 
                        border-radius: 0 0 20px 20px; 
                        padding: 10px 230px; 
                        transition: background-color 0.3s ease;
                        border: 1px solid #ccc; 
                        box-shadow: 0 0 10px rgba(0,0,0,0.15);
                        margin-top: -12px;
                    }
                                    
                    
                    .tab-content3 .img-container2 {
                        width: 100%; 
                        height: 100%; 
                        position: relative; 
                    }
                    
                    .tab-content3 .img-wrapper {
                        position: relative;
                        width: 100%;
                        height: 100%;
                        overflow: hidden; 
                    }
                    
                    .tab-content3 .img-wrapper img {
                        width: 100%; 
                        height: auto; 
                        border-radius: 10px;
                        box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
                        transition: all 0.3s ease-in-out;
                    }

                    .tab-content3 .button-container {
                        position: absolute; 
                        top: 110%; 
                        left: 29%; /
                        transform: translate(-50%, -50%); 
                        z-index: 1; 
                    }
                    

                    .tab-content3:before {
                        content: '';
                        position: absolute;
                        top: 0px;
                        left: 0;
                        border-left: 10px solid transparent;
                        border-right: 10px solid transparent;
                        border-top: 10px solid #F1E8FC; 
                    }
                    .tab-content3 input[type='text'],
                    .tab-content3 select {
                        width: 250%; 
                        max-width: 550px; 
                        padding: 5px; 
                        box-sizing: border-box;
                        margin: 0 auto;
                        text-align: center;
                    }
                ";
        $css .= "
                .tab-content3empty {
                    position: relative;
                    background-color: #F1E8FC; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 180px; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                }
                                   
                
                .tab-content3empty {
                    width: 100%; 
                    height: 100%; 
                    position: relative; 
                }
                
                .tab-content3empty .img-wrapper {
                    position: relative;
                    width: 100%;
                    height: 100%;
                    overflow: hidden; 
                }
                
                .tab-content3empty .img-wrapper img {
                    width: 100%; 
                    height: auto; 
                    border-radius: 10px;
                    box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
                    transition: all 0.3s ease-in-out;
                }

                .tab-content3empty .button-container {
                    position: absolute; 
                    top: 110%; 
                    left: 29%; /
                    transform: translate(-50%, -50%); 
                    z-index: 1; 
                }
                

                .tab-content3empty:before {
                    content: '';
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #F1E8FC; 
                }
                .tab-content3empty input[type='text'],
                .tab-content3empty select {
                    width: 250%; 
                    max-width: 550px; 
                    padding: 5px; 
                    box-sizing: border-box;
                    margin: 0 auto;
                    text-align: center;
                }
            ";
        $css .= "
                .tab-content3-1 {
                    position: relative;
                    background-color: #F1E8FC; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 200px; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                }
                #section3.tab-content3-1.empty-packs {
                    padding: 10px 168px;  
                }                    
                
                .tab-content3-1 .img-container2 {
                    width: 100%; 
                    height: 100%; 
                    position: relative; 
                }
                
                .tab-content3-1 .img-wrapper {
                    position: relative;
                    width: 100%;
                    height: 100%;
                    overflow: hidden; 
                }
                
                .tab-content3-1 .img-wrapper img {
                    width: 100%; 
                    height: auto; 
                    border-radius: 10px;
                    box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
                    transition: all 0.3s ease-in-out;
                }

                .tab-content3-1 .button-container {
                    position: absolute; 
                    top: 110%; 
                    left: 29%; /
                    transform: translate(-50%, -50%); 
                    z-index: 1; 
                }
                

                .tab-content3-1:before {
                    content: '';
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #F1E8FC; 
                }
                .tab-content3-1 input[type='text'],
                .tab-content3-1 select {
                    width: 250%; 
                    max-width: 550px; 
                    padding: 5px; 
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                }
            ";
        $css .= "
                    .tab-content4 {
                        position: relative;
                        background-color: #FEDFF0; 
                        border-radius: 0 0 20px 20px; 
                        padding: 10px 240px; 
                        transition: background-color 0.3s ease;
                        border: 1px solid #ccc; 
                        box-shadow: 0 0 10px rgba(0,0,0,0.15);
                        margin-top: -12px;
                    }

                    .tab-content4:before {
                        content: '';
                        position: absolute;
                        top: 0px;
                        left: 0;
                        border-left: 10px solid transparent;
                        border-right: 10px solid transparent;
                        border-top: 10px solid #FEDFF0;
                    }
                    .tab-content4 input[type='text'],
                    .tab-content4 select,
                    .tab-content4 input[type='date'] {
                        width: 250%;
                        max-width: 480px;
                        padding: 5px;
                        box-sizing: border-box;
                        margin: 0 auto;
                        text-align: center;
                    }
                ";
        $css .= "
                .tab-content4-1 {
                    position: relative;
                    background-color: #FEDFF0; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 280px; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                }

                .tab-content4-1:before {
                    content: '';
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FEDFF0;
                }
                .tab-content4-1 input[type='text'],
                .tab-content4-1 select,
                .tab-content4-1 input[type='date'] {
                    width: 250%;
                    max-width: 480px;
                    padding: 5px;
                    box-sizing: border-box;
                    margin: 0 auto;
                    text-align: center;
                }
            ";
        $css .= "
                    .tab-content4-2 {
                        position: relative;
                        background-color: #FEDFF0; 
                        border-radius: 0 0 20px 20px; 
                        padding: 10px 165px; 
                        transition: background-color 0.3s ease;
                        border: 1px solid #ccc; 
                        box-shadow: 0 0 10px rgba(0,0,0,0.15); 
                        margin-top: -12px;
                    }

                    .tab-content4-2:before {
                        content: '';
                        position: absolute;
                        top: 0px;
                        left: 0;
                        border-left: 10px solid transparent;
                        border-right: 10px solid transparent;
                        border-top: 10px solid #FEDFF0;
                    }
                    .tab-content4-2 input[type='text'],
                    .tab-content4-2 select{,
                        width: 250%; 
                        max-width: 480px; 
                        padding: 5px;
                        box-sizing: border-box;
                        margin: 0 auto;
                        text-align: center;
                    }
                    .tab-content4-2 textarea {
                        width: 100% !important;
                        max-width: 480px !important;
                        padding: 5px !important;
                        box-sizing: border-box !important;
                        margin: 0 auto !important;
                        text-align: center !important;
                    }

                   
                    
                ";
        $css .= "
                    .tab-content4-3 {
                        position: relative;
                        background-color: #FEDFF0; 
                        border-radius: 0 0 20px 20px; 
                        padding: 10px 220px; 
                        transition: background-color 0.3s ease;
                        border: 1px solid #ccc; 
                        box-shadow: 0 0 10px rgba(0,0,0,0.15);
                        margin-top: -12px;
                    }

                    .tab-content4-3:before {
                        content: '';
                        position: absolute;
                        top: 0px;
                        left: 0;
                        border-left: 10px solid transparent;
                        border-right: 10px solid transparent;
                        border-top: 10px solid #FEDFF0; 
                    }
                    .tab-content4-3 input[type='text'],
                    .tab-content4-3 select,
                    .tab-content4-3 input[type='date'] {
                        width: 250%; 
                        max-width: 480px; 
                        padding: 5px; 
                        box-sizing: border-box;
                        margin: 0 auto;
                        text-align: center;
                    }
                ";
        $css .= "
                    .tab-content4-4 {
                        position: relative;
                        background-color: #FEDFF0; 
                        border-radius: 0 0 20px 20px; 
                        padding: 10px 282px;
                        transition: background-color 0.3s ease;
                        border: 1px solid #ccc;
                        box-shadow: 0 0 10px rgba(0,0,0,0.15);
                        margin-top: -12px;
                    }

                    .tab-content4-4:before {
                        content: '';
                        position: absolute;
                        top: 0px;
                        left: 0;
                        border-left: 10px solid transparent;
                        border-right: 10px solid transparent;
                        border-top: 10px solid #FEDFF0; 
                    }
                    .tab-content4-4 input[type='text'],
                    .tab-content4-4 select,
                    .tab-content4-4 input[type='date'] {
                        width: 250%;
                        max-width: 480px;
                        padding: 5px; 
                        box-sizing: border-box; 
                        margin: 0 auto;
                        text-align: center;
                    }
                    
                ";
        $css .= "
                    .tab-content4-5 {
                        position: relative;
                        background-color: #FEDFF0;
                        border-radius: 0 0 20px 20px;
                        padding: 10px 236px; 
                        transition: background-color 0.3s ease;
                        border: 1px solid #ccc;
                        box-shadow: 0 0 10px rgba(0,0,0,0.15);
                        margin-top: -12px;
                    }

                    .tab-content4-5:before {
                        content: '';
                        position: absolute;
                        top: 0px;
                        left: 0;
                        border-left: 10px solid transparent;
                        border-right: 10px solid transparent;
                        border-top: 10px solid #FEDFF0;
                    }
                    .tab-content4-5 input[type='text'],
                    .tab-content4-5 select,
                    .tab-content4-5 input[type='date'] {
                        width: 250%; 
                        max-width: 480px; 
                        padding: 5px;
                        box-sizing: border-box; 
                        margin: 0 auto;
                        text-align: center;
                    }
                ";
        $css .= "
                .tab-content4-1-1 {
                    position: relative;
                    background-color: #FEDFF0; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 240px; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                }

                .tab-content4-1-1:before {
                    content: '';
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FEDFF0;
                }
                .tab-content4-1-1 input[type='text'],
                .tab-content4-1-1 select,
                .tab-content4-1-1 input[type='date'] {
                    width: 250%;
                    max-width: 480px;
                    padding: 5px;
                    box-sizing: border-box;
                    margin: 0 auto;
                    text-align: center;
                }
            ";
        $css .= "
                    .tab-content4-2-1 {
                        position: relative;
                        background-color: #FEDFF0;
                        border-radius: 0 0 20px 20px; 
                        padding: 10px 210px; 
                        transition: background-color 0.3s ease;
                        border: 1px solid #ccc; 
                        box-shadow: 0 0 10px rgba(0,0,0,0.15);
                        margin-top: -12px;
                    }

                    .tab-content4-2-1:before {
                        content: '';
                        position: absolute;
                        top: 0px;
                        left: 0;
                        border-left: 10px solid transparent;
                        border-right: 10px solid transparent;
                        border-top: 10px solid #FEDFF0;
                    }
                    .tab-content4-2-1 input[type='text'],
                    .tab-content4-2-1 select{
                        width: 250%; 
                        max-width: 480px;
                        padding: 5px;
                        box-sizing: border-box;
                        margin: 0 auto;
                        text-align: center;
                    }
                   
                    
                ";
        $css .= "
                    .tab-content4-3-1 {
                        position: relative;
                        background-color: #FEDFF0; 
                        border-radius: 0 0 20px 20px;
                        padding: 10px 282px; 
                        transition: background-color 0.3s ease;
                        border: 1px solid #ccc;
                        box-shadow: 0 0 10px rgba(0,0,0,0.15);
                        margin-top: -12px;
                    }

                    .tab-content4-3-1:before {
                        content: '';
                        position: absolute;
                        top: 0px;
                        left: 0;
                        border-left: 10px solid transparent;
                        border-right: 10px solid transparent;
                        border-top: 10px solid #FEDFF0;
                    }
                    .tab-content4-3-1 input[type='text'],
                    .tab-content4-3-1 select,
                    .tab-content4-3-1 input[type='date'] {
                        width: 250%; 
                        max-width: 480px;
                        padding: 5px;
                        box-sizing: border-box; 
                        margin: 0 auto;
                        text-align: center;
                    }
                ";
        $css .= "
                    .tab-content4-4-1 {
                        position: relative;
                        background-color: #FEDFF0; 
                        border-radius: 0 0 20px 20px; 
                        padding: 10px 260px;
                        transition: background-color 0.3s ease;
                        border: 1px solid #ccc;
                        box-shadow: 0 0 10px rgba(0,0,0,0.15); 
                        margin-top: -12px;
                    }

                    .tab-content4-4-1:before {
                        content: '';
                        position: absolute;
                        top: 0px;
                        left: 0;
                        border-left: 10px solid transparent;
                        border-right: 10px solid transparent;
                        border-top: 10px solid #FEDFF0;
                    }
                    .tab-content4-4-1 input[type='text'],
                    .tab-content4-4-1 select,
                    .tab-content4-4-1 input[type='date'] {
                        width: 250%;
                        max-width: 480px;
                        padding: 5px;
                        box-sizing: border-box; 
                        margin: 0 auto;
                        text-align: center;
                    }
                    
                ";
        $css .= "
                    .tab-content4-5-1 {
                        position: relative;
                        background-color: #FEDFF0;
                        border-radius: 0 0 20px 20px; 
                        padding: 10px 236px; 
                        transition: background-color 0.3s ease;
                        border: 1px solid #ccc; 
                        box-shadow: 0 0 10px rgba(0,0,0,0.15);
                        margin-top: -12px;
                    }

                    .tab-content4-5-1:before {
                        content: '';
                        position: absolute;
                        top: 0px;
                        left: 0;
                        border-left: 10px solid transparent;
                        border-right: 10px solid transparent;
                        border-top: 10px solid #FEDFF0; 
                    }
                    .tab-content4-5-1 input[type='text'],
                    .tab-content4-5-1 select,
                    .tab-content4-5-1 input[type='date'] {
                        width: 250%; 
                        max-width: 480px; 
                        padding: 5px; 
                        box-sizing: border-box; 
                        margin: 0 auto;
                        text-align: center;
                    }
                ";
        $css .= "
                    .tab-content5 {
                        position: relative;
                        background-color: #ffd8b2; 
                        border-radius: 0 0 20px 20px; 
                        padding: 10px 222px; 
                        transition: background-color 0.3s ease;
                        border: 1px solid #ccc; 
                        box-shadow: 0 0 10px rgba(0,0,0,0.15);
                        margin-top: -12px;
                    }

                    .tab-content5:before {
                        content: '';
                        position: absolute;
                        top: 0px;
                        left: 0;
                        border-left: 10px solid transparent;
                        border-right: 10px solid transparent;
                        border-top: 10px solid #ffd8b2;
                    }
                    .tab-content5 input[type='text'],
                    .tab-content5 select,
                    .tab-content5 input[type='date'] {
                        width: 250%; 
                        max-width: 480px; 
                        padding: 5px;
                        box-sizing: border-box; 
                        margin: 0 auto;
                        text-align: center;
                    }
                ";
        $css .= "
                .tab-content5-1 {
                    position: relative;
                    background-color: #ffd8b2; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 200px; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                }

                .tab-content5-1:before {
                    content: '';
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #ffd8b2;
                }
                .tab-content5-1 input[type='text'],
                .tab-content5-1 select,
                .tab-content5-1 input[type='date'] {
                    width: 250%; 
                    max-width: 480px; 
                    padding: 5px;
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                }
            ";
        $css .= "
            .tab-content6 {
                position: relative;
                background-color: #FFF5BA; 
                border-radius: 0 0 20px 20px; 
                padding: 10px 248px; 
                transition: background-color 0.3s ease;
                border: 1px solid #ccc; 
                box-shadow: 0 0 10px rgba(0,0,0,0.15);
                margin-top: -12px;
            }

            .tab-content6:before {
                content: '';
                position: absolute;
                top: 0px;
                left: 0;
                border-left: 10px solid transparent;
                border-right: 10px solid transparent;
                border-top: 10px solid #FFF5BA;
            }
            .tab-content6 input[type='text'],
            .tab-content6 select,
            .tab-content6 input[type='date'],
            .tab-content6 input[type='number'],
            .tab-content6 textarea {
                
                width: 250%; 
                max-width: 480px; 
                padding: 5px;
                box-sizing: border-box; 
                margin: 0 auto;
                text-align: center;
            }
            ";
        $css .= "
            .tab-content6-1 {
                position: relative;
                background-color: #FFF5BA; 
                border-radius: 0 0 20px 20px; 
                padding: 10px 238px; 
                transition: background-color 0.3s ease;
                border: 1px solid #ccc; 
                box-shadow: 0 0 10px rgba(0,0,0,0.15);
                margin-top: -12px;
            }

            .tab-content6-1:before {
                content: '';
                position: absolute;
                top: 0px;
                left: 0;
                border-left: 10px solid transparent;
                border-right: 10px solid transparent;
                border-top: 10px solid #FFF5BA;
            }
            .tab-content6-1 input[type='text'],
            .tab-content6-1 select,
            .tab-content6-1 input[type='date'],
            .tab-content6-1 input[type='number'],
            .tab-content6-1 textarea {
                
                width: 250%; 
                max-width: 480px; 
                padding: 5px;
                box-sizing: border-box; 
                margin: 0 auto;
                text-align: center;
            }
            ";

        $css .= "
            .tab-content6-1-1 {
                position: relative;
                background-color: #FFF5BA; 
                border-radius: 0 0 20px 20px; 
                padding: 10px 200px; 
                transition: background-color 0.3s ease;
                border: 1px solid #ccc; 
                box-shadow: 0 0 10px rgba(0,0,0,0.15);
                margin-top: -12px;
            }

            .tab-content6-1-1:before {
                content: '';
                position: absolute;
                top: 0px;
                left: 0;
                border-left: 10px solid transparent;
                border-right: 10px solid transparent;
                border-top: 10px solid #FFF5BA;
            }
            .tab-content6-1-1 input[type='text'],
            .tab-content6-1-1 select,
            .tab-content6-1-1 input[type='date'],
            .tab-content6-1-1 input[type='number'],
            .tab-content6-1-1 textarea {
                width: 250%; 
                max-width: 480px; 
                padding: 5px;
                box-sizing: border-box; 
                margin: 0 auto;
                text-align: center;
            }
            .tab-content6-1-1 .img-container2 img {
                width: 290px;
                height: auto; 
            }
            ";

        $css .= '
            ' . $addon_id . ' .form-group {
                margin-bottom: 2rem;
                display: flex;
                flex-wrap: wrap;
                gap: 12px;
                align-items: center;
            }
            
            ' . $addon_id . ' .form-control {
                flex: 1 0 100%;
                border-radius: 8px;
                border: 2px solid #3498db;
                transition: border 0.3s ease-in-out; 
                padding: 10px;
                box-sizing: border-box;
                font-size: 16px;    
            }
            
            ' . $addon_id . ' .form-control:focus {
                border-color: #007bff !important; 
            }
            
            ' . $addon_id . ' select {
                flex: 1 0 100%;
                border-radius: 8px;
                border: 1px solid #3498db;
                transition: border 0.3s ease-in-out; 
                padding: 6px;
                box-sizing: border-box;
                font-size: 15px;
                text-align: center;
            }
            
            ' . $addon_id . ' select:focus {
                border-color: #007bff !important; 
            }            
            ';
        $css .= '
                .status-text {
                    font-size: 15px;
                    margin-top: 0.2em; 
                }
            ';
        $css .= '
            ' . $addon_id . ' input[type="number"] {
                flex: 1 0 100%;
                text-align: center;
                border-radius: 8px;
                border: 2px solid #3498db;
                transition: border 0.3s ease-in-out; 
                padding: 10px;
                box-sizing: border-box;
                font-size: 16px;
                -moz-appearance: textfield; 
            }

            ' . $addon_id . ' input[type="number"]::-webkit-inner-spin-button,
            ' . $addon_id . ' input[type="number"]::-webkit-outer-spin-button {
                -webkit-appearance: none;
                margin: 0;
                text-align: center;
            }

            ' . $addon_id . ' input[type="number"]:focus {
                border-color: #007bff !important; 
                text-align: center;
            }
            ';

        $css .= '
                ' . $addon_id . ' .img-container {
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    justify-content: center;
                    width: 10%;
                    margin: 0 5px;
                }

                ' . $addon_id . ' .img-container img {
                    width: 100%;
                    height: auto;
                    border-radius: 10px;
                    box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
                    transition: all 0.3s ease-in-out; 
                    
                }
                

                ' . $addon_id . ' .img-container p {
                    text-align: center;
                    margin-top: 5px;
                }
                ' . $addon_id . ' img:hover {
                    box-shadow: 0 0 30px rgba(0, 0, 0, 0.2);
                }
            
                ' . $addon_id . ' .img.active {
                    filter: none; 
                    background-color: #d9e8ed;
                }
                ' . $addon_id . ' .img.active2 {
                    filter: none; 
                    background-color: #a5cfd4;
                }
                
                ' . $addon_id . ' {
                    display: flex;
                    flex-wrap: wrap;
                }
                
                ' . $addon_id . ' .img-container2 {
                    box-sizing: border-box;
                    flex: 1 0 calc(20% - 20px); 
                    max-width: calc(20% - 20px);
                    margin-right: 20px;
                    margin-bottom: 20px;
                    
                }
                
                
                ' . $addon_id . ' .invoice-number {
                    font-size: 14px; 
                    margin-top: 5px; 
                }
                
                ' . $addon_id . ' .button-group button {
                    font-size: 10px; 
                    margin-left: 5px;
                }

            ';

        $css .= '
            @media (max-width: 800px) {
                ' . $addon_id . ' .sppb-addon {
                    max-width: calc(100% - 40px);
                    padding: 10px; 
                }
            
                ' . $addon_id . ' .tab {
                    padding: 10px 10px;
                }
            
                ' . $addon_id . ' .tab-content {
                    position: relative;
                    background-color: #DBE9FF; 
                    border-radius: 0 0 20px 20px;
                    padding: 10px 105px;
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    display: flex; 
                    justify-content: center;
                    align-items: center; 
                }
            
                ' . $addon_id . ' .tab-content-1 {
                    position: relative;
                    background-color: #DBE9FF; 
                    border-radius: 0 0 20px 20px;
                    padding: 10px 80px; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                }
                
                ' . $addon_id . ' .container {
                    display: flex;
                    flex-direction: column;
                    justify-content: center;
                    align-items: center; 
                    flex-wrap: wrap;
                }
                ' . $addon_id . ' .img-container2 {
                    box-sizing: border-box;
                    flex: 1 0 calc(20% - 20px); 
                    max-width: calc(20% - 20px);
                    margin: -10px;
                    padding: -10px;
                    
                }
                
                ' . $addon_id . ' .img-container2 img {
                    width: 100%;
                    height: auto;
                    max-width: 100%; 
                    border-radius: 10px;
                    box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
                    transition: all 0.3s ease-in-out;
                    
                    
                }
                
                ' . $addon_id . ' .invoice-number {
                    font-size: 14px; 
                    margin-top: 5px; 
                }
                
                ' . $addon_id . ' .button-group button {
                    font-size: 10px; 
                    margin-left: 2px;
                }
        
                ' . $addon_id . ' .form-group {
                    margin-bottom: 1rem; 
                    flex-direction: column; 
                    align-items: center;
                    justify-content: center;
                }
        
                ' . $addon_id . ' .img-container {
                    max-width: 25%; 
                    margin: 0 auto; 
                }
                ' . $addon_id . ' .status-text {
                    font-size: 10px;
                    margin-top: 0.2em; 
                }
        
                ' . $addon_id . ' .img-container img {
                    max-width: 100%;
                    height: auto;
                    display: block; 
                    margin: 0 auto; /
                }

                ' . $addon_id . ' .form-control,
                ' . $addon_id . ' #document_upload,
                ' . $addon_id . ' select {
                    border-radius: 8px;
                    border: 2px solid #3498db;
                    font-size: 12px; 
                    margin-bottom: 0.5em;
                    box-sizing: border-box;
                    width: calc(100% - 2em); 
                    padding: 0.3em; 
                }

                ' . $addon_id . ' .form-group {
                    display: flex;
                    flex-direction: column;
                    justify-content: center;
                    align-items: center;
                    width: 100%;
                }
                ' . $addon_id . ' .form-group .btn,
                ' . $addon_id . ' .form-group .btn-danger,
                ' . $addon_id . ' .form-group .btn-primary,
                ' . $addon_id . ' .form-group .btn-warning {
                    border-radius: 8px;
                    padding: 0.5em 1em; 
                    font-size: 12px; 
                    margin-top: 1.5em; /* Ajusta el margen superior según sea necesario */
                    margin-left: auto; /* Centra horizontalmente */
                    margin-right: auto; /* Centra horizontalmente */
                    display: block; /* Asegura que el botón sea un bloque para centrarlo horizontalmente */
                }
                
                ' . $addon_id . ' form {
                    width: 100%;
                    box-sizing: border-box;
                }
                ' . $addon_id . ' .tab-content2 {
                    position: relative;
                    background-color: #E2FAE1; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 135px; /* Ajusta el padding según sea necesario */
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15); 
                    margin-top: -12px;
                }
            
                ' . $addon_id . ' .tab-content2 .img-container2 img {
                    width: 486px;
                    height: auto;
                    max-width: 100%; 
                    border-radius: 10px;
                    box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
                    transition: all 0.3s ease-in-out;    
                }                   
            
                ' . $addon_id . ' .tab-content2:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #E2FAE1; 
                }
            
                ' . $addon_id . ' .tab-content2-1 {
                    position: relative;
                    background-color: #E2FAE1;
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 120px; /* Ajusta el padding según sea necesario */
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15); 
                    margin-top: -12px;
                }
            
                ' . $addon_id . ' .tab-content2-1:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #E2FAE1; 
                }
            
                ' . $addon_id . ' .tab-content2-1 .img-container2 img {
                    width: 100%;
                    height: auto;
                    max-width: 100%; 
                    border-radius: 10px;
                    box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
                    transition: all 0.3s ease-in-out;     
                }
                
                
                ' . $addon_id . ' .tab-content3 .button-container {
                    position: absolute; 
                    top: 110%; 
                    left: 50%;
                    transform: translate(-50%, -50%); 
                    z-index: 1; 
                }
                ' . $addon_id . ' .tab-content3 {
                    position: relative;
                    background-color: #F1E8FC; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                }
            
                ' . $addon_id . ' #section3.tab-content3.empty-packs {
                    padding: 10px 168px;  
                }                    
            
                ' . $addon_id . ' .tab-content3 .img-container2 {
                    width: 100%; 
                    height: 100%; 
                    position: relative; 
                }
            
                ' . $addon_id . ' .tab-content3 .img-wrapper {
                    position: relative;
                    width: 100%;
                    height: 100%;
                    overflow: hidden; 
                }
            
                ' . $addon_id . ' .tab-content3 .img-wrapper img {
                    width: 100%; 
                    height: auto; 
                    border-radius: 10px;
                    box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
                    transition: all 0.3s ease-in-out;
                }
            
                ' . $addon_id . ' .tab-content3:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #F1E8FC; 
                }
            
                ' . $addon_id . ' .tab-content3 input[type="text"],
                ' . $addon_id . ' .tab-content3 select {
                    width: 80%; 
                    max-width: 80%; 
                    padding: 5px; 
                    box-sizing: border-box;
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content3-1 {
                    position: relative;
                    background-color: #F1E8FC; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 80px; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                }
            
                ' . $addon_id . ' #section3.tab-content3-1.empty-packs {
                    padding: 10px 168px;  
                }                    
            
                ' . $addon_id . ' .tab-content3-1 .img-container2 {
                    width: 100%; 
                    height: 100%; 
                    position: relative; 
                }
            
                ' . $addon_id . ' .tab-content3-1 .img-wrapper {
                    position: relative;
                    width: 100%;
                    height: 100%;
                    overflow: hidden; 
                }
            
                ' . $addon_id . ' .tab-content3-1 .img-wrapper img {
                    width: 100%; 
                    height: auto; 
                    border-radius: 10px;
                    box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
                    transition: all 0.3s ease-in-out;
                }
            
                ' . $addon_id . ' .tab-content3-1 .button-container {
                    position: absolute; 
                    top: 110%; 
                    left: 29%; 
                    transform: translate(-50%, -50%); 
                    z-index: 1; 
                }
            
                ' . $addon_id . ' .tab-content3-1:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #F1E8FC; 
                }
            
                ' . $addon_id . ' .tab-content3-1 input[type="text"],
                ' . $addon_id . ' .tab-content3-1 select {
                    width: 80%; 
                    max-width: 80%; 
                    padding: 5px; 
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content4 {
                    position: relative;
                    background-color: #FEDFF0; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 90px; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                }
            
                ' . $addon_id . ' .tab-content4:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FEDFF0;
                }
                ' . $addon_id . ' .tab-content4 input[type="text"],
                ' . $addon_id . ' .tab-content4 select,
                ' . $addon_id . ' .tab-content4 input[type="date"],
                ' . $addon_id . ' .tab-content4 input[type="file"] {
                    width: 480px;
                    max-width: 100%;
                    padding: 5px;
                    box-sizing: border-box;
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content4-4 {
                    position: relative;
                    background-color: #FEDFF0; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 80px;
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc;
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                }

                ' . $addon_id . ' .tab-content4-4:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FEDFF0; 
                }
                ' . $addon_id . ' .tab-content4-4 input[type="text"],
                ' . $addon_id . ' .tab-content4-4 select,
                ' . $addon_id . ' .tab-content4-4 input[type="date"] {
                    width: 250%;
                    max-width: 480px;
                    padding: 5px; 
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content4-2 {
                    position: relative;
                    background-color: #FEDFF0; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 80px; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15); 
                    margin-top: -12px;
                }

                ' . $addon_id . ' .tab-content4-2:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FEDFF0;
                }
                ' . $addon_id . ' .tab-content4-2 input[type="text"],
                ' . $addon_id . ' .tab-content4-2 select{
                    width: 250%; 
                    max-width: 480px; 
                    padding: 5px;
                    box-sizing: border-box;
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content4-3 {
                    position: relative;
                    background-color: #FEDFF0; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 80px; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                }
                ' . $addon_id . ' .tab-content4-3:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FEDFF0; 
                }
                ' . $addon_id . ' .tab-content4-3 input[type="text"],
                ' . $addon_id . ' .tab-content4-3 select,
                ' . $addon_id . ' .tab-content4-3 input[type="date"] {
                    width: 250%; 
                    max-width: 480px; 
                    padding: 5px; 
                    box-sizing: border-box;
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content4-1 {
                    position: relative;
                    background-color: #FEDFF0; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 80px; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                }

                ' . $addon_id . ' .tab-content4-1:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FEDFF0;
                }
                ' . $addon_id . ' .tab-content4-1 input[type="text"],
                ' . $addon_id . ' .tab-content4-1 select,
                ' . $addon_id . ' .tab-content4-1 input[type="date"] {
                    width: 250%;
                    max-width: 480px;
                    padding: 5px;
                    box-sizing: border-box;
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content4-5 {
                    position: relative;
                    background-color: #FEDFF0;
                    border-radius: 0 0 20px 20px;
                    padding: 10px 80px; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc;
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                }

                ' . $addon_id . ' .tab-content4-5:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FEDFF0;
                }
                ' . $addon_id . ' .tab-content4-5 input[type="text"],
                ' . $addon_id . ' .tab-content4-5 select,
                ' . $addon_id . ' .tab-content4-5 input[type="date"] {
                    width: 250%; 
                    max-width: 480px; 
                    padding: 5px;
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content4-2-1 {
                    position: relative;
                    background-color: #FEDFF0;
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 80px; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                }

                ' . $addon_id . ' .tab-content4-2-1:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FEDFF0;
                }
                ' . $addon_id . ' .tab-content4-2-1 input[type="text"],
                ' . $addon_id . ' .tab-content4-2-1 select{
                    width: 250%; 
                    max-width: 480px;
                    padding: 5px;
                    box-sizing: border-box;
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content4-3-1 {
                    position: relative;
                    background-color: #FEDFF0; 
                    border-radius: 0 0 20px 20px;
                    padding: 10px 80px; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc;
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                }

                ' . $addon_id . ' .tab-content4-3-1:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FEDFF0;
                }
                ' . $addon_id . ' .tab-content4-3-1 input[type="text"],
                ' . $addon_id . ' .tab-content4-3-1 select,
                ' . $addon_id . ' .tab-content4-3-1 input[type="date"] {
                    width: 250%; 
                    max-width: 480px;
                    padding: 5px;
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content4-4-1 {
                    position: relative;
                    background-color: #FEDFF0; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 80px;
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc;
                    box-shadow: 0 0 10px rgba(0,0,0,0.15); 
                    margin-top: -12px;
                }

                ' . $addon_id . ' .tab-content4-4-1:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FEDFF0;
                }
                ' . $addon_id . ' .tab-content4-4-1 input[type="text"],
                ' . $addon_id . ' .tab-content4-4-1 select,
                ' . $addon_id . ' .tab-content4-4-1 input[type="date"] {
                    width: 250%;
                    max-width: 480px;
                    padding: 5px;
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content4-5-1 {
                    position: relative;
                    background-color: #FEDFF0;
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 80px; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                }

                ' . $addon_id . ' .tab-content4-5-1:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FEDFF0; 
                }
                ' . $addon_id . ' .tab-content4-5-1 input[type="text"],
                ' . $addon_id . ' .tab-content4-5-1 select,
                ' . $addon_id . ' .tab-content4-5-1 input[type="date"] {
                    width: 250%; 
                    max-width: 480px; 
                    padding: 5px; 
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content4-1-1 {
                    position: relative;
                    background-color: #FEDFF0; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 80px; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                }

                ' . $addon_id . ' .tab-content4-1-1:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FEDFF0;
                }
                ' . $addon_id . ' .tab-content4-1-1 input[type="text"],
                ' . $addon_id . ' .tab-content4-1-1 select,
                ' . $addon_id . ' .tab-content4-1-1 input[type="date"] {
                    width: 250%;
                    max-width: 480px;
                    padding: 5px;
                    box-sizing: border-box;
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content5 {
                    position: relative;
                    background-color: #ffd8b2; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 105px; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                }

                ' . $addon_id . ' .tab-content5:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #ffd8b2;
                }
                ' . $addon_id . ' .tab-content5 input[type="text"],
                ' . $addon_id . ' .tab-content5 select,
                ' . $addon_id . '.tab-content5 input[type="date"] {
                    width: 250%; 
                    max-width: 480px; 
                    padding: 5px;
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content5-1 {
                    position: relative;
                    background-color: #ffd8b2; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 80px; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                }

                ' . $addon_id . ' .tab-content5-1:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #ffd8b2;
                }
                ' . $addon_id . ' .tab-content5-1 input[type="text"],
                ' . $addon_id . ' .tab-content5-1 select,
                ' . $addon_id . '.tab-content5-1 input[type="date"] {
                    width: 250%; 
                    max-width: 480px; 
                    padding: 5px;
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content6 {
                    position: relative;
                    background-color: #FFF5BA; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 116px; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                }
    
                ' . $addon_id . ' .tab-content6:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FFF5BA;
                }
                ' . $addon_id . ' .tab-content6 input[type="text"],
                ' . $addon_id . ' .tab-content6 select,
                ' . $addon_id . ' .tab-content6 input[type="date"],
                ' . $addon_id . ' .tab-content6 input[type="number"],
                ' . $addon_id . ' .tab-content6 textarea {
                    width: 250%; 
                    max-width: 480px; 
                    padding: 5px;
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content6-1 {
                    position: relative;
                    background-color: #FFF5BA; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 80px; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                }
    
                ' . $addon_id . ' .tab-content6-1:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FFF5BA;
                }
                ' . $addon_id . ' .tab-content6-1 input[type="text"],
                ' . $addon_id . ' .tab-content6-1 select,
                ' . $addon_id . ' .tab-content6-1 input[type="date"],
                ' . $addon_id . ' .tab-content6-1 input[type="number"],
                ' . $addon_id . ' .tab-content6-1 textarea {
                    width: 100%; 
                    max-width: 100%; 
                    padding: 5px;
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content6-1-1 {
                    position: relative;
                    background-color: #FFF5BA; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 80px; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                }
    
                ' . $addon_id . ' .tab-content6-1-1:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FFF5BA;
                }
                ' . $addon_id . ' .tab-content6-1-1 input[type="text"],
                ' . $addon_id . ' .tab-content6-1-1 select,
                ' . $addon_id . ' .tab-content6-1-1 input[type="date"],
                ' . $addon_id . ' .tab-content6-1-1 input[type="number"],
                ' . $addon_id . ' .tab-content6-1-1 textarea {
                    width: 100%; 
                    max-width: 100%; 
                    padding: 5px;
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                }
                .tab-content6-1-1 .img-container2 img {
                    width: 290px;
                    height: auto; 
                }
                
            }
            
            ';

        $css .= '
            @media (max-width: 500px) {
                ' . $addon_id . ' .sppb-addon {
                    max-width: calc(100% - 20px);
                    padding: 10px;
                }
                ' . $addon_id . ' input[type="file"] {
                    font-size: 12px;
                }
            
                ' . $addon_id . ' .tab {
                    padding: 5px 5px; 
                    max-width: calc(100% - 10px); 
                    overflow-x: auto;
                    font-size: 10px;
                }

                ' . $addon_id . ' .tab  span {
                    font-size: 5px;
                }
            
                ' . $addon_id . ' .tab-content {
                    position: relative;
                    background-color: #DBE9FF; 
                    border-radius: 0 0 20px 20px;
                    padding: 10px 105px;
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    display: flex; 
                    justify-content: center;
                    align-items: center; 
                }
            
                ' . $addon_id . ' .tab-content-1 {
                    position: relative;
                    background-color: #DBE9FF; 
                    border-radius: 0 0 20px 20px;
                    padding: 10px 20px !important; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    width: 101%;
                }
                
                ' . $addon_id . ' .container {
                    display: flex;
                    flex-direction: column;
                    justify-content: center;
                    align-items: center; 
                    flex-wrap: wrap;
                }
               
                
                ' . $addon_id . ' .invoice-number {
                    font-size: 14px; 
                    margin-top: 5px; 
                }
                
                ' . $addon_id . ' .button-group button {
                    font-size: 10px; 
                    margin-left: 2px;
                }
        
                ' . $addon_id . ' .form-group {
                    margin-bottom: 1rem; 
                    flex-direction: column; 
                    align-items: center;
                    justify-content: center;
                }
        
               
                ' . $addon_id . ' .status-text {
                    font-size: 10px;
                    margin-top: 0.2em; 
                }
        
                
                ' . $addon_id . ' .form-control,
                ' . $addon_id . ' #document_upload,
                ' . $addon_id . ' select {
                    border-radius: 8px;
                    border: 2px solid #3498db;
                    font-size: 12px; 
                    margin-bottom: 0.5em;
                    box-sizing: border-box;
                    width: calc(100% - 2em); 
                    padding: 0.3em; 
                }

                ' . $addon_id . ' .form-group {
                    display: flex;
                    flex-direction: column;
                    justify-content: center;
                    align-items: center;
                    width: 100%;
                }
                ' . $addon_id . ' .form-group .btn,
                ' . $addon_id . ' .form-group .btn-danger,
                ' . $addon_id . ' .form-group .btn-primary,
                ' . $addon_id . ' .form-group .btn-warning {
                    border-radius: 8px;
                    padding: 0.5em 1em; 
                    font-size: 12px; 
                    margin-top: 1.5em; 
                    margin-left: auto; 
                    margin-right: auto; 
                    display: block; 
                }
                
                ' . $addon_id . ' form {
                    width: 100%;
                    box-sizing: border-box;
                }
                ' . $addon_id . ' .tab-content2 {
                    position: relative;
                    background-color: #E2FAE1; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 135px; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15); 
                    margin-top: -12px;
                }
            
                ' . $addon_id . ' .tab-content2 .img-container2 img {
                    width: 486px;
                    height: auto;
                    max-width: 100%; 
                    border-radius: 10px;
                    box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
                    transition: all 0.3s ease-in-out;    
                }                   
            
                ' . $addon_id . ' .tab-content2:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #E2FAE1; 
                }
            
                ' . $addon_id . ' .tab-content2-1 {
                    position: relative;
                    background-color: #E2FAE1;
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15); 
                    margin-top: -12px;
                    width: 101%;
                }
            
                ' . $addon_id . ' .tab-content2-1:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #E2FAE1; 
                }
            
                
                ' . $addon_id . ' .tab-content3 .button-container {
                    position: absolute; 
                    top: 110%; 
                    left: 50%;
                    transform: translate(-50%, -50%); 
                    z-index: 1; 
                }
                ' . $addon_id . ' .tab-content3 {
                    position: relative;
                    background-color: #F1E8FC; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important;
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    width: 100%;
                }
            
                ' . $addon_id . ' #section3.tab-content3.empty-packs {
                    padding: 10px 168px;  
                }                    
            
                ' . $addon_id . ' .tab-content3 .img-container2 {
                    width: 100%; 
                    height: 100%; 
                    position: relative; 
                }
            
                ' . $addon_id . ' .tab-content3 .img-wrapper {
                    position: relative;
                    width: 100%;
                    height: 100%;
                    overflow: hidden; 
                }
            
                ' . $addon_id . ' .tab-content3 .img-wrapper img {
                    width: 100%; 
                    height: auto; 
                    border-radius: 10px;
                    box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
                    transition: all 0.3s ease-in-out;
                }
            
                ' . $addon_id . ' .tab-content3:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #F1E8FC; 
                }
            
                ' . $addon_id . ' .tab-content3 input[type="text"],
                ' . $addon_id . ' .tab-content3 select {
                    width: 80%; 
                    max-width: 80%; 
                    padding: 5px; 
                    box-sizing: border-box;
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content3-1 {
                    position: relative;
                    background-color: #F1E8FC; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    width: 101%;
                }
            
                ' . $addon_id . ' #section3.tab-content3-1.empty-packs {
                    padding: 10px 10px !important;   
                }                    
            
                ' . $addon_id . ' .tab-content3-1 .img-container2 {
                    width: 100%; 
                    height: 100%; 
                    position: relative; 
                }
            
                ' . $addon_id . ' .tab-content3-1 .img-wrapper {
                    position: relative;
                    width: 100%;
                    height: 100%;
                    overflow: hidden; 
                }
            
                ' . $addon_id . ' .tab-content3-1 .img-wrapper img {
                    width: 100%; 
                    height: auto; 
                    border-radius: 10px;
                    box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
                    transition: all 0.3s ease-in-out;
                }
            
                ' . $addon_id . ' .tab-content3-1 .button-container {
                    position: absolute; 
                    top: 110%; 
                    left: 29%; 
                    transform: translate(-50%, -50%); 
                    z-index: 1; 
                }
            
                ' . $addon_id . ' .tab-content3-1:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #F1E8FC; 
                }
            
                ' . $addon_id . ' .tab-content3-1 input[type="text"],
                ' . $addon_id . ' .tab-content3-1 select {
                    width: 80%; 
                    max-width: 80%; 
                    padding: 5px; 
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content4 {
                    position: relative;
                    background-color: #FEDFF0; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    width: 101%;
                }
            
                ' . $addon_id . ' .tab-content4:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FEDFF0;
                }
                ' . $addon_id . ' .tab-content4 input[type="text"],
                ' . $addon_id . ' .tab-content4 select,
                ' . $addon_id . ' .tab-content4 input[type="date"],
                ' . $addon_id . ' .tab-content4 input[type="file"] {
                    width: 480px;
                    max-width: 100%;
                    padding: 5px;
                    box-sizing: border-box;
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content4 input[type="file"] {
                    font-size: 12px;
                }
                ' . $addon_id . ' .tab-content4-4 {
                    position: relative;
                    background-color: #FEDFF0; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    width: 101%;
                }

                ' . $addon_id . ' .tab-content4-4:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FEDFF0; 
                }
                ' . $addon_id . ' .tab-content4-4 input[type="text"],
                ' . $addon_id . ' .tab-content4-4 select,
                ' . $addon_id . ' .tab-content4-4 input[type="date"] {
                    width: 100%;
                    max-width: 100px;
                    padding: 5px; 
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content4-4 input[type="file"] {
                    font-size: 12px;
                }
                ' . $addon_id . ' .tab-content4-2 {
                    position: relative;
                    background-color: #FEDFF0; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    width: 101%;
                }

                ' . $addon_id . ' .tab-content4-2:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FEDFF0;
                }
                ' . $addon_id . ' .tab-content4-2 input[type="text"],
                ' . $addon_id . ' .tab-content4-2 select{
                    width: 100%; 
                    max-width: 100%; 
                    padding: 5px;
                    box-sizing: border-box;
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content4-2 input[type="file"] {
                    font-size: 12px;
                }
                ' . $addon_id . ' .tab-content4-3 {
                    position: relative;
                    background-color: #FEDFF0; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    width: 101%;
                }
                ' . $addon_id . ' .tab-content4-3:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FEDFF0; 
                }
                ' . $addon_id . ' .tab-content4-3 input[type="text"],
                ' . $addon_id . ' .tab-content4-3 select,
                ' . $addon_id . ' .tab-content4-3 input[type="date"] {
                    width: 100%; 
                    max-width: 100%; 
                    padding: 5px; 
                    box-sizing: border-box;
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content4-3 input[type="file"] {
                    font-size: 12px;
                }
                ' . $addon_id . ' .tab-content4-1 {
                    position: relative;
                    background-color: #FEDFF0; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    width: 101%;
                }

                ' . $addon_id . ' .tab-content4-1:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FEDFF0;
                }
                ' . $addon_id . ' .tab-content4-1 input[type="text"],
                ' . $addon_id . ' .tab-content4-1 select,
                ' . $addon_id . ' .tab-content4-1 input[type="date"] {
                    width: 100%;
                    max-width: 100%;
                    padding: 5px;
                    box-sizing: border-box;
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content4-1 input[type="file"] {
                    font-size: 12px;
                }
                ' . $addon_id . ' .tab-content4-5 {
                    position: relative;
                    background-color: #FEDFF0; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    width: 101%;
                }

                ' . $addon_id . ' .tab-content4-5:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FEDFF0;
                }
                ' . $addon_id . ' .tab-content4-5 input[type="text"],
                ' . $addon_id . ' .tab-content4-5 select,
                ' . $addon_id . ' .tab-content4-5 input[type="date"] {
                    width: 100%; 
                    max-width: 100%; 
                    padding: 5px;
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content4-5 input[type="file"] {
                    font-size: 12px;
                }
                ' . $addon_id . ' .tab-content4-2-1 {
                    position: relative;
                    background-color: #FEDFF0; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    width: 101%;
                }

                ' . $addon_id . ' .tab-content4-2-1:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FEDFF0;
                }
                ' . $addon_id . ' .tab-content4-2-1 input[type="text"],
                ' . $addon_id . ' .tab-content4-2-1 select{
                    width: 100%; 
                    max-width: 100%;
                    padding: 5px;
                    box-sizing: border-box;
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content4-3-1 {
                    position: relative;
                    background-color: #FEDFF0;
                    border-radius: 0 0 20px 20px;
                    padding: 10px 10px !important;
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc;
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    width: 100%; /* Reduced width */
                    
                }
                ' . $addon_id . ' .tab-content4-3-1 .img-container2 img {
                    max-width: 20px; 
                    max-height: 20px; 
                }

                ' . $addon_id . ' .tab-content4-3-1:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FEDFF0;
                }
                ' . $addon_id . ' .tab-content4-3-1 input[type="text"],
                ' . $addon_id . ' .tab-content4-3-1 select,
                ' . $addon_id . ' .tab-content4-3-1 input[type="date"] {
                    width: 50%; 
                    max-width: 50%;
                    padding: 5px;
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                    
                }
                ' . $addon_id . ' .tab-content4-4-1 {
                    position: relative;
                    background-color: #FEDFF0; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    width: 101%;
                }

                ' . $addon_id . ' .tab-content4-4-1:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FEDFF0;
                }
                ' . $addon_id . ' .tab-content4-4-1 input[type="text"],
                ' . $addon_id . ' .tab-content4-4-1 select,
                ' . $addon_id . ' .tab-content4-4-1 input[type="date"] {
                    width: 100%;
                    max-width: 100%;
                    padding: 5px;
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                    
                }
                ' . $addon_id . ' .tab-content4-5-1 {
                    position: relative;
                    background-color: #FEDFF0; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    width: 101%;
                }

                ' . $addon_id . ' .tab-content4-5-1:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FEDFF0; 
                }
                ' . $addon_id . ' .tab-content4-5-1 input[type="text"],
                ' . $addon_id . ' .tab-content4-5-1 select,
                ' . $addon_id . ' .tab-content4-5-1 input[type="date"] {
                    width: 100%; 
                    max-width: 100%; 
                    padding: 5px; 
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content4-1-1 {
                    position: relative;
                    background-color: #FEDFF0; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    width: 101%;
                }

                ' . $addon_id . ' .tab-content4-1-1:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FEDFF0;
                }
                ' . $addon_id . ' .tab-content4-1-1 input[type="text"],
                ' . $addon_id . ' .tab-content4-1-1 select,
                ' . $addon_id . ' .tab-content4-1-1 input[type="date"] {
                    width: 100%;
                    max-width: 100%;
                    padding: 5px;
                    box-sizing: border-box;
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content5 {
                    position: relative;
                    background-color: #ffd8b2; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important;
                    width: 101%;
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                }

                ' . $addon_id . ' .tab-content5:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #ffd8b2;
                }
                ' . $addon_id . ' .tab-content5 input[type="text"],
                ' . $addon_id . ' .tab-content5 select{
                    width: 100%; 
                    max-width: 100%; 
                    padding: 5px;
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content5 input[type="date"] {
                    width: 100%; 
                    max-width: 100%; 
                    padding: 5px;
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                }
                
                ' . $addon_id . ' .tab-content5-1 {
                    position: relative;
                    background-color: #ffd8b2; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important;
                    width: 101%;
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                }

                ' . $addon_id . ' .tab-content5-1:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #ffd8b2;
                }
                ' . $addon_id . ' .tab-content5-1 input[type="text"],
                ' . $addon_id . ' .tab-content5-1 select{
                    width: 100%; 
                    max-width: 100%; 
                    padding: 5px;
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content5-1 input[type="date"] {
                    width: 100%; 
                    max-width: 100%; 
                    padding: 5px;
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                }
                
                
                ' . $addon_id . ' .tab-content6 {
                    position: relative;
                    background-color: #FFF5BA; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important;
                    width: 101%;
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                }
    
                ' . $addon_id . ' .tab-content6:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FFF5BA;
                }
                ' . $addon_id . ' .tab-content6 input[type="text"],
                ' . $addon_id . ' .tab-content6 select,
                ' . $addon_id . ' .tab-content6 input[type="date"],
                ' . $addon_id . ' .tab-content6 input[type="number"],
                ' . $addon_id . ' .tab-content6 textarea {
                    width: 100%; 
                    max-width: 100%; 
                    padding: 5px;
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content6-1 {
                    position: relative;
                    background-color: #FFF5BA; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important;
                    width: 101%; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                }
    
                ' . $addon_id . ' .tab-content6-1:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FFF5BA;
                }
                ' . $addon_id . ' .tab-content6-1 input[type="text"],
                ' . $addon_id . ' .tab-content6-1 select,
                ' . $addon_id . ' .tab-content6-1 input[type="date"],
                ' . $addon_id . ' .tab-content6-1 input[type="number"],
                ' . $addon_id . ' .tab-content6-1 textarea {
                    width: 100%; 
                    max-width: 100%; 
                    padding: 5px;
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content6-1-1 {
                    position: relative;
                    background-color: #FFF5BA; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important;
                    width: 101%;
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                }
    
                ' . $addon_id . ' .tab-content6-1-1:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FFF5BA;
                }
                ' . $addon_id . ' .tab-content6-1-1 input[type="text"],
                ' . $addon_id . ' .tab-content6-1-1 select,
                ' . $addon_id . ' .tab-content6-1-1 input[type="date"],
                ' . $addon_id . ' .tab-content6-1-1 input[type="number"],
                ' . $addon_id . ' .tab-content6-1-1 textarea {
                    width: 100%; 
                    max-width: 100%; 
                    padding: 5px;
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                }
            }
            ';
        $css .= '
            @media (max-width: 430px) {
                ' . $addon_id . ' .sppb-addon {
                    max-width: calc(100% - 20px);
                    padding: 10px;
                }
                ' . $addon_id . ' input[type="file"] {
                    font-size: 12px;
                }
            
                ' . $addon_id . ' .tab {
                    padding: 6px 6px; 
                    max-width: calc(100% - 10px); 
                    overflow-x: auto;
                    font-size: 10px;
                }

                ' . $addon_id . ' .tab  span {
                    font-size: 5px;
                }
            
                ' . $addon_id . ' .tab-content {
                    position: relative;
                    background-color: #DBE9FF; 
                    border-radius: 0 0 20px 20px;
                    padding: 10px 105px;
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    display: flex; 
                    justify-content: center;
                    align-items: center; 
                }
            
                ' . $addon_id . ' .tab-content-1 {
                    position: relative;
                    background-color: #DBE9FF; 
                    border-radius: 0 0 20px 20px;
                    padding: 10px 20px !important; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    width: 101%;
                }
                
                ' . $addon_id . ' .container {
                    display: flex;
                    flex-direction: column;
                    justify-content: center;
                    align-items: center; 
                    flex-wrap: wrap;
                }
               
                
                ' . $addon_id . ' .invoice-number {
                    font-size: 14px; 
                    margin-top: 5px; 
                }
                
                ' . $addon_id . ' .button-group button {
                    font-size: 10px; 
                    margin-left: 2px;
                }
        
                ' . $addon_id . ' .form-group {
                    margin-bottom: 1rem; 
                    flex-direction: column; 
                    align-items: center;
                    justify-content: center;
                }
        
               
                ' . $addon_id . ' .status-text {
                    font-size: 10px;
                    margin-top: 0.2em; 
                }
        
                
                ' . $addon_id . ' .form-control,
                ' . $addon_id . ' #document_upload,
                ' . $addon_id . ' select {
                    border-radius: 8px;
                    border: 2px solid #3498db;
                    font-size: 12px; 
                    margin-bottom: 0.5em;
                    box-sizing: border-box;
                    width: calc(100% - 2em); 
                    padding: 0.3em; 
                }

                ' . $addon_id . ' .form-group {
                    display: flex;
                    flex-direction: column;
                    justify-content: center;
                    align-items: center;
                    width: 100%;
                }
                ' . $addon_id . ' .form-group .btn,
                ' . $addon_id . ' .form-group .btn-danger,
                ' . $addon_id . ' .form-group .btn-primary,
                ' . $addon_id . ' .form-group .btn-warning {
                    border-radius: 8px;
                    padding: 0.5em 1em; 
                    font-size: 12px; 
                    margin-top: 1.5em; 
                    margin-left: auto; 
                    margin-right: auto; 
                    display: block; 
                }
                
                ' . $addon_id . ' form {
                    width: 100%;
                    box-sizing: border-box;
                }
                ' . $addon_id . ' .tab-content2 {
                    position: relative;
                    background-color: #E2FAE1; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 135px; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15); 
                    margin-top: -12px;
                }
            
                ' . $addon_id . ' .tab-content2 .img-container2 img {
                    width: 486px;
                    height: auto;
                    max-width: 100%; 
                    border-radius: 10px;
                    box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
                    transition: all 0.3s ease-in-out;    
                }                   
            
                ' . $addon_id . ' .tab-content2:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #E2FAE1; 
                }
            
                ' . $addon_id . ' .tab-content2-1 {
                    position: relative;
                    background-color: #E2FAE1;
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15); 
                    margin-top: -12px;
                    width: 101%;
                }
            
                ' . $addon_id . ' .tab-content2-1:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #E2FAE1; 
                }
            
                
                ' . $addon_id . ' .tab-content3 .button-container {
                    position: absolute; 
                    top: 110%; 
                    left: 50%;
                    transform: translate(-50%, -50%); 
                    z-index: 1; 
                }
                ' . $addon_id . ' .tab-content3 {
                    position: relative;
                    background-color: #F1E8FC; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important;
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    width: 100%;
                }
            
                ' . $addon_id . ' #section3.tab-content3.empty-packs {
                    padding: 10px 168px;  
                }                    
            
                ' . $addon_id . ' .tab-content3 .img-container2 {
                    width: 100%; 
                    height: 100%; 
                    position: relative; 
                }
            
                ' . $addon_id . ' .tab-content3 .img-wrapper {
                    position: relative;
                    width: 100%;
                    height: 100%;
                    overflow: hidden; 
                }
            
                ' . $addon_id . ' .tab-content3 .img-wrapper img {
                    width: 100%; 
                    height: auto; 
                    border-radius: 10px;
                    box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
                    transition: all 0.3s ease-in-out;
                }
            
                ' . $addon_id . ' .tab-content3:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #F1E8FC; 
                }
            
                ' . $addon_id . ' .tab-content3 input[type="text"],
                ' . $addon_id . ' .tab-content3 select {
                    width: 80%; 
                    max-width: 80%; 
                    padding: 5px; 
                    box-sizing: border-box;
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content3-1 {
                    position: relative;
                    background-color: #F1E8FC; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    width: 101%;
                }
            
                ' . $addon_id . ' #section3.tab-content3-1.empty-packs {
                    padding: 10px 10px !important;   
                }                    
            
                ' . $addon_id . ' .tab-content3-1 .img-container2 {
                    width: 100%; 
                    height: 100%; 
                    position: relative; 
                }
            
                ' . $addon_id . ' .tab-content3-1 .img-wrapper {
                    position: relative;
                    width: 100%;
                    height: 100%;
                    overflow: hidden; 
                }
            
                ' . $addon_id . ' .tab-content3-1 .img-wrapper img {
                    width: 100%; 
                    height: auto; 
                    border-radius: 10px;
                    box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
                    transition: all 0.3s ease-in-out;
                }
            
                ' . $addon_id . ' .tab-content3-1 .button-container {
                    position: absolute; 
                    top: 110%; 
                    left: 29%; 
                    transform: translate(-50%, -50%); 
                    z-index: 1; 
                }
            
                ' . $addon_id . ' .tab-content3-1:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #F1E8FC; 
                }
            
                ' . $addon_id . ' .tab-content3-1 input[type="text"],
                ' . $addon_id . ' .tab-content3-1 select {
                    width: 80%; 
                    max-width: 80%; 
                    padding: 5px; 
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content4 {
                    position: relative;
                    background-color: #FEDFF0; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    width: 101%;
                }
            
                ' . $addon_id . ' .tab-content4:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FEDFF0;
                }
                ' . $addon_id . ' .tab-content4 input[type="text"],
                ' . $addon_id . ' .tab-content4 select,
                ' . $addon_id . ' .tab-content4 input[type="date"],
                ' . $addon_id . ' .tab-content4 input[type="file"] {
                    width: 480px;
                    max-width: 100%;
                    padding: 5px;
                    box-sizing: border-box;
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content4 input[type="file"] {
                    font-size: 12px;
                }
                ' . $addon_id . ' .tab-content4-4 {
                    position: relative;
                    background-color: #FEDFF0; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    width: 101%;
                }

                ' . $addon_id . ' .tab-content4-4:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FEDFF0; 
                }
                ' . $addon_id . ' .tab-content4-4 input[type="text"],
                ' . $addon_id . ' .tab-content4-4 select,
                ' . $addon_id . ' .tab-content4-4 input[type="date"] {
                    width: 100%;
                    max-width: 100px;
                    padding: 5px; 
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content4-4 input[type="file"] {
                    font-size: 12px;
                }
                ' . $addon_id . ' .tab-content4-2 {
                    position: relative;
                    background-color: #FEDFF0; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    width: 101%;
                }

                ' . $addon_id . ' .tab-content4-2:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FEDFF0;
                }
                ' . $addon_id . ' .tab-content4-2 input[type="text"],
                ' . $addon_id . ' .tab-content4-2 select{
                    width: 100%; 
                    max-width: 100%; 
                    padding: 5px;
                    box-sizing: border-box;
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content4-2 input[type="file"] {
                    font-size: 12px;
                }
                ' . $addon_id . ' .tab-content4-3 {
                    position: relative;
                    background-color: #FEDFF0; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    width: 101%;
                }
                ' . $addon_id . ' .tab-content4-3:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FEDFF0; 
                }
                ' . $addon_id . ' .tab-content4-3 input[type="text"],
                ' . $addon_id . ' .tab-content4-3 select,
                ' . $addon_id . ' .tab-content4-3 input[type="date"] {
                    width: 100%; 
                    max-width: 100%; 
                    padding: 5px; 
                    box-sizing: border-box;
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content4-3 input[type="file"] {
                    font-size: 12px;
                }
                ' . $addon_id . ' .tab-content4-1 {
                    position: relative;
                    background-color: #FEDFF0; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    width: 101%;
                }

                ' . $addon_id . ' .tab-content4-1:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FEDFF0;
                }
                ' . $addon_id . ' .tab-content4-1 input[type="text"],
                ' . $addon_id . ' .tab-content4-1 select,
                ' . $addon_id . ' .tab-content4-1 input[type="date"] {
                    width: 100%;
                    max-width: 100%;
                    padding: 5px;
                    box-sizing: border-box;
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content4-1 input[type="file"] {
                    font-size: 12px;
                }
                ' . $addon_id . ' .tab-content4-5 {
                    position: relative;
                    background-color: #FEDFF0; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    width: 101%;
                }

                ' . $addon_id . ' .tab-content4-5:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FEDFF0;
                }
                ' . $addon_id . ' .tab-content4-5 input[type="text"],
                ' . $addon_id . ' .tab-content4-5 select,
                ' . $addon_id . ' .tab-content4-5 input[type="date"] {
                    width: 100%; 
                    max-width: 100%; 
                    padding: 5px;
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content4-5 input[type="file"] {
                    font-size: 12px;
                }
                ' . $addon_id . ' .tab-content4-2-1 {
                    position: relative;
                    background-color: #FEDFF0; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    width: 101%;
                }

                ' . $addon_id . ' .tab-content4-2-1:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FEDFF0;
                }
                ' . $addon_id . ' .tab-content4-2-1 input[type="text"],
                ' . $addon_id . ' .tab-content4-2-1 select{
                    width: 100%; 
                    max-width: 100%;
                    padding: 5px;
                    box-sizing: border-box;
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content4-3-1 {
                    position: relative;
                    background-color: #FEDFF0;
                    border-radius: 0 0 20px 20px;
                    padding: 10px 10px !important;
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc;
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    width: 100%; /* Reduced width */
                    
                }
                ' . $addon_id . ' .tab-content4-3-1 .img-container2 img {
                    max-width: 20px; 
                    max-height: 20px; 
                }

                ' . $addon_id . ' .tab-content4-3-1:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FEDFF0;
                }
                ' . $addon_id . ' .tab-content4-3-1 input[type="text"],
                ' . $addon_id . ' .tab-content4-3-1 select,
                ' . $addon_id . ' .tab-content4-3-1 input[type="date"] {
                    width: 50%; 
                    max-width: 50%;
                    padding: 5px;
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                    
                }
                ' . $addon_id . ' .tab-content4-4-1 {
                    position: relative;
                    background-color: #FEDFF0; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    width: 101%;
                }

                ' . $addon_id . ' .tab-content4-4-1:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FEDFF0;
                }
                ' . $addon_id . ' .tab-content4-4-1 input[type="text"],
                ' . $addon_id . ' .tab-content4-4-1 select,
                ' . $addon_id . ' .tab-content4-4-1 input[type="date"] {
                    width: 100%;
                    max-width: 100%;
                    padding: 5px;
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                    
                }
                ' . $addon_id . ' .tab-content4-5-1 {
                    position: relative;
                    background-color: #FEDFF0; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    width: 101%;
                }

                ' . $addon_id . ' .tab-content4-5-1:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FEDFF0; 
                }
                ' . $addon_id . ' .tab-content4-5-1 input[type="text"],
                ' . $addon_id . ' .tab-content4-5-1 select,
                ' . $addon_id . ' .tab-content4-5-1 input[type="date"] {
                    width: 100%; 
                    max-width: 100%; 
                    padding: 5px; 
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content4-1-1 {
                    position: relative;
                    background-color: #FEDFF0; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    width: 101%;
                }

                ' . $addon_id . ' .tab-content4-1-1:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FEDFF0;
                }
                ' . $addon_id . ' .tab-content4-1-1 input[type="text"],
                ' . $addon_id . ' .tab-content4-1-1 select,
                ' . $addon_id . ' .tab-content4-1-1 input[type="date"] {
                    width: 100%;
                    max-width: 100%;
                    padding: 5px;
                    box-sizing: border-box;
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content5 {
                    position: relative;
                    background-color: #ffd8b2; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important;
                    width: 101%;
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                }

                ' . $addon_id . ' .tab-content5:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #ffd8b2;
                }
                ' . $addon_id . ' .tab-content5 input[type="text"],
                ' . $addon_id . ' .tab-content5 select{
                    width: 100%; 
                    max-width: 100%; 
                    padding: 5px;
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content5 input[type="date"] {
                    width: 100%; 
                    max-width: 100%; 
                    padding: 5px;
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                }
                
                ' . $addon_id . ' .tab-content5-1 {
                    position: relative;
                    background-color: #ffd8b2; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important;
                    width: 101%;
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                }

                ' . $addon_id . ' .tab-content5-1:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #ffd8b2;
                }
                ' . $addon_id . ' .tab-content5-1 input[type="text"],
                ' . $addon_id . ' .tab-content5-1 select{
                    width: 100%; 
                    max-width: 100%; 
                    padding: 5px;
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content5-1 input[type="date"] {
                    width: 100%; 
                    max-width: 100%; 
                    padding: 5px;
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                }
                
                
                ' . $addon_id . ' .tab-content6 {
                    position: relative;
                    background-color: #FFF5BA; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important;
                    width: 101%;
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                }
    
                ' . $addon_id . ' .tab-content6:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FFF5BA;
                }
                ' . $addon_id . ' .tab-content6 input[type="text"],
                ' . $addon_id . ' .tab-content6 select,
                ' . $addon_id . ' .tab-content6 input[type="date"],
                ' . $addon_id . ' .tab-content6 input[type="number"],
                ' . $addon_id . ' .tab-content6 textarea {
                    width: 100%; 
                    max-width: 100%; 
                    padding: 5px;
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content6-1 {
                    position: relative;
                    background-color: #FFF5BA; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important;
                    width: 101%; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                }
    
                ' . $addon_id . ' .tab-content6-1:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FFF5BA;
                }
                ' . $addon_id . ' .tab-content6-1 input[type="text"],
                ' . $addon_id . ' .tab-content6-1 select,
                ' . $addon_id . ' .tab-content6-1 input[type="date"],
                ' . $addon_id . ' .tab-content6-1 input[type="number"],
                ' . $addon_id . ' .tab-content6-1 textarea {
                    width: 100%; 
                    max-width: 100%; 
                    padding: 5px;
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content6-1-1 {
                    position: relative;
                    background-color: #FFF5BA; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important;
                    width: 101%;
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                }
    
                ' . $addon_id . ' .tab-content6-1-1:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FFF5BA;
                }
                ' . $addon_id . ' .tab-content6-1-1 input[type="text"],
                ' . $addon_id . ' .tab-content6-1-1 select,
                ' . $addon_id . ' .tab-content6-1-1 input[type="date"],
                ' . $addon_id . ' .tab-content6-1-1 input[type="number"],
                ' . $addon_id . ' .tab-content6-1-1 textarea {
                    width: 100%; 
                    max-width: 100%; 
                    padding: 5px;
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                }
            }
            ';
        $css .= '
            @media (max-width: 415px) {
                ' . $addon_id . ' .sppb-addon {
                    max-width: calc(100% - 20px);
                    padding: 10px;
                }
                ' . $addon_id . ' input[type="file"] {
                    font-size: 12px;
                }
            
                ' . $addon_id . ' .tab {
                    padding: 12px 12px; 
                    max-width: calc(100% - 10px); 
                    overflow-x: auto;
                    font-size: 10px;
                }

                ' . $addon_id . ' .tab  span {
                    font-size: 5px;
                }
            
                ' . $addon_id . ' .tab-content {
                    position: relative;
                    background-color: #DBE9FF; 
                    border-radius: 0 0 20px 20px;
                    padding: 10px 105px;
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    display: flex; 
                    justify-content: center;
                    align-items: center; 
                }
            
                ' . $addon_id . ' .tab-content-1 {
                    position: relative;
                    background-color: #DBE9FF; 
                    border-radius: 0 0 20px 20px;
                    padding: 10px 20px !important; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    width: 101%;
                }
                
                ' . $addon_id . ' .container {
                    display: flex;
                    flex-direction: column;
                    justify-content: center;
                    align-items: center; 
                    flex-wrap: wrap;
                }
               
                
                ' . $addon_id . ' .invoice-number {
                    font-size: 14px; 
                    margin-top: 5px; 
                }
                
                ' . $addon_id . ' .button-group button {
                    font-size: 10px; 
                    margin-left: 2px;
                }
        
                ' . $addon_id . ' .form-group {
                    margin-bottom: 1rem; 
                    flex-direction: column; 
                    align-items: center;
                    justify-content: center;
                }
        
               
                ' . $addon_id . ' .status-text {
                    font-size: 10px;
                    margin-top: 0.2em; 
                }
        
                
                ' . $addon_id . ' .form-control,
                ' . $addon_id . ' #document_upload,
                ' . $addon_id . ' select {
                    border-radius: 8px;
                    border: 2px solid #3498db;
                    font-size: 12px; 
                    margin-bottom: 0.5em;
                    box-sizing: border-box;
                    width: calc(100% - 2em); 
                    padding: 0.3em; 
                }

                ' . $addon_id . ' .form-group {
                    display: flex;
                    flex-direction: column;
                    justify-content: center;
                    align-items: center;
                    width: 100%;
                }
                ' . $addon_id . ' .form-group .btn,
                ' . $addon_id . ' .form-group .btn-danger,
                ' . $addon_id . ' .form-group .btn-primary,
                ' . $addon_id . ' .form-group .btn-warning {
                    border-radius: 8px;
                    padding: 0.5em 1em; 
                    font-size: 12px; 
                    margin-top: 1.5em; 
                    margin-left: auto; 
                    margin-right: auto; 
                    display: block; 
                }
                
                ' . $addon_id . ' form {
                    width: 100%;
                    box-sizing: border-box;
                }
                ' . $addon_id . ' .tab-content2 {
                    position: relative;
                    background-color: #E2FAE1; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 135px; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15); 
                    margin-top: -12px;
                }
            
                ' . $addon_id . ' .tab-content2 .img-container2 img {
                    width: 486px;
                    height: auto;
                    max-width: 100%; 
                    border-radius: 10px;
                    box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
                    transition: all 0.3s ease-in-out;    
                }                   
            
                ' . $addon_id . ' .tab-content2:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #E2FAE1; 
                }
            
                ' . $addon_id . ' .tab-content2-1 {
                    position: relative;
                    background-color: #E2FAE1;
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15); 
                    margin-top: -12px;
                    width: 101%;
                }
            
                ' . $addon_id . ' .tab-content2-1:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #E2FAE1; 
                }
            
                
                ' . $addon_id . ' .tab-content3 .button-container {
                    position: absolute; 
                    top: 110%; 
                    left: 50%;
                    transform: translate(-50%, -50%); 
                    z-index: 1; 
                }
                ' . $addon_id . ' .tab-content3 {
                    position: relative;
                    background-color: #F1E8FC; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important;
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    width: 100%;
                }
            
                ' . $addon_id . ' #section3.tab-content3.empty-packs {
                    padding: 10px 168px;  
                }                    
            
                ' . $addon_id . ' .tab-content3 .img-container2 {
                    width: 100%; 
                    height: 100%; 
                    position: relative; 
                }
            
                ' . $addon_id . ' .tab-content3 .img-wrapper {
                    position: relative;
                    width: 100%;
                    height: 100%;
                    overflow: hidden; 
                }
            
                ' . $addon_id . ' .tab-content3 .img-wrapper img {
                    width: 100%; 
                    height: auto; 
                    border-radius: 10px;
                    box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
                    transition: all 0.3s ease-in-out;
                }
            
                ' . $addon_id . ' .tab-content3:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #F1E8FC; 
                }
            
                ' . $addon_id . ' .tab-content3 input[type="text"],
                ' . $addon_id . ' .tab-content3 select {
                    width: 80%; 
                    max-width: 80%; 
                    padding: 5px; 
                    box-sizing: border-box;
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content3-1 {
                    position: relative;
                    background-color: #F1E8FC; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    width: 101%;
                }
            
                ' . $addon_id . ' #section3.tab-content3-1.empty-packs {
                    padding: 10px 10px !important;   
                }                    
            
                ' . $addon_id . ' .tab-content3-1 .img-container2 {
                    width: 100%; 
                    height: 100%; 
                    position: relative; 
                }
            
                ' . $addon_id . ' .tab-content3-1 .img-wrapper {
                    position: relative;
                    width: 100%;
                    height: 100%;
                    overflow: hidden; 
                }
            
                ' . $addon_id . ' .tab-content3-1 .img-wrapper img {
                    width: 100%; 
                    height: auto; 
                    border-radius: 10px;
                    box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
                    transition: all 0.3s ease-in-out;
                }
            
                ' . $addon_id . ' .tab-content3-1 .button-container {
                    position: absolute; 
                    top: 110%; 
                    left: 29%; 
                    transform: translate(-50%, -50%); 
                    z-index: 1; 
                }
            
                ' . $addon_id . ' .tab-content3-1:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #F1E8FC; 
                }
            
                ' . $addon_id . ' .tab-content3-1 input[type="text"],
                ' . $addon_id . ' .tab-content3-1 select {
                    width: 80%; 
                    max-width: 80%; 
                    padding: 5px; 
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content4 {
                    position: relative;
                    background-color: #FEDFF0; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    width: 101%;
                }
            
                ' . $addon_id . ' .tab-content4:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FEDFF0;
                }
                ' . $addon_id . ' .tab-content4 input[type="text"],
                ' . $addon_id . ' .tab-content4 select,
                ' . $addon_id . ' .tab-content4 input[type="date"],
                ' . $addon_id . ' .tab-content4 input[type="file"] {
                    width: 480px;
                    max-width: 100%;
                    padding: 5px;
                    box-sizing: border-box;
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content4 input[type="file"] {
                    font-size: 12px;
                }
                ' . $addon_id . ' .tab-content4-4 {
                    position: relative;
                    background-color: #FEDFF0; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    width: 101%;
                }

                ' . $addon_id . ' .tab-content4-4:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FEDFF0; 
                }
                ' . $addon_id . ' .tab-content4-4 input[type="text"],
                ' . $addon_id . ' .tab-content4-4 select,
                ' . $addon_id . ' .tab-content4-4 input[type="date"] {
                    width: 100%;
                    max-width: 100px;
                    padding: 5px; 
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content4-4 input[type="file"] {
                    font-size: 12px;
                }
                ' . $addon_id . ' .tab-content4-2 {
                    position: relative;
                    background-color: #FEDFF0; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    width: 101%;
                }

                ' . $addon_id . ' .tab-content4-2:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FEDFF0;
                }
                ' . $addon_id . ' .tab-content4-2 input[type="text"],
                ' . $addon_id . ' .tab-content4-2 select{
                    width: 100%; 
                    max-width: 100%; 
                    padding: 5px;
                    box-sizing: border-box;
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content4-2 input[type="file"] {
                    font-size: 12px;
                }
                ' . $addon_id . ' .tab-content4-3 {
                    position: relative;
                    background-color: #FEDFF0; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    width: 101%;
                }
                ' . $addon_id . ' .tab-content4-3:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FEDFF0; 
                }
                ' . $addon_id . ' .tab-content4-3 input[type="text"],
                ' . $addon_id . ' .tab-content4-3 select,
                ' . $addon_id . ' .tab-content4-3 input[type="date"] {
                    width: 100%; 
                    max-width: 100%; 
                    padding: 5px; 
                    box-sizing: border-box;
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content4-3 input[type="file"] {
                    font-size: 12px;
                }
                ' . $addon_id . ' .tab-content4-1 {
                    position: relative;
                    background-color: #FEDFF0; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    width: 101%;
                }

                ' . $addon_id . ' .tab-content4-1:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FEDFF0;
                }
                ' . $addon_id . ' .tab-content4-1 input[type="text"],
                ' . $addon_id . ' .tab-content4-1 select,
                ' . $addon_id . ' .tab-content4-1 input[type="date"] {
                    width: 100%;
                    max-width: 100%;
                    padding: 5px;
                    box-sizing: border-box;
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content4-1 input[type="file"] {
                    font-size: 12px;
                }
                ' . $addon_id . ' .tab-content4-5 {
                    position: relative;
                    background-color: #FEDFF0; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    width: 101%;
                }

                ' . $addon_id . ' .tab-content4-5:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FEDFF0;
                }
                ' . $addon_id . ' .tab-content4-5 input[type="text"],
                ' . $addon_id . ' .tab-content4-5 select,
                ' . $addon_id . ' .tab-content4-5 input[type="date"] {
                    width: 100%; 
                    max-width: 100%; 
                    padding: 5px;
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content4-5 input[type="file"] {
                    font-size: 12px;
                }
                ' . $addon_id . ' .tab-content4-2-1 {
                    position: relative;
                    background-color: #FEDFF0; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    width: 101%;
                }

                ' . $addon_id . ' .tab-content4-2-1:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FEDFF0;
                }
                ' . $addon_id . ' .tab-content4-2-1 input[type="text"],
                ' . $addon_id . ' .tab-content4-2-1 select{
                    width: 100%; 
                    max-width: 100%;
                    padding: 5px;
                    box-sizing: border-box;
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content4-3-1 {
                    position: relative;
                    background-color: #FEDFF0;
                    border-radius: 0 0 20px 20px;
                    padding: 10px 10px !important;
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc;
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    width: 100%; /* Reduced width */
                    
                }
                ' . $addon_id . ' .tab-content4-3-1 .img-container2 img {
                    max-width: 20px; 
                    max-height: 20px; 
                }

                ' . $addon_id . ' .tab-content4-3-1:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FEDFF0;
                }
                ' . $addon_id . ' .tab-content4-3-1 input[type="text"],
                ' . $addon_id . ' .tab-content4-3-1 select,
                ' . $addon_id . ' .tab-content4-3-1 input[type="date"] {
                    width: 50%; 
                    max-width: 50%;
                    padding: 5px;
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                    
                }
                ' . $addon_id . ' .tab-content4-4-1 {
                    position: relative;
                    background-color: #FEDFF0; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    width: 101%;
                }

                ' . $addon_id . ' .tab-content4-4-1:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FEDFF0;
                }
                ' . $addon_id . ' .tab-content4-4-1 input[type="text"],
                ' . $addon_id . ' .tab-content4-4-1 select,
                ' . $addon_id . ' .tab-content4-4-1 input[type="date"] {
                    width: 100%;
                    max-width: 100%;
                    padding: 5px;
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                    
                }
                ' . $addon_id . ' .tab-content4-5-1 {
                    position: relative;
                    background-color: #FEDFF0; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    width: 101%;
                }

                ' . $addon_id . ' .tab-content4-5-1:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FEDFF0; 
                }
                ' . $addon_id . ' .tab-content4-5-1 input[type="text"],
                ' . $addon_id . ' .tab-content4-5-1 select,
                ' . $addon_id . ' .tab-content4-5-1 input[type="date"] {
                    width: 100%; 
                    max-width: 100%; 
                    padding: 5px; 
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content4-1-1 {
                    position: relative;
                    background-color: #FEDFF0; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    width: 101%;
                }

                ' . $addon_id . ' .tab-content4-1-1:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FEDFF0;
                }
                ' . $addon_id . ' .tab-content4-1-1 input[type="text"],
                ' . $addon_id . ' .tab-content4-1-1 select,
                ' . $addon_id . ' .tab-content4-1-1 input[type="date"] {
                    width: 100%;
                    max-width: 100%;
                    padding: 5px;
                    box-sizing: border-box;
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content5 {
                    position: relative;
                    background-color: #ffd8b2; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important;
                    width: 101%;
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                }

                ' . $addon_id . ' .tab-content5:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #ffd8b2;
                }
                ' . $addon_id . ' .tab-content5 input[type="text"],
                ' . $addon_id . ' .tab-content5 select{
                    width: 100%; 
                    max-width: 100%; 
                    padding: 5px;
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content5 input[type="date"] {
                    width: 100%; 
                    max-width: 100%; 
                    padding: 5px;
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                }
                
                ' . $addon_id . ' .tab-content5-1 {
                    position: relative;
                    background-color: #ffd8b2; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important;
                    width: 101%;
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                }

                ' . $addon_id . ' .tab-content5-1:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #ffd8b2;
                }
                ' . $addon_id . ' .tab-content5-1 input[type="text"],
                ' . $addon_id . ' .tab-content5-1 select{
                    width: 100%; 
                    max-width: 100%; 
                    padding: 5px;
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content5-1 input[type="date"] {
                    width: 100%; 
                    max-width: 100%; 
                    padding: 5px;
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                }
                
                
                ' . $addon_id . ' .tab-content6 {
                    position: relative;
                    background-color: #FFF5BA; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important;
                    width: 101%;
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                }
    
                ' . $addon_id . ' .tab-content6:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FFF5BA;
                }
                ' . $addon_id . ' .tab-content6 input[type="text"],
                ' . $addon_id . ' .tab-content6 select,
                ' . $addon_id . ' .tab-content6 input[type="date"],
                ' . $addon_id . ' .tab-content6 input[type="number"],
                ' . $addon_id . ' .tab-content6 textarea {
                    width: 100%; 
                    max-width: 100%; 
                    padding: 5px;
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content6-1 {
                    position: relative;
                    background-color: #FFF5BA; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important;
                    width: 101%; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                }
    
                ' . $addon_id . ' .tab-content6-1:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FFF5BA;
                }
                ' . $addon_id . ' .tab-content6-1 input[type="text"],
                ' . $addon_id . ' .tab-content6-1 select,
                ' . $addon_id . ' .tab-content6-1 input[type="date"],
                ' . $addon_id . ' .tab-content6-1 input[type="number"],
                ' . $addon_id . ' .tab-content6-1 textarea {
                    width: 100%; 
                    max-width: 100%; 
                    padding: 5px;
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content6-1-1 {
                    position: relative;
                    background-color: #FFF5BA; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important;
                    width: 101%;
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                }
    
                ' . $addon_id . ' .tab-content6-1-1:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FFF5BA;
                }
                ' . $addon_id . ' .tab-content6-1-1 input[type="text"],
                ' . $addon_id . ' .tab-content6-1-1 select,
                ' . $addon_id . ' .tab-content6-1-1 input[type="date"],
                ' . $addon_id . ' .tab-content6-1-1 input[type="number"],
                ' . $addon_id . ' .tab-content6-1-1 textarea {
                    width: 100%; 
                    max-width: 100%; 
                    padding: 5px;
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                }
            }
            ';

        $css .= '
            @media (max-width: 400px) {
                ' . $addon_id . ' .sppb-addon {
                    max-width: calc(100% - 20px);
                    padding: 10px;
                }
                ' . $addon_id . ' input[type="file"] {
                    font-size: 12px;
                }
            
                ' . $addon_id . ' .tab {
                    padding: 10px 10px; 
                    max-width: calc(100% - 10px); 
                    overflow-x: auto;
                    font-size: 9px;
                }

                ' . $addon_id . ' .tab  span {
                    font-size: 5px;
                }
            
                ' . $addon_id . ' .tab-content {
                    position: relative;
                    background-color: #DBE9FF; 
                    border-radius: 0 0 20px 20px;
                    padding: 10px 105px;
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    display: flex; 
                    justify-content: center;
                    align-items: center; 
                }
            
                ' . $addon_id . ' .tab-content-1 {
                    position: relative;
                    background-color: #DBE9FF; 
                    border-radius: 0 0 20px 20px;
                    padding: 10px 20px !important; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    width: 101%;
                }
                
                ' . $addon_id . ' .container {
                    display: flex;
                    flex-direction: column;
                    justify-content: center;
                    align-items: center; 
                    flex-wrap: wrap;
                }
               
                
                ' . $addon_id . ' .invoice-number {
                    font-size: 14px; 
                    margin-top: 5px; 
                }
                
                ' . $addon_id . ' .button-group button {
                    font-size: 10px; 
                    margin-left: 2px;
                }
        
                ' . $addon_id . ' .form-group {
                    margin-bottom: 1rem; 
                    flex-direction: column; 
                    align-items: center;
                    justify-content: center;
                }
        
               
                ' . $addon_id . ' .status-text {
                    font-size: 10px;
                    margin-top: 0.2em; 
                }
        
                
                ' . $addon_id . ' .form-control,
                ' . $addon_id . ' #document_upload,
                ' . $addon_id . ' select {
                    border-radius: 8px;
                    border: 2px solid #3498db;
                    font-size: 12px; 
                    margin-bottom: 0.5em;
                    box-sizing: border-box;
                    width: calc(100% - 2em); 
                    padding: 0.3em; 
                }

                ' . $addon_id . ' .form-group {
                    display: flex;
                    flex-direction: column;
                    justify-content: center;
                    align-items: center;
                    width: 100%;
                }
                ' . $addon_id . ' .form-group .btn,
                ' . $addon_id . ' .form-group .btn-danger,
                ' . $addon_id . ' .form-group .btn-primary,
                ' . $addon_id . ' .form-group .btn-warning {
                    border-radius: 8px;
                    padding: 0.5em 1em; 
                    font-size: 12px; 
                    margin-top: 1.5em; 
                    margin-left: auto; 
                    margin-right: auto; 
                    display: block; 
                }
                
                ' . $addon_id . ' form {
                    width: 100%;
                    box-sizing: border-box;
                }
                ' . $addon_id . ' .tab-content2 {
                    position: relative;
                    background-color: #E2FAE1; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 135px; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15); 
                    margin-top: -12px;
                }
            
                ' . $addon_id . ' .tab-content2 .img-container2 img {
                    width: 486px;
                    height: auto;
                    max-width: 100%; 
                    border-radius: 10px;
                    box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
                    transition: all 0.3s ease-in-out;    
                }                   
            
                ' . $addon_id . ' .tab-content2:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #E2FAE1; 
                }
            
                ' . $addon_id . ' .tab-content2-1 {
                    position: relative;
                    background-color: #E2FAE1;
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15); 
                    margin-top: -12px;
                    width: 101%;
                }
            
                ' . $addon_id . ' .tab-content2-1:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #E2FAE1; 
                }
            
                
                ' . $addon_id . ' .tab-content3 .button-container {
                    position: absolute; 
                    top: 110%; 
                    left: 50%;
                    transform: translate(-50%, -50%); 
                    z-index: 1; 
                }
                ' . $addon_id . ' .tab-content3 {
                    position: relative;
                    background-color: #F1E8FC; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important;
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    width: 100%;
                }
            
                ' . $addon_id . ' #section3.tab-content3.empty-packs {
                    padding: 10px 168px;  
                }                    
            
                ' . $addon_id . ' .tab-content3 .img-container2 {
                    width: 100%; 
                    height: 100%; 
                    position: relative; 
                }
            
                ' . $addon_id . ' .tab-content3 .img-wrapper {
                    position: relative;
                    width: 100%;
                    height: 100%;
                    overflow: hidden; 
                }
            
                ' . $addon_id . ' .tab-content3 .img-wrapper img {
                    width: 100%; 
                    height: auto; 
                    border-radius: 10px;
                    box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
                    transition: all 0.3s ease-in-out;
                }
            
                ' . $addon_id . ' .tab-content3:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #F1E8FC; 
                }
            
                ' . $addon_id . ' .tab-content3 input[type="text"],
                ' . $addon_id . ' .tab-content3 select {
                    width: 80%; 
                    max-width: 80%; 
                    padding: 5px; 
                    box-sizing: border-box;
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content3-1 {
                    position: relative;
                    background-color: #F1E8FC; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    width: 101%;
                }
            
                ' . $addon_id . ' #section3.tab-content3-1.empty-packs {
                    padding: 10px 10px !important;   
                }                    
            
                ' . $addon_id . ' .tab-content3-1 .img-container2 {
                    width: 100%; 
                    height: 100%; 
                    position: relative; 
                }
            
                ' . $addon_id . ' .tab-content3-1 .img-wrapper {
                    position: relative;
                    width: 100%;
                    height: 100%;
                    overflow: hidden; 
                }
            
                ' . $addon_id . ' .tab-content3-1 .img-wrapper img {
                    width: 100%; 
                    height: auto; 
                    border-radius: 10px;
                    box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
                    transition: all 0.3s ease-in-out;
                }
            
                ' . $addon_id . ' .tab-content3-1 .button-container {
                    position: absolute; 
                    top: 110%; 
                    left: 29%; 
                    transform: translate(-50%, -50%); 
                    z-index: 1; 
                }
            
                ' . $addon_id . ' .tab-content3-1:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #F1E8FC; 
                }
            
                ' . $addon_id . ' .tab-content3-1 input[type="text"],
                ' . $addon_id . ' .tab-content3-1 select {
                    width: 80%; 
                    max-width: 80%; 
                    padding: 5px; 
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content4 {
                    position: relative;
                    background-color: #FEDFF0; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    width: 101%;
                }
            
                ' . $addon_id . ' .tab-content4:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FEDFF0;
                }
                ' . $addon_id . ' .tab-content4 input[type="text"],
                ' . $addon_id . ' .tab-content4 select,
                ' . $addon_id . ' .tab-content4 input[type="date"],
                ' . $addon_id . ' .tab-content4 input[type="file"] {
                    width: 480px;
                    max-width: 100%;
                    padding: 5px;
                    box-sizing: border-box;
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content4 input[type="file"] {
                    font-size: 12px;
                }
                ' . $addon_id . ' .tab-content4-4 {
                    position: relative;
                    background-color: #FEDFF0; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    width: 101%;
                }

                ' . $addon_id . ' .tab-content4-4:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FEDFF0; 
                }
                ' . $addon_id . ' .tab-content4-4 input[type="text"],
                ' . $addon_id . ' .tab-content4-4 select,
                ' . $addon_id . ' .tab-content4-4 input[type="date"] {
                    width: 100%;
                    max-width: 100px;
                    padding: 5px; 
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content4-4 input[type="file"] {
                    font-size: 12px;
                }
                ' . $addon_id . ' .tab-content4-2 {
                    position: relative;
                    background-color: #FEDFF0; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    width: 101%;
                }

                ' . $addon_id . ' .tab-content4-2:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FEDFF0;
                }
                ' . $addon_id . ' .tab-content4-2 input[type="text"],
                ' . $addon_id . ' .tab-content4-2 select{
                    width: 100%; 
                    max-width: 100%; 
                    padding: 5px;
                    box-sizing: border-box;
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content4-2 input[type="file"] {
                    font-size: 12px;
                }
                ' . $addon_id . ' .tab-content4-3 {
                    position: relative;
                    background-color: #FEDFF0; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    width: 101%;
                }
                ' . $addon_id . ' .tab-content4-3:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FEDFF0; 
                }
                ' . $addon_id . ' .tab-content4-3 input[type="text"],
                ' . $addon_id . ' .tab-content4-3 select,
                ' . $addon_id . ' .tab-content4-3 input[type="date"] {
                    width: 100%; 
                    max-width: 100%; 
                    padding: 5px; 
                    box-sizing: border-box;
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content4-3 input[type="file"] {
                    font-size: 12px;
                }
                ' . $addon_id . ' .tab-content4-1 {
                    position: relative;
                    background-color: #FEDFF0; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    width: 101%;
                }

                ' . $addon_id . ' .tab-content4-1:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FEDFF0;
                }
                ' . $addon_id . ' .tab-content4-1 input[type="text"],
                ' . $addon_id . ' .tab-content4-1 select,
                ' . $addon_id . ' .tab-content4-1 input[type="date"] {
                    width: 100%;
                    max-width: 100%;
                    padding: 5px;
                    box-sizing: border-box;
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content4-1 input[type="file"] {
                    font-size: 12px;
                }
                ' . $addon_id . ' .tab-content4-5 {
                    position: relative;
                    background-color: #FEDFF0; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    width: 101%;
                }

                ' . $addon_id . ' .tab-content4-5:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FEDFF0;
                }
                ' . $addon_id . ' .tab-content4-5 input[type="text"],
                ' . $addon_id . ' .tab-content4-5 select,
                ' . $addon_id . ' .tab-content4-5 input[type="date"] {
                    width: 100%; 
                    max-width: 100%; 
                    padding: 5px;
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content4-5 input[type="file"] {
                    font-size: 12px;
                }
                ' . $addon_id . ' .tab-content4-2-1 {
                    position: relative;
                    background-color: #FEDFF0; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    width: 101%;
                }

                ' . $addon_id . ' .tab-content4-2-1:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FEDFF0;
                }
                ' . $addon_id . ' .tab-content4-2-1 input[type="text"],
                ' . $addon_id . ' .tab-content4-2-1 select{
                    width: 100%; 
                    max-width: 100%;
                    padding: 5px;
                    box-sizing: border-box;
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content4-3-1 {
                    position: relative;
                    background-color: #FEDFF0;
                    border-radius: 0 0 20px 20px;
                    padding: 10px 10px !important;
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc;
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    width: 100%; /* Reduced width */
                    
                }
                ' . $addon_id . ' .tab-content4-3-1 .img-container2 img {
                    max-width: 20px; 
                    max-height: 20px; 
                }

                ' . $addon_id . ' .tab-content4-3-1:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FEDFF0;
                }
                ' . $addon_id . ' .tab-content4-3-1 input[type="text"],
                ' . $addon_id . ' .tab-content4-3-1 select,
                ' . $addon_id . ' .tab-content4-3-1 input[type="date"] {
                    width: 50%; 
                    max-width: 50%;
                    padding: 5px;
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                    
                }
                ' . $addon_id . ' .tab-content4-4-1 {
                    position: relative;
                    background-color: #FEDFF0; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    width: 101%;
                }

                ' . $addon_id . ' .tab-content4-4-1:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FEDFF0;
                }
                ' . $addon_id . ' .tab-content4-4-1 input[type="text"],
                ' . $addon_id . ' .tab-content4-4-1 select,
                ' . $addon_id . ' .tab-content4-4-1 input[type="date"] {
                    width: 100%;
                    max-width: 100%;
                    padding: 5px;
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                    
                }
                ' . $addon_id . ' .tab-content4-5-1 {
                    position: relative;
                    background-color: #FEDFF0; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    width: 101%;
                }

                ' . $addon_id . ' .tab-content4-5-1:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FEDFF0; 
                }
                ' . $addon_id . ' .tab-content4-5-1 input[type="text"],
                ' . $addon_id . ' .tab-content4-5-1 select,
                ' . $addon_id . ' .tab-content4-5-1 input[type="date"] {
                    width: 100%; 
                    max-width: 100%; 
                    padding: 5px; 
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content4-1-1 {
                    position: relative;
                    background-color: #FEDFF0; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    width: 101%;
                }

                ' . $addon_id . ' .tab-content4-1-1:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FEDFF0;
                }
                ' . $addon_id . ' .tab-content4-1-1 input[type="text"],
                ' . $addon_id . ' .tab-content4-1-1 select,
                ' . $addon_id . ' .tab-content4-1-1 input[type="date"] {
                    width: 100%;
                    max-width: 100%;
                    padding: 5px;
                    box-sizing: border-box;
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content5 {
                    position: relative;
                    background-color: #ffd8b2; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important;
                    width: 101%;
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                }

                ' . $addon_id . ' .tab-content5:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #ffd8b2;
                }
                ' . $addon_id . ' .tab-content5 input[type="text"],
                ' . $addon_id . ' .tab-content5 select{
                    width: 100%; 
                    max-width: 100%; 
                    padding: 5px;
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content5 input[type="date"] {
                    width: 100%; 
                    max-width: 100%; 
                    padding: 5px;
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                }
                
                ' . $addon_id . ' .tab-content5-1 {
                    position: relative;
                    background-color: #ffd8b2; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important;
                    width: 101%;
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                }

                ' . $addon_id . ' .tab-content5-1:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #ffd8b2;
                }
                ' . $addon_id . ' .tab-content5-1 input[type="text"],
                ' . $addon_id . ' .tab-content5-1 select{
                    width: 100%; 
                    max-width: 100%; 
                    padding: 5px;
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content5-1 input[type="date"] {
                    width: 100%; 
                    max-width: 100%; 
                    padding: 5px;
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                }
                
                
                ' . $addon_id . ' .tab-content6 {
                    position: relative;
                    background-color: #FFF5BA; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important;
                    width: 101%;
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                }
    
                ' . $addon_id . ' .tab-content6:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FFF5BA;
                }
                ' . $addon_id . ' .tab-content6 input[type="text"],
                ' . $addon_id . ' .tab-content6 select,
                ' . $addon_id . ' .tab-content6 input[type="date"],
                ' . $addon_id . ' .tab-content6 input[type="number"],
                ' . $addon_id . ' .tab-content6 textarea {
                    width: 100%; 
                    max-width: 100%; 
                    padding: 5px;
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content6-1 {
                    position: relative;
                    background-color: #FFF5BA; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important;
                    width: 101%; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                }
    
                ' . $addon_id . ' .tab-content6-1:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FFF5BA;
                }
                ' . $addon_id . ' .tab-content6-1 input[type="text"],
                ' . $addon_id . ' .tab-content6-1 select,
                ' . $addon_id . ' .tab-content6-1 input[type="date"],
                ' . $addon_id . ' .tab-content6-1 input[type="number"],
                ' . $addon_id . ' .tab-content6-1 textarea {
                    width: 100%; 
                    max-width: 100%; 
                    padding: 5px;
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content6-1-1 {
                    position: relative;
                    background-color: #FFF5BA; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important;
                    width: 101%;
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                }
    
                ' . $addon_id . ' .tab-content6-1-1:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FFF5BA;
                }
                ' . $addon_id . ' .tab-content6-1-1 input[type="text"],
                ' . $addon_id . ' .tab-content6-1-1 select,
                ' . $addon_id . ' .tab-content6-1-1 input[type="date"],
                ' . $addon_id . ' .tab-content6-1-1 input[type="number"],
                ' . $addon_id . ' .tab-content6-1-1 textarea {
                    width: 100%; 
                    max-width: 100%; 
                    padding: 5px;
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                }
            }
            ';
        $css .= '
            @media (max-width: 390px) {
                ' . $addon_id . ' .sppb-addon {
                    max-width: calc(100% - 20px);
                    padding: 10px;
                }
                ' . $addon_id . ' input[type="file"] {
                    font-size: 12px;
                }
            
                ' . $addon_id . ' .tab {
                    padding: 13px 13px !important; 
                    max-width: calc(100% - 10px); 
                    overflow-x: auto;
                    font-size: 9px !important;
                }

                ' . $addon_id . ' .tab  span {
                    font-size: 5px;
                }
            
                ' . $addon_id . ' .tab-content {
                    position: relative;
                    background-color: #DBE9FF; 
                    border-radius: 0 0 20px 20px;
                    padding: 10px 105px;
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    display: flex; 
                    justify-content: center;
                    align-items: center; 
                }
            
                ' . $addon_id . ' .tab-content-1 {
                    position: relative;
                    background-color: #DBE9FF; 
                    border-radius: 0 0 20px 20px;
                    padding: 10px 20px !important; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    width: 101%;
                }
                
                ' . $addon_id . ' .container {
                    display: flex;
                    flex-direction: column;
                    justify-content: center;
                    align-items: center; 
                    flex-wrap: wrap;
                }
               
                
                ' . $addon_id . ' .invoice-number {
                    font-size: 14px; 
                    margin-top: 5px; 
                }
                
                ' . $addon_id . ' .button-group button {
                    font-size: 10px; 
                    margin-left: 2px;
                }
        
                ' . $addon_id . ' .form-group {
                    margin-bottom: 1rem; 
                    flex-direction: column; 
                    align-items: center;
                    justify-content: center;
                }
        
               
                ' . $addon_id . ' .status-text {
                    font-size: 10px;
                    margin-top: 0.2em; 
                }
        
                
                ' . $addon_id . ' .form-control,
                ' . $addon_id . ' #document_upload,
                ' . $addon_id . ' select {
                    border-radius: 8px;
                    border: 2px solid #3498db;
                    font-size: 12px; 
                    margin-bottom: 0.5em;
                    box-sizing: border-box;
                    width: calc(100% - 2em); 
                    padding: 0.3em; 
                }

                ' . $addon_id . ' .form-group {
                    display: flex;
                    flex-direction: column;
                    justify-content: center;
                    align-items: center;
                    width: 100%;
                }
                ' . $addon_id . ' .form-group .btn,
                ' . $addon_id . ' .form-group .btn-danger,
                ' . $addon_id . ' .form-group .btn-primary,
                ' . $addon_id . ' .form-group .btn-warning {
                    border-radius: 8px;
                    padding: 0.5em 1em; 
                    font-size: 12px; 
                    margin-top: 1.5em; 
                    margin-left: auto; 
                    margin-right: auto; 
                    display: block; 
                }
                
                ' . $addon_id . ' form {
                    width: 100%;
                    box-sizing: border-box;
                }
                ' . $addon_id . ' .tab-content2 {
                    position: relative;
                    background-color: #E2FAE1; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 135px; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15); 
                    margin-top: -12px;
                }
            
                ' . $addon_id . ' .tab-content2 .img-container2 img {
                    width: 486px;
                    height: auto;
                    max-width: 100%; 
                    border-radius: 10px;
                    box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
                    transition: all 0.3s ease-in-out;    
                }                   
            
                ' . $addon_id . ' .tab-content2:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #E2FAE1; 
                }
            
                ' . $addon_id . ' .tab-content2-1 {
                    position: relative;
                    background-color: #E2FAE1;
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15); 
                    margin-top: -12px;
                    width: 101%;
                }
            
                ' . $addon_id . ' .tab-content2-1:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #E2FAE1; 
                }
            
                
                ' . $addon_id . ' .tab-content3 .button-container {
                    position: absolute; 
                    top: 110%; 
                    left: 50%;
                    transform: translate(-50%, -50%); 
                    z-index: 1; 
                }
                ' . $addon_id . ' .tab-content3 {
                    position: relative;
                    background-color: #F1E8FC; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important;
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    width: 100%;
                }
            
                ' . $addon_id . ' #section3.tab-content3.empty-packs {
                    padding: 10px 168px;  
                }                    
            
                ' . $addon_id . ' .tab-content3 .img-container2 {
                    width: 100%; 
                    height: 100%; 
                    position: relative; 
                }
            
                ' . $addon_id . ' .tab-content3 .img-wrapper {
                    position: relative;
                    width: 100%;
                    height: 100%;
                    overflow: hidden; 
                }
            
                ' . $addon_id . ' .tab-content3 .img-wrapper img {
                    width: 100%; 
                    height: auto; 
                    border-radius: 10px;
                    box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
                    transition: all 0.3s ease-in-out;
                }
            
                ' . $addon_id . ' .tab-content3:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #F1E8FC; 
                }
            
                ' . $addon_id . ' .tab-content3 input[type="text"],
                ' . $addon_id . ' .tab-content3 select {
                    width: 80%; 
                    max-width: 80%; 
                    padding: 5px; 
                    box-sizing: border-box;
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content3-1 {
                    position: relative;
                    background-color: #F1E8FC; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    width: 101%;
                }
            
                ' . $addon_id . ' #section3.tab-content3-1.empty-packs {
                    padding: 10px 10px !important;   
                }                    
            
                ' . $addon_id . ' .tab-content3-1 .img-container2 {
                    width: 100%; 
                    height: 100%; 
                    position: relative; 
                }
            
                ' . $addon_id . ' .tab-content3-1 .img-wrapper {
                    position: relative;
                    width: 100%;
                    height: 100%;
                    overflow: hidden; 
                }
            
                ' . $addon_id . ' .tab-content3-1 .img-wrapper img {
                    width: 100%; 
                    height: auto; 
                    border-radius: 10px;
                    box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
                    transition: all 0.3s ease-in-out;
                }
            
                ' . $addon_id . ' .tab-content3-1 .button-container {
                    position: absolute; 
                    top: 110%; 
                    left: 29%; 
                    transform: translate(-50%, -50%); 
                    z-index: 1; 
                }
            
                ' . $addon_id . ' .tab-content3-1:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #F1E8FC; 
                }
            
                ' . $addon_id . ' .tab-content3-1 input[type="text"],
                ' . $addon_id . ' .tab-content3-1 select {
                    width: 80%; 
                    max-width: 80%; 
                    padding: 5px; 
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content4 {
                    position: relative;
                    background-color: #FEDFF0; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    width: 101%;
                }
            
                ' . $addon_id . ' .tab-content4:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FEDFF0;
                }
                ' . $addon_id . ' .tab-content4 input[type="text"],
                ' . $addon_id . ' .tab-content4 select,
                ' . $addon_id . ' .tab-content4 input[type="date"],
                ' . $addon_id . ' .tab-content4 input[type="file"] {
                    width: 480px;
                    max-width: 100%;
                    padding: 5px;
                    box-sizing: border-box;
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content4 input[type="file"] {
                    font-size: 12px;
                }
                ' . $addon_id . ' .tab-content4-4 {
                    position: relative;
                    background-color: #FEDFF0; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    width: 101%;
                }

                ' . $addon_id . ' .tab-content4-4:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FEDFF0; 
                }
                ' . $addon_id . ' .tab-content4-4 input[type="text"],
                ' . $addon_id . ' .tab-content4-4 select,
                ' . $addon_id . ' .tab-content4-4 input[type="date"] {
                    width: 100%;
                    max-width: 100px;
                    padding: 5px; 
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content4-4 input[type="file"] {
                    font-size: 12px;
                }
                ' . $addon_id . ' .tab-content4-2 {
                    position: relative;
                    background-color: #FEDFF0; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    width: 101%;
                }

                ' . $addon_id . ' .tab-content4-2:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FEDFF0;
                }
                ' . $addon_id . ' .tab-content4-2 input[type="text"],
                ' . $addon_id . ' .tab-content4-2 select{
                    width: 100%; 
                    max-width: 100%; 
                    padding: 5px;
                    box-sizing: border-box;
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content4-2 input[type="file"] {
                    font-size: 12px;
                }
                ' . $addon_id . ' .tab-content4-3 {
                    position: relative;
                    background-color: #FEDFF0; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    width: 101%;
                }
                ' . $addon_id . ' .tab-content4-3:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FEDFF0; 
                }
                ' . $addon_id . ' .tab-content4-3 input[type="text"],
                ' . $addon_id . ' .tab-content4-3 select,
                ' . $addon_id . ' .tab-content4-3 input[type="date"] {
                    width: 100%; 
                    max-width: 100%; 
                    padding: 5px; 
                    box-sizing: border-box;
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content4-3 input[type="file"] {
                    font-size: 12px;
                }
                ' . $addon_id . ' .tab-content4-1 {
                    position: relative;
                    background-color: #FEDFF0; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    width: 101%;
                }

                ' . $addon_id . ' .tab-content4-1:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FEDFF0;
                }
                ' . $addon_id . ' .tab-content4-1 input[type="text"],
                ' . $addon_id . ' .tab-content4-1 select,
                ' . $addon_id . ' .tab-content4-1 input[type="date"] {
                    width: 100%;
                    max-width: 100%;
                    padding: 5px;
                    box-sizing: border-box;
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content4-1 input[type="file"] {
                    font-size: 12px;
                }
                ' . $addon_id . ' .tab-content4-5 {
                    position: relative;
                    background-color: #FEDFF0; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    width: 101%;
                }

                ' . $addon_id . ' .tab-content4-5:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FEDFF0;
                }
                ' . $addon_id . ' .tab-content4-5 input[type="text"],
                ' . $addon_id . ' .tab-content4-5 select,
                ' . $addon_id . ' .tab-content4-5 input[type="date"] {
                    width: 100%; 
                    max-width: 100%; 
                    padding: 5px;
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content4-5 input[type="file"] {
                    font-size: 12px;
                }
                ' . $addon_id . ' .tab-content4-2-1 {
                    position: relative;
                    background-color: #FEDFF0; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    width: 101%;
                }

                ' . $addon_id . ' .tab-content4-2-1:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FEDFF0;
                }
                ' . $addon_id . ' .tab-content4-2-1 input[type="text"],
                ' . $addon_id . ' .tab-content4-2-1 select{
                    width: 100%; 
                    max-width: 100%;
                    padding: 5px;
                    box-sizing: border-box;
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content4-3-1 {
                    position: relative;
                    background-color: #FEDFF0;
                    border-radius: 0 0 20px 20px;
                    padding: 10px 10px !important;
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc;
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    width: 100%; /* Reduced width */
                    
                }
                ' . $addon_id . ' .tab-content4-3-1 .img-container2 img {
                    max-width: 20px; 
                    max-height: 20px; 
                }

                ' . $addon_id . ' .tab-content4-3-1:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FEDFF0;
                }
                ' . $addon_id . ' .tab-content4-3-1 input[type="text"],
                ' . $addon_id . ' .tab-content4-3-1 select,
                ' . $addon_id . ' .tab-content4-3-1 input[type="date"] {
                    width: 50%; 
                    max-width: 50%;
                    padding: 5px;
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                    
                }
                ' . $addon_id . ' .tab-content4-4-1 {
                    position: relative;
                    background-color: #FEDFF0; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    width: 101%;
                }

                ' . $addon_id . ' .tab-content4-4-1:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FEDFF0;
                }
                ' . $addon_id . ' .tab-content4-4-1 input[type="text"],
                ' . $addon_id . ' .tab-content4-4-1 select,
                ' . $addon_id . ' .tab-content4-4-1 input[type="date"] {
                    width: 100%;
                    max-width: 100%;
                    padding: 5px;
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                    
                }
                ' . $addon_id . ' .tab-content4-5-1 {
                    position: relative;
                    background-color: #FEDFF0; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    width: 101%;
                }

                ' . $addon_id . ' .tab-content4-5-1:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FEDFF0; 
                }
                ' . $addon_id . ' .tab-content4-5-1 input[type="text"],
                ' . $addon_id . ' .tab-content4-5-1 select,
                ' . $addon_id . ' .tab-content4-5-1 input[type="date"] {
                    width: 100%; 
                    max-width: 100%; 
                    padding: 5px; 
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content4-1-1 {
                    position: relative;
                    background-color: #FEDFF0; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    width: 101%;
                }

                ' . $addon_id . ' .tab-content4-1-1:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FEDFF0;
                }
                ' . $addon_id . ' .tab-content4-1-1 input[type="text"],
                ' . $addon_id . ' .tab-content4-1-1 select,
                ' . $addon_id . ' .tab-content4-1-1 input[type="date"] {
                    width: 100%;
                    max-width: 100%;
                    padding: 5px;
                    box-sizing: border-box;
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content5 {
                    position: relative;
                    background-color: #ffd8b2; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important;
                    width: 101%;
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                }

                ' . $addon_id . ' .tab-content5:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #ffd8b2;
                }
                ' . $addon_id . ' .tab-content5 input[type="text"],
                ' . $addon_id . ' .tab-content5 select{
                    width: 100%; 
                    max-width: 100%; 
                    padding: 5px;
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content5 input[type="date"] {
                    width: 100%; 
                    max-width: 100%; 
                    padding: 5px;
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                }
                
                ' . $addon_id . ' .tab-content5-1 {
                    position: relative;
                    background-color: #ffd8b2; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important;
                    width: 101%;
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                }

                ' . $addon_id . ' .tab-content5-1:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #ffd8b2;
                }
                ' . $addon_id . ' .tab-content5-1 input[type="text"],
                ' . $addon_id . ' .tab-content5-1 select{
                    width: 100%; 
                    max-width: 100%; 
                    padding: 5px;
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content5-1 input[type="date"] {
                    width: 100%; 
                    max-width: 100%; 
                    padding: 5px;
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                }
                
                
                ' . $addon_id . ' .tab-content6 {
                    position: relative;
                    background-color: #FFF5BA; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important;
                    width: 101%;
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                }
    
                ' . $addon_id . ' .tab-content6:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FFF5BA;
                }
                ' . $addon_id . ' .tab-content6 input[type="text"],
                ' . $addon_id . ' .tab-content6 select,
                ' . $addon_id . ' .tab-content6 input[type="date"],
                ' . $addon_id . ' .tab-content6 input[type="number"],
                ' . $addon_id . ' .tab-content6 textarea {
                    width: 100%; 
                    max-width: 100%; 
                    padding: 5px;
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content6-1 {
                    position: relative;
                    background-color: #FFF5BA; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important;
                    width: 101%; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                }
    
                ' . $addon_id . ' .tab-content6-1:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FFF5BA;
                }
                ' . $addon_id . ' .tab-content6-1 input[type="text"],
                ' . $addon_id . ' .tab-content6-1 select,
                ' . $addon_id . ' .tab-content6-1 input[type="date"],
                ' . $addon_id . ' .tab-content6-1 input[type="number"],
                ' . $addon_id . ' .tab-content6-1 textarea {
                    width: 100%; 
                    max-width: 100%; 
                    padding: 5px;
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content6-1-1 {
                    position: relative;
                    background-color: #FFF5BA; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important;
                    width: 101%;
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                }
    
                ' . $addon_id . ' .tab-content6-1-1:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FFF5BA;
                }
                ' . $addon_id . ' .tab-content6-1-1 input[type="text"],
                ' . $addon_id . ' .tab-content6-1-1 select,
                ' . $addon_id . ' .tab-content6-1-1 input[type="date"],
                ' . $addon_id . ' .tab-content6-1-1 input[type="number"],
                ' . $addon_id . ' .tab-content6-1-1 textarea {
                    width: 100%; 
                    max-width: 100%; 
                    padding: 5px;
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                }
            }
            ';
        $css .= '
            @media (max-width: 375px) {
                ' . $addon_id . ' .sppb-addon {
                    max-width: calc(100% - 20px);
                    padding: 10px;
                }
                ' . $addon_id . ' input[type="file"] {
                    font-size: 12px;
                }
            
                ' . $addon_id . ' .tab {
                    padding: 12px 12px !important; 
                    max-width: calc(100% - 10px); 
                    overflow-x: auto;
                    font-size: 8px !important;
                }

                ' . $addon_id . ' .tab  span {
                    font-size: 5px;
                }
            
                ' . $addon_id . ' .tab-content {
                    position: relative;
                    background-color: #DBE9FF; 
                    border-radius: 0 0 20px 20px;
                    padding: 10px 105px;
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    display: flex; 
                    justify-content: center;
                    align-items: center; 
                }
            
                ' . $addon_id . ' .tab-content-1 {
                    position: relative;
                    background-color: #DBE9FF; 
                    border-radius: 0 0 20px 20px;
                    padding: 10px 20px !important; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    width: 101%;
                }
                
                ' . $addon_id . ' .container {
                    display: flex;
                    flex-direction: column;
                    justify-content: center;
                    align-items: center; 
                    flex-wrap: wrap;
                }
               
                
                ' . $addon_id . ' .invoice-number {
                    font-size: 14px; 
                    margin-top: 5px; 
                }
                
                ' . $addon_id . ' .button-group button {
                    font-size: 10px; 
                    margin-left: 2px;
                }
        
                ' . $addon_id . ' .form-group {
                    margin-bottom: 1rem; 
                    flex-direction: column; 
                    align-items: center;
                    justify-content: center;
                }
        
               
                ' . $addon_id . ' .status-text {
                    font-size: 10px;
                    margin-top: 0.2em; 
                }
        
                
                ' . $addon_id . ' .form-control,
                ' . $addon_id . ' #document_upload,
                ' . $addon_id . ' select {
                    border-radius: 8px;
                    border: 2px solid #3498db;
                    font-size: 12px; 
                    margin-bottom: 0.5em;
                    box-sizing: border-box;
                    width: calc(100% - 2em); 
                    padding: 0.3em; 
                }

                ' . $addon_id . ' .form-group {
                    display: flex;
                    flex-direction: column;
                    justify-content: center;
                    align-items: center;
                    width: 100%;
                }
                ' . $addon_id . ' .form-group .btn,
                ' . $addon_id . ' .form-group .btn-danger,
                ' . $addon_id . ' .form-group .btn-primary,
                ' . $addon_id . ' .form-group .btn-warning {
                    border-radius: 8px;
                    padding: 0.5em 1em; 
                    font-size: 12px; 
                    margin-top: 1.5em; 
                    margin-left: auto; 
                    margin-right: auto; 
                    display: block; 
                }
                
                ' . $addon_id . ' form {
                    width: 100%;
                    box-sizing: border-box;
                }
                ' . $addon_id . ' .tab-content2 {
                    position: relative;
                    background-color: #E2FAE1; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 135px; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15); 
                    margin-top: -12px;
                }
            
                ' . $addon_id . ' .tab-content2 .img-container2 img {
                    width: 486px;
                    height: auto;
                    max-width: 100%; 
                    border-radius: 10px;
                    box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
                    transition: all 0.3s ease-in-out;    
                }                   
            
                ' . $addon_id . ' .tab-content2:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #E2FAE1; 
                }
            
                ' . $addon_id . ' .tab-content2-1 {
                    position: relative;
                    background-color: #E2FAE1;
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15); 
                    margin-top: -12px;
                    width: 101%;
                }
            
                ' . $addon_id . ' .tab-content2-1:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #E2FAE1; 
                }
            
                
                ' . $addon_id . ' .tab-content3 .button-container {
                    position: absolute; 
                    top: 110%; 
                    left: 50%;
                    transform: translate(-50%, -50%); 
                    z-index: 1; 
                }
                ' . $addon_id . ' .tab-content3 {
                    position: relative;
                    background-color: #F1E8FC; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important;
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    width: 100%;
                }
            
                ' . $addon_id . ' #section3.tab-content3.empty-packs {
                    padding: 10px 168px;  
                }                    
            
                ' . $addon_id . ' .tab-content3 .img-container2 {
                    width: 100%; 
                    height: 100%; 
                    position: relative; 
                }
            
                ' . $addon_id . ' .tab-content3 .img-wrapper {
                    position: relative;
                    width: 100%;
                    height: 100%;
                    overflow: hidden; 
                }
            
                ' . $addon_id . ' .tab-content3 .img-wrapper img {
                    width: 100%; 
                    height: auto; 
                    border-radius: 10px;
                    box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
                    transition: all 0.3s ease-in-out;
                }
            
                ' . $addon_id . ' .tab-content3:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #F1E8FC; 
                }
            
                ' . $addon_id . ' .tab-content3 input[type="text"],
                ' . $addon_id . ' .tab-content3 select {
                    width: 80%; 
                    max-width: 80%; 
                    padding: 5px; 
                    box-sizing: border-box;
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content3-1 {
                    position: relative;
                    background-color: #F1E8FC; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    width: 101%;
                }
            
                ' . $addon_id . ' #section3.tab-content3-1.empty-packs {
                    padding: 10px 10px !important;   
                }                    
            
                ' . $addon_id . ' .tab-content3-1 .img-container2 {
                    width: 100%; 
                    height: 100%; 
                    position: relative; 
                }
            
                ' . $addon_id . ' .tab-content3-1 .img-wrapper {
                    position: relative;
                    width: 100%;
                    height: 100%;
                    overflow: hidden; 
                }
            
                ' . $addon_id . ' .tab-content3-1 .img-wrapper img {
                    width: 100%; 
                    height: auto; 
                    border-radius: 10px;
                    box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
                    transition: all 0.3s ease-in-out;
                }
            
                ' . $addon_id . ' .tab-content3-1 .button-container {
                    position: absolute; 
                    top: 110%; 
                    left: 29%; 
                    transform: translate(-50%, -50%); 
                    z-index: 1; 
                }
            
                ' . $addon_id . ' .tab-content3-1:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #F1E8FC; 
                }
            
                ' . $addon_id . ' .tab-content3-1 input[type="text"],
                ' . $addon_id . ' .tab-content3-1 select {
                    width: 80%; 
                    max-width: 80%; 
                    padding: 5px; 
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content4 {
                    position: relative;
                    background-color: #FEDFF0; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    width: 101%;
                }
            
                ' . $addon_id . ' .tab-content4:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FEDFF0;
                }
                ' . $addon_id . ' .tab-content4 input[type="text"],
                ' . $addon_id . ' .tab-content4 select,
                ' . $addon_id . ' .tab-content4 input[type="date"],
                ' . $addon_id . ' .tab-content4 input[type="file"] {
                    width: 480px;
                    max-width: 100%;
                    padding: 5px;
                    box-sizing: border-box;
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content4 input[type="file"] {
                    font-size: 12px;
                }
                ' . $addon_id . ' .tab-content4-4 {
                    position: relative;
                    background-color: #FEDFF0; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    width: 101%;
                }

                ' . $addon_id . ' .tab-content4-4:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FEDFF0; 
                }
                ' . $addon_id . ' .tab-content4-4 input[type="text"],
                ' . $addon_id . ' .tab-content4-4 select,
                ' . $addon_id . ' .tab-content4-4 input[type="date"] {
                    width: 100%;
                    max-width: 100px;
                    padding: 5px; 
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content4-4 input[type="file"] {
                    font-size: 12px;
                }
                ' . $addon_id . ' .tab-content4-2 {
                    position: relative;
                    background-color: #FEDFF0; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    width: 101%;
                }

                ' . $addon_id . ' .tab-content4-2:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FEDFF0;
                }
                ' . $addon_id . ' .tab-content4-2 input[type="text"],
                ' . $addon_id . ' .tab-content4-2 select{
                    width: 100%; 
                    max-width: 100%; 
                    padding: 5px;
                    box-sizing: border-box;
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content4-2 input[type="file"] {
                    font-size: 12px;
                }
                ' . $addon_id . ' .tab-content4-3 {
                    position: relative;
                    background-color: #FEDFF0; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    width: 101%;
                }
                ' . $addon_id . ' .tab-content4-3:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FEDFF0; 
                }
                ' . $addon_id . ' .tab-content4-3 input[type="text"],
                ' . $addon_id . ' .tab-content4-3 select,
                ' . $addon_id . ' .tab-content4-3 input[type="date"] {
                    width: 100%; 
                    max-width: 100%; 
                    padding: 5px; 
                    box-sizing: border-box;
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content4-3 input[type="file"] {
                    font-size: 12px;
                }
                ' . $addon_id . ' .tab-content4-1 {
                    position: relative;
                    background-color: #FEDFF0; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    width: 101%;
                }

                ' . $addon_id . ' .tab-content4-1:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FEDFF0;
                }
                ' . $addon_id . ' .tab-content4-1 input[type="text"],
                ' . $addon_id . ' .tab-content4-1 select,
                ' . $addon_id . ' .tab-content4-1 input[type="date"] {
                    width: 100%;
                    max-width: 100%;
                    padding: 5px;
                    box-sizing: border-box;
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content4-1 input[type="file"] {
                    font-size: 12px;
                }
                ' . $addon_id . ' .tab-content4-5 {
                    position: relative;
                    background-color: #FEDFF0; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    width: 101%;
                }

                ' . $addon_id . ' .tab-content4-5:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FEDFF0;
                }
                ' . $addon_id . ' .tab-content4-5 input[type="text"],
                ' . $addon_id . ' .tab-content4-5 select,
                ' . $addon_id . ' .tab-content4-5 input[type="date"] {
                    width: 100%; 
                    max-width: 100%; 
                    padding: 5px;
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content4-5 input[type="file"] {
                    font-size: 12px;
                }
                ' . $addon_id . ' .tab-content4-2-1 {
                    position: relative;
                    background-color: #FEDFF0; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    width: 101%;
                }

                ' . $addon_id . ' .tab-content4-2-1:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FEDFF0;
                }
                ' . $addon_id . ' .tab-content4-2-1 input[type="text"],
                ' . $addon_id . ' .tab-content4-2-1 select{
                    width: 100%; 
                    max-width: 100%;
                    padding: 5px;
                    box-sizing: border-box;
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content4-3-1 {
                    position: relative;
                    background-color: #FEDFF0;
                    border-radius: 0 0 20px 20px;
                    padding: 10px 10px !important;
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc;
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    width: 100%; /* Reduced width */
                    
                }
                ' . $addon_id . ' .tab-content4-3-1 .img-container2 img {
                    max-width: 20px; 
                    max-height: 20px; 
                }

                ' . $addon_id . ' .tab-content4-3-1:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FEDFF0;
                }
                ' . $addon_id . ' .tab-content4-3-1 input[type="text"],
                ' . $addon_id . ' .tab-content4-3-1 select,
                ' . $addon_id . ' .tab-content4-3-1 input[type="date"] {
                    width: 50%; 
                    max-width: 50%;
                    padding: 5px;
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                    
                }
                ' . $addon_id . ' .tab-content4-4-1 {
                    position: relative;
                    background-color: #FEDFF0; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    width: 101%;
                }

                ' . $addon_id . ' .tab-content4-4-1:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FEDFF0;
                }
                ' . $addon_id . ' .tab-content4-4-1 input[type="text"],
                ' . $addon_id . ' .tab-content4-4-1 select,
                ' . $addon_id . ' .tab-content4-4-1 input[type="date"] {
                    width: 100%;
                    max-width: 100%;
                    padding: 5px;
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                    
                }
                ' . $addon_id . ' .tab-content4-5-1 {
                    position: relative;
                    background-color: #FEDFF0; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    width: 101%;
                }

                ' . $addon_id . ' .tab-content4-5-1:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FEDFF0; 
                }
                ' . $addon_id . ' .tab-content4-5-1 input[type="text"],
                ' . $addon_id . ' .tab-content4-5-1 select,
                ' . $addon_id . ' .tab-content4-5-1 input[type="date"] {
                    width: 100%; 
                    max-width: 100%; 
                    padding: 5px; 
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content4-1-1 {
                    position: relative;
                    background-color: #FEDFF0; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    width: 101%;
                }

                ' . $addon_id . ' .tab-content4-1-1:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FEDFF0;
                }
                ' . $addon_id . ' .tab-content4-1-1 input[type="text"],
                ' . $addon_id . ' .tab-content4-1-1 select,
                ' . $addon_id . ' .tab-content4-1-1 input[type="date"] {
                    width: 100%;
                    max-width: 100%;
                    padding: 5px;
                    box-sizing: border-box;
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content5 {
                    position: relative;
                    background-color: #ffd8b2; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important;
                    width: 101%;
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                }

                ' . $addon_id . ' .tab-content5:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #ffd8b2;
                }
                ' . $addon_id . ' .tab-content5 input[type="text"],
                ' . $addon_id . ' .tab-content5 select{
                    width: 100%; 
                    max-width: 100%; 
                    padding: 5px;
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content5 input[type="date"] {
                    width: 100%; 
                    max-width: 100%; 
                    padding: 5px;
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                }
                
                ' . $addon_id . ' .tab-content5-1 {
                    position: relative;
                    background-color: #ffd8b2; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important;
                    width: 101%;
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                }

                ' . $addon_id . ' .tab-content5-1:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #ffd8b2;
                }
                ' . $addon_id . ' .tab-content5-1 input[type="text"],
                ' . $addon_id . ' .tab-content5-1 select{
                    width: 100%; 
                    max-width: 100%; 
                    padding: 5px;
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content5-1 input[type="date"] {
                    width: 100%; 
                    max-width: 100%; 
                    padding: 5px;
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                }
                
                
                ' . $addon_id . ' .tab-content6 {
                    position: relative;
                    background-color: #FFF5BA; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important;
                    width: 101%;
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                }
    
                ' . $addon_id . ' .tab-content6:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FFF5BA;
                }
                ' . $addon_id . ' .tab-content6 input[type="text"],
                ' . $addon_id . ' .tab-content6 select,
                ' . $addon_id . ' .tab-content6 input[type="date"],
                ' . $addon_id . ' .tab-content6 input[type="number"],
                ' . $addon_id . ' .tab-content6 textarea {
                    width: 100%; 
                    max-width: 100%; 
                    padding: 5px;
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content6-1 {
                    position: relative;
                    background-color: #FFF5BA; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important;
                    width: 101%; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                }
    
                ' . $addon_id . ' .tab-content6-1:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FFF5BA;
                }
                ' . $addon_id . ' .tab-content6-1 input[type="text"],
                ' . $addon_id . ' .tab-content6-1 select,
                ' . $addon_id . ' .tab-content6-1 input[type="date"],
                ' . $addon_id . ' .tab-content6-1 input[type="number"],
                ' . $addon_id . ' .tab-content6-1 textarea {
                    width: 100%; 
                    max-width: 100%; 
                    padding: 5px;
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content6-1-1 {
                    position: relative;
                    background-color: #FFF5BA; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important;
                    width: 101%;
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                }
    
                ' . $addon_id . ' .tab-content6-1-1:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FFF5BA;
                }
                ' . $addon_id . ' .tab-content6-1-1 input[type="text"],
                ' . $addon_id . ' .tab-content6-1-1 select,
                ' . $addon_id . ' .tab-content6-1-1 input[type="date"],
                ' . $addon_id . ' .tab-content6-1-1 input[type="number"],
                ' . $addon_id . ' .tab-content6-1-1 textarea {
                    width: 100%; 
                    max-width: 100%; 
                    padding: 5px;
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                }
            }
            ';
        $css .= '
            @media (max-width: 360px) {
                ' . $addon_id . ' .sppb-addon {
                    max-width: calc(100% - 20px);
                    padding: 10px;
                }
                ' . $addon_id . ' input[type="file"] {
                    font-size: 12px;
                }
            
                ' . $addon_id . ' .tab {
                    padding: 10px 10px !important; 
                    max-width: calc(100% - 10px); 
                    overflow-x: auto;
                    font-size: 8px !important;
                }

                ' . $addon_id . ' .tab  span {
                    font-size: 5px;
                }
            
                ' . $addon_id . ' .tab-content {
                    position: relative;
                    background-color: #DBE9FF; 
                    border-radius: 0 0 20px 20px;
                    padding: 10px 105px;
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    display: flex; 
                    justify-content: center;
                    align-items: center; 
                }
            
                ' . $addon_id . ' .tab-content-1 {
                    position: relative;
                    background-color: #DBE9FF; 
                    border-radius: 0 0 20px 20px;
                    padding: 10px 20px !important; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    width: 101%;
                }
                
                ' . $addon_id . ' .container {
                    display: flex;
                    flex-direction: column;
                    justify-content: center;
                    align-items: center; 
                    flex-wrap: wrap;
                }
               
                
                ' . $addon_id . ' .invoice-number {
                    font-size: 14px; 
                    margin-top: 5px; 
                }
                
                ' . $addon_id . ' .button-group button {
                    font-size: 10px; 
                    margin-left: 2px;
                }
        
                ' . $addon_id . ' .form-group {
                    margin-bottom: 1rem; 
                    flex-direction: column; 
                    align-items: center;
                    justify-content: center;
                }
        
               
                ' . $addon_id . ' .status-text {
                    font-size: 10px;
                    margin-top: 0.2em; 
                }
        
                
                ' . $addon_id . ' .form-control,
                ' . $addon_id . ' #document_upload,
                ' . $addon_id . ' select {
                    border-radius: 8px;
                    border: 2px solid #3498db;
                    font-size: 12px; 
                    margin-bottom: 0.5em;
                    box-sizing: border-box;
                    width: calc(100% - 2em); 
                    padding: 0.3em; 
                }

                ' . $addon_id . ' .form-group {
                    display: flex;
                    flex-direction: column;
                    justify-content: center;
                    align-items: center;
                    width: 100%;
                }
                ' . $addon_id . ' .form-group .btn,
                ' . $addon_id . ' .form-group .btn-danger,
                ' . $addon_id . ' .form-group .btn-primary,
                ' . $addon_id . ' .form-group .btn-warning {
                    border-radius: 8px;
                    padding: 0.5em 1em; 
                    font-size: 12px; 
                    margin-top: 1.5em; 
                    margin-left: auto; 
                    margin-right: auto; 
                    display: block; 
                }
                
                ' . $addon_id . ' form {
                    width: 100%;
                    box-sizing: border-box;
                }
                ' . $addon_id . ' .tab-content2 {
                    position: relative;
                    background-color: #E2FAE1; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 135px; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15); 
                    margin-top: -12px;
                }
            
                ' . $addon_id . ' .tab-content2 .img-container2 img {
                    width: 486px;
                    height: auto;
                    max-width: 100%; 
                    border-radius: 10px;
                    box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
                    transition: all 0.3s ease-in-out;    
                }                   
            
                ' . $addon_id . ' .tab-content2:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #E2FAE1; 
                }
            
                ' . $addon_id . ' .tab-content2-1 {
                    position: relative;
                    background-color: #E2FAE1;
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15); 
                    margin-top: -12px;
                    width: 101%;
                }
            
                ' . $addon_id . ' .tab-content2-1:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #E2FAE1; 
                }
            
                
                ' . $addon_id . ' .tab-content3 .button-container {
                    position: absolute; 
                    top: 110%; 
                    left: 50%;
                    transform: translate(-50%, -50%); 
                    z-index: 1; 
                }
                ' . $addon_id . ' .tab-content3 {
                    position: relative;
                    background-color: #F1E8FC; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important;
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    width: 100%;
                }
            
                ' . $addon_id . ' #section3.tab-content3.empty-packs {
                    padding: 10px 168px;  
                }                    
            
                ' . $addon_id . ' .tab-content3 .img-container2 {
                    width: 100%; 
                    height: 100%; 
                    position: relative; 
                }
            
                ' . $addon_id . ' .tab-content3 .img-wrapper {
                    position: relative;
                    width: 100%;
                    height: 100%;
                    overflow: hidden; 
                }
            
                ' . $addon_id . ' .tab-content3 .img-wrapper img {
                    width: 100%; 
                    height: auto; 
                    border-radius: 10px;
                    box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
                    transition: all 0.3s ease-in-out;
                }
            
                ' . $addon_id . ' .tab-content3:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #F1E8FC; 
                }
            
                ' . $addon_id . ' .tab-content3 input[type="text"],
                ' . $addon_id . ' .tab-content3 select {
                    width: 80%; 
                    max-width: 80%; 
                    padding: 5px; 
                    box-sizing: border-box;
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content3-1 {
                    position: relative;
                    background-color: #F1E8FC; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    width: 101%;
                }
            
                ' . $addon_id . ' #section3.tab-content3-1.empty-packs {
                    padding: 10px 10px !important;   
                }                    
            
                ' . $addon_id . ' .tab-content3-1 .img-container2 {
                    width: 100%; 
                    height: 100%; 
                    position: relative; 
                }
            
                ' . $addon_id . ' .tab-content3-1 .img-wrapper {
                    position: relative;
                    width: 100%;
                    height: 100%;
                    overflow: hidden; 
                }
            
                ' . $addon_id . ' .tab-content3-1 .img-wrapper img {
                    width: 100%; 
                    height: auto; 
                    border-radius: 10px;
                    box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
                    transition: all 0.3s ease-in-out;
                }
            
                ' . $addon_id . ' .tab-content3-1 .button-container {
                    position: absolute; 
                    top: 110%; 
                    left: 29%; 
                    transform: translate(-50%, -50%); 
                    z-index: 1; 
                }
            
                ' . $addon_id . ' .tab-content3-1:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #F1E8FC; 
                }
            
                ' . $addon_id . ' .tab-content3-1 input[type="text"],
                ' . $addon_id . ' .tab-content3-1 select {
                    width: 80%; 
                    max-width: 80%; 
                    padding: 5px; 
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content4 {
                    position: relative;
                    background-color: #FEDFF0; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    width: 101%;
                }
            
                ' . $addon_id . ' .tab-content4:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FEDFF0;
                }
                ' . $addon_id . ' .tab-content4 input[type="text"],
                ' . $addon_id . ' .tab-content4 select,
                ' . $addon_id . ' .tab-content4 input[type="date"],
                ' . $addon_id . ' .tab-content4 input[type="file"] {
                    width: 480px;
                    max-width: 100%;
                    padding: 5px;
                    box-sizing: border-box;
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content4 input[type="file"] {
                    font-size: 12px;
                }
                ' . $addon_id . ' .tab-content4-4 {
                    position: relative;
                    background-color: #FEDFF0; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    width: 101%;
                }

                ' . $addon_id . ' .tab-content4-4:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FEDFF0; 
                }
                ' . $addon_id . ' .tab-content4-4 input[type="text"],
                ' . $addon_id . ' .tab-content4-4 select,
                ' . $addon_id . ' .tab-content4-4 input[type="date"] {
                    width: 100%;
                    max-width: 100px;
                    padding: 5px; 
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content4-4 input[type="file"] {
                    font-size: 12px;
                }
                ' . $addon_id . ' .tab-content4-2 {
                    position: relative;
                    background-color: #FEDFF0; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    width: 101%;
                }

                ' . $addon_id . ' .tab-content4-2:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FEDFF0;
                }
                ' . $addon_id . ' .tab-content4-2 input[type="text"],
                ' . $addon_id . ' .tab-content4-2 select{
                    width: 100%; 
                    max-width: 100%; 
                    padding: 5px;
                    box-sizing: border-box;
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content4-2 input[type="file"] {
                    font-size: 12px;
                }
                ' . $addon_id . ' .tab-content4-3 {
                    position: relative;
                    background-color: #FEDFF0; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    width: 101%;
                }
                ' . $addon_id . ' .tab-content4-3:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FEDFF0; 
                }
                ' . $addon_id . ' .tab-content4-3 input[type="text"],
                ' . $addon_id . ' .tab-content4-3 select,
                ' . $addon_id . ' .tab-content4-3 input[type="date"] {
                    width: 100%; 
                    max-width: 100%; 
                    padding: 5px; 
                    box-sizing: border-box;
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content4-3 input[type="file"] {
                    font-size: 12px;
                }
                ' . $addon_id . ' .tab-content4-1 {
                    position: relative;
                    background-color: #FEDFF0; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    width: 101%;
                }

                ' . $addon_id . ' .tab-content4-1:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FEDFF0;
                }
                ' . $addon_id . ' .tab-content4-1 input[type="text"],
                ' . $addon_id . ' .tab-content4-1 select,
                ' . $addon_id . ' .tab-content4-1 input[type="date"] {
                    width: 100%;
                    max-width: 100%;
                    padding: 5px;
                    box-sizing: border-box;
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content4-1 input[type="file"] {
                    font-size: 12px;
                }
                ' . $addon_id . ' .tab-content4-5 {
                    position: relative;
                    background-color: #FEDFF0; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    width: 101%;
                }

                ' . $addon_id . ' .tab-content4-5:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FEDFF0;
                }
                ' . $addon_id . ' .tab-content4-5 input[type="text"],
                ' . $addon_id . ' .tab-content4-5 select,
                ' . $addon_id . ' .tab-content4-5 input[type="date"] {
                    width: 100%; 
                    max-width: 100%; 
                    padding: 5px;
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content4-5 input[type="file"] {
                    font-size: 12px;
                }
                ' . $addon_id . ' .tab-content4-2-1 {
                    position: relative;
                    background-color: #FEDFF0; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    width: 101%;
                }

                ' . $addon_id . ' .tab-content4-2-1:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FEDFF0;
                }
                ' . $addon_id . ' .tab-content4-2-1 input[type="text"],
                ' . $addon_id . ' .tab-content4-2-1 select{
                    width: 100%; 
                    max-width: 100%;
                    padding: 5px;
                    box-sizing: border-box;
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content4-3-1 {
                    position: relative;
                    background-color: #FEDFF0;
                    border-radius: 0 0 20px 20px;
                    padding: 10px 10px !important;
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc;
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    width: 100%; /* Reduced width */
                    
                }
                ' . $addon_id . ' .tab-content4-3-1 .img-container2 img {
                    max-width: 20px; 
                    max-height: 20px; 
                }

                ' . $addon_id . ' .tab-content4-3-1:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FEDFF0;
                }
                ' . $addon_id . ' .tab-content4-3-1 input[type="text"],
                ' . $addon_id . ' .tab-content4-3-1 select,
                ' . $addon_id . ' .tab-content4-3-1 input[type="date"] {
                    width: 50%; 
                    max-width: 50%;
                    padding: 5px;
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                    
                }
                ' . $addon_id . ' .tab-content4-4-1 {
                    position: relative;
                    background-color: #FEDFF0; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    width: 101%;
                }

                ' . $addon_id . ' .tab-content4-4-1:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FEDFF0;
                }
                ' . $addon_id . ' .tab-content4-4-1 input[type="text"],
                ' . $addon_id . ' .tab-content4-4-1 select,
                ' . $addon_id . ' .tab-content4-4-1 input[type="date"] {
                    width: 100%;
                    max-width: 100%;
                    padding: 5px;
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                    
                }
                ' . $addon_id . ' .tab-content4-5-1 {
                    position: relative;
                    background-color: #FEDFF0; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    width: 101%;
                }

                ' . $addon_id . ' .tab-content4-5-1:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FEDFF0; 
                }
                ' . $addon_id . ' .tab-content4-5-1 input[type="text"],
                ' . $addon_id . ' .tab-content4-5-1 select,
                ' . $addon_id . ' .tab-content4-5-1 input[type="date"] {
                    width: 100%; 
                    max-width: 100%; 
                    padding: 5px; 
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content4-1-1 {
                    position: relative;
                    background-color: #FEDFF0; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    width: 101%;
                }

                ' . $addon_id . ' .tab-content4-1-1:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FEDFF0;
                }
                ' . $addon_id . ' .tab-content4-1-1 input[type="text"],
                ' . $addon_id . ' .tab-content4-1-1 select,
                ' . $addon_id . ' .tab-content4-1-1 input[type="date"] {
                    width: 100%;
                    max-width: 100%;
                    padding: 5px;
                    box-sizing: border-box;
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content5 {
                    position: relative;
                    background-color: #ffd8b2; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important;
                    width: 101%;
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                }

                ' . $addon_id . ' .tab-content5:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #ffd8b2;
                }
                ' . $addon_id . ' .tab-content5 input[type="text"],
                ' . $addon_id . ' .tab-content5 select{
                    width: 100%; 
                    max-width: 100%; 
                    padding: 5px;
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content5 input[type="date"] {
                    width: 100%; 
                    max-width: 100%; 
                    padding: 5px;
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                }
                
                ' . $addon_id . ' .tab-content5-1 {
                    position: relative;
                    background-color: #ffd8b2; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important;
                    width: 101%;
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                }

                ' . $addon_id . ' .tab-content5-1:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #ffd8b2;
                }
                ' . $addon_id . ' .tab-content5-1 input[type="text"],
                ' . $addon_id . ' .tab-content5-1 select{
                    width: 100%; 
                    max-width: 100%; 
                    padding: 5px;
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content5-1 input[type="date"] {
                    width: 100%; 
                    max-width: 100%; 
                    padding: 5px;
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                }
                
                
                ' . $addon_id . ' .tab-content6 {
                    position: relative;
                    background-color: #FFF5BA; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important;
                    width: 101%;
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                }
    
                ' . $addon_id . ' .tab-content6:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FFF5BA;
                }
                ' . $addon_id . ' .tab-content6 input[type="text"],
                ' . $addon_id . ' .tab-content6 select,
                ' . $addon_id . ' .tab-content6 input[type="date"],
                ' . $addon_id . ' .tab-content6 input[type="number"],
                ' . $addon_id . ' .tab-content6 textarea {
                    width: 100%; 
                    max-width: 100%; 
                    padding: 5px;
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content6-1 {
                    position: relative;
                    background-color: #FFF5BA; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important;
                    width: 101%; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                }
    
                ' . $addon_id . ' .tab-content6-1:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FFF5BA;
                }
                ' . $addon_id . ' .tab-content6-1 input[type="text"],
                ' . $addon_id . ' .tab-content6-1 select,
                ' . $addon_id . ' .tab-content6-1 input[type="date"],
                ' . $addon_id . ' .tab-content6-1 input[type="number"],
                ' . $addon_id . ' .tab-content6-1 textarea {
                    width: 100%; 
                    max-width: 100%; 
                    padding: 5px;
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content6-1-1 {
                    position: relative;
                    background-color: #FFF5BA; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important;
                    width: 101%;
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                }
    
                ' . $addon_id . ' .tab-content6-1-1:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FFF5BA;
                }
                ' . $addon_id . ' .tab-content6-1-1 input[type="text"],
                ' . $addon_id . ' .tab-content6-1-1 select,
                ' . $addon_id . ' .tab-content6-1-1 input[type="date"],
                ' . $addon_id . ' .tab-content6-1-1 input[type="number"],
                ' . $addon_id . ' .tab-content6-1-1 textarea {
                    width: 100%; 
                    max-width: 100%; 
                    padding: 5px;
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                }
            }
            ';
        $css .= '
            @media (max-width: 345px) {
                ' . $addon_id . ' .sppb-addon {
                    max-width: calc(100% - 20px);
                    padding: 10px;
                }
                ' . $addon_id . ' input[type="file"] {
                    font-size: 12px;
                }
            
                ' . $addon_id . ' .tab {
                    padding: 10px 10px !important; 
                    max-width: calc(100% - 10px); 
                    overflow-x: auto;
                    font-size: 7px !important;
                }

                ' . $addon_id . ' .tab  span {
                    font-size: 5px;
                }
            
                ' . $addon_id . ' .tab-content {
                    position: relative;
                    background-color: #DBE9FF; 
                    border-radius: 0 0 20px 20px;
                    padding: 10px 105px;
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    display: flex; 
                    justify-content: center;
                    align-items: center; 
                }
            
                ' . $addon_id . ' .tab-content-1 {
                    position: relative;
                    background-color: #DBE9FF; 
                    border-radius: 0 0 20px 20px;
                    padding: 10px 20px !important; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    width: 101%;
                }
                
                ' . $addon_id . ' .container {
                    display: flex;
                    flex-direction: column;
                    justify-content: center;
                    align-items: center; 
                    flex-wrap: wrap;
                }
               
                
                ' . $addon_id . ' .invoice-number {
                    font-size: 14px; 
                    margin-top: 5px; 
                }
                
                ' . $addon_id . ' .button-group button {
                    font-size: 10px; 
                    margin-left: 2px;
                }
        
                ' . $addon_id . ' .form-group {
                    margin-bottom: 1rem; 
                    flex-direction: column; 
                    align-items: center;
                    justify-content: center;
                }
        
               
                ' . $addon_id . ' .status-text {
                    font-size: 10px;
                    margin-top: 0.2em; 
                }
        
                
                ' . $addon_id . ' .form-control,
                ' . $addon_id . ' #document_upload,
                ' . $addon_id . ' select {
                    border-radius: 8px;
                    border: 2px solid #3498db;
                    font-size: 12px; 
                    margin-bottom: 0.5em;
                    box-sizing: border-box;
                    width: calc(100% - 2em); 
                    padding: 0.3em; 
                }

                ' . $addon_id . ' .form-group {
                    display: flex;
                    flex-direction: column;
                    justify-content: center;
                    align-items: center;
                    width: 100%;
                }
                ' . $addon_id . ' .form-group .btn,
                ' . $addon_id . ' .form-group .btn-danger,
                ' . $addon_id . ' .form-group .btn-primary,
                ' . $addon_id . ' .form-group .btn-warning {
                    border-radius: 8px;
                    padding: 0.5em 1em; 
                    font-size: 12px; 
                    margin-top: 1.5em; 
                    margin-left: auto; 
                    margin-right: auto; 
                    display: block; 
                }
                
                ' . $addon_id . ' form {
                    width: 100%;
                    box-sizing: border-box;
                }
                ' . $addon_id . ' .tab-content2 {
                    position: relative;
                    background-color: #E2FAE1; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 135px; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15); 
                    margin-top: -12px;
                }
            
                ' . $addon_id . ' .tab-content2 .img-container2 img {
                    width: 486px;
                    height: auto;
                    max-width: 100%; 
                    border-radius: 10px;
                    box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
                    transition: all 0.3s ease-in-out;    
                }                   
            
                ' . $addon_id . ' .tab-content2:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #E2FAE1; 
                }
            
                ' . $addon_id . ' .tab-content2-1 {
                    position: relative;
                    background-color: #E2FAE1;
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15); 
                    margin-top: -12px;
                    width: 101%;
                }
            
                ' . $addon_id . ' .tab-content2-1:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #E2FAE1; 
                }
            
                
                ' . $addon_id . ' .tab-content3 .button-container {
                    position: absolute; 
                    top: 110%; 
                    left: 50%;
                    transform: translate(-50%, -50%); 
                    z-index: 1; 
                }
                ' . $addon_id . ' .tab-content3 {
                    position: relative;
                    background-color: #F1E8FC; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important;
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    width: 100%;
                }
            
                ' . $addon_id . ' #section3.tab-content3.empty-packs {
                    padding: 10px 168px;  
                }                    
            
                ' . $addon_id . ' .tab-content3 .img-container2 {
                    width: 100%; 
                    height: 100%; 
                    position: relative; 
                }
            
                ' . $addon_id . ' .tab-content3 .img-wrapper {
                    position: relative;
                    width: 100%;
                    height: 100%;
                    overflow: hidden; 
                }
            
                ' . $addon_id . ' .tab-content3 .img-wrapper img {
                    width: 100%; 
                    height: auto; 
                    border-radius: 10px;
                    box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
                    transition: all 0.3s ease-in-out;
                }
            
                ' . $addon_id . ' .tab-content3:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #F1E8FC; 
                }
            
                ' . $addon_id . ' .tab-content3 input[type="text"],
                ' . $addon_id . ' .tab-content3 select {
                    width: 80%; 
                    max-width: 80%; 
                    padding: 5px; 
                    box-sizing: border-box;
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content3-1 {
                    position: relative;
                    background-color: #F1E8FC; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    width: 101%;
                }
            
                ' . $addon_id . ' #section3.tab-content3-1.empty-packs {
                    padding: 10px 10px !important;   
                }                    
            
                ' . $addon_id . ' .tab-content3-1 .img-container2 {
                    width: 100%; 
                    height: 100%; 
                    position: relative; 
                }
            
                ' . $addon_id . ' .tab-content3-1 .img-wrapper {
                    position: relative;
                    width: 100%;
                    height: 100%;
                    overflow: hidden; 
                }
            
                ' . $addon_id . ' .tab-content3-1 .img-wrapper img {
                    width: 100%; 
                    height: auto; 
                    border-radius: 10px;
                    box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
                    transition: all 0.3s ease-in-out;
                }
            
                ' . $addon_id . ' .tab-content3-1 .button-container {
                    position: absolute; 
                    top: 110%; 
                    left: 29%; 
                    transform: translate(-50%, -50%); 
                    z-index: 1; 
                }
            
                ' . $addon_id . ' .tab-content3-1:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #F1E8FC; 
                }
            
                ' . $addon_id . ' .tab-content3-1 input[type="text"],
                ' . $addon_id . ' .tab-content3-1 select {
                    width: 80%; 
                    max-width: 80%; 
                    padding: 5px; 
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content4 {
                    position: relative;
                    background-color: #FEDFF0; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    width: 101%;
                }
            
                ' . $addon_id . ' .tab-content4:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FEDFF0;
                }
                ' . $addon_id . ' .tab-content4 input[type="text"],
                ' . $addon_id . ' .tab-content4 select,
                ' . $addon_id . ' .tab-content4 input[type="date"],
                ' . $addon_id . ' .tab-content4 input[type="file"] {
                    width: 480px;
                    max-width: 100%;
                    padding: 5px;
                    box-sizing: border-box;
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content4 input[type="file"] {
                    font-size: 12px;
                }
                ' . $addon_id . ' .tab-content4-4 {
                    position: relative;
                    background-color: #FEDFF0; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    width: 101%;
                }

                ' . $addon_id . ' .tab-content4-4:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FEDFF0; 
                }
                ' . $addon_id . ' .tab-content4-4 input[type="text"],
                ' . $addon_id . ' .tab-content4-4 select,
                ' . $addon_id . ' .tab-content4-4 input[type="date"] {
                    width: 100%;
                    max-width: 100px;
                    padding: 5px; 
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content4-4 input[type="file"] {
                    font-size: 12px;
                }
                ' . $addon_id . ' .tab-content4-2 {
                    position: relative;
                    background-color: #FEDFF0; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    width: 101%;
                }

                ' . $addon_id . ' .tab-content4-2:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FEDFF0;
                }
                ' . $addon_id . ' .tab-content4-2 input[type="text"],
                ' . $addon_id . ' .tab-content4-2 select{
                    width: 100%; 
                    max-width: 100%; 
                    padding: 5px;
                    box-sizing: border-box;
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content4-2 input[type="file"] {
                    font-size: 12px;
                }
                ' . $addon_id . ' .tab-content4-3 {
                    position: relative;
                    background-color: #FEDFF0; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    width: 101%;
                }
                ' . $addon_id . ' .tab-content4-3:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FEDFF0; 
                }
                ' . $addon_id . ' .tab-content4-3 input[type="text"],
                ' . $addon_id . ' .tab-content4-3 select,
                ' . $addon_id . ' .tab-content4-3 input[type="date"] {
                    width: 100%; 
                    max-width: 100%; 
                    padding: 5px; 
                    box-sizing: border-box;
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content4-3 input[type="file"] {
                    font-size: 12px;
                }
                ' . $addon_id . ' .tab-content4-1 {
                    position: relative;
                    background-color: #FEDFF0; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    width: 101%;
                }

                ' . $addon_id . ' .tab-content4-1:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FEDFF0;
                }
                ' . $addon_id . ' .tab-content4-1 input[type="text"],
                ' . $addon_id . ' .tab-content4-1 select,
                ' . $addon_id . ' .tab-content4-1 input[type="date"] {
                    width: 100%;
                    max-width: 100%;
                    padding: 5px;
                    box-sizing: border-box;
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content4-1 input[type="file"] {
                    font-size: 12px;
                }
                ' . $addon_id . ' .tab-content4-5 {
                    position: relative;
                    background-color: #FEDFF0; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    width: 101%;
                }

                ' . $addon_id . ' .tab-content4-5:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FEDFF0;
                }
                ' . $addon_id . ' .tab-content4-5 input[type="text"],
                ' . $addon_id . ' .tab-content4-5 select,
                ' . $addon_id . ' .tab-content4-5 input[type="date"] {
                    width: 100%; 
                    max-width: 100%; 
                    padding: 5px;
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content4-5 input[type="file"] {
                    font-size: 12px;
                }
                ' . $addon_id . ' .tab-content4-2-1 {
                    position: relative;
                    background-color: #FEDFF0; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    width: 101%;
                }

                ' . $addon_id . ' .tab-content4-2-1:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FEDFF0;
                }
                ' . $addon_id . ' .tab-content4-2-1 input[type="text"],
                ' . $addon_id . ' .tab-content4-2-1 select{
                    width: 100%; 
                    max-width: 100%;
                    padding: 5px;
                    box-sizing: border-box;
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content4-3-1 {
                    position: relative;
                    background-color: #FEDFF0;
                    border-radius: 0 0 20px 20px;
                    padding: 10px 10px !important;
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc;
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    width: 100%; /* Reduced width */
                    
                }
                ' . $addon_id . ' .tab-content4-3-1 .img-container2 img {
                    max-width: 20px; 
                    max-height: 20px; 
                }

                ' . $addon_id . ' .tab-content4-3-1:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FEDFF0;
                }
                ' . $addon_id . ' .tab-content4-3-1 input[type="text"],
                ' . $addon_id . ' .tab-content4-3-1 select,
                ' . $addon_id . ' .tab-content4-3-1 input[type="date"] {
                    width: 50%; 
                    max-width: 50%;
                    padding: 5px;
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                    
                }
                ' . $addon_id . ' .tab-content4-4-1 {
                    position: relative;
                    background-color: #FEDFF0; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    width: 101%;
                }

                ' . $addon_id . ' .tab-content4-4-1:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FEDFF0;
                }
                ' . $addon_id . ' .tab-content4-4-1 input[type="text"],
                ' . $addon_id . ' .tab-content4-4-1 select,
                ' . $addon_id . ' .tab-content4-4-1 input[type="date"] {
                    width: 100%;
                    max-width: 100%;
                    padding: 5px;
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                    
                }
                ' . $addon_id . ' .tab-content4-5-1 {
                    position: relative;
                    background-color: #FEDFF0; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    width: 101%;
                }

                ' . $addon_id . ' .tab-content4-5-1:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FEDFF0; 
                }
                ' . $addon_id . ' .tab-content4-5-1 input[type="text"],
                ' . $addon_id . ' .tab-content4-5-1 select,
                ' . $addon_id . ' .tab-content4-5-1 input[type="date"] {
                    width: 100%; 
                    max-width: 100%; 
                    padding: 5px; 
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content4-1-1 {
                    position: relative;
                    background-color: #FEDFF0; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    width: 101%;
                }

                ' . $addon_id . ' .tab-content4-1-1:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FEDFF0;
                }
                ' . $addon_id . ' .tab-content4-1-1 input[type="text"],
                ' . $addon_id . ' .tab-content4-1-1 select,
                ' . $addon_id . ' .tab-content4-1-1 input[type="date"] {
                    width: 100%;
                    max-width: 100%;
                    padding: 5px;
                    box-sizing: border-box;
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content5 {
                    position: relative;
                    background-color: #ffd8b2; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important;
                    width: 101%;
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                }

                ' . $addon_id . ' .tab-content5:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #ffd8b2;
                }
                ' . $addon_id . ' .tab-content5 input[type="text"],
                ' . $addon_id . ' .tab-content5 select{
                    width: 100%; 
                    max-width: 100%; 
                    padding: 5px;
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content5 input[type="date"] {
                    width: 100%; 
                    max-width: 100%; 
                    padding: 5px;
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                }
                
                ' . $addon_id . ' .tab-content5-1 {
                    position: relative;
                    background-color: #ffd8b2; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important;
                    width: 101%;
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                }

                ' . $addon_id . ' .tab-content5-1:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #ffd8b2;
                }
                ' . $addon_id . ' .tab-content5-1 input[type="text"],
                ' . $addon_id . ' .tab-content5-1 select{
                    width: 100%; 
                    max-width: 100%; 
                    padding: 5px;
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content5-1 input[type="date"] {
                    width: 100%; 
                    max-width: 100%; 
                    padding: 5px;
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                }
                
                
                ' . $addon_id . ' .tab-content6 {
                    position: relative;
                    background-color: #FFF5BA; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important;
                    width: 101%;
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                }
    
                ' . $addon_id . ' .tab-content6:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FFF5BA;
                }
                ' . $addon_id . ' .tab-content6 input[type="text"],
                ' . $addon_id . ' .tab-content6 select,
                ' . $addon_id . ' .tab-content6 input[type="date"],
                ' . $addon_id . ' .tab-content6 input[type="number"],
                ' . $addon_id . ' .tab-content6 textarea {
                    width: 100%; 
                    max-width: 100%; 
                    padding: 5px;
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content6-1 {
                    position: relative;
                    background-color: #FFF5BA; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important;
                    width: 101%; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                }
    
                ' . $addon_id . ' .tab-content6-1:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FFF5BA;
                }
                ' . $addon_id . ' .tab-content6-1 input[type="text"],
                ' . $addon_id . ' .tab-content6-1 select,
                ' . $addon_id . ' .tab-content6-1 input[type="date"],
                ' . $addon_id . ' .tab-content6-1 input[type="number"],
                ' . $addon_id . ' .tab-content6-1 textarea {
                    width: 100%; 
                    max-width: 100%; 
                    padding: 5px;
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content6-1-1 {
                    position: relative;
                    background-color: #FFF5BA; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important;
                    width: 101%;
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                }
    
                ' . $addon_id . ' .tab-content6-1-1:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FFF5BA;
                }
                ' . $addon_id . ' .tab-content6-1-1 input[type="text"],
                ' . $addon_id . ' .tab-content6-1-1 select,
                ' . $addon_id . ' .tab-content6-1-1 input[type="date"],
                ' . $addon_id . ' .tab-content6-1-1 input[type="number"],
                ' . $addon_id . ' .tab-content6-1-1 textarea {
                    width: 100%; 
                    max-width: 100%; 
                    padding: 5px;
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                }
            }
            ';
        $css .= '
            @media (max-width: 321px) {
                ' . $addon_id . ' .sppb-addon {
                    max-width: calc(100% - 20px);
                    padding: 10px;
                }
                ' . $addon_id . ' input[type="file"] {
                    font-size: 12px;
                }
            
                ' . $addon_id . ' .tab {
                    padding: 7px 7px !important; 
                    max-width: calc(100% - 10px); 
                    overflow-x: auto;
                    font-size: 6px !important;
                }

                ' . $addon_id . ' .tab  span {
                    font-size: 5px;
                }
            
                ' . $addon_id . ' .tab-content {
                    position: relative;
                    background-color: #DBE9FF; 
                    border-radius: 0 0 20px 20px;
                    padding: 10px 105px;
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    display: flex; 
                    justify-content: center;
                    align-items: center; 
                }
            
                ' . $addon_id . ' .tab-content-1 {
                    position: relative;
                    background-color: #DBE9FF; 
                    border-radius: 0 0 20px 20px;
                    padding: 10px 20px !important; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    width: 101%;
                }
                
                ' . $addon_id . ' .container {
                    display: flex;
                    flex-direction: column;
                    justify-content: center;
                    align-items: center; 
                    flex-wrap: wrap;
                }
               
                
                ' . $addon_id . ' .invoice-number {
                    font-size: 14px; 
                    margin-top: 5px; 
                }
                
                ' . $addon_id . ' .button-group button {
                    font-size: 10px; 
                    margin-left: 2px;
                }
        
                ' . $addon_id . ' .form-group {
                    margin-bottom: 1rem; 
                    flex-direction: column; 
                    align-items: center;
                    justify-content: center;
                }
        
               
                ' . $addon_id . ' .status-text {
                    font-size: 10px;
                    margin-top: 0.2em; 
                }
        
                
                ' . $addon_id . ' .form-control,
                ' . $addon_id . ' #document_upload,
                ' . $addon_id . ' select {
                    border-radius: 8px;
                    border: 2px solid #3498db;
                    font-size: 12px; 
                    margin-bottom: 0.5em;
                    box-sizing: border-box;
                    width: calc(100% - 2em); 
                    padding: 0.3em; 
                }

                ' . $addon_id . ' .form-group {
                    display: flex;
                    flex-direction: column;
                    justify-content: center;
                    align-items: center;
                    width: 100%;
                }
                ' . $addon_id . ' .form-group .btn,
                ' . $addon_id . ' .form-group .btn-danger,
                ' . $addon_id . ' .form-group .btn-primary,
                ' . $addon_id . ' .form-group .btn-warning {
                    border-radius: 8px;
                    padding: 0.5em 1em; 
                    font-size: 12px; 
                    margin-top: 1.5em; 
                    margin-left: auto; 
                    margin-right: auto; 
                    display: block; 
                }
                
                ' . $addon_id . ' form {
                    width: 100%;
                    box-sizing: border-box;
                }
                ' . $addon_id . ' .tab-content2 {
                    position: relative;
                    background-color: #E2FAE1; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 135px; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15); 
                    margin-top: -12px;
                }
            
                ' . $addon_id . ' .tab-content2 .img-container2 img {
                    width: 486px;
                    height: auto;
                    max-width: 100%; 
                    border-radius: 10px;
                    box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
                    transition: all 0.3s ease-in-out;    
                }                   
            
                ' . $addon_id . ' .tab-content2:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #E2FAE1; 
                }
            
                ' . $addon_id . ' .tab-content2-1 {
                    position: relative;
                    background-color: #E2FAE1;
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15); 
                    margin-top: -12px;
                    width: 101%;
                }
            
                ' . $addon_id . ' .tab-content2-1:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #E2FAE1; 
                }
            
                
                ' . $addon_id . ' .tab-content3 .button-container {
                    position: absolute; 
                    top: 110%; 
                    left: 50%;
                    transform: translate(-50%, -50%); 
                    z-index: 1; 
                }
                ' . $addon_id . ' .tab-content3 {
                    position: relative;
                    background-color: #F1E8FC; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important;
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    width: 100%;
                }
            
                ' . $addon_id . ' #section3.tab-content3.empty-packs {
                    padding: 10px 168px;  
                }                    
            
                ' . $addon_id . ' .tab-content3 .img-container2 {
                    width: 100%; 
                    height: 100%; 
                    position: relative; 
                }
            
                ' . $addon_id . ' .tab-content3 .img-wrapper {
                    position: relative;
                    width: 100%;
                    height: 100%;
                    overflow: hidden; 
                }
            
                ' . $addon_id . ' .tab-content3 .img-wrapper img {
                    width: 100%; 
                    height: auto; 
                    border-radius: 10px;
                    box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
                    transition: all 0.3s ease-in-out;
                }
            
                ' . $addon_id . ' .tab-content3:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #F1E8FC; 
                }
            
                ' . $addon_id . ' .tab-content3 input[type="text"],
                ' . $addon_id . ' .tab-content3 select {
                    width: 80%; 
                    max-width: 80%; 
                    padding: 5px; 
                    box-sizing: border-box;
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content3-1 {
                    position: relative;
                    background-color: #F1E8FC; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    width: 101%;
                }
            
                ' . $addon_id . ' #section3.tab-content3-1.empty-packs {
                    padding: 10px 10px !important;   
                }                    
            
                ' . $addon_id . ' .tab-content3-1 .img-container2 {
                    width: 100%; 
                    height: 100%; 
                    position: relative; 
                }
            
                ' . $addon_id . ' .tab-content3-1 .img-wrapper {
                    position: relative;
                    width: 100%;
                    height: 100%;
                    overflow: hidden; 
                }
            
                ' . $addon_id . ' .tab-content3-1 .img-wrapper img {
                    width: 100%; 
                    height: auto; 
                    border-radius: 10px;
                    box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
                    transition: all 0.3s ease-in-out;
                }
            
                ' . $addon_id . ' .tab-content3-1 .button-container {
                    position: absolute; 
                    top: 110%; 
                    left: 29%; 
                    transform: translate(-50%, -50%); 
                    z-index: 1; 
                }
            
                ' . $addon_id . ' .tab-content3-1:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #F1E8FC; 
                }
            
                ' . $addon_id . ' .tab-content3-1 input[type="text"],
                ' . $addon_id . ' .tab-content3-1 select {
                    width: 80%; 
                    max-width: 80%; 
                    padding: 5px; 
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content4 {
                    position: relative;
                    background-color: #FEDFF0; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    width: 101%;
                }
            
                ' . $addon_id . ' .tab-content4:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FEDFF0;
                }
                ' . $addon_id . ' .tab-content4 input[type="text"],
                ' . $addon_id . ' .tab-content4 select,
                ' . $addon_id . ' .tab-content4 input[type="date"],
                ' . $addon_id . ' .tab-content4 input[type="file"] {
                    width: 480px;
                    max-width: 100%;
                    padding: 5px;
                    box-sizing: border-box;
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content4 input[type="file"] {
                    font-size: 12px;
                }
                ' . $addon_id . ' .tab-content4-4 {
                    position: relative;
                    background-color: #FEDFF0; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    width: 101%;
                }

                ' . $addon_id . ' .tab-content4-4:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FEDFF0; 
                }
                ' . $addon_id . ' .tab-content4-4 input[type="text"],
                ' . $addon_id . ' .tab-content4-4 select,
                ' . $addon_id . ' .tab-content4-4 input[type="date"] {
                    width: 100%;
                    max-width: 100px;
                    padding: 5px; 
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content4-4 input[type="file"] {
                    font-size: 12px;
                }
                ' . $addon_id . ' .tab-content4-2 {
                    position: relative;
                    background-color: #FEDFF0; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    width: 101%;
                }

                ' . $addon_id . ' .tab-content4-2:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FEDFF0;
                }
                ' . $addon_id . ' .tab-content4-2 input[type="text"],
                ' . $addon_id . ' .tab-content4-2 select{
                    width: 100%; 
                    max-width: 100%; 
                    padding: 5px;
                    box-sizing: border-box;
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content4-2 input[type="file"] {
                    font-size: 12px;
                }
                ' . $addon_id . ' .tab-content4-3 {
                    position: relative;
                    background-color: #FEDFF0; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    width: 101%;
                }
                ' . $addon_id . ' .tab-content4-3:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FEDFF0; 
                }
                ' . $addon_id . ' .tab-content4-3 input[type="text"],
                ' . $addon_id . ' .tab-content4-3 select,
                ' . $addon_id . ' .tab-content4-3 input[type="date"] {
                    width: 100%; 
                    max-width: 100%; 
                    padding: 5px; 
                    box-sizing: border-box;
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content4-3 input[type="file"] {
                    font-size: 12px;
                }
                ' . $addon_id . ' .tab-content4-1 {
                    position: relative;
                    background-color: #FEDFF0; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    width: 101%;
                }

                ' . $addon_id . ' .tab-content4-1:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FEDFF0;
                }
                ' . $addon_id . ' .tab-content4-1 input[type="text"],
                ' . $addon_id . ' .tab-content4-1 select,
                ' . $addon_id . ' .tab-content4-1 input[type="date"] {
                    width: 100%;
                    max-width: 100%;
                    padding: 5px;
                    box-sizing: border-box;
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content4-1 input[type="file"] {
                    font-size: 12px;
                }
                ' . $addon_id . ' .tab-content4-5 {
                    position: relative;
                    background-color: #FEDFF0; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    width: 101%;
                }

                ' . $addon_id . ' .tab-content4-5:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FEDFF0;
                }
                ' . $addon_id . ' .tab-content4-5 input[type="text"],
                ' . $addon_id . ' .tab-content4-5 select,
                ' . $addon_id . ' .tab-content4-5 input[type="date"] {
                    width: 100%; 
                    max-width: 100%; 
                    padding: 5px;
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content4-5 input[type="file"] {
                    font-size: 12px;
                }
                ' . $addon_id . ' .tab-content4-2-1 {
                    position: relative;
                    background-color: #FEDFF0; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    width: 101%;
                }

                ' . $addon_id . ' .tab-content4-2-1:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FEDFF0;
                }
                ' . $addon_id . ' .tab-content4-2-1 input[type="text"],
                ' . $addon_id . ' .tab-content4-2-1 select{
                    width: 100%; 
                    max-width: 100%;
                    padding: 5px;
                    box-sizing: border-box;
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content4-3-1 {
                    position: relative;
                    background-color: #FEDFF0;
                    border-radius: 0 0 20px 20px;
                    padding: 10px 10px !important;
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc;
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    width: 100%; /* Reduced width */
                    
                }
                ' . $addon_id . ' .tab-content4-3-1 .img-container2 img {
                    max-width: 20px; 
                    max-height: 20px; 
                }

                ' . $addon_id . ' .tab-content4-3-1:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FEDFF0;
                }
                ' . $addon_id . ' .tab-content4-3-1 input[type="text"],
                ' . $addon_id . ' .tab-content4-3-1 select,
                ' . $addon_id . ' .tab-content4-3-1 input[type="date"] {
                    width: 50%; 
                    max-width: 50%;
                    padding: 5px;
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                    
                }
                ' . $addon_id . ' .tab-content4-4-1 {
                    position: relative;
                    background-color: #FEDFF0; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    width: 101%;
                }

                ' . $addon_id . ' .tab-content4-4-1:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FEDFF0;
                }
                ' . $addon_id . ' .tab-content4-4-1 input[type="text"],
                ' . $addon_id . ' .tab-content4-4-1 select,
                ' . $addon_id . ' .tab-content4-4-1 input[type="date"] {
                    width: 100%;
                    max-width: 100%;
                    padding: 5px;
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                    
                }
                ' . $addon_id . ' .tab-content4-5-1 {
                    position: relative;
                    background-color: #FEDFF0; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    width: 101%;
                }

                ' . $addon_id . ' .tab-content4-5-1:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FEDFF0; 
                }
                ' . $addon_id . ' .tab-content4-5-1 input[type="text"],
                ' . $addon_id . ' .tab-content4-5-1 select,
                ' . $addon_id . ' .tab-content4-5-1 input[type="date"] {
                    width: 100%; 
                    max-width: 100%; 
                    padding: 5px; 
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content4-1-1 {
                    position: relative;
                    background-color: #FEDFF0; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                    width: 101%;
                }

                ' . $addon_id . ' .tab-content4-1-1:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FEDFF0;
                }
                ' . $addon_id . ' .tab-content4-1-1 input[type="text"],
                ' . $addon_id . ' .tab-content4-1-1 select,
                ' . $addon_id . ' .tab-content4-1-1 input[type="date"] {
                    width: 100%;
                    max-width: 100%;
                    padding: 5px;
                    box-sizing: border-box;
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content5 {
                    position: relative;
                    background-color: #ffd8b2; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important;
                    width: 101%;
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                }

                ' . $addon_id . ' .tab-content5:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #ffd8b2;
                }
                ' . $addon_id . ' .tab-content5 input[type="text"],
                ' . $addon_id . ' .tab-content5 select{
                    width: 100%; 
                    max-width: 100%; 
                    padding: 5px;
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content5 input[type="date"] {
                    width: 100%; 
                    max-width: 100%; 
                    padding: 5px;
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                }
                
                ' . $addon_id . ' .tab-content5-1 {
                    position: relative;
                    background-color: #ffd8b2; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important;
                    width: 101%;
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                }

                ' . $addon_id . ' .tab-content5-1:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #ffd8b2;
                }
                ' . $addon_id . ' .tab-content5-1 input[type="text"],
                ' . $addon_id . ' .tab-content5-1 select{
                    width: 100%; 
                    max-width: 100%; 
                    padding: 5px;
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content5-1 input[type="date"] {
                    width: 100%; 
                    max-width: 100%; 
                    padding: 5px;
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                }
                
                
                ' . $addon_id . ' .tab-content6 {
                    position: relative;
                    background-color: #FFF5BA; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important;
                    width: 101%;
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                }
    
                ' . $addon_id . ' .tab-content6:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FFF5BA;
                }
                ' . $addon_id . ' .tab-content6 input[type="text"],
                ' . $addon_id . ' .tab-content6 select,
                ' . $addon_id . ' .tab-content6 input[type="date"],
                ' . $addon_id . ' .tab-content6 input[type="number"],
                ' . $addon_id . ' .tab-content6 textarea {
                    width: 100%; 
                    max-width: 100%; 
                    padding: 5px;
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content6-1 {
                    position: relative;
                    background-color: #FFF5BA; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important;
                    width: 101%; 
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                }
    
                ' . $addon_id . ' .tab-content6-1:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FFF5BA;
                }
                ' . $addon_id . ' .tab-content6-1 input[type="text"],
                ' . $addon_id . ' .tab-content6-1 select,
                ' . $addon_id . ' .tab-content6-1 input[type="date"],
                ' . $addon_id . ' .tab-content6-1 input[type="number"],
                ' . $addon_id . ' .tab-content6-1 textarea {
                    width: 100%; 
                    max-width: 100%; 
                    padding: 5px;
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                }
                ' . $addon_id . ' .tab-content6-1-1 {
                    position: relative;
                    background-color: #FFF5BA; 
                    border-radius: 0 0 20px 20px; 
                    padding: 10px 20px !important;
                    width: 101%;
                    transition: background-color 0.3s ease;
                    border: 1px solid #ccc; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.15);
                    margin-top: -12px;
                }
    
                ' . $addon_id . ' .tab-content6-1-1:before {
                    content: "";
                    position: absolute;
                    top: 0px;
                    left: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #FFF5BA;
                }
                ' . $addon_id . ' .tab-content6-1-1 input[type="text"],
                ' . $addon_id . ' .tab-content6-1-1 select,
                ' . $addon_id . ' .tab-content6-1-1 input[type="date"],
                ' . $addon_id . ' .tab-content6-1-1 input[type="number"],
                ' . $addon_id . ' .tab-content6-1-1 textarea {
                    width: 100%; 
                    max-width: 100%; 
                    padding: 5px;
                    box-sizing: border-box; 
                    margin: 0 auto;
                    text-align: center;
                }
            }
            ';

        $css .= '
        @media (min-width: 769px) {
            ' . $addon_id . ' .form-group {
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                width: 100%;
            }
        
            ' . $addon_id . ' .form-group label {
                width: 100%;
                text-align: center; 
            }
        
            ' . $addon_id . ' .form-group .btn,
            ' . $addon_id . ' .form-group .btn-primary,
            ' . $addon_id . ' .form-group .btn-danger,
            ' . $addon_id . ' .form-group .btn-warning {
                margin-right: 0.5em;
            }

            ' . $addon_id . ' select {
                flex: 1 0 100%;
                border-radius: 8px;
                border: 1px solid #3498db;
                transition: border 0.3s ease-in-out; 
                padding: 6px !important;
                box-sizing: border-box;
                font-size: 20px;
            }
            
            ' . $addon_id . ' select:focus {
                border-color: #007bff !important; 
            }            
            
        
            ' . $addon_id . ' .container {
                display: flex;
                justify-content: center;
                align-items: center; 
                flex-wrap: wrap;
            }
            
            ' . $addon_id . ' .img-container2 {
                box-sizing: border-box;
                flex: 1 0 calc(20% - 20px); 
                max-width: calc(20% - 20px);
                margin: 10px;
                
            }
            
            
            ' . $addon_id . ' .invoice-number {
                font-size: 14px; 
                margin-top: 5px; 
            }
            
            ' . $addon_id . ' .button-group button {
                font-size: 10px; 
                margin-left: 5px;
            }
        
            ' . $addon_id . ' input[type="text"] {
                width: 100%; 
                font-size: 18px; 
                padding: 12px; 
                margin-bottom: 10px;
            }
            ' . $addon_id . ' input[type="number"] {
                flex: 1 0 100%;
                border-radius: 8px;
                border: 2px solid #3498db;
                transition: border 0.3s ease-in-out; 
                padding: 10px;
                box-sizing: border-box;
                font-size: 16px;
                -moz-appearance: textfield; /* Firefox */
            }
        
            ' . $addon_id . ' input[type="number"]::-webkit-inner-spin-button,
            ' . $addon_id . ' input[type="number"]::-webkit-outer-spin-button {
                -webkit-appearance: none;
                margin: 0;
            }
        
            ' . $addon_id . ' input[type="number"]:focus {
                border-color: #007bff !important; 
            }
        }
        
        ';
        return $css;
    }

    public static function getTemplate()
    {
        $lodash = new Lodash('#sppb-addon-{{ data.id }}');
        $output = '<style type="text/css">';
        $output .= $lodash->alignment('text-align', '.sppb-addon-rich_text', 'data.alignment');
        $output .= '</style>';

        $output .= '
                <#
                    let btnUrl = "";
                    let target = "";
                    let rel = "";
                    if (!_.isEmpty(data.url) && _.isObject(data.url)){
                        const {url, page, menu, type, new_tab, nofollow} = data?.url;
                        if(type === "url") btnUrl = url;
                        if(type === "menu") btnUrl = menu;
                        if(type === "page") btnUrl = page ? `index.php?option=com_sppagebuilder&view=page&id=${page}` : "";
        
                        target = new_tab ? "_blank": "";
                        rel = nofollow ? "noopener noreferrer": "";
                    }
                    
                #>
            ';
        $output .= '<div class="sppb-addon sppb-addon-rich_text {{ data.class}}">';
        $output .= '<p>Archivo: {{ data.archivo }}</p>';
        $output .= '<p>Seleccionar opción: {{ data.select_option }}</p>';
        $output .= '</div>';
        return $output;
    }
}
?>
