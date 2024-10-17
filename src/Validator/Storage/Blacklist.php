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

class Blacklist extends AbstractValidator
{
    /**
     * @var array<string>
     */
    protected array $blockedMimeTypes = [
        'application/x-httpd-php',
        'application/x-httpd-php-source',
        'application/x-php',
        'text/php',
        'text/x-php',
        'application/octet-stream',
        // windows specific
        'application/x-msdownload',
        'application/x-msdos-program',
        'application/x-msi',
        'application/x-msdos-windows',
        'application/x-msdos-program',
    ];
    /**
     * @var array<string>
     */
    protected array $blockedExtensions = [
        'php',
        'php3',
        'php4',
        'php5',
        'phtml',
        'phar',
        'phpt',
        'phps',
        'php-s',
        'pht',
        'htaccess',
        'htpasswd',
        'inc',
        'ini',
        'sh',
        'bash',
        'bashrc',
        'bash_profile',
        'bash_aliases',
        'bash_history',
        'bash_logout',
        'bash_login',
        'bashrc',
        'bin',
        'cgi',
        // windows specific
        'bat',
        'cmd',
        'com',
        'cpl',
        'exe',
        'gadget',
        'inf',
        'ins',
        'inx',
        'isu',
        'job',
        'jse',
        'lnk',
        'msc',
        'msi',
        'msp',
        'mst',
    ];

    /**
     * Validate the uploaded file.
     *
     * @throws \Scrawler\Exception\FileValidationException
     */
    #[\Override]
    public function validate(UploadedFile|File $file): void
    {
        if (\in_array($file->getMimeType(), $this->blockedMimeTypes)) {
            throw new \Scrawler\Exception\FileValidationException('Invalid file type.');
        }
        // @codeCoverageIgnoreStart
        if (\in_array($file->guessExtension(), $this->blockedExtensions)) {
            throw new \Scrawler\Exception\FileValidationException('Invalid file extension.');
        }
        // @codeCoverageIgnoreEnd
    }
}
