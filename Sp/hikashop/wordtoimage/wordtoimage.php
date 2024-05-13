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
        $app = Joomla\CMS\Factory::getApplication();
        $uri = new Joomla\CMS\Uri\Uri($app->input->server->get('REQUEST_URI'));
        $currentUrl = $uri->toString();
        $language = JFactory::getLanguage()->getTag();
        echo $language;
        if (strpos($currentUrl, '/administrator/index.php?option=com_hikashop&ctrl=product')!== false) {
            return;
        }

        $renderedContent = $this->getRenderedContent();
        $pluginParams = $this->getPluginParams();

        if ($pluginParams) {
            $this->replaceWordsWithImages($renderedContent, $pluginParams);
        }

        $app->setBody($renderedContent);
    }

    private function getRenderedContent() {
        $app = JFactory::getApplication();
        return $app->getBody();
    }

    private function getPluginParams() {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query->select($db->quoteName('plugin_params'));
        $query->from($db->quoteName('josmwt_hikashop_plugin'));
        $query->where($db->quoteName('plugin_params'). ' LIKE '. $db->quote('%word_to_replace%'));

        $db->setQuery($query);
        $results = $db->loadObjectList();

        if ($results) {
            foreach ($results as $result) {
                $pluginParams = unserialize($result->plugin_params);
                if ($pluginParams && isset($pluginParams->word_to_replace) && isset($pluginParams->image_to_insert)) {
                    return $pluginParams;
                }
            }
        }

        return null;
    }

    private function replaceWordsWithImages(&$content, $pluginParams) {
        $wordToReplace = $pluginParams->word_to_replace;
        $imageToInsert = $pluginParams->image_to_insert;
        $link = isset($pluginParams->link)? $pluginParams->link : '';
        $language = JFactory::getLanguage()->getTag();
        if (is_array($wordToReplace)) {
            foreach ($wordToReplace as $index => $word) {
                if (strpos($content, $word)!== false) {
                    $pattern = '/<(input[^>]*type="(?:text|select|radio|checkbox)"[^>]*value="([^"]*'.$word.'[^"]*)"[^>]*|select[^>]*type="list"[^>]*value="'.$word.'"[^>]*|select[^>]*value="'.$word.'"[^>]*|input[^>]*type="radio"[^>]*name="[^"]*'.$word.'[^"]*"[^>]*|input[^>]*type="checkbox"[^>]*name="[^"]*'.$word.'[^"]*"[^>]*|textarea[^>]*>'.$word.'<\/textarea>|<div[^>]*class="hikashop_filter_module"[^>]*>'.$word.'<\/div>)/i';
                    if (preg_match($pattern, $content, $matches)) {
                        continue;
                    }
                    $wordpage = $word . "_" . $language;
                    if ($wordpage == 'Dielectrico_es-ES') {
                        $linkpage = "https://mwt.one/index.php?option=com_sppagebuilder&view=page&id=41";
                    } elseif ($wordpage == 'Dielectrico_en-US') {
                        $linkpage = "https://mwt.one/index.php?option=com_sppagebuilder&view=page&id=42";
                    } else {
                        $linkpage = $link[$index];
                    }
                    
                    $imageTag = '<a href="'.  $linkpage. '" target="_blank"><img src="'. $imageToInsert[$index]. '" alt="'. $word.'"  title="' . $word . '" width="50" height="50" style="display: inline-block;"/></a>';
                    $content = str_replace($word, $imageTag, $content);
                }
            }
        } else {
            $pattern = '/<(input[^>]*type="(?:text|select|radio|checkbox)"[^>]*value="([^"]*'.$wordToReplace.'[^"]*)"[^>]*|select[^>]*type="list"[^>]*value="'.$wordToReplace.'"[^>]*|select[^>]*value="'.$wordToReplace.'"[^>]*|input[^>]*type="radio"[^>]*name="[^"]*'.$wordToReplace.'[^"]*"[^>]*|input[^>]*type="checkbox"[^>]*name="[^"]*'.$wordToReplace.'[^"]*"[^>]*|textarea[^>]*>'.$wordToReplace.'<\/textarea>|<div[^>]*class="hikashop_filter_module"[^>]*>'.$wordToReplace.'<\/div>)/i';
            if (preg_match($pattern, $content, $matches)) {
                return;
            }

            $imageTag = '<a href="'. $link . '" target="_blank"><img src="'. $imageToInsert . '" alt="'. $wordToReplace.'"  title="' . $word . '" style="display: inline-block;"/></a>';
            $content = str_replace($wordToReplace, $imageTag, $content);
        }
    }
}