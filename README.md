# Brick

[![Build Status](https://travis-ci.org/gajus/brick.png?branch=master)](https://travis-ci.org/gajus/brick)
[![Coverage Status](https://coveralls.io/repos/gajus/brick/badge.png)](https://coveralls.io/r/gajus/brick)

# Documentation

```php
// Instantiate Brick Template with an absolute path to the templates directory:
$template = new \Gajus\Brick\Template(__DIR__ . '/template');

// Refer to template files relative to the templates directory.
$template->render('hello');
```

## Template

Inside the template, there will be `$template` variable that refers to the parent Template instance.

```php
$template->getNames(); // Get names of all the 