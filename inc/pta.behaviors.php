<?php
/**
 * @brief postTitleAutonum, a plugin for Dotclear 2
 *
 * @package Dotclear
 * @subpackage Plugins
 *
 * @author Franck Paul
 *
 * @copyright Franck Paul carnet.franck.paul@gmail.com
 * @copyright GPL-2.0 https://www.gnu.org/licenses/gpl-2.0.html
 */
if (!defined('DC_CONTEXT_ADMIN')) {
    return;
}

class ptaBehaviors
{
    private static function entryHeaders($type = 'post')
    {
        dcCore::app()->blog->settings->addNameSpace('pta');

        if (dcCore::app()->blog->settings->pta->enabled) {
            $pta_options = [
                'post_type' => $type,
            ];

            return
            dcPage::jsJson('pta_options', $pta_options) .
            dcPage::jsModuleLoad('postTitleAutonum/js/suggest.js', dcCore::app()->getVersion('series'));
        }
    }

    public static function postHeaders()
    {
        return self::entryHeaders('post');
    }

    public static function pageHeaders()
    {
        return self::entryHeaders('page');
    }

    public static function adminBlogPreferencesForm($core = null, $settings)
    {
        $settings->addNameSpace('pta');
        echo
        '<div class="fieldset" id="pta"><h4>' . __('postTitleAutonum') . '</h4>' .
        '<p><label class="classic">' .
        form::checkbox('pta_enabled', '1', $settings->pta->enabled) .
        __('Enable auto numbering of duplicate titles') . '</label></p>' .
        '<p><label class="classic">' .
        form::checkbox('pta_use_prefix', '1', $settings->pta->use_prefix) .
        __('Use prefix before number') . '</label></p>' .
        '<p><label>' .
        __('User defined prefix:') . ' ' .
        form::field('pta_prefix', 25, 50, $settings->pta->prefix) .
        '</label></p>' . "\n" .
        '<p class="form-note">' . __('Leave empty to use the default prefix:') . ' "' . __('number') . '"</p>' . "\n" .
        '</div>';
    }

    public static function adminBeforeBlogSettingsUpdate($settings)
    {
        $settings->addNameSpace('pta');
        $settings->pta->put('enabled', !empty($_POST['pta_enabled']), 'boolean');
        $settings->pta->put('use_prefix', !empty($_POST['pta_use_prefix']), 'boolean');
        $settings->pta->put('prefix', empty($_POST['pta_prefix']) ? '' : html::escapeHTML($_POST['pta_prefix']), 'string');
    }
}
