<?php
/**
 * @package    Cambio Palabras por Imágenes for Joomla! HikaShop
 * @version    1.0.0
 * @author     Obsidev
 * @copyright  (C) 2021 Obsidev
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?>
<div id="page-plugins" class="hk-row-fluid hikashop_backend_tile_edition">
    <div class="hkc-md-6">
        <div class="hikashop_tile_block">
            <div>
                <div class="hikashop_tile_title"><?php echo JText::_('MAIN_INFORMATION'); ?></div>
                <dl class="hika_options">
                    <dt><?php echo JText::_('PLUGIN_NAME'); ?></dt>
                    <dd>
                        <input id="wordtoimage_plugin_name_field" type="text" name="data[plugin][plugin_name]" value="<?php echo $this->escape(@$this->element->plugin_name); ?>" />
                    </dd>
                    <dt><?php echo JText::_('PUBLISHED'); ?></dt>
                    <dd><?php echo JHTML::_('hikaselect.booleanlist', 'data[plugin][plugin_published]', '', @$this->element->plugin_published); ?></dd>
                </dl>
            </div>
        </div>
    </div>
    <div class="hkc-md-12">
        <div class="hikashop_tile_block">
            <div>
                <div class="hikashop_tile_title"><?php echo JText::_('PLUGIN_SPECIFIC_CONFIGURATION'); ?></div>
                <dl class="hika_options">
                    <dt><?php echo JText::_('WORD_TO_REPLACE'); ?></dt>
                    <dd>
                    <input id="word_to_replace_field" type="text" name="data[plugin][plugin_params][word_to_replace]" value="<?php echo htmlspecialchars($this->escape(str_replace('#', '\#', @$this->element->plugin_params->word_to_replace))); ?>" />
                    </dd>
                    <dt><?php echo JText::_('IMAGE_TO_INSERT'); ?></dt>
                    <dd>
                        <input id="image_to_insert_field" type="text" name="data[plugin][plugin_params][image_to_insert]" value="<?php echo $this->escape(@$this->element->plugin_params->image_to_insert); ?>" />
                    </dd>
                    <dt><?php echo JText::_('LINK'); ?></dt>
                    <dd>
                        <input id="link" type="text" name="data[plugin][plugin_params][link]" value="<?php echo $this->escape(@$this->element->plugin_params->link); ?>" />
                    </dd>
                </dl>
            </div>
        </div>
    </div>
</div>
<input type="hidden" name="data[plugin][plugin_id]" value="<?php echo @$this->element->plugin_id; ?>" />
<input type="hidden" name="data[plugin][plugin_type]" value="<?php echo $this->name; ?>" />
<input type="hidden" name="task" value="save" />