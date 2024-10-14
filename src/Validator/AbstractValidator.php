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

use \Symfony\Component\HttpFoundation\File\UploadedFile;

abstract class AbstractValidator
{
    protected int $maxSize = 0;

    /*
    * Get processed file content.
    */
    public function getProcessedContent(UploadedFile $file): string
    {
        return $file->getContent();
    }

    /**
     * Basic Validate the uploaded file.
     *
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $file
     * @throws \Scrawler\Exception\FileValidationException
     */
    public function runValidate(UploadedFile $file): void
    {
        if($this->maxSize = 0){
            $this->maxSize = UploadedFile::getMaxFilesize();
        }

        if(!$file->isValid()){
            throw new \Scrawler\Exception\FileValidationException($file->getErrorMessage());
        }

        if ($file->getError() !== \UPLOAD_ERR_OK) {
            throw new \Scrawler\Exception\FileValidationException($file->getErrorMessage());
        }

        if ($this->maxSize > 0 && $file->getSize() > $this->maxSize) {
            throw new \Scrawler\Exception\FileValidationException('File size size too large.');
        }

        $this->validate($file);
    }

    /**
     * Validate the uploaded file.
     *
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $file
     * @throws \Scrawler\Exception\FileValidationException
     */
    public abstract function validate(UploadedFile $file): void;
    

}
