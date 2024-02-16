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
class SppagebuilderAddonCustomertable extends SppagebuilderAddons
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
        $output .= '<h3>Registros Clientes</h3>';
        $output .= '<table class="table">';
        $output .= '<thead>';
        $output .= '<tr>';
        $output .= '<th>Nombre</th>';
        $output .= '<th>Credito</th>';
        $output .= '<th>Tiempo Pago</th>';
        $output .= '</tr>';
        $output .= '</thead>';
        $output .= '<tbody>';
        $existingRecords = $this->getExistingRecords();
        foreach ($existingRecords as $record) {
            $output .= '<tr>';
            $output .= '<td>' . $record['customer_name'] . '</td>';
            $output .= '<td>' . $record['customer_credit'] . '</td>';
            $output .= '<td>' . $record['customer_payment_time'] . '</td>';
            $output .= '<td>';
            $output .= '<button class="btn btn-danger" onclick="deleteRecord(' . $record['customer_id'] . ')"><i class="fa fa-times"></i></button>';
            $output .= '<a href="https://mwt.one/index.php/en/?option=com_sppagebuilder&view=page&id=21&customer_id=' . $record['customer_id'] . '" class="btn btn-primary">Actualizar</a>';
            $output .= '</td>';
            $output .= '</tr>';
        }
        
        $output .= '</tbody>';
        $output .= '</table>';
        $output .= '</div>';
        $output .= '<div>';
        $output .= '<br>';
        $output .= '<a href="https://mwt.one/index.php?option=com_sppagebuilder&view=page&id=21" class="btn btn-warning">Registro Nuevo</a>';
        $output .= '</div>';
        $output .= '<div>';
        $output .= '<br>';
        $output .= '<a href="https://mwt.one/index.php/" class="btn btn-success">Volver</a>';
        $output .= '</div>';
        $output .= '<script>';
        $output .= 'function deleteRecord(recordId) {';
        $output .= '    var confirmation = confirm("¿Estás seguro de que quieres eliminar este registro?");';
        $output .= '    if (confirmation) {';
        $output .= '        var xhr = new XMLHttpRequest();';
        $output .= '        xhr.onreadystatechange = function() {';
        $output .= '            if (xhr.readyState === 4) {';
        $output .= '                if (xhr.status === 200) {';
        $output .= '                    alert(xhr.responseText);';
        $output .= '                    window.location.reload();';
        $output .= '                } else {';
        $output .= '                    alert("Error al eliminar el registro");';
        $output .= '                }';
        $output .= '            }';
        $output .= '        };';
        $output .= '        xhr.open("POST", "", true);';
        $output .= '        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");';
        $output .= '        xhr.send("deleteRecordId=" + encodeURIComponent(recordId));';
        $output .= '    }';
        $output .= '}';
        $output .= '</script>';
        if (isset($_POST['deleteRecordId'])) {
            $recordIdToDelete = JFactory::getApplication()->input->getInt('deleteRecordId');
            try {
                $result = $this->deletecustomeruser($recordIdToDelete);
                if ($result) {
                    echo "Registro eliminado con éxito";
                } else {
                    echo "Error al eliminar el registro";
                }
            } catch (Exception $e) {
                echo "Error: " . $e->getMessage();
            }
            JFactory::getApplication()->close();
        }
        return $output;
        }
    public function getExistingRecords() {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select($db->quoteName(['cu.customer_id', 'cu.customer_name', 'cu.customer_credit', 'cu.customer_payment_time']))
            ->from($db->quoteName('josmwt_customer') . ' AS cu');
    
        $db->setQuery($query);
        $results = $db->loadAssocList();
    
        return $results;
    }
    public function deletecustomeruser($recordIdToDelete) {
        $db = JFactory::getDbo();
        try {
            $query = $db->getQuery(true);
            $query->delete($db->quoteName('josmwt_customer'));
            $query->where($db->quoteName('customer_id') . ' = ' . (int)$recordIdToDelete);
            $db->setQuery($query);
            $db->execute();
            return true; 
        } catch (Exception $e) {
            throw new Exception('Error eliminando el registro: ' . $e->getMessage());
        }
    }
    
    public function getcustomer() {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select($db->quoteName(['customer_id', 'customer_name']));
        $query->from($db->quoteName('josmwt_customer'));
        $db->setQuery($query);
        $results = $db->loadAssocList();
    
        return $results;
        }
        public function getusers() {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->select($db->quoteName(['id', 'name','username']));
            $query->from($db->quoteName('josmwt_users'));
            $db->setQuery($query);
            $results = $db->loadAssocList();
        
            return $results;
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
                ' . $addon_id . ' .form-group .btn-danger,
                ' . $addon_id . ' .form-group .btn-warning {
                    border-radius: 5px;
                    padding: 0.5em 1em; 
                    font-size: 8px; 
                    margin-top: 0.5em;
                }
                
                ' . $addon_id . ' .button-group button,
                ' . $addon_id . ' .button-group a {
                    font-size: 8px; 
                    margin-left: 2px;
                    padding: 0.3em 0.1em;
                }
                .btn-primary {
                    font-size: 13px; 
                    padding: 0.3em 0.5em;
                    margin-top: 3px; 
                }
                
                .btn-danger {
                    font-size: 12px; 
                    padding: 0.3em 0.5em;
                    margin-bottom: 3px;
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