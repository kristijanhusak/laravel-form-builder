---
layout: page
title: "Custom fields"
category: custom
order: 3
date: 2015-06-03 14:06:57
---

If you want to create your own custom field, you can do it like this:

``` php
<?php namespace App\Forms\Fields;

use Kris\LaravelFormBuilder\Fields\FormField;

class DatetimeType extends FormField {

    protected function getTemplate()
    {
        // At first it tries to load config variable,
        // and if fails falls back to loading view
        // resources/views/fields/datetime.blade.php
        return 'fields.datetime';
    }

    public function render(array $options = [], $showLabel = true, $showField = true, $showError = true)
    {
        $options['somedata'] = 'This is some data for view';

        return parent::render($options, $showLabel, $showField, $showError);
    }
}
```

And then in view you can use what you need:

``` php
// ...

<?= $options['somedata'] ?>

// ...
```

**Notice:** Package templates uses plain PHP for printing because of plans for supporting version 4 (prevent conflict with tags), but you can use blade for custom fields, just make sure to use tags that are not escaping html (`{!! !!}`)

And then add it to published config file(`config/packages/kris/laravel-form-builder/config.php`) in key `custom-fields` key this:

``` php
// ...
    'custom_fields' => [
        'datetime' => 'App\Forms\Fields\DatetimeType'
    ]
// ...
```

Or if you want to load it only for a single form, you can do it directly in BuildForm method:

``` php
<?php namespace App\Forms;

use Kris\LaravelFormBuilder\Form;

class PostForm extends Form
{
    public function __construct()
    {
        $this->addCustomField('datetime', 'App\Forms\Fields\DatetimeType');
    }

    public function buildForm()
    {
        $this
            ->add('title', 'text')
            ->add('created_at', 'datetime')
    }
}
```


