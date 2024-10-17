<div align="center">
<img src="https://user-images.githubusercontent.com/7591484/170873489-6aa40fe3-9d5c-476b-9434-f12f0a896c85.png" width="20%">

<h1> Scrawler Storage </h1>

<a href="https://github.com/scrawler-labs/storage/actions/workflows/main.yml"><img alt="GitHub Workflow Status" src="https://img.shields.io/github/actions/workflow/status/scrawler-labs/storage/main.yml?style=flat-square">
</a>
[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/quality/g/scrawler-labs/storage?style=flat-square)](https://scrutinizer-ci.com/g/scrawler-labs/storage/?branch=main)
<a href="[https://github.com/scrawler-labs/app/actions/workflows/main.yml](https://github.com/scrawler-labs/app/actions/workflows/main.yml)"><img src="https://img.shields.io/badge/PHPStan-enabled-brightgreen.svg?style=flat-square" alt="PHPStan Enabled"></a>
[![Packagist Version](https://img.shields.io/packagist/v/scrawler/app?style=flat-square)](https://packagist.org/packages/scrawler/app)
[![Packagist License](https://img.shields.io/packagist/l/scrawler/app?style=flat-square)](https://packagist.org/packages/scrawler/app)

<br><br>


🔥Wrapper around Flysystem with added features for scrawler storage engine 🔥<br>
🇮🇳 Made in India 🇮🇳
</div>

## 💻 Installation
You can install Scrawler App via Composer. If you don't have composer installed , you can download composer from [here](https://getcomposer.org/download/)

```sh
composer require scrawler/storage
```

## ✨ Basic usage
```php
<?php

require __DIR__ . '/vendor/autoload.php';

storage()->setAdapter(new \Scrawler\Adapters\Storage\LocalAdapter(__DIR__ . '/../storage/app'));
storage()->write('hello.txt','hello world');

```



