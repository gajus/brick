# Brick

[![Build Status](https://travis-ci.org/gajus/brick.png?branch=master)](https://travis-ci.org/gajus/brick)
[![Coverage Status](https://coveralls.io/repos/gajus/brick/badge.png?branch=master)](https://coveralls.io/r/gajus/brick?branch=master)
[![Latest Stable Version](https://poser.pugx.org/gajus/brick/version.png)](https://packagist.org/packages/gajus/brick)
[![License](https://poser.pugx.org/gajus/brick/license.png)](https://packagist.org/packages/gajus/brick)

PHP template system that's fast, easy to use and easy to extend.

* Plain PHP, no new syntax to learn.
* Framework-agnostic, will work with any project.

Brick introduces convenience checks for handling not found template files, protecting you from [directory traversal attacks](http://en.wikipedia.org/wiki/Directory_traversal_attack) (at the template inclusion level), and helping to deal with variable scopes.

The following examples together with the included [unit tests](https://github.com/gajus/brick/tree/master/tests) will set you going. Please [raise an issue](https://github.com/gajus/brick/issues) if you feel that there are bits that need to be clarified.

## Getting started

`System` class is responsible for template resolution and scope management.

```php
$system = new \Gajus\Brick\System($template_directory);
```

`Template` class is responsible for isolating template execution scope, extracting scope variables and capturing the output buffer.

```php
$template = new \Gajus\Brick\Template($file, $scope);
```

### Producing a View

```php
$system = new \Gajus\Brick\System(__DIR__ . '/templates');
echo $system->view('foo');
```

### Variables

Variables are assigned at the time of producing a view.

```php
$system->view('template_that_is_using_foo_variable', ['foo' => 'bar']);
```

Scope variables are extracted to the execution context of the template, i.e. you can access the 'foo' property as a regular variable `$foo` inside the template.

```php
// template_that_is_using_foo_variable.php
$foo; // 'bar'
```

### Globals

Globals are variables shared across all views managed by the same instance of the `System`.

```php
$system->getGlobals(['foo' => 'bar']);
```

Now all views that are produced using this instance of `System` have access to `$globals['foo']` variable.