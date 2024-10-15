<?php

/*
 * This file is part of the Scrawler package.
 *
 * (c) Pranjal Pandey <its.pranjalpandey@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Scrawler\Validator;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class Whitelist extends AbstractValidator
{
    /**
     * @var array<string>
     */
    protected array $allowedMimeTypes = [];
    /**
     * @var array<string>
     */
    protected array $allowedExtensions = [];
    protected int $maxSize = 0;

    /**
     * Set the allowed mime types.
     *
     * @param array<string> $allowedMimeTypes
     */
    public function allowedMimeTypes(array $allowedMimeTypes): void
    {
        $this->allowedMimeTypes = $allowedMimeTypes;
    }

    /**
     * Set the allowed extensions.
     *
     * @param array<string> $allowedExtensions
     */
    public function allowedExtensions(array $allowedExtensions): void
    {
        $this->allowedExtensions = $allowedExtensions;
    }

    /**
     * Validate the uploaded file.
     *
     * @throws \Scrawler\Exception\FileValidationException
     */
    #[\Override]
    public function validate(UploadedFile $file): void
    {
        if ([] !== $this->allowedMimeTypes && !\in_array($file->getMimeType(), $this->allowedMimeTypes, true)) {
            throw new \Scrawler\Exception\FileValidationException('Invalid file type.');
        }

        if ([] !== $this->allowedExtensions && !\in_array($file->guessExtension(), $this->allowedExtensions, true)) {
            throw new \Scrawler\Exception\FileValidationException('Invalid file extension.');
        }
    }

    /**
     * Pdf whitelist for pdf with 5MB max file size.
     */
    public static function pdf(): self
    {
        $whitelist = new self();
        $whitelist->allowedMimeTypes([
            'application/pdf',
        ]);
        $whitelist->allowedExtensions([
            'pdf',
        ]);
        $whitelist->maxSize(1024 * 1024 * 5);

        return $whitelist;
    }

    /**
     * Video whitelist for mp4, mov, mpeg, webm with 20MB max file size.
     */
    public static function video(): self
    {
        $whitelist = new self();
        $whitelist->allowedMimeTypes([
            'video/mp4',
            'video/quicktime',
            'video/mpeg',
            'video/webm',
        ]);
        $whitelist->allowedExtensions([
            'mp4',
            'mov',
            'mpeg',
            'webm',
        ]);
        $whitelist->maxSize(1024 * 1024 * 20);

        return $whitelist;
    }
}
