---
layout: page
title: "Plain form"
category: form
order: 4
date: 2015-05-24 17:55:17
---

Plain form is useful when you want quickly to create a form, for which you do not need separate Form class.
It basicallly instantiates `Kris\LaravelFormBuilder\Form` class. It's same as creating regular form, only the
first parameter is skipped.

```php
<?php
$formOptions = [
    'url' => route('password.reset'),
    'method' => 'POST'
];

$form = \FormBuilder::plain($formOptions)->add('email', 'email', [
    'label' => 'Email address'
]);
```


