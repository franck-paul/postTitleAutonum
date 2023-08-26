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
use Dotclear\Database\Statement\SelectStatement;

class BackendRest
{
    private static function getTitle($title, $type = 'post')
    {
        $sql = new SelectStatement();
        $sql
            ->column('post_title')
            ->from(dcCore::app()->blog->prefix . dcBlog::POST_TABLE_NAME)
            ->where('post_title = ' . $sql->quote($title))
            ->and('post_type = ' . $sql->quote($type))
            ->and('blog_id = ' . $sql->quote(dcCore::app()->blog->id))
            ->order('post_title DESC')
        ;

        $rs = $sql->select();
        if (!is_null($rs) && !$rs->isEmpty()) {
            $sql = new SelectStatement();

            // Try to find similar titles (beginning with, including a space)
            if (dcCore::app()->con->syntax() == 'mysql') {
                // MySQL
                $clause = "REGEXP '^" . $sql->escape(preg_quote($title)) . " '";
            } elseif (dcCore::app()->con->syntax() == 'postgresql') {
                // PostgreSQL
                $clause = "~ '^" . $sql->escape(preg_quote($title)) . " '";
            } else {
                // SQlite
                $clause = "LIKE '" .
                $sql->escape(preg_replace(['%', '_', '!'], ['!%', '!_', '!!'], $title)) . " ' ESCAPE '!'";  // @phpstan-ignore-line
            }

            $sql
                ->column('post_title')
                ->from(dcCore::app()->blog->prefix . dcBlog::POST_TABLE_NAME)
                ->where('post_title ' . $clause)
                ->and('post_type = ' . $sql->quote($type))
                ->and('blog_id = ' . $sql->quote(dcCore::app()->blog->id))
                ->order('post_title DESC')
            ;

            $rs = $sql->select();
            $a  = [];
            while ($rs->fetch()) {
                $a[] = $rs->post_title;
            }

            $i = 1;
            if (count($a)) {
                natsort($a);
                if (preg_match('/(.*?)(\d+)$/', end($a), $m)) {
                    $i = (int) $m[2];
                }
            }

            if ($i > 0) {
                $settings = My::settings();
                $prefix   = $settings->use_prefix ? ($settings->prefix ?: __('#')) : '';

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
