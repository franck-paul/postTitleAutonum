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
if (!defined('DC_CONTEXT_ADMIN')) {
    return;
}

class ptaRest
{
    private static function getTitle($core, $title, $type = 'post')
    {
        $strReq = 'SELECT post_title FROM ' . $core->blog->prefix . 'post ' .
        "WHERE post_title = '" . $core->blog->con->escape($title) . "' " .
        "AND post_type = '" . $core->blog->con->escape($type) . "' " .
        "AND blog_id = '" . $core->blog->con->escape($core->blog->id) . "' " .
        'ORDER BY post_title DESC';

        $rs = $core->blog->con->select($strReq);

        if (!$rs->isEmpty()) {
            // Try to find similar titles (beginning with)
            if ($core->blog->con->syntax() == 'mysql') {
                $clause = "REGEXP '^" . $core->blog->con->escape(preg_quote($title)) . " '";
            } elseif ($core->blog->con->driver() == 'pgsql') {
                $clause = "~ '^" . $core->blog->con->escape(preg_quote($title)) . " '";
            } else {
                $clause = "LIKE '" .
                $core->blog->con->escape(preg_replace(['%', '_', '!'], ['!%', '!_', '!!'], $title)) . " ' ESCAPE '!'";  // @phpstan-ignore-line
            }
            $strReq = 'SELECT post_title FROM ' . $core->blog->prefix . 'post ' .
            'WHERE post_title ' . $clause . ' ' .
            "AND post_type = '" . $core->blog->con->escape($type) . "' " .
            "AND blog_id = '" . $core->blog->con->escape($core->blog->id) . "' " .
            'ORDER BY post_title DESC ';

            $rs = $core->blog->con->select($strReq);
            $a  = [];
            while ($rs->fetch()) {
                $a[] = $rs->post_title;
            }

            natsort($a);
            if (preg_match('/(.*?)([0-9]+)$/', end($a), $m)) {
                $i = (integer) $m[2];
            } else {
                $i = 1;
            }

            if ($i > 0) {
                $prefix = $core->blog->settings->pta->use_prefix ? ($core->blog->settings->pta->prefix ?: __('number')) : '';
                $title .= ' ' . $prefix . ($i + 1);
            }
        }

        return $title;
    }

    public static function suggestTitle($core, $get)
    {
        $title = !empty($get['title']) ? $get['title'] : '';
        $type  = !empty($get['type']) ? $get['type'] : 'post';

        $rsp = new xmlTag('tpa');

        $suggest = self::getTitle($core, $title, $type);

        $rsp->ret     = ($title !== $suggest);
        $rsp->msg     = sprintf(__('The “%s” title is already used, would you replace it by “%s”?'), $title, $suggest);
        $rsp->suggest = $suggest;

        return $rsp;
    }
}
