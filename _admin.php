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

// dead but useful code, in order to have translations
__('postTitleAutonum') . __('Auto numbering of duplicate titles');

$core->addBehavior('adminBlogPreferencesForm', ['ptaBehaviors', 'adminBlogPreferencesForm']);
$core->addBehavior('adminBeforeBlogSettingsUpdate', ['ptaBehaviors', 'adminBeforeBlogSettingsUpdate']);

$core->addBehavior('coreBeforePostCreate', ['ptaBehaviors', 'coreBeforePostCreate']);

class ptaBehaviors
{
    public static function adminBlogPreferencesForm($core, $settings)
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

    public static function coreBeforePostCreate($blog, $cur)
    {
        $blog->settings->addNameSpace('pta');
        if ($blog->settings->pta->enabled) {
            $strReq = 'SELECT post_title FROM ' . $blog->prefix . 'post ' .
            "WHERE post_title = '" . $blog->con->escape($cur->post_title) . "' " .
            'AND post_id <> ' . (integer) $cur->post_id . ' ' .
            "AND blog_id = '" . $blog->con->escape($blog->id) . "' " .
            'ORDER BY post_title DESC';

            $rs = $blog->con->select($strReq);

            if (!$rs->isEmpty()) {
                if ($blog->con->syntax() == 'mysql') {
                    $clause = "REGEXP '^" . $blog->con->escape(preg_quote($cur->post_title)) . " '";
                } elseif ($blog->con->driver() == 'pgsql') {
                    $clause = "~ '^" . $blog->con->escape(preg_quote($cur->post_title)) . " '";
                } else {
                    $clause = "LIKE '" .
                    $blog->con->escape(preg_replace(['%', '_', '!'], ['!%', '!_', '!!'], $cur->post_title)) . " ' ESCAPE '!'";  // @phpstan-ignore-line
                }
                $strReq = 'SELECT post_title FROM ' . $blog->prefix . 'post ' .
                'WHERE post_title ' . $clause . ' ' .
                'AND post_id <> ' . (integer) $cur->post_id . ' ' .
                "AND blog_id = '" . $blog->con->escape($blog->id) . "' " .
                'ORDER BY post_title DESC ';

                $rs = $blog->con->select($strReq);
                $a  = [];
                while ($rs->fetch()) {
                    $a[] = $rs->post_title;
                }

                natsort($a);
                if (preg_match('/(.*?)([0-9]+)$/', end($a), $m)) {
                    $i = (integer) $m[2];
                } else {
                    $i = 1;
                }

                if ($i > 0) {
                    $prefix = $blog->settings->pta->use_prefix ? ($blog->settings->pta->prefix ?: __('number')) : '';
                    // Update title
                    $cur->post_title .= ' ' . $prefix . ($i + 1);
                    // Update URL accordingly
                    $cur->post_url = $blog->getPostURL('', $cur->post_dt, $cur->post_title, $cur->post_id);
                }
            }
        }
    }
}
