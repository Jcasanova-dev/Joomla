<?php

/**
 * @package SP Page Builder
 * @author JoomShaper http://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2022 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
//no direct access
defined('_JEXEC') or die('Restricted access');

class SppagebuilderAddonShipping extends SppagebuilderAddons
{
    /**
     * The addon frontend render method.
     * The returned HTML string will render to the frontend page.
     *
     * @return  string  The HTML string.
     * @since   1.0.0
     */
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

        // Link Parse
        list($link, $target) = AddonHelper::parseLink($settings, 'url');

        $output .= '<div class="sppb-addon sppb-addon-rich_text' . $class . '">';

        // Title
        $output .= '<' . $heading_selector . ' class="sppb-addon-title">';
        $output .= nl2br($title);
        $output .= '</' . $heading_selector . '>';

        // Content
        $output .= '<p>' . $content . '</p>';

        // Button
        $output .= !empty($link) ? '<a ' . $target . ' href="' . $link . '">' : '';
        $output .= '<button class="sppb-btn sppb-btn-' . $button_type . '">' . $button . '</button>';
        $output .= !empty($link) ? '</a>' : '';

        $output .= '</div>';

        return $output;
    }

    /**
     * Generate the CSS string for the frontend page.
     *
     * @return     string     The CSS string for the page.
     * @since     1.0.0
     */
    public function css()
    {
        $addon_id = '#sppb-addon-' . $this->addon->id;
        $cssHelper = new CSSHelper($addon_id);

        $css = '';

        $settings = $this->addon->settings;
        $settings->alignment = CSSHelper::parseAlignment($settings, 'alignment');
        $alignment = $cssHelper->generateStyle('.sppb-addon.sppb-addon-rich_text', $settings, ['alignment' => 'text-align'], false);

        $css .= $alignment;

        return $css;
    }

    /**
     * Generate the lodash template string for the frontend editor.
     *
     * @return     string     The lodash template string.
     * @since     1.0.0
     */
    public static function getTemplate()
    {
        $lodash = new Lodash('#sppb-addon-{{ data.id }}');

        // Inline Styles
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

        // Title
        $output .= '<{{ data.heading_selector }} class="sppb-addon-title">';
        $output .= '<span class="sp-inline-editable-element" data-id={{data.id}} data-fieldName="title" contenteditable="true">{{{ data.title }}}</span>';
        $output .= '</{{ data.heading_selector }}>';

        // Content
        $output .= '<p>';
        $output .= '<span class="sp-inline-editable-element" data-id={{data.id}} data-fieldName="content" contenteditable="true">{{{ data.content }}}</span>';
        $output .= '</p>';

        // Button
        $output .= '<a href=\'{{ btnUrl }}\' target=\'{{ target }}\' rel=\'{{ rel }}\'>';
        $output .= '<button id="btn-{{ data.id }}" class="sppb-btn sppb-btn-{{ data.type }}">';
        $output .= '<span class="sp-inline-editable-element" data-id={{data.id}} data-fieldName="button" contenteditable="true" data-placeholder="Add text...">{{{ data.button }}}</span>';
        $output .= '</button>';
        $output .= '</a>';

        $output .= '</div>';

        return $output;
    }
}
