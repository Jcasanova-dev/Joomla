<?php

/**
 * @package    Cambio Palabras por Imágenes for Joomla! HikaShop
 * @version    1.0.0
 * @author     Obsidev
 * @copyright  (C) 2021 Obsidev
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');

class plgHikashopWordtoimage extends hikashopPlugin
{
    public $name = 'wordtoimage';

    public function onPluginConfiguration(&$element)
    {
        return $this->pluginConfiguration($element);
    }

    public function onAfterRender()
    {
        $app = Joomla\CMS\Factory::getApplication();
        $uri = new Joomla\CMS\Uri\Uri($app->input->server->get('REQUEST_URI'));
        $currentUrl = $uri->toString();
        $language = JFactory::getLanguage()->getTag();

        // Check if we're in the administrator side
        if ($app->isClient('administrator')) {
            if (strpos($currentUrl, 'index.php?option=com_hikashop&ctrl=product') !== false) {
                return;
            }
        } else {
            $renderedContent = $this->getRenderedContent();
            $pluginParams = $this->getPluginParams();

            if ($pluginParams) {
                $this->replaceWordsWithImages($renderedContent, $pluginParams);
            }

            $app->setBody($renderedContent);
        }
    }

    private function getRenderedContent()
    {
        $app = JFactory::getApplication();
        return $app->getBody();
    }

    private function getPluginParams()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query->select($db->quoteName('plugin_params'));
        $query->from($db->quoteName('josmwt_hikashop_plugin'));
        $query->where($db->quoteName('plugin_params') . ' LIKE ' . $db->quote('%word_to_replace%'));

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

    private function replaceWordsWithImages(&$content, $pluginParams)
    {
        $wordToReplace = $pluginParams->word_to_replace;
        $imageToInsert = $pluginParams->image_to_insert;
        $descripcion = $pluginParams->descripcion;
        $link = isset($pluginParams->link) ? $pluginParams->link : [];
        $language = JFactory::getLanguage()->getTag();
        if (is_array($wordToReplace)) {
            foreach ($wordToReplace as $index => $word) {
                if (!isset($descripcion[$index])) {
                    $descripcion[$index] = '';
                }
                $pattern = '/(<input\s+[^>]*\btype\s*=\s*["\']?hidden["\']?[^>]*>|<(a|li|select|option)[^>]*>.*?<\/\2>(*SKIP)(*FAIL))|' . preg_quote($word, '/') . '/i';
                $content = preg_replace_callback(
                    $pattern,
                    function ($matches) use ($word, $imageToInsert, $link, $index, $language, $descripcion) {
                        if (!empty($matches[1])) {
                            return $matches[1];
                        }
                        $wordpage = $word;
                        $route = $this->getRouteFromDatabase($wordpage, $language);
                        $linkpage = $route ? "https://mwt.one/" . $route : (isset($link[$index]) ? $link[$index] : '#');
                        $linkpage2 = "https://mwt.one/index.php/es/?option=com_sppagebuilder&view=page&id=48";
                        $params = array(
                            'img' => $imageToInsert[$index],
                            'descripcion' => $descripcion[$index]
                        );
                        $linkpage2 .= '&' . http_build_query($params);
                        $imageTag = '<a href="' . $linkpage2 . '" target="_blank"><img src="' . $imageToInsert[$index] . '" alt="' . $word . '" title="' . $word . '" width="70" height="70" style="display: inline-block;"/></a>';
                        return $imageTag;
                    },
                    $content
                );
            }
        }
    }
    private function getRouteFromDatabase($wordpage, $language)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select($db->quoteName('route'))
            ->from($db->quoteName('josmwt_finder_links'))
            ->where($db->quoteName('title') . ' = ' . $db->quote($wordpage))
            ->where($db->quoteName('language') . ' = ' . $db->quote($language));
        $db->setQuery($query);
        $route = $db->loadResult();
        return $route;
    }
}
