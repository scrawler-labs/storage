<?php
/*
 * This file is part of the Scrawler package.
 *
 * (c) Pranjal Pandey <its.pranjalpandey@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

if (!function_exists('storage')) {
    function storage(): Scrawler\Storage
    {
        // @codeCoverageIgnoreStart
        if (class_exists('\Scrawler\App')) {
            if (!Scrawler\App::engine()->has('storage')) {
                Scrawler\App::engine()->register('storage', new Scrawler\Storage());
            }

            return Scrawler\App::engine()->storage();
        }
        // @codeCoverageIgnoreEnd

        return new Scrawler\Storage();
    }
}
