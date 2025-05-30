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
$this->registerModule(
    'postTitleAutonum',
    'Auto numbering of duplicate titles',
    'Franck Paul',
    '5.0',
    [
        'date'        => '2025-05-12T09:56:04+0200',
        'requires'    => [['core', '2.33']],
        'permissions' => 'My',
        'type'        => 'plugin',
        'settings'    => ['blog' => '#params.pta'],

        'details'    => 'https://open-time.net/?q=postTitleAutonum',
        'support'    => 'https://github.com/franck-paul/postTitleAutonum',
        'repository' => 'https://raw.githubusercontent.com/franck-paul/postTitleAutonum/main/dcstore.xml',
        'license'    => 'gpl2',
    ]
);
