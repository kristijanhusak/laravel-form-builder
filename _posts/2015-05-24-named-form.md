---
layout: page
title: "Named form"
category: form
order: 3
date: 2015-05-24 17:55:13
---

Named form is basically a regular form with only one addition. It has name that is used to namespace fields in the form.
There are **3** ways to set up a named form:

1. Passing it as form option. This is **recommended** method

```php
    <?php

    FormBuilder::create('App\Forms\LoginForm', [
        'name' => 'users';
    ])
```

2. Setting as property on the Form class. Useful when namespacing is always needed

```php
    <?php
    Class LoginForm extends Form
    {
        protected $name = 'users';
    }
```

3. Using `setName` method on form class after instantiation. **NOT RECOMMENDED**

**Avoid** using this method because it requires rebuilding fields to set proper names.

```php
    <?php
    FormBuilder::create('App\Forms\LoginForm')->setName('users');
```
