<?php
/**
 * @package    Cambio Palabras por Imágenes for Joomla! HikaShop
 * @version    1.0.0
 * @author     Obsidev
 * @copyright  (C) 2021 Obsidev
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $originalData = array(
        'word_to_replace' => $_POST['data']['plugin']['plugin_params']['word_to_replace'][0],
        'image_to_insert' => $_POST['data']['plugin']['plugin_params']['image_to_insert'][0],
        'link' => $_POST['data']['plugin']['plugin_params']['link'][0]
    );

    $duplicatedData = array();
    if (isset($_POST['data']['plugin']['plugin_params']['word_to_replace'])) {
        foreach ($_POST['data']['plugin']['plugin_params']['word_to_replace'] as $key => $value) {
            if ($key != 0) { 
                $duplicatedData[] = array(
                    'word_to_replace' => $_POST['data']['plugin']['plugin_params']['word_to_replace'][$key],
                    'image_to_insert' => $_POST['data']['plugin']['plugin_params']['image_to_insert'][$key],
                    'link' => $_POST['data']['plugin']['plugin_params']['link'][$key]
                );
            }
        }
    }

    // Combina los datos originales y duplicados
    $allData = array();
    $allData[] = (object)$originalData; // Convertimos a objeto
    foreach ($duplicatedData as $data) {
        $allData[] = (object)$data; // Convertimos a objeto
    }

    // Serializa los datos en JSON
    $pluginParams = json_encode($allData);

    $db = JFactory::getDbo();
    $query = $db->getQuery(true);
    $query->select($db->quoteName('plugin_id'));
    $query->from($db->quoteName('josmwt_hikashop_plugin'));
    $query->where($db->quoteName('plugin_name') . ' = ' . $db->quote('wordtoimage'));
    $db->setQuery($query);
    $existingPluginId = $db->loadResult();

    if (!$existingPluginId) {
        $query = $db->getQuery(true);
        $query->insert($db->quoteName('josmwt_hikashop_plugin'));
        $query->columns(array(
            $db->quoteName('plugin_name'),
            $db->quoteName('plugin_params')
        ));
        $query->values($db->quote('wordtoimage') . ', ' . $db->quote($pluginParams));
        $db->setQuery($query);
        $db->execute();
    } else {
        $query = $db->getQuery(true);
        $query->update($db->quoteName('josmwt_hikashop_plugin'));
        $query->set($db->quoteName('plugin_params') . ' = ' . $db->quote($pluginParams));
        $query->where($db->quoteName('plugin_id') . ' = ' . (int)$existingPluginId);
        $db->setQuery($query);
        $db->execute();
    }
}
?>
<div id="page-plugins" class="hk-row-fluid hikashop_backend_tile_edition">
    <div class="hkc-md-6">
        <div class="hikashop_tile_block">
            <div>
                <div class="hikashop_tile_title"><?php echo JText::_('MAIN_INFORMATION');?></div>
                <dl class="hika_options">
                    <dt><?php echo JText::_('PLUGIN_NAME');?></dt>
                    <dd>
                        <input id="wordtoimage_plugin_name_field" type="text" name="data[plugin][plugin_name]" value="<?php echo $this->escape(@$this->element->plugin_name);?>" />
                    </dd>
                    <dt><?php echo JText::_('PUBLISHED');?></dt>
                    <dd><?php echo JHTML::_('hikaselect.booleanlist', 'data[plugin][plugin_published]', '', @$this->element->plugin_published);?></dd>
                </dl>
            </div>
        </div>
    </div>
    <div class="hkc-md-12">
        <div class="hikashop_tile_block">
            <div>
                <div class="hikashop_tile_title"><?php echo JText::_('PLUGIN_SPECIFIC_CONFIGURATION');?></div>
                <?php
                $allParams = null;
                $pluginParamsData = $this->element->plugin_params;
                echo '<div id="clonable_fieldset_container">';
                $index = 0;
                if ($pluginParamsData) {
                    $allParams = array(); // Crear un array vacío para almacenar todos los parámetros

                    if (is_array($pluginParamsData->word_to_replace)) {
                        foreach ($pluginParamsData->word_to_replace as $index => $word) {
                            $params = array(
                                'word_to_replace' => $word,
                                'image_to_insert' => $pluginParamsData->image_to_insert[$index],
                                'link' => $pluginParamsData->link[$index]
                            );
                            $allParams[] = $params; // Agregar cada parámetro al array
                        }
                    } else {
                        $params = array(
                            'word_to_replace' => $pluginParamsData->word_to_replace,
                            'image_to_insert' => $pluginParamsData->image_to_insert,
                            'link' => $pluginParamsData->link
                        );
                        $allParams[] = $params; // Agregar cada parámetro al array
                    }

                    // Ahora puedes utilizar el array $allParams para mostrar los parámetros
                    foreach ($allParams as $index => $params) {
                        echo '<dl id="wordtoimage_params_'. $index. '" class="hika_options">';
                        echo '<dt>'. JText::_('Palabra a Remplazar'). '</dt>';
                        echo '<dd><input id="word_to_replace_field_'. $index. '" type="text" name="data[plugin][plugin_params][word_to_replace][]" value="'. $this->escape($params['word_to_replace']). '" /></dd>';
                        echo '<dt>'. JText::_('Imagen Deseada'). '</dt>';
                        echo '<dd><input id="image_to_insert_field_'. $index. '" type="text" name="data[plugin][plugin_params][image_to_insert][]" value="'. $this->escape($params['image_to_insert']). '" /></dd>';
                        echo '<dt>'. JText::_('URL Destino'). '</dt>';
                        echo '<dd><input id="link_'. $index. '" type="text" name="data[plugin][plugin_params][link][]" value="'. $this->escape($params['link']). '" /></dd>';
                        echo '</dl>';
                    }
                } else {
                    $index = $allParams !== null ? count($allParams) : 0; // Obtener el número total de campos clonados
                    echo '<dl class="hika_options">';
                    echo '<dt>' . JText::_('Palabra a Remplazar') . '</dt>';
                    echo '<dd><input id="word_to_replace_field_' . $index . '" type="text" name="data[plugin][plugin_params][word_to_replace][]" value="" /></dd>';
                    echo '<dt>' . JText::_('Imagen Deseada') . '</dt>';
                    echo '<dd><input id="image_to_insert_field_' . $index . '" type="text" name="data[plugin][plugin_params][image_to_insert][]" value="" /></dd>';
                    echo '<dt>' . JText::_('URL Destino') . '</dt>';
                    echo '<dd><input id="link_' . $index . '" type="text" name="data[plugin][plugin_params][link][]" value="" /></dd>';
                    echo '</dl>';
                }
                echo '</div>';
                ?>
            </div>
        </div>
    </div>
</div>
<input type="hidden" name="data[plugin][plugin_id]" value="<?php echo @$this->element->plugin_id;?>" />
<input type="hidden" name="data[plugin][plugin_type]" value="<?php echo $this->name;?>" />
<input type="hidden" name="task" value="save" />

<a href="#" onclick="cloneFieldset(); return false;">Add More</a>

<script>
function cloneFieldset() {
    // Get the container div element
    var container = document.getElementById('clonable_fieldset_container');

    // Clone the last fieldset
    var clone = container.lastElementChild.cloneNode(true);

    // Increment the index in the cloned element's ID
    var index = container.children.length;

    // Increment the IDs of the cloned elements
    clone.id = 'wordtoimage_params_' + index;

    // Reset the values of the inputs in the clone
    clone.querySelectorAll('input[type="text"]').forEach(function(input) {
        input.value = '';
    });

    // Append the clone to the container
    container.appendChild(clone);
}
</script>
