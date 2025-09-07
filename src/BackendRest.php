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

use Dotclear\App;
use Dotclear\Database\Statement\SelectStatement;

class BackendRest
{
    private static function getTitle(string $title, string $type = 'post'): string
    {
        $sql = new SelectStatement();
        $sql
            ->column('post_title')
            ->from(App::db()->con()->prefix() . App::blog()::POST_TABLE_NAME)
            ->where('post_title = ' . $sql->quote($title))
            ->and('post_type = ' . $sql->quote($type))
            ->and('blog_id = ' . $sql->quote(App::blog()->id()))
            ->order('post_title DESC')
        ;

        $rs = $sql->select();
        if ($rs && !$rs->isEmpty()) {
            $sql = new SelectStatement();

            // Try to find similar titles (beginning with, including a space)
            if (App::db()->con()->syntax() == 'mysql') {
                // MySQL
                $clause = "REGEXP '^" . $sql->escape(preg_quote($title)) . " '";
            } elseif (App::db()->con()->syntax() == 'postgresql') {
                // PostgreSQL
                $clause = "~ '^" . $sql->escape(preg_quote($title)) . " '";
            } else {
                // SQlite
                $clause = "LIKE '" .
                $sql->escape((string) preg_replace(['/\%/', '/\_/', '/\!/'], ['!%', '!_', '!!'], $title)) . " ' ESCAPE '!'";
            }

            $sql
                ->column('post_title')
                ->from(App::db()->con()->prefix() . App::blog()::POST_TABLE_NAME)
                ->where('post_title ' . $clause)
                ->and('post_type = ' . $sql->quote($type))
                ->and('blog_id = ' . $sql->quote(App::blog()->id()))
                ->order('post_title DESC')
            ;

            $rs = $sql->select();
            $a  = [];
            if ($rs) {
                while ($rs->fetch()) {
                    $a[] = $rs->post_title;
                }
            }

            $i = 1;
            if ($a !== []) {
                natsort($a);
                if (preg_match('/(.*?)(\d+)$/', (string) end($a), $m)) {
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

    /**
     * @param      array<string, string>   $get    The cleaned $_GET
     *
     * @return     array<string, mixed>
     */
    public static function suggestTitle($get): array
    {
        $title = $get['title'] ?? '';
        $type  = $get['type']  ?? 'post';

        $suggest = self::getTitle($title, $type);

        return [
            'ret'     => ($title !== $suggest),
            'msg'     => sprintf(__('The “%1$s” title is already used, would you replace it by “%2$s”?'), $title, $suggest),
            'suggest' => $suggest,
        ];
    }
}
