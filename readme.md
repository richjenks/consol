# Consul

Micro-framework for command line PHP apps

*It's like Slim for console applications*

- Dispatches requests
- Generates command directory
- Autoloads your program
- Publishes app as a PHAR

## Usage

1. Download latest version of box from https://github.com/box-project/box2/releases
1. Put in Consol dir
1. Copy `App/box.json.sample` to `box.json` and adjust as necessary
1. Run `box build`

To access app features within a callback, use `global $app;` which exposes any additional arguments passed in `$app->args` as well as several functions like `say()` which accept text and a color, e.g. `say('Hi!', 'green')`.

Requirements:
- PHP 5.4
- Phar extension
- phar.readonly = 0

Notes:

Commands directory should handle really long command names!
Group commands like box does
PHPUnit for tests?
How to reconcile composer's autoloader with an include-all?
Auto-update? How do box and composer do it?
help command with arg for documentation to query?
Help class needs some work!