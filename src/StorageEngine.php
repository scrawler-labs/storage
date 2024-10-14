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
    public function saveRequest(string $path = '',?\Scrawler\Validator\Whitelist $whitelist=null): array
    {
        if (function_exists('request')) {
            $uploaded = [];
            $files = request()->files->all();
            foreach ($files as $name => $file) {
                if (\is_array($file)) {
                    $paths = [];
                    foreach ($file as $single) {
                        if ($single) {
                            $filepath = $this->writeUploaded($single, $path,$whitelist);
                            $paths[] = $filepath;
                        }
                    }
                    $uploaded[$name] = $paths;
                } elseif ($file) {
                    $uploaded[$name] = $this->writeUploaded($file, $path,$whitelist);
                }
            }

            return $uploaded;
        }

        throw new \Exception('saveRequest() method requires scrawler\http package');
    }

    /**
     * Write the request's uploaded file to the storage.
     */
    public function writeUploaded(UploadedFile $file ,string $path = '',?\Scrawler\Validator\AbstractValidator $validator=null, ?string $filename = null): string
    {
        if (null === $validator) {
           $validator = new \Scrawler\Validator\Blacklist();
        }

        $validator->validate($file);
        $content = $validator->getProcessedContent($file);

        $originalname = explode('.', $file->getClientOriginalName());
        if (null == $filename) {
            $filename = $this->sanitizeFilename($originalname).'.'.$file->guessExtension();
        } else {
            $filename = $this->sanitizeFilename($filename).'.'.$file->guessExtension();
        }
        $this->write($path.$filename, $content);

        return $path.$filename;
    }

    /**
     * Sanitize the filename.
     */
    private function sanitizeFilename(string $filename): string
    {

        $name = preg_replace('/[^a-z0-9-_.]/', '', subject: strtolower($filename));
        $name = preg_replace('/[\. _-]+/', '-', $name);
        $name = trim($name, '-');
        $name = substr($name, 0, 100);
        $name = $name.'_'.uniqid();
        return $name;

    }

    /**
     * Get file public Url.
     */
    public function getUrl(string $path): string
    {
        return $this->getAdapter()->getUrl($path);
    }
}
