# Brick

[![Build Status](https://travis-ci.org/gajus/brick.png?branch=master)](https://travis-ci.org/gajus/brick)
[![Coverage Status](https://coveralls.io/repos/gajus/brick/badge.png?branch=master)](https://coveralls.io/r/gajus/brick?branch=master)
[![Latest Stable Version](https://poser.pugx.org/gajus/brick/version.png)](https://packagist.org/packages/gajus/brick)
[![License](https://poser.pugx.org/gajus/brick/license.png)](https://packagist.org/packages/gajus/brick)

PHP template system that's fast, easy to use and easy to extend.

* Plain PHP, no new syntax to learn.
* Framework-agnostic, will work with any project.

## Features

* Handles not found template files
* Protects from [directory traversal attacks](http://en.wikipedia.org/wiki/Directory_traversal_attack) (at the template inclusion level)
* Isolates template execution scope

The following examples together with the included [unit tests](https://github.com/gajus/brick/tree/master/tests) will set you going. Please [raise an issue](https://github.com/gajus/brick/issues) if you feel that there are bits that need to be clarified.

## Getting Started

`System` class is responsible for template resolution and scope management.

```php
$system = new \Gajus\Brick\System($templates_directory);
```

`Template` class is responsible for isolating template execution scope, extracting scope variables and capturing the output buffer.

```php
$template = new \Gajus\Brick\Template($file, $scope);
```

### Producing a View

```php
// Set the absolute path to the folder containing templates.
$system = new \Gajus\Brick\System(__DIR__ . '/templates');
// Refer to the template using a path relative to the template folder.
echo $system->view('foo');
```

Template file must have a ".php" extension. When referring to templates, do not include the file extension. You can change the name of the extension:

```php
$system->setTemplateExtension('.tpl.php');
```

### Assigning Variables

Scope variables are extracted to the execution context of the template, i.e. template can access them as regular variables.

```php
// template_that_is_using_foo_variable.php
$foo;
```

Scope variables are assigned at the time of producing a view.

```php
$system->view('template_that_is_using_foo_variable', ['foo' => 'bar']); // 'bar'
```

### Globals

Views produced using the same instance of the `System` have access to a `$globals` variables.

```php
$system->getGlobals(['foo' => 'bar']);
$system->view('template_that_is_using_foo_variable'); // 'bar'
```

### View Scope

* `$system` an instance of the system that produced the scope.
* `$globals` variables shared across all views produced by the same instance of `System`.