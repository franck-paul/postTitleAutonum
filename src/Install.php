<?php
/**
 * @brief postTitleAutonum, a plugin for Dotclear 2
 *
 * @package Dotclear
 * @subpackage Plugins
 *
 * @author Franck Paul and contributors
 *
 * @copyright Franck Paul carnet.franck.paul@gmail.com
 * @copyright GPL-2.0 https://www.gnu.org/licenses/gpl-2.0.html
 */
declare(strict_types=1);

namespace Dotclear\Plugin\postTitleAutonum;

use Dotclear\App;
use Dotclear\Core\Process;
use Exception;

class Install extends Process
{
    public static function init(): bool
    {
        return self::status(My::checkContext(My::INSTALL));
    }

    public static function process(): bool
    {
        if (!self::status()) {
            return false;
        }

        try {
            $old_version = App::version()->getVersion(My::id());
            if (version_compare((string) $old_version, '3.2', '<')) {
                // Rename settings namespace
                if (App::blog()->settings()->exists('pta')) {
                    App::blog()->settings()->delWorkspace(My::id());
                    App::blog()->settings()->renWorkspace('pta', My::id());
                }
            }

            $settings = My::settings();

            $settings->put('enabled', false, 'boolean', 'Active', false, true);
            $settings->put('use_prefix', false, 'boolean', 'Use prefix', false, true);
            $settings->put('prefix', '', 'string', 'Prefix', false, true);
        } catch (Exception $e) {
            App::error()->add($e->getMessage());
        }

        return true;
    }
}
