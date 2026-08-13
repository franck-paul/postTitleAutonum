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
declare(strict_types=1);

if (isset($this) && is_object($this) && method_exists($this, 'registerModule') && isset($this->id) && is_string($this->id)) {
    $this->registerModule(
        'postTitleAutonum',
        'Auto numbering of duplicate titles',
        'Franck Paul',
        '7.0',
        [
            'date'        => '2026-08-03T10:08:46+0200',
            'requires'    => [['core', '2.39']],
            'permissions' => 'My',
            'type'        => 'plugin',
            'settings'    => ['blog' => '#params.pta'],

            'details'    => 'https://open-time.net/?q=postTitleAutonum',
            'support'    => 'https://github.com/franck-paul/postTitleAutonum',
            'repository' => 'https://raw.githubusercontent.com/franck-paul/postTitleAutonum/main/dcstore.xml',
            'license'    => 'gpl2',
        ]
    );
}
