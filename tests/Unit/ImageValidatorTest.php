<?php

it('tests image validator', function () {
    $storage = get_storage();
    $request = new Scrawler\Http\Request(files: ['test' => uploaded('test.jpg'), uploaded('test.png'), uploaded('test.gif')]);
    $validator = new Scrawler\Validator\Storage\Image();
    $uploaded = $storage->writeRequest(request: $request, validators: $validator);
    expect($uploaded)->toBeArray();
});

it('tests image validator exception', function () {
    $storage = get_storage();
    $request = new Scrawler\Http\Request(files: ['test' => uploaded('test.pdf')]);
    $validator = new Scrawler\Validator\Storage\Image();
    expect(fn () => $storage->writeRequest(request: $request, validators: $validator))->toThrow(Scrawler\Exception\FileValidationException::class, 'Invalid file type.');
});

it('tests image validator invalid file exception', function () {
    $storage = get_storage();
    $request = new Scrawler\Http\Request(files: ['test' => uploaded('corrupt.jpg')]);
    $validator = new Scrawler\Validator\Storage\Image();
    expect(fn () => $storage->writeRequest(request: $request, validators: $validator))->toThrow(Scrawler\Exception\FileValidationException::class, 'Invalid file type.');
});
