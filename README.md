# Brick

[![Build Status](https://travis-ci.org/gajus/brick.png?branch=master)](https://travis-ci.org/gajus/brick)
[![Coverage Status](https://coveralls.io/repos/gajus/brick/badge.png)](https://coveralls.io/r/gajus/brick)

* Native PHP templates, no new syntax to learn
* Framework-agnostic, will work with any project

If the above definition seems familiar, it is because it is a copy-paste-scrap from [Plates](http://platesphp.com/) documentation. Brick is lightweight version of Pilates (less is more!), without:

* Namespaced templates (because you can achieve this using folder structure)
* [Sections](http://platesphp.com/sections/) or [Inheritance](http://platesphp.com/inheritance/) (because you can use [http://uk3.php.net/manual/en/book.outcontrol.php](Output Buffering Control) without wrapping paper)

In addition to the above differences, Brick does not share variables between templates unless you [explicitly expand them](#variables).

## Documentation

```php
// Instantiate Brick Template with an absolute path to the templates directory:
$template = new \Gajus\Brick\Template(__DIR__ . '/template');

// Refer to template files relative to the templates directory.
$template->render('hello');
```

### Template

Inside the template, there will be `$template` variable that refers to the parent Template instance.

```php
// Get names of all the included templates,
// e.g. in the case of the preceeding code, the output is ['hello'].
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
