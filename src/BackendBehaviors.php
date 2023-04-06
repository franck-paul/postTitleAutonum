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
declare(strict_types=1);

namespace Dotclear\Plugin\postTitleAutonum;

use dcCore;
use dcPage;
use Dotclear\Helper\Html\Form\Checkbox;
use Dotclear\Helper\Html\Form\Fieldset;
use Dotclear\Helper\Html\Form\Input;
use Dotclear\Helper\Html\Form\Label;
use Dotclear\Helper\Html\Form\Legend;
use Dotclear\Helper\Html\Form\Para;
use Dotclear\Helper\Html\Form\Text;
use Dotclear\Helper\Html\Html;

class BackendBehaviors
{
    private static function entryHeaders($type = 'post')
    {
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

    public static function adminBlogPreferencesForm($settings)
    {
        echo
        (new Fieldset('post_title_autonum'))
        ->legend((new Legend(__('postTitleAutonum'))))
        ->fields([
            (new Para())->items([
                (new Checkbox('pta_enabled', $settings->pta->enabled))
                    ->value(1)
                    ->label((new Label(__('Enable auto numbering of duplicate titles'), Label::INSIDE_TEXT_AFTER))),
            ]),
            (new Para())->items([
                (new Checkbox('pta_use_prefix', $settings->pta->use_prefix))
                    ->value(1)
                    ->label((new Label(__('Use prefix before number'), Label::INSIDE_TEXT_AFTER))),
            ]),
            (new Para())->items([
                (new Input('pta_prefix'))
                    ->value($settings->pta->use_prefix)
                    ->size(25)
                    ->maxlength(50)
                    ->label((new Label(__('User defined prefix:'), Label::INSIDE_TEXT_BEFORE))),
            ]),
            (new Para())->items([
                (new Text(null, __('Leave empty to use the default prefix:') . ' "' . __('number') . '"'))
                    ->class(['clear', 'form-note']),
            ]),
        ])
        ->render();
    }

    public static function adminBeforeBlogSettingsUpdate($settings)
    {
        $settings->pta->put('enabled', !empty($_POST['pta_enabled']), 'boolean');
        $settings->pta->put('use_prefix', !empty($_POST['pta_use_prefix']), 'boolean');
        $settings->pta->put('prefix', empty($_POST['pta_prefix']) ? '' : Html::escapeHTML($_POST['pta_prefix']), 'string');
    }
}
