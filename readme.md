# Consol — Micro framework for command line PHP apps

> It's like Slim for console applications

## Hello, World!

```php
// index.php
$app = new RichJenks\Consol\App;

$app->map(':', function () {
    return 'Hello World';
});

```

```bash
# terminal
php index.php
# Hello, World!
```

## Features

- Routes terminal requests
- Simplifies parameters and options
- Generates command directory
- Packages your app as a PHAR