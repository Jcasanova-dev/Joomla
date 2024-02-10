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
    $output .= '<label for="first_names">First Names:</label>';
    $output .= '<input type="text" id="first_names" name="first_names" ';
    $output .= 'oninput="validateFirstNames(this)" required>';
    $output .= '<span id="first_names_error" style="color: red;"></span>';
    $output .= '</div>';
    $output .= '<script>';
    $output .= 'function validateFirstNames(input) {';
    $output .= '    var pattern = /^[a-zA-Z ]+$/;';
    $output .= '    var errorMessage = "Solo se permiten letras y espacios en blanco en el campo First Names";';
    $output .= '    var errorElement = document.getElementById("first_names_error");';
    $output .= '    if (!pattern.test(input.value)) {';
    $output .= '        errorElement.textContent = errorMessage;';
    $output .= '    } else {';
    $output .= '        errorElement.textContent = "";';
    $output .= '    }';
    $output .= '}';
    $output .= '</script>';
    $output .= '<div>';
    $output .= '<label for="last_names">Last Names:</label>';
    $output .= '<input type="text" id="last_names" name="last_names" ';
    $output .= 'oninput="validateLastNames(this)" required>';
    $output .= '<span id="last_names_error" style="color: red;"></span>';
    $output .= '</div>';
    $output .= '<script>';
    $output .= 'function validateLastNames(input) {';
    $output .= '    var pattern = /^[a-zA-Z ]+$/;';
    $output .= '    var errorMessage = "Solo se permiten letras y espacios en blanco en el campo Last Names";';
    $output .= '    var errorElement = document.getElementById("last_names_error");';
    $output .= '    if (!pattern.test(input.value)) {';
    $output .= '        errorElement.textContent = errorMessage;';
    $output .= '    } else {';
    $output .= '        errorElement.textContent = "";';
    $output .= '    }';
    $output .= '}';
    $output .= '</script>';
    $output .= '<div>';
    $output .= '<label for="email_address">Email Address:</label>';
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
    $output .= '<label for="phone_number">Phone Number:</label>';
    $output .= '<input type="tel" id="phone_number" name="phone_number" ';
    $output .= 'oninput="validatePhoneNumber(this)" required>';
    $output .= '<span id="phone_number_error" style="color: red;"></span>';
    $output .= '</div>';
    $output .= '<script>';
    $output .= 'function validatePhoneNumber(input) {';
    $output .= '    var pattern = /^[0-9+]+$/;';
    $output .= '    var errorMessage = "Solo se permiten números y el símbolo \'+\' en el campo Phone Number";';
    $output .= '    var errorElement = document.getElementById("phone_number_error");';
    $output .= '    if (!pattern.test(input.value)) {';
    $output .= '        errorElement.textContent = errorMessage;';
    $output .= '    } else {';
    $output .= '        errorElement.textContent = "";';
    $output .= '    }';
    $output .= '}';
    $output .= '</script>';
    $output .= '<div>';
    $output .= '<label for="customer">Cliente:</label>';
    $output .= '<input type="text" class="customer" name="customer[]" list="customer_name_list">';
    $output .= '<datalist id="customer_name_list"></datalist>';
    $output .= '<br>';
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
    $output .= '        var nuevoCampo = \'<div><label for="customer_nuevo">Cliente:</label>\';';
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
    $output .= '<label for="additional">Additional:</label>';
    $output .= '<textarea id="additional" name="additional" rows="4" cols="50" ';
    $output .= 'oninput="countCharacters(this)" maxlength="500"></textarea>';
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
            $body .= "<p>Adicional: $additional</p>";
    
            // Correo electrónico de destino
            //$to = ['alejandro@muitowork.com', 'alvaro@muitowork.com'];
            $to = 'alejandro@muitowork.com';
            $subject = "Solicitud Creacion de Usuario";
    
            // Enviar correo electrónico
            $this->sendEmail($to, $subject, $body);
    
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

    public function sendEmail($to, $subject, $body) {
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

        $css .= $alignment;

        // Aquí agregas tu código CSS
        $css .= '
        .sppb-addon.sppb-addon-rich_text {
            font-family: Arial, sans-serif; /* Cambia la fuente del formulario */
            color: #333; /* Cambia el color del texto */
            background-color: #f9f9f9; /* Cambia el color de fondo del formulario */
            padding: 20px; /* Añade un poco de espacio alrededor del formulario */
            border-radius: 5px; /* Añade bordes redondeados al formulario */
        }
        
        .sppb-addon.sppb-addon-rich_text form div {
            margin-bottom: 15px;
        }
        
        .sppb-addon.sppb-addon-rich_text form label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold; /* Hace que las etiquetas sean negritas */
        }
        
        .sppb-addon.sppb-addon-rich_text form input[type="text"],
        .sppb-addon.sppb-addon-rich_text form input[type="email"],
        .sppb-addon.sppb-addon-rich_text form input[type="tel"],
        .sppb-addon.sppb-addon-rich_text form select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc; /* Añade un borde a los campos de entrada */
            border-radius: 4px; /* Añade bordes redondeados a los campos de entrada */
        }
        
        .sppb-addon.sppb-addon-rich_text form input[type="submit"] {
            cursor: pointer;
            background-color: #4CAF50; /* Cambia el color de fondo del botón */
            color: white; /* Cambia el color del texto del botón */
            border: none; /* Elimina el borde del botón */
            border-radius: 4px; /* Añade bordes redondeados al botón */
            transition: all 0.5s; /* Añade una transición a todos los cambios de estilo del botón */
        }
        
        .sppb-addon.sppb-addon-rich_text form input[type="submit"]:hover {
            background-color: #45a049; /* Cambia el color de fondo del botón cuando el mouse está encima */
        }
        .sppb-addon.sppb-addon-rich_text form textarea {
            width: 100%; /* Hace que el campo de entrada ocupe toda la línea */
            padding: 10px; /* Espacio interno del campo de entrada */
            border: 1px solid #ccc; /* Añade un borde al campo de entrada */
            border-radius: 4px; /* Añade bordes redondeados al campo de entrada */
            resize: vertical; /* Permite al usuario redimensionar el campo de entrada verticalmente */
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

