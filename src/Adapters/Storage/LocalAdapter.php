<?php
/*
 * This file is part of the Scrawler package.
 *
 * (c) Pranjal Pandey <its.pranjalpandey@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Scrawler\Adapters\Storage;

use League\Flysystem\Local\LocalFilesystemAdapter;
use Scrawler\Interfaces\StorageInterface;

class LocalAdapter extends LocalFilesystemAdapter implements StorageInterface
{
    public function __construct(
        private readonly string $storagePath,
    ) {
        parent::__construct($storagePath);
    }

    #[\Override]
    public function getUrl(string $path): string
    {
        if (function_exists('url')) {
            // @codeCoverageIgnoreStart
            return url($this->storagePath.'/'.$path);
            // @codeCoverageIgnoreEnd
        }

        return $this->storagePath.'/'.$path;
    }
}
