<!DOCTYPE html>
<html>
<head>
    <link href="https://fonts.googleapis.com/css2?family=PT+Serif&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'PT Serif', serif;
        }
    </style>
</head>
<body>
<?php

/**
 * @package SP Page Builder
 * @author JoomShaper http://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2022 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
//no direct access
defined('_JEXEC') or die('Restricted access');

class SppagebuilderAddonRich_text extends SppagebuilderAddons
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
    list($link, $target) = AddonHelper::parseLink($settings, 'url');
    $output .= '<div class="sppb-addon sppb-addon-rich_text' . $class . '">';
    $output .= '<form action="" method="post" enctype="multipart/form-data">';
    $session = JFactory::getSession();
    $userId = $session->get('user')->id;
    $db = JFactory::getDbo();
    $output .= '<div>';
    $output .= '<br>';
    $output .= '<label for="first_names">Nombre</label>';
    $output .= '<input type="text" id="first_names" name="first_names" ';
    $output .= 'oninput="validateFirstNames(this)" required>';
    $output .= '<span id="first_names_error" style="color: red;"></span>';
    $output .= '</div>';
    $output .= '<script>';
    $output .= 'function validateFirstNames(input) {';
    $output .= '    var pattern = /^[a-zA-Z ]+$/;';
    $output .= '    var errorMessage = "Solo se permiten letras y espacios en blanco en el campo Nombre";';
    $output .= '    var errorElement = document.getElementById("first_names_error");';
    $output .= '    if (!pattern.test(input.value)) {';
    $output .= '        errorElement.textContent = errorMessage;';
    $output .= '    } else {';
    $output .= '        errorElement.textContent = "";';
    $output .= '    }';
    $output .= '}';
    $output .= '</script>';
    $output .= '<div>';
    $output .= '<br>';
    $output .= '<label for="last_names">Apellidos</label>';
    $output .= '<input type="text" id="last_names" name="last_names" ';
    $output .= 'oninput="validateLastNames(this)" required>';
    $output .= '<span id="last_names_error" style="color: red;"></span>';
    $output .= '</div>';
    $output .= '<script>';
    $output .= 'function validateLastNames(input) {';
    $output .= '    var pattern = /^[a-zA-Z ]+$/;';
    $output .= '    var errorMessage = "Solo se permiten letras y espacios en blanco en el campo Apellidos";';
    $output .= '    var errorElement = document.getElementById("last_names_error");';
    $output .= '    if (!pattern.test(input.value)) {';
    $output .= '        errorElement.textContent = errorMessage;';
    $output .= '    } else {';
    $output .= '        errorElement.textContent = "";';
    $output .= '    }';
    $output .= '}';
    $output .= '</script>';
    $output .= '<div>';
    $output .= '<br>';
    $output .= '<label for="email_address">Email</label>';
    $output .= '<input type="email" id="email_address" name="email_address" ';
    $output .= 'oninput="validateEmailAddress(this)" required>';
    $output .= '<span id="email_address_error" style="color: red;"></span>';
    $output .= '</div>';
    $output .= '<script>';
    $output .= 'function validateEmailAddress(input) {';
    $output .= '    var pattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;';
    $output .= '    var errorMessage = "Por favor, ingrese una dirección de correo electrónico válida";';
    $output .= '    var errorElement = document.getElementById("email_address_error");';
    $output .= '    if (!pattern.test(input.value)) {';
    $output .= '        errorElement.textContent = errorMessage;';
    $output .= '    } else {';
    $output .= '        errorElement.textContent = "";';
    $output .= '    }';
    $output .= '}';
    $output .= '</script>';
    $output .= '<div>';
    $output .= '<br>';
    $output .= '<label for="phone_number">Numero Telefonico</label>';
    $output .= '<input type="tel" id="phone_number" name="phone_number" ';
    $output .= 'oninput="validatePhoneNumber(this)" required>';
    $output .= '<span id="phone_number_error" style="color: red;"></span>';
    $output .= '</div>';
    $output .= '<script>';
    $output .= 'function validatePhoneNumber(input) {';
    $output .= '    var pattern = /^[0-9+]+$/;';
    $output .= '    var errorMessage = "Solo se permiten números y el símbolo \'+\' en el campo Numero Telefonico";';
    $output .= '    var errorElement = document.getElementById("phone_number_error");';
    $output .= '    if (!pattern.test(input.value)) {';
    $output .= '        errorElement.textContent = errorMessage;';
    $output .= '    } else {';
    $output .= '        errorElement.textContent = "";';
    $output .= '    }';
    $output .= '}';
    $output .= '</script>';
    $output .= '<div>';
    $output .= '<br>';
    $output .= '<label for="customer">Cliente</label>';
    $output .= '<input type="text" class="customer" name="customer[]" list="customer_name_list">';
    $output .= '<datalist id="customer_name_list"></datalist>';
    $output .= '<div id="customer_container">';
    $output .= '</div>';
    $output .= '<br>'; 
    $output .= '<button type="button" id="agregar_customer">Agregar Cliente</button>';
    $output .= '<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>';
    $output .= '<script>';
    $output .= '$(document).ready(function() {';
    $output .= '    $("body").on("input", ".customer", function() {';
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
    $output .= '    $("#agregar_customer").on("click", function() {';
    $output .= '        var nuevoCampo = \'<div><label for="customer_nuevo">Cliente</label>\';';
    $output .= '        nuevoCampo += \'<input type="text" class="customer" name="customer_nuevo[]" list="customer_name_list_\' + Date.now() + \'">\';';
    $output .= '        nuevoCampo += \'<datalist id="customer_name_list_\' + Date.now() + \'"></datalist>\';';
    $output .= '        nuevoCampo += \'<button type="button" class="eliminar_customer">X</button></div>\';';
    $output .= '        $("#customer_container").append(nuevoCampo);';
    $output .= '    });';
    $output .= '    $("body").on("click", ".eliminar_customer", function() {';
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
    function getCustomers($query2) {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select($db->quoteName(array('c.customer_id', 'c.customer_name')));
        $query->from($db->quoteName('josmwt_customer', 'c'));
        $query->where(
            $db->quoteName('c.customer_name') . " LIKE " . $db->quote('%' . $db->escape($query2, true) . '%')
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
                $output[] = $result['customer_name'];
            }
        }
    
        return implode(',', $output);
    }
    
    if (isset($_POST['query'])) {
        $query2 = $_POST['query'];
        echo getCustomers($query2);
        exit();
    }

    $output .= '<div>';
    $output .= '<br>';
    $output .= '<label for="additional">Observaciones</label>';
    $output .= '<textarea id="additional" name="additional" rows="4" cols="50" ';
    $output .= 'oninput="countCharacters(this)" maxlength="500" class="form-control"></textarea>';
    $output .= '<div id="character_count" style="color: gray;"></div>';
    $output .= '</div>';
    $output .= '<script>';
    $output .= 'function countCharacters(textarea) {';
    $output .= '    var maxLength = 500;';
    $output .= '    var currentLength = textarea.value.length;';
    $output .= '    var remainingCharacters = maxLength - currentLength;';
    $output .= '    var countElement = document.getElementById("character_count");';
    $output .= '    countElement.textContent = "Characters remaining: " + remainingCharacters;';
    $output .= '}';
    $output .= '</script>';
    $output .= '<br>';
    $output .= '<input type="submit" name="submit_form" class="btn btn-primary" value="Enviar">';
    $output .= '</form>';   
    $output .= '</div>';
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        try {
            // Obtener datos del formulario
            $firstNames = htmlspecialchars($_POST['first_names']);
            $lastNames = htmlspecialchars($_POST['last_names']);
            $emailAddress = htmlspecialchars($_POST['email_address']);
            $phoneNumber = htmlspecialchars($_POST['phone_number']);
            $additional = htmlspecialchars($_POST['additional']);
            $customer = $_POST['customer'];
            $customer_nuevos = $_POST['customer_nuevo'];
            if (!empty($customer)) {
                $cadena_customer = implode(',', $customer);
            } 
            if (!empty($customer_nuevos)) {
                $cadena_customer_nuevos = implode(',', $customer_nuevos);
                $customer2 = $cadena_customer . ", " . $cadena_customer_nuevos;
                
            } else {
                $customer2 = $cadena_customers;
               
            }   
            // Construir cuerpo del correo
            $body = "<p>Nombres: $firstNames</p>";
            $body .= "<p>Apellidos: $lastNames</p>";
            $body .= "<p>Correo electrónico: $emailAddress</p>";
            $body .= "<p>Número de teléfono: $phoneNumber</p>";
            $body .= "<p>Cliente Asociados: $customer2</p>";
            $body .= "<p>Obeservaciones: $additional</p>";
    
            // Correo electrónico de destino
            $to2 = 'alejandro@muitowork.com';
            $to = 'alvaro@muitowork.com';
            $subject = "Solicitud Creacion de Usuario";
    
            // Enviar correo electrónico
            $this->sendEmail($to, $to2,$subject, $body);
    
            // Mostrar mensaje de éxito al usuario
            echo '<script>alert("El formulario se envió con éxito.");</script>';
        } catch (Exception $e) {
            JFactory::getDocument()->addScriptDeclaration('
                document.addEventListener("DOMContentLoaded", function() {
                    alert("Error al enviar el formulario: ' . $e->getMessage() . '");
                });
            ');
            throw new Exception('Error al enviar el formulario: ' . $e->getMessage());
        }
    }
    return $output;
}
    public function getCustomers() {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select($db->quoteName(['c.customer_id', 'c.customer_name']));
        $query->from($db->quoteName('josmwt_customer', 'c'));
        try {
            $db->setQuery($query);
            $results = $db->loadAssocList();
        
            return $results;
        } catch (Exception $e) {
            JFactory::getApplication()->enqueueMessage("Error: " . $e->getMessage());
            return array(); // Devuelve un array vacío en caso de error
        }
    }

    public function sendEmail($to, $to2,$subject, $body) {
        try {
            $config = JFactory::getConfig();
            $mailfrom = $config->get('mailfrom');
            $fromname = $config->get('fromname');
    
            if (empty($mailfrom) || empty($fromname)) {
                throw new Exception('Invalid mail configuration: mailfrom and fromname cannot be empty.');
            }
            if (empty($to)) {
                throw new Exception('No recipients specified.');
            }
            $mail = JFactory::getMailer();
            $sender = array($mailfrom, $fromname);
            $mail->setSender($sender);
            if (is_array($to)) {
                $to = implode(',', $to);
            }
            $mail->addRecipient($to);
            $mail->addRecipient($to2);
            $mail->setSubject($subject);
            $mail->setBody($body);
            $mail->isHtml(true);
            $mail->send();
        } catch (Exception $e) {
            JFactory::getApplication()->enqueueMessage('Error sending email: ' . $e->getMessage(), 'error');
            throw new Exception('Error sending email: ' . $e->getMessage());
        }
    }
    
    public function css()
        {
            $addon_id = '#sppb-addon-' . $this->addon->id;
            $cssHelper = new CSSHelper($addon_id);
            $css = '';
            $settings = $this->addon->settings;
            $settings->alignment = CSSHelper::parseAlignment($settings, 'alignment');
            $alignment = $cssHelper->generateStyle('.sppb-addon.sppb-addon-rich_text', $settings, ['alignment' => 'text-align'], false);
            $css .= "body { font-family: 'PT Serif', serif; }";
            $css .= $alignment;
            $css .= '
                ' . $addon_id . ' .sppb-addon {
                    max-width: 100%; /* Cambia el ancho máximo al 100% para que ocupe todo el ancho de la pantalla */
                    margin: auto;
                    padding: 20px;
                    border-radius: 10px;
                    box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
                    background-color: #ffffff; 
                    transition: all 0.3s ease-in-out; 
                }
                ' . $addon_id . ' .sppb-addon:hover {
                    box-shadow: 0 0 30px rgba(0, 0, 0, 0.2);
                }
            ';

            $css .= '
                ' . $addon_id . ' .form-group {
                    margin-bottom: 2rem;
                    display: flex;
                    flex-wrap: wrap;
                    gap: 12px;
                    align-items: flex-start; 
                    flex-direction: column; 
                }

                ' . $addon_id . ' .form-group label {
                    margin-bottom: 5px;
                    font-size: 18px; /* Aumenta el tamaño de la fuente para que se vea más grande */
                }

                ' . $addon_id . ' .form-control {
                    flex: 1 0 100%;
                    border-radius: 8px;
                    border: 2px solid #3498db;
                    transition: border 0.3s ease-in-out; 
                    padding: 10px;
                    box-sizing: border-box;
                    font-size: 18px; /* Aumenta el tamaño de la fuente para que se vea más grande */   
                }

                ' . $addon_id . ' .form-control:focus {
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
            ' . $addon_id . '#additional-container {
                display: flex;
                flex-direction: column;
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
                    filter: grayscale(100%);
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
                
                ' . $addon_id . ' .img-container2 img {
                    width: 100%;
                    height: auto;
                    max-width: 100%; 
                    border-radius: 10px;
                    box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
                    transition: all 0.3s ease-in-out;
                    filter: grayscale(100%);
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
            @media (max-width: 768px) {
                ' . $addon_id . ' .sppb-addon {
                    max-width: 100%;
                    padding: 1em; 
                    box-sizing: border-box; 
                    margin: auto;
                    text-align: center; 
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
                    filter: grayscale(100%);
                    
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
                .status-text {
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

                ' . $addon_id . ' .form-group .btn,
                ' . $addon_id . ' .form-group .btn-primary,
                ' . $addon_id . ' .form-group .btn-danger,
                ' . $addon_id . ' .form-group .btn-warning {
                    border-radius: 8px;
                    padding: 0.5em 1em; 
                    font-size: 12px; 
                    margin-top: 0.5em;
                }

                ' . $addon_id . ' form {
                    width: 100%;
                    box-sizing: border-box;
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
            
            ' . $addon_id . ' .img-container2 img {
                width: 100%;
                height: auto;
                max-width: 100%; 
                border-radius: 10px;
                box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
                transition: all 0.3s ease-in-out;
                filter: grayscale(100%);
                
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
   </body>
</html>