<?php
/**
 * Adapter for storing in local filesystem
 *
 * @package: Scrawler
 * @author: Pranjal Pandey
 */
namespace Scrawler\Adapters\Storage;

use Scrawler\Interfaces\StorageInterface;
use League\Flysystem\Local\LocalFilesystemAdapter;

class LocalAdapter extends LocalFilesystemAdapter implements StorageInterface{

    public function __construct($storagePath){
        parent::__construct($storagePath);
    }

    public function getUrl($path){
        return url(\Scrawler\App::engine()->config()->get('storage.local').'/'.$path);
    }
}