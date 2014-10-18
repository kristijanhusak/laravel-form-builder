[![Build Status](https://img.shields.io/travis/kristijanhusak/laravel-form-builder/master.svg?style=flat)](https://travis-ci.org/kristijanhusak/laravel-form-builder)
[![Coverage Status](http://img.shields.io/scrutinizer/coverage/g/kristijanhusak/laravel-form-builder.svg?style=flat)](https://scrutinizer-ci.com/g/kristijanhusak/laravel-form-builder/?branch=master)
[![Quality Score](http://img.shields.io/scrutinizer/g/kristijanhusak/laravel-form-builder.svg?style=flat)](https://scrutinizer-ci.com/g/kris/laravel-form-builder)
[![Total Downloads](https://img.shields.io/packagist/dt/kris/laravel-form-builder.svg?style=flat)](https://packagist.org/packages/kris/laravel-form-builder)
[![Latest Stable Version](https://img.shields.io/packagist/v/kris/laravel-form-builder.svg?style=flat)](https://packagist.org/packages/kris/laravel-form-builder)
[![License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat)](LICENSE)

# Laravel 5 form builder

Form builder for Laravel 5 inspired by Symfony's form builder. With help of Laravels FormBuilder class creates forms that can be easy modified and reused.
By default it supports Bootstrap 3.

## Table of contents
1. [Installation](#installation)
2. [Basic usage](#usage)
  1. [Usage in controllers](#usage-in-controllers)
  2. [Usage in views](#usage-in-views)
3. [Plain form](#plain-form)
4. [Field customization](#field-customization)
5. [Changing configuration and templates](#changing-configuration-and-templates)
6. [Custom fields](#custom-fields)

###Installation

``` json
{
    "require": {
        "kris/laravel-form-builder": "1.1"
    }
}
```

run `composer update`

Then add Service provider to `config/app.php`

``` php
    'providers' => [
        // ...
        'Kris\LaravelFormBuilder\FormBuilderServiceProvider'
    ]
```

And Facade (also in `config/app.php`)

``` php
    'aliases' => [
        // ...
        'FormBuilder' => 'Kris\LaravelFormBuilder\Facades\FormBuilder'
    ]

```

**Notice**: This package will add `illuminate/html` package and load Aliases (Form, Html) if they does not exist in the IoC container

### Basic usage

Creating form classes is easy. With a simple artisan command:

``` sh
    php artisan form:make Forms/PostForm
```

you create form class in path `app/Forms/PostForm.php` that looks like this:

``` php
<?php namespace App\Forms;

use Kris\LaravelFormBuilder\Form;

class PostForm extends Form
{
    public function buildForm()
    {
        // Add fields here...
    }
}
```

You can add fields which you want when creating command like this:

``` sh
php artisan laravel-form-builder:make Forms/SongForm --fields="name:text, lyrics:textarea, publish:checkbox"
```

And that will create form in path `app/Forms/SongForm.php` with content:

``` php
<?php namespace App\Forms;

use Kris\LaravelFormBuilder\Form;

class SongForm extends Form
{
    public function buildForm()
    {
        $this
            ->add('name', 'text')
            ->add('lyrics', 'textarea')
            ->add('publish', 'checkbox');
    }
}
```

#### Usage in controllers

Forms can be used in controller like this:

``` php
<?php namespace App/Http/Controllers;

use Illuminate\Routing\Controller;

class SongsController extends BaseController {

    /**
     * @Get("/songs/create", as="song.create")
     */
    public function index()
    {
        $form = \FormBuilder::create('App\Forms\SongForm', [
            'method' => 'POST',
            'url' => route('song.store')
        ]);

        return view('song.create', compact('form'));
    }

    /**
     * @Post("/songs", as="song.store")
     */
    public function store()
    {
    }
}
```

#### Usage in views

From controller they can be used in views like this:

``` html
<!-- resources/views/song/create.blade.php -->

@extend('layouts.master')

@section('content')
    {!! form($form) !!}
@endsection
```

`{!! form($form) !!}` Will generate this html:

``` html
<form method="POST" action="http://example.dev/songs">
    <input name="_token" type="hidden" value="FaHZmwcnaOeaJzVdyp4Ml8B6l1N1DLUDsZmsjRFL">
    <div class="form-group">
        <label for="name" class="control-label">name</label>
        <input type="text" class="form-control" id="name">
    </div>
    <div class="form-group">
        <label for="lyrics" class="control-label">lyrics</label>
        <textarea name="lyrics" class="form-control"></textarea>
    </div>
    <div class="form-group">
        <label for="publish" class="control-label">publish</label>
        <input type="checkbox" name="publish" id="publish">
    </div>
</form>
```

There are several helper methods that can help you customize your rendering:


``` html
<!-- This function: -->

{!! form_row($form->lyrics, ['attr' => ['class' => 'big-textarea']]) !!}

<!-- Renders this: -->

<div class="form-group">
    <label for="lyrics" class="control-label">lyrics</label>
    <textarea name="lyrics" id="lyrics" class="big-textarea"></textarea>
</div>
```

You can also split it even more:
``` html
{!! form_start($form) !!}
<form method="POST" action="http://example.dev/songs">

{!! form_label($form->publish) !!}
<label for="publish" class="control-label">publish</label>

{!! form_widget($form->publish, ['checked' => true]) !!}
<input type="checkbox" name="publish" checked="checked">

{!! form_errors($form->publish) !!}
<div class="text-danger">This field is required.</div> <!-- Rendered only if validation errors occur. -->

{!! form_rest($form) !!}
<div class="form-group">
    <label for="name" class="control-label">name</label>
    <input type="text" class="form-control" id="name">
</div>
<div class="form-group">
    <label for="publish" class="control-label">publish</label>
    <input type="text" name="publish" id="publish">
</div>

{!! form_end($form) !!}
</form>

```
### Plain form

If you need to quick create a small form that does not to be reused, you can use `plain` method:

``` php
<?php namespace App/Http/Controllers;

use Illuminate\Routing\Controller;

class SongsController extends BaseController {

    /**
     * @Get("/login", as="login-page")
     */
    public function index()
    {
        $form = \FormBuilder::plain([
            'method' => 'POST',
            'url' => route('login')
        ])->add('username', 'text')->add('password', 'password')->add('login', 'submit');

        return view('auth.login', compact('form'));
    }

    /**
     * @Post("/login", as="login")
     */
    public function login()
    {
    }
}
```

### Field Customization
Fields can be easily customized within the class or view:

``` php
<?php namespace App\Forms;

use Kris\LaravelFormBuilder\Form;

class PostForm extends Form
{
    /**
     * By default validation error for each field is
     * shown under it. If you want to totally disable
     * showing those errors, set this to false
     */
    protected $showFieldErrors = true;

    public function buildForm()
    {
        $this
            ->add('name', 'text', [
                'wrapper' => 'name-input-container',
                'attr' => ['class' => 'input-name', 'placeholder' => 'Enter name here...'],
                'label' => 'Full name'
            ])
            ->add('bio', 'textarea')
            // This creates a select field
            ->add('subscription', 'choice', [
                'choices' => ['monthly' => 'Monthly', 'yearly' => 'Yearly'],
                'selected' => 'yearly',
                'multiple' => false // This is default. If set to true, it creates select with multiple select posibility
            ])
            // This creates radio buttons
            ->add('gender', 'choice', [
                'choices' => ['m' => 'Male', 'f' => 'Female'],
                'selected' => 'Male',
                'expanded' => true
            ])
            // Automatically adds enctype="multipart/form-data" to form
            ->add('image', 'file', [
                'label' => 'Upload your image'
            ])
            // This creates a checkbox list
            ->add('languages', 'choice', [
                'choices' => ['en' => 'English', 'de' => 'German', 'fr' => 'France'],
                'selected' => ['English', 'France']
                'expanded' => true,
                'multiple' => true
            ])
            ->add('policy-agree', 'checkbox', [
                'default_value' => 1,    //  <input type="checkbox" value="1">
                'label' => 'I agree to policy',
                'checked' => false    // This is the default.
            ])
            ->add('save', 'submit', [
                'attr' = ['class' => 'btn btn-primary']
            ])
            ->add('clear', 'reset', [
                'label' => 'Clear the form',
                'attr' => ['class' => 'btn btn-danger']
            ]);
    }
}
```
Here is the list of all available field types:
* text
* email
* url
* tel
* number
* date
* password
* hidden
* textarea
* submit
* reset
* button
* file
* image
* select
* checkbox
* radio
* choice

You can also bind the model to the class and add other options with setters

``` php
<?php namespace App/Http/Controllers;

use Illuminate\Routing\Controller;

class PostsController extends BaseController {

    /**
     * @Get("/posts/{id}/edit", as="posts.edit")
     */
    public function edit($id)
    {
        $model = Post::findOrFail($id);

        $form = \FormBuilder::create('App\Forms\PostForm')
            ->setMethod('PUT')
            ->setUrl(route('post.update'))
            ->setModel($model); // This will automatically do Form::model($model) in the form

        return view('posts.edit', compact('form'));
    }

    /**
     * @Post("/posts/{id}", as="posts.update")
     */
    public function update()
    {
    }
}
```

And in form, you can use that model to populate some fields like this

``` php
<?php namespace App\Forms;

use Kris\LaravelFormBuilder\Form;

class PostForm extends Form
{
    public function buildForm()
    {
        $this
            ->add('title', 'text')
            ->add('body', 'textearea')
            ->add('category', 'select', [
                'choices' => $this->model->categories()->lists('id', 'name')
            ]);
    }
}
```

### Changing configuration and templates

As mentioned above, bootstrap 3 form classes are used. If you want to change the defaults you need to publish the config like this:
``` sh
php artisan publish:config kris/laravel-form-builder
```
This will create folder `kris` in `config/packages` folder which will contain
[config.php](https://github.com/kristijanhusak/laravel-form-builder/blob/master/src/config/config.php) file.

change values in `defaults` key as you wish.

If you want to customize the views for fields and forms you can publish the views like this:
``` sh
php artisan publish:views kris/laravel-form-builder
```

This will create folder with all files in `resources/views/packages/kris/laravel-form-builder`

Other way is to change path to the templates in the
[config.php](https://github.com/kristijanhusak/laravel-form-builder/blob/master/src/config/config.php) file.

``` php
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
        $this
            ->add('title', 'text')
            ->add('body', 'textearea', [
                'template' => 'posts.textarea'    // resources/views/posts/textarea.blade.php
            ]);
    }
}
```

**When you are adding custom templates make sure they inherit functionality from defaults to prevent breaking.**

### Custom fields

If you want to create your own custom field, you can do it like this:

``` php
<?php namespace App\Forms\Fields;

use Kris\LaravelFormBuilder\Fields\FormField;

class DatetimeType extends FormField {

    protected function getTemplate()
    {
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
    public function buildForm()
    {
        $this->addCustomField('datetime', 'App\Forms\Fields\DatetimeType');

        $this
            ->add('title', 'text')
            ->add('created_at', 'datetime')
    }
}
```

### Todo
* Add possibility to disable showing validation errors under fields - **DONE**
* Add event dispatcher ?
