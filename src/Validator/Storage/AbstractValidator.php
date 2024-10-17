<?php
/*
 * This file is part of the Scrawler package.
 *
 * (c) Pranjal Pandey <its.pranjalpandey@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Scrawler\Validator\Storage;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

abstract class AbstractValidator
{
    protected int $maxSize = 0;

    /*
    * Get processed file content.
    */
    public function getProcessedContent(UploadedFile|File $file): string
    {
        return $file->getContent();
    }

    public function maxSize(int $maxSize): void
    {
        $this->maxSize = $this->maxSize > $maxSize || 0 == $this->maxSize ? $maxSize : $this->maxSize;
    }

    /**
     * Basic Validate the uploaded file.
     *
     * @throws \Scrawler\Exception\FileValidationException
     */
    public function runValidate(UploadedFile|File $file): void
    {
        // @codeCoverageIgnoreStart
        if ($file instanceof UploadedFile) {
            if (!$file->isValid()) {
                throw new \Scrawler\Exception\FileValidationException($file->getErrorMessage());
            }

            if (\UPLOAD_ERR_OK !== $file->getError()) {
                throw new \Scrawler\Exception\FileValidationException($file->getErrorMessage());
            }
        }
        // @codeCoverageIgnoreEnd
        if (0 === $this->maxSize) {
            $this->maxSize = (int) UploadedFile::getMaxFilesize();
        }

        if ($file->getSize() > $this->maxSize) {
            throw new \Scrawler\Exception\FileValidationException('File size size too large.');
        }

        $this->validate($file);
    }

    /**
     * Validate the uploaded file.
     *
     * @throws \Scrawler\Exception\FileValidationException
     */
    abstract public function validate(UploadedFile|File $file): void;
}
