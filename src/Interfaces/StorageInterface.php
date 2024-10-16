<?php
/*
 * This file is part of the Scrawler package.
 *
 * (c) Pranjal Pandey <its.pranjalpandey@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Scrawler\Interfaces;

interface StorageInterface extends \League\Flysystem\FilesystemAdapter
{
    /**
     * Get file Url.
     */
    public function getUrl(string $path): string;
}
