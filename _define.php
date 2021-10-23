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
    'postTitleAutonum',                         // Name
    'Auto numbering of duplicate titles',       // Description
    'Franck Paul',                              // Author
    '0.2',                                      // Version
    [
        'requires'    => [['core', '2.13']],                                // Dependencies
        'permissions' => 'admin',                                           // Permissions
        'type'        => 'plugin',                                          // Type
        'settings'    => ['blog' => '#params.pta'],                         // Settings

        'details'    => 'https://open-time.net/?q=postTitleAutonum',       // Details URL
        'support'    => 'https://github.com/franck-paul/postTitleAutonum', // Support URL
        'repository' => 'https://raw.githubusercontent.com/franck-paul/postTitleAutonum/main/dcstore.xml'
    ]
);
