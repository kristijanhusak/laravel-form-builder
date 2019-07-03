---
layout: page
title: "Installation"
category: overview
order: 1
date: 2015-05-24 17:09:14
---

Add package to `composer.json`

```json
{
    "require": {
        "kris/laravel-form-builder": "1.6.*"
    }
}
```

run `composer update`

Then add Service provider to `config/app.php`

```php
<?php
return [
    // ...

    'providers' => [
        // ...
        'Kris\LaravelFormBuilder\FormBuilderServiceProvider'
    ]
];
```

And Facade (also in `config/app.php`)

```php
<?php
return [
    // ...

    'aliases' => [
        // ...
        'FormBuilder' => 'Kris\LaravelFormBuilder\Facades\FormBuilder'
    ]
];
```

**Notice**: This package will add `illuminate/html` package and load Aliases (Form, Html) if they do not exist in the IoC container
