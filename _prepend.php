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

$__autoload['ptaBehaviors'] = dirname(__FILE__) . '/inc/pta.behaviors.php';
$__autoload['ptaRest']      = dirname(__FILE__) . '/_services.php';

// Public, XML/RPC and Admin mode

if (!defined('DC_CONTEXT_ADMIN')) {
    return false;
}
// Admin mode only

$core->addBehavior('adminBlogPreferencesForm', ['ptaBehaviors', 'adminBlogPreferencesForm']);
$core->addBehavior('adminBeforeBlogSettingsUpdate', ['ptaBehaviors', 'adminBeforeBlogSettingsUpdate']);

$core->addBehavior('adminPostHeaders', ['ptaBehaviors', 'postHeaders']);
$core->addBehavior('adminPageHeaders', ['ptaBehaviors', 'pageHeaders']);

$core->rest->addFunction('suggestTitle', ['ptaRest', 'suggestTitle']);
