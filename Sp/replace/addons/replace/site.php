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
        $this->addHeadLinks();
        $settings = $this->addon->settings;
        list($link, $target) = AddonHelper::parseLink($settings, 'url');
        $icon = JFactory::getApplication()->input->get('img', '', 'string');
        $descripcion = JFactory::getApplication()->input->get('descripcion', '', 'string');
        $class = (isset($settings->class) && $settings->class) ? ' ' . $settings->class : '';
        $title = (isset($settings->title) && $settings->title) ? $settings->title : 'Personal Delivery';
        $features = (isset($settings->features) && $settings->features) ? $settings->features : [
            'Package weight: 5kg - 10 kg',
            'Size: 110’ x 100’',
            'Shipping time: 12 Hour',
            'Cost: $5.00'
        ];
        $button_text = (isset($settings->button_text) && $settings->button_text) ? $settings->button_text : 'Order Now';
        $button_link = (isset($settings->button_link) && $settings->button_link) ? $settings->button_link : '#';
        $output = '<div class="sppb-column-addons2">';
        $output .= '<div id="sppb-addon-wrapper-' . uniqid() . '" class="sppb-addon-wrapper2 addon-root-icon">';
        $output .= '<div id="sppb-addon-' . uniqid() . '" class="clearfix">';
        $output .= '<div class="sppb-icon2">';
        $output .= '<span class="sppb-icon-inner">';
        $output .= '<img src="' . $icon . '" alt="" title="" width="120" height="auto">';
        $output .= '<span class="sppb-form-label-visually-hidden">' . $icon . '</span>';
        $output .= '</span>';
        $output .= '</div>';
        $output .= '</div>';
        $output .= '</div>';
        $output .= '<div id="sppb-addon-wrapper-' . uniqid() . '" class="sppb-addon-wrapper2 addon-root-pricing">';
        $output .= '<div id="sppb-addon-' . uniqid() . '" class="clearfix">';
        $output .= '<div class="sppb-addon sppb-addon-pricing-table">';
        $output .= '<div class="sppb-pricing-box">';
        $output .= '<div class="sppb-pricing-header">';
        $output .= '<h3 class="sppb-addon-title2 sppb-pricing-title">' .  $descripcion . '</h3>';
        $output .= '<div class="sppb-pricing-price-container"></div>';
        $output .= '</div>';
        $output .= '</div>';
        $output .= '</div>';
        $output .= '</div>';
        $output .= '</div>';
        $output .= '</div>';
        return $output;
    }

    private function addHeadLinks()
    {
        $doc = JFactory::getDocument();

        $links = [
            'https://fonts.googleapis.com' => 'preconnect',
            'https://fonts.gstatic.com' => 'preconnect',
            'https://sppagebuilder.com' => 'preconnect',
            '/media/vendor/bootstrap/js/alert.js?5.3.2' => 'modulepreload',
            '/media/vendor/bootstrap/js/button.js?5.3.2' => 'modulepreload',
            '/media/vendor/bootstrap/js/carousel.js?5.3.2' => 'modulepreload',
            '/media/vendor/bootstrap/js/collapse.js?5.3.2' => 'modulepreload',
            '/media/vendor/bootstrap/js/dropdown.js?5.3.2' => 'modulepreload',
            '/media/vendor/bootstrap/js/modal.js?5.3.2' => 'modulepreload',
            '/media/vendor/bootstrap/js/offcanvas.js?5.3.2' => 'modulepreload',
            '/media/vendor/bootstrap/js/popover.js?5.3.2' => 'modulepreload',
            '/media/vendor/bootstrap/js/scrollspy.js?5.3.2' => 'modulepreload',
            '/media/vendor/bootstrap/js/tab.js?5.3.2' => 'modulepreload',
            '/media/vendor/bootstrap/js/toast.js?5.3.2' => 'modulepreload',
            '/media/system/js/showon.js?891646' => 'modulepreload',
            '/media/system/js/messages.js?7a5169' => 'modulepreload',
            'https://fonts.googleapis.com/css?family=Rubik:100,100italic,200,200italic,300,300italic,400,400italic,500,500italic,600,600italic,700,700italic,800,800italic,900,900italic&display=swap' => 'preload',
            '//fonts.googleapis.com/css?family=PT%20Serif:100,100i,200,200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i&subset=cyrillic&display=swap' => 'preload',
            '//fonts.googleapis.com/css?family=Fira%20Sans:100,100i,200,200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i&subset=cyrillic&display=swap' => 'preload',
            'https://fonts.googleapis.com/css2?family=PT+Sans:ital,wght@0,400;0,700;1,400;1,700&display=swap' => 'preload',
            '/media/com_jchoptimize/cache/css/533319f48463b21b2daaaad099e2210effcbc6121c61d0d3302938d0612b1f87.css' => 'preload',
            '/media/vendor/debugbar/vendor/highlightjs/styles/github.css' => 'stylesheet',
            '/media/vendor/debugbar/debugbar.css' => 'stylesheet',
            '/media/vendor/debugbar/widgets.css' => 'stylesheet',
            '/media/vendor/debugbar/openhandler.css' => 'stylesheet',
            '/media/plg_system_debug/widgets/info/widget.min.css' => 'stylesheet',
            '/media/plg_system_debug/widgets/sqlqueries/widget.min.css' => 'stylesheet'
        ];

        foreach ($links as $href => $rel) {
            $attributes = ['rel' => $rel];
            if (strpos($href, 'https://') === 0) {
                $attributes['crossorigin'] = 'anonymous';
            }
            if ($rel === 'preload') {
                $attributes['as'] = 'style';
                $attributes['onload'] = "this.rel='stylesheet'";
                $attributes['media'] = 'all';
            }
            $doc->addHeadLink($href, $attributes);
        }

        $scripts = [
            '/media/vendor/debugbar/vendor/highlightjs/highlight.pack.js',
            '/media/vendor/debugbar/debugbar.js',
            '/media/vendor/debugbar/widgets.js',
            '/media/vendor/debugbar/openhandler.js',
            '/media/plg_system_debug/widgets/info/widget.min.js',
            '/media/plg_system_debug/widgets/sqlqueries/widget.min.js'
        ];

        foreach ($scripts as $src) {
            $doc->addScript($src, ['defer' => 'defer']);
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
        $css .= "body { font-family: 'PT Serif', serif; }";
        $css .= ".sppb-addon-wrapper2 {
            display: flex;
            flex-direction: column;
            align-items: center;
            border: 1px solid #ddd;
            padding: 20px;
            margin: 10px;
            background-color: #FFFFFF;
        }
        
        .sppb-pricing-box {
            text-align: justify;
        }
        
        .sppb-icon2 {
            font-size: 50px;
            color: #333;
        }
        
        .sppb-addon-title2 {
            font-size: 24px;
            color: #333;
            margin-top: 20px;
            font-family: 'PT Serif', serif;
            text-align: justify;
        }
        
        .sppb-pricing-features2 {
            list-style-type: none;
            padding: 0;
            margin-top: 20px;
        }
        
        .sppb-pricing-features2 li {
            font-size: 16px;
            color: #666;
            line-height: 1.5;
        }
        
        .sppb-btn2 {
            background-color: #f00;
            color: #fff;
            padding: 10px 20px;
            margin-top: 20px;
            text-decoration: none;
            border-radius: 5px;
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
