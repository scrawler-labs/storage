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

use Scrawler\Interfaces\StorageInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Scrawler filesystem storage engine.
 */
class StorageEngine extends \League\Flysystem\Filesystem
{
    /**
     * @param array<mixed> $config
     */
    public function __construct(protected StorageInterface $adapter, array $config = [])
    {
        parent::__construct($this->adapter, $config);
    }

    /**
     * Get the Adapter.
     */
    public function getAdapter(): StorageInterface
    {
        return $this->adapter;
    }

    /**
     * Stores the files in request to  specific path.
     *
     * @return array<array<int<0, max>, string>|string>
     */
    public function saveRequest(string $path = ''): array
    {
        if (function_exists('request')) {
            $uploaded = [];
            $files = request()->files->all();
            foreach ($files as $name => $file) {
                if (\is_array($file)) {
                    $paths = [];
                    foreach ($file as $single) {
                        if ($single) {
                            $filepath = $this->writeUploaded($single, $path);
                            $paths[] = $filepath;
                        }
                    }
                    $uploaded[$name] = $paths;
                } elseif ($file) {
                    $uploaded[$name] = $this->writeUploaded($file, $path);
                }
            }

            return $uploaded;
        }

        throw new \Exception('saveRequest() method requires scrawler\http package');
    }

    /**
     * Write the request's uploaded file to the storage.
     */
    public function writeUploaded(UploadedFile $file, string $path = '', ?string $filename = null): string
    {
        $content = \Safe\file_get_contents($file->getPathname());

        $originalname = explode('.', $file->getClientOriginalName());
        if (null == $filename) {
            $filename = $originalname[0].'_'.uniqid().'.'.$file->getClientOriginalExtension();
        } else {
            $filename = $filename.'.'.$file->getClientOriginalExtension();
        }
        $this->write($path.$filename, $content);

        return $path.$filename;
    }

    /**
     * Get file public Url.
     */
    public function getUrl(string $path): string
    {
        return $this->getAdapter()->getUrl($path);
    }
}
