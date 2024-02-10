<?php
/**
 * @package SP Page Builder
 * @author JoomShaper http:   //www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2022 JoomShaper
 * @license http:   //www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Text;

/**
 * Inline editor settings rules:
 * 1. The inline array must have an attribute named `buttons`
 * 2. The buttons array contains all the editor buttons. The key of the array must be unique.
 * 3. Every button contains some attributes like-
 *         a. action (string) (required) [The action will perform after clicking the button]
 *         b. type (string) (required) [The type of the button. valid values are `placeholder`, `icon-text`]
 *         c. placeholder (string) (optional) [If the button is dynamic and this cannot be expressed as icon/text.]
 *         d. icon (string) (optional) [A valid icon name available in the pagebuilder]
 *         e. text (string) (optional) [A text value to show as the button text]
 *         f. icon_position (string) (optional) [`left` or `right` position of the icon to the text. Default `left`]
 *         g. tooltip (string) (optional) [Tooltip text to show on the button hover.]
 *         h. fieldset (array) (conditional) [An conditional array (which is required if action is dropdown) for representing the fieldset fields.
 *             This is valid only if the action is `dropdown`.
 *             The direct children of the fieldset array would be the tabs.
 *             Inside the tabs name you should define the fields descriptions.
 *             If there is only one fieldset child then that means no tabs]
 *         i. options (array) (conditional) [This is a conditional field. This is required if the action is dropdown
 *             but you need to show some options not fields.]
 *         j. default (mixed) (conditional) [This is required if there is the options key. This indicates the default value of the button from the options array.]
 */

SpAddonsConfig::addonConfig(
    [
        'type' => 'content',
        'addon_name' => 'customer',
        'title' => 'Customer',
        'desc' => 'Form Customer',
        'category' => 'Custom',
        'icon' => '',
        'inline' => [
            'buttons' => [
                'rich_text_general_settings' => [
                    'action' => 'dropdown',
                    'icon' => 'addon::customer',
                    'tooltip' => 'Customer',
                    'fieldset' => [
                        'tab_groups' => [
                            'title' => [
                                'fields' => [
                                    [
                                        'title' => [
                                            'type' => 'text',
                                            'title' => Text::_('COM_SPPAGEBUILDER_ADDON_TITLE'),
                                            'desc' => Text::_('COM_SPPAGEBUILDER_ADDON_TITLE_DESC'),
                                            'std' => 'This is a advanced heading',
                                        ],
                                        'heading_selector' => [
                                            'type' => 'headings',
                                            'title' => Text::_('COM_SPPAGEBUILDER_ADDON_HEADINGS'),
                                            'std' => 'h3',
                                        ],
                                    ],
                                ],
                            ],
                            'content' => [
                                'fields' => [
                                    [
                                        'content' => [
                                            'type' => 'editor',
                                            'title' => Text::_('COM_SPPAGEBUILDER_ADDON_TITLE'),
                                            'desc' => Text::_('COM_SPPAGEBUILDER_ADDON_TITLE_DESC'),
                                            'std' => "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.",
                                        ],
                                    ],
                                ],
                            ],
                            'button' => [
                                'fields' => [
                                    [
                                        'button' => [
                                            'type' => 'text',
                                            'title' => Text::_('COM_SPPAGEBUILDER_GLOBAL_BUTTON_TEXT'),
                                            'desc' => Text::_('COM_SPPAGEBUILDER_GLOBAL_BUTTON_TEXT_DESC'),
                                            'std' => 'Button',
                                        ],
                                        'type' => [
                                            'type' => 'select',
                                            'title' => Text::_('COM_SPPAGEBUILDER_GLOBAL_BUTTON_STYLE'),
                                            'desc' => Text::_('COM_SPPAGEBUILDER_GLOBAL_BUTTON_STYLE_DESC'),
                                            'values' => [
                                                'default' => Text::_('COM_SPPAGEBUILDER_GLOBAL_DEFAULT'),
                                                'primary' => Text::_('COM_SPPAGEBUILDER_GLOBAL_PRIMARY'),
                                                'secondary' => Text::_('COM_SPPAGEBUILDER_GLOBAL_SECONDARY'),
                                                'success' => Text::_('COM_SPPAGEBUILDER_GLOBAL_SUCCESS'),
                                                'info' => Text::_('COM_SPPAGEBUILDER_GLOBAL_INFO'),
                                                'warning' => Text::_('COM_SPPAGEBUILDER_GLOBAL_WARNING'),
                                                'danger' => Text::_('COM_SPPAGEBUILDER_GLOBAL_DANGER'),
                                                'dark' => Text::_('COM_SPPAGEBUILDER_GLOBAL_DARK'),
                                                'link' => Text::_('COM_SPPAGEBUILDER_GLOBAL_LINK'),
                                            ],
                                            'std' => 'default',
                                        ],
                                        'url' => [
                                            'type' => 'link',
                                            'title' => Text::_('COM_SPPAGEBUILDER_GLOBAL_BUTTON_URL'),
                                            'mediaType' => 'attachment',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                'rich_text_alignment_separator' => [
                    'action' => 'separator',
                ],

                'rich_text_alignment_options' => [
                    'action' => 'dropdown',
                    'type' => 'placeholder',
                    'style' => 'inline',
                    'showCaret' => true,
                    'tooltip' => Text::_('COM_SPPAGEBUILDER_GLOBAL_ALIGNMENT'),
                    'placeholder' => [
                        'type' => 'list',
                        'options' => [
                            'left' => ['icon' => 'textAlignLeft'],
                            'center' => ['icon' => 'textAlignCenter'],
                            'right' => ['icon' => 'textAlignRight'],
                        ],
                        'display_field' => 'alignment',
                    ],
                    'default' => [
                        'xl' => 'center',
                    ],
                    'fieldset' => [
                        'basic' => [
                            'alignment' => [
                                'type' => 'alignment',
                                'inline' => true,
                                'responsive' => true,
                                'available_options' => ['left', 'center', 'right'],
                                'std' => [
                                    'xl' => 'center',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],

        'attr' => [],
    ],
);
