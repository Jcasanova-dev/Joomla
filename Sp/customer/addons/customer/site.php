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
class SppagebuilderAddonCustomer extends SppagebuilderAddons
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
        $session = JFactory::getSession();
        $userId = $session->get('user')->id;
        $db = JFactory::getDbo();
        $output .= '<div>';
        $output .= '<form action="" method="post" enctype="multipart/form-data">';
        $output .= '<div class="form-group">';
        $output .= '<br>';   
            $output .= '<div>';
            $output .= '<br>';
            $output .= '<label for="number_purchase">Nombre de la Empresa:</label>';
            $output .= '<input type="text" id="nombre_cliente" name="nombre_cliente" ';
            $output .= 'oninput="validateAlphabeticInput(this)" required>';
            $output .= '<span id="number_purchase_error" style="color: red;"></span>';
            $output .= '</div>';
            $output .= '<script>';
            $output .= 'function validateAlphabeticInput(input) {';
            $output .= '    var pattern = /^[a-zA-Z ]+$/;';
            $output .= '    var errorMessage = "Solo se permiten letras;";';
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
            $output .= '<label for="tyme_credit">Dias de Credito Disponibles:</label>';
            $output .= '<input type="number" id="yme_credit" name="tyme_credit" ';
            $output .= 'oninput="validateAlphabeticInput2(this)" required>';
            $output .= '<span id="tyme_credit_error" style="color: red;"></span>';
            $output .= '</div>';
            $output .= '<script>';
            $output .= 'function validateAlphabeticInput2(input) {';
            $output .= '    var pattern = /^[0-9]+$/;';
            $output .= '    var errorMessage = "Solo se permiten numeros;";';
            $output .= '    var errorElement = document.getElementById("tyme_credit_error");';
            $output .= '    if (!pattern.test(input.value)) {';
            $output .= '        errorElement.textContent = errorMessage;';
            $output .= '    } else {';
            $output .= '        errorElement.textContent = "";';
            $output .= '    }';
            $output .= '}';
            $output .= '</script>';
            $output .= '<div>';
            $output .= '<br>';
            $output .= '<label for="credit">Monto Credito Disponibles:</label>';
            $output .= '<input type="number" id="credit" name="credit" ';
            $output .= 'oninput="validateAlphabeticInput3(this)" required>';
            $output .= '<span id="credit_error" style="color: red;"></span>';
            $output .= '</div>';
            $output .= '<script>';
            $output .= 'function validateAlphabeticInput3(input) {';
            $output .= '    var pattern = /^[0-9]+$/;';
            $output .= '    var errorMessage = "Solo se permiten numeros;";';
            $output .= '    var errorElement = document.getElementById("credit_error");';
            $output .= '    if (!pattern.test(input.value)) {';
            $output .= '        errorElement.textContent = errorMessage;';
            $output .= '    } else {';
            $output .= '        errorElement.textContent = "";';
            $output .= '    }';
            $output .= '}';
            $output .= '</script>';
            $output .= '<div>';
            $output .= '<label for="tax_id">Tax ID:</label>';
            $output .= '<input type="text" id="tax_id" name="tax_id" ';
            $output .= 'oninput="validateTaxID(this)" required>';
            $output .= '<span id="tax_id_error" style="color: red;"></span>';
            $output .= '</div>';
            $output .= '<script>';
            $output .= 'function validateTaxID(input) {';
            $output .= '    var pattern = /^[0-9-]+$/;';
            $output .= '    var errorMessage = "Solo se permiten números y el guion \'-\';";';
            $output .= '    var errorElement = document.getElementById("tax_id_error");';
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
            $output .= '<label for="country">Country:</label>';
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
            $output .= '<label for="city">City:</label>';
            $output .= '<input type="text" id="city" name="city" ';
            $output .= 'oninput="validateCity(this)" required>';
            $output .= '<span id="city_error" style="color: red;"></span>';
            $output .= '</div>';
            $output .= '<script>';
            $output .= 'function validateCity(input) {';
            $output .= '    var pattern = /^[a-zA-Z ]+$/;';  // Se añade el espacio al patrón
            $output .= '    var errorMessage = "Solo se permiten letras y espacios en el campo City";';
            $output .= '    var errorElement = document.getElementById("city_error");';
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
            $output .= '<br>';
            $output .= '<label for="document_upload">Subir Contrato: </label>';
            $output .= '<input type="file" id="document_upload" name="document_upload" >';
            $output .= '</div>';
            $output .= '<div class="button-group">';
            $output .= '<input type="submit" name="upload_form" class="btn btn-primary" value="Enviar">';
            $output .= '</div>';  
            $output .= '</div>';
            $output .= '<div>';
            $output .= '<br>';
            $output .= '<a href="https://mwt.one/index.php/" class="btn btn-success">Volver</a>';
            $output .= '</div>';
            $output .= '</form>';
            $output .= '</div>';
        
            //Procesador de formulario
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                if (isset($_POST['upload_form'])) {
                    $nombre_cliente = $_POST['nombre_cliente'];
                    $tyme_credit = $_POST['tyme_credit'];
                    $credit = $_POST['credit'];
                    $tax_id =  $_POST['tax_id'];
                    $email_address = $_POST['email_address'];
                    $country = $_POST['country'];
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
                    $city =$_POST['city'];
                    $phone = $_POST['phone_number'];
                    if (isset($_FILES['document_upload']) && $_FILES['document_upload']['error'] == UPLOAD_ERR_OK) {
                        $documentUpload = $_FILES['document_upload']['tmp_name'];
                        try {
                            $this->uploadAndSaveDocument($documentUpload, $nombre_cliente, $tyme_credit, $credit, $tax_id, $email_address, $countryName, $city, $phone);
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
                }elseif (isset($_POST['delete_files'])) {
                    $order_number = $_POST['order_number'];
                    $this->deleteUploadedFiles($order_number, $userId);
                    echo '<script type="text/javascript">window.location.href = window.location.href;</script>';
                    exit();
                }
            }

            return $output;
    }   
    //Funcion para subir orden de compra
    protected function uploadAndSaveDocument($documentUpload, $nombre_cliente, $tyme_credit, $credit, $tax_id, $email_address, $countryName, $city, $phone)
    {
        try {
            $uploadDir = '/images/contrato/';
            $filename = 'pdf_' . time() . '.pdf';
            $filePath = JPATH_ROOT . $uploadDir . $filename;

            if (!file_exists(JPATH_ROOT . $uploadDir)) {
                mkdir(JPATH_ROOT . $uploadDir, 0755, true);
            }

            if (move_uploaded_file($documentUpload, $filePath)) {
                $this->saveDataToPreforma($uploadDir . $filename, $nombre_cliente, $tyme_credit, $credit, $tax_id, $email_address, $countryName, $city, $phone);
            } else {
                throw new Exception('Error moving uploaded file');
            }
        } catch (Exception $e) {
            throw new Exception('Error uploading and saving document: ' . $e->getMessage());
        }
    }    
   //funcion para guardar el nuimero de orden de compra
   protected function saveDataToPreforma($filePath, $nombre_cliente, $tyme_credit, $credit, $tax_id, $email_address, $countryName, $city, $phone)
    {
        $db = JFactory::getDbo();

        try {
            $query = $db->getQuery(true);
            $columns = array('customer_contrato', 'customer_name', 'customer_credit', 'customer_payment_time', 'customer_tax', 'customer_email', 'customer_country', 'customer_city','customer_phone');
            $values = array($db->quote($filePath), $db->quote($nombre_cliente), $db->quote($credit), $db->quote($tyme_credit), $db->quote($tax_id), $db->quote($email_address), $db->quote($countryName), $db->quote($city), $db->quote($phone));

            $query
                ->insert($db->quoteName('josmwt_customer'))
                ->columns($db->quoteName($columns))
                ->values(implode(',', $values));

            $db->setQuery($query);
            $db->execute();
        } catch (Exception $e) {
            throw new Exception('Error saving data to preforma: ' . $e->getMessage());
        }
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
    //funcion para obtener los archivos orden de compra
    public function getUploadedFiles($order_number,$userId) {
        try {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->select('preformar');
            $query->from($db->quoteName('josmwt_preforma','p'));
            $query->join('INNER', $db->quoteName('josmwt_hikashop_user', 'u') . ' ON (' . $db->quoteName('p.user_id') . ' = ' . $db->quoteName('u.user_id') . ')');
            $query->where($db->quoteName('u.user_cms_id') . ' = ' . (int)$userId); 
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
    //funcion para obtener los archivos preforma
    
    //funcion para eliminar los archivos ordenes de compra
    public function deleteUploadedFiles($order_number,$userId) {
        $filePathRelative = $this->getUploadedFiles($order_number,$userId);
        $status = 'confirmed';
        if ($filePathRelative !== null) {
            $filePathFull = $_SERVER['DOCUMENT_ROOT'].$filePathRelative;
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
    
        //funcion para obtener el userid de hikashop
        public function getUserId($userId) {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->select($db->quoteName('user_email'));
            $query->from($db->quoteName('josmwt_hikashop_user'));
            $query->where($db->quoteName('user_cms_id') . ' = '. $db->quote($userId));
            $db->setQuery($query);
            $result = $db->loadAssoc();
            return $result;
        }        
        //funcion para enviar email
        public function sendEmail($to, $subject, $body, $orderNumber) {
            try {
                $mail = JFactory::getMailer();
                $config = JFactory::getConfig();
                $sender = array( 
                    $config->get('mailfrom'),
                    $config->get('fromname') 
                );
                $mail->setSender($sender);
                $mail->addRecipient($to);
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
            ' . $addon_id . ' .sppb-addon {
                max-width: 1200px;
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
            @media (max-width: 768px) {
                ' . $addon_id . ' .sppb-addon {
                    max-width: 100%;
                    padding: 1em; 
                    box-sizing: border-box; 
                    margin: auto;
                    text-align: center; 
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
                    margin-left: 10px;
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
                max-width: 100%; 
                margin: auto;
            }
        
            ' . $addon_id . ' .form-control,
            ' . $addon_id . ' select {
                font-size: 20px; /* Aumenta el tamaño de la fuente */
            }
            
            ' . $addon_id . ' .form-group label {
                font-size: 20px; /* Aumenta el tamaño de la fuente de las etiquetas */
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
            
            ' . $addon_id . ' .form-group .btn,
            ' . $addon_id . ' .form-group .btn-primary,
            ' . $addon_id . ' .form-group .btn-danger,
            ' . $addon_id . ' .form-group .btn-warning {
                margin-right: 0.5em;
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