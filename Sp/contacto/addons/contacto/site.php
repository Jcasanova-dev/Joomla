<?php

/**
 * @package SP Page Builder
 * @author JoomShaper http://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2022 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
//no direct access
defined('_JEXEC') or die('Restricted access');

class SppagebuilderAddonContacto extends SppagebuilderAddons
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
        $output .= '<div>';
        $output .= '<form action="' . $_SERVER['PHP_SELF'] . '" method="post" enctype="multipart/form-data">';
        $output .= '<div>';
        $output .= '<label for="name">Nombre Completo:</label>';
        $output .= '<input type="text" id="name" name="name" ';
        $output .= 'oninput="validateCompanyName(this)" required>';
        $output .= '<span id="company_name_error" style="color: red;"></span>';
        $output .= '</div>';
        $output .= '<script>';
        $output .= 'function validateCompanyName(input) {';
        $output .= '    var pattern = /^[a-zA-Z ]+$/;';
        $output .= '    var errorMessage = "Solo se permiten letras en el campo Nombre";';
        $output .= '    var errorElement = document.getElementById("company_name_error");';
        $output .= '    if (!pattern.test(input.value)) {';
        $output .= '        errorElement.textContent = errorMessage;';
        $output .= '    } else {';
        $output .= '        errorElement.textContent = "";';
        $output .= '    }';
        $output .= '}';
        $output .= '</script>';
        $output .= '<div>';
            $output .= '<label for="email_address">Email:</label>';
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
        $output .= '<label for="country">Pais:</label>';
        $output .= '<select id="country" name="country">';
        $countrys = $this->getpais();
        if (!empty($countrys)) {
            foreach ($countrys as $country) {
                $output .= '<option value="' . $country['id'] . '">' . $country['nombre'] . '</option>';
            }
        } else {
            $output .= '<option value="">No countrys found</option>';
        }
        $output .= '</select>';
        $output .= '</div>';
        $output .= '<div>';
            $output .= '<label for="city">Ciudad:</label>';
            $output .= '<input type="text" id="city" name="city" ';
            $output .= 'oninput="validateCity(this)" required>';
            $output .= '<span id="city_error" style="color: red;"></span>';
        $output .= '</div>';
            $output .= '<script>';
            $output .= 'function validateCity(input) {';
            $output .= '    var pattern = /^[a-zA-Z ]+$/;';
            $output .= '    var errorMessage = "Solo se permiten letras en el campo Ciudad";';
            $output .= '    var errorElement = document.getElementById("city_error");';
            $output .= '    if (!pattern.test(input.value)) {';
            $output .= '        errorElement.textContent = errorMessage;';
            $output .= '    } else {';
            $output .= '        errorElement.textContent = "";';
            $output .= '    }';
            $output .= '}';
            $output .= '</script>';
            $output .= '<div>';
            $output .= '<label for="phone_number">Numero Telefonico:</label>';
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
            $output .= '<label for="additional">Comentarios:</label>';
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
            $output .= '    countElement.textContent = "Caracteres restantes: " + remainingCharacters;';
            $output .= '}';
            $output .= '</script>';            
        $output .= '<input type="submit" name="submit_form" class="btn btn-primary" value="Enviar">';
        $output .= '</form>';   
        $output .= '</div>';
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            try {
                // Obtener datos del formulario
                $company = htmlspecialchars($_POST['name']);
                $emailAddress = htmlspecialchars($_POST['email_address']);
                $phoneNumber = htmlspecialchars($_POST['phone_number']);
                $country = htmlspecialchars($_POST['country']);
                $additional = htmlspecialchars($_POST['additional']);
                $city = htmlspecialchars($_POST['city']);
                $countrys = $this->getpais();
                $countryName = '';
                if (!empty($countrys)) {
                    foreach ($countrys as $countryData) {
                        if ($countryData['id'] == $country) {
                            $countryName = $countryData['nombre'];
                            break;
                        }
                    }
                }
                $body = "<p>Nombre: $company</p>";
                $body .= "<p>Correo electrónico: $emailAddress</p>";
                $body .= "<p>Country: $countryName</p>";
                $body .= "<p>City: $city</p>";
                $body .= "<p>Número de teléfono: $phoneNumber</p>";
                $body .= "<p>Comentarios: $additional</p>";
                //$to = ['alejandro@muitowork.com', 'alvaro@muitowork.com'];
                $to = 'alejandro@muitowork.com';
                $subject = "Dudas o Comentarios";
                $this->sendEmail($to, $subject, $body);
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
        public function getpais() {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->select($db->quoteName(['id', 'nombre']));
            $query->from($db->quoteName('paises'));
            $db->setQuery($query);
            $results = $db->loadAssocList();

            return $results;
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