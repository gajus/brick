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

```PHP
// Instantiate Brick Template with an absolute path to the templates directory:
$template = new \Gajus\Brick\Template(__DIR__ . '/template');

// Refer to template files relative to the templates directory
// Your template files must have `.tpl.php` file extension.
// You must not include the file extension when referencing templates.
$template->render('hello');
```

### Template

Inside the template, there will be `$template` variable that refers to the parent Template instance.

```PHP
// Get names of all the included templates,
// e.g. in the case of the preceeding code, the output is ["hello"].
$template->getNames();

// Include another template inside of this template:
echo $template->render('world');
```

### Variables

To assign a variable to your template, pass it as a second argument to `render` method.

```PHP
// your application logic
$template->render('house', ['colour' => 'red']);
```

Inside the `house` template, the only two variables available will be `$template` and `$colour`.

Beware that templates do not inherit variables from the parent scope. If `house` template includes `room` template, and you want all of the `house` scope variables to be copied to `room`, then you need to pass them using [get_defined_vars](http://php.net/get_defined_vars), e.g.

```PHP
// house.tpl.php
$template->render('room', get_defined_vars());
```

### Shared variables

There might be a case when you want to make certain variables accessible in all templates. In such case, you can use `$template` variable as an array.

```PHP
// application
$this->template->render('shared/set');
$this->template->render('shared/get'));
```

```PHP
// shared/set
$template['foo'] = 'bar';
```

```PHP
// shared/get
echo $template['foo'];
```

### Inheritence

`extend` method is used when you need template to wrap itself in another template, e.g. a blog application, which consists of [post](tests/template/safe/inheritence/post.tpl.php) and [blog](tests/template/safe/inheritence/blog.tpl.php) templates.

Your `post` template might look something like this:

```HTML+PHP
<?php $template->extend('inheritence/blog', ['post' => $post])?>
<h1><?=$post['name']?></h1>
<p><?=$post['body']?></p>
```

When `post` template is rendered, the output will be passed to the `blog` template.

```HTML+PHP
<!DOCTYPE html>
<html>
    <head>
        <title><?=$post['name']?></title>
    </head>
    <body>
        <?=$output?>
    </body>
</html>
```

The original call to get the `post` template will produce the output of the `post` template wrapped in the `blog` template.

```HTML+PHP
<!DOCTYPE html>
<html>
    <head>
        <title>a</title>
    </head>
    <body>
        <h1>a</h1>
        <p>b</p>
    </body>
</html>
```