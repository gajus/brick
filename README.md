# Brick

[![Build Status](https://travis-ci.org/gajus/brick.png?branch=master)](https://travis-ci.org/gajus/brick)
[![Coverage Status](https://coveralls.io/repos/gajus/brick/badge.png?branch=master)](https://coveralls.io/r/gajus/brick?branch=master)
[![Latest Stable Version](https://poser.pugx.org/gajus/brick/version.png)](https://packagist.org/packages/gajus/brick)
[![License](https://poser.pugx.org/gajus/brick/license.png)](https://packagist.org/packages/gajus/brick)

PHP template system that's fast, easy to use and easy to extend.

* Plain PHP, no new syntax to learn.
* Framework-agnostic, will work with any project.

## Documentation

Brick introduces convenience checks for handling not found template files, protecting you from [directory traversal attacks](http://en.wikipedia.org/wiki/Directory_traversal_attack) (at the template inclusion level), and helping to deal with variable scopes.

The following examples together with the included [unit tests](tests/TemplateTest.php) will set you going. Please [raise an issue](https://github.com/gajus/brick/issues) if you feel that there are bits that need to be clarified.

### Getting started

```php
// Instantiate Brick Template with an absolute path to the templates directory:
$template = new \Gajus\Brick\Template(__DIR__ . '/template');

// Refer to the template files relative to the templates directory.
// Your template files must have `.php` file extension.
// You must not include the file extension when referencing templates.
$template->render('hello');
```

### Template

Inside the template, there will be a `$template` variable that refers to the parent Template instance.

```php
// Get names of all the included templates,
$template->getNames();

// Get name of the current template.
$template->getName();
// "hello"

// Include another template inside of this template:
echo $template->render('world');
```

### Variables

To assign a variable to your template, pass it as a second argument to `render` method.

```php
// your application logic
$template->render('house', ['colour' => 'red']);
```

Inside the `house` template, the only two variables available will be `$template` and `$colour`.

Templates do not inherit variables from the parent scope. If `house` template includes `room` template, and you want all of the `house` scope variables to be copied to `room`, then you need to pass them using [get_defined_vars](http://php.net/get_defined_vars), e.g.

```php
// house
$template->render('room', get_defined_vars());
```

### Shared variables

To make variables accessible in all templates, use `$template` variable as an array.

```php
// application
$this->template->render('shared/set');
$this->template->render('shared/get'));
```

```php
$template['foo'] = 'bar';
```

You can access these values from any template:

```php
echo $template['foo'];
```

### Logging

Brick implements [PSR-3](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-3-logger-interface.md) `LoggerAwareInterface` for tracking template rendering.