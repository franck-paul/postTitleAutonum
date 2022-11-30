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
if (!defined('DC_RC_PATH')) {
    return;
}

$this->registerModule(
    'postTitleAutonum',
    'Auto numbering of duplicate titles',
    'Franck Paul',
    '1.0',
    [
        'requires'    => [['core', '2.24']],
        'permissions' => dcCore::app()->auth->makePermissions([
            dcAuth::PERMISSION_ADMIN,
        ]),
        'type'     => 'plugin',
        'settings' => ['blog' => '#params.pta'],

        'details'    => 'https://open-time.net/?q=postTitleAutonum',
        'support'    => 'https://github.com/franck-paul/postTitleAutonum',
        'repository' => 'https://raw.githubusercontent.com/franck-paul/postTitleAutonum/main/dcstore.xml',
    ]
);
