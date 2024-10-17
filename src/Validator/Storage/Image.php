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

class Image extends AbstractValidator
{
    protected ?string $mime = null;
    /**
     * @var array<string>
     */
    protected array $allowedMimeTypes = [
        'image/gif',
        'image/x-gif',
        'image/agif',
        'image/x-png',
        'image/png',
        'image/a-png',
        'image/apng',
        'image/jpg',
        'image/jpe',
        'image/jpeg',
        'image/pjpeg',
        'image/x-jpeg',
    ];

    /**
     * @var array<string>
     */
    protected array $allowedExtensions = [
        'jpeg',
        'jpg',
        'png',
        'gif',
        'apng',
    ];

    protected int $maxSize = 10 * 1024 * 1024;

    /**
     * @return array<string>
     */
    private function mimes(string $which): array
    {
        return match ($which) {
            'png' => ['image/x-png', 'image/png', 'image/a-png', 'image/apng'],
            'jpg' => ['image/jpg', 'image/jpe', 'image/jpeg', 'image/pjpeg', 'image/x-jpeg'],
            'normalize' => ['image/gif', 'image/jpeg', 'image/png'],
            default => [],
        };
    }

    /**
     * Validate the uploaded file.
     *
     * @throws \Scrawler\Exception\FileValidationException
     */
    #[\Override]
    public function validate(UploadedFile|File $file): void
    {
        if (!in_array($file->getMimeType(), $this->allowedMimeTypes)) {
            throw new \Scrawler\Exception\FileValidationException('Invalid file type.');
        }

        // @codeCoverageIgnoreStart
        if (!in_array($file->guessExtension(), $this->allowedExtensions)) {
            throw new \Scrawler\Exception\FileValidationException('Invalid file extension.');
        }
        // @codeCoverageIgnoreEnd

        $this->mimeScan($file);
        $this->binaryScan($file);
    }

    #[\Override]
    public function getProcessedContent(UploadedFile|File $file): string
    {
        $file = $this->processImage($file);

        return $file->getContent();
    }

    private function mimeScan(UploadedFile|File $file): void
    {
        // Mime-type assignment
        if (in_array($file->getMimeType(), $this->mimes('png'))) {
            $this->mime = 'image/png';
        } elseif (in_array($file->getMimeType(), $this->mimes('jpg'))) {
            $this->mime = 'image/jpeg';
        } else {
            $this->mime = $file->getMimeType(); // unknown or gif.
        }
    }

    private function binaryScan(UploadedFile|File $file): void
    {
        $readfile = $file->getContent();
        $chunk = strtolower(bin2hex($readfile));
        $normalize = $this->mimes('normalize');

        // Experimental binary validation
        // @codeCoverageIgnoreStart
        switch ($this->mime) {
            // We allow for 16 bit padding
            case $normalize[0]:
                if (!\Safe\preg_match('/474946/msx', substr($chunk, 0, 16)) && 'image/gif' === $this->mime) {
                    throw new \Scrawler\Exception\FileValidationException('Invalid GIF file');
                }
                break;
            case $normalize[1]:
                if (!\Safe\preg_match('/ff(d8|d9|c0|c2|c4|da|db|dd)/msx', substr($chunk, 0, 16)) && 'image/jpeg' === $this->mime) {
                    throw new \Scrawler\Exception\FileValidationException(message: 'Invalid JPEG file');
                    // preg_match('/[{0001}-{0022}]/u', $chunk);
                }
                if (!\Safe\preg_match('/ffd9/', substr($chunk, strlen($chunk) - 32, 32)) && 'image/jpeg' === $this->mime) {
                    throw new \Scrawler\Exception\FileValidationException(message: 'Invalid JPEG file');
                }

                break;
            case $normalize[2]:
                if (!\Safe\preg_match('/504e47/', substr($chunk, 0, 16)) && 'image/png' === $this->mime) {
                    throw new \Scrawler\Exception\FileValidationException(message: 'Invalid PNG file');
                }
                break;
        }
        // @codeCoverageIgnoreEnd
    }

    private function processImage(UploadedFile|File $file): UploadedFile|File
    {
        $normalize = $this->mimes('normalize');

        [$w, $h] = \Safe\getimagesize($file->getPathname());
        // If thumbnail, get new size.

        $new_width = $w;
        $new_height = $h;

        // Check if GD is available
        $gdcheck = gd_info();

        // Re-sample.
        switch ($this->mime) {
            case $normalize[0]:
                if (true == isset($gdcheck['GIF Create Support'])) {
                    $image = \Safe\imagecreatefromgif($file->getPathname());
                    $resampled = \Safe\imagecreatetruecolor($new_width, $new_height);
                    \Safe\imagecopyresampled($resampled, $image, 0, 0, 0, 0, $new_width, $new_height, $w, $h);
                    \Safe\imagegif($resampled, $file->getPathname());
                    $endsize = \Safe\filesize($file->getPathname());
                }
                break;
            case $normalize[1]:
                if (true == isset($gdcheck['JPG Support']) || true == isset($gdcheck['JPEG Support'])) {
                    $image = \Safe\imagecreatefromjpeg($file->getPathname());
                    $resampled = \Safe\imagecreatetruecolor($new_width, $new_height);
                    \Safe\imagecopyresampled($resampled, $image, 0, 0, 0, 0, $new_width, $new_height, $w, $h);
                    \Safe\imagejpeg($resampled, $file->getPathname(), 100);
                    $endsize = \Safe\filesize($file->getPathname());
                }
                break;
            case $normalize[2]:
                if (true == isset($gdcheck['PNG Support'])) {
                    $resampled = \Safe\imagecreatetruecolor($new_width, $new_height);
                    $image = \Safe\imagecreatefrompng($file->getPathname());
                    \Safe\imagealphablending($resampled, true);
                    \Safe\imagesavealpha($resampled, true);
                    \Safe\imagecopyresampled($resampled, $image, 0, 0, 0, 0, $new_width, $new_height, $w, $h);
                    \Safe\imagepng($resampled, $file->getPathname(), 9);
                    $endsize = \Safe\filesize($file->getPathname());
                }
                break;
        }

        return $file;
    }
}
