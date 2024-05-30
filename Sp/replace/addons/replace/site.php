<?php

/**
 * @package SP Page Builder
 * @author JoomShaper http://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2022 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
//no direct access
defined('_JEXEC') or die('Restricted access');

class SppagebuilderAddonReplace extends SppagebuilderAddons
{
    public function __construct($addon)
    {
        parent::__construct($addon);
    }

    public function render()
    {
        $settings = $this->addon->settings;
        list($link, $target) = AddonHelper::parseLink($settings, 'url');
        $icon = JFactory::getApplication()->input->get('img', '', 'string');
        $descripcion = JFactory::getApplication()->input->get('descripcion', '', 'raw');
        $descripcion = htmlspecialchars_decode($descripcion, ENT_QUOTES);
        $clase2 = JFactory::getApplication()->input->get('clase', '', 'string');
        $output = '';
        $matches = $this->getPluginParamsByClase($clase2);
        if (!empty($matches)) {
            $output .= '<div class="custom-container-wrapper">'; // Contenedor principal
            foreach ($matches as $match) {
                $image_to_insert = $match['image_to_insert'];
                $descripcion = $match['descripcion'];

                $output .= '<div class="custom-container">';
                $output .= '<div class="custom-image-wrapper">';
                $output .= '<img src="' . htmlspecialchars($image_to_insert) . '" alt="" title="" width="120" height="auto">';
                $output .= '</div>';
                $output .= '<h3 class="sppb-addon-title2 sppb-pricing-title">' . $descripcion . '</h3>';
                $output .= '</div>';
            }
            $output .= '</div>'; // Cerrar contenedor principal
        } else {
            $output .= '<p>No se encontraron resultados.</p>';
        }
        return $output;
    }



    private function getPluginParamsByClase($clase2)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select($db->quoteName('plugin_params'));
        $query->from($db->quoteName('josmwt_hikashop_plugin'));
        $query->where($db->quoteName('plugin_params') . ' LIKE ' . $db->quote('%clase%'));
        $db->setQuery($query);
        $results = $db->loadObjectList();

        $matches = [];

        if ($results) {
            foreach ($results as $result) {
                $pluginParams = unserialize($result->plugin_params);
                foreach ($pluginParams->clase as $key => $value) {
                    if ($value == $clase2) {
                        $image_to_insert = isset($pluginParams->image_to_insert[$key]) ? $pluginParams->image_to_insert[$key] : null;
                        $descripcion = isset($pluginParams->descripcion[$key]) ? $pluginParams->descripcion[$key] : null;
                        $matches[] = array(
                            'image_to_insert' => $image_to_insert,
                            'descripcion' => $descripcion
                        );
                    }
                }
            }
        }

        return $matches;
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
        $css .= "
        .custom-container-wrapper {
            display: flex;
            flex-wrap: wrap; /* Permite que las cajas se envuelvan a la siguiente línea si no caben en una sola */
            gap: 10px; /* Espacio entre las cajas */
        }
        
        .custom-container {
            background-color: white;
            padding: 20px;
            border: 1px solid #ddd;
            text-align: center;
            display: inline-block;
            flex: 1 1 calc(33.333% - 20px); /* Ajusta el tamaño de las cajas según el espacio disponible */
            box-sizing: border-box; /* Incluye padding y border en el tamaño total del elemento */
        }
        
        .custom-image-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 200px; /* Altura del contenedor para centrar verticalmente */
        }
        
        .custom-image-wrapper img {
            max-height: 100%;
            max-width: 100%;
            object-fit: contain; /* Asegura que la imagen se ajuste dentro del contenedor sin distorsionarse */
        }
       
        ";
        $css .= $alignment;
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
