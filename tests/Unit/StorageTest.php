<?php

use Scrawler\Exception\FileValidationException;
use Scrawler\Validator\Storage\Whitelist;

afterAll(function () {
    $storage = get_storage();
    $storage->deleteDirectory('');
});

it('tests for storage adapter exception', function () {
    $storage = new Scrawler\Storage();
    expect(fn () => $storage->write('test.txt', 'Hello World'))->toThrow(Exception::class, 'Please set adapter using storage()->setAdapter($adapter) first');
});

it('tests for writeRequest() method', function () {
    $storage = get_storage();
    $request = new Scrawler\Http\Request(files: ['test' => uploaded('hello.txt')]);
    $uploaded = $storage->writeRequest(request: $request);
    expect($uploaded)->toBeArray();
    expect($uploaded['test'])->toContain('hello');
});

it('tests for writeRequest() method with file array', function () {
    $storage = get_storage();
    $request = new Scrawler\Http\Request(files: ['test' => [uploaded('hello.txt'), uploaded('test.jpg')]]);
    $uploaded = $storage->writeRequest(request: $request);
    expect($uploaded)->toBeArray();
    expect($uploaded['test'][0])->toContain('hello');
});

it('tests for writeFile() method ', function () {
    $storage = get_storage();
    $request = new Scrawler\Http\Request(files: ['test' => uploaded('hello.txt')]);
    $files = $request->files->all();
    $uploaded = $storage->writeFile($files['test'], options: ['filename' => 'custom']);

    expect($uploaded)->toContain('custom');
});

it('tests for writeRequest() method default blacklist', function () {
    $storage = get_storage();
    $request = new Scrawler\Http\Request(files: ['test' => uploaded('test.php')]);
    expect(fn () => $storage->writeRequest(request: $request))->toThrow(FileValidationException::class, 'Invalid file type.');
});

it('tests for writeRequest() method with whitelist extension', function () {
    $storage = get_storage();
    $request = new Scrawler\Http\Request(files: ['test' => uploaded('test.jpg')]);
    $validator = new Whitelist();
    $validator->allowedExtensions(['png']);
    expect(fn () => $storage->writeRequest(request: $request, validators: $validator))->toThrow(FileValidationException::class, 'Invalid file extension.');
});

it('tests for writeRequest() method with whitelist mimetype', function () {
    $storage = get_storage();
    $request = new Scrawler\Http\Request(files: ['test' => uploaded('test.jpg')]);
    $validator = new Whitelist();
    $validator->allowedMimeTypes(['image/png']);
    expect(fn () => $storage->writeRequest(request: $request, validators: $validator))->toThrow(FileValidationException::class, 'Invalid file type.');
});

it('tests for writeRequest() method with multiple whitelist validator', function () {
    $storage = get_storage();
    $request = new Scrawler\Http\Request(files: ['testvid' => uploaded('test.jpg'), 'testpdf' => uploaded('test.png')]);
    $vid_validtor = Whitelist::video();
    $pdf_validator = Whitelist::pdf();
    expect(fn () => $storage->writeRequest(request: $request, validators: ['testvid' => $vid_validtor, 'testpdf' => $pdf_validator]))->toThrow(FileValidationException::class, 'Invalid file type.');
    $request = new Scrawler\Http\Request(files: ['testvid' => uploaded('test.mp4'), 'testpdf' => uploaded('test.pdf')]);
    $uploaded = $storage->writeRequest(request: $request, validators: ['testvid' => $vid_validtor, 'testpdf' => $pdf_validator]);
    expect($uploaded)->toBeArray();
});

it('tests for max size during validation', function () {
    $storage = get_storage();
    $request = new Scrawler\Http\Request(files: ['test' => uploaded('test.jpg')]);
    $validator = new Whitelist();
    $validator->maxSize(1);
    expect(fn () => $storage->writeRequest(request: $request, validators: $validator))->toThrow(FileValidationException::class, 'File size size too large.');
});
