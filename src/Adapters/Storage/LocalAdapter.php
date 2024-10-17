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
use League\Flysystem\UnableToGeneratePublicUrl;
use League\Flysystem\UrlGeneration\PublicUrlGenerator;

class LocalAdapter extends LocalFilesystemAdapter implements PublicUrlGenerator
{
    public function __construct(
        private readonly string $storagePath,
    ) {
        parent::__construct($storagePath);
    }

    #[\Override]
    public function publicUrl(string $path, \League\Flysystem\Config $config): string
    {
        if ($this->fileExists('public/'.$path)) {
            if (function_exists('url')) {
                // @codeCoverageIgnoreStart
                return url($this->storagePath.'//public//'.$path);
                // @codeCoverageIgnoreEnd
            }

            return $this->storagePath.'//public//'.$path;
        }
        throw new UnableToGeneratePublicUrl('File is not public', $path);
    }
}
