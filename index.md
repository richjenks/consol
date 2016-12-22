# Consol — Micro framework for command line PHP apps

Consol is a minimal and unopinionated micro framework for developing simple yet powerful command line apps and services that is easy to read and write, requires minimal prerequisite knowledge, has no dependencies and prefers common-case over edge-case solutions.

## Hello, World!

```php
// app.php
$app = new RichJenks\Consol\App;

$app->map('hello', function () {
    return 'Hello World';
});

```

```bash
# terminal
php app.php hello
# Hello World
```

## Key Features

- Routes terminal requests
- Simplifies parameters and options
- Generates command directory
- Packages your app as a PHAR

## Requirements

- PHP >=5.5
- Phar PHP extension
- `phar.readonly = 0` in `/etc/php5/cli/php.ini`

## Installation

Install via [Composer](https://getcomposer.org/) with `composer require richjenks/consol` or download and include it in your code with `require 'consol/App.php';`

## Mapping Commands to Code

A Consol app could look like this:

```php
#!/usr/local/bin/php
<?php require 'vendor/autoload.php';

$app->map('hello', function ($app) {
    return 'Hello World';
}, 'Say "Hello" to the World');

$app->run();
```

Calling `php app.php hello` from the command line will run this command. The `map` function accepts 3 parameters:

1. Command to trigger the route
2. Callback to map to the command
3. Optional description of the command

> Commands can only contain alphanumerics, colons, hyphens and underscores.

## Magic Commands

Magic Commands are commands that are not included in the command map and are called by special events. There are 2 Magic Commands and they are:

1. Colon (`:`) which represents when no command is issued (the root command)
2. Question Mark (`?`) which represents an unknown command

Here's an example of overriding the default unknown command handler:

```php
$app->map('?', function ($app) {
    return $app->say('I have no idea what I\'m doing...');
});
```

Both have default handlers which are overridden by mapping them to your own code. By default, the root command will show the Command Directory (which has its own section below) and unknown commands will display the text "Unknown Command" in red and may attempt to suggest an intended function in case of a typo via the `suggest()` function,  which is documented further down.

## Using `$app`

The `$app` object is made available to your commands and it exposes a number of useful functions:

### Say

Outputs text to the console and should be used instead of `echo`, `print`, etc. because it outputs immediately (rather than at the end of the script) and allows responses to be edited by Middleware (which has its own section below). It accepts 2 parameters:

1. The text to be output
2. An optional color for the text

Available colors are `aqua`, `black`, `blue`, `cyan`, `emerald`, `gray`, `lilac`, `green`, `maroon`, `navy`, `orange`, `purple`, `red`, `silver`, `white` and `yellow`.

For example: `$app->say('Oh hai!', 'green');`

### Color

Identical to `say()` except it returns the text rather than outputting immediately.

### Ask

Prompt the user to provide information, for example:

```php
$name = ask('What is your name: ');
say("Hello, $name!");
```

It has three parameters but only the first is required:

1. The text to display to the user
2. Default value if user enters nothing (or `null` for no default value)
3. Regular Expression against which the value is checked

> Providing `null` as the default value means that no default value will be considered — it does not mean it will return `null` if no value is provided.

Here's a more practical example:

```php
$name = $app->ask('What is your name: ', null, '.+');
$app->say("Hello, $name!");
```

The above example will prompt the user for their name, but if none is given then it will ask again. The answer must be at least one character. `ask` isn't very clever, so you are free to define whatever logic you like.

Or to ask for confirmation:

```php
$sure = $app->ask('Are you sure? [y/n] ', 'n', 'y|n');
$app->say(($sure === 'y') ? 'You are sure' : 'You are unsure');
```

Here are a few example regular expressions for common requirements:

- `.*`  Accept anything, including empty values
- `.+` Accept anything, must be at least one character
- `y|n` Accepts anything containing "y", "n" (basic confirmation)
- `^yes$|^no$|^maybe$` Accepts only "yes", "no" or "maybe"

> Neither validation rules nor default values are shown to the user — you are free to provide whatever information you wish in the first "question" parameter.

### Progress

Shows a progress bar to the user and accepts two values: current progress and total. For example:

```php
$app->say('Progressing...' . PHP_EOL);
$progress = 0;
while ($progress <= 70) {
    $app->progress($progress, 70);
    $progress += 3;
    sleep(1);
}
$app->progress(70, 70);
$app->say(PHP_EOL . 'Progress complete' . PHP_EOL);
```

### Table
### Suggest
### Get Request

This is a testing function that can be used to analyse the request. It returns an array with the keys `command`, `params`, `options` and `map`:

```php
var_dump($app->get_request());
//
```

The `param()` and `option()` functions are preferred as they integrate will middleware and are generally "nicer" from a syntax perspective.

## Command Directory

When mapping commands to callbacks, the `$app->map()` function accepts an optional third argument: a description of the command. This is used to construct a directory of available commands that is shown by the default handler for the root command and it looks like this:

```
Commands:
  hello   Say "Hello" to the World

```

The directory makes use of the `table()` function (documented below) to output tabular data and it can also be shown manually by returning `$app->directory()` from within a command's callback:

```php
$app->map('directory', function ($app) {
    $app->say($app->directory());
});
```

## Parameters

Data can be retrieved from the command line in the form of parameters. For example, running `php app.php hello World` will return `Hello World`. `$app->param()` can be used to get the value of a parameter in a one-indexed fashion — zero contains the command, which in this case is `hello`.

For example, to say hello in the color of your choosing, you could register the following command:

```php
$app->map('hello', function ($app) {
    return $app->say(
        'Hello ' . $app->param(1),
        $app->param(2)
    );
});
```

and then call:

```bash
php app.php hello World blue
# Outputs "Hello World" in blue
```

## Options

Options are similar to Parameters but are prepended by two hyphens, e.g. `--foo`, and can optionally provide values, e.g. `--foo=bar`. They can be at any position in the command line call but are ignored by the indexing of parameters. For example, if you call `php app.php foo --fizzbuzz bar` then `foo` will still be parameter one and `bar` will still be parameter two.

> No options are sacred to a Consol app — for example, there is no concept of verbosity and no options are reserved, so you are free to implement any options you like!

The two types of options are *Flag* and *Value* options:

### Flags

Flags are `true` when present and `false` when omitted:

```php
$app->map('hello', function ($app) {
    $greeting = 'Hello ' . $app->param(1);
    if ($app->option('yell')) $greeting = strtoupper($greeting);
    return $app->say($greeting);
});
```

```bash
php app.php hello World --yell
# Outputs "HELLO WORLD"
```

Flag options can also be passed in short form (e.g. `-yv`) and you can check for the presence of multiple flags to facilitate this, e.g. `if ($app->option('y', 'yell')) { ... }`. No link exists between short and long options so you are free to define any combination you wish by use of the `option()` function.

### Values

Values hold strings when present and are `false` when omitted:

```php
$app->map('hello', function ($app) {
    $greeting = 'Hello ' . $app->param(1);
    if (is_string($app->option('from')))
        $greeting .= ', from ' . $app->option('from');
    return $app->say($greeting);
});
```

```bash
php app.php hello World --from=Moon
# Outputs "Hello World, from Moon"
```

Note that Consol can't distinguish between a flag option and an empty value option, so `is_string()` should be used to check for the presence of a value option. Absent value options return `false` like an omitted flag option.

## Middleware

Middleware allows you to edit a request before it is routed through the map and the response output before it is shown to the console. Middleware in console applications is not as important as with web applications because of the proportion of things happening in the background and things visible to the user, but it is still a useful tool that allows multiple commands to be edited by the same filter(s).