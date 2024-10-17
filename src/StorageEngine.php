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
use Scrawler\Http\Request;
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
    public function __construct(protected FilesystemAdapter $adapter, array $config = [])
    {
        parent::__construct($this->adapter, $config);
    }

    /**
     * Stores the files in request to  specific path.
     *
     * @param array<string,Validator>|Validator|null $validators
     * @param array<string,mixed>                    $options
     *
     * @return array<array<int<0, max>, string>|string>
     */
    public function writeRequest(Request $request, string $path = '', array|Validator|null $validators = null, array $options = []): array
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
                        $filepath = $this->writeFile($single, $path, $validator, $options);
                        $paths[] = $filepath;
                    }
                }
                $uploaded[$name] = $paths;
            } elseif ($file) {
                $uploaded[$name] = $this->writeFile($file, $path, $validator, $options);
            }
        }

        return $uploaded;
    }

    /**
     * Write the request's uploaded file to the storage.
     *
     * @param array<string,mixed> $options
     */
    public function writeFile(UploadedFile|File $file, string $path = '', ?Validator $validator = null, array $options = []): string
    {
        if (!$validator instanceof Validator) {
            $validator = new Blacklist();
        }

        $validator->runValidate($file);
        $content = $validator->getProcessedContent($file);

        $originalname = explode('.', $file->getFilename());
        if (array_key_exists('filename', $options)) {
            $filename = $this->sanitizeFilename($options['filename']).'.'.$file->guessExtension();
        } else {
            $filename = $this->sanitizeFilename($originalname[0]).'.'.$file->guessExtension();
        }
        $visibility = $options['visibility'] ?? 'public';
        $this->write($path.'/'.$visibility.'/'.$filename, $content, ['visibility' => $visibility]);

        return $path.'/'.$visibility.'/'.$filename;
    }

    /**
     * Write the content to the storage.
     *
     * @param array<string,mixed> $config
     */
    public function write(string $path, string $content, array $config = []): void
    {
        if (!array_key_exists('visibility', $config)) {
            $config['visibility'] = 'public';
        }

        parent::write($config['visibility'].'/'.$path, $content, $config);
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
}
