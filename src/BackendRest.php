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

use dcBlog;
use dcCore;
use Dotclear\Database\MetaRecord;

class BackendRest
{
    private static function getTitle($title, $type = 'post')
    {
        $strReq = 'SELECT post_title FROM ' . dcCore::app()->blog->prefix . dcBlog::POST_TABLE_NAME . ' ' .
        "WHERE post_title = '" . dcCore::app()->blog->con->escape($title) . "' " .
        "AND post_type = '" . dcCore::app()->blog->con->escape($type) . "' " .
        "AND blog_id = '" . dcCore::app()->blog->con->escape(dcCore::app()->blog->id) . "' " .
        'ORDER BY post_title DESC';

        $rs = new MetaRecord(dcCore::app()->blog->con->select($strReq));

        if (!$rs->isEmpty()) {
            // Try to find similar titles (beginning with)
            if (dcCore::app()->blog->con->syntax() == 'mysql') {
                $clause = "REGEXP '^" . dcCore::app()->blog->con->escape(preg_quote($title)) . " '";
            } elseif (dcCore::app()->blog->con->driver() == 'pgsql') {
                $clause = "~ '^" . dcCore::app()->blog->con->escape(preg_quote($title)) . " '";
            } else {
                $clause = "LIKE '" .
                dcCore::app()->blog->con->escape(preg_replace(['%', '_', '!'], ['!%', '!_', '!!'], $title)) . " ' ESCAPE '!'";  // @phpstan-ignore-line
            }
            $strReq = 'SELECT post_title FROM ' . dcCore::app()->blog->prefix . dcBlog::POST_TABLE_NAME . ' ' .
            'WHERE post_title ' . $clause . ' ' .
            "AND post_type = '" . dcCore::app()->blog->con->escape($type) . "' " .
            "AND blog_id = '" . dcCore::app()->blog->con->escape(dcCore::app()->blog->id) . "' " .
            'ORDER BY post_title DESC ';

            $rs = new MetaRecord(dcCore::app()->blog->con->select($strReq));
            $a  = [];
            while ($rs->fetch()) {
                $a[] = $rs->post_title;
            }

            natsort($a);
            if (preg_match('/(.*?)(\d+)$/', end($a), $m)) {
                $i = (int) $m[2];
            } else {
                $i = 1;
            }

            if ($i > 0) {
                $prefix = dcCore::app()->blog->settings->pta->use_prefix ? (dcCore::app()->blog->settings->pta->prefix ?: __('number')) : '';
                $title .= ' ' . $prefix . ($i + 1);
            }
        }

        return $title;
    }

    public static function suggestTitle($get)
    {
        $title = $get['title'] ?? '';
        $type  = $get['type']  ?? 'post';

        $suggest = self::getTitle($title, $type);

        $payload = [
            'ret'     => ($title !== $suggest),
            'msg'     => sprintf(__('The “%s” title is already used, would you replace it by “%s”?'), $title, $suggest),
            'suggest' => $suggest,
        ];

        return $payload;
    }
}
