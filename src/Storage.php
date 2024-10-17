<?php
/*
 * This file is part of the Scrawler package.
 *
 * (c) Pranjal Pandey <its.pranjalpandey@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Scrawler;

use League\Flysystem\FilesystemAdapter;

/**
 * Scrawler Filesystem Class.
 *
 * @mixin \Scrawler\StorageEngine
 */
class Storage
{
    private ?StorageEngine $engine = null;

    public const PUBLIC = 'public';
    public const PRIVATE = 'private';

    public function setAdapter(FilesystemAdapter $adapter): void
    {
        $this->engine = new StorageEngine($adapter);
    }

    /**
     * @param array<mixed> $args
     *
     * @throws \Exception
     */
    public function __call(string $method, array $args): mixed
    {
        if (is_null($this->engine)) {
            throw new \Exception('Please set adapter using storage()->setAdapter($adapter) first');
        }

        return $this->engine->$method(...$args);
    }
}
