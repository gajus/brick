# Brick

[![Build Status](https://travis-ci.org/gajus/brick.png?branch=master&2)](https://travis-ci.org/gajus/brick)
[![Coverage Status](https://coveralls.io/repos/gajus/brick/badge.png?branch=master&2)](https://coveralls.io/r/gajus/brick?branch=master)
[![Latest Stable Version](https://poser.pugx.org/gajus/brick/version.png?2)](https://packagist.org/packages/gajus/brick)
[![License](https://poser.pugx.org/gajus/brick/license.png)](https://packagist.org/packages/gajus/brick)

PHP template system that's fast, easy to use and easy to extend.

* Plain PHP, no new syntax to learn.
* Framework-agnostic, will work with any project.

> This requires some explanation to do.
> This is not a template engine that would lex/parse template as a string (think Twig). If you are using one, you must have a good reason (such as cross-platform template processing).
> Some of you might remember Chad Minick's article "Simple PHP Template Engine" http://chadminick.com/articles/simple-php-template-engine.html (2009; Has it been that long?). I have been using a variation of an abstraction following the principles outlined in Chad's article for a long time. Brick is the final of the gang. I am happy with the API, I am happy with the inheritance rules, scope definition; it is perfect!
> I am using Brick in several freelance projects and I am making it open to others. Critique is welcome and it would be fun to find people-contributors who share the same mindset about what template handling in PHP should be.

– http://www.reddit.com/r/PHP/comments/2nduuc/brick_php_template_system_thats_fast_easy_to_use/

## Features

* Handles not found template files
* Protects from [directory traversal attacks](http://en.wikipedia.org/wiki/Directory_traversal_attack) (at the template inclusion level)
* Isolates template execution scope

The following examples together with the included [unit tests](https://github.com/gajus/brick/tree/master/tests) will set you going. Please [raise an issue](https://github.com/gajus/brick/issues) if you feel that there are bits that need to be clarified.

## Overview

### System

`System` class is responsible for template resolution and scope management.

Public methods:

* [setDirectory](https://github.com/gajus/brick/blob/master/src/System.php)
* [getDirectory](https://github.com/gajus/brick/blob/master/src/System.php)
* [setGlobals](https://github.com/gajus/brick/blob/master/src/System.php)
* [getGlobals](https://github.com/gajus/brick/blob/master/src/System.php)
* [setTemplateExtension](https://github.com/gajus/brick/blob/master/src/System.php)
* [getTemplateExtension](https://github.com/gajus/brick/blob/master/src/System.php)
* [view](https://github.com/gajus/brick/blob/master/src/System.php)
* [template](https://github.com/gajus/brick/blob/master/src/System.php)

### Subsystem

`Subsystem` class is responsible for template resolution and scope management.

Views that are built using an instance of `System` will be using `Subsystem` to produce inner views. This restricts a template access to controlling the globals and other sensitive variables.

Public methods:

* [view](https://github.com/gajus/brick/blob/master/src/Subsystem.php)

### Template

`Template` class is responsible for isolating template execution scope, extracting scope variables and capturing the output buffer.

Public methods:

* [render](https://github.com/gajus/brick/blob/master/src/Template.php)

## Getting Started

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

#### View Scope

* `$system` an instance of `Subsystem`.
* `$globals` variables shared across all views produced by the same instance of `System`.
* Variables assigned to the view at the time of producing the view.

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