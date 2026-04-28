<?php

/**
 * @brief postTitleAutonum, a plugin for Dotclear 2
 *
 * @package Dotclear
 * @subpackage Plugins
 *
 * @author Franck Paul
 *
 * @copyright Franck Paul contact@open-time.net
 * @copyright GPL-2.0 https://www.gnu.org/licenses/gpl-2.0.html
 */
$this->registerModule(
    'postTitleAutonum',
    'Auto numbering of duplicate titles',
    'Franck Paul',
    '6.4',
    [
        'date'        => '2026-03-26T12:01:01+0100',
        'requires'    => [['core', '2.36']],
        'permissions' => 'My',
        'type'        => 'plugin',
        'settings'    => ['blog' => '#params.pta'],

        'details'    => 'https://open-time.net/?q=postTitleAutonum',
        'support'    => 'https://github.com/franck-paul/postTitleAutonum',
        'repository' => 'https://raw.githubusercontent.com/franck-paul/postTitleAutonum/main/dcstore.xml',
        'license'    => 'gpl2',
    ]
);
