<?php
/**
 * @package    Cambio Palabras por Imágenes for Joomla! HikaShop
 * @version    1.0.0
 * @author     Obsidev
 * @copyright  (C) 2021 Obsidev
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');

class plgHikashopWordtoimage extends hikashopPlugin {
    public $name = 'wordtoimage';

    public function onPluginConfiguration(&$element) {
        // Aquí puedes realizar configuraciones adicionales si es necesario
        return $this->pluginConfiguration($element);
    }

    public function onAfterRender() {
        // Obtener el contenido de la página renderizada
        $app = JFactory::getApplication();
        $buffer = $app->getBody();
    
        // Obtener la conexión a la base de datos
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
    
        // Construir la consulta
        $query->select($db->quoteName('plugin_params'));
        $query->from($db->quoteName('josmwt_hikashop_plugin'));
        $query->where($db->quoteName('plugin_params') . ' LIKE ' . $db->quote('%word_to_replace%'));
        $db->setQuery($query);
    
        // Ejecutar la consulta y obtener los resultados
        $results = $db->loadObjectList();
    
        // Verificar si se encontraron resultados
        if ($results) {
            foreach ($results as $result) {
                // Deserializar la cadena serializada en un objeto stdClass
                $pluginParams = unserialize($result->plugin_params);
    
                // Verificar si se obtuvieron los parámetros correctamente
                if ($pluginParams && isset($pluginParams->word_to_replace) && isset($pluginParams->image_to_insert)) {
                    $word_to_replace = $pluginParams->word_to_replace;
                    $image_to_insert = $pluginParams->image_to_insert;
                    $link = isset($pluginParams->link) ? $pluginParams->link : ''; // Verificar si el enlace está definido
    
                    // Verificar si la palabra a reemplazar existe en el contenido
                    if (is_array($word_to_replace)) {
                        foreach ($word_to_replace as $index => $word) {
                            if (strpos($buffer, $word) !== false) {
                                // Buscar etiquetas de input type="text" que contengan la palabra a reemplazar
                                $pattern = '/<input[^>]*type="text"[^>]*value="([^"]*'.$word.'[^"]*)"[^>]*>/i';
                                if (preg_match($pattern, $buffer, $matches)) {
                                    continue;
                                }
    
                                $image_tag = '<a href="' . $link[$index] . '" target="_blank"><img src="' . $image_to_insert[$index] . '" alt="' . $word . '" title="' . $word . '" width="80" height="80"></a>';
    
                                // Reemplazar la palabra con la etiqueta de la imagen
                                $buffer = str_replace($word, $image_tag, $buffer);
                            }
                        }
                    } else {
                        if (strpos($buffer, $word_to_replace) !== false) {
                            // Buscar etiquetas de input type="text" que contengan la palabra a reemplazar
                            $pattern = '/<input[^>]*type="text"[^>]*value="([^"]*'.$word_to_replace.'[^"]*)"[^>]*>/i';
                            if (preg_match($pattern, $buffer, $matches)) {
                                continue;
                            }
    
                            $image_tag = '<a href="' . $link . '" target="_blank"><img src="' . $image_to_insert . '" alt="' . $word_to_replace . '" title="' . $word_to_replace . '" width="80" height="80"></a>';
    
                            // Reemplazar la palabra con la etiqueta de la imagen
                            $buffer = str_replace($word_to_replace, $image_tag, $buffer);
                        }
                    }
                }
            }
    
            // Establecer el contenido modificado
            $app->setBody($buffer);
        }
    }
}