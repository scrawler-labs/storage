<?php

function get_storage()
{
    $storage = new Scrawler\Storage();
    $storage->setAdapter(new Scrawler\Adapters\Storage\LocalAdapter(__DIR__.'/storage'));

    return $storage;
}

function uploaded($name)
{
    return new Symfony\Component\HttpFoundation\File\UploadedFile(__DIR__.'/files/'.$name, $name, test: true);
}
