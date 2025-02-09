# dusk-cli

A php command line interface for [Laravel Dusk](https://laravel.com/docs/10.x/dusk).

**Note** - This is a work in progress and is not ready for production use.

## Installation

-   Install Chrome:
-   Install CLI:

```bash
# install locally and set up an alias or add ./vendor/bin to your $PATH
composer install surgiie/dusk-cli
# or install globally
composer global require surgiie/dusk-cli
```

-   Install Driver:

```bash
dusk install:chrome-driver
```

**Note:** If using wsl2 on windows, you need to install chrome in the linux subsystem.

## Usage

```
dusk visit https://google.com
```

## Perform Actions

All methods in the [Laravel Dusk API](https://laravel.com/docs/11.x/dusk) are performed by passing the method name as a kebab case command option.

For example, to call the `assertSee` method, you would use the `--assert-see` option:

```bash
dusk visit https://laravel.com --assert-see="Laravel"
```

**Note** - Please note that all actions have NOT been tested thoroughly and some actions may not be supported so please report any issues you find.

### Passing Arguments To Methods.

When passing options to call methods that accept more than one argument, you can pass arguments to the methods by separating them with a comma.

For example, when using the `--assert-query-string-has` option:

```bash
dusk visit https://example.com --assert-query-string-has="q,pizza"
```

This will call the `assertQueryStringHas` method with the arguments `q` and `pizza` as follows:

```php
$browser->assertQueryStringHas('q', 'pizza');
```

#### Escaping Commas

If you dont want to the cli to mistake a literal comma delimited string as function arguments, use the `\` character on each comma in the string to escape it:

```bash
# will be interpreted as single argument of "foo,bar,baz"
dusk visit https://example.com --assert-see="foo\,bar\,baz"
```

## Screenshots

To take a screenshot, use the `--screenshot` option:

```bash
dusk visit https://example.com --screenshot="/home/your-user/example.png"
```
