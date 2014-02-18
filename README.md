# Brick

[![Build Status](https://travis-ci.org/gajus/brick.png?branch=master)](https://travis-ci.org/gajus/brick)
[![Coverage Status](https://coveralls.io/repos/gajus/brick/badge.png)](https://coveralls.io/r/gajus/brick)

* Native PHP templates, no new syntax to learn.
* Framework-agnostic, will work with any project.

If the above definition seems familiar, it is because it is a copy-paste-scrap from [Plates](http://platesphp.com/) documentation. Brick is Plates without the bells and whistles, namely:

* [Namespaced](http://platesphp.com/folders/) templates (because you can achieve this using folder structure).
* [Sections](http://platesphp.com/sections/) or [Inheritance](http://platesphp.com/inheritance/) (because you can use [Output Buffering Control](http://uk3.php.net/manual/en/book.outcontrol.php) without the wrapping paper; see examples of how can you achieve the same).

In addition to the above differences, Brick does not share variables between templates unless you [explicitly expand them](#variables).

## Documentation

Brick does not interfere with your template code (for all Brick cares, you can have template driven website with no controllers). Nevertheless, Brick introduces convenience checks for handling not found template files, protecting you from [directory traversal attacks](http://en.wikipedia.org/wiki/Directory_traversal_attack) (at the template inclusion level), and helping to deal with variable scopes.

The above does not require a great deal of documentation. The following examples together with the included [unit tests](tests/TemplateTest.php) will set you going. However, Please [raise an issue](https://github.com/gajus/brick/issues) if you feel that there are bits that need to be clarified.

### Getting started

```php
// Instantiate Brick Template with an absolute path to the templates directory:
$template = new \Gajus\Brick\Template(__DIR__ . '/template');

// Refer to template files relative to the templates directory
// Your template files must have `.tpl.php` file extension.
// You must not include the file extension when referencing templates.
$template->render('hello');
```

### Template

Inside the template, there will be `$template` variable that refers to the parent Template instance.

```php
// Get names of all the included templates,
// e.g. in the case of the preceeding code, the output is ["hello"].
$template->getNames();

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

Beware that templates do not inherit variables from the parent scope. If `house` template includes `room` template, and you want all of the `house` scope variables to be copied to `room`, then you need to pass them using [get_defined_vars](http://php.net/get_defined_vars), e.g.

```php
// house.tpl.php

$template->render('room', get_defined_vars());
```

### Shared variables

There might be a case when you want to make certain variables accessible in all templates. In such case, you can use `$template` variable as an array.

```php
// application
$this->template->render('shared/set');
$this->template->render('shared/get'));
```

```
// shared/set
$template['foo'] = 'bar';
```

```
// shared/get
echo $template['foo'];
```

### Inheritence

Suppose you have a blog application, which consists of [post.tpl.php](tests/template/safe/inheritence/post.tpl.php) and [blog.inc.tpl.php](tests/template/safe/inheritence/blog.inc.tpl.php) templates.

Your `post` template might look something like this:

```
<?php ob_start()?>
<h1><?=$post['name']?></h1>
<p><?=$post['body']?></p>
<?=$template->render('inheritence/blog.inc', ['post' => $post, 'body' => ob_get_clean()])?>
```

Then your `blog` template can supress the previous output and inject it where it seen it fit:

```
<!DOCTYPE html>
<html>
<head>
    <title><?=$post['name']?></title>
</head>
<body>
    <?=$body?>
</body>
</html>
```