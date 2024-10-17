<?php
use League\Flysystem\UnableToGeneratePublicUrl;

it('tests for storage function', function () {
    $storage = storage();
    expect($storage)->toBeInstanceOf(Scrawler\Storage::class);
});

it('tests for storage write', function () {
    $storage = get_storage();
    $storage->write('test.txt', 'Hello World');
    expect(file_exists(__DIR__.'/../storage/test.txt'))->toBeTrue();
    $content = file_get_contents(__DIR__.'/../storage/test.txt');
    expect($content)->toBe('Hello World');
});

it('tests for publicUrl()', function () {
    $storage = get_storage();
    expect($storage->publicUrl('test.txt'))->toBeString();
});

it('tests for publicUrl() in private file', function () {
    $storage = get_storage();
    $storage->write('testpvt.txt', 'Hello World', ['visibility' => 'private']); 
    expect(fn() => $storage->publicUrl('testpvt.txt'))->toThrow(UnableToGeneratePublicUrl::class);
});
