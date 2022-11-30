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

Clearbricks::lib()->autoload([
    'ptaBehaviors' => __DIR__ . '/inc/pta.behaviors.php',
    'ptaRest'      => __DIR__ . '/_services.php',
]);

// Public, XML/RPC and Admin mode

if (!defined('DC_CONTEXT_ADMIN')) {
    return false;
}
// Admin mode only

dcCore::app()->addBehavior('adminBlogPreferencesFormV2', [ptaBehaviors::class, 'adminBlogPreferencesForm']);
dcCore::app()->addBehavior('adminBeforeBlogSettingsUpdate', [ptaBehaviors::class, 'adminBeforeBlogSettingsUpdate']);

dcCore::app()->addBehavior('adminPostHeaders', [ptaBehaviors::class, 'postHeaders']);
dcCore::app()->addBehavior('adminPageHeaders', [ptaBehaviors::class, 'pageHeaders']);

dcCore::app()->rest->addFunction('suggestTitle', [ptaRest::class, 'suggestTitle']);
