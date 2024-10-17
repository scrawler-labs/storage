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

use Scrawler\Http\Request;
use Scrawler\Interfaces\StorageInterface;
use Scrawler\Validator\Storage\AbstractValidator as Validator;
use Scrawler\Validator\Storage\Blacklist;
use Symfony\Component\HttpFoundation\File\File;
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
     * @param array<string,Validator>|Validator|null $validators
     *
     * @return array<array<int<0, max>, string>|string>
     */
    public function writeRequest(Request $request, ?string $path = '', array|Validator|null $validators = null): array
    {
        $uploaded = [];
        $files = $request->files->all();
        foreach ($files as $name => $file) {
            if (is_array($validators) && array_key_exists($name, $validators)) {
                $validator = $validators[$name];
            } elseif ($validators instanceof Validator) {
                $validator = $validators;
            } else {
                $validator = null;
            }
            if (\is_array($file)) {
                $paths = [];
                foreach ($file as $single) {
                    if ($single) {
                        $filepath = $this->writeFile($single, $path, $validator);
                        $paths[] = $filepath;
                    }
                }
                $uploaded[$name] = $paths;
            } elseif ($file) {
                $uploaded[$name] = $this->writeFile($file, $path, $validator);
            }
        }

        return $uploaded;
    }

    /**
     * Write the request's uploaded file to the storage.
     */
    public function writeFile(UploadedFile|File $file, ?string $path = '', ?Validator $validator = null, ?string $filename = null): string
    {
        if (!$validator instanceof Validator) {
            $validator = new Blacklist();
        }

        $validator->runValidate($file);
        $content = $validator->getProcessedContent($file);

        $originalname = explode('.', $file->getFilename());
        if (null == $filename) {
            $filename = $this->sanitizeFilename($originalname[0]).'.'.$file->guessExtension();
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
        $name = \Safe\preg_replace('/[^a-z0-9-_.]/', '', subject: strtolower($filename));
        $name = \Safe\preg_replace('/[\. _-]+/', '-', (string) $name);
        $name = trim((string) $name, '-');
        $name = substr($name, 0, 100);

        return $name.'_'.uniqid();
    }

    /**
     * Get file public Url if availabe else returns path.
     */
    public function getUrl(string $path): string
    {
        return $this->getAdapter()->getUrl($path);
    }
}
