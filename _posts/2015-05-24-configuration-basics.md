---
layout: page
title: "Basics"
category: custom
order: 1
date: 2015-05-24 18:31:55
---

Bootstrap 3 classes are used by default for styling the forms. That can be modified by changing the configuration.

First, publish the package assets/configuration.

```sh
php artisan vendor:publish
```

This will create config file `config/laravel-form-builder.php` and folder with views in `resources/views/vendor/laravel-form-builder`.

Structure of the config needs to be like this:
[config.php](https://github.com/kristijanhusak/laravel-form-builder/blob/master/src/config/config.php) file.

change values in `defaults` key as you wish.

If you would like to avoid typing in full namespace of the form class when creating, you can add default namespace to the config that was just published, and it will prepend it every time you want to create form:

``` php
<?php

// config/app/laravel-form-builder.php

return [
    'default_namespace' => 'App\Forms'
]

// app/Http/Controllers/HomeController

public function indexAction()
{
    \FormBuilder::create('SongForm');
}
```
