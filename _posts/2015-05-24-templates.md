---
layout: page
title: "Templates"
category: custom
order: 2
date: 2015-05-24 18:29:43
---

If you want to modify templates, make sure they have similar structure to this:
[views](https://github.com/kristijanhusak/laravel-form-builder/tree/master/src/views)

Globally changing paths to templates is available in the configuration
[config.php](https://github.com/kristijanhusak/laravel-form-builder/blob/master/src/config/config.php) file.

``` php
<?php
return [
    // ...
    'checkbox' => 'posts.my-custom-checkbox'    // resources/views/posts/my-custom-checkbox.blade.php
];
```

One more way to change template is directly from Form class:

``` php
<?php namespace App\Forms;

use Kris\LaravelFormBuilder\Form;

class PostForm extends Form
{
    public function buildForm()
    {
        // Change the form template (default from form.php)
        $this->setFormOption('template', 'posts.form'); // resources/views/posts/form.blade.php
        
        // Change a field's template
        $this
            ->add('title', 'text')
            ->add('body', 'textearea', [
                'template' => 'posts.textarea'    // resources/views/posts/textarea.blade.php
            ]);
    }
}
```

**When you are adding custom templates make sure they inherit functionality from defaults to prevent breaking.**
